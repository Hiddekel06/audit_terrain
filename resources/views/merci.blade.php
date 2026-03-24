@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100 py-5">
    <div class="modern-card p-4 p-md-5 text-center" style="max-width: 420px; width: 100%;">
        <h2 class="form-title mb-3">Merci !</h2>
        <p class="mb-4" style="font-size:1.1rem; color:#388e3c;">Vos choix de régions ont bien été enregistrés.<br>Nous vous contacterons si votre candidature est retenue.</p>
        <img src="/images/auditlogo.png" alt="Logo" style="height:60px; margin-bottom:1.5rem;">
        <a href="/" class="btn btn-success mt-2">Retour à l'accueil</a>
    </div>
</div>
@endsection
