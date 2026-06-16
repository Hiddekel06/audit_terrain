@extends('layouts.admin')

@section('admin-title', 'Gestion des Photos')
@section('admin-subtitle', 'Importez et gérez les photos de profil des agents')

@section('content')
<div class="container-fluid px-4">
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="glass-card p-4 border-start border-4 border-primary">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-camera text-primary fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Agents avec Photo</h6>
                        <h4 class="mb-0 fw-bold">{{ $totalPhotos }} / {{ $totalUsers }}</h4>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $totalUsers > 0 ? ($totalPhotos/$totalUsers)*100 : 0 }}%"></div>
                        </div>
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

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="glass-card p-5 text-center">
                <div class="mb-4">
                    <div class="bg-primary bg-opacity-10 d-inline-flex p-4 rounded-circle mb-3">
                        <i class="bi bi-file-earmark-zip fs-1 text-primary"></i>
                    </div>
                    <h4 class="fw-bold">Importation de masse (ZIP)</h4>
                    <p class="text-muted mx-auto" style="max-width: 500px;">
                        Préparez un dossier contenant les photos de vos agents. 
                        <strong>Important :</strong> Nommez chaque fichier par le <strong>Matricule</strong> de l'agent (ex: <code>123456A.jpg</code>).
                    </p>
                </div>

                <form action="{{ route('admin.photos.import_zip') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="upload-zone border-dashed border-2 rounded-4 p-5 mb-4 bg-light bg-opacity-50 cursor-pointer" id="dropZone">
                        <input type="file" name="zip_file" class="form-control d-none" id="zipFile" accept=".zip" required onchange="updateFileName(this)">
                        <label for="zipFile" class="cursor-pointer mb-0">
                            <i class="bi bi-cloud-arrow-up fs-2 text-primary opacity-50 mb-2 d-block"></i>
                            <span class="fw-bold text-dark d-block" id="fileNameDisplay">Cliquez ou glissez votre fichier .ZIP ici</span>
                            <span class="text-muted small">Taille max : 50 Mo</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-modern-primary px-5 py-3">
                        <i class="bi bi-lightning-charge-fill me-2"></i> Lancer l'association automatique
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-4">Instructions d'import</h5>
                <ul class="list-unstyled small">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-1-circle-fill text-primary me-2 mt-1"></i>
                        <span>Compressez vos photos directement (ne compressez pas le dossier parent, mais les fichiers eux-mêmes).</span>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-2-circle-fill text-primary me-2 mt-1"></i>
                        <span>Formats supportés : <strong>.JPG, .PNG, .WEBP</strong>.</span>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-3-circle-fill text-primary me-2 mt-1"></i>
                        <span>Le nom du fichier doit correspondre EXACTEMENT au matricule saisi en base (ex: 695130D.jpg).</span>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-info-circle-fill text-info me-2 mt-1"></i>
                        <span>Si un agent a déjà une photo, elle sera remplacée par la nouvelle.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        const display = document.getElementById('fileNameDisplay');
        if (input.files.length > 0) {
            display.textContent = input.files[0].name;
            display.classList.add('text-primary', 'fw-bold');
        }
    }
</script>

<style>
    .upload-zone { transition: all 0.3s ease; border-color: #cbd5e1; }
    .upload-zone:hover { border-color: #2563eb; background: rgba(37, 99, 235, 0.05) !important; }
    .cursor-pointer { cursor: pointer; }
    .btn-modern-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: white; border: none; border-radius: 0.75rem; font-weight: 600;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
</style>
@endsection
