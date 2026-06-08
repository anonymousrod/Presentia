@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="mdi mdi-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="row">
        <!-- Main details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $activity->title }}</h4>
                    <span class="badge bg-{{ match($activity->status->value) {
                        'PUBLISHED' => 'success',
                        'DRAFT' => 'warning',
                        'CANCELLED' => 'danger',
                        'ARCHIVED' => 'secondary',
                        default => 'primary'
                    } }}">{{ $activity->status->value }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Description</h6>
                        <p class="text-muted">{!! nl2br(e($activity->description)) ?: '<em>Aucune description fournie</em>' !!}</p>
                    </div>

                    @if($activity->status === \App\Enums\ActivityStatus::CANCELLED && $activity->cancellation_reason)
                        <div class="alert alert-danger">
                            <h6><i class="mdi mdi-alert-circle"></i> Motif d'annulation :</h6>
                            <p class="mb-0">{{ $activity->cancellation_reason }}</p>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <strong>Date de début :</strong>
                            <p class="text-muted">{{ $activity->start_time->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date de fin :</strong>
                            <p class="text-muted">{{ $activity->end_time->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Lieu :</strong>
                            <p class="text-muted"><i class="mdi mdi-map-marker"></i> {{ $activity->location ?: 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Capacité :</strong>
                            <p class="text-muted">{{ $activity->capacity ? $activity->capacity . ' personnes max.' : 'Illimitée' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inscriptions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Membres inscrits ({{ $activity->registrations->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom Complet</th>
                                    <th>Email</th>
                                    <th>Date d'inscription</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activity->registrations as $reg)
                                <tr>
                                    <td>{{ $reg->user->first_name }} {{ $reg->user->name }}</td>
                                    <td>{{ $reg->user->email }}</td>
                                    <td>{{ $reg->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">Aucun membre inscrit pour le moment.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar details -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations complémentaires</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Type d'activité :</strong>
                        <p><span class="badge bg-soft-info text-info fs-12">{{ $activity->type->value }}</span></p>
                    </div>

                    <div class="mb-3">
                        <strong>Visibilité :</strong>
                        <p><span class="badge bg-soft-dark text-dark fs-12">{{ $activity->visibility->value }}</span></p>
                        @if($activity->visibility === \App\Enums\ActivityVisibility::GROUP)
                            <div class="text-muted"><i class="mdi mdi-account-group"></i> Groupe : {{ $activity->group?->name }}</div>
                        @elseif($activity->visibility === \App\Enums\ActivityVisibility::ROLE)
                            <div class="text-muted"><i class="mdi mdi-shield-account"></i> Rôle : {{ $activity->role?->name }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Responsable :</strong>
                        <p class="text-muted">
                            @if($activity->responsible)
                                {{ $activity->responsible->first_name }} {{ $activity->responsible->name }}
                            @else
                                Aucun
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <strong>QR Code Version :</strong>
                        <p class="text-muted">v{{ $activity->qr_version }}</p>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-primary">
                            <i class="mdi mdi-pencil"></i> Modifier l'activité
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="mdi mdi-trash-can"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>

            <!-- QR Code de Présence Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">QR Code d'Émargement</h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $qrUrl = session("activity_qr_url_{$activity->id}");
                        $qrExpires = session("activity_qr_expires_{$activity->id}");
                        $qrSvg = null;
                        if ($qrUrl) {
                            try {
                                $qrCode = new \Endroid\QrCode\QrCode(
                                    data: $qrUrl,
                                    size: 200,
                                    margin: 0
                                );
                                $writer = new \Endroid\QrCode\Writer\SvgWriter();
                                $result = $writer->write($qrCode);
                                $qrSvg = $result->getString();
                            } catch (\Exception $e) {
                                $qrSvg = null;
                            }
                        }
                    @endphp

                    @if($qrSvg)
                        <div class="d-flex justify-content-center mb-3">
                            <div style="
                                width: 200px;
                                height: 200px;
                                background: white;
                                padding: 10px;
                                border-radius: 8px;
                                border: 1px solid #e3e3e3;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                overflow: hidden;
                            ">
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                    {!! preg_replace('/<svg/', '<svg style="width:100%;height:100%;display:block;"', $qrSvg, 1) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Alpine.js countdown -->
                        <div x-data="qrcodeCountdown('{{ $qrExpires }}')" x-init="startTimer()" class="mb-3">
                            <span class="text-muted fs-12">Le QR Code expire dans :</span>
                            <div class="fw-bold fs-16 text-primary mt-1" x-text="formattedTime">--:--:--</div>
                            <div x-show="expired" class="alert alert-danger p-2 mt-2 mb-0 fs-12">
                                <i class="mdi mdi-alert-circle"></i> Le QR Code a expiré.
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.activities.qr.pdf', $activity) }}" class="btn btn-soft-success">
                                <i class="mdi mdi-download"></i> Télécharger le PDF
                            </a>
                            <form action="{{ route('admin.activities.qr.revoke', $activity) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir révoquer ce QR Code ? Toutes les signatures précédentes seront invalidées.');">
                                @csrf
                                <button type="submit" class="btn btn-soft-danger w-100">
                                    <i class="mdi mdi-cancel"></i> Révoquer / Désactiver
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="py-3">
                            <i class="mdi mdi-qrcode-scan fs-48 text-muted d-block mb-2"></i>
                            <p class="text-muted fs-13">Aucun QR Code d'émargement actif pour cette activité.</p>
                        </div>

                        <form action="{{ route('admin.activities.qr.generate', $activity) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="mdi mdi-qrcode"></i> Générer un QR Code
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" action="{{ route('admin.activities.destroy', $activity) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) {
            document.getElementById('delete-form').submit();
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('qrcodeCountdown', (expiryTimestamp) => ({
            expiry: parseInt(expiryTimestamp) * 1000,
            formattedTime: '00:00:00',
            expired: false,
            timer: null,
            startTimer() {
                this.updateTime();
                this.timer = setInterval(() => {
                    this.updateTime();
                }, 1000);
            },
            updateTime() {
                const now = new Date().getTime();
                const diff = this.expiry - now;
                if (diff <= 0) {
                    this.formattedTime = '00:00:00';
                    this.expired = true;
                    clearInterval(this.timer);
                    return;
                }
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                this.formattedTime = 
                    String(hours).padStart(2, '0') + ':' + 
                    String(minutes).padStart(2, '0') + ':' + 
                    String(seconds).padStart(2, '0');
            }
        }));
    });
</script>
@endpush

