@php
    $currentRoute = Route::currentRouteName();
    $links = [
        ['route' => 'home',     'label' => 'Home',    'icon' => '🏠'],
        ['route' => 'blog',     'label' => 'Blog',    'icon' => '📰'],
        ['route' => 'partite',  'label' => 'Partite', 'icon' => '📅'],
        ['route' => 'giocatori','label' => 'Tutti i Giocatori', 'icon' => '⭐', 'cta' => true],
    ];
@endphp

<nav id="mainNav" style="position:sticky; top:0; z-index:1000;
     background:rgba(10,10,15,0.92); backdrop-filter:blur(12px);
     border-bottom:1px solid rgba(255,255,255,0.07); padding:0.75rem 0;">
    <div class="container d-flex align-items-center justify-content-between">

        {{-- LOGO --}}
        <a href="{{ route('home') }}"
           style="font-family:'Barlow Condensed',sans-serif; font-size:1.7rem;
                  font-weight:900; letter-spacing:0.05em; color:#fff;
                  text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem;">
            <span style="background:var(--nba-red); color:#fff;
                         padding:0.1rem 0.55rem; border-radius:4px; line-height:1;">NBA</span>
            UNIVERSE
        </a>

        {{-- PULSANTE MENU --}}
        <button id="navBtn" onclick="navToggle(event)"
                style="background:none; border:2px solid rgba(255,255,255,0.15);
                       border-radius:10px; padding:0.4rem 0.8rem; cursor:pointer;
                       display:flex; align-items:center; gap:0.45rem; transition:border-color 0.2s;
                       position:relative; z-index:1001;"
                onmouseover="this.style.borderColor='var(--nba-red)'"
                onmouseout="this.style.borderColor='rgba(255,255,255,0.15)'">
            <span style="font-size:1.3rem; line-height:1;">🏀</span>
            <svg id="navChevron" width="12" height="8" viewBox="0 0 12 8" fill="none"
                 style="transition:transform 0.25s; pointer-events:none;">
                <path d="M1 1l5 5 5-5" stroke="rgba(255,255,255,0.6)"
                      stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

    </div>
</nav>

{{-- MENU DROPDOWN — fuori dal nav per evitare problemi di overflow --}}
<div id="navMenu"
     style="display:none; position:fixed; z-index:9999;
            background:#12121a; border:1px solid var(--dark-border);
            border-radius:14px; min-width:220px; overflow:hidden;
            box-shadow:0 24px 48px rgba(0,0,0,0.7);">

    @foreach($links as $link)
        @if(!empty($link['cta']))
            <a href="{{ route($link['route']) }}"
               style="display:flex; align-items:center; gap:0.75rem;
                      padding:0.9rem 1.25rem; background:var(--nba-blue);
                      color:#fff; text-decoration:none; font-size:0.95rem;
                      font-family:'Barlow Condensed',sans-serif; font-weight:700;
                      letter-spacing:0.06em; text-transform:uppercase; transition:background 0.15s;"
               onmouseover="this.style.background='#152f61'"
               onmouseout="this.style.background='var(--nba-blue)'">
                <span>{{ $link['icon'] }}</span> {{ $link['label'] }} →
            </a>
        @else
            @php
                $isActive = $currentRoute === $link['route']
                    || ($link['route'] === 'giocatori' && $currentRoute === 'giocatori.show');
            @endphp
            <a href="{{ route($link['route']) }}"
               style="display:flex; align-items:center; gap:0.75rem;
                      padding:0.85rem 1.25rem;
                      color:{{ $isActive ? '#fff' : 'var(--text-secondary)' }};
                      background:{{ $isActive ? 'rgba(29,66,138,0.2)' : 'transparent' }};
                      text-decoration:none; font-size:0.95rem; font-weight:500;
                      border-bottom:1px solid var(--dark-border); transition:background 0.15s, color 0.15s;"
               onmouseover="this.style.background='rgba(255,255,255,0.06)'; this.style.color='#fff'"
               onmouseout="this.style.background='{{ $isActive ? 'rgba(29,66,138,0.2)' : 'transparent' }}'; this.style.color='{{ $isActive ? '#fff' : 'var(--text-secondary)' }}'">
                <span>{{ $link['icon'] }}</span> {{ $link['label'] }}
                @if($isActive)
                    <span style="margin-left:auto; width:6px; height:6px; border-radius:50%; background:var(--nba-red);"></span>
                @endif
            </a>
        @endif
    @endforeach
</div>

<script>
function navToggle(e) {
    e.stopPropagation();
    var btn     = document.getElementById('navBtn');
    var menu    = document.getElementById('navMenu');
    var chevron = document.getElementById('navChevron');
    var open    = menu.style.display === 'block';

    if (open) {
        menu.style.display = 'none';
        chevron.style.transform = '';
        return;
    }

    // Posiziona il menu
    var rect = btn.getBoundingClientRect();
    var top  = rect.bottom + window.scrollY + 8;
    var right = window.innerWidth - rect.right;

    menu.style.top     = top + 'px';
    menu.style.right   = right + 'px';
    menu.style.left    = 'auto';
    menu.style.display = 'block';
    chevron.style.transform = 'rotate(180deg)';
}

function navClose() {
    var menu = document.getElementById('navMenu');
    var chevron = document.getElementById('navChevron');
    if (menu) menu.style.display = 'none';
    if (chevron) chevron.style.transform = '';
}

document.addEventListener('click', navClose);
window.addEventListener('resize', navClose);
</script>