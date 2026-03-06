{{-- FILE: resources/views/giocatori.blade.php
     Riceve dal Controller:
       $giocatori → array di tutti i giocatori (API o statico)
       $daApi     → bool: true se dati da API reale --}}

<x-layout title="Giocatori NBA - Lista Completa">

    {{-- Header specifico per questa pagina --}}
    <x-slot name="header">
        <x-header
            title="I GIOCATORI"
            subtitle="{{ $daApi ? count($giocatori) . ' giocatori NBA reali. Clicca per le statistiche complete' : '50 stelle della NBA: clicca su una card per vedere le statistiche complete' }}"
        />
    </x-slot>

    {{-- Gli stili sono in public/css/style.css --}}

    <section style="padding: 4rem 0;">
        <div class="container">

            {{-- Intestazione sezione con contatore + badge fonte dati --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <h2 class="section-title" style="margin-bottom: 0;">Tutti i Giocatori</h2>
                <div class="d-flex align-items-center gap-3">

                    {{-- Badge: dati live da API o statici 
                    @if($daApi)
                        <span style="display:inline-flex; align-items:center; gap:0.4rem;
                            background:rgba(29,66,138,0.15); border:1px solid rgba(29,66,138,0.3);
                            color:#6fa3f7; font-size:0.72rem; font-weight:700;
                            letter-spacing:0.08em; text-transform:uppercase;
                            padding:0.25rem 0.8rem; border-radius:999px;">
                            <span style="width:7px;height:7px;border-radius:50%;background:#4ade80;display:inline-block;"></span>
                            Dati reali · API
                        </span>
                    @else
                        <span style="display:inline-flex; align-items:center; gap:0.4rem;
                            background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);
                            color:var(--text-secondary); font-size:0.72rem; font-weight:700;
                            letter-spacing:0.08em; text-transform:uppercase;
                            padding:0.25rem 0.8rem; border-radius:999px;">
                            📋 Dati statici · Esegui <code style="font-size:0.7rem;">php artisan sync:giocatori</code>
                        </span>
                    @endif
                                                                                                                                    --}}

                                                                                                                                    
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">
                        <span id="counter" style="color: var(--nba-red); font-weight: 700; font-size: 1.1rem;">{{ count($giocatori) }}</span>
                        giocatori
                    </div>
                    
                </div>
            </div>

            {{-- BARRA DI RICERCA (filtro JavaScript) --}}
            <div style="margin-bottom: 2.5rem; position: relative;">
                <input
                    type="text"
                    id="searchInput"
                    class="search-bar"
                    placeholder="🔍  Cerca per nome, squadra o ruolo..."
                    oninput="filtraGiocatori()"
                >
            </div>

            {{-- Messaggio "nessun risultato" (nascosto di default) --}}
            <div id="noResults" style="
                display: none;
                text-align: center;
                padding: 4rem;
                color: var(--text-secondary);
            ">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🏀</div>
                <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.5rem; color:var(--text-primary);">
                    Nessun giocatore trovato
                </h3>
                <p>Prova con un altro nome o squadra</p>
            </div>

            {{-- GRIGLIA GIOCATORI --}}
            <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 row-cols-xl-5 g-3" id="playersGrid">
                @foreach($giocatori as $giocatore)
                {{-- Ogni card va dentro un .col di Bootstrap --}}
                <div class="col">
                    <x-card :giocatore="$giocatore" />
                </div>
                @endforeach
            </div>
        </div>
    </section>

    
    <script>
        function filtraGiocatori() {
            // Con Bootstrap ogni card è dentro un .col
            // Dobbiamo nascondere il .col (non solo la card)
            // altrimenti la griglia lascia spazi vuoti
            const colonne = document.querySelectorAll('#playersGrid .col');

            const termine = document.getElementById('searchInput').value.toLowerCase().trim();
            let visibili = 0;

            colonne.forEach(col => {
                const card    = col.querySelector('.player-card');
                const nome    = card?.getAttribute('data-nome')    || '';
                const squadra = card?.getAttribute('data-squadra') || '';
                const ruolo   = card?.getAttribute('data-ruolo')   || '';

                if (nome.includes(termine) || squadra.includes(termine) || ruolo.includes(termine)) {
                    col.style.display = '';   // mostra il .col
                    visibili++;
                } else {
                    col.style.display = 'none'; // nasconde il .col
                }
            });

            document.getElementById('counter').textContent = visibili;
            document.getElementById('noResults').style.display = visibili === 0 ? 'block' : 'none';
        }
    </script>

</x-layout>