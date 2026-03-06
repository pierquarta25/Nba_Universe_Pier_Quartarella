<x-layout title="NBA Universe - Home">

    <x-slot name="header">
        <x-header
            title="WELCOME"
            subtitle="Scopri le stelle della NBA: statistiche, profili e tanto altro sul basket professionistico americano."
        />
    </x-slot>

    {{-- STATISTICHE RAPIDE --}}
    <section style="padding: 5rem 0 3rem; background: linear-gradient(to bottom, var(--dark-bg), #0d0d18);">
        <div class="container">
            <div class="stats-grid" style="display:grid; grid-template-columns:repeat(4,1fr); gap:1px; background:var(--dark-border); border:1px solid var(--dark-border); border-radius:8px; overflow:hidden;">
                @foreach([
                    ['num'=>'30',   'label'=>'Squadre NBA',              'icon'=>'🏀'],
                    ['num'=>'50',   'label'=>'Giocatori nel Database',    'icon'=>'⭐'],
                    ['num'=>'82',   'label'=>'Partite per Stagione',      'icon'=>'📅'],
                    ['num'=>'1946', 'label'=>'Anno di Fondazione',        'icon'=>'🏆'],
                ] as $stat)
                <div style="background:var(--dark-card); padding:2rem; text-align:center; transition:background 0.3s;"
                     onmouseover="this.style.background='#1a1a28'"
                     onmouseout="this.style.background='var(--dark-card)'">
                    <div style="font-size:2rem; margin-bottom:0.5rem;">{{ $stat['icon'] }}</div>
                    <div style="font-family:'Barlow Condensed',sans-serif; font-size:3rem; font-weight:900;
                                color:var(--nba-red); line-height:1; margin-bottom:0.5rem;">
                        {{ $stat['num'] }}
                    </div>
                    <div style="color:var(--text-secondary); font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em;">
                        {{ $stat['label'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ULTIME PARTITE --}}
    <section style="padding: 4rem 0;">
        <div class="container">

            {{-- Titolo + badge fonte dati + link pagina partite --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <h2 class="section-title mb-0">Ultime Partite</h2>
                <div class="d-flex align-items-center gap-3">
                    {{-- Badge: dati live o statici --}}
                    @if($daApi)
                        <span style="display:inline-flex; align-items:center; gap:0.4rem;
                            background:rgba(29,66,138,0.15); border:1px solid rgba(29,66,138,0.3);
                            color:#6fa3f7; font-size:0.72rem; font-weight:700;
                            letter-spacing:0.08em; text-transform:uppercase;
                            padding:0.25rem 0.7rem; border-radius:999px;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;display:inline-block;"></span>
                            Live API
                        </span>
                    @else
                        <span style="display:inline-flex; align-items:center; gap:0.4rem;
                            background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);
                            color:var(--text-secondary); font-size:0.72rem; font-weight:700;
                            letter-spacing:0.08em; text-transform:uppercase;
                            padding:0.25rem 0.7rem; border-radius:999px;">
                            📋 Dati esempio
                        </span>
                    @endif
                    <a href="{{ route('partite') }}" class="btn btn-primary btn-sm">Vedi tutte →</a>
                </div>
            </div>

            {{-- Lista partite --}}
            <div class="d-flex flex-column gap-3">
                @foreach($partite as $partita)
                @php
                    $casaVince   = $partita['finita'] && $partita['casa_punteggio'] > $partita['ospite_punteggio'];
                    $ospiteVince = $partita['finita'] && $partita['ospite_punteggio'] > $partita['casa_punteggio'];
                @endphp
                <div style="background:var(--dark-card); border:1px solid var(--dark-border);
                             border-radius:8px; padding:1.1rem 1.75rem;
                             display:grid; grid-template-columns:1fr auto 1fr auto;
                             align-items:center; gap:1rem; transition:border-color 0.2s;"
                     onmouseover="this.style.borderColor='rgba(29,66,138,0.4)'"
                     onmouseout="this.style.borderColor='var(--dark-border)'">

                    {{-- Casa --}}
                    <div class="text-end">
                        <div style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700;
                                    color:{{ $casaVince ? '#fff' : 'var(--text-secondary)' }};">
                            {{ $partita['casa_nome'] }}
                        </div>
                        <div style="font-size:0.75rem; color:var(--text-secondary);">Casa</div>
                    </div>

                    {{-- Punteggio --}}
                    <div class="text-center" style="min-width:130px;">
                        <div style="font-family:'Barlow Condensed',sans-serif; font-size:1.9rem; font-weight:900; color:var(--nba-red); line-height:1;">
                            {{ $partita['casa_punteggio'] }} — {{ $partita['ospite_punteggio'] }}
                        </div>
                        <div style="font-size:0.72rem; color:var(--text-secondary); letter-spacing:0.08em; text-transform:uppercase; margin-top:0.2rem;">
                            @if($partita['in_corso'])
                                <span style="color:#4ade80;">● Q{{ $partita['periodo'] }} {{ $partita['tempo'] }}</span>
                            @elseif($partita['finita'])
                                Finale
                            @else
                                In programma
                            @endif
                        </div>
                    </div>

                    {{-- Ospite --}}
                    <div>
                        <div style="font-family:'Barlow Condensed',sans-serif; font-size:1.2rem; font-weight:700;
                                    color:{{ $ospiteVince ? '#fff' : 'var(--text-secondary)' }};">
                            {{ $partita['ospite_nome'] }}
                        </div>
                        <div style="font-size:0.75rem; color:var(--text-secondary);">Trasferta</div>
                    </div>

                    {{-- Data --}}
                    <div style="color:var(--text-secondary); font-size:0.82rem; text-align:right; white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($partita['data'])->locale('it')->isoFormat('D MMM') }}
                    </div>

                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- TOP GIOCATORI --}}
    <section style="padding: 4rem 0; background: #0d0d18;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <h2 class="section-title mb-0">Top Giocatori</h2>
                <a href="{{ route('giocatori') }}" class="btn btn-primary btn-sm">Vedi Tutti →</a>
            </div>

            <div class="top-giocatori-grid" style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; min-width:0;">
                @foreach($giocatoriInEvidenza as $index => $giocatore)
                <div style="position:relative;">
                    <div style="position:absolute; top:1rem; left:1rem; width:36px; height:36px;
                                background:var(--nba-red); color:#fff;
                                font-family:'Barlow Condensed',sans-serif;
                                font-size:1.1rem; font-weight:900;
                                border-radius:50%; display:flex; align-items:center; justify-content:center;
                                z-index:1;">#{{ $index + 1 }}</div>
                    <x-card :giocatore="$giocatore" size="large" :showBio="true" />
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CALL TO ACTION --}}
    <section style="padding: 6rem 0; text-align:center;">
        <div class="container">
            <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:3rem; font-weight:900;
                       text-transform:uppercase; margin-bottom:1rem;">
                Scopri Tutti i <span style="color:var(--nba-red);">50 Giocatori</span>
            </h2>
            <p style="color:var(--text-secondary); margin-bottom:2rem; font-size:1.1rem;">
                Esplora statistiche dettagliate, biografie e molto altro
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap cta-btns">
                <a href="{{ route('giocatori') }}" class="btn btn-primary" style="font-size:1.1rem; padding:0.9rem 2.5rem;">
                    🏀 Lista Giocatori
                </a>
                <a href="{{ route('partite') }}" class="btn btn-danger" style="font-size:1.1rem; padding:0.9rem 2.5rem;">
                    📅 Risultati Partite
                </a>
            </div>
        </div>
    </section>

</x-layout>