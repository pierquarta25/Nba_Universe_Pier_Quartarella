<?php



namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncGiocatori extends Command
{
    protected $signature   = 'sync:giocatori';
    protected $description = 'Scarica i giocatori NBA attivi con statistiche stagione 2025-26';

    private array $ruoliMap = [
        'G'   => 'Guard',        'F'   => 'Forward',
        'C'   => 'Center',       'G-F' => 'Guard-Forward',
        'F-G' => 'Forward-Guard','F-C' => 'Forward-Center',
        'C-F' => 'Center-Forward', ''  => 'N/D',
    ];

    private string $apiKey;
    private string $baseUrl;
    private string $imgBase = 'https://ak-static.cms.nba.com/wp-content/uploads/headshots/nba/latest/260x190/';

    public function handle(): int
    {
        $this->apiKey  = config('services.nba.key', '');
        $this->baseUrl = config('services.nba.base_url', 'https://api.balldontlie.io/v1');

        if (empty($this->apiKey)) {
            $this->error('NBA_API_KEY non configurata nel .env!');
            return Command::FAILURE;
        }

        $this->info('');
        $this->info('🏀 NBA Universe — Sync Giocatori Stagione 2025-26');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $start = microtime(true);

        // ── STEP 1: Scarica tutti i profili giocatori 
        $this->info('');
        $this->line('👤 Step 1/3 — Download profili giocatori...');

        $profili = $this->scaricaTuttiProfili();

        if (empty($profili)) {
            $this->error('Nessun giocatore trovato. Controlla la API Key nel .env');
            $this->line('  Chiave attuale: ' . substr($this->apiKey, 0, 8) . '...');
            return Command::FAILURE;
        }

        $this->line('   ✓ ' . count($profili) . ' giocatori trovati');

        // ── STEP 2: Filtra solo chi ha una squadra (attivi) 
        $attivi = array_filter($profili, fn($g) => !empty($g['team']['id']));
        $attivi = array_values($attivi);
        $this->line('   ✓ ' . count($attivi) . ' con squadra assegnata (attivi)');

        // ── STEP 3: Scarica statistiche in batch da 25
        $this->info('');
        $this->line('📊 Step 2/3 — Download statistiche (batch da 25)...');

        $ids   = array_column($attivi, 'id');
        $stats = $this->scaricaStatsBatch($ids, 2025);

        // Fallback: prova con stagione 2024 se 2025 non ha dati
        if (empty($stats)) {
            $this->line('   ⚠ Nessuna stat per il 2025-26, provo con 2024-25...');
            $stats = $this->scaricaStatsBatch($ids, 2024);
        }

        $this->line('   ✓ Statistiche trovate per ' . count($stats) . ' giocatori');

        // ── STEP 4: Unisci e salva 
        $this->info('');
        $this->line('🔗 Step 3/3 — Unione dati e salvataggio in cache...');

        $giocatori = [];
        foreach ($attivi as $p) {
            $s            = $stats[$p['id']] ?? [];
            $giocatori[]  = $this->normalizza($p, $s);
        }

        // Ordina per cognome
        usort($giocatori, fn($a, $b) => strcmp($a['cognome'], $b['cognome']));

        Cache::put('nba_tutti_giocatori_attivi', $giocatori, 86400);

        // ── Risultato 
        $durata = round(microtime(true) - $start, 1);

        $this->info('');
        $this->info("✅ Sync completato in {$durata}s");
        $this->info('');
        $this->table(['', ''], [
            ['Giocatori attivi',  count($giocatori)],
            ['Con statistiche',   count($stats)],
            ['Cache valida fino', now()->addDay()->format('d/m/Y H:i')],
        ]);
        $this->info('');
        $this->line('💡 Visita /giocatori per vedere i dati aggiornati');

        return Command::SUCCESS;
    }

    // ── Scarica TUTTI i profili (tutte le pagine)
    private function scaricaTuttiProfili(): array
    {
        $tutti  = [];
        $cursor = null;
        $pagina = 1;

        do {
            $params = ['per_page' => 100];
            if ($cursor !== null) $params['cursor'] = $cursor;

            try {
                $response = Http::timeout(20)
                    ->withHeaders(['Authorization' => $this->apiKey])
                    ->get($this->baseUrl . '/players', $params);

                if ($response->status() === 401) {
                    $this->error('API Key non valida (401 Unauthorized)');
                    $this->line('  → Vai su https://app.balldontlie.io e controlla la tua chiave');
                    return [];
                }

                $response->throw();
                $json = $response->json();

            } catch (\Exception $e) {
                Log::warning("scaricaTuttiProfili p{$pagina}: " . $e->getMessage());
                break;
            }

            $tutti  = array_merge($tutti, $json['data'] ?? []);
            $cursor = $json['meta']['next_cursor'] ?? null;
            $pagina++;

            // Progress ogni 5 pagine
            if ($pagina % 5 === 0) {
                $this->line("   ... pagina {$pagina}, totale: " . count($tutti));
            }

            if ($cursor) usleep(300000); // 0.3s tra chiamate

        } while ($cursor !== null && $pagina <= 25);

        return $tutti;
    }

    // ── Scarica statistiche in batch da 25 ID alla volta 
    // balldontlie.io richiede player_ids[] espliciti, non supporta
    // "dammi tutti" sull'endpoint /season_averages
    private function scaricaStatsBatch(array $ids, int $season): array
    {
        $stats  = [];
        $chunks = array_chunk($ids, 25); // max 25 per chiamata
        $totale = count($chunks);

        foreach ($chunks as $i => $batch) {
            // Costruiamo la query manualmente perché l'HTTP client
            // di Laravel non gestisce array ripetuti correttamente
            $query = 'season=' . $season;
            foreach ($batch as $id) {
                $query .= '&player_ids[]=' . $id;
            }

            try {
                $response = Http::timeout(20)
                    ->withHeaders(['Authorization' => $this->apiKey])
                    ->get($this->baseUrl . '/season_averages?' . $query);

                if (!$response->successful()) {
                    Log::warning("Stats batch {$i}: HTTP " . $response->status());
                    continue;
                }

                $json = $response->json();

            } catch (\Exception $e) {
                Log::warning("Stats batch {$i}: " . $e->getMessage());
                continue;
            }

            foreach ($json['data'] ?? [] as $s) {
                // L'API può restituire player_id diretto o annidato in player.id
                $pid = $s['player_id'] ?? ($s['player']['id'] ?? null);
                if ($pid) $stats[$pid] = $s;
            }

            // Progress ogni 10 batch
            if (($i + 1) % 10 === 0) {
                $this->line("   ... batch " . ($i + 1) . "/{$totale}, stats trovate: " . count($stats));
            }

            usleep(300000); // 0.3s tra batch
        }

        return $stats;
    }

    // ── Normalizza un record API al formato viste 
    private function normalizza(array $g, array $s): array
    {
        $nbaId   = $g['id'];
        $cognome = $g['last_name'] ?? '';

        return [
            'id'           => $nbaId,
            'nba_id'       => $nbaId,
            'nome'         => trim(($g['first_name'] ?? '') . ' ' . $cognome),
            'cognome'      => $cognome,
            'squadra'      => $g['team']['full_name']    ?? 'Free Agent',
            'squadra_abb'  => $g['team']['abbreviation'] ?? '---',
            'ruolo'        => $this->ruoliMap[$g['position'] ?? ''] ?? ($g['position'] ?? 'N/D'),
            'numero'       => $g['jersey_number']        ?? '—',
            'nazionalita'  => $g['country']              ?? 'USA',
            'altezza'      => $g['height']               ?? 'N/D',
            'peso'         => $g['weight']               ?? 'N/D',
            'college'      => $g['college']              ?? 'N/D',
            'eta'          => '—',
            'bio'          => '',
            // Statistiche (vuote se il piano API non le include)
            'punti'        => round($s['pts']  ?? 0, 1),
            'rimbalzi'     => round($s['reb']  ?? 0, 1),
            'assist'       => round($s['ast']  ?? 0, 1),
            'stoppate'     => round($s['blk']  ?? 0, 1),
            'palle_rubate' => round($s['stl']  ?? 0, 1),
            'fg_pct'       => isset($s['fg_pct'])  ? round($s['fg_pct']  * 100, 1) : 0,
            'fg3_pct'      => isset($s['fg3_pct']) ? round($s['fg3_pct'] * 100, 1) : 0,
            'ft_pct'       => isset($s['ft_pct'])  ? round($s['ft_pct']  * 100, 1) : 0,
            'partite'      => $s['games_played'] ?? 0,
            'minuti'       => $s['min']          ?? '0',
            'immagine'     => $this->imgBase . $nbaId . '.png',
            'da_api'       => true,
        ];
    }
}