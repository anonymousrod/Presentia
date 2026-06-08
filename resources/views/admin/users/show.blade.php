@extends('layouts.app')

@section('title', 'Détails du compte utilisateur')

@section('content')
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img src="{{ asset('assets/images/profile-bg.jpg') }}" alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="user-img" class="img-thumbnail rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" />
                    @else
                        <div class="img-thumbnail rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 100%; height: 100%; font-size: 32px;">
                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>
            <!--end col-->
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1">{{ $user->first_name }} {{ $user->name }}</h3>
                    <p class="text-white text-opacity-75">
                        <span class="badge bg-{{ match($user->status->value) {
                            'ACTIVE' => 'success',
                            'PENDING' => 'warning',
                            'INACTIVE' => 'secondary',
                            'SUSPENDED' => 'danger',
                            default => 'primary'
                        } }} fs-12">
                            {{ $user->status->value }}
                        </span>
                    </p>
                    <div class="hstack text-white-50 gap-1">
                        <div class="me-2"><i class="ri-mail-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>{{ $user->email ?? 'Email non renseigné' }}</div>
                        <div>
                            <i class="ri-phone-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>{{ $user->phone ?? 'Téléphone non renseigné' }}
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
            <div class="col-12 col-lg-auto order-last order-lg-0">
                <div class="row text text-white-50 text-center">
                    <div class="col-lg-4 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">{{ $user->groups()->count() }}</h4>
                            <p class="fs-14 mb-0">Groupes</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">{{ $user->registrations()->count() }}</h4>
                            <p class="fs-14 mb-0">Inscriptions</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-4">
                        <div class="p-2">
                            <h4 class="text-white mb-1">{{ $user->attendances()->count() }}</h4>
                            <p class="fs-14 mb-0">Présences</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex profile-wrapper">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Vue d'ensemble</span>
                            </a>
                        </li>
                    </ul>
                    <div class="flex-shrink-0 d-flex gap-2">
                        <a href="{{ route('admin.users.permissions.edit', $user) }}" class="btn btn-warning">
                            <i class="ri-key-line align-bottom"></i> Permissions
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-success">
                            <i class="ri-edit-box-line align-bottom"></i> Modifier le profil
                        </a>
                    </div>
                </div>
                <!-- Tab panes -->
                <div class="tab-content pt-4 text-muted">
                    <div class="tab-pane active" id="overview-tab" role="tabpanel">
                        <div class="row">
                            <div class="col-xxl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Informations de contact</h5>
                                        <div>
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Prénom :</th>
                                                        <td class="text-muted text-break">{{ $user->first_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Nom :</th>
                                                        <td class="text-muted text-break">{{ $user->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row" style="width: 30%;">Email :</th>
                                                        <td class="text-muted text-break">{{ $user->email ?? 'Non renseigné' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Téléphone :</th>
                                                        <td class="text-muted">{{ $user->phone ?? 'Non renseigné' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Date de naissance :</th>
                                                        <td class="text-muted">{{ $user->birth_date ? $user->birth_date->format('d/m/Y') : 'Non renseignée' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Membre depuis :</th>
                                                        <td class="text-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->

                            </div>
                            <!--end col-->

                            <div class="col-xxl-9">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Groupes de l'utilisateur</h5>
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nom du groupe</th>
                                                        <th>Rôle dans le groupe</th>
                                                        <th>Rejoint le</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($user->groups as $group)
                                                    <tr>
                                                        <td>{{ $group->name }}</td>
                                                        <td>Membre</td>
                                                        <td>{{ $group->pivot->joined_at ? \Carbon\Carbon::parse($group->pivot->joined_at)->format('d/m/Y') : '-' }}</td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-4">L'utilisateur n'appartient à aucun groupe.</td>
                                                    </tr>
                                                    @endforelse
                                                    
                                                    @foreach($user->ledGroups as $ledGroup)
                                                    <tr>
                                                        <td>{{ $ledGroup->name }}</td>
                                                        <td><span class="badge bg-primary">Leader</span></td>
                                                        <td>-</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->

                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
