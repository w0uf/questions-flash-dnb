<?php
/**
 * Automatisme : Tables de multiplication
 * Difficulté : FACILE (range 1.0 - 2.0)
 */

function generer_tables() {
    // PLACEHOLDER - À développer
    $a = rand(2, 9);
    $b = rand(2, 9);
    $resultat = $a * $b;
    
    // Difficulté basée sur les nombres
    $difficulte_id = 1.0 + (($a + $b) / 18);
    
    return [
        'type' => 'tables',
        'difficulte_id' => $difficulte_id,
        'question' => '<p>Combien font <strong>' . $a . ' × ' . $b . '</strong> ?</p>',
        'reponse' => '<p><strong>' . $a . ' × ' . $b . ' = ' . $resultat . '</strong></p>'
    ];
}
