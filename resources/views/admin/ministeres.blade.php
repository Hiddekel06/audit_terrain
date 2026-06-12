@extends('layouts.admin')

@section('admin-title', 'Répartition par ministère')
@section('admin-subtitle', 'Vue d\'ensemble des agents classés par structure et par profil')

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 1.1rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12);
    }

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

    .ministere-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: var(--accent-bg);
        color: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .count-badge {
        font-size: 1.2rem;
        font-weight: 800;
        color: #1e293b;
    }

    .badge-count {
        background: var(--accent-bg);
        color: var(--accent-color);
        padding: 0.25rem 0.8rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.7rem;
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

    .stat-tile {
        border-radius: 0.85rem;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(148, 163, 184, 0.15);
        padding: 0.7rem 0.85rem;
    }

    .stat-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        font-weight: 700;
    }
</style>

<div class="container-fluid p-0 pb-5">
    <div class="glass-card p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <h2 class="h5 fw-bold mb-1 text-dark">Répartition par ministère</h2>
                <p class="text-muted small mb-0">Consultez le volume d'agents par structure et la composition par profil.</p>
            </div>
            <div class="col-md-6">
                <form method="GET" action="{{ route('admin.ministeres.index') }}" class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 pe-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="q"
                            value="{{ $search ?? '' }}"
                            class="form-control filter-input border-0"
                            placeholder="Rechercher un ministère..."
                        >
                    </div>
                    @if(!empty($search))
                        <a href="{{ route('admin.ministeres.index') }}" class="btn btn-light rounded-pill px-4 d-flex align-items-center">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4 row-cols-1 row-cols-md-2 row-cols-xl-5">
        @php
            $summaryCards = [
                ['label' => 'Agents', 'value' => $totals['agents'] ?? 0, 'icon' => 'bi-people', 'class' => 'variant-indigo'],
                ['label' => 'Chefs d\'équipe', 'value' => $totals['chefs'] ?? 0, 'icon' => 'bi-person-badge', 'class' => 'variant-emerald'],
                ['label' => 'Auditeurs', 'value' => $totals['auditeurs'] ?? 0, 'icon' => 'bi-person-check', 'class' => 'variant-amber'],
                ['label' => 'Supports', 'value' => $totals['supports'] ?? 0, 'icon' => 'bi-tools', 'class' => 'variant-violet'],
                ['label' => 'Chauffeurs', 'value' => $totals['chauffeurs'] ?? 0, 'icon' => 'bi-car-front', 'class' => 'variant-rose'],
            ];
        @endphp

        @foreach($summaryCards as $card)
            <div class="col">
                <div class="glass-card p-2 h-100 {{ $card['class'] }}">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ministere-icon">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="stat-label mb-1">{{ $card['label'] }}</div>
                            <div class="stat-value">{{ $card['value'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        @php $variants = ['indigo', 'emerald', 'amber', 'violet', 'rose']; @endphp
        @forelse($ministeres as $index => $ministere)
            @php
                $variant = $variants[$index % count($variants)];
                $total = max(1, (int) $ministere->total_agents);
                $chefsPct = round(($ministere->chefs_count / $total) * 100);
                $auditeursPct = round(($ministere->auditeurs_count / $total) * 100);
                $supportsPct = round(($ministere->supports_count / $total) * 100);
                $chauffeursPct = round(($ministere->chauffeurs_count / $total) * 100);
            @endphp
            <div class="col-xl-4 col-md-6">
                <div class="glass-card p-3 h-100 d-flex flex-column variant-{{ $variant }}">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="ministere-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem; line-height: 1.2;">{{ $ministere->nom }}</h5>
                            <span class="text-muted small">Agents affectés par profil</span>
                        </div>
                    </div>

                    <div class="flex-grow-1 mb-2">
                        <div class="text-center mb-3">
                            <div class="count-badge mb-1">{{ $ministere->total_agents }}</div>
                            <div class="badge-count">Agents</div>
                        </div>

                        <div class="d-grid gap-2">
                            <div class="stat-tile d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label">Chefs d'équipe</div>
                                    <div class="fw-bold text-dark">{{ $ministere->chefs_count }}</div>
                                </div>
                                <span class="badge rounded-pill text-bg-light">{{ $chefsPct }}%</span>
                            </div>
                            <div class="stat-tile d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label">Auditeurs IT</div>
                                    <div class="fw-bold text-dark">{{ $ministere->auditeurs_count }}</div>
                                </div>
                                <span class="badge rounded-pill text-bg-light">{{ $auditeursPct }}%</span>
                            </div>
                            <div class="stat-tile d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label">Administratifs</div>
                                    <div class="fw-bold text-dark">{{ $ministere->supports_count }}</div>
                                </div>
                                <span class="badge rounded-pill text-bg-light">{{ $supportsPct }}%</span>
                            </div>
                            <div class="stat-tile d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label">Chauffeurs</div>
                                    <div class="fw-bold text-dark">{{ $ministere->chauffeurs_count }}</div>
                                </div>
                                <span class="badge rounded-pill text-bg-light">{{ $chauffeursPct }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="glass-card p-5 text-center">
                    <i class="bi bi-building text-muted fs-1 mb-3 d-block"></i>
                    <h5 class="text-dark fw-bold">Aucun ministère trouvé</h5>
                    <p class="text-muted">Essayez un autre mot-clé de recherche.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($ministeres->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $ministeres->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
