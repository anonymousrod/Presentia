<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vos identifiants de connexion Presentia</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            color: #1f2937;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        .header {
            background-color: #6366f1;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
        }
        .credentials-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .credentials-item {
            margin-bottom: 10px;
        }
        .credentials-item:last-child {
            margin-bottom: 0;
        }
        .label {
            font-weight: 600;
            color: #4b5563;
            display: inline-block;
            width: 150px;
        }
        .value {
            font-family: monospace;
            font-size: 15px;
            color: #111827;
            background-color: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            background-color: #6366f1;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 15px;
            text-align: center;
        }
        .btn:hover {
            background-color: #4f46e5;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue sur Presentia</h1>
        </div>
        <div class="content">
            <p>Bonjour {{ $user->first_name }},</p>
            
            <p>Votre compte utilisateur sur la plateforme <strong>Presentia</strong> a été créé avec succès par l'administrateur.</p>
            
            <p>Voici vos identifiants temporaires pour vous connecter :</p>
            
            <div class="credentials-box">
                <div class="credentials-item">
                    <span class="label">Adresse Email :</span>
                    <span class="value">{{ $user->email }}</span>
                </div>
                <div class="credentials-item">
                    <span class="label">Mot de passe :</span>
                    <span class="value">{{ $plainPassword }}</span>
                </div>
            </div>
            
            <p><strong>Remarque importante :</strong> Par mesure de sécurité, votre compte a été créé avec le statut <strong>En attente</strong>. Vous serez invité à modifier obligatoirement ce mot de passe temporaire lors de votre première connexion.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="btn">Me connecter maintenant</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Presentia - Plateforme EBER. Tous droits réservés.
        </div>
    </div>
</body>
</html>
