<x-layout title="{{ $giocatore['nome'] }} - NBA Universe">

    {{-- HERO --}}
    <div style="position:relative; height:50vh; min-height:350px; overflow:hidden;">
        <div style="position:absolute; inset:0;
                    background-image:url('{{ $giocatore['immagine'] }}');
                    background-size:cover; background-position:center top;
                    filter:blur(3px) brightness(0.3); transform:scale(1.05);"></div>
        <div style="position:absolute; inset:0;
                    background:linear-gradient(to bottom, rgba(10,10,15,0) 0%, var(--dark-bg) 100%);"></div>
        <div style="position:relative; z-index:1; padding:2rem;" class="container">
            <a href="{{ route('giocatori') }}"
               style="color:var(--text-secondary); font-size:0.85rem; display:inline-flex; align-items:center; gap:0.5rem; transition:color 0.2s;"
               onmouseover="this.style.color='var(--nba-red)'"
               onmouseout="this.style.color='var(--text-secondary)'">
                ← Torna a tutti i giocatori
            </a>

            {{-- Badge dati live --}}
            @if(!empty($giocatore['da_api']))
            <span style="margin-left:1rem; display:inline-flex; align-items:center; gap:0.4rem;
                background:rgba(29,66,138,0.2); border:1px solid rgba(29,66,138,0.4);
                color:#6fa3f7; font-size:0.7rem; font-weight:700;
                letter-spacing:0.08em; text-transform:uppercase;
                padding:0.2rem 0.7rem; border-radius:999px;">
                <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;display:inline-block;"></span>
                Dati reali 2025-26
            </span>
            @endif
        </div>
    </div>

    {{-- CONTENUTO --}}
    <section style="padding:0 0 6rem; margin-top:-180px; position:relative; z-index:1;">
        <div class="container">
            <div class="show-hero-grid" style="display:grid; grid-template-columns:280px 1fr; gap:2.5rem; align-items:start;">

                {{-- ── COLONNA SINISTRA ── --}}
                <div>
                    {{-- Foto --}}
                    <div style="width:100%; aspect-ratio:3/4; border-radius:12px; overflow:hidden;
                                border:2px solid var(--dark-border); box-shadow:0 24px 64px rgba(0,0,0,0.6);
                                margin-bottom:1.5rem;">
                        <img src="{{ $giocatore['immagine'] }}"
                             alt="Foto di {{ $giocatore['nome'] }}"
                             style="width:100%; height:100%; object-fit:cover; object-position:top center;"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'280\' height=\'380\' viewBox=\'0 0 280 380\'%3E%3Crect width=\'280\' height=\'380\' fill=\'%231D428A\'/%3E%3Ccircle cx=\'140\' cy=\'130\' r=\'60\' fill=\'%23ffffff22\'/%3E%3Ccircle cx=\'140\' cy=\'108\' r=\'44\' fill=\'%23ffffff33\'/%3E%3Crect x=\'50\' y=\'200\' width=\'180\' height=\'180\' rx=\'90\' fill=\'%23ffffff22\'/%3E%3Ctext x=\'140\' y=\'360\' font-family=\'Arial\' font-size=\'14\' fill=\'%23ffffff66\' text-anchor=\'middle\'%3EFoto non disponibile%3C/text%3E%3C/svg%3E';">
                    </div>

                    {{-- Scheda tecnica --}}
                    <div style="background:var(--dark-card); border:1px solid var(--dark-border); border-radius:12px; padding:1.5rem;">
                        <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:0.85rem; font-weight:700;
                                   letter-spacing:0.15em; text-transform:uppercase; color:var(--nba-red); margin-bottom:1rem;">
                            Scheda Tecnica
                        </h3>

                        <div class="info-item">
                            <span style="color:var(--text-secondary); font-size:0.85rem;">Numero</span>
                            <span style="font-weight:600;">#{{ $giocatore['numero'] }}</span>
                        </div>
                        <div class="info-item">
                            <span style="color:var(--text-secondary); font-size:0.85rem;">Ruolo</span>
                            <span class="badge-ruolo">{{ $giocatore['ruolo'] }}</span>
                        </div>
                        <div class="info-item">
                            <span style="color:var(--text-secondary); font-size:0.85rem;">Squadra</span>
                            <span style="font-weight:600; font-size:0.85rem;">{{ $giocatore['squadra'] }}</span>
                        </div>
                        <div class="info-item">
                            <span style="color:var(--text-secondary); font-size:0.85rem;">Nazionalità</span>
                            <span style="font-weight:600; font-size:0.9rem;">{{ $giocatore['nazionalita'] }}</span>
                        </div>

                        {{-- Campi extra disponibili solo con dati API --}}
                        @if(!empty($giocatore['altezza']) && $giocatore['altezza'] !== 'N/D')
                        <div class="info-item">
                            <span style="color:var(--text-secondary); font-size:0.85rem;">Altezza</span>
                            <span style="font-weight:600;">{{ $giocatore['altezza'] }}</span>
                        </div>
                        @endif

                        @if(!empty($giocatore['peso']) && $giocatore['peso'] !== 'N/D')
                        <div class="info-item">
                            <span style="color:var(--text-secondary); font-size:0.85rem;">Peso</span>
                            <span style="font-weight:600;">{{ $giocatore['peso'] }} lbs</span>
                        </div>
                        @endif

                        @if(!empty($giocatore['college']) && $giocatore['college'] !== 'N/D')
                        <div class="info-item">
                            <span style="color:var(--text-secondary); font-size:0.85rem;">College</span>
                            <span style="font-weight:600; font-size:0.82rem;">{{ $giocatore['college'] }}</span>
                        </div>
                        @endif

                        @if(!empty($giocatore['eta']) && $giocatore['eta'] !== '—')
                        <div class="info-item">
                            <span style="color:var(--text-secondary); font-size:0.85rem;">Età</span>
                            <span style="font-weight:600;">{{ $giocatore['eta'] }} anni</span>
                        </div>
                        @endif

                        @if(!empty($giocatore['partite']) && $giocatore['partite'] > 0)
                        <div class="info-item">
                            <span style="color:var(--text-secondary); font-size:0.85rem;">Partite 2025-26</span>
                            <span style="font-weight:600; color:var(--nba-red);">{{ $giocatore['partite'] }}</span>
                        </div>
                        @endif

                        @if(!empty($giocatore['minuti']) && $giocatore['minuti'] !== '0')
                        <div class="info-item">
                            <span style="color:var(--text-secondary); font-size:0.85rem;">Min / Partita</span>
                            <span style="font-weight:600;">{{ $giocatore['minuti'] }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ── COLONNA DESTRA ── --}}
                <div>
                    {{-- Nome e squadra --}}
                    <div style="margin-bottom:2rem;">
                        <div style="font-size:0.85rem; font-weight:600; letter-spacing:0.2em;
                                    text-transform:uppercase; color:var(--nba-red); margin-bottom:0.5rem;">
                            {{ $giocatore['squadra'] }}
                        </div>
                        <h1 style="font-family:'Barlow Condensed',sans-serif; font-size:clamp(3rem,6vw,5rem);
                                   font-weight:900; text-transform:uppercase; line-height:0.95; margin-bottom:1.5rem;">
                            {{ $giocatore['nome'] }}
                        </h1>
                    </div>

                    {{-- STATISTICHE --}}
                    <div style="background:var(--dark-card); border:1px solid var(--dark-border);
                                border-radius:12px; padding:2rem; margin-bottom:2rem;">

                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem;">
                            <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1rem; font-weight:700;
                                       letter-spacing:0.15em; text-transform:uppercase; color:var(--nba-red); margin:0;">
                                Statistiche Stagione 2025-26
                            </h2>
                            @if(!empty($giocatore['da_api']))
                                <span style="font-size:0.7rem; color:#4ade80; letter-spacing:0.06em; text-transform:uppercase;">● Live</span>
                            @endif
                        </div>

                        {{-- 3 grandi numeri --}}
                        <div class="show-stats-grid" style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:2rem;">
                            @foreach([
                                ['val' => $giocatore['punti'],    'label' => 'Punti / Partita',    'color' => 'var(--nba-red)'],
                                ['val' => $giocatore['rimbalzi'], 'label' => 'Rimbalzi / Partita',  'color' => 'var(--text-primary)'],
                                ['val' => $giocatore['assist'],   'label' => 'Assist / Partita',    'color' => 'var(--text-primary)'],
                            ] as $s)
                            <div style="text-align:center; background:rgba(200,16,46,0.06); border-radius:10px;
                                        padding:1.5rem; border:1px solid rgba(200,16,46,0.15);">
                                <div style="font-family:'Barlow Condensed',sans-serif; font-size:3.5rem;
                                            font-weight:900; color:{{ $s['color'] }}; line-height:1;">
                                    {{ $s['val'] > 0 ? $s['val'] : '—' }}
                                </div>
                                <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.1em;
                                            color:var(--text-secondary); margin-top:0.3rem;">
                                    {{ $s['label'] }}
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Percentuali (solo se disponibili) --}}
                        @if(!empty($giocatore['fg_pct']) || !empty($giocatore['fg3_pct']) || !empty($giocatore['ft_pct']))
                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:0.75rem; margin-bottom:2rem;">
                            @foreach([
                                ['val' => ($giocatore['fg_pct'] ?? 0),  'label' => 'FG%'],
                                ['val' => ($giocatore['fg3_pct'] ?? 0), 'label' => '3P%'],
                                ['val' => ($giocatore['ft_pct'] ?? 0),  'label' => 'FT%'],
                            ] as $p)
                            <div style="text-align:center; background:rgba(255,255,255,0.03);
                                        border-radius:8px; padding:1rem; border:1px solid var(--dark-border);">
                                <div style="font-family:'Barlow Condensed',sans-serif; font-size:2rem;
                                            font-weight:800; color:var(--nba-blue); line-height:1;">
                                    {{ $p['val'] > 0 ? $p['val'].'%' : '—' }}
                                </div>
                                <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.08em;
                                            color:var(--text-secondary); margin-top:0.25rem;">{{ $p['label'] }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Barre animate --}}
                        <div style="display:flex; flex-direction:column; gap:1.2rem;">
                            @foreach([
                                ['label'=>'Punti',    'val'=>$giocatore['punti'],    'max'=>45, 'color'=>'var(--nba-blue), #3a6fd8'],
                                ['label'=>'Rimbalzi', 'val'=>$giocatore['rimbalzi'], 'max'=>20, 'color'=>'var(--nba-blue), #3a6fd8'],
                                ['label'=>'Assist',   'val'=>$giocatore['assist'],   'max'=>15, 'color'=>'var(--nba-red), #ff4d6d'],
                                ['label'=>'Stoppate', 'val'=>($giocatore['stoppate'] ?? 0), 'max'=>5, 'color'=>'var(--nba-red), #ff4d6d'],
                            ] as $bar)
                            @if($bar['val'] > 0)
                            <div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem;">
                                    <span style="font-size:0.85rem; color:var(--text-secondary);">{{ $bar['label'] }}</span>
                                    <span style="font-size:0.85rem; font-weight:600;">{{ $bar['val'] }}</span>
                                </div>
                                <div class="stat-bar">
                                    <div class="stat-bar-fill"
                                         data-value="{{ min(($bar['val'] / $bar['max']) * 100, 100) }}"
                                         style="background:linear-gradient(to right, {{ $bar['color'] }});"></div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- BIOGRAFIA --}}
                    @if(!empty($giocatore['bio']))
                    <div style="background:var(--dark-card); border:1px solid var(--dark-border);
                                border-radius:12px; padding:2rem; margin-bottom:2rem;">
                        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1rem; font-weight:700;
                                   letter-spacing:0.15em; text-transform:uppercase; color:var(--nba-red); margin-bottom:1rem;">
                            Profilo
                        </h2>
                        <p style="color:var(--text-secondary); line-height:1.8; font-size:0.95rem;">
                            {{ $giocatore['bio'] }}
                        </p>
                    </div>
                    @else
                    {{-- Per i giocatori API mostriamo info extra al posto della bio --}}
                    <div style="background:var(--dark-card); border:1px solid var(--dark-border);
                                border-radius:12px; padding:2rem; margin-bottom:2rem;">
                        <h2 style="font-family:'Barlow Condensed',sans-serif; font-size:1rem; font-weight:700;
                                   letter-spacing:0.15em; text-transform:uppercase; color:var(--nba-red); margin-bottom:1rem;">
                            Informazioni
                        </h2>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;">
                            @if(!empty($giocatore['college']) && $giocatore['college'] !== 'N/D')
                            <div style="color:var(--text-secondary); font-size:0.88rem;">🎓 {{ $giocatore['college'] }}</div>
                            @endif
                            @if(!empty($giocatore['nazionalita']))
                            <div style="color:var(--text-secondary); font-size:0.88rem;">🌍 {{ $giocatore['nazionalita'] }}</div>
                            @endif
                            @if(!empty($giocatore['altezza']) && $giocatore['altezza'] !== 'N/D')
                            <div style="color:var(--text-secondary); font-size:0.88rem;">📏 {{ $giocatore['altezza'] }}</div>
                            @endif
                            @if(!empty($giocatore['peso']) && $giocatore['peso'] !== 'N/D')
                            <div style="color:var(--text-secondary); font-size:0.88rem;">⚖️ {{ $giocatore['peso'] }} lbs</div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <a href="{{ route('giocatori') }}" class="btn btn-primary">← Torna alla Lista</a>
                </div>

            </div>
        </div>
    </section>

</x-layout>