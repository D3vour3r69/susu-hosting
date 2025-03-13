<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;

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
        try {
            // Валидация
            $validated = $request->validate([
                'feature_items' => 'required|array',
                'feature_items.*' => 'exists:feature_items,id',
                'notes' => 'nullable|string',
            ]);

            // Получаем тестового пользователя
            $testUser = User::firstOrCreate(
                ['email' => 'test@example.com'],
                [
                    'name' => 'Test User',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now() // Добавьте запятую, если будут новые элементы
                ] // Закрывающая скобка массива параметров
            );

            // Создание заявки
            $application = Application::create([
                'user_id' => $testUser->id,
                'notes' => $validated['notes'],
            ]);

            // Привязка параметров
            $application->featureItems()->attach($validated['feature_items']);

            return redirect()->route('applications.create')
                ->with('success', 'Заявка успешно создана!')
                ->with('application_id', $application->id); // Передаем объект заявки
        } catch (Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Ошибка: ' . $e->getMessage()])
                ->withInput();
        }

    }
    public function download($id)
    {
        $application = Application::with(['user', 'featureItems.feature'])->findOrFail($id);

        $content = "Служебная записка #{$application->id}\n";
        $content .= "Дата: {$application->created_at->format('d.m.Y H:i')}\n";
        $content .= "Пользователь: {$application->user->name}\n";
        $content .= "Выбранные параметры:\n";

        foreach ($application->featureItems as $item) {
            $content .= "- {$item->feature->name}: {$item->name}\n";
        }

        $content .= "\nДополнительные заметки:\n{$application->notes}";

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="application_'.$application->id.'.txt"');
    }
} // Закрывающая скобка класса
