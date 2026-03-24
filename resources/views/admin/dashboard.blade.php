@extends('layouts.app')

@section('no-header')@endsection
@section('no-footer')@endsection
@section('body-bg', '#f4f7f6') {{-- Un gris-bleu plus pro --}}

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Header avec Titre et Action --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-0">Tableau de Bord Analytique</h1>
            <p class="text-muted mb-0">Suivi des inscriptions et des motivations en temps réel.</p>
        </div>
        <button class="btn btn-primary shadow-sm d-flex align-items-center gap-2 px-4 py-2" data-bs-toggle="modal" data-bs-target="#addMotivationModal">
            <i class="bi bi-plus-lg"></i>
            <span class="fw-medium">Nouvelle Motivation</span>
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    {{-- ===== KPI CARDS (Haut de page) ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-people text-primary fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small uppercase fw-bold">Total Inscrits</h6>
                        <h3 class="fw-bold mb-0">{{ $totalUsers }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-journal-check text-success fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold">Motivations Types</h6>
                        <h3 class="fw-bold mb-0">{{ $motivationStats['types'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-chat-dots text-warning fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold">Personnalisées</h6>
                        <h3 class="fw-bold mb-0">{{ $motivationStats['libres'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-geo-alt text-info fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold">Régions Actives</h6>
                        <h3 class="fw-bold mb-0">{{ $tendances->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- ===== GRAPHIQUE DES TENDANCES ===== --}}
        <div class="col-lg-8">
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

        {{-- ===== DERNIÈRES MOTIVATIONS (LISTE PROPRE) ===== --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Motivations Libres</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($motivationsLibres->take(6) as $libre)
                            <div class="list-group-item px-0 border-0 mb-2">
                                <div class="d-flex gap-3">
                                    <div class="mt-1">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person text-secondary small"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-dark fw-medium small">{{ Str::limit($libre->motivation_libre, 60) }}</p>
                                        <span class="text-muted" style="font-size: 0.75rem;">Ajouté récemment</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">Aucune donnée</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== DÉTAILS DES CHOIX (Cartes compactes) ===== --}}
    <h5 class="fw-bold mb-3 mt-5">Analyse par Priorité de Choix</h5>
    <div class="row g-4">
        @php
            $choices = [
                1 => ['title' => 'Premier Choix', 'data' => $tendancesChoix1, 'color' => 'primary'],
                2 => ['title' => 'Deuxième Choix', 'data' => $tendancesChoix2, 'color' => 'info'],
                3 => ['title' => 'Troisième Choix', 'data' => $tendancesChoix3, 'color' => 'secondary'],
            ];
        @endphp

        @foreach($choices as $choice)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-uppercase text-{{ $choice['color'] }} mb-0">{{ $choice['title'] }}</h6>
                            <span class="badge rounded-pill bg-{{ $choice['color'] }} bg-opacity-10 text-{{ $choice['color'] }}">
                                {{ $choice['data']->sum('total') }} inscrits
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless align-middle mb-0">
                                <tbody>
                                    @foreach($choice['data']->take(5) as $trend)
                                        <tr>
                                            <td class="ps-0 py-2 fw-medium text-dark">{{ $trend->region->nom ?? 'Inconnue' }}</td>
                                            <td class="text-end pe-0 py-2">
                                                <span class="fw-bold text-dark">{{ $trend->total }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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

{{-- Gardez votre modal existante ici --}}
@endsection