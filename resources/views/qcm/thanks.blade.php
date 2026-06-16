<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci | Évaluation Terminée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .thanks-card { max-width: 500px; width: 100%; border: 0; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .icon-box { width: 80px; height: 80px; background: #ecfdf5; color: #065f46; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin: 0 auto 20px; }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="card thanks-card mx-auto text-center">
        <div class="card-body p-5">
            <div class="icon-box">
                <i class="bi bi-check-lg fs-1"></i>
            </div>
            
            @if(isset($alreadyDone) && $alreadyDone)
                <h3 class="fw-bold mb-3">Évaluation déjà effectuée</h3>
                <p class="text-muted mb-4">Vous avez déjà soumis vos réponses pour le quiz <strong>{{ $quiz->titre }}</strong>.</p>
            @else
                <h3 class="fw-bold mb-3">Merci !</h3>
                <p class="text-muted mb-4">Vos réponses pour le quiz <strong>{{ $quiz->titre }}</strong> ont été enregistrées avec succès.</p>
            @endif

            @if(isset($score))
                @php
                    $percentage = ($totalPoints > 0) ? ($score / $totalPoints) * 100 : 0;
                    $badgeClass = 'bg-danger';
                    if($percentage >= 80) $badgeClass = 'bg-success';
                    elseif($percentage >= 50) $badgeClass = 'bg-warning text-dark';
                @endphp
                <div class="score-display mb-4 p-4 rounded-4 bg-light border">
                    <div class="text-uppercase small fw-bold text-muted mb-2">Votre Score</div>
                    <div class="display-4 fw-bold mb-2">{{ $score }} <small class="fs-4 text-muted">/ {{ $totalPoints }}</small></div>
                    <div class="badge {{ $badgeClass }} rounded-pill px-3 py-2">
                        {{ number_format($percentage, 0) }}% de bonnes réponses
                    </div>
                </div>
            @endif

            <p class="small text-muted mb-5">Vous pouvez maintenant fermer cette fenêtre ou retourner à l'accueil.</p>

            <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-house me-2"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</div>

</body>
</html>
