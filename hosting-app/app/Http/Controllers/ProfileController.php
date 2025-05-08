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
        $user = Auth::user()->load([
            'positions.unit',
            'managedUnits'
        ]);

        $units = Unit::with('positions')->get();

        return view('profile.show', [
            'user' => $user,
            'units' => $units
        ]);
    }

    public function storePosition(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'position_id' => [
                'required',
                'exists:positions,id',
                Rule::unique('position_user')->where('user_id', auth()->id())
            ],
            'is_head' => 'sometimes|boolean'
        ]);

        // Привязка позиции
        auth()->user()->positions()->attach($request->position_id);

        // Назначение руководителем подразделения
        if ($request->has('is_head')) {
            $unit = Unit::find($request->unit_id);
            $unit->update(['head_id' => auth()->id()]);

            // Добавляем позицию руководителя если нужно
            $headPosition = Position::firstOrCreate([
                'name' => 'Руководитель',
                'unit_id' => $unit->id
            ]);

            auth()->user()->positions()->attach($headPosition->id);
        }

        return back()->with('success', 'Должность успешно добавлена!');
    }

    public function updateHeadStatus(Request $request, Unit $unit)
    {
        $request->validate(['is_head' => 'required|boolean']);

        if ($request->is_head) {
            $unit->update(['head_id' => auth()->id()]);
            $position = Position::firstOrCreate([
                'name' => 'Руководитель',
                'unit_id' => $unit->id
            ]);
            auth()->user()->positions()->syncWithoutDetaching([$position->id]);
        } else {
            $unit->update(['head_id' => null]);
            $position = Position::where([
                'name' => 'Руководитель',
                'unit_id' => $unit->id
            ])->first();

            if ($position) {
                auth()->user()->positions()->detach($position->id);
            }
        }

        return back()->with('success', 'Статус руководителя обновлен');
    }

    public function destroyPosition(Position $position)
    {
        // Проверка что пользователь не является руководителем
        if ($position->unit->head_id === auth()->id()) {
            $position->unit->update(['head_id' => null]);
        }

        auth()->user()->positions()->detach($position->id);

        return back()->with('success', 'Должность удалена');
    }

    protected function userCanManageUnits(): bool
    {
        return auth()->user()->hasRole('admin') ||
            auth()->user()->positions()
                ->whereHas('unit', fn($q) => $q->where('head_id', auth()->id()))
                ->exists();
    }
}
