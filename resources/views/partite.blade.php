<x-layout title="Partite NBA - NBA Universe">

    {{-- Header sezione --}}
    <x-slot name="header">
        <x-header
            title="PARTITE"
            subtitle="Risultati e punteggi NBA aggiornati in tempo reale"
        />
    </x-slot>

    <section style="padding: 4rem 0 6rem;">
        <div class="container">

            {{-- NAVIGAZIONE --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-5">
                <h2 class="section-title mb-0">
                    Partite del
                    <span style="color:var(--nba-red);">
                        {{ \Carbon\Carbon::parse($data)->locale('it')->isoFormat('D MMMM YYYY') }}
                    </span>
                </h2>

                {{-- Navigatore date --}}
                <div class="d-flex align-items-center gap-2">
                    {{-- Giorno precedente --}}
                    <a href="{{ route('partite', ['data' => \Carbon\Carbon::parse($data)->subDay()->format('Y-m-d')]) }}"
                       class="btn btn-primary btn-sm">← Ieri</a>

                    {{-- Input data (Bootstrap form-control) --}}
                    <input type="date"
                           id="datePicker"
                           class="form-control form-control-sm"
                           value="{{ $data }}"
                           max="{{ now()->format('Y-m-d') }}"
                           style="width:auto;"
                           onchange="window.location='{{ route('partite') }}?data='+this.value">

                    {{-- Giorno successivo (disabilitato se oggi) --}}
                    @if($data < now()->format('Y-m-d'))
                        <a href="{{ route('partite', ['data' => \Carbon\Carbon::parse($data)->addDay()->format('Y-m-d')]) }}"
                           class="btn btn-primary btn-sm">Domani →</a>
                    @else
                        <span class="btn btn-primary btn-sm disabled opacity-50">Domani →</span>
                    @endif
                </div>
            </div>

            {{-- Badge fonte dati --}}
            <div class="mb-4">
                @if($daApi)
                    <span style="
                        display:inline-flex; align-items:center; gap:0.4rem;
                        background:rgba(29,66,138,0.15); border:1px solid rgba(29,66,138,0.3);
                        color:#6fa3f7; font-size:0.75rem; font-weight:600;
                        letter-spacing:0.08em; text-transform:uppercase;
                        padding:0.3rem 0.8rem; border-radius:999px;
                    ">
                        {{-- Pallino verde animato = dati live --}}
                        <span style="width:7px;height:7px;border-radius:50%;background:#4ade80;
                                     display:inline-block;animation:pulse 1.5s ease-in-out infinite;"></span>
                        Dati reali · balldontlie.io API
                    </span>
                @else
                    <span style="
                        display:inline-flex; align-items:center; gap:0.4rem;
                        background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1);
                        color:var(--text-secondary); font-size:0.75rem; font-weight:600;
                        letter-spacing:0.08em; text-transform:uppercase;
                        padding:0.3rem 0.8rem; border-radius:999px;
                    ">
                        📋 Dati di esempio · Configura NBA_API_KEY nel .env per dati reali
                    </span>
                @endif
            </div>

            {{-- ============================================
                 LISTA PARTITE
                 ============================================ --}}
            @if(count($partite) === 0)
                {{-- Nessuna partita trovata --}}
                <div class="text-center py-5" style="color:var(--text-secondary);">
                    <div style="font-size:4rem; margin-bottom:1rem;">🏀</div>
                    <h3 style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; color:var(--text-primary);">
                        Nessuna partita in questa data
                    </h3>
                    <p class="mt-2">Prova a selezionare un'altra data.</p>
                </div>

            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($partite as $partita)

                    @php
                        $casaVince   = $partita['finita'] && $partita['casa_punteggio'] > $partita['ospite_punteggio'];
                        $ospiteVince = $partita['finita'] && $partita['ospite_punteggio'] > $partita['casa_punteggio'];
                    @endphp

                    <div style="
                        background: var(--dark-card);
                        border: 1px solid var(--dark-border);
                        border-radius: 12px;
                        padding: 1.5rem 2rem;
                        transition: border-color 0.2s, transform 0.2s;
                    " onmouseover="this.style.borderColor='rgba(29,66,138,0.5)'; this.style.transform='translateX(4px)'"
                       onmouseout="this.style.borderColor='var(--dark-border)'; this.style.transform='none'">

                        <div class="row align-items-center g-3">

                            {{-- SQUADRA CASA --}}
                            <div class="col-5">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    {{-- Nome squadra --}}
                                    <div class="text-end">
                                        <div style="
                                            font-family:'Barlow Condensed',sans-serif;
                                            font-size:1.35rem; font-weight:800;
                                            color: {{ $casaVince ? '#fff' : 'var(--text-secondary)' }};
                                        ">{{ $partita['casa_nome'] }}</div>
                                        <div style="font-size:0.75rem; color:var(--text-secondary); letter-spacing:0.1em; text-transform:uppercase;">
                                            Casa
                                        </div>
                                    </div>
                                    {{-- Abbreviazione squadra --}}
                                    <div style="
                                        font-family:'Barlow Condensed',sans-serif;
                                        font-size:2.2rem; font-weight:900;
                                        color: {{ $casaVince ? 'var(--nba-red)' : 'var(--text-secondary)' }};
                                        min-width:60px; text-align:center;
                                        opacity: {{ $casaVince ? '1' : '0.5' }};
                                    ">{{ $partita['casa_punteggio'] }}</div>
                                </div>
                            </div>

                            {{-- CENTRO: VS / Status --}}
                            <div class="col-2 text-center">
                                @if($partita['in_corso'])
                                    {{-- Partita in corso: pallino rosso animato + periodo --}}
                                    <div style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;">
                                        <span style="width:10px;height:10px;border-radius:50%;background:var(--nba-red);
                                                     display:inline-block;animation:pulse 1s ease-in-out infinite;"></span>
                                        <span style="font-family:'Barlow Condensed',sans-serif;font-size:0.9rem;
                                                     font-weight:700;color:var(--nba-red);">
                                            Q{{ $partita['periodo'] }} {{ $partita['tempo'] }}
                                        </span>
                                    </div>
                                @elseif($partita['finita'])
                                    <span style="font-family:'Barlow Condensed',sans-serif;font-size:0.85rem;
                                                 font-weight:700;color:var(--text-secondary);letter-spacing:0.1em;">
                                        FINALE
                                    </span>
                                @else
                                    <span style="font-family:'Barlow Condensed',sans-serif;font-size:1rem;
                                                 font-weight:900;color:var(--text-secondary);">VS</span>
                                @endif
                            </div>

                            {{-- SQUADRA OSPITE --}}
                            <div class="col-5">
                                <div class="d-flex align-items-center gap-3">
                                    {{-- Punteggio ospite --}}
                                    <div style="
                                        font-family:'Barlow Condensed',sans-serif;
                                        font-size:2.2rem; font-weight:900;
                                        color: {{ $ospiteVince ? 'var(--nba-red)' : 'var(--text-secondary)' }};
                                        min-width:60px; text-align:center;
                                        opacity: {{ $ospiteVince ? '1' : '0.5' }};
                                    ">{{ $partita['ospite_punteggio'] }}</div>
                                    {{-- Nome ospite --}}
                                    <div>
                                        <div style="
                                            font-family:'Barlow Condensed',sans-serif;
                                            font-size:1.35rem; font-weight:800;
                                            color: {{ $ospiteVince ? '#fff' : 'var(--text-secondary)' }};
                                        ">{{ $partita['ospite_nome'] }}</div>
                                        <div style="font-size:0.75rem; color:var(--text-secondary); letter-spacing:0.1em; text-transform:uppercase;">
                                            Trasferta
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- fine .row --}}
                    </div>

                    @endforeach
                </div>
            @endif

        </div>
    </section>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.85); }
        }
    </style>

</x-layout>