@extends('layouts.admin')

@section('admin-title', 'Plans de Déploiement')
@section('admin-subtitle', 'Consultez et exportez vos scénarios sauvegardés')

@section('content')
<div class="container-fluid p-0">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 ps-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="glass-card p-0 overflow-hidden" style="border-radius: 1.5rem;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light bg-opacity-50">
                    <tr>
                        <th class="ps-4 py-3 border-0 text-muted small text-uppercase fw-bold">Dénomination</th>
                        <th class="py-3 border-0 text-muted small text-uppercase fw-bold">Date de sauvegarde</th>
                        <th class="py-3 border-0 text-muted small text-uppercase fw-bold text-center">Équipes</th>
                        <th class="py-3 border-0 text-muted small text-uppercase fw-bold text-center">Effectif</th>
                        <th class="py-3 border-0 text-muted small text-uppercase fw-bold text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $plan->nom }}</div>
                                <div class="text-muted small">ID #{{ $plan->id }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar3 text-primary opacity-50"></i>
                                    <span class="text-dark fw-semibold small">{{ $plan->summary['created_at_human'] ?? $plan->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-primary border border-primary border-opacity-25 rounded-pill px-3">
                                    {{ $plan->summary['teams_count'] ?? count($plan->data) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-success border border-success border-opacity-25 rounded-pill px-3">
                                    {{ $plan->summary['members_count'] ?? 0 }} agents
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.deployment_plans.download', $plan) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold">
                                        <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel
                                    </a>
                                    <form action="{{ route('admin.deployment_plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Supprimer ce plan définitivement ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border rounded-pill p-2 text-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center">
                                <div class="py-4">
                                    <i class="bi bi-archive text-muted opacity-25 display-1 mb-3"></i>
                                    <p class="text-muted fw-bold">Aucun plan sauvegardé pour le moment.</p>
                                    <a href="{{ route('admin.operations.research') }}" class="btn btn-sm btn-modern-primary mt-2">
                                        Créer une simulation
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
