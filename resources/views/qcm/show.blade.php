<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quiz->titre }} | Évaluation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .quiz-header { background: #065f46; color: white; padding: 2rem 0; border-radius: 0 0 32px 32px; margin-bottom: 2rem; }
        .question-card { border: 0; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 1.5rem; transition: all 0.3s ease; }
        .option-label { 
            display: block; 
            padding: 1rem 1.25rem; 
            border: 2px solid #edf2f7; 
            border-radius: 12px; 
            cursor: pointer; 
            transition: all 0.2s; 
            margin-bottom: 0.75rem;
            position: relative;
        }
        .option-input:checked + .option-label { 
            border-color: #065f46; 
            background: #ecfdf5; 
            color: #065f46; 
            font-weight: 600; 
        }
        .option-input { display: none; }
        .btn-submit { background: #065f46; border: 0; padding: 12px 30px; border-radius: 12px; font-weight: 700; width: 100%; }
        .btn-submit:hover { background: #064e3b; }
        .quiz-step { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<header class="quiz-header shadow-sm">
    <div class="container px-4">
        <div class="d-flex align-items-center gap-3 mb-2">
            <div class="bg-white bg-opacity-20 p-2 rounded-3">
                <img src="/images/auditlogo.png" alt="Logo" style="width: 40px; height: 40px; object-fit: contain;">
            </div>
            <div>
                <h4 class="fw-bold mb-0">{{ $quiz->titre }}</h4>
                <p class="small mb-0 opacity-75">Agent : {{ $user->nom }} {{ $user->prenom }} ({{ $user->matricule }})</p>
            </div>
        </div>
    </div>
</header>

<div class="container px-4 pb-5">
    <form action="{{ route('qcm.submit', $quiz->slug) }}" method="POST" id="quizForm">
        @csrf
        
        @php 
            $globalQuestionIndex = 1;
            $activeSections = $quiz->sections->filter(fn($s) => $s->questions->count() > 0)->values();
        @endphp

        @foreach($activeSections as $sIndex => $section)
            <div class="quiz-step" id="step-{{ $sIndex }}" style="{{ $sIndex > 0 ? 'display:none;' : '' }}">
                <div class="section-header mb-4 mt-5">
                    <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                        <span class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.9rem;">
                            {{ $sIndex + 1 }}
                        </span>
                        {{ $section->titre }}
                    </h5>
                    @if($section->description)
                        <p class="text-muted small mb-0 ms-4 ps-2">{{ $section->description }}</p>
                    @endif
                    <hr class="mt-2 opacity-10">
                </div>

                @foreach($section->questions as $question)
                    <div class="card question-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-primary rounded-pill px-3">Question {{ $globalQuestionIndex++ }}</span>
                                @if($question->type === 'multiple')
                                    <span class="badge bg-warning text-dark rounded-pill px-3">Plusieurs choix possibles</span>
                                @endif
                            </div>
                            <h5 class="fw-bold mb-4">{{ $question->libelle }}</h5>

                            <div class="options-list">
                                @foreach($question->options as $option)
                                    <div class="option-item">
                                        <input class="option-input" 
                                               type="{{ $question->type === 'multiple' ? 'checkbox' : 'radio' }}" 
                                               name="answers[{{ $question->id }}]{{ $question->type === 'multiple' ? '[]' : '' }}" 
                                               id="option_{{ $option->id }}" 
                                               value="{{ $option->id }}">
                                        <label class="option-label" for="option_{{ $option->id }}">
                                            {{ $option->libelle }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="step-navigation d-flex gap-3 mt-5">
                    @if(!$loop->first)
                        <button type="button" class="btn btn-outline-secondary btn-lg rounded-pill px-4 flex-grow-1 prev-step" data-current="{{ $sIndex }}">
                            <i class="bi bi-arrow-left me-2"></i> Précédent
                        </button>
                    @endif

                    @if(!$loop->last)
                        <button type="button" class="btn btn-primary btn-lg rounded-pill px-4 flex-grow-1 next-step" data-current="{{ $sIndex }}">
                            Suivant <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    @else
                        <div class="flex-grow-1">
                            <p class="text-muted small text-center mb-2">Assurez-vous d'avoir répondu à tout avant de valider.</p>
                            <button type="button" class="btn btn-success btn-lg rounded-pill px-4 w-100 shadow-lg" id="btnPreSubmit">
                                Terminer et envoyer <i class="bi bi-check-all ms-2"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </form>
</div>

{{-- Modale de Confirmation de Soumission --}}
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg text-center p-4">
            <div class="modal-body">
                <div class="mb-3">
                    <i class="bi bi-question-circle fs-1 text-primary"></i>
                </div>
                <h5 class="fw-bold mb-3">Soumettre vos réponses ?</h5>
                <p class="text-muted">Une fois validées, vous ne pourrez plus modifier vos réponses pour cette évaluation.</p>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="button" class="btn btn-primary rounded-pill py-2 fw-bold" id="btnFinalSubmit">
                        Oui, je valide <i class="bi bi-check-circle ms-2"></i>
                    </button>
                    <button type="button" class="btn btn-link text-muted small text-decoration-none" data-bs-dismiss="modal">Vérifier encore mes choix</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quizForm = document.getElementById('quizForm');
        const btnPreSubmit = document.getElementById('btnPreSubmit');
        const btnFinalSubmit = document.getElementById('btnFinalSubmit');
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));

        // Navigation entre étapes
        const nextBtns = document.querySelectorAll('.next-step');
        const prevBtns = document.querySelectorAll('.prev-step');

        function validateStep(stepId) {
            const stepContainer = document.getElementById('step-' + stepId);
            if (!stepContainer) return true;

            const questions = stepContainer.querySelectorAll('.question-card');
            let allAnswered = true;
            let firstUnanswered = null;

            questions.forEach((card) => {
                const inputs = card.querySelectorAll('input');
                const isAnswered = Array.from(inputs).some(input => input.checked);
                
                if (!isAnswered) {
                    allAnswered = false;
                    card.style.border = '2px solid #dc3545';
                    if (!firstUnanswered) firstUnanswered = card;
                } else {
                    card.style.border = '0';
                }
            });

            if (!allAnswered) {
                alert('Veuillez répondre à toutes les questions de cette section avant de continuer.');
                if (firstUnanswered) {
                    firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            return true;
        }

        nextBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const current = parseInt(this.getAttribute('data-current'));
                if (validateStep(current)) {
                    document.getElementById('step-' + current).style.display = 'none';
                    document.getElementById('step-' + (current + 1)).style.display = 'block';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });

        prevBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const current = parseInt(this.getAttribute('data-current'));
                document.getElementById('step-' + current).style.display = 'none';
                document.getElementById('step-' + (current - 1)).style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        if (btnPreSubmit) {
            btnPreSubmit.addEventListener('click', function() {
                // On récupère l'ID de la dernière section visible
                const currentStepVisible = document.querySelector('.quiz-step[style*="display: block"]');
                const lastStepId = currentStepVisible ? parseInt(currentStepVisible.id.replace('step-', '')) : 0;
                
                if (validateStep(lastStepId)) {
                    confirmModal.show();
                }
            });
        }

        btnFinalSubmit.addEventListener('click', function() {
            btnFinalSubmit.disabled = true;
            btnFinalSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';
            quizForm.submit();
        });
    });
</script>
</body>
</html>
