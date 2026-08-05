<?php
/**
 * Automatisme : Pourcentages
 * Difficulté : FACILE (range 1.0 - 2.0)
 */

function generer_pourcentages() {
    // PLACEHOLDER - À développer
    $pourcents = [10, 20, 25, 50, 75];
    $pct = $pourcents[array_rand($pourcents)];
    $nombre = rand(2, 20) * 10;
    $resultat = $nombre * $pct / 100;
    
    // Difficulté basée sur le pourcentage (50% = plus facile)
    $difficulte_map = [10 => 1.5, 20 => 1.4, 25 => 1.3, 50 => 1.1, 75 => 1.6];
    $difficulte_id = $difficulte_map[$pct];
    
    return [
        'type' => 'pourcentages',
        'difficulte_id' => $difficulte_id,
        'question' => '<p>Calculer <strong>' . $pct . '%</strong> de <strong>' . $nombre . '</strong></p>',
        'reponse' => '<p><strong>' . $pct . '% de ' . $nombre . ' = ' . $resultat . '</strong></p>'
    ];
}
