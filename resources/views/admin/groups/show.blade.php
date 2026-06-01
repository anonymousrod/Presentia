@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Breadcrumbs & Header --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.groups.index') }}">Groupes</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $group->name }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h1 class="h2 mb-0 text-dark font-weight-bold">{{ $group->name }}</h1>
                @if($group->category)
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill font-weight-semibold">
                        <i class="mdi mdi-tag-outline me-1"></i>{{ $group->category }}
                    </span>
                @endif
            </div>
            <p class="text-muted mb-0 mt-1">Gérer les membres, le chef et l'historique du groupe.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.groups.index') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Retour
            </a>
            <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-primary">
                <i class="mdi mdi-pencil me-1"></i> Modifier
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Column: Active Members & History --}}
        <div class="col-lg-8">
            {{-- Active Members Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                    <h5 class="card-title mb-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-account-group text-primary me-2 fs-4"></i>
                        Membres Actifs ({{ $activeMembers->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Membre</th>
                                    <th>Contact</th>
                                    <th>Rejoint le</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeMembers as $member)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($member->photo)
                                                <img src="{{ asset('storage/' . $member->photo) }}" alt="Photo" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center font-weight-bold" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                                    {{ strtoupper(substr($member->first_name, 0, 1) . substr($member->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-weight-semibold text-dark">{{ $member->first_name }} {{ $member->name }}</div>
                                                @if($group->leader_id === $member->id)
                                                    <span class="badge bg-info text-dark font-weight-normal" style="font-size: 0.75rem;">
                                                        <i class="mdi mdi-crown me-1"></i>Chef de groupe
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark" style="font-size: 0.9rem;">{{ $member->email ?? '—' }}</div>
                                        <small class="text-muted">{{ $member->phone ?? '—' }}</small>
                                    </td>
                                    <td>
                                        <div class="text-dark" style="font-size: 0.9rem;">
                                            {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d/m/Y H:i') : '—' }}
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('admin.groups.members.remove', [$group, $member]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm border-0" title="Retirer du groupe"
                                                onclick="return confirm('Retirer {{ $member->first_name }} {{ $member->name }} du groupe ? Cette action sera historisée.')">
                                                <i class="mdi mdi-account-minus fs-5"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="mdi mdi-account-off-outline fs-1 d-block mb-2 text-muted opacity-50"></i>
                                        Aucun membre actif dans ce groupe.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- History Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="card-title mb-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-history text-secondary me-2 fs-4"></i>
                        Historique des Membres
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Membre</th>
                                    <th>Rejoint le</th>
                                    <th>Quitté le</th>
                                    <th class="pe-4">Durée</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allMembers as $pastMember)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="font-weight-semibold text-dark">{{ $pastMember->first_name }} {{ $pastMember->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $pastMember->pivot->joined_at ? \Carbon\Carbon::parse($pastMember->pivot->joined_at)->format('d/m/Y H:i') : '—' }}
                                    </td>
                                    <td>
                                        <span class="text-danger">
                                            {{ $pastMember->pivot->left_at ? \Carbon\Carbon::parse($pastMember->pivot->left_at)->format('d/m/Y H:i') : '—' }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-muted">
                                        @if($pastMember->pivot->joined_at && $pastMember->pivot->left_at)
                                            @php
                                                $joined = \Carbon\Carbon::parse($pastMember->pivot->joined_at);
                                                $left = \Carbon\Carbon::parse($pastMember->pivot->left_at);
                                                $diff = $joined->diffForHumans($left, true, false, 2);
                                            @endphp
                                            {{ $diff }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Aucun historique d'ancien membre.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Group Details & Assignment Form --}}
        <div class="col-lg-4">
            {{-- Group Info Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="font-weight-bold text-dark mb-3">Informations</h5>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block uppercase tracking-wider font-weight-bold" style="font-size: 0.75rem;">Description</small>
                        <p class="text-dark mb-0 mt-1" style="font-size: 0.95rem; white-space: pre-line;">
                            {{ $group->description ?? 'Aucune description fournie.' }}
                        </p>
                    </div>

                    <hr class="my-3 text-muted opacity-25">

                    <div class="mb-3">
                        <small class="text-muted d-block uppercase tracking-wider font-weight-bold" style="font-size: 0.75rem;">Créé le</small>
                        <div class="text-dark mt-1" style="font-size: 0.95rem;">
                            {{ $group->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Group Leader Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="font-weight-bold text-dark mb-3">Chef de groupe</h5>
                    @if($group->leader)
                        <div class="d-flex align-items-center gap-3">
                            @if($group->leader->photo)
                                <img src="{{ asset('storage/' . $group->leader->photo) }}" alt="Photo" class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-soft-info text-info d-flex align-items-center justify-content-center font-weight-bold" style="width: 50px; height: 50px; font-size: 1.1rem;">
                                    {{ strtoupper(substr($group->leader->first_name, 0, 1) . substr($group->leader->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0 font-weight-bold text-dark">{{ $group->leader->first_name }} {{ $group->leader->name }}</h6>
                                <span class="badge bg-soft-info text-info mt-1 font-weight-normal" style="font-size: 0.75rem;">Chef Désigné</span>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-top border-light">
                            <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 0.9rem;">
                                <i class="mdi mdi-email text-muted"></i>
                                <span class="text-dark">{{ $group->leader->email ?? '—' }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                                <i class="mdi mdi-phone text-muted"></i>
                                <span class="text-dark">{{ $group->leader->phone ?? '—' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="mdi mdi-crown-outline fs-1 d-block mb-2 text-muted opacity-50"></i>
                            Aucun chef de groupe désigné.
                            <div class="mt-2">
                                <a href="{{ route('admin.groups.edit', $group) }}" class="btn btn-sm btn-outline-primary">
                                    Désigner un chef
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Member Assignment Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="card-title mb-0 font-weight-bold text-dark">
                        <i class="mdi mdi-account-plus text-primary me-2"></i>Affecter un membre
                    </h5>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('admin.groups.members.assign', $group) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label text-muted" style="font-size: 0.85rem;">Sélectionner un membre</label>
                            <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">— Sélectionner un utilisateur —</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->first_name }} {{ $user->name }}
                                        @if($user->phone) ({{ $user->phone }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="mdi mdi-plus me-1"></i> Ajouter au groupe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling elements for a more premium card design */
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
    }
    .font-weight-semibold {
        font-weight: 600;
    }
    .font-weight-bold {
        font-weight: 700;
    }
    .uppercase {
        text-transform: uppercase;
    }
    .tracking-wider {
        letter-spacing: 0.05em;
    }
    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
    }
</style>
@endsection
