<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = \App\Models\ActivityType::withCount('activities')->orderBy('name')->get();
        return view('admin.activity-types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.activity-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $churchId = session('tenant_church_id') ?? auth()->user()?->church_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('activity_types', 'name')->where('church_id', $churchId)
            ],
            'color' => 'required|string|size:7|starts_with:#',
        ]);

        \App\Models\ActivityType::create($validated);

        return redirect()->route('admin.activity-types.index')
            ->with('success', 'Type d\'activité créé avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\ActivityType $activityType)
    {
        return view('admin.activity-types.edit', compact('activityType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, \App\Models\ActivityType $activityType)
    {
        $churchId = session('tenant_church_id') ?? auth()->user()?->church_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('activity_types', 'name')->where('church_id', $churchId)->ignore($activityType->id)
            ],
            'color' => 'required|string|size:7|starts_with:#',
        ]);

        $activityType->update($validated);

        return redirect()->route('admin.activity-types.index')
            ->with('success', 'Type d\'activité mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\ActivityType $activityType)
    {
        if ($activityType->activities()->count() > 0) {
            return redirect()->route('admin.activity-types.index')
                ->with('error', 'Impossible de supprimer ce type car il est utilisé par des activités.');
        }

        $activityType->delete();

        return redirect()->route('admin.activity-types.index')
            ->with('success', 'Type d\'activité supprimé avec succès.');
    }
}
