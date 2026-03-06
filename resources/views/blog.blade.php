<x-layout title="Blog NBA - News e Approfondimenti">

    <x-slot name="header">
        <x-header
            title="IL BLOG NBA"
            subtitle="News, analisi e curiosità dal mondo della pallacanestro professionistica americana"
        />
    </x-slot>

    {{-- Gli stili sono in public/css/style.css --}}

    <section style="padding: 4rem 0;">
        <div class="container">

            {{-- ARTICOLO IN EVIDENZA --}}
            <div style="margin-bottom: 3rem;">
                <h2 class="section-title">Articolo in Evidenza</h2>

                <div class="article-card article-featured" style="
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    min-height: 420px;
                ">
                    {{-- Immagine sinistra --}}
                    <div class="article-img-wrapper">
                        <div class="article-img" style="
                            height: 100%;
                            background-image: url('{{ $articoloPrincipale['immagine'] }}');
                            filter: brightness(0.85);
                        "></div>
                    </div>

                    {{-- Testo destra --}}
                    <div class="article-featured-text" style="padding:3rem; display:flex; flex-direction:column; justify-content:center;">

                        {{-- Badge categoria + tempo lettura --}}
                        <div style="display:flex; gap:0.75rem; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap;">
                            <span style="
                                background: {{ $articoloPrincipale['colore'] }};
                                color: #fff;
                                font-size: 0.7rem;
                                font-weight: 700;
                                letter-spacing: 0.12em;
                                text-transform: uppercase;
                                padding: 0.3rem 0.8rem;
                                border-radius: 4px;
                            ">{{ $articoloPrincipale['categoria'] }}</span>
                            <span style="color:rgba(255,255,255,0.4); font-size:0.8rem;">
                                ⏱ {{ $articoloPrincipale['lettura'] }} di lettura
                            </span>
                        </div>

                        {{-- Titolo --}}
                        <h3 style="
                            font-family: 'Barlow Condensed', sans-serif;
                            font-size: 2.2rem;
                            font-weight: 900;
                            line-height: 1.15;
                            margin-bottom: 1rem;
                            color: #fff;
                        ">{{ $articoloPrincipale['titolo'] }}</h3>

                        {{-- Sommario --}}
                        <p style="color:rgba(255,255,255,0.6); line-height:1.8; font-size:0.9rem; margin-bottom:0.75rem;">
                            {{ $articoloPrincipale['sommario'] }}
                        </p>

                        {{-- Testo completo --}}
                        <p style="color:rgba(255,255,255,0.5); line-height:1.8; font-size:0.88rem; margin-bottom:1.5rem;">
                            {{ $articoloPrincipale['testo'] }}
                        </p>

                        {{-- Autore e data --}}
                        <div style="color:rgba(255,255,255,0.35); font-size:0.8rem; border-top:1px solid rgba(255,255,255,0.07); padding-top:1rem;">
                            di <strong style="color:rgba(255,255,255,0.7);">{{ $articoloPrincipale['autore'] }}</strong>
                            — {{ $articoloPrincipale['data'] }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ALTRI ARTICOLI --}}
            <h2 class="section-title">Tutti gli Articoli</h2>

            <div class="blog-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:1.5rem;">

                @foreach($altriArticoli as $articolo)
                <article class="article-card">

                    {{-- Immagine --}}
                    <div class="article-img-wrapper">
                        <div class="article-img"
                             style="background-image:url('{{ $articolo['immagine'] }}'); filter:brightness(0.8);">
                        </div>
                    </div>

                    {{-- Contenuto --}}
                    <div style="padding:1.5rem;">

                        {{-- Badge + lettura --}}
                        <div style="display:flex; gap:0.75rem; align-items:center; margin-bottom:0.75rem; flex-wrap:wrap;">
                            <span style="
                                background: {{ $articolo['colore'] }};
                                color: #fff;
                                font-size: 0.65rem;
                                font-weight: 700;
                                letter-spacing: 0.12em;
                                text-transform: uppercase;
                                padding: 0.2rem 0.6rem;
                                border-radius: 3px;
                            ">{{ $articolo['categoria'] }}</span>
                            <span style="color:rgba(255,255,255,0.35); font-size:0.75rem;">
                                ⏱ {{ $articolo['lettura'] }}
                            </span>
                        </div>

                        {{-- Titolo --}}
                        <h3 style="
                            font-family: 'Barlow Condensed', sans-serif;
                            font-size: 1.45rem;
                            font-weight: 800;
                            line-height: 1.2;
                            margin-bottom: 0.75rem;
                            color: #fff;
                        ">{{ $articolo['titolo'] }}</h3>

                        {{-- Sommario --}}
                        <p style="color:rgba(255,255,255,0.5); font-size:0.85rem; line-height:1.7; margin-bottom:0.75rem;">
                            {{ $articolo['sommario'] }}
                        </p>

                        {{-- Testo --}}
                        <p style="color:rgba(255,255,255,0.4); font-size:0.82rem; line-height:1.7; margin-bottom:1rem;">
                            {{ $articolo['testo'] }}
                        </p>

                        {{-- Autore e data --}}
                        <div style="
                            padding-top: 0.75rem;
                            border-top: 1px solid rgba(255,255,255,0.07);
                            color: rgba(255,255,255,0.3);
                            font-size: 0.78rem;
                        ">
                            di <strong style="color:rgba(255,255,255,0.6);">{{ $articolo['autore'] }}</strong>
                            — {{ $articolo['data'] }}
                        </div>
                    </div>

                </article>
                @endforeach
                

            </div>
        </div>
    </section>

</x-layout>