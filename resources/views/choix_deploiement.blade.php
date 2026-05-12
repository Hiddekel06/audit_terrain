@extends('layouts.app')

@section('content')
@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300..700&display=swap');
        .deploy-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            background: linear-gradient(135deg, #f6fbf6 0%, #eef7ee 100%);
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }

        .deploy-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e6efe6;
            width: 100%;
            max-width: 720px;
            padding: 1.3rem 1.6rem 1.6rem;
            box-shadow: 0 8px 20px rgba(31, 63, 31, 0.06);
            text-align: left;
        }

        .deploy-eyebrow {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0.45rem;
        }

        .deploy-eyebrow__dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4a8c5c;
            flex-shrink: 0;
        }

        .deploy-title {
            font-size: 20px;
            font-weight: 700;
            color: #163215;
            margin: 0 0 0.35rem 0;
        }

        .deploy-subtitle {
            color: #557b56;
            font-size: 14px;
            margin-bottom: 1rem;
            line-height: 1.45;
        }

        .decision-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 1rem;
        }

        .id-btn {
            height: 52px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
            width: 100%;
        }

        .id-btn--primary {
            background: linear-gradient(135deg, #2e7d32 0%, #4caf50 55%);
            color: #fff;
            box-shadow: 0 10px 22px rgba(46,125,50,0.12);
        }

        .id-btn--secondary {
            background: #f6fbf6;
            color: #2f5f3d;
            border: 1px solid #e6efe6;
        }

        .id-btn:hover { transform: translateY(-2px); }

        @media (max-width: 640px) {
            .decision-row { grid-template-columns: 1fr; }
        }
    </style>
@endpush

<div class="deploy-wrap">
    <div class="deploy-card">
        <div class="deploy-eyebrow">
            <span class="deploy-eyebrow__dot"></span>
            <span class="text-uppercase" style="font-size:11px;color:#4a8c5c;font-weight:600;letter-spacing:0.06em;">Portail interne</span>
        </div>

        <h2 class="deploy-title">Êtes-vous prêt(e) à être déployé(e) dans toutes les régions ?</h2>
        <p class="deploy-subtitle">Choisissez « Oui » si vous êtes disponible pour être affecté(e) sur l'ensemble du territoire. Choisissez « Non » si vous souhaitez indiquer une seule région.</p>

        <form method="POST" action="{{ route('user_region_choice.decision.store') }}" class="decision-form">
            @csrf
            <div class="decision-row">
                <button type="submit" name="ready_to_deploy" value="yes" class="id-btn id-btn--primary">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 10.8L3.2 8l-1 1.1L6 13l8-8-1-1.1L6 10.8z" fill="currentColor"/></svg>
                    Oui, je suis prêt(e)
                </button>

                <button type="submit" name="ready_to_deploy" value="no" class="id-btn id-btn--secondary">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 3l6 5-6 5V3z" fill="currentColor"/></svg>
                    Non, je choisis une région
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
