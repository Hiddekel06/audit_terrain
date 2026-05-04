<header class="py-2 py-md-3 mb-4" style="background-color: #ffffff; border-bottom: 2px solid #e9f5e9;">
    <div class="container">
        <!-- Barre principale avec hamburger sur mobile -->
        <div class="d-flex align-items-center justify-content-between">
            
            <!-- Logo + texte ministère -->
            <div class="d-flex align-items-center gap-2" style="flex: 1; min-width: 0;">
                <img src="/images/MFPremove.png" alt="Logo Ministère" style="height:40px; width:auto; display:block; background:transparent;">
                <div class="ms-1 ms-sm-2" style="line-height:1.2;">
                    <span style="font-size:0.8rem; font-size:clamp(0.75rem, 3vw, 1rem); font-weight:700; color:#222;">
                        Ministère de la Fonction Publique du Travail<br>et de la Réforme du Service Public
                    </span>
                </div>
            </div>
            
            <!-- Bouton hamburger (visible sur mobile) -->
            <button class="btn btn-link d-md-none p-2" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu" aria-expanded="false" aria-label="Menu">
                <i class="bi bi-list" style="font-size: 1.8rem; color: #2c5e2e;"></i>
            </button>
            
            <!-- Contenu desktop (visible sur tablette/desktop) -->
            <div class="d-none d-md-flex align-items-center gap-3">
                <h1 class="h5 mb-0 fw-semibold" style="color: #2c5e2e; letter-spacing: -0.3px;">Plateforme Audit</h1>
                <img src="/images/auditlogo.png" alt="Logo Audit" style="height:40px; width:auto; display:block;">
            </div>
        </div>
        
        <!-- Menu mobile collapse -->
        <div class="collapse d-md-none mt-3" id="mobileMenu">
            <div class="d-flex flex-column align-items-center gap-2 pt-2 border-top" style="border-color: #e9f5e9 !important;">
                <h1 class="h6 mb-0 fw-semibold" style="color: #2c5e2e;">Plateforme Audit</h1>
                <img src="/images/auditlogo.png" alt="Logo Audit" style="height:35px; width:auto;">
            </div>
        </div>
    </div>
</header>

<!-- Bootstrap Icons & JS (pour le collapse) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>