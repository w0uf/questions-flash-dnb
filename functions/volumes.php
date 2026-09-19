<?php
/**
 * Automatisme DNB 2026 : Connaître et utiliser les formules du volume d'un cube, d'un pavé droit, d'un prisme droit, d'un cylindre
 * Difficulté : MOYEN (range 2.0 - 3.0)
 * 
 * Types de solides :
 * - Cube : V = c³ (25%)
 * - Pavé droit : V = L × l × h (25%)
 * - Prisme triangulaire : V = Aire_base × h (20%)
 * - Cylindre : V = π × r² × h (20%)
 * - Prisme pentagonal : V = Aire_base × h (10%)
 */

function generer_volumes($famille = '') {
    // Filtre optionnel : permet à une page hôte de ne travailler qu'une famille
    // de solides. Sans argument, le comportement historique (tirage sur tout le
    // catalogue avec anti-doublon) est inchangé — la session DNB n'est pas touchée.
    $par_famille = [
        'droits'  => [1, 2, 3, 4, 5],   // cube, pavé, prisme, cylindre, prisme pentagonal
        'pointus' => [6, 7, 8],         // pyramide, cône, boule
    ];
    if (isset($par_famille[$famille])) {
        $types = $par_famille[$famille];
        $type_solide = $types[array_rand($types)];
        $niveau = rand(1, 2);
        switch ($type_solide) {
            case 1: return generer_volume_cube($niveau);
            case 2: return generer_volume_pave($niveau);
            case 3: return generer_volume_prisme($niveau);
            case 4: return generer_volume_cylindre($niveau);
            case 5: return generer_volume_prisme_pentagonal($niveau);
            case 6: return generer_volume_pyramide($niveau);
            case 7: return generer_volume_cone($niveau);
            case 8: return generer_volume_boule($niveau);
        }
    }

    // Anti-doublon : tracker les combinaisons utilisées
    if (!isset($_SESSION['dnb_volumes_used'])) {
        $_SESSION['dnb_volumes_used'] = [];
    }

    // Définir toutes les combinaisons possibles
    // Format : [type_solide, niveau_difficulte]
    // type_solide : 1=cube, 2=pavé, 3=prisme triangulaire, 4=cylindre, 5=prisme pentagonal,
    //               6=pyramide à base carrée, 7=cône, 8=boule  (6 à 8 ajoutés en août 2026 :
    //               les solides « pointus » et la boule sont au programme de 3e et tombent
    //               régulièrement au DNB, ils manquaient totalement ici)
    // niveau_difficulte : 1=facile (petits nombres entiers), 2=moyen (grands nombres ou décimaux)
    $all_combinations = [];
    for ($type = 1; $type <= 8; $type++) {
        for ($niveau = 1; $niveau <= 2; $niveau++) {
            $all_combinations[] = [$type, $niveau];
        }
    }
    
    // Filtrer les combinaisons déjà utilisées
    $available = array_filter($all_combinations, function($combo) {
        return !in_array($combo, $_SESSION['dnb_volumes_used']);
    });
    
    // Si toutes utilisées, reset
    if (empty($available)) {
        $_SESSION['dnb_volumes_used'] = [];
        $available = $all_combinations;
    }
    
    // Tirer une combinaison
    $available = array_values($available);
    $chosen = $available[array_rand($available)];
    list($type_solide, $niveau) = $chosen;
    
    // Marquer comme utilisée
    $_SESSION['dnb_volumes_used'][] = $chosen;
    
    // Générer la question selon le type
    switch ($type_solide) {
        case 1:
            return generer_volume_cube($niveau);
        case 2:
            return generer_volume_pave($niveau);
        case 3:
            return generer_volume_prisme($niveau);
        case 4:
            return generer_volume_cylindre($niveau);
        case 5:
            return generer_volume_prisme_pentagonal($niveau);
        case 6:
            return generer_volume_pyramide($niveau);
        case 7:
            return generer_volume_cone($niveau);
        case 8:
            return generer_volume_boule($niveau);
    }
}

/**
 * Pyramide à base carrée : V = (c² × h) ÷ 3
 * La hauteur est choisie multiple de 3 pour que le volume tombe juste.
 */
function generer_volume_pyramide($niveau) {
    if ($niveau == 1) {
        $cote = rand(3, 6);
        $hauteur = 3 * rand(2, 4);          // 6, 9 ou 12
        $difficulte_id = 2.3;
    } else {
        $cote = rand(6, 12);
        $hauteur = 3 * rand(3, 6);          // 9 à 18
        $difficulte_id = 2.7;
    }

    $aire_base = $cote * $cote;
    $volume = $aire_base * $hauteur / 3;

    $question = '<div style="text-align: center;">'
              . generer_svg_pyramide_avec_cotes($cote, $hauteur)
              . '<p style="margin-top: 20px;">Cette pyramide a une <strong>base carrée</strong>.<br>'
              . 'Quel est son <strong>volume</strong>, <strong>en cm³</strong> ?</p>'
              . '</div>';

    $reponse = '<p>Aire de la base : ' . $cote . ' × ' . $cote . ' = ' . $aire_base . ' cm²</p>'
             . '<p>V = (aire de la base × hauteur) ÷ 3 = (' . $aire_base . ' × ' . $hauteur . ') ÷ 3 '
             . '= ' . ($aire_base * $hauteur) . ' ÷ 3 = <strong>' . $volume . ' cm³</strong></p>'
             . '<p style="font-size: 0.9em; color: #666;">⚠️ Ne pas oublier le ÷ 3 : une pyramide occupe '
             . 'le tiers du prisme de même base et de même hauteur.</p>';

    return [
        'type' => 'volumes',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Cône de révolution : V = (π × r² × h) ÷ 3, en valeur exacte avec π
 * (même convention que le cylindre : aucun arrondi).
 */
function generer_volume_cone($niveau) {
    if ($niveau == 1) {
        $rayon = rand(2, 5);
        $hauteur = 3 * rand(2, 4);
        $difficulte_id = 2.5;
    } else {
        $rayon = rand(5, 9);
        $hauteur = 3 * rand(3, 5);
        $difficulte_id = 2.9;
    }

    $rayon_carre = $rayon * $rayon;
    $coefficient = $rayon_carre * $hauteur / 3;
    $formule_exacte = ($coefficient == 1) ? 'π' : $coefficient . 'π';

    $question = '<div style="text-align: center;">'
              . generer_svg_cone_avec_cotes($rayon, $hauteur)
              . '<p style="margin-top: 20px;">Quel est le <strong>volume</strong> de ce cône ?</p>'
              . '<p style="font-size: 0.9em; color: #666; margin-top: 5px;">(Donner la valeur exacte avec π)</p>'
              . '</div>';

    $reponse = '<p>Le volume du cône est : <strong>' . $formule_exacte . ' cm³</strong></p>'
             . '<p style="font-size: 0.9em; color: #666;">Calcul : (π × ' . $rayon . '² × ' . $hauteur . ') ÷ 3 '
             . '= (π × ' . $rayon_carre . ' × ' . $hauteur . ') ÷ 3 = ' . $formule_exacte . ' cm³</p>';

    return [
        'type' => 'volumes',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Boule : V = (4 × π × r³) ÷ 3, en valeur exacte avec π.
 * Rayon multiple de 3 pour que 4r³/3 soit entier.
 */
function generer_volume_boule($niveau) {
    $rayon = ($niveau == 1) ? 3 : 6;
    $difficulte_id = ($niveau == 1) ? 2.6 : 3.0;

    $r_cube = $rayon * $rayon * $rayon;
    $coefficient = 4 * $r_cube / 3;

    $question = '<div style="text-align: center;">'
              . generer_svg_boule_avec_cote($rayon)
              . '<p style="margin-top: 20px;">Quel est le <strong>volume</strong> de cette boule ?</p>'
              . '<p style="font-size: 0.9em; color: #666; margin-top: 5px;">(Donner la valeur exacte avec π)</p>'
              . '</div>';

    $reponse = '<p>Le volume de la boule est : <strong>' . $coefficient . 'π cm³</strong></p>'
             . '<p style="font-size: 0.9em; color: #666;">Calcul : (4 × π × ' . $rayon . '³) ÷ 3 '
             . '= (4 × π × ' . $r_cube . ') ÷ 3 = ' . $coefficient . 'π cm³</p>'
             . '<p style="font-size: 0.9em; color: #666;">La formule V = 4πr³ ÷ 3 est donnée au brevet : '
             . 'ce qui compte est de l\'appliquer avec r, et non avec le diamètre.</p>';

    return [
        'type' => 'volumes',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Cube : V = c³
 */
function generer_volume_cube($niveau) {
    if ($niveau == 1) {
        // Facile : petits cubes (2 à 5 cm)
        $arete = rand(2, 5);
        $difficulte_id = 2.0 + ($arete / 10);
    } else {
        // Moyen : cubes plus grands (6 à 10 cm)
        $arete = rand(6, 10);
        $difficulte_id = 2.3 + ($arete / 15);
    }
    
    $volume = $arete * $arete * $arete;
    
    // SVG du cube avec dimension
    $svg = generer_svg_cube_avec_cote($arete);
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>volume</strong> de ce cube ?</p>';
    $question .= '</div>';
    
    // Réponse
    $reponse = '<p>Le volume du cube est : <strong>' . $volume . ' cm³</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Calcul : ' . $arete . '³ = ' . $arete . ' × ' . $arete . ' × ' . $arete . ' = ' . $volume . ' cm³</p>';
    
    return [
        'type' => 'volumes',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Pavé droit : V = L × l × h
 */
function generer_volume_pave($niveau) {
    if ($niveau == 1) {
        // Facile : petites dimensions entières
        $longueur = rand(4, 8);
        $largeur = rand(2, 5);
        $hauteur = rand(3, 6);
        $difficulte_id = 2.1 + (($longueur + $largeur + $hauteur) / 30);
    } else {
        // Moyen : dimensions plus grandes
        $longueur = rand(8, 15);
        $largeur = rand(4, 10);
        $hauteur = rand(5, 12);
        $difficulte_id = 2.4 + (($longueur + $largeur + $hauteur) / 40);
    }
    
    $volume = $longueur * $largeur * $hauteur;
    
    // SVG du pavé avec dimensions
    $svg = generer_svg_pave_avec_cotes($longueur, $largeur, $hauteur);
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>volume</strong> de ce pavé droit ?</p>';
    $question .= '</div>';
    
    // Réponse
    $reponse = '<p>Le volume du pavé droit est : <strong>' . $volume . ' cm³</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Calcul : ' . $longueur . ' × ' . $largeur . ' × ' . $hauteur . ' = ' . $volume . ' cm³</p>';
    
    return [
        'type' => 'volumes',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Prisme droit à base triangulaire : V = Aire_base × h
 */
function generer_volume_prisme($niveau) {
    if ($niveau == 1) {
        // Facile : dimensions simples pour calculs faciles
        // Base triangle : b et h du triangle
        $base_triangle = rand(4, 8);      // base du triangle
        $hauteur_triangle = rand(4, 8);   // hauteur du triangle (perpendiculaire à la base)
        $profondeur = rand(6, 10);        // profondeur du prisme (longueur des arêtes latérales)
        $difficulte_id = 2.3 + (($base_triangle + $hauteur_triangle + $profondeur) / 35);
    } else {
        // Moyen : dimensions plus grandes
        $base_triangle = rand(8, 12);
        $hauteur_triangle = rand(6, 10);
        $profondeur = rand(10, 15);
        $difficulte_id = 2.6 + (($base_triangle + $hauteur_triangle + $profondeur) / 45);
    }
    
    $aire_base = ($base_triangle * $hauteur_triangle) / 2;
    $volume = $aire_base * $profondeur;
    
    // SVG du prisme avec dimensions
    $svg = generer_svg_prisme_avec_cotes($base_triangle, $hauteur_triangle, $profondeur);
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>volume</strong> de ce prisme droit à base triangulaire ?</p>';
    $question .= '</div>';
    
    // Réponse
    // Vérifier si le volume est entier pour éviter les décimales inutiles
    $volume_str = ($volume == floor($volume)) ? (int)$volume : number_format($volume, 1, ',', ' ');
    $aire_base_str = ($aire_base == floor($aire_base)) ? (int)$aire_base : number_format($aire_base, 1, ',', ' ');
    
    $reponse = '<p>Le volume du prisme droit est : <strong>' . $volume_str . ' cm³</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Aire de la base triangulaire : (' . $base_triangle . ' × ' . $hauteur_triangle . ') ÷ 2 = ' . $aire_base_str . ' cm²</p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Volume : ' . $aire_base_str . ' × ' . $profondeur . ' = ' . $volume_str . ' cm³</p>';
    
    return [
        'type' => 'volumes',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Cylindre : V = π × r² × h
 */
function generer_volume_cylindre($niveau) {
    if ($niveau == 1) {
        // Facile : petits rayons entiers
        $rayon = rand(2, 5);
        $hauteur = rand(4, 8);
        $difficulte_id = 2.4 + (($rayon + $hauteur) / 20);
    } else {
        // Moyen : rayons plus grands
        $rayon = rand(5, 10);
        $hauteur = rand(8, 15);
        $difficulte_id = 2.7 + (($rayon + $hauteur) / 25);
    }
    
    $rayon_carre = $rayon * $rayon;
    
    // SVG du cylindre avec dimensions
    $svg = generer_svg_cylindre_avec_cotes($rayon, $hauteur);
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>volume</strong> de ce cylindre ?</p>';
    $question .= '<p style="font-size: 0.9em; color: #666; margin-top: 5px;">(Donner la valeur exacte avec π)</p>';
    $question .= '</div>';
    
    // Réponse exacte avec π
    // Simplifier l'expression
    $coefficient = $rayon_carre * $hauteur;
    $formule_exacte = ($coefficient == 1) ? 'π' : $coefficient . 'π';
    
    $reponse = '<p>Le volume du cylindre est : <strong>' . $formule_exacte . ' cm³</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Calcul : π × ' . $rayon . '² × ' . $hauteur . ' = π × ' . $rayon_carre . ' × ' . $hauteur . ' = ' . $formule_exacte . ' cm³</p>';
    
    return [
        'type' => 'volumes',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

// ============================================
// FONCTIONS SVG POUR LES VOLUMES
// ============================================

function generer_svg_cube_avec_cote($arete) {
    $svg = '<svg viewBox="0 0 380 350" width="380" height="350" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block; max-width:100%; height:auto;">';
    
    // Cube en projection - sans remplissage, juste les arêtes
    // Face avant : carré de (70, 180) à (190, 300)
    // Face arrière : carré de (140, 110) à (260, 230)
    
    // ARÊTES VISIBLES (traits noirs pleins)
    // Face avant (carré)
    $svg .= '<line x1="70" y1="180" x2="190" y2="180" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="190" y1="180" x2="190" y2="300" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="190" y1="300" x2="70" y2="300" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="70" y1="300" x2="70" y2="180" stroke="#000" stroke-width="2.5"/>';
    
    // Face arrière (carré)
    $svg .= '<line x1="140" y1="110" x2="260" y2="110" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="260" y1="110" x2="260" y2="230" stroke="#000" stroke-width="2.5"/>';
    // Arête bas arrière EN POINTILLÉS (cachée)
    $svg .= '<line x1="260" y1="230" x2="140" y2="230" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    // Arête gauche arrière EN POINTILLÉS (cachée)
    $svg .= '<line x1="140" y1="230" x2="140" y2="110" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    
    // Arêtes de profondeur visibles (3 sur 4)
    $svg .= '<line x1="190" y1="180" x2="260" y2="110" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="190" y1="300" x2="260" y2="230" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="70" y1="180" x2="140" y2="110" stroke="#000" stroke-width="2.5"/>';
    
    // ARÊTE DE PROFONDEUR CACHÉE (pointillés) - coin arrière-gauche-bas
    $svg .= '<line x1="70" y1="300" x2="140" y2="230" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    
    // ===== COTATION TECHNIQUE =====
    // Arête avant (en bas)
    $svg .= '<line x1="70" y1="325" x2="190" y2="325" stroke="#000" stroke-width="1.5"/>';
    $svg .= '<polygon points="70,325 76,322 76,328" fill="#000"/>';
    $svg .= '<polygon points="190,325 184,322 184,328" fill="#000"/>';
    // Traits de rappel
    $svg .= '<line x1="70" y1="300" x2="70" y2="330" stroke="#000" stroke-width="1"/>';
    $svg .= '<line x1="190" y1="300" x2="190" y2="330" stroke="#000" stroke-width="1"/>';
    // Texte
    $svg .= '<text x="130" y="345" font-size="17" fill="#000" font-weight="bold" text-anchor="middle">' . $arete . ' cm</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_pave_avec_cotes($longueur, $largeur, $hauteur) {
    $svg = '<svg viewBox="0 0 450 380" width="450" height="380" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block; max-width:100%; height:auto;">';
    
    // Pavé en projection - sans remplissage, juste les arêtes
    // Face avant : rectangle de (100, 150) largeur=160, hauteur=120
    // Face arrière : rectangle de (180, 70) largeur=160, hauteur=120
    
    // ARÊTES VISIBLES (traits noirs pleins)
    // Face avant
    $svg .= '<line x1="100" y1="150" x2="260" y2="150" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="260" y1="150" x2="260" y2="270" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="260" y1="270" x2="100" y2="270" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="100" y1="270" x2="100" y2="150" stroke="#000" stroke-width="2.5"/>';
    
    // Face arrière
    $svg .= '<line x1="180" y1="70" x2="340" y2="70" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="340" y1="70" x2="340" y2="190" stroke="#000" stroke-width="2.5"/>';
    // Arête bas arrière EN POINTILLÉS (cachée)
    $svg .= '<line x1="340" y1="190" x2="180" y2="190" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    // Arête gauche arrière EN POINTILLÉS (cachée)
    $svg .= '<line x1="180" y1="190" x2="180" y2="70" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    
    // Arêtes de profondeur visibles (3 sur 4)
    $svg .= '<line x1="100" y1="150" x2="180" y2="70" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="260" y1="150" x2="340" y2="70" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="260" y1="270" x2="340" y2="190" stroke="#000" stroke-width="2.5"/>';
    
    // ARÊTE DE PROFONDEUR CACHÉE (pointillés) - coin arrière-gauche-bas
    $svg .= '<line x1="100" y1="270" x2="180" y2="190" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    
    // ===== COTATIONS TECHNIQUES =====
    
    // 1. LONGUEUR (base avant, en bas)
    $svg .= '<line x1="100" y1="295" x2="260" y2="295" stroke="#000" stroke-width="1.5"/>';
    $svg .= '<polygon points="100,295 106,292 106,298" fill="#000"/>';
    $svg .= '<polygon points="260,295 254,292 254,298" fill="#000"/>';
    $svg .= '<line x1="100" y1="270" x2="100" y2="300" stroke="#000" stroke-width="1"/>';
    $svg .= '<line x1="260" y1="270" x2="260" y2="300" stroke="#000" stroke-width="1"/>';
    $svg .= '<text x="180" y="315" font-size="17" fill="#000" font-weight="bold" text-anchor="middle">' . $longueur . ' cm</text>';
    
    // 2. LARGEUR (côté droit, décalé)
    // Segment de (270, 275) à (350, 195) : vecteur (80, -80), angle = -45°
    $svg .= '<line x1="270" y1="275" x2="350" y2="195" stroke="#000" stroke-width="1.5"/>';
    // Flèches identiques aux horizontales mais avec rotation de -45°
    $svg .= '<polygon points="270,275 276,272 276,278" transform="rotate(-45 270 275)" fill="#000"/>';
    $svg .= '<polygon points="350,195 344,192 344,198" transform="rotate(-45 350 195)" fill="#000"/>';
    $svg .= '<line x1="260" y1="270" x2="275" y2="278" stroke="#000" stroke-width="1"/>';
    $svg .= '<line x1="340" y1="190" x2="355" y2="198" stroke="#000" stroke-width="1"/>';
    $svg .= '<text x="320" y="245" font-size="17" fill="#000" font-weight="bold">' . $largeur . ' cm</text>';
    
    // 3. HAUTEUR (arête verticale gauche)
    $svg .= '<line x1="80" y1="150" x2="80" y2="270" stroke="#000" stroke-width="1.5"/>';
    $svg .= '<polygon points="80,150 77,156 83,156" fill="#000"/>';
    $svg .= '<polygon points="80,270 77,264 83,264" fill="#000"/>';
    $svg .= '<line x1="100" y1="150" x2="85" y2="150" stroke="#000" stroke-width="1"/>';
    $svg .= '<line x1="100" y1="270" x2="85" y2="270" stroke="#000" stroke-width="1"/>';
    $svg .= '<text x="65" y="215" font-size="17" fill="#000" font-weight="bold" text-anchor="end">' . $hauteur . ' cm</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_prisme_avec_cotes($base, $hauteur_triangle, $profondeur) {
    $svg = '<svg viewBox="0 0 400 350" width="400" height="350" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block; max-width:100%; height:auto;">';
    
    // TRIANGLE AVANT (face avant du prisme) - trait noir simple
    // Base horizontale en bas : de (60, 260) à (200, 260)
    // Sommet du triangle : (130, 160)
    
    // TRIANGLE ARRIÈRE (face arrière du prisme)
    // Décalage de 100px vers le haut-droite pour effet 3D
    // Base : (160, 160) à (300, 160)
    // Sommet : (230, 60)
    
    // ARÊTES VISIBLES (traits noirs) - pas d'ombrage, juste les contours
    // Triangle avant
    $svg .= '<line x1="60" y1="260" x2="200" y2="260" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="200" y1="260" x2="130" y2="160" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="130" y1="160" x2="60" y2="260" stroke="#000" stroke-width="2.5"/>';
    
    // Triangle arrière
    // Arête du bas EN POINTILLÉS (cachée)
    $svg .= '<line x1="160" y1="160" x2="300" y2="160" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    $svg .= '<line x1="300" y1="160" x2="230" y2="60" stroke="#000" stroke-width="2.5"/>';
    // Arête gauche arrière EN POINTILLÉS (cachée)
    $svg .= '<line x1="230" y1="60" x2="160" y2="160" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    
    // Arêtes latérales VISIBLES (profondeur)
    $svg .= '<line x1="200" y1="260" x2="300" y2="160" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="130" y1="160" x2="230" y2="60" stroke="#000" stroke-width="2.5"/>';
    
    // ARÊTE DE PROFONDEUR CACHÉE (pointillés) - coin arrière-gauche
    $svg .= '<line x1="60" y1="260" x2="160" y2="160" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    
    // ===== COTATIONS TECHNIQUES =====
    
    // 1. BASE DU TRIANGLE (en bas) - cotation avec flèches
    // Trait de cote horizontal sous la base
    $svg .= '<line x1="60" y1="285" x2="200" y2="285" stroke="#000" stroke-width="1.5"/>';
    // Flèches aux extrémités
    $svg .= '<polygon points="60,285 66,282 66,288" fill="#000"/>';
    $svg .= '<polygon points="200,285 194,282 194,288" fill="#000"/>';
    // Traits de rappel verticaux
    $svg .= '<line x1="60" y1="260" x2="60" y2="290" stroke="#000" stroke-width="1"/>';
    $svg .= '<line x1="200" y1="260" x2="200" y2="290" stroke="#000" stroke-width="1"/>';
    // Texte de la cote
    $svg .= '<text x="115" y="305" font-size="17" fill="#000" font-weight="bold" text-anchor="middle">' . $base . ' cm</text>';
    
    // 2. HAUTEUR DU TRIANGLE (perpendiculaire à la base) - EN ROUGE
    // Trait rouge vertical de la base au sommet
    $svg .= '<line x1="130" y1="260" x2="130" y2="160" stroke="#d00" stroke-width="2" stroke-dasharray="5,3"/>';
    // Petit point rouge au sommet
    $svg .= '<circle cx="130" cy="160" r="3" fill="#d00"/>';
    // Texte de la hauteur en rouge à gauche du trait
    $svg .= '<text x="100" y="215" font-size="17" fill="#d00" font-weight="bold">' . $hauteur_triangle . ' cm</text>';
    
    // 3. PROFONDEUR DU PRISME (arête latérale droite) - cotation avec flèches
    // Segment de (210, 265) à (310, 165) : vecteur (100, -100), angle = -45°
    $svg .= '<line x1="210" y1="265" x2="310" y2="165" stroke="#000" stroke-width="1.5"/>';
    // Flèches identiques aux horizontales mais avec rotation de -45°
    $svg .= '<polygon points="210,265 216,262 216,268" transform="rotate(-45 210 265)" fill="#000"/>';
    $svg .= '<polygon points="310,165 304,162 304,168" transform="rotate(-45 310 165)" fill="#000"/>';
    // Traits de rappel
    $svg .= '<line x1="200" y1="260" x2="215" y2="268" stroke="#000" stroke-width="1"/>';
    $svg .= '<line x1="300" y1="160" x2="315" y2="168" stroke="#000" stroke-width="1"/>';
    // Texte de la cote
    $svg .= '<text x="270" y="225" font-size="17" fill="#000" font-weight="bold">' . $profondeur . ' cm</text>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_cylindre_avec_cotes($rayon, $hauteur) {
    $svg = '<svg viewBox="0 0 300 280" width="300" height="280" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block; max-width:100%; height:auto;">';
    
    // Corps
    $svg .= '<rect x="100" y="80" width="100" height="140" fill="#e8e8e8" stroke="none"/>';
    
    // Arêtes latérales
    $svg .= '<line x1="100" y1="80" x2="100" y2="220" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="200" y1="80" x2="200" y2="220" stroke="#000" stroke-width="2"/>';
    
    // Base supérieure
    $svg .= '<ellipse cx="150" cy="80" rx="50" ry="15" fill="#d5d5d5" stroke="#000" stroke-width="2"/>';
    
    // Base inférieure : remplissage
    $svg .= '<ellipse cx="150" cy="220" rx="50" ry="15" fill="#b8b8b8" stroke="none"/>';
    
    // Arc arrière (pointillés)
    $svg .= '<path d="M 100 220 A 50 15 0 0 1 200 220" fill="none" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    
    // Arc avant (trait plein)
    $svg .= '<path d="M 100 220 A 50 15 0 0 0 200 220" fill="none" stroke="#000" stroke-width="2"/>';
    
    // Rayon tracé sur la base supérieure
    $svg .= '<line x1="150" y1="80" x2="200" y2="80" stroke="#000" stroke-width="2"/>';
    
    // Annotations
    // Rayon - positionné au-dessus et écarté
    $svg .= '<text x="160" y="65" font-size="15" fill="#000" font-weight="bold">r = ' . $rayon . ' cm</text>';
    // Hauteur - positionné à droite du cylindre
    $svg .= '<text x="210" y="155" font-size="15" fill="#000" font-weight="bold">h = ' . $hauteur . ' cm</text>';
    
    $svg .= '</svg>';
    return $svg;
}

/**
 * Prisme droit à base pentagonale : V = Aire_base × h
 */
function generer_volume_prisme_pentagonal($niveau) {
    if ($niveau == 1) {
        // Facile : aire donnée, hauteur simple
        $aire_base = rand(20, 40);  // cm²
        $hauteur = rand(6, 10);     // cm
        $difficulte_id = 2.5 + (($aire_base + $hauteur) / 80);
    } else {
        // Moyen : aires et hauteurs plus grandes
        $aire_base = rand(40, 80);
        $hauteur = rand(10, 15);
        $difficulte_id = 2.7 + (($aire_base + $hauteur) / 100);
    }
    
    $volume = $aire_base * $hauteur;
    
    // SVG du prisme pentagonal avec aire de base et hauteur
    $svg = generer_svg_prisme_pentagonal_avec_cotes($aire_base, $hauteur);
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>volume</strong> de ce prisme droit à base pentagonale ?</p>';
    $question .= '</div>';
    
    // Réponse
    $reponse = '<p>Le volume du prisme droit est : <strong>' . $volume . ' cm³</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Volume = Aire de la base × hauteur</p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Volume = ' . $aire_base . ' × ' . $hauteur . ' = ' . $volume . ' cm³</p>';
    
    return [
        'type' => 'volumes',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

function generer_svg_prisme_pentagonal_avec_cotes($aire_base, $hauteur) {
    $svg = '<svg viewBox="0 0 450 320" width="450" height="320" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block; max-width:100%; height:auto;">';
    
    // FACE AVANT (pentagone grisé) - décalé à droite pour laisser place à la cotation
    $svg .= '<path d="M 125 165 L 205 155 L 230 210 L 190 250 L 115 233 Z" fill="#e8e8e8" stroke="none"/>';
    
    // PENTAGONE AVANT (5 arêtes pleines)
    $svg .= '<line x1="125" y1="165" x2="205" y2="155" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="205" y1="155" x2="230" y2="210" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="230" y1="210" x2="190" y2="250" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="190" y1="250" x2="115" y2="233" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="115" y1="233" x2="125" y2="165" stroke="#000" stroke-width="2.5"/>';
    
    // VERTICALES (arêtes de profondeur - 3 pleines)
    $svg .= '<line x1="125" y1="165" x2="205" y2="55" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="205" y1="155" x2="285" y2="45" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="230" y1="210" x2="310" y2="100" stroke="#000" stroke-width="2.5"/>';
    
    // Arêtes de profondeur EN POINTILLÉS (2 cachées)
    $svg .= '<line x1="190" y1="250" x2="270" y2="140" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    $svg .= '<line x1="115" y1="233" x2="195" y2="123" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    
    // PENTAGONE ARRIÈRE (2 arêtes pleines visibles)
    $svg .= '<line x1="205" y1="55" x2="285" y2="45" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="285" y1="45" x2="310" y2="100" stroke="#000" stroke-width="2.5"/>';
    
    // Arêtes arrière EN POINTILLÉS (3 cachées)
    $svg .= '<line x1="310" y1="100" x2="270" y2="140" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    $svg .= '<line x1="270" y1="140" x2="195" y2="123" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    $svg .= '<line x1="195" y1="123" x2="205" y2="55" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    
    // ===== ANNOTATIONS =====
    
    // Aire de la base (centrée dans le pentagone)
    $svg .= '<text x="175" y="215" font-size="17" fill="#000" font-weight="bold" text-anchor="middle">' . $aire_base . ' cm²</text>';
    
    // Hauteur du prisme (cotation PARALLÈLE à l'arête latérale gauche)
    // Segment de (95, 173) à (175, 63) : vecteur (80, -110)
    // Angle = atan2(-110, 80) = atan2(-1.375) ≈ -54°
    $svg .= '<line x1="95" y1="173" x2="175" y2="63" stroke="#000" stroke-width="1.5"/>';
    
    // Flèches identiques aux horizontales mais avec rotation de -54°
    $svg .= '<polygon points="95,173 101,170 101,176" transform="rotate(-54 95 173)" fill="#000"/>';
    $svg .= '<polygon points="175,63 169,60 169,66" transform="rotate(-54 175 63)" fill="#000"/>';
    
    // Texte (décalé de 50px vers la gauche)
    $svg .= '<text x="65" y="125" font-size="17" fill="#000" font-weight="bold">' . $hauteur . ' cm</text>';
    
    $svg .= '</svg>';
    return $svg;
}

// ============================================
// FIGURES DES SOLIDES « POINTUS » ET DE LA BOULE
// Ajoutées en août 2026, en viewBox : contrairement aux figures ci-dessus,
// elles se mettent à l'échelle sur téléphone au lieu d'être rognées.
// ============================================

/** Pyramide à base carrée, arêtes cachées en pointillés, cotée c et h. */
function generer_svg_pyramide_avec_cotes($cote, $hauteur) {
    // Base ABCD en perspective cavalière : A avant-gauche, B avant-droit,
    // C arrière-droit, D arrière-gauche (D est le sommet caché).
    $Ax = 80;  $Ay = 300;
    $Bx = 240; $By = 300;
    $Cx = 310; $Cy = 240;
    $Dx = 150; $Dy = 240;
    $Sx = 195; $Sy = 80;    // sommet
    $centre_x = 195; $centre_y = 270;

    $svg = '<svg viewBox="0 0 420 370" width="420" height="370" xmlns="http://www.w3.org/2000/svg" '
         . 'style="margin:15px auto; display:block; max-width:100%; height:auto;">';

    // Arêtes cachées (celles qui partent de D)
    $svg .= '<line x1="' . $Ax . '" y1="' . $Ay . '" x2="' . $Dx . '" y2="' . $Dy . '" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    $svg .= '<line x1="' . $Dx . '" y1="' . $Dy . '" x2="' . $Cx . '" y2="' . $Cy . '" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';
    $svg .= '<line x1="' . $Sx . '" y1="' . $Sy . '" x2="' . $Dx . '" y2="' . $Dy . '" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';

    // Hauteur (pointillés bleus, du sommet au centre de la base)
    $svg .= '<line x1="' . $Sx . '" y1="' . $Sy . '" x2="' . $centre_x . '" y2="' . $centre_y . '" stroke="#2f7ed8" stroke-width="2" stroke-dasharray="6,4"/>';
    // Petit carré d'angle droit à la base de la hauteur
    $svg .= '<polyline points="' . ($centre_x) . ',' . ($centre_y - 14) . ' ' . ($centre_x + 14) . ',' . ($centre_y - 14) . ' ' . ($centre_x + 14) . ',' . $centre_y . '" fill="none" stroke="#2f7ed8" stroke-width="1.5"/>';
    $svg .= '<text x="' . ($Sx + 10) . '" y="' . (($Sy + $centre_y) / 2) . '" font-size="17" fill="#2f7ed8" font-weight="bold">' . $hauteur . ' cm</text>';

    // Arêtes visibles
    $svg .= '<line x1="' . $Ax . '" y1="' . $Ay . '" x2="' . $Bx . '" y2="' . $By . '" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="' . $Bx . '" y1="' . $By . '" x2="' . $Cx . '" y2="' . $Cy . '" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="' . $Sx . '" y1="' . $Sy . '" x2="' . $Ax . '" y2="' . $Ay . '" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="' . $Sx . '" y1="' . $Sy . '" x2="' . $Bx . '" y2="' . $By . '" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="' . $Sx . '" y1="' . $Sy . '" x2="' . $Cx . '" y2="' . $Cy . '" stroke="#000" stroke-width="2.5"/>';

    // Cotation de l'arête de base avant
    $svg .= '<line x1="' . $Ax . '" y1="330" x2="' . $Bx . '" y2="330" stroke="#000" stroke-width="1.5"/>';
    $svg .= '<polygon points="' . $Ax . ',330 ' . ($Ax + 6) . ',327 ' . ($Ax + 6) . ',333" fill="#000"/>';
    $svg .= '<polygon points="' . $Bx . ',330 ' . ($Bx - 6) . ',327 ' . ($Bx - 6) . ',333" fill="#000"/>';
    $svg .= '<line x1="' . $Ax . '" y1="' . $Ay . '" x2="' . $Ax . '" y2="335" stroke="#000" stroke-width="1"/>';
    $svg .= '<line x1="' . $Bx . '" y1="' . $By . '" x2="' . $Bx . '" y2="335" stroke="#000" stroke-width="1"/>';
    $svg .= '<text x="' . (($Ax + $Bx) / 2) . '" y="352" font-size="17" fill="#000" font-weight="bold" text-anchor="middle">' . $cote . ' cm</text>';

    $svg .= '</svg>';
    return $svg;
}

/** Cône de révolution : base en ellipse (arrière en pointillés), coté r et h. */
function generer_svg_cone_avec_cotes($rayon, $hauteur) {
    $cx = 195; $cy = 290;       // centre de la base
    $rx = 95;  $ry = 30;        // demi-axes de l'ellipse
    $sx = 195; $sy = 75;        // sommet

    $svg = '<svg viewBox="0 0 420 370" width="420" height="370" xmlns="http://www.w3.org/2000/svg" '
         . 'style="margin:15px auto; display:block; max-width:100%; height:auto;">';

    // Demi-ellipse arrière (cachée)
    $svg .= '<path d="M ' . ($cx - $rx) . ' ' . $cy . ' A ' . $rx . ' ' . $ry . ' 0 0 1 ' . ($cx + $rx) . ' ' . $cy
          . '" fill="none" stroke="#666" stroke-width="2" stroke-dasharray="5,5"/>';

    // Hauteur
    $svg .= '<line x1="' . $sx . '" y1="' . $sy . '" x2="' . $cx . '" y2="' . $cy . '" stroke="#2f7ed8" stroke-width="2" stroke-dasharray="6,4"/>';
    $svg .= '<polyline points="' . $cx . ',' . ($cy - 14) . ' ' . ($cx + 14) . ',' . ($cy - 14) . ' ' . ($cx + 14) . ',' . $cy . '" fill="none" stroke="#2f7ed8" stroke-width="1.5"/>';
    $svg .= '<text x="' . ($sx + 10) . '" y="' . (($sy + $cy) / 2) . '" font-size="17" fill="#2f7ed8" font-weight="bold">' . $hauteur . ' cm</text>';

    // Rayon
    $svg .= '<line x1="' . $cx . '" y1="' . $cy . '" x2="' . ($cx + $rx) . '" y2="' . $cy . '" stroke="#c0392b" stroke-width="2"/>';
    $svg .= '<polygon points="' . ($cx + $rx) . ',' . $cy . ' ' . ($cx + $rx - 8) . ',' . ($cy - 4) . ' ' . ($cx + $rx - 8) . ',' . ($cy + 4) . '" fill="#c0392b"/>';
    $svg .= '<text x="' . ($cx + $rx / 2) . '" y="' . ($cy + 22) . '" font-size="17" fill="#c0392b" font-weight="bold" text-anchor="middle">' . $rayon . ' cm</text>';

    // Demi-ellipse avant (visible) + génératrices
    $svg .= '<path d="M ' . ($cx - $rx) . ' ' . $cy . ' A ' . $rx . ' ' . $ry . ' 0 0 0 ' . ($cx + $rx) . ' ' . $cy
          . '" fill="none" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="' . $sx . '" y1="' . $sy . '" x2="' . ($cx - $rx) . '" y2="' . $cy . '" stroke="#000" stroke-width="2.5"/>';
    $svg .= '<line x1="' . $sx . '" y1="' . $sy . '" x2="' . ($cx + $rx) . '" y2="' . $cy . '" stroke="#000" stroke-width="2.5"/>';

    $svg .= '</svg>';
    return $svg;
}

/** Boule : cercle + équateur en perspective, rayon coté. */
function generer_svg_boule_avec_cote($rayon) {
    $cx = 195; $cy = 185; $r = 110;

    $svg = '<svg viewBox="0 0 420 370" width="420" height="370" xmlns="http://www.w3.org/2000/svg" '
         . 'style="margin:15px auto; display:block; max-width:100%; height:auto;">';

    $svg .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" fill="none" stroke="#000" stroke-width="2.5"/>';
    // Équateur : moitié arrière en pointillés, moitié avant pleine
    $svg .= '<path d="M ' . ($cx - $r) . ' ' . $cy . ' A ' . $r . ' 32 0 0 1 ' . ($cx + $r) . ' ' . $cy
          . '" fill="none" stroke="#666" stroke-width="1.8" stroke-dasharray="5,5"/>';
    $svg .= '<path d="M ' . ($cx - $r) . ' ' . $cy . ' A ' . $r . ' 32 0 0 0 ' . ($cx + $r) . ' ' . $cy
          . '" fill="none" stroke="#666" stroke-width="1.8"/>';

    // Rayon coté, du centre vers la droite
    $svg .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="3.5" fill="#c0392b"/>';
    $svg .= '<line x1="' . $cx . '" y1="' . $cy . '" x2="' . ($cx + $r) . '" y2="' . $cy . '" stroke="#c0392b" stroke-width="2"/>';
    $svg .= '<polygon points="' . ($cx + $r) . ',' . $cy . ' ' . ($cx + $r - 8) . ',' . ($cy - 4) . ' ' . ($cx + $r - 8) . ',' . ($cy + 4) . '" fill="#c0392b"/>';
    $svg .= '<text x="' . ($cx + $r / 2) . '" y="' . ($cy - 10) . '" font-size="17" fill="#c0392b" font-weight="bold" text-anchor="middle">' . $rayon . ' cm</text>';

    $svg .= '</svg>';
    return $svg;
}

?>
