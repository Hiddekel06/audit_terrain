@extends('layouts.admin')

@section('admin-title', 'Recherche opérationnelle')
@section('admin-subtitle', 'Outils d\'optimisation et de simulation')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                    <i class="bi bi-gear-wide-connected text-warning fs-2"></i>
                </div>
                <div>
                    <h2 class="h4 fw-bold mb-1">Module de recherche opérationnelle</h2>
                    <p class="text-muted mb-0">Cette section accueillera les modèles d\'optimisation et les simulations.</p>
                </div>
            </div>

            <div class="alert alert-info border-0 mb-0" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>En cours de déploiement</strong>
            </div>
        </div>
    </div>
</div>
@endsection
