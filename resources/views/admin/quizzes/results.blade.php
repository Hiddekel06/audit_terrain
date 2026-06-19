@extends('layouts.admin')

@section('admin-title', 'Tableau de bord des Résultats')
@section('admin-subtitle', 'Analyse des performances et scores des agents')

@section('content')
<div class="container-fluid">
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
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Filtrer par Quiz</label>
                    <select name="quiz_id" id="quiz_id_filter" class="form-select">
                        <option value="">Tous les Quiz</option>
                        @foreach($quizzes as $q)
                            <option value="{{ $q->id }}" {{ request('quiz_id') == $q->id ? 'selected' : '' }}>{{ $q->titre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Filtrer par Question (Affiche réponse)</label>
                    <select name="question_id" id="question_id_filter" class="form-select">
                        <option value="">Toutes les questions / Aucune</option>
                        @foreach($quizzes as $q)
                            <optgroup label="{{ $q->titre }}" data-quiz-id="{{ $q->id }}">
                                @foreach($q->questions as $question)
                                    <option value="{{ $question->id }}" {{ request('question_id') == $question->id ? 'selected' : '' }}>
                                        {{ Str::limit($question->libelle, 50) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Filtrer par Profil</label>
                    <select name="profil_id" class="form-select">
                        <option value="">Tous les Profils</option>
                        @foreach($profils as $p)
                            <option value="{{ $p->id }}" {{ request('profil_id') == $p->id ? 'selected' : '' }}>{{ $p->libelle }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtre de statut de réponse (visible si question sélectionnée) --}}
                <div class="col-md-3" id="status_filter_container" style="display: none;">
                    <label class="form-label small fw-bold text-muted">Statut de la réponse</label>
                    <select name="question_status" id="question_status_filter" class="form-select">
                        <option value="">Tous les agents</option>
                        <option value="correct" {{ request('question_status') == 'correct' ? 'selected' : '' }}>Correct (A trouvé)</option>
                        <option value="incorrect" {{ request('question_status') == 'incorrect' ? 'selected' : '' }}>Incorrect / Échoué</option>
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

    {{-- Liste des Résultats --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0">Agent & Profil</th>
                            <th class="py-3 border-0">Ministère</th>
                            <th class="py-3 border-0">Évaluation</th>
                            @if(isset($selectedQuestion))
                                <th class="py-3 border-0" style="max-width: 250px;">
                                    <div class="text-truncate" title="{{ $selectedQuestion->libelle }}">
                                        Q: {{ $selectedQuestion->libelle }}
                                    </div>
                                </th>
                            @endif
                            <th class="py-3 border-0 text-center">Score Obtenu</th>
                            <th class="py-3 border-0">Date de passage</th>
                            <th class="pe-4 py-3 border-0 text-end">Analyse</th>
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
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-light text-muted border px-2 py-1 x-small">{{ $result->user->matricule }}</span>
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 x-small">{{ $result->user->profil->libelle ?? 'Non défini' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($result->user->ministere)
                                        <div class="small fw-medium text-dark text-truncate" style="max-width: 150px;" title="{{ $result->user->ministere->nom }}">
                                            {{ $result->user->ministere->nom }}
                                        </div>
                                    @else
                                        <span class="text-muted small italic">Non renseigné</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $result->quiz->titre }}</div>
                                    <div class="x-small text-muted">{{ $result->quiz->questions_count ?? $result->quiz->questions->count() }} questions</div>
                                </td>
                                @if(isset($selectedQuestion))
                                    <td style="max-width: 250px;">
                                        @if($result->quiz_id !== $selectedQuestion->quiz_id)
                                            <span class="text-muted small italic">N/A (Quiz différent)</span>
                                        @else
                                            @php
                                                $userAns = $result->answers_json[$selectedQuestion->id] ?? null;
                                                $userAnsIds = is_array($userAns) ? array_map('intval', $userAns) : ($userAns !== null ? [intval($userAns)] : []);
                                            @endphp
                                            
                                            @if(empty($userAnsIds))
                                                <span class="text-muted small italic">Non répondu</span>
                                            @else
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($selectedQuestion->options as $option)
                                                        @if(in_array($option->id, $userAnsIds))
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
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#resultModal{{ $result->id }}">
                                        Détails <i class="bi bi-chevron-right ms-1"></i>
                                    </button>

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
                                <td colspan="{{ isset($selectedQuestion) ? 7 : 6 }}" class="py-5 text-center text-muted">
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
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.75rem; }
</style>

<style>
    .x-small { font-size: 0.75rem; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const quizFilter = document.getElementById('quiz_id_filter');
    const questionFilter = document.getElementById('question_id_filter');
    const statusFilterContainer = document.getElementById('status_filter_container');
    const statusFilter = document.getElementById('question_status_filter');

    if (quizFilter && questionFilter) {
        function filterQuestionsAndToggleStatus(isInitialLoad = false) {
            const quizId = quizFilter.value;
            const optgroups = questionFilter.querySelectorAll('optgroup');
            let currentSelectedOption = questionFilter.options[questionFilter.selectedIndex];
            let currentSelectedGroup = currentSelectedOption ? currentSelectedOption.closest('optgroup') : null;

            // 1. Filtrer les questions selon le Quiz
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
                questionFilter.value = '';
                currentSelectedOption = null;
            }

            const questionId = questionFilter.value;

            // 2. Afficher/Masquer le filtre de statut en fonction de la question sélectionnée
            if (questionId) {
                statusFilterContainer.style.display = '';
            } else {
                statusFilterContainer.style.display = 'none';
                statusFilter.value = ''; // Réinitialiser le statut
            }
        }

        quizFilter.addEventListener('change', function() {
            filterQuestionsAndToggleStatus(false);
        });
        
        questionFilter.addEventListener('change', function() {
            filterQuestionsAndToggleStatus(false);
        });
        
        // Initialiser au chargement de la page
        filterQuestionsAndToggleStatus(true);
    }
});
</script>
@endpush
@endsection
