<?php
/**
 * Automatisme DNB 2026 : Interpréter suite d'instructions
 * 
 * 3 types :
 * 1. Programme de calcul (organigramme)
 * 2. Programme de déplacement (quadrillage + flèches)
 * 3. Programme de construction géométrique (Scratch)
 */

// ========================================
// MODE DEBUG
// ========================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function generer_suites_instructions() {
    // Pool 3 types
    if (!isset($_SESSION['algorithmique_type_pool']) || empty($_SESSION['algorithmique_type_pool'])) {
        $_SESSION['algorithmique_type_pool'] = [
            'calcul', 'calcul', 'calcul',  // 40%
            'deplacement', 'deplacement',  // 30%
            'construction', 'construction', 'construction'  // 30%
        ];
        shuffle($_SESSION['algorithmique_type_pool']);
    }
    
    $type = array_shift($_SESSION['algorithmique_type_pool']);
    
    switch ($type) {
        case 'calcul':
            return si_generer_programme_calcul();
        case 'deplacement':
            return si_generer_programme_deplacement();
        case 'construction':
            return si_generer_programme_construction();
    }
}

// ============================================
// TYPE 1 : PROGRAMME DE CALCUL
// ============================================

function si_generer_programme_calcul() {
    // Pool type A/B
    if (!isset($_SESSION['calcul_type_pool']) || empty($_SESSION['calcul_type_pool'])) {
        $_SESSION['calcul_type_pool'] = ['A', 'B'];
        shuffle($_SESSION['calcul_type_pool']);
    }
    
    $type = array_shift($_SESSION['calcul_type_pool']);
    
    // Décider si 2 ou 3 opérations (50/50)
    $nb_operations = rand(0, 1) == 0 ? 2 : 3;
    
    // Pool d'opérations
    if ($nb_operations == 2) {
        $operations = [
            ['op1' => 'Multiplier par', 'val1' => rand(2, 5), 'op2' => 'Ajouter', 'val2' => rand(3, 10)],
            ['op1' => 'Ajouter', 'val1' => rand(5, 15), 'op2' => 'Multiplier par', 'val2' => rand(2, 4)],
            ['op1' => 'Multiplier par', 'val1' => rand(2, 3), 'op2' => 'Soustraire', 'val2' => rand(5, 12)],
            ['op1' => 'Soustraire', 'val1' => rand(3, 8), 'op2' => 'Multiplier par', 'val2' => rand(2, 5)],
            ['op1' => 'Multiplier par', 'val1' => 2, 'op2' => 'Ajouter', 'val2' => 3],
            ['op1' => 'Ajouter', 'val1' => 10, 'op2' => 'Diviser par', 'val2' => 2],
        ];
        $prog = $operations[array_rand($operations)];
        $prog['op3'] = null;
    } else {
        // 3 opérations
        $operations_3 = [
            ['op1' => 'Multiplier par', 'val1' => rand(2, 3), 'op2' => 'Ajouter', 'val2' => rand(3, 8), 'op3' => 'Soustraire', 'val3' => rand(2, 5)],
            ['op1' => 'Ajouter', 'val1' => rand(5, 10), 'op2' => 'Multiplier par', 'val2' => 2, 'op3' => 'Diviser par', 'val3' => 2],
            ['op1' => 'Multiplier par', 'val1' => 2, 'op2' => 'Soustraire', 'val2' => rand(3, 7), 'op3' => 'Multiplier par', 'val3' => rand(2, 3)],
        ];
        $prog = $operations_3[array_rand($operations_3)];
    }
    
    $depart = rand(2, 10);
    
    // Calcul étape 1
    if ($prog['op1'] == 'Multiplier par') {
        $etape1 = $depart * $prog['val1'];
    } elseif ($prog['op1'] == 'Ajouter') {
        $etape1 = $depart + $prog['val1'];
    } else {
        $etape1 = $depart - $prog['val1'];
    }
    
    // Calcul étape 2
    if ($prog['op2'] == 'Multiplier par') {
        $etape2 = $etape1 * $prog['val2'];
    } elseif ($prog['op2'] == 'Ajouter') {
        $etape2 = $etape1 + $prog['val2'];
    } elseif ($prog['op2'] == 'Diviser par') {
        $etape2 = $etape1 / $prog['val2'];
    } else {
        $etape2 = $etape1 - $prog['val2'];
    }
    
    // Calcul étape 3 si existe
    if ($prog['op3']) {
        if ($prog['op3'] == 'Multiplier par') {
            $resultat = $etape2 * $prog['val3'];
        } elseif ($prog['op3'] == 'Ajouter') {
            $resultat = $etape2 + $prog['val3'];
        } elseif ($prog['op3'] == 'Diviser par') {
            $resultat = $etape2 / $prog['val3'];
        } else {
            $resultat = $etape2 - $prog['val3'];
        }
    } else {
        $resultat = $etape2;
    }
    
    // Formater avec virgule française si décimal
    $resultat_fr = is_int($resultat) ? $resultat : str_replace('.', ',', $resultat);
    
    $q = '<p><strong>Programme de calcul :</strong></p>';
    $q .= '<div style="display: flex; flex-direction: column; align-items: center; margin: 20px 0;">';
    $q .= '<div style="border: 2px solid #333; padding: 12px 20px; background: #f0f0f0; min-width: 220px; text-align: center;">Choisir un nombre</div>';
    $q .= '<div style="font-size: 20px; line-height: 1;">▼</div>';
    $q .= '<div style="border: 2px solid #333; padding: 12px 20px; background: #f0f0f0; min-width: 220px; text-align: center;">' . $prog['op1'] . ' ' . $prog['val1'] . '</div>';
    $q .= '<div style="font-size: 20px; line-height: 1;">▼</div>';
    $q .= '<div style="border: 2px solid #333; padding: 12px 20px; background: #f0f0f0; min-width: 220px; text-align: center;">' . $prog['op2'] . ' ' . $prog['val2'] . '</div>';
    
    if ($prog['op3']) {
        $q .= '<div style="font-size: 20px; line-height: 1;">▼</div>';
        $q .= '<div style="border: 2px solid #333; padding: 12px 20px; background: #f0f0f0; min-width: 220px; text-align: center;">' . $prog['op3'] . ' ' . $prog['val3'] . '</div>';
    }
    
    $q .= '<div style="font-size: 20px; line-height: 1;">▼</div>';
    $q .= '<div style="border: 2px solid #333; padding: 12px 20px; background: #e8f4f8; min-width: 220px; text-align: center; font-weight: bold;">Afficher Résultat</div>';
    $q .= '</div>';
    
    if ($type == 'A') {
        // Type A : Nombre de départ donné, trouver le résultat
        $q .= '<p><strong>' . $depart . ' est le nombre de départ. Qu\'obtient-on ?</strong></p>';
        $r = '<p><strong>' . $resultat_fr . '</strong></p>';
    } else {
        // Type B : Résultat donné, trouver le nombre de départ
        $q .= '<p><strong>' . $resultat_fr . ' est le résultat obtenu. Quel était le nombre de départ ?</strong></p>';
        $r = '<p><strong>' . $depart . '</strong></p>';
    }
    
    $diff = $nb_operations == 2 ? 1.0 : 1.3;
    if ($type == 'B') $diff += 0.3; // Type B plus difficile
    
    return [
        'type' => 'algorithmique',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// TYPE 2 : PROGRAMME DE DÉPLACEMENT
// ============================================

function si_generer_programme_deplacement() {
    // Pool type A/B
    if (!isset($_SESSION['deplacement_type_pool']) || empty($_SESSION['deplacement_type_pool'])) {
        $_SESSION['deplacement_type_pool'] = ['A', 'B'];
        shuffle($_SESSION['deplacement_type_pool']);
    }
    
    $type = array_shift($_SESSION['deplacement_type_pool']);
    
    if ($type == 'A') {
        // Type A : Départ + flèches → trouver arrivée
        return si_deplacement_type_a();
    } else {
        // Type B : Départ + arrivée → coder le chemin
        return si_deplacement_type_b();
    }
}

function si_deplacement_type_a() {
    // Quadrillage 5×5 avec coordonnées échiquier
    $cols = ['A', 'B', 'C', 'D', 'E'];
    $lignes = [1, 2, 3, 4, 5];
    
    $depart_col = rand(0, 3);
    $depart_ligne = rand(1, 3);
    
    // Générer 4-6 déplacements
    $nb_depl = rand(4, 6);
    $fleches = ['→', '↑', '←', '↓'];
    $fleches_texte = ['droite', 'haut', 'gauche', 'bas'];
    $deplacement = [];
    
    $col = $depart_col;
    $ligne = $depart_ligne;
    
    for ($i = 0; $i < $nb_depl; $i++) {
        $dir = rand(0, 3);
        $deplacement[] = ['fleche' => $fleches[$dir], 'texte' => $fleches_texte[$dir]];
        
        // Appliquer déplacement
        if ($dir == 0) { // droite
            $col = min($col + 1, 4);
        } elseif ($dir == 1) { // haut
            $ligne = min($ligne + 1, 5);
        } elseif ($dir == 2) { // gauche
            $col = max($col - 1, 0);
        } else { // bas
            $ligne = max($ligne - 1, 1);
        }
    }
    
    // Générer quadrillage SVG
    $svg = '<svg width="350" height="350" style="border: 1px solid #ddd; background: white; margin: 20px auto; display: block;">';
    
    // Lignes du quadrillage
    for ($i = 0; $i <= 5; $i++) {
        $pos = 50 + $i * 50;
        $svg .= '<line x1="50" y1="' . $pos . '" x2="300" y2="' . $pos . '" stroke="#333" stroke-width="2"/>';
        $svg .= '<line x1="' . $pos . '" y1="50" x2="' . $pos . '" y2="300" stroke="#333" stroke-width="2"/>';
    }
    
    // Lettres colonnes (A-E)
    for ($i = 0; $i < 5; $i++) {
        $pos = 50 + $i * 50 + 25;
        $svg .= '<text x="' . $pos . '" y="330" text-anchor="middle" font-size="18" font-weight="bold" fill="#333">' . $cols[$i] . '</text>';
    }
    
    // Chiffres lignes (1-5) inversés pour correspondre au système de coordonnées
    for ($i = 0; $i < 5; $i++) {
        $pos = 50 + (4 - $i) * 50 + 25; // Inverser l'ordre
        $svg .= '<text x="25" y="' . ($pos + 6) . '" text-anchor="middle" font-size="18" font-weight="bold" fill="#333">' . ($i + 1) . '</text>';
    }
    
    // Chat au départ
    $chat_x = 50 + $depart_col * 50 + 25;
    $chat_y = 50 + (5 - $depart_ligne) * 50 + 25; // Inverser pour correspondre au système
    $svg .= '<text x="' . $chat_x . '" y="' . ($chat_y + 10) . '" text-anchor="middle" font-size="36">🐱</text>';
    
    $svg .= '</svg>';
    
    $q = '<p><strong>Programme de déplacement :</strong></p>';
    $q .= $svg;
    $q .= '<p>Le chat est en <strong>' . $cols[$depart_col] . $depart_ligne . '</strong>.</p>';
    $q .= '<p><strong>Séquence de déplacements :</strong></p>';
    $q .= '<p style="font-size: 32px; letter-spacing: 8px; text-align: center;">';
    foreach ($deplacement as $d) {
        $q .= $d['fleche'] . ' ';
    }
    $q .= '</p>';
    $q .= '<p><strong>Où arrive le chat ?</strong></p>';
    
    $r = '<p><strong>' . $cols[$col] . $ligne . '</strong></p>';
    
    return [
        'type' => 'algorithmique',
        'difficulte_id' => 1.2,
        'question' => $q,
        'reponse' => $r
    ];
}

function si_deplacement_type_b() {
    // Type B : coder le chemin le plus court
    $cols = ['A', 'B', 'C', 'D', 'E'];
    $lignes = [1, 2, 3, 4, 5];
    
    $depart_col = rand(0, 2);
    $depart_ligne = rand(1, 3);
    
    $arrivee_col = $depart_col + rand(1, 3);
    $arrivee_ligne = $depart_ligne + rand(1, 3);
    
    if ($arrivee_col > 4) $arrivee_col = 4;
    if ($arrivee_ligne > 5) $arrivee_ligne = 5;
    
    // Calculer déplacements nécessaires
    $nb_droite = $arrivee_col - $depart_col;
    $nb_haut = $arrivee_ligne - $depart_ligne;
    
    // Générer quadrillage
    $svg = '<svg width="350" height="350" style="border: 1px solid #ddd; background: white; margin: 20px auto; display: block;">';
    
    for ($i = 0; $i <= 5; $i++) {
        $pos = 50 + $i * 50;
        $svg .= '<line x1="50" y1="' . $pos . '" x2="300" y2="' . $pos . '" stroke="#333" stroke-width="2"/>';
        $svg .= '<line x1="' . $pos . '" y1="50" x2="' . $pos . '" y2="300" stroke="#333" stroke-width="2"/>';
    }
    
    for ($i = 0; $i < 5; $i++) {
        $pos = 50 + $i * 50 + 25;
        $svg .= '<text x="' . $pos . '" y="330" text-anchor="middle" font-size="18" font-weight="bold" fill="#333">' . $cols[$i] . '</text>';
    }
    
    for ($i = 0; $i < 5; $i++) {
        $pos = 50 + (4 - $i) * 50 + 25;
        $svg .= '<text x="25" y="' . ($pos + 6) . '" text-anchor="middle" font-size="18" font-weight="bold" fill="#333">' . ($i + 1) . '</text>';
    }
    
    // Chat départ
    $chat_x = 50 + $depart_col * 50 + 25;
    $chat_y = 50 + (5 - $depart_ligne) * 50 + 25;
    $svg .= '<text x="' . $chat_x . '" y="' . ($chat_y + 10) . '" text-anchor="middle" font-size="36">🐱</text>';
    
    // Croix arrivée
    $croix_x = 50 + $arrivee_col * 50 + 25;
    $croix_y = 50 + (5 - $arrivee_ligne) * 50 + 25;
    $svg .= '<text x="' . $croix_x . '" y="' . ($croix_y + 10) . '" text-anchor="middle" font-size="36" fill="red">✖</text>';
    
    $svg .= '</svg>';
    
    $q = '<p><strong>Programme de déplacement :</strong></p>';
    $q .= $svg;
    $q .= '<p>Le chat est en <strong>' . $cols[$depart_col] . $depart_ligne . '</strong> et doit aller en <strong>' . $cols[$arrivee_col] . $arrivee_ligne . '</strong>.</p>';
    $q .= '<p><strong>Coder avec des flèches le plus court chemin.</strong></p>';
    
    $fleche_droite = $nb_droite > 1 ? 'flèches' : 'flèche';
    $fleche_haut = $nb_haut > 1 ? 'flèches' : 'flèche';
    
    $r = '<p><strong>Il faut ' . $nb_droite . ' ' . $fleche_droite . ' vers la droite (→) et ' . $nb_haut . ' ' . $fleche_haut . ' vers le haut (↑).</strong></p>';
    $r .= '<p>Exemple de solution : ';
    for ($i = 0; $i < $nb_droite; $i++) $r .= '→';
    for ($i = 0; $i < $nb_haut; $i++) $r .= '↑';
    $r .= '</p>';
    
    return [
        'type' => 'algorithmique',
        'difficulte_id' => 1.5,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// TYPE 3 : PROGRAMME DE CONSTRUCTION (Scratch)
// ============================================

function si_generer_programme_construction() {
    // Pool polygone/complexe
    if (!isset($_SESSION['construction_type_pool']) || empty($_SESSION['construction_type_pool'])) {
        $_SESSION['construction_type_pool'] = ['polygone', 'complexe'];
        shuffle($_SESSION['construction_type_pool']);
    }
    
    $type = array_shift($_SESSION['construction_type_pool']);
    
    if ($type == 'polygone') {
        return si_generer_scratch_polygone();
    } else {
        return si_generer_scratch_complexe();
    }
}

function si_generer_scratch_polygone() {
    // Pool de figures polygonales
    $figures = [
        ['nom' => 'carré', 'repet' => 4, 'angle' => 90, 'cote' => 50, 'montrer_figure' => rand(0,1)],
        ['nom' => 'triangle équilatéral', 'repet' => 3, 'angle' => 120, 'cote' => 60, 'montrer_figure' => false], // Jamais de SVG
        ['nom' => 'hexagone régulier', 'repet' => 6, 'angle' => 60, 'cote' => 40, 'montrer_figure' => rand(0,1)],
        ['nom' => 'pentagone régulier', 'repet' => 5, 'angle' => null, 'cote' => 45, 'montrer_figure' => false], // Pas d'angle demandé
    ];
    
    $fig = $figures[array_rand($figures)];
    
    // Générer SVG de la figure SI nécessaire
    $svg = $fig['montrer_figure'] ? si_generer_svg_figure_scratch($fig) : '';
    
    // Script Scratch avec valeurs manquantes
    $q = '<p><strong>Programme de construction Scratch :</strong></p>';
    $q .= '<div style="font-family: Arial; display: inline-block; margin: 20px 0;">';
    $q .= '<div style="background: #FFAB19; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0;">🏁 Quand drapeau vert pressé</div>';
    $q .= '<div style="background: #9966FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 20px;"><span style="opacity: 0.7;">1.</span> Stylo en position d\'écriture</div>';
    
    if ($fig['angle'] === null) {
        // Pentagone : seulement répétitions manquantes
        $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 20px;"><span style="opacity: 0.7;">2.</span> Répéter <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px; font-weight: bold;">[?]</span> fois</div>';
        $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">3.</span> Avancer de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px;">' . $fig['cote'] . '</span></div>';
        $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">4.</span> Tourner à droite de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px;">72</span> degrés</div>';
        $q .= '</div>';
        $q .= '<p><strong>Par quelle valeur doit-on compléter la ligne 2 pour obtenir un ' . $fig['nom'] . ' ?</strong></p>';
        
        $r = '<p><strong>Ligne 2 : ' . $fig['repet'] . '</strong></p>';
        
    } else {
        // Autres figures : répétitions ET angle
        $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 20px;"><span style="opacity: 0.7;">2.</span> Répéter <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px; font-weight: bold;">[?]</span> fois</div>';
        $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">3.</span> Avancer de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px;">' . $fig['cote'] . '</span></div>';
        $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">4.</span> Tourner à gauche de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px; font-weight: bold;">[?]</span> degrés</div>';
        $q .= '</div>';
        
        if ($fig['montrer_figure']) {
            $q .= $svg;
        }
        
        $q .= '<p><strong>Par quelles valeurs doit-on compléter pour obtenir un ' . $fig['nom'] . ' ?</strong></p>';
        
        $r = '<p><strong>Ligne 2 : ' . $fig['repet'] . '</strong></p>';
        $r .= '<p><strong>Ligne 4 : ' . $fig['angle'] . '°</strong></p>';
    }
    
    return [
        'type' => 'algorithmique',
        'difficulte_id' => 1.5,
        'question' => $q,
        'reponse' => $r
    ];
}

function si_generer_scratch_complexe() {
    // Pool de figures complexes
    $figures_complexes = [
        'escalier',
        'maison'
    ];
    
    $type_fig = $figures_complexes[array_rand($figures_complexes)];
    
    if ($type_fig == 'escalier') {
        return si_generer_scratch_escalier();
    } elseif ($type_fig == 'maison') {
        return si_generer_scratch_maison();
    }
}

function si_generer_scratch_escalier() {
    $nb_marches = rand(4, 6);
    
    // Varier hauteur et largeur pour différencier
    $variantes = [
        ['largeur' => 40, 'hauteur' => 30],
        ['largeur' => 30, 'hauteur' => 40],
        ['largeur' => 35, 'hauteur' => 25],
        ['largeur' => 25, 'hauteur' => 35],
        ['largeur' => 50, 'hauteur' => 30],
        ['largeur' => 30, 'hauteur' => 50],
    ];
    
    $variante = $variantes[array_rand($variantes)];
    $largeur_marche = $variante['largeur'];
    $hauteur_marche = $variante['hauteur'];
    
    // Générer SVG de l'escalier
    $svg = '<svg width="350" height="350" style="border: 1px solid #ddd; background: white; margin: 20px auto; display: block;">';
    $svg .= '<text x="175" y="25" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">Figure à obtenir</text>';
    
    // Dessiner l'escalier
    $x = 50;
    $y = 250;
    $path = 'M ' . $x . ',' . $y;
    
    for ($i = 0; $i < $nb_marches; $i++) {
        // Avancer à droite
        $x += $largeur_marche;
        $path .= ' L ' . $x . ',' . $y;
        // Monter
        $y -= $hauteur_marche;
        $path .= ' L ' . $x . ',' . $y;
    }
    
    $svg .= '<path d="' . $path . '" fill="none" stroke="#2196F3" stroke-width="3"/>';
    $svg .= '<circle cx="50" cy="250" r="5" fill="#FF5722"/>'; // Point de départ
    
    // Cotations : flèche horizontale pour la largeur (première marche)
    $svg .= '<line x1="50" y1="260" x2="' . (50 + $largeur_marche) . '" y2="260" stroke="#FF5722" stroke-width="1" marker-end="url(#arrowred)" marker-start="url(#arrowred)"/>';
    $svg .= '<text x="' . (50 + $largeur_marche/2) . '" y="275" text-anchor="middle" font-size="14" font-weight="bold" fill="#FF5722">' . $largeur_marche . '</text>';
    
    // Cotations : flèche verticale pour la hauteur (première marche)
    $svg .= '<line x1="40" y1="250" x2="40" y2="' . (250 - $hauteur_marche) . '" stroke="#FF5722" stroke-width="1" marker-end="url(#arrowred)" marker-start="url(#arrowred)"/>';
    $svg .= '<text x="25" y="' . (250 - $hauteur_marche/2 + 5) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#FF5722">' . $hauteur_marche . '</text>';
    
    // Définir les marqueurs de flèches
    $svg .= '<defs>';
    $svg .= '<marker id="arrowred" markerWidth="10" markerHeight="10" refX="5" refY="3" orient="auto" markerUnits="strokeWidth">';
    $svg .= '<path d="M0,0 L0,6 L9,3 z" fill="#FF5722" />';
    $svg .= '</marker>';
    $svg .= '</defs>';
    
    $svg .= '</svg>';
    
    // Script Scratch
    $q = '<p><strong>Programme de construction Scratch :</strong></p>';
    $q .= '<div style="display: flex; gap: 30px; align-items: flex-start; justify-content: center; margin: 20px 0;">';
    
    // Programme Scratch à gauche
    $q .= '<div style="font-family: Arial;">';
    $q .= '<div style="background: #FFAB19; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0;">🏁 Quand drapeau vert pressé</div>';
    $q .= '<div style="background: #9966FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 20px;"><span style="opacity: 0.7;">1.</span> Stylo en position d\'écriture</div>';
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 20px;"><span style="opacity: 0.7;">2.</span> Répéter <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px; font-weight: bold;">[?]</span> fois</div>';
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">3.</span> Avancer de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px; font-weight: bold;">[?]</span></div>';
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">4.</span> Tourner à gauche de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px;">90</span> degrés</div>';
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">5.</span> Avancer de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px; font-weight: bold;">[?]</span></div>';
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">6.</span> Tourner à droite de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px;">90</span> degrés</div>';
    $q .= '</div>';
    
    // Figure SVG à droite
    $q .= '<div>' . $svg . '</div>';
    
    $q .= '</div>';
    $q .= '<p><strong>Compléter le programme pour obtenir la figure ci-dessus.</strong></p>';
    
    $r = '<p><strong>Ligne 2 : ' . $nb_marches . '</strong></p>';
    $r .= '<p><strong>Ligne 3 : ' . $largeur_marche . '</strong></p>';
    $r .= '<p><strong>Ligne 5 : ' . $hauteur_marche . '</strong></p>';
    
    return [
        'type' => 'algorithmique',
        'difficulte_id' => 1.8,
        'question' => $q,
        'reponse' => $r
    ];
}

function si_generer_scratch_maison() {
    $cote = rand(3, 5) * 10; // 30, 40 ou 50 - même longueur pour tout !
    
    // Générer SVG de la maison avec triangle équilatéral
    $svg = '<svg width="350" height="350" style="border: 1px solid #ddd; background: white; margin: 20px auto; display: block;">';
    $svg .= '<text x="175" y="25" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">Figure à obtenir</text>';
    
    // Position de départ (coin haut gauche du carré)
    $x_depart = 100;
    $y_depart = 170;
    
    // Dessiner le carré (base de la maison)
    $svg .= '<rect x="' . $x_depart . '" y="' . $y_depart . '" width="' . $cote . '" height="' . $cote . '" fill="none" stroke="#2196F3" stroke-width="3"/>';
    
    // Dessiner le toit (triangle équilatéral)
    $x_sommet = $x_depart + $cote / 2;
    $hauteur_triangle = $cote * sqrt(3) / 2; // Hauteur d'un triangle équilatéral
    $y_sommet = $y_depart - $hauteur_triangle;
    $svg .= '<path d="M ' . $x_depart . ',' . $y_depart . ' L ' . $x_sommet . ',' . $y_sommet . ' L ' . ($x_depart + $cote) . ',' . $y_depart . '" fill="none" stroke="#2196F3" stroke-width="3"/>';
    
    // Point de départ (coin haut gauche)
    $svg .= '<circle cx="' . $x_depart . '" cy="' . $y_depart . '" r="5" fill="#FF5722"/>';
    
    // Pas de cotations
    
    $svg .= '</svg>';
    
    // Script Scratch
    $q = '<p><strong>Programme de construction Scratch :</strong></p>';
    $q .= '<div style="display: flex; gap: 30px; align-items: flex-start; justify-content: center; margin: 20px 0;">';
    
    // Programme Scratch à gauche
    $q .= '<div style="font-family: Arial;">';
    $q .= '<div style="background: #FFAB19; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0;">🏁 Quand drapeau vert pressé</div>';
    $q .= '<div style="background: #9966FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 20px;"><span style="opacity: 0.7;">1.</span> Stylo en position d\'écriture</div>';
    
    // Tourner à gauche pour commencer
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 20px;"><span style="opacity: 0.7;">2.</span> Tourner à gauche de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px;">180</span> degrés</div>';
    
    // Carré (4 fois : tourner à gauche 90° + avancer)
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 20px;"><span style="opacity: 0.7;">3.</span> Répéter <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px; font-weight: bold;">[?]</span> fois</div>';
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">4.</span> Tourner à gauche de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px; font-weight: bold;">[?]</span> degrés</div>';
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">5.</span> Avancer de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px;">' . $cote . '</span></div>';
    
    // Toit (2 fois : tourner à droite 120° + avancer)
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 20px;"><span style="opacity: 0.7;">6.</span> Répéter <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px; font-weight: bold;">[?]</span> fois</div>';
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">7.</span> Tourner à droite de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px;">120</span> degrés</div>';
    $q .= '<div style="background: #4C97FF; color: white; padding: 8px 12px; border-radius: 8px; margin: 2px 0; margin-left: 40px;"><span style="opacity: 0.7;">8.</span> Avancer de <span style="background: white; color: #4C97FF; padding: 2px 8px; border-radius: 4px;">' . $cote . '</span></div>';
    $q .= '</div>';
    
    // Figure SVG à droite
    $q .= '<div>' . $svg . '</div>';
    
    $q .= '</div>';
    $q .= '<p><strong>Compléter le programme pour obtenir la figure ci-dessus.</strong></p>';
    
    $r = '<p><strong>Ligne 3 : 4</strong> (répétitions pour le carré)</p>';
    $r .= '<p><strong>Ligne 4 : 90°</strong> (angle du carré)</p>';
    $r .= '<p><strong>Ligne 6 : 2</strong> (répétitions pour le toit)</p>';
    
    return [
        'type' => 'algorithmique',
        'difficulte_id' => 2.0,
        'question' => $q,
        'reponse' => $r
    ];
}

function si_generer_svg_figure_scratch($fig) {
    $width = 300;
    $height = 300;
    $cx = $width / 2;
    $cy = $height / 2;
    
    $svg = '<svg width="' . $width . '" height="' . $height . '" style="border: 1px solid #ddd; background: white; margin: 20px auto; display: block;">';
    
    // Titre
    $svg .= '<text x="' . $cx . '" y="25" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">Figure obtenue</text>';
    
    // Calculer points du polygone
    $rayon = 80;
    $points = [];
    
    for ($i = 0; $i < $fig['repet']; $i++) {
        $angle = deg2rad(90 - $i * (360 / $fig['repet'])); // Commence en haut
        $x = $cx + $rayon * cos($angle);
        $y = $cy - $rayon * sin($angle);
        $points[] = [$x, $y];
    }
    
    // Tracer le polygone
    $path = 'M ' . $points[0][0] . ',' . $points[0][1];
    for ($i = 1; $i < count($points); $i++) {
        $path .= ' L ' . $points[$i][0] . ',' . $points[$i][1];
    }
    $path .= ' Z';
    
    $svg .= '<path d="' . $path . '" fill="none" stroke="#2196F3" stroke-width="3"/>';
    
    // Points
    foreach ($points as $p) {
        $svg .= '<circle cx="' . $p[0] . '" cy="' . $p[1] . '" r="4" fill="#FF5722"/>';
    }
    
    $svg .= '</svg>';
    
    return $svg;
}
?>
