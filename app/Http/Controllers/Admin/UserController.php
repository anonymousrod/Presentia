<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserStatus;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
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

        // Pagination
        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Sauvegarder un nouvel utilisateur.
     */
    public function store(CreateUserRequest $request)
    {
        $tempPassword = Str::random(10);

        $user = new User([
            'name'       => $request->name,
            'first_name' => $request->first_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'birth_date' => $request->birth_date,
            'status'     => UserStatus::PENDING,
            'password'   => $tempPassword,
        ]);

        $user->plain_password = $tempPassword;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "L'utilisateur {$user->first_name} {$user->name} a été créé avec succès. Mot de passe temporaire : {$tempPassword}");
    }

    /**
     * Afficher les détails d'un utilisateur.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Afficher le formulaire d'édition.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
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

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur (soft delete).
     */
    public function destroy(User $user)
    {
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
        $user->restore();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur restauré avec succès.');
    }

    /**
     * Modifier le statut de plusieurs utilisateurs en masse.
     */
    public function bulkUpdateStatus(Request $request)
    {
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
        $query = User::with('roles', 'groups')->orderBy('name');

        $statusFilter = null;
        if ($status = $request->input('status')) {
            $query->where('status', $status);
            $statusFilter = $status;
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

        $pdf = Pdf::loadView('admin.users.pdf', compact('users', 'statusFilter', 'searchQuery', 'logoUeebBase64', 'logoJeunesseBase64'));
        return $pdf->download("Liste_Membres_" . now()->format('Ymd-His') . ".pdf");
    }
}
