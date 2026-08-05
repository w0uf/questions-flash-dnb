<?php
/**
 * Automatisme DNB 2026 : Comparer et calculer avec des décimaux
 */

function generer_comparer_decimaux() {
    // Pool 4 types
    if (!isset($_SESSION['comparer_decimaux_pool']) || empty($_SESSION['comparer_decimaux_pool'])) {
        $_SESSION['comparer_decimaux_pool'] = [
            'comparer', 'comparer',      // 25%
            'ranger', 'ranger',          // 25%
            'addition', 'addition',      // 25%
            'multiplication', 'multiplication' // 25%
        ];
        shuffle($_SESSION['comparer_decimaux_pool']);
    }
    
    $type = array_shift($_SESSION['comparer_decimaux_pool']);
    
    if ($type == 'comparer') {
        return cd_comparer();
    } elseif ($type == 'ranger') {
        return cd_ranger();
    } elseif ($type == 'addition') {
        return cd_addition();
    } else {
        return cd_multiplication();
    }
}

function cd_comparer() {
    // Pool de types de comparaisons piégeuses
    $types_comparaison = [
        'different_decimales',  // 1,25 vs 1,3
        'negatif_vs_positif',   // -2,5 vs 1,2
        'deux_negatifs',        // -3,4 vs -2,8
        'un_vs_deux_decimales', // 3,4 vs 3,45
    ];
    
    $type = $types_comparaison[array_rand($types_comparaison)];
    
    if ($type == 'different_decimales') {
        // Cas piégeux : différentes longueurs (ex: 1,25 vs 1,3)
        $partie_entiere = rand(1, 9);
        $decimales_a = rand(10, 98); // 10 à 98 pour avoir 2 chiffres
        $decimales_b = rand(1, 9);   // 1 à 9 pour avoir 1 chiffre
        
        $a = $partie_entiere + $decimales_a / 100;  // Ex: 1 + 25/100 = 1,25
        $b = $partie_entiere + $decimales_b / 10;   // Ex: 1 + 3/10 = 1,3
        
        // S'assurer qu'ils sont différents
        while (abs($a - $b) < 0.01) {
            $decimales_b = rand(1, 9);
            $b = $partie_entiere + $decimales_b / 10;
        }
        
    } elseif ($type == 'negatif_vs_positif') {
        // Négatif vs positif : -2,5 vs 1,2
        $a = -(rand(10, 50) / 10);  // Négatif avec 1 décimale
        $b = rand(10, 50) / 10;      // Positif avec 1 décimale
        
    } elseif ($type == 'deux_negatifs') {
        // Deux négatifs : -3,45 vs -2,8 
        $a = -(rand(250, 500) / 100); // Ex: -3,45
        $b = -(rand(15, 24) / 10);     // Ex: -2,8
        
    } else {
        // Un décimal vs deux décimales : 3,4 vs 3,45
        $partie_entiere = rand(1, 9);
        $decimales_a = rand(1, 9);    // 1 chiffre : 3,4
        $decimales_b = rand(40, 98);  // 2 chiffres : 3,45
        
        $a = $partie_entiere + $decimales_a / 10;
        $b = $partie_entiere + $decimales_b / 100;
        
        while (abs($a - $b) < 0.01) {
            $decimales_b = rand(40, 98);
            $b = $partie_entiere + $decimales_b / 100;
        }
    }
    
    // Formatage simple : afficher tel quel sans zéros inutiles
    $a_fr = str_replace('.', ',', sprintf('%.10f', $a)); // Beaucoup de décimales
    $a_fr = rtrim($a_fr, '0'); // Enlever zéros finaux
    $a_fr = rtrim($a_fr, ','); // Enlever virgule si entier
    
    $b_fr = str_replace('.', ',', sprintf('%.10f', $b));
    $b_fr = rtrim($b_fr, '0');
    $b_fr = rtrim($b_fr, ',');
    
    $q = '<p>Comparer les deux nombres suivants en utilisant le symbole &lt; ou &gt; :</p>';
    $q .= '<p style="font-size: 28px; text-align: center; margin: 30px 0;"><strong>' . $a_fr . '</strong> .... <strong>' . $b_fr . '</strong></p>';
    
    $symbole = $a < $b ? '&lt;' : '&gt;';
    $r = '<p style="font-size: 28px; text-align: center;"><strong>' . $a_fr . ' ' . $symbole . ' ' . $b_fr . '</strong></p>';
    
    return [
        'type' => 'comparer_decimaux',
        'difficulte_id' => 1.2,
        'question' => $q,
        'reponse' => $r
    ];
}

function cd_ranger() {
    // Ranger par ordre croissant
    // On garantit 4 valeurs distinctes pour éviter une "égalité" dans le
    // corrigé (qui n'utilise que des < stricts entre les nombres rangés)
    $nombres = [];
    while (count($nombres) < 4) {
        $n = rand(10, 99) / 10;
        if (!in_array($n, $nombres)) {
            $nombres[] = $n;
        }
    }
    
    $nombres_fr = array_map(function($n) { return str_replace('.', ',', $n); }, $nombres);
    $nombres_tries = $nombres;
    sort($nombres_tries);
    $nombres_tries_fr = array_map(function($n) { return str_replace('.', ',', $n); }, $nombres_tries);
    
    $q = '<p>Ranger les nombres suivants dans l\'ordre croissant :</p>';
    $q .= '<p style="font-size: 24px; text-align: center; margin: 30px 0;"><strong>' . implode(' ; ', $nombres_fr) . '</strong></p>';
    
    $r = '<p style="font-size: 24px; text-align: center;"><strong>' . implode(' &lt; ', $nombres_tries_fr) . '</strong></p>';
    
    return [
        'type' => 'comparer_decimaux',
        'difficulte_id' => 1.0,
        'question' => $q,
        'reponse' => $r
    ];
}

function cd_addition() {
    // Addition de deux décimaux (toujours positifs)
    $a = rand(10, 99) / 10;
    $b = rand(10, 99) / 10;
    
    $a_fr = str_replace('.', ',', $a);
    $b_fr = str_replace('.', ',', $b);
    
    $resultat = $a + $b;
    $resultat_fr = str_replace('.', ',', round($resultat, 2));
    
    $q = '<p>Calculer :</p>';
    $q .= '<p style="font-size: 28px; text-align: center; margin: 30px 0;"><strong>' . $a_fr . ' + ' . $b_fr . '</strong></p>';
    
    $r = '<p style="font-size: 28px; text-align: center;"><strong>' . $resultat_fr . '</strong></p>';
    
    return [
        'type' => 'comparer_decimaux',
        'difficulte_id' => 1.0,
        'question' => $q,
        'reponse' => $r
    ];
}

function cd_multiplication() {
    // Multiplication d'un décimal par un petit entier
    $decimal = rand(10, 99) / 10;
    $entier = rand(2, 9);
    
    $decimal_fr = str_replace('.', ',', $decimal);
    
    $resultat = $decimal * $entier;
    $resultat_fr = str_replace('.', ',', round($resultat, 2));
    
    $q = '<p>Calculer :</p>';
    $q .= '<p style="font-size: 28px; text-align: center; margin: 30px 0;"><strong>' . $decimal_fr . ' × ' . $entier . '</strong></p>';
    
    $r = '<p style="font-size: 28px; text-align: center;"><strong>' . $resultat_fr . '</strong></p>';
    
    return [
        'type' => 'comparer_decimaux',
        'difficulte_id' => 1.0,
        'question' => $q,
        'reponse' => $r
    ];
}
?>
