<?php
/**
 * Automatisme : Théorème de Pythagore
 * Difficulté : MOYEN (range 2.0 - 3.0)
 */

require_once(__DIR__ . '/utils.php'); // get_unique_question_num()

function generer_pythagore() {
    $nb_includes = 100;
    // Aligné sur thales.php : pool en session plutôt que rand(), sinon la même
    // figure revenait au bout de quelques clics sur « Nouvelle question ».
    $num = get_unique_question_num('pythagore', $nb_includes);

    // Charger la question et la réponse depuis des fichiers séparés
    $question_file = __DIR__ . "/../includes/qf_pythagore/question_" . sprintf("%03d", $num) . ".html";
    $reponse_file = __DIR__ . "/../includes/qf_pythagore/reponse_" . sprintf("%03d", $num) . ".html";
    
    if (!file_exists($question_file) || !file_exists($reponse_file)) {
        return [
            'type' => 'pythagore',
            'difficulte_id' => 2.0,
            'question' => '<p>Erreur : fichier non trouvé</p>',
            'reponse' => ''
        ];
    }
    
    $question = file_get_contents($question_file);
    $reponse = file_get_contents($reponse_file);
    
    // Difficulté basée sur le numéro (approximation)
    // Questions 1-30 = plus simples (2.0-2.3)
    // Questions 31-70 = moyennes (2.3-2.7)
    // Questions 71-100 = plus complexes (2.7-3.0)
    $difficulte_id = 2.0 + ($num / 100);
    
    return [
        'type' => 'pythagore',
        'difficulte_id' => $difficulte_id,
        'question' => trim($question),
        'reponse' => trim($reponse)
    ];
}
