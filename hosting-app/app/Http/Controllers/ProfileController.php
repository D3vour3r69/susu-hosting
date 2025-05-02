<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Показ формы
    public function show()
    {
        $user = Auth::user()->load([
            'units',
            'managedUnits'
        ]);

        $availableUnits = Unit::whereDoesntHave('users', function($query) {
            $query->where('user_id', auth()->id());
        })->get();

        return view('profile.show', compact('user', 'availableUnits'));
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'units' => ['required', 'array', 'min:1'],
            'units.*' => ['integer', Rule::exists('units', 'id')]
        ]);


        if ($this->userCanEditUnits($validated['units'])) {
            $user->units()->sync($validated['units']);
            return back()->with('success', 'Привязка к подразделениям обновлена');
        }

        return back()->withErrors(['units' => 'Недопустимые подразделения']);
    }

    public function storeUnit(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'role' => 'required|in:member,head'
        ]);

        try {
            DB::beginTransaction();

            // Добавляем в подразделение
            auth()->user()->units()->attach($request->unit_id, [
                'position' => $request->role === 'head' ? 'Руководитель' : 'Сотрудник'
            ]);

            // Если выбрана роль руководителя
            if ($request->role === 'head') {
                Unit::where('id', $request->unit_id)->update([
                    'head_id' => auth()->id()
                ]);
            }

            DB::commit();

            return redirect()->route('profile.show')->with('success', 'Подразделение добавлено!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ошибка при сохранении: ' . $e->getMessage()]);
        }
    }

    public function updateUnit(Request $request, Unit $unit)
    {
        $request->validate([
            'position' => 'required|in:head,member'
        ]);

        auth()->user()->units()->updateExistingPivot($unit->id, [
            'position' => $request->position
        ]);

        return back()->with('success', 'Роль обновлена');
    }

    public function destroyUnit(Unit $unit)
    {
        auth()->user()->units()->detach($unit->id);
        return back()->with('success', 'Подразделение удалено');
    }


    protected function userCanEditUnits(array $unitIds): bool
    {
        return auth()->user()->is_admin
            ? true
            : Unit::whereIn('id', $unitIds)->exists();
    }
}
