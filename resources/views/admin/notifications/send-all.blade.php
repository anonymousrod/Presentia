@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Envoyer une notification globale</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.notifications.send-all') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" required maxlength="255">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                              rows="5" required>{{ old('message') }}</textarea>
                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-1"></i>
                    Cette notification sera envoyée à <strong>tous les membres</strong> de la plateforme.
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-send me-1"></i> Envoyer à tous
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
