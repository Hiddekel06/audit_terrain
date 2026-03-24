@extends('layouts.app')

@section('no-header')@endsection
@section('no-footer')@endsection
@section('body-bg', '#fff')

@section('content')
<style>
    .modern-card .form-control:focus {
        border-color: #5a8f4c;
        box-shadow: 0 0 0 2px rgba(90, 143, 76, 0.10);
        outline: none;
    }
    .modern-card .form-control {
        font-size: 1rem;
        padding: 0.65rem 1rem;
        border-radius: 12px;
        border-width: 1.5px;
    }
    .modern-card .input-group-text {
        background: #f8f9fc;
        border-color: #e2e8e0;
        font-size: 1.1rem;
    }
</style>
<div class="d-flex justify-content-center align-items-center min-vh-100 py-5" style="background: #fff;">
    <div class="modern-card p-4 p-md-5 shadow-lg" style="max-width: 410px; width: 100%; border-radius: 22px; background: #fff;">
        <div class="text-center mb-4">
            <img src="/images/auditlogo.png" alt="Logo" style="height: 54px; margin-bottom: 0.5rem;">
            <h2 class="form-title mb-1" style="font-size:1.5rem; font-weight:700; color:#2e7d32; letter-spacing:-1px;">Espace Administrateur</h2>
            <div style="font-size:1rem; color:#4a5b44; opacity:0.85;">Connexion sécurisée</div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger text-start">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.login.submit') }}" autocomplete="off">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius:12px 0 0 12px; color:#2e7d32;">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" class="form-control border-start-0" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@email.com" style="border-radius:0 12px 12px 0;">
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius:12px 0 0 12px; color:#2e7d32;">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control border-start-0" id="password" name="password" required placeholder="Votre mot de passe" style="border-radius:0 12px 12px 0;">
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-2 w-100" style="font-size:1.1rem; border-radius: 40px; font-weight:600;">Se connecter</button>
        </form>
    </div>
</div>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endsection
