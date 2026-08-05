<?php
/**
 * Automatisme DNB 2026 : Exprimer une fréquence simple
 */

function generer_frequence() {
    if (!isset($_SESSION['frequence_pool']) || empty($_SESSION['frequence_pool'])) {
        $_SESSION['frequence_pool'] = [
            'classe', 'classe',
            'sport', 'sport',
            'enquete', 'enquete',
            'tableau', 'tableau', 'tableau', 'tableau',  // 4 au lieu de 3
            'inverse',
            'experience', 'experience'
        ];
        shuffle($_SESSION['frequence_pool']);
    }
    
    $type = array_shift($_SESSION['frequence_pool']);
    
    switch ($type) {
        case 'classe': return freq_classe();
        case 'sport': return freq_sport();
        case 'enquete': return freq_enquete();
        case 'tableau': return freq_tableau();
        case 'inverse': return freq_inverse();
        case 'experience': return freq_experience();
    }
}

function freq_classe() {
    $total = [20, 25, 30, 40, 50][array_rand([20, 25, 30, 40, 50])];
    $effectif = effectif_simple($total);
    
    $cats = [['filles', 'garçons'], ['externes', 'demi-pensionnaires']];
    $cat = $cats[array_rand($cats)];
    $cible = $cat[array_rand($cat)];
    
    $q = '<p>Dans une classe de ' . $total . ' élèves, ' . $effectif . ' sont ' . $cible . '.</p>';
    $q .= '<p><strong>Quelle est la fréquence des ' . $cible . ' ?</strong></p>';
    
    return reponse_freq($q, $effectif, $total, 1.2);
}

function freq_sport() {
    $sports = [
        ['matchs', 'victoires'], ['tirs', 'buts'],
        ['lancers francs', 'réussites'], ['services', 'aces']
    ];
    
    list($nom_total, $nom_eff) = $sports[array_rand($sports)];
    $total = [20, 25, 30, 40, 50][array_rand([20, 25, 30, 40, 50])];
    $effectif = effectif_simple($total);
    
    $prenoms = ['Léa', 'Tom', 'Sarah', 'Lucas', 'Emma'];
    $prenom = $prenoms[array_rand($prenoms)];
    
    $q = '<p>' . $prenom . ' : ' . $effectif . ' ' . $nom_eff . ' en ' . $total . ' ' . $nom_total . '.</p>';
    $q .= '<p><strong>Quelle est la fréquence de réussite ?</strong></p>';
    
    return reponse_freq($q, $effectif, $total, 1.3);
}

function freq_enquete() {
    $total = [20, 25, 50][array_rand([20, 25, 50])];
    
    $choix = [
        ['pomme', 'pommes'], 
        ['banane', 'bananes'], 
        ['orange', 'oranges']
    ];
    $nb = count($choix);
    $effs = repartir($total, $nb);
    
    $cible_idx = array_rand($choix);
    list($singulier, $pluriel) = $choix[$cible_idx];
    
    $q = '<p>Sondage auprès de ' . $total . ' personnes sur leur fruit préféré :</p><ul>';
    foreach ($choix as $i => $c) {
        $q .= '<li>' . ucfirst($c[0]) . ' : ' . $effs[$i] . '</li>';
    }
    $q .= '</ul>';
    $q .= '<p><strong>Quelle est la fréquence des personnes préférant les ' . $pluriel . ' ?</strong></p>';
    
    return reponse_freq($q, $effs[$cible_idx], $total, 1.5);
}

function freq_tableau() {
    $total = [20, 25, 30, 40][array_rand([20, 25, 30, 40])];
    $valeurs = [12, 13, 14, 15];
    $effs = repartir($total, 4);
    
    $idx = array_rand($valeurs);
    
    $q = '<p>Répartition des âges :</p>';
    $q .= '<table border="1" cellpadding="12" cellspacing="0" style="margin: 20px auto; border-collapse: collapse; font-size: 1.1em;">';
    $q .= '<tr style="background-color: #4CAF50; color: white;"><th style="padding: 12px; border: 1px solid #2e7d32;">Âge</th>';
    foreach ($valeurs as $v) $q .= '<th style="padding: 12px; border: 1px solid #2e7d32;">' . $v . ' ans</th>';
    $q .= '</tr><tr><th style="background-color: #e8f5e9; padding: 12px; border: 1px solid #ccc;">Effectif</th>';
    foreach ($effs as $e) $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;">' . $e . '</td>';
    $q .= '</tr></table>';
    $q .= '<p><strong>Quelle est la fréquence des individus de ' . $valeurs[$idx] . ' ans ?</strong></p>';
    
    return reponse_freq($q, $effs[$idx], $total, 1.6);
}

function freq_inverse() {
    $total = [20, 40, 50, 200][array_rand([20, 40, 50, 200])];
    
    $freqs = [[1,2,0.5], [1,4,0.25], [3,4,0.75], [1,5,0.2], [2,5,0.4]];
    list($num, $denom, $dec) = $freqs[array_rand($freqs)];
    
    while (($total * $num / $denom) != floor($total * $num / $denom)) {
        $total = [20, 40, 50, 200][array_rand([20, 40, 50, 200])];
    }
    
    $effectif = $total * $num / $denom;
    
    $formats = [fraction($num, $denom), str_replace('.', ',', $dec), ($dec*100) . ' %'];
    $freq_txt = $formats[array_rand($formats)];
    
    $q = '<p>Fréquence des filles : ' . $freq_txt . '</p>';
    $q .= '<p>Total : ' . $total . ' élèves.</p>';
    $q .= '<p><strong>Nombre de filles ?</strong></p>';
    
    $r = '<p><strong>' . $effectif . ' filles</strong></p>';
    
    return ['type' => 'frequence', 'difficulte_id' => 2.0, 'question' => $q, 'reponse' => $r];
}

function freq_experience() {
    // Nombre de lancers : uniquement des diviseurs de 100 pour que chaque
    // fréquence eff/nb_lancers soit une décimale EXACTE à 2 chiffres
    // (40 exclu : 1/40 = 0,025 → serait arrondi alors que l'énoncé ne le prévoit pas).
    $nb_lancers = [10, 20, 25, 50][array_rand([10, 20, 25, 50])];
    
    // Simuler les lancers d'un dé à 6 faces
    $effectifs = [0, 0, 0, 0, 0, 0]; // Pour les faces 1 à 6
    for ($i = 0; $i < $nb_lancers; $i++) {
        $face = rand(1, 6);
        $effectifs[$face - 1]++;
    }
    
    // Choisir format : décimale ou pourcentage (50/50)
    $format_pourcent = (rand(0, 1) == 1);
    
    // Construire le tableau
    $q = '<p>On a lancé ' . $nb_lancers . ' fois un dé à 6 faces. Voici les résultats :</p>';
    $q .= '<table border="1" cellpadding="12" cellspacing="0" style="margin: 20px auto; border-collapse: collapse; font-size: 1.1em;">';
    
    // Ligne des faces
    $q .= '<tr style="background-color: #2196F3; color: white;"><th style="padding: 12px;">Face</th>';
    for ($i = 1; $i <= 6; $i++) {
        $q .= '<th style="padding: 12px; min-width: 60px;">' . $i . '</th>';
    }
    $q .= '</tr>';
    
    // Ligne des effectifs
    $q .= '<tr><th style="background-color: #e3f2fd; padding: 12px;">Effectif</th>';
    foreach ($effectifs as $eff) {
        $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;">' . $eff . '</td>';
    }
    $q .= '</tr>';
    
    // Ligne des fréquences (vide à compléter)
    if ($format_pourcent) {
        $q .= '<tr><th style="background-color: #fff9c4; padding: 12px;">Fréquence (%)</th>';
        for ($i = 0; $i < 6; $i++) {
            $q .= '<td style="text-align: center; background-color: #ffffcc; padding: 12px; border: 2px solid #ffa000;"></td>';
        }
        $q .= '</tr>';
    } else {
        $q .= '<tr><th style="background-color: #fff9c4; padding: 12px;">Fréquence décimale</th>';
        for ($i = 0; $i < 6; $i++) {
            $q .= '<td style="text-align: center; background-color: #ffffcc; padding: 12px; border: 2px solid #ffa000;"></td>';
        }
        $q .= '</tr>';
    }
    
    $q .= '</table>';
    $q .= '<p style="font-size: 1.2em; margin-top: 20px;"><strong style="background-color: #ffffcc; padding: 8px; border: 2px solid #ffa000;">⚠️ RECOPIER et COMPLÉTER ce tableau.</strong></p>';
    
    // Construire la réponse avec toutes les fréquences
    $r = '<table border="1" cellpadding="12" cellspacing="0" style="margin: 20px auto; border-collapse: collapse; font-size: 1.1em;">';
    
    // Ligne des faces
    $r .= '<tr style="background-color: #2196F3; color: white;"><th style="padding: 12px;">Face</th>';
    for ($i = 1; $i <= 6; $i++) {
        $r .= '<th style="padding: 12px; min-width: 60px;">' . $i . '</th>';
    }
    $r .= '</tr>';
    
    // Ligne des effectifs
    $r .= '<tr><th style="background-color: #e3f2fd; padding: 12px;">Effectif</th>';
    foreach ($effectifs as $eff) {
        $r .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;">' . $eff . '</td>';
    }
    $r .= '</tr>';
    
    // Ligne des fréquences complétée
    if ($format_pourcent) {
        $r .= '<tr><th style="background-color: #c8e6c9; padding: 12px;">Fréquence (%)</th>';
        foreach ($effectifs as $eff) {
            $pourcent = ($eff / $nb_lancers) * 100;
            if ($pourcent == 0 || $pourcent == 100) {
                $r .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;"><strong>' . intval($pourcent) . ' %</strong></td>';
            } else {
                $r .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;"><strong>' . rtrim(rtrim(number_format($pourcent, 1, ',', ''), '0'), ',') . ' %</strong></td>';
            }
        }
        $r .= '</tr>';
    } else {
        $r .= '<tr><th style="background-color: #c8e6c9; padding: 12px;">Fréquence décimale</th>';
        foreach ($effectifs as $eff) {
            $decimal = $eff / $nb_lancers;
            if ($decimal == 0 || $decimal == 1) {
                $r .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;"><strong>' . intval($decimal) . '</strong></td>';
            } else {
                $r .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;"><strong>' . str_replace('.', ',', rtrim(rtrim(number_format($decimal, 2), '0'), ',')) . '</strong></td>';
            }
        }
        $r .= '</tr>';
    }
    
    $r .= '</table>';
    
    return ['type' => 'frequence', 'difficulte_id' => 1.8, 'question' => $q, 'reponse' => $r];
}

// Utilitaires
function effectif_simple($total) {
    $divs = [];
    for ($i = 2; $i < $total; $i++) {
        if ($total % $i == 0) $divs[] = $i;
    }
    if (empty($divs)) return rand(1, $total-1);
    $d = $divs[array_rand($divs)];
    return $total / $d * rand(1, $d-1);
}

function repartir($total, $nb) {
    $effs = [];
    $rest = $total;
    for ($i = 0; $i < $nb - 1; $i++) {
        $e = rand(1, max(1, $rest - ($nb-$i-1)));
        $effs[] = $e;
        $rest -= $e;
    }
    $effs[] = $rest;
    shuffle($effs);
    return $effs;
}

function reponse_freq($q, $eff, $tot, $diff) {
    $pgcd = gcd($eff, $tot);
    $num = $eff / $pgcd;
    $denom = $tot / $pgcd;
    
    // 33% fraction, 33% pourcentage, 33% décimale
    $format = rand(0, 2);
    
    // Vérifier si le dénominateur permet pourcentage/décimale exacte
    $denom_decimal = in_array($denom, [2, 4, 5, 10, 20, 25, 50]);
    
    if ($format == 0 || !$denom_decimal) {
        // Fraction (ou si dénominateur ne permet pas décimale exacte)
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse sous forme de FRACTION IRRÉDUCTIBLE.</strong></p>';
        $r = '<p><strong>' . fraction($num, $denom) . '</strong></p>';
    } elseif ($format == 1) {
        // Pourcentage
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse en POURCENTAGE.</strong></p>';
        $p = ($num / $denom) * 100;
        if ($p == 0 || $p == 100) {
            $r = '<p><strong>' . intval($p) . ' %</strong></p>';
        } else {
            $r = '<p><strong>' . rtrim(rtrim(number_format($p, 1, ',', ''), '0'), ',') . ' %</strong></p>';
        }
    } else {
        // Décimale
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse sous forme DÉCIMALE.</strong></p>';
        $decimal = $num / $denom;
        if ($decimal == 0 || $decimal == 1) {
            $r = '<p><strong>' . intval($decimal) . '</strong></p>';
        } else {
            $r = '<p><strong>' . str_replace('.', ',', rtrim(rtrim(number_format($decimal, 2), '0'), ',')) . '</strong></p>';
        }
    }
    
    return ['type' => 'frequence', 'difficulte_id' => $diff, 'question' => $q, 'reponse' => $r];
}
?>
