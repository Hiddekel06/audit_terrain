@extends('layouts.admin')

@section('admin-title', 'Tableau de Bord Analytique')
@section('admin-subtitle', 'Suivi des inscriptions, motivations et questions dynamiques')
@section('body-bg', '#f4f7f6') {{-- Un gris-bleu plus pro --}}

@section('content')
<style>
    .admin-kpi-card {
        background: linear-gradient(180deg, #ffffff 0%, #fbfdfb 100%);
        border: 1px solid #dce8dc;
        border-radius: 20px;
        box-shadow: 0 10px 24px rgba(26, 46, 26, 0.06);
        overflow: hidden;
        position: relative;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .admin-kpi-card::before {
        content: '';
        position: absolute;
        top: -18px;
        right: -18px;
        width: 72px;
        height: 36px;
        transform: none;
        border-radius: 0 0 0 72px;
        opacity: 0.95;
        box-shadow: 0 10px 18px rgba(26, 46, 26, 0.08);
    }

    .admin-kpi-card--total::before {
        background: linear-gradient(180deg, rgba(90, 166, 200, 0.28), rgba(90, 166, 200, 0.5));
    }

    .admin-kpi-card--profile::before {
        background: linear-gradient(180deg, rgba(74, 140, 92, 0.26), rgba(122, 185, 138, 0.5));
    }

    .admin-kpi-card--profile-a::before {
        background: linear-gradient(180deg, rgba(90, 166, 200, 0.26), rgba(120, 202, 221, 0.5));
    }

    .admin-kpi-card--profile-b::before {
        background: linear-gradient(180deg, rgba(209, 108, 114, 0.24), rgba(232, 156, 161, 0.5));
    }

    .admin-kpi-card--profile-c::before {
        background: linear-gradient(180deg, rgba(217, 164, 65, 0.24), rgba(237, 198, 108, 0.52));
    }

    .admin-kpi-card--profile-d::before {
        background: linear-gradient(180deg, rgba(124, 109, 207, 0.24), rgba(166, 152, 236, 0.5));
    }

    .admin-kpi-card--yes::before {
        background: linear-gradient(180deg, rgba(74, 140, 92, 0.28), rgba(122, 185, 138, 0.52));
    }

    .admin-kpi-card--no::before {
        background: linear-gradient(180deg, rgba(209, 108, 114, 0.24), rgba(232, 156, 161, 0.5));
    }

    .admin-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(26, 46, 26, 0.1);
        border-color: #c8ddc8;
    }

    .admin-kpi-card__accent {
        height: 5px;
        width: 100%;
        background: linear-gradient(90deg, #4a8c5c 0%, #79b98a 100%);
    }

    .admin-kpi-card__accent--blue {
        background: linear-gradient(90deg, #4a8c5c 0%, #5aa6c8 100%);
    }

    .admin-kpi-card__accent--amber {
        background: linear-gradient(90deg, #4a8c5c 0%, #d9a441 100%);
    }

    .admin-kpi-card__accent--rose {
        background: linear-gradient(90deg, #4a8c5c 0%, #d16c72 100%);
    }

    .admin-kpi-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }

    .admin-kpi-icon--profile {
        background: linear-gradient(135deg, rgba(74, 140, 92, 0.16), rgba(122, 185, 138, 0.28));
        color: #2f6a40;
    }

    .admin-kpi-icon--profile-a {
        background: linear-gradient(135deg, rgba(90, 166, 200, 0.16), rgba(120, 202, 221, 0.3));
        color: #2f6f86;
    }

    .admin-kpi-icon--profile-b {
        background: linear-gradient(135deg, rgba(209, 108, 114, 0.14), rgba(232, 156, 161, 0.28));
        color: #a4444b;
    }

    .admin-kpi-icon--profile-c {
        background: linear-gradient(135deg, rgba(217, 164, 65, 0.14), rgba(239, 206, 126, 0.3));
        color: #91680d;
    }

    .admin-kpi-icon--profile-d {
        background: linear-gradient(135deg, rgba(124, 109, 207, 0.14), rgba(183, 175, 239, 0.3));
        color: #5a4ea3;
    }

    .admin-kpi-icon--total {
        background: linear-gradient(135deg, rgba(90, 166, 200, 0.16), rgba(120, 202, 221, 0.3));
        color: #2f6f86;
    }

    .admin-kpi-icon--yes {
        background: linear-gradient(135deg, rgba(74, 140, 92, 0.16), rgba(122, 185, 138, 0.3));
        color: #2f6a40;
    }

    .admin-kpi-icon--no {
        background: linear-gradient(135deg, rgba(209, 108, 114, 0.14), rgba(232, 156, 161, 0.28));
        color: #a4444b;
    }

    .admin-kpi-card .kpi-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #5a6e5a;
        font-weight: 700;
    }

    .admin-kpi-card .kpi-value {
        font-size: 2rem;
        line-height: 1;
        color: #1a2e1a;
        margin: 0;
        font-weight: 800;
    }

    .admin-kpi-card .kpi-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 11px;
        border-radius: 999px;
        padding: 0.42rem 0.75rem;
        border: 1px solid rgba(74, 140, 92, 0.2);
        background: rgba(74, 140, 92, 0.07);
        color: #2f6a40;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.18s ease;
    }

    .admin-kpi-card .kpi-link:hover {
        background: rgba(74, 140, 92, 0.12);
        border-color: rgba(74, 140, 92, 0.3);
        color: #245131;
    }

    .admin-kpi-card .kpi-subtitle {
        color: #6b7e6b;
        font-size: 12px;
    }

    .admin-kpi-section-title {
        color: #1a2e1a;
        font-weight: 800;
        letter-spacing: -0.02em;
    }
</style>
<div class="container-fluid p-0">

    {{-- Header avec Titre et Action --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-0">Tableau de Bord Analytique</h1>
            <p class="text-muted mb-0">Suivi des inscriptions et des motivations en temps réel.</p>
        </div>
        {{-- Actions admin retirées (boutons d'ajout supprimés) --}}
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    {{-- ===== KPI CARDS (Haut de page) ===== --}}
    <div class="row g-3 mb-4">
        @php
            $profilThemes = [
                ['cardClass' => 'admin-kpi-card--profile-a', 'iconClass' => 'admin-kpi-icon--profile-a', 'icon' => 'bi-stars', 'linkClass' => 'kpi-link--profile-a'],
                ['cardClass' => 'admin-kpi-card--profile-b', 'iconClass' => 'admin-kpi-icon--profile-b', 'icon' => 'bi-people-fill', 'linkClass' => 'kpi-link--profile-b'],
                ['cardClass' => 'admin-kpi-card--profile-c', 'iconClass' => 'admin-kpi-icon--profile-c', 'icon' => 'bi-briefcase-fill', 'linkClass' => 'kpi-link--profile-c'],
                ['cardClass' => 'admin-kpi-card--profile-d', 'iconClass' => 'admin-kpi-icon--profile-d', 'icon' => 'bi-graph-up-arrow', 'linkClass' => 'kpi-link--profile-d'],
            ];
        @endphp

        <div class="col-sm-6 col-md-3">
            <div class="admin-kpi-card admin-kpi-card--total h-100">
                <div class="p-3 p-lg-4 h-100 d-flex align-items-center gap-3">
                    <div class="admin-kpi-icon admin-kpi-icon--total">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="kpi-label mb-1">Total utilisateurs</div>
                        <h3 class="kpi-value">{{ $totalUsers }}</h3>
                        <div class="kpi-subtitle mb-2">Tous les agents inscrits</div>
                        <a href="{{ route('admin.candidates.index') }}" class="kpi-link">
                            Voir détails <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @forelse($candidatesByProfil as $profilKpi)
            @php
                $profilTheme = $profilThemes[$loop->index % count($profilThemes)];
            @endphp
            <div class="col-sm-6 col-md-3">
                <div class="admin-kpi-card {{ $profilTheme['cardClass'] }} h-100">
                    <div class="p-3 p-lg-4 h-100 d-flex align-items-center gap-3">
                        <div class="admin-kpi-icon {{ $profilTheme['iconClass'] }}">
                            <i class="bi {{ $profilTheme['icon'] }} fs-3"></i>
                        </div>
                        <div>
                            <div class="kpi-label mb-1">{{ $profilKpi->libelle ?? 'Non assigné' }}</div>
                            <h3 class="kpi-value">{{ $profilKpi->total }}</h3>
                            @if($profilKpi->profil_id)
                                <a href="{{ route('admin.candidates.index', ['profil_id' => $profilKpi->profil_id]) }}" class="kpi-link mt-2 {{ $profilTheme['linkClass'] }}">
                                    Voir détails <i class="bi bi-arrow-right-short"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-muted small">Aucun profil actif.</div>
            </div>
        @endforelse
    </div>

    {{-- ===== KPI DEPLOIEMENT ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-6">
            <div class="admin-kpi-card admin-kpi-card--yes h-100">
                <div class="p-3 p-lg-4 h-100 d-flex align-items-center gap-3">
                    <div class="admin-kpi-icon admin-kpi-icon--yes">
                        <i class="bi bi-check2-circle fs-3"></i>
                    </div>
                    <div>
                        <div class="kpi-label mb-1">Prêts à déployer</div>
                        <h3 class="kpi-value">{{ $readyYes }}</h3>
                        <div class="kpi-subtitle">Ont confirmé “Oui”</div>
                        <a href="{{ route('admin.candidates.index', ['ready_to_deploy' => 'yes']) }}" class="kpi-link mt-2">
                            Voir détails <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="admin-kpi-card admin-kpi-card--no h-100">
                <div class="p-3 p-lg-4 h-100 d-flex align-items-center gap-3">
                    <div class="admin-kpi-icon admin-kpi-icon--no">
                        <i class="bi bi-x-circle fs-3"></i>
                    </div>
                    <div>
                        <div class="kpi-label mb-1">Prêts à déployer</div>
                        <h3 class="kpi-value">{{ $readyNo }}</h3>
                        <div class="kpi-subtitle">Ont répondu “Non”</div>
                        <a href="{{ route('admin.candidates.index', ['ready_to_deploy' => 'no']) }}" class="kpi-link mt-2">
                            Voir détails <i class="bi bi-arrow-right-short"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== SECTION CANDIDATS (Analyse des profils) ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="admin-kpi-section-title mb-1">Analyse des Candidats</h5>
                        <p class="text-muted mb-0 small">Distribution par profil, expérience professionnelle et région</p>
                    </div>
                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-sm btn-outline-primary">Voir tous les candidats</a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        {{-- Distribution par expérience --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mb-3">Par Expérience</h6>
                            <div class="list-group list-group-flush">
                                @forelse($candidatesByExperience as $experience => $count)
                                    <div class="list-group-item px-0 border-0 py-2 d-flex justify-content-between align-items-center">
                                        <span class="text-dark">{{ $experience }}</span>
                                        <span class="badge bg-success rounded-pill">{{ $count }}</span>
                                    </div>
                                @empty
                                    <p class="text-muted small">Aucune donnée</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Distribution par niveau numérique --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mb-3">Par Niveau Numérique</h6>
                            <div class="list-group list-group-flush">
                                @forelse($candidatesByNiveau as $niveau => $count)
                                    <div class="list-group-item px-0 border-0 py-2 d-flex justify-content-between align-items-center">
                                        <span class="text-dark">{{ ucfirst(str_replace('_', ' ', $niveau)) }}</span>
                                        <span class="badge bg-success rounded-pill">{{ $count }}</span>
                                    </div>
                                @empty
                                    <p class="text-muted small">Aucune donnée</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI 'Répondants', 'Questions actives' et 'Soumissions régions' retirés par demande produit --}}

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between">
                    <h5 class="fw-bold mb-0">Répartition par Région</h5>
                    <i class="bi bi-three-dots-vertical text-muted"></i>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="regionChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Script pour le graphique (À ajouter à votre stack JS ou en bas de page) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('regionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($tendances->pluck('region.nom')) !!},
            datasets: [{
                label: 'Nombre de choix',
                data: {!! json_encode($tendances->pluck('total')) !!},
                backgroundColor: '#0d6efd',
                borderRadius: 8,
                barThickness: 25,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5], drawBorder: false } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

{{-- Modals d'ajout supprimés (fonctionnalité désactivée en production) --}}
@endsection