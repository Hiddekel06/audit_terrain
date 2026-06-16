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
        .question-card { border: 0; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 1.5rem; }
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
        .sticky-footer { position: sticky; bottom: 0; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); padding: 1rem; border-top: 1px solid #edf2f7; z-index: 100; }
        .btn-submit { background: #065f46; border: 0; padding: 12px 30px; border-radius: 12px; font-weight: 700; width: 100%; }
        .btn-submit:hover { background: #064e3b; }
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
        
        @foreach($quiz->questions as $index => $question)
            <div class="card question-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-pill px-3">Question {{ $index + 1 }}</span>
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

        <div class="mt-4 mb-5 text-center">
            <p class="text-muted small">Assurez-vous d'avoir répondu à toutes les questions avant de valider.</p>
            <button type="button" class="btn btn-primary btn-submit shadow-lg" id="btnPreSubmit">
                Valider mes réponses <i class="bi bi-send-fill ms-2"></i>
            </button>
        </div>
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

        btnPreSubmit.addEventListener('click', function() {
            // 1. Vérifier si toutes les questions ont une réponse
            const questions = document.querySelectorAll('.question-card');
            let allAnswered = true;
            let firstUnanswered = null;

            questions.forEach((card, index) => {
                const inputs = card.querySelectorAll('input');
                const isAnswered = Array.from(inputs).some(input => input.checked);
                
                if (!isAnswered) {
                    allAnswered = false;
                    card.classList.add('border', 'border-danger');
                    if (!firstUnanswered) firstUnanswered = card;
                } else {
                    card.classList.remove('border', 'border-danger');
                }
            });

            if (!allAnswered) {
                alert('Veuillez répondre à toutes les questions avant de soumettre.');
                if (firstUnanswered) {
                    firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            // 2. Si tout est OK, ouvrir la modale
            confirmModal.show();
        });

        btnFinalSubmit.addEventListener('click', function() {
            btnFinalSubmit.disabled = true;
            btnFinalSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';
            quizForm.submit();
        });
    });
</script>
</body>
</html>
