<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitApplicationController extends Controller
{
    public function index(Request $request)
    {
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
        $user = Auth::user();
        $selectedUnitId = $request->input('unit_id');

        // Для администратора - все подразделения
        if ($user->hasRole('admin')) {
            $units = Unit::all();
        }
        // Для руководителя - только его подразделения
        elseif ($user->hasRole('user_head')) {
            $units = $user->units;
        }
        // Для остальных - пустой список
        else {
            $units = collect();
        }

        $applications = Application::with(['user', 'user.units'])
            ->when($user->hasRole('user_head'), function ($query) use ($user) {
                // Фильтр для руководителя - только его подразделения
                $query->whereHas('user.units', function ($q) use ($user) {
                    $q->whereIn('units.id', $user->units->pluck('id'));
                });
            })
            ->when($selectedUnitId, function ($query) use ($selectedUnitId) {
                // Дополнительный фильтр по выбранному подразделению
                $query->whereHas('user.units', function ($q) use ($selectedUnitId) {
                    $q->where('units.id', $selectedUnitId);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('applications.unit-index', compact('applications', 'units', 'selectedUnitId'));
    }
}
