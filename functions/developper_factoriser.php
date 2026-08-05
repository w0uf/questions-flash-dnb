<?php
/**
 * Automatisme : Développer et factoriser une expression simple
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.5)
 * Format : Réponse directe (expression développée ou factorisée)
 */

function generer_developper_factoriser() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['developper_factoriser_pool']) || empty($_SESSION['developper_factoriser_pool'])) {
        $_SESSION['developper_factoriser_pool'] = [
            // DÉVELOPPER : a(bx + c) = abx + ac (simple distributivité)
            'developper_simple_1',
            'developper_simple_2',
            
            // DÉVELOPPER : (ax + b)(cx + d) (double distributivité)
            'developper_double_1',
            'developper_double_2',
            'developper_double_3',
            
            // DÉVELOPPER : (ax + b)² (identité remarquable)
            'developper_carre_1',
            'developper_carre_2',
            
            // FACTORISER : ax² + bx = x(ax + b)
            'factoriser_simple_1',
            'factoriser_simple_2',
            
            // FACTORISER : a²x² - b² = (ax - b)(ax + b) (différence de carrés)
            'factoriser_diff_carres_1',
            'factoriser_diff_carres_2',
            'factoriser_diff_carres_3'
        ];
        shuffle($_SESSION['developper_factoriser_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['developper_factoriser_pool']);
    
    // Variables disponibles
    $variables = ['a', 'b', 'c', 'd', 'p', 'q', 'x', 'y'];
    
    // ========================================
    // GÉNÉRER LA QUESTION SELON LE TYPE
    // ========================================
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // DÉVELOPPER SIMPLE : a(bx + c) = abx + ac
        // ============================================
        
        case 'developper_simple_1':
        case 'developper_simple_2':
            $var = $variables[array_rand($variables)];
            $a = rand(2, 5);
            $b = rand(2, 6);
            $c = rand(2, 9);
            
            $resultat_coef = $a * $b;
            $resultat_const = $a * $c;
            
            // Affichage sans coefficient 1
            $terme_var = ($b == 1) ? $var : $b . $var;
            $terme_resultat = ($resultat_coef == 1) ? $var : $resultat_coef . $var;
            
            $question_html = '<p>Développer ' . $a . '(' . $terme_var . ' + ' . $c . ')</p>';
            $reponse_html = '<p><strong>' . $terme_resultat . ' + ' . $resultat_const . '</strong></p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // DÉVELOPPER DOUBLE : (ax + b)(cx + d)
        // ============================================
        
        case 'developper_double_1':
        case 'developper_double_2':
        case 'developper_double_3':
            $var = $variables[array_rand($variables)];
            $a = rand(2, 4);
            $b = rand(1, 5);
            $c = rand(2, 4);
            $d = rand(1, 5);
            
            // Calcul : ac·x² + (ad + bc)·x + bd
            $coef_x2 = $a * $c;
            $coef_x = $a * $d + $b * $c;
            $coef_const = $b * $d;
            
            // Affichage des termes sans coefficient 1
            $terme_x2 = ($coef_x2 == 1) ? $var . '²' : $coef_x2 . $var . '²';
            $terme_x = ($coef_x == 1) ? $var : $coef_x . $var;
            
            // Affichage des facteurs
            $fact1_var = ($a == 1) ? $var : $a . $var;
            $fact2_var = ($c == 1) ? $var : $c . $var;
            
            // Construction de l'expression développée
            $expression = $terme_x2 . ' + ' . $terme_x . ' + ' . $coef_const;
            
            $question_html = '<p>Développer (' . $fact1_var . ' + ' . $b . ')(' . $fact2_var . ' + ' . $d . ')</p>';
            $reponse_html = '<p><strong>' . $expression . '</strong></p>';
            $difficulte = 1.5;
            break;
            
        // ============================================
        // DÉVELOPPER CARRÉ : (ax + b)²
        // ============================================
        
        case 'developper_carre_1':
        case 'developper_carre_2':
            $var = $variables[array_rand($variables)];
            $a = rand(2, 4);
            $b = rand(2, 5);
            
            // Calcul : a²x² + 2ab·x + b²
            $coef_x2 = $a * $a;
            $coef_x = 2 * $a * $b;
            $coef_const = $b * $b;
            
            // Affichage des termes sans coefficient 1
            $terme_x2 = ($coef_x2 == 1) ? $var . '²' : $coef_x2 . $var . '²';
            $terme_x = ($coef_x == 1) ? $var : $coef_x . $var;
            
            // Affichage du facteur
            $fact_var = ($a == 1) ? $var : $a . $var;
            
            $question_html = '<p>Développer (' . $fact_var . ' + ' . $b . ')²</p>';
            $reponse_html = '<p><strong>' . $terme_x2 . ' + ' . $terme_x . ' + ' . $coef_const . '</strong></p>';
            $difficulte = 1.5;
            break;
            
        // ============================================
        // FACTORISER SIMPLE : ax² + bx = x(ax + b)
        // ============================================
        
        case 'factoriser_simple_1':
        case 'factoriser_simple_2':
            $var = $variables[array_rand($variables)];
            $a = rand(2, 6);
            $b = rand(2, 9);
            
            // Affichage sans coefficient 1
            $terme_x2 = ($a == 1) ? $var . '²' : $a . $var . '²';
            $terme_x = ($b == 1) ? $var : $b . $var;
            $fact_x = ($a == 1) ? $var : $a . $var;
            
            $question_html = '<p>Factoriser ' . $terme_x2 . ' + ' . $terme_x . '</p>';
            $reponse_html = '<p><strong>' . $var . '(' . $fact_x . ' + ' . $b . ')</strong></p>';
            $difficulte = 1.4;
            break;
            
        // ============================================
        // FACTORISER DIFFÉRENCE DE CARRÉS : a²x² - b² = (ax - b)(ax + b)
        // ============================================
        
        case 'factoriser_diff_carres_1':
        case 'factoriser_diff_carres_2':
        case 'factoriser_diff_carres_3':
            $var = $variables[array_rand($variables)];
            
            // Choisir a et b tels que a² et b² soient des carrés parfaits
            $a = rand(1, 4); // a peut être 1 pour avoir x² - 16
            $b = rand(2, 5);
            
            $a_carre = $a * $a;
            $b_carre = $b * $b;
            
            // Affichage sans coefficient 1 pour a²x²
            if ($a == 1) {
                $terme_x2 = $var . '²';
                $fact_var = $var;
            } else {
                $terme_x2 = ($a_carre == 1) ? $var . '²' : $a_carre . $var . '²';
                $fact_var = ($a == 1) ? $var : $a . $var;
            }
            
            $question_html = '<p>Factoriser ' . $terme_x2 . ' - ' . $b_carre . '</p>';
            $reponse_html = '<p><strong>(' . $fact_var . ' - ' . $b . ')(' . $fact_var . ' + ' . $b . ')</strong></p>';
            $difficulte = 1.5;
            break;
    }
    
    return [
        'type' => 'developper_factoriser',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}
