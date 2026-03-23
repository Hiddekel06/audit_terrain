@extends('layouts.app')

@section('content')
<style>
    body {
        font-family: 'Roboto', system-ui, -apple-system, 'Segoe UI', sans-serif;
        background-color: #f5f5f5;
    }

    .google-form-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        background-color: #fff;
        transition: box-shadow 0.2s ease-in-out;
    }

    .form-title {
        font-size: 1.75rem;
        font-weight: 500;
        color: #202124;
        margin-bottom: 1.5rem;
        letter-spacing: -0.25px;
    }

    .form-label {
        font-weight: 500;
        color: #5f6368;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-control {
        border: 1px solid #dadce0;
        border-radius: 4px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        line-height: 1.5;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        border-color: #1a73e8;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05), 0 0 0 2px rgba(26, 115, 232, 0.2);
        outline: none;
    }

    .btn-google {
        background-color: #1a73e8;
        border: none;
        border-radius: 4px;
        padding: 0.75rem 1rem;
        font-weight: 500;
        font-size: 0.9375rem;
        color: #fff;
        transition: background-color 0.2s, box-shadow 0.2s;
        width: 100%;
    }

    .btn-google:hover {
        background-color: #1765cc;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .btn-google:focus {
        box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.4);
        outline: none;
    }

    .alert-custom {
        background-color: #fce8e6;
        border-left: 4px solid #d93025;
        border-radius: 4px;
        color: #d93025;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
    }

    .alert-custom ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    .alert-custom li {
        margin-bottom: 0.25rem;
    }

    /* Optional: add a subtle hint for optional fields */
    .optional-hint {
        font-size: 0.75rem;
        color: #5f6368;
        margin-left: 0.25rem;
        font-weight: normal;
    }
</style>

<div class="d-flex justify-content-center align-items-center min-vh-100 py-5" style="background-color: #f5f5f5;">
    <div class="google-form-card p-4 p-md-5" style="max-width: 500px; width: 100%;">
        <h2 class="form-title text-center text-md-start">Identification utilisateur</h2>

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

            <div class="mb-4">
                <label for="matricule" class="form-label">
                    Matricule <span class="optional-hint">(optionnel)</span>
                </label>
                <input type="text" class="form-control" id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="Entrez votre matricule">
            </div>

            <div class="mb-4">
                <label for="telephone" class="form-label">
                    Téléphone <span class="optional-hint">(optionnel si matricule)</span>
                </label>
                <input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="Votre numéro de téléphone">
            </div>

            <button type="submit" class="btn-google mt-2">Valider</button>
        </form>
    </div>
</div>
@endsection