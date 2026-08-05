<?php
/**
 * Automatisme : Calculer la valeur d'une expression algébrique y compris avec des puissances, sans technicité excessive
 * Difficulté : FACILE à MOYEN (range 1.1 - 1.4)
 * Format : Réponse directe (calcul numérique)
 */

function generer_valeur_expression() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['valeur_expression_pool']) || empty($_SESSION['valeur_expression_pool'])) {
        $_SESSION['valeur_expression_pool'] = [
            // Expressions simples (3) - peuvent avoir décimaux
            'simple_entier_1',
            'simple_entier_2',
            'simple_decimal',
            
            // Expressions avec parenthèses (3) - peuvent avoir décimaux
            'parenthese_entier_1',
            'parenthese_entier_2',
            'parenthese_decimal',
            
            // Expressions avec puissance (3) - SEULEMENT entiers
            'puissance_1',
            'puissance_2',
            'puissance_3',
            
            // Expressions avec puissance et termes (3) - SEULEMENT entiers
            'puissance_termes_1',
            'puissance_termes_2',
            'puissance_termes_3'
        ];
        shuffle($_SESSION['valeur_expression_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['valeur_expression_pool']);
    
    // Variables disponibles
    $variables = ['a', 'b', 'c', 'd', 'p', 'q'];
    
    // ========================================
    // GÉNÉRER LA QUESTION SELON LE TYPE
    // ========================================
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.1;
    
    switch ($type_question) {
        // ============================================
        // EXPRESSIONS SIMPLES : ax + b
        // ============================================
        
        case 'simple_entier_1':
        case 'simple_entier_2':
            $var = $variables[array_rand($variables)];
            $coef = rand(2, 5);
            // Constante non nulle
            $constante = rand(-5, 10);
            if ($constante == 0) $constante = rand(1, 5); // Éviter 0
            $valeur = rand(2, 5); // Éviter 1 (trop facile)
            
            $resultat = $coef * $valeur + $constante;
            
            // Affichage sans coefficient 1
            $terme_var = ($coef == 1) ? $var : $coef . $var;
            $expression = $terme_var;
            if ($constante >= 0) {
                $expression .= ' + ' . $constante;
            } else {
                $expression .= ' - ' . abs($constante);
            }
            
            $question_html = '<p>Calculer ' . $expression . ' pour ' . $var . ' = ' . $valeur . '</p>';
            $reponse_html = '<p><strong>' . $resultat . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'simple_decimal':
            $var = $variables[array_rand($variables)];
            $coef = [2, 4, 6, 8][rand(0, 3)]; // Coefficients pairs pour calculs faciles avec 0,5
            $constante = rand(1, 8);
            $valeur = [0.5, 1.5, 2.5][rand(0, 2)];
            
            $resultat = $coef * $valeur + $constante;
            
            // Affichage sans coefficient 1
            $terme_var = ($coef == 1) ? $var : $coef . $var;
            $expression = $terme_var . ' + ' . $constante;
            
            $question_html = '<p>Calculer ' . $expression . ' pour ' . $var . ' = ' . str_replace('.', ',', $valeur) . '</p>';
            $reponse_html = '<p><strong>' . str_replace('.', ',', $resultat) . '</strong></p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // EXPRESSIONS AVEC PARENTHÈSES : a(bx + c)
        // ============================================
        
        case 'parenthese_entier_1':
        case 'parenthese_entier_2':
            $var = $variables[array_rand($variables)];
            $coef_ext = rand(2, 4);
            $coef_int = rand(2, 4);
            $constante = rand(1, 5);
            $valeur = rand(2, 3); // Éviter 1 (trop facile)
            
            $resultat = $coef_ext * ($coef_int * $valeur + $constante);
            
            // Affichage sans coefficient 1
            $terme_int = ($coef_int == 1) ? $var : $coef_int . $var;
            $expression = $coef_ext . '(' . $terme_int . ' + ' . $constante . ')';
            
            $question_html = '<p>Calculer ' . $expression . ' pour ' . $var . ' = ' . $valeur . '</p>';
            $reponse_html = '<p><strong>' . $resultat . '</strong></p>';
            $difficulte = 1.2;
            break;
            
        case 'parenthese_decimal':
            $var = $variables[array_rand($variables)];
            $coef_ext = rand(2, 4);
            $coef_int = [2, 4, 6][rand(0, 2)]; // Pairs pour calculs faciles
            $constante = rand(1, 4);
            $valeur = 0.5;
            
            $resultat = $coef_ext * ($coef_int * $valeur + $constante);
            
            // Affichage sans coefficient 1
            $terme_int = ($coef_int == 1) ? $var : $coef_int . $var;
            $expression = $coef_ext . '(' . $terme_int . ' + ' . $constante . ')';
            
            $question_html = '<p>Calculer ' . $expression . ' pour ' . $var . ' = ' . str_replace('.', ',', $valeur) . '</p>';
            $reponse_html = '<p><strong>' . str_replace('.', ',', $resultat) . '</strong></p>';
            $difficulte = 1.3;
            break;
            
        // ============================================
        // EXPRESSIONS AVEC PUISSANCE : ax² + b
        // ============================================
        
        case 'puissance_1':
        case 'puissance_2':
        case 'puissance_3':
            $var = $variables[array_rand($variables)];
            $coef = rand(2, 4);
            // Constante non nulle
            $constante = rand(-3, 8);
            if ($constante == 0) $constante = rand(1, 5); // Éviter 0
            $valeur = rand(2, 4); // Éviter 1 (trop facile), SEULEMENT entiers pour puissances
            
            $resultat = $coef * ($valeur * $valeur) + $constante;
            
            // Affichage sans coefficient 1
            $terme_carre = ($coef == 1) ? $var . '²' : $coef . $var . '²';
            $expression = $terme_carre;
            if ($constante >= 0) {
                $expression .= ' + ' . $constante;
            } else {
                $expression .= ' - ' . abs($constante);
            }
            
            $question_html = '<p>Calculer ' . $expression . ' pour ' . $var . ' = ' . $valeur . '</p>';
            $reponse_html = '<p><strong>' . $resultat . '</strong></p>';
            $difficulte = 1.3;
            break;
            
        // ============================================
        // EXPRESSIONS AVEC PUISSANCE ET TERMES : ax² + bx + c
        // ============================================
        
        case 'puissance_termes_1':
        case 'puissance_termes_2':
        case 'puissance_termes_3':
            $var = $variables[array_rand($variables)];
            $coef_carre = rand(1, 3);
            $coef_x = rand(2, 4); // Éviter 1 pour le terme en x
            // Constante non nulle
            $constante = rand(-3, 6);
            if ($constante == 0) $constante = rand(1, 4); // Éviter 0
            $valeur = rand(2, 3); // Éviter 1 (trop facile), SEULEMENT entiers pour puissances
            
            $resultat = $coef_carre * ($valeur * $valeur) + $coef_x * $valeur + $constante;
            
            // Affichage sans coefficient 1
            $terme_carre = ($coef_carre == 1) ? $var . '²' : $coef_carre . $var . '²';
            $terme_x = ($coef_x == 1) ? $var : $coef_x . $var;
            
            $expression = $terme_carre . ' + ' . $terme_x;
            if ($constante >= 0) {
                $expression .= ' + ' . $constante;
            } else {
                $expression .= ' - ' . abs($constante);
            }
            
            $question_html = '<p>Calculer ' . $expression . ' pour ' . $var . ' = ' . $valeur . '</p>';
            $reponse_html = '<p><strong>' . $resultat . '</strong></p>';
            $difficulte = 1.4;
            break;
    }
    
    return [
        'type' => 'valeur_expression',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}
