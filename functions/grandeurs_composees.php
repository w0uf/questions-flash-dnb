<?php
/**
 * Automatisme : Grandeurs composées (vitesse, distance, durée, débit)
 * Difficulté : MOYEN (range 1.6 - 1.9)
 * Format : réponse ouverte, UNITÉ précisée dans l'énoncé et reprise dans la réponse.
 * Valeurs choisies pour un calcul mental exact (résultats entiers, heures entières).
 */

function generer_grandeurs_composees() {
    if (!isset($_SESSION['grandeurs_composees_pool']) || empty($_SESSION['grandeurs_composees_pool'])) {
        $_SESSION['grandeurs_composees_pool'] = ['distance', 'vitesse', 'duree', 'debit'];
        shuffle($_SESSION['grandeurs_composees_pool']);
    }
    $type = array_shift($_SESSION['grandeurs_composees_pool']);

    switch ($type) {
        case 'distance': return gc_distance();
        case 'vitesse':  return gc_vitesse();
        case 'duree':    return gc_duree();
        case 'debit':    return gc_debit();
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
