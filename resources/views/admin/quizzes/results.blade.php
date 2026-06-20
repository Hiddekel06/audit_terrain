@extends('layouts.admin')

@section('admin-title', 'Tableau de bord des Résultats')
@section('admin-subtitle', 'Analyse des performances et scores des agents')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-4 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- Barre de Filtres Smart --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.quizzes.results') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Rechercher un agent</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nom, prénom ou matricule..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Trier par note</label>
                    <select name="score_order" class="form-select">
                        <option value="">Par défaut</option>
                        <option value="asc" {{ request('score_order') == 'asc' ? 'selected' : '' }}>Note croissante</option>
                        <option value="desc" {{ request('score_order') == 'desc' ? 'selected' : '' }}>Note décroissante</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold text-muted">Affichage</label>
                    <select name="per_page" class="form-select">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == '25' || !request('per_page') ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Filtrer par Quiz</label>
                    <select name="quiz_id" id="quiz_id_filter" class="form-select">
                        <option value="">Tous les Quiz</option>
                        @foreach($quizzes as $q)
                            <option value="{{ $q->id }}" {{ request('quiz_id') == $q->id ? 'selected' : '' }}>{{ $q->titre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Filtrer par Profil</label>
                    <select name="profil_id" class="form-select">
                        <option value="">Tous les Profils</option>
                        @foreach($profils as $p)
                            <option value="{{ $p->id }}" {{ request('profil_id') == $p->id ? 'selected' : '' }}>{{ $p->libelle }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Row 2 --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Question A</label>
                    <select name="question_a_id" id="question_a_filter" class="form-select">
                        <option value="">Aucune question sélectionnée</option>
                        @foreach($quizzes as $q)
                            <optgroup label="{{ $q->titre }}" data-quiz-id="{{ $q->id }}">
                                @foreach($q->questions as $question)
                                    <option value="{{ $question->id }}" {{ request('question_a_id') == $question->id ? 'selected' : '' }}>
                                        {{ Str::limit($question->libelle, 50) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2" id="status_a_container" style="display: none;">
                    <label class="form-label small fw-bold text-muted">Statut Réponse A</label>
                    <select name="status_a" id="status_a_filter" class="form-select">
                        <option value="">Tous</option>
                        <option value="correct" {{ request('status_a') == 'correct' ? 'selected' : '' }}>Correct</option>
                        <option value="incorrect" {{ request('status_a') == 'incorrect' ? 'selected' : '' }}>Incorrect</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Question B</label>
                    <select name="question_b_id" id="question_b_filter" class="form-select">
                        <option value="">Aucune question sélectionnée</option>
                        @foreach($quizzes as $q)
                            <optgroup label="{{ $q->titre }}" data-quiz-id="{{ $q->id }}">
                                @foreach($q->questions as $question)
                                    <option value="{{ $question->id }}" {{ request('question_b_id') == $question->id ? 'selected' : '' }}>
                                        {{ Str::limit($question->libelle, 50) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2" id="status_b_container" style="display: none;">
                    <label class="form-label small fw-bold text-muted">Statut Réponse B</label>
                    <select name="status_b" id="status_b_filter" class="form-select">
                        <option value="">Tous</option>
                        <option value="correct" {{ request('status_b') == 'correct' ? 'selected' : '' }}>Correct</option>
                        <option value="incorrect" {{ request('status_b') == 'incorrect' ? 'selected' : '' }}>Incorrect</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Sélection Physique</label>
                    <select name="validation_status" class="form-select">
                        <option value="">Tous</option>
                        <option value="officiel" {{ request('validation_status') == 'officiel' ? 'selected' : '' }}>Sélectionné (Officiel)</option>
                        <option value="reserve" {{ request('validation_status') == 'reserve' ? 'selected' : '' }}>Réserve</option>
                        <option value="none" {{ request('validation_status') == 'none' ? 'selected' : '' }}>Non sélectionné</option>
                    </select>
                </div>
                
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.quizzes.results') }}" class="btn btn-light rounded-3 px-3" title="Réinitialiser">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser
                    </a>
                    <button type="submit" name="export" value="1" class="btn btn-success rounded-3 px-3" title="Exporter les résultats sous Excel">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bloc Statistiques --}}
    @php
        $uniqueUsers = $results->pluck('user')->filter()->unique('id');
        $totalAgents = $uniqueUsers->count();
        $totalSelected = $uniqueUsers->filter(fn($u) => str_contains($u->validation_status ?? '', 'officiel'))->count();
        $totalReserve = $uniqueUsers->where('validation_status', 'reserve')->count();

        // Regroupement par profil pour les statistiques
        $byProfile = [];
        foreach($profils as $p) {
            $usersInProfile = $uniqueUsers->where('profil_id', $p->id);
            $byProfile[$p->libelle] = [
                'total' => $usersInProfile->count(),
                'selected' => $usersInProfile->filter(fn($u) => str_contains($u->validation_status ?? '', 'officiel'))->count(),
                'reserve' => $usersInProfile->where('validation_status', 'reserve')->count(),
            ];
        }
    @endphp
    
    <div class="row g-3 mb-4">
        {{-- Card Global --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                        <span class="badge bg-light text-muted border px-2 py-1 x-small">Global</span>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">Total Évalués</h6>
                    <div class="h3 fw-bold text-dark mb-2">{{ $totalAgents }}</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-1 x-small" title="Sélectionnés pour l'audit physique">
                            <i class="bi bi-shield-check"></i> {{ $totalSelected }} Off.
                        </span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 px-2 py-1 x-small" title="En liste de réserve">
                            <i class="bi bi-archive"></i> {{ $totalReserve }} Rés.
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Chefs d'équipe --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-person-badge fs-5"></i>
                        </div>
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 px-2 py-1 x-small">Chef d'Équipe</span>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">Chefs d'Équipe</h6>
                    <div class="h3 fw-bold text-dark mb-2">{{ $byProfile["Chef d'équipe"]['total'] ?? 0 }}</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-1 x-small">
                            <i class="bi bi-shield-check"></i> {{ $byProfile["Chef d'équipe"]['selected'] ?? 0 }} Off.
                        </span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 px-2 py-1 x-small">
                            <i class="bi bi-archive"></i> {{ $byProfile["Chef d'équipe"]['reserve'] ?? 0 }} Rés.
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Auditeurs IT --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-laptop fs-5"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 px-2 py-1 x-small">Auditeur IT</span>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">Auditeurs IT</h6>
                    <div class="h3 fw-bold text-dark mb-2">{{ $byProfile["Auditeur IT"]['total'] ?? 0 }}</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-1 x-small">
                            <i class="bi bi-shield-check"></i> {{ $byProfile["Auditeur IT"]['selected'] ?? 0 }} Off.
                        </span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 px-2 py-1 x-small">
                            <i class="bi bi-archive"></i> {{ $byProfile["Auditeur IT"]['reserve'] ?? 0 }} Rés.
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Auditeurs Administratifs --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-file-earmark-person fs-5"></i>
                        </div>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 px-2 py-1 x-small">Auditeur Admin</span>
                    </div>
                    <h6 class="text-muted small fw-bold mb-1">Auditeurs Admin</h6>
                    <div class="h3 fw-bold text-dark mb-2">{{ $byProfile["Auditeur Administratif"]['total'] ?? 0 }}</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-1 x-small">
                            <i class="bi bi-shield-check"></i> {{ $byProfile["Auditeur Administratif"]['selected'] ?? 0 }} Off.
                        </span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 px-2 py-1 x-small">
                            <i class="bi bi-archive"></i> {{ $byProfile["Auditeur Administratif"]['reserve'] ?? 0 }} Rés.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des Résultats --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0">Agent & Profil</th>
                            <th class="py-3 border-0 d-none d-md-table-cell">Ministère</th>
                            <th class="py-3 border-0 d-none d-md-table-cell">Hiérarchie</th>
                            <th class="py-3 border-0 d-none d-md-table-cell">Métier</th>
                            <th class="py-3 border-0">Évaluation</th>
                            @if(isset($selectedQuestionA))
                                <th class="py-3 border-0" style="max-width: 200px;">
                                    <div class="text-truncate" title="{{ $selectedQuestionA->libelle }}">
                                        QA: {{ $selectedQuestionA->libelle }}
                                    </div>
                                </th>
                            @endif
                            @if(isset($selectedQuestionB))
                                <th class="py-3 border-0" style="max-width: 200px;">
                                    <div class="text-truncate" title="{{ $selectedQuestionB->libelle }}">
                                        QB: {{ $selectedQuestionB->libelle }}
                                    </div>
                                </th>
                            @endif
                            <th class="py-3 border-0 text-center">Score Obtenu</th>
                            <th class="py-3 border-0">Date de passage</th>
                            <th class="pe-4 py-3 border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <span class="fw-bold text-primary">{{ substr($result->user->prenom, 0, 1) }}{{ substr($result->user->nom, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $result->user->nom }} {{ $result->user->prenom }}</div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="badge bg-light text-muted border px-2 py-1 x-small" title="Matricule">{{ $result->user->matricule }}</span>
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 x-small" title="Profil">{{ $result->user->profil->libelle ?? 'Non défini' }}</span>
                                                @if($result->user->validation_status === 'officiel_inscrit')
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 x-small" title="Statut de validation">
                                                        <i class="bi bi-shield-check"></i> Officiel
                                                    </span>
                                                @elseif($result->user->validation_status === 'officiel_attente')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 x-small" title="Statut de validation">
                                                        <i class="bi bi-shield-exclamation"></i> Officiel (Attente)
                                                    </span>
                                                @elseif($result->user->validation_status === 'reserve')
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 x-small" title="Statut de validation">
                                                        <i class="bi bi-archive"></i> Réserve
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border px-2 py-1 x-small" title="Statut de validation">
                                                        Non sélectionné
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    @if($result->user->ministere)
                                        <div class="small fw-medium text-dark text-truncate" style="max-width: 150px;" title="{{ $result->user->ministere->nom }}">
                                            {{ $result->user->ministere->nom }}
                                        </div>
                                    @else
                                        <span class="text-muted small italic">Non renseigné</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    @if($result->user->hierarchie)
                                        <span class="badge bg-light text-dark border px-2 py-1 x-small">{{ $result->user->hierarchie }}</span>
                                    @else
                                        <span class="text-muted small italic">—</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    @if($result->user->metier)
                                        <div class="small fw-medium text-dark text-truncate" style="max-width: 150px;" title="{{ $result->user->metier }}">
                                            {{ $result->user->metier }}
                                        </div>
                                    @else
                                        <span class="text-muted small italic">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $result->quiz->titre }}</div>
                                    <div class="x-small text-muted">{{ $result->quiz->questions_count ?? $result->quiz->questions->count() }} questions</div>
                                </td>
                                @if(isset($selectedQuestionA))
                                    <td style="max-width: 200px;">
                                        @if($result->quiz_id !== $selectedQuestionA->quiz_id)
                                            <span class="text-muted small italic">N/A (Quiz différent)</span>
                                        @else
                                            @php
                                                $userAnsA = $result->answers_json[$selectedQuestionA->id] ?? null;
                                                $userAnsIdsA = is_array($userAnsA) ? array_map('intval', $userAnsA) : ($userAnsA !== null ? [intval($userAnsA)] : []);
                                            @endphp
                                            
                                            @if(empty($userAnsIdsA))
                                                <span class="text-muted small italic">Non répondu</span>
                                            @else
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($selectedQuestionA->options as $option)
                                                        @if(in_array($option->id, $userAnsIdsA))
                                                            @php
                                                                $badgeClass = $option->is_correct 
                                                                    ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-10' 
                                                                    : 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10';
                                                                $icon = $option->is_correct ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
                                                            @endphp
                                                            <div class="badge {{ $badgeClass }} d-flex align-items-center gap-1 x-small text-wrap text-start w-100" style="white-space: normal; line-height: 1.2;" title="{{ $option->is_correct ? 'Bonne réponse' : 'Mauvaise réponse' }}">
                                                                <i class="bi {{ $icon }} flex-shrink-0"></i> 
                                                                <span>{{ $option->libelle }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                @endif
                                @if(isset($selectedQuestionB))
                                    <td style="max-width: 200px;">
                                        @if($result->quiz_id !== $selectedQuestionB->quiz_id)
                                            <span class="text-muted small italic">N/A (Quiz différent)</span>
                                        @else
                                            @php
                                                $userAnsB = $result->answers_json[$selectedQuestionB->id] ?? null;
                                                $userAnsIdsB = is_array($userAnsB) ? array_map('intval', $userAnsB) : ($userAnsB !== null ? [intval($userAnsB)] : []);
                                            @endphp
                                            
                                            @if(empty($userAnsIdsB))
                                                <span class="text-muted small italic">Non répondu</span>
                                            @else
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($selectedQuestionB->options as $option)
                                                        @if(in_array($option->id, $userAnsIdsB))
                                                            @php
                                                                $badgeClass = $option->is_correct 
                                                                    ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-10' 
                                                                    : 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10';
                                                                $icon = $option->is_correct ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
                                                            @endphp
                                                            <div class="badge {{ $badgeClass }} d-flex align-items-center gap-1 x-small text-wrap text-start w-100" style="white-space: normal; line-height: 1.2;" title="{{ $option->is_correct ? 'Bonne réponse' : 'Mauvaise réponse' }}">
                                                                <i class="bi {{ $icon }} flex-shrink-0"></i> 
                                                                <span>{{ $option->libelle }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                @endif
                                <td class="text-center">
                                    @php
                                        $totalPossible = $result->quiz->questions->sum('points');
                                        $percentage = $totalPossible > 0 ? ($result->score / $totalPossible) * 100 : 0;
                                        $colorClass = $percentage >= 70 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="d-inline-block text-center">
                                        <div class="badge rounded-pill bg-{{ $colorClass }} px-3 py-2 fs-6 mb-1">
                                            {{ $result->score }} / {{ $totalPossible }}
                                        </div>
                                        <div class="progress mt-1" style="height: 4px; width: 60px; margin: 0 auto;">
                                            <div class="progress-bar bg-{{ $colorClass }}" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark small fw-medium">{{ $result->created_at->translatedFormat('d M Y') }}</div>
                                    <div class="text-muted x-small">{{ $result->created_at->format('H:i') }}</div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex gap-2">
                                        @if(str_contains($result->user->validation_status ?? '', 'officiel'))
                                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#selectAgentModal{{ $result->id }}" title="Modifier la sélection de l'agent">
                                                <i class="bi bi-shield-check me-1"></i> Sélectionné
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#selectAgentModal{{ $result->id }}" title="Sélectionner pour l'audit physique">
                                                <i class="bi bi-shield me-1"></i> Sélectionner
                                            </button>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#resultModal{{ $result->id }}">
                                            Détails <i class="bi bi-chevron-right ms-1"></i>
                                        </button>
                                    </div>

                                    {{-- Modal Sélection Agent --}}
                                    <div class="modal fade" id="selectAgentModal{{ $result->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4 shadow-lg text-start">
                                                <form action="{{ route('admin.candidates.update', $result->user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header border-0 pt-4 px-4 bg-light rounded-top-4">
                                                        <div>
                                                            <h5 class="fw-bold mb-0">Sélection de l'agent</h5>
                                                            <div class="small text-muted">{{ $result->user->nom }} {{ $result->user->prenom }}</div>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-center">
                                                        @if(str_contains($result->user->validation_status ?? '', 'officiel'))
                                                            <div class="mb-3">
                                                                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                                                            </div>
                                                            <p class="mb-0 fs-6">
                                                                Voulez-vous retirer l'agent <strong>{{ $result->user->nom }} {{ $result->user->prenom }}</strong> (Matricule: {{ $result->user->matricule ?? '—' }}) de la liste officielle pour l'audit physique ?
                                                            </p>
                                                            <input type="hidden" name="validation_status" value="reserve">
                                                        @else
                                                            <div class="mb-3">
                                                                <i class="bi bi-question-circle text-primary" style="font-size: 3rem;"></i>
                                                            </div>
                                                            <p class="mb-0 fs-6">
                                                                Voulez-vous sélectionner l'agent <strong>{{ $result->user->nom }} {{ $result->user->prenom }}</strong> (Matricule: {{ $result->user->matricule ?? '—' }}) et le garder pour l'audit physique ?
                                                            </p>
                                                            <input type="hidden" name="validation_status" value="officiel">
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4 d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                                                        @if(str_contains($result->user->validation_status ?? '', 'officiel'))
                                                            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Retirer</button>
                                                        @else
                                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Confirmer</button>
                                                        @endif
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal Détails --}}
                                    <div class="modal fade" id="resultModal{{ $result->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4 shadow-lg text-start">
                                                <div class="modal-header border-0 pt-4 px-4 bg-light rounded-top-4">
                                                    <div>
                                                        <h5 class="fw-bold mb-0">Rapport d'évaluation</h5>
                                                        <div class="small text-muted">{{ $result->user->nom }} {{ $result->user->prenom }} - {{ $result->quiz->titre }}</div>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3 h-100">
                                                                <div class="small text-muted mb-1">Performance Globale</div>
                                                                <div class="d-flex align-items-baseline gap-2">
                                                                    <span class="h2 fw-bold text-{{ $colorClass }} mb-0">{{ round($percentage) }}%</span>
                                                                    <span class="text-muted">({{ $result->score }} / {{ $totalPossible }} pts)</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3 h-100">
                                                                <div class="small text-muted mb-1">Profil de l'agent</div>
                                                                <div class="fw-bold text-dark">{{ $result->user->profil->libelle ?? 'Non défini' }}</div>
                                                                <div class="x-small text-muted text-truncate">{{ $result->user->ministere->nom_ministere ?? 'Sans ministère' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                                        <i class="bi bi-list-check text-primary"></i> Analyse des réponses
                                                    </h6>
                                                    <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                                                        @foreach($result->quiz->questions as $question)
                                                            <div class="list-group-item p-3">
                                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                                    <div class="fw-bold text-dark mb-2">{{ $question->libelle }}</div>
                                                                    <span class="badge bg-light text-muted border x-small">{{ $question->points }} pts</span>
                                                                </div>
                                                                @php
                                                                    $userAns = $result->answers_json[$question->id] ?? null;
                                                                    $userAnsIds = is_array($userAns) ? array_map('intval', $userAns) : [intval($userAns)];
                                                                @endphp
                                                                
                                                                <div class="row g-2 mt-1">
                                                                    @foreach($question->options as $option)
                                                                        @php
                                                                            $wasSelected = in_array($option->id, $userAnsIds);
                                                                            $borderClass = '';
                                                                            $icon = 'bi-circle';
                                                                            $textClass = 'text-muted';
                                                                            
                                                                            if ($option->is_correct) {
                                                                                $borderClass = 'border-success bg-success bg-opacity-10';
                                                                                $icon = 'bi-check-circle-fill text-success';
                                                                                $textClass = 'text-success fw-bold';
                                                                            } elseif ($wasSelected && !$option->is_correct) {
                                                                                $borderClass = 'border-danger bg-danger bg-opacity-10';
                                                                                $icon = 'bi-x-circle-fill text-danger';
                                                                                $textClass = 'text-danger fw-bold';
                                                                            }
                                                                        @endphp
                                                                        <div class="col-6">
                                                                            <div class="p-2 border rounded-2 d-flex align-items-center gap-2 {{ $borderClass }}" style="font-size: 0.8rem;">
                                                                                <i class="bi {{ $wasSelected ? $icon : ($option->is_correct ? 'bi-check-circle text-success' : 'bi-circle text-light-emphasis') }}"></i>
                                                                                <span class="{{ $wasSelected ? $textClass : ($option->is_correct ? 'text-success' : 'text-muted') }}">{{ $option->libelle }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 8 + (isset($selectedQuestionA) ? 1 : 0) + (isset($selectedQuestionB) ? 1 : 0) }}" class="py-5 text-center text-muted">
                                    <div class="mb-3">
                                        <i class="bi bi-clipboard-x fs-1 text-light-emphasis"></i>
                                    </div>
                                    <h5 class="fw-bold">Aucun résultat trouvé</h5>
                                    <p class="small">Essayez de modifier vos filtres de recherche.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Modernisée -->
            <div class="p-4 border-top border-light d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted small">
                    Affichage de <strong>{{ $results->firstItem() ?? 0 }}</strong> à <strong>{{ $results->lastItem() ?? 0 }}</strong> sur <strong>{{ $results->total() }}</strong> résultats
                </div>
                <div>
                    {{ $results->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.75rem; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const quizFilter = document.getElementById('quiz_id_filter');
    const questionAFilter = document.getElementById('question_a_filter');
    const statusAContainer = document.getElementById('status_a_container');
    const statusAFilter = document.getElementById('status_a_filter');

    const questionBFilter = document.getElementById('question_b_filter');
    const statusBContainer = document.getElementById('status_b_container');
    const statusBFilter = document.getElementById('status_b_filter');

    if (quizFilter && questionAFilter && questionBFilter) {
        function filterQuestionsAndToggleStatus(isInitialLoad = false) {
            const quizId = quizFilter.value;

            // 1. Filtrer les questions selon le Quiz pour Question A et Question B
            [questionAFilter, questionBFilter].forEach(filter => {
                const optgroups = filter.querySelectorAll('optgroup');
                let currentSelectedOption = filter.options[filter.selectedIndex];
                let currentSelectedGroup = currentSelectedOption ? currentSelectedOption.closest('optgroup') : null;

                optgroups.forEach(group => {
                    const groupQuizId = group.getAttribute('data-quiz-id');
                    if (!quizId || groupQuizId === quizId) {
                        group.style.display = '';
                        group.querySelectorAll('option').forEach(opt => {
                            opt.disabled = false;
                            opt.style.display = '';
                        });
                    } else {
                        group.style.display = 'none';
                        group.querySelectorAll('option').forEach(opt => {
                            opt.disabled = true;
                            opt.style.display = 'none';
                        });
                    }
                });

                // Si le filtre de quiz change et que la question sélectionnée appartient à un autre quiz, réinitialiser
                if (!isInitialLoad && quizId && currentSelectedGroup && currentSelectedGroup.getAttribute('data-quiz-id') !== quizId) {
                    filter.value = '';
                }
            });

            // 2. Afficher/Masquer le filtre de statut pour Question A
            if (questionAFilter.value) {
                statusAContainer.style.display = '';
            } else {
                statusAContainer.style.display = 'none';
                statusAFilter.value = '';
            }

            // 3. Afficher/Masquer le filtre de statut pour Question B
            if (questionBFilter.value) {
                statusBContainer.style.display = '';
            } else {
                statusBContainer.style.display = 'none';
                statusBFilter.value = '';
            }
        }

        quizFilter.addEventListener('change', function() {
            filterQuestionsAndToggleStatus(false);
        });
        
        questionAFilter.addEventListener('change', function() {
            filterQuestionsAndToggleStatus(false);
        });

        questionBFilter.addEventListener('change', function() {
            filterQuestionsAndToggleStatus(false);
        });
        
        // Initialiser au chargement de la page
        filterQuestionsAndToggleStatus(true);
    }
});
</script>
@endpush
@endsection
