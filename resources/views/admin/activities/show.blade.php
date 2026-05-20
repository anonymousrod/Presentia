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
</script>
@endpush
