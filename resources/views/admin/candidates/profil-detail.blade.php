@extends('layouts.admin')

@section('admin-title', 'Analyse par profil')
@section('admin-subtitle', 'Statistiques et répartition pour le profil sélectionné')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <!-- Retour -->
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('admin.candidates.index') }}" style="color: #065f46; text-decoration: none; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem;">
            <i class="bi bi-arrow-left"></i> Retour à la liste
        </a>
    </div>

        <!-- En-tête profil -->
        <div class="glass-card" style="background: linear-gradient(135deg, #059669 0%, #064e3b 100%); padding: 2.5rem; margin-bottom: 2rem; color: white; border: none;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 style="font-size: 2.25rem; font-weight: 800; margin: 0 0 0.5rem; letter-spacing: -0.025em;">{{ $profil->libelle }}</h1>
                    <p style="margin: 0; opacity: 0.9; font-size: 1rem; max-width: 600px;">{{ $profil->description }}</p>
                </div>
                <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(4px); padding: 1.25rem 2rem; border-radius: 1rem; text-align: center; min-width: 180px;">
                    <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.9; margin-bottom: 0.5rem;">Candidats</div>
                    <div style="font-size: 2.5rem; font-weight: 800; line-height: 1;">{{ $totalByProfil }}</div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Source de création -->
            <div class="glass-card p-4">
                <h3 style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 1.25rem;">Source de création</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @forelse($sourceStats as $source)
                        @php
                            $sourceType = $source->source_type ?? 'manual';
                            $isImport = in_array($sourceType, ['import', 'master_sync']);
                            $label = $isImport ? 'Importé' : 'Inscrit';
                            $bgColor = $isImport ? '#ecfdf5' : '#f1f5f9';
                            $textColor = $isImport ? '#059669' : '#475569';
                        @endphp
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.9rem; font-weight: 600; color: #1e293b;">{{ $label }}</span>
                            <span style="background: {{ $bgColor }}; color: {{ $textColor }}; padding: 0.35rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 700;">{{ $source->total }}</span>
                        </div>
                    @empty
                        <p style="color: #94a3b8; font-size: 0.9rem;">Aucune donnée</p>
                    @endforelse
                </div>
            </div>

            <!-- Métiers -->
            <div class="glass-card p-4">
                <h3 style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 1.25rem;">Métiers</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @forelse($metierStats as $item)
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.9rem; font-weight: 600; color: #1e293b;">{{ $item->metier_label }}</span>
                            <span style="background: #f1f5f9; color: #475569; padding: 0.35rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 700;">{{ $item->total }}</span>
                        </div>
                    @empty
                        <p style="color: #94a3b8; font-size: 0.9rem;">Aucune donnée</p>
                    @endforelse
                </div>
            </div>

            <!-- Niveau numérique -->
            <div class="glass-card p-4">
                <h3 style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 1.25rem;">Distribution par niveau</h3>
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    @forelse($niveauStats as $niveau => $count)
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; align-items: center;">
                                <span style="font-size: 0.85rem; color: #1e293b; font-weight: 700;">{{ ucfirst(str_replace('_', ' ', $niveau)) }}</span>
                                <span style="font-size: 0.85rem; font-weight: 800; color: #059669;">{{ $count }}</span>
                            </div>
                            <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; background: #059669; width: {{ min(100, ($count / max(1, $totalByProfil)) * 100) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <p style="color: #94a3b8; font-size: 0.9rem;">Aucune donnée</p>
                    @endforelse
                </div>
            </div>

            <!-- Compétences techniques -->
            <div class="glass-card p-4">
                <h3 style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 1.25rem;">Top compétences techniques</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @forelse($competencesStats as $competence => $count)
                        <div style="background: #ecfdf5; color: #059669; padding: 0.5rem 0.9rem; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                            {{ str_replace('_', ' ', ucfirst($competence)) }}
                            <span style="background: rgba(5,150,105,0.15); padding: 0.1rem 0.4rem; border-radius: 0.35rem; font-size: 0.7rem;">{{ $count }}</span>
                        </div>
                    @empty
                        <p style="color: #94a3b8; font-size: 0.9rem;">Aucune donnée</p>
                    @endforelse
                </div>
            </div>

            <!-- Expériences -->
            <div class="glass-card p-4">
                <h3 style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin: 0 0 1.25rem;">Top expériences</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @forelse($experiencesStats as $experience => $count)
                        <div style="background: #f1f5f9; color: #475569; padding: 0.5rem 0.9rem; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                            {{ str_replace('_', ' ', ucfirst($experience)) }}
                            <span style="background: rgba(71,85,105,0.1); padding: 0.1rem 0.4rem; border-radius: 0.35rem; font-size: 0.7rem;">{{ $count }}</span>
                        </div>
                    @empty
                        <p style="color: #94a3b8; font-size: 0.9rem;">Aucune donnée</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Choix régionaux -->
        <div class="glass-card overflow-hidden">
            <div class="p-4 border-bottom border-light">
                <h2 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.025em;">Préférences régionales</h2>
            </div>
            
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Région</th>
                            <th class="text-center">1er choix</th>
                            <th class="text-center">2e choix</th>
                            <th class="text-center">3e choix</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($regionalChoices->groupBy('nom') as $region => $items)
                            <tr>
                                <td style="font-weight: 700; color: #0f172a;">{{ $region }}</td>
                                <td class="text-center" style="color: #64748b;">
                                    {{ $items->where('ordre', 1)->sum('total') ?: '—' }}
                                </td>
                                <td class="text-center" style="color: #64748b;">
                                    {{ $items->where('ordre', 2)->sum('total') ?: '—' }}
                                </td>
                                <td class="text-center" style="color: #64748b;">
                                    {{ $items->where('ordre', 3)->sum('total') ?: '—' }}
                                </td>
                                <td class="text-center">
                                    <span style="background: #ecfdf5; color: #059669; padding: 0.35rem 0.75rem; border-radius: 0.5rem; font-weight: 800; font-size: 0.85rem;">
                                        {{ $items->sum('total') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-5 text-muted">Aucune donnée enregistrée pour le moment</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
