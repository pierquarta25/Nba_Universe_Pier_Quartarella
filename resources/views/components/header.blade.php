@props([
    'title' => 'WELCOME',
    'subtitle' => 'Il mondo della pallacanestro professionistica americana'
])

<header style="
    position: relative;
    height: 65vh;
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    text-align: center;
">
    {{-- IMMAGINE DI SFONDO: l'immagine di un campo da basket --}}
    <div style="
        position: absolute;
        inset: 0;
        background-image: url('https://images.unsplash.com/photo-1504450758481-7338eba7524a?w=1600&q=80');
        background-size: cover;
        background-position: center;
        filter: brightness(0.4);
        transform: scale(1.05);
        transition: transform 8s ease;
    " id="headerBg"></div>

    {{-- OVERLAY: strato di colore scuro sopra l'immagine --}}
    <div style="
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to bottom,
            rgba(10,10,15,0.1) 0%,
            rgba(10,10,15,0.6) 100%
        );
    "></div>

    {{-- 
         CONTENUTO DELL'HEADER CON EFFETTO BLUR
         backdrop-filter: blur() crea l'effetto
         "vetro smerigliato" sullo sfondo dell'elemento
                                                         --}}
    <div style="
        position: relative;
        z-index: 1;
        padding: 3rem 4rem;
        background: rgba(255, 255, 255, 0.04);
        backdrop-filter: blur(12px);          /* Questo è l'effetto blur! */
        -webkit-backdrop-filter: blur(12px);  /* Per Safari */
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 8px;
        max-width: 700px;
        width: 90%;
    ">
        {{-- Etichetta decorativa sopra il titolo --}}
        <div style="
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: var(--nba-orange);
            margin-bottom: 1rem;
        ">
            ⭐ NBA Universe ⭐
        </div>

        {{-- Titolo principale --}}
        <h1 style="
            font-family: 'Barlow Condensed', sans-serif;
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 900;
            text-transform: uppercase;
            line-height: 0.95;
            color: #ffffff;
            text-shadow: 0 0 40px rgba(247,148,29,0.4);
            margin-bottom: 1rem;
        ">
            {{ $title }}
        </h1>

        {{-- Linea decorativa --}}
        <div style="
            width: 80px;
            height: 3px;
            background: var(--nba-orange);
            margin: 0 auto 1.5rem;
        "></div>

        {{-- Sottotitolo --}}
        <p style="
            font-size: 1rem;
            color: rgba(255,255,255,0.75);
            font-weight: 300;
            max-width: 450px;
            margin: 0 auto;
        ">
            {{ $subtitle }}
        </p>
    </div>
</header>