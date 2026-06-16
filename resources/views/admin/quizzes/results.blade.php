@extends('layouts.admin')

@section('admin-title', 'Tableau de bord des Résultats')
@section('admin-subtitle', 'Analyse des performances et scores des agents')

@section('content')
<div class="container-fluid">
    {{-- Barre de Filtres Smart --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.quizzes.results') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Rechercher un agent</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nom, prénom ou matricule..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Filtrer par Quiz</label>
                    <select name="quiz_id" class="form-select">
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
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 rounded-3">
                        Filtrer
                    </button>
                    <a href="{{ route('admin.quizzes.results') }}" class="btn btn-light rounded-3" title="Réinitialiser">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
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
                                <td colspan="6" class="py-5 text-center text-muted">
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
@endsection
