@extends('layouts.admin')

@section('admin-title', "Rapports d'import")
@section('admin-subtitle', 'Fichiers CSV des lignes ignorées lors des imports')

@section('content')
    <div class="container-fluid p-0 pb-5">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="glass-card p-4">
                    <h4 class="mb-3">Rapports d'import</h4>
                    @if(count($files) === 0)
                        <div class="text-muted">Aucun rapport trouvé dans <code>storage/app/imports</code>.</div>
                    @else
                        <ul class="list-group">
                            @foreach($files as $f)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $f['name'] }}</div>
                                        <div class="small text-muted">{{ date('Y-m-d H:i:s', $f['mtime']) }} — {{ number_format($f['size'] / 1024, 2) }} KB</div>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.import_reports.download', ['filename' => $f['name']]) }}" class="btn btn-sm btn-modern-outline me-2">Télécharger</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
