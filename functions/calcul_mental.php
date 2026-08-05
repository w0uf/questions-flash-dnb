<?php
/**
 * Automatisme : Calcul mental (additions, soustractions)
 * Difficulté : FACILE (range 1.0 - 2.0)
 */

function generer_calcul_mental() {
    // PLACEHOLDER - À développer
    $operations = ['+', '-'];
    $op = $operations[array_rand($operations)];
    
    if ($op == '+') {
        $a = rand(10, 99);
        $b = rand(10, 99);
        $resultat = $a + $b;
    } else {
        $a = rand(50, 99);
        $b = rand(10, 49);
        $resultat = $a - $b;
    }
    
    $difficulte_id = 1.2 + (($a + $b) / 200);
    
    return [
        'type' => 'calcul_mental',
        'difficulte_id' => $difficulte_id,
        'question' => '<p>Calculer : <strong>' . $a . ' ' . $op . ' ' . $b . '</strong></p>',
        'reponse' => '<p><strong>' . $a . ' ' . $op . ' ' . $b . ' = ' . $resultat . '</strong></p>'
    ];
}
