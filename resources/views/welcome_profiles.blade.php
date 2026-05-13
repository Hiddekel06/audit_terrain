@extends('layouts.app')

@section('body-bg', '#ffffff')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap');

    :root {
        --primary-dark: #0f172a;
        --primary-mid: #1e293b;
        --primary-light: #f8fafc;
        --accent-blue: #2563eb;
        --accent-green: #0f766e;
        --accent-orange: #b45309;
        --accent-purple: #4f46e5;
        --gray-soft: #64748b;
        --border-light: rgba(15, 23, 42, 0.08);
        --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.06);
        --shadow-md: 0 8px 24px rgba(15, 23, 42, 0.08);
        --shadow-lg: 0 16px 40px rgba(15, 23, 42, 0.12);
        --shadow-xl: 0 24px 60px rgba(15, 23, 42, 0.16);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
        color: var(--primary-mid);
    }

    .audit-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 3rem 2rem 5rem;
    }

    .audit-header {
        margin-bottom: 2.5rem;
        padding: 2.25rem 2rem;
        border: 1px solid rgba(37, 99, 235, 0.14);
        border-radius: 1.4rem;
        background:
            radial-gradient(circle at 5% 0%, rgba(37, 99, 235, 0.11), transparent 38%),
            radial-gradient(circle at 100% 100%, rgba(15, 118, 110, 0.09), transparent 45%),
            rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(10px);
        box-shadow: 0 16px 35px -22px rgba(15, 23, 42, 0.35);
        position: relative;
        overflow: hidden;
    }

    .audit-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #00853f 0%, #fdef42 50%, #e31b23 100%);
    }

    .audit-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #e0f2fe;
        color: #075985;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.45rem 0.8rem;
        border-radius: 9999px;
        margin-bottom: 1rem;
    }

    .audit-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        color: var(--primary-dark);
        margin-bottom: 0.8rem;
        letter-spacing: -0.03em;
        font-family: 'Space Grotesk', 'Plus Jakarta Sans', sans-serif;
        line-height: 1.05;
        text-wrap: balance;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.45);
    }

    .audit-subtitle {
        color: var(--gray-soft);
        font-size: 1.04rem;
        line-height: 1.75;
        max-width: 800px;
        font-weight: 500;
        text-wrap: pretty;
    }

    .profiles-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .profile-card {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid var(--border-light);
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        backdrop-filter: blur(10px);
    }

    .profile-card:hover {
        box-shadow: var(--shadow-lg);
        border-color: rgba(37, 99, 235, 0.18);
        transform: translateY(-4px);
    }

    .card-icon {
        width: 100%;
        padding: 1.5rem 1.5rem 0.5rem;
    }

    .card-icon span {
        width: 52px;
        height: 52px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: var(--shadow-sm);
    }

    .icon-chef span { background: #dbeafe; color: var(--accent-blue); }
    .icon-auditeur span { background: #d1fae5; color: var(--accent-green); }
    .icon-support span { background: #fef3c7; color: var(--accent-orange); }
    .icon-superviseur span { background: #e0e7ff; color: var(--accent-purple); }

    .card-content {
        padding: 0 1.5rem 1rem;
         overflow: hidden;
    }
    
    .card-content h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 0.5rem;
         overflow: hidden;
    }

    .card-mission {
        font-size: 0.9rem;
        color: var(--gray-soft);
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .card-preview {
        list-style: none;
        margin: 0 0 1rem 0;
        padding: 0;
    }

    .card-preview li {
        font-size: 0.88rem;
        color: var(--primary-mid);
        padding: 0.35rem 0;
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }

    .card-preview li i {
        font-size: 0.72rem;
        color: var(--gray-soft);
    }

    .btn-details {
        width: 100%;
        padding: 0.9rem;
        background: var(--primary-light);
        border: none;
        border-top: 1px solid var(--border-light);
        color: var(--primary-mid);
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-details:hover {
        background: #e2e8f0;
        color: var(--primary-dark);
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        inset: 0;
        background: rgba(15, 23, 42, 0.58);
        backdrop-filter: blur(8px);
        justify-content: center;
        align-items: center;
        padding: 1rem;
    }

    .modal-content {
        background: rgba(255, 255, 255, 0.96);
        max-width: 700px;
        width: 100%;
        max-height: 85vh;
        border-radius: 1.25rem;
        box-shadow: var(--shadow-xl);
        animation: modalFadeIn 0.2s ease;
        overflow-y: auto;
        border: 1px solid rgba(255,255,255,0.7);
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.96);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-header {
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        gap: 1rem;
        position: sticky;
        top: 0;
        background: rgba(255,255,255,0.98);
        backdrop-filter: blur(8px);
        z-index: 10;
    }

    .modal-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }

    .modal-title h2 {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--primary-dark);
        margin-bottom: 0.25rem;
    }

    .modal-title p {
        font-size: 0.9rem;
        color: var(--gray-soft);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .detail-section {
        margin-bottom: 1.5rem;
    }

    .detail-section h4 {
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--primary-mid);
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .detail-list li {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 0;
        border-bottom: 1px solid var(--border-light);
        font-size: 0.92rem;
        color: var(--primary-mid);
    }

    .detail-list li:last-child {
        border-bottom: none;
    }

    .detail-list li i {
        width: 24px;
        color: var(--gray-soft);
    }

    .badge-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.35rem 0.8rem;
        border-radius: 9999px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid rgba(29, 78, 216, 0.12);
    }

    .modal-footer {
        padding: 1rem 1.5rem 1.5rem;
        border-top: 1px solid var(--border-light);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        position: sticky;
        bottom: 0;
        background: rgba(255,255,255,0.98);
        backdrop-filter: blur(8px);
    }

    .btn-close-modal,
    .btn-continue,
    .btn-cta {
        border-radius: 0.8rem;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-close-modal {
        background: white;
        color: var(--primary-mid);
        border: 1px solid var(--border-light);
        padding: 0.7rem 1.25rem;
    }

    .btn-close-modal:hover {
        background: var(--primary-light);
    }

    .btn-select-profile {
        background: #00853f;
        color: #fff;
        border: none;
        border-radius: 0.8rem;
        padding: 0.7rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-width: 190px;
    }

    .btn-select-profile:hover,
    .btn-select-profile:focus {
        background: #065f46;
        color: #fff;
    }

    .btn-continue,
    .btn-cta {
        background: var(--primary-dark);
        color: white;
        border: none;
        padding: 0.8rem 1.35rem;
        box-shadow: var(--shadow-sm);
        text-decoration: none;
    }

    .btn-continue:hover,
    .btn-cta:hover {
        background: #1e293b;
        transform: translateY(-1px);
    }

    .action-section {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
    }

    .btn-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        background: #00853f;
        color: #fff;
        border: none;
        font-weight: 700;
        font-size: 1.05rem;
        letter-spacing: 0.01em;
        box-shadow: 0 4px 16px -8px rgba(15,23,42,0.13);
        padding: 0.9rem 2.1rem;
        transition: background 0.18s, color 0.18s, box-shadow 0.18s, transform 0.18s;
        outline: none;
    }

    .btn-cta:hover, .btn-cta:focus {
        background: #065f46;
        color: #fff;
        box-shadow: 0 8px 24px -10px rgba(15,23,42,0.18);
        transform: translateY(-2px) scale(1.03);
    }

    @media (max-width: 1024px) {
        .profiles-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .audit-container {
            padding: 1.25rem 1rem 3rem;
        }

        .audit-header {
            padding: 1.25rem;
        }

        .audit-title {
            font-size: clamp(1.6rem, 7.4vw, 2.1rem);
        }

        .audit-subtitle {
            font-size: 0.96rem;
            line-height: 1.6;
        }

        .profiles-grid {
            grid-template-columns: 1fr;
        }

        .modal-footer {
            flex-direction: column;
        }

        .btn-close-modal,
        .btn-select-profile,
        .btn-continue,
        .btn-cta {
            width: 100%;
            text-align: center;
        }

        .modal-header {
            align-items: flex-start;
        }
    }
</style>

<div class="audit-container">
    <div style="width:100%;margin-bottom:1.1rem;">
        <div style="width:180px;height:4px;display:flex;align-items:center;justify-content:center;position:relative;margin:0 auto 0.5rem auto;">
            <div style="flex:1;background:#00853f;height:100%;border-radius:2px 0 0 2px;"></div>
            <div style="flex:1;background:#fdef42;height:100%;display:flex;align-items:center;justify-content:center;position:relative;">
                <svg width="16" height="16" viewBox="0 0 28 28" style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);z-index:2;" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="14,4 16.09,11.26 23.82,11.26 17.91,15.74 20,23 14,18.52 8,23 10.09,15.74 4.18,11.26 11.91,11.26" fill="#00853f"/>
                </svg>
            </div>
            <div style="flex:1;background:#e31b23;height:100%;border-radius:0 2px 2px 0;"></div>
        </div>
        <div style="text-align:center;margin-top:1.2rem;margin-bottom:0.7rem;">
            <h1 style="font-family:Arial, Helvetica, sans-serif;font-size:clamp(2.1rem,4vw,2.7rem);font-weight:700;color:#222;letter-spacing:-0.03em;margin-bottom:0.5rem;">Audit Physique &amp; Biométrique</h1>
            <div style="font-size:1.13rem;color:#222;font-weight:500;max-width:700px;margin:0 auto;line-height:1.7;">
                Appel à manifestation d’intérêt pour la constitution des équipes chargées de l'audit physique et biométrique.
            </div>
             <div style="font-size:1rem;color:#1e3a8a;font-weight:500;max-width:600px;margin:12px auto 0;line-height:1.6;text-align:center;background:#f5f7fa;padding:12px 18px;border-radius:8px;">
               Veuillez choisir le profil qui correspond le mieux à votre expérience et à vos compétences.
            </div>

        </div>
    </div>

    <div class="profiles-grid">
        <div class="profile-card">
            <div class="card-icon icon-chef">
                <span><i class="bi bi-people-fill"></i></span>
            </div>
            <div class="card-content">
                <h3>Chef d'équipe</h3>
                <div class="card-mission">Coordination globale de l'équipe d'audit</div>
                <ul class="card-preview">
                    <li><i class="bi bi-diagram-3"></i> Organisation des opérations</li>
                    <li><i class="bi bi-building"></i> Interface avec autorités</li>
                    <li><i class="bi bi-database-check"></i> Validation des données</li>
                </ul>
            </div>
            <button class="btn-details" onclick="openModal('chef', 1)">
                <i class="bi bi-eye"></i> Voir les détails
            </button>
        </div>

        <div class="profile-card">
            <div class="card-icon icon-auditeur">
                <span><i class="bi bi-person-badge"></i></span>
            </div>
            <div class="card-content">
                <h3>Auditeur</h3>
                <div class="card-mission">Réaliser l'audit et garantir la fiabilité</div>
                <ul class="card-preview">
                    <li><i class="bi bi-id-card"></i> Vérification identité</li>
                    <li><i class="bi bi-calendar-check"></i> Contrôle présence</li>
                    <li><i class="bi bi-fingerprint"></i> Enrôlement biométrique</li>
                </ul>
            </div>
            <button class="btn-details" onclick="openModal('auditeur', 2)">
                <i class="bi bi-eye"></i> Voir les détails
            </button>
        </div>

        <div class="profile-card">
            <div class="card-icon icon-support">
                <span><i class="bi bi-tools"></i></span>
            </div>
            <div class="card-content">
                <h3>Support Technique</h3>
                <div class="card-mission">Fiabilité technique et qualité des données</div>
                <ul class="card-preview">
                    <li><i class="bi bi-gear"></i> Configuration équipements</li>
                    <li><i class="bi bi-headset"></i> Assistance aux auditeurs</li>
                    <li><i class="bi bi-fingerprint"></i> Contrôle qualité biométrique</li>
                </ul>
            </div>
            <button class="btn-details" onclick="openModal('support', 3)">
                <i class="bi bi-eye"></i> Voir les détails
            </button>
        </div>

    </div>

    <!--  <div class="action-section">
        <button type="button" class="btn-cta" id="openEngagementModal">
            <i class="bi bi-check2-circle"></i> Faire mon choix
        </button>
    </div> -->
</div>

<div id="engagementModal" class="modal">
    <div class="modal-content" style="max-width: 520px;">
        <div class="modal-header" style="justify-content: center;">
            <div style="text-align: center;">
                <div style="width: 80px; height: 52px; margin: 0 auto 1rem; background: linear-gradient(90deg, #00853f 0% 33.33%, #fdef42 33.33% 66.66%, #e31b23 66.66% 100%); border-radius: 6px; position: relative; box-shadow: var(--shadow-sm);">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1rem;">★</div>
                </div>
                <h3 style="font-size: 1.15rem; color: var(--primary-dark); margin-bottom: 0.25rem;">Engagement Administratif</h3>
                <p style="font-size: 0.8rem; color: var(--gray-soft);">République du Sénégal – Mission d'État</p>
            </div>
        </div>
        <div class="modal-body">
            <p style="font-size: 0.98rem; line-height: 1.8; color: var(--primary-mid);">
                L'audit physique et biométrique constitue une opération importante de l’État, visant à garantir la fiabilité des données et la bonne gestion des ressources publiques.
                Elle s’inscrit dans une démarche de transparence et d’efficacité de l’Administration.
                À ce titre, elle requiert de la part de chacun sérieux, rigueur et sincérité dans les informations fournies.
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn-close-modal" onclick="closeEngagementModal()">Refuser</button>
            <a href="{{ route('utilisateur.form') }}" class="btn-continue" id="continueFormLink" data-form-url="{{ route('utilisateur.form') }}">J'accepte et je continue</a>
        </div>
    </div>
</div>

<div id="detailModal" class="modal">
    <div class="modal-content" id="detailModalContent">
    </div>
</div>

<script>
    const profiles = {
        chef: {
            id: 1,
            icon: 'bi-people-fill',
            iconBg: '#dbeafe',
            iconColor: '#2563eb',
            title: 'Chef d\'équipe',
            mission: 'Assurer la coordination globale de l\'équipe d\'audit sur site.',
            activities: [
                'Organisation des opérations',
                'Interface avec autorités locales',
                'Validation des données',
                'Gestion des incidents'
            ],
            skills: [
                'Management',
                'Connaissance du processus d\'audit',
                'Suivi des indicateurs'
            ],
            itSkills: [
                'Suivi synchronisation',
                'Lecture tableaux de bord'
            ],
            badges: ['Coordination', 'Management', 'Indicateurs']
        },
        auditeur: {
            id: 2,
            icon: 'bi-person-badge',
            iconBg: '#d1fae5',
            iconColor: '#0f766e',
            title: 'Auditeur',
            mission: 'Réaliser l\'audit et garantir la fiabilité des données.',
            activities: [
                'Vérification identité',
                'Contrôle présence',
                'Saisie et correction',
                'Enrôlement biométrique',
                'Signalement anomalies'
            ],
            skills: [
                'Rigueur administrative',
                'Utilisation tablette',
                'Manipulation basique du kit biométrique'
            ],
            badges: ['Collecte terrain', 'Fiabilité', 'Rigueur']
        },
        support: {
            id: 3,
            icon: 'bi-tools',
            iconBg: '#fef3c7',
            iconColor: '#b45309',
            title: 'Auditeur – Support Technique',
            mission: 'Garantir la fiabilité technique et la qualité des données.',
            activities: [
                'Configuration équipements',
                'Assistance aux auditeurs',
                'Contrôle qualité biométrique',
                'Gestion incidents techniques',
                'Synchronisation des données'
            ],
            technicalSkills: [
                'Android / tablettes',
                'Réseaux (4G, partage connexion)',
                'Biométrie (empreintes, photo)',
                'Synchronisation données'
            ],
            security: [
                'Protection des données',
                'Gestion accès',
                'Respect des normes'
            ],
            indicators: [
                'Taux de synchronisation',
                'Incidents résolus',
                'Qualité biométrique'
            ],
            badges: ['Support IT', 'Sécurité', 'Qualité']
        },
        superviseur: {
            icon: 'bi-diagram-3',
            iconBg: '#e0e7ff',
            iconColor: '#4f46e5',
            title: 'Superviseur Technique Régional',
            mission: 'Superviser plusieurs équipes terrain.',
            activities: [
                'Support technique avancé',
                'Gestion incidents majeurs',
                'Coordination avec le QG technique'
            ],
            skills: [
                'Supervision régionale',
                'Infrastructure technique',
                'Management d\'équipes terrain',
                'Résolution de crises techniques'
            ],
            badges: ['Régional', 'Coordination', 'Expertise senior']
        }
    };

    let selectedProfileId = null;

    function updateContinueLink() {
        const continueLink = document.getElementById('continueFormLink');
        if (!continueLink) {
            return;
        }

        const baseUrl = continueLink.dataset.formUrl || continueLink.href;
        if (!selectedProfileId) {
            continueLink.href = baseUrl;
            return;
        }

        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('profil_id', String(selectedProfileId));
        continueLink.href = url.toString();
    }

    function openModal(profileKey, profileId) {
        const p = profiles[profileKey];
        if (!p) return;

        selectedProfileId = profileId || p.id || null;
        updateContinueLink();

        const modalContent = document.getElementById('detailModalContent');

        let html = `
            <div class="modal-header">
                <div class="modal-icon" style="background: ${p.iconBg}; color: ${p.iconColor};">
                    <i class="bi ${p.icon}"></i>
                </div>
                <div class="modal-title">
                    <h2>${p.title}</h2>
                    <p>${p.mission}</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="detail-section">
                    <h4><i class="bi bi-check-circle"></i> Activités</h4>
                    <ul class="detail-list">
                        ${p.activities.map(a => `<li><i class="bi bi-caret-right-fill"></i> ${a}</li>`).join('')}
                    </ul>
                </div>
        `;

        if (p.skills) {
            html += `
                <div class="detail-section">
                    <h4><i class="bi bi-stars"></i> Compétences</h4>
                    <ul class="detail-list">
                        ${p.skills.map(s => `<li><i class="bi bi-caret-right-fill"></i> ${s}</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        if (p.itSkills) {
            html += `
                <div class="detail-section">
                    <h4><i class="bi bi-laptop"></i> Compétences IT</h4>
                    <ul class="detail-list">
                        ${p.itSkills.map(s => `<li><i class="bi bi-caret-right-fill"></i> ${s}</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        if (p.technicalSkills) {
            html += `
                <div class="detail-section">
                    <h4><i class="bi bi-gear"></i> Compétences techniques</h4>
                    <ul class="detail-list">
                        ${p.technicalSkills.map(s => `<li><i class="bi bi-caret-right-fill"></i> ${s}</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        if (p.security) {
            html += `
                <div class="detail-section">
                    <h4><i class="bi bi-shield-lock"></i> Sécurité</h4>
                    <ul class="detail-list">
                        ${p.security.map(s => `<li><i class="bi bi-caret-right-fill"></i> ${s}</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        if (p.indicators) {
            html += `
                <div class="detail-section">
                    <h4><i class="bi bi-graph-up"></i> Indicateurs</h4>
                    <ul class="detail-list">
                        ${p.indicators.map(i => `<li><i class="bi bi-caret-right-fill"></i> ${i}</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        html += `
                <div class="detail-section">
                    <h4><i class="bi bi-tags"></i> Tags</h4>
                    <div class="badge-group">
                        ${p.badges.map(b => `<span class="badge"><i class="bi bi-tag"></i> ${b}</span>`).join('')}
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="justify-content: space-between;">
                <button class="btn-select-profile" onclick="closeDetailModalAndOpenEngagement()">
                    <i class="bi bi-check2-circle"></i> Sélectionner ce profil
                </button>
                <button class="btn-close-modal" onclick="closeDetailModal()">Fermer</button>
            </div>
        `;

        modalContent.innerHTML = html;
        document.getElementById('detailModal').style.display = 'flex';
    }

    function closeDetailModal() {
        document.getElementById('detailModal').style.display = 'none';
    }

    function closeEngagementModal() {
        document.getElementById('engagementModal').style.display = 'none';
    }

    function openEngagementModal() {
        updateContinueLink();
        document.getElementById('engagementModal').style.display = 'flex';
    }

    function closeDetailModalAndOpenEngagement() {
        closeDetailModal();
        openEngagementModal();
    }

    document.getElementById('openEngagementModal')?.addEventListener('click', openEngagementModal);

    window.onclick = function(event) {
        const detailModal = document.getElementById('detailModal');
        const engagementModal = document.getElementById('engagementModal');

        if (event.target === detailModal) closeDetailModal();
        if (event.target === engagementModal) closeEngagementModal();
    }
</script>
@endsection