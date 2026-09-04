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
use App\Traits\OptimizesImages;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    use OptimizesImages;

    /**
     * Liste des utilisateurs avec filtres et recherche.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        // Scope Multi-Tenant : Restreindre strictement aux utilisateurs de l'église active
        if (auth()->check()) {
            $authUser = auth()->user();
            $activeChurchId = session('tenant_church_id') ?? $authUser->church_id;
            if ($activeChurchId) {
                $query->where('church_id', $activeChurchId);
            }
        }

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

        // Notifier les admins DE LA MEME EGLISE uniquement
        $churchId = session('tenant_church_id') ?? auth()->user()?->church_id ?? null;
        if (\Spatie\Permission\Models\Role::where('name', 'Administrateur')->where('church_id', $churchId)->exists()) {
            User::role('Administrateur')
                ->when($churchId, fn ($q) => $q->where('church_id', $churchId))
                ->each(fn ($admin) => $admin->notify(new NewMemberCreatedNotification($user)));
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

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $data['photo'] = $this->optimizeAndStoreImage($request->file('photo'), 'photos');
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

        if ($request->has('user_ids')) {
            $request->merge([
                'user_ids' => array_map(function ($id) {
                    return decode_id($id);
                }, (array) $request->user_ids)
            ]);
        }

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
        $churchId = session('tenant_church_id') ?? auth()->user()?->church_id ?? null;

        $query = User::with('roles', 'groups')
            ->when($churchId, fn ($q) => $q->where('church_id', $churchId))
            ->orderBy('name');

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

        $churchId = session('tenant_church_id') ?? auth()->user()?->church_id;
        $church = $churchId ? \App\Models\Church::find($churchId) : auth()->user()?->church;
        if (!$church) {
            $church = \App\Models\Church::first();
        }

        $settings = $church ? \App\Models\AppSetting::where('church_id', $church->id)->first() : null;
        if (!$settings) {
            $settings = \App\Models\AppSetting::find(1);
        }

        $logo1Path = $settings?->pdf_logo_1 ?: ($church?->logo_path ?: ($settings?->logo_sm ?: 'assets/images/home/church-default.svg'));
        $logo2Path = $settings?->pdf_logo_2 ?: ($settings?->logo_dark ?: 'assets/images/logo-dark.png');

        $logoUeebBase64 = $this->getLogoBase64($logo1Path);
        $logoJeunesseBase64 = $this->getLogoBase64($logo2Path);

        $pdf = Pdf::loadView('admin.users.pdf', compact('users', 'statusFilter', 'searchQuery', 'directoryFilter', 'logoUeebBase64', 'logoJeunesseBase64', 'church'));
        $churchSlug = $church ? Str::slug($church->name) . '_' : '';
        return $pdf->download("Liste_Membres_{$churchSlug}" . now()->format('Ymd-His') . ".pdf");
    }

    private function getLogoBase64(?string $path): string
    {
        if (!$path) {
            return '';
        }

        $fullPath = null;
        if (file_exists(public_path($path))) {
            $fullPath = public_path($path);
        } elseif (file_exists(public_path('storage/' . $path))) {
            $fullPath = public_path('storage/' . $path);
        } elseif (file_exists(storage_path('app/public/' . $path))) {
            $fullPath = storage_path('app/public/' . $path);
        } elseif (file_exists(public_path('assets/images/' . basename($path)))) {
            $fullPath = public_path('assets/images/' . basename($path));
        }

        if ($fullPath && file_exists($fullPath)) {
            $mime = @mime_content_type($fullPath) ?: 'image/png';
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
        }

        return '';
    }
}
