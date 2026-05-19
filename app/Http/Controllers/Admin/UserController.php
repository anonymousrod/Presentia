<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserStatus;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        // 1. Générer un mot de passe temporaire de 10 caractères alphanumériques
        $tempPassword = Str::random(10);

        // 2. Initialiser le modèle utilisateur
        $user = new User([
            'name'       => $request->name,
            'first_name' => $request->first_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'birth_date' => $request->birth_date,
            'status'     => UserStatus::PENDING, // Statut PENDING par défaut
            'password'   => $tempPassword,        // Hashé automatiquement par le cast du modèle
        ]);

        // 3. Passer le mot de passe en clair à l'Observer via une propriété temporaire
        $user->plain_password = $tempPassword;
        
        // 4. Sauvegarder (déclenche UserObserver::created)
        $user->save();

        // 5. Redirection avec un message flash de succès
        return redirect()->route('admin.users.create')
            ->with('success', "L'utilisateur {$user->first_name} {$user->name} a été créé avec succès. Mot de passe temporaire : {$tempPassword}");
    }
}
