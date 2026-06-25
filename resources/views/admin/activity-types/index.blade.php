@extends('layouts.app')

@section('title', 'Gestion des Types d\'Activités')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Types d'Activités</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Types d'Activités</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="ri-check-line me-1 align-middle fs-16"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="ri-error-warning-line me-1 align-middle fs-16"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card" id="activityTypeList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                    <h5 class="card-title mb-0 flex-grow-1">Liste des Types d'Activités</h5>
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.activity-types.create') }}" class="btn btn-success add-btn">
                            <i class="ri-add-line align-bottom me-1"></i> Nouveau type
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                {{-- VUE DESKTOP --}}
                <div class="table-responsive table-card mb-1 d-none d-md-block">
                    <table class="table align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th class="ps-4">Nom</th>
                                <th>Couleur</th>
                                <th>Activités Liées</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($types as $type)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm flex-shrink-0 me-3">
                                                <div class="avatar-title rounded-circle bg-primary-subtle text-primary fs-16">
                                                    <i class="ri-settings-4-line"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="fs-14 mb-0 fw-semibold">{{ $type->name }}</h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-inline-block rounded-circle" style="width: 14px; height: 14px; background-color: {{ $type->color }}; border: 1px solid rgba(0,0,0,0.1);"></span>
                                            <code class="text-muted">{{ $type->color }}</code>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info fs-12">
                                            <i class="ri-calendar-event-line me-1 align-middle"></i>{{ $type->activities_count }} activité{{ $type->activities_count > 1 ? 's' : '' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <ul class="list-inline hstack gap-2 justify-content-end mb-0">
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Modifier">
                                                <a href="{{ route('admin.activity-types.edit', $type) }}" class="text-primary d-inline-block edit-item-btn">
                                                    <i class="ri-pencil-fill fs-16"></i>
                                                </a>
                                            </li>
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Supprimer">
                                                <button type="button" class="btn btn-link text-danger p-0 border-0 align-middle remove-item-btn" 
                                                        onclick="confirmDelete({{ $type->id }})" 
                                                        {{ $type->activities_count > 0 ? 'disabled' : '' }}>
                                                    <i class="ri-delete-bin-5-fill fs-16"></i>
                                                </button>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="noresult py-5">
                                            <div class="text-center">
                                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                <h5 class="mt-2 text-muted">Désolé ! Aucun type d'activité trouvé.</h5>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- VUE MOBILE (Cartes) --}}
                <div class="d-md-none mt-3">
                    <div class="d-flex flex-column gap-3">
                        @forelse($types as $type)
                            <div class="card border border-light shadow-sm mb-0">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm flex-shrink-0">
                                                <div class="avatar-title rounded-circle bg-primary-subtle text-primary fs-16">
                                                    <i class="ri-settings-4-line"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1">{{ $type->name }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 d-flex justify-content-between border-top border-bottom py-2">
                                        <div class="text-center w-50 border-end">
                                            <small class="text-muted d-block mb-1">Couleur</small>
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background-color: {{ $type->color }}; border: 1px solid rgba(0,0,0,0.1);"></span>
                                                <code class="text-muted fs-11">{{ $type->color }}</code>
                                            </div>
                                        </div>
                                        <div class="text-center w-50">
                                            <small class="text-muted d-block mb-1">Activités</small>
                                            <span class="badge bg-info-subtle text-info fs-11">
                                                <i class="ri-calendar-event-line me-1"></i>{{ $type->activities_count }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.activity-types.edit', $type) }}" class="btn btn-sm btn-primary flex-grow-1" title="Modifier">
                                            <i class="ri-pencil-fill me-1"></i>Modifier
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger flex-grow-1" 
                                                onclick="confirmDelete({{ $type->id }})" 
                                                {{ $type->activities_count > 0 ? 'disabled' : '' }} title="Supprimer">
                                            <i class="ri-delete-bin-5-fill me-1"></i>Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="noresult py-4 text-center border rounded">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                <h5 class="mt-2 text-muted">Désolé ! Aucun type d'activité trouvé.</h5>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($types as $type)
<form id="delete-form-{{ $type->id }}" action="{{ route('admin.activity-types.destroy', $type) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endforeach

@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        confirmAction(
            'Êtes-vous sûr de vouloir supprimer ce type d\'activité ?',
            () => document.getElementById('delete-form-' + id).submit(),
            'Supprimer le type',
            'Supprimer',
            'btn-danger'
        );
    }
</script>
@endpush
