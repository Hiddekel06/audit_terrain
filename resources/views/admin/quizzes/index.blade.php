@extends('layouts.admin')

@section('admin-title', 'Évaluations QCM')
@section('admin-subtitle', 'Gérez les questionnaires pour les formations')

@section('admin-actions')
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Nouveau Quiz</span>
    </a>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Configuration Globale --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 bg-primary bg-opacity-10">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h5 class="fw-bold text-primary mb-2">
                        <i class="bi bi-gear-fill me-2"></i> Configuration de la session de formation
                    </h5>
                    <p class="text-muted mb-lg-0">Définissez le mot de passe que les agents devront saisir pour accéder aux tests via le QR Code.</p>
                </div>
                <div class="col-lg-4">
                    <form action="{{ route('admin.quizzes.settings.update') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <div class="flex-grow-1">
                            <input type="text" name="training_default_password" class="form-control border-primary border-opacity-25" value="{{ $trainingPassword }}" placeholder="Mot de passe formation">
                        </div>
                        <button type="submit" class="btn btn-primary px-4">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($quizzes as $quiz)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="badge {{ $quiz->is_active ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                                {{ $quiz->is_active ? 'Actif' : 'Inactif' }}
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical fs-5"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('admin.quizzes.show', $quiz) }}"><i class="bi bi-eye me-2"></i> Voir les questions</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.quizzes.edit', $quiz) }}"><i class="bi bi-pencil me-2"></i> Modifier</a></li>
                                    <li>
                                        <form action="{{ route('admin.quizzes.toggle', $quiz) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi {{ $quiz->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-2"></i>
                                                {{ $quiz->is_active ? 'Désactiver' : 'Activer' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" onsubmit="return confirm('Supprimer ce Quiz ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i> Supprimer
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <h5 class="card-title fw-bold mb-2">{{ $quiz->titre }}</h5>
                        <p class="card-text text-muted small mb-4 text-truncate-2" style="height: 3rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            {{ $quiz->description ?? 'Aucune description.' }}
                        </p>

                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="d-flex align-items-center gap-2 text-dark bg-light px-3 py-2 rounded-3">
                                <i class="bi bi-list-check fs-5 text-primary"></i>
                                <span class="fw-bold">{{ $quiz->questions_count }}</span>
                                <span class="small text-muted">questions</span>
                            </div>
                            @if($quiz->profils->isNotEmpty())
                                <div class="d-flex align-items-center gap-2 text-dark bg-light px-3 py-2 rounded-3" title="{{ $quiz->profils->pluck('libelle')->implode(', ') }}">
                                    <i class="bi bi-person-badge fs-5 text-success"></i>
                                    <span class="small fw-medium">
                                        {{ $quiz->profils->count() }} profil(s)
                                    </span>
                                </div>
                            @else
                                <div class="d-flex align-items-center gap-2 text-dark bg-light px-3 py-2 rounded-3">
                                    <i class="bi bi-people fs-5 text-secondary"></i>
                                    <span class="small fw-medium">Tous les profils</span>
                                </div>
                            @endif
                        </div>

                        <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="text-muted x-small">Créé le {{ $quiz->created_at->format('d/m/Y') }}</span>
                            <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill">
                                Gérer <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="mb-3 text-muted">
                    <i class="bi bi-patch-question" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold">Aucun Quiz pour le moment</h4>
                <p class="text-muted">Commencez par créer votre premier questionnaire de formation.</p>
                <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-lg me-2"></i> Créer un Quiz
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
