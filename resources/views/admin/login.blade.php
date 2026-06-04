@extends('layouts.app')

@section('no-header')@endsection
@section('no-footer')@endsection
@section('body-bg', '#fff')

@section('content')
<style>
    :root {
        --emerald-50: #ecfdf5;
        --emerald-100: #d1fae5;
        --emerald-500: #10b981;
        --emerald-600: #059669;
        --emerald-700: #047857;
        --slate-50: #f8fafc;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
    }

    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: 
            radial-gradient(circle at top right, var(--emerald-50), transparent 40%),
            radial-gradient(circle at bottom left, var(--slate-50), transparent 40%),
            #ffffff;
        padding: 1.5rem;
    }

    .login-card {
        width: 100%;
        max-width: 1100px;
        background: #fff;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        border: 1px solid var(--emerald-100);
        display: flex;
        flex-direction: row;
    }

    .login-visual {
        flex: 1;
        position: relative;
        background-color: var(--slate-50);
        min-height: 520px;
        display: none; /* Hidden on mobile */
    }

    @media (min-width: 768px) {
        .login-visual {
            display: flex;
        }
    }

    .login-visual img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 3rem;
        z-index: 1;
    }

    .login-visual-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.85) 0%, rgba(248, 250, 252, 0.75) 50%, rgba(236, 253, 245, 0.75) 100%);
        z-index: 2;
    }

    .login-visual-content {
        position: relative;
        z-index: 10;
        height: 100%;
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: var(--slate-800);
    }

    .login-form-container {
        flex: 1;
        padding: 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    @media (min-width: 992px) {
        .login-form-container {
            padding: 4rem;
        }
    }

    .form-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--slate-900);
        margin-bottom: 0.5rem;
    }

    .form-subtitle {
        color: var(--slate-600);
        margin-bottom: 2rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--slate-700);
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.625rem;
        transition: all 0.2s;
        font-size: 0.95rem;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--emerald-500);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }

    .btn-login {
        width: 100%;
        padding: 0.75rem 1rem;
        background-color: var(--emerald-600);
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 0.625rem;
        transition: all 0.2s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-top: 1.5rem;
    }

    .btn-login:hover {
        background-color: var(--emerald-700);
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .btn-login:active {
        transform: scale(0.98);
    }

    .error-box {
        background-color: #fff1f2;
        border: 1px solid #fecdd3;
        color: #be123c;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .eyebrow {
        text-transform: uppercase;
        letter-spacing: 0.2em;
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--emerald-700);
        margin-bottom: 1rem;
    }

    .remember-me {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--slate-600);
        cursor: pointer;
    }

    .remember-me input {
        width: 1rem;
        height: 1rem;
        accent-color: var(--emerald-600);
        cursor: pointer;
    }
</style>

<div class="login-container">
    <div class="login-card">
        <!-- Visual Pane (Hidden on Mobile) -->
        <div class="login-visual">
            <img src="{{ asset('images/auditlogo.png') }}" alt="Audit Terrain Visual">
            <div class="login-visual-overlay"></div>
            <div class="login-visual-content">
                <div>
                    <div class="eyebrow">Audit Terrain - Système RH</div>
                    <h2 class="fw-bold display-6">Gérez les déploiements et équipes en toute simplicité</h2>
                    <p class="mt-3 opacity-75">Suivi des agents, gestion des régions et organisation des unités opérationnelles sur le terrain.</p>
                </div>
                <div class="small fw-bold opacity-50">Version 1.2</div>
            </div>
        </div>

        <!-- Form Pane -->
        <div class="login-form-container">
            <div class="text-center mb-4 d-md-none">
                <img src="{{ asset('images/auditlogo.png') }}" alt="Logo" style="height: 40px;">
            </div>
            
            <div class="text-center text-md-start mb-4">
                <h1 class="form-title">Connexion</h1>
                <p class="form-subtitle">Accédez à votre espace de gestion administrateur</p>
            </div>

            @if ($errors->any())
                <div class="error-box">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@audit-terrain.sn">
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <div class="d-flex align-items-center justify-content-between">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                    
                    <a href="#" class="text-emerald-700 text-decoration-none small fw-bold hover-opacity-75">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn-login">
                    Se connecter
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endsection
