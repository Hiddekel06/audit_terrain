@extends('layouts.admin')

@section('admin-title', 'Détail du candidat')
@section('admin-subtitle', 'Consultez les informations détaillées du profil')

@section('content')
@php
    $formatSenegalPhone = function ($value) {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

        if ($digits === '') {
            return '—';
        }

        if (strlen($digits) > 9) {
            $digits = substr($digits, -9);
        }

        if (strlen($digits) === 9) {
            return '+221 ' . preg_replace('/(\d{2})(\d{3})(\d{2})(\d{2})/', '$1 $2 $3 $4', $digits);
        }

        return '+221 ' . $digits;
    };
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap');

    :root{
        --primary: #065f46; /* emerald-800 */
        --accent: #059669;  /* emerald-600 */
        --muted: #64748b;
        --surface: #f8fafc;
        --border: rgba(5, 150, 105, 0.12);
        --card: #ffffff;
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
        padding: 0.5rem 1.25rem;
        background: var(--card);
        border-radius: 0.75rem;
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border);
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .back-link:hover {
        background: #ecfdf5;
        color: var(--accent);
        transform: translateY(-1px);
        border-color: var(--accent);
    }

    /* Header Card */
    .candidate-header {
        background: var(--card);
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-bottom: 1.6rem;
        box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);
        border: 1px solid var(--border);
        transition: transform 0.2s ease;
    }

    .candidate-header-body {
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid #f1f5f9;
    }

    .header-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 260px;
        gap: 1.5rem;
        align-items: start;
    }

    .candidate-name {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.75rem 0;
        letter-spacing: -0.025em;
    }

    .candidate-matricule {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #475569;
        font-size: 0.875rem;
        background: #f1f5f9;
        padding: 0.4rem 0.9rem;
        border-radius: 0.75rem;
        font-weight: 600;
    }

    .candidate-source {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-left: 0.5rem;
        padding: 0.35rem 0.8rem;
        border-radius: 0.5rem;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .candidate-source.manual {
        background: #f1f5f9;
        color: #475569;
    }

    .candidate-source.import {
        background: #ecfdf5;
        color: #059669;
    }

    .candidate-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .candidate-meta-card {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 1rem;
        padding: 1rem;
    }

    .candidate-meta-value {
        margin-top: 0.35rem;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        word-break: break-word;
    }

    /* Profile Card Premium Styles */
    .profile-premium-card {
        border-radius: 1.15rem;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 8px 24px -4px rgba(0,0,0,0.1);
    }

    /* Thème Chef d'équipe (Amber/Gold) */
    .theme-chef {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    /* Thème Auditeur (Blue/Indigo) */
    .theme-auditeur {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }
    
    /* Thème Support (Emerald/Teal) */
    .theme-support {
        background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    }

    /* Thème Par défaut */
    .theme-default {
        background: linear-gradient(135deg, #64748b 0%, #334155 100%);
    }

    .profile-premium-header {
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(4px);
    }

    .profile-premium-icon {
        background: rgba(255, 255, 255, 0.25);
        border-radius: 0.5rem;
        padding: 6px;
        display: inline-flex;
        align-items: center;
    }

    .profile-premium-badge {
        font-size: 10px;
        font-weight: 800;
        color: white;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .profile-premium-content {
        padding: 1.5rem 1rem;
        text-align: center;
    }

    .profile-premium-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .profile-premium-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: white;
        margin: 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Section Cards */
    .info-section {
        background: var(--card);
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);
        border: 1px solid var(--border);
    }

    .section-title {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .section-title svg {
        color: var(--accent);
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
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
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
        border-radius: 0.75rem;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .tag-green {
        background: #ecfdf5;
        color: #059669;
    }

    .tag-gray {
        background: #f1f5f9;
        color: #475569;
    }

    /* Regional Choice Cards */
    .choice-card {
        border: 1px solid #f1f5f9;
        border-radius: 1rem;
        padding: 1.25rem;
        background: #f8fafc;
        transition: all 0.2s ease;
    }

    .choice-card:hover {
        border-color: var(--accent);
        background: #ecfdf5;
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
        background: #065f46;
        color: white;
        padding: 0.35rem 0.9rem;
        border-radius: 0.5rem;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .choice-region {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .motivations-list {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .motivation-tag {
        background: white;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 0.35rem 0.8rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Footer Meta */
    .meta-footer {
        background: #f8fafc;
        border-radius: 1rem;
        padding: 1.25rem;
        border: 1px solid #f1f5f9;
    }

    /* Responsive */
    @media (max-width: 768px) {
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
</style>

<div class="candidate-container">
    <!-- Retour amélioré + navigation précédent/suivant -->
    <div style="display:flex; gap:0.5rem; align-items:center; margin-bottom:1.5rem;">
        <a href="{{ route('admin.candidates.index', request()->query()) }}" class="back-link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Retour à la liste
        </a>
        <div style="margin-left: auto; display:flex; gap:0.5rem;">
            @if(!empty($prevId))
                <a href="{{ route('admin.candidates.show', array_merge(['user' => $prevId], request()->query())) }}" class="back-link" style="background:#eef3ff; color:var(--primary);">
                    ← Précédent
                </a>
            @else
                <span class="back-link" style="opacity:0.45; pointer-events:none;">← Précédent</span>
            @endif

            @if(!empty($nextId))
                <a href="{{ route('admin.candidates.show', array_merge(['user' => $nextId], request()->query())) }}" class="back-link" style="background:#eef3ff; color:var(--primary);">
                    Suivant →
                </a>
            @else
                <span class="back-link" style="opacity:0.45; pointer-events:none;">Suivant →</span>
            @endif
            @if(Auth::guard('admin')->user()?->role === 'super_admin')
                <!-- Formulaire de suppression du candidat -->
                <form method="POST" action="{{ route('admin.candidates.destroy', $user) }}" onsubmit="return confirm('Confirmer la suppression de ce candidat ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="back-link" style="background:#ffecec; color:#9b1c1c; border-color: rgba(155,28,28,0.12);">
                        Supprimer
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- En-tête candidat -->
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
                    <span class="candidate-source {{ ($user->source_type ?? 'manual') === 'import' ? 'import' : 'manual' }}">
                        {{ ($user->source_type ?? 'manual') === 'import' ? 'Importé' : 'Inscrit' }}
                    </span>
                    
                    @if($user->validation_status === 'officiel_inscrit')
                        <span class="candidate-source" style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
                            <i class="bi bi-patch-check-fill me-1"></i> Officiel
                        </span>
                    @elseif($user->validation_status === 'officiel_attente')
                        <span class="candidate-source" style="background: #fef9c3; color: #a16207; border: 1px solid #fef08a;">
                            <i class="bi bi-clock-history me-1"></i> Officiel (Attente)
                        </span>
                    @elseif($user->validation_status === 'reserve')
                        <span class="candidate-source" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">
                            Réserve
                        </span>
                    @endif
                </div>
            </div>
            <div style="text-align: right;">
                <button type="button" class="back-link" data-bs-toggle="modal" data-bs-target="#editAgentModal" style="background:#f0f7ff; color:#2563eb; border-color: rgba(37,99,235,0.12); cursor: pointer;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Modifier l'agent
                </button>
                <div class="profile-premium-card {{ $themeClass ?? 'theme-default' }}" style="margin-top: 1rem;">
                    <div class="profile-premium-header">
                        <div class="profile-premium-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="profile-premium-badge">Profil vérifié</div>
                    </div>
                    <div class="profile-premium-content">
                        <div class="profile-premium-label">Catégorie</div>
                        <div class="profile-premium-value">{{ $user->profil->libelle ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="candidate-header-body">
            <div class="candidate-meta-grid">
                <div class="candidate-meta-card">
                    <div class="info-label">Email</div>
                    <div class="candidate-meta-value">{{ $user->email }}</div>
                </div>
                <div class="candidate-meta-card">
                    <div class="info-label">Téléphone</div>
                    <div class="candidate-meta-value">{{ $formatSenegalPhone($user->telephone) }}</div>
                </div>
                <div class="candidate-meta-card">
                    <div class="info-label">Ministère</div>
                    <div class="candidate-meta-value">{{ $user->ministere->nom ?? '—' }}</div>
                </div>
                <div class="candidate-meta-card">
                    <div class="info-label">Direction</div>
                    <div class="candidate-meta-value">{{ $user->direction ?? '—' }}</div>
                </div>
                    <div class="candidate-meta-card">
                        <div class="info-label">Métier</div>
                        <div class="candidate-meta-value">{{ $user->metier ?? '—' }}</div>
                    </div>
                <div class="candidate-meta-card">
                    <div class="info-label">Disponibilité</div>
                    <div class="candidate-meta-value">
                        <span class="tag tag-green" style="display: inline-flex; padding: 0.35rem 0.7rem; font-size: 0.75rem;">
                            {{ ['immediate' => 'Immédiate', 'sous_7_jours' => 'Sous 7 jours', 'sous_15_jours' => 'Sous 15 jours', 'selon_calendrier' => 'Selon le calendrier'][$user->disponibilite] ?? '—' }}
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
                {{ $user->niveau_numerique ? ucfirst(str_replace('_', ' ', $user->niveau_numerique)) : '—' }}
            </span>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <div class="info-label" style="margin-bottom: 0.75rem;">Expériences</div>
            <div class="tags-container">
                @forelse($experiences as $exp)
                    <span class="tag tag-green">
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
            Affectation
        </h2>
        <div style="display: grid; gap: 1rem;">
            @if($user->team)
                <div class="choice-card" style="border-color: #2b8c5e; background: #f0f7f0;">
                    <div class="choice-header">
                        <div>
                            <span class="choice-order" style="background: #2b8c5e;">Équipe</span>
                            <span class="choice-region" style="margin-left: 0.75rem;">{{ $user->team->nom }}</span>
                        </div>
                    </div>
                </div>
            @endif

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
                @if(!empty($user->ready_to_deploy_all_regions))
                    <div class="choice-card" style="border-color: #4c6ef5; background: #f0f3ff;">
                        <div class="choice-header">
                            <div>
                                <span class="choice-order" style="background: #4c6ef5;">National</span>
                                <span class="choice-region" style="margin-left: 0.75rem;">Prêt pour toutes les régions</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="info-value" style="text-align: center; padding: 2rem;">Aucun choix régional enregistré</div>
                @endif
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

<!-- Modal de modification -->
<div class="modal fade" id="editAgentModal" tabindex="-1" aria-labelledby="editAgentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 1.25rem; border: none; shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 1.5rem;">
                <h5 class="modal-title fw-bold" id="editAgentModalLabel">Modifier les informations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.candidates.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding: 1.5rem;">
                    <div class="mb-4">
                        <label for="matricule" class="info-label" style="display: block; margin-bottom: 0.5rem;">Matricule</label>
                        <input type="text" name="matricule" id="matricule" class="form-control" 
                               value="{{ $user->matricule }}" placeholder="123456A" 
                               style="border-radius: 0.75rem; font-weight: 600; text-transform: uppercase;">
                        <small class="text-muted">Format: 6 chiffres + 1 lettre majuscule.</small>
                    </div>

                    <div class="mb-4">
                        <label for="telephone" class="info-label" style="display: block; margin-bottom: 0.5rem;">Numéro de téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.75rem 0 0 0.75rem;">+221</span>
                            <input type="text" name="telephone" id="telephone" class="form-control border-start-0" 
                                   value="{{ $user->telephone }}" placeholder="771234567" 
                                   style="border-radius: 0 0.75rem 0.75rem 0; font-weight: 600;">
                        </div>
                        <small class="text-muted">9 chiffres sans indicatif.</small>
                    </div>

                    <div class="mb-2">
                        <label class="info-label" style="display: block; margin-bottom: 0.5rem;">Statut de validation</label>
                        <div class="d-grid gap-2">
                            <input type="radio" class="btn-check" name="validation_status" id="status_reserve" value="reserve" {{ $user->validation_status === 'reserve' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary text-start p-3" for="status_reserve" style="border-radius: 0.75rem;">
                                <div class="fw-bold">Réserve</div>
                                <div style="font-size: 0.75rem; opacity: 0.8;">Candidat non retenu sur la liste officielle.</div>
                            </label>

                            <input type="radio" class="btn-check" name="validation_status" id="status_officiel" value="officiel" {{ str_contains($user->validation_status ?? '', 'officiel') ? 'checked' : '' }}>
                            <label class="btn btn-outline-success text-start p-3" for="status_officiel" style="border-radius: 0.75rem;">
                                <div class="fw-bold">Officiel</div>
                                <div style="font-size: 0.75rem; opacity: 0.8;">Inscrire l'agent sur la liste maître officielle.</div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 1.25rem;">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 0.75rem; padding: 0.6rem 1.25rem;">Annuler</button>
                    <button type="submit" class="btn btn-primary fw-bold" style="border-radius: 0.75rem; padding: 0.6rem 1.25rem; background: var(--primary); border: none;">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
