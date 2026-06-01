@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Demandes de réinitialisation (WhatsApp)</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Demandes en attente</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Utilisateur</th>
                            <th>Téléphone</th>
                            <th>Date de demande</th>
                            <th>Expire le</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td>
                                {{ $request->user->first_name }} {{ $request->user->name }}
                            </td>
                            <td>
                                {{ $request->user->phone }}
                            </td>
                            <td>
                                {{ $request->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                {{ $request->expires_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.password-requests.validate', $request) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Générer un mot de passe temporaire et l\'envoyer par WhatsApp ?')">
                                        Valider et Envoyer
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Aucune demande en attente.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($requests->hasPages())
        <div class="card-footer">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
