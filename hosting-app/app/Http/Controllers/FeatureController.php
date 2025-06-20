<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\FeatureItem;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index()
    {
        $features = Feature::with('items')->get();

        return view('features.index', compact('features'));
    }

    public function store(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'name' => 'required|unique:features|max:255',
            'slug' => 'required|unique:features|max:255',
        ]);

        Feature::create($validated);

        return redirect()->route('features.index')->with('success', 'Категория создана');
    }

    public function storeItem(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|unique:feature_items|max:255',
        ]);

        $feature->items()->create($validated);

        return redirect()->back()->with('success', 'Пункт добавлен');
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();

        return redirect()->route('features.index');
    }

    public function destroyItem(FeatureItem $item)
    {
        $item->delete();

        return redirect()->back()->with('success', 'Пункт удален');
    }
}
