@extends('layouts.admin')

@section('admin-title', 'Analyse par profil')
@section('admin-subtitle', 'Statistiques et répartition pour le profil sélectionné')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <!-- Retour -->
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('admin.candidates.index') }}" style="color: #4a8c5c; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            ← Retour à la liste
        </a>
    </div>

        <!-- En-tête profil -->
        <div style="background: linear-gradient(135deg, #4a8c5c 0%, #2f5f3d 100%); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; color: white;">
            <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem;">{{ $profil->libelle }}</h1>
            <p style="margin: 0; opacity: 0.9; font-size: 15px;">{{ $profil->description }}</p>
            <div style="margin-top: 1rem; display: flex; gap: 2rem;">
                <div>
                    <div style="font-size: 12px; opacity: 0.8;">Total de candidats</div>
                    <div style="font-size: 2rem; font-weight: 700;">{{ $totalByProfil }}</div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Niveau numérique -->
            <div style="background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #dce8dc;">
                <h3 style="font-size: 14px; font-weight: 700; color: #1a2e1a; margin: 0 0 1rem;">Distribution par niveau</h3>
                <div style="display: flex; flex-direction: column; gap: 0.7rem;">
                    @forelse($niveauStats as $niveau => $count)
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
                                <span style="font-size: 13px; color: #4a5e4a; font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $niveau)) }}</span>
                                <span style="font-size: 13px; font-weight: 700; color: #4a8c5c;">{{ $count }}</span>
                            </div>
                            <div style="height: 8px; background: #eef5ee; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; background: #4a8c5c; width: {{ min(100, ($count / $totalByProfil) * 100) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <p style="color: #8a9a8a; font-size: 14px; margin: 0;">Aucune donnée</p>
                    @endforelse
                </div>
            </div>

            <!-- Compétences techniques -->
            <div style="background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #dce8dc;">
                <h3 style="font-size: 14px; font-weight: 700; color: #1a2e1a; margin: 0 0 1rem;">Top compétences techniques</h3>
                <div style="display: flex; flex-direction: column; gap: 0.7rem;">
                    @forelse($competencesStats as $competence => $count)
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 13px; color: #4a5e4a;">{{ str_replace('_', ' ', ucfirst($competence)) }}</span>
                            <span style="display: inline-block; background: #e8f4eb; color: #1a4d2e; padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 12px; font-weight: 600;">{{ $count }}</span>
                        </div>
                    @empty
                        <p style="color: #8a9a8a; font-size: 14px; margin: 0;">Aucune donnée</p>
                    @endforelse
                </div>
            </div>

            <!-- Expériences -->
            <div style="background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #dce8dc;">
                <h3 style="font-size: 14px; font-weight: 700; color: #1a2e1a; margin: 0 0 1rem;">Top expériences</h3>
                <div style="display: flex; flex-direction: column; gap: 0.7rem;">
                    @forelse($experiencesStats as $experience => $count)
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 13px; color: #4a5e4a;">{{ str_replace('_', ' ', ucfirst($experience)) }}</span>
                            <span style="display: inline-block; background: #f0f4f0; color: #1a2e1a; padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 12px; font-weight: 600;">{{ $count }}</span>
                        </div>
                    @empty
                        <p style="color: #8a9a8a; font-size: 14px; margin: 0;">Aucune donnée</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Choix régionaux -->
        <div style="background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #dce8dc;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: #1a2e1a; margin: 0 0 1.5rem;">Préférences régionales</h2>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f0f7f0; border-bottom: 1px solid #dce8dc;">
                            <th style="padding: 1rem; text-align: left; font-size: 12px; font-weight: 600; color: #4a5e4a;">Région</th>
                            <th style="padding: 1rem; text-align: center; font-size: 12px; font-weight: 600; color: #4a5e4a;">1er choix</th>
                            <th style="padding: 1rem; text-align: center; font-size: 12px; font-weight: 600; color: #4a5e4a;">2e choix</th>
                            <th style="padding: 1rem; text-align: center; font-size: 12px; font-weight: 600; color: #4a5e4a;">3e choix</th>
                            <th style="padding: 1rem; text-align: center; font-size: 12px; font-weight: 600; color: #4a5e4a;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($regionalChoices->groupBy('nom') as $region => $items)
                            <tr style="border-bottom: 1px solid #eef5ee;">
                                <td style="padding: 1rem; font-size: 14px; font-weight: 600; color: #1a2e1a;">{{ $region }}</td>
                                <td style="padding: 1rem; text-align: center; font-size: 14px; color: #5a6e5a;">
                                    {{ $items->where('ordre', 1)->sum('total') ?: '—' }}
                                </td>
                                <td style="padding: 1rem; text-align: center; font-size: 14px; color: #5a6e5a;">
                                    {{ $items->where('ordre', 2)->sum('total') ?: '—' }}
                                </td>
                                <td style="padding: 1rem; text-align: center; font-size: 14px; color: #5a6e5a;">
                                    {{ $items->where('ordre', 3)->sum('total') ?: '—' }}
                                </td>
                                <td style="padding: 1rem; text-align: center; font-size: 14px; font-weight: 700; color: #4a8c5c;">
                                    {{ $items->sum('total') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 2rem; text-align: center; color: #8a9a8a;">Aucune donnée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
