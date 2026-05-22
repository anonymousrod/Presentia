@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card overflow-hidden">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0 text-white"><i class="mdi mdi-qrcode-scan me-2"></i>Scanner de Présence</h4>
            </div>
            <div class="card-body p-4 text-center">
                <div id="scanner-view">
                    <p class="text-muted mb-4">Placez le QR Code de l'activité dans le cadre ci-dessous pour valider votre présence.</p>
                    
                    <div id="reader-container" class="position-relative mb-4">
                        <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto; border-radius: 12px; overflow: hidden; border: 2px solid #eff2f7;"></div>
                        <div id="scan-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-none" style="background: rgba(255,255,255,0.7); z-index: 10; display: flex; align-items: center; justify-content: center;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Validation...</span>
                            </div>
                        </div>
                    </div>

                    <div id="scanning-status" class="mt-3">
                        <span class="badge rounded-pill bg-info-subtle text-info p-2 px-3 fs-13">
                            <i class="mdi mdi-camera me-1"></i>Initialisation de la caméra...
                        </span>
                    </div>
                </div>

                <div id="scan-result" class="d-none mt-3 animate__animated animate__fadeIn">
                    <div class="avatar-lg mx-auto mb-4">
                        <div class="avatar-title bg-success-subtle text-success display-3 rounded-circle" id="result-icon">
                            <i class="mdi mdi-check-circle-outline"></i>
                        </div>
                    </div>
                    
                    <h4 class="mb-3" id="result-title">Succès !</h4>
                    <div id="result-details" class="bg-light p-3 rounded-3 mb-4 text-start">
                        <!-- Details will be injected here -->
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="mdi mdi-view-dashboard-outline me-1"></i>Retour au tableau de bord
                        </a>
                        <button onclick="window.location.reload()" class="btn btn-outline-secondary" id="btn-retry">
                            <i class="mdi mdi-refresh me-1"></i>Scanner à nouveau
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const html5QrCode = new Html5Qrcode("reader");
        const scanOverlay = document.getElementById('scan-overlay');
        const scannerView = document.getElementById('scanner-view');
        const scanResult = document.getElementById('scan-result');
        const resultIcon = document.getElementById('result-icon');
        const resultTitle = document.getElementById('result-title');
        const resultDetails = document.getElementById('result-details');
        const btnRetry = document.getElementById('btn-retry');

        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            console.log(`Code matched = ${decodedText}`, decodedResult);
            
            // On affiche l'overlay de chargement
            scanOverlay.classList.remove('d-none');
            
            // On arrête le scanner
            html5QrCode.stop().then(() => {
                validateAttendance(decodedText);
            }).catch((err) => {
                console.warn("Erreur lors de l'arrêt du scanner", err);
                validateAttendance(decodedText);
            });
        };
        
        const config = { fps: 15, qrbox: { width: 250, height: 250 } };

        // Démarrage de la caméra
        if (window.isSecureContext) {
            html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
                .then(() => {
                    document.getElementById('scanning-status').innerHTML = `
                        <span class="badge rounded-pill bg-success-subtle text-success p-2 px-3 fs-13">
                            <i class="mdi mdi-record-circle-outline me-1"></i>Scanner actif
                        </span>
                    `;
                })
                .catch((err) => {
                    console.error("Impossible de démarrer la caméra", err);
                    let message = "Erreur d'accès à la caméra.";
                    if (err.includes("NotAllowedError") || err.includes("Permission denied")) {
                        message = "Accès à la caméra refusé. Veuillez autoriser la caméra dans les paramètres de votre navigateur.";
                    }
                    document.getElementById('scanning-status').innerHTML = `
                        <div class="alert alert-danger mb-0 mt-2" role="alert">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> ${message}
                        </div>
                    `;
                });
        } else {
            document.getElementById('scanning-status').innerHTML = `
                <div class="alert alert-warning mb-0 mt-2" role="alert">
                    <h5 class="alert-heading fs-14"><i class="mdi mdi-shield-lock-outline me-1"></i> Connexion non sécurisée</h5>
                    <p class="mb-0 fs-12">Le scanner nécessite une connexion <strong>HTTPS</strong> pour accéder à la caméra (sauf sur localhost).</p>
                </div>
            `;
        }

        function validateAttendance(url) {
            // Extraction des paramètres si c'est une URL complète
            let targetUrl = url;
            
            // On vérifie que c'est une URL de validation
            if (!url.includes('/attendance/validate') && !url.includes('/attendance/scan')) {
                showFinalResult(false, "QR Code Invalide", "Ce QR Code n'est pas reconnu par le système de présence.");
                return;
            }

            // Envoi de la requête AJAX POST
            fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.status === 403) {
                    return response.json().then(data => { throw new Error(data.message || "Lien expiré ou invalide"); });
                }
                if (!response.ok) {
                    throw new Error("Erreur serveur (" + response.status + ")");
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    let statusLabel = data.data.status === 'PRESENT' ? 
                        '<span class="badge bg-success">PRÉSENT (À l\'heure)</span>' : 
                        '<span class="badge bg-warning text-dark">EN RETARD</span>';
                    
                    let detailsHtml = `
                        <p class="mb-1"><strong>${data.message}</strong></p>
                        <hr class="my-2">
                        <p class="mb-1"><i class="mdi mdi-bookmark-outline me-2"></i><strong>Activité :</strong> ${data.data.activity}</p>
                        <p class="mb-1"><i class="mdi mdi-clock-outline me-2"></i><strong>Heure :</strong> ${data.data.scanned_at}</p>
                        <p class="mb-0"><i class="mdi mdi-check-decagram-outline me-2"></i><strong>Statut :</strong> ${statusLabel}</p>
                    `;
                    showFinalResult(true, "Présence Validée", detailsHtml);
                } else {
                    showFinalResult(false, "Échec", data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showFinalResult(false, "Erreur", error.message);
            });
        }

        function showFinalResult(isSuccess, title, contentHtml) {
            scannerView.classList.add('d-none');
            scanResult.classList.remove('d-none');
            
            resultTitle.innerText = title;
            resultDetails.innerHTML = contentHtml;
            
            if (isSuccess) {
                resultIcon.className = "avatar-title bg-success-subtle text-success display-3 rounded-circle";
                resultIcon.innerHTML = '<i class="mdi mdi-check-circle-outline"></i>';
                resultTitle.className = "mb-3 text-success";
                btnRetry.classList.add('d-none');
            } else {
                resultIcon.className = "avatar-title bg-danger-subtle text-danger display-3 rounded-circle";
                resultIcon.innerHTML = '<i class="mdi mdi-alert-circle-outline"></i>';
                resultTitle.className = "mb-3 text-danger";
                btnRetry.classList.remove('d-none');
            }
        }
    });
</script>
@endpush
