@extends('layouts.app')

@section('content')
<div style="padding: 2rem; background: #f7f8fa; min-height: 100vh;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <!-- En-tête -->
        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1a2e1a; margin: 0 0 1rem;">Gestion des candidats</h1>
            <p style="color: #5a6e5a; margin: 0;">Explorez et analysez tous les profils des candidats</p>
        </div>

        <!-- Filtres -->
        <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid #dce8dc;">
            <form method="GET" action="{{ route('admin.candidates.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: flex-end;">
                <!-- Recherche -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.5rem;">Recherche</label>
                    <input type="text" name="search" placeholder="Nom, matricule, email..." 
                        value="{{ request('search') }}"
                        style="width: 100%; padding: 0.7rem; border: 1px solid #dde5dd; border-radius: 8px; font-size: 14px;">
                </div>

                <!-- Profil -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.5rem;">Profil</label>
                    <select name="profil_id" style="width: 100%; padding: 0.7rem; border: 1px solid #dde5dd; border-radius: 8px; font-size: 14px;">
                        <option value="">— Tous —</option>
                        @foreach($profils as $profil)
                            <option value="{{ $profil->id }}" @selected(request('profil_id') == $profil->id)>
                                {{ $profil->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Niveau numérique -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.5rem;">Niveau numérique</label>
                    <select name="niveau_numerique" style="width: 100%; padding: 0.7rem; border: 1px solid #dde5dd; border-radius: 8px; font-size: 14px;">
                        <option value="">— Tous —</option>
                        @foreach($niveauxNumeriques as $niveau)
                            <option value="{{ $niveau }}" @selected(request('niveau_numerique') == $niveau)>
                                {{ ucfirst(str_replace('_', ' ', $niveau)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Expérience -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.5rem;">Expérience</label>
                    <select name="experience" style="width: 100%; padding: 0.7rem; border: 1px solid #dde5dd; border-radius: 8px; font-size: 14px;">
                        <option value="">— Tous —</option>
                        @foreach($experiences as $value => $label)
                            <option value="{{ $value }}" @selected(request('experience') == $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Ministère -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.5rem;">Ministère</label>
                    <select name="ministere_id" style="width: 100%; padding: 0.7rem; border: 1px solid #dde5dd; border-radius: 8px; font-size: 14px;">
                        <option value="">— Tous —</option>
                        @foreach($ministeres as $ministere)
                            <option value="{{ $ministere->id }}" @selected(request('ministere_id') == $ministere->id)>
                                {{ $ministere->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Région -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.5rem;">Région</label>
                    <select name="region_id" style="width: 100%; padding: 0.7rem; border: 1px solid #dde5dd; border-radius: 8px; font-size: 14px;">
                        <option value="">— Tous —</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" @selected(request('region_id') == $region->id)>
                                {{ $region->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Engagement de déploiement -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #4a5e4a; margin-bottom: 0.5rem;">Déploiement</label>
                    <select name="ready_to_deploy" style="width: 100%; padding: 0.7rem; border: 1px solid #dde5dd; border-radius: 8px; font-size: 14px;">
                        <option value="">— Tous —</option>
                        <option value="yes" @selected(request('ready_to_deploy') === 'yes')>Oui, toutes les régions</option>
                        <option value="no" @selected(request('ready_to_deploy') === 'no')>Non, une seule région</option>
                    </select>
                </div>

                <!-- Boutons -->
                <div>
                    <button type="submit" style="width: 100%; padding: 0.7rem; background: #4a8c5c; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">
                        Filtrer
                    </button>
                </div>

                @if(request()->filled('search') || request()->filled('profil_id') || request()->filled('niveau_numerique') || request()->filled('experience') || request()->filled('ministere_id') || request()->filled('region_id') || request()->filled('ready_to_deploy'))
                    <div>
                        <a href="{{ route('admin.candidates.index') }}" style="width: 100%; padding: 0.7rem; background: #eef5ee; color: #2f5f3d; border: 1px solid #dce8dc; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px; text-align: center; text-decoration: none; display: block;">
                            Réinitialiser
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Tableau des candidats -->
        <div style="background: white; border-radius: 12px; border: 1px solid #dce8dc; overflow: hidden;">
            @if($candidates->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f0f7f0; border-bottom: 1px solid #dce8dc;">
                                <th style="padding: 1rem; text-align: left; font-size: 12px; font-weight: 600; color: #4a5e4a;">Nom</th>
                                <th style="padding: 1rem; text-align: left; font-size: 12px; font-weight: 600; color: #4a5e4a;">Matricule</th>
                                <th style="padding: 1rem; text-align: left; font-size: 12px; font-weight: 600; color: #4a5e4a;">Profil</th>
                                <th style="padding: 1rem; text-align: left; font-size: 12px; font-weight: 600; color: #4a5e4a;">Niveau</th>
                                <th style="padding: 1rem; text-align: left; font-size: 12px; font-weight: 600; color: #4a5e4a;">Ministère</th>
                                <th style="padding: 1rem; text-align: left; font-size: 12px; font-weight: 600; color: #4a5e4a;">Région</th>
                                <th style="padding: 1rem; text-align: left; font-size: 12px; font-weight: 600; color: #4a5e4a;">Email</th>
                                <th style="padding: 1rem; text-align: center; font-size: 12px; font-weight: 600; color: #4a5e4a;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($candidates as $candidate)
                                <tr class="candidate-row" style="border-bottom: 1px solid #eef5ee;">
                                    <td style="padding: 1rem; font-size: 14px; color: #1a2e1a; font-weight: 600;">
                                        {{ $candidate->nom }} {{ $candidate->prenom }}
                                    </td>
                                    <td style="padding: 1rem; font-size: 14px; color: #5a6e5a;">
                                        {{ $candidate->matricule ?? $candidate->email }}
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span style="display: inline-block; background: #e8f4eb; color: #1a4d2e; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                            {{ $candidate->profil->libelle ?? '—' }}
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; font-size: 14px; color: #5a6e5a;">
                                        <span style="display: inline-block; background: #f0f4f0; color: #4a5e4a; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 12px;">
                                            {{ ucfirst(str_replace('_', ' ', $candidate->niveau_numerique ?? '—')) }}
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; font-size: 13px; color: #5a6e5a;">
                                        {{ $candidate->ministere->nom ?? '—' }}
                                    </td>
                                    <td style="padding: 1rem; font-size: 13px; color: #5a6e5a;">
                                        @php
                                            $regionLabel = '—';
                                            if (!empty($candidate->ready_to_deploy_all_regions)) {
                                                $regionLabel = 'Toutes les régions';
                                            } else {
                                                $firstChoice = $candidate->regionChoices->first();
                                                $regionLabel = $firstChoice?->region->nom ?? '—';
                                            }
                                        @endphp

                                        {{ $regionLabel }}
                                    </td>
                                    <td style="padding: 1rem; font-size: 13px; color: #5a6e5a;">
                                        {{ $candidate->email }}
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <a href="{{ route('admin.candidates.show', $candidate) }}" style="display: inline-block; padding: 0.5rem 1rem; background: #4a8c5c; color: white; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                            Voir détail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="padding: 1.5rem; border-top: 1px solid #dce8dc; display: flex; justify-content: center;">
                    {{ $candidates->links() }}
                </div>
            @else
                <div style="padding: 3rem; text-align: center; color: #8a9a8a;">
                    <p style="margin: 0; font-size: 16px;">Aucun candidat ne correspond à vos critères.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .candidate-row:hover {
        background: #f9fbf9 !important;
    }

    tr:hover {
        background: #f9fbf9 !important;
    }
    a {
        transition: all 0.2s ease;
    }
</style>
@endsection
