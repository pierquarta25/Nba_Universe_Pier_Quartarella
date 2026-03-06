
<div id="successPopup" role="alert" style="
    display:none;
    position:fixed; bottom:2rem; right:2rem; z-index:9999;
    background:var(--nba-blue); border:1px solid var(--nba-red);
    border-radius:12px; padding:1.25rem 1.75rem;
    max-width:340px; min-width:280px;
    box-shadow:0 20px 60px rgba(0,0,0,0.5);
    animation: slideUp 0.4s ease;
">
    <button onclick="chiudiPopup()" style="
        position:absolute; top:0.6rem; right:0.8rem;
        background:none; border:none; color:rgba(255,255,255,0.5);
        font-size:1.1rem; cursor:pointer; line-height:1;
    " aria-label="Chiudi">✕</button>
    <div style="font-size:2rem; margin-bottom:0.4rem;">🏀</div>
    <div style="font-family:'Barlow Condensed',sans-serif; font-size:1.3rem; font-weight:800; color:#fff; margin-bottom:0.3rem;">
        Messaggio Inviato!
    </div>
    <div id="popupMsg" style="font-size:0.85rem; color:rgba(255,255,255,0.7); line-height:1.5;"></div>
</div>

{{-- Gli stili sono in public/css/style.css --}}

<footer style="
    background:#03030a;
    border-top: 3px solid var(--nba-blue);
    padding: 5rem 0 2rem;
    margin-top: 5rem;
">
    <div class="container">

        
        <div class="row g-5 mb-5">

            {{-- Colonna 1: Logo e descrizione --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="d-flex align-items-center gap-2 mb-3"
                     style="font-family:'Barlow Condensed',sans-serif; font-size:1.7rem; font-weight:900;">
                    <span style="background:var(--nba-red);color:#fff;padding:0.1rem 0.55rem;border-radius:4px;">NBA</span>
                    <span style="color:#fff;">UNIVERSE</span>
                </div>
                <p style="color:rgba(255,255,255,0.4); font-size:0.86rem; line-height:1.8; max-width:240px;">
                    Il tuo portale completo dedicato alla NBA: statistiche, profili e tutto sul basket professionistico americano.
                </p>
                {{-- Barre colori NBA --}}
                <div class="d-flex gap-2 mt-3">
                    <span style="width:28px;height:5px;background:var(--nba-blue);border-radius:3px;display:block;"></span>
                    <span style="width:28px;height:5px;background:var(--nba-red);border-radius:3px;display:block;"></span>
                    <span style="width:28px;height:5px;background:rgba(255,255,255,0.2);border-radius:3px;display:block;"></span>
                </div>
            </div>

            {{-- Colonna 2: Navigazione --}}
            <div class="col-6 col-md-3 col-lg-2">
                <h3 style="font-family:'Barlow Condensed',sans-serif;font-size:0.8rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:var(--nba-red);margin-bottom:1.1rem;">
                    Navigazione
                </h3>
                <a href="{{ route('home') }}"      class="footer-link">→ Home</a>
                <a href="{{ route('giocatori') }}" class="footer-link">→ Giocatori</a>
                <a href="{{ route('blog') }}"      class="footer-link">→ Blog NBA</a>
            </div>

            {{-- Colonna 3: Contatti info --}}
            <div class="col-6 col-md-3 col-lg-2">
                <h3 style="font-family:'Barlow Condensed',sans-serif;font-size:0.8rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:var(--nba-red);margin-bottom:1.1rem;">
                    Info
                </h3>
                <div style="color:rgba(255,255,255,0.4);font-size:0.84rem;line-height:1.7;">
                    <strong style="color:rgba(255,255,255,0.7);display:block;">Email</strong>
                    info@nbauniverse.it
                </div>
                <div style="color:rgba(255,255,255,0.4);font-size:0.84rem;line-height:1.7;margin-top:0.75rem;">
                    <strong style="color:rgba(255,255,255,0.7);display:block;">Social</strong>
                    @NBAUniverse_IT
                </div>
            </div>

            {{-- Colonna 4: FORM DI CONTATTO --}}
            <div class="col-12 col-lg-5">
                <h3 style="font-family:'Barlow Condensed',sans-serif;font-size:0.8rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:var(--nba-red);margin-bottom:1.1rem;">
                    Scrivici
                </h3>

                {{-- FORM DI CONTATTO CON BOOTSTRAP --}}
                <form action="{{ route('contatti') }}" method="POST"
                      class="footer-form" novalidate>
                    @csrf

                    {{-- Campo Email --}}
                    <div class="mb-3">
                        <label for="footerEmail" class="form-label">La tua Email *</label>
                        <input
                            type="email"
                            id="footerEmail"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="tua@email.com"
                            value="{{ old('email') }}"
                        >
                        {{-- @error → classe Bootstrap is-invalid + messaggio --}}
                        @error('email')
                            <div class="invalid-feedback">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Campo Oggetto --}}
                    <div class="mb-3">
                        <label for="footerOggetto" class="form-label">Oggetto *</label>
                        <input
                            type="text"
                            id="footerOggetto"
                            name="oggetto"
                            class="form-control @error('oggetto') is-invalid @enderror"
                            placeholder="Es: Informazioni sui giocatori"
                            value="{{ old('oggetto') }}"
                        >
                        @error('oggetto')
                            <div class="invalid-feedback">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Campo Messaggio --}}
                    <div class="mb-3">
                        <label for="footerMessaggio" class="form-label">Messaggio *</label>
                        <textarea
                            id="footerMessaggio"
                            name="messaggio"
                            class="form-control @error('messaggio') is-invalid @enderror"
                            placeholder="Scrivi il tuo messaggio..."
                            rows="3"
                        >{{ old('messaggio') }}</textarea>
                        @error('messaggio')
                            <div class="invalid-feedback">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Pulsante → btn Bootstrap colore rosso NBA --}}
                    <button type="submit" class="btn btn-danger w-100"
                            style="font-size:1.05rem; padding:0.7rem; letter-spacing:0.06em;">
                        🏀 Invia Messaggio
                    </button>

                </form>
            </div>

        </div>{{-- fine .row --}}

        {{-- Separatore --}}
        <hr style="border-color:rgba(255,255,255,0.07); margin-bottom:1.5rem;">

        {{-- Copyright --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <p class="mb-0" style="color:rgba(255,255,255,0.22); font-size:0.78rem;">
                &copy; {{ date('Y') }} NBA Universe — Progetto Laravel di Pierfilippo Quartarella
            </p>
            <p class="mb-0" style="color:rgba(255,255,255,0.22); font-size:0.78rem;">
                Realizzato con ❤️ da Pierfilippo Quartarella
            </p>
        </div>

    </div>
</footer>


<script>
    const msg = @json(session('successo'));
    if (msg) {
        document.getElementById('popupMsg').textContent = msg;
        document.getElementById('successPopup').style.display = 'block';
        setTimeout(chiudiPopup, 6000);
    }
</script>