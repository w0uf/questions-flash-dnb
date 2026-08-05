<?php
/**
 * Automatisme : Connaître la somme des angles d'un triangle, calculer le 3ème angle
 * Difficulté : FACILE (range 1.0 - 1.3)
 * Format : QCM 4 propositions (A, B, C, D)
 */

function generer_angles_triangle() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    if (!isset($_SESSION['angles_triangle_pool']) || empty($_SESSION['angles_triangle_pool'])) {
        $_SESSION['angles_triangle_pool'] = [
            // THÉORIE (1 question)
            'theorie_somme',
            
            // CALCULS SIMPLES (6 questions)
            'calcul_50_60',   // 50° + 60° → 70°
            'calcul_40_80',   // 40° + 80° → 60°
            'calcul_30_110',  // 30° + 110° → 40°
            'calcul_75_45',   // 75° + 45° → 60°
            'calcul_25_95',   // 25° + 95° → 60°
            'calcul_55_85',   // 55° + 85° → 40°
            
            // TRIANGLE RECTANGLE (3 questions)
            'rectangle_90_40',  // 90° + 40° → 50°
            'rectangle_90_35',  // 90° + 35° → 55°
            'rectangle_90_60',  // 90° + 60° → 30°
            
            // TRIANGLE ISOCÈLE (3 questions)
            'isocele_50_50',    // 50° + 50° → 80°
            'isocele_70_70',    // 70° + 70° → 40°
            'isocele_45_45',    // 45° + 45° → 90°
            
            // TRIANGLE ÉQUILATÉRAL (2 questions)
            'equilateral_angle',   // Chaque angle = 60°
            'equilateral_calcul',  // Deux angles = 60°, le 3ème = ?
        ];
        shuffle($_SESSION['angles_triangle_pool']);
    }
    
    $type_question = array_shift($_SESSION['angles_triangle_pool']);
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // THÉORIE
        // ============================================
        
        case 'theorie_somme':
            $propositions = [
                '90' => '90°',
                '180' => '180°',
                '270' => '270°',
                '360' => '360°'
            ];
            $qcm = generer_qcm_triangle($propositions, '180');
            $question_html = '<p>Quelle est la somme des angles d\'un triangle ?</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 180°</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // CALCULS SIMPLES
        // ============================================
        
        case 'calcul_50_60':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(50, 60);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ',<br>' . angle_chapeau($t['sommet1']) . ' = 50° et ' . angle_chapeau($t['sommet2']) . ' = 60°. Calculer ' . angle_chapeau($t['sommet3']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 70°</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'calcul_40_80':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(40, 80);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ',<br>' . angle_chapeau($t['sommet1']) . ' = 40° et ' . angle_chapeau($t['sommet2']) . ' = 80°. Calculer ' . angle_chapeau($t['sommet3']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 60°</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'calcul_30_110':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(30, 110);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ',<br>' . angle_chapeau($t['sommet1']) . ' = 30° et ' . angle_chapeau($t['sommet2']) . ' = 110°. Calculer ' . angle_chapeau($t['sommet3']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 40°</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'calcul_75_45':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(75, 45);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ',<br>' . angle_chapeau($t['sommet1']) . ' = 75° et ' . angle_chapeau($t['sommet2']) . ' = 45°. Calculer ' . angle_chapeau($t['sommet3']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 60°</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'calcul_25_95':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(25, 95);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ',<br>' . angle_chapeau($t['sommet1']) . ' = 25° et ' . angle_chapeau($t['sommet2']) . ' = 95°. Calculer ' . angle_chapeau($t['sommet3']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 60°</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'calcul_55_85':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(55, 85);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ',<br>' . angle_chapeau($t['sommet1']) . ' = 55° et ' . angle_chapeau($t['sommet2']) . ' = 85°. Calculer ' . angle_chapeau($t['sommet3']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 40°</strong></p>';
            $difficulte = 1.1;
            break;
            
        // ============================================
        // TRIANGLE RECTANGLE
        // ============================================
        
        case 'rectangle_90_40':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(90, 40);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ' rectangle en ' . $t['sommet1'] . ',<br>' . angle_chapeau($t['sommet2']) . ' = 40°. Calculer ' . angle_chapeau($t['sommet3']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 50°</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'rectangle_90_35':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(90, 35);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ' rectangle en ' . $t['sommet1'] . ',<br>' . angle_chapeau($t['sommet2']) . ' = 35°. Calculer ' . angle_chapeau($t['sommet3']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 55°</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'rectangle_90_60':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(90, 60);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ' rectangle en ' . $t['sommet1'] . ',<br>' . angle_chapeau($t['sommet2']) . ' = 60°. Calculer ' . angle_chapeau($t['sommet3']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 30°</strong></p>';
            $difficulte = 1.1;
            break;
            
        // ============================================
        // TRIANGLE ISOCÈLE
        // ============================================
        
        case 'isocele_50_50':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(50, 50);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ' isocèle en ' . $t['sommet1'] . ',<br>' . angle_chapeau($t['sommet2']) . ' = ' . angle_chapeau($t['sommet3']) . ' = 50°. Calculer ' . angle_chapeau($t['sommet1']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 80°</strong></p>';
            $difficulte = 1.2;
            break;
            
        case 'isocele_70_70':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(70, 70);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ' isocèle en ' . $t['sommet1'] . ',<br>' . angle_chapeau($t['sommet2']) . ' = ' . angle_chapeau($t['sommet3']) . ' = 70°. Calculer ' . angle_chapeau($t['sommet1']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 40°</strong></p>';
            $difficulte = 1.2;
            break;
            
        case 'isocele_45_45':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(45, 45);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ' isocèle en ' . $t['sommet1'] . ',<br>' . angle_chapeau($t['sommet2']) . ' = ' . angle_chapeau($t['sommet3']) . ' = 45°. Calculer ' . angle_chapeau($t['sommet1']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 90°</strong></p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // TRIANGLE ÉQUILATÉRAL
        // ============================================
        
        case 'equilateral_angle':
            $propositions = [
                '60' => '60°',
                '90' => '90°',
                '120' => '120°',
                '45' => '45°'
            ];
            $qcm = generer_qcm_triangle($propositions, '60');
            $question_html = '<p>Dans un triangle équilatéral,<br>chaque angle mesure :</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 60°</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'equilateral_calcul':
            $t = generer_nom_triangle();
            $qcm = generer_qcm_calcul_angle(60, 60);
            $question_html = '<p>Dans le triangle ' . $t['nom'] . ' équilatéral,<br>' . angle_chapeau($t['sommet1']) . ' = ' . angle_chapeau($t['sommet2']) . ' = 60°. Calculer ' . angle_chapeau($t['sommet3']) . '.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '. 60°</strong></p>';
            $difficulte = 1.1;
            break;
    }
    
    return [
        'type' => 'angles_triangle',
        'question' => $question_html,
        'reponse' => $reponse_html,
        'difficulte' => $difficulte
    ];
}

// ============================================
// FONCTION HELPER POUR NOMS DE TRIANGLES ALÉATOIRES
// ============================================

function generer_nom_triangle() {
    // Chaîne de toutes les lettres
    $chaine = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    // Mélanger
    $chaine = str_shuffle($chaine);
    
    // Prendre les 3 premiers caractères
    return [
        'nom' => $chaine[0] . $chaine[1] . $chaine[2],
        'sommet1' => $chaine[0],
        'sommet2' => $chaine[1],
        'sommet3' => $chaine[2]
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
// FONCTION GÉNÉRATION QCM AVEC DISTRACTEURS INTELLIGENTS
// ============================================

function generer_qcm_calcul_angle($angle_a, $angle_b) {
    // Calcul de la bonne réponse
    $bonne_reponse = 180 - $angle_a - $angle_b;
    
    // Génération des distracteurs
    $distracteurs_potentiels = [];
    
    // Distracteur 1 : Addition des angles
    $distracteurs_potentiels[] = $angle_a + $angle_b;
    
    // Distracteur 2 : Oubli du premier angle
    $distracteurs_potentiels[] = 180 - $angle_a;
    
    // Distracteur 3 : Oubli du deuxième angle
    $distracteurs_potentiels[] = 180 - $angle_b;
    
    // Distracteur 4 : Erreur +10°
    $distracteurs_potentiels[] = $bonne_reponse + 10;
    
    // Distracteur 5 : Erreur -10°
    $distracteurs_potentiels[] = $bonne_reponse - 10;
    
    // Distracteur 6 : Erreur +20°
    $distracteurs_potentiels[] = $bonne_reponse + 20;
    
    // Distracteur 7 : Erreur -20°
    $distracteurs_potentiels[] = $bonne_reponse - 20;
    
    // FILTRAGE : Retirer les doublons, la bonne réponse, et les valeurs hors limites
    $distracteurs_potentiels = array_unique($distracteurs_potentiels);
    $distracteurs_valides = array_filter($distracteurs_potentiels, function($d) use ($bonne_reponse) {
        return $d != $bonne_reponse && $d > 0 && $d < 180;
    });
    
    // VÉRIFICATION : Si moins de 3 distracteurs, ajouter des valeurs stratégiques
    if (count($distracteurs_valides) < 3) {
        // Ajouter des multiples de 10 proches
        for ($offset = 30; $offset <= 150 && count($distracteurs_valides) < 5; $offset += 10) {
            if ($offset != $bonne_reponse && !in_array($offset, $distracteurs_valides)) {
                $distracteurs_valides[] = $offset;
            }
        }
    }
    
    // Prendre les 3 premiers distracteurs
    $distracteurs_valides = array_values($distracteurs_valides);
    $distracteurs_finaux = array_slice($distracteurs_valides, 0, 3);
    
    // Construire les 4 propositions
    $propositions = [];
    $propositions[$bonne_reponse] = $bonne_reponse . '°';
    foreach ($distracteurs_finaux as $d) {
        $propositions[$d] = $d . '°';
    }
    
    // VÉRIFICATION FINALE : S'assurer qu'on a bien 4 propositions différentes
    if (count($propositions) != 4) {
        // Sécurité : ajouter des valeurs aléatoires si besoin
        $tentatives = 0;
        while (count($propositions) < 4 && $tentatives < 20) {
            $random = rand(10, 170);
            if (!isset($propositions[$random])) {
                $propositions[$random] = $random . '°';
            }
            $tentatives++;
        }
    }
    
    return generer_qcm_triangle($propositions, $bonne_reponse);
}

// ============================================
// FONCTION QCM STANDARD (FORMAT EN LIGNE)
// ============================================

function generer_qcm_triangle($propositions_data, $bonne_reponse_key) {
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
        $qcm_html .= '<span style="padding-right: 70px;"><strong>' . $lettre . '.</strong> ' . $prop['texte'] . '</span>';
    }
    
    $qcm_html .= '</p>';
    
    return ['html' => $qcm_html, 'bonne_lettre' => $bonne_lettre];
}
?>
