<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();

            $table->string('favicon')->nullable()
                ->comment("L'icône du site affichée dans l'onglet du navigateur (Favicon). Valeur par défaut : Icone J-EBER.png.");

            $table->string('logo_sm')->nullable()
                ->comment("Logo réduit (pour la sidebar réduite et le header mobile).");

            $table->string('logo_dark')->nullable()
                ->comment("Logo principal (version sombre pour thèmes clairs). Valeur par défaut : Icone J-EBER.png.");

            $table->string('logo_light')->nullable()
                ->comment("Logo principal (version claire pour thèmes sombres). Valeur par défaut : Icone J-EBER.png.");

            $table->string('pdf_logo_1')->nullable()
                ->comment("Logo UEEB utilisé dans les exports PDF (Présences, Utilisateurs).");

            $table->string('pdf_logo_2')->nullable()
                ->comment("Logo Jeunesse Étoile Rouge utilisé dans les exports PDF.");

            $table->string('default_avatar')->nullable()
                ->comment("Image de profil par défaut pour un nouvel utilisateur (au lieu de avatar-1.jpg).");

            $table->string('default_cover')->nullable()
                ->comment("Image de couverture de profil par défaut (au lieu de profile-bg.jpg).");

            $table->string('sidebar_bg_1')->nullable()
                ->comment("Image d'arrière-plan 1 proposée pour la sidebar dans la personnalisation du thème.");

            $table->string('sidebar_bg_2')->nullable()
                ->comment("Image d'arrière-plan 2 proposée pour la sidebar dans la personnalisation du thème.");

            $table->string('sidebar_bg_3')->nullable()
                ->comment("Image d'arrière-plan 3 proposée pour la sidebar dans la personnalisation du thème.");

            $table->string('sidebar_bg_4')->nullable()
                ->comment("Image d'arrière-plan 4 proposée pour la sidebar dans la personnalisation du thème.");

            $table->string('auth_bg')->nullable()
                ->comment("Image d'arrière-plan de la page de connexion, mot de passe oublié et réinitialisation.");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
