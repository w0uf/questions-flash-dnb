<?php
/**
 * Automatisme : Reconnaître et citer des angles sur une configuration géométrique
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.4)
 * Format : Questions OUI/NON et QCM
 */

function generer_angles() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    if (!isset($_SESSION['angles_pool']) || empty($_SESSION['angles_pool'])) {
        $_SESSION['angles_pool'] = [
            // CLASSIFICATION PAR MESURE - QCM (6 questions)
            'angle_45_aigu',           // 45° → aigu ?
            'angle_120_obtus',         // 120° → obtus ?
            'angle_90_droit',          // 90° → droit ?
            'angle_180_plat',          // 180° → plat ?
            'angle_60_obtus_faux',     // 60° → obtus ? NON
            'angle_135_aigu_faux',     // 135° → aigu ? NON
            
            // ANGLES OPPOSÉS PAR LE SOMMET - OUI/NON (3 questions)
            'opposes_vrais',           // 2 droites sécantes, angles opposés → OUI
            'opposes_adjacents',       // 2 droites sécantes, angles adjacents → NON
            'opposes_egaux_faux',      // Angles égaux mais pas en position opposée → NON
            
            // ANGLES ADJACENTS - OUI/NON (4 questions)
            'adjacents_vrais',         // Même sommet, côté commun → OUI
            'adjacents_separes',       // Angles séparés → NON
            'adjacents_opposes',       // Angles opposés → NON
            'adjacents_alignes',       // Alignés sans côté commun → NON
            
            // ANGLES SUPPLÉMENTAIRES - OUI/NON (4 questions)
            'supplementaires_90_90',   // 90° + 90° = 180° → OUI
            'supplementaires_110_70',  // 110° + 70° = 180° → OUI
            'supplementaires_60_80',   // 60° + 80° ≠ 180° → NON
            'supplementaires_45_135',  // 45° + 135° = 180° → OUI
        ];
        shuffle($_SESSION['angles_pool']);
    }
    
    $type_question = array_shift($_SESSION['angles_pool']);
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // CLASSIFICATION PAR MESURE (QCM)
        // ============================================
        
        case 'angle_45_aigu':
            $svg = generer_angle_isole(45);
            $propositions = [
                'aigu' => 'aigu',
                'droit' => 'droit',
                'obtus' => 'obtus',
                'plat' => 'plat'
            ];
            $qcm = generer_qcm_angles($propositions, 'aigu');
            $question_html = '<p>L\'angle ' . angle_chapeau('ABC') . ' mesure 45°.</p>' . $svg . '<p>Cet angle est :</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. aigu</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'angle_120_obtus':
            $svg = generer_angle_isole(120);
            $propositions = [
                'aigu' => 'aigu',
                'droit' => 'droit',
                'obtus' => 'obtus',
                'plat' => 'plat'
            ];
            $qcm = generer_qcm_angles($propositions, 'obtus');
            $question_html = '<p>L\'angle ' . angle_chapeau('ABC') . ' mesure 120°.</p>' . $svg . '<p>Cet angle est :</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. obtus</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'angle_90_droit':
            $svg = generer_angle_isole(90);
            $propositions = [
                'aigu' => 'aigu',
                'droit' => 'droit',
                'obtus' => 'obtus',
                'plat' => 'plat'
            ];
            $qcm = generer_qcm_angles($propositions, 'droit');
            $question_html = '<p>L\'angle ' . angle_chapeau('ABC') . ' mesure 90°.</p>' . $svg . '<p>Cet angle est :</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. droit</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'angle_180_plat':
            $svg = generer_angle_plat();
            $propositions = [
                'aigu' => 'aigu',
                'droit' => 'droit',
                'obtus' => 'obtus',
                'plat' => 'plat'
            ];
            $qcm = generer_qcm_angles($propositions, 'plat');
            $question_html = '<p>L\'angle ' . angle_chapeau('ABC') . ' mesure 180°.</p>' . $svg . '<p>Cet angle est :</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. plat</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'angle_60_obtus_faux':
            $svg = generer_angle_isole(60);
            $question_html = '<p>L\'angle ' . angle_chapeau('ABC') . ' mesure 60°. Est-il obtus ?</p>' . $svg;
            $reponse_html = '<p><strong>NON</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'angle_135_aigu_faux':
            $svg = generer_angle_isole(135);
            $question_html = '<p>L\'angle ' . angle_chapeau('ABC') . ' mesure 135°. Est-il aigu ?</p>' . $svg;
            $reponse_html = '<p><strong>NON</strong></p>';
            $difficulte = 1.1;
            break;
            
        // ============================================
        // ANGLES OPPOSÉS PAR LE SOMMET
        // ============================================
        
        case 'opposes_vrais':
            $svg = generer_droites_secantes('opposes');
            $question_html = '<p>Les angles α et β sont-ils opposés par le sommet ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'opposes_adjacents':
            $svg = generer_droites_secantes('adjacents');
            $question_html = '<p>Les angles α et β sont-ils opposés par le sommet ?</p>' . $svg;
            $reponse_html = '<p><strong>NON.</strong> Ils sont adjacents.</p>';
            $difficulte = 1.2;
            break;
            
        case 'opposes_egaux_faux':
            $svg = generer_angles_egaux_non_opposes();
            $question_html = '<p>Les angles α et β sont-ils opposés par le sommet ?</p>' . $svg;
            $reponse_html = '<p><strong>NON.</strong> Ils sont égaux mais pas en position opposée.</p>';
            $difficulte = 1.3;
            break;
            
        // ============================================
        // ANGLES ADJACENTS
        // ============================================
        
        case 'adjacents_vrais':
            $svg = generer_angles_adjacents_vrais();
            $question_html = '<p>Les angles α et β sont-ils adjacents ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'adjacents_separes':
            $svg = generer_angles_separes();
            $question_html = '<p>Les angles α et β sont-ils adjacents ?</p>' . $svg;
            $reponse_html = '<p><strong>NON.</strong> Ils n\'ont pas de sommet commun.</p>';
            $difficulte = 1.1;
            break;
            
        case 'adjacents_opposes':
            $svg = generer_droites_secantes('opposes');
            $question_html = '<p>Les angles α et β sont-ils adjacents ?</p>' . $svg;
            $reponse_html = '<p><strong>NON.</strong> Ils sont opposés par le sommet.</p>';
            $difficulte = 1.2;
            break;
            
        case 'adjacents_alignes':
            $svg = generer_angles_alignes_non_adjacents();
            $question_html = '<p>Les angles α et β sont-ils adjacents ?</p>' . $svg;
            $reponse_html = '<p><strong>NON.</strong> Ils n\'ont pas de côté commun.</p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // ANGLES SUPPLÉMENTAIRES
        // ============================================
        
        case 'supplementaires_90_90':
            $svg = generer_deux_angles_droits_codes();
            $question_html = '<p>Les angles codés sont-ils supplémentaires ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'supplementaires_110_70':
            $svg = generer_deux_angles_mesures(110, 70);
            $question_html = '<p>L\'angle α mesure 110° et l\'angle β mesure 70°.</p>' . $svg . '<p>Ces angles sont-ils supplémentaires ?</p>';
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'supplementaires_60_80':
            $svg = generer_deux_angles_mesures(60, 80);
            $question_html = '<p>L\'angle α mesure 60° et l\'angle β mesure 80°.</p>' . $svg . '<p>Ces angles sont-ils supplémentaires ?</p>';
            $reponse_html = '<p><strong>NON</strong></p>';
            $difficulte = 1.2;
            break;
            
        case 'supplementaires_45_135':
            $svg = generer_deux_angles_mesures(45, 135);
            $question_html = '<p>L\'angle α mesure 45° et l\'angle β mesure 135°.</p>' . $svg . '<p>Ces angles sont-ils supplémentaires ?</p>';
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.1;
            break;
    }
    
    return [
        'type' => 'angles',
        'question' => $question_html,
        'reponse' => $reponse_html,
        'difficulte' => $difficulte
    ];
}

// ============================================
// FONCTION HELPER POUR ANGLES AVEC CHAPEAU
// ============================================

if (!function_exists('angle_chapeau')) {
    function angle_chapeau($lettres) {
        return '<span class="angle"><span class="hat">^</span>' . $lettres . '</span>';
    }
}

// ============================================
// FONCTION HELPER POUR QCM (FORMAT STANDARD EN LIGNE)
// ============================================

function generer_qcm_angles($propositions_data, $bonne_reponse_key) {
    // Mélanger les propositions
    $propositions_array = [];
    foreach ($propositions_data as $key => $texte) {
        $propositions_array[] = ['key' => $key, 'texte' => $texte];
    }
    shuffle($propositions_array);
    
    // Trouver la position de la bonne réponse
    $lettres = ['A', 'B', 'C', 'D'];
    $bonne_lettre = '';
    
    $qcm_html = '<p>';
    
    foreach ($propositions_array as $index => $prop) {
        $lettre = $lettres[$index];
        
        // Vérifier si c'est la bonne réponse
        if ($prop['key'] == $bonne_reponse_key) {
            $bonne_lettre = $lettre;
        }
        
        // Afficher la proposition en ligne avec padding
        $qcm_html .= '<span style="padding-right: 50px;"><strong>' . $lettre . '.</strong> ' . $prop['texte'] . '</span>';
    }
    
    $qcm_html .= '</p>';
    
    return ['html' => $qcm_html, 'bonne_lettre' => $bonne_lettre];
}

// ============================================
// FONCTIONS DE GÉNÉRATION SVG - ANGLE ISOLÉ
// ============================================

function generer_angle_isole($mesure_degres) {
    $svg = '<svg width="350" height="280" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Point B (sommet de l'angle) - décalé plus bas
    $bx = 80; $by = 220;
    
    // Point A (sur un côté horizontal)
    $ax = 300; $ay = 220;
    
    // Point C (sur l'autre côté, angle de $mesure_degres)
    $angle_rad = deg2rad($mesure_degres);
    $longueur = 220;
    $cx = $bx + $longueur * cos($angle_rad);
    $cy = $by - $longueur * sin($angle_rad);  // - car y inversé
    
    // Dessiner les deux côtés
    $svg .= '<line x1="' . $bx . '" y1="' . $by . '" x2="' . $ax . '" y2="' . $ay . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $bx . '" y1="' . $by . '" x2="' . $cx . '" y2="' . $cy . '" stroke="#333" stroke-width="2" />';
    
    // Codage angle droit si 90° (carré seulement, pas d'arc)
    if ($mesure_degres == 90) {
        $taille = 15;
        $svg .= '<rect x="' . $bx . '" y="' . ($by - $taille) . '" width="' . $taille . '" height="' . $taille . '" fill="none" stroke="#333" stroke-width="2" />';
    } else {
        // Arc de l'angle avec la fonction codage_angle (seulement si pas 90°)
        require_once('codage_figures.php');
        $svg .= codage_angle($bx, $by, $ax, $ay, $cx, $cy, 1);
    }
    
    // Afficher la mesure
    $svg .= '<text x="' . ($bx + 50) . '" y="' . ($by - 10) . '" font-size="18" font-weight="bold" fill="#e74c3c">' . $mesure_degres . '°</text>';
    
    // Labels des points
    $svg .= '<text x="' . ($ax + 10) . '" y="' . ($ay + 5) . '" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 20) . '" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 5) . '" y="' . ($cy - 5) . '" font-size="16" font-weight="bold">C</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_angle_plat() {
    $svg = '<svg width="350" height="120" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Angle plat = droite
    $ax = 30; $ay = 60;
    $bx = 175; $by = 60;
    $cx = 320; $cy = 60;
    
    $svg .= '<line x1="' . $ax . '" y1="' . $ay . '" x2="' . $cx . '" y2="' . $cy . '" stroke="#333" stroke-width="2" />';
    
    // Arc plat (demi-cercle au-dessus)
    $rayon = 30;
    $svg .= '<path d="M ' . ($bx - $rayon) . ' ' . $by . ' A ' . $rayon . ' ' . $rayon . ' 0 0 1 ' . ($bx + $rayon) . ' ' . $by . '" fill="none" stroke="#e74c3c" stroke-width="2.5" />';
    
    // Mesure
    $svg .= '<text x="' . $bx . '" y="' . ($by - 40) . '" text-anchor="middle" font-size="18" font-weight="bold" fill="#e74c3c">180°</text>';
    
    // Labels
    $svg .= '<text x="' . $ax . '" y="' . ($ay + 25) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . $bx . '" y="' . ($by + 25) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . $cx . '" y="' . ($cy + 25) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    
    $svg .= '</svg>';
    return $svg;
}

// Suite des fonctions SVG dans le prochain fichier...

// ============================================
// FONCTIONS DE GÉNÉRATION SVG - DROITES SÉCANTES
// ============================================

function generer_droites_secantes($type_angles) {
    $svg = '<svg width="350" height="300" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Point d'intersection O
    $ox = 175; $oy = 150;
    
    // Première droite (horizontale légèrement inclinée)
    $d1_x1 = 30; $d1_y1 = 170;
    $d1_x2 = 320; $d1_y2 = 130;
    
    // Deuxième droite (verticale légèrement inclinée)
    $d2_x1 = 150; $d2_y1 = 30;
    $d2_x2 = 200; $d2_y2 = 270;
    
    // Dessiner les droites
    $svg .= '<line x1="' . $d1_x1 . '" y1="' . $d1_y1 . '" x2="' . $d1_x2 . '" y2="' . $d1_y2 . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $d2_x1 . '" y1="' . $d2_y1 . '" x2="' . $d2_x2 . '" y2="' . $d2_y2 . '" stroke="#333" stroke-width="2" />';
    
    // Marquer les angles selon le type
    if ($type_angles == 'opposes') {
        // Angles opposés (haut-gauche et bas-droite)
        $svg .= '<text x="' . ($ox - 50) . '" y="' . ($oy - 20) . '" font-size="20" font-weight="bold" fill="#e74c3c">α</text>';
        $svg .= '<text x="' . ($ox + 40) . '" y="' . ($oy + 35) . '" font-size="20" font-weight="bold" fill="#2980b9">β</text>';
    } else {
        // Angles adjacents (haut-gauche et haut-droite)
        $svg .= '<text x="' . ($ox - 50) . '" y="' . ($oy - 20) . '" font-size="20" font-weight="bold" fill="#e74c3c">α</text>';
        $svg .= '<text x="' . ($ox + 40) . '" y="' . ($oy - 20) . '" font-size="20" font-weight="bold" fill="#2980b9">β</text>';
    }
    
    // Point O
    $svg .= '<circle cx="' . $ox . '" cy="' . $oy . '" r="3" fill="#333" />';
    $svg .= '<text x="' . ($ox + 10) . '" y="' . ($oy + 20) . '" font-size="14">O</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_angles_egaux_non_opposes() {
    $svg = '<svg width="350" height="250" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Deux angles de 60° égaux mais séparés
    // Premier angle à gauche
    $b1x = 80; $b1y = 180;
    $a1x = 250; $a1y = 180;
    $angle1 = 60;
    $c1x = $b1x + 170 * cos(deg2rad($angle1));
    $c1y = $b1y - 170 * sin(deg2rad($angle1));
    
    $svg .= '<line x1="' . $b1x . '" y1="' . $b1y . '" x2="' . $a1x . '" y2="' . $a1y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $b1x . '" y1="' . $b1y . '" x2="' . $c1x . '" y2="' . $c1y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<text x="' . ($b1x + 40) . '" y="' . ($b1y - 15) . '" font-size="20" font-weight="bold" fill="#e74c3c">α</text>';
    $svg .= '<text x="' . ($b1x + 50) . '" y="' . ($b1y - 35) . '" font-size="14" fill="#666">60°</text>';
    
    // Deuxième angle à droite
    $b2x = 270; $b2y = 80;
    $a2x = 100; $a2y = 80;
    $angle2 = 60;
    $c2x = $b2x + 170 * cos(deg2rad(180 - $angle2));
    $c2y = $b2y - 170 * sin(deg2rad(180 - $angle2));
    
    $svg .= '<line x1="' . $b2x . '" y1="' . $b2y . '" x2="' . $a2x . '" y2="' . $a2y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $b2x . '" y1="' . $b2y . '" x2="' . $c2x . '" y2="' . $c2y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<text x="' . ($b2x - 50) . '" y="' . ($b2y - 15) . '" font-size="20" font-weight="bold" fill="#2980b9">β</text>';
    $svg .= '<text x="' . ($b2x - 60) . '" y="' . ($b2y - 35) . '" font-size="14" fill="#666">60°</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_angles_quelconques() {
    $svg = '<svg width="350" height="250" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Deux angles sans relation particulière
    // Premier angle 50°
    $b1x = 80; $b1y = 180;
    $a1x = 250; $a1y = 180;
    $c1x = $b1x + 170 * cos(deg2rad(50));
    $c1y = $b1y - 170 * sin(deg2rad(50));
    
    $svg .= '<line x1="' . $b1x . '" y1="' . $b1y . '" x2="' . $a1x . '" y2="' . $a1y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $b1x . '" y1="' . $b1y . '" x2="' . $c1x . '" y2="' . $c1y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<text x="' . ($b1x + 40) . '" y="' . ($b1y - 15) . '" font-size="20" font-weight="bold" fill="#e74c3c">α</text>';
    
    // Deuxième angle 75°
    $b2x = 270; $b2y = 80;
    $a2x = 100; $a2y = 80;
    $c2x = $b2x + 170 * cos(deg2rad(105));  // 180-75
    $c2y = $b2y - 170 * sin(deg2rad(105));
    
    $svg .= '<line x1="' . $b2x . '" y1="' . $b2y . '" x2="' . $a2x . '" y2="' . $a2y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $b2x . '" y1="' . $b2y . '" x2="' . $c2x . '" y2="' . $c2y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<text x="' . ($b2x - 50) . '" y="' . ($b2y - 15) . '" font-size="20" font-weight="bold" fill="#2980b9">β</text>';
    
    $svg .= '</svg>';
    return $svg;
}

// ============================================
// FONCTIONS DE GÉNÉRATION SVG - ANGLES ADJACENTS
// ============================================

function generer_angles_adjacents_vrais() {
    $svg = '<svg width="350" height="250" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Sommet commun O
    $ox = 80; $oy = 180;
    
    // Trois demi-droites partant de O
    $ax = 300; $ay = 180;  // Horizontale
    $bx = $ox + 200 * cos(deg2rad(70));
    $by = $oy - 200 * sin(deg2rad(70));  // 70°
    $cx = $ox + 200 * cos(deg2rad(130));
    $cy = $oy - 200 * sin(deg2rad(130));  // 130°
    
    // Dessiner les trois côtés
    $svg .= '<line x1="' . $ox . '" y1="' . $oy . '" x2="' . $ax . '" y2="' . $ay . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $ox . '" y1="' . $oy . '" x2="' . $bx . '" y2="' . $by . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $ox . '" y1="' . $oy . '" x2="' . $cx . '" y2="' . $cy . '" stroke="#333" stroke-width="2" />';
    
    // Labels des angles
    $svg .= '<text x="' . ($ox + 60) . '" y="' . ($oy - 15) . '" font-size="20" font-weight="bold" fill="#e74c3c">α</text>';
    $svg .= '<text x="' . ($ox - 40) . '" y="' . ($oy - 30) . '" font-size="20" font-weight="bold" fill="#2980b9">β</text>';
    
    // Point O
    $svg .= '<circle cx="' . $ox . '" cy="' . $oy . '" r="3" fill="#333" />';
    $svg .= '<text x="' . ($ox - 15) . '" y="' . ($oy + 20) . '" font-size="14">O</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_angles_separes() {
    $svg = '<svg width="350" height="250" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Premier angle à gauche
    $o1x = 80; $o1y = 180;
    $a1x = 200; $a1y = 180;
    $b1x = $o1x + 120 * cos(deg2rad(60));
    $b1y = $o1y - 120 * sin(deg2rad(60));
    
    $svg .= '<line x1="' . $o1x . '" y1="' . $o1y . '" x2="' . $a1x . '" y2="' . $a1y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $o1x . '" y1="' . $o1y . '" x2="' . $b1x . '" y2="' . $b1y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<text x="' . ($o1x + 35) . '" y="' . ($o1y - 15) . '" font-size="20" font-weight="bold" fill="#e74c3c">α</text>';
    
    // Deuxième angle à droite (séparé)
    $o2x = 270; $o2y = 80;
    $a2x = 150; $a2y = 80;
    $b2x = $o2x + 120 * cos(deg2rad(120));
    $b2y = $o2y - 120 * sin(deg2rad(120));
    
    $svg .= '<line x1="' . $o2x . '" y1="' . $o2y . '" x2="' . $a2x . '" y2="' . $a2y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $o2x . '" y1="' . $o2y . '" x2="' . $b2x . '" y2="' . $b2y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<text x="' . ($o2x - 45) . '" y="' . ($o2y - 15) . '" font-size="20" font-weight="bold" fill="#2980b9">β</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_angles_alignes_non_adjacents() {
    $svg = '<svg width="400" height="150" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Trois points alignés A, O, B, C
    $ax = 30; $ay = 75;
    $ox = 150; $oy = 75;
    $bx = 270; $by = 75;
    $cx = 370; $cy = 75;
    
    // Ligne
    $svg .= '<line x1="' . $ax . '" y1="' . $ay . '" x2="' . $cx . '" y2="' . $cy . '" stroke="#333" stroke-width="2" />';
    
    // Deux demi-droites perpendiculaires
    $d1x = $ox;
    $d1y = 10;
    $d2x = $bx;
    $d2y = 10;
    
    $svg .= '<line x1="' . $ox . '" y1="' . $oy . '" x2="' . $d1x . '" y2="' . $d1y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $bx . '" y1="' . $by . '" x2="' . $d2x . '" y2="' . $d2y . '" stroke="#333" stroke-width="2" />';
    
    // Labels
    $svg .= '<text x="' . ($ox - 25) . '" y="' . ($oy - 10) . '" font-size="20" font-weight="bold" fill="#e74c3c">α</text>';
    $svg .= '<text x="' . ($bx + 15) . '" y="' . ($by - 10) . '" font-size="20" font-weight="bold" fill="#2980b9">β</text>';
    
    // Points
    $svg .= '<text x="' . $ax . '" y="' . ($ay + 20) . '" text-anchor="middle" font-size="14">A</text>';
    $svg .= '<text x="' . $ox . '" y="' . ($oy + 20) . '" text-anchor="middle" font-size="14">O</text>';
    $svg .= '<text x="' . $bx . '" y="' . ($by + 20) . '" text-anchor="middle" font-size="14">B</text>';
    
    $svg .= '</svg>';
    return $svg;
}

// ============================================
// FONCTIONS DE GÉNÉRATION SVG - ANGLES SUPPLÉMENTAIRES
// ============================================

function generer_deux_angles_droits_codes() {
    $svg = '<svg width="400" height="200" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Premier angle droit à gauche
    $o1x = 100; $o1y = 150;
    $a1x = 230; $a1y = 150;  // Horizontal
    $b1x = 100; $b1y = 30;   // Vertical
    
    $svg .= '<line x1="' . $o1x . '" y1="' . $o1y . '" x2="' . $a1x . '" y2="' . $a1y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $o1x . '" y1="' . $o1y . '" x2="' . $b1x . '" y2="' . $b1y . '" stroke="#333" stroke-width="2" />';
    
    // Carré angle droit
    $taille = 15;
    $svg .= '<rect x="' . $o1x . '" y="' . ($o1y - $taille) . '" width="' . $taille . '" height="' . $taille . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Deuxième angle droit à droite
    $o2x = 300; $o2y = 150;
    $a2x = 170; $a2y = 150;  // Horizontal
    $b2x = 300; $b2y = 30;   // Vertical
    
    $svg .= '<line x1="' . $o2x . '" y1="' . $o2y . '" x2="' . $a2x . '" y2="' . $a2y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $o2x . '" y1="' . $o2y . '" x2="' . $b2x . '" y2="' . $b2y . '" stroke="#333" stroke-width="2" />';
    
    // Carré angle droit (à gauche du sommet pour le second angle)
    $svg .= '<rect x="' . ($o2x - $taille) . '" y="' . ($o2y - $taille) . '" width="' . $taille . '" height="' . $taille . '" fill="none" stroke="#333" stroke-width="2" />';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_deux_angles_mesures($angle1, $angle2) {
    $svg = '<svg width="400" height="200" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Premier angle à gauche
    $o1x = 80; $o1y = 150;
    $a1x = 220; $a1y = 150;
    $b1x = $o1x + 140 * cos(deg2rad($angle1));
    $b1y = $o1y - 140 * sin(deg2rad($angle1));
    
    $svg .= '<line x1="' . $o1x . '" y1="' . $o1y . '" x2="' . $a1x . '" y2="' . $a1y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $o1x . '" y1="' . $o1y . '" x2="' . $b1x . '" y2="' . $b1y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<text x="' . ($o1x + 30) . '" y="' . ($o1y - 15) . '" font-size="20" font-weight="bold" fill="#e74c3c">α</text>';
    $svg .= '<text x="' . ($o1x + 40) . '" y="' . ($o1y - 35) . '" font-size="14" fill="#666">' . $angle1 . '°</text>';
    
    // Deuxième angle à droite
    $o2x = 320; $o2y = 150;
    $a2x = 180; $a2y = 150;
    $b2x = $o2x + 140 * cos(deg2rad(180 - $angle2));
    $b2y = $o2y - 140 * sin(deg2rad(180 - $angle2));
    
    $svg .= '<line x1="' . $o2x . '" y1="' . $o2y . '" x2="' . $a2x . '" y2="' . $a2y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<line x1="' . $o2x . '" y1="' . $o2y . '" x2="' . $b2x . '" y2="' . $b2y . '" stroke="#333" stroke-width="2" />';
    $svg .= '<text x="' . ($o2x - 45) . '" y="' . ($o2y - 15) . '" font-size="20" font-weight="bold" fill="#2980b9">β</text>';
    $svg .= '<text x="' . ($o2x - 55) . '" y="' . ($o2y - 35) . '" font-size="14" fill="#666">' . $angle2 . '°</text>';
    
    $svg .= '</svg>';
    return $svg;
}
?>
