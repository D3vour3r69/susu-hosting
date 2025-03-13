<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    // Показ формы
    public function create()
    {
        $features = Feature::with('items')->get();
        return view('applications.create', compact('features'));
    }

    // Сохранение заявки
    public function store(Request $request)
    {
        // Валидация
        $validated = $request->validate([
            'feature_items' => 'required|array',
            'feature_items.*' => 'exists:feature_items,id',
            'notes' => 'nullable|string',
        ]);

        // Создание заявки
        $application = Application::create([
            'user_id' => auth()->id(),
            'notes' => $validated['notes'],
        ]);

        // Привязка выбранных параметров
        $application->featureItems()->attach($validated['feature_items']);

        return redirect()->route('applications.create')->with('success', 'Заявка успешно создана!');
    }
}
