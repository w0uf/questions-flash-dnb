<?php
/**
 * Automatisme : Priorités opératoires
 * Difficulté : FACILE (range 1.0 - 2.0)
 */

function generer_priorites() {
    // PLACEHOLDER - À développer
    $a = rand(2, 9);
    $b = rand(2, 9);
    $c = rand(1, 9);
    $resultat = $a * $b + $c;
    
    $difficulte_id = 1.3 + (($a + $b + $c) / 27);
    
    return [
        'type' => 'priorites',
        'difficulte_id' => $difficulte_id,
        'question' => '<p>Calculer : <strong>' . $a . ' × ' . $b . ' + ' . $c . '</strong></p>',
        'reponse' => '<p><strong>' . $a . ' × ' . $b . ' + ' . $c . ' = ' . ($a * $b) . ' + ' . $c . ' = ' . $resultat . '</strong></p>'
    ];
}
