@extends('layouts.admin')

@section('admin-title', 'Régions - Vue Synthèse')
@section('admin-subtitle', 'Volume de soumissions par région et par ordre de priorité')

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
        box-shadow: 0 1px 2px rgba(12, 41, 24, 0.06);
    }

    .regions-pagination .page-item.active .page-link {
        background: #1f3a2f;
        border-color: #1f3a2f;
        color: #fff;
    }

    .regions-pagination .page-item.disabled .page-link {
        color: #9daaa2;
        background: #f7faf8;
        border-color: #e4ece7;
        box-shadow: none;
    }

    .regions-pagination .page-item .page-link:hover {
        background: #eef6f1;
        border-color: #bfd8ca;
        color: #173127;
    }

    .regions-pagination .page-item.active .page-link:hover {
        background: #173127;
        border-color: #173127;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1 text-dark">Ciblage des régions</h2>
            <p class="text-muted mb-0">Clique sur une région pour voir la liste détaillée des agents et leurs priorités.</p>
        </div>

        <form method="GET" action="{{ route('admin.regions.priorities') }}" class="d-flex align-items-center gap-2">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                class="form-control"
                placeholder="Rechercher une région"
                style="min-width: 240px;"
            >
            <button type="submit" class="btn btn-outline-success">Filtrer</button>
            @if(!empty($search))
                <a href="{{ route('admin.regions.priorities') }}" class="btn btn-outline-secondary">Réinitialiser</a>
            @endif
        </form>
    </div>

    <div class="row g-4">
        @forelse($regions as $region)
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $region->nom ?? 'Région sans nom' }}</h5>
                            <p class="text-muted mb-0 small">{{ $region->total_choices }} soumission(s) pour cette région</p>
                        </div>
                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">{{ $region->total_choices }} choix</span>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="p-3 rounded-3 bg-primary bg-opacity-10">
                                    <p class="small text-muted mb-1">Priorité 1</p>
                                    <h5 class="fw-bold mb-0 text-primary">{{ $region->ordre_1_count }}</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-3 bg-info bg-opacity-10">
                                    <p class="small text-muted mb-1">Priorité 2</p>
                                    <h5 class="fw-bold mb-0 text-info">{{ $region->ordre_2_count }}</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-3 bg-secondary bg-opacity-10">
                                    <p class="small text-muted mb-1">Priorité 3</p>
                                    <h5 class="fw-bold mb-0 text-secondary">{{ $region->ordre_3_count }}</h5>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.regions.priorities.show', $region) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-2">
                            <i class="bi bi-list-ul"></i>
                            <span>Voir les utilisateurs</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm mb-0">Aucune région trouvée.</div>
            </div>
        @endforelse
    </div>

    @if($regions->hasPages())
        <div class="d-flex justify-content-center mt-4 regions-pagination">
            {{ $regions->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
