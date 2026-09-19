<?php
/**
 * Automatisme : Exploiter le codage d'une figure pour identifier des triangles, des quadrilatères particuliers, une médiatrice
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.5)
 * Format : Questions OUI/NON avec réponse enrichie si pertinent
 */

function generer_codage_figures() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    if (!isset($_SESSION['codage_figures_pool']) || empty($_SESSION['codage_figures_pool'])) {
        $_SESSION['codage_figures_pool'] = [
            // TRIANGLES - OUI simples (2 questions)
            'triangle_isocele_simple',
            'triangle_rectangle',
            
            // TRIANGLES - OUI avec codage angles (1 question)
            'triangle_angles_egaux_isocele',  // 2 angles égaux → isocèle ? OUI
            
            // TRIANGLES - OUI avec codage 3 angles (2 questions)
            'triangle_3_angles_isocele',      // 3 angles égaux → isocèle ? OUI (+ équilatéral)
            'triangle_3_angles_equilateral',  // 3 angles égaux → équilatéral ? OUI
            
            // TRIANGLES - NON avec codage 3 angles (1 question)
            'triangle_3_angles_rectangle',    // 3 angles égaux → rectangle ? NON
            
            // TRIANGLES - PIÈGES OUI (2 questions)
            'triangle_isocele_equilateral',  // isocèle ? OUI (+ équilatéral)
            'triangle_rectangle_isocele',    // rectangle ? OUI (+ isocèle)
            
            // TRIANGLES - NON (2 questions)
            'triangle_equilateral_faux',     // isocèle → équilatéral ? NON
            'triangle_rectangle_faux',       // isocèle → rectangle ? NON
            
            // QUADRILATÈRES - OUI simples (2 questions)
            'quad_losange',
            'quad_rectangle',
            
            // QUADRILATÈRES - PIÈGES OUI (2 questions)
            'quad_carre_losange',            // losange ? OUI (+ carré)
            'quad_carre_rectangle',          // rectangle ? OUI (+ carré)
            
            // QUADRILATÈRES - NON (3 questions)
            'quad_2_angles_droits',          // 2 angles droits → rectangle ? NON
            'quad_losange_faux',             // rectangle → losange ? NON
            'quad_rectangle_faux',           // losange → rectangle ? NON
            
            // MÉDIATRICE - OUI (1 question)
            'mediatrice_vraie',
            
            // MÉDIATRICE - NON (2 questions)
            'mediatrice_fausse_perp',
            'mediatrice_fausse_milieu'
        ];
        shuffle($_SESSION['codage_figures_pool']);
    }
    
    $type_question = array_shift($_SESSION['codage_figures_pool']);
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // TRIANGLES
        // ============================================
        
        case 'triangle_isocele_simple':
            // Triangle avec 2 côtés égaux seulement
            $svg = generer_svg_triangle_isocele(false);
            $question_html = '<p>Ce triangle est-il isocèle ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'triangle_isocele_equilateral':
            // Triangle équilatéral (piège : aussi isocèle)
            $svg = generer_svg_triangle_equilateral();
            $question_html = '<p>Ce triangle est-il isocèle ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI.</strong> De plus, il est équilatéral.</p>';
            $difficulte = 1.4;
            break;
            
        case 'triangle_rectangle':
            // Triangle rectangle
            $svg = generer_svg_triangle_rectangle();
            $question_html = '<p>Ce triangle est-il rectangle ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'triangle_angles_egaux_isocele':
            // Triangle avec 2 angles égaux codés → isocèle
            $svg = generer_svg_triangle_angles_egaux();
            $question_html = '<p>Ce triangle est-il isocèle ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'triangle_3_angles_isocele':
            // Triangle équilatéral avec 3 angles égaux codés → isocèle ?
            $svg = generer_svg_triangle_3_angles_egaux();
            $question_html = '<p>Ce triangle est-il isocèle ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI.</strong> De plus, il est équilatéral.</p>';
            $difficulte = 1.4;
            break;
            
        case 'triangle_3_angles_equilateral':
            // Triangle équilatéral avec 3 angles égaux codés → équilatéral ?
            $svg = generer_svg_triangle_3_angles_egaux();
            $question_html = '<p>Ce triangle est-il équilatéral ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.2;
            break;
            
        case 'triangle_3_angles_rectangle':
            // Triangle équilatéral avec 3 angles égaux codés → rectangle ?
            $svg = generer_svg_triangle_3_angles_egaux();
            $question_html = '<p>Ce triangle est-il rectangle ?</p>' . $svg;
            $reponse_html = '<p><strong>NON</strong></p>';
            $difficulte = 1.2;
            break;
            
        case 'triangle_isocele_equilateral':
            // Triangle équilatéral (piège : aussi isocèle)
            $svg = generer_svg_triangle_equilateral();
            $question_html = '<p>Ce triangle est-il isocèle ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI.</strong> De plus, il est équilatéral.</p>';
            $difficulte = 1.4;
            break;
            
        case 'triangle_rectangle_isocele':
            // Triangle rectangle isocèle (piège : rectangle ET isocèle)
            $svg = generer_svg_triangle_rectangle_isocele();
            $question_html = '<p>Ce triangle est-il rectangle ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI.</strong> De plus, il est isocèle.</p>';
            $difficulte = 1.4;
            break;
            
        case 'triangle_equilateral_faux':
            // Triangle isocèle qui n'est PAS équilatéral
            $svg = generer_svg_triangle_isocele(false);
            $question_html = '<p>Ce triangle est-il équilatéral ?</p>' . $svg;
            $reponse_html = '<p><strong>NON</strong></p>';
            $difficulte = 1.2;
            break;
            
        case 'triangle_rectangle_faux':
            // Triangle rectangle qui n'est PAS isocèle
            $svg = generer_svg_triangle_rectangle();
            $question_html = '<p>Ce triangle est-il isocèle ?</p>' . $svg;
            $reponse_html = '<p><strong>NON</strong></p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // QUADRILATÈRES
        // ============================================
        
        case 'quad_losange':
            // Losange (4 côtés égaux)
            $svg = generer_svg_losange();
            $question_html = '<p>Ce quadrilatère est-il un losange ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'quad_rectangle':
            // Rectangle (4 angles droits)
            $svg = generer_svg_rectangle();
            $question_html = '<p>Ce quadrilatère est-il un rectangle ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'quad_carre_losange':
            // Carré (piège : aussi losange)
            $svg = generer_svg_carre();
            $question_html = '<p>Ce quadrilatère est-il un losange ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI.</strong> De plus, c\'est un carré.</p>';
            $difficulte = 1.5;
            break;
            
        case 'quad_carre_rectangle':
            // Carré (piège : aussi rectangle)
            $svg = generer_svg_carre();
            $question_html = '<p>Ce quadrilatère est-il un rectangle ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI.</strong> De plus, c\'est un carré.</p>';
            $difficulte = 1.5;
            break;
            
        case 'quad_2_angles_droits':
            // Quadrilatère avec 2 angles droits seulement (pas rectangle)
            $svg = generer_svg_quad_2_angles_droits();
            $question_html = '<p>Ce quadrilatère est-il un rectangle ?</p>' . $svg;
            $reponse_html = '<p><strong>NON.</strong> Il n\'a que 2 angles droits (il en faut au moins 3).</p>';
            $difficulte = 1.4;
            break;
            
        case 'quad_losange_faux':
            // Rectangle qui n'est PAS un losange
            $svg = generer_svg_rectangle();
            $question_html = '<p>Ce quadrilatère est-il un losange ?</p>' . $svg;
            $reponse_html = '<p><strong>NON.</strong> C\'est un rectangle (les côtés ne sont pas tous égaux).</p>';
            $difficulte = 1.3;
            break;
            
        case 'quad_rectangle_faux':
            // Losange qui n'est PAS un rectangle
            $svg = generer_svg_losange();
            $question_html = '<p>Ce quadrilatère est-il un rectangle ?</p>' . $svg;
            $reponse_html = '<p><strong>NON.</strong> C\'est un losange (les angles ne sont pas droits).</p>';
            $difficulte = 1.3;
            break;
            
        // ============================================
        // MÉDIATRICE
        // ============================================
        
        case 'mediatrice_vraie':
            // Médiatrice correctement codée
            $svg = generer_svg_mediatrice(true, true);
            $question_html = '<p>La droite (d) est-elle la médiatrice de [AB] ?</p>' . $svg;
            $reponse_html = '<p><strong>OUI</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'mediatrice_fausse_perp':
            // Perpendiculaire SANS passer par le milieu
            $svg = generer_svg_mediatrice(true, false);
            $question_html = '<p>La droite (d) est-elle la médiatrice de [AB] ?</p>' . $svg;
            $reponse_html = '<p><strong>NON.</strong> Elle est perpendiculaire mais ne passe pas par le milieu.</p>';
            $difficulte = 1.3;
            break;
            
        case 'mediatrice_fausse_milieu':
            // Passe par le milieu mais PAS perpendiculaire (oblique 89° ou 91°)
            $svg = generer_svg_mediatrice(false, true);
            $question_html = '<p>La droite (d) est-elle la médiatrice de [AB] ?</p>' . $svg;
            $reponse_html = '<p><strong>NON.</strong> Elle passe par le milieu mais n\'est pas perpendiculaire.</p>';
            $difficulte = 1.3;
            break;
    }
    
    return [
        'type' => 'codage_figures',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}

// ============================================
// FONCTIONS DE GÉNÉRATION SVG - TRIANGLES
// ============================================

function generer_svg_triangle_isocele($equilateral = false) {
    $svg = '<svg viewBox="0 0 300 280" width="300" height="280" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    if ($equilateral) {
        // Triangle équilatéral (3 côtés égaux)
        $ax = 150; $ay = 50;
        $bx = 80; $by = 230;
        $cx = 220; $cy = 230;
    } else {
        // Triangle isocèle (2 côtés égaux)
        $ax = 150; $ay = 40;
        $bx = 60; $by = 240;
        $cx = 240; $cy = 240;
    }
    
    // Triangle
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage des côtés égaux
    if ($equilateral) {
        // 3 côtés avec même codage (simple trait)
        $svg .= codage_segment($ax, $ay, $bx, $by, 1);
        $svg .= codage_segment($ax, $ay, $cx, $cy, 1);
        $svg .= codage_segment($bx, $by, $cx, $cy, 1);
    } else {
        // 2 côtés avec même codage
        $svg .= codage_segment($ax, $ay, $bx, $by, 1);
        $svg .= codage_segment($ax, $ay, $cx, $cy, 1);
    }
    
    // Labels
    $svg .= '<text x="' . $ax . '" y="' . ($ay - 10) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 15) . '" y="' . ($by + 15) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 15) . '" y="' . ($cy + 15) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_triangle_equilateral() {
    return generer_svg_triangle_isocele(true);
}

function generer_svg_triangle_rectangle() {
    $svg = '<svg viewBox="0 0 300 280" width="300" height="280" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Triangle rectangle en A
    $ax = 70; $ay = 220;
    $bx = 70; $by = 60;
    $cx = 230; $cy = 220;
    
    // Triangle
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage angle droit en A
    $svg .= '<rect x="' . ($ax) . '" y="' . ($ay - 15) . '" width="15" height="15" fill="none" stroke="#333" stroke-width="2" />';
    
    // Labels
    $svg .= '<text x="' . ($ax - 20) . '" y="' . ($ay + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 20) . '" y="' . ($cy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_triangle_rectangle_isocele() {
    $svg = '<svg viewBox="0 0 300 280" width="300" height="280" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Triangle rectangle isocèle en A (AB = AC)
    $ax = 70; $ay = 220;
    $bx = 70; $by = 80;
    $cx = 210; $cy = 220;
    
    // Triangle
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage angle droit en A
    $svg .= '<rect x="' . ($ax) . '" y="' . ($ay - 15) . '" width="15" height="15" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage côtés égaux AB et AC
    $svg .= codage_segment($ax, $ay, $bx, $by, 1);
    $svg .= codage_segment($ax, $ay, $cx, $cy, 1);
    
    // Labels
    $svg .= '<text x="' . ($ax - 20) . '" y="' . ($ay + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 20) . '" y="' . ($cy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_triangle_angles_egaux() {
    $svg = '<svg viewBox="0 0 300 280" width="300" height="280" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Triangle isocèle avec 2 angles égaux à la base (en B et C)
    $ax = 150; $ay = 40;
    $bx = 60; $by = 240;
    $cx = 240; $cy = 240;
    
    // Triangle
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage angles égaux en B et C avec la fonction générique
    // Angle en B (sommet B, côtés vers A et C)
    $svg .= codage_angle($bx, $by, $ax, $ay, $cx, $cy, 1);
    
    // Angle en C (sommet C, côtés vers B et A)
    $svg .= codage_angle($cx, $cy, $bx, $by, $ax, $ay, 1);
    
    // Labels
    $svg .= '<text x="' . $ax . '" y="' . ($ay - 10) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 20) . '" y="' . ($cy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_triangle_3_angles_egaux() {
    $svg = '<svg viewBox="0 0 300 280" width="300" height="280" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Triangle équilatéral avec 3 angles de 60° codés
    $ax = 150; $ay = 40;
    $bx = 60; $by = 213;
    $cx = 240; $cy = 213;
    
    // Triangle
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage des 3 angles égaux avec la fonction générique
    // Angle en A (sommet A, côtés vers B et C)
    $svg .= codage_angle($ax, $ay, $bx, $by, $cx, $cy, 1);
    
    // Angle en B (sommet B, côtés vers A et C)
    $svg .= codage_angle($bx, $by, $ax, $ay, $cx, $cy, 1);
    
    // Angle en C (sommet C, côtés vers B et A)
    $svg .= codage_angle($cx, $cy, $bx, $by, $ax, $ay, 1);
    
    // Labels
    $svg .= '<text x="' . $ax . '" y="' . ($ay - 10) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 20) . '" y="' . ($cy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    
    $svg .= '</svg>';
    return $svg;
}

// ============================================
// FONCTIONS DE GÉNÉRATION SVG - QUADRILATÈRES
// ============================================

function generer_svg_losange() {
    $svg = '<svg viewBox="0 0 300 280" width="300" height="280" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Losange
    $ax = 150; $ay = 40;
    $bx = 60; $by = 140;
    $cx = 150; $cy = 240;
    $dx = 240; $dy = 140;
    
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . ' ' . $dx . ',' . $dy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage : 4 côtés égaux (même symbole)
    $svg .= codage_segment($ax, $ay, $bx, $by, 1);
    $svg .= codage_segment($bx, $by, $cx, $cy, 1);
    $svg .= codage_segment($cx, $cy, $dx, $dy, 1);
    $svg .= codage_segment($dx, $dy, $ax, $ay, 1);
    
    // Labels
    $svg .= '<text x="' . $ax . '" y="' . ($ay - 10) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . $cx . '" y="' . ($cy + 25) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    $svg .= '<text x="' . ($dx + 20) . '" y="' . ($dy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">D</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_rectangle() {
    $svg = '<svg viewBox="0 0 350 250" width="350" height="250" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Rectangle
    $ax = 50; $ay = 60;
    $bx = 50; $by = 190;
    $cx = 300; $cy = 190;
    $dx = 300; $dy = 60;
    
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . ' ' . $dx . ',' . $dy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage : côtés opposés égaux
    $svg .= codage_segment($ax, $ay, $bx, $by, 1);
    $svg .= codage_segment($cx, $cy, $dx, $dy, 1);
    $svg .= codage_segment($bx, $by, $cx, $cy, 2);
    $svg .= codage_segment($dx, $dy, $ax, $ay, 2);
    
    // Codage : UN angle droit (en A suffit pour indiquer rectangle)
    $svg .= '<rect x="' . $ax . '" y="' . $ay . '" width="15" height="15" fill="none" stroke="#333" stroke-width="2" />';
    
    // Labels
    $svg .= '<text x="' . ($ax - 20) . '" y="' . ($ay + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 20) . '" y="' . ($cy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    $svg .= '<text x="' . ($dx + 20) . '" y="' . ($dy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">D</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_carre() {
    $svg = '<svg viewBox="0 0 300 300" width="300" height="300" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Carré
    $ax = 60; $ay = 60;
    $bx = 60; $by = 240;
    $cx = 240; $cy = 240;
    $dx = 240; $dy = 60;
    
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . ' ' . $dx . ',' . $dy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage : 4 côtés égaux
    $svg .= codage_segment($ax, $ay, $bx, $by, 1);
    $svg .= codage_segment($bx, $by, $cx, $cy, 1);
    $svg .= codage_segment($cx, $cy, $dx, $dy, 1);
    $svg .= codage_segment($dx, $dy, $ax, $ay, 1);
    
    // Codage : 4 angles droits
    $svg .= '<rect x="' . $ax . '" y="' . $ay . '" width="18" height="18" fill="none" stroke="#333" stroke-width="2" />';
    
    // Labels
    $svg .= '<text x="' . ($ax - 20) . '" y="' . ($ay + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 20) . '" y="' . ($cy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    $svg .= '<text x="' . ($dx + 20) . '" y="' . ($dy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">D</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_parallelogramme() {
    $svg = '<svg viewBox="0 0 350 250" width="350" height="250" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Parallélogramme
    $ax = 60; $ay = 60;
    $bx = 40; $by = 190;
    $cx = 270; $cy = 190;
    $dx = 290; $dy = 60;
    
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . ' ' . $dx . ',' . $dy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage : côtés opposés égaux (suffisant pour identifier un parallélogramme)
    $svg .= codage_segment($ax, $ay, $bx, $by, 1);
    $svg .= codage_segment($cx, $cy, $dx, $dy, 1);
    $svg .= codage_segment($bx, $by, $cx, $cy, 2);
    $svg .= codage_segment($dx, $dy, $ax, $ay, 2);
    
    // Labels
    $svg .= '<text x="' . ($ax - 15) . '" y="' . ($ay - 5) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 20) . '" y="' . ($cy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    $svg .= '<text x="' . ($dx + 15) . '" y="' . ($dy - 5) . '" text-anchor="middle" font-size="16" font-weight="bold">D</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_quadrilatere_quelconque() {
    $svg = '<svg viewBox="0 0 350 250" width="350" height="250" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Quadrilatère quelconque (trapèze irrégulier)
    $ax = 70; $ay = 60;
    $bx = 50; $by = 190;
    $cx = 250; $cy = 200;
    $dx = 280; $dy = 70;
    
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . ' ' . $dx . ',' . $dy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Pas de codage (aucun côté égal, aucun angle droit)
    
    // Labels
    $svg .= '<text x="' . ($ax - 15) . '" y="' . ($ay - 5) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 20) . '" y="' . ($cy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    $svg .= '<text x="' . ($dx + 20) . '" y="' . ($dy - 5) . '" text-anchor="middle" font-size="16" font-weight="bold">D</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_quad_2_angles_droits() {
    $svg = '<svg viewBox="0 0 350 250" width="350" height="250" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Quadrilatère qui RESSEMBLE à un rectangle mais n'a que 2 angles droits
    // Forme presque rectangulaire mais les angles C et D ne sont pas exactement droits (88° et 92°)
    $ax = 60; $ay = 70;
    $bx = 60; $by = 190;
    $cx = 290; $cy = 188;  // Légèrement décalé pour angle non droit
    $dx = 290; $dy = 72;   // Légèrement décalé pour angle non droit
    
    $svg .= '<polygon points="' . $ax . ',' . $ay . ' ' . $bx . ',' . $by . ' ' . $cx . ',' . $cy . ' ' . $dx . ',' . $dy . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // Codage : 2 angles droits seulement (en A et B)
    // Pas de codage en C et D → ce ne sont PAS des angles droits
    $svg .= '<rect x="' . $ax . '" y="' . $ay . '" width="15" height="15" fill="none" stroke="#333" stroke-width="2" />';
    $svg .= '<rect x="' . $bx . '" y="' . ($by - 15) . '" width="15" height="15" fill="none" stroke="#333" stroke-width="2" />';
    
    // Labels
    $svg .= '<text x="' . ($ax - 20) . '" y="' . ($ay + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx - 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">B</text>';
    $svg .= '<text x="' . ($cx + 20) . '" y="' . ($cy + 5) . '" text-anchor="middle" font-size="16" font-weight="bold">C</text>';
    $svg .= '<text x="' . ($dx + 20) . '" y="' . ($dy - 5) . '" text-anchor="middle" font-size="16" font-weight="bold">D</text>';
    
    $svg .= '</svg>';
    return $svg;
}

// ============================================
// FONCTIONS DE GÉNÉRATION SVG - MÉDIATRICE
// ============================================

function generer_svg_mediatrice($perpendiculaire, $milieu) {
    $svg = '<svg viewBox="0 0 400 350" width="400" height="350" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Segment [AB] HORIZONTAL pour simplifier
    $ax = 80; $ay = 200;
    $bx = 320; $by = 200;
    
    $svg .= '<line x1="' . $ax . '" y1="' . $ay . '" x2="' . $bx . '" y2="' . $by . '" stroke="#333" stroke-width="3" />';
    
    // Milieu M
    $mx = ($ax + $bx) / 2;  // 200
    $my = ($ay + $by) / 2;  // 200
    
    if ($perpendiculaire && $milieu) {
        // MÉDIATRICE VRAIE : droite VERTICALE passant par M
        $dx1 = $mx; $dy1 = 50;
        $dx2 = $mx; $dy2 = 300;
        $angle_droit_x = $mx;
        $angle_droit_y = $my;
        
    } elseif ($perpendiculaire && !$milieu) {
        // Perpendiculaire mais PAS par le milieu (décalée de 4px - invisible à l'œil)
        $dx1 = $mx + 4; $dy1 = 50;
        $dx2 = $mx + 4; $dy2 = 300;
        $angle_droit_x = $mx + 4;
        $angle_droit_y = $my;
        // On ne code PAS le milieu dans ce cas
        $milieu = false;
        
    } elseif (!$perpendiculaire && $milieu) {
        // Passe par M mais PAS perpendiculaire (oblique PRESQUE verticale, 89° ou 91°)
        // L'œil ne peut pas voir la différence, seul le CODAGE donne la réponse
        // Angle aléatoire : 89° ou 91° (±1° de la verticale)
        $angle_degres = rand(0, 1) == 0 ? 89 : 91;
        $angle_rad = deg2rad($angle_degres);
        
        // Vecteur directeur de la droite (légèrement incliné)
        $longueur = 150;
        $dx_vec = $longueur * cos($angle_rad);
        $dy_vec = -$longueur * sin($angle_rad);  // - car y est inversé en SVG
        
        // Points de la droite centrés sur M
        $dx1 = $mx - $dx_vec; 
        $dy1 = $my - $dy_vec;
        $dx2 = $mx + $dx_vec; 
        $dy2 = $my + $dy_vec;
        
        // Pas de codage angle droit
    }
    
    // Droite (d)
    $svg .= '<line x1="' . $dx1 . '" y1="' . $dy1 . '" x2="' . $dx2 . '" y2="' . $dy2 . '" stroke="#e74c3c" stroke-width="2.5" stroke-dasharray="6,4" />';
    
    // Label droite
    $svg .= '<text x="' . ($dx2 + 15) . '" y="' . ($dy2 - 5) . '" font-size="18" font-weight="bold" fill="#e74c3c">(d)</text>';
    
    if ($perpendiculaire) {
        // Codage angle droit : petit carré dans UN COIN (en haut à droite de l'intersection)
        // Pour éviter superposition avec M qui est en bas
        $taille_carre = 16;
        // [AB] horizontal et (d) vertical : carré en haut à droite de M
        $svg .= '<rect x="' . $angle_droit_x . '" y="' . ($angle_droit_y - $taille_carre) . '" width="' . $taille_carre . '" height="' . $taille_carre . '" fill="none" stroke="#333" stroke-width="2" />';
    }
    
    if ($milieu) {
        // Codage milieu : segments égaux de A à M et de M à B
        $svg .= codage_segment($ax, $ay, $mx, $my, 1);
        $svg .= codage_segment($mx, $my, $bx, $by, 1);
        
        // Point M visible
        $svg .= '<circle cx="' . $mx . '" cy="' . $my . '" r="4" fill="#333" />';
        $svg .= '<text x="' . $mx . '" y="' . ($my + 25) . '" text-anchor="middle" font-size="16" font-weight="bold">M</text>';
    }
    
    // Labels points A et B
    $svg .= '<circle cx="' . $ax . '" cy="' . $ay . '" r="4" fill="#333" />';
    $svg .= '<circle cx="' . $bx . '" cy="' . $by . '" r="4" fill="#333" />';
    $svg .= '<text x="' . ($ax - 20) . '" y="' . ($ay + 5) . '" text-anchor="middle" font-size="18" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx + 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="18" font-weight="bold">B</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_mediatrice_fausse_milieu() {
    $svg = '<svg viewBox="0 0 400 350" width="400" height="350" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Segment [AB] HORIZONTAL
    $ax = 80; $ay = 200;
    $bx = 320; $by = 200;
    
    $svg .= '<line x1="' . $ax . '" y1="' . $ay . '" x2="' . $bx . '" y2="' . $by . '" stroke="#333" stroke-width="3" />';
    
    // Milieu réel
    $mx = ($ax + $bx) / 2;  // 200
    $my = ($ay + $by) / 2;  // 200
    
    // Droite perpendiculaire TRÈS légèrement décalée (4px - invisible à l'œil)
    $decalage = 4;
    $dx1 = $mx + $decalage; $dy1 = 50;
    $dx2 = $mx + $decalage; $dy2 = 300;
    
    // Droite (d)
    $svg .= '<line x1="' . $dx1 . '" y1="' . $dy1 . '" x2="' . $dx2 . '" y2="' . $dy2 . '" stroke="#e74c3c" stroke-width="2.5" stroke-dasharray="6,4" />';
    
    // Label droite
    $svg .= '<text x="' . ($dx2 + 15) . '" y="' . ($dy2 - 5) . '" font-size="18" font-weight="bold" fill="#e74c3c">(d)</text>';
    
    // Codage angle droit (elle EST perpendiculaire)
    $taille_carre = 16;
    $svg .= '<rect x="' . ($dx1) . '" y="' . ($my - $taille_carre) . '" width="' . $taille_carre . '" height="' . $taille_carre . '" fill="none" stroke="#333" stroke-width="2" />';
    
    // PAS de codage du milieu ! (c'est là le piège)
    // Visuellement semble passer par le milieu mais décalage de 4px
    
    // Labels points A et B seulement
    $svg .= '<circle cx="' . $ax . '" cy="' . $ay . '" r="4" fill="#333" />';
    $svg .= '<circle cx="' . $bx . '" cy="' . $by . '" r="4" fill="#333" />';
    $svg .= '<text x="' . ($ax - 20) . '" y="' . ($ay + 5) . '" text-anchor="middle" font-size="18" font-weight="bold">A</text>';
    $svg .= '<text x="' . ($bx + 20) . '" y="' . ($by + 5) . '" text-anchor="middle" font-size="18" font-weight="bold">B</text>';
    
    $svg .= '</svg>';
    return $svg;
}

// ============================================
// FONCTIONS UTILITAIRES DE CODAGE
// ============================================

/**
 * Code un angle avec un arc (pour angles égaux)
 * @param float $sommet_x, $sommet_y - Coordonnées du sommet de l'angle
 * @param float $cote1_x, $cote1_y - Point sur le premier côté de l'angle
 * @param float $cote2_x, $cote2_y - Point sur le deuxième côté de l'angle
 * @param int $type - Type de codage (1 = simple arc, 2 = double arc, 3 = triple arc)
 * @return string - Code SVG de l'arc
 */
function codage_angle($sommet_x, $sommet_y, $cote1_x, $cote1_y, $cote2_x, $cote2_y, $type = 1) {
    // Vecteurs du sommet vers les deux côtés
    $v1x = $cote1_x - $sommet_x;
    $v1y = $cote1_y - $sommet_y;
    $v2x = $cote2_x - $sommet_x;
    $v2y = $cote2_y - $sommet_y;
    
    // Normaliser les vecteurs
    $len1 = sqrt($v1x * $v1x + $v1y * $v1y);
    $len2 = sqrt($v2x * $v2x + $v2y * $v2y);
    $v1x /= $len1;
    $v1y /= $len1;
    $v2x /= $len2;
    $v2y /= $len2;
    
    // Rayon de l'arc de base
    $rayon_base = 25;
    
    $svg = '';
    
    // Dessiner 1, 2 ou 3 arcs selon le type
    for ($i = 0; $i < $type; $i++) {
        $rayon = $rayon_base + ($i * 5);  // Espacer les arcs de 5px
        
        // Point de départ de l'arc (sur le premier côté)
        $start_x = $sommet_x + $v1x * $rayon;
        $start_y = $sommet_y + $v1y * $rayon;
        
        // Point d'arrivée de l'arc (sur le deuxième côté)
        $end_x = $sommet_x + $v2x * $rayon;
        $end_y = $sommet_y + $v2y * $rayon;
        
        // Point de contrôle pour courbe quadratique (sur la bissectrice)
        $bisectrice_x = ($v1x + $v2x) / 2;
        $bisectrice_y = ($v1y + $v2y) / 2;
        $len_bis = sqrt($bisectrice_x * $bisectrice_x + $bisectrice_y * $bisectrice_y);
        if ($len_bis > 0) {
            $bisectrice_x /= $len_bis;
            $bisectrice_y /= $len_bis;
        }
        
        $ctrl_x = $sommet_x + $bisectrice_x * $rayon;
        $ctrl_y = $sommet_y + $bisectrice_y * $rayon;
        
        // Dessiner l'arc avec courbe quadratique
        $svg .= '<path d="M ' . $start_x . ' ' . $start_y . ' Q ' . $ctrl_x . ' ' . $ctrl_y . ' ' . $end_x . ' ' . $end_y . '" fill="none" stroke="#e74c3c" stroke-width="2.5" />';
    }
    
    return $svg;
}

/**
 * Génère le codage d'un segment (traits perpendiculaires)
 */
function codage_segment($x1, $y1, $x2, $y2, $type) {
    // Milieu du segment
    $mx = ($x1 + $x2) / 2;
    $my = ($y1 + $y2) / 2;
    
    // Vecteur perpendiculaire
    $dx = $x2 - $x1;
    $dy = $y2 - $y1;
    $longueur = sqrt($dx * $dx + $dy * $dy);
    $nx = -$dy / $longueur;
    $ny = $dx / $longueur;
    
    $svg = '';
    $espacement = 4;
    $taille = 10;
    
    if ($type == 1) {
        // Un trait
        $svg .= '<line x1="' . ($mx + $nx * $taille) . '" y1="' . ($my + $ny * $taille) . '" x2="' . ($mx - $nx * $taille) . '" y2="' . ($my - $ny * $taille) . '" stroke="#e74c3c" stroke-width="2" />';
    } elseif ($type == 2) {
        // Deux traits
        $svg .= '<line x1="' . ($mx + $nx * $taille - $dx/$longueur * $espacement) . '" y1="' . ($my + $ny * $taille - $dy/$longueur * $espacement) . '" x2="' . ($mx - $nx * $taille - $dx/$longueur * $espacement) . '" y2="' . ($my - $ny * $taille - $dy/$longueur * $espacement) . '" stroke="#e74c3c" stroke-width="2" />';
        $svg .= '<line x1="' . ($mx + $nx * $taille + $dx/$longueur * $espacement) . '" y1="' . ($my + $ny * $taille + $dy/$longueur * $espacement) . '" x2="' . ($mx - $nx * $taille + $dx/$longueur * $espacement) . '" y2="' . ($my - $ny * $taille + $dy/$longueur * $espacement) . '" stroke="#e74c3c" stroke-width="2" />';
    }
    
    return $svg;
}

