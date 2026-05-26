@extends('layouts.admin')

@section('admin-title', 'Recherche Opérationnelle')
@section('admin-subtitle', 'Gestion et optimisation des équipes terrain')

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
        border-color: rgba(255, 255, 255, 0.5);
    }
    .btn-modern-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        border: none;
        border-radius: 9999px;
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
        background: rgba(255, 255, 255, 0.5);
        border: 1px solid rgba(37, 99, 235, 0.2);
        color: #2563eb;
        border-radius: 9999px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-modern-outline:hover {
        background: #f8fafc;
        border-color: #2563eb;
        transform: translateY(-2px);
    }
    .badge-status {
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .member-slot-modern {
        background: rgba(255, 255, 255, 0.5);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 1rem;
        transition: all 0.2s ease;
    }
    .member-slot-modern:hover {
        background: white;
        border-color: rgba(37, 99, 235, 0.3);
    }
    .avatar-modern {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .drag-card {
        cursor: grab;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .drag-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.08);
    }

    .drag-card.dragging {
        opacity: 0.5;
        transform: scale(0.98);
    }

    .simulation-members.drop-ready {
        background: rgba(34, 197, 94, 0.08);
        border-radius: 0.5rem;
        outline: 2px dashed rgba(34, 197, 94, 0.4);
    }

    .drop-slot.drop-ready {
        border-color: rgba(34, 197, 94, 0.5);
        background: rgba(240, 253, 244, 0.85);
        box-shadow: inset 0 0 0 2px rgba(34, 197, 94, 0.15);
    }

    .drop-slot.drop-blocked {
        border-color: rgba(239, 68, 68, 0.4);
        background: rgba(254, 242, 242, 0.9);
    }

    .drop-zone-unassigned.drop-ready {
        outline: 2px dashed rgba(37, 99, 235, 0.45);
        outline-offset: -10px;
        background: rgba(239, 246, 255, 0.55);
    }

    .action-mini-btn {
        width: 30px;
        height: 30px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(37, 99, 235, 0.16);
        background: white;
        color: #2563eb;
        transition: all 0.2s ease;
    }

    .action-mini-btn:hover {
        transform: translateY(-1px);
        border-color: rgba(37, 99, 235, 0.3);
        background: #eff6ff;
        color: #1d4ed8;
    }

    .deploy-modal .modal-content {
        border-radius: 28px;
        overflow: hidden;
        border: 1px solid rgba(37, 99, 235, 0.08);
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.12);
    }

    .deploy-modal__header {
        background: linear-gradient(180deg, rgba(37, 99, 235, 0.08) 0%, rgba(37, 99, 235, 0.02) 100%);
    }

    .deploy-modal__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.08);
        color: #1d4ed8;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .deploy-mode-card {
        border: 1px solid rgba(37, 99, 235, 0.08);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .deploy-block-row {
        background: rgba(248, 250, 252, 0.9);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        padding: 1rem;
    }

    .deploy-block-remove {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        border: 1px solid rgba(239, 68, 68, 0.15);
        background: rgba(254, 242, 242, 0.85);
        color: #dc2626;
    }
</style>

<div class="container-fluid p-0 pb-5">
    <!-- Header Actions -->
    <div class="glass-card p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <form action="{{ route('admin.operations.research') }}" method="GET" id="regionFilterForm">
                    <label class="form-label fw-bold small text-uppercase text-muted ms-2">Filtrer par région</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                            <i class="bi bi-geo-alt text-primary"></i>
                        </span>
                        <select name="region_id" class="form-select border-start-0 rounded-end-pill bg-white" onchange="this.form.submit()">
                            <option value="">Toutes les régions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" @selected($selectedRegionId == $region->id)>{{ $region->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-8 text-md-end d-flex gap-2 justify-content-md-end pt-md-4">
                <button type="button" class="btn btn-modern-primary" data-bs-toggle="modal" data-bs-target="#autoDeployModal">
                    <i class="bi bi-magic me-2"></i> Simuler la répartition
                </button>
                <button type="button" class="btn btn-modern-outline" data-bs-toggle="modal" data-bs-target="#resetDeploymentModal">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Réinitialiser
                </button>
                <button type="button" class="btn btn-modern-outline" data-bs-toggle="modal" data-bs-target="#createTeamModal">
                    <i class="bi bi-plus-lg me-2"></i> Nouvelle équipe
                </button>
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
        <div class="glass-card p-4 mb-4">
            <h4 class="h6 fw-bold mb-3 text-primary">Aperçu de simulation</h4>
            <div class="row g-3">
                @foreach($simulationTeams as $tIndex => $team)
                    <div class="col-md-6 col-xl-4">
                        <div class="border rounded-4 p-3 h-100 bg-white shadow-sm simulation-team" data-sim-team-index="{{ $tIndex }}">
                            <div class="fw-bold text-dark mb-2">{{ $team['nom'] }}</div>
                            <div class="simulation-members" data-sim-team-index="{{ $tIndex }}" style="display: flex; flex-direction: column; gap: 0.75rem;">
                                @forelse($team['members'] as $mIndex => $member)
                                    <div class="d-flex justify-content-between gap-2 py-3 px-2 sim-member drag-card"
                                         style="background: #f8fafc; border-radius: 0.5rem; cursor: grab; align-items: center;"
                                         draggable="true"
                                         data-sim-user-id="{{ $member['id'] }}"
                                         data-sim-team-index="{{ $tIndex }}"
                                         data-sim-member-index="{{ $mIndex }}"
                                    >
                                        <span class="sim-member-role" style="font-size: 0.9rem; font-weight: 500; color: #666;">{{ $member['role'] }}</span>
                                        <span class="fw-semibold text-dark sim-member-name">{{ $member['name'] }}</span>
                                    </div>
                                @empty
                                    <div class="text-muted" style="padding: 1rem; text-align: center;">Aucun membre simulé pour cette équipe.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endisset

    <form id="dragAssignForm" action="{{ route('admin.operations.assign') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="user_id" id="dragUserId">
        <input type="hidden" name="team_id" id="dragTeamId">
    </form>

    <div class="modal fade" id="moveMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary">Confirmer le déplacement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-3">
                    <p class="mb-2" id="moveMemberModalMessage">Confirmer le déplacement ?</p>
                    <div class="small text-muted">
                        L'agent sera déplacé vers la nouvelle équipe après confirmation.
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-modern-primary" id="confirmMoveButton">Confirmer</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade deploy-modal" id="autoDeployModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content glass-card border-0">
                <form action="{{ route('admin.operations.simulate') }}" method="POST">
                    @csrf
                    @php
                        $blocks = old('deployment_blocks', $deploymentBlocks ?? []);
                        $deploymentProfiles = $deploymentProfiles ?? [];
                        if (empty($blocks)) {
                            $blocks = [[
                                'team_count' => 3,
                                'team_size' => 3,
                            ]];
                        }
                    @endphp
                    <div class="modal-header border-0 pt-4 px-4 deploy-modal__header">
                        <div>
                            <div class="deploy-modal__eyebrow mb-2">
                                <i class="bi bi-sliders2"></i> Déploiement paramétrable
                            </div>
                            <h5 class="modal-title fw-bold text-primary mb-0">Prévisualiser avant d'appliquer</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 p-md-4">
                        

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <div class="fw-bold text-dark">Blocs de répartition</div>
                                <div class="small text-muted">Chaque bloc décrit un lot d'équipes avec la même taille.</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-modern-outline rounded-pill px-3" id="addDeploymentBlockBtn">
                                <i class="bi bi-plus-lg me-1"></i> Ajouter un bloc
                            </button>
                        </div>

                        <div id="deploymentBlocksContainer" class="d-grid gap-3">
                            @foreach($blocks as $index => $block)
                                <div class="deploy-block-row" data-deployment-block>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <div class="small text-uppercase text-muted fw-semibold">Bloc {{ $index + 1 }}</div>
                                            <div class="fw-semibold text-dark">Paramétrer un lot d'équipes</div>
                                        </div>
                                        <button type="button" class="deploy-block-remove" data-remove-deployment-block title="Supprimer ce bloc" @if($loop->first) style="display:none;" @endif>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-muted small text-uppercase">Nombre d'équipes</label>
                                            <input type="number" name="deployment_blocks[{{ $index }}][team_count]" class="form-control rounded-pill border-light bg-white px-4" min="1" max="100" value="{{ $block['team_count'] ?? 1 }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-muted small text-uppercase">Taille de l'équipe</label>
                                            <input type="number" name="deployment_blocks[{{ $index }}][team_size]" class="form-control rounded-pill border-light bg-white px-4" min="3" max="20" value="{{ $block['team_size'] ?? 3 }}" required>
                                        </div>
                                        <div class="col-12">
                                            <div class="row g-2 mt-2">
                                                @foreach($deploymentProfiles as $profileIndex => $profile)
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-bold text-muted small text-uppercase">{{ $profile['label'] }} par équipe</label>
                                                        <input
                                                            type="number"
                                                            name="deployment_blocks[{{ $index }}][quotas][{{ $profile['id'] }}]"
                                                            class="form-control rounded-pill border-light bg-white px-4"
                                                            min="0"
                                                            max="20"
                                                            value="{{ $block['quotas'][$profile['id']] ?? ($loop->last ? (isset($block['team_size']) ? max(0, $block['team_size'] - max(0, count($deploymentProfiles) - 1)) : 1) : 1) }}"
                                                            data-quota-default="{{ $loop->last ? (isset($block['team_size']) ? max(0, $block['team_size'] - max(0, count($deploymentProfiles) - 1)) : 1) : 1 }}"
                                                            data-profile-code="{{ $profile['code'] }}"
                                                        >
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-primary border-0 rounded-4 mt-4 mb-0 py-3 px-4 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-info-circle me-2"></i>
                            La prévisualisation sert à vérifier le résultat. L'optimisation automatique s'appuie uniquement sur les données libres.
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4 justify-content-between flex-wrap gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <button type="submit" class="btn btn-modern-primary rounded-pill px-4">
                                <i class="bi bi-eye me-2"></i> Prévisualiser les blocs
                            </button>
                            <button type="submit" class="btn btn-outline-primary rounded-pill px-4" formaction="{{ route('admin.operations.optimize') }}" formnovalidate>
                                <i class="bi bi-stars me-2"></i> Optimiser automatiquement
                            </button>
                            <button type="submit" class="btn btn-secondary rounded-pill px-4 opacity-75" formaction="{{ route('admin.operations.auto') }}" disabled aria-disabled="true" title="Déploiement temporairement désactivé">
                                <i class="bi bi-check2-circle me-2"></i> Appliquer le déploiement
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resetDeploymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <form action="{{ route('admin.operations.reset') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-danger">Réinitialiser le déploiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="mb-2 fw-semibold">Voulez-vous vraiment remettre tout le module à zéro ?</p>
                        <div class="small text-muted">
                            Cette action va retirer tous les agents de leurs équipes et supprimer les équipes existantes. Vous repartirez avec une page où personne n'est assigné.
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4 justify-content-between">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Réinitialiser
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <form action="{{ route('admin.operations.profile.update') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-primary">Modifier le profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="user_id" id="profileUserId">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Profil</label>
                            <select name="profil_id" id="profileSelect" class="form-select rounded-pill border-light bg-light px-4" required>
                                <option value="">Sélectionner un profil</option>
                                @foreach($profils as $profil)
                                    <option value="{{ $profil->id }}">{{ $profil->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Identité</label>
                            <div class="small text-muted">
                                <div><span class="fw-semibold">Nom :</span> <span id="profileNom">—</span></div>
                                <div><span class="fw-semibold">Prénom :</span> <span id="profilePrenom">—</span></div>
                                <div><span class="fw-semibold">Matricule :</span> <span id="profileMatricule">—</span></div>
                                <div><span class="fw-semibold">Structure :</span> <span id="profileStructure">—</span></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Choix régionaux</label>
                            <div id="profileChoicesList" class="small text-muted">Aucun choix régional enregistré.</div>
                        </div>
                        <div class="small text-muted">
                            La modification prend effet immédiatement et respecte les contraintes de l'équipe.
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4 justify-content-between">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-modern-primary">
                            <i class="bi bi-check2-circle me-2"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Liste des Équipes -->
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                <h3 class="h5 fw-bold mb-0 d-flex align-items-center">
                    <span class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </span>
                    Équipes <span class="badge bg-light text-primary ms-2 rounded-pill">{{ $teams->count() }}</span>
                </h3>
            </div>
            
            <div class="row g-3">
                @forelse($teams as $team)
                    <div class="col-md-6">
                        <div class="glass-card h-100 p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="h6 fw-bold mb-1 text-dark text-uppercase letter-spacing-wider">{{ $team->nom }}</h4>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small text-muted">
                                            <i class="bi bi-geo-alt-fill me-1 text-primary"></i> {{ $team->region?->nom ?? 'Région non définie' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical fs-5"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 overflow-hidden">
                                        <li>
                                            <form action="{{ route('admin.operations.team.destroy', $team) }}" method="POST" onsubmit="return confirm('Supprimer cette équipe et libérer ses membres ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger py-2 px-3">
                                                    <i class="bi bi-trash me-2"></i> Supprimer l'équipe
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="members-list">
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
                                        class="member-slot-modern drop-slot p-3 mb-2 d-flex align-items-center gap-3"
                                        data-team-id="{{ $team->id }}"
                                        data-profil-id="{{ $id }}"
                                        data-has-member="{{ $member ? '1' : '0' }}"
                                    >
                                        <div class="avatar-modern text-primary bg-primary bg-opacity-10">
                                            <i class="bi {{ $icon }}"></i>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            @if($member)
                                                <div
                                                    class="small fw-bold text-dark text-truncate drag-card"
                                                    draggable="true"
                                                    data-user-id="{{ $member->id }}"
                                                    data-user-name="{{ $member->prenom }} {{ $member->nom }}"
                                                    data-profil-id="{{ $id }}"
                                                    data-source-team-id="{{ $team->id }}"
                                                    title="Glisser cet agent vers une autre équipe"
                                                >
                                                    {{ $member->prenom }} {{ $member->nom }}
                                                </div>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $label }} - glisser pour déplacer</div>
                                            @else
                                                <div class="small text-muted italic fw-medium">{{ $label }}</div>
                                                <div class="text-danger fw-bold" style="font-size: 0.7rem;">Poste vacant</div>
                                            @endif
                                        </div>
                                        @if($member)
                                            <form action="{{ route('admin.operations.assign') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $member->id }}">
                                                <input type="hidden" name="team_id" value="">
                                                <button type="submit" class="btn btn-sm text-danger opacity-50 hover-opacity-100" title="Retirer de l'équipe">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                            <button
                                                type="button"
                                                class="action-mini-btn"
                                                title="Modifier le profil"
                                                data-bs-toggle="modal"
                                                data-bs-target="#profileModal"
                                                data-user-id="{{ $member->id }}"
                                                data-user-name="{{ $member->prenom }} {{ $member->nom }}"
                                                data-user-prenom="{{ $member->prenom }}"
                                                data-user-nom="{{ $member->nom }}"
                                                data-user-matricule="{{ $member->matricule ?? '—' }}"
                                                data-structure-name="{{ $member->ministere?->nom ?? '—' }}"
                                                data-direction-name="{{ $member->direction ?? '—' }}"
                                                data-current-profil-id="{{ $member->profil_id }}"
                                                data-region-choices='@json($member->regionChoices->sortBy("ordre")->pluck("region.nom"))'
                                                data-initial-profil-name="{{ $member->initialProfil?->libelle ?? '' }}"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-3">
                                @if($team->members->count() < max(1, count($deploymentProfiles)))
                                    <span class="badge-status bg-warning bg-opacity-10 text-warning">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Incomplète
                                    </span>
                                @else
                                    <span class="badge-status bg-success bg-opacity-10 text-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Opérationnelle
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="glass-card text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                                <i class="bi bi-people text-muted fs-1"></i>
                            </div>
                            <h5 class="text-dark fw-bold">Aucune équipe</h5>
                            <p class="text-muted">Utilisez la répartition automatique ou créez une équipe manuellement.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Candidats Disponibles -->
        <div class="col-lg-4">
            <div class="d-flex align-items-center mb-3 px-2">
                <h3 class="h5 fw-bold mb-0 d-flex align-items-center">
                    <span class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3">
                        <i class="bi bi-person-plus"></i>
                    </span>
                    Candidats Libres <span class="badge bg-light text-success ms-2 rounded-pill">{{ $unassignedUsers->count() }}</span>
                </h3>
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
                            title="Glisser cet agent vers une équipe"
                        >
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-modern bg-light text-dark">
                                    {{ substr($user->prenom, 0, 1) }}{{ substr($user->nom, 0, 1) }}
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-dark text-truncate small">{{ $user->prenom }} {{ $user->nom }}</div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" style="font-size: 0.65rem;">
                                            {{ $user->profil?->libelle }}
                                        </span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-modern-outline px-3 py-1" type="button" data-bs-toggle="dropdown">
                                        Assigner
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-xl rounded-4 overflow-hidden" style="max-height: 250px; overflow-y: auto;">
                                        <li class="dropdown-header text-uppercase small letter-spacing-wider">Choisir une équipe</li>
                                        @foreach($teams as $team)
                                            <li>
                                                <form action="{{ route('admin.operations.assign') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    <input type="hidden" name="team_id" value="{{ $team->id }}">
                                                    <button type="submit" class="dropdown-item py-2 px-3 small d-flex justify-content-between align-items-center">
                                                        <span>{{ $team->nom }}</span>
                                                        <i class="bi bi-plus-circle text-primary opacity-50"></i>
                                                    </button>
                                                </form>
                                            </li>
                                        @endforeach
                                        @if($teams->isEmpty())
                                            <li><span class="dropdown-item disabled small">Aucune équipe active</span></li>
                                        @endif
                                    </ul>
                                </div>
                                <button
                                    type="button"
                                    class="action-mini-btn"
                                    title="Modifier le profil"
                                    data-bs-toggle="modal"
                                    data-bs-target="#profileModal"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->prenom }} {{ $user->nom }}"
                                    data-user-prenom="{{ $user->prenom }}"
                                    data-user-nom="{{ $user->nom }}"
                                    data-user-matricule="{{ $user->matricule ?? '—' }}"
                                    data-structure-name="{{ $user->ministere?->nom ?? '—' }}"
                                    data-direction-name="{{ $user->direction ?? '—' }}"
                                    data-current-profil-id="{{ $user->profil_id }}"
                                    data-region-choices='@json($user->regionChoices->sortBy("ordre")->pluck("region.nom"))'
                                    data-initial-profil-name="{{ $user->initialProfil?->libelle ?? '' }}"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center">
                            <i class="bi bi-emoji-smile text-muted fs-2 mb-3 d-block"></i>
                            <p class="text-muted small mb-0">Tous les candidats ont été assignés !</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        let draggedPayload = null;
        let pendingMove = null;

        const dragAssignForm = document.getElementById('dragAssignForm');
        const dragUserId = document.getElementById('dragUserId');
        const dragTeamId = document.getElementById('dragTeamId');
        const moveMemberModalElement = document.getElementById('moveMemberModal');
        const moveMemberModalMessage = document.getElementById('moveMemberModalMessage');
        const confirmMoveButton = document.getElementById('confirmMoveButton');
        const profileModalElement = document.getElementById('profileModal');
        const profileUserId = document.getElementById('profileUserId');
        const profileSelect = document.getElementById('profileSelect');
        const moveMemberModal = moveMemberModalElement && window.bootstrap ? new bootstrap.Modal(moveMemberModalElement) : null;
        const profileModal = profileModalElement && window.bootstrap ? new bootstrap.Modal(profileModalElement) : null;

        function clearDropStates() {
            document.querySelectorAll('.drop-slot').forEach((slot) => {
                slot.classList.remove('drop-ready', 'drop-blocked');
            });

            document.querySelectorAll('.drop-zone-unassigned').forEach((zone) => {
                zone.classList.remove('drop-ready');
            });
        }

        function submitMove(userId, teamId) {
            dragUserId.value = userId;
            dragTeamId.value = teamId ?? '';
            dragAssignForm.submit();
        }

        function openMoveConfirm(userId, teamId, userName, teamName) {
            pendingMove = { userId, teamId };

            if (moveMemberModalMessage) {
                moveMemberModalMessage.textContent = teamId
                    ? `Confirmer le déplacement de ${userName} vers ${teamName} ?`
                    : `Confirmer le retrait de ${userName} vers les candidats libres ?`;
            }

            if (moveMemberModal) {
                moveMemberModal.show();
            } else {
                submitMove(userId, teamId);
            }
        }

        if (confirmMoveButton) {
            confirmMoveButton.addEventListener('click', () => {
                if (!pendingMove) return;
                if (moveMemberModal) {
                    moveMemberModal.hide();
                }
                submitMove(pendingMove.userId, pendingMove.teamId);
            });
        }

        if (profileModalElement) {
            profileModalElement.addEventListener('show.bs.modal', (event) => {
                const button = event.relatedTarget;
                if (!button) return;

                const userId = button.getAttribute('data-user-id');
                const currentProfilId = button.getAttribute('data-current-profil-id');
                const userNom = button.getAttribute('data-user-nom') || '';
                const userPrenom = button.getAttribute('data-user-prenom') || '';
                const userMatricule = button.getAttribute('data-user-matricule') || '';
                const structureName = button.getAttribute('data-structure-name') || '';

                if (profileUserId) {
                    profileUserId.value = userId || '';
                }

                if (profileSelect) {
                    profileSelect.value = currentProfilId || '';
                }

                // Initial profil
                const initialName = button.getAttribute('data-initial-profil-name') || '';
                const initialLabel = document.getElementById('profileInitialLabel');
                if (initialLabel) {
                    initialLabel.textContent = initialName || '—';
                }

                const nomLabel = document.getElementById('profileNom');
                const prenomLabel = document.getElementById('profilePrenom');
                const matriculeLabel = document.getElementById('profileMatricule');
                const structureLabel = document.getElementById('profileStructure');
                if (nomLabel) nomLabel.textContent = userNom || '—';
                if (prenomLabel) prenomLabel.textContent = userPrenom || '—';
                if (matriculeLabel) matriculeLabel.textContent = userMatricule || '—';
                if (structureLabel) structureLabel.textContent = structureName || '—';
            });
        }

        // Populate regional choices in profile modal
        if (profileModalElement) {
            profileModalElement.addEventListener('show.bs.modal', (event) => {
                const button = event.relatedTarget;
                if (!button) return;

                const regionChoicesJson = button.getAttribute('data-region-choices') || '[]';
                try {
                    const choices = JSON.parse(regionChoicesJson || '[]');
                    const container = document.getElementById('profileChoicesList');
                    if (container) {
                        if (Array.isArray(choices) && choices.length > 0) {
                            container.innerHTML = choices.map((c, i) => `<div>${i+1}. ${c}</div>`).join('');
                        } else {
                            container.innerHTML = 'Aucun choix régional enregistré.';
                        }
                    }
                } catch (err) {
                    // ignore
                }
            });
        }

        document.querySelectorAll('.drag-card').forEach((card) => {
            card.addEventListener('dragstart', (event) => {
                draggedPayload = {
                    userId: card.dataset.userId,
                    userName: card.dataset.userName || '',
                    profilId: card.dataset.profilId,
                    sourceTeamId: card.dataset.sourceTeamId || '',
                };

                card.classList.add('dragging');
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', JSON.stringify(draggedPayload));
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('dragging');
                clearDropStates();
            });
        });

        document.querySelectorAll('.drop-slot').forEach((slot) => {
            slot.addEventListener('dragover', (event) => {
                if (!draggedPayload) return;

                const targetProfilId = slot.dataset.profilId;
                const hasMember = slot.dataset.hasMember === '1';

                if (draggedPayload.profilId === targetProfilId && !hasMember) {
                    event.preventDefault();
                    slot.classList.add('drop-ready');
                    slot.classList.remove('drop-blocked');
                } else {
                    slot.classList.add('drop-blocked');
                    slot.classList.remove('drop-ready');
                }
            });

            slot.addEventListener('dragleave', () => {
                slot.classList.remove('drop-ready', 'drop-blocked');
            });

            slot.addEventListener('drop', (event) => {
                event.preventDefault();
                if (!draggedPayload) return;

                const targetTeamId = slot.dataset.teamId;
                const targetProfilId = slot.dataset.profilId;
                const hasMember = slot.dataset.hasMember === '1';
                const targetTeamName = slot.closest('.glass-card')?.querySelector('h4')?.textContent?.trim() || 'cette équipe';
                const sourceName = draggedPayload.userName || 'cet agent';

                if (draggedPayload.profilId !== targetProfilId || hasMember) {
                    clearDropStates();
                    return;
                }

                if (draggedPayload.sourceTeamId === targetTeamId) {
                    clearDropStates();
                    return;
                }

                clearDropStates();
                openMoveConfirm(draggedPayload.userId, targetTeamId, sourceName, targetTeamName);
            });
        });

        document.querySelectorAll('.drop-zone-unassigned').forEach((zone) => {
            zone.addEventListener('dragover', (event) => {
                if (!draggedPayload) return;

                event.preventDefault();
                zone.classList.add('drop-ready');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('drop-ready');
            });

            zone.addEventListener('drop', (event) => {
                event.preventDefault();
                if (!draggedPayload) return;

                zone.classList.remove('drop-ready');
                if (!draggedPayload.sourceTeamId) return;

                const sourceName = draggedPayload.userName || 'cet agent';
                openMoveConfirm(draggedPayload.userId, '', sourceName, 'les candidats libres');
            });
        });
    })();
</script>

<script>
    (function () {
        // Drag & drop for simulation preview: allow swapping/moving members between simulated teams
        const simMembers = document.querySelectorAll('.sim-member');
        let simDragged = null;

        function recalcSimulationSummary() {
            // Recalculate assigned counts and missingTotal based on DOM
            const summary = {
                teams: 0,
                requestedTotal: 0,
                assignedTotal: 0,
                assignedChefs: 0,
                assignedAuditeurs: 0,
                assignedSupports: 0,
            };

            document.querySelectorAll('.simulation-team').forEach((team) => {
                summary.teams++;
                const members = team.querySelectorAll('.sim-member');
                summary.requestedTotal += members.length;
                summary.assignedTotal += members.length;

                members.forEach((m) => {
                    const role = (m.querySelector('.sim-member-role')?.textContent || '').toLowerCase();
                    if (role.includes('chef')) summary.assignedChefs++;
                    else if (role.includes('auditeur')) summary.assignedAuditeurs++;
                    else summary.assignedSupports++;
                });
            });

            // Update badges if present
            const teamsBadge = document.querySelector('.badge.bg-primary');
            if (teamsBadge) teamsBadge.textContent = `Équipes simulées: ${summary.teams}`;
        }

        function clearDropStates() {
            document.querySelectorAll('.simulation-members').forEach((el) => {
                el.classList.remove('drop-ready');
            });
        }

        function handleDragStart(e) {
            const el = e.currentTarget;
            simDragged = {
                userId: el.getAttribute('data-sim-user-id'),
                teamIndex: parseInt(el.getAttribute('data-sim-team-index'), 10),
                memberIndex: parseInt(el.getAttribute('data-sim-member-index'), 10),
                node: el,
            };
            el.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            try { e.dataTransfer.setData('text/plain', JSON.stringify(simDragged)); } catch (err) {}
        }

        function handleDragEnd(e) {
            if (simDragged && simDragged.node) simDragged.node.classList.remove('dragging');
            simDragged = null;
            document.querySelectorAll('.simulation-members .sim-member').forEach((it) => it.classList.remove('drop-target'));
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            e.currentTarget.classList.add('drop-ready');
        }

        function handleDragLeave(e) {
            e.currentTarget.classList.remove('drop-ready');
        }

        // When dropping on a team container, open confirm modal to move member there
        function handleDropOnTeam(e) {
            e.preventDefault();
            if (!simDragged) return;

            const teamContainer = e.currentTarget;
            const targetTeamCard = teamContainer.closest('.simulation-team');
            const targetTeamIndex = parseInt(targetTeamCard.getAttribute('data-sim-team-index'), 10);

            // Don't move to same team
            if (targetTeamIndex === simDragged.teamIndex) {
                clearDropStates();
                return;
            }

            pendingOperation = {
                type: 'move',
                source: { node: simDragged.node, teamIndex: simDragged.teamIndex, memberIndex: simDragged.memberIndex },
                target: { teamContainer: teamContainer, teamIndex: targetTeamIndex },
            };

            const confirmSimMoveModalElement = document.getElementById('confirmSimMoveModal');
            if (confirmSimMoveModalElement && window.bootstrap) {
                const srcName = simDragged.node.querySelector('.sim-member-name')?.textContent || '';
                document.getElementById('confirmSimMoveMessage').textContent = `Confirmer le déplacement de ${srcName} vers cette équipe ?`;
                const m = new bootstrap.Modal(confirmSimMoveModalElement);
                m.show();
            } else {
                performPendingOperation();
            }

            clearDropStates();
        }

        function attachSimHandlers(el) {
            el.addEventListener('dragstart', handleDragStart);
            el.addEventListener('dragend', handleDragEnd);
        }

        // Attach handlers to initial members
        document.querySelectorAll('.sim-member').forEach((m) => {
            attachSimHandlers(m);
        });

        // Make team member containers accept drops
        document.querySelectorAll('.simulation-members').forEach((container) => {
            container.addEventListener('dragover', handleDragOver);
            container.addEventListener('dragleave', handleDragLeave);
            container.addEventListener('drop', handleDropOnTeam);
        });

        // initial recalc
        recalcSimulationSummary();

        // pending operation + modal
        let pendingOperation = null;

        function performPendingOperation() {
            if (!pendingOperation) return;

            if (pendingOperation.type === 'move') {
                const sourceNode = pendingOperation.source.node;
                const teamContainer = pendingOperation.target.teamContainer;
                const clone = sourceNode.cloneNode(true);
                attachSimHandlers(clone);
                teamContainer.appendChild(clone);
                sourceNode.parentNode.removeChild(sourceNode);
            }

            pendingOperation = null;
            recalcSimulationSummary();
        }

        // Setup modal listeners after a short delay to ensure Bootstrap is ready
        setTimeout(function() {
            const confirmSimMoveModalElement = document.getElementById('confirmSimMoveModal');
            const cancelBtn = document.getElementById('confirmSimMoveCancel');
            const confirmBtn = document.getElementById('confirmSimMoveConfirm');

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function () {
                    pendingOperation = null;
                    if (simDragged && simDragged.node) simDragged.node.classList.remove('dragging');
                });
            }

            if (confirmBtn) {
                confirmBtn.addEventListener('click', function () {
                    if (confirmSimMoveModalElement && window.bootstrap) {
                        const m = bootstrap.Modal.getInstance(confirmSimMoveModalElement) || new bootstrap.Modal(confirmSimMoveModalElement);
                        m.hide();
                    }
                    performPendingOperation();
                });
            }
        }, 100);
    })();
</script>

<script>
    (function () {
        const container = document.getElementById('deploymentBlocksContainer');
        const addButton = document.getElementById('addDeploymentBlockBtn');

        if (!container || !addButton) {
            return;
        }

        function refreshBlockIndexes() {
            const blocks = Array.from(container.querySelectorAll('[data-deployment-block]'));

            blocks.forEach((block, index) => {
                block.querySelectorAll('input').forEach((input) => {
                    input.name = input.name.replace(/deployment_blocks\[\d+\]/, `deployment_blocks[${index}]`);
                });

                const label = block.querySelector('.small.text-uppercase');
                if (label) {
                    label.textContent = `Bloc ${index + 1}`;
                }

                const removeButton = block.querySelector('[data-remove-deployment-block]');
                if (removeButton) {
                    removeButton.style.display = index === 0 ? 'none' : 'inline-flex';
                }
            });
        }

        addButton.addEventListener('click', function () {
            const firstBlock = container.querySelector('[data-deployment-block]');
            if (!firstBlock) {
                return;
            }

            const clone = firstBlock.cloneNode(true);
            // reset numeric fields to sensible defaults
            clone.querySelectorAll('input').forEach((input) => {
                if (input.name.includes('team_count')) {
                    input.value = 1;
                }

                if (input.name.includes('team_size')) {
                    input.value = 3;
                }

                if (input.name.includes('[quotas]')) {
                    const defaultQuota = input.dataset.quotaDefault;
                    input.value = defaultQuota !== undefined ? defaultQuota : 1;
                }
            });

            container.appendChild(clone);
            refreshBlockIndexes();
        });

        container.addEventListener('click', function (event) {
            const removeButton = event.target.closest('[data-remove-deployment-block]');

            if (!removeButton) {
                return;
            }

            const block = removeButton.closest('[data-deployment-block]');
            const blocks = container.querySelectorAll('[data-deployment-block]');

            if (block && blocks.length > 1) {
                block.remove();
                refreshBlockIndexes();
            }
        });

        refreshBlockIndexes();
    })();
</script>

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
                        <label class="form-label fw-bold text-muted small text-uppercase">Nom de l'équipe</label>
                        <input type="text" name="nom" class="form-control rounded-pill border-light bg-light px-4" placeholder="Ex: Équipe Alpha" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Région d'affectation</label>
                        <select name="region_id" class="form-select rounded-pill border-light bg-light px-4" required>
                            <option value="">Sélectionner une région</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" @selected($selectedRegionId == $region->id)>{{ $region->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-modern-primary">Créer l'équipe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal confirmation simulation move -->
<div class="modal fade" id="confirmSimMoveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-primary">Confirmer l'opération</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p id="confirmSimMoveMessage" class="mb-2">Confirmer ?</p>
                <div class="small text-muted">L'échange est seulement appliqué dans la prévisualisation et n'est pas enregistré en base.</div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 justify-content-between">
                <button type="button" class="btn btn-light rounded-pill px-4" id="confirmSimMoveCancel" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-modern-primary" id="confirmSimMoveConfirm">Confirmer</button>
            </div>
        </div>
    </div>
</div>
@endsection
