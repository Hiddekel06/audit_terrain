@extends('layouts.admin')

@section('admin-title', 'Gestion des candidats')
@section('admin-subtitle', 'Explorez et analysez tous les profils des candidats')

@section('content')
    
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(16, 185, 129, 0.1);
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    .btn-modern-primary {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        border: none;
        border-radius: 0.75rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
    }
    .btn-modern-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(5, 150, 105, 0.3);
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
        background: #f8fafc;
        border-color: #059669;
        color: #059669;
        transform: translateY(-1px);
    }
    .table-modern thead th {
        background: #f8fafc;
        border-bottom: 1px solid #edf2f7;
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem;
    }
    .table-modern tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
    .table-modern tbody tr:hover {
        background: rgba(16, 185, 129, 0.03);
    }
    .badge-modern {
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .filter-input {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 0.6rem 1rem !important;
        font-size: 0.875rem !important;
        transition: all 0.2s ease;
    }
    .filter-input:focus {
        border-color: #059669 !important;
        box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1) !important;
    }
    .action-mini-btn {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        transition: all 0.2s ease;
    }
    .action-mini-btn:hover { 
        border-color: #059669; 
        color: #059669; 
        background: #ecfdf5; 
    }
    .avatar-modern {
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
    }
    .source-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.6rem;
        border-radius: 0.5rem;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .source-badge.manual {
        background: #f1f5f9;
        color: #475569;
    }
    .source-badge.import {
        background: #ecfdf5;
        color: #059669;
    }
</style>

<div class="container-fluid p-0 pb-5">
    @if(session('success'))
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="alert alert-success border-0 shadow-sm rounded-4 px-4 py-3 animate__animated animate__fadeIn">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                </div>
            </div>
        </div>
    @endif
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold mb-0 text-dark">Liste des candidats</h2>
        <button type="button" class="btn btn-modern-primary" data-bs-toggle="modal" data-bs-target="#createAgentModal">
            <i class="bi bi-person-plus me-2"></i> Nouvel agent
        </button>
    </div>

    <!-- Filtres -->
    <div class="glass-card p-4 mb-4">
        <form method="GET" action="{{ route('admin.candidates.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Recherche</label>
                <input type="text" name="search" class="form-control filter-input" placeholder="Nom, matricule, email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Profil</label>
                <select name="profil_id" class="form-select filter-input">
                    <option value="">Tous les profils</option>
                    <option value="_none_" @selected(request('profil_id') === '_none_')>Profil non défini</option>
                    @foreach($profils as $profil)
                        <option value="{{ $profil->id }}" @selected(request('profil_id') == $profil->id)>{{ $profil->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Expérience</label>
                <select name="experience" class="form-select filter-input">
                    <option value="">Toutes</option>
                    @foreach($experiences as $value => $label)
                        <option value="{{ $value }}" @selected(request('experience') == $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Structure</label>
                <select name="ministere_id" class="form-select filter-input">
                    <option value="">Toutes les structures</option>
                    @foreach($ministeres as $ministere)
                        <option value="{{ $ministere->id }}" @selected(request('ministere_id') == $ministere->id)>{{ $ministere->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Direction</label>
                <select name="direction" class="form-select filter-input">
                    <option value="">Toutes les directions</option>
                    <option value="_none_" @selected(request('direction') === '_none_')>Sans direction</option>
                    @foreach($directions as $dir)
                        <option value="{{ $dir }}" @selected(request('direction') === $dir)>{{ $dir }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Téléphone</label>
                <select name="telephone_status" class="form-select filter-input">
                    <option value="">Tous</option>
                    <option value="missing" @selected(request('telephone_status') === 'missing')>Sans téléphone</option>
                    <option value="present" @selected(request('telephone_status') === 'present')>Avec téléphone</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Statut Validation</label>
                <select name="validation_status" class="form-select filter-input">
                    <option value="">Tous les statuts</option>
                    <option value="officiel_inscrit" @selected(request('validation_status') === 'officiel_inscrit')>Officiel & Inscrit</option>
                    <option value="officiel_attente" @selected(request('validation_status') === 'officiel_attente')>Officiel & En attente</option>
                    <option value="reserve" @selected(request('validation_status') === 'reserve')>Liste de Réserve</option>
                    <option value="_none_" @selected(request('validation_status') === '_none_')>Juste inscrit (Non validé)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">État du Profil</label>
                <select name="profile_completion" class="form-select filter-input">
                    <option value="">Tous les états</option>
                    <option value="completed" @selected(request('profile_completion') === 'completed')>Profil Rempli</option>
                    <option value="incomplete" @selected(request('profile_completion') === 'incomplete')>Profil Incomplet</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-modern-primary w-100 py-2">
                    <i class="bi bi-filter me-2"></i> Filtrer
                </button>
                @if(request()->anyFilled(['search', 'profil_id', 'experience', 'ministere_id', 'direction', 'telephone_status', 'region_id', 'ready_to_deploy', 'niveau_numerique', 'profile_completion']))
                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-modern-outline py-2" title="Réinitialiser">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
                <a href="{{ route('admin.candidates.export', request()->query()) }}" class="btn btn-modern-outline py-2" title="Exporter les résultats filtrés">
                    <i class="bi bi-download me-1"></i> Export
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="glass-card overflow-hidden">
        @if($candidates->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Candidat</th>
                            <th>Profil</th>
                            <th>Niveau</th>
                            <th>Structure</th>
                            <th>Affectation</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($candidates as $candidate)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($candidate->photo)
                                            <img src="{{ asset('storage/' . $candidate->photo) }}" alt="" class="avatar-modern" style="object-fit: cover; border: 2px solid #ecfdf5;">
                                        @else
                                            <div class="avatar-modern" style="background: #ecfdf5; color: #059669;">
                                                {{ substr($candidate->prenom, 0, 1) }}{{ substr($candidate->nom, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $candidate->nom }} {{ $candidate->prenom }}</div>
                                            <div class="text-muted small">{{ $candidate->matricule ?? $candidate->email }}</div>
                                            {{-- Métier retiré : champ non utilisé --}}
                                            @php($candidateSource = $candidate->source_type ?? 'manual')
                                            <div class="mt-2 d-flex flex-wrap gap-1">
                                                <span class="source-badge {{ $candidateSource === 'import' || $candidateSource === 'master_sync' ? 'import' : 'manual' }}">
                                                    <i class="bi bi-{{ $candidateSource === 'import' || $candidateSource === 'master_sync' ? 'upload' : 'person-check' }}"></i>
                                                    {{ $candidateSource === 'import' || $candidateSource === 'master_sync' ? 'Importé' : 'Inscrit' }}
                                                </span>

                                                @if($candidate->validation_status === 'officiel_inscrit')
                                                    <span class="source-badge" style="background: #dcfce7; color: #166534;">
                                                        <i class="bi bi-shield-check"></i> Officiel
                                                    </span>
                                                @elseif($candidate->validation_status === 'officiel_attente')
                                                    <span class="source-badge" style="background: #fef9c3; color: #854d0e;">
                                                        <i class="bi bi-shield-exclamation"></i> Officiel (Attente)
                                                    </span>
                                                @elseif($candidate->validation_status === 'reserve')
                                                    <span class="source-badge" style="background: #f1f5f9; color: #475569;">
                                                        <i class="bi bi-archive"></i> Réserve
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-modern" style="background: #ecfdf5; color: #059669;">
                                        {{ $candidate->profil->libelle ?? '—' }}
                                    </span>
                                    @if($candidate->profil_initial_id && $candidate->profil_id != $candidate->profil_initial_id)
                                        <div class="mt-1" title="Initialement inscrit comme : {{ $candidate->initialProfil->libelle ?? '—' }}">
                                            <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.6rem;">
                                                <i class="bi bi-exclamation-triangle"></i> Changé
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-modern" style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;">
                                        {{ $candidate->niveau_numerique ? ucfirst(str_replace('_', ' ', $candidate->niveau_numerique)) : '—' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-muted small fw-medium" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $candidate->ministere->nom ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        @if($candidate->team)
                                            <span style="color: #059669; font-weight: 700;"><i class="bi bi-people-fill me-1"></i> {{ $candidate->team->nom }}</span>
                                        @elseif(!empty($candidate->ready_to_deploy_all_regions))
                                            <span style="color: #059669;"><i class="bi bi-globe2 me-1"></i> National</span>
                                        @else
                                            <span class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $candidate->regionChoices->first()?->region->nom ?? '—' }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div style="display:flex; gap:0.5rem; justify-content:center; align-items:center;">
                                        <a href="{{ route('admin.candidates.show', array_merge(['user' => $candidate->id], request()->query())) }}" title="Voir" class="btn btn-sm btn-modern-outline" style="padding:0.45rem 0.6rem;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                        
                                        @if(Auth::guard('admin')->user()?->role === 'super_admin')
                                            <button
                                                type="button"
                                                class="action-mini-btn"
                                                title="Modifier le profil"
                                                data-bs-toggle="modal"
                                                data-bs-target="#profileModal"
                                                data-user-id="{{ $candidate->id }}"
                                                data-user-name="{{ $candidate->prenom }} {{ $candidate->nom }}"
                                                data-user-prenom="{{ $candidate->prenom }}"
                                                data-user-nom="{{ $candidate->nom }}"
                                                data-user-matricule="{{ $candidate->matricule ?? '—' }}"
                                                data-structure-id="{{ $candidate->ministere_id }}"
                                                data-structure-name="{{ $candidate->ministere?->nom ?? '—' }}"
                                                data-direction-name="{{ $candidate->direction ?? '—' }}"
                                                data-current-profil-id="{{ $candidate->profil_id }}"
                                                data-region-choices='@json($candidate->regionChoices->sortBy("ordre")->pluck("region.nom"))'
                                                data-initial-profil-name="{{ $candidate->initialProfil?->libelle ?? '' }}"
                                                style="margin-left:4px;"
                                            >
                                                <i class="bi bi-pencil-square" style="font-size:0.9rem"></i>
                                            </button>

                                            <button type="button" title="Supprimer" class="btn btn-sm btn-modern-outline btn-delete" style="padding:0.45rem 0.6rem; background:#fff5f5; border-color: rgba(153,27,27,0.1); color:#991b1b;" data-action="{{ route('admin.candidates.destroy', $candidate) }}" data-name="{{ $candidate->nom }} {{ $candidate->prenom }}">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                    <path d="M10 11v6"></path>
                                                    <path d="M14 11v6"></path>
                                                    <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Modernisée -->
            <div class="p-4 border-top border-light d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Affichage de <strong>{{ $candidates->firstItem() }}</strong> à <strong>{{ $candidates->lastItem() }}</strong> sur <strong>{{ $candidates->total() }}</strong> candidats
                </div>
                <div>
                    {{ $candidates->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="p-5 text-center">
                <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                    <i class="bi bi-search text-muted fs-1"></i>
                </div>
                <h5 class="text-dark fw-bold">Aucun résultat</h5>
                <p class="text-muted">Modifiez vos filtres pour trouver ce que vous cherchez.</p>
                <a href="{{ route('admin.candidates.index') }}" class="btn btn-modern-outline mt-2">Voir tous les candidats</a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Simple modal styles */
    .ct-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }
    .ct-modal {
        background: white;
        border-radius: 12px;
        max-width: 420px;
        width: 100%;
        padding: 1.25rem;
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    }
</style>

<div id="ct-confirm-delete" class="ct-modal-backdrop">
    <div class="ct-modal" role="dialog" aria-modal="true" aria-labelledby="ct-confirm-title">
        <h5 id="ct-confirm-title">Confirmer la suppression</h5>
        <p id="ct-confirm-message" class="text-muted">Voulez-vous vraiment supprimer ce candidat ? Cette action est irréversible.</p>

        <form id="ct-delete-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:1rem;">
                <button type="button" id="ct-cancel" class="btn btn-modern-outline">Annuler</button>
                <button type="submit" id="ct-confirm" class="btn btn-modern-primary" style="background:linear-gradient(135deg,#ef4444,#dc2626); border:none;">Supprimer</button>
            </div>
        </form>
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
                        <label class="form-label fw-bold text-muted small text-uppercase">Structure</label>
                        <select name="ministere_id" id="profileMinistereSelect" class="form-select rounded-pill border-light bg-light px-4">
                            <option value="">— Sélectionner —</option>
                            @foreach($ministeres as $ministere)
                                <option value="{{ $ministere->id }}">{{ $ministere->nom }}</option>
                            @endforeach
                        </select>
                    </div>
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
                        <label for="profileDirectionInput" class="form-label fw-bold text-muted small text-uppercase">Direction</label>
                        <input
                            type="text"
                            id="profileDirectionInput"
                            name="direction"
                            class="form-control rounded-pill border-light bg-light px-4"
                            placeholder="Saisir la direction"
                        >
                        <small class="text-muted d-block mt-1">Tu peux saisir une direction manuellement si tu veux l’ajouter ou la corriger.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Profil initial</label>
                        <div id="profileInitialLabel" class="small text-muted">—</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Identité</label>
                        <div class="small text-muted">
                            <div><span class="fw-semibold">Nom :</span> <span id="profileNom">—</span></div>
                            <div><span class="fw-semibold">Prénom :</span> <span id="profilePrenom">—</span></div>
                            <div><span class="fw-semibold">Matricule :</span> <span id="profileMatricule">—</span></div>
                            <div><span class="fw-semibold">Structure :</span> <span id="profileStructure">—</span></div>
                            <div><span class="fw-semibold">Direction :</span> <span id="profileDirection">—</span></div>
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

<!-- Modal Création Agent -->
<div class="modal fade" id="createAgentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card border-0">
            <form action="{{ route('admin.candidates.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary">Nouvel agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <style>
                        .form-label-modern { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.4rem; margin-left: 0.5rem; }
                        .form-control-modern, .form-select-modern { background: rgba(255, 255, 255, 0.5); border: 1px solid rgba(0, 0, 0, 0.05); border-radius: 1rem; padding: 0.75rem 1.25rem; transition: all 0.2s ease; }
                        .form-control-modern:focus, .form-select-modern:focus { background: white; border-color: #059669; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1); outline: none; }
                    </style>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-modern">Prénom</label>
                            <input type="text" name="prenom" class="form-control form-control-modern" value="{{ old('prenom') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Nom</label>
                            <input type="text" name="nom" class="form-control form-control-modern" value="{{ old('nom') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Matricule / CIN</label>
                            <input type="text" name="matricule" class="form-control form-control-modern" value="{{ old('matricule') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Email</label>
                            <input type="email" name="email" class="form-control form-control-modern" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Téléphone</label>
                            <input type="text" name="telephone" id="index-telephone" class="form-control form-control-modern js-phone-9" value="{{ old('telephone') }}" required inputmode="numeric" pattern="[0-9]{9}" maxlength="9">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Ministère / Structure</label>
                            <select name="ministere_id" class="form-select form-select-modern" required>
                                <option value="">Choisir...</option>
                                @foreach($ministeres as $m)
                                    <option value="{{ $m->id }}">{{ $m->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Direction</label>
                            <input type="text" name="direction" class="form-control form-control-modern" value="{{ old('direction') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Profil</label>
                            <select name="profil_id" class="form-select form-select-modern" required>
                                <option value="">Choisir...</option>
                                @foreach($profils as $p)
                                    <option value="{{ $p->id }}">{{ $p->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="p-3 rounded-4 bg-light text-muted small">
                                <i class="bi bi-info-circle me-1"></i> L'agent sera automatiquement marqué comme prêt pour <strong>toutes les régions</strong>.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-modern-primary">
                        <i class="bi bi-plus-lg me-2"></i> Ajouter l'agent
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function(){
        document.querySelectorAll('.js-phone-9').forEach(function(input) {
            input.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 9);
            });
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                var pasted = (e.clipboardData || window.clipboardData).getData('text');
                e.target.value = pasted.replace(/\D/g, '').slice(0, 9);
            });
        });

        const modal = document.getElementById('ct-confirm-delete');
        const form = document.getElementById('ct-delete-form');
        const message = document.getElementById('ct-confirm-message');
        const cancel = document.getElementById('ct-cancel');

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(){
                const action = this.dataset.action;
                const name = this.dataset.name || 'ce candidat';
                form.action = action;
                message.textContent = `Voulez-vous vraiment supprimer ${name} ? Cette action est irréversible.`;
                modal.style.display = 'flex';
            });
        });

        cancel.addEventListener('click', function(){
            modal.style.display = 'none';
            form.action = '';
        });

        // Close modal when clicking backdrop
        modal.addEventListener('click', function(e){
            if (e.target === modal) {
                modal.style.display = 'none';
                form.action = '';
            }
        });
    })();
</script>

<script>
    (function(){
        const profileModalElement = document.getElementById('profileModal');
        const profileUserId = document.getElementById('profileUserId');
        const profileSelect = document.getElementById('profileSelect');
        const profileDirectionInput = document.getElementById('profileDirectionInput');

        if (profileModalElement) {
            profileModalElement.addEventListener('show.bs.modal', (event) => {
                const button = event.relatedTarget;
                if (!button) return;

                const userId = button.getAttribute('data-user-id');
                const currentProfilId = button.getAttribute('data-current-profil-id');
                const regionChoicesJson = button.getAttribute('data-region-choices') || '[]';
                const userNom = button.getAttribute('data-user-nom') || '';
                const userPrenom = button.getAttribute('data-user-prenom') || '';
                const userMatricule = button.getAttribute('data-user-matricule') || '';
                const structureId = button.getAttribute('data-structure-id') || '';
                const structureName = button.getAttribute('data-structure-name') || '';
                const directionName = button.getAttribute('data-direction-name') || '';

                if (profileUserId) profileUserId.value = userId || '';
                if (profileSelect) profileSelect.value = currentProfilId || '';
                if (profileDirectionInput) profileDirectionInput.value = directionName === '—' ? '' : directionName;

                const profileMinistereSelect = document.getElementById('profileMinistereSelect');
                if (profileMinistereSelect) profileMinistereSelect.value = structureId || '';

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
                const directionLabel = document.getElementById('profileDirection');

                if (nomLabel) nomLabel.textContent = userNom || '—';
                if (prenomLabel) prenomLabel.textContent = userPrenom || '—';
                if (matriculeLabel) matriculeLabel.textContent = userMatricule || '—';
                if (structureLabel) structureLabel.textContent = structureName || '—';
                if (directionLabel) directionLabel.textContent = directionName || '—';

                // Render regional choices
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
                    // ignore parse errors
                }
            });
        }

        // Auto-open create modal if URL has ?action=new
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'new') {
            const createModal = new bootstrap.Modal(document.getElementById('createAgentModal'));
            createModal.show();
        }
    })();
</script>
@endpush
