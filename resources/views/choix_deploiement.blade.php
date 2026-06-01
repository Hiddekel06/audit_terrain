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
            position: relative;
        }

        .deploy-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e6efe6;
            width: 100%;
            max-width: 720px;
            padding: 1.3rem 1.6rem 1.6rem;
            position: relative;
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
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 1rem;
        }

        .id-btn {
            height: 52px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease, background-color 0.16s ease, color 0.16s ease;
            width: 100%;
            letter-spacing: 0.01em;
            background: #fbfdfb;
        }

        .id-btn--centered {
            max-width: 320px;
        }

        .id-btn--primary {
            background: #f4faf4;
            color: #24552b;
            border-color: #cfe2cf;
            box-shadow: 0 6px 14px rgba(31, 63, 31, 0.06);
        }

        .id-btn--secondary {
            background: #ffffff;
            color: #385b43;
            border-color: #dde8dd;
            box-shadow: 0 6px 14px rgba(31, 63, 31, 0.04);
        }

        .id-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(31, 63, 31, 0.10);
        }

        .id-btn--primary:hover {
            background: #eef6ee;
            border-color: #b9d4ba;
            color: #1f4a25;
        }

        .id-btn--secondary:hover {
            background: #f8fbf8;
            border-color: #cfd9cf;
            color: #284f31;
        }

        @media (max-width: 640px) {
            .decision-row {
                flex-direction: column;
            }

            .id-btn--centered {
                max-width: none;
            }
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 12px;
            background: #ffffff;
            color: #2f5f3d;
            border: 2px solid #2f5f3d;
            font-weight: 700;
            font-size: 14px;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(31,63,31,0.15);
            cursor: pointer;
            margin-bottom: 12px;
        }

        .back-btn:hover { 
            background: #f0f7f0;
            border-color: #1d3a1f;
            transform: translateX(-2px);
        }
    </style>
@endpush

<div class="deploy-wrap">
    <div class="deploy-card">
        <div class="deploy-eyebrow">
            <span class="deploy-eyebrow__dot"></span>
            <span class="text-uppercase" style="font-size:11px;color:#4a8c5c;font-weight:600;letter-spacing:0.06em;"></span>
        </div>

        <button type="button" class="back-btn" data-back-button aria-label="Retour">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L4 8l6 5"/></svg>
            Retour
        </button>

        <h2 class="deploy-title">Êtes-vous prêt(e) à être déployé(e) dans toutes les régions ?</h2>
        <p class="deploy-subtitle">Cliquez sur « Oui » pour confirmer votre disponibilité sur l'ensemble du territoire national.</p>

        <form method="POST" action="{{ route('user_region_choice.decision.store') }}" class="decision-form">
            @csrf
            <div class="decision-row">
                <button type="submit" name="ready_to_deploy" value="yes" class="id-btn id-btn--primary id-btn--centered">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 10.8L3.2 8l-1 1.1L6 13l8-8-1-1.1L6 10.8z" fill="currentColor"/></svg>
                    Oui, je confirme
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const backBtn = document.querySelector('[data-back-button]');
        backBtn?.addEventListener('click', function () {
            // go back to previous page in history
            if (window.history.length > 1) {
                window.history.back();
            } else {
                // fallback: redirect to home
                window.location.href = '{{ url("/") }}';
            }
        });
    });
</script>

@endsection
