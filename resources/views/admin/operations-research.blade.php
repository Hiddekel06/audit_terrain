@extends('layouts.admin')

@section('admin-title', 'Recherche Opérationnelle')
@section('admin-subtitle', 'Gestion et optimisation des équipes terrain')

@section('content')

@push('styles')
<style>
    .glass-card {
        background: #ffffff;
        border: 1px solid rgba(37, 99, 235, 0.1);
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        border-color: rgba(37, 99, 235, 0.3);
    }

    .btn-modern-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1e3a8a 100%);
        color: white;
        border: none;
        border-radius: 0.75rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        color: white;
    }

    .btn-modern-outline {
        background: white;
        border: 1px solid #e2e8f0;
        color: #334155;
        border-radius: 0.75rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-modern-outline:hover {
        border-color: #2563eb;
        color: #2563eb;
        background: #eff6ff;
    }

    /* Card Unité Opérationnelle */
    .team-card-clean {
        padding: 1.75rem;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .team-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        margin-bottom: 0.25rem;
    }

    .team-subtitle {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    /* Membres */
    .member-line {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .member-line:last-child { border-bottom: none; }

    .member-line--vacant { opacity: 0.6; }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-dot--filled { background: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
    .status-dot--empty { border: 2px solid #cbd5e1; }

    .role-label {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        width: 100px;
        flex-shrink: 0;
    }

    .member-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .member-name--empty {
        color: #94a3b8;
        font-style: italic;
        font-weight: 500;
    }

    /* Drag & Drop Visuals */
    .drag-card { cursor: grab; }
    .drag-card:active { cursor: grabbing; }
    .dragging { opacity: 0.4; transform: scale(0.98); }
    
    .drop-ready { background: #eff6ff !important; border-color: #2563eb !important; }
    .drop-blocked { background: #fef2f2 !important; border-color: #ef4444 !important; }

    /* Modale de déploiement */
    .deploy-modal .modal-content {
        border: 1px solid rgba(148, 163, 184, 0.22);
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.18);
        overflow: hidden;
        height: min(92vh, 920px);
        display: flex;
        flex-direction: column;
        background:
            radial-gradient(circle at top left, rgba(37, 99, 235, 0.12), transparent 32%),
            radial-gradient(circle at top right, rgba(16, 185, 129, 0.08), transparent 28%),
            linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .deploy-modal .modal-dialog {
        max-width: 1120px;
    }

    .deploy-modal .modal-header,
    .deploy-modal .modal-footer {
        flex: 0 0 auto;
    }

    .deploy-modal .modal-content > form {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        min-height: 0;
        width: 100%;
    }

    .deploy-modal__header {
        position: relative;
        padding: 1.75rem 1.9rem 1.55rem;
        color: #0f172a;
        border-bottom: 1px solid rgba(148, 163, 184, 0.16);
        background:
            linear-gradient(135deg, rgba(239, 246, 255, 0.98), rgba(255, 255, 255, 0.98)),
            radial-gradient(circle at top right, rgba(37, 99, 235, 0.12), transparent 34%);
    }

    .deploy-modal__header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, #2563eb 0%, #10b981 100%);
    }

    .deploy-modal__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #2563eb;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .deploy-modal .modal-title {
        color: #0f172a;
        letter-spacing: -0.03em;
    }

    .deploy-modal__subtitle {
        color: #475569;
        font-size: 0.95rem;
        max-width: 42rem;
    }

    .deploy-modal__topline {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-top: 1.05rem;
    }

    .deploy-modal__chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 999px;
        padding: 0.45rem 0.8rem;
        font-size: 0.78rem;
        font-weight: 700;
        background: rgba(37, 99, 235, 0.08);
        color: #1d4ed8;
        border: 1px solid rgba(37, 99, 235, 0.12);
    }

    .deploy-modal__body {
        padding: 1.5rem 1.9rem 1.15rem;
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
    }

    .deploy-block-row {
        position: relative;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.96)) !important;
        border-color: rgba(148, 163, 184, 0.18) !important;
        box-shadow: 0 16px 35px rgba(15, 23, 42, 0.06);
        backdrop-filter: blur(8px);
        padding-left: 1.4rem !important;
    }

    .deploy-block-row::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        border-radius: 1rem 0 0 1rem;
        background: linear-gradient(180deg, #2563eb, #10b981);
        opacity: 0.9;
    }

    .deploy-block-row__head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.15rem;
    }

    .deploy-block-row__meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .deploy-block-row__index {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 999px;
        display: grid;
        place-items: center;
        font-size: 0.86rem;
        font-weight: 800;
        color: #0f172a;
        background: linear-gradient(180deg, #e0f2fe, #dbeafe);
        border: 1px solid rgba(37, 99, 235, 0.12);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .deploy-block-row__title {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .deploy-block-row__hint {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 0.2rem;
    }

    .deploy-block-row .form-control,
    .deploy-block-row .form-select {
        border-color: rgba(148, 163, 184, 0.28);
        background: rgba(255, 255, 255, 0.92);
        box-shadow: none;
        border-radius: 0.95rem;
    }

    .deploy-block-row .form-control:focus,
    .deploy-block-row .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    .quota-card {
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.98));
        border-color: rgba(148, 163, 184, 0.18) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }

    .quota-card__label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.85rem;
    }

    .quota-card__badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #334155;
        background: #eef2ff;
    }

    .quota-card .bi,
    .team-header-label,
    .deploy-modal .text-primary,
    .deploy-modal .btn-outline-primary,
    .deploy-modal .btn-modern-primary {
        color: inherit;
    }

    .deploy-modal .btn-deploy-neutral {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(148, 163, 184, 0.22);
        color: #334155;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }

    .deploy-modal .btn-deploy-neutral:hover {
        background: #ffffff;
        border-color: rgba(37, 99, 235, 0.28);
        color: #111827;
    }

    .deploy-modal .btn-deploy-dark {
        background: #16a34a;
        border: 1px solid #16a34a;
        color: #ffffff;
        font-weight: 800;
        box-shadow: 0 10px 22px rgba(22, 163, 74, 0.18);
    }

    .deploy-modal .btn-deploy-dark:hover {
        background: #15803d;
        border-color: #15803d;
        color: #ffffff;
    }

    .deploy-modal__footer {
        padding-top: 1rem;
        padding-bottom: 1.35rem;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.3), rgba(241, 245, 249, 0.78));
        border-top: 1px solid rgba(148, 163, 184, 0.14);
        position: sticky;
        bottom: 0;
        backdrop-filter: blur(12px);
    }

    .deploy-modal__footnote {
        font-size: 0.8rem;
        color: #64748b;
    }

    .team-header-label {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #64748b;
        margin-bottom: 0.25rem;
    }
</style>
@endpush

<div class="container-fluid p-0 pb-5">
    <!-- Header Actions -->
    <div class="glass-card p-4 mb-5" style="border-radius: 1.5rem; border: none; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <form action="{{ route('admin.operations.research') }}" method="GET" id="regionFilterForm">
                    <div class="team-header-label ms-2">Secteur Géographique</div>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 rounded-start-pill ps-3">
                            <i class="bi bi-geo-alt text-primary"></i>
                        </span>
                        <select name="region_id" class="form-select border-0 bg-light rounded-end-pill fw-bold" onchange="this.form.submit()">
                            <option value="">Toutes les régions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" @selected($selectedRegionId == $region->id)>{{ $region->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-8 text-md-end d-flex gap-2 justify-content-md-end pt-md-3">
                <button type="button" class="btn btn-modern-primary" data-bs-toggle="modal" data-bs-target="#autoDeployModal">
                    <i class=""></i> Deploiement optimisé
                </button>
                <div class="dropdown">
                    <button type="button" class="btn btn-modern-outline dropdown-toggle" data-bs-toggle="dropdown">
                        Actions
                    </button>
                    <ul class="dropdown-menu border-0 shadow-lg rounded-4 p-2">
                        <li>
                            <button type="button" class="dropdown-item rounded-3 py-2" data-bs-toggle="modal" data-bs-target="#createTeamModal">
                                <i class="bi bi-plus-lg me-2 text-primary"></i> Nouvelle équipe
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item rounded-3 py-2 text-danger" data-bs-toggle="modal" data-bs-target="#resetDeploymentModal">
                                <i class="bi bi-arrow-counterclockwise me-2"></i> Réinitialiser tout
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 ps-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('info'))
        <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 ps-4">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 ps-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @isset($simulationTeams)
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-4 px-2">
                <div>
                    <h3 class="h5 fw-bold mb-0 text-primary"><i class="bi bi-eye me-2"></i>Aperçu de simulation</h3>
                    <p class="text-muted small mb-0">Visualisez et ajustez la répartition avant validation</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm fw-bold" id="simCountBadge">
                        {{ count($simulationTeams) }} Équipes simulées
                    </span>
                    <button type="button" class="btn btn-sm btn-modern-outline" onclick="window.location.reload()">
                        <i class="bi bi-x-lg"></i> Annuler
                    </button>
                </div>
            </div>

            <div class="row g-4">
                @foreach($simulationTeams as $tIndex => $team)
                    <div class="col-md-6 col-xl-4">
                        <div class="glass-card team-card-clean simulation-team h-100" data-sim-team-index="{{ $tIndex }}">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <div class="team-title">{{ $team['nom'] }}</div>
                                    <div class="team-subtitle">
                                        
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark rounded-pill px-2 py-1 small fw-bold border">SIM</span>
                            </div>

                            <div class="simulation-members flex-grow-1 d-flex flex-column" data-sim-team-index="{{ $tIndex }}">
                                @forelse($team['members'] as $mIndex => $member)
                                    <div class="member-line sim-member drag-card"
                                         draggable="true"
                                         data-sim-user-id="{{ $member['id'] }}"
                                         data-sim-team-index="{{ $tIndex }}"
                                         data-sim-member-index="{{ $mIndex }}"
                                    >
                                        <span class="status-dot status-dot--filled"></span>
                                        <div class="role-label text-truncate">{{ $member['role'] }}</div>
                                        <div class="member-name flex-grow-1 text-truncate">{{ $member['name'] }}</div>
                                        <i class="bi bi-grip-vertical text-muted"></i>
                                    </div>
                                @empty
                                    <div class="text-center py-4 border-dashed rounded-4 bg-light bg-opacity-50">
                                        <p class="text-muted small fw-bold mb-0">Poste vacant</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endisset

    <div class="row g-4">
        <!-- Liste des Équipes -->
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4 px-2">
                <div>
                    <h3 class="h5 fw-bold mb-0 text-dark">Unités Opérationnelles</h3>
                    <p class="text-muted small mb-0">Effectifs déployés sur le terrain</p>
                </div>
                <span class="badge bg-white text-primary border px-3 py-2 rounded-pill shadow-sm fw-bold">
                    {{ $teams->count() }} Équipes
                </span>
            </div>
            
            <div class="row g-4">
                @forelse($teams as $team)
                    <div class="col-md-6">
                        <div class="glass-card team-card-clean">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <div class="team-title">{{ $team->nom }}</div>
                                    <div class="team-subtitle">
                                        <i class="bi bi-geo-alt-fill text-primary"></i> {{ $team->region?->nom ?? 'Secteur libre' }}
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical fs-5"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 overflow-hidden">
                                        <li>
                                            <form action="{{ route('admin.operations.team.destroy', $team) }}" method="POST" onsubmit="return confirm('Dissoudre l\'équipe et libérer les agents ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger py-2 px-3">
                                                    <i class="bi bi-trash me-2"></i> Dissoudre
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="members-list flex-grow-1">
                                @php
                                    $currentMembers = $team->members->groupBy('profil_id');
                                @endphp

                                @foreach($deploymentProfiles as $profile)
                                    @php
                                        $id = $profile['id'];
                                        $label = $profile['label'];
                                        $icon = $profile['icon'];
                                        $member = $currentMembers->get($id)?->first();
                                    @endphp
                                    <div
                                        class="member-line drop-slot {{ !$member ? 'member-line--vacant' : '' }}"
                                        data-team-id="{{ $team->id }}"
                                        data-profil-id="{{ $id }}"
                                        data-has-member="{{ $member ? '1' : '0' }}"
                                    >
                                        <span class="status-dot {{ $member ? 'status-dot--filled' : 'status-dot--empty' }}"></span>
                                        <div class="role-label">{{ $label }}</div>
                                        
                                        @if($member)
                                            <div
                                                class="member-name drag-card flex-grow-1"
                                                draggable="true"
                                                data-user-id="{{ $member->id }}"
                                                data-user-name="{{ $member->prenom }} {{ $member->nom }}"
                                                data-profil-id="{{ $id }}"
                                                data-source-team-id="{{ $team->id }}"
                                                title="Glisser pour déplacer"
                                            >
                                                {{ $member->prenom }} {{ $member->nom }}
                                            </div>
                                            <form action="{{ route('admin.operations.assign') }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $member->id }}">
                                                <input type="hidden" name="team_id" value="">
                                                <button type="submit" class="btn btn-sm btn-link p-0 text-muted opacity-50 hover-opacity-100">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                        @else
                                            <div class="member-name member-name--empty flex-grow-1">Poste vacant</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Aucune équipe active pour cette région.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Candidats Disponibles -->
        <div class="col-lg-4">
            <div class="d-flex align-items-center justify-content-between mb-4 px-2">
                <h3 class="h5 fw-bold mb-0">Agents Libres</h3>
                <span class="badge bg-light text-dark border-0 rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                    {{ $unassignedUsers->count() }} dispo.
                </span>
            </div>
            
            <div class="glass-card overflow-hidden drop-zone-unassigned" data-drop-unassigned="1">
                <div class="list-group list-group-flush bg-transparent">
                    @forelse($unassignedUsers as $user)
                        <div
                            class="list-group-item p-3 border-0 border-bottom bg-transparent hover-bg-light transition-all drag-card"
                            draggable="true"
                            data-user-id="{{ $user->id }}"
                            data-user-name="{{ $user->prenom }} {{ $user->nom }}"
                            data-profil-id="{{ $user->profil_id }}"
                            data-source-team-id=""
                        >
                            <div class="d-flex align-items-center gap-3">
                                <div class="status-dot status-dot--filled" style="width:8px; height:8px; background:#2563eb;"></div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-dark small">{{ $user->prenom }} {{ $user->nom }}</div>
                                    <div class="text-primary small fw-bold" style="font-size: 0.6rem; text-transform: uppercase;">
                                        {{ $user->profil?->libelle }}
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border rounded-pill px-2 py-0" type="button" data-bs-toggle="dropdown" style="font-size: 0.7rem; font-weight: 700;">
                                        Assigner
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-xl rounded-4 overflow-hidden" style="max-height: 250px; overflow-y: auto;">
                                        @foreach($teams as $team)
                                            <li>
                                                <form action="{{ route('admin.operations.assign') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    <input type="hidden" name="team_id" value="{{ $team->id }}">
                                                    <button type="submit" class="dropdown-item py-2 px-3 small d-flex justify-content-between align-items-center">
                                                        <span>{{ $team->nom }}</span>
                                                        <i class="bi bi-plus text-primary"></i>
                                                    </button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted small fw-bold">Vivier vide</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<form id="dragAssignForm" action="{{ route('admin.operations.assign') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="user_id" id="dragUserId">
    <input type="hidden" name="team_id" id="dragTeamId">
</form>

<form id="dragSwapForm" action="{{ route('admin.operations.swap') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="user1_id" id="swapUser1Id">
    <input type="hidden" name="user2_id" id="swapUser2Id">
</form>

{{-- Modals --}}

<!-- Modal Déplacement -->
<div class="modal fade" id="moveMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-primary">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p id="moveMemberModalMessage" class="mb-0 fw-bold"></p>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 justify-content-between">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-modern-primary" id="confirmMoveButton">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Création Équipe -->
<div class="modal fade" id="createTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <form action="{{ route('admin.operations.team.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary">Nouvelle équipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Désignation</label>
                        <input type="text" name="nom" class="form-control rounded-3 border-light bg-light px-4 py-2" placeholder="Ex: Équipe Alpha" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Région</label>
                        <select name="region_id" class="form-select rounded-3 border-light bg-light px-4 py-2" required>
                            <option value="">Sélectionner une région</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" @selected($selectedRegionId == $region->id)>{{ $region->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-modern-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Config IA -->
<div class="modal fade deploy-modal" id="autoDeployModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0">
            <form action="{{ route('admin.operations.simulate') }}" method="POST">
                @csrf
                @php
                    $blocks = old('deployment_blocks', $deploymentBlocks ?? []);
                    if (empty($blocks)) {
                        $blocks = [['team_count' => 3, 'team_size' => 5]];
                    }
                @endphp
                <div class="modal-header border-0 deploy-modal__header">
                    <div class="w-100">
                        <div class="deploy-modal__eyebrow mb-2">
                            <i class="bi bi-sliders2"></i> Paramétrage intelligent
                        </div>
                        <h5 class="modal-title fw-bold mb-2">Configuration du Déploiement</h5>
                        <p class="deploy-modal__subtitle mb-0">
                            Composez vos équipes avec une logique lisible, maîtrisée et immédiatement exploitable.
                        </p>
                        <div class="deploy-modal__topline">
                            <span class="deploy-modal__chip"><i class="bi bi-people-fill"></i> 3 profils principaux</span>
                            <span class="deploy-modal__chip"><i class="bi bi-car-front-fill"></i> Chauffeur inclus</span>
                            <span class="deploy-modal__chip"><i class="bi bi-shield-check"></i> Quotas contrôlés</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body deploy-modal__body">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <div class="fw-bold text-dark h6 mb-1">Groupes d'équipes</div>
                            <div class="small text-muted">Définissez une base claire avant la simulation automatique.</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-deploy-neutral rounded-pill px-3" id="addDeploymentBlockBtn">
                            <i class="bi bi-plus-lg me-1"></i> Ajouter un bloc
                        </button>
                    </div>

                    <div id="deploymentBlocksContainer" class="d-grid gap-4">
                        @foreach($blocks as $index => $block)
                            <div class="deploy-block-row p-4 border rounded-4" data-deployment-block>
                                <div class="deploy-block-row__head">
                                    <div class="deploy-block-row__meta">
                                        <div class="deploy-block-row__index">{{ $index + 1 }}</div>
                                        <div>
                                            <div class="deploy-block-row__title">Bloc {{ $index + 1 }}</div>
                                            <div class="deploy-block-row__hint">Une équipe sur mesure, sans surcharge ni flou de répartition.</div>
                                        </div>
                                    </div>
                                    <button type="button" class="deploy-block-remove btn btn-sm btn-outline-danger border-0 rounded-circle" data-remove-deployment-block title="Supprimer ce bloc" @if($loop->first) style="display:none;" @endif>
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">Nombre d'équipes</label>
                                        <input type="number" name="deployment_blocks[{{ $index }}][team_count]" class="form-control rounded-3" min="1" max="100" value="{{ $block['team_count'] ?? 1 }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">Membres par équipe</label>
                                        <input type="number" name="deployment_blocks[{{ $index }}][team_size]" class="form-control rounded-3" min="3" max="20" value="{{ $block['team_size'] ?? 5 }}" required data-team-size-input>
                                    </div>
                                </div>

                                <div class="quota-card bg-white p-3 rounded-4 border shadow-sm">
                                    <div class="quota-card__label">
                                        <label class="form-label fw-bold text-muted small text-uppercase mb-0 px-1">Quotas par équipe</label>
                                        <span class="quota-card__badge"><i class="bi bi-sliders"></i> Répartition par profil</span>
                                    </div>
                                    <div class="row g-3 row-cols-1 row-cols-md-2">
                                        @foreach($deploymentProfiles as $profile)
                                            <div class="col">
                                                <div class="d-flex align-items-center justify-content-between p-2 border rounded-3 bg-white">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 2rem; height: 2rem;">
                                                            <i class="bi {{ $profile['icon'] }} text-secondary"></i>
                                                        </span>
                                                        <span class="small fw-semibold text-dark">{{ $profile['label'] }}</span>
                                                    </div>
                                                    <input type="number" name="deployment_blocks[{{ $index }}][quotas][{{ $profile['id'] }}]" class="form-control form-control-sm border-0 bg-transparent text-end fw-bold" style="width: 50px;" min="0" max="{{ $block['team_size'] ?? 5 }}" value="{{ min((int) ($block['quotas'][$profile['id']] ?? 1), (int) ($block['team_size'] ?? 5)) }}" data-quota-default="1" data-quota-input>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 justify-content-between align-items-center deploy-modal__footer">
                    <div class="deploy-modal__footnote d-none d-md-block">
                        La somme des quotas ne peut pas dépasser la taille d’équipe. La simulation reste alignée sur vos blocs.
                    </div>
                    <div class="d-flex gap-2 ms-auto">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-deploy-neutral">Simuler</button>
                        <button type="submit" class="btn btn-deploy-dark" formaction="{{ route('admin.operations.optimize') }}">Optimiser</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmation Reset -->
<div class="modal fade" id="resetDeploymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <form action="{{ route('admin.operations.reset') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-danger">Réinitialisation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-0 fw-bold">Voulez-vous vraiment vider tout le déploiement ?</p>
                    <p class="text-muted small mt-2">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Réinitialiser</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Transfert Simulation -->
<div class="modal fade" id="confirmSimMoveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-primary">Transfert d'agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p id="confirmSimMoveMessage" class="mb-0 fw-bold text-dark"></p>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 justify-content-between">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-modern-primary" id="confirmSimMoveConfirm">Confirmer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        let draggedPayload = null;
        let pendingMove = null;

        const dragAssignForm = document.getElementById('dragAssignForm');
        const dragUserId = document.getElementById('dragUserId');
        const dragTeamId = document.getElementById('dragTeamId');
        
        const dragSwapForm = document.getElementById('dragSwapForm');
        const swapUser1Id = document.getElementById('swapUser1Id');
        const swapUser2Id = document.getElementById('swapUser2Id');

        const moveMemberModalElement = document.getElementById('moveMemberModal');
        const moveMemberModalMessage = document.getElementById('moveMemberModalMessage');
        const confirmMoveButton = document.getElementById('confirmMoveButton');
        const moveMemberModal = moveMemberModalElement && window.bootstrap ? new bootstrap.Modal(moveMemberModalElement) : null;

        function clearDropStates() {
            document.querySelectorAll('.drop-slot').forEach(s => s.classList.remove('drop-ready', 'drop-blocked'));
            document.querySelectorAll('.drop-zone-unassigned').forEach(z => z.classList.remove('drop-ready'));
        }

        function submitMove(userId, teamId) {
            dragUserId.value = userId;
            dragTeamId.value = teamId ?? '';
            dragAssignForm.submit();
        }

        function submitSwap(user1Id, user2Id) {
            swapUser1Id.value = user1Id;
            swapUser2Id.value = user2Id;
            dragSwapForm.submit();
        }

        function openMoveConfirm(userId, teamId, userName, teamName, targetUserId = null, targetUserName = null) {
            pendingMove = { userId, teamId, targetUserId };
            
            const titleEl = moveMemberModalElement.querySelector('.modal-title');
            
            if (targetUserId) {
                if (titleEl) titleEl.textContent = "Permutation d'agents";
                moveMemberModalMessage.innerHTML = `Voulez-vous permuter l'agent <span class="text-primary">${userName}</span> avec l'agent <span class="text-primary">${targetUserName}</span> ?`;
            } else {
                if (titleEl) titleEl.textContent = "Confirmation";
                moveMemberModalMessage.textContent = teamId ? `Déplacer ${userName} vers ${teamName} ?` : `Retirer ${userName} ?`;
            }
            
            if (moveMemberModal) moveMemberModal.show(); else {
                if (targetUserId) submitSwap(userId, targetUserId); else submitMove(userId, teamId);
            }
        }

        if (confirmMoveButton) {
            confirmMoveButton.addEventListener('click', () => {
                if (!pendingMove) return;
                if (moveMemberModal) moveMemberModal.hide();
                if (pendingMove.targetUserId) {
                    submitSwap(pendingMove.userId, pendingMove.targetUserId);
                } else {
                    submitMove(pendingMove.userId, pendingMove.teamId);
                }
            });
        }

        document.querySelectorAll('.drag-card').forEach(card => {
            card.addEventListener('dragstart', (e) => {
                const ds = card.dataset;
                draggedPayload = { 
                    userId: ds.userId, 
                    userName: ds.userName || card.textContent.trim(), 
                    profilId: ds.profilId, 
                    sourceTeamId: ds.sourceTeamId || '' 
                };
                card.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            });
            card.addEventListener('dragend', () => { card.classList.remove('dragging'); clearDropStates(); });
        });

        document.querySelectorAll('.drop-slot').forEach(slot => {
            slot.addEventListener('dragover', (e) => {
                if (!draggedPayload) return;
                // Allow if same profile type (either vacant or swap)
                if (draggedPayload.profilId === slot.dataset.profilId) {
                    e.preventDefault(); slot.classList.add('drop-ready');
                } else { slot.classList.add('drop-blocked'); }
            });
            slot.addEventListener('dragleave', () => clearDropStates());
            slot.addEventListener('drop', (e) => {
                e.preventDefault();
                if (!draggedPayload || draggedPayload.sourceTeamId === slot.dataset.teamId) return;
                
                const targetTeamName = slot.closest('.glass-card').querySelector('.team-title').textContent.trim();
                const occupiedBy = slot.querySelector('.drag-card');
                
                if (occupiedBy) {
                    // It's a swap
                    const targetUserId = occupiedBy.dataset.userId;
                    const targetUserName = occupiedBy.dataset.userName || occupiedBy.textContent.trim();
                    openMoveConfirm(draggedPayload.userId, slot.dataset.teamId, draggedPayload.userName, targetTeamName, targetUserId, targetUserName);
                } else {
                    // It's a simple move
                    openMoveConfirm(draggedPayload.userId, slot.dataset.teamId, draggedPayload.userName, targetTeamName);
                }
            });
        });

        document.querySelectorAll('.drop-zone-unassigned').forEach(zone => {
            zone.addEventListener('dragover', (e) => { if (draggedPayload?.sourceTeamId) e.preventDefault(); });
            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                if (draggedPayload?.sourceTeamId) openMoveConfirm(draggedPayload.userId, '', draggedPayload.userName, '');
            });
        });

        // Simulation logic
        document.querySelectorAll('.simulation-members').forEach(container => {
            container.addEventListener('dragover', e => e.preventDefault());
            container.addEventListener('drop', e => {
                e.preventDefault();
                const simDragged = document.querySelector('.sim-member.dragging');
                if (!simDragged || simDragged.dataset.simTeamIndex === container.dataset.simTeamIndex) return;
                
                const mName = simDragged.querySelector('.member-name').textContent;
                const cModalElement = document.getElementById('confirmSimMoveModal');
                const cModal = new bootstrap.Modal(cModalElement);
                document.getElementById('confirmSimMoveMessage').textContent = `Transférer ${mName} ?`;
                cModal.show();
                document.getElementById('confirmSimMoveConfirm').onclick = () => {
                    container.appendChild(simDragged);
                    simDragged.dataset.simTeamIndex = container.dataset.simTeamIndex;
                    cModal.hide();
                };
            });
        });

        // Block Management
        const blockContainer = document.getElementById('deploymentBlocksContainer');
        const addBlockBtn = document.getElementById('addDeploymentBlockBtn');
        if (addBlockBtn && blockContainer) {
            addBlockBtn.onclick = () => {
                const firstBlock = blockContainer.querySelector('[data-deployment-block]');
                if (!firstBlock) return;
                const clone = firstBlock.cloneNode(true);
                clone.querySelectorAll('input').forEach(i => i.value = i.dataset.quotaDefault || 1);
                blockContainer.appendChild(clone);
                refreshIndexes();
            };
            blockContainer.onclick = e => {
                const btn = e.target.closest('[data-remove-deployment-block]');
                if (btn && blockContainer.querySelectorAll('[data-deployment-block]').length > 1) {
                    btn.closest('[data-deployment-block]').remove(); refreshIndexes();
                }
            };

            function syncQuotaLimits(block) {
                const teamSizeInput = block.querySelector('[data-team-size-input]');
                const quotas = block.querySelectorAll('[data-quota-input]');
                const teamSize = Math.max(3, parseInt(teamSizeInput?.value || '3', 10) || 3);

                if (teamSizeInput) {
                    teamSizeInput.value = teamSize;
                }

                quotas.forEach((quotaInput) => {
                    quotaInput.max = String(teamSize);
                    const currentValue = parseInt(quotaInput.value || '0', 10) || 0;
                    if (currentValue > teamSize) {
                        quotaInput.value = teamSize;
                    }
                });
            }

            function refreshIndexes() {
                blockContainer.querySelectorAll('[data-deployment-block]').forEach((b, i) => {
                    b.querySelectorAll('input').forEach(inp => inp.name = inp.name.replace(/deployment_blocks\[\d+\]/, `deployment_blocks[${i}]`));
                    b.querySelector('.badge').textContent = `Bloc ${i+1}`;
                    b.querySelector('[data-remove-deployment-block]').style.display = i === 0 ? 'none' : 'block';
                    syncQuotaLimits(b);
                });
            }

            blockContainer.addEventListener('input', (event) => {
                const block = event.target.closest('[data-deployment-block]');
                if (!block) return;

                if (event.target.matches('[data-team-size-input]')) {
                    syncQuotaLimits(block);
                }
            });

            blockContainer.querySelectorAll('[data-deployment-block]').forEach(syncQuotaLimits);
        }
    })();
</script>
@endpush
