<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Admin | Plateforme Audit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --admin-sidebar-width: 280px;
            --admin-sidebar-collapsed-width: 88px;
            --admin-sidebar-bg: #2f5f3d;
            --admin-sidebar-bg-soft: #3e7750;
            --admin-sidebar-border: rgba(255, 255, 255, 0.10);
            --admin-sidebar-text: #f6fbf7;
            --admin-sidebar-muted: #d7eadc;
            --admin-content-bg: #f7faf8;
            --admin-sidebar-active: rgba(255, 255, 255, 0.16);
            --admin-sidebar-hover: rgba(255, 255, 255, 0.10);
            --admin-card-bg: #ffffff;
            --admin-card-border: rgba(47, 95, 61, 0.08);
            --admin-surface: #ffffff;
        }

        body.admin-layout {
            margin: 0;
            min-height: 100vh;
            background: var(--admin-content-bg);
            overflow-x: hidden;
        }

        .admin-shell {
            min-height: 100vh;
            display: flex;
        }

        .admin-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--admin-sidebar-width);
            background: linear-gradient(180deg, var(--admin-sidebar-bg) 0%, var(--admin-sidebar-bg-soft) 100%);
            color: var(--admin-sidebar-text);
            border-right: 1px solid var(--admin-sidebar-border);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: width 0.25s ease, transform 0.25s ease;
        }

        .admin-sidebar__brand {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid var(--admin-sidebar-border);
        }

        .admin-sidebar__brand-link {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            text-decoration: none;
            color: inherit;
        }

        .admin-sidebar__brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.18);
            display: grid;
            place-items: center;
            overflow: hidden;
            flex: 0 0 auto;
        }

        .admin-sidebar__brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .admin-sidebar__brand-text {
            min-width: 0;
        }

        .admin-sidebar__brand-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.1;
        }

        .admin-sidebar__brand-subtitle {
            margin: 0.15rem 0 0;
            font-size: 0.8rem;
            color: var(--admin-sidebar-muted);
        }

        .admin-sidebar__nav {
            padding: 1rem 0.75rem 1.25rem;
            overflow-y: auto;
            flex: 1 1 auto;
                scrollbar-width: none;
        }

        .admin-sidebar__section-title {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--admin-sidebar-muted);
            padding: 0 0.75rem;
            margin: 0.75rem 0 0.5rem;
        }

        .admin-sidebar__link {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            width: 100%;
            border: 0;
            background: transparent;
            color: var(--admin-sidebar-text);
            text-decoration: none;
            border-radius: 16px;
            padding: 0.9rem 1rem;
            transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .admin-sidebar__link:hover,
        .admin-sidebar__link.active {
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
            transform: translateX(2px);
        }

        .admin-sidebar__link:hover {
            background: var(--admin-sidebar-hover);
        }

        .admin-sidebar__link i {
            font-size: 1.1rem;
            width: 1.5rem;
            text-align: center;
            flex: 0 0 auto;
        }

        .admin-sidebar__link-label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .admin-sidebar__footer {
            padding: 1rem 1rem 1.25rem;
            border-top: 1px solid var(--admin-sidebar-border);
        }

        .admin-sidebar__collapse-btn {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            background: rgba(255, 255, 255, 0.12);
            color: var(--admin-sidebar-text);
            padding: 0.6rem 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            margin-bottom: 0.75rem;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .admin-sidebar__collapse-btn:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.35);
        }

        .admin-card,
        .card {
            background: var(--admin-card-bg);
            border-color: var(--admin-card-border);
        }

        .admin-topbar {
            background: rgba(255, 255, 255, 0.92);
            border-bottom: 1px solid rgba(47, 95, 61, 0.08);
        }

        .admin-sidebar__footer .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.35);
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .admin-sidebar__footer .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .admin-sidebar__collapse-label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .admin-content {
            margin-left: var(--admin-sidebar-width);
            min-height: 100vh;
            width: calc(100% - var(--admin-sidebar-width));
            transition: margin-left 0.25s ease, width 0.25s ease;
        }

        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            backdrop-filter: blur(16px);
            background: rgba(244, 247, 246, 0.9);
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        }

        .admin-page-body {
            padding: 1.25rem;
        }

        .admin-sidebar-toggle {
            width: 44px;
            height: 44px;
            border-radius: 14px;
        }

        .admin-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 1035;
        }

        body.admin-sidebar-collapsed .admin-sidebar {
            width: var(--admin-sidebar-collapsed-width);
        }

        body.admin-sidebar-collapsed .admin-content {
            margin-left: var(--admin-sidebar-collapsed-width);
            width: calc(100% - var(--admin-sidebar-collapsed-width));
        }

        body.admin-sidebar-collapsed .admin-sidebar__brand-text,
        body.admin-sidebar-collapsed .admin-sidebar__link-label,
        body.admin-sidebar-collapsed .admin-sidebar__section-title,
        body.admin-sidebar-collapsed .admin-sidebar__footer .btn span,
        body.admin-sidebar-collapsed .admin-sidebar__collapse-label {
            display: none;
        }

        body.admin-sidebar-collapsed .admin-sidebar__brand {
            padding-inline: 0.75rem;
        }

        body.admin-sidebar-collapsed .admin-sidebar__brand-link,
        body.admin-sidebar-collapsed .admin-sidebar__link,
        body.admin-sidebar-collapsed .admin-sidebar__collapse-btn {
            justify-content: center;
        }

        body.admin-sidebar-collapsed .admin-sidebar__link {
            padding-inline: 0.75rem;
        }

        body.admin-sidebar-collapsed .admin-sidebar__collapse-btn {
            padding-inline: 0.5rem;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .admin-content {
                margin-left: 0;
                width: 100%;
            }

            body.admin-sidebar-collapsed .admin-sidebar,
            body.admin-sidebar-open .admin-sidebar {
                transform: translateX(0);
            }

            body.admin-sidebar-collapsed .admin-content {
                margin-left: 0;
                width: 100%;
            }

            body.admin-sidebar-open .admin-sidebar-overlay {
                display: block;
            }

            body.admin-sidebar-collapsed .admin-sidebar__brand-text,
            body.admin-sidebar-collapsed .admin-sidebar__link-label,
            body.admin-sidebar-collapsed .admin-sidebar__section-title,
            body.admin-sidebar-collapsed .admin-sidebar__footer .btn span,
            body.admin-sidebar-collapsed .admin-sidebar__collapse-label {
                display: inline;
            }
        }

        @media (max-width: 575.98px) {
            .admin-page-body {
                padding: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="admin-layout">
    <div class="admin-sidebar-overlay" data-admin-sidebar-overlay></div>

    @include('partials.admin-sidebar')

    <div class="admin-content">
        <div class="admin-topbar">
            <div class="d-flex align-items-center justify-content-between gap-3 px-3 px-md-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-outline-secondary admin-sidebar-toggle" data-admin-sidebar-toggle aria-label="Basculer la sidebar">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div>
                        <h1 class="h5 fw-bold mb-0 text-dark">@yield('admin-title', 'Administration')</h1>
                        <p class="text-muted mb-0 small">@yield('admin-subtitle', 'Pilotage et suivi de la plateforme')</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @yield('admin-actions')
                </div>
            </div>
        </div>

        <main class="admin-page-body">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const body = document.body;
            const toggleButtons = document.querySelectorAll('[data-admin-sidebar-toggle]');
            const overlay = document.querySelector('[data-admin-sidebar-overlay]');
            const mq = window.matchMedia('(max-width: 991.98px)');
            const storageKey = 'admin-sidebar-collapsed';
            const collapseIcons = document.querySelectorAll('[data-admin-collapse-icon]');

            function syncCollapseIcons() {
                const isCollapsed = body.classList.contains('admin-sidebar-collapsed');
                collapseIcons.forEach(function (icon) {
                    icon.classList.remove('bi-layout-sidebar', 'bi-layout-sidebar-inset');
                    icon.classList.add(isCollapsed ? 'bi-layout-sidebar-inset' : 'bi-layout-sidebar');
                });
            }

            function closeMobileSidebar() {
                body.classList.remove('admin-sidebar-open');
            }

            function toggleSidebar() {
                if (mq.matches) {
                    body.classList.toggle('admin-sidebar-open');
                    body.classList.remove('admin-sidebar-collapsed');
                } else {
                    body.classList.toggle('admin-sidebar-collapsed');
                    body.classList.remove('admin-sidebar-open');
                    localStorage.setItem(storageKey, body.classList.contains('admin-sidebar-collapsed') ? '1' : '0');
                }

                syncCollapseIcons();
            }

            if (!mq.matches) {
                const collapsed = localStorage.getItem(storageKey) === '1';
                body.classList.toggle('admin-sidebar-collapsed', collapsed);
            }

            syncCollapseIcons();

            toggleButtons.forEach(function (button) {
                button.addEventListener('click', toggleSidebar);
            });

            if (overlay) {
                overlay.addEventListener('click', closeMobileSidebar);
            }

            window.addEventListener('resize', function () {
                if (!mq.matches) {
                    body.classList.remove('admin-sidebar-open');
                    const collapsed = localStorage.getItem(storageKey) === '1';
                    body.classList.toggle('admin-sidebar-collapsed', collapsed);
                } else {
                    body.classList.remove('admin-sidebar-collapsed');
                }

                syncCollapseIcons();
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
