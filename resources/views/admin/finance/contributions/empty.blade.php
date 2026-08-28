@extends('layouts.app')

@section('title', 'Suivi des contributions - Aucun groupe')

@section('content')
<div class="container-fluid max-w-1000 py-4 py-md-5">
    {{-- En-tête / Fil d'ariane --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-muted">Finances</a></li>
                <li class="breadcrumb-item active fw-medium" aria-current="page">Contributions</li>
            </ol>
        </nav>
        <h3 class="fw-bold mb-0 fs-20 fs-md-24 text-body">Suivi des Contributions & Cotisations</h3>
    </div>

    {{-- Carte d'état vide --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center py-5 px-3 px-md-5">
        <div class="card-body py-4">
            <div class="avatar-lg bg-primary-subtle text-primary rounded-circle mx-auto d-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                <i class="mdi mdi-account-group-outline fs-36"></i>
            </div>

            <h4 class="fw-bold text-body mb-2 fs-18 fs-md-22">Aucun groupe configuré pour la collecte</h4>
            
            <p class="text-muted fs-14 mx-auto mb-4" style="max-width: 580px; line-height: 1.6;">
                Le module de suivi des contributions et cotisations hebdomadaires fonctionne par groupe / ministère. Pour commencer à enregistrer les cotisations, veuillez créer au moins un groupe dans votre église et y assigner des membres.
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
                        <p class="text-muted fs-12 mb-0">Définissez vos départements, chorales ou groupes de maison.</p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-3 bg-light-subtle rounded-3 h-100 border border-light-subtle">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary rounded-circle px-2 py-1 fs-11">2</span>
                            <h6 class="mb-0 fw-bold fs-13">Assigner les membres</h6>
                        </div>
                        <p class="text-muted fs-12 mb-0">Ajoutez les jeunes et membres dans leurs groupes respectifs.</p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-3 bg-light-subtle rounded-3 h-100 border border-light-subtle">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success rounded-circle px-2 py-1 fs-11">3</span>
                            <h6 class="mb-0 fw-bold fs-13">Collecter & Suivre</h6>
                        </div>
                        <p class="text-muted fs-12 mb-0">Enregistrez les cotisations dimanche par dimanche et faites vos versements.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
