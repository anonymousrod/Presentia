<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales | {{ config('app.name') }}</title>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
</head>
<body class="bg-light">
<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h1 class="mb-4 fw-bold text-primary">Mentions Légales</h1>

                    <h4 class="fw-bold mt-4">1. Éditeur de la Plateforme</h4>
                    <p>
                        La plateforme de gestion de la <strong>Jeunesse EBER</strong> est éditée par le bureau exécutif 
                        de la jeunesse, dans le but d'optimiser l'administration, le suivi des présences et la gestion 
                        financière de ses membres.
                    </p>

                    <h4 class="fw-bold mt-4">2. Propriété Intellectuelle</h4>
                    <p>
                        L'ensemble des éléments figurant sur cette plateforme (textes, logos, images, graphismes, icônes, code source) 
                        sont la propriété exclusive de la Jeunesse EBER ou font l'objet d'une autorisation d'utilisation. 
                        Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments de 
                        la plateforme, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable.
                    </p>

                    <h4 class="fw-bold mt-4">3. Protection des Données Personnelles</h4>
                    <p>
                        Soucieux du respect de votre vie privée, l'éditeur de la plateforme s'engage à ce que la collecte et 
                        le traitement de vos données soient conformes aux dispositions légales en vigueur en République du Bénin 
                        et aux recommandations de l'<strong>Autorité de Protection des Données Personnelles (APDP)</strong>.
                        <br>
                        Pour en savoir plus sur notre gestion de vos données, veuillez consulter notre 
                        <a href="{{ route('privacy') }}" class="text-primary fw-bold">Politique de Confidentialité</a>.
                    </p>

                    <h4 class="fw-bold mt-4">4. Responsabilité</h4>
                    <p>
                        La Jeunesse EBER s'efforce de fournir sur la plateforme des informations aussi précises que possible. 
                        Toutefois, elle ne pourra être tenue responsable des oublis, des inexactitudes et des carences dans la 
                        mise à jour, qu'elles soient de son fait ou du fait des tiers partenaires qui lui fournissent ces informations.
                    </p>
                    
                    <h4 class="fw-bold mt-4">5. Contact</h4>
                    <p>
                        Pour toute question ou demande d'information concernant le site, ou tout signalement de contenu ou 
                        d'activités illicites, l'utilisateur peut contacter l'administrateur via les canaux officiels de la jeunesse.
                    </p>

                    <div class="mt-5 text-center">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary"><i class="ri-arrow-left-line me-1"></i> Retour à l'accueil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
