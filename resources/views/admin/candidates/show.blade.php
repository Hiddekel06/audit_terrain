@extends('layouts.app')

@section('content')
<div style="padding: 2rem; background: #f7f8fa; min-height: 100vh;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <!-- Retour -->
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('admin.candidates.index') }}" style="color: #4a8c5c; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                ← Retour à la liste
            </a>
        </div>

        <!-- En-tête candidat -->
        <div style="background: white; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; border: 1px solid #dce8dc;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <h1 style="font-size: 1.8rem; font-weight: 700; color: #1a2e1a; margin: 0 0 0.5rem;">
                        {{ $user->nom }} {{ $user->prenom }}
                    </h1>
                    <p style="color: #5a6e5a; margin: 0; font-size: 14px;">
                        Matricule: <span style="font-weight: 600;">{{ $user->matricule }}</span>
                    </p>
                </div>
                <div style="text-align: right;">
                    <div style="background: #e8f4eb; padding: 1rem; border-radius: 8px;">
                        <div style="font-size: 12px; color: #4a5e4a; margin-bottom: 0.5rem;">Profil</div>
                        <div style="font-size: 18px; font-weight: 700; color: #1a4d2e;">
                            {{ $user->profil->libelle ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations générales -->
        <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid #dce8dc;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: #1a2e1a; margin: 0 0 1.5rem;">Informations générales</h2>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.4rem;">Email</div>
                    <div style="font-size: 14px; color: #1a2e1a;">{{ $user->email }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.4rem;">Téléphone</div>
                    <div style="font-size: 14px; color: #1a2e1a;">+221 {{ $user->telephone }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.4rem;">Ministère</div>
                    <div style="font-size: 14px; color: #1a2e1a;">{{ $user->ministere->nom ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.4rem;">Disponibilité</div>
                    <div style="font-size: 14px; color: #1a2e1a;">
                        {{ match($user->disponibilite) {
                            'immediate' => 'Immédiate',
                            'sous_7_jours' => 'Sous 7 jours',
                            'sous_15_jours' => 'Sous 15 jours',
                            'selon_calendrier' => 'Selon le calendrier',
                            default => '—'
                        } }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Compétences -->
        <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid #dce8dc;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: #1a2e1a; margin: 0 0 1.5rem;">Compétences</h2>
            
            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.5rem;">Niveau numérique</div>
                <div style="display: inline-block; background: #f0f4f0; color: #1a2e1a; padding: 0.5rem 1rem; border-radius: 8px; font-size: 14px; font-weight: 600;">
                    {{ ucfirst(str_replace('_', ' ', $user->niveau_numerique ?? '—')) }}
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.8rem;">Expériences</div>
                <div style="display: flex; flex-wrap: wrap; gap: 0.7rem;">
                    @forelse($experiences as $exp)
                        <span style="display: inline-block; background: #e8f4eb; color: #1a4d2e; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 13px; font-weight: 600;">
                            {{ str_replace('_', ' ', ucfirst($exp)) }}
                        </span>
                    @empty
                        <span style="color: #8a9a8a; font-size: 14px;">—</span>
                    @endforelse
                </div>
            </div>

            <div>
                <div style="font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.8rem;">Compétences techniques</div>
                <div style="display: flex; flex-wrap: wrap; gap: 0.7rem;">
                    @forelse($competencesTechniques as $comp)
                        <span style="display: inline-block; background: #f0f4f0; color: #1a2e1a; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 13px; font-weight: 600;">
                            {{ str_replace('_', ' ', ucfirst($comp)) }}
                        </span>
                    @empty
                        <span style="color: #8a9a8a; font-size: 14px;">—</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Choix régionaux -->
        <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid #dce8dc;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: #1a2e1a; margin: 0 0 1.5rem;">Choix régionaux</h2>
            
            <div style="display: grid; gap: 1rem;">
                @forelse($regionalChoices as $choice)
                    <div style="border: 1px solid #dce8dc; border-radius: 8px; padding: 1rem; background: #f9fbf9;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="display: inline-block; background: #4a8c5c; color: white; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 12px; font-weight: 700; margin-right: 1rem;">
                                    Choix {{ $choice->ordre }}
                                </div>
                                <span style="font-size: 16px; font-weight: 700; color: #1a2e1a;">
                                    {{ $choice->region->nom }}
                                </span>
                            </div>
                        </div>
                        
                        @if($choice->motivations->count() > 0)
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #dce8dc;">
                                <div style="font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.7rem;">Motivations:</div>
                                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                    @foreach($choice->motivations as $mot)
                                        <span style="display: inline-block; background: #e8f4eb; color: #1a4d2e; padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 12px;">
                                            {{ $mot->motivation->libelle ?? $mot->motivation_libre }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div style="color: #8a9a8a; font-size: 14px;">Aucun choix régional enregistré</div>
                @endforelse
            </div>
        </div>

        <!-- Métadonnées -->
        <div style="background: #f0f7f0; border-radius: 12px; padding: 1.5rem; border: 1px solid #dce8dc;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.4rem;">Inscrit le</div>
                    <div style="font-size: 14px; color: #1a2e1a;">{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.4rem;">Dernière mise à jour</div>
                    <div style="font-size: 14px; color: #1a2e1a;">{{ $user->updated_at->format('d/m/Y à H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
