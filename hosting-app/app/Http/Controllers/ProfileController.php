<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load('positions.unit');

        // Получаем ID подразделений, которые уже есть у пользователя
        $attachedUnitIds = $user->positions->pluck('unit.id')->toArray();

        // Фильтруем подразделения, исключая привязанные
        $availableUnits = Unit::whereNotIn('id', $attachedUnitIds)
            ->with('positions')
            ->get();

        return view('profile.show', compact('user', 'availableUnits'));
    }

    public function storePosition(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'position_name' => 'required|string|max:255', // Поле для названия должности
            'is_head' => 'sometimes|boolean'
        ]);

        // Создаем или находим должность в выбранном подразделении
        $position = Position::firstOrCreate([
            'unit_id' => $request->unit_id,
            'name' => $request->position_name
        ]);

        // Проверяем, не привязана ли уже эта должность к пользователю
        if (auth()->user()->positions()->where('position_id', $position->id)->exists()) {
            return back()->withErrors(['position_name' => 'Эта должность уже привязана']);
        }

        // Привязываем должность
        auth()->user()->positions()->attach($position->id);

        // Назначение руководителем (если выбрано)
        if ($request->is_head) {
            $unit = Unit::find($request->unit_id);
            $unit->update(['head_id' => auth()->id()]);
        }

        return back()->with('success', 'Должность добавлена!');
    }

    public function updateHeadStatus(Request $request, Unit $unit)
    {
        $request->validate(['is_head' => 'required|boolean']);

        if ($request->is_head) {
            // Назначение руководителем
            $unit->update(['head_id' => auth()->id()]);
        } else {
            // Снятие статуса
            $unit->update(['head_id' => null]);
        }

        return back()->with('success', 'Статус руководителя обновлен');
    }

    public function destroyPosition(Position $position)
    {
        $unit = $position->unit;

        // Если пользователь - руководитель этого подразделения, снимаем статус
        if ($unit->head_id === auth()->id()) {
            $unit->update(['head_id' => null]);
        }

        // Удаляем связь с должностью
        auth()->user()->positions()->detach($position->id);

        return back()->with('success', 'Должность удалена');
    }

    protected function userCanManageUnits(): bool
    {
        return auth()->user()->hasRole('admin')
            || auth()->user()->managedUnits()->exists();
    }
}
