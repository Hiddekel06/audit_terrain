<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion QCM | Plateforme Audit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            max-width: 450px;
            width: 100%;
            border: 0;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        }
        .btn-primary {
            background: #065f46;
            border: 0;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #064e3b;
        }
        .brand-logo {
            width: auto;
            height: 80px;
            max-width: 100%;
            object-fit: contain;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="card login-card mx-auto">
        <div class="card-body p-4 p-md-5 text-center">
            <img src="/images/auditlogo.png" alt="Logo" class="brand-logo">
            <h4 class="fw-bold mb-1">Accès à l'évaluation</h4>
            <p class="text-muted mb-4">{{ $quiz->titre }}</p>

            @if($errors->any())
                <div class="alert alert-danger border-0 small py-2">
                    <ul class="mb-0 list-unstyled">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('qcm.login.submit', $quiz->slug) }}" method="POST">
                @csrf
                <div class="text-start mb-3">
                    <label class="form-label small fw-bold text-muted">Matricule ou CIN</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="identifier" class="form-control bg-light border-0" placeholder="Ex: 695130D" value="{{ old('identifier') }}" required>
                    </div>
                </div>

                <div class="text-start mb-4">
                    <label class="form-label small fw-bold text-muted">Mot de passe formation</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-shield-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-0" placeholder="Donné par le formateur" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3 shadow-sm">
                    Accéder au Quiz <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Modale d'inscription (Garde-fou) --}}
@if(session('show_registration_modal'))
    <div class="modal fade" id="registrationModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg text-center p-4">
                <div class="modal-body">
                    <div class="mb-3">
                        <i class="bi bi-person-check fs-1 text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Presque fini !</h5>
                    <p class="text-muted">Votre matricule <strong>{{ session('user_matricule') }}</strong> est bien reconnu sur la liste officielle, mais vous n'avez pas encore finalisé votre inscription sur la plateforme.</p>
                    <p class="small text-muted mb-4">Merci de remplir votre profil (2 minutes) pour débloquer l'accès à l'évaluation.</p>
                    
                    <a href="{{ route('utilisateur.form') }}?matricule={{ session('user_matricule') }}" class="btn btn-success w-100 rounded-pill py-2 fw-bold">
                        Finaliser mon inscription <i class="bi bi-pencil-square ms-2"></i>
                    </a>
                    <button type="button" class="btn btn-link text-muted mt-3 small text-decoration-none" data-bs-dismiss="modal">Plus tard</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
        modal.show();
    </script>
@endif

</body>
</html>
