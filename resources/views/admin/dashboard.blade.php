@extends('layouts.admin')

@section('admin-title', 'Tableau de Bord Analytique')
@section('admin-subtitle', 'Suivi des inscriptions, motivations et questions dynamiques')
@section('body-bg', '#f4f7f6') {{-- Un gris-bleu plus pro --}}

@section('content')
<div class="container-fluid p-0">

    {{-- Header avec Titre et Action --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-0">Tableau de Bord Analytique</h1>
            <p class="text-muted mb-0">Suivi des inscriptions et des motivations en temps réel.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary shadow-sm d-flex align-items-center gap-2 px-4 py-2" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                <i class="bi bi-patch-question"></i>
                <span class="fw-medium">Nouvelle Question</span>
            </button>
            <button class="btn btn-primary shadow-sm d-flex align-items-center gap-2 px-4 py-2" data-bs-toggle="modal" data-bs-target="#addMotivationModal">
                <i class="bi bi-plus-lg"></i>
                <span class="fw-medium">Nouvelle Motivation</span>
            </button>
        </div>
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
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-people text-primary fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small uppercase fw-bold">Total candidats</h6>
                        <h3 class="fw-bold mb-0">{{ $totalUsers }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-check-circle text-success fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold">Soumissions complètes</h6>
                        <h3 class="fw-bold mb-0">{{ $completedUsers }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-journal-check text-warning fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold">Répondants (Q. dyn.)</h6>
                        <h3 class="fw-bold mb-0">{{ $dynamicRespondents }}</h3>
                        <small class="text-muted">{{ $dynamicRespondentsRate }}%</small>
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

    {{-- ===== SECTION CANDIDATS (Analyse des profils) ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1">Analyse des Candidats</h5>
                        <p class="text-muted mb-0 small">Distribution par profil, niveau numérique et disponibilité</p>
                    </div>
                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-sm btn-outline-primary">Voir tous les candidats</a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        {{-- Distribution par profil --}}
                        <div class="col-md-4">
                            <h6 class="fw-bold text-dark mb-3">Par Profil</h6>
                            <div class="list-group list-group-flush">
                                @forelse($candidatesByProfil as $item)
                                    <div class="list-group-item px-0 border-0 py-2 d-flex justify-content-between align-items-center">
                                        @if($item->profil_id)
                                            <a href="{{ route('admin.candidates.profil', $item->profil_id) }}" style="color: #4a8c5c; text-decoration: none; font-weight: 600;">
                                                {{ $item->libelle ?? 'Non assigné' }}
                                            </a>
                                        @else
                                            <span style="color: #6c757d; font-weight: 600;">{{ $item->libelle ?? 'Non assigné' }}</span>
                                        @endif
                                        <span class="badge bg-primary rounded-pill">{{ $item->total }}</span>
                                    </div>
                                @empty
                                    <p class="text-muted small">Aucun candidat</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Distribution par niveau --}}
                        <div class="col-md-4">
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

                        {{-- Distribution par disponibilité --}}
                        <div class="col-md-4">
                            <h6 class="fw-bold text-dark mb-3">Par Disponibilité</h6>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item px-0 border-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="text-dark">Immédiate</span>
                                    <span class="badge bg-warning rounded-pill">{{ $candidatesByDisponibilite->get('immediate', 0) }}</span>
                                </div>
                                <div class="list-group-item px-0 border-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="text-dark">Sous 7 jours</span>
                                    <span class="badge bg-info rounded-pill">{{ $candidatesByDisponibilite->get('sous_7_jours', 0) }}</span>
                                </div>
                                <div class="list-group-item px-0 border-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="text-dark">Sous 15 jours</span>
                                    <span class="badge bg-secondary rounded-pill">{{ $candidatesByDisponibilite->get('sous_15_jours', 0) }}</span>
                                </div>
                                <div class="list-group-item px-0 border-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="text-dark">Selon calendrier</span>
                                    <span class="badge bg-dark rounded-pill">{{ $candidatesByDisponibilite->get('selon_calendrier', 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="metrics-section" class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-dark bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-ui-checks-grid text-dark fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold">Réponses dynamiques</h6>
                        <h3 class="fw-bold mb-0">{{ $totalDynamicAnswers }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-person-check text-success fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold">Répondants (questions dyn.)</h6>
                        <h3 class="fw-bold mb-0">{{ $dynamicRespondents }}</h3>
                        <small class="text-muted">{{ $dynamicRespondentsRate }}% des soumissions</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-question-circle text-info fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold">Questions actives</h6>
                        <h3 class="fw-bold mb-0">{{ $dynamicQuestions->where('is_active', true)->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-pencil-square text-warning fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small fw-bold">Soumissions régions</h6>
                        <h3 class="fw-bold mb-0">{{ $tendances->sum('total') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="motivations-section" class="row g-4 mb-4">
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

    <div id="questions-section" class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1">Toutes les motivations</h5>
                        <p class="text-muted mb-0 small">Liste complète des motivations ajoutées, avec le statut actif ou supprimé.</p>
                    </div>
                    <span class="badge rounded-pill bg-dark-subtle text-dark px-3 py-2">{{ $motivations->count() }} au total</span>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-muted small text-uppercase">Libellé</th>
                                    <th class="text-muted small text-uppercase">Statut</th>
                                    <th class="text-muted small text-uppercase text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($motivations as $motivation)
                                    <tr>
                                        <td class="fw-medium text-dark">{{ $motivation->libelle }}</td>
                                        <td>
                                            @if($motivation->trashed())
                                                <span class="badge rounded-pill bg-danger-subtle text-danger">Supprimée</span>
                                            @else
                                                <span class="badge rounded-pill bg-success-subtle text-success">Active</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($motivation->trashed())
                                                <form action="{{ route('admin.motivations.restore', $motivation->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">Restaurer</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.motivations.destroy', $motivation) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette motivation ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Aucune motivation disponible.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1">Questions dynamiques</h5>
                        <p class="text-muted mb-0 small">Gestion des questions affichées dans le formulaire utilisateur.</p>
                    </div>
                    <span class="badge rounded-pill bg-dark-subtle text-dark px-3 py-2">{{ $dynamicQuestions->count() }} questions</span>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-muted small text-uppercase">Question</th>
                                    <th class="text-muted small text-uppercase">Type</th>
                                    <th class="text-muted small text-uppercase">Requise</th>
                                    <th class="text-muted small text-uppercase">Statut</th>
                                    <th class="text-muted small text-uppercase">Réponses</th>
                                    <th class="text-muted small text-uppercase">Ordre</th>
                                    <th class="text-muted small text-uppercase text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dynamicQuestions as $question)
                                    <tr>
                                        <td>
                                            <p class="fw-medium text-dark mb-1">{{ $question->libelle }}</p>
                                            @if($question->type === 'select')
                                                <small class="text-muted">Options: {{ $question->options->pluck('libelle')->join(', ') }}</small>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $question->type }}</span></td>
                                        <td>
                                            @if($question->is_required)
                                                <span class="badge rounded-pill bg-warning-subtle text-warning">Oui</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary-subtle text-secondary">Non</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($question->is_active)
                                                <span class="badge rounded-pill bg-success-subtle text-success">Active</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger-subtle text-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $question->answers_count }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.questions.order', $question) }}" method="POST" class="d-flex gap-2 align-items-center">
                                                @csrf
                                                <input type="number" min="0" name="ordre" value="{{ $question->ordre }}" class="form-control form-control-sm" style="max-width:90px;">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">OK</button>
                                            </form>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editQuestionModal{{ $question->id }}">
                                                Modifier
                                            </button>
                                            <form action="{{ route('admin.questions.toggle', $question) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $question->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                    {{ $question->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <tr class="d-none">
                                        <td colspan="6">
                                            <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1" aria-labelledby="editQuestionModalLabel{{ $question->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                                        <form action="{{ route('admin.questions.update', $question) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header border-0 px-4 pt-4 pb-2">
                                                                <div>
                                                                    <h5 class="modal-title fw-bold mb-1" id="editQuestionModalLabel{{ $question->id }}">Modifier la question</h5>
                                                                    <p class="text-muted mb-0 small">Mets à jour le libellé, le type, les options et le statut.</p>
                                                                </div>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                            </div>
                                                            <div class="modal-body px-4 py-3">
                                                                <div class="mb-3">
                                                                    <label for="question-libelle-{{ $question->id }}" class="form-label fw-medium">Libellé</label>
                                                                    <input type="text" id="question-libelle-{{ $question->id }}" name="libelle" class="form-control" maxlength="255" value="{{ $question->libelle }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="question-type-{{ $question->id }}" class="form-label fw-medium">Type</label>
                                                                    <select id="question-type-{{ $question->id }}" name="type" class="form-control" required>
                                                                        <option value="text" @selected($question->type === 'text')>Texte</option>
                                                                        <option value="select" @selected($question->type === 'select')>Liste déroulante</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="question-placeholder-{{ $question->id }}" class="form-label fw-medium">Placeholder (optionnel)</label>
                                                                    <input type="text" id="question-placeholder-{{ $question->id }}" name="placeholder" class="form-control" maxlength="255" value="{{ $question->placeholder }}">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="question-options-{{ $question->id }}" class="form-label fw-medium">Options (si type = liste, séparées par des virgules)</label>
                                                                    <input type="text" id="question-options-{{ $question->id }}" name="options_text" class="form-control" value="{{ $question->options->pluck('libelle')->join(', ') }}" placeholder="Oui, Non, Peut-être">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="question-ordre-{{ $question->id }}" class="form-label fw-medium">Ordre</label>
                                                                    <input type="number" id="question-ordre-{{ $question->id }}" name="ordre" class="form-control" min="0" value="{{ $question->ordre }}">
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="checkbox" value="1" id="question-required-{{ $question->id }}" name="is_required" @checked($question->is_required)>
                                                                    <label class="form-check-label" for="question-required-{{ $question->id }}">
                                                                        Question obligatoire
                                                                    </label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" value="1" id="question-active-{{ $question->id }}" name="is_active" @checked($question->is_active)>
                                                                    <label class="form-check-label" for="question-active-{{ $question->id }}">
                                                                        Active
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                            </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Aucune question dynamique créée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== DÉTAILS DES CHOIX (Cartes compactes) ===== --}}
    <section id="regions-section" class="mt-5">
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
    </section>
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

<div class="modal fade" id="addMotivationModal" tabindex="-1" aria-labelledby="addMotivationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.motivations.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="addMotivationModalLabel">Ajouter une motivation</h5>
                        <p class="text-muted mb-0 small">Créer une nouvelle motivation disponible dans les formulaires utilisateurs.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <label for="motivation-libelle" class="form-label fw-medium">Libellé</label>
                    <input
                        type="text"
                        class="form-control form-control-lg @error('libelle') is-invalid @enderror"
                        id="motivation-libelle"
                        name="libelle"
                        value="{{ old('libelle') }}"
                        placeholder="Ex. Proximité familiale"
                        maxlength="255"
                        required
                    >
                    @error('libelle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.questions.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="addQuestionModalLabel">Ajouter une question dynamique</h5>
                        <p class="text-muted mb-0 small">Types disponibles à cette étape: texte et liste déroulante.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label for="question-libelle" class="form-label fw-medium">Libellé</label>
                        <input type="text" id="question-libelle" name="libelle" class="form-control" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label for="question-type" class="form-label fw-medium">Type</label>
                        <select id="question-type" name="type" class="form-control" required>
                            <option value="text">Texte</option>
                            <option value="select">Liste déroulante</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="question-placeholder" class="form-label fw-medium">Placeholder (optionnel)</label>
                        <input type="text" id="question-placeholder" name="placeholder" class="form-control" maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label for="question-options" class="form-label fw-medium">Options (si type = liste, séparées par des virgules)</label>
                        <input type="text" id="question-options" name="options_text" class="form-control" placeholder="Oui, Non, Peut-être">
                    </div>
                    <div class="mb-3">
                        <label for="question-ordre" class="form-label fw-medium">Ordre</label>
                        <input type="number" id="question-ordre" name="ordre" class="form-control" min="0" value="0">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="1" id="question-required" name="is_required">
                        <label class="form-check-label" for="question-required">
                            Question obligatoire
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="question-active" name="is_active" checked>
                        <label class="form-check-label" for="question-active">
                            Active dès maintenant
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection