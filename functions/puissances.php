<?php
/**
 * Automatisme : Puissances et ordre de grandeur
 * Difficulté : MOYEN (range 1.5 - 2.0)
 * Format : réponse ouverte (aucune unité — nombres purs)
 */

function generer_puissances() {
    // Pool équilibré des sous-types
    if (!isset($_SESSION['puissances_pool']) || empty($_SESSION['puissances_pool'])) {
        $_SESSION['puissances_pool'] = [
            'entiere', 'entiere', 'entiere',
            'dix', 'dix',
            'produit', 'produit',
            'ordre', 'ordre',
        ];
        shuffle($_SESSION['puissances_pool']);
    }
    $type = array_shift($_SESSION['puissances_pool']);

    switch ($type) {
        case 'entiere': return puiss_entiere();
        case 'dix':     return puiss_dix();
        case 'produit': return puiss_produit();
        case 'ordre':   return puiss_ordre();
    }
    return puiss_entiere();
}

// a^n avec a petit (2..5) et n (2..5)
function puiss_entiere() {
    $a = rand(2, 5);
    $n = rand(2, 5);
    $res = pow($a, $n);
    $question = '<p>Calculer <strong>' . $a . '<sup>' . $n . '</sup></strong></p>';
    $reponse  = '<p><strong>' . $a . '<sup>' . $n . '</sup> = ' . $res . '</strong></p>';
    return [
        'type' => 'puissances',
        'difficulte_id' => 1.5,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

// 10^n sous forme décimale, n de -3 à 6
function puiss_dix() {
    $n = rand(-3, 6);
    while ($n === 0 || $n === 1) { $n = rand(-3, 6); } // éviter 10^0 et 10^1 triviaux
    if ($n > 0) {
        $dec = number_format(pow(10, $n), 0, ',', ' ');
        $exp = $n;
    } else {
        // 10^-k = 0,00...1 avec (k-1) zéros après la virgule
        $k = -$n;
        $dec = '0,' . str_repeat('0', $k - 1) . '1';
        $exp = '&minus;' . $k;
    }
    $question = '<p>Écrire <strong>10<sup>' . $exp . '</sup></strong> sous forme décimale.</p>';
    $reponse  = '<p>10<sup>' . $exp . '</sup> = <strong>' . $dec . '</strong></p>';
    return [
        'type' => 'puissances',
        'difficulte_id' => 1.6,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

// 10^a × 10^b ou 10^a ÷ 10^b sous la forme 10^n
function puiss_produit() {
    if (rand(0, 1) === 0) {
        // produit
        $a = rand(2, 6);
        $b = rand(2, 6);
        $n = $a + $b;
        $question = '<p>Écrire <strong>10<sup>' . $a . '</sup> &times; 10<sup>' . $b . '</sup></strong> sous la forme 10<sup>n</sup>.</p>';
        $reponse  = '<p>10<sup>' . $a . '</sup> &times; 10<sup>' . $b . '</sup> = <strong>10<sup>' . $n . '</sup></strong></p>';
    } else {
        // quotient (a > b pour rester positif)
        $b = rand(1, 4);
        $a = $b + rand(1, 4);
        $n = $a - $b;
        $question = '<p>Écrire <strong>10<sup>' . $a . '</sup> &divide; 10<sup>' . $b . '</sup></strong> sous la forme 10<sup>n</sup>.</p>';
        $reponse  = '<p>10<sup>' . $a . '</sup> &divide; 10<sup>' . $b . '</sup> = <strong>10<sup>' . $n . '</sup></strong></p>';
    }
    return [
        'type' => 'puissances',
        'difficulte_id' => 1.9,
        'question' => $question,
        'reponse' => $reponse,
    ];
}

// Ordre de grandeur (1 chiffre significatif) d'un nombre à 3 ou 4 chiffres
function puiss_ordre() {
    if (rand(0, 1) === 0) {
        // centaines : 100..950, arrondi à la centaine, éviter les x50
        do { $n = rand(120, 949); } while ($n % 100 === 50);
        $arr = round($n / 100) * 100;
    } else {
        // milliers : 1000..9499, arrondi au millier, éviter les x500
        do { $n = rand(1100, 9499); } while ($n % 1000 === 500);
        $arr = round($n / 1000) * 1000;
    }
    $n_fr   = number_format($n, 0, ',', ' ');
    $arr_fr = number_format($arr, 0, ',', ' ');
    $question = '<p>Donner un ordre de grandeur de <strong>' . $n_fr . '</strong>.</p>';
    $reponse  = '<p>Ordre de grandeur : <strong>' . $arr_fr . '</strong></p>';
    return [
        'type' => 'puissances',
        'difficulte_id' => 1.7,
        'question' => $question,
        'reponse' => $reponse,
    ];
}
