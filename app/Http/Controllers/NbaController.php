<?php


// INTEGRAZIONE API — Pattern "API-first con fallback":
//   1. API configurata nel .env? → usa dati reali
//   2. API offline o chiave mancante? → dati statici
//   Il sito funziona SEMPRE in entrambi i casi.


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use App\Services\NBAApiService;

class NBAController extends Controller
{
    // Dependency Injection: Laravel istanzia NBAApiService
    // automaticamente e lo passa al costruttore
    public function __construct(
        private NBAApiService $api
    ) {}

    // CDN ufficiale NBA (aggiornato 2024 — ak-static è dismesso)
    // Formato: https://cdn.nba.com/headshots/nba/latest/1040x760/{nba_id}.png
    private string $nbaImgBase = 'https://cdn.nba.com/headshots/nba/latest/1040x760/';

   
    // HOME 
    public function home()
    {
        $tuttiGiocatori = $this->withImages($this->giocatoriData());
        usort($tuttiGiocatori, fn($a, $b) => $b['punti'] <=> $a['punti']);
        $giocatoriInEvidenza = array_slice($tuttiGiocatori, 0, 3);

        $partite = [];
        $daApi   = false;

        if ($this->api->isConfigurata()) {
            $risposta = $this->api->getPartiteOggi();
            if (!empty($risposta['data'])) {
                $partite = array_map(fn($p) => $this->api->normalizzaPartita($p), array_slice($risposta['data'], 0, 6));
                $daApi   = true;
            }
        }

        if (empty($partite)) {
            $partite = $this->partiteStatiche();
        }

        return view('home', compact('giocatoriInEvidenza', 'partite', 'daApi'));
    }

    // =====================================================
    // GIOCATORI — /giocatori
    // =====================================================
    public function giocatori()
    {
        // Solo dati statici — ordinati per media punti (PPG) decrescente
        $giocatori = $this->withImages($this->giocatoriData());
        usort($giocatori, fn($a, $b) => $b['punti'] <=> $a['punti']);
        $daApi = false;

        return view('giocatori', compact('giocatori', 'daApi'));
    }

    
    // SHOW — /giocatori/{id}
    // Usa solo i dati statici — nessuna API, nessuna cache.
    // L'id nell'URL corrisponde sempre all'id statico (1-50).
    public function show(int $id)
    {
        $tutti     = $this->withImages($this->giocatoriData());
        $trovati   = array_filter($tutti, fn($g) => $g['id'] === $id);
        $giocatore = array_values($trovati)[0] ?? null;

        if (!$giocatore) abort(404);

        $statsReali = null;
        return view('show', compact('giocatore', 'statsReali'));
    }

    
    // PARTITE
    public function partite(Request $request)
    {
        $data    = $request->query('data', now()->format('Y-m-d'));
        $partite = [];
        $daApi   = false;

        if ($this->api->isConfigurata()) {
            $risposta = $this->api->getPartite($data);
            if (!empty($risposta['data'])) {
                $partite = array_map(fn($p) => $this->api->normalizzaPartita($p), $risposta['data']);
                $daApi   = true;
            }
        }

        if (empty($partite)) {
            $partite = $this->partiteStatiche();
        }

        return view('partite', compact('partite', 'data', 'daApi'));
    }

    
    // BLOG 
    public function blog()
    {
        $articoli = [
            ['id'=>1, 'titolo'=>"L'Era dei Tiratori: Come Curry Ha Cambiato la NBA per Sempre", 'categoria'=>'Analisi Tattica', 'data'=>'3 Gennaio 2025', 'autore'=>'Marco Ferretti', 'immagine'=>'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=800&q=80', 'sommario'=>"Prima dell'avvento di Stephen Curry, la linea dei tre punti era considerata quasi un'opzione di emergenza.", 'testo'=>"Oggi ogni squadra NBA schiera almeno due o tre tiratori dal perimetro.", 'lettura'=>'5 min', 'colore'=>'#1D428A'],
            ['id'=>2, 'titolo'=>'Wembanyama: Il Futuro della NBA è Già Arrivato', 'categoria'=>'Giovani Stelle', 'data'=>'29 Dicembre 2024', 'autore'=>'Laura Bianchi', 'immagine'=>'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?w=800&q=80', 'sommario'=>"Victor Wembanyama è un fenomeno irripetibile.", 'testo'=>"La sua combinazione di 2,24m con abilità tecniche da guardia è inedita.", 'lettura'=>'4 min', 'colore'=>'#C8102E'],
            ['id'=>3, 'titolo'=>'Il Dominio Europeo: Come il Vecchio Continente Ha Conquistato la NBA', 'categoria'=>'Internazionale', 'data'=>'22 Dicembre 2024', 'autore'=>'Andrea Conti', 'immagine'=>'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&q=80', 'sommario'=>"Tony Parker, Pau Gasol e Dirk Nowitzki: i precursori di una rivoluzione.", 'testo'=>"Jokic tre MVP, Giannis campione, Doncic top-5: il vecchio continente manda superstar.", 'lettura'=>'6 min', 'colore'=>'#1D428A'],
            ['id'=>4, 'titolo'=>'I Playoff NBA: Lo Spettacolo Sportivo Più Avvincente al Mondo', 'categoria'=>'Cultura NBA', 'data'=>'15 Dicembre 2024', 'autore'=>'Giulia Romano', 'immagine'=>'https://images.unsplash.com/photo-1518063319789-7217e6706b04?w=800&q=80', 'sommario'=>"In nessun altro sport il singolo individuo conta quanto nella pallacanestro playoff.", 'testo'=>"In quei momenti la NBA smette di essere sport e diventa leggenda.", 'lettura'=>'7 min', 'colore'=>'#C8102E'],
            ['id'=>5, 'titolo'=>'Analytics e Basket: La Rivoluzione dei Dati nella NBA Moderna', 'categoria'=>'Tecnologia', 'data'=>'8 Dicembre 2024', 'autore'=>'Roberto Sala', 'immagine'=>'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&q=80', 'sommario'=>"La NBA ha abbracciato gli analytics in modo totale e irreversibile.", 'testo'=>"Le telecamere tracciano ogni movimento 25 volte al secondo.", 'lettura'=>'5 min', 'colore'=>'#1D428A'],
            ['id'=>6, 'titolo'=>'Le 5 Dinastie NBA Più Grandi della Storia', 'categoria'=>'Storia NBA', 'data'=>'1 Dicembre 2024', 'autore'=>'Marco Ferretti', 'immagine'=>'https://images.unsplash.com/photo-1567446537708-ac4aa75c9c28?w=800&q=80', 'sommario'=>"Dai Celtics di Russell ai Bulls di Jordan: storie di dominio.", 'testo'=>"I Warriors degli anni 2010 hanno dimostrato che si può costruire una dinastia senza un centro dominante.", 'lettura'=>'8 min', 'colore'=>'#C8102E'],
        ];

        $articoloPrincipale = $articoli[0];
        $altriArticoli      = array_slice($articoli, 1);

        return view('blog', compact('articoloPrincipale', 'altriArticoli'));
    }

    
    // STORE MAIL — POST /contatti
    public function storeMail(Request $request)
    {
        $dati = $request->validate([
            'email'     => ['required', 'email', 'max:255'],
            'oggetto'   => ['required', 'string', 'min:5', 'max:100'],
            'messaggio' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        Mail::to('admin@nba.it')->send(new ContactMail($dati));

        return redirect()->back()->with('successo', 'Messaggio inviato con successo! Ti risponderemo presto. 🏀');
    }

    
    // HELPERS PRIVATI

    private function withImages(array $giocatori): array
    {
        return array_map(function ($g) {
            $g['immagine'] = $this->nbaImgBase . $g['nba_id'] . '.png';
            return $g;
        }, $giocatori);
    }

    private function partiteStatiche(): array
    {
        return [
            ['id'=>1,'data'=>now()->subDay()->toDateString(),'status'=>'Final','periodo'=>4,'tempo'=>'','casa_nome'=>'Boston Celtics','casa_abb'=>'BOS','casa_punteggio'=>119,'ospite_nome'=>'Golden State Warriors','ospite_abb'=>'GSW','ospite_punteggio'=>108,'in_corso'=>false,'finita'=>true],
            ['id'=>2,'data'=>now()->subDay()->toDateString(),'status'=>'Final','periodo'=>4,'tempo'=>'','casa_nome'=>'Denver Nuggets','casa_abb'=>'DEN','casa_punteggio'=>124,'ospite_nome'=>'Los Angeles Lakers','ospite_abb'=>'LAL','ospite_punteggio'=>122,'in_corso'=>false,'finita'=>true],
            ['id'=>3,'data'=>now()->subDay()->toDateString(),'status'=>'Final','periodo'=>4,'tempo'=>'','casa_nome'=>'Oklahoma City Thunder','casa_abb'=>'OKC','casa_punteggio'=>115,'ospite_nome'=>'Milwaukee Bucks','ospite_abb'=>'MIL','ospite_punteggio'=>98,'in_corso'=>false,'finita'=>true],
            ['id'=>4,'data'=>now()->subDay()->toDateString(),'status'=>'Final','periodo'=>4,'tempo'=>'','casa_nome'=>'Minnesota Timberwolves','casa_abb'=>'MIN','casa_punteggio'=>112,'ospite_nome'=>'Phoenix Suns','ospite_abb'=>'PHX','ospite_punteggio'=>105,'in_corso'=>false,'finita'=>true],
        ];
    }

    private function giocatoriData(): array
    {
        return [
            ['id'=>1,  'nba_id'=>201939, 'nome'=>'Stephen Curry',           'squadra'=>'Golden State Warriors',    'ruolo'=>'Point Guard',    'punti'=>29.4,'rimbalzi'=>5.1, 'assist'=>6.3, 'numero'=>30,'nazionalita'=>'USA',                 'eta'=>36,'bio'=>"Considerato il miglior tiratore da tre punti nella storia della NBA, Curry ha rivoluzionato il gioco moderno con la sua capacità di segnare dalla distanza."],
            ['id'=>2,  'nba_id'=>202691, 'nome'=>'Klay Thompson',           'squadra'=>'Dallas Mavericks',         'ruolo'=>'Shooting Guard', 'punti'=>17.9,'rimbalzi'=>3.3, 'assist'=>2.5, 'numero'=>11,'nazionalita'=>'USA',                 'eta'=>34,'bio'=>"Quattro volte campione NBA con i Warriors, Thompson è celebre per le sue prestazioni esplosive e il record di 14 triple in una singola partita."],
            ['id'=>3,  'nba_id'=>203110, 'nome'=>'Draymond Green',          'squadra'=>'Golden State Warriors',    'ruolo'=>'Power Forward',  'punti'=>8.6, 'rimbalzi'=>7.3, 'assist'=>6.0, 'numero'=>23,'nazionalita'=>'USA',                 'eta'=>34,'bio'=>"Motore difensivo dei Warriors, Green è stato più volte nel quintetto difensivo dell'anno e vincitore di 4 titoli NBA."],
            ['id'=>4,  'nba_id'=>2544,   'nome'=>'LeBron James',            'squadra'=>'Los Angeles Lakers',       'ruolo'=>'Small Forward',  'punti'=>25.7,'rimbalzi'=>7.3, 'assist'=>8.3, 'numero'=>23,'nazionalita'=>'USA',                 'eta'=>40,'bio'=>"Il \"Re\" della pallacanestro moderna. Vincitore di 4 titoli NBA, LeBron è il massimo realizzatore all-time della lega con oltre 40.000 punti."],
            ['id'=>5,  'nba_id'=>203076, 'nome'=>'Anthony Davis',           'squadra'=>'Dallas Mavericks',         'ruolo'=>'Center',         'punti'=>24.7,'rimbalzi'=>12.6,'assist'=>3.5, 'numero'=>3, 'nazionalita'=>'USA',                 'eta'=>31,'bio'=>"Campione NBA 2020, Davis è tra i più dominanti centri-ala della storia recente grazie alla sua versatilità offensiva e difensiva."],
            ['id'=>6,  'nba_id'=>1629672,'nome'=>'Austin Reaves',           'squadra'=>'Los Angeles Lakers',       'ruolo'=>'Shooting Guard', 'punti'=>15.9,'rimbalzi'=>4.3, 'assist'=>5.5, 'numero'=>15,'nazionalita'=>'USA',                 'eta'=>26,'bio'=>"Passato dall'essere un agente libero non draftato a pilastro dei Lakers, Reaves è la prova che la determinazione può fare miracoli."],
            ['id'=>7,  'nba_id'=>1628369,'nome'=>'Jayson Tatum',            'squadra'=>'Boston Celtics',           'ruolo'=>'Small Forward',  'punti'=>26.9,'rimbalzi'=>8.1, 'assist'=>4.9, 'numero'=>0, 'nazionalita'=>'USA',                 'eta'=>26,'bio'=>"Campione NBA 2024 e stella di Boston, Tatum è uno dei giocatori più completi della sua generazione con un attacco da élite."],
            ['id'=>8,  'nba_id'=>1627759,'nome'=>'Jaylen Brown',            'squadra'=>'Boston Celtics',           'ruolo'=>'Shooting Guard', 'punti'=>23.0,'rimbalzi'=>5.5, 'assist'=>3.6, 'numero'=>7, 'nazionalita'=>'USA',                 'eta'=>27,'bio'=>"MVP delle Finals 2024, Brown è la perfetta spalla di Tatum e uno dei migliori difensori nel suo ruolo a livello NBA."],
            ['id'=>9,  'nba_id'=>201143, 'nome'=>'Al Horford',              'squadra'=>'Boston Celtics',           'ruolo'=>'Center',         'punti'=>9.2, 'rimbalzi'=>6.4, 'assist'=>2.8, 'numero'=>42,'nazionalita'=>'Repubblica Dominicana','eta'=>38,'bio'=>"Veterano con grande esperienza internazionale, Horford porta leadership e qualità difensiva ai Celtics."],
            ['id'=>10, 'nba_id'=>203999, 'nome'=>'Nikola Jokic',            'squadra'=>'Denver Nuggets',           'ruolo'=>'Center',         'punti'=>26.4,'rimbalzi'=>12.4,'assist'=>9.0, 'numero'=>15,'nazionalita'=>'Serbia',               'eta'=>29,'bio'=>"Il \"Joker\" è il centro con più assist della storia recente NBA. Tre volte MVP della regular season e campione NBA 2023."],
            ['id'=>11, 'nba_id'=>1627750,'nome'=>'Jamal Murray',            'squadra'=>'Denver Nuggets',           'ruolo'=>'Point Guard',    'punti'=>21.2,'rimbalzi'=>4.0, 'assist'=>6.5, 'numero'=>27,'nazionalita'=>'Canada',               'eta'=>27,'bio'=>"Playmaker canadese dai nervi d'acciaio, Murray è il co-protagonista perfetto per Jokic nei playoff."],
            ['id'=>12, 'nba_id'=>1629008,'nome'=>'Michael Porter Jr.',      'squadra'=>'Denver Nuggets',           'ruolo'=>'Small Forward',  'punti'=>14.4,'rimbalzi'=>6.7, 'assist'=>1.4, 'numero'=>1, 'nazionalita'=>'USA',                 'eta'=>26,'bio'=>"Talento cristallino con doti fisiche straordinarie, Porter è uno dei tiratori più puri della lega quando in salute."],
            ['id'=>13, 'nba_id'=>201142, 'nome'=>'Kevin Durant',            'squadra'=>'Phoenix Suns',             'ruolo'=>'Small Forward',  'punti'=>27.1,'rimbalzi'=>6.6, 'assist'=>4.0, 'numero'=>35,'nazionalita'=>'USA',                 'eta'=>35,'bio'=>"Con oltre 2 metri di altezza e il touch di una guardia, Durant è probabilmente lo scorer più efficiente della storia NBA."],
            ['id'=>14, 'nba_id'=>1626164,'nome'=>'Devin Booker',            'squadra'=>'Phoenix Suns',             'ruolo'=>'Shooting Guard', 'punti'=>25.6,'rimbalzi'=>4.5, 'assist'=>6.9, 'numero'=>1, 'nazionalita'=>'USA',                 'eta'=>27,'bio'=>"Scorer letale capace di esplodere in qualsiasi momento, Booker è il volto dei Suns e uno dei migliori realizzatori della sua generazione."],
            ['id'=>15, 'nba_id'=>203524, 'nome'=>'Bradley Beal',            'squadra'=>'Phoenix Suns',             'ruolo'=>'Shooting Guard', 'punti'=>18.2,'rimbalzi'=>4.4, 'assist'=>5.0, 'numero'=>3, 'nazionalita'=>'USA',                 'eta'=>31,'bio'=>"Tre volte All-Star, Beal è uno dei realizzatori più costanti della lega con una tecnica offensiva raffinata."],
            ['id'=>16, 'nba_id'=>203507, 'nome'=>'Giannis Antetokounmpo',   'squadra'=>'Milwaukee Bucks',          'ruolo'=>'Power Forward',  'punti'=>30.4,'rimbalzi'=>11.5,'assist'=>6.5, 'numero'=>34,'nazionalita'=>'Grecia',               'eta'=>29,'bio'=>"Il \"Greek Freak\", due volte MVP della NBA e campione nel 2021, è probabilmente il giocatore più dominante fisicamente della lega."],
            ['id'=>17, 'nba_id'=>203114, 'nome'=>'Khris Middleton',         'squadra'=>'Washington Wizards',       'ruolo'=>'Small Forward',  'punti'=>11.0,'rimbalzi'=>3.8, 'assist'=>3.5, 'numero'=>22,'nazionalita'=>'USA',                 'eta'=>33,'bio'=>"Partner ideale di Giannis, Middleton è un attaccante completo capace di cambiare le partite con le sue triple in momenti chiave."],
            ['id'=>18, 'nba_id'=>1628978,'nome'=>'Brook Lopez',             'squadra'=>'Milwaukee Bucks',          'ruolo'=>'Center',         'punti'=>12.4,'rimbalzi'=>4.7, 'assist'=>1.4, 'numero'=>11,'nazionalita'=>'USA',                 'eta'=>36,'bio'=>"Uno dei migliori centri tiratori della lega, Lopez è un pilastro difensivo dei Bucks con eccellente senso della posizione."],
            ['id'=>19, 'nba_id'=>1629029,'nome'=>'Luka Doncic',             'squadra'=>'Los Angeles Lakers',       'ruolo'=>'Point Guard',    'punti'=>28.5,'rimbalzi'=>8.2, 'assist'=>8.5, 'numero'=>77,'nazionalita'=>'Slovenia',              'eta'=>25,'bio'=>"Fenomeno sloveno che ha già raggiunto il livello delle leggende, Doncic guida la lega in quasi tutte le statistiche offensive."],
            ['id'=>20, 'nba_id'=>1628384,'nome'=>'Kyrie Irving',            'squadra'=>'Dallas Mavericks',         'ruolo'=>'Point Guard',    'punti'=>24.7,'rimbalzi'=>5.0, 'assist'=>5.2, 'numero'=>11,'nazionalita'=>'Australia',              'eta'=>32,'bio'=>"Uno dei ball-handler più tecnici di sempre, Irving è un artista del basket con capacità di finishing eccezionali."],
            ['id'=>21, 'nba_id'=>1630591,'nome'=>'Shai Gilgeous-Alexander', 'squadra'=>'Oklahoma City Thunder',    'ruolo'=>'Point Guard',    'punti'=>30.1,'rimbalzi'=>5.5, 'assist'=>6.2, 'numero'=>2, 'nazionalita'=>'Canada',               'eta'=>26,'bio'=>"SGA è diventato uno dei più difficili da marcare grazie al suo stile fluido e imprevedibile."],
            ['id'=>22, 'nba_id'=>1631117,'nome'=>'Jalen Williams',          'squadra'=>'Oklahoma City Thunder',    'ruolo'=>'Small Forward',  'punti'=>23.5,'rimbalzi'=>4.5, 'assist'=>5.6, 'numero'=>8, 'nazionalita'=>'USA',                 'eta'=>23,'bio'=>"Williams ha fatto un salto di qualità enorme diventando uno dei giovani più promettenti della lega."],
            ['id'=>23, 'nba_id'=>204001, 'nome'=>'Jimmy Butler',            'squadra'=>'Golden State Warriors',    'ruolo'=>'Small Forward',  'punti'=>20.0,'rimbalzi'=>5.6, 'assist'=>4.9, 'numero'=>22,'nazionalita'=>'USA',                 'eta'=>34,'bio'=>"Simbolo della cultura Heat, Butler è il massimo giocatore nei momenti decisivi."],
            ['id'=>24, 'nba_id'=>1629216,'nome'=>'Bam Adebayo',             'squadra'=>'Miami Heat',               'ruolo'=>'Center',         'punti'=>19.3,'rimbalzi'=>10.4,'assist'=>3.2, 'numero'=>13,'nazionalita'=>'USA',                 'eta'=>26,'bio'=>"Pilastro difensivo dei Heat, Adebayo è tra i migliori centri della lega nella lettura del gioco."],
            ['id'=>25, 'nba_id'=>203954, 'nome'=>'Joel Embiid',             'squadra'=>'Philadelphia 76ers',       'ruolo'=>'Center',         'punti'=>34.7,'rimbalzi'=>11.0,'assist'=>5.6, 'numero'=>21,'nazionalita'=>'Camerun',              'eta'=>29,'bio'=>"MVP 2022-23, Embiid è il centro più offensivamente completo della sua era."],
            ['id'=>26, 'nba_id'=>1628473,'nome'=>'Tyrese Maxey',            'squadra'=>'Philadelphia 76ers',       'ruolo'=>'Point Guard',    'punti'=>25.9,'rimbalzi'=>3.7, 'assist'=>6.2, 'numero'=>0, 'nazionalita'=>'USA',                 'eta'=>23,'bio'=>"Maxey è diventato uno dei point guard più veloci e imprevedibili della lega."],
            ['id'=>27, 'nba_id'=>203944, 'nome'=>'Julius Randle',           'squadra'=>'New York Knicks',          'ruolo'=>'Power Forward',  'punti'=>24.0,'rimbalzi'=>9.2, 'assist'=>5.0, 'numero'=>30,'nazionalita'=>'USA',                 'eta'=>29,'bio'=>"Rinato a New York, Randle è diventato All-Star con un gioco fisico e grande capacità realizzativa."],
            ['id'=>28, 'nba_id'=>1628386,'nome'=>'Jalen Brunson',           'squadra'=>'New York Knicks',          'ruolo'=>'Point Guard',    'punti'=>28.7,'rimbalzi'=>3.6, 'assist'=>6.7, 'numero'=>11,'nazionalita'=>'USA',                 'eta'=>27,'bio'=>"Leader assoluto dei Knicks, Brunson è uno dei migliori point guard della lega."],
            ['id'=>29, 'nba_id'=>1629680,'nome'=>'Scottie Barnes',          'squadra'=>'Toronto Raptors',          'ruolo'=>'Power Forward',  'punti'=>19.9,'rimbalzi'=>8.2, 'assist'=>6.1, 'numero'=>4, 'nazionalita'=>'Canada',               'eta'=>22,'bio'=>"Rookie of the Year 2022, Barnes è uno dei talenti più completi della sua generazione."],
            ['id'=>30, 'nba_id'=>203897, 'nome'=>'Zach LaVine',             'squadra'=>'Sacramento Kings',         'ruolo'=>'Shooting Guard', 'punti'=>22.0,'rimbalzi'=>4.2, 'assist'=>4.0, 'numero'=>8, 'nazionalita'=>'USA',                 'eta'=>29,'bio'=>"LaVine abbina esplosività atletica a una tecnica offensiva raffinata."],
            ['id'=>31, 'nba_id'=>1629636,'nome'=>'Darius Garland',          'squadra'=>'Cleveland Cavaliers',      'ruolo'=>'Point Guard',    'punti'=>21.6,'rimbalzi'=>3.2, 'assist'=>7.8, 'numero'=>10,'nazionalita'=>'USA',                 'eta'=>24,'bio'=>"Uno dei passatori più creativi della lega, Garland guida i Cavaliers con vision e tecnica classica."],
            ['id'=>32, 'nba_id'=>1628970,'nome'=>'Donovan Mitchell',        'squadra'=>'Cleveland Cavaliers',      'ruolo'=>'Shooting Guard', 'punti'=>26.6,'rimbalzi'=>4.4, 'assist'=>6.1, 'numero'=>45,'nazionalita'=>'USA',                 'eta'=>27,'bio'=>"Mitchell è uno dei migliori scorer in situazioni di pressione della NBA moderna."],
            ['id'=>33, 'nba_id'=>1628368,'nome'=>'Trae Young',              'squadra'=>'Atlanta Hawks',            'ruolo'=>'Point Guard',    'punti'=>25.7,'rimbalzi'=>2.8, 'assist'=>10.8,'numero'=>11,'nazionalita'=>'USA',                 'eta'=>25,'bio'=>"Il \"Ice Tray\" è uno dei passatori e tiratori più creativi della lega."],
            ['id'=>34, 'nba_id'=>1629628,'nome'=>'Anfernee Simons',         'squadra'=>'Portland Trail Blazers',   'ruolo'=>'Shooting Guard', 'punti'=>24.6,'rimbalzi'=>3.8, 'assist'=>6.2, 'numero'=>1, 'nazionalita'=>'USA',                 'eta'=>25,'bio'=>"Simons ha dimostrato di poter essere una prima opzione in NBA dopo le cessioni dei compagni."],
            ['id'=>35, 'nba_id'=>204060, 'nome'=>'Zion Williamson',         'squadra'=>'New Orleans Pelicans',     'ruolo'=>'Power Forward',  'punti'=>26.0,'rimbalzi'=>7.0, 'assist'=>4.6, 'numero'=>1, 'nazionalita'=>'USA',                 'eta'=>23,'bio'=>"Fisicamente unico nella storia NBA: quando è in salute è quasi ingiocabile."],
            ['id'=>36, 'nba_id'=>1627734,'nome'=>"De'Aaron Fox",            'squadra'=>'San Antonio Spurs',        'ruolo'=>'Point Guard',    'punti'=>19.8,'rimbalzi'=>4.1, 'assist'=>6.2, 'numero'=>5, 'nazionalita'=>'USA',                 'eta'=>26,'bio'=>"Uno dei point guard più veloci della lega, Fox guida i Kings con transizione fulminea."],
            ['id'=>37, 'nba_id'=>1630162,'nome'=>'Anthony Edwards',         'squadra'=>'Minnesota Timberwolves',   'ruolo'=>'Shooting Guard', 'punti'=>25.9,'rimbalzi'=>5.4, 'assist'=>5.1, 'numero'=>5, 'nazionalita'=>'USA',                 'eta'=>22,'bio'=>"Edwards è il grande favorito a diventare il volto della NBA nella prossima decade."],
            ['id'=>38, 'nba_id'=>203952, 'nome'=>'Rudy Gobert',             'squadra'=>'Minnesota Timberwolves',   'ruolo'=>'Center',         'punti'=>13.5,'rimbalzi'=>12.9,'assist'=>1.3, 'numero'=>27,'nazionalita'=>'Francia',               'eta'=>31,'bio'=>"Quattro volte Difensore dell'Anno NBA, il centro difensivo più dominante della sua era."],
            ['id'=>39, 'nba_id'=>1641705,'nome'=>'Victor Wembanyama',       'squadra'=>'San Antonio Spurs',        'ruolo'=>'Center',         'punti'=>21.4,'rimbalzi'=>10.6,'assist'=>3.9, 'numero'=>1, 'nazionalita'=>'Francia',               'eta'=>20,'bio'=>"Il prospetto generazionale più atteso degli ultimi decenni: 2,24m con abilità da guardia."],
            ['id'=>40, 'nba_id'=>1630162,'nome'=>'Ja Morant',               'squadra'=>'Memphis Grizzlies',        'ruolo'=>'Point Guard',    'punti'=>25.1,'rimbalzi'=>5.6, 'assist'=>8.1, 'numero'=>12,'nazionalita'=>'USA',                 'eta'=>24,'bio'=>"Forse il giocatore più spettacolare della lega: le sue schiacciate in contropiede sono leggendarie."],
            ['id'=>41, 'nba_id'=>1630193,'nome'=>'Tyrese Haliburton',       'squadra'=>'Indiana Pacers',           'ruolo'=>'Point Guard',    'punti'=>20.1,'rimbalzi'=>3.9, 'assist'=>10.9,'numero'=>0, 'nazionalita'=>'USA',                 'eta'=>24,'bio'=>"Haliburton guida i Pacers con visione di gioco e un tiro da tre insidioso."],
            ['id'=>42, 'nba_id'=>203110, 'nome'=>'Kyle Kuzma',              'squadra'=>'Milwaukee Bucks',          'ruolo'=>'Power Forward',  'punti'=>18.0,'rimbalzi'=>6.5, 'assist'=>3.4, 'numero'=>33,'nazionalita'=>'USA',                 'eta'=>28,'bio'=>"Diventato leader dei Wizards, Kuzma ha fatto un grande passo avanti come prima opzione offensiva."],
            ['id'=>43, 'nba_id'=>1631094,'nome'=>'Cade Cunningham',         'squadra'=>'Detroit Pistons',          'ruolo'=>'Point Guard',    'punti'=>22.7,'rimbalzi'=>4.4, 'assist'=>7.5, 'numero'=>2, 'nazionalita'=>'USA',                 'eta'=>22,'bio'=>"Prima scelta assoluta del draft 2021, pronto a guidare Detroit verso un futuro competitivo."],
            ['id'=>44, 'nba_id'=>1630178,'nome'=>'LaMelo Ball',             'squadra'=>'Charlotte Hornets',        'ruolo'=>'Point Guard',    'punti'=>23.9,'rimbalzi'=>5.8, 'assist'=>8.0, 'numero'=>2, 'nazionalita'=>'USA',                 'eta'=>22,'bio'=>"LaMelo è tra i passatori più creativi della lega con uno stile di gioco unico."],
            ['id'=>45, 'nba_id'=>1631105,'nome'=>'Jalen Green',             'squadra'=>'Houston Rockets',          'ruolo'=>'Shooting Guard', 'punti'=>22.0,'rimbalzi'=>4.4, 'assist'=>4.5, 'numero'=>4, 'nazionalita'=>'USA',                 'eta'=>22,'bio'=>"Drafted #2 nel 2021, Green è una delle guardie più esplosive della lega."],
            ['id'=>46, 'nba_id'=>1631109,'nome'=>'Lauri Markkanen',         'squadra'=>'Utah Jazz',                'ruolo'=>'Power Forward',  'punti'=>23.2,'rimbalzi'=>8.3, 'assist'=>1.9, 'numero'=>23,'nazionalita'=>'Finlandia',              'eta'=>26,'bio'=>"Il finlandese: mix letale di tiro da tre e attacco al canestro difficilissimo da marcare."],
            ['id'=>47, 'nba_id'=>1630217,'nome'=>'Paolo Banchero',          'squadra'=>'Orlando Magic',            'ruolo'=>'Power Forward',  'punti'=>22.6,'rimbalzi'=>6.9, 'assist'=>5.4, 'numero'=>5, 'nazionalita'=>'USA',                 'eta'=>21,'bio'=>"Rookie of the Year 2023, prima scelta assoluta e futuro top player della lega."],
            ['id'=>48, 'nba_id'=>1628390,'nome'=>'Mikal Bridges',           'squadra'=>'New York Knicks',          'ruolo'=>'Small Forward',  'punti'=>19.6,'rimbalzi'=>3.7, 'assist'=>3.4, 'numero'=>1, 'nazionalita'=>'USA',                 'eta'=>27,'bio'=>"Uno dei migliori difensori della lega, capace di marcare il miglior attaccante avversario."],
            ['id'=>49, 'nba_id'=>203500, 'nome'=>'Kawhi Leonard',           'squadra'=>'Los Angeles Clippers',     'ruolo'=>'Small Forward',  'punti'=>23.7,'rimbalzi'=>6.1, 'assist'=>3.6, 'numero'=>2, 'nazionalita'=>'USA',                 'eta'=>32,'bio'=>"Due volte campione NBA con squadre diverse, tra i migliori bidimensionali della storia recente."],
            ['id'=>50, 'nba_id'=>1629636,'nome'=>'Andrew Wiggins',          'squadra'=>'Miami Heat',               'ruolo'=>'Small Forward',  'punti'=>17.6,'rimbalzi'=>4.9, 'assist'=>2.1, 'numero'=>22,'nazionalita'=>'Canada',               'eta'=>28,'bio'=>"Rinato a Golden State, vincitore del titolo NBA 2022 e difensore di alto livello."],
        ];
    }
}