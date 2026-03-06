# 🏀 NBA Universe

Progetto Laravel 12 — sito dedicato alla NBA con giocatori, partite e blog.

---

## Requisiti

- PHP >= 8.2
- Composer
- Node.js (opzionale, solo se usi Vite)
- Laravel 12

---

## Installazione

```bash
# 1. Clona il progetto
git clone <repo-url>
cd Laravel_NBA_Pier_Quartarella

# 2. Installa le dipendenze PHP
composer install

# 3. Copia il file di configurazione
cp .env.example .env

# 4. Genera la chiave dell'applicazione
php artisan key:generate

# 5. Avvia il server locale
php artisan serve
```

Il sito sarà disponibile su `http://127.0.0.1:8000`

---

## Configurazione API (opzionale)

Il sito funziona senza API key usando **dati statici** (50 giocatori).

Per abilitare i dati reali dalla stagione NBA corrente:

```env
# .env
NBA_API_KEY=la_tua_chiave_da_balldontlie.io
```

Ottieni una chiave gratuita su [balldontlie.io](https://app.balldontlie.io)

Dopo aver aggiunto la chiave, esegui la sincronizzazione:

```bash
php artisan cache:clear
php artisan sync:giocatori
```

---

## Struttura pagine

| URL | Descrizione |
|-----|-------------|
| `/` | Home — statistiche rapide, ultime partite, top giocatori |
| `/giocatori` | Lista dei 50 giocatori ordinati per PPG |
| `/giocatori/{id}` | Scheda dettaglio giocatore |
| `/partite` | Risultati e partite in programma |
| `/blog` | Articoli e analisi NBA |

---

## Comandi Artisan

```bash
# Sincronizza giocatori dalla API (richiede NBA_API_KEY)
php artisan sync:giocatori

# Svuota la cache
php artisan cache:clear

# Svuota la cache delle viste Blade
php artisan view:clear
```

---

## Struttura del progetto

```
app/
├── Console/Commands/
│   └── SyncGiocatori.php       # Comando sync giocatori da API
├── Http/Controllers/
│   └── NBAController.php       # Controller principale
├── Mail/
│   └── ContactMail.php         # Mail form contatti
└── Services/
    └── NBAApiService.php        # Servizio API balldontlie.io

resources/views/
├── components/
│   ├── layout.blade.php         # Layout principale
│   ├── navbar.blade.php         # Navbar con menu a tendina
│   ├── footer.blade.php         # Footer con form contatti
│   ├── header.blade.php         # Hero header pagine
│   └── card.blade.php           # Card giocatore riutilizzabile
├── errors/
│   └── 404.blade.php            # Pagina errore 404
├── home.blade.php
├── giocatori.blade.php
├── show.blade.php
├── partite.blade.php
└── blog.blade.php

public/
├── css/
│   └── style.css               # CSS custom + responsive
├── media/
│   ├── favicon.png
│   └── favicon.ico
└── js/
    └── main.js

routes/
└── web.php                     # Rotte del sito
```

---

## Tecnologie usate

- **Laravel 12** — framework PHP
- **Bootstrap 5.3** — griglia e componenti UI
- **Barlow Condensed + Inter** — font Google
- **balldontlie.io API** — dati NBA (giocatori, partite, statistiche)
- **cdn.nba.com** — foto ufficiali giocatori

---

## Note

- I dati statici (50 giocatori) sono aggiornati alla stagione 2025-26
- Le immagini vengono caricate dal CDN ufficiale NBA tramite `nba_id`
- La sincronizzazione automatica è schedulata ogni giorno alle 04:00
- La pagina 404 è completamente standalone (nessuna dipendenza Blade)

---

## Autore

**Pierfilippo Quartarella** — Progetto Aulab 