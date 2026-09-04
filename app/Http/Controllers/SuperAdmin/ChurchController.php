<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Church;
use App\Models\Group;
use App\Models\Subscription;
use App\Models\User;
use App\Services\AuditService;
use App\Traits\OptimizesImages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChurchController extends Controller
{
    use OptimizesImages;

    /**
     * Liste de toutes les églises avec filtres et recherche.
     */
    public function index(Request $request)
    {
        $query = Church::withCount(['users', 'groups', 'activities']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('status', 'active')->where('subscription_expires_at', '>', Carbon::now());
            } elseif ($status === 'expired') {
                $query->where(function ($q) {
                    $q->where('subscription_expires_at', '<=', Carbon::now())
                      ->orWhere('status', 'expired');
                });
            } elseif ($status === 'suspended') {
                $query->where('status', 'suspended');
            }
        }

        $churches = $query->orderByDesc('created_at')->orderByDesc('id')->paginate(15)->withQueryString();

        $totalCount = Church::count();
        $activeCount = Church::where('status', 'active')->where('subscription_expires_at', '>', Carbon::now())->count();
        $expiredCount = Church::where(function ($q) {
            $q->where('subscription_expires_at', '<=', Carbon::now())
              ->orWhere('status', 'expired');
        })->count();

        return view('super-admin.churches.index', compact('churches', 'totalCount', 'activeCount', 'expiredCount'));
    }

    /**
     * Formulaire de création d'une nouvelle église cliente.
     */
    public function create()
    {
        return view('super-admin.churches.create');
    }

    /**
     * Enregistrement d'une nouvelle église + son abonnement 1 an initial + compte administrateur.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Infos Église
            'name'                => 'required|string|max:255',
            'email'               => 'nullable|email|max:255',
            'phone'               => 'nullable|string|max:50',
            'address'             => 'nullable|string|max:255',
            'city'                => 'nullable|string|max:100',
            'logo'                => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'subscription_amount' => 'required|numeric|min:0',
            'payment_method'      => 'required|string|max:100',
            'payment_reference'   => 'nullable|string|max:100',
            'notes'               => 'nullable|string',

            // Infos Administrateur Principal
            'admin_first_name'    => 'required|string|max:100',
            'admin_name'          => 'required|string|max:100',
            'admin_email'         => 'nullable|email|max:255|unique:users,email|required_without:admin_phone',
            'admin_phone'         => 'nullable|string|max:50|unique:users,phone|required_without:admin_email',
        ], [
            'name.required'                => 'Le nom de l\'église est obligatoire.',
            'admin_first_name.required'    => 'Le prénom de l\'administrateur est obligatoire.',
            'admin_name.required'          => 'Le nom de l\'administrateur est obligatoire.',
            'admin_email.email'            => 'L\'adresse email de l\'administrateur doit être valide.',
            'admin_email.unique'           => 'Cette adresse email est déjà utilisée par un autre utilisateur.',
            'admin_email.required_without' => 'L\'adresse email de l\'administrateur est obligatoire si le téléphone n\'est pas renseigné.',
            'admin_phone.unique'           => 'Ce numéro de téléphone est déjà utilisé par un autre utilisateur.',
            'admin_phone.required_without' => 'Le numéro de téléphone de l\'administrateur est obligatoire si l\'email n\'est pas renseigné.',
            'subscription_amount.required' => 'Le montant de l\'abonnement est obligatoire.',
            'payment_method.required'      => 'Le mode de paiement est obligatoire.',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Génération du slug unique et code (en vérifiant toutes les entrées y compris soft-deleted)
            $baseSlug = Str::slug($request->input('name'));
            if (empty($baseSlug)) {
                $baseSlug = 'eglise';
            }
            $slug = $baseSlug;
            $count = 1;
            while (Church::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }

            $code = 'CH-' . strtoupper(Str::random(5));
            while (Church::withTrashed()->where('code', $code)->exists()) {
                $code = 'CH-' . strtoupper(Str::random(5));
            }

            $startsAt = Carbon::now();
            $expiresAt = Carbon::now()->addYear(); // 1 an d'abonnement

            // 2. Création de l'église
            $church = Church::create([
                'name'                   => $request->input('name'),
                'slug'                   => $slug,
                'code'                   => $code,
                'email'                  => $request->input('email'),
                'phone'                  => $request->input('phone'),
                'address'                => $request->input('address'),
                'city'                   => $request->input('city'),
                'status'                 => 'active',
                'subscription_starts_at' => $startsAt,
                'subscription_expires_at' => $expiresAt,
                'subscription_amount'    => (int) $request->input('subscription_amount'),
                'subscription_plan'      => 'Annuel (1 an)',
                'notes'                  => $request->input('notes'),
            ]);

            // 3. Création de l'enregistrement de paiement d'abonnement
            Subscription::create([
                'church_id'         => $church->id,
                'starts_at'         => $startsAt,
                'expires_at'        => $expiresAt,
                'amount'            => (int) $request->input('subscription_amount'),
                'plan_name'         => 'Abonnement Annuel (1 an)',
                'payment_method'    => $request->input('payment_method'),
                'payment_reference' => $request->input('payment_reference') ?? 'REC-' . Carbon::now()->format('Ymd') . '-' . rand(100, 999),
                'status'            => 'active',
                'created_by'        => auth()->id(),
                'notes'             => 'Abonnement initial de création d\'église.',
            ]);

            // 3.1 Sauvegarde du logo de l'église si fourni
            if ($request->hasFile('logo')) {
                $logoPath = $this->optimizeAndStoreImage($request->file('logo'), 'churches');
                $church->logo_path = $logoPath;
                $church->save();
            }

            // 4. Création des paramètres par défaut de l'église
            AppSetting::create([
                'church_id'       => $church->id,
                'hero_title'      => 'Bienvenue à ' . $church->name,
                'hero_subtitle'   => 'Plateforme de gestion de la jeunesse et des activités.',
                'contact_phone'   => $church->phone,
                'pdf_logo_1'      => $church->logo_path,
            ]);

            // 4.1 Génération des rôles et permissions propres à cette église
            \Database\Seeders\RolesAndPermissionsSeeder::seedRolesForChurch($church->id);
            setPermissionsTeamId($church->id);

            // 5. Création du compte administrateur local de l'église avec génération automatique du mot de passe
            $tempPassword = Str::random(10);

            $user = new User([
                'church_id'   => $church->id,
                'first_name'  => $request->input('admin_first_name'),
                'name'        => $request->input('admin_name'),
                'email'       => $request->input('admin_email'),
                'phone'       => $request->input('admin_phone'),
                'password'    => $tempPassword,
                'status'      => 'ACTIVE',
            ]);

            $user->plain_password = $tempPassword;
            $user->save();

            // S'assurer que le rôle Administrateur propre à cette nouvelle église est assigné
            $adminRole = \Spatie\Permission\Models\Role::where('name', 'Administrateur')
                ->where('church_id', $church->id)
                ->first();
            if ($adminRole) {
                setPermissionsTeamId($church->id);
                $user->syncRoles([$adminRole]);

                // Double vérification dans la table de pivot model_has_roles
                DB::table('model_has_roles')->updateOrInsert(
                    [
                        'role_id'    => $adminRole->id,
                        'model_type' => User::class,
                        'model_id'   => $user->id,
                    ],
                    [
                        'church_id'  => $church->id,
                    ]
                );
                app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            }

            AuditService::log('created', $church, null, $church->toArray());

            return redirect()->route('super-admin.churches.show', $church)
                ->with('success', "L'église « {$church->name} » a été créée avec succès avec un abonnement actif de 1 an. Les identifiants de connexion ont été automatiquement envoyés par email à {$user->email}.");
        });
    }

    /**
     * Fiche complète d'une église cliente avec ses statistiques et historique d'abonnements.
     */
    public function show(Church $church)
    {
        $church->loadCount(['users', 'groups', 'activities']);
        $subscriptions = $church->subscriptions()->with('creator')->get();
        $admins = User::withoutGlobalScopes()
            ->where('users.church_id', $church->id)
            ->join('model_has_roles', function ($join) use ($church) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                     ->where('model_has_roles.model_type', '=', User::class)
                     ->where('model_has_roles.church_id', '=', $church->id);
            })
            ->join('roles', function ($join) use ($church) {
                $join->on('model_has_roles.role_id', '=', 'roles.id')
                     ->where('roles.name', '=', 'Administrateur')
                     ->where('roles.church_id', '=', $church->id);
            })
            ->select('users.*')
            ->distinct()
            ->get();

        if ($admins->isEmpty()) {
            $admins = User::withoutGlobalScopes()->where('church_id', $church->id)->take(2)->get();
        }

        return view('super-admin.churches.show', compact('church', 'subscriptions', 'admins'));
    }

    /**
     * Formulaire d'édition des coordonnées d'une église et de son administrateur principal.
     */
    public function edit(Church $church)
    {
        $admin = User::withoutGlobalScopes()
            ->where('users.church_id', $church->id)
            ->join('model_has_roles', function ($join) use ($church) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                     ->where('model_has_roles.model_type', '=', User::class)
                     ->where('model_has_roles.church_id', '=', $church->id);
            })
            ->join('roles', function ($join) use ($church) {
                $join->on('model_has_roles.role_id', '=', 'roles.id')
                     ->where('roles.name', '=', 'Administrateur')
                     ->where('roles.church_id', '=', $church->id);
            })
            ->select('users.*')
            ->first();

        if (!$admin) {
            $admin = User::withoutGlobalScopes()->where('church_id', $church->id)->first();
        }

        return view('super-admin.churches.edit', compact('church', 'admin'));
    }

    /**
     * Mise à jour des informations de l'église et de son administrateur.
     */
    public function update(Request $request, Church $church)
    {
        $admin = User::withoutGlobalScopes()
            ->where('users.church_id', $church->id)
            ->join('model_has_roles', function ($join) use ($church) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                     ->where('model_has_roles.model_type', '=', User::class)
                     ->where('model_has_roles.church_id', '=', $church->id);
            })
            ->join('roles', function ($join) use ($church) {
                $join->on('model_has_roles.role_id', '=', 'roles.id')
                     ->where('roles.name', '=', 'Administrateur')
                     ->where('roles.church_id', '=', $church->id);
            })
            ->select('users.*')
            ->first() ?? User::withoutGlobalScopes()->where('church_id', $church->id)->first();

        $adminId = $admin?->id;

        $request->validate([
            // Infos Église
            'name'                => 'required|string|max:255',
            'email'               => 'nullable|email|max:255',
            'phone'               => 'nullable|string|max:50',
            'address'             => 'nullable|string|max:255',
            'city'                => 'nullable|string|max:100',
            'logo'                => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'notes'               => 'nullable|string',

            // Infos Administrateur
            'admin_first_name'    => 'nullable|string|max:100',
            'admin_name'          => 'nullable|string|max:100',
            'admin_email'         => 'nullable|email|max:255|unique:users,email,' . ($adminId ?? 'NULL') . ',id|required_without:admin_phone',
            'admin_phone'         => 'nullable|string|max:50|unique:users,phone,' . ($adminId ?? 'NULL') . ',id|required_without:admin_email',
        ], [
            'name.required'                => 'Le nom de l\'église est obligatoire.',
            'admin_email.email'            => 'L\'adresse email de l\'administrateur doit être valide.',
            'admin_email.unique'           => 'Cette adresse email est déjà utilisée par un autre utilisateur.',
            'admin_email.required_without' => 'L\'adresse email de l\'administrateur est obligatoire si le téléphone n\'est pas renseigné.',
            'admin_phone.unique'           => 'Ce numéro de téléphone est déjà utilisé par un autre utilisateur.',
            'admin_phone.required_without' => 'Le numéro de téléphone de l\'administrateur est obligatoire si l\'email n\'est pas renseigné.',
        ]);

        return DB::transaction(function () use ($request, $church, $admin) {
            $oldChurch = $church->toArray();
            $updateData = $request->only(['name', 'email', 'phone', 'address', 'city', 'notes']);

            if ($request->hasFile('logo')) {
                $logoPath = $this->optimizeAndStoreImage($request->file('logo'), 'churches');
                $updateData['logo_path'] = $logoPath;

                AppSetting::updateOrCreate(
                    ['church_id' => $church->id],
                    ['pdf_logo_1' => $logoPath]
                );
            }

            $church->update($updateData);
            AuditService::log('updated', $church, $oldChurch, $church->toArray());

            // Mise à jour de l'administrateur s'il existe et que les données sont fournies
            if ($admin && ($request->filled('admin_name') || $request->filled('admin_first_name'))) {
                $oldAdmin = $admin->toArray();
                $admin->update([
                    'first_name' => $request->input('admin_first_name', $admin->first_name),
                    'name'       => $request->input('admin_name', $admin->name),
                    'email'      => $request->input('admin_email', $admin->email),
                    'phone'      => $request->input('admin_phone', $admin->phone),
                ]);
                AuditService::log('updated', $admin, $oldAdmin, $admin->toArray());
            }

            return redirect()->route('super-admin.churches.show', $church)
                ->with('success', "Les informations de l'église « {$church->name} » et de son administrateur ont été mises à jour avec succès.");
        });
    }

    /**
     * Suppression d'une église cliente (Soft Delete complet : Église, Administrateur/Utilisateurs, Abonnements, Groupes).
     */
    public function destroy(Church $church)
    {
        if ($church->id === 1 || $church->code === 'EBER-001') {
            return redirect()->back()->with('error', "L'église principale du système ne peut pas être supprimée.");
        }

        return DB::transaction(function () use ($church) {
            $churchName = $church->name;
            $old = $church->toArray();

            // 1. Suppression et désactivation de tous les administrateurs et utilisateurs de cette église
            $users = User::withoutGlobalScopes()->where('church_id', $church->id)->get();
            foreach ($users as $u) {
                $u->status = \App\Enums\UserStatus::SUSPENDED;
                // Libérer email et téléphone pour d'éventuelles futures réinscriptions
                if ($u->email) {
                    $u->email = $u->email . '-del-' . time() . '-' . $u->id;
                }
                if ($u->phone) {
                    $u->phone = $u->phone . '-del-' . time() . '-' . $u->id;
                }
                $u->save();
                $u->delete();
            }

            // 2. Suppression et annulation des abonnements de cette église
            Subscription::where('church_id', $church->id)->update([
                'status' => 'cancelled',
            ]);
            Subscription::where('church_id', $church->id)->delete();

            // 3. Suppression des groupes
            Group::withoutGlobalScopes()->where('church_id', $church->id)->delete();

            // 4. Libérer le slug et le code pour permettre la réutilisation future du nom
            $church->slug = $church->slug . '-del-' . time();
            if ($church->code) {
                $church->code = $church->code . '-DEL-' . time();
            }
            $church->status = 'suspended';
            $church->save();

            $church->delete();

            AuditService::log('deleted', $church, $old, null);

            return redirect()->route('super-admin.churches.index')
                ->with('success', "L'église « {$churchName} », ses administrateurs et ses abonnements ont été supprimés avec succès.");
        });
    }

    /**
     * Formulaire de renouvellement de l'abonnement pour 1 an.
     */
    public function renewForm(Church $church)
    {
        return view('super-admin.churches.renew', compact('church'));
    }

    /**
     * Enregistrement du renouvellement d'abonnement pour 1 an.
     */
    public function renew(Request $request, Church $church)
    {
        $request->validate([
            'amount'            => 'required|numeric|min:0',
            'payment_method'    => 'required|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
            'notes'             => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $church) {
            // Déterminer la date de début : si l'abonnement actuel est encore futur, on prolonge à partir de cette date, sinon à partir d'aujourd'hui
            $currentExpires = $church->subscription_expires_at;
            $startsAt = ($currentExpires && $currentExpires->isFuture()) ? $currentExpires : Carbon::now();
            $expiresAt = (clone $startsAt)->addYear(); // Ajout d'1 an

            // Mettre à jour l'église
            $old = $church->toArray();
            $church->update([
                'status'                 => 'active',
                'subscription_starts_at' => $startsAt,
                'subscription_expires_at' => $expiresAt,
                'subscription_amount'    => (int) $request->input('amount'),
            ]);

            // Enregistrer l'historique d'abonnement
            Subscription::create([
                'church_id'         => $church->id,
                'starts_at'         => $startsAt,
                'expires_at'        => $expiresAt,
                'amount'            => (int) $request->input('amount'),
                'plan_name'         => 'Renouvellement Annuel (1 an)',
                'payment_method'    => $request->input('payment_method'),
                'payment_reference' => $request->input('payment_reference') ?? 'RENEW-' . Carbon::now()->format('Ymd') . '-' . rand(100, 999),
                'status'            => 'active',
                'created_by'        => auth()->id(),
                'notes'             => $request->input('notes'),
            ]);

            AuditService::log('validated', $church, $old, $church->toArray());

            return redirect()->route('super-admin.churches.show', $church)
                ->with('success', "L'abonnement de « {$church->name} » a été renouvelé avec succès pour 1 an (jusqu'au " . $expiresAt->format('d/m/Y') . ").");
        });
    }

    /**
     * Activer ou Suspendre une église.
     */
    public function toggleStatus(Church $church)
    {
        $newStatus = ($church->status === 'active') ? 'suspended' : 'active';
        $old = $church->toArray();
        $church->status = $newStatus;
        $church->save();

        AuditService::log('updated', $church, $old, $church->toArray());

        $msg = ($newStatus === 'active') ? "L'église a été activée." : "L'accès de l'église a été suspendu.";

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Basculer dans le contexte d'une église cliente (Support technique Super Admin).
     */
    public function impersonate(Church $church)
    {
        session(['tenant_church_id' => $church->id]);
        return redirect()->route('dashboard')
            ->with('info', "Vous naviguez actuellement dans l'espace de « {$church->name} » en mode support.");
    }

    /**
     * Quitter le mode support et revenir au contexte global Super Admin.
     */
    public function leaveImpersonation()
    {
        session()->forget('tenant_church_id');
        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Vous êtes revenu au portail Super Administrateur.');
    }
}
