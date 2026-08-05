<?php
/**
 * Automatisme : Théorème de Thalès 3ème (configurations classique et papillon)
 * Difficulté : VARIABLE (1.0 - 2.5)
 * 
 * Banque de 100 figures : 55 questions directes (calculer une longueur)
 * et 45 questions réciproques (vérifier le parallélisme, difficulté 1.0).
 * (Le commentaire annonçait 75/25 : compté sur la banque le 01/08/2026.)
 */

require_once(__DIR__ . '/utils.php'); // get_unique_question_num()

function generer_thales() {
    $nb_includes = 100;
    $num = get_unique_question_num('thales', $nb_includes);  // Évite les répétitions
    
    // Charger la question
    $question_file = __DIR__ . "/../includes/qf_thales/question_" . sprintf("%03d", $num) . ".html";
    $reponse_file = __DIR__ . "/../includes/qf_thales/reponse_" . sprintf("%03d", $num) . ".html";
    
    if (!file_exists($question_file) || !file_exists($reponse_file)) {
        return [
            'type' => 'thales',
            'difficulte_id' => 1.5,
            'question' => '<p>Erreur : fichier non trouvé</p>',
            'reponse' => ''
        ];
    }
    
    // Lire le contenu
    $question = file_get_contents($question_file);
    $reponse = file_get_contents($reponse_file);
    
    // DÉTECTION DU TYPE DE QUESTION depuis le contenu HTML
    // Question réciproque/contraposée : contient "sont-elles parallèles"
    // Question directe : contient "Calculer"
    
    if (stripos($question, 'sont-elles parall') !== false) {
        // Question RÉCIPROQUE/CONTRAPOSÉE
        $difficulte_id = 1.0;  // Toujours 0,5 point
    } else {
        // Question DIRECTE (calcul de longueur)
        // Difficulté basée sur la complexité des données
        
        // Détecter les nombres dans la question pour estimer difficulté
        preg_match_all('/(\d+(?:,\d+)?)\s*cm/', $question, $matches);
        $nombres = $matches[1] ?? [];
        
        // Si nombres avec virgules ou grands nombres → plus difficile
        $has_decimal = false;
        $max_nombre = 0;
        foreach ($nombres as $nb) {
            $nb_float = floatval(str_replace(',', '.', $nb));
            if (strpos($nb, ',') !== false) {
                $has_decimal = true;
            }
            if ($nb_float > $max_nombre) {
                $max_nombre = $nb_float;
            }
        }
        
        // Attribution difficulté
        if ($max_nombre <= 6 && !$has_decimal) {
            $difficulte_id = 1.2;  // Facile : petits nombres entiers
        } else if ($max_nombre <= 12 && !$has_decimal) {
            $difficulte_id = 1.6;  // Moyen : nombres moyens
        } else if ($has_decimal || $max_nombre > 12) {
            $difficulte_id = 2.0;  // Difficile : décimaux ou grands nombres
        } else {
            $difficulte_id = 1.5;  // Par défaut
        }
    }
    
    return [
        'type' => 'thales',
        'difficulte_id' => $difficulte_id,
        'question' => trim($question),
        'reponse' => trim($reponse)
    ];
}
