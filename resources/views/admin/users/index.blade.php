@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des membres</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.export', request()->all()) }}"
               class="btn btn-outline-primary d-flex align-items-center gap-2"
               style="border-radius: 0.5rem;">
                <i class="mdi mdi-file-pdf-outline fs-16"></i>
                <span>Exporter PDF</span>
            </a>
            <a href="{{ route('admin.users.create') }}"
               class="btn btn-primary d-flex align-items-center gap-2"
               style="border-radius: 0.5rem;">
                <i class="mdi mdi-plus fs-16"></i>
                <span>Nouveau membre</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nom, prénom, téléphone...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>En attente</option>
                        <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Actif</option>
                        <option value="INACTIVE" {{ request('status') === 'INACTIVE' ? 'selected' : '' }}>Inactif</option>
                        <option value="SUSPENDED" {{ request('status') === 'SUSPENDED' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2 align-items-end mt-2 mt-md-0">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filtrer</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary flex-shrink-0">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table with Bulk Actions -->
    <form id="bulkStatusForm" action="{{ route('admin.users.bulk-status') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des membres</h5>
                <div class="d-flex align-items-center">
                    <select name="status" class="form-select form-select-sm me-2" required>
                        <option value="">Actions en masse...</option>
                        <option value="ACTIVE">Activer</option>
                        <option value="INACTIVE">Désactiver</option>
                        <option value="SUSPENDED">Suspendre</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary">Appliquer</button>
                </div>
            </div>
            <div class="card-body p-0">
                {{-- VUE DESKTOP --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </th>
                                <th>Photo</th>
                                <th>Nom complet</th>
                                <th>Contact</th>
                                <th>Statut</th>
                                <th>Inscrit le</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <input class="form-check-input user-checkbox" type="checkbox" name="user_ids[]" value="{{ $user->id }}">
                                </td>
                                <td>
                                    @if($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    {{ $user->first_name }} {{ $user->name }}
                                </td>
                                <td>
                                    {{ $user->email }}<br>
                                    <small class="text-muted">{{ $user->phone }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ match($user->status->value) {
                                        'ACTIVE' => 'success',
                                        'PENDING' => 'warning',
                                        'INACTIVE' => 'secondary',
                                        'SUSPENDED' => 'danger',
                                        default => 'primary'
                                    } }}">
                                        {{ $user->status->label() }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.permissions.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Permissions">
                                        <i class="mdi mdi-key"></i>
                                    </a>
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info" title="Voir">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary" title="Modifier">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $user->id }})" title="Supprimer">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Aucun membre trouvé.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- VUE MOBILE (Cartes) --}}
                <div class="d-md-none p-3">
                    <div class="d-flex flex-column gap-3">
                        @forelse($users as $user)
                        <div class="card border border-light shadow-sm mb-0">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <input class="form-check-input user-checkbox" type="checkbox" name="user_ids[]" value="{{ $user->id }}">
                                        @if($user->photo)
                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="badge bg-{{ match($user->status->value) {
                                        'ACTIVE' => 'success',
                                        'PENDING' => 'warning',
                                        'INACTIVE' => 'secondary',
                                        'SUSPENDED' => 'danger',
                                        default => 'primary'
                                    } }}">
                                        {{ $user->status->label() }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <h6 class="fw-bold mb-1">{{ $user->first_name }} {{ $user->name }}</h6>
                                    <div class="text-muted fs-13"><i class="mdi mdi-email-outline me-1"></i>{{ $user->email }}</div>
                                    <div class="text-muted fs-13"><i class="mdi mdi-phone-outline me-1"></i>{{ $user->phone }}</div>
                                </div>
                                <div class="d-flex gap-2 flex-wrap border-top pt-3">
                                    <a href="{{ route('admin.users.permissions.edit', $user) }}" class="btn btn-sm btn-outline-warning flex-grow-1" title="Permissions">
                                        <i class="mdi mdi-key me-1"></i>Perms
                                    </a>
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info flex-grow-1" title="Voir">
                                        <i class="mdi mdi-eye me-1"></i>Voir
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary flex-grow-1" title="Modifier">
                                        <i class="mdi mdi-pencil me-1"></i>Modif
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger flex-grow-1" onclick="confirmDelete({{ $user->id }})" title="Supprimer">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="mdi mdi-account-search fs-24 d-block mb-2"></i>
                            Aucun membre trouvé.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        </div>
    </form>
</div>

<!-- Forms for delete -->
@foreach($users as $user)
<form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endforeach

@endsection

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    // Intercepter la soumission du formulaire d'action en masse
    document.getElementById('bulkStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Vérifier si au moins un membre est sélectionné
        const selected = document.querySelectorAll('.user-checkbox:checked').length;
        if (selected === 0) {
            alert('Veuillez sélectionner au moins un membre.');
            return;
        }

        confirmAction(
            'Êtes-vous sûr de vouloir modifier le statut des utilisateurs sélectionnés ?',
            () => this.submit(),
            'Modifier le statut',
            'Appliquer',
            'btn-primary'
        );
    });

    function confirmDelete(id) {
        confirmAction(
            'Êtes-vous sûr de vouloir supprimer ce membre ? S\'il a des données liées, il sera archivé.',
            () => document.getElementById('delete-form-' + id).submit(),
            'Supprimer le membre',
            'Supprimer',
            'btn-danger'
        );
    }
</script>
@endpush
