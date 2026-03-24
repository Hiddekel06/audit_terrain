<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Plateforme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: @yield('body-bg', '#e6f4ea');
        }
    </style>
</head>
<body>
    @hasSection('no-header')
        {{-- Pas de header --}}
    @else
        @include('partials.header')
    @endif
    <main>
        @yield('content')
    </main>
    @hasSection('no-footer')
        {{-- Pas de footer --}}
    @else
        @include('partials.footer')
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>