<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use App\Models\ActivityType;
use Spatie\Permission\Models\Role;
use App\Events\ActivityCreated;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Http\Requests\Admin\UpdateActivityRequest;
use App\Enums\ActivityStatus;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Activity::class);

        $query = Activity::with(['responsible', 'group', 'role']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('activity_type_id', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(10)->appends(request()->query());

        $activityTypes = ActivityType::orderBy('name')->get();

        return view('admin.activities.index', compact('activities', 'activityTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Activity::class);

        $responsibles = User::all();
        $groups = Group::all();
        $roles = Role::all();

        $activityTypes = ActivityType::orderBy('name')->get();

        return view('admin.activities.create', compact('responsibles', 'groups', 'roles', 'activityTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request)
    {
        $this->authorize('create', Activity::class);

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
        $this->authorize('view', $activity);
        $user = auth()->user();
        $listType = '';

        if ($user->hasRole('Administrateur') || $user->can(\App\Enums\PermissionEnum::ATTENDANCE_VIEW->value)) {
            $activity->load(['responsible', 'group', 'role', 'registrations.user.groups', 'attendances.user.groups']);
            $listType = 'Globale';
        } else {
            // Filter attendances for user's own group
            $ledGroupIds = $user->ledGroups()->pluck('groups.id')->toArray();
            $activity->load(['responsible', 'group', 'role', 'registrations.user.groups']);
            $activity->load(['attendances' => function ($query) use ($ledGroupIds) {
                $query->whereHas('user.groups', function ($q) use ($ledGroupIds) {
                    $q->whereIn('groups.id', $ledGroupIds);
                });
            }, 'attendances.user.groups']);
            $listType = 'Mon Groupe';
        }

        $allGroups = \App\Models\Group::orderBy('name')->get();

        return view('admin.activities.show', compact('activity', 'listType', 'allGroups'));
    }

    /**
     * Télécharger la liste des inscriptions au format PDF.
     */
    public function downloadRegistrationsPdf(Activity $activity)
    {
        $this->authorize('view', $activity);

        $activity->load(['responsible', 'group', 'role', 'registrations.user.groups']);

        // Filter valid registrations (non-waitlisted, status PRESENT or UNCERTAIN)
        $registrations = $activity->registrations()
            ->where('registrations.is_waitlisted', false)
            ->whereIn('registrations.status', [\App\Enums\RegistrationStatus::PRESENT->value, \App\Enums\RegistrationStatus::UNCERTAIN->value])
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->orderBy('users.first_name')
            ->select('registrations.*')
            ->get();

        $settings = \App\Models\AppSetting::firstOrCreate(['id' => 1]);

        $logoUeebPath = str_starts_with($settings->pdf_logo_1, 'assets/') ? public_path($settings->pdf_logo_1) : storage_path('app/public/' . $settings->pdf_logo_1);
        $logoJeunessePath = str_starts_with($settings->pdf_logo_2, 'assets/') ? public_path($settings->pdf_logo_2) : storage_path('app/public/' . $settings->pdf_logo_2);

        $logoUeebBase64 = '';
        if ($settings->pdf_logo_1 && file_exists($logoUeebPath)) {
            $logoUeebBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoUeebPath));
        }

        $logoJeunesseBase64 = '';
        if ($settings->pdf_logo_2 && file_exists($logoJeunessePath)) {
            $logoJeunesseBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoJeunessePath));
        }

        $pdf = Pdf::loadView('admin.activities.registrations-pdf', compact('activity', 'registrations', 'logoUeebBase64', 'logoJeunesseBase64'));
        return $pdf->download("Liste_Inscriptions_{$activity->id}_{$activity->title}.pdf");
    }

    /**
     * Télécharger la liste de présence au format PDF.
     */
    public function downloadAttendancePdf(Activity $activity)
    {
        $this->authorize('view', $activity);

        $activity->load(['responsible', 'group', 'role', 'attendances.user.groups']);

        // Retrieve valid attendances (Present or Late or Excused or Absent)
        $attendances = $activity->attendances()
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->orderBy('users.first_name')
            ->select('attendances.*')
            ->get();

        $settings = \App\Models\AppSetting::firstOrCreate(['id' => 1]);

        $logoUeebPath = str_starts_with($settings->pdf_logo_1, 'assets/') ? public_path($settings->pdf_logo_1) : storage_path('app/public/' . $settings->pdf_logo_1);
        $logoJeunessePath = str_starts_with($settings->pdf_logo_2, 'assets/') ? public_path($settings->pdf_logo_2) : storage_path('app/public/' . $settings->pdf_logo_2);

        $logoUeebBase64 = '';
        if ($settings->pdf_logo_1 && file_exists($logoUeebPath)) {
            $logoUeebBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoUeebPath));
        }

        $logoJeunesseBase64 = '';
        if ($settings->pdf_logo_2 && file_exists($logoJeunessePath)) {
            $logoJeunesseBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoJeunessePath));
        }

        $pdf = Pdf::loadView('admin.activities.attendance-pdf', compact('activity', 'attendances', 'logoUeebBase64', 'logoJeunesseBase64'));
        return $pdf->download("Liste_Presence_{$activity->id}_{$activity->title}.pdf");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        $this->authorize('update', $activity);

        $responsibles = User::all();
        $groups = Group::all();
        $roles = Role::all();

        $activityTypes = ActivityType::orderBy('name')->get();

        return view('admin.activities.edit', compact('activity', 'responsibles', 'groups', 'roles', 'activityTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $this->authorize('update', $activity);

        $oldStatus = $activity->status;

        $activity->update($request->validated());

        // Si le statut passe de DRAFT à PUBLISHED, on dispatch l'event pour tout le monde ou groupe
        if ($oldStatus === ActivityStatus::DRAFT && $activity->status === ActivityStatus::PUBLISHED) {
            event(new ActivityCreated($activity));
        }
        // Helper function for getting users by visibility
        $getUsersByVisibility = function ($activity) {
            if ($activity->visibility === \App\Enums\ActivityVisibility::ALL) {
                return User::all();
            } elseif ($activity->visibility === \App\Enums\ActivityVisibility::GROUP && $activity->visibility_group_id) {
                $group = \App\Models\Group::find($activity->visibility_group_id);
                return $group ? $group->members()->wherePivotNull('left_at')->get() : collect();
            } elseif ($activity->visibility === \App\Enums\ActivityVisibility::ROLE && $activity->visibility_role_id) {
                $role = \Spatie\Permission\Models\Role::find($activity->visibility_role_id);
                return $role ? User::role($role->name)->get() : collect();
            }
            return collect();
        };

        // Si le statut passe à CANCELLED (et qu'il n'y était pas déjà)
        if ($oldStatus !== ActivityStatus::CANCELLED && $activity->status === ActivityStatus::CANCELLED) {
            $users = $getUsersByVisibility($activity);
            if ($users->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\Activity\ActivityCancelledNotification($activity->title, $activity->cancellation_reason));
            }
        }
        // Sinon, si l'activité était déjà publiée et qu'elle est modifiée
        elseif ($oldStatus === ActivityStatus::PUBLISHED && $activity->status === ActivityStatus::PUBLISHED) {
            // Envoyer la notification uniquement si des champs essentiels ont changé
            if ($activity->wasChanged(['title', 'start_time', 'end_time', 'location'])) {
                $users = $getUsersByVisibility($activity);
                if ($users->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\Activity\ActivityUpdatedNotification($activity));
                }
            }
        }

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activité mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        $this->authorize('delete', $activity);

        $activity->delete();

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activité supprimée avec succès.');
    }
}
