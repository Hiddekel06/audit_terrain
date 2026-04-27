@extends('layouts.admin')

@section('admin-title', 'Détail Région')
@section('admin-subtitle', 'Liste paginée des utilisateurs ayant ciblé une région')

@push('styles')
<style>
    .regions-pagination .pagination {
        gap: 0.35rem;
        margin-bottom: 0;
    }

    .regions-pagination .page-item .page-link {
        border: 1px solid #d8e4dd;
        color: #1f3a2f;
        background: #ffffff;
        border-radius: 0.7rem;
        min-width: 2.2rem;
        height: 2.2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 0.65rem;
        font-size: 0.92rem;
    }

    .regions-pagination .page-item.active .page-link {
        background: #1f3a2f;
        border-color: #1f3a2f;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1 text-dark">{{ $region->nom ?? 'Région' }}</h2>
            <p class="text-muted mb-0">Utilisateurs ayant choisi cette région, avec filtre par ordre et recherche.</p>
        </div>

        <a href="{{ route('admin.regions.priorities') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Retour synthèse</span>
        </a>
    </div>

    <form method="GET" action="{{ route('admin.regions.priorities.show', $region) }}" class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body d-flex flex-wrap align-items-end gap-3">
            <div>
                <label class="form-label mb-1 small text-muted">Ordre de priorité</label>
                <select name="ordre" class="form-select" style="min-width: 180px;">
                    <option value="">Tous</option>
                    <option value="1" @selected((string) $orderFilter === '1')>Priorité 1</option>
                    <option value="2" @selected((string) $orderFilter === '2')>Priorité 2</option>
                    <option value="3" @selected((string) $orderFilter === '3')>Priorité 3</option>
                </select>
            </div>

            <div>
                <label class="form-label mb-1 small text-muted">Recherche</label>
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    class="form-control"
                    placeholder="Nom, prénom ou matricule"
                    style="min-width: 260px;"
                >
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Filtrer</button>
                @if(!empty($orderFilter) || !empty($search))
                    <a href="{{ route('admin.regions.priorities.show', $region) }}" class="btn btn-outline-secondary">Réinitialiser</a>
                @endif
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body px-4 pb-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-muted small text-uppercase">Utilisateur</th>
                            <th class="text-muted small text-uppercase">Prénom</th>
                            <th class="text-muted small text-uppercase">Matricule</th>
                            <th class="text-muted small text-uppercase">Téléphone</th>
                            <th class="text-muted small text-uppercase">Ordre</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($choices as $choice)
                            <tr>
                                <td class="fw-medium text-dark">{{ $choice->user->nom ?? '-' }}</td>
                                <td>{{ $choice->user->prenom ?? '-' }}</td>
                                <td>{{ $choice->user->matricule ?? '-' }}</td>
                                <td>{{ $choice->user->telephone ?? '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $choice->ordre == 1 ? 'bg-primary-subtle text-primary' : ($choice->ordre == 2 ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary') }}">
                                        {{ $choice->ordre }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Aucun résultat pour ce filtre.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($choices->hasPages())
        <div class="d-flex justify-content-center mt-4 regions-pagination">
            {{ $choices->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
