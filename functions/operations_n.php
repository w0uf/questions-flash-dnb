<?php
/**
 * Automatisme : Pour un nombre entier, exprimer son double, triple, moitié, prédécesseur, successeur, carré
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.3)
 * Format : Réponse directe (calcul numérique ou expression algébrique)
 */

require_once(__DIR__ . '/utils.php');

function generer_operations_n() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['operations_n_pool']) || empty($_SESSION['operations_n_pool'])) {
        $_SESSION['operations_n_pool'] = [
            // Calculs numériques avec entiers positifs (3)
            'calcul_positif_double',
            'calcul_positif_carre',
            'calcul_positif_moitie',
            
            // Calculs numériques avec entiers relatifs négatifs (3)
            'calcul_negatif_double',
            'calcul_negatif_successeur',
            'calcul_negatif_predecesseur',
            
            // Expressions algébriques (6)
            'expression_double_a',
            'expression_triple_b',
            'expression_moitie_c',
            'expression_carre_d',
            'expression_successeur_p',
            'expression_predecesseur_q'
        ];
        shuffle($_SESSION['operations_n_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['operations_n_pool']);
    
    // ========================================
    // GÉNÉRER LA QUESTION SELON LE TYPE
    // ========================================
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // CALCULS NUMÉRIQUES - ENTIERS POSITIFS
        // ============================================
        
        case 'calcul_positif_double':
            $nombre = rand(5, 15);
            $resultat = $nombre * 2;
            
            $question_html = '<p>Quel est le double de ' . $nombre . ' ?</p>';
            $reponse_html = '<p><strong>' . $resultat . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'calcul_positif_carre':
            $nombre = rand(3, 12);
            $resultat = $nombre * $nombre;
            
            $question_html = '<p>Quel est le carré de ' . $nombre . ' ?</p>';
            $reponse_html = '<p><strong>' . $resultat . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'calcul_positif_moitie':
            // Nombre pair pour avoir un résultat entier
            $moitie = rand(3, 10);
            $nombre = $moitie * 2;
            
            $question_html = '<p>Quelle est la moitié de ' . $nombre . ' ?</p>';
            $reponse_html = '<p><strong>' . $moitie . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // CALCULS NUMÉRIQUES - ENTIERS RELATIFS NÉGATIFS
        // ============================================
        
        case 'calcul_negatif_double':
            $nombre = -rand(2, 8);
            $resultat = $nombre * 2;
            
            $question_html = '<p>Quel est le double de ' . $nombre . ' ?</p>';
            $reponse_html = '<p><strong>' . $resultat . '</strong></p>';
            $difficulte = 1.3;
            break;
            
        case 'calcul_negatif_successeur':
            $nombre = -rand(2, 10);
            $resultat = $nombre + 1;
            
            $question_html = '<p>Quel est le successeur de ' . $nombre . ' ?</p>';
            $reponse_html = '<p><strong>' . $resultat . '</strong></p>';
            $difficulte = 1.3;
            break;
            
        case 'calcul_negatif_predecesseur':
            $nombre = -rand(1, 10);
            $resultat = $nombre - 1;
            
            $question_html = '<p>Quel est le prédécesseur de ' . $nombre . ' ?</p>';
            $reponse_html = '<p><strong>' . $resultat . '</strong></p>';
            $difficulte = 1.3;
            break;
            
        // ============================================
        // EXPRESSIONS ALGÉBRIQUES
        // ============================================
        
        case 'expression_double_a':
            $question_html = '<p>Soit a un nombre entier. Exprimer le double de a.</p>';
            $reponse_html = '<p><strong>2a</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'expression_triple_b':
            $question_html = '<p>Soit b un nombre entier. Exprimer le triple de b.</p>';
            $reponse_html = '<p><strong>3b</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'expression_moitie_c':
            $question_html = '<p>Soit c un nombre entier. Exprimer la moitié de c.</p>';
            $reponse_html = '<p><strong>' . frac_html('c', 2) . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'expression_carre_d':
            $question_html = '<p>Soit d un nombre entier. Exprimer le carré de d.</p>';
            $reponse_html = '<p><strong>d²</strong></p>';
            $difficulte = 1.2;
            break;
            
        case 'expression_successeur_p':
            $question_html = '<p>Soit p un nombre entier. Exprimer le successeur de p.</p>';
            $reponse_html = '<p><strong>p + 1</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'expression_predecesseur_q':
            $question_html = '<p>Soit q un nombre entier. Exprimer le prédécesseur de q.</p>';
            $reponse_html = '<p><strong>q - 1</strong></p>';
            $difficulte = 1.1;
            break;
    }
    
    return [
        'type' => 'operations_n',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}
