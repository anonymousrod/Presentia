@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="mdi mdi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Créer une nouvelle activité</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.activities.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type d'activité <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Sélectionner un type</option>
                                    @foreach(\App\Enums\ActivityType::cases() as $type)
                                        <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    @foreach(\App\Enums\ActivityStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ old('status', \App\Enums\ActivityStatus::PUBLISHED->value) === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Date et heure de début <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">Date et heure de fin <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Lieu</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="capacity" class="form-label">Capacité max. (optionnel)</label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity') }}" min="1">
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="responsible_id" class="form-label">Responsable</label>
                            <select class="form-select @error('responsible_id') is-invalid @enderror" id="responsible_id" name="responsible_id">
                                <option value="">Aucun responsable spécifique</option>
                                @foreach($responsibles as $resp)
                                    <option value="{{ $resp->id }}" {{ old('responsible_id') == $resp->id ? 'selected' : '' }}>{{ $resp->first_name }} {{ $resp->name }}</option>
                                @endforeach
                            </select>
                            @error('responsible_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="visibility" class="form-label">Visibilité <span class="text-danger">*</span></label>
                            <select class="form-select @error('visibility') is-invalid @enderror" id="visibility" name="visibility" required>
                                @foreach(\App\Enums\ActivityVisibility::cases() as $vis)
                                    <option value="{{ $vis->value }}" {{ old('visibility', \App\Enums\ActivityVisibility::ALL->value) === $vis->value ? 'selected' : '' }}>{{ $vis->label() }}</option>
                                @endforeach
                            </select>
                            @error('visibility')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 d-none" id="group-select-container">
                            <label for="visibility_group_id" class="form-label">Groupe cible <span class="text-danger">*</span></label>
                            <select class="form-select @error('visibility_group_id') is-invalid @enderror" id="visibility_group_id" name="visibility_group_id">
                                <option value="">Sélectionner un groupe</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('visibility_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                @endforeach
                            </select>
                            @error('visibility_group_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 d-none" id="role-select-container">
                            <label for="visibility_role_id" class="form-label">Rôle cible <span class="text-danger">*</span></label>
                            <select class="form-select @error('visibility_role_id') is-invalid @enderror" id="visibility_role_id" name="visibility_role_id">
                                <option value="">Sélectionner un rôle</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('visibility_role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('visibility_role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 d-none" id="cancellation-reason-container">
                            <label for="cancellation_reason" class="form-label">Motif d'annulation <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('cancellation_reason') is-invalid @enderror" id="cancellation_reason" name="cancellation_reason" rows="3">{{ old('cancellation_reason') }}</textarea>
                            @error('cancellation_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Créer l'activité</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
        const status = document.getElementById('status').value;
        const container = document.getElementById('cancellation-reason-container');
        if (status === 'CANCELLED') {
            container.classList.remove('d-none');
        } else {
            container.classList.add('d-none');
        }
    }

    document.getElementById('visibility').addEventListener('change', toggleVisibilityFields);
    document.getElementById('status').addEventListener('change', toggleCancellationReason);

    // Run on load
    toggleVisibilityFields();
    toggleCancellationReason();
</script>
@endpush
