@extends('layouts.admin')

@section('admin-title', 'Détails du Quiz')
@section('admin-subtitle', 'Gérez les questions et les réponses')

@section('admin-actions')
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Retour</span>
    </a>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
        <i class="bi bi-plus-circle"></i>
        <span>Ajouter une question</span>
    </button>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        {{-- Sidebar Détails --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-4">
                            <i class="bi bi-patch-question fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-truncate-1">{{ $quiz->titre }}</h5>
                            <span class="badge {{ $quiz->is_active ? 'bg-success' : 'bg-secondary' }} mt-1">
                                {{ $quiz->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>

                    <p class="text-muted small mb-4">{{ $quiz->description ?? 'Aucune description fournie.' }}</p>

                    <div class="list-group list-group-flush small">
                        <div class="list-group-item d-flex flex-column px-0 py-3 bg-transparent">
                            <span class="text-muted mb-2">Profils cibles :</span>
                            <div>
                                @forelse($quiz->profils as $profil)
                                    <span class="badge bg-light text-primary border border-primary border-opacity-25 mb-1">{{ $profil->libelle }}</span>
                                @empty
                                    <span class="fw-bold">Tous les profils</span>
                                @endforelse
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0 py-3 bg-transparent">
                            <span class="text-muted">Total questions :</span>
                            <span class="fw-bold">{{ $quiz->questions->count() }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0 py-3 bg-transparent">
                            <span class="text-muted">Total points :</span>
                            <span class="fw-bold">{{ $quiz->questions->sum('points') }} pts</span>
                        </div>
                        <div class="list-group-item d-flex flex-column px-0 py-3 bg-transparent">
                            <span class="text-muted mb-2">Lien direct (QR Code) :</span>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control bg-light border-0" id="quizUrl" value="{{ route('qcm.login', $quiz->slug) }}" readonly>
                                <button class="btn btn-outline-primary border-0" type="button" onclick="copyToClipboard()">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </div>
                            <div id="copyFeedback" class="text-success small mt-1" style="display: none;">
                                <i class="bi bi-check-circle-fill me-1"></i> Lien copié !
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <form action="{{ route('admin.quizzes.toggle', $quiz) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn {{ $quiz->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} w-100 rounded-pill">
                                <i class="bi {{ $quiz->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-2"></i>
                                {{ $quiz->is_active ? 'Désactiver le Quiz' : 'Activer le Quiz' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Liste des Questions --}}
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold mb-0">Questions du questionnaire</h4>
            </div>

            @forelse($quiz->questions as $index => $question)
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden border-start border-4 {{ $question->type == 'multiple' ? 'border-warning' : 'border-primary' }}">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-light text-dark fs-6 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    {{ $index + 1 }}
                                </span>
                                <h5 class="fw-bold mb-0">{{ $question->libelle }}</h5>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical fs-5"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Supprimer cette question ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger small">
                                                <i class="bi bi-trash me-2"></i> Supprimer
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-4">
                            <span class="badge bg-light text-muted border px-2 py-1">
                                <i class="bi {{ $question->type == 'multiple' ? 'bi-check2-all' : 'bi-check2-circle' }} me-1"></i>
                                {{ $question->type == 'multiple' ? 'Choix multiples' : 'Choix unique' }}
                            </span>
                            <span class="badge bg-light text-muted border px-2 py-1 ms-2">
                                <i class="bi bi-star me-1"></i> {{ $question->points }} {{ Str::plural('point', $question->points) }}
                            </span>
                        </div>

                        <div class="row g-2">
                            @foreach($question->options as $option)
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between {{ $option->is_correct ? 'bg-success bg-opacity-10 border-success border-opacity-25' : 'bg-white' }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="bi {{ $option->is_correct ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
                                            <span class="{{ $option->is_correct ? 'fw-bold text-success' : '' }}">{{ $option->libelle }}</span>
                                        </div>
                                        @if($option->is_correct)
                                            <span class="badge bg-success small">Juste</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-4 bg-light">
                    <div class="card-body p-5 text-center">
                        <i class="bi bi-plus-circle text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="fw-bold">Aucune question ajoutée</h5>
                        <p class="text-muted">Commencez par ajouter une question pour que ce quiz soit prêt pour la formation.</p>
                        <button type="button" class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            <i class="bi bi-plus-lg me-2"></i> Ajouter ma première question
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal Ajout Question --}}
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Nouvelle question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.quizzes.questions.store', $quiz) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Intitulé de la question</label>
                        <input type="text" name="libelle" class="form-control" placeholder="Saisissez votre question..." required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Type de réponse</label>
                            <select name="type" class="form-select" required>
                                <option value="unique">Choix unique (Radio)</option>
                                <option value="multiple">Choix multiples (Checkbox)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Points attribués</label>
                            <input type="number" name="points" class="form-control" value="1" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold mb-0">Réponses possibles</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-option">
                                <i class="bi bi-plus-lg"></i> Ajouter une réponse
                            </button>
                        </div>
                        <div id="options-container">
                            {{-- Lignes de réponses injectées en JS --}}
                            <div class="option-row mb-2 d-flex align-items-center gap-3 bg-light p-3 rounded-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="options[0][is_correct]" value="1">
                                </div>
                                <input type="text" name="options[0][libelle]" class="form-control" placeholder="Option 1" required>
                                <button type="button" class="btn btn-link text-danger remove-option p-0"><i class="bi bi-trash fs-5"></i></button>
                            </div>
                            <div class="option-row mb-2 d-flex align-items-center gap-3 bg-light p-3 rounded-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="options[1][is_correct]" value="1">
                                </div>
                                <input type="text" name="options[1][libelle]" class="form-control" placeholder="Option 2" required>
                                <button type="button" class="btn btn-link text-danger remove-option p-0"><i class="bi bi-trash fs-5"></i></button>
                            </div>
                        </div>
                        <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Cochez la case à gauche pour désigner la (ou les) bonne(s) réponse(s).</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Enregistrer la question</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard() {
        const urlField = document.getElementById('quizUrl');
        urlField.select();
        urlField.setSelectionRange(0, 99999); // Pour mobile
        navigator.clipboard.writeText(urlField.value).then(() => {
            const feedback = document.getElementById('copyFeedback');
            feedback.style.display = 'block';
            setTimeout(() => {
                feedback.style.display = 'none';
            }, 2000);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('options-container');
        const addButton = document.getElementById('add-option');
        let optionCount = 2;

        addButton.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'option-row mb-2 d-flex align-items-center gap-3 bg-light p-3 rounded-3';
            row.innerHTML = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="options[${optionCount}][is_correct]" value="1">
                </div>
                <input type="text" name="options[${optionCount}][libelle]" class="form-control" placeholder="Option ${optionCount + 1}" required>
                <button type="button" class="btn btn-link text-danger remove-option p-0"><i class="bi bi-trash fs-5"></i></button>
            `;
            container.appendChild(row);
            optionCount++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-option')) {
                const rows = container.querySelectorAll('.option-row');
                if (rows.length > 2) {
                    e.target.closest('.option-row').remove();
                } else {
                    alert('Une question doit avoir au moins 2 options.');
                }
            }
        });
    });
</script>
@endpush
@endsection
