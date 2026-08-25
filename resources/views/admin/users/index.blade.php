@extends('layouts.app')

@section('content')
<div class="container-fluid max-w-1200 py-3 py-md-4">
    {{-- =================== EN-TÊTE & ACTIONS =================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Tableau de bord</a></li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">Utilisateurs</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 fs-20 fs-md-24">Gestion des membres</h3>
            <p class="text-muted mb-0 fs-13 mt-1">Gérez tous les comptes, rôles et statuts depuis cet espace.</p>
        </div>
        <div class="d-flex gap-2">
            @can('member.export')
            <a href="{{ route('admin.users.export', request()->all()) }}"
               class="btn btn-soft-danger d-flex align-items-center gap-2 rounded-pill px-3 shadow-sm">
                <i class="mdi mdi-file-pdf-box fs-16"></i>
                <span class="d-none d-sm-inline">Exporter PDF</span>
            </a>
            @endcan
            @can('member.create')
            <a href="{{ route('admin.users.create') }}"
               class="btn btn-primary d-flex align-items-center gap-2 rounded-pill px-4 shadow-sm"
               style="background: linear-gradient(135deg, var(--vz-primary), #3b82f6);">
                <i class="mdi mdi-plus fs-16"></i>
                <span>Nouveau membre</span>
            </a>
            @endcan
        </div>
    </div>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert border-0 mb-4 d-flex align-items-center gap-3 p-3 shadow-sm rounded-3"
             style="background: rgba(var(--vz-success-rgb), 0.12); border-left: 4px solid var(--vz-success) !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:40px; height:40px; background: rgba(var(--vz-success-rgb), 0.2);">
                <i class="mdi mdi-check-circle fs-20" style="color: var(--vz-success);"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold text-success">Succès !</h6>
                <span class="fs-13 text-body">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- =================== FILTRES =================== --}}
    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
        <div class="card-header border-0 bg-light bg-opacity-50 py-3 px-4" data-bs-toggle="collapse" data-bs-target="#filterCollapse" style="cursor: pointer;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded p-1 bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="mdi mdi-filter-variant text-primary fs-18"></i>
                    </div>
                    <h6 class="mb-0 fw-semibold">Filtres de recherche</h6>
                </div>
                <i class="mdi mdi-chevron-down fs-20 text-muted"></i>
            </div>
        </div>
        <div id="filterCollapse" class="collapse show">
            <div class="card-body p-4 border-top border-light-subtle">
                <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1">Recherche</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-light-subtle"><i class="mdi mdi-magnify text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-light-subtle" value="{{ request('search') }}" placeholder="Nom, prénom, téléphone...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1">Statut</label>
                        <select name="status" class="form-select bg-light border-light-subtle">
                            <option value="">Tous les statuts</option>
                            <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>En attente</option>
                            <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Actif</option>
                            <option value="INACTIVE" {{ request('status') === 'INACTIVE' ? 'selected' : '' }}>Inactif</option>
                            <option value="SUSPENDED" {{ request('status') === 'SUSPENDED' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12 fw-semibold text-muted text-uppercase tracking-wider mb-1">Répertoire</label>
                        <select name="directory" class="form-select bg-light border-light-subtle">
                            <option value="">Tous les membres</option>
                            <option value="recenses" {{ request('directory') === 'recenses' ? 'selected' : '' }}>Membres recensés</option>
                            <option value="hors_repertoire" {{ request('directory') === 'hors_repertoire' ? 'selected' : '' }}>Hors répertoire</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="mdi mdi-filter me-1"></i> Filtrer</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-soft-secondary flex-shrink-0" title="Réinitialiser"><i class="mdi mdi-refresh"></i></a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- =================== LISTE DES UTILISATEURS =================== --}}
    <form id="bulkStatusForm" action="{{ route('admin.users.bulk-status') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header border-0 py-3 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="background: rgba(var(--vz-primary-rgb), 0.03); border-bottom: 1px solid rgba(var(--vz-primary-rgb), 0.1) !important;">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-format-list-bulleted fs-20 text-primary"></i>
                    <h5 class="mb-0 fw-bold fs-15 text-primary text-uppercase tracking-wider">Liste des Membres</h5>
                    <span class="badge bg-primary-subtle text-primary rounded-pill ms-2">{{ $users->total() }}</span>
                </div>
                
                <div class="d-flex align-items-center w-100 w-sm-auto">
                    @can('member.edit')
                    <div class="input-group input-group-sm">
                        <select name="status" class="form-select border-primary-subtle" required style="min-width: 150px;">
                            <option value="">Actions en masse...</option>
                            <option value="ACTIVE">Activer sélectionnés</option>
                            <option value="INACTIVE">Désactiver sélectionnés</option>
                            <option value="SUSPENDED">Suspendre sélectionnés</option>
                        </select>
                        <button type="submit" class="btn btn-primary"><i class="mdi mdi-check"></i></button>
                    </div>
                    @endcan
                </div>
            </div>
            
            <div class="card-body p-0">
                {{-- VUE DESKTOP --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle table-hover mb-0">
                        <thead style="background: rgba(var(--vz-light-rgb), 0.5);">
                            <tr>
                                <th class="ps-4" style="width: 40px;">
                                    @can('member.edit')
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                    @endcan
                                </th>
                                <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Membre</th>
                                <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Contact</th>
                                <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Statut</th>
                                <th class="fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Inscrit le</th>
                                <th class="text-end pe-4 fw-semibold text-muted fs-12 text-uppercase tracking-wider py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="border-bottom">
                                <td class="ps-4">
                                    @can('member.edit')
                                    <div class="form-check">
                                        <input class="form-check-input user-checkbox" type="checkbox" name="user_ids[]" value="{{ encode_id($user->id) }}">
                                    </div>
                                    @endcan
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($user->photo)
                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="rounded-circle shadow-sm border" width="40" height="40" style="object-fit: cover; flex-shrink:0;">
                                        @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0" 
                                                 style="width: 40px; height: 40px; background: rgba(var(--vz-primary-rgb), 0.1); color: var(--vz-primary); font-size: 0.9rem;">
                                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 fw-semibold fs-14">{{ $user->first_name }} {{ $user->name }}</h6>
                                            @if($user->hasRole('admin'))
                                                <span class="badge bg-danger-subtle text-danger rounded-pill mt-1" style="font-size:0.65rem;"><i class="mdi mdi-shield-account me-1"></i>Admin</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="fs-13"><i class="mdi mdi-email-outline text-muted me-1"></i> {{ $user->email ?? '—' }}</div>
                                        <div class="fs-13 text-muted"><i class="mdi mdi-phone-outline me-1"></i> {{ $user->phone ?? '—' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ match($user->status->value) {
                                        'ACTIVE' => 'success',
                                        'PENDING' => 'warning',
                                        'INACTIVE' => 'secondary',
                                        'SUSPENDED' => 'danger',
                                        default => 'primary'
                                    } }} rounded-pill px-3 py-1 fs-11 uppercase tracking-wider">
                                        {{ $user->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fs-13 text-muted"><i class="mdi mdi-calendar text-muted me-1"></i>{{ $user->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        @can('role.manage')
                                        <a href="{{ route('admin.users.permissions.edit', $user) }}" class="btn btn-sm btn-icon btn-soft-warning rounded-circle" title="Permissions">
                                            <i class="mdi mdi-key fs-15"></i>
                                        </a>
                                        @endcan
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-icon btn-soft-info rounded-circle" title="Voir profil">
                                            <i class="mdi mdi-eye fs-15"></i>
                                        </a>
                                        @can('member.edit')
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-icon btn-soft-primary rounded-circle" title="Modifier">
                                            <i class="mdi mdi-pencil fs-15"></i>
                                        </a>
                                        @endcan
                                        @can('member.delete')
                                        <button type="button" class="btn btn-sm btn-icon btn-soft-danger rounded-circle" onclick="confirmDelete({{ $user->id }})" title="Supprimer">
                                            <i class="mdi mdi-trash-can fs-15"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center gap-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:60px; height:60px;">
                                            <i class="mdi mdi-account-search-outline fs-24 text-muted"></i>
                                        </div>
                                        <p class="text-muted mb-0 fs-14">Aucun membre ne correspond à vos critères.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- VUE MOBILE (Cartes) --}}
                <div class="d-md-none px-3 py-2">
                    <div class="d-flex flex-column gap-3">
                        @forelse($users as $user)
                        <div class="card shadow-none border border-light-subtle rounded-3 mb-0">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        @can('member.edit')
                                        <div class="form-check mb-0">
                                            <input class="form-check-input user-checkbox" type="checkbox" name="user_ids[]" value="{{ encode_id($user->id) }}">
                                        </div>
                                        @endcan
                                        @if($user->photo)
                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="rounded-circle shadow-sm border" width="48" height="48" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                                 style="width: 48px; height: 48px; background: rgba(var(--vz-primary-rgb), 0.1); color: var(--vz-primary); font-size: 1.1rem;">
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
                                    } }} rounded-pill px-2 py-1 fs-10 uppercase tracking-wider">
                                        {{ $user->status->label() }}
                                    </span>
                                </div>
                                <div class="mb-3 ps-{{ auth()->user()->can('member.edit') ? '4' : '0' }} ms-{{ auth()->user()->can('member.edit') ? '3' : '0' }}">
                                    <h6 class="fw-bold mb-1 fs-15">{{ $user->first_name }} {{ $user->name }}</h6>
                                    <div class="bg-light rounded-2 p-2 mt-2 fs-13 text-muted d-flex flex-column gap-1">
                                        @if($user->email)
                                        <div><i class="mdi mdi-email-outline me-1"></i>{{ $user->email }}</div>
                                        @endif
                                        @if($user->phone)
                                        <div><i class="mdi mdi-phone-outline me-1"></i>{{ $user->phone }}</div>
                                        @endif
                                        <div><i class="mdi mdi-calendar-check me-1"></i>{{ $user->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 flex-wrap border-top border-light-subtle pt-3">
                                    @can('role.manage')
                                    <a href="{{ route('admin.users.permissions.edit', $user) }}" class="btn btn-sm btn-soft-warning flex-grow-1 rounded-3">
                                        <i class="mdi mdi-key me-1"></i>Perms
                                    </a>
                                    @endcan
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-soft-info flex-grow-1 rounded-3">
                                        <i class="mdi mdi-eye me-1"></i>Voir
                                    </a>
                                    @can('member.edit')
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-soft-primary flex-grow-1 rounded-3">
                                        <i class="mdi mdi-pencil me-1"></i>Modif
                                    </a>
                                    @endcan
                                    @can('member.delete')
                                    <button type="button" class="btn btn-sm btn-soft-danger flex-grow-1 rounded-3" onclick="confirmDelete({{ $user->id }})">
                                        <i class="mdi mdi-trash-can"></i>
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 text-muted">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px; height:60px;">
                                <i class="mdi mdi-account-search-outline fs-24 text-muted"></i>
                            </div>
                            Aucun membre trouvé.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <div class="card-footer border-0 bg-transparent px-4 pb-4 pt-2">
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
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    // Intercepter la soumission du formulaire d'action en masse
    document.getElementById('bulkStatusForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Vérifier si au moins un membre est sélectionné
        const selected = document.querySelectorAll('.user-checkbox:checked').length;
        if (selected === 0) {
            alert('Veuillez sélectionner au moins un membre.');
            return;
        }

        const action = this.querySelector('select[name="status"]').value;
        if (!action) return;

        confirmAction(
            'Êtes-vous sûr de vouloir modifier le statut des ' + selected + ' utilisateur(s) sélectionné(s) ?',
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
