<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Head;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Application::class, 'application');
    }

    public function unitIndex(Request $request)
    {
        $units = Unit::with('head')->get();
        $selectedUnitId = $request->input('unit_id');

        $query = Application::with(['user', 'unit']);

        if (auth()->user()->hasRole('admin')) {
            // Админ видит все заявки
            if ($selectedUnitId) {
                $query->where('unit_id', $selectedUnitId);
            }
        } elseif (auth()->user()->hasRole('user_head')) {

            $headUnitIds = auth()->user()->positions->pluck('unit_id')->toArray();

            $query->whereIn('unit_id', $headUnitIds);

            if ($selectedUnitId && in_array($selectedUnitId, $headUnitIds)) {
                $query->where('unit_id', $selectedUnitId);
            }
        } else {
            // Обычный пользователь видит только свои заявки
            $query->where('user_id', auth()->id());

            if ($selectedUnitId) {
                $query->where('unit_id', $selectedUnitId);
            }
        }

        $applications = $query->orderByDesc('created_at')->paginate(10);

        return view('applications.unit-index', compact('applications', 'units', 'selectedUnitId'));
    }

    public function index(Request $request)
    {

        $status = $request->input('status');
        $domain = $request->input('domain');
        $query = Application::query();
        $query->with(['head']);
        $showCompleted = $request->boolean('show_completed');
        if (auth()->user()->hasRole('admin')) {
            $query->with(['featureItems.feature', 'unit', 'user']);
        } elseif (auth()->user()->hasRole('user_head')) {
            $query->whereHas('unit', function ($q) {
                $q->where('head_id', auth()->id());
            });
        } else {
            $query->where('user_id', auth()->id());
            if (! $showCompleted) {
                $query->where('status', '!=', 'completed');
            }
        }

        if ($status && in_array($status, ['active', 'inactive', 'completed'])) {
            $query->where('status', $status);
        }

        if ($domain) {
            $query->where('domain', 'like', '%'.$domain.'%');
        }

        $applications = $query->latest()->paginate(10);

        return view('applications.index', compact('applications', 'showCompleted', 'domain', 'status'));
    }

    public function create()
    {

        $user = auth()->user();
        $userUnits = $user->positions->pluck('unit')->unique();

        if ($userUnits->isEmpty()) {
            abort(403, 'У пользователя не найдено подразделение');
        }
        $units = $userUnits->all();

        $allResponsibles = \App\Models\User::whereHas('positions', function ($query) use ($userUnits) {
            $query->whereIn('unit_id', $userUnits->pluck('id'));
        })->with(['positions' => function($query) {
            $query->with('unit');
        }])->get()->toArray();

        $heads = \App\Models\Head::all();
        $features = \App\Models\Feature::with('items')->get();

        return view('applications.create', compact('allResponsibles', 'units', 'heads', 'features'));
    }

    public function destroy(Application $application)
    {
        $application->delete();

        return redirect()->route('applications.index');
    }

    public function approve(Application $application)
    {
        $this->authorize('manage', $application);

        $application->update([
            'status' => 'completed',
            'approved' => true,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Заявка одобрена');
    }

    public function approved(Request $request)
    {

        $query = Application::where('approved', true);

        if (! auth()->user()->hasRole('admin')) {
            $query->whereHas('unit', function ($q) {
                $q->where('head_id', auth()->id());
            });
        }

        if ($request->filled('domain')) {
            $domain = $request->input('domain');
            $query->where('domain', 'like', "%{$domain}%");
        }

        $applications = $query->paginate(10);

        return view('applications.approved', compact('applications'));
    }

    public function reject(Application $application)
    {
        $this->authorize('manage', $application);
        if ($application->status === 'completed') {
            $application->update([
                'status' => 'active',
                'approved' => false,
            ]);
        } else {
            $application->update([
                'status' => 'inactive',
                'approved' => false,
            ]);
        }

        return back()->with('warning', 'Заявка отклонена');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $userUnits = $user->positions->pluck('unit')->unique();
        if ($userUnits->count() === 1) {
            $unitId = $userUnits->first()->id;
        } else {
            $validatedUnit = $request->validate(['unit_id' => 'required|exists:units,id']);
            $unitId = $validatedUnit['unit_id'];
        }

        $validated = $request->validate([
            'features' => 'required|array',
            'features.*' => 'required|exists:feature_items,id',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,completed',
            'domain' => 'required|string|max:255|unique:applications',
            'responsible_id' => 'required|exists:users,id',
            'head_id' => 'required|exists:heads,id',
        ]);

        try {
            $application = Application::create([
                'user_id' => $user->id,
                'unit_id' => $unitId,
                'head_id' => $validated['head_id'],
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
                'domain' => $validated['domain'],
                'responsible_id' => $validated['responsible_id'],
            ]);

            $application->featureItems()->sync($validated['features']);

            return redirect()->route('applications.index')->with('success', 'Записка создана!');
        } catch (\Exception $e) {
            \Log::error('Ошибка: '.$e->getMessage());

            return back()->withInput()->withErrors(['error' => 'Ошибка создания.']);
        }
    }

    public function download(Application $application)
    {
        $this->authorize('view', $application);

        $application->load([
            'unit.head',
            'responsible',
            'featureItems',
            'head',
        ]);

        //        $heads = $application->heads
        //
        //
        //        $head = $heads->first();

        $pdf = PDF::loadView('applications.pdf', [
            'application' => $application,
            //            'head' => $head
        ]);

        $pdf->setOption('defaultFont', 'times');
        $pdf->setOption('isRemoteEnabled', true);

        $filename = "Служебная_записка_{$application->id}.pdf";

        return $pdf->download($filename);
    }
}
