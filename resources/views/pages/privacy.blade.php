<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de Confidentialité | {{ config('app.name') }}</title>
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
                    <h1 class="mb-4 fw-bold text-primary">Politique de Confidentialité</h1>
                    <p class="text-muted mb-5">Dernière mise à jour : {{ date('d/m/Y') }}</p>

                    <h4 class="fw-bold mt-4">1. Préambule</h4>
                    <p>
                        La présente politique de confidentialité a pour but d'informer les utilisateurs de la plateforme de la 
                        <strong>Jeunesse EBER</strong> (ci-après "la Plateforme") sur la manière dont leurs données personnelles 
                        sont collectées, traitées et protégées. 
                        Elle s'inscrit dans le strict respect de la <strong>Loi n° 2017-20 du 20 avril 2018 portant Code du numérique en République du Bénin</strong>, 
                        particulièrement en son Livre V relatif à la protection des données à caractère personnel, et des directives de l'<strong>Autorité de Protection des Données Personnelles (APDP)</strong>.
                    </p>

                    <h4 class="fw-bold mt-4">2. Les données que nous collectons</h4>
                    <p>Dans le cadre de la gestion de nos membres et de nos activités, nous sommes amenés à collecter les données suivantes :</p>
                    <ul>
                        <li><strong>Données d'identification :</strong> Nom, prénoms, date de naissance, profession/domaine d'études.</li>
                        <li><strong>Données de contact :</strong> Numéro de téléphone (WhatsApp), adresse email, commune et quartier de résidence.</li>
                        <li><strong>Données liées à la vie religieuse :</strong> Service dans l'église, groupe d'appartenance.</li>
                        <li><strong>Données techniques :</strong> Informations de connexion, logs d'audit sécurisés.</li>
                    </ul>

                    <h4 class="fw-bold mt-4">3. Finalité du traitement</h4>
                    <p>Vos données sont collectées exclusivement pour des finalités légitimes, explicites et déterminées, à savoir :</p>
                    <ul>
                        <li>La gestion de votre compte membre sur la plateforme.</li>
                        <li>Le suivi de votre participation aux activités (système de pointage par QR Code).</li>
                        <li>L'envoi de notifications et d'informations importantes concernant la jeunesse (via WhatsApp ou email).</li>
                        <li>La gestion administrative, statistique et financière (cotisations) en interne.</li>
                    </ul>

                    <h4 class="fw-bold mt-4">4. Sécurité et Confidentialité</h4>
                    <p>
                        Conformément à la réglementation de l'APDP, la Jeunesse EBER met en œuvre toutes les mesures techniques et 
                        organisationnelles nécessaires pour garantir la sécurité et la confidentialité de vos données personnelles 
                        contre la perte, l'accès non autorisé, la modification ou la divulgation.
                        <strong>Vos données ne sont ni vendues, ni louées, ni communiquées à des tiers à des fins commerciales.</strong>
                    </p>

                    <h4 class="fw-bold mt-4">5. Vos droits (Conformément aux directives de l'APDP)</h4>
                    <p>En tant qu'utilisateur, vous disposez des droits suivants sur vos données :</p>
                    <ul>
                        <li><strong>Droit d'accès et d'information :</strong> Vous pouvez demander à savoir quelles données nous détenons sur vous.</li>
                        <li><strong>Droit de rectification :</strong> Vous pouvez modifier vos informations inexactes depuis votre profil ou demander leur correction.</li>
                        <li><strong>Droit d'opposition et d'effacement :</strong> Sous réserve d'obligations légales, vous pouvez demander la suppression de votre compte et de vos données.</li>
                    </ul>
                    <p>Pour exercer ces droits, vous pouvez contacter directement le bureau de la Jeunesse ou l'Administrateur du système.</p>

                    <h4 class="fw-bold mt-4">6. Consentement</h4>
                    <p>
                        En créant un compte et en utilisant la Plateforme, vous consentez expressément et librement à la collecte et 
                        au traitement de vos données personnelles dans les conditions définies par la présente politique.
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
