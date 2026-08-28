@extends('layouts.app')

@section('title', 'Abonnement Annuel Expiré')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 text-center overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <div class="avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto d-flex align-items-center justify-content-center mb-4 shadow-sm" style="width: 80px; height: 80px;">
                        <i class="mdi mdi-clock-alert-outline fs-36"></i>
                    </div>

                    <h3 class="fw-bold text-body mb-2">Abonnement Annuel Arrivé à Échéance</h3>
                    
                    @if($church)
                        <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fs-13 mb-3">
                            <i class="mdi mdi-church me-1"></i>{{ $church->name }}
                        </div>
                    @endif

                    <p class="text-muted fs-14 mb-4">
                        L'abonnement annuel de votre église à la plateforme <strong>{{ config('app.name', 'MeVoici') }}</strong> a expiré le 
                        <strong class="text-danger">{{ $church && $church->subscription_expires_at ? $church->subscription_expires_at->format('d/m/Y') : 'récemment' }}</strong>.
                        L'accès aux fonctionnalités d'administration a été temporairement suspendu.
                    </p>

                    <div class="bg-body-tertiary p-3 rounded-3 mb-4 text-start border border-light-subtle">
                        <h6 class="fw-bold text-body mb-2"><i class="mdi mdi-information-outline text-primary me-1"></i>Comment renouveler votre accès ?</h6>
                        <ul class="text-muted fs-13 mb-0 ps-3">
                            <li>Contactez le Super Administrateur de la plateforme pour effectuer le règlement de votre abonnement annuel.</li>
                            <li>Dès confirmation du paiement, votre église sera réactivée instantanément pour une nouvelle période de <strong>1 an</strong>.</li>
                            <li>Toutes vos données (membres, présences, trésorerie) sont conservées en toute sécurité.</li>
                        </ul>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                        <a href="tel:+22969129089" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                            <i class="mdi mdi-phone me-1"></i> Contacter le Support
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary rounded-pill px-4 py-2 w-100">
                                <i class="mdi mdi-logout me-1"></i> Se déconnecter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
