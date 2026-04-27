@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300..700&display=swap');

    body {
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
        background-color: #f7f8fa;
    }

    .id-wrap {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1rem 3rem;
        background: #f7f8fa;
    }

    .id-card {
        background: #ffffff;
        border-radius: 20px;
        border: 0.5px solid #e3e8e3;
        width: 100%;
        max-width: 480px;
        overflow: hidden;
    }

    .id-card__top {
        background: #f0f7f0;
        border-bottom: 0.5px solid #dce8dc;
        padding: 1.5rem 1.75rem 1.25rem;
    }

    .id-eyebrow {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 0.5rem;
    }

    .id-eyebrow__dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #4a8c5c;
        flex-shrink: 0;
    }

    .id-eyebrow__text {
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.07em;
        color: #4a8c5c;
        text-transform: uppercase;
    }

    .id-card__title {
        font-size: 20px;
        font-weight: 600;
        color: #1a2e1a;
        line-height: 1.2;
        margin: 0;
    }

    .id-card__body {
        padding: 1.5rem 1.75rem 1.75rem;
    }

    .id-alert {
        background: #fff8f0;
        border: 0.5px solid #f0c080;
        border-radius: 10px;
        padding: 0.7rem 1rem;
        margin-bottom: 1.1rem;
        font-size: 12.5px;
        color: #7a4a10;
    }

    .id-alert ul {
        margin: 0;
        padding-left: 1.1rem;
    }

    .id-alert li {
        margin-bottom: 0.2rem;
    }

    .field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 1.1rem;
    }

    .field-group {
        margin-bottom: 1.1rem;
    }

    .id-label {
        display: block;
        font-size: 12px;
        font-weight: 500;
        color: #4a5e4a;
        margin-bottom: 5px;
        letter-spacing: 0.02em;
    }

    .id-input {
        width: 100%;
        height: 42px;
        border: 1px solid #dde5dd;
        border-radius: 10px;
        padding: 0 14px;
        font-size: 14px;
        color: #1a2e1a;
        background: #fafcfa;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .id-input:focus {
        border-color: #4a8c5c;
        box-shadow: 0 0 0 3px rgba(74, 140, 92, 0.12);
    }

    .id-input::placeholder {
        color: #aab8aa;
    }

    .id-input.is-invalid {
        border-color: #d9534f;
    }

    .id-select {
        width: 100%;
        height: 42px;
        border: 1px solid #dde5dd;
        border-radius: 10px;
        padding: 0 36px 0 14px;
        font-size: 14px;
        color: #1a2e1a;
        background: #fafcfa url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' fill='none'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%234a5e4a' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 14px center;
        appearance: none;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .id-select:focus {
        border-color: #4a8c5c;
        box-shadow: 0 0 0 3px rgba(74, 140, 92, 0.12);
    }

    .id-select.is-invalid {
        border-color: #d9534f;
    }

    .check-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 7px;
    }

    .check-row input[type="checkbox"] {
        width: 15px;
        height: 15px;
        accent-color: #4a8c5c;
        cursor: pointer;
        flex-shrink: 0;
    }

    .check-row label {
        font-size: 12.5px;
        color: #5a6e5a;
        cursor: pointer;
        font-weight: 400;
        margin: 0;
    }

    .phone-wrap {
        display: flex;
        height: 42px;
        border: 1px solid #dde5dd;
        border-radius: 10px;
        overflow: hidden;
        background: #fafcfa;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .phone-wrap:focus-within {
        border-color: #4a8c5c;
        box-shadow: 0 0 0 3px rgba(74, 140, 92, 0.12);
    }

    .phone-prefix {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 0 12px;
        background: #f0f4f0;
        border-right: 1px solid #dde5dd;
        font-size: 13px;
        color: #4a5e4a;
        font-weight: 500;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .phone-prefix img {
        width: 18px;
        height: 12px;
        border-radius: 2px;
        object-fit: cover;
    }

    .phone-wrap .id-input {
        border: none;
        border-radius: 0;
        background: transparent;
        padding-left: 10px;
        box-shadow: none !important;
    }

    .id-divider {
        border: none;
        border-top: 0.5px solid #e5ece5;
        margin: 1.4rem 0 1.1rem;
    }

    .section-label {
        font-size: 11px;
        font-weight: 500;
        color: #4a8c5c;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-label::after {
        content: '';
        flex: 1;
        height: 0.5px;
        background: #dce8dc;
    }

    .invalid-feedback {
        font-size: 12px;
        color: #c0392b;
        margin-top: 4px;
    }

    .id-btn {
        width: 100%;
        height: 46px;
        background: #4a8c5c;
        border: none;
        border-radius: 40px;
        color: #fff;
        font-size: 14.5px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 0.5rem;
        letter-spacing: 0.01em;
        transition: background 0.15s, transform 0.1s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .id-btn:hover { background: #3a7048; transform: translateY(-1px); }
    .id-btn:active { transform: translateY(1px); }

    .id-btn svg {
        width: 16px;
        height: 16px;
    }

    @media (max-width: 480px) {
        .field-row { grid-template-columns: 1fr; }
    }
</style>

<div class="id-wrap">
    <div class="id-card">

        <div class="id-card__top">
            <div class="id-eyebrow">
                <span class="id-eyebrow__dot"></span>
                <span class="id-eyebrow__text">Portail interne</span>
            </div>
            <h1 class="id-card__title">Identification utilisateur</h1>
        </div>

        <div class="id-card__body">

            @if ($errors->any())
                <div class="id-alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('utilisateur.store') }}">
                @csrf

                <div class="field-row">
                    <div>
                        <label for="prenom" class="id-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="id-input" id="prenom" name="prenom"
                               value="{{ old('prenom') }}" required placeholder="Votre prénom">
                    </div>
                    <div>
                        <label for="nom" class="id-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="id-input" id="nom" name="nom"
                               value="{{ old('nom') }}" required placeholder="Votre nom">
                    </div>
                </div>

                <div class="field-group">
                    <label for="matricule" class="id-label">Matricule <span class="text-danger">*</span></label>
                    <input type="text" class="id-input" id="matricule" name="matricule"
                           value="{{ old('matricule') }}" placeholder="Entrez votre matricule" required>
                    <div class="check-row">
                        <input class="form-check-input" type="checkbox" value="1"
                               id="no_matricule" name="no_matricule">
                        <label for="no_matricule">Je n'ai pas de matricule</label>
                    </div>
                </div>

                <div class="field-group" id="telephone_block" style="display:none;">
                    <label for="telephone" class="id-label">Téléphone <span class="text-danger">*</span></label>
                    <div class="phone-wrap">
                        <span class="phone-prefix">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/f/fd/Flag_of_Senegal.svg" alt="SN">
                            +221
                        </span>
                        <input type="text" class="id-input" id="telephone" name="telephone"
                               value="{{ old('telephone') }}" placeholder="7X XXX XX XX"
                               inputmode="numeric" pattern="[0-9]*" maxlength="9">
                    </div>
                </div>

                @if(isset($dynamicQuestions) && $dynamicQuestions->count())
                    <hr class="id-divider">
                    <div class="section-label">Questions complémentaires</div>

                    @foreach($dynamicQuestions as $question)
                        @php $fieldName = 'question_' . $question->id; @endphp
                        <div class="field-group">
                            <label for="{{ $fieldName }}" class="id-label">
                                {{ $question->libelle }}
                                @if($question->is_required)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @if($question->type === 'select')
                                <select class="id-select @error($fieldName) is-invalid @enderror"
                                        id="{{ $fieldName }}" name="{{ $fieldName }}"
                                        @if($question->is_required) required @endif>
                                    <option value="">— Sélectionner —</option>
                                    @foreach($question->options as $option)
                                        <option value="{{ $option->id }}"
                                            @selected(old($fieldName) == $option->id)>
                                            {{ $option->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text"
                                       class="id-input @error($fieldName) is-invalid @enderror"
                                       id="{{ $fieldName }}" name="{{ $fieldName }}"
                                       value="{{ old($fieldName) }}"
                                       placeholder="{{ $question->placeholder ?: 'Votre réponse' }}"
                                       @if($question->is_required) required @endif>
                            @endif

                            @error($fieldName)
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                @endif

                <button type="submit" class="id-btn">
                    Valider
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 8h10M9 4l4 4-4 4"/>
                    </svg>
                </button>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const noMatricule = document.getElementById('no_matricule');
        const matriculeInput = document.getElementById('matricule');
        const telBlock = document.getElementById('telephone_block');
        const telInput = document.getElementById('telephone');

        function toggleMatricule() {
            if (noMatricule.checked) {
                matriculeInput.value = '';
                matriculeInput.setAttribute('disabled', 'disabled');
                matriculeInput.removeAttribute('required');
                telBlock.style.display = '';
                telInput.setAttribute('required', 'required');
            } else {
                matriculeInput.removeAttribute('disabled');
                matriculeInput.setAttribute('required', 'required');
                telBlock.style.display = 'none';
                telInput.value = '';
                telInput.removeAttribute('required');
            }
        }

        noMatricule.addEventListener('change', toggleMatricule);
        toggleMatricule();

        telInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>
@endsection