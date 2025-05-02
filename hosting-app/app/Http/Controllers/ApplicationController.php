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
    public function index()
    {
        $applications = Auth::user()
            ->applications()
            ->with(['featureItems.feature', 'unit'])
            ->latest()
            ->get();

        return view('applications.index', compact('applications'));
    }


    public function create()
    {
        $features = Feature::with('items')->get();
        return view('applications.create', compact('features'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'features' => 'required|array',
            'features.*' => 'required|exists:feature_items,id',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,completed',
            'unit_id' => 'required|exists:units,id'
        ]);

        try {
            // Создание записки
            $application = Application::create([
                'user_id' => auth()->id(),
                'notes' => $validated['notes'],
                'status' => $validated['status']
            ]);

            // Привязка выбранных параметров
            $application->featureItems()->sync(
                collect($validated['features'])->values()->all()
            );

            return redirect()->route('applications.index')
                ->with([
                    'success' => 'Записка успешно создана!',
                    'application_id' => $application->id
                ]);

        } catch (\Exception $e) {
            // Логирование ошибки
            \Log::error('Ошибка создания записки: ' . $e->getMessage());

            return back()->withInput()
                ->withErrors(['error' => 'Ошибка создания. Попробуйте позже.']);
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
