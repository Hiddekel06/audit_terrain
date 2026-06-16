@extends('layouts.admin')

@section('admin-title', 'Nouveau Quiz')
@section('admin-subtitle', 'Définissez les bases de votre évaluation')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Informations générales</h5>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.quizzes.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="titre" class="form-label fw-medium text-dark">Titre du Quiz <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" placeholder="Ex: Aptitude Auditeur IT - Session Juin" value="{{ old('titre') }}" required>
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark d-block mb-3">Profils cibles</label>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="form-text mb-3"><i class="bi bi-info-circle me-1"></i> Sélectionnez les profils autorisés à passer ce quiz. Laissez vide pour "Tous les profils".</div>
                                        <div class="row row-cols-1 row-cols-md-2 g-2">
                                            @foreach($profils as $profil)
                                                <div class="col">
                                                    <div class="form-check custom-checkbox p-2 border rounded-2 bg-white">
                                                        <input class="form-check-input ms-1" type="checkbox" name="profil_ids[]" value="{{ $profil->id }}" id="profil_{{ $profil->id }}" {{ is_array(old('profil_ids')) && in_array($profil->id, old('profil_ids')) ? 'checked' : '' }}>
                                                        <label class="form-check-label ms-2 fw-medium" for="profil_{{ $profil->id }}">
                                                            {{ $profil->libelle }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('profil_ids')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium text-dark">Description / Consignes</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Présentez le quiz ou donnez des consignes aux agents...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch p-3 bg-light rounded-3">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium text-dark" for="is_active">Activer immédiatement après création</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="{{ route('admin.quizzes.index') }}" class="btn btn-light text-muted px-4 rounded-pill">
                                <i class="bi bi-x-lg me-2"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
                                Suivant <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
