<?php
/**
 * Automatisme : Grandeurs composées (vitesse, distance, durée, débit)
 * Difficulté : MOYEN (range 1.6 - 1.9)
 * Format : réponse ouverte, UNITÉ précisée dans l'énoncé et reprise dans la réponse.
 * Valeurs choisies pour un calcul mental exact (résultats entiers, heures entières).
 */

function generer_grandeurs_composees($famille = '') {
    // Les quatre premiers types travaillent en heures entières ; les suivants
    // (ajoutés en août 2026) font entrer les minutes, les m/s et les grandeurs
    // composées non horaires (consommation, débit en m³/h), où se concentrent
    // les erreurs réelles des élèves.
    $par_famille = [
        'heures'   => ['distance', 'vitesse', 'duree'],
        'minutes'  => ['duree_min', 'distance_min'],
        'debit'    => ['debit', 'debit_m3'],
        'autres'   => ['vitesse_ms', 'conso'],
    ];

    if (isset($par_famille[$famille])) {
        $liste = $par_famille[$famille];
        $type  = $liste[array_rand($liste)];
    } else {
        if (!isset($_SESSION['grandeurs_composees_pool']) || empty($_SESSION['grandeurs_composees_pool'])) {
            $_SESSION['grandeurs_composees_pool'] = array_merge(...array_values($par_famille));
            shuffle($_SESSION['grandeurs_composees_pool']);
        }
        $type = array_shift($_SESSION['grandeurs_composees_pool']);
    }

    switch ($type) {
        case 'distance':     return gc_distance();
        case 'vitesse':      return gc_vitesse();
        case 'duree':        return gc_duree();
        case 'debit':        return gc_debit();
        case 'duree_min':    return gc_duree_minutes();
        case 'distance_min': return gc_distance_minutes();
        case 'vitesse_ms':   return gc_vitesse_ms();
        case 'debit_m3':     return gc_debit_m3();
        case 'conso':        return gc_conso();
    }
    return gc_distance();
}

// d = v × t
function gc_distance() {
    $vitesses = [30, 40, 50, 60, 80, 90, 100, 120];
    $v = $vitesses[array_rand($vitesses)];
    $t = rand(2, 5);
    $d = $v * $t;
    $vehicules = ['Une voiture', 'Un car', 'Un train', 'Un cycliste (à allure régulière)'];
    $veh = $vehicules[array_rand($vehicules)];
    $question = '<p>' . $veh . ' roule à <strong>' . $v . ' km/h</strong> pendant <strong>' . $t . ' h</strong>.</p>'
              . '<p>Quelle distance est ainsi parcourue, <strong>en km</strong> ?</p>';
    $reponse  = '<p>d = v &times; t = ' . $v . ' &times; ' . $t . ' = <strong>' . $d . ' km</strong></p>';
    return [
        'type' => 'grandeurs_composees',
        'difficulte_id' => 1.6,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

// v = d / t  (d multiple de t)
function gc_vitesse() {
    $t = rand(2, 5);
    $vitesses = [15, 20, 25, 30, 40, 50, 60];
    $v = $vitesses[array_rand($vitesses)];
    $d = $v * $t; // garantit d/t entier
    $question = '<p>Un cycliste parcourt <strong>' . $d . ' km</strong> en <strong>' . $t . ' h</strong>.</p>'
              . '<p>Quelle est sa vitesse moyenne, <strong>en km/h</strong> ?</p>';
    $reponse  = '<p>v = d &divide; t = ' . $d . ' &divide; ' . $t . ' = <strong>' . $v . ' km/h</strong></p>';
    return [
        'type' => 'grandeurs_composees',
        'difficulte_id' => 1.8,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

// t = d / v  (d multiple de v)
function gc_duree() {
    $vitesses = [40, 50, 60, 80, 90, 100];
    $v = $vitesses[array_rand($vitesses)];
    $t = rand(2, 5);
    $d = $v * $t; // garantit d/v = t entier
    $question = '<p>Une voiture roule à <strong>' . $v . ' km/h</strong>.</p>'
              . '<p>En combien de temps parcourt-elle <strong>' . $d . ' km</strong>, <strong>en heures</strong> ?</p>';
    $reponse  = '<p>t = d &divide; v = ' . $d . ' &divide; ' . $v . ' = <strong>' . $t . ' h</strong></p>';
    return [
        'type' => 'grandeurs_composees',
        'difficulte_id' => 1.9,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

// t = d / v, réponse EN MINUTES : le passage heures ⇄ minutes est le point
// d'échec le plus fréquent (« 1,5 h » écrit « 1 h 5 »).
function gc_duree_minutes() {
    do {
        $v = [30, 40, 60, 80, 90, 100, 120][rand(0, 6)];
        $m = [15, 20, 30, 40, 45][rand(0, 4)];
        $d = $v * $m / 60;
    } while ($d != intval($d));
    $d = intval($d);

    $vehicules = ['Un cycliste', 'Une voiture', 'Un scooter', 'Un train'];
    $veh = $vehicules[array_rand($vehicules)];

    $question = '<p>' . $veh . ' roule à <strong>' . $v . ' km/h</strong>.</p>'
              . '<p>Combien de temps met-il pour parcourir <strong>' . $d . ' km</strong>, '
              . '<strong>en minutes</strong> ?</p>';
    $reponse  = '<p>t = d &divide; v = ' . $d . ' &divide; ' . $v . ' = '
              . gc_nombre($d / $v) . ' h</p>'
              . '<p>On convertit en minutes : ' . gc_nombre($d / $v) . ' &times; 60 = <strong>'
              . $m . ' min</strong></p>'
              . '<p><small>Repère utile : en ' . $m . ' min on parcourt ' . $m . '/60 de la distance '
              . 'faite en 1 h.</small></p>';

    return [
        'type' => 'grandeurs_composees',
        'difficulte_id' => 2.2,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

// d = v × t, avec une durée donnée EN MINUTES.
function gc_distance_minutes() {
    do {
        $v = [30, 40, 60, 80, 90, 100, 120][rand(0, 6)];
        $m = [15, 20, 30, 40, 45, 50][rand(0, 5)];
        $d = $v * $m / 60;
    } while ($d != intval($d));
    $d = intval($d);

    $question = '<p>Un car roule à <strong>' . $v . ' km/h</strong> pendant <strong>' . $m . ' min</strong>.</p>'
              . '<p>Quelle distance parcourt-il, <strong>en km</strong> ?</p>';
    $reponse  = '<p>' . $m . ' min = ' . $m . ' &divide; 60 = ' . gc_nombre($m / 60) . ' h</p>'
              . '<p>d = v &times; t = ' . $v . ' &times; ' . gc_nombre($m / 60)
              . ' = <strong>' . $d . ' km</strong></p>';

    return [
        'type' => 'grandeurs_composees',
        'difficulte_id' => 2.1,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

// v = d / t en m/s (contexte sportif), avec le rappel de la conversion en km/h.
function gc_vitesse_ms() {
    $contextes = [
        ['Un sprinteur',            [100, 200, 400]],
        ['Un nageur',               [50, 100, 200]],
        ['Une balle de tennis',     [20, 30, 40]],
    ];
    list($qui, $distances) = $contextes[array_rand($contextes)];
    $d = $distances[array_rand($distances)];

    // On choisit une durée qui divise la distance : vitesse entière.
    $diviseurs = [];
    for ($t = 4; $t <= 50; $t++) {
        if ($d % $t === 0 && $d / $t >= 2 && $d / $t <= 12) { $diviseurs[] = $t; }
    }
    if (empty($diviseurs)) { $t = 10; } else { $t = $diviseurs[array_rand($diviseurs)]; }
    $v = $d / $t;

    $question = '<p>' . $qui . ' parcourt <strong>' . $d . ' m</strong> en <strong>' . $t . ' s</strong>.</p>'
              . '<p>Quelle est sa vitesse moyenne, <strong>en m/s</strong> ?</p>';
    $reponse  = '<p>v = d &divide; t = ' . $d . ' &divide; ' . $t . ' = <strong>' . $v . ' m/s</strong></p>'
              . '<p><small>Pour passer en km/h on multiplierait par 3,6 : '
              . gc_nombre($v * 3.6) . ' km/h.</small></p>';

    return [
        'type' => 'grandeurs_composees',
        'difficulte_id' => 1.9,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

// débit d'un gros volume, en m³/h.
function gc_debit_m3() {
    $t = [2, 3, 4, 5, 6, 8, 10][rand(0, 6)];
    $debit = rand(3, 15);
    $vol = $debit * $t;

    $contextes = [
        ['Une piscine de',        'est remplie en'],
        ['Un bassin de',          'est vidé en'],
        ['Une citerne de',        'est remplie en'],
    ];
    list($intro, $verbe) = $contextes[array_rand($contextes)];

    $question = '<p>' . $intro . ' <strong>' . $vol . ' m<sup>3</sup></strong> ' . $verbe
              . ' <strong>' . $t . ' h</strong>.</p>'
              . '<p>Quel est le débit, <strong>en m<sup>3</sup>/h</strong> ?</p>';
    $reponse  = '<p>débit = V &divide; t = ' . $vol . ' &divide; ' . $t
              . ' = <strong>' . $debit . ' m<sup>3</sup>/h</strong></p>';

    return [
        'type' => 'grandeurs_composees',
        'difficulte_id' => 1.8,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

// Consommation en L/100 km : une grandeur composée que les élèves ne
// reconnaissent pas comme telle.
function gc_conso() {
    $c = [4, 5, 6, 7, 8, 10][rand(0, 5)];       // L aux 100 km
    $km = [50, 150, 200, 250, 300, 400, 500][rand(0, 6)];
    // km/100 vaut toujours un multiple de 0,5 : le résultat tombe donc juste,
    // au demi-litre près (2,5 L est une réponse exacte, pas un arrondi).
    $litres = $c * $km / 100;

    $question = '<p>Une voiture consomme <strong>' . $c . ' L aux 100 km</strong>.</p>'
              . '<p>Quelle quantité de carburant consomme-t-elle sur <strong>' . $km . ' km</strong>, '
              . '<strong>en litres</strong> ?</p>';
    $reponse  = '<p>' . $km . ' km, c\'est ' . gc_nombre($km / 100) . ' fois 100 km.</p>'
              . '<p>' . $c . ' &times; ' . gc_nombre($km / 100) . ' = <strong>'
              . gc_nombre($litres) . ' L</strong></p>';

    return [
        'type' => 'grandeurs_composees',
        'difficulte_id' => 2.0,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

/** Affichage à la française, sans zéros inutiles : 1,5 — 2 — 0,75. */
function gc_nombre($n) {
    if ($n == intval($n)) { return (string)intval($n); }
    return str_replace('.', ',', rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.'));
}

// débit = V / t
function gc_debit() {
    $temps = [2, 3, 4, 5, 6, 10];
    $t = $temps[array_rand($temps)];
    $debit = rand(2, 12);
    $vol = $debit * $t; // volume entier, débit entier
    $contextes = [
        ['Un robinet remplit', 'L', 'min'],
        ['Une pompe évacue',   'L', 'min'],
    ];
    list($intro, $uV, $uT) = $contextes[array_rand($contextes)];
    $question = '<p>' . $intro . ' <strong>' . $vol . ' ' . $uV . '</strong> en <strong>' . $t . ' ' . $uT . '</strong>.</p>'
              . '<p>Quel est son débit, <strong>en ' . $uV . '/' . $uT . '</strong> ?</p>';
    $reponse  = '<p>débit = V &divide; t = ' . $vol . ' &divide; ' . $t . ' = <strong>' . $debit . ' ' . $uV . '/' . $uT . '</strong></p>';
    return [
        'type' => 'grandeurs_composees',
        'difficulte_id' => 1.8,
        'question' => $question,
        'reponse' => $reponse,
    ];
}
