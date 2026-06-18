@extends('layouts.admin')

@section('admin-title', 'Détails du Quiz')
@section('admin-subtitle', 'Gérez les questions et les réponses')

@section('admin-actions')
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Retour</span>
    </a>
    <div class="btn-group">
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            <i class="bi bi-plus-circle"></i>
            <span>Ajouter une question</span>
        </button>
        <button type="button" class="btn btn-dark d-flex align-items-center gap-2 border-start border-white border-opacity-25" data-bs-toggle="modal" data-bs-target="#addSectionModal">
            <i class="bi bi-folder-plus"></i>
            <span>Nouvelle section</span>
        </button>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        {{-- Sidebar Détails --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-4">
                            <i class="bi bi-patch-question fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-truncate-1">{{ $quiz->titre }}</h5>
                            <span class="badge {{ $quiz->is_active ? 'bg-success' : 'bg-secondary' }} mt-1">
                                {{ $quiz->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>

                    <p class="text-muted small mb-4">{{ $quiz->description ?? 'Aucune description fournie.' }}</p>

                    <div class="list-group list-group-flush small">
                        <div class="list-group-item d-flex flex-column px-0 py-3 bg-transparent">
                            <span class="text-muted mb-2">Profils cibles :</span>
                            <div>
                                @forelse($quiz->profils as $profil)
                                    <span class="badge bg-light text-primary border border-primary border-opacity-25 mb-1">{{ $profil->libelle }}</span>
                                @empty
                                    <span class="fw-bold">Tous les profils</span>
                                @endforelse
                            </div>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0 py-3 bg-transparent">
                            <span class="text-muted">Nombre de sections :</span>
                            <span class="fw-bold">{{ $quiz->sections->count() }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0 py-3 bg-transparent">
                            <span class="text-muted">Total questions :</span>
                            <span class="fw-bold">{{ $quiz->questions->count() }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0 py-3 bg-transparent">
                            <span class="text-muted">Total points :</span>
                            <span class="fw-bold">{{ $quiz->questions->sum('points') }} pts</span>
                        </div>
                        <div class="list-group-item d-flex flex-column px-0 py-3 bg-transparent">
                            <span class="text-muted mb-2">Lien direct (QR Code) :</span>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control bg-light border-0" id="quizUrl" value="{{ route('qcm.login', $quiz->slug) }}" readonly>
                                <button class="btn btn-outline-primary border-0" type="button" onclick="copyToClipboard()">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </div>
                            <div id="copyFeedback" class="text-success small mt-1" style="display: none;">
                                <i class="bi bi-check-circle-fill me-1"></i> Lien copié !
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <form action="{{ route('admin.quizzes.toggle', $quiz) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn {{ $quiz->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} w-100 rounded-pill">
                                <i class="bi {{ $quiz->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-2"></i>
                                {{ $quiz->is_active ? 'Désactiver le Quiz' : 'Activer le Quiz' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenu Principal --}}
        <div class="col-lg-8">
            {{-- Système d'onglets Smart --}}
            <ul class="nav nav-pills nav-fill bg-white p-2 rounded-4 shadow-sm mb-4 border" id="quizTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-3 fw-bold py-2" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions-content" type="button" role="tab">
                        <i class="bi bi-list-nested me-2"></i>Structure & Questions
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-3 fw-bold py-2 position-relative" id="participation-tab" data-bs-toggle="tab" data-bs-target="#participation-content" type="button" role="tab">
                        <i class="bi bi-people me-2"></i>Participation
                        @if($stats['total_target'] > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">
                                {{ count($stats['pending']) }}
                                <span class="visually-hidden">En attente</span>
                            </span>
                        @endif
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="quizTabsContent">
                {{-- Onglet 1 : Structure & Questions --}}
                <div class="tab-pane fade show active" id="questions-content" role="tabpanel">
                    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
                        <h4 class="fw-bold mb-0">Contenu du questionnaire</h4>
                    </div>

                    @forelse($quiz->sections as $sectionIndex => $section)
                        <div class="section-container mb-5">
                            {{-- Header de Section --}}
                            <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded-4 mb-3 border">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                        {{ $sectionIndex + 1 }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">{{ $section->titre }}</h6>
                                        @if($section->description)
                                            <span class="text-muted small">{{ $section->description }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                                        Options
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <button type="button" class="dropdown-item small" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editSectionModal"
                                                    data-section="{{ json_encode($section->only(['id', 'titre', 'description'])) }}"
                                                    data-action="{{ route('admin.sections.update', $section) }}">
                                                <i class="bi bi-pencil me-2"></i> Modifier la section
                                            </button>
                                        </li>
                                        @if($section->questions->count() == 0)
                                            <li>
                                                <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" onsubmit="return confirm('Supprimer cette section ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger small">
                                                        <i class="bi bi-trash me-2"></i> Supprimer
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>

                            {{-- Questions de la Section --}}
                            @forelse($section->questions as $qIndex => $question)
                                <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden border-start border-4 {{ $question->type == 'multiple' ? 'border-warning' : 'border-primary' }}">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge bg-light text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">
                                                    {{ $qIndex + 1 }}
                                                </span>
                                                <h6 class="fw-bold mb-0 text-dark">{{ $question->libelle }}</h6>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-light text-muted border px-2 py-1 x-small">
                                                    <i class="bi {{ $question->type == 'multiple' ? 'bi-check2-all' : 'bi-check2-circle' }} me-1"></i>
                                                    {{ $question->type == 'multiple' ? 'Multi' : 'Unique' }}
                                                </span>
                                                <span class="badge bg-light text-muted border px-2 py-1 x-small">
                                                    {{ $question->points }} pts
                                                </span>
                                                <div class="dropdown">
                                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                        <li>
                                                            <button type="button" class="dropdown-item small" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editQuestionModal" 
                                                                    data-question="{{ json_encode($question->only(['id', 'libelle', 'type', 'points', 'section_id'])) }}"
                                                                    data-options="{{ json_encode($question->options->map(fn($o) => $o->only(['libelle', 'is_correct']))) }}"
                                                                    data-action="{{ route('admin.questions.update', $question) }}">
                                                                <i class="bi bi-pencil me-2"></i> Modifier
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Supprimer cette question ?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger small">
                                                                    <i class="bi bi-trash me-2"></i> Supprimer
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-2 mt-1">
                                            @foreach($question->options as $option)
                                                <div class="col-md-6 col-xl-4">
                                                    <div class="p-2 rounded-3 border d-flex align-items-center gap-2 {{ $option->is_correct ? 'bg-success bg-opacity-10 border-success border-opacity-25' : 'bg-white' }}" style="font-size: 0.8rem;">
                                                        <i class="bi {{ $option->is_correct ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}" style="font-size: 0.7rem;"></i>
                                                        <span class="{{ $option->is_correct ? 'fw-bold text-success' : 'text-muted' }} text-truncate">{{ $option->libelle }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 border border-dashed rounded-4 text-muted small">
                                    Aucune question dans cette section.
                                </div>
                            @endforelse
                        </div>
                    @empty
                        <div class="card border-0 shadow-sm rounded-4 bg-light">
                            <div class="card-body p-5 text-center">
                                <i class="bi bi-folder-plus text-muted mb-3" style="font-size: 3rem;"></i>
                                <h5 class="fw-bold">Aucune section définie</h5>
                                <p class="text-muted">Créez d'abord une section pour pouvoir y ajouter des questions.</p>
                                <button type="button" class="btn btn-dark rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                                    <i class="bi bi-plus-lg me-2"></i> Créer ma première section
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Onglet 2 : Participation --}}
                <div class="tab-pane fade" id="participation-content" role="tabpanel">
                    {{-- ... (reste du contenu inchangé pour la participation) ... --}}

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                        <div class="card-body p-4 text-center">
                            <div class="display-5 fw-bold mb-1">{{ $stats['total_target'] }}</div>
                            <div class="small opacity-75">Agents ciblés</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
                        <div class="card-body p-4 text-center">
                            <div class="display-5 fw-bold mb-1">{{ $stats['total_responded'] }}</div>
                            <div class="small opacity-75">Ont répondu</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark">
                        <div class="card-body p-4 text-center">
                            <div class="display-5 fw-bold mb-1">{{ count($stats['pending']) }}</div>
                            <div class="small opacity-75">En attente</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-3">
                    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-4">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Suivi nominatif des officiels</h5>
                            <p class="text-muted small mb-0">Seuls les agents inscrits sur la liste maître apparaissent ici.</p>
                        </div>
                        <div class="d-flex flex-column flex-md-row flex-grow-1 justify-content-xl-end gap-3">
                            <div class="search-box">
                                <i class="bi bi-search search-icon text-muted"></i>
                                <input type="text" id="participationSearch" class="form-control form-control-modern" placeholder="Nom ou matricule...">
                            </div>
                            <div class="status-filters d-flex bg-light p-1 rounded-3">
                                <input type="radio" class="btn-check" name="statusFilter" id="filterAll" checked>
                                <label class="btn btn-sm btn-filter flex-grow-1" for="filterAll">Tous</label>

                                <input type="radio" class="btn-check" name="statusFilter" id="filterDone">
                                <label class="btn btn-sm btn-filter flex-grow-1" for="filterDone">Terminés</label>

                                <input type="radio" class="btn-check" name="statusFilter" id="filterPending">
                                <label class="btn btn-sm btn-filter flex-grow-1" for="filterPending">En attente</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="participationTable">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="ps-4 py-3 border-0 text-uppercase x-small fw-bold text-muted">Agent officiel</th>
                                    <th class="py-3 border-0 text-uppercase x-small fw-bold text-muted">Profil</th>
                                    <th class="py-3 border-0 text-uppercase x-small fw-bold text-muted text-center">État d'avancement</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['responded'] as $agent)
                                    <tr class="participation-row border-bottom border-light" data-status="done">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-sm bg-success bg-opacity-10 text-success fw-bold rounded-circle d-flex align-items-center justify-content-center">
                                                    {{ substr($agent->prenom, 0, 1) }}{{ substr($agent->nom, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold agent-name text-dark">{{ $agent->nom }} {{ $agent->prenom }}</div>
                                                    <div class="small text-muted agent-matricule">{{ $agent->matricule }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-secondary fw-medium border px-2 py-1">{{ $agent->profil->libelle ?? '-' }}</span></td>
                                        <td class="text-center">
                                            <div class="status-badge status-badge-success">
                                                <i class="bi bi-check-circle-fill"></i> Terminé
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach($stats['pending'] as $agent)
                                    <tr class="participation-row border-bottom border-light" data-status="pending">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-sm bg-light text-muted fw-bold rounded-circle d-flex align-items-center justify-content-center">
                                                    {{ substr($agent->prenom, 0, 1) }}{{ substr($agent->nom, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold agent-name text-dark">{{ $agent->nom }} {{ $agent->prenom }}</div>
                                                    <div class="small text-muted agent-matricule">{{ $agent->matricule }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-secondary fw-medium border px-2 py-1">{{ $agent->profil->libelle ?? '-' }}</span></td>
                                        <td class="text-center">
                                            <div class="status-badge status-badge-pending">
                                                <i class="bi bi-hourglass-split"></i> En attente
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
            </div>
            </div>

</div>

{{-- Modal Édition Question --}}
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Modifier la question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editQuestionForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Section de destination</label>
                        <select name="section_id" id="edit-section_id" class="form-select" required>
                            @foreach($quiz->sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->titre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Intitulé de la question</label>
                        <input type="text" name="libelle" id="edit-libelle" class="form-control" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Type de réponse</label>
                            <select name="type" id="edit-type" class="form-select" required>
                                <option value="unique">Choix unique (Radio)</option>
                                <option value="multiple">Choix multiples (Checkbox)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Points attribués</label>
                            <input type="number" name="points" id="edit-points" class="form-control" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold mb-0">Réponses possibles</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-option-edit">
                                <i class="bi bi-plus-lg"></i> Ajouter une réponse
                            </button>
                        </div>
                        <div id="options-container-edit">
                            {{-- Lignes injectées via JS --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Ajout Question --}}
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Nouvelle question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.quizzes.questions.store', $quiz) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Section de destination</label>
                        <select name="section_id" class="form-select" required>
                            @foreach($quiz->sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->titre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Intitulé de la question</label>
                        <input type="text" name="libelle" class="form-control" placeholder="Saisissez votre question..." required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Type de réponse</label>
                            <select name="type" class="form-select" required>
                                <option value="unique">Choix unique (Radio)</option>
                                <option value="multiple">Choix multiples (Checkbox)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Points attribués</label>
                            <input type="number" name="points" class="form-control" value="1" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold mb-0">Réponses possibles</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-option">
                                <i class="bi bi-plus-lg"></i> Ajouter une réponse
                            </button>
                        </div>
                        <div id="options-container">
                            {{-- Lignes de réponses injectées en JS --}}
                            <div class="option-row mb-2 d-flex align-items-center gap-3 bg-light p-3 rounded-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="options[0][is_correct]" value="1">
                                </div>
                                <input type="text" name="options[0][libelle]" class="form-control" placeholder="Option 1" required>
                                <button type="button" class="btn btn-link text-danger remove-option p-0"><i class="bi bi-trash fs-5"></i></button>
                            </div>
                            <div class="option-row mb-2 d-flex align-items-center gap-3 bg-light p-3 rounded-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="options[1][is_correct]" value="1">
                                </div>
                                <input type="text" name="options[1][libelle]" class="form-control" placeholder="Option 2" required>
                                <button type="button" class="btn btn-link text-danger remove-option p-0"><i class="bi bi-trash fs-5"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Enregistrer la question</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Ajout Section --}}
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Nouvelle section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.quizzes.sections.store', $quiz) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Titre de la section</label>
                        <input type="text" name="titre" class="form-control" placeholder="Ex: Culture Générale" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Description (optionnel)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Texte d'introduction..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold shadow-sm">Créer la section</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Édition Section --}}
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Modifier la section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editSectionForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Titre de la section</label>
                        <input type="text" name="titre" id="edit-section-titre" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Description (optionnel)</label>
                        <textarea name="description" id="edit-section-description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .search-box { position: relative; min-width: 250px; }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); z-index: 5; }
    .form-control-modern { padding-left: 35px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 0.9rem; transition: all 0.2s; }
    .form-control-modern:focus { background: white; border-color: #065f46; box-shadow: 0 0 0 3px rgba(6, 95, 70, 0.1); }
    
    .btn-filter { border: none; border-radius: 8px !important; color: #64748b; font-weight: 600; padding: 6px 15px; transition: all 0.2s; }
    .btn-check:checked + .btn-filter { background: white !important; color: #065f46 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    
    .avatar-sm { width: 38px; height: 38px; font-size: 0.8rem; }
    
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .status-badge-success { background: #ecfdf5; color: #065f46; }
    .status-badge-pending { background: #fff7ed; color: #9a3412; }
    
    .table-hover tbody tr:hover { background-color: #f8fafc !important; }
    .x-small { font-size: 0.7rem; }
    .table-light-warning { background-color: #fffbeb !important; }
    .text-truncate-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endpush

@push('scripts')
<script>
    function copyToClipboard() {
        const urlField = document.getElementById('quizUrl');
        urlField.select();
        urlField.setSelectionRange(0, 99999); // Pour mobile
        navigator.clipboard.writeText(urlField.value).then(() => {
            const feedback = document.getElementById('copyFeedback');
            feedback.style.display = 'block';
            setTimeout(() => {
                feedback.style.display = 'none';
            }, 2000);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des options de question
        const container = document.getElementById('options-container');
        const addButton = document.getElementById('add-option');
        let optionCount = 2;

        if (addButton) {
            addButton.addEventListener('click', function() {
                const row = document.createElement('div');
                row.className = 'option-row mb-2 d-flex align-items-center gap-3 bg-light p-3 rounded-3';
                row.innerHTML = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="options[${optionCount}][is_correct]" value="1">
                    </div>
                    <input type="text" name="options[${optionCount}][libelle]" class="form-control" placeholder="Option ${optionCount + 1}" required>
                    <button type="button" class="btn btn-link text-danger remove-option p-0"><i class="bi bi-trash fs-5"></i></button>
                `;
                container.appendChild(row);
                optionCount++;
            });
        }

        if (container) {
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-option')) {
                    const rows = container.querySelectorAll('.option-row');
                    if (rows.length > 2) {
                        e.target.closest('.option-row').remove();
                    } else {
                        alert('Une question doit avoir au moins 2 options.');
                    }
                }
            });
        }

        // Gestion de l'édition Question
        const editQuestionModal = document.getElementById('editQuestionModal');
        const editContainer = document.getElementById('options-container-edit');
        const addOptionEditBtn = document.getElementById('add-option-edit');
        let optionCountEdit = 0;

        if (editQuestionModal) {
            editQuestionModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const question = JSON.parse(button.getAttribute('data-question'));
                const options = JSON.parse(button.getAttribute('data-options'));
                const action = button.getAttribute('data-action');

                document.getElementById('editQuestionForm').action = action;
                document.getElementById('edit-libelle').value = question.libelle;
                document.getElementById('edit-type').value = question.type;
                document.getElementById('edit-points').value = question.points;
                document.getElementById('edit-section_id').value = question.section_id;

                editContainer.innerHTML = '';
                optionCountEdit = 0;

                options.forEach(opt => {
                    addOptionRowEdit(opt.libelle, opt.is_correct);
                });
            });
        }

        function addOptionRowEdit(libelle = '', isCorrect = false) {
            const row = document.createElement('div');
            row.className = 'option-row mb-2 d-flex align-items-center gap-3 bg-light p-3 rounded-3';
            row.innerHTML = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="options[${optionCountEdit}][is_correct]" value="1" ${isCorrect ? 'checked' : ''}>
                </div>
                <input type="text" name="options[${optionCountEdit}][libelle]" class="form-control" placeholder="Option" value="${libelle}" required>
                <button type="button" class="btn btn-link text-danger remove-option-edit p-0"><i class="bi bi-trash fs-5"></i></button>
            `;
            editContainer.appendChild(row);
            optionCountEdit++;
        }

        if (addOptionEditBtn) {
            addOptionEditBtn.addEventListener('click', () => addOptionRowEdit());
        }

        if (editContainer) {
            editContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-option-edit')) {
                    const rows = editContainer.querySelectorAll('.option-row');
                    if (rows.length > 2) {
                        e.target.closest('.option-row').remove();
                    } else {
                        alert('Une question doit avoir au moins 2 options.');
                    }
                }
            });
        }

        // Gestion de l'édition Section
        const editSectionModal = document.getElementById('editSectionModal');
        if (editSectionModal) {
            editSectionModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const section = JSON.parse(button.getAttribute('data-section'));
                const action = button.getAttribute('data-action');

                document.getElementById('editSectionForm').action = action;
                document.getElementById('edit-section-titre').value = section.titre;
                document.getElementById('edit-section-description').value = section.description || '';
            });
        }

        // Validation avant soumission
        const validateForm = (form) => {
            const corrects = form.querySelectorAll('input[type="checkbox"][name*="[is_correct]"]:checked');
            if (corrects.length === 0) {
                alert('Attention : Vous devez cocher au moins une réponse juste.');
                return false;
            }
            return true;
        };

        const addQModal = document.getElementById('addQuestionModal');
        if (addQModal) {
            addQModal.querySelector('form').onsubmit = function() {
                return validateForm(this);
            };
        }

        const editQForm = document.getElementById('editQuestionForm');
        if (editQForm) {
            editQForm.onsubmit = function() {
                return validateForm(this);
            };
        }

        // Filtrage dynamique Participation
        const searchInput = document.getElementById('participationSearch');
        const statusFilters = document.querySelectorAll('input[name="statusFilter"]');
        const tableRows = document.querySelectorAll('.participation-row');

        function filterParticipation() {
            const searchTerm = searchInput.value.toLowerCase();
            const activeFilter = document.querySelector('input[name="statusFilter"]:checked').id;

            tableRows.forEach(row => {
                const name = row.querySelector('.agent-name').textContent.toLowerCase();
                const matricule = row.querySelector('.agent-matricule').textContent.toLowerCase();
                const status = row.getAttribute('data-status');

                let matchesSearch = name.includes(searchTerm) || matricule.includes(searchTerm);
                let matchesStatus = true;

                if (activeFilter === 'filterDone') matchesStatus = (status === 'done');
                if (activeFilter === 'filterPending') matchesStatus = (status === 'pending');

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterParticipation);
            statusFilters.forEach(radio => radio.addEventListener('change', filterParticipation));
        }
    });
</script>
@endpush
@endsection
