<?php
/**
 * Automatisme DNB 2026 : Exprimer une moyenne
 */

function generer_moyenne() {
    if (!isset($_SESSION['moyenne_pool']) || empty($_SESSION['moyenne_pool'])) {
        $_SESSION['moyenne_pool'] = [
            'simple', 'simple', 'simple', 'simple', 'simple',
            'tableau', 'tableau', 'tableau',
            'somme', 'somme',
            'completer', 'completer'
        ];
        shuffle($_SESSION['moyenne_pool']);
    }
    
    $type = array_shift($_SESSION['moyenne_pool']);
    
    switch ($type) {
        case 'simple': return moy_simple();
        case 'tableau': return moy_tableau();
        case 'somme': return moy_somme();
        case 'completer': return moy_completer();
    }
}

function moy_simple() {
    $contextes = [
        ['notes en mathématiques', '', [8, 10, 12, 14, 16, 18, 20]],
        ['températures', '°C', [15, 16, 17, 18, 19, 20, 21, 22]],
        ['prix d\'articles', '€', [5, 8, 10, 12, 15, 18, 20]],
        ['scores', ' points', [10, 12, 14, 15, 16, 18, 20]],
        ['âges', ' ans', [8, 10, 11, 12, 13, 14, 15]]
    ];
    
    list($nom, $unite, $valeurs_possibles) = $contextes[array_rand($contextes)];
    
    // Générer 3 à 5 valeurs
    $nb = rand(3, 5);
    $valeurs = [];
    for ($i = 0; $i < $nb; $i++) {
        $valeurs[] = $valeurs_possibles[array_rand($valeurs_possibles)];
    }
    
    // Vérifier que la somme et division sont faciles
    $somme = array_sum($valeurs);
    $moyenne = $somme / $nb;
    
    // Régénérer si la moyenne n'est pas "belle"
    $tentatives = 0;
    while (!est_moyenne_belle($moyenne) && $tentatives < 10) {
        $valeurs = [];
        for ($i = 0; $i < $nb; $i++) {
            $valeurs[] = $valeurs_possibles[array_rand($valeurs_possibles)];
        }
        $somme = array_sum($valeurs);
        $moyenne = $somme / $nb;
        $tentatives++;
    }
    
    $q = '<p>' . ucfirst($nom) . ' : ';
    for ($i = 0; $i < count($valeurs); $i++) {
        if ($i == count($valeurs) - 1) {
            $q .= ' et ' . $valeurs[$i];
        } elseif ($i == 0) {
            $q .= $valeurs[$i];
        } else {
            $q .= ', ' . $valeurs[$i];
        }
    }
    $q .= ($unite ? ' ' . $unite : '') . '</p>';
    $q .= '<p><strong>Quelle est la moyenne ?</strong></p>';
    
    $moyenne_txt = formater_nombre($moyenne);
    $r = '<p><strong>' . $moyenne_txt . ($unite ? ' ' . $unite : '') . '</strong></p>';
    $r .= '<p style="font-size: 0.9em; color: #666;">Calcul : (' . implode(' + ', $valeurs) . ') ÷ ' . $nb . ' = ' . $somme . ' ÷ ' . $nb . ' = ' . $moyenne_txt . '</p>';
    
    return ['type' => 'moyenne', 'difficulte_id' => 1.3, 'question' => $q, 'reponse' => $r];
}

function moy_tableau() {
    $contextes = [
        ['Note', '', [8, 10, 12, 14, 16]],
        ['Âge', ' ans', [10, 11, 12, 13, 14]],
        ['Score', ' points', [10, 12, 14, 15, 18]]
    ];
    
    list($titre, $unite, $valeurs_possibles) = $contextes[array_rand($contextes)];
    
    // Choisir 3 ou 4 valeurs
    $nb_valeurs = rand(3, 4);
    shuffle($valeurs_possibles);
    $valeurs = array_slice($valeurs_possibles, 0, $nb_valeurs);
    sort($valeurs);
    
    // Générer effectifs
    $effectifs = [];
    $total_eff = 0;
    for ($i = 0; $i < $nb_valeurs; $i++) {
        $eff = rand(2, 5);
        $effectifs[] = $eff;
        $total_eff += $eff;
    }
    
    // Vérifier que le total n'est pas trop grand
    if ($total_eff > 20) {
        $effectifs = array_map(function($e) { return max(1, intval($e / 2)); }, $effectifs);
        $total_eff = array_sum($effectifs);
    }
    
    // Calculer moyenne
    $somme_ponderee = 0;
    for ($i = 0; $i < $nb_valeurs; $i++) {
        $somme_ponderee += $valeurs[$i] * $effectifs[$i];
    }
    $moyenne = $somme_ponderee / $total_eff;
    
    // Régénérer si pas belle
    $tentatives = 0;
    while (!est_moyenne_belle($moyenne) && $tentatives < 5) {
        $effectifs = [];
        $total_eff = 0;
        for ($i = 0; $i < $nb_valeurs; $i++) {
            $eff = rand(2, 4);
            $effectifs[] = $eff;
            $total_eff += $eff;
        }
        $somme_ponderee = 0;
        for ($i = 0; $i < $nb_valeurs; $i++) {
            $somme_ponderee += $valeurs[$i] * $effectifs[$i];
        }
        $moyenne = $somme_ponderee / $total_eff;
        $tentatives++;
    }
    
    $q = '<p>Voici une série statistique :</p>';
    $q .= '<table border="1" cellpadding="12" cellspacing="0" style="margin: 20px auto; border-collapse: collapse; font-size: 1.1em;">';
    $q .= '<tr style="background-color: #4CAF50; color: white;"><th style="padding: 12px; border: 1px solid #2e7d32;">' . $titre . '</th>';
    foreach ($valeurs as $v) {
        $q .= '<th style="padding: 12px; border: 1px solid #2e7d32;">' . $v . ($unite ? ' ' . $unite : '') . '</th>';
    }
    $q .= '</tr><tr><th style="background-color: #e8f5e9; padding: 12px; border: 1px solid #ccc;">Effectif</th>';
    foreach ($effectifs as $e) {
        $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;">' . $e . '</td>';
    }
    $q .= '</tr></table>';
    $q .= '<p><strong>Quelle est la moyenne ?</strong></p>';
    
    $moyenne_txt = formater_nombre($moyenne);
    $r = '<p><strong>' . $moyenne_txt . ($unite ? ' ' . $unite : '') . '</strong></p>';
    
    // Détail calcul
    $details = [];
    for ($i = 0; $i < $nb_valeurs; $i++) {
        $details[] = $valeurs[$i] . '×' . $effectifs[$i];
    }
    $r .= '<p style="font-size: 0.9em; color: #666;">(' . implode(' + ', $details) . ') ÷ ' . $total_eff . ' = ' . $somme_ponderee . ' ÷ ' . $total_eff . ' = ' . $moyenne_txt . '</p>';
    
    return ['type' => 'moyenne', 'difficulte_id' => 1.8, 'question' => $q, 'reponse' => $r];
}

function moy_somme() {
    $contextes = [
        ['notes', 'notes'],
        ['températures', 'températures'],
        ['scores', 'scores'],
        ['prix', 'prix']
    ];
    
    list($nom, $nom_pluriel) = $contextes[array_rand($contextes)];
    
    // Nombre de valeurs
    $nb = [3, 4, 5, 10][array_rand([3, 4, 5, 10])];
    
    // Moyenne "belle"
    $moyennes = [10, 12, 14, 15, 16, 18, 20];
    $moyenne = $moyennes[array_rand($moyennes)];
    
    $somme = $moyenne * $nb;
    
    $q = '<p>La moyenne de ' . $nb . ' ' . $nom_pluriel . ' est ' . $moyenne . '.</p>';
    $q .= '<p><strong>Quelle est la somme de ces ' . $nb . ' ' . $nom_pluriel . ' ?</strong></p>';
    
    $r = '<p><strong>' . $somme . '</strong></p>';
    $r .= '<p style="font-size: 0.9em; color: #666;">Somme = Moyenne × Nombre = ' . $moyenne . ' × ' . $nb . ' = ' . $somme . '</p>';
    
    return ['type' => 'moyenne', 'difficulte_id' => 2.0, 'question' => $q, 'reponse' => $r];
}

function moy_completer() {
    $contextes = [
        ['notes', ''],
        ['températures', '°C'],
        ['scores', ' points'],
        ['prix', '€']
    ];
    
    list($nom, $unite) = $contextes[array_rand($contextes)];
    
    // Générer 3 valeurs + une inconnue
    $valeurs_possibles = [8, 10, 12, 14, 15, 16, 18, 20];
    $valeurs = [];
    for ($i = 0; $i < 3; $i++) {
        $valeurs[] = $valeurs_possibles[array_rand($valeurs_possibles)];
    }
    
    // Choisir une moyenne qui donne un x entier
    $somme_3 = array_sum($valeurs);
    $moyennes = [10, 12, 13, 14, 15, 16];
    
    foreach ($moyennes as $m) {
        $x = $m * 4 - $somme_3;
        if ($x > 0 && $x <= 20 && $x == intval($x)) {
            $moyenne = $m;
            break;
        }
    }
    
    // Si pas trouvé, forcer
    if (!isset($moyenne)) {
        $moyenne = 12;
        $x = $moyenne * 4 - $somme_3;
    }
    
    $q = '<p>' . ucfirst($nom) . ' : ' . implode(', ', $valeurs) . ' et une 4<sup>e</sup> valeur inconnue.</p>';
    $q .= '<p>La moyenne est ' . $moyenne . ($unite ? ' ' . $unite : '') . '.</p>';
    $q .= '<p><strong>Quelle est la 4<sup>e</sup> valeur ?</strong></p>';
    
    $somme_totale = $moyenne * 4;
    $r = '<p><strong>' . $x . ($unite ? ' ' . $unite : '') . '</strong></p>';
    $r .= '<p style="font-size: 0.9em; color: #666;">La somme totale est ' . $moyenne . ' × 4 = ' . $somme_totale . '</p>';
    $r .= '<p style="font-size: 0.9em; color: #666;">La 4<sup>e</sup> valeur est ' . $somme_totale . ' − (' . implode(' + ', $valeurs) . ') = ' . $somme_totale . ' − ' . $somme_3 . ' = ' . $x . '</p>';
    
    return ['type' => 'moyenne', 'difficulte_id' => 2.2, 'question' => $q, 'reponse' => $r];
}

function est_moyenne_belle($m) {
    // Entier ou .5 ou .25 ou .75
    return ($m == intval($m)) || 
           (abs($m - round($m * 2) / 2) < 0.01) || 
           (abs($m - round($m * 4) / 4) < 0.01);
}

function formater_nombre($n) {
    if ($n == intval($n)) {
        return intval($n);
    }
    return str_replace('.', ',', rtrim(rtrim(number_format($n, 2), '0'), ','));
}
?>
