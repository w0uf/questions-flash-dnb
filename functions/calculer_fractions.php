<?php
/**
 * Automatisme : Simplifier, comparer, calculer avec des fractions
 * Difficulté : FACILE à MOYEN (range 1.0 - 2.5)
 */

require_once(__DIR__ . '/utils.php');

// Fonction pour calculer le PGCD
function pgcd($a, $b) {
    while ($b != 0) {
        $temp = $b;
        $b = $a % $b;
        $a = $temp;
    }
    return $a;
}

// Fonction pour simplifier une fraction
function simplifier_fraction($num, $den) {
    $diviseur = pgcd(abs($num), abs($den));
    return [$num / $diviseur, $den / $diviseur];
}

function generer_calculer_fractions() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['fractions_pool']) || empty($_SESSION['fractions_pool'])) {
        $_SESSION['fractions_pool'] = [
            'simplifier_diviseur_2',
            'simplifier_diviseur_3',
            'simplifier_diviseur_5',
            'comparer_meme_den',
            'comparer_meme_num',
            'comparer_unite',
            'calculer_add_meme_den',
            'calculer_sous_meme_den',
            'calculer_add_multiples',
            'calculer_sous_multiples',
            'calculer_mult_simple',
            'calculer_mult_entier'
        ];
        shuffle($_SESSION['fractions_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['fractions_pool']);
    
    // ========================================
    // SIMPLIFIER - DIVISEUR 2
    // ========================================
    if ($type_question == 'simplifier_diviseur_2') {
        $diviseur = 2;
        $den_final = rand(2, 9);
        $num_final = rand(1, $den_final - 1);
        
        $num = $num_final * $diviseur;
        $den = $den_final * $diviseur;
        
        list($num_reduit, $den_reduit) = simplifier_fraction($num, $den);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.2,
            'question' => '<p>Simplifier la fraction : ' . frac_html($num, $den) . '</p>',
            'reponse' => '<p>' . frac_html($num, $den) . ' = ' . frac_html($num_reduit, $den_reduit) . '</p>'
        ];
    }
    
    // ========================================
    // SIMPLIFIER - DIVISEUR 3
    // ========================================
    elseif ($type_question == 'simplifier_diviseur_3') {
        $diviseur = 3;
        $den_final = rand(2, 7);
        $num_final = rand(1, $den_final - 1);
        
        $num = $num_final * $diviseur;
        $den = $den_final * $diviseur;
        
        list($num_reduit, $den_reduit) = simplifier_fraction($num, $den);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.3,
            'question' => '<p>Simplifier la fraction : ' . frac_html($num, $den) . '</p>',
            'reponse' => '<p>' . frac_html($num, $den) . ' = ' . frac_html($num_reduit, $den_reduit) . '</p>'
        ];
    }
    
    // ========================================
    // SIMPLIFIER - DIVISEUR 5
    // ========================================
    elseif ($type_question == 'simplifier_diviseur_5') {
        $diviseur = 5;
        $den_final = rand(2, 6);
        $num_final = rand(1, $den_final - 1);
        
        $num = $num_final * $diviseur;
        $den = $den_final * $diviseur;
        
        list($num_reduit, $den_reduit) = simplifier_fraction($num, $den);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.4,
            'question' => '<p>Simplifier la fraction : ' . frac_html($num, $den) . '</p>',
            'reponse' => '<p>' . frac_html($num, $den) . ' = ' . frac_html($num_reduit, $den_reduit) . '</p>'
        ];
    }
    
    // ========================================
    // COMPARER - MÊME DÉNOMINATEUR
    // ========================================
    elseif ($type_question == 'comparer_meme_den') {
        $den = rand(5, 12);
        $num1 = rand(1, $den - 2);
        $num2 = rand($num1 + 1, $den - 1);
        
        // Mélanger l'ordre
        if (rand(0, 1) == 0) {
            $temp = $num1;
            $num1 = $num2;
            $num2 = $temp;
        }
        
        $plus_grand = ($num1 > $num2) ? frac_html($num1, $den) : frac_html($num2, $den);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.1,
            'question' => '<p>Quelle est la plus grande fraction : ' . frac_html($num1, $den) . ' ou ' . frac_html($num2, $den) . ' ?</p>',
            'reponse' => '<p>La plus grande est : ' . $plus_grand . '</p>'
        ];
    }
    
    // ========================================
    // COMPARER - MÊME NUMÉRATEUR
    // ========================================
    elseif ($type_question == 'comparer_meme_num') {
        // num < den1 < den2 : les deux fractions restent strictement inférieures à 1
        // (sinon on pouvait tirer 7/7 ou 7/8, où la « plus grande » vaut 1).
        $den1 = rand(3, 10);
        $den2 = rand($den1 + 1, 12);
        $num  = rand(2, $den1 - 1);

        // Mélanger l'ordre
        if (rand(0, 1) == 0) {
            $temp = $den1;
            $den1 = $den2;
            $den2 = $temp;
        }
        
        // Plus le dénominateur est petit, plus la fraction est grande
        $plus_grand = ($den1 < $den2) ? frac_html($num, $den1) : frac_html($num, $den2);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.3,
            'question' => '<p>Quelle est la plus grande fraction : ' . frac_html($num, $den1) . ' ou ' . frac_html($num, $den2) . ' ?</p>',
            'reponse' => '<p>La plus grande est : ' . $plus_grand . '</p>'
        ];
    }
    
    // ========================================
    // COMPARER - AVEC L'UNITÉ
    // ========================================
    elseif ($type_question == 'comparer_unite') {
        // Générer une fraction supérieure OU inférieure à 1
        if (rand(0, 1) == 0) {
            // Fraction > 1
            $den = rand(3, 8);
            $num = rand($den + 1, $den + 5);
            $reponse_text = frac_html($num, $den) . ' > 1';
        } else {
            // Fraction < 1
            $den = rand(3, 8);
            $num = rand(1, $den - 1);
            $reponse_text = frac_html($num, $den) . ' < 1';
        }
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.2,
            'question' => '<p>Compléter avec <strong>&lt;</strong> ou <strong>&gt;</strong> : ' . frac_html($num, $den) . ' ... 1</p>',
            'reponse' => '<p>' . $reponse_text . '</p>'
        ];
    }
    
    // ========================================
    // CALCULER - ADDITION MÊME DÉNOMINATEUR
    // ========================================
    elseif ($type_question == 'calculer_add_meme_den') {
        $den = rand(5, 12);
        $num1 = rand(1, 5);
        $num2 = rand(1, $den - $num1 - 1);
        
        $num_resultat = $num1 + $num2;
        list($num_reduit, $den_reduit) = simplifier_fraction($num_resultat, $den);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.4,
            'question' => '<p>Calculer : ' . frac_html($num1, $den) . ' + ' . frac_html($num2, $den) . '</p>',
            'reponse' => '<p>' . frac_html($num1, $den) . ' + ' . frac_html($num2, $den) . ' = ' . frac_html($num_reduit, $den_reduit) . '</p>'
        ];
    }
    
    // ========================================
    // CALCULER - SOUSTRACTION MÊME DÉNOMINATEUR
    // ========================================
    elseif ($type_question == 'calculer_sous_meme_den') {
        $den = rand(5, 12);
        $num1 = rand(3, $den - 1);
        $num2 = rand(1, $num1 - 1);
        
        $num_resultat = $num1 - $num2;
        list($num_reduit, $den_reduit) = simplifier_fraction($num_resultat, $den);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.5,
            'question' => '<p>Calculer : ' . frac_html($num1, $den) . ' - ' . frac_html($num2, $den) . '</p>',
            'reponse' => '<p>' . frac_html($num1, $den) . ' - ' . frac_html($num2, $den) . ' = ' . frac_html($num_reduit, $den_reduit) . '</p>'
        ];
    }
    
    // ========================================
    // CALCULER - ADDITION DÉNOMINATEURS MULTIPLES
    // ========================================
    elseif ($type_question == 'calculer_add_multiples') {
        // Choisir des dénominateurs simples où l'un est multiple de l'autre
        $choix = rand(1, 4);
        switch($choix) {
            case 1: $den1 = 2; $den2 = 4; break;
            case 2: $den1 = 2; $den2 = 6; break;
            case 3: $den1 = 3; $den2 = 6; break;
            case 4: $den1 = 2; $den2 = 8; break;
        }
        
        $num1 = rand(1, $den1 - 1);
        $num2 = rand(1, $den2 - 2);
        
        // Calculer avec le dénominateur commun (le plus grand)
        $den_commun = max($den1, $den2);
        $num1_conv = $num1 * ($den_commun / $den1);
        $num2_conv = $num2 * ($den_commun / $den2);
        
        $num_resultat = $num1_conv + $num2_conv;
        list($num_reduit, $den_reduit) = simplifier_fraction($num_resultat, $den_commun);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.8,
            'question' => '<p>Calculer : ' . frac_html($num1, $den1) . ' + ' . frac_html($num2, $den2) . '</p>',
            'reponse' => '<p>' . frac_html($num1, $den1) . ' + ' . frac_html($num2, $den2) . ' = ' . frac_html($num_reduit, $den_reduit) . '</p>'
        ];
    }
    
    // ========================================
    // CALCULER - SOUSTRACTION DÉNOMINATEURS MULTIPLES
    // ========================================
    elseif ($type_question == 'calculer_sous_multiples') {
        // Choisir des dénominateurs simples où l'un est multiple de l'autre
        $choix = rand(1, 4);
        switch($choix) {
            case 1: $den1 = 2; $den2 = 4; break;
            case 2: $den1 = 2; $den2 = 6; break;
            case 3: $den1 = 3; $den2 = 6; break;
            case 4: $den1 = 2; $den2 = 8; break;
        }
        
        // den2 est toujours un multiple de den1 : il sert de dénominateur commun.
        $den_commun = $den2;

        // num1 < den1 : la première fraction reste strictement inférieure à 1.
        // (l'ancien tirage rand(ceil($den1*0.6), $den1) donnait systématiquement
        //  num1 = den1 = 2, d'où des énoncés « 2/2 - ... » dans 3 cas sur 4.)
        $num1 = rand(1, $den1 - 1);
        $num1_conv = $num1 * ($den_commun / $den1);

        // num2 choisi sous num1 converti : la différence est strictement positive.
        $num2 = rand(1, $num1_conv - 1);
        $num2_conv = $num2;

        $num_resultat = $num1_conv - $num2_conv;
        list($num_reduit, $den_reduit) = simplifier_fraction($num_resultat, $den_commun);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 2.0,
            'question' => '<p>Calculer : ' . frac_html($num1, $den1) . ' - ' . frac_html($num2, $den2) . '</p>',
            'reponse' => '<p>' . frac_html($num1, $den1) . ' - ' . frac_html($num2, $den2) . ' = ' . frac_html($num_reduit, $den_reduit) . '</p>'
        ];
    }
    
    // ========================================
    // CALCULER - MULTIPLICATION SIMPLE
    // ========================================
    elseif ($type_question == 'calculer_mult_simple') {
        // Fractions simples pour multiplication
        $num1 = rand(2, 5);
        $den1 = rand(3, 7);
        while ($num1 == $den1) {   // pas de fraction égale à 1
            $den1 = rand(3, 7);
        }

        $num2 = rand(2, 5);
        $den2 = rand(3, 7);

        // Éviter que num1 = den2 ou num2 = den1 (simplification trop évidente),
        // et éviter num2 = den2, qui revient à multiplier par 1.
        while ($num1 == $den2 || $num2 == $den1 || $num2 == $den2) {
            $num2 = rand(2, 5);
            $den2 = rand(3, 7);
        }
        
        $num_resultat = $num1 * $num2;
        $den_resultat = $den1 * $den2;
        
        list($num_reduit, $den_reduit) = simplifier_fraction($num_resultat, $den_resultat);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.7,
            'question' => '<p>Calculer : ' . frac_html($num1, $den1) . ' × ' . frac_html($num2, $den2) . '</p>',
            'reponse' => '<p>' . frac_html($num1, $den1) . ' × ' . frac_html($num2, $den2) . ' = ' . frac_html($num_reduit, $den_reduit) . '</p>'
        ];
    }
    
    // ========================================
    // CALCULER - MULTIPLICATION PAR ENTIER
    // ========================================
    elseif ($type_question == 'calculer_mult_entier') {
        $entier = rand(2, 6);
        $num = rand(1, 7);
        $den = rand(3, 9);
        
        // S'assurer que num et den sont premiers entre eux
        while (pgcd($num, $den) > 1) {
            $num = rand(1, 7);
        }
        
        $num_resultat = $entier * $num;
        list($num_reduit, $den_reduit) = simplifier_fraction($num_resultat, $den);
        
        return [
            'type' => 'calculer_fractions',
            'difficulte_id' => 1.5,
            'question' => '<p>Calculer : ' . $entier . ' × ' . frac_html($num, $den) . '</p>',
            'reponse' => '<p>' . $entier . ' × ' . frac_html($num, $den) . ' = ' . frac_html($num_reduit, $den_reduit) . '</p>'
        ];
    }
    
    // ========================================
    // FALLBACK (ne devrait jamais arriver)
    // ========================================
    else {
        return generer_calculer_fractions();
    }
}
