<?php
/**
 * Automatisme : Carrés des nombres de 1 à 12
 * Difficulté : FACILE (range 1.0 - 2.0)
 */

function generer_carres() {
    // Initialiser le tracker anti-doublon
    if (!isset($_SESSION['dnb_carres_used'])) {
        $_SESSION['dnb_carres_used'] = [];
    }
    
    // Générer toutes les combinaisons possibles : (n, type) avec n=1..12 et type=1..3
    $all_combinations = [];
    for ($i = 1; $i <= 12; $i++) {
        for ($t = 1; $t <= 3; $t++) {
            $all_combinations[] = [$i, $t];
        }
    }
    
    // Filtrer celles déjà utilisées
    $available = array_filter($all_combinations, function($combo) {
        return !in_array($combo, $_SESSION['dnb_carres_used']);
    });
    
    // Si toutes utilisées, reset
    if (empty($available)) {
        $_SESSION['dnb_carres_used'] = [];
        $available = $all_combinations;
    }
    
    // Tirer une combinaison au hasard
    $available = array_values($available); // Réindexer
    $chosen = $available[array_rand($available)];
    list($n, $type_question) = $chosen;
    
    // Marquer comme utilisée
    $_SESSION['dnb_carres_used'][] = $chosen;
    
    $carre = $n * $n;
    
    if ($type_question == 1) {
        // Type A : Notation mathématique (plus facile)
        $question = '<p>Calculer <strong>' . $n . '²</strong></p>';
        $reponse = '<p><strong>' . $n . '² = ' . $carre . '</strong></p>';
        $difficulte_id = 1.0 + ($n / 15);
        
    } elseif ($type_question == 2) {
        // Type B : Formulation verbale (moyen)
        $question = '<p>Quel est le carré de <strong>' . $n . '</strong> ?</p>';
        $reponse = '<p>Le carré de <strong>' . $n . '</strong> est <strong>' . $carre . '</strong></p>';
        $difficulte_id = 1.1 + ($n / 14);
        
    } else {
        // Type C : Racine carrée (plus difficile)
        $question = '<p>Quel nombre <strong>positif</strong> &eacute;lev&eacute; au carr&eacute; donne <strong>' . $carre . '</strong> ?</p>';
        $reponse = '<p><strong>' . $n . '</strong> &eacute;lev&eacute; au carr&eacute; donne <strong>' . $carre . '</strong></p>';
        $difficulte_id = 1.3 + ($n / 12);
    }
    
    return [
        'type' => 'carres',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}
