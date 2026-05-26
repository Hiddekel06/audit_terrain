@php
    $currentRoute = request()->route()?->getName();
@endphp

<aside class="admin-sidebar">
    <div class="admin-sidebar__brand">
        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__brand-link">
            <div class="admin-sidebar__brand-logo">
                <img src="/images/auditlogo.png" alt="Logo">
            </div>
            <div class="admin-sidebar__brand-text">
                <p class="admin-sidebar__brand-title">Plateforme Audit</p>
                <p class="admin-sidebar__brand-subtitle">Espace administrateur</p>
            </div>
        </a>
    </div>

    <nav class="admin-sidebar__nav">
        <div class="admin-sidebar__section-title">Navigation</div>
        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__link {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span class="admin-sidebar__link-label">Dashboard</span>
        </a>

        <a href="{{ route('admin.candidates.index') }}" class="admin-sidebar__link {{ str_starts_with($currentRoute, 'admin.candidates.') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span class="admin-sidebar__link-label">Gestion des candidats</span>
        </a>

        <a href="{{ route('admin.operations.research') }}" class="admin-sidebar__link {{ $currentRoute === 'admin.operations.research' ? 'active' : '' }}">
            <i class="bi bi-diagram-3"></i>
            <span class="admin-sidebar__link-label">Recherche opérationnelle</span>
        </a>

        <a
            href="{{ route('admin.regions.priorities') }}"
            class="admin-sidebar__link {{ $currentRoute === 'admin.regions.priorities' ? 'active' : '' }}"
            title="Utilisateurs par région"
            aria-label="Utilisateurs par région"
        >
            <i class="bi bi-people-fill"></i>
            <span class="admin-sidebar__link-label d-flex align-items-center justify-content-between w-100">
                <span>Utilisateurs par région</span>
                <span class="badge rounded-pill text-bg-light ms-2">Nouveau</span>
            </span>
        </a>

        <div class="admin-sidebar__section-title">Dashboard</div>
        <a href="{{ route('admin.ministeres.index') }}" class="admin-sidebar__link {{ $currentRoute === 'admin.ministeres.index' ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            <span class="admin-sidebar__link-label">Ministères</span>
        </a>

        <a href="{{ route('admin.dashboard') }}#motivations-section" class="admin-sidebar__link">
            <i class="bi bi-journal-text"></i>
            <span class="admin-sidebar__link-label">Motivations</span>
        </a>

        <a href="{{ route('admin.candidates.create') }}" class="admin-sidebar__link {{ $currentRoute === 'admin.candidates.create' ? 'active' : '' }}">
            <i class="bi bi-person-plus"></i>
            <span class="admin-sidebar__link-label">Nouvel agent</span>
        </a>

        <a href="{{ route('admin.dashboard') }}#regions-section" class="admin-sidebar__link">
            <i class="bi bi-geo-alt"></i>
            <span class="admin-sidebar__link-label">Tendances régions</span>
        </a>
    </nav>

    <div class="admin-sidebar__footer">
        <a href="{{ url('/') }}" class="btn btn-sm btn-outline-light w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-globe"></i>
            <span>Voir le site</span>
        </a>

        <button type="button" class="admin-sidebar__collapse-btn" data-admin-sidebar-toggle aria-label="Réduire ou étendre la sidebar">
            <i class="bi bi-layout-sidebar" data-admin-collapse-icon></i>
            <span class="admin-sidebar__collapse-label">Réduire le menu</span>
        </button>

        <form action="{{ route('admin.logout') }}" method="POST" class="d-grid">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-right"></i>
                <span>Déconnexion</span>
            </button>
        </form>
    </div>
</aside>
