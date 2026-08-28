@extends('layouts.app')

@section('title', 'Statistiques de groupe - Aucun groupe')

@section('content')
<div class="container-fluid max-w-1000 py-4 py-md-5">
    {{-- En-tête / Fil d'ariane --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-muted">Statistiques</a></li>
                <li class="breadcrumb-item active fw-medium" aria-current="page">Mon Groupe</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 fs-20 fs-md-24 text-body">Statistiques du Groupe</h3>
    </div>

    {{-- Carte d'état vide --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center py-5 px-3 px-md-5">
        <div class="card-body py-4">
            <div class="avatar-lg bg-info-subtle text-info rounded-circle mx-auto d-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                <i class="mdi mdi-chart-box-outline fs-36"></i>
            </div>

            <h4 class="fw-bold text-body mb-2 fs-18 fs-md-22">Aucun groupe configuré pour l'analyse</h4>
            
            <p class="text-muted fs-14 mx-auto mb-4" style="max-width: 580px; line-height: 1.6;">
                Les statistiques de groupe permettent de suivre l'assiduité, les taux de présence et l'engagement des membres au sein de chaque groupe ou ministère. Veuillez d'abord créer vos groupes et y assigner des membres.
            </p>

            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 gap-sm-3 pt-2">
                @can('group.create')
                <a href="{{ route('admin.groups.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fs-14 shadow-sm d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="mdi mdi-plus-circle-outline fs-18"></i>
                    <span>Créer un premier groupe</span>
                </a>
                @endcan

                @can('user.create')
                <a href="{{ route('admin.users.create') }}" class="btn btn-soft-primary rounded-pill px-4 py-2 fs-14 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="mdi mdi-account-plus-outline fs-18"></i>
                    <span>Inscrire des membres</span>
                </a>
                @endcan

                <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4 py-2 fs-14 d-inline-flex align-items-center justify-content-center">
                    <span>Tableau de bord</span>
                </a>
            </div>

            {{-- Étapes de démarrage --}}
            <div class="row g-3 mt-4 pt-4 border-top text-start justify-content-center">
                <div class="col-12 col-md-4">
                    <div class="p-3 bg-light-subtle rounded-3 h-100 border border-light-subtle">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary rounded-circle px-2 py-1 fs-11">1</span>
                            <h6 class="mb-0 fw-bold fs-13">Créer vos groupes</h6>
                        </div>
                        <p class="text-muted fs-12 mb-0">Départements, commissions ou groupes d'impact.</p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-3 bg-light-subtle rounded-3 h-100 border border-light-subtle">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary rounded-circle px-2 py-1 fs-11">2</span>
                            <h6 class="mb-0 fw-bold fs-13">Assigner les membres</h6>
                        </div>
                        <p class="text-muted fs-12 mb-0">Rattachez les membres et désignez un responsable de groupe.</p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-3 bg-light-subtle rounded-3 h-100 border border-light-subtle">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-info rounded-circle px-2 py-1 fs-11">3</span>
                            <h6 class="mb-0 fw-bold fs-13">Analyser l'assiduité</h6>
                        </div>
                        <p class="text-muted fs-12 mb-0">Consultez les taux de présence et l'évolution temporelle.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
