@extends('layouts.admin')

@section('admin-title', 'Résultats des Évaluations')
@section('admin-subtitle', 'Suivez les performances des agents')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0">Agent</th>
                            <th class="py-3 border-0">Quiz</th>
                            <th class="py-3 border-0 text-center">Score</th>
                            <th class="py-3 border-0">Date</th>
                            <th class="pe-4 py-3 border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $result->user->nom }} {{ $result->user->prenom }}</div>
                                            <div class="small text-muted">{{ $result->user->matricule }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $result->quiz->titre }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-primary px-3 py-2 fs-6">
                                        {{ $result->score }} pts
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $result->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="pe-4 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#resultModal{{ $result->id }}">
                                        Détails
                                    </button>

                                    {{-- Modal Détails Réponses --}}
                                    <div class="modal fade" id="resultModal{{ $result->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4 shadow-lg text-start">
                                                <div class="modal-header border-0 pt-4 px-4">
                                                    <h5 class="fw-bold mb-0">Réponses de {{ $result->user->nom }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-4 p-3 bg-light rounded-3">
                                                        <div class="small text-muted mb-1">Score total obtenu</div>
                                                        <div class="h3 fw-bold text-primary mb-0">{{ $result->score }} points</div>
                                                    </div>
                                                    
                                                    <h6 class="fw-bold mb-3 border-bottom pb-2">Détail des choix :</h6>
                                                    <div class="list-group list-group-flush small">
                                                        @foreach($result->quiz->questions as $question)
                                                            <div class="list-group-item px-0 py-3 bg-transparent">
                                                                <div class="fw-bold mb-1 text-dark">{{ $question->libelle }}</div>
                                                                @php
                                                                    $userAns = $result->answers_json[$question->id] ?? null;
                                                                    $userAnsIds = is_array($userAns) ? $userAns : [$userAns];
                                                                @endphp
                                                                <div class="text-muted mt-1">
                                                                    Réponses choisies : 
                                                                    @foreach($question->options as $option)
                                                                        @if(in_array($option->id, $userAnsIds))
                                                                            <span class="badge {{ $option->is_correct ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $option->is_correct ? 'text-success' : 'text-danger' }} border {{ $option->is_correct ? 'border-success' : 'border-danger' }} border-opacity-25 ms-1">
                                                                                {{ $option->libelle }}
                                                                            </span>
                                                                        @endif
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
                                <td colspan="5" class="py-5 text-center text-muted">
                                    <i class="bi bi-clipboard-x fs-1 mb-3 d-block"></i>
                                    Aucun résultat enregistré pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
