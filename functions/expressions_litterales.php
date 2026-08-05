<?php
/**
 * Automatisme : Simplifier des expressions littérales
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.5)
 * Format : Réponse directe (expression simplifiée)
 */

function generer_expressions_litterales() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['expressions_litterales_pool']) || empty($_SESSION['expressions_litterales_pool'])) {
        $_SESSION['expressions_litterales_pool'] = [
            // Sommes de termes semblables (3)
            'somme_simple_1',
            'somme_simple_2',
            'somme_simple_3',
            
            // Sommes avec constantes (2)
            'somme_constante_1',
            'somme_constante_2',
            
            // Produits simples (2)
            'produit_simple_1',
            'produit_simple_2',
            
            // Produits avec puissances (2)
            'produit_puissance_1',
            'produit_puissance_2',
            
            // Produits avec exposants (2)
            'produit_exposant_1',
            'produit_exposant_2',
            
            // Produit plusieurs variables (1)
            'produit_multi_variables'
        ];
        shuffle($_SESSION['expressions_litterales_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['expressions_litterales_pool']);
    
    // Variables disponibles
    $variables = ['a', 'b', 'c', 'd', 'p', 'q'];
    
    // ========================================
    // GÉNÉRER LA QUESTION SELON LE TYPE
    // ========================================
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // SOMMES DE TERMES SEMBLABLES
        // ============================================
        
        case 'somme_simple_1':
        case 'somme_simple_2':
        case 'somme_simple_3':
            $var = $variables[array_rand($variables)];
            $coef1 = rand(2, 7);
            $coef2 = rand(2, 6);
            $resultat = $coef1 + $coef2;
            
            $question_html = '<p>Simplifier ' . $coef1 . $var . ' + ' . $coef2 . $var . '</p>';
            // Enlever coefficient 1 si le résultat vaut 1
            $reponse_text = ($resultat == 1) ? $var : $resultat . $var;
            $reponse_html = '<p><strong>' . $reponse_text . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // SOMMES AVEC CONSTANTES
        // ============================================
        
        case 'somme_constante_1':
        case 'somme_constante_2':
            $var = $variables[array_rand($variables)];
            $coef1 = rand(4, 8);
            $coef2 = rand(1, $coef1 - 1); // Pour avoir un résultat positif
            $const1 = rand(3, 9);
            $const2 = rand(1, 8);
            
            $resultat_coef = $coef1 - $coef2;
            $resultat_const = $const1 + $const2;
            
            $question_html = '<p>Simplifier ' . $coef1 . $var . ' + ' . $const1 . ' - ' . $coef2 . $var . ' + ' . $const2 . '</p>';
            // Enlever coefficient 1 si le résultat vaut 1
            $terme_var = ($resultat_coef == 1) ? $var : $resultat_coef . $var;
            $reponse_html = '<p><strong>' . $terme_var . ' + ' . $resultat_const . '</strong></p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // PRODUITS SIMPLES
        // ============================================
        
        case 'produit_simple_1':
        case 'produit_simple_2':
            $var = $variables[array_rand($variables)];
            $coef1 = rand(2, 5);
            $coef2 = rand(2, 4);
            $resultat = $coef1 * $coef2;
            
            $question_html = '<p>Simplifier ' . $coef1 . ' × ' . $var . ' × ' . $coef2 . '</p>';
            // Enlever coefficient 1 si le résultat vaut 1
            $reponse_text = ($resultat == 1) ? $var : $resultat . $var;
            $reponse_html = '<p><strong>' . $reponse_text . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        // ============================================
        // PRODUITS AVEC PUISSANCES (même variable)
        // ============================================
        
        case 'produit_puissance_1':
        case 'produit_puissance_2':
            $var = $variables[array_rand($variables)];
            $coef1 = rand(2, 4);
            $coef2 = rand(2, 5);
            $resultat_coef = $coef1 * $coef2;
            
            $question_html = '<p>Simplifier ' . $coef1 . ' × ' . $var . ' × ' . $coef2 . ' × ' . $var . '</p>';
            // Enlever coefficient 1 si le résultat vaut 1
            $reponse_text = ($resultat_coef == 1) ? $var . '²' : $resultat_coef . $var . '²';
            $reponse_html = '<p><strong>' . $reponse_text . '</strong></p>';
            $difficulte = 1.3;
            break;
            
        // ============================================
        // PRODUITS AVEC EXPOSANTS
        // ============================================
        
        case 'produit_exposant_1':
        case 'produit_exposant_2':
            $var = $variables[array_rand($variables)];
            $coef = rand(2, 5);
            
            $question_html = '<p>Simplifier ' . $coef . $var . '² × ' . $var . '</p>';
            // Enlever coefficient 1 si le résultat vaut 1
            $reponse_text = ($coef == 1) ? $var . '³' : $coef . $var . '³';
            $reponse_html = '<p><strong>' . $reponse_text . '</strong></p>';
            $difficulte = 1.4;
            break;
            
        // ============================================
        // PRODUIT PLUSIEURS VARIABLES
        // ============================================
        
        case 'produit_multi_variables':
            // Choisir 2 variables différentes
            $vars_shuffled = $variables;
            shuffle($vars_shuffled);
            $var1 = $vars_shuffled[0];
            $var2 = $vars_shuffled[1];
            
            $coef1 = rand(2, 4);
            $coef2 = rand(2, 4);
            $resultat_coef = $coef1 * $coef2;
            
            // Ordre alphabétique pour les variables
            $vars_array = [$var1, $var2];
            sort($vars_array);
            
            $question_html = '<p>Simplifier ' . $coef1 . ' × ' . $var1 . ' × ' . $coef2 . ' × ' . $var2 . '</p>';
            // Enlever coefficient 1 si le résultat vaut 1
            $reponse_text = ($resultat_coef == 1) ? $vars_array[0] . $vars_array[1] : $resultat_coef . $vars_array[0] . $vars_array[1];
            $reponse_html = '<p><strong>' . $reponse_text . '</strong></p>';
            $difficulte = 1.3;
            break;
    }
    
    return [
        'type' => 'expressions_litterales',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}
