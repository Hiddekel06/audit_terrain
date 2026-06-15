@extends('layouts.admin')

@section('admin-title', 'Synchronisation Liste Maître')
@section('admin-subtitle', 'Réconciliation de la base avec la liste officielle')

@section('content')
<div class="container-fluid px-4">
    <!-- Statistiques de Validation -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 border-start border-4 border-primary">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-people text-primary fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Agents</h6>
                        <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 border-start border-4 border-success">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-check-all text-success fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Officiels Inscrits</h6>
                        <h4 class="mb-0 fw-bold">{{ $stats['officiel_inscrit'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 border-start border-4 border-warning">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-clock-history text-warning fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Officiels Attente</h6>
                        <h4 class="mb-0 fw-bold">{{ $stats['officiel_attente'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-4 border-start border-4 border-secondary">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-secondary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-box-seam text-secondary fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Liste de Réserve</h6>
                        <h4 class="mb-0 fw-bold">{{ $stats['reserve'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    @if($preview)
    <!-- Rapport de Prévisualisation Interactif -->
    <div class="row mb-5 animate__animated animate__fadeIn">
        <div class="col-12">
            <div class="glass-card p-0 border-primary border-2">
                <div class="bg-primary bg-opacity-10 p-4 rounded-top-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-primary mb-1">Validation de la liste : {{ $preview['source_name'] }}</h5>
                        <p class="text-muted small mb-0">Cochez les agents à <strong>fusionner</strong> avec la base existante. Les lignes décochées seront <strong>créées</strong>.</p>
                    </div>
                    <div class="badge bg-primary px-3 py-2 rounded-pill">Mode : {{ $preview['reset_mode'] === 'reset' ? 'Nettoyage' : 'Cumulatif' }}</div>
                </div>
                <div class="p-4">
                    <div class="mt-2">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <form id="confirmSyncForm" action="{{ route('admin.master_sync.confirm') }}" method="POST">
                                @csrf
                                <table class="table table-sm table-hover table-modern small mb-0">
                                    <thead class="sticky-top bg-white shadow-sm">
                                        <tr>
                                            <th style="width: 40px;"></th>
                                            <th>Agent (Excel)</th>
                                            <th>Matricule</th>
                                            <th>Profil Agent</th>
                                            <th>Profil Excel</th>
                                            <th>Confiance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($preview['analysis']['details'] as $idx => $detail)
                                            <tr class="{{ $detail['confidence'] == 'medium' ? 'table-warning' : '' }}">
                                                <td class="text-center align-middle">
                                                    <input type="checkbox" name="validated[{{ $idx }}]" value="1" 
                                                        class="form-check-input border-primary" 
                                                        {{ $detail['confidence'] == 'high' ? 'checked' : '' }}>
                                                    <input type="hidden" name="rows[{{ $idx }}][parsed_b64]" value="{{ $detail['parsed_json'] }}">
                                                    <input type="hidden" name="rows[{{ $idx }}][user_id]" value="{{ $detail['existing_user_id'] }}">
                                                </td>
                                                <td class="align-middle">
                                                    <div class="fw-bold">{{ $detail['name'] }}</div>
                                                    <small class="text-muted">{{ Str::limit($detail['ministere'], 25) }}</small>
                                                </td>
                                                <td class="align-middle"><code>{{ $detail['matricule'] }}</code></td>
                                                <td class="align-middle">
                                                    <span class="small {{ $detail['action'] == 'Changement Profil' ? 'text-danger fw-bold' : '' }}">
                                                        {{ $detail['profil_actuel'] }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge {{ $detail['action'] == 'Changement Profil' ? 'bg-warning text-dark' : 'bg-light text-dark border' }}">
                                                        {{ $detail['profil_excel'] }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    @if($detail['confidence'] == 'high')
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Exact</span>
                                                    @elseif($detail['confidence'] == 'medium')
                                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">Homonyme ?</span>
                                                    @else
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2">Nouveau</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="button" onclick="document.getElementById('confirmSyncForm').submit()" class="btn btn-modern-primary flex-grow-1 py-3">
                            <i class="bi bi-check2-all me-2"></i> Appliquer les choix et finaliser
                        </button>
                        <a href="{{ route('admin.master_sync.cancel') }}" class="btn btn-modern-outline py-3 px-5">Annuler l'import</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                        <i class="bi bi-cloud-upload text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Importer la Référence Absolue</h5>
                </div>

                <form action="{{ route('admin.master_sync.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Nom de la source / Liste</label>
                            <input type="text" name="source_name" class="form-control rounded-pill border-light bg-light px-4" placeholder="Ex: Interne, Externe..." required>
                            @if($sources->count() > 0)
                                <div class="mt-2">
                                    <small class="text-muted">Déjà chargées : </small>
                                    @foreach($sources as $source)
                                        <span class="badge bg-light text-dark rounded-pill border">{{ $source }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Ministère par défaut</label>
                            <select name="default_ministere_id" class="form-select rounded-pill border-light bg-light px-4">
                                <option value="">Utiliser la colonne Excel</option>
                                @foreach(\App\Models\Ministere::orderBy('nom')->get() as $min)
                                    <option value="{{ $min->id }}">{{ $min->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Mode d'importation</label>
                            <select name="reset_mode" class="form-select rounded-pill border-light bg-light px-4" required>
                                <option value="additive">Ajouter (Cumulatif)</option>
                                <option value="reset">Remplacer tout (Nettoyage)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="upload-zone border-dashed border-2 rounded-4 p-5 text-center bg-light bg-opacity-50">
                            <input type="file" name="file" class="form-control d-none" id="masterSyncFile" accept=".xlsx,.xls,.csv" required onchange="updateFileName(this)">
                            <label for="masterSyncFile" class="cursor-pointer">
                                <i class="bi bi-file-earmark-excel fs-1 text-primary opacity-50 mb-3 d-block"></i>
                                <span class="fw-bold text-dark d-block mb-1" id="fileNameDisplay">Cliquez pour choisir le fichier Excel</span>
                                <span class="text-muted small">Format accepté : .xlsx, .xls, .csv</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-modern-primary w-100 py-3 mt-3">
                        Lancer l'Analyse du Fichier
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <h5 class="fw-bold mb-4">Actions de Maintenance</h5>
                
                <div class="alert alert-danger border-0 rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-danger mb-2"><i class="bi bi-trash3-fill me-2"></i> Réinitialisation</h6>
                    <p class="small mb-3">Remet tous les agents en état "Inconnu" et supprime les badges officiels. Indispensable avant une nouvelle réconciliation propre.</p>
                    
                    <form action="{{ route('admin.master_sync.reset') }}" method="POST" onsubmit="return confirm('Voulez-vous réinitialiser toutes les sources ?')">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 rounded-pill">
                            Vider la Source Officielle
                        </button>
                    </form>
                </div>

                <div class="mt-auto p-3 rounded-4 bg-light border">
                    <h6 class="fw-bold small text-uppercase mb-3">Statistiques par Source</h6>
                    @php
                        $sourceStats = \App\Models\User::whereNotNull('validation_source')
                            ->selectRaw('validation_source, count(*) as count')
                            ->groupBy('validation_source')
                            ->get();
                    @endphp
                    @forelse($sourceStats as $s)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">{{ $s->validation_source }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $s->count }}</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Aucune source.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        const display = document.getElementById('fileNameDisplay');
        if (input.files.length > 0) {
            display.textContent = input.files[0].name;
            display.classList.add('text-primary');
        } else {
            display.textContent = 'Cliquez pour choisir le fichier Excel';
            display.classList.remove('text-primary');
        }
    }
</script>

<style>
    .upload-zone { transition: all 0.3s ease; border-color: #cbd5e1; }
    .upload-zone:hover { border-color: #2563eb; background: rgba(37, 99, 235, 0.05) !important; }
    .cursor-pointer { cursor: pointer; }
    .table-modern thead th { border: none; font-weight: 700; color: #64748b; padding: 12px; }
    .table-modern tbody td { border-bottom: 1px solid #f1f5f9; padding: 12px; vertical-align: middle; }
    .form-check-input { width: 1.2em; height: 1.2em; }
</style>
@endsection
