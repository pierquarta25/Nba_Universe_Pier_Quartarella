@props([
    'giocatore',
    'size'    => 'small',
    'showBio' => false,
])

@php
    $isLarge  = $size === 'large';
    $imgH     = $isLarge ? '220px' : '155px';
    $statSize = $isLarge ? '1.4rem' : '1.15rem';
    $nameSize = $isLarge ? '1.75rem' : '1.15rem';
@endphp

<a href="{{ route('giocatori.show', $giocatore['id']) }}"
   class="card player-card h-100"
   data-nome="{{ strtolower($giocatore['nome']) }}"
   data-squadra="{{ strtolower($giocatore['squadra']) }}"
   data-ruolo="{{ strtolower($giocatore['ruolo']) }}"
   style="border-radius:12px; overflow:hidden; text-decoration:none;">

    {{-- IMMAGINE --}}
    <div style="height:{{ $imgH }}; overflow:hidden; position:relative; flex-shrink:0; background:#0d1b3e;">

        <img
            src="{{ $giocatore['immagine'] }}"
            alt="{{ $giocatore['nome'] }}"
            class="w-100 h-100"
            style="object-fit:cover; object-position:top center; display:block;"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >

        {{-- Placeholder mostrato solo se foto non disponibile --}}
        <div style="display:none; position:absolute; inset:0;
                    align-items:center; justify-content:center; flex-direction:column;
                    background:linear-gradient(160deg, #1D428A 0%, #0a1628 100%);">
            <div style="font-size:2.5rem; opacity:0.3;">🏀</div>
            <div style="color:rgba(255,255,255,0.3); font-size:0.65rem; letter-spacing:0.12em; margin-top:0.4rem;">N/D</div>
        </div>

        {{-- Numero maglia --}}
        <span style="position:absolute; top:0.5rem; right:0.5rem;
                     background:rgba(0,0,0,0.75); color:var(--nba-red);
                     font-family:'Barlow Condensed',sans-serif; font-size:0.85rem; font-weight:700;
                     padding:0.15rem 0.5rem; border-radius:4px; backdrop-filter:blur(4px);">
            #{{ $giocatore['numero'] }}
        </span>

        @if($isLarge)
        <span style="position:absolute; bottom:0.5rem; left:0.5rem;
                     background:rgba(0,0,0,0.7); color:rgba(255,255,255,0.7);
                     font-size:0.72rem; padding:0.15rem 0.5rem; border-radius:4px;">
            🌍 {{ $giocatore['nazionalita'] }}
        </span>
        @endif
    </div>

    {{-- CORPO --}}
    <div class="card-body p-3 d-flex flex-column">

        <h5 class="card-title text-truncate mb-1"
            style="font-family:'Barlow Condensed',sans-serif; font-size:{{ $nameSize }};
                   font-weight:800; color:var(--text-primary);">
            {{ $giocatore['nome'] }}
        </h5>

        <p class="card-text small text-truncate mb-2" style="color:var(--text-secondary);">
            {{ $giocatore['squadra'] }}
        </p>

        <div class="mb-2 d-flex align-items-center gap-2">
            <span class="badge-ruolo">{{ $giocatore['ruolo'] }}</span>
            @if($isLarge && !empty($giocatore['eta']) && $giocatore['eta'] !== '—')
                <span class="small" style="color:var(--text-secondary);">{{ $giocatore['eta'] }} anni</span>
            @endif
        </div>

        @if($showBio && !empty($giocatore['bio']))
        <p class="card-text small mb-3"
           style="color:var(--text-secondary); line-height:1.6;
                  display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
            {{ $giocatore['bio'] }}
        </p>
        @endif

        {{-- STATS --}}
        <div class="row g-1 mt-auto pt-2" style="border-top:1px solid var(--dark-border);">
            @foreach([
                ['val' => $giocatore['punti'],    'label' => 'PPG', 'color' => 'var(--nba-red)'],
                ['val' => $giocatore['rimbalzi'], 'label' => 'RPG', 'color' => 'var(--text-primary)'],
                ['val' => $giocatore['assist'],   'label' => 'APG', 'color' => 'var(--text-primary)'],
            ] as $stat)
            <div class="col-4 text-center">
                <div style="font-family:'Barlow Condensed',sans-serif; font-size:{{ $statSize }};
                            font-weight:700; color:{{ $stat['color'] }}; line-height:1.1;">
                    {{ $stat['val'] }}
                </div>
                <div style="color:var(--text-secondary); font-size:0.6rem;
                            text-transform:uppercase; letter-spacing:0.05em;">
                    {{ $stat['label'] }}
                </div>
            </div>
            @endforeach
        </div>

    </div>
</a>