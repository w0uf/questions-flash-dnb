<?php
/**
 * Automatisme : Étendue d'une série statistique (max - min)
 * Difficulté : FACILE (range 1.2 - 1.4)
 * Format : réponse ouverte, UNITÉ précisée dans l'énoncé et reprise dans la réponse
 */

function generer_etendue() {
    // Contextes : [phrase, unité affichée, libellé unité question, min, max]
    $contextes = [
        ['Les températures relevées cette semaine sont (en °C) : ', '&nbsp;°C', 'en °C', -5, 35],
        ['Les notes obtenues par Léa ce trimestre sont : ',        '',        'en points', 0, 20],
        ['Les âges des membres du club sont (en années) : ',        '&nbsp;ans', 'en années', 8, 75],
        ['Les prix de plusieurs articles sont (en €) : ',           '&nbsp;€',  'en €', 3, 60],
        ['Les tailles de plusieurs élèves sont (en cm) : ',         '&nbsp;cm', 'en cm', 150, 185],
        ['Les masses de plusieurs colis sont (en kg) : ',           '&nbsp;kg', 'en kg', 1, 50],
    ];
    list($phrase, $unite, $unite_q, $min, $max) = $contextes[array_rand($contextes)];

    // 5 à 7 valeurs entières distinctes
    $nb = rand(5, 7);
    $valeurs = [];
    $garde = 0;
    while (count($valeurs) < $nb && $garde++ < 1000) {
        $v = rand($min, $max);
        if (!in_array($v, $valeurs, true)) {
            $valeurs[] = $v;
        }
    }

    $vmin = min($valeurs);
    $vmax = max($valeurs);
    $etendue = $vmax - $vmin;

    $liste = implode(' ; ', $valeurs);

    $question = '<p>' . $phrase . '<br><strong>' . $liste . '</strong></p>'
              . '<p>Quelle est l\'étendue de cette série, <strong>' . $unite_q . '</strong> ?</p>';
    $reponse  = '<p>Étendue = max &minus; min = ' . $vmax . ' &minus; ' . $vmin
              . ' = <strong>' . $etendue . $unite . '</strong></p>';

    return [
        'type' => 'etendue',
        'difficulte_id' => ($nb <= 5) ? 1.2 : 1.4,
        'question' => $question,
        'reponse' => $reponse,
    ];
}
