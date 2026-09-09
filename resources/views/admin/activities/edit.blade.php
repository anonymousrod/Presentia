@extends('layouts.app')

@push('css')
<style>
    /* Thematic Section Headers */
    .section-header {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--vz-primary);
        font-weight: 700;
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .section-header i {
        margin-right: 0.5rem;
        font-size: 1.2rem;
    }
    .section-header::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(var(--vz-primary-rgb), 0.15);
        margin-left: 1rem;
    }

    /* Custom Inputs */
    .premium-input {
        border-radius: 0.5rem;
        padding: 0.65rem 1rem;
        transition: all 0.3s ease;
    }
    .premium-input:focus {
        border-color: var(--vz-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--vz-primary-rgb), 0.15);
    }
    .input-group-text.premium-addon {
        border-radius: 0.5rem 0 0 0.5rem;
        border-right: 0;
        background-color: transparent;
    }
    .premium-input.with-addon {
        border-left: 0;
    }
    .premium-input.with-addon:focus {
        border-left: 1px solid var(--vz-primary);
    }

    /* Radio Status Buttons */
    .status-radio-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .status-radio-container .btn-check:checked + .btn {
        background-color: var(--vz-primary);
        color: #fff;
        border-color: var(--vz-primary);
        box-shadow: 0 4px 10px rgba(var(--vz-primary-rgb), 0.3);
        transform: translateY(-1px);
    }
    .status-radio-container .btn {
        flex: 1 1 auto;
        padding: 0.65rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        border: 1px solid var(--vz-border-color);
        color: var(--vz-body-color);
        background-color: transparent;
    }
    .status-radio-container .btn:hover {
        background-color: rgba(var(--vz-primary-rgb), 0.05);
        border-color: var(--vz-primary);
        color: var(--vz-primary);
    }

    /* Save Button */
    .btn-save {
        padding: 0.8rem 2rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 0.5rem;
        box-shadow: 0 4px 10px rgba(var(--vz-primary-rgb), 0.3);
        transition: all 0.3s;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(var(--vz-primary-rgb), 0.4);
    }

</style>
@endpush

@section('content')

<div class="row mb-3 pb-1 mt-4 px-4">
    <div class="col-12">
        <div class="d-flex align-items-lg-center flex-lg-row flex-column justify-content-between">
            <div class="flex-grow-1">
                <h4 class="fs-16 mb-1">Modifier l'activité : {{ $activity->title }}</h4>
                <p class="text-muted mb-0">Modifiez les informations de l'activité ci-dessous.</p>
            </div>
            <div class="mt-3 mt-lg-0">
                <a href="{{ route('admin.activities.index') }}" class="btn btn-soft-secondary d-flex align-items-center gap-1">
                    <i class="mdi mdi-arrow-left"></i> Retour aux activités
                </a>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid max-w-1200 px-4">
    <form action="{{ route('admin.activities.update', $activity) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <!-- Left Column: Main Information -->
            <div class="col-lg-8">
                
                <!-- Bloc 1: Informations Générales -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5">
                        <div class="section-header">
                            <i class="mdi mdi-information-outline"></i> Informations principales
                        </div>

                        <div class="mb-4">
                            <label for="title" class="form-label fw-medium text-body">Titre de l'activité <span class="text-danger">*</span></label>
                            <input type="text" class="form-control premium-input @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $activity->title) }}" placeholder="Ex: Culte de jeunesse, Sortie d'évangélisation..." required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium text-body">Description</label>
                            <textarea class="form-control premium-input @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Décrivez les objectifs et le programme de l'activité...">{{ old('description', $activity->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label fw-medium text-body">Affiche de l'événement <span class="text-muted">(Optionnel)</span></label>
                            @if($activity->image_path)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $activity->image_path) }}" alt="Affiche" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control premium-input @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="activity_type_id" class="form-label fw-medium text-body">Type d'activité <span class="text-danger">*</span></label>
                                <select name="activity_type_id" id="activity_type_id" class="form-select premium-input @error('activity_type_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un type</option>
                                    @foreach($activityTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('activity_type_id', $activity->activity_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('activity_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium text-body">Statut <span class="text-danger">*</span></label>
                                <div class="status-radio-container">
                                    @foreach(\App\Enums\ActivityStatus::cases() as $status)
                                        @if($status->value === 'ARCHIVED' && $activity->end_time && $activity->end_time->isFuture())
                                            @continue
                                        @endif
                                        <input type="radio" class="btn-check" name="status" id="status_{{ $status->value }}" value="{{ $status->value }}" {{ old('status', $activity->status->value) === $status->value ? 'checked' : '' }}>
                                        <label class="btn" for="status_{{ $status->value }}">{{ $status->label() }}</label>
                                    @endforeach
                                </div>
                                @error('status')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4 d-none p-3 rounded bg-soft-danger border border-danger-subtle" id="cancellation-reason-container">
                            <label for="cancellation_reason" class="form-label fw-medium text-danger">Motif d'annulation <span class="text-danger">*</span></label>
                            <textarea class="form-control premium-input @error('cancellation_reason') is-invalid @enderror" id="cancellation_reason" name="cancellation_reason" rows="2" placeholder="Pourquoi cette activité est-elle annulée ?">{{ old('cancellation_reason', $activity->cancellation_reason) }}</textarea>
                            @error('cancellation_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Bloc 2: Dates et Lieu -->
                <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5">
                        <div class="section-header">
                            <i class="mdi mdi-calendar-clock"></i> Planification & Lieu
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label fw-medium text-body">Date et heure de début <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control premium-input @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $activity->start_time ? $activity->start_time->format('Y-m-d\TH:i') : '') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_time" class="form-label fw-medium text-body">Date et heure de fin <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control premium-input @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $activity->end_time ? $activity->end_time->format('Y-m-d\TH:i') : '') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-8">
                                <label for="location" class="form-label fw-medium text-body">Lieu de l'activité</label>
                                <div class="input-group">
                                    <span class="input-group-text premium-addon"><i class="mdi mdi-map-marker"></i></span>
                                    <input type="text" class="form-control premium-input with-addon @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $activity->location) }}" placeholder="Ex: Temple principal, Salle polyvalente...">
                                </div>
                                @error('location')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="capacity" class="form-label fw-medium text-body">Capacité max.</label>
                                <div class="input-group">
                                    <span class="input-group-text premium-addon"><i class="mdi mdi-account-group"></i></span>
                                    <input type="number" class="form-control premium-input with-addon @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $activity->capacity) }}" min="1" placeholder="Illimité">
                                </div>
                                @error('capacity')
                                    <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right Column: Settings & Configuration -->
            <div class="col-lg-4 pb-5 pb-lg-0 mb-4 mb-lg-0">
                
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem; position: sticky; top: 20px;">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <i class="mdi mdi-cog-outline"></i> Configuration
                        </div>

                        <div class="mb-4">
                            <label for="responsible_id" class="form-label fw-medium text-body">Responsable</label>
                            <select class="form-select premium-input @error('responsible_id') is-invalid @enderror" id="responsible_id" name="responsible_id">
                                <option value="">Aucun responsable spécifique</option>
                                @foreach($responsibles as $resp)
                                    <option value="{{ $resp->id }}" {{ old('responsible_id', $activity->responsible_id) == $resp->id ? 'selected' : '' }}>{{ $resp->first_name }} {{ $resp->name }}</option>
                                @endforeach
                            </select>
                            @error('responsible_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="border-dashed my-4">

                        <div class="mb-4">
                            <label for="visibility" class="form-label fw-medium text-body">Visibilité <span class="text-danger">*</span></label>
                            <select class="form-select premium-input @error('visibility') is-invalid @enderror" id="visibility" name="visibility" required>
                                @foreach(\App\Enums\ActivityVisibility::cases() as $vis)
                                    <option value="{{ $vis->value }}" {{ old('visibility', $activity->visibility->value) === $vis->value ? 'selected' : '' }}>{{ $vis->label() }}</option>
                                @endforeach
                            </select>
                            @error('visibility')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 d-none p-3 bg-light rounded" id="group-select-container">
                            <label for="visibility_group_id" class="form-label fw-medium text-body">Groupe cible <span class="text-danger">*</span></label>
                            <select class="form-select premium-input @error('visibility_group_id') is-invalid @enderror" id="visibility_group_id" name="visibility_group_id">
                                <option value="">Sélectionner un groupe</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('visibility_group_id', $activity->visibility_group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                @endforeach
                            </select>
                            @error('visibility_group_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 d-none p-3 bg-light rounded" id="role-select-container">
                            <label for="visibility_role_id" class="form-label fw-medium text-body">Rôle cible <span class="text-danger">*</span></label>
                            <select class="form-select premium-input @error('visibility_role_id') is-invalid @enderror" id="visibility_role_id" name="visibility_role_id">
                                <option value="">Sélectionner un rôle</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('visibility_role_id', $activity->visibility_role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('visibility_role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="border-dashed my-4">

                        <!-- Registration Toggle -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-check-label fw-medium text-body mb-0" for="is_registration_required">Inscription essentielle</label>
                                <div class="form-check form-switch form-switch-lg form-switch-success mb-0" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="is_registration_required" name="is_registration_required" value="1" {{ old('is_registration_required', $activity->is_registration_required) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.8rem; line-height: 1.4;">
                                Si décoché, l'entrée sera libre. Les membres pourront scanner le QR Code sur place sans inscription préalable.
                            </p>
                            @error('is_registration_required')
                                <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-save">
                                <i class="mdi mdi-check-all me-1"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('admin.activities.index') }}" class="btn btn-ghost-secondary">
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    function toggleVisibilityFields() {
        const visibility = document.getElementById('visibility').value;
        const groupContainer = document.getElementById('group-select-container');
        const roleContainer = document.getElementById('role-select-container');

        if (visibility === 'GROUP') {
            groupContainer.classList.remove('d-none');
            roleContainer.classList.add('d-none');
        } else if (visibility === 'ROLE') {
            roleContainer.classList.remove('d-none');
            groupContainer.classList.add('d-none');
        } else {
            groupContainer.classList.add('d-none');
            roleContainer.classList.add('d-none');
        }
    }

    function toggleCancellationReason() {
        const statusElement = document.querySelector('input[name="status"]:checked');
        const status = statusElement ? statusElement.value : null;
        const container = document.getElementById('cancellation-reason-container');
        if (status === 'CANCELLED') {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }

    document.getElementById('visibility').addEventListener('change', toggleVisibilityFields);
    
    // Add event listeners to radio buttons
    document.querySelectorAll('input[name="status"]').forEach((elem) => {
        elem.addEventListener('change', toggleCancellationReason);
    });

    // Run on load
    toggleVisibilityFields();
    toggleCancellationReason();
</script>
@endpush
