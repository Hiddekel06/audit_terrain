@extends('layouts.admin')

@section('admin-title', 'Gestion des candidats')
@section('admin-subtitle', 'Explorez et analysez tous les profils des candidats')

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        transition: all 0.3s ease;
    }
    .btn-modern-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        border: none;
        border-radius: 9999px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        color: white;
    }
    .btn-modern-outline {
        background: rgba(255, 255, 255, 0.5);
        border: 1px solid rgba(37, 99, 235, 0.2);
        color: #2563eb;
        border-radius: 9999px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-modern-outline:hover {
        background: #f8fafc;
        border-color: #2563eb;
        transform: translateY(-2px);
    }
    .table-modern thead th {
        background: rgba(37, 99, 235, 0.03);
        border-bottom: 2px solid rgba(37, 99, 235, 0.05);
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1.25rem 1rem;
    }
    .table-modern tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid rgba(0,0,0,0.03);
    }
    .table-modern tbody tr:hover {
        background: rgba(37, 99, 235, 0.02);
    }
    .badge-modern {
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .filter-input {
        background: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        border-radius: 0.75rem !important;
        padding: 0.6rem 1rem !important;
        font-size: 0.875rem !important;
        transition: all 0.2s ease;
    }
    .filter-input:focus {
        background: white !important;
        border-color: #2563eb !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
    }
</style>

<div class="container-fluid p-0 pb-5">
    <!-- Filtres -->
    <div class="glass-card p-4 mb-4">
        <form method="GET" action="{{ route('admin.candidates.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Recherche</label>
                <input type="text" name="search" class="form-control filter-input" placeholder="Nom, matricule, email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Profil</label>
                <select name="profil_id" class="form-select filter-input">
                    <option value="">Tous les profils</option>
                    @foreach($profils as $profil)
                        <option value="{{ $profil->id }}" @selected(request('profil_id') == $profil->id)>{{ $profil->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Expérience</label>
                <select name="experience" class="form-select filter-input">
                    <option value="">Toutes</option>
                    @foreach($experiences as $value => $label)
                        <option value="{{ $value }}" @selected(request('experience') == $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted ms-2">Structure</label>
                <select name="ministere_id" class="form-select filter-input">
                    <option value="">Toutes les structures</option>
                    @foreach($ministeres as $ministere)
                        <option value="{{ $ministere->id }}" @selected(request('ministere_id') == $ministere->id)>{{ $ministere->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-modern-primary w-100 py-2">
                    <i class="bi bi-filter me-2"></i> Filtrer
                </button>
                @if(request()->anyFilled(['search', 'profil_id', 'experience', 'ministere_id', 'region_id', 'ready_to_deploy', 'niveau_numerique']))
                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-modern-outline py-2" title="Réinitialiser">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="glass-card overflow-hidden">
        @if($candidates->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Candidat</th>
                            <th>Profil</th>
                            <th>Niveau</th>
                            <th>Structure</th>
                            <th>Affectation</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($candidates as $candidate)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-modern bg-primary bg-opacity-10 text-primary">
                                            {{ substr($candidate->prenom, 0, 1) }}{{ substr($candidate->nom, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $candidate->nom }} {{ $candidate->prenom }}</div>
                                            <div class="text-muted small">{{ $candidate->matricule ?? $candidate->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-modern bg-primary bg-opacity-10 text-primary">
                                        {{ $candidate->profil->libelle ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-modern bg-light text-dark">
                                        {{ ucfirst(str_replace('_', ' ', $candidate->niveau_numerique ?? '—')) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-muted small fw-medium" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $candidate->ministere->nom ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        @if($candidate->team)
                                            <span class="text-success fw-bold"><i class="bi bi-people-fill me-1"></i> {{ $candidate->team->nom }}</span>
                                        @elseif(!empty($candidate->ready_to_deploy_all_regions))
                                            <span class="text-primary"><i class="bi bi-globe2 me-1"></i> National</span>
                                        @else
                                            <span class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $candidate->regionChoices->first()?->region->nom ?? '—' }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.candidates.show', $candidate) }}" class="btn btn-sm btn-modern-outline px-3">
                                        Détails
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Modernisée -->
            <div class="p-4 border-top border-light d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Affichage de <strong>{{ $candidates->firstItem() }}</strong> à <strong>{{ $candidates->lastItem() }}</strong> sur <strong>{{ $candidates->total() }}</strong> candidats
                </div>
                <div>
                    {{ $candidates->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="p-5 text-center">
                <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                    <i class="bi bi-search text-muted fs-1"></i>
                </div>
                <h5 class="text-dark fw-bold">Aucun résultat</h5>
                <p class="text-muted">Modifiez vos filtres pour trouver ce que vous cherchez.</p>
                <a href="{{ route('admin.candidates.index') }}" class="btn btn-modern-outline mt-2">Voir tous les candidats</a>
            </div>
        @endif
    </div>
</div>
@endsection
