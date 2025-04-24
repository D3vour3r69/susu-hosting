<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\Application;
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

    public function index()
    {
        $applications = Auth::user()->applications()
            ->with(['featureItems.feature'])
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
            'status' => 'required|in:active,inactive,completed'
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
        $this->authorize('view', $application);

        $content = view('applications.download', compact('application'));

        return response()->streamDownload(
            fn() => print($content),
            "application_{$application->id}.txt"
        );
    }

    public function destroy(Application $application)
    {
        $application->delete();
        return redirect()->back()
            ->with('success', 'Записка успешно удалена');
    }
}
