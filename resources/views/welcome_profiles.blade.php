@extends('layouts.app')

@section('body-bg', '#f6f9f7')

@section('content')
<style>
    :root {
        --primary-dark: #0a2b22;
        --primary-mid: #1c4e3f;
        --primary-light: #eaf6f1;
        --accent-blue: #2563eb;
        --accent-green: #10b981;
        --gray-soft: #6b7b72;
        --border-light: rgba(0, 0, 0, 0.05);
    }

    .welcome-hero {
        max-width: 1100px;
        margin: 3rem auto 4rem;
        padding: 0 1.5rem;
    }

    /* Header section avec effet glass */
    .welcome-header {
        text-align: center;
        margin-bottom: 3.5rem;
        position: relative;
    }

    .welcome-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        background: linear-gradient(135deg, #0a2b22 0%, #1c6e52 100%);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }

    .welcome-subtitle {
        color: var(--gray-soft);
        max-width: 620px;
        margin: 0 auto;
        font-size: 1.1rem;
        line-height: 1.5;
        font-weight: 400;
    }

    /* Décoration ornementale */
    .hero-accent {
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-blue), var(--accent-green));
        border-radius: 999px;
        opacity: 0.6;
    }

    /* Grille améliorée */
    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }

    /* Cartes modernes avec glassmorphisme et hover 3D */
    .profile-card {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(0px);
        border-radius: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.5);
        transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    .profile-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.03), transparent 70%);
        pointer-events: none;
    }

    .profile-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 35px -12px rgba(0, 0, 0, 0.15);
        border-color: rgba(37, 99, 235, 0.2);
        background: white;
    }

    /* Header de carte avec gradient subtil */
    .profile-card__header {
        padding: 1.5rem 1.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
    }

    .profile-card__icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        transition: transform 0.2s ease;
    }

    .profile-card:hover .profile-card__icon {
        transform: scale(1.05);
    }

    .it-icon {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0369a1;
        box-shadow: 0 4px 8px rgba(3, 105, 161, 0.1);
    }

    .support-icon {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #047857;
        box-shadow: 0 4px 8px rgba(4, 120, 87, 0.1);
    }

    .profile-card__title {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 700;
        background: linear-gradient(135deg, #1e293b 0%, #2d3a4a 100%);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        letter-spacing: -0.01em;
    }

    /* Body avec meilleure hiérarchie */
    .profile-card__body {
        padding: 0.25rem 1.75rem 1.75rem;
    }

    .profile-card__text {
        color: #475569;
        margin-bottom: 1.25rem;
        font-size: 0.95rem;
        line-height: 1.5;
        border-left: 3px solid #e2e8f0;
        padding-left: 0.85rem;
    }

    /* Liste stylisée avec puces modernes */
    .profile-list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .profile-list li {
        color: #334155;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        transition: transform 0.2s;
    }

    .profile-list li::before {
        content: '▹';
        color: var(--accent-green);
        font-size: 0.8rem;
        font-weight: 600;
        opacity: 0.8;
    }

    .profile-list li:hover {
        transform: translateX(4px);
        color: var(--primary-mid);
    }

    /* Badge plus stylé */
    .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 1.2rem;
        padding: 0.45rem 1rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        backdrop-filter: blur(4px);
        transition: all 0.2s;
    }

    .badge-soft i {
        font-size: 0.85rem;
    }

    .it-badge {
        background: rgba(37, 99, 235, 0.08);
        color: #1e40af;
        border: 1px solid rgba(37, 99, 235, 0.2);
    }

    .support-badge {
        background: rgba(16, 185, 129, 0.08);
        color: #0b5e42;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .badge-soft:hover {
        transform: translateY(-2px);
        filter: brightness(0.98);
    }

    .welcome-action {
        margin-top: 2.2rem;
        display: flex;
        justify-content: center;
    }

    .welcome-action__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        text-decoration: none;
        color: #ffffff;
        background: linear-gradient(135deg, #1b5a45 0%, #2a7a5c 100%);
        border: 1px solid rgba(24, 76, 58, 0.45);
        border-radius: 999px;
        padding: 0.85rem 1.4rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        box-shadow: 0 12px 24px -14px rgba(12, 52, 37, 0.65);
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    .welcome-action__btn:hover {
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 14px 28px -14px rgba(12, 52, 37, 0.75);
        filter: brightness(1.02);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .welcome-hero {
            margin: 1.5rem auto 2.5rem;
        }

        .profile-card__header {
            padding: 1.25rem 1.25rem 0.75rem;
        }

        .profile-card__body {
            padding: 0.25rem 1.25rem 1.25rem;
        }
    }

    /* Animation d'apparition */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-card {
        animation: fadeUp 0.6s ease backwards;
    }

    .profile-card:first-child {
        animation-delay: 0.1s;
    }

    .profile-card:last-child {
        animation-delay: 0.2s;
    }
</style>

<div class="welcome-hero">
    <div class="welcome-header">
        <div class="hero-accent"></div>
        <h1 class="welcome-title">
            Audit Physique et Biometrique
        </h1>
        <p class="welcome-subtitle">
            Cette page présente les profils disponibles dans notre organisation. Elle sert de point d'information avant la soumission du formulaire utilisateur.
        </p>
    </div>

    <div class="profile-grid">
        <!-- Profils IT -->
        <article class="profile-card">
            <div class="profile-card__header">
                <span class="profile-card__icon it-icon">
                    <i class="bi bi-cpu"></i>
                </span>
                <h2 class="profile-card__title">Profils IT</h2>
            </div>
            <div class="profile-card__body">
                <p class="profile-card__text">
                    Profils techniques orientés systèmes, développement et accompagnement digital.
                </p>
                <ul class="profile-list">
                    <li>Développement applicatif</li>
                    <li>Administration système et réseau</li>
                    <li>Sécurité et infrastructure</li>
                    <li>Support technique outils</li>
                </ul>
                <span class="badge-soft it-badge">
                    <i class="bi bi-terminal"></i> Domaine technique
                </span>
            </div>
        </article>

        <!-- Profils Supports -->
        <article class="profile-card">
            <div class="profile-card__header">
                <span class="profile-card__icon support-icon">
                    <i class="bi bi-people"></i>
                </span>
                <h2 class="profile-card__title">Profils Supports</h2>
            </div>
            <div class="profile-card__body">
                <p class="profile-card__text">
                    Profils fonctionnels et transverses pour le pilotage et l'appui aux opérations.
                </p>
                <ul class="profile-list">
                    <li>Ressources humaines</li>
                    <li>Finances et contrôle</li>
                    <li>Achats et logistique</li>
                    <li>Administration générale</li>
                </ul>
                <span class="badge-soft support-badge">
                    <i class="bi bi-briefcase"></i> Domaine fonctionnel
                </span>
            </div>
        </article>
    </div>

    <div class="welcome-action">
        <a href="{{ route('utilisateur.form') }}" class="welcome-action__btn">
            <i class="bi bi-arrow-right-circle"></i>
            <span>Faire mon choix</span>
        </a>
    </div>
</div>
@endsection