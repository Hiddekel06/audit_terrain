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
        max-width: 860px;
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

    .wizard-progress {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 1.25rem;
    }

    .wizard-progress__item {
        border: 1px solid #dce8dc;
        border-radius: 999px;
        padding: 0.75rem 1rem;
        font-size: 12px;
        font-weight: 600;
        color: #5a6e5a;
        background: #f7faf7;
        text-align: center;
    }

    .wizard-progress__item.is-active {
        background: #4a8c5c;
        border-color: #4a8c5c;
        color: #fff;
    }

    .wizard-step {
        display: none;
    }

    .wizard-step.is-active {
        display: block;
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

    .wizard-actions {
        display: flex;
        gap: 12px;
        margin-top: 1.4rem;
    }

    .wizard-actions > * {
        flex: 1;
    }

    .profile-choice-grid {
        display: grid;
        gap: 12px;
        margin-bottom: 1rem;
    }

    .profile-choice {
        display: block;
        position: relative;
        border: 1px solid #dde5dd;
        border-radius: 14px;
        background: #fafcfa;
        padding: 1rem 1rem 0.95rem;
        cursor: pointer;
        transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
    }

    .profile-choice:hover {
        border-color: #b9cdb9;
        transform: translateY(-1px);
    }

    .profile-choice.is-selected {
        border-color: #4a8c5c;
        box-shadow: 0 0 0 3px rgba(74, 140, 92, 0.12);
    }

    .profile-choice input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .profile-choice__title {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #1a2e1a;
        margin-bottom: 0.25rem;
    }

    .profile-choice__description {
        display: block;
        font-size: 12.5px;
        line-height: 1.5;
        color: #5a6e5a;
    }

    .check-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 0.5rem;
    }

    .check-item {
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #dde5dd;
        border-radius: 10px;
        background: #fafcfa;
        padding: 0.65rem 0.75rem;
    }

    .check-item input[type="checkbox"] {
        width: 15px;
        height: 15px;
        accent-color: #4a8c5c;
        cursor: pointer;
        flex-shrink: 0;
    }

    .check-item label {
        margin: 0;
        font-size: 13px;
        color: #344734;
        cursor: pointer;
    }

    .check-item--subtle {
        border: none;
        background: transparent;
        padding: 0.5rem 0;
    }

    .check-item--subtle label {
        font-size: 12px;
        color: #8a9a8a;
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

    .id-btn:hover {
        background: #3a7048;
        transform: translateY(-1px);
    }

    .id-btn:active {
        transform: translateY(1px);
    }

    .id-btn--secondary {
        background: #eef5ee;
        color: #2f5f3d;
        border: 1px solid #dce8dc;
    }

    .id-btn--secondary:hover {
        background: #e5eee5;
    }

    .id-btn svg {
        width: 16px;
        height: 16px;
    }

    @media (max-width: 700px) {
        .wizard-progress {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .field-row {
            grid-template-columns: 1fr;
        }

        .check-grid {
            grid-template-columns: 1fr;
        }

        .wizard-actions {
            flex-direction: column;
        }
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

            @php
                $initialStep = 1;
                if ($errors->hasAny([
                    'niveau_numerique',
                    'experiences',
                    'experiences.*',
                    'competences_techniques',
                    'competences_techniques.*',
                ])) {
                    $initialStep = 2;
                }

                $availabilityOptions = [
                    'immediate' => 'Tout au long de l\'audit',
                ];

                $showCinField = old('no_matricule');
                $oldExperiences = old('experiences', []);
                $oldCompetences = old('competences_techniques', []);
            @endphp

            <form method="POST" action="{{ route('utilisateur.store') }}" id="userWizardForm" data-initial-step="{{ $initialStep }}">
                @csrf

                @if(isset($selectedProfile) && $selectedProfile)
                    <input type="hidden" name="profil_id" value="{{ old('profil_id', $selectedProfile->id) }}">
                    <div class="field-group" style="margin-bottom: 1rem;">
                        <div style="border: 1px solid #dce8dc; background: #f7faf7; border-radius: 12px; padding: 0.85rem 1rem;">
                            <div class="id-label" style="margin-bottom: 0.2rem;">Profil choisi</div>
                            <div style="font-weight: 700; color: #1a2e1a;">{{ $selectedProfile->libelle }}</div>
                        </div>
                    </div>
                @else
                    <div class="field-group" style="margin-bottom: 1rem;">
                        <label for="profil_id" class="id-label">Profil souhaité <span class="text-danger">*</span></label>
                        <select class="id-select @error('profil_id') is-invalid @enderror" id="profil_id" name="profil_id" required>
                            <option value="">— Sélectionner le profil —</option>
                            @foreach($profils as $profil)
                                <option value="{{ $profil->id }}" @selected(old('profil_id') == $profil->id)>{{ $profil->libelle }}</option>
                            @endforeach
                        </select>
                        @error('profil_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="wizard-progress">
                    <div class="wizard-progress__item" data-step-indicator="1">1. Informations générales</div>
                    <div class="wizard-progress__item" data-step-indicator="2">2. Compétences</div>
                </div>

                <div class="wizard-step is-active" data-step="1">
                    <div class="field-row">
                        <div>
                            <label for="nom" class="id-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="id-input @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required placeholder="Votre nom">
                            @error('nom')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="prenom" class="id-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="id-input @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required placeholder="Votre prénom">
                            @error('prenom')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="telephone" class="id-label">Téléphone <span class="text-danger">*</span></label>
                        <div class="phone-wrap">
                            <span class="phone-prefix">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fd/Flag_of_Senegal.svg" alt="SN">
                                +221
                            </span>
                            <input type="text" class="id-input @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="7X XXX XX XX" inputmode="numeric" pattern="[0-9]*" maxlength="9" required>
                        </div>
                        @error('telephone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="email" class="id-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="id-input @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="profil@gmail.com" required>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="ministere_id" class="id-label">Strucutre <span class="text-danger">*</span></label>
                        <select class="id-select @error('ministere_id') is-invalid @enderror" id="ministere_id" name="ministere_id" required>
                            <option value="">— Sélectionner la structure dans laquelle vous êtes —</option>
                            @foreach($ministeres as $ministere)
                                <option value="{{ $ministere->id }}" @selected(old('ministere_id') == $ministere->id)>{{ $ministere->nom }}</option>
                            @endforeach
                        </select>
                        @error('ministere_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group {{ old('ministere_id') ? '' : 'd-none' }}" data-direction-wrapper>
                        <label for="direction" class="id-label">Direction <span class="text-danger">*</span></label>
                        <input type="text" class="id-input @error('direction') is-invalid @enderror" id="direction" name="direction" value="{{ old('direction') }}" placeholder="Ex: DSI , DENS...">
                        @error('direction')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">Votre service d'appartenance.</small>
                    </div>

                    <div class="field-row">
                        <div>
                            <label for="matricule" class="id-label">Matricule <span class="text-danger">*</span></label>
                            <input type="text" class="id-input @error('matricule') is-invalid @enderror" id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="123456A" inputmode="text" maxlength="7" pattern="[0-9]{6}[A-Za-z]{1}">
                            
                            @error('matricule')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="field-group mt-2">
                        <div class="check-item check-item--subtle">
                            <input type="checkbox" id="no_matricule" name="no_matricule" value="1" @checked((bool) $showCinField)>
                            <label for="no_matricule">Pas de matricule</label>
                        </div>
                    </div>

                    <div class="field-group {{ $showCinField ? '' : 'd-none' }}" data-cin-wrapper>
                        <label for="cin" class="id-label">CIN <span class="text-danger">*</span></label>
                        <input type="text" class="id-input @error('cin') is-invalid @enderror" id="cin" name="cin" value="{{ old('cin') }}" placeholder="13 chiffres" inputmode="numeric" pattern="[0-9]{13}" maxlength="13" {{ $showCinField ? 'required' : '' }}>
                        @error('cin')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">Renseignez ce champ uniquement si vous n’avez pas de matricule.</small>
                    </div>

                    <div class="wizard-actions">
                        <div></div>
                        <button type="button" class="id-btn id-btn--secondary" data-next-step-one>
                            Suivant
                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 8h10M9 4l4 4-4 4"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="wizard-step" data-step="2">
                    <div class="section-label">Section 3 : Niveau numérique</div>
                    <div class="field-group">
                        <label for="niveau_numerique" class="id-label">Quel est votre niveau en compétences numériques ? <span class="text-danger">*</span></label>
                        <select class="id-select @error('niveau_numerique') is-invalid @enderror" id="niveau_numerique" name="niveau_numerique" required>
                            <option value="">— Sélectionner —</option>
                            <option value="debutant" @selected(old('niveau_numerique') === 'debutant')>Débutant</option>
                            <option value="intermediaire" @selected(old('niveau_numerique') === 'intermediaire')>Intermédiaire</option>
                            <option value="avance" @selected(old('niveau_numerique') === 'avance')>Avancé</option>
                            <option value="expert" @selected(old('niveau_numerique') === 'expert')>Expert</option>
                        </select>
                        @error('niveau_numerique')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="section-label mt-3">Section 4 : Expérience</div>
                    <div class="field-group">
                        <p class="id-label mb-2">Sélectionnez les projets dans lequels vous avez dejà travaillé<span class="text-danger">*</span></p>
                        <div class="check-grid">
                            <div class="check-item">
                                <input type="checkbox" id="experience_audit" name="experiences[]" value="audit_recensement" @checked(in_array('audit_recensement', $oldExperiences))>
                                <label for="experience_audit">Audit / recensement</label>
                            </div>
                            <div class="check-item">
                                <input type="checkbox" id="experience_biometrie" name="experiences[]" value="biometrie" @checked(in_array('biometrie', $oldExperiences))>
                                <label for="experience_biometrie">Biométrie</label>
                            </div>
                            <div class="check-item">
                                <input type="checkbox" id="experience_it" name="experiences[]" value="projets_it" @checked(in_array('projets_it', $oldExperiences))>
                                <label for="experience_it">Projets IT</label>
                            </div>
                            <div class="check-item">
                                <input type="checkbox" id="experience_aucune" name="experiences[]" value="aucune" @checked(in_array('aucune', $oldExperiences))>
                                <label for="experience_aucune">Aucune</label>
                            </div>
                        </div>
                        @error('experiences')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('experiences.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="section-label mt-3">Section 5 : Compétences techniques</div>
                    <div class="field-group">
                        <p class="id-label mb-2">Sélectionnez vos competences techniques <span class="text-danger">*</span></p>
                        <div class="check-grid">
                            <div class="check-item">
                                <input type="checkbox" id="competence_tablette" name="competences_techniques[]" value="tablette_smartphone" @checked(in_array('tablette_smartphone', $oldCompetences))>
                                <label for="competence_tablette">Systemes Android / iOS</label>
                            </div>
                            <div class="check-item">
                                <input type="checkbox" id="competence_kit" name="competences_techniques[]" value="kit_biometrique" @checked(in_array('kit_biometrique', $oldCompetences))>
                                <label for="competence_kit">Kit biométrique</label>
                            </div>
                            <div class="check-item">
                                <input type="checkbox" id="competence_reseau" name="competences_techniques[]" value="reseau_4g_hotspot" @checked(in_array('reseau_4g_hotspot', $oldCompetences))>
                                <label for="competence_reseau">Réseau (4G / hotspot)</label>
                            </div>
                            <div class="check-item">
                                <input type="checkbox" id="competence_support" name="competences_techniques[]" value="support_technique" @checked(in_array('support_technique', $oldCompetences))>
                                <label for="competence_support">Support technique</label>
                            </div>
                            <div class="check-item">
                                <input type="checkbox" id="competence_excel" name="competences_techniques[]" value="excel_donnees" @checked(in_array('excel_donnees', $oldCompetences))>
                                <label for="competence_excel">Excel / données</label>
                            </div>
                            <div class="check-item">
                                <input type="checkbox" id="competence_aucune" name="competences_techniques[]" value="aucune" @checked(in_array('aucune', $oldCompetences))>
                                <label for="competence_aucune">Aucune</label>
                            </div>
                        </div>
                        @error('competences_techniques')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('competences_techniques.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="wizard-actions">
                        <button type="button" class="id-btn id-btn--secondary" data-prev-step-two>Précédent</button>
                        <button type="submit" class="id-btn">
                            Valider et continuer
                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 8h10M9 4l4 4-4 4"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('userWizardForm');
        const steps = Array.from(document.querySelectorAll('.wizard-step'));
        const indicators = Array.from(document.querySelectorAll('[data-step-indicator]'));
        const nextButtonOne = document.querySelector('[data-next-step-one]');
        const matriculeInput = document.getElementById('matricule');
        const noMatriculeInput = document.getElementById('no_matricule');
        const cinInput = document.getElementById('cin');
        const cinWrapper = document.querySelector('[data-cin-wrapper]');
        const ministereInput = document.getElementById('ministere_id');
        const directionInput = document.getElementById('direction');
        const directionWrapper = document.querySelector('[data-direction-wrapper]');
        const telInput = document.getElementById('telephone');
        const initialStep = Number(form?.dataset.initialStep || 1);

        function showStep(stepNumber) {
            steps.forEach((step, index) => {
                step.classList.toggle('is-active', index + 1 === stepNumber);
            });

            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('is-active', index + 1 === stepNumber);
            });
        }

        function validateRequiredInStep(stepSelector) {
            const step = document.querySelector(stepSelector);
            if (!step) {
                return true;
            }

            const requiredFields = Array.from(step.querySelectorAll('input[required], select[required]'));
            for (const field of requiredFields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }

            return true;
        }

        function syncIdentityFields() {
            const useCin = Boolean(noMatriculeInput?.checked);

            cinWrapper?.classList.toggle('d-none', !useCin);

            if (cinInput) {
                cinInput.required = useCin;
                cinInput.disabled = !useCin;

                if (!useCin) {
                    cinInput.value = '';
                }
            }

            if (matriculeInput) {
                matriculeInput.required = !useCin;
                matriculeInput.disabled = useCin;
                if (useCin) {
                    matriculeInput.value = '';
                }
            }
        }

        function syncDirectionField() {
            const hasStructure = Boolean(ministereInput?.value);

            directionWrapper?.classList.toggle('d-none', !hasStructure);

            if (directionInput) {
                directionInput.required = hasStructure;
                directionInput.disabled = !hasStructure;

                if (!hasStructure) {
                    directionInput.value = '';
                }
            }
        }

        function setupExclusiveNone(groupName) {
            const noneInput = form?.querySelector('input[name="' + groupName + '[]"][value="aucune"]');
            const allInputs = Array.from(form?.querySelectorAll('input[name="' + groupName + '[]"]') || []);
            const otherInputs = allInputs.filter((input) => input.value !== 'aucune');

            function normalizeOnLoad() {
                if (noneInput?.checked) {
                    otherInputs.forEach((input) => {
                        input.checked = false;
                    });
                }
            }

            noneInput?.addEventListener('change', function () {
                if (this.checked) {
                    otherInputs.forEach((input) => {
                        input.checked = false;
                    });
                }
            });

            otherInputs.forEach((input) => {
                input.addEventListener('change', function () {
                    if (this.checked && noneInput) {
                        noneInput.checked = false;
                    }
                });
            });

            normalizeOnLoad();
        }

        nextButtonOne?.addEventListener('click', function () {
            if (validateRequiredInStep('[data-step="1"]')) {
                showStep(2);
            }
        });

        const prevButtonTwo = document.querySelector('[data-prev-step-two]');
        prevButtonTwo?.addEventListener('click', function () {
            // Retour à l'étape 1 sans validation supplémentaire
            showStep(1);
        });

        telInput?.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        matriculeInput?.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/[^0-9A-Z]/g, '');
        });

        noMatriculeInput?.addEventListener('change', syncIdentityFields);
        ministereInput?.addEventListener('change', syncDirectionField);

        setupExclusiveNone('experiences');
        setupExclusiveNone('competences_techniques');

        syncIdentityFields();
        syncDirectionField();
        showStep(initialStep);
    });
</script>
@endsection
