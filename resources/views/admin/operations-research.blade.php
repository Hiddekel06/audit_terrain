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
                <form action="{{ route('admin.operations.auto') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-modern-primary">
                        <i class="bi bi-magic me-2"></i> Répartition automatique
                    </button>
                </form>
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
                                    $profilesNeeded = [1 => 'Chef d\'équipe', 2 => 'Auditeur', 3 => 'Support'];
                                    $currentMembers = $team->members->groupBy('profil_id');
                                @endphp

                                @foreach($profilesNeeded as $id => $label)
                                    @php $member = $currentMembers->get($id)?->first(); @endphp
                                    <div class="member-slot-modern p-3 mb-2 d-flex align-items-center gap-3">
                                        <div class="avatar-modern text-primary bg-primary bg-opacity-10">
                                            <i class="bi {{ $id == 1 ? 'bi-person-badge' : ($id == 2 ? 'bi-person' : 'bi-tools') }}"></i>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            @if($member)
                                                <div class="small fw-bold text-dark text-truncate">{{ $member->prenom }} {{ $member->nom }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ $label }}</div>
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
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-3">
                                @if($team->members->count() < 3)
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
            
            <div class="glass-card overflow-hidden">
                <div class="list-group list-group-flush bg-transparent">
                    @forelse($unassignedUsers as $user)
                        <div class="list-group-item p-3 border-0 border-bottom bg-transparent hover-bg-light transition-all">
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
@endsection
