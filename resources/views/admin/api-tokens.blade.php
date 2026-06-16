@extends('layouts.admin')

@section('admin-title', 'Configuration API')
@section('admin-subtitle', 'Gestion des accès pour les plateformes tierces')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                <div>
                    <strong>Succès !</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('plainTextToken'))
        <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <h5 class="alert-heading mb-0">Nouveau Token Généré</h5>
            </div>
            <p>Veuillez copier ce token maintenant. Pour des raisons de sécurité, <strong>il ne sera plus jamais affiché</strong>.</p>
            <div class="input-group">
                <input type="text" class="form-control font-monospace bg-white" id="newToken" value="{{ session('plainTextToken') }}" readonly>
                <button class="btn btn-dark" type="button" onclick="copyToken()">
                    <i class="bi bi-clipboard me-2"></i>Copier
                </button>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Création de Token --}}
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Générer un accès</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">
                        Donnez un nom descriptif à la plateforme ou à l'application qui utilisera cette API (ex: "Plateforme RH", "Application Mobile").
                    </p>
                    <form action="{{ route('admin.api.tokens.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nom de la plateforme</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="ex: Plateforme Regionale" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 rounded-3 fw-bold">
                                <i class="bi bi-key me-2"></i>Générer le Token
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Liste des accès --}}
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2 text-success"></i>Accès Actifs</h5>
                    <span class="badge bg-light text-dark">{{ $tokens->count() }} plateforme(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-0">Plateforme / Application</th>
                                    <th class="py-3 border-0">Dernière utilisation</th>
                                    <th class="py-3 border-0">Créé le</th>
                                    <th class="pe-4 py-3 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tokens as $token)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                                    <i class="bi bi-hdd-network text-success"></i>
                                                </div>
                                                <span class="fw-semibold text-dark">{{ $token->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if($token->last_used_at)
                                                <span class="text-dark">{{ $token->last_used_at->diffForHumans() }}</span>
                                            @else
                                                <span class="badge bg-light text-muted fw-normal">Jamais utilisé</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-muted">
                                            {{ $token->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <form action="{{ route('admin.api.tokens.destroy', $token->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir révoquer cet accès ? Cette action est irréversible.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                    <i class="bi bi-trash me-1"></i>Révoquer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-5 text-center text-muted">
                                            <i class="bi bi-inbox fs-1 mb-3 d-block opacity-25"></i>
                                            Aucun token API généré pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Documentation Rapide --}}
    <div class="card border-0 shadow-sm rounded-4 mt-4 bg-dark text-white overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-bold mb-3"><i class="bi bi-journal-code me-2 text-warning"></i>Guide d'utilisation Rapide</h5>
                    <p class="text-white text-opacity-75 mb-0">
                        Pour utiliser cet accès, la plateforme distante doit inclure le token dans le header HTTP :<br>
                        <code>Authorization: Bearer <span class="text-warning">VOTRE_TOKEN</span></code>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="/api/v1/agents" target="_blank" class="btn btn-outline-light rounded-pill px-4">
                        <i class="bi bi-eye me-2"></i>Tester l'endpoint (JSON)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyToken() {
        var copyText = document.getElementById("newToken");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        alert("Token copié dans le presse-papier !");
    }
</script>
@endpush
