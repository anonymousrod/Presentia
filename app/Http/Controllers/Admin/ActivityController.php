<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Events\ActivityCreated;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Http\Requests\Admin\UpdateActivityRequest;
use App\Enums\ActivityStatus;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Activity::with(['responsible', 'group', 'role']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $activities = $query->orderBy('start_time', 'desc')->paginate(10);

        return view('admin.activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $responsibles = User::all();
        $groups = Group::all();
        $roles = Role::all();

        return view('admin.activities.create', compact('responsibles', 'groups', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request)
    {
        $activity = Activity::create($request->validated());

        if ($activity->status === ActivityStatus::PUBLISHED) {
            event(new ActivityCreated($activity));
        }

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activité créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        $activity->load(['responsible', 'group', 'role', 'registrations.user']);
        return view('admin.activities.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        $responsibles = User::all();
        $groups = Group::all();
        $roles = Role::all();

        return view('admin.activities.edit', compact('activity', 'responsibles', 'groups', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $oldStatus = $activity->status;

        $activity->update($request->validated());

        // Si le statut passe de DRAFT à PUBLISHED, on dispatch l'event
        if ($oldStatus === ActivityStatus::DRAFT && $activity->status === ActivityStatus::PUBLISHED) {
            event(new ActivityCreated($activity));
        }

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activité mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activité supprimée avec succès.');
    }
}
