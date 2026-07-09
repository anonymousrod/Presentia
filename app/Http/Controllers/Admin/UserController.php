<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserStatus;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Notifications\Member\AccountCreatedNotification;
use App\Notifications\Member\AccountStatusChangedNotification;
use App\Notifications\Admin\NewMemberCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs avec filtres et recherche.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        // Recherche textuelle
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtre de statut
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filtre de répertoire
        if ($directory = $request->input('directory')) {
            if ($directory === 'recenses') {
                $query->whereHas('groups', function ($q) {
                    $q->whereNull('group_members.left_at');
                });
            } elseif ($directory === 'hors_repertoire') {
                $query->whereDoesntHave('groups', function ($q) {
                    $q->whereNull('group_members.left_at');
                });
            }
        }

        // Pagination
        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        return view('admin.users.create');
    }

    /**
     * Sauvegarder un nouvel utilisateur.
     */
    public function store(CreateUserRequest $request)
    {
        $this->authorize('create', User::class);

        $tempPassword = Str::random(10);

        $user = new User([
            'name'       => $request->name,
            'first_name' => $request->first_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'birth_date' => $request->birth_date,
            'status'     => UserStatus::PENDING,
            'password'   => $tempPassword,
            'weekly_contribution' => $request->weekly_contribution,
            'church_service'      => $request->church_service,
            'additional_info'     => $request->additional_info,
        ]);

        $user->plain_password = $tempPassword;
        $user->save();

        // Notifier le nouveau membre
        $user->notify(new AccountCreatedNotification());

        // Notifier les admins
        if (\Spatie\Permission\Models\Role::where('name', 'Administrateur')->exists()) {
            User::role('Administrateur')->each(fn ($admin) => $admin->notify(new NewMemberCreatedNotification($user)));
        }

        return redirect()->route('admin.users.index')
            ->with('success', "L'utilisateur {$user->first_name} {$user->name} a été créé avec succès. Mot de passe temporaire : {$tempPassword}");
    }

    /**
     * Afficher les détails d'un utilisateur.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        // Calcul des cotisations annuelles (Février à Novembre)
        $startOfYear = \Carbon\Carbon::parse(date('Y') . '-02-01')->startOfDay();
        $endOfYear = \Carbon\Carbon::parse(date('Y') . '-11-30')->endOfDay();

        $totalSundaysInYear = 0;
        $dateIt = $startOfYear->copy()->next(\Carbon\Carbon::SUNDAY);
        if ($startOfYear->isSunday()) {
            $dateIt = $startOfYear->copy();
        }
        while ($dateIt->lte($endOfYear)) {
            $totalSundaysInYear++;
            $dateIt->addWeek();
        }

        $expectedContribution = $user->weekly_contribution ? $user->weekly_contribution * $totalSundaysInYear : 0;
        $paidContribution = $user->contributions()->whereBetween('date', [$startOfYear, $endOfYear])->sum('amount');

        return view('admin.users.show', compact('user', 'expectedContribution', 'paidContribution', 'totalSundaysInYear'));
    }

    /**
     * Afficher le formulaire d'édition.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        // Traitement de la photo avec intervention/image
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $file = $request->file('photo');
            $filename = 'photos/' . Str::uuid() . '.' . $file->getClientOriginalExtension();

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            $image->scaleDown(800, 800);

            Storage::disk('public')->put($filename, (string) $image->encode());
            $data['photo'] = $filename;
        }

        $oldStatus = $user->status->value;
        $user->update($data);

        // Notifier si le statut a changé
        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            $user->notify(new AccountStatusChangedNotification($data['status']));
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur (soft delete).
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->attendances()->count() > 0 || $user->registrations()->count() > 0) {
            // Soft delete
            $user->delete();
            $message = "L'utilisateur a été archivé (soft delete) car il possède des données liées.";
        } else {
            // Dans le cadre du ticket, on préfère toujours le soft delete.
            $user->delete();
            $message = "L'utilisateur a été supprimé.";
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    /**
     * Restaurer un utilisateur supprimé.
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('restore', $user);

        $user->restore();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur restauré avec succès.');
    }

    /**
     * Modifier le statut de plusieurs utilisateurs en masse.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $this->authorize('update', User::class);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:PENDING,ACTIVE,INACTIVE,SUSPENDED'
        ]);

        User::whereIn('id', $request->user_ids)->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Statut mis à jour pour les utilisateurs sélectionnés.');
    }

    /**
     * Exporter les utilisateurs en PDF — format professionnel.
     */
    public function export(Request $request)
    {
        $this->authorize('export', User::class);
        $query = User::with('roles', 'groups')->orderBy('name');

        $statusFilter = null;
        if ($status = $request->input('status')) {
            $query->where('status', $status);
            $statusFilter = $status;
        }

        $directoryFilter = null;
        if ($directory = $request->input('directory')) {
            if ($directory === 'recenses') {
                $query->whereHas('groups', function ($q) {
                    $q->whereNull('group_members.left_at');
                });
                $directoryFilter = 'Membres recensés';
            } elseif ($directory === 'hors_repertoire') {
                $query->whereDoesntHave('groups', function ($q) {
                    $q->whereNull('group_members.left_at');
                });
                $directoryFilter = 'Hors répertoire';
            }
        }

        $searchQuery = null;
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
            $searchQuery = $search;
        }

        $users = $query->get();

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

        $pdf = Pdf::loadView('admin.users.pdf', compact('users', 'statusFilter', 'searchQuery', 'directoryFilter', 'logoUeebBase64', 'logoJeunesseBase64'));
        return $pdf->download("Liste_Membres_" . now()->format('Ymd-His') . ".pdf");
    }
}
