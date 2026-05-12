@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100 py-5" style="background: linear-gradient(135deg, #eef5e8 0%, #e0f0e0 100%);">
    <div class="modern-card">
        @php
            $singleRegionSelection = (bool) ($singleRegionSelection ?? false);
        @endphp

        <div class="step-indicator">
            <div class="step active"></div>
            @if(!$singleRegionSelection)
                <div class="step"></div>
                <div class="step"></div>
            @endif
        </div>
        <h2 class="form-title">Choisissez votre région</h2>
        <p class="form-subtitle">
            {{ $singleRegionSelection ? 'Sélectionnez la région de votre choix pour l’audit.' : 'Sélectionnez la zone où vous souhaitez être déployé pour l’audit.' }}
        </p>

        @if ($errors->any())
            <div class="alert-custom" role="alert">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="alert-content">
                    <strong>Veuillez corriger les erreurs suivantes :</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="alert-close" aria-label="Fermer">&times;</button>
            </div>
        @endif

        <form method="POST" action="{{ route('user_region_choice.store') }}" id="regionForm" data-single-region-selection="{{ $singleRegionSelection ? 1 : 0 }}">
            @csrf
            @if($singleRegionSelection)
                <div class="choice-group" data-choice="1">
                    <div class="choice-header">
                        <label for="choix_1" class="form-label">Région <span class="required-star">*</span></label>
                        <span class="choice-badge">1/1</span>
                    </div>
                    <div class="custom-select-wrapper">
                        <select class="form-select modern-select" id="choix_1" name="choix_1" required data-region-select>
                            <option value="">Sélectionnez une région</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->nom }}</option>
                            @endforeach
                        </select>
                        <div class="select-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>
                    <div class="motivation-wrapper mt-2">
                        <label class="form-label mb-1">Motivation(s) pour ce choix :</label>
                        <div class="row g-2 align-items-center">
                            @foreach($motivations as $motivation)
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input motivation-type" type="checkbox" name="motivations_1[]" value="{{ $motivation->id }}" id="motivation_1_{{ $motivation->id }}">
                                        <label class="form-check-label" for="motivation_1_{{ $motivation->id }}">{{ $motivation->libelle }}</label>
                                    </div>
                                </div>
                            @endforeach
                            {{-- Champ de motivation libre retiré (non requis) --}}
                        </div>
                    </div>
                </div>
            @else
                @for ($i = 1; $i <= 3; $i++)
                    <div class="choice-group" data-choice="{{ $i }}">
                        <div class="choice-header">
                            <label for="choix_{{ $i }}" class="form-label">Choix {{ $i }} <span class="required-star">*</span></label>
                            <span class="choice-badge">{{ $i }}/3</span>
                        </div>
                        <div class="custom-select-wrapper">
                            <select class="form-select modern-select" id="choix_{{ $i }}" name="choix_{{ $i }}" required data-region-select>
                                <option value="">Sélectionnez une région</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->nom }}</option>
                                @endforeach
                            </select>
                            <div class="select-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>
                        <div class="motivation-wrapper mt-2">
                            <label class="form-label mb-1">Motivation(s) pour ce choix :</label>
                            <div class="row g-2 align-items-center">
                                @foreach($motivations as $motivation)
                                    <div class="col-auto">
                                        <div class="form-check">
                                            <input class="form-check-input motivation-type" type="checkbox" name="motivations_{{ $i }}[]" value="{{ $motivation->id }}" id="motivation_{{ $i }}_{{ $motivation->id }}">
                                            <label class="form-check-label" for="motivation_{{ $i }}_{{ $motivation->id }}">{{ $motivation->libelle }}</label>
                                        </div>
                                    </div>
                                @endforeach
                                {{-- Champ de motivation libre retiré (non requis) --}}
                            </div>
                        </div>
                    </div>
                @endfor
            @endif
            <button type="submit" class="btn-modern">
                <span>{{ $singleRegionSelection ? 'Valider mon choix' : 'Valider mes choix' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap');

    * {
        font-family: 'Inter', sans-serif;
    }

    .modern-card {
        max-width: 560px;
        width: 100%;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(0px);
        border-radius: 32px;
        padding: 2rem 1.8rem 2.2rem;
        box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.02);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: cardAppear 0.5s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }

    @keyframes cardAppear {
        0% {
            opacity: 0;
            transform: scale(0.96) translateY(10px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .modern-card:hover {
        box-shadow: 0 25px 40px -14px rgba(0, 0, 0, 0.15);
    }

    .step-indicator {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
    }

    .step {
        height: 4px;
        flex: 1;
        background-color: #e2e8f0;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .step.active {
        background: linear-gradient(90deg, #2e7d32, #66bb6a);
        background-size: 200% auto;
        animation: shimmer 1.2s ease;
    }

    @keyframes shimmer {
        0% { background-position: 0% 0; }
        100% { background-position: 200% 0; }
    }

    .form-title {
        font-size: 1.75rem;
        font-weight: 700;
        background: linear-gradient(135deg, #1b5e20, #2e7d32);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 0.5rem;
        letter-spacing: -0.3px;
    }

    .form-subtitle {
        font-size: 0.9rem;
        color: #5b6b8c;
        margin-bottom: 1.75rem;
        line-height: 1.4;
    }

    .choice-group {
        margin-bottom: 1.8rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #edf2f7;
    }

    .choice-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 8px;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.95rem;
        color: #1e293b;
        letter-spacing: -0.2px;
    }

    .required-star {
        color: #ef4444;
        font-size: 1rem;
    }

    .choice-badge {
        font-size: 0.7rem;
        background: #e8f5e9;
        padding: 2px 8px;
        border-radius: 30px;
        color: #1e5e1e;
        font-weight: 500;
    }

    .custom-select-wrapper {
        position: relative;
        margin-bottom: 12px;
    }

    .modern-select {
        appearance: none;
        -webkit-appearance: none;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 12px 38px 12px 18px;
        font-size: 0.95rem;
        color: #0f172a;
        width: 100%;
        transition: all 0.2s ease;
        cursor: pointer;
        font-weight: 500;
    }

    .modern-select:focus {
        outline: none;
        border-color: #66bb6a;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.15);
    }

    .select-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #94a3b8;
        transition: transform 0.2s;
    }

    .modern-select:focus + .select-icon {
        transform: translateY(-50%) rotate(180deg);
        color: #2e7d32;
    }

    .motivation-wrapper {
        position: relative;
        margin-top: 6px;
    }

    .modern-textarea {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 12px 18px;
        font-size: 0.9rem;
        width: 100%;
        resize: vertical;
        transition: all 0.2s;
        background-color: #fefefe;
    }

    .modern-textarea:focus {
        outline: none;
        border-color: #66bb6a;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }

    .char-counter {
        text-align: right;
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 6px;
        margin-bottom: 6px;
    }

    .input-hint {
        font-size: 0.7rem;
        color: #6c7a91;
        display: block;
        margin-top: 4px;
    }

    .btn-modern {
        background: linear-gradient(105deg, #2e7d32 0%, #4caf50 100%);
        border: none;
        border-radius: 40px;
        padding: 14px 24px;
        font-weight: 600;
        font-size: 1rem;
        color: white;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.25);
        margin-top: 12px;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 22px -8px rgba(46, 125, 50, 0.4);
        background: linear-gradient(105deg, #388e3c 0%, #66bb6a 100%);
    }

    .btn-modern:active {
        transform: translateY(1px);
    }

    .alert-custom {
        background-color: #fef2f2;
        border-left: 4px solid #ef4444;
        border-radius: 20px;
        padding: 12px 18px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 0.85rem;
        color: #991b1b;
    }

    .alert-icon {
        flex-shrink: 0;
        margin-top: 2px;
    }

    .alert-content {
        flex: 1;
    }

    .alert-close {
        background: none;
        border: none;
        font-size: 1.2rem;
        line-height: 1;
        cursor: pointer;
        color: #b91c1c;
        opacity: 0.7;
        transition: opacity 0.2s;
        padding: 0 4px;
    }

    .alert-close:hover {
        opacity: 1;
    }

    @media (max-width: 560px) {
        .modern-card {
            padding: 1.5rem;
            margin: 0 16px;
        }
        .form-title {
            font-size: 1.5rem;
        }
    }
</style>

<script>
    // Prevent duplicate region selection
    const selects = Array.from(document.querySelectorAll('[data-region-select]'));

    function updateOptions() {
        if (selects.length < 2) {
            return;
        }

        const values = selects.map(s => s.value);
        selects.forEach((select) => {
            Array.from(select.options).forEach(option => {
                if (option.value === "") return;
                option.disabled = values.includes(option.value) && select.value !== option.value;
            });
        });
    }

    selects.forEach(select => {
        select.addEventListener('change', updateOptions);
    });

    // Character counters for motivation fields
    for (let i = 1; i <= 3; i++) {
        const textarea = document.getElementById(`motivation_${i}`);
        const counterSpan = document.getElementById(`counter_${i}`);
        if (textarea && counterSpan) {
            const updateCounter = () => {
                const length = textarea.value.length;
                counterSpan.textContent = length;
                if (length > 180) {
                    counterSpan.style.color = '#eab308';
                } else if (length > 190) {
                    counterSpan.style.color = '#ef4444';
                } else {
                    counterSpan.style.color = '#94a3b8';
                }
            };
            textarea.addEventListener('input', updateCounter);
            updateCounter();
        }
    }

    // Optional: close alert button
    const closeAlert = document.querySelector('.alert-close');
    if (closeAlert) {
        closeAlert.addEventListener('click', function() {
            const alertBox = this.closest('.alert-custom');
            if (alertBox) alertBox.style.display = 'none';
        });
    }

    // Step indicator - simple visual feedback on selection completion
    function updateStepIndicator() {
        if (selects.length < 2) {
            return;
        }

        const filled = selects.filter(s => s.value !== "").length;
        const steps = document.querySelectorAll('.step');
        steps.forEach((step, idx) => {
            if (idx < filled) step.classList.add('active');
            else step.classList.remove('active');
        });
    }
    selects.forEach(select => {
        select.addEventListener('change', updateStepIndicator);
    });
    updateStepIndicator();

</script>
@endsection