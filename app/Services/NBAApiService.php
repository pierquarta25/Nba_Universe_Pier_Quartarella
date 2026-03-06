<?php



namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NBAApiService
{
    private string $baseUrl;
    private string $apiKey;

    // CDN ufficiale NBA (aggiornato 2024 — ak-static è dismesso)
    private string $imgBase = 'https://cdn.nba.com/headshots/nba/latest/1040x760/';

    // Mappatura posizioni API (inglese abbreviato) → italiano leggibile
    private array $ruoliMap = [
        'G'   => 'Guard',
        'F'   => 'Forward',
        'C'   => 'Center',
        'G-F' => 'Guard-Forward',
        'F-G' => 'Forward-Guard',
        'F-C' => 'Forward-Center',
        'C-F' => 'Center-Forward',
        ''    => 'N/D',
    ];

    public function __construct()
    {
        $this->baseUrl = config('services.nba.base_url', 'https://api.balldontlie.io/v1');
        $this->apiKey  = config('services.nba.key', '');
    }

    
    private function get(string $endpoint, array $params = [], int $cacheTtl = 3600): ?array
    {
        // Chiave univoca basata su endpoint + parametri
        $cacheKey = 'nba_api_' . md5($endpoint . serialize($params));

        try {
            return Cache::remember($cacheKey, $cacheTtl, function () use ($endpoint, $params) {
                $response = Http::timeout(10)
                    ->withHeaders(['Authorization' => $this->apiKey])
                    ->get($this->baseUrl . $endpoint, $params);

                // throw() lancia un'eccezione se status != 2xx
                $response->throw();

                return $response->json();
            });

        } catch (\Exception $e) {
            // Logghiamo l'errore senza far crashare il sito
            Log::warning("NBA API error [{$endpoint}]: " . $e->getMessage());
            return null;
        }
    }

    
    // GIOCATORI — endpoint /players con paginazione cursor-based
    public function getGiocatori(int $perPage = 50, int $cursor = 0): ?array
    {
        $params = ['per_page' => $perPage];
        if ($cursor > 0) $params['cursor'] = $cursor;

        return $this->get('/players', $params);
    }

    
    public function getTuttiGiocatori(bool $soloAttivi = true): array
    {
        $cacheKey = 'nba_tutti_giocatori_' . ($soloAttivi ? 'attivi' : 'tutti');

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $tutti     = [];
        $cursor    = null;
        $pagina    = 1;
        $maxPagine = 20;

        do {
            $params = ['per_page' => 100];
            if ($cursor !== null) {
                $params['cursor'] = $cursor;
            }

            try {
                $response = Http::timeout(15)
                    ->withHeaders(['Authorization' => $this->apiKey])
                    ->get($this->baseUrl . '/players', $params);
                $response->throw();
                $json = $response->json();
            } catch (\Exception $e) {
                Log::warning("getTuttiGiocatori errore pagina {$pagina}: " . $e->getMessage());
                break;
            }

            $giocatori = $json['data'] ?? [];

            if ($soloAttivi) {
                $giocatori = array_filter($giocatori, fn($g) => !empty($g['team']['id']));
            }

            $tutti  = array_merge($tutti, array_values($giocatori));
            $cursor = $json['meta']['next_cursor'] ?? null;
            $pagina++;

            if ($cursor !== null) {
                usleep(200000);
            }

        } while ($cursor !== null && $pagina <= $maxPagine);

        usort($tutti, fn($a, $b) => strcmp($a['last_name'] ?? '', $b['last_name'] ?? ''));

        Cache::put($cacheKey, $tutti, 86400);

        return $tutti;
    }

    
    // CERCA GIOCATORI per nome (usato dalla search bar)
    
    public function cercaGiocatori(string $nome): ?array
    {
        return $this->get('/players', [
            'search'   => $nome,
            'per_page' => 25,
        ], cacheTtl: 300); // cache 5 min (ricerche cambiano spesso)
    }

   
    // SINGOLO GIOCATORE per ID API
   
    public function getGiocatore(int $id): ?array
    {
        return $this->get("/players/{$id}");
    }

   
    // STATISTICHE STAGIONE di uno o più giocatori
    
    public function getStats(array $playerIds, int $season = 2024): ?array
    {
        // L'API vuole player_ids[] come array in querystring
        $params = ['season' => $season];
        foreach ($playerIds as $id) {
            $params['player_ids[]'] = $id;
        }

        return $this->get('/season_averages', $params);
    }

   
    // TUTTE LE SQUADRE NBA (30 teams)
    
    public function getSquadre(): ?array
    {
        return $this->get('/teams', ['per_page' => 30], cacheTtl: 86400); // 24h
    }

   
    // PARTITE — filtrabili per data
   
    public function getPartite(string $data = null, int $perPage = 15): ?array
    {
        $data = $data ?? now()->format('Y-m-d');

        return $this->get('/games', [
            'dates[]'  => $data,
            'per_page' => $perPage,
        ], cacheTtl: 120); // cache 2 min: i punteggi cambiano spesso
    }

    
    // PARTITE LIVE (oggi + ieri come fallback)
    
    public function getPartiteOggi(): ?array
    {
        $oggi = $this->getPartite(now()->format('Y-m-d'));

        // Se oggi non ci sono partite (es. off-season), prende ieri
        if (empty($oggi['data'])) {
            return $this->getPartite(now()->subDay()->format('Y-m-d'));
        }

        return $oggi;
    }

    
    public function normalizzaGiocatore(array $g, array $stats = []): array
    {
        $nbaId = $g['id'] ?? 0;

        return [
            'id'          => $nbaId,
            'nba_id'      => $nbaId,
            'nome'        => trim(($g['first_name'] ?? '') . ' ' . ($g['last_name'] ?? '')),
            'squadra'     => $g['team']['full_name']     ?? 'Free Agent',
            'squadra_abb' => $g['team']['abbreviation']  ?? '---',
            'ruolo'       => $this->ruoliMap[$g['position'] ?? ''] ?? ($g['position'] ?? 'N/D'),
            'numero'      => $g['jersey_number']         ?? '—',
            'nazionalita' => $g['country']               ?? 'USA',
            'altezza'     => $g['height']                ?? 'N/D',
            'peso'        => $g['weight']                ?? 'N/D',
            'college'     => $g['college']               ?? 'N/D',
            // Statistiche (dall'endpoint /season_averages)
            'punti'       => $stats['pts']   ?? 0,
            'rimbalzi'    => $stats['reb']   ?? 0,
            'assist'      => $stats['ast']   ?? 0,
            'stoppate'    => $stats['blk']   ?? 0,
            'palle_rubate'=> $stats['stl']   ?? 0,
            'fg_pct'      => isset($stats['fg_pct'])  ? round($stats['fg_pct'] * 100, 1)  : 0,
            'fg3_pct'     => isset($stats['fg3_pct']) ? round($stats['fg3_pct'] * 100, 1) : 0,
            'ft_pct'      => isset($stats['ft_pct'])  ? round($stats['ft_pct'] * 100, 1)  : 0,
            'partite'     => $stats['games_played'] ?? 0,
            'minuti'      => $stats['min']   ?? '0',
            // Immagine dal CDN ufficiale NBA
            'immagine'    => $this->imgBase . $nbaId . '.png',
            'bio'         => '',
            'eta'         => '—',
            // Flag per indicare che i dati vengono dall'API
            'da_api'      => true,
        ];
    }

    
    // NORMALIZZA una partita dall'API
    
    public function normalizzaPartita(array $g): array
    {
        return [
            'id'            => $g['id'],
            'data'          => $g['date']       ?? now()->toDateString(),
            'status'        => $g['status']     ?? 'TBD',
            'periodo'       => $g['period']     ?? 0,
            'tempo'         => $g['time']       ?? '',
            'casa_nome'     => $g['home_team']['full_name']        ?? '—',
            'casa_abb'      => $g['home_team']['abbreviation']     ?? '—',
            'casa_punteggio'=> $g['home_team_score']               ?? 0,
            'ospite_nome'   => $g['visitor_team']['full_name']     ?? '—',
            'ospite_abb'    => $g['visitor_team']['abbreviation']  ?? '—',
            'ospite_punteggio' => $g['visitor_team_score']         ?? 0,
            'in_corso'      => $g['status'] === 'In Progress',
            'finita'        => is_numeric($g['status'] ?? ''),
        ];
    }

    
    // Verificare sempre se la chiave API è configurata nel .env
    
    public function isConfigurata(): bool
    {
        return !empty($this->apiKey);
    }
}