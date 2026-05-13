@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap');

    :root{
        --primary: #1f3c88; /* indigo dark */
        --accent: #4c6ef5;  /* blue */
        --muted: #6b7280;
        --surface: #f7fbff;
        --border: rgba(76,110,245,0.12);
        --card: #ffffff;
    }

    .candidate-dashboard {
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #f8fbff 0%, #f2f6ff 100%);
        min-height: 100vh;
        padding: 2rem;
    }

    .candidate-container {
        max-width: 900px;
        margin: 0 auto;
    }

    /* Back button */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        background: var(--card);
        border-radius: 40px;
        transition: all 0.15s ease;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border);
    }

    .back-link:hover {
        background: var(--accent);
        color: white;
        transform: translateX(-4px);
        border-color: var(--accent);
    }

    /* Header Card */
    .candidate-header {
        background: var(--card);
        border-radius: 24px;
        padding: 1.15rem 1.2rem;
        margin-bottom: 1.6rem;
        box-shadow: 0 6px 18px rgba(31,60,136,0.06);
        border: 1px solid var(--border);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .candidate-header:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(31,60,136,0.08);
    }

    .candidate-header-body {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e8f0e8;
    }

    .header-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 240px;
        gap: 1rem;
        align-items: start;
    }

    .candidate-name {
        font-size: 1.7rem;
        font-weight: 800;
        color: #1a3b2e;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.02em;
    }

    .candidate-matricule {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #5f7b6b;
        font-size: 0.85rem;
        background: #f0f7f0;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
    }

    .candidate-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .candidate-meta-card {
        background: #f7faf7;
        border: 1px solid #e3eee3;
        border-radius: 16px;
        padding: 0.8rem 0.9rem;
        min-height: 74px;
    }

    .candidate-meta-value {
        margin-top: 0.25rem;
        font-size: 0.92rem;
        font-weight: 600;
        color: #1a3b2e;
        word-break: break-word;
    }

    /* Profile Card Premium */
    .profile-premium-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        border-radius: 20px;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(31,60,136,0.12);
    }

    .profile-premium-header {
        padding: 0.8rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }

    .profile-premium-icon {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 8px;
        display: inline-flex;
        align-items: center;
        backdrop-filter: blur(4px);
    }

    .profile-premium-badge {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(4px);
        padding: 4px 12px;
        border-radius: 40px;
        font-size: 11px;
        font-weight: 700;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .profile-premium-content {
        padding: 1rem;
        text-align: center;
    }

    .profile-premium-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0.5rem;
    }

    .profile-premium-value {
        font-size: 1.2rem;
        font-weight: 800;
        color: white;
        margin: 0;
        word-break: break-word;
    }

    /* Section Cards */
    .info-section {
        background: var(--card);
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(31,60,136,0.04);
        border: 1px solid var(--border);
        transition: box-shadow 0.15s ease;
    }

    .info-section:hover {
        box-shadow: 0 6px 18px rgba(31,60,136,0.06);
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1a3b2e;
        margin: 0 0 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e8f0e8;
    }

    .section-title svg {
        color: #2b8c5e;
    }

    /* Grid Layouts */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label {
        font-size: 11px;
        font-weight: 600;
        color: #6b8c7a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 0.95rem;
        font-weight: 500;
        color: #1a3b2e;
        word-break: break-word;
    }

    /* Tags */
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .tag-accent {
        background: linear-gradient(135deg, rgba(76,110,245,0.06) 0%, rgba(76,110,245,0.03) 100%);
        color: var(--primary);
    }

    .tag-accent:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(76,110,245,0.08);
    }

    .tag-gray {
        background: #f0f4f0;
        color: #2a4a3a;
    }

    /* Regional Choice Cards */
    .choice-card {
        border: 1px solid #e8f0e8;
        border-radius: 16px;
        padding: 1.25rem;
        background: #fafdfb;
        transition: all 0.2s ease;
    }

    .choice-card:hover {
        transform: translateX(4px);
        border-color: var(--accent);
        box-shadow: 0 6px 14px rgba(76,110,245,0.06);
    }

    .choice-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .choice-order {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #2b8c5e 0%, #1a5c3e 100%);
        color: white;
        padding: 0.25rem 0.8rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .choice-region {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a3b2e;
    }

    .motivations-list {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e8f0e8;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .motivation-tag {
        background: white;
        border: 1px solid #d4ecda;
        color: #2b8c5e;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Footer Meta */
    .meta-footer {
        background: linear-gradient(135deg, #f8fbff 0%, #f2f6ff 100%);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid var(--border);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .candidate-dashboard {
            padding: 1rem;
        }
        
        .header-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .candidate-name {
            font-size: 1.45rem;
        }

        .candidate-meta-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Panel scrollable central pour CV / sections longues */
    .scrollable-panel {
        max-height: calc(72vh - 120px);
        overflow: auto;
        padding-right: 0.75rem;
    }

    /* Ajustements pour la grille show */
    .show-grid {
        display:grid;
        grid-template-columns: 1fr 320px;
        gap:1.5rem;
        align-items:start;
    }

    .show-grid .main-column {
        max-width: 860px;
        margin: 0 auto;
    }

    @media (max-width: 900px) {
        .show-grid {
            grid-template-columns: 1fr !important;
        }

        .side-column {
            position: static;
        }

        .scrollable-panel {
            max-height: none;
            overflow: visible;
        }
    }
</style>

<div class="candidate-dashboard">
    <div class="candidate-container">
        <!-- Retour amélioré + navigation précédent/suivant -->
        <div style="display:flex; gap:0.5rem; align-items:center; margin-bottom:1.5rem;">
            <a href="{{ route('admin.candidates.index') }}" class="back-link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Retour à la liste des candidats
        </a>
            <div style="margin-left: auto; display:flex; gap:0.5rem;">
                @php
                    $query = request()->query();
                    if (isset($query['page'])) {
                        unset($query['page']);
                    }
                    $qs = http_build_query($query);
                    $qs = $qs ? ('?'.$qs) : '';
                @endphp
                @if(!empty($prevId))
                    <a href="{{ route('admin.candidates.show', ['user' => $prevId]) }}{{ $qs }}" class="back-link" style="background:#eef3ff; color:var(--primary);">
                        ← Précédent
                    </a>
                @else
                    <span class="back-link" style="opacity:0.45; pointer-events:none;">← Précédent</span>
                @endif

                @if(!empty($nextId))
                    <a href="{{ route('admin.candidates.show', ['user' => $nextId]) }}{{ $qs }}" class="back-link" style="background:#eef3ff; color:var(--primary);">
                        Suivant →
                    </a>
                @else
                    <span class="back-link" style="opacity:0.45; pointer-events:none;">Suivant →</span>
                @endif
            </div>
        </div>

        <!-- En-tête candidat amélioré -->
        <div class="candidate-header">
            <div class="header-grid">
                <div>
                    <h1 class="candidate-name">{{ $user->nom }} {{ $user->prenom }}</h1>
                    <div class="candidate-matricule">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="M8 12h8"/>
                        </svg>
                        Matricule: {{ $user->matricule }}
                    </div>
                </div>
                <div>
                    <!-- Profil redesigné plus classe -->
                    <div class="profile-premium-card">
                        <div class="profile-premium-header">
                            <div class="profile-premium-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div class="profile-premium-badge">Profil</div>
                        </div>
                        <div class="profile-premium-content">
                            <div class="profile-premium-label">Catégorie</div>
                            <div class="profile-premium-value">{{ $user->profil->libelle ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="candidate-header-body">
                @php
                    $dispoLabels = [
                        'immediate' => 'Immédiate',
                        'sous_7_jours' => 'Sous 7 jours',
                        'sous_15_jours' => 'Sous 15 jours',
                        'selon_calendrier' => 'Selon le calendrier'
                    ];
                @endphp

                <div class="candidate-meta-grid">
                    <div class="candidate-meta-card">
                        <div class="info-label">Email</div>
                        <div class="candidate-meta-value">{{ $user->email }}</div>
                    </div>
                    <div class="candidate-meta-card">
                        <div class="info-label">Téléphone</div>
                        <div class="candidate-meta-value">+221 {{ $user->telephone }}</div>
                    </div>
                    <div class="candidate-meta-card">
                        <div class="info-label">Ministère</div>
                        <div class="candidate-meta-value">{{ $user->ministere->nom ?? '—' }}</div>
                    </div>
                    <div class="candidate-meta-card">
                        <div class="info-label">Disponibilité</div>
                        <div class="candidate-meta-value">
                            <span class="tag tag-green" style="display: inline-flex; padding: 0.35rem 0.7rem; font-size: 0.75rem;">
                                {{ $dispoLabels[$user->disponibilite] ?? '—' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compétences -->
        <div class="info-section">
            <h2 class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
                Compétences & Expertises
            </h2>
            
            <div style="margin-bottom: 1.5rem;">
                <div class="info-label" style="margin-bottom: 0.75rem;">Niveau numérique</div>
                <span class="tag tag-green">
                    {{ ucfirst(str_replace('_', ' ', $user->niveau_numerique ?? '—')) }}
                </span>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div class="info-label" style="margin-bottom: 0.75rem;">Expériences</div>
                <div class="tags-container">
                    @forelse($experiences as $exp)
                        <span class="tag tag-green">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            {{ str_replace('_', ' ', ucfirst($exp)) }}
                        </span>
                    @empty
                        <span class="info-value">—</span>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="info-label" style="margin-bottom: 0.75rem;">Compétences techniques</div>
                <div class="tags-container">
                    @forelse($competencesTechniques as $comp)
                        <span class="tag tag-gray">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                                <polyline points="2 17 12 22 22 17"/>
                                <polyline points="2 12 12 17 22 12"/>
                            </svg>
                            {{ str_replace('_', ' ', ucfirst($comp)) }}
                        </span>
                    @empty
                        <span class="info-value">—</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Choix régionaux -->
        <div class="info-section">
            <h2 class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                Choix régionaux
            </h2>
            
            <div style="display: grid; gap: 1rem;">
                @forelse($regionalChoices as $choice)
                    <div class="choice-card">
                        <div class="choice-header">
                            <div>
                                <span class="choice-order">Choix {{ $choice->ordre }}</span>
                                <span class="choice-region" style="margin-left: 0.75rem;">{{ $choice->region->nom }}</span>
                            </div>
                        </div>
                        
                        @if($choice->motivations->count() > 0)
                            <div class="motivations-list">
                                @foreach($choice->motivations as $mot)
                                    <span class="motivation-tag">
                                        {{ $mot->motivation->libelle ?? $mot->motivation_libre }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="info-value" style="text-align: center; padding: 2rem;">Aucun choix régional enregistré</div>
                @endforelse
            </div>
        </div>

        <!-- Métadonnées -->
        <div class="meta-footer">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div>
                    <div class="info-label">Inscrit le</div>
                    <div class="info-value">{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                </div>
                <div>
                    <div class="info-label">Dernière mise à jour</div>
                    <div class="info-value">{{ $user->updated_at->format('d/m/Y à H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection