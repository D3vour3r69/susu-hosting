<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\Application;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $applications = Application::with(['user', 'unit'])
            ->when($selectedUnitId, function($query) use ($selectedUnitId) {
                $query->whereHas('unit', function($q) use ($selectedUnitId) {
                    $q->where('id', $selectedUnitId);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('applications.unit-index', compact('applications', 'units', 'selectedUnitId'));
    }
//    public function index(Request $request)
//    {
//        $units = Unit::all();
//        $selectedUnitId = $request->input('unit_id');
//
//        $applications = Application::with(['user', 'user.units'])
//            ->when($selectedUnitId, function ($query) use ($selectedUnitId) {
//                $query->whereHas('user.units', function ($q) use ($selectedUnitId) {
//                    $q->where('units.id', $selectedUnitId);
//                });
//            })
//            ->orderByDesc('created_at')
//            ->paginate(10);
//
//        return view('applications.unit-index', compact('applications', 'units', 'selectedUnitId'));
//    }
    public function index(Request $request)
    {
        $status = $request->input('status');
        $query = Application::query();

        if (auth()->user()->hasRole('admin')) {
            $query->with(['featureItems.feature', 'unit', 'user']);
        } elseif (auth()->user()->hasRole('user_head')) {
            $query->whereHas('unit', function($q) {
                $q->where('head_id', auth()->id());
            });
        } else {
            $query->where('user_id', auth()->id());
        }

        if ($status && in_array($status, ['active', 'inactive', 'completed'])) {
            $query->where('status', $status);
        }

        $applications = $query->latest()->paginate(10);

        return view('applications.index', compact('applications'));
    }


    public function create()
    {
        $features = Feature::with('items')->get();
        return view('applications.create', compact('features'));
    }

    public function approve(Application $application)
    {
        $this->authorize('manage', $application);

        $application->update([
            'status' => 'completed',
            'approved' => true,
            'approved_at' => now()
        ]);

        return back()->with('success', 'Заявка одобрена');
    }

    public function approved()
    {
        $applications = Application::where('approved', true)
            ->when(!auth()->user()->hasRole('admin'), function($q) {
                $q->whereHas('unit', function($q) {
                    $q->where('head_id', auth()->id());
                });
            })
            ->paginate(10);

        return view('applications.approved', compact('applications'));
    }

    public function reject(Application $application)
    {
        $this->authorize('manage', $application);

        $application->update([
            'status' => 'inactive',
            'approved' => false
        ]);

        return back()->with('warning', 'Заявка отклонена');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
//        if ($user->positions->doesntExist()) {
//            $unit = Unit::firstOrCreate(['name' => 'Временное подразделение']);
//            $user->units()->attach($unit, ['position' => 'Временная должность']);
//        }
        $userUnits = $user->positions->pluck('unit')->unique();
        if ($userUnits->count() === 1)
        {
            $unitId = $userUnits->first()->id;
        }
        else
        {
            $validatedUnit = $request->validate(['unit_id' => 'required|exists:units,id']);
            $unitId = $validatedUnit['unit_id'];
        }
        $validated = $request->validate([
            'features' => 'required|array',
            'features.*' => 'required|exists:feature_items,id',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,completed'
        ]);

        try {
            $application = Application::create([
                'user_id' => $user->id,
                'unit_id' => $unitId,
                'notes' => $validated['notes'],
                'status' => $validated['status']
            ]);

            $application->featureItems()->sync($validated['features']);
            return redirect()->route('applications.index')->with('success', 'Записка создана!');
        } catch (\Exception $e) {
            \Log::error('Ошибка: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Ошибка создания.']);
        }
    }

    public function download(Application $application)
    {

//        $content = view('applications.download', compact('application'));
//
//        return response()->streamDownload(
//            fn() => print($content),
//            "application_{$application->id}.txt"
//        );
        $this->authorize('view', $application);

        $pdf = Pdf::loadView('applications.pdf', compact('application'));
        return $pdf->download("application_{$application->id}.pdf");
    }

    public function destroy(Application $application)
    {
        $application->delete();
        return redirect()->back()
            ->with('success', 'Записка успешно удалена');
    }
}
