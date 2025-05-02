<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Application;
use Illuminate\Http\Request;

class UnitApplicationController extends Controller
{
    public function index(Request $request)
    {
        $units = Unit::all();
        $selectedUnitId = $request->input('unit_id');

        $applications = Application::with(['user', 'user.units'])
            ->when($selectedUnitId, function ($query) use ($selectedUnitId) {
                $query->whereHas('user.units', function ($q) use ($selectedUnitId) {
                    $q->where('units.id', $selectedUnitId);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('applications.unit-index', compact('applications', 'units', 'selectedUnitId'));
    }
}
