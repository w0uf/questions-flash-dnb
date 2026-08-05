<?php
/**
 * Automatisme : Fractions et décimaux
 * Difficulté : FACILE (range 1.0 - 2.0)
 */

require_once(__DIR__ . '/utils.php');

function generer_fractions_decimales() {
    $fractions = [
        ['num' => 1, 'den' => 2, 'decimal' => '0,5', 'difficulte' => 1.1],
        ['num' => 1, 'den' => 4, 'decimal' => '0,25', 'difficulte' => 1.2],
        ['num' => 3, 'den' => 4, 'decimal' => '0,75', 'difficulte' => 1.3],
        ['num' => 1, 'den' => 5, 'decimal' => '0,2', 'difficulte' => 1.4],
        ['num' => 2, 'den' => 5, 'decimal' => '0,4', 'difficulte' => 1.5],
        ['num' => 3, 'den' => 5, 'decimal' => '0,6', 'difficulte' => 1.6],
        ['num' => 4, 'den' => 5, 'decimal' => '0,8', 'difficulte' => 1.7],
        ['num' => 1, 'den' => 10, 'decimal' => '0,1', 'difficulte' => 1.3],
        ['num' => 3, 'den' => 10, 'decimal' => '0,3', 'difficulte' => 1.5],
        ['num' => 7, 'den' => 10, 'decimal' => '0,7', 'difficulte' => 1.6],
        ['num' => 9, 'den' => 10, 'decimal' => '0,9', 'difficulte' => 1.7]
    ];
    
    $choix = $fractions[array_rand($fractions)];
    $frac_display = frac_html($choix['num'], $choix['den']);
    
    if (rand(0, 1) == 0) {
        // Fraction → Décimal
        return [
            'type' => 'fractions_decimales',
            'difficulte_id' => $choix['difficulte'],
            'question' => '<p>Quelle est l\'écriture décimale de ' . $frac_display . ' ?</p>',
            'reponse' => '<p>' . $frac_display . ' = <strong>' . $choix['decimal'] . '</strong></p>'
        ];
    } else {
        // Décimal → Fraction (légèrement plus difficile)
        return [
            'type' => 'fractions_decimales',
            'difficulte_id' => $choix['difficulte'] + 0.1,
            'question' => '<p>Quelle fraction irréductible correspond à <strong>' . $choix['decimal'] . '</strong> ?</p>',
            'reponse' => '<p><strong>' . $choix['decimal'] . '</strong> = ' . $frac_display . '</p>'
        ];
    }
}
