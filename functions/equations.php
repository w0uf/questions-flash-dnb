<?php
/**
 * Automatisme : Résoudre des équations du type ax = c, x+b = c, ax+b = c
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.5)
 * Format : Réponse directe (valeur de x)
 */

require_once(__DIR__ . '/utils.php');

function generer_equations() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['equations_pool']) || empty($_SESSION['equations_pool'])) {
        $_SESSION['equations_pool'] = [
            // TYPE 1 : ax = c (4 questions)
            'type_ax_c_1',
            'type_ax_c_2',
            'type_ax_c_3',
            'type_ax_c_4',
            
            // TYPE 2 : x + b = c (4 questions)
            'type_x_plus_b_1',
            'type_x_plus_b_2',
            'type_x_plus_b_3',
            'type_x_plus_b_4',
            
            // TYPE 3 : ax + b = c (4 questions)
            'type_ax_plus_b_1',
            'type_ax_plus_b_2',
            'type_ax_plus_b_3',
            'type_ax_plus_b_4'
        ];
        shuffle($_SESSION['equations_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['equations_pool']);
    
    // ========================================
    // GÉNÉRER LA QUESTION SELON LE TYPE
    // ========================================
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // TYPE 1 : ax = c
        // ============================================
        
        case 'type_ax_c_1':
        case 'type_ax_c_2':
        case 'type_ax_c_3':
        case 'type_ax_c_4':
            $a = rand(2, 8);
            
            // Choisir x tel que c = ax soit un entier raisonnable
            $x = rand(1, 10);
            $c = $a * $x;
            
            // Éviter coefficient 1
            if ($a == 1) $a = 2;
            
            $question_html = '<p>Résoudre ' . $a . 'x = ' . $c . '</p>';
            $reponse_html = '<p><strong>x = ' . $x . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // TYPE 2 : x + b = c
        // ============================================
        
        case 'type_x_plus_b_1':
        case 'type_x_plus_b_2':
        case 'type_x_plus_b_3':
        case 'type_x_plus_b_4':
            $b = rand(-10, 10);
            $x = rand(-10, 10);
            
            // Éviter b = 0 (trop facile)
            if ($b == 0) $b = rand(1, 8);
            
            // Éviter x = 0 (trop facile)
            if ($x == 0) $x = rand(1, 8);
            
            $c = $x + $b;
            
            // Affichage avec signe correct
            if ($b >= 0) {
                $equation = 'x + ' . $b . ' = ' . $c;
            } else {
                $equation = 'x - ' . abs($b) . ' = ' . $c;
            }
            
            $question_html = '<p>Résoudre ' . $equation . '</p>';
            $reponse_html = '<p><strong>x = ' . $x . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        // ============================================
        // TYPE 3 : ax + b = c
        // ============================================
        
        case 'type_ax_plus_b_1':
        case 'type_ax_plus_b_2':
        case 'type_ax_plus_b_3':
        case 'type_ax_plus_b_4':
            $a = rand(2, 6);
            $b = rand(-8, 12);
            
            // Éviter b = 0 (revient au type 1)
            if ($b == 0) $b = rand(1, 8);
            
            // Choisir x pour avoir une solution entière ou demi-entière simple
            $type_solution = rand(1, 3);
            
            if ($type_solution == 1) {
                // Solution entière positive
                $x = rand(1, 8);
            } elseif ($type_solution == 2) {
                // Solution entière négative
                $x = rand(-6, -1);
            } else {
                // Solution demi-entière (0.5, 1.5, 2.5...)
                $x_entier = rand(1, 5);
                $x = $x_entier + 0.5;
                // Ajuster a pour que ce soit pair (pour avoir résultat entier)
                if ($a % 2 != 0) $a = $a + 1;
            }
            
            $c = $a * $x + $b;
            
            // Éviter coefficient 1
            if ($a == 1) $a = 2;
            
            // Affichage avec signe correct
            if ($b >= 0) {
                $equation = $a . 'x + ' . $b . ' = ' . $c;
            } else {
                $equation = $a . 'x - ' . abs($b) . ' = ' . $c;
            }
            
            // Affichage de la réponse (entier ou fraction)
            if (is_int($x)) {
                $reponse_html = '<p><strong>x = ' . $x . '</strong></p>';
            } else {
                // Convertir décimal en fraction avec équivalence décimale
                if ($x == 0.5) {
                    $reponse_html = '<p><strong>x = ' . frac_html(1, 2) . ' = 0,5</strong></p>';
                } elseif ($x == 1.5) {
                    $reponse_html = '<p><strong>x = ' . frac_html(3, 2) . ' = 1,5</strong></p>';
                } elseif ($x == 2.5) {
                    $reponse_html = '<p><strong>x = ' . frac_html(5, 2) . ' = 2,5</strong></p>';
                } elseif ($x == 3.5) {
                    $reponse_html = '<p><strong>x = ' . frac_html(7, 2) . ' = 3,5</strong></p>';
                } elseif ($x == 4.5) {
                    $reponse_html = '<p><strong>x = ' . frac_html(9, 2) . ' = 4,5</strong></p>';
                } elseif ($x == 5.5) {
                    $reponse_html = '<p><strong>x = ' . frac_html(11, 2) . ' = 5,5</strong></p>';
                } else {
                    // Fallback pour autres décimaux
                    $reponse_html = '<p><strong>x = ' . str_replace('.', ',', $x) . '</strong></p>';
                }
            }
            
            $question_html = '<p>Résoudre ' . $equation . '</p>';
            $difficulte = 1.4;
            break;
    }
    
    return [
        'type' => 'equations',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}
