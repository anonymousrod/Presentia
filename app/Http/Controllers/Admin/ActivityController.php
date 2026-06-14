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
use Barryvdh\DomPDF\Facade\Pdf;

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

        $activities = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

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
        $activity->load(['responsible', 'group', 'role', 'registrations.user', 'attendances.user']);
        return view('admin.activities.show', compact('activity'));
    }

    /**
     * Télécharger la liste des inscriptions au format PDF.
     */
    public function downloadRegistrationsPdf(Activity $activity)
    {
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

        $logoUeebPath = "D:/TFG_Projet/front_back_ecomerce/Projet_Presentia/LOGO UEEB.png";
        $logoJeunessePath = "D:/TFG_Projet/front_back_ecomerce/Projet_Presentia/LOGO Jeunesse Etoile Rouge/LOGO/Logo Jeunesse/PNG/Fichier 10 1.png";

        $logoUeebBase64 = '';
        if (file_exists($logoUeebPath)) {
            $logoUeebBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoUeebPath));
        }

        $logoJeunesseBase64 = '';
        if (file_exists($logoJeunessePath)) {
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
        $activity->load(['responsible', 'group', 'role', 'attendances.user.groups']);

        // Retrieve valid attendances (Present or Late or Excused or Absent)
        $attendances = $activity->attendances()
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->orderBy('users.first_name')
            ->select('attendances.*')
            ->get();

        $logoUeebPath = "D:/TFG_Projet/front_back_ecomerce/Projet_Presentia/LOGO UEEB.png";
        $logoJeunessePath = "D:/TFG_Projet/front_back_ecomerce/Projet_Presentia/LOGO Jeunesse Etoile Rouge/LOGO/Logo Jeunesse/PNG/Fichier 10 1.png";

        $logoUeebBase64 = '';
        if (file_exists($logoUeebPath)) {
            $logoUeebBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoUeebPath));
        }

        $logoJeunesseBase64 = '';
        if (file_exists($logoJeunessePath)) {
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
