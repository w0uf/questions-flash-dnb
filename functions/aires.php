<?php
/**
 * Automatisme : Aires (triangle, rectangle, disque, carré)
 * Difficulté : VARIABLE (1.1 - 2.0)
 * 
 * 40% triangles quelconques avec hauteur
 * 30% rectangles
 * 20% disques (aire exacte avec π)
 * 10% carrés
 */

function generer_aires() {
    $nb_includes = 100;
    
    // Anti-doublon : tracker les numéros déjà utilisés
    if (!isset($_SESSION['dnb_aires_used'])) {
        $_SESSION['dnb_aires_used'] = [];
    }
    
    // Trouver les numéros disponibles
    $available = array_diff(range(1, $nb_includes), $_SESSION['dnb_aires_used']);
    
    // Si tout est utilisé, reset
    if (empty($available)) {
        $_SESSION['dnb_aires_used'] = [];
        $available = range(1, $nb_includes);
    }
    
    // Tirer un numéro au hasard
    $available = array_values($available); // Réindexer
    $num = $available[array_rand($available)];
    
    // Marquer comme utilisé
    $_SESSION['dnb_aires_used'][] = $num;
    
    // Charger la question et la réponse depuis des fichiers séparés
    $question_file = __DIR__ . "/../includes/qf_aires/question_" . sprintf("%03d", $num) . ".html";
    $reponse_file = __DIR__ . "/../includes/qf_aires/reponse_" . sprintf("%03d", $num) . ".html";
    
    if (!file_exists($question_file) || !file_exists($reponse_file)) {
        return [
            'type' => 'aires',
            'difficulte_id' => 1.5,
            'question' => '<p>Erreur : fichier non trouvé</p>',
            'reponse' => ''
        ];
    }
    
    $question = file_get_contents($question_file);
    $reponse = file_get_contents($reponse_file);
    
    // Détection du type pour calibrer difficulté
    if (stripos($question, 'Triangle') !== false) {
        $difficulte_id = 1.5;
    } elseif (stripos($question, 'Disque') !== false) {
        $difficulte_id = 1.8;
    } elseif (stripos($question, 'Carr') !== false) {
        $difficulte_id = 1.1;
    } else {
        $difficulte_id = 1.2; // Rectangle
    }
    
    return [
        'type' => 'aires',
        'difficulte_id' => $difficulte_id,
        'question' => trim($question),
        'reponse' => trim($reponse)
    ];
}
