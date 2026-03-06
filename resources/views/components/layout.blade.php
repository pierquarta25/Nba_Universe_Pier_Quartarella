<!DOCTYPE html>
<html lang="it" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'NBA Universe' }}</title>

    {{-- FAVICON — pallone basket con colori NBA --}}
    {{-- SVG: nitido a qualsiasi dimensione (browser moderni) --}}
    <link rel="icon" type="image/png" href="{{ asset('media/favicon.png') }}">
    {{-- ICO: fallback per browser più vecchi e tab Windows --}}
    <link rel="alternate icon" href="{{ asset('media/favicon.ico') }}">
    {{-- PNG 32x32: fallback aggiuntivo --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('media/favicon.png') }}">

    {{-- BOOTSTRAP CSS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

    {{-- GOOGLE FONTS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- CSS CUSTOM — dopo Bootstrap così possiamo sovrascriverlo --}}
    {{-- asset() genera l'URL verso public/css/style.css --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>
<body>

    {{-- NAVBAR Bootstrap --}}
    <x-navbar />

    {{-- Header opzionale (slot) --}}
    {{ $header ?? '' }}

    {{-- Contenuto principale --}}
    <main>{{ $slot }}</main>

    {{-- Footer --}}
    <x-footer />

    {{-- BOOTSTRAP JS --}}
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmcp/OmEd3j38DZ6fHp8Y1tMUyNG"
        crossorigin="anonymous">
    </script>

    {{-- JS custom --}}
    <script src="{{ asset('js/main.js') }}"></script>

</body>
</html>