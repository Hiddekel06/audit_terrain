@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100 py-5" style="background: linear-gradient(135deg, #eef5e8 0%, #e0f0e0 100%);">
    <div class="modern-card" style="max-width: 560px; width: 100%; padding: 2rem 1.8rem 2.2rem; text-align: center;">
        <h2 class="form-title mb-3">Êtes-vous prêts à être déployés dans toutes les régions du Sénegal?</h2>


        <form method="POST" action="{{ route('user_region_choice.decision.store') }}" class="decision-form">
            @csrf
            <button type="submit" name="ready_to_deploy" value="yes" class="decision-btn decision-btn--primary">
                <span class="decision-btn__icon">✓</span>
                <span>Oui, je suis prêt</span>
            </button>
            <button type="submit" name="ready_to_deploy" value="no" class="decision-btn decision-btn--secondary">
                <span class="decision-btn__icon">→</span>
                <span>Non, je choisis une région</span>
            </button>
        </form>
    </div>
</div>

<style>
    .decision-form {
        display: grid;
        gap: 14px;
        margin-top: 1.25rem;
    }

    .decision-btn {
        appearance: none;
        border: 0;
        border-radius: 18px;
        min-height: 58px;
        padding: 0.95rem 1.1rem;
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        cursor: pointer;
        transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease, opacity 0.18s ease;
        letter-spacing: 0.01em;
        width: 100%;
    }

    .decision-btn:hover {
        transform: translateY(-2px);
    }

    .decision-btn:active {
        transform: translateY(0);
    }

    .decision-btn__icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        line-height: 1;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.18);
    }

    .decision-btn--primary {
        color: #fff;
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 55%, #66bb6a 100%);
        box-shadow: 0 14px 28px rgba(46, 125, 50, 0.24), 0 2px 6px rgba(46, 125, 50, 0.16);
    }

    .decision-btn--primary:hover {
        box-shadow: 0 18px 34px rgba(46, 125, 50, 0.28), 0 4px 10px rgba(46, 125, 50, 0.18);
    }

    .decision-btn--secondary {
        color: #23402a;
        background: linear-gradient(135deg, #ffffff 0%, #f4fbf4 100%);
        border: 1px solid #cfe0d1;
        box-shadow: 0 10px 22px rgba(26, 46, 26, 0.08);
    }

    .decision-btn--secondary .decision-btn__icon {
        background: #e7f3e7;
        color: #2e7d32;
    }

    .decision-btn--secondary:hover {
        border-color: #b9d2bc;
        box-shadow: 0 14px 28px rgba(26, 46, 26, 0.12);
    }

    .decision-btn:focus-visible {
        outline: 3px solid rgba(46, 125, 50, 0.18);
        outline-offset: 2px;
    }
</style>
@endsection
