<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class ApplicationController extends Controller
{


    public function index()
    {
        $applications = Application::where('user_id', auth()->id())
            ->with(['featureItems.feature'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('applications', compact('applications'));
    }
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
            'status' => 'required|in:active,inactive,completed'
        ]);

            // Получаем тестового пользователя
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ]
        );

            // Создание заявки
        $application = Application::create([
            'user_id' => auth()->id(),
            'notes' => $validated['notes'],
            'status' => $validated['status']
        ]);

        // Привязка параметров
        $application->featureItems()->attach($validated['feature_items']);

        return redirect()->route('applications.create')
            ->with('success', 'Заявка успешно создана!')
            ->with('application_id', $application->id); // Передаем объект заявки
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

    public function destroy($id)
    {
        $application = Application::findOrFail($id);

        // Проверка прав доступа
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        $application->delete();

        return redirect()->back()
            ->with('success', 'Заявка успешно удалена');
    }
}
