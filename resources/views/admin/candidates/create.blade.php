@extends('layouts.admin')

@section('admin-title', 'Ajout Manuel d\'Agent')
@section('admin-subtitle', 'Espace dédié à l\'enrichissement du vivier')

@section('content')
<style>
    /* Effet de flou sur le fond quand la modale est ouverte */
    .modal-backdrop.show {
        backdrop-filter: none;
        background-color: transparent;
    }

    /* Petit message d'état pour l'import */
    #import-status {
        display: none;
    }

    .glass-card-mini {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        max-width: 500px;
        margin: 0 auto;
        padding: 2.5rem !important;
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        border: none;
        border-radius: 9999px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        color: white;
    }

    .add-illustration-small {
        width: 80px;
        height: 80px;
        background: rgba(37, 99, 235, 0.08);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #2563eb;
        font-size: 2rem;
    }

    /* Modal Elegance */
    .modal-content-premium {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 2rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    .form-label-modern { 
        font-size: 0.7rem; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 0.08em; 
        color: #94a3b8; 
        margin-bottom: 0.5rem; 
        margin-left: 0.75rem; 
    }
    
    .form-control-modern, .form-select-modern { 
        background: white; 
        border: 1px solid #e2e8f0; 
        border-radius: 1rem; 
        padding: 0.75rem 1.25rem; 
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }
    
    .form-control-modern:focus, .form-select-modern:focus { 
        border-color: #2563eb; 
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08); 
        outline: none; 
    }
</style>

<div class="container-fluid p-0 pb-5">
    @if(session('success'))
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="alert alert-success border-0 shadow-sm rounded-4 px-4 py-3 animate__animated animate__fadeIn">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    @if(session('import_preview'))
        @php($importPreview = session('import_preview'))
        @php($importMode = $importPreview['mode'] ?? 'classic')
        <div class="row justify-content-center mb-4">
            <div class="col-12">
                <div class="glass-card-mini" style="max-width: 100%;">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                        <div>
                            <h3 class="h5 fw-bold text-dark mb-1">
                                {{ $importMode === 'phone_update' ? 'Prévisualisation de mise à jour téléphone' : 'Prévisualisation d\'import' }}
                            </h3>
                            <div class="text-muted small">
                                {{ $importPreview['original_name'] ?? 'Fichier importé' }} -
                                {{ $importPreview['total'] ?? 0 }} ligne(s),
                                {{ $importPreview['valid'] ?? 0 }} prête(s),
                                {{ $importPreview['invalid'] ?? 0 }} bloquée(s)
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <form action="{{ route('admin.candidates.import.cancel') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-light rounded-pill px-4">Annuler</button>
                            </form>
                            <form action="{{ route('admin.candidates.import.confirm') }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="preview_token" value="{{ $importPreview['token'] }}">
                                <button type="submit" class="btn btn-modern-primary px-4">
                                    {{ $importMode === 'phone_update' ? 'Confirmer la mise à jour' : 'Confirmer l’import' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                @if($importMode === 'phone_update')
                                    <tr>
                                        <th>Ligne</th>
                                        <th>Matricule</th>
                                        <th>Téléphone actuel</th>
                                        <th>Téléphone importé</th>
                                        <th>Statut</th>
                                    </tr>
                                @else
                                    <tr>
                                        <th>Ligne</th>
                                        <th>Membres</th>
                                        <th>Prénom</th>
                                        <th>Nom</th>
                                        <th>Matricule</th>
                                        <th>Téléphone</th>
                                        <th>Direction</th>
                                        <th>Structure saisie</th>
                                        <th>Ministère reconnu</th>
                                        <th>Métier</th>
                                        <th>Profil</th>
                                        <th>Profil reconnu</th>
                                        <th>Profil secondaire</th>
                                        <th>Statut</th>
                                    </tr>
                                @endif
                            </thead>
                            <tbody>
                                @foreach(($importPreview['rows'] ?? []) as $row)
                                    @if($importMode === 'phone_update')
                                        <tr>
                                            <td>{{ $row['line'] ?? '—' }}</td>
                                            <td>{{ $row['matricule'] ?: '—' }}</td>
                                            <td>{{ $row['current_telephone'] ?: '—' }}</td>
                                            <td>{{ $row['telephone'] ?: '—' }}</td>
                                            <td>
                                                @if(($row['status'] ?? '') === 'ok')
                                                    <span class="badge bg-success">Prête</span>
                                                @else
                                                    <span class="badge bg-danger">Bloquée</span>
                                                @endif
                                                @if(!empty($row['issues']))
                                                    <div class="text-danger small">{{ implode(' | ', $row['issues']) }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ $row['line'] ?? '—' }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $row['full_name'] ?: '—' }}</div>
                                                @if(!empty($row['warnings']))
                                                    <div class="text-warning small">{{ implode(' | ', $row['warnings']) }}</div>
                                                @endif
                                                @if(!empty($row['issues']))
                                                    <div class="text-danger small">{{ implode(' | ', $row['issues']) }}</div>
                                                @endif
                                            </td>
                                            <td>{{ $row['prenom'] ?: '—' }}</td>
                                            <td>{{ $row['nom'] ?: '—' }}</td>
                                            <td>{{ $row['matricule'] ?: '—' }}</td>
                                            <td>{{ $row['telephone'] ?: '—' }}</td>
                                            <td>{{ $row['direction'] ?: '—' }}</td>
                                            <td>{{ $row['ministere_input'] ?: '—' }}</td>
                                            <td>
                                                @if(!empty($row['ministere_reconnu']))
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $row['ministere_reconnu'] }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $row['metier'] ?: '—' }}</td>
                                            <td>{{ $row['profil_input'] ?: '—' }}</td>
                                            <td>
                                                @if(!empty($row['profil_reconnu']))
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $row['profil_reconnu'] }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($row['profil_secondaires']))
                                                    <span class="small">{{ implode(', ', $row['profil_secondaires']) }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(($row['status'] ?? '') === 'ok')
                                                    <span class="badge bg-success">Prête</span>
                                                @elseif(($row['status'] ?? '') === 'warning')
                                                    <span class="badge bg-warning text-dark">À vérifier</span>
                                                @else
                                                    <span class="badge bg-danger">Bloquée</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="alert alert-danger border-0 shadow-sm rounded-4 px-4 py-3 animate__animated animate__fadeIn">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

    <div class="row justify-content-center g-4">
        <!-- Ajout Individuel -->
        <div class="col-lg-4">
            <div class="glass-card-mini h-100">
                <div class="add-illustration-small">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h2 class="h4 fw-bold text-dark mb-2">Ajout individuel</h2>
                <p class="text-muted small mb-4">
                    Saisissez manuellement les informations d'un seul agent.
                </p>
                
                <button type="button" class="btn btn-modern-primary w-100" data-bs-toggle="modal" data-bs-target="#createAgentModal">
                    <i class="bi bi-plus-lg me-2"></i> Ouvrir le formulaire
                </button>
            </div>
        </div>

        <!-- Import classique -->
        <div class="col-lg-4">
            <div class="glass-card-mini h-100">
                <div class="add-illustration-small" style="background: rgba(16, 185, 129, 0.08); color: #10b981;">
                    <i class="bi bi-file-earmark-arrow-up-fill"></i>
                </div>
                <h2 class="h4 fw-bold text-dark mb-2">Import classique</h2>
                <p class="text-muted small mb-4">
                    Déposez un fichier Excel pour créer des agents avec le flux habituel.
                </p>
                
                <form action="{{ route('admin.candidates.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <input type="hidden" name="import_mode" value="classic">
                    <input type="file" name="excel_file" id="excel_file" class="d-none" accept=".xlsx,.xls" onchange="document.getElementById('import-status').classList.remove('d-none'); this.form.submit()">
                    <button type="button" class="btn btn-modern-primary w-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);" onclick="document.getElementById('excel_file').click()">
                        <i class="bi bi-upload me-2"></i> Prévisualiser le fichier
                    </button>
                    <div id="import-status" class="alert alert-info border-0 rounded-4 mt-3 mb-0 py-2 px-3 small d-none">
                        <i class="bi bi-hourglass-split me-2"></i> Import en cours, merci de patienter...
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ route('admin.candidates.template', ['mode' => 'classic']) }}" class="text-primary small fw-bold text-decoration-none">
                        <i class="bi bi-download me-1"></i> Télécharger le modèle classique (Excel)
                    </a>
                </div>
            </div>
        </div>

        <!-- Import téléphone -->
        <div class="col-lg-4">
            <div class="glass-card-mini h-100">
                <div class="add-illustration-small" style="background: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <h2 class="h4 fw-bold text-dark mb-2">Mise à jour téléphone</h2>
                <p class="text-muted small mb-4">
                    Importez un fichier Excel avec les colonnes <strong>matricule</strong> et <strong>telephone</strong> pour mettre à jour les numéros existants.
                </p>

                <form action="{{ route('admin.candidates.import') }}" method="POST" enctype="multipart/form-data" id="phoneImportForm">
                    @csrf
                    <input type="hidden" name="import_mode" value="phone_update">
                    <input type="file" name="excel_file" id="phone_excel_file" class="d-none" accept=".xlsx,.xls" onchange="document.getElementById('phone-import-status').classList.remove('d-none'); this.form.submit()">
                    <button type="button" class="btn btn-modern-primary w-100" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);" onclick="document.getElementById('phone_excel_file').click()">
                        <i class="bi bi-arrow-repeat me-2"></i> Prévisualiser la mise à jour
                    </button>
                    <div id="phone-import-status" class="alert alert-info border-0 rounded-4 mt-3 mb-0 py-2 px-3 small d-none">
                        <i class="bi bi-hourglass-split me-2"></i> Mise à jour en cours, merci de patienter...
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ route('admin.candidates.template', ['mode' => 'phone_update']) }}" class="text-primary small fw-bold text-decoration-none">
                        <i class="bi bi-download me-1"></i> Télécharger le modèle téléphone (Excel)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Création Agent -->
<div class="modal fade" id="createAgentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-premium border-0">
            <form action="{{ route('admin.candidates.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <div class="ps-2">
                        <h5 class="modal-title fw-bold text-dark">Nouvel agent</h5>
                        <p class="text-muted small mb-0">Remplissez les informations pour créer le profil.</p>
                    </div>
                    <button type="button" class="btn-close me-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 p-md-5 text-start">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-modern">Prénom</label>
                            <input type="text" name="prenom" class="form-control form-control-modern" placeholder="Ex: Moussa" value="{{ old('prenom') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Nom</label>
                            <input type="text" name="nom" class="form-control form-control-modern" placeholder="Ex: Diop" value="{{ old('nom') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Matricule / CIN</label>
                            <input type="text" name="matricule" id="matricule" class="form-control form-control-modern" placeholder="123456A" value="{{ old('matricule') }}" pattern="[0-9]{6}[A-Z]" maxlength="7">
                            <div class="invalid-feedback d-none" id="matricule-feedback"></div>
                            <div class="form-text">Renseignez ce champ ou le numéro de téléphone.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Email</label>
                            <input type="email" name="email" class="form-control form-control-modern" placeholder="adresse@email.com" value="{{ old('email') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 rounded-start-4 text-muted small">+221</span>
                                <input type="text" name="telephone" id="telephone" class="form-control form-control-modern border-start-0 rounded-end-4 js-phone-9" placeholder="771234567" value="{{ old('telephone') }}" inputmode="numeric" pattern="[0-9]{9}" maxlength="9">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Ministère / Structure</label>
                            <select name="ministere_id" class="form-select form-select-modern" required>
                                <option value="" selected disabled>Choisir une structure</option>
                                @foreach($ministeres as $m)
                                    <option value="{{ $m->id }}">{{ $m->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Direction</label>
                            <input type="text" name="direction" class="form-control form-control-modern @error('direction') is-invalid @enderror" placeholder="Nom de la direction" value="{{ old('direction') }}">
                            @error('direction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Métier</label>
                            <input type="text" name="metier" class="form-control form-control-modern @error('metier') is-invalid @enderror" placeholder="Ex: Enseignant" value="{{ old('metier') }}">
                            @error('metier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Profil visé</label>
                            <select name="profil_id" class="form-select form-select-modern @error('profil_id') is-invalid @enderror" required>
                                <option value="" selected disabled>Choisir un rôle</option>
                                @foreach($profils as $p)
                                    <option value="{{ $p->id }}" @selected(old('profil_id') == $p->id)>{{ $p->libelle }}</option>
                                @endforeach
                            </select>
                            @error('profil_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 p-md-5 pt-0 justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-modern-primary px-5 shadow-lg">
                        Confirmer l'ajout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Succès -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-premium border-0 text-center">
            <div class="modal-body p-4">
                <div class="add-illustration-small mb-3">
                    <i class="bi bi-check-lg"></i>
                </div>
                <h5 class="fw-bold">Succès</h5>
                <p class="text-muted small mb-0">{{ session('success') }}</p>
                <div class="mt-3">
                    <button type="button" class="btn btn-modern-primary px-4" data-bs-dismiss="modal">Fermer</button>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function clearModalArtifacts() {
        document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop').forEach(function(node) {
            node.remove();
        });

        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    window.addEventListener('pageshow', clearModalArtifacts);
    window.addEventListener('load', clearModalArtifacts);

    document.getElementById('excel_file').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.getElementById('import-status').style.display = 'block';
            document.getElementById('importForm').submit();
        }
    });

    // Limite tous les champs téléphone marqués .js-phone-9 à 9 chiffres max
    (function() {
        var phoneInputs = document.querySelectorAll('.js-phone-9');
        phoneInputs.forEach(function(input) {
            input.addEventListener('input', function(e) {
                var digits = e.target.value.replace(/\D/g, '').slice(0, 9);
                e.target.value = digits;
            });
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                var pasted = (e.clipboardData || window.clipboardData).getData('text');
                e.target.value = pasted.replace(/\D/g, '').slice(0, 9);
            });
        });
    })();

    // Vérification AJAX du matricule pour éviter les doublons
    (function() {
        var matriculeInput = document.getElementById('matricule');
        var feedback = document.getElementById('matricule-feedback');
        var submitBtn = document.querySelector('#createAgentModal form button[type="submit"]');

        function setInvalid(message) {
            feedback.textContent = message;
            feedback.classList.remove('d-none');
            matriculeInput.classList.add('is-invalid');
            submitBtn.disabled = true;
        }

        function clearInvalid() {
            feedback.textContent = '';
            feedback.classList.add('d-none');
            matriculeInput.classList.remove('is-invalid');
            submitBtn.disabled = false;
        }

        if (matriculeInput) {
            matriculeInput.addEventListener('input', function(e) {
                // Force uppercase for the trailing letter
                var v = e.target.value.toUpperCase();
                e.target.value = v;
                // Basic pattern check client-side
                var re = /^[0-9]{6}[A-Z]$/;
                if (v.length === 7 && !re.test(v)) {
                    setInvalid('Format invalide : 6 chiffres suivis d\'une lettre majuscule.');
                    return;
                }
                if (v.length < 7) {
                    clearInvalid();
                    return;
                }

                // AJAX check
                fetch("{{ route('admin.candidates.check_matricule') }}?matricule=" + encodeURIComponent(v))
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.exists) {
                            setInvalid('Ce matricule existe déjà.');
                        } else {
                            clearInvalid();
                        }
                    }).catch(function() {
                        // En cas d'erreur, ne pas bloquer la saisie
                        clearInvalid();
                    });
            });
        }
    })();

    // Ouvrir la modal de succès si présent en session, avec fallback si Bootstrap est bloqué
    @if(session('success'))
        (function() {
            var message = @json(session('success'));
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                } else {
                    alert(message);
                }
            } catch (e) {
                console.error('Modal show failed, falling back to alert', e);
                alert(message);
            }
        })();
    @endif

    // Ouvrir la modal de création automatiquement si des erreurs de validation existent
    @if($errors->any())
        (function() {
            var firstMessage = @json($errors->first());
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var createModal = new bootstrap.Modal(document.getElementById('createAgentModal'));
                    createModal.show();
                } else {
                    alert(firstMessage);
                }
            } catch (e) {
                console.error('Auto-open modal failed, falling back to alert', e);
                alert(firstMessage);
            }
        })();
    @endif

    @if(session('import_preview'))
        (function() {
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    clearModalArtifacts();
                }
            } catch (e) {
                console.error('Preview modal failed', e);
            }
        })();
    @endif

    document.addEventListener('hidden.bs.modal', clearModalArtifacts);
</script>
@endpush
