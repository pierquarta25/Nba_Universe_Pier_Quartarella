/* 
   1. NAVBAR — diventa più solida quando si scrolla la pagina.
   La classe CSS "scrolled" aumenta l'opacità dello sfondo.
                                                                */
(function initNavbar() {
    const nav = document.getElementById('mainNav');
    if (!nav) return; // esci se la navbar non esiste sulla pagina

    window.addEventListener('scroll', () => {
        // classList.toggle(classe, condizione):
        // aggiunge "scrolled" se scrollY > 20, la rimuove altrimenti
        nav.classList.toggle('scrolled', window.scrollY > 20);
    });
})();


/* 
   2. HEADER — effetto parallax leggero sull'immagine di sfondo.
   L'immagine parte scalata a 1.05 (CSS) e torna a 1 (JS),
   creando un effetto di "entrata" al caricamento della pagina.
                                                                */
(function initHeaderParallax() {
    document.addEventListener('DOMContentLoaded', () => {
        const bg = document.getElementById('headerBg');
        if (!bg) return; // esci se l'header non è presente

        // Piccolo ritardo per far partire la transizione CSS
        setTimeout(() => {
            bg.style.transform = 'scale(1)';
        }, 100);
    });
})();



(function initStatBars() {
    document.addEventListener('DOMContentLoaded', () => {
        const bars = document.querySelectorAll('.stat-bar-fill');
        if (bars.length === 0) return; // esci se non ci sono barre

        setTimeout(() => {
            bars.forEach(bar => {
                // Ogni barra ha data-value="42" (es: 42%)
                const targetWidth = bar.getAttribute('data-value');
                if (targetWidth) {
                    bar.style.width = targetWidth + '%';
                }
            });
        }, 300);
    });
})();


/* 
   4. GIOCATORI — filtro di ricerca live.
   Chiamata da: oninput="filtraGiocatori()" sull'<input>.

   Come funziona:
   - Legge il testo digitato dall'utente
   - Per ogni .col (wrapper Bootstrap) legge i data-* della card
   - Nasconde le colonne che non corrispondono alla ricerca
   - Aggiorna il contatore e il messaggio "nessun risultato"
                                                                 */
function filtraGiocatori() {
    const input    = document.getElementById('searchInput');
    const counter  = document.getElementById('counter');
    const noResult = document.getElementById('noResults');

    if (!input) return;

    // toLowerCase() = non distingue maiuscole/minuscole
    // trim() = rimuove spazi iniziali/finali
    const termine = input.value.toLowerCase().trim();

    // Selezioniamo i .col di Bootstrap che wrappano le card.
    // IMPORTANTE: nascondiamo il .col (non solo la card),
    // altrimenti la griglia Bootstrap lascia spazi vuoti.
    const colonne = document.querySelectorAll('#playersGrid .col');

    let visibili = 0;

    colonne.forEach(col => {
        const card    = col.querySelector('.player-card');
        const nome    = card?.getAttribute('data-nome')    || '';
        const squadra = card?.getAttribute('data-squadra') || '';
        const ruolo   = card?.getAttribute('data-ruolo')   || '';

        // String.includes() → true se il termine è contenuto
        if (nome.includes(termine) || squadra.includes(termine) || ruolo.includes(termine)) {
            col.style.display = '';     // mostra il .col
            visibili++;
        } else {
            col.style.display = 'none'; // nasconde il .col
        }
    });

    // Aggiorna il contatore in tempo reale
    if (counter) counter.textContent = visibili;

    // Mostra/nasconde il messaggio "nessun risultato"
    if (noResult) {
        noResult.style.display = visibili === 0 ? 'block' : 'none';
    }
}


/* 
   5. POPUP SUCCESSO — chiude il popup del form contatti.

   NOTA: la funzione che MOSTRA il popup (mostraPopup) rimane
   inline nel footer.blade.php perché usa @json(session('successo'))
   che è sintassi Blade e non può stare in un file .js esterno.
   Qui invece teniamo solo chiudiPopup() che è puro JavaScript.
                                                                     */
function chiudiPopup() {
    const popup = document.getElementById('successPopup');
    if (!popup) return;

    // Animazione di uscita via JS (CSS inline)
    popup.style.transition = 'opacity 0.3s, transform 0.3s';
    popup.style.opacity    = '0';
    popup.style.transform  = 'translateY(10px)';

    // Dopo la transizione, nasconde completamente il popup
    setTimeout(() => {
        popup.style.display    = 'none';
        popup.style.opacity    = '';
        popup.style.transform  = '';
        popup.style.transition = '';
    }, 320);
}
