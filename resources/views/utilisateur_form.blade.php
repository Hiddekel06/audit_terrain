@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300..700&display=swap');

    body {
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
        background-color: #f8f9fc;
    }

    /* Form card */
    .modern-card {
        border: none;
        border-radius: 20px;
        background-color: #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02), 0 2px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .form-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #1f2e1c;
        margin-bottom: 1.75rem;
        letter-spacing: -0.3px;
        border-left: 4px solid #5a8f4c;
        padding-left: 1rem;
    }

    .form-label {
        font-weight: 500;
        color: #4a5b44;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        letter-spacing: -0.2px;
    }

    .form-control {
        border: 1px solid #e2e8e0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        line-height: 1.5;
        background-color: #fefefe;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        border-color: #5a8f4c;
        box-shadow: 0 0 0 3px rgba(90, 143, 76, 0.15);
        outline: none;
    }

    .btn-modern {
        background-color: #5a8f4c;
        border: none;
        border-radius: 40px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 0.9375rem;
        color: white;
        transition: background-color 0.2s, transform 0.1s;
        width: 100%;
        letter-spacing: 0.3px;
    }

    .btn-modern:hover {
        background-color: #457a37;
        transform: translateY(-1px);
    }

    .btn-modern:active {
        transform: translateY(1px);
    }

    /* Alert custom */
    .alert-custom {
        background-color: #fef5e8;
        border-left: 4px solid #d68c3c;
        border-radius: 12px;
        color: #a45d2e;
        padding: 0.75rem 1rem;
        margin-bottom: 1.75rem;
        font-size: 0.875rem;
    }

    .alert-custom ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    .alert-custom li {
        margin-bottom: 0.25rem;
    }

    /* Optional hint */
    .optional-hint {
        font-size: 0.7rem;
        color: #8a9a82;
        margin-left: 0.25rem;
        font-weight: 400;
    }

    /* Smooth transitions */
    input, button {
        transition: all 0.2s ease;
    }
</style>

<div class="d-flex justify-content-center align-items-center min-vh-100 py-5" style="background-color: #f8f9fc;">
    <div class="modern-card p-4 p-md-5" style="max-width: 500px; width: 100%;">
        <h2 class="form-title">Identification utilisateur</h2>

        @if ($errors->any())
            <div class="alert-custom">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('utilisateur.store') }}">
            @csrf
            <div class="mb-4">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom') }}" required placeholder="Votre prénom">
            </div>

            <div class="mb-4">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}" required placeholder="Votre nom">
            </div>

            <div class="mb-3">
                <label for="matricule" class="form-label">Matricule <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="Entrez votre matricule" required>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" value="1" id="no_matricule" name="no_matricule">
                    <label class="form-check-label" for="no_matricule">
                        Pas de matricule
                    </label>
                </div>
            </div>

            <div class="mb-4" id="telephone_block" style="display:none;">
                <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text" style="background:#f3f3f3; min-width:70px; border-radius:12px 0 0 12px; border:1px solid #e2e8e0;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/f/fd/Flag_of_Senegal.svg" alt="SN" style="width:22px; height:15px; margin-right:6px; border-radius:2px;"> +221
                    </span>
                    <input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="Votre numéro de téléphone" style="border-radius:0 12px 12px 0;" inputmode="numeric" pattern="[0-9]*" maxlength="9">
                </div>
            </div>

            <button type="submit" class="btn-modern mt-2">Valider</button>
        </form>
    </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const noMatricule = document.getElementById('no_matricule');
        const matriculeInput = document.getElementById('matricule');
        const telBlock = document.getElementById('telephone_block');
        const telInput = document.getElementById('telephone');

        function toggleMatricule() {
            if (noMatricule.checked) {
                matriculeInput.value = '';
                matriculeInput.setAttribute('disabled', 'disabled');
                matriculeInput.removeAttribute('required');
                telBlock.style.display = '';
                telInput.setAttribute('required', 'required');
            } else {
                matriculeInput.removeAttribute('disabled');
                matriculeInput.setAttribute('required', 'required');
                telBlock.style.display = 'none';
                telInput.value = '';
                telInput.removeAttribute('required');
            }
        }
        noMatricule.addEventListener('change', toggleMatricule);
        toggleMatricule();

        // Empêche la saisie de caractères non numériques dans le champ téléphone
        telInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>
@endsection