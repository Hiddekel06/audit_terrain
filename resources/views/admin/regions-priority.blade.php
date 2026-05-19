@extends('layouts.admin')

@section('admin-title', 'Ciblage des Régions')
@section('admin-subtitle', 'Vue d\'ensemble de l\'intérêt des candidats par zone géographique')

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        transition: all 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12);
    }
    /* Variantes de couleurs */
    .variant-indigo { --accent-color: #4f46e5; --accent-bg: rgba(79, 70, 229, 0.1); }
    .variant-emerald { --accent-color: #059669; --accent-bg: rgba(5, 150, 105, 0.1); }
    .variant-amber { --accent-color: #d97706; --accent-bg: rgba(217, 119, 6, 0.1); }
    .variant-violet { --accent-color: #7c3aed; --accent-bg: rgba(124, 58, 237, 0.1); }
    .variant-rose { --accent-color: #e11d48; --accent-bg: rgba(225, 29, 72, 0.1); }

    .btn-modern-outline {
        background: rgba(255, 255, 255, 0.5);
        border: 1px solid var(--accent-bg);
        color: var(--accent-color);
        border-radius: 9999px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-modern-outline:hover {
        background: var(--accent-color);
        color: white;
        transform: translateY(-2px);
    }
    .region-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: var(--accent-bg);
        color: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .count-badge {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
    }
    .badge-count {
        background: var(--accent-bg);
        color: var(--accent-color);
        padding: 0.35rem 1rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .filter-input {
        background: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        border-radius: 9999px !important;
        padding: 0.75rem 1.5rem !important;
        transition: all 0.2s ease;
    }
    .filter-input:focus {
        background: white !important;
        border-color: #2563eb !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
    }
</style>

<div class="container-fluid p-0 pb-5">
    <!-- Header & Search -->
    <div class="glass-card p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <h2 class="h5 fw-bold mb-1 text-dark">Répartition par région</h2>
                <p class="text-muted small mb-0">Visualisez le nombre de candidats intéressés par chaque zone.</p>
            </div>
            <div class="col-md-6">
                <form method="GET" action="{{ route('admin.regions.priorities') }}" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 pe-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="q"
                            value="{{ $search ?? '' }}"
                            class="form-control filter-input border-0"
                            placeholder="Rechercher une région..."
                        >
                    </div>
                    @if(!empty($search))
                        <a href="{{ route('admin.regions.priorities') }}" class="btn btn-light rounded-pill px-4 d-flex align-items-center">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Regions Grid -->
    <div class="row g-4">
        @php $variants = ['indigo', 'emerald', 'amber', 'violet', 'rose']; @endphp
        @forelse($regions as $index => $region)
            @php $variant = $variants[$index % count($variants)]; @endphp
            <div class="col-xl-4 col-md-6">
                <div class="glass-card p-4 h-100 d-flex flex-column variant-{{ $variant }}">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="region-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">{{ $region->nom }}</h5>
                            <span class="text-muted small">Candidats intéressés</span>
                        </div>
                    </div>
                    
                    <div class="flex-grow-1 d-flex align-items-center justify-content-center mb-4">
                        <div class="text-center">
                            <div class="count-badge mb-1">{{ $region->total_choices }}</div>
                            <div class="badge-count">
                                Personnes
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <a href="{{ route('admin.regions.priorities.show', $region) }}" class="btn btn-modern-outline w-100 justify-content-center">
                            <i class="bi bi-eye"></i>
                            Voir les candidats
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="glass-card p-5 text-center">
                    <i class="bi bi-geo-alt text-muted fs-1 mb-3 d-block"></i>
                    <h5 class="text-dark fw-bold">Aucune région trouvée</h5>
                    <p class="text-muted">Essayez un autre mot-clé de recherche.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($regions->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $regions->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
