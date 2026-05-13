@extends('layouts.app')

@section('content')
<div class="confirmation-wrapper">
    <div class="confirmation-card">
        <!-- Icône de succès animée -->
        <div class="success-icon">
            <svg viewBox="0 0 52 52" xmlns="http://www.w3.org/2000/svg">
                <circle class="success-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="success-check" fill="none" d="M14 27L23 36L38 18"/>
            </svg>
        </div>

        <h2 class="confirmation-title">Merci !</h2>
        
        <p class="confirmation-message">
            Vos choix de régions ont bien été enregistrés.
        </p>
        
        <p class="confirmation-submessage">
            Nous vous contacterons si votre candidature est retenue.
        </p>

        <div class="logo-container">
            <img src="/images/auditlogo.png" alt="Logo" class="logo">
        </div>

        <div class="button-group">
            <a href="/" class="btn-home">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-5v-8H7v8H5a2 2 0 0 1-2-2z"/>
                </svg>
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap');

    .confirmation-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: linear-gradient(135deg, #f5f7f5 0%, #e8ede8 100%);
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
    }

    .confirmation-card {
        max-width: 480px;
        width: 100%;
        background: #ffffff;
        border-radius: 32px;
        padding: 2.5rem;
        text-align: center;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(46, 125, 50, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .confirmation-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 30px 55px -12px rgba(46, 125, 50, 0.2);
    }

    /* Icône de succès animée */
    .success-icon {
        margin: 0 auto 1.5rem;
        width: 72px;
        height: 72px;
    }

    .success-circle {
        stroke: #2e7d32;
        stroke-width: 3;
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }

    .success-check {
        stroke: #2e7d32;
        stroke-width: 3;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.4s cubic-bezier(0.65, 0, 0.45, 1) 0.4s forwards;
    }

    @keyframes stroke {
        100% {
            stroke-dashoffset: 0;
        }
    }

    .confirmation-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1a3b2e;
        margin: 0 0 1rem 0;
        letter-spacing: -0.02em;
    }

    .confirmation-message {
        font-size: 1rem;
        font-weight: 500;
        color: #2e7d32;
        margin: 0 0 0.5rem 0;
        line-height: 1.5;
    }

    .confirmation-submessage {
        font-size: 0.9rem;
        color: #5f7b6b;
        margin: 0 0 2rem 0;
        line-height: 1.5;
    }

    .logo-container {
        margin: 2rem 0 1.5rem 0;
        padding-top: 1rem;
        border-top: 1px solid #e8ece8;
    }

    .logo {
        height: 48px;
        width: auto;
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .logo:hover {
        opacity: 1;
    }

    .button-group {
        margin-top: 0.5rem;
    }

    .btn-home {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 28px;
        background: linear-gradient(105deg, #1a5c3e 0%, #2b8c5e 100%);
        color: white;
        text-decoration: none;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        cursor: pointer;
        letter-spacing: 0.01em;
    }

    .btn-home:hover {
        background: linear-gradient(105deg, #0e4b31 0%, #1f6e4a 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(26, 92, 62, 0.25);
        color: white;
        text-decoration: none;
    }

    .btn-home:active {
        transform: translateY(0);
    }

    /* Responsive */
    @media (max-width: 640px) {
        .confirmation-card {
            padding: 2rem 1.5rem;
        }
        
        .confirmation-title {
            font-size: 1.75rem;
        }
        
        .btn-home {
            padding: 10px 24px;
            font-size: 0.85rem;
        }
    }
</style>
</div>
@endsection