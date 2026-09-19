<?php
/**
 * Automatisme : Sur une droite graduée, lire l'abscisse d'un point
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.5)
 * Format : QCM 4 propositions (A, B, C, D)
 * 
 * Principe des graduations intelligentes :
 * - Subdivisions en 1/4 → permet de lire 1/4, 1/2, 3/4
 * - Subdivisions en 1/6 → permet de lire 1/6, 1/3, 1/2, 2/3, 5/6
 * - Subdivisions en 1/8 → permet de lire 1/8, 1/4, 3/8, 1/2, 5/8, 3/4, 7/8
 * - Subdivisions en 1/10 → permet de lire 1/10, 1/5, 3/10, 2/5, 1/2, 3/5, 7/10, 4/5, 9/10
 * 
 * Le zéro est toujours proche du milieu de la droite (nombres négatifs ET positifs)
 */

require_once(__DIR__ . '/utils.php');

function generer_droite_graduee() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['droite_graduee_pool']) || empty($_SESSION['droite_graduee_pool'])) {
        $_SESSION['droite_graduee_pool'] = [
            // Graduations en 1/4 (3 questions)
            'grad_quarts_1',
            'grad_quarts_2',
            'grad_quarts_3',
            
            // Graduations en 1/6 (3 questions)
            'grad_sixiemes_1',
            'grad_sixiemes_2',
            'grad_sixiemes_3',
            
            // Graduations en 1/8 (3 questions)
            'grad_huitiemes_1',
            'grad_huitiemes_2',
            'grad_huitiemes_3',
            
            // Graduations en 1/10 (3 questions)
            'grad_dixiemes_1',
            'grad_dixiemes_2',
            'grad_dixiemes_3'
        ];
        shuffle($_SESSION['droite_graduee_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['droite_graduee_pool']);
    
    // ========================================
    // GÉNÉRER LA QUESTION SELON LE TYPE
    // ========================================
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // GRADUATIONS EN 1/4
        // ============================================
        
        case 'grad_quarts_1':
        case 'grad_quarts_2':
        case 'grad_quarts_3':
            // Droite de -1 à 2 (ou -2 à 1) avec graduations en 1/4
            $debut = rand(0, 1) == 0 ? -1 : -2;
            $fin = ($debut == -1) ? 2 : 1;
            $subdivision = 4;
            
            // Générer TOUS les points possibles sur cette droite
            $points_possibles = [];
            for ($i = $debut * 4 + 1; $i < $fin * 4; $i++) {
                // Éviter 0 et les entiers (trop faciles)
                if ($i != 0 && $i % 4 != 0) {
                    // Simplifier la fraction
                    $num = $i;
                    $den = 4;
                    if ($i % 2 == 0) {
                        $num = $i / 2;
                        $den = 2;
                    }
                    $points_possibles[] = ['num' => $num, 'den' => $den];
                }
            }
            
            // Choisir le point E
            $point_e = $points_possibles[array_rand($points_possibles)];
            
            // Distracteurs possibles
            $distracteurs_possibles = array_merge($points_possibles, [
                ['num' => 5, 'den' => 4], ['num' => 7, 'den' => 4], ['num' => 9, 'den' => 4],
                ['num' => -5, 'den' => 4], ['num' => -7, 'den' => 4],
                ['num' => 1, 'den' => 3], ['num' => 2, 'den' => 3], 
                ['num' => 1, 'den' => 5], ['num' => 2, 'den' => 5], ['num' => 3, 'den' => 5]
            ]);
            
            $distracteurs = generer_distracteurs($point_e, $distracteurs_possibles, 3);
            
            $svg = generer_svg_droite_fraction($debut, $fin, $subdivision, $point_e['num'] / $point_e['den']);
            
            $qcm = generer_qcm_fractions($point_e, $distracteurs);
            
            $question_html = '<p>Sur cette droite graduée, l\'abscisse du point E est :</p>' . $svg . $qcm['question'];
            $reponse_html = '<p><strong>Réponse ' . $qcm['reponse'] . '</strong></p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // GRADUATIONS EN 1/6
        // ============================================
        
        case 'grad_sixiemes_1':
        case 'grad_sixiemes_2':
        case 'grad_sixiemes_3':
            // Droite de -1 à 2 avec graduations en 1/6
            $debut = -1;
            $fin = 2;
            $subdivision = 6;
            
            // Générer TOUS les points possibles sur cette droite (de -6/6 à 12/6)
            $points_possibles = [];
            for ($i = $debut * 6 + 1; $i < $fin * 6; $i++) {
                // Éviter 0 et les entiers
                if ($i != 0 && $i % 6 != 0) {
                    // Simplifier la fraction
                    $num = $i;
                    $den = 6;
                    if ($i % 3 == 0) {
                        $num = $i / 3;
                        $den = 2;
                    } elseif ($i % 2 == 0) {
                        $num = $i / 2;
                        $den = 3;
                    }
                    $points_possibles[] = ['num' => $num, 'den' => $den];
                }
            }
            
            $point_e = $points_possibles[array_rand($points_possibles)];
            
            $distracteurs_possibles = array_merge($points_possibles, [
                ['num' => 1, 'den' => 4], ['num' => 3, 'den' => 4], 
                ['num' => 1, 'den' => 5], ['num' => 2, 'den' => 5], ['num' => 3, 'den' => 5],
                ['num' => 1, 'den' => 8], ['num' => 3, 'den' => 8], ['num' => 5, 'den' => 8]
            ]);
            
            $distracteurs = generer_distracteurs($point_e, $distracteurs_possibles, 3);
            
            $svg = generer_svg_droite_fraction($debut, $fin, $subdivision, $point_e['num'] / $point_e['den']);
            
            $qcm = generer_qcm_fractions($point_e, $distracteurs);
            
            $question_html = '<p>Sur cette droite graduée, l\'abscisse du point E est :</p>' . $svg . $qcm['question'];
            $reponse_html = '<p><strong>Réponse ' . $qcm['reponse'] . '</strong></p>';
            $difficulte = 1.4;
            break;
            
        // ============================================
        // GRADUATIONS EN 1/8
        // ============================================
        
        case 'grad_huitiemes_1':
        case 'grad_huitiemes_2':
        case 'grad_huitiemes_3':
            // Droite de -1 à 2 avec graduations en 1/8
            $debut = -1;
            $fin = 2;
            $subdivision = 8;
            
            // Générer TOUS les points possibles sur cette droite (de -8/8 à 16/8)
            $points_possibles = [];
            for ($i = $debut * 8 + 1; $i < $fin * 8; $i++) {
                // Éviter 0 et les entiers
                if ($i != 0 && $i % 8 != 0) {
                    // Simplifier la fraction
                    $num = $i;
                    $den = 8;
                    if ($i % 4 == 0) {
                        $num = $i / 4;
                        $den = 2;
                    } elseif ($i % 2 == 0) {
                        $num = $i / 2;
                        $den = 4;
                    }
                    $points_possibles[] = ['num' => $num, 'den' => $den];
                }
            }
            
            $point_e = $points_possibles[array_rand($points_possibles)];
            
            $distracteurs_possibles = array_merge($points_possibles, [
                ['num' => 1, 'den' => 3], ['num' => 2, 'den' => 3], 
                ['num' => 1, 'den' => 6], ['num' => 5, 'den' => 6],
                ['num' => 1, 'den' => 5], ['num' => 2, 'den' => 5], ['num' => 3, 'den' => 5]
            ]);
            
            $distracteurs = generer_distracteurs($point_e, $distracteurs_possibles, 3);
            
            $svg = generer_svg_droite_fraction($debut, $fin, $subdivision, $point_e['num'] / $point_e['den']);
            
            $qcm = generer_qcm_fractions($point_e, $distracteurs);
            
            $question_html = '<p>Sur cette droite graduée, l\'abscisse du point E est :</p>' . $svg . $qcm['question'];
            $reponse_html = '<p><strong>Réponse ' . $qcm['reponse'] . '</strong></p>';
            $difficulte = 1.3;
            break;
            
        // ============================================
        // GRADUATIONS EN 1/10
        // ============================================
        
        case 'grad_dixiemes_1':
        case 'grad_dixiemes_2':
        case 'grad_dixiemes_3':
            // Droite de -1 à 2 avec graduations en 1/10
            $debut = -1;
            $fin = 2;
            $subdivision = 10;
            
            // Points possibles : TOUS les points entre -1 et 2 avec dénominateur 10 (ou simplifiés)
            // De -10/10 à 20/10 soit de -1 à 2
            $points_possibles = [
                // Entre -1 et 0
                ['num' => -9, 'den' => 10],
                ['num' => -4, 'den' => 5],  // -8/10 simplifié
                ['num' => -7, 'den' => 10],
                ['num' => -3, 'den' => 5],  // -6/10 simplifié
                ['num' => -1, 'den' => 2],  // -5/10 simplifié
                ['num' => -2, 'den' => 5],  // -4/10 simplifié
                ['num' => -3, 'den' => 10],
                ['num' => -1, 'den' => 5],  // -2/10 simplifié
                ['num' => -1, 'den' => 10],
                // Entre 0 et 1
                ['num' => 1, 'den' => 10],
                ['num' => 1, 'den' => 5],  // 2/10 simplifié
                ['num' => 3, 'den' => 10],
                ['num' => 2, 'den' => 5],  // 4/10 simplifié
                ['num' => 1, 'den' => 2],  // 5/10 simplifié
                ['num' => 3, 'den' => 5],  // 6/10 simplifié
                ['num' => 7, 'den' => 10],
                ['num' => 4, 'den' => 5],  // 8/10 simplifié
                ['num' => 9, 'den' => 10],
                // Entre 1 et 2
                ['num' => 11, 'den' => 10],
                ['num' => 6, 'den' => 5],  // 12/10 simplifié
                ['num' => 13, 'den' => 10],
                ['num' => 7, 'den' => 5],  // 14/10 simplifié
                ['num' => 3, 'den' => 2],  // 15/10 simplifié
                ['num' => 8, 'den' => 5],  // 16/10 simplifié
                ['num' => 17, 'den' => 10],
                ['num' => 9, 'den' => 5],  // 18/10 simplifié
                ['num' => 19, 'den' => 10]
            ];
            
            $point_e = $points_possibles[array_rand($points_possibles)];
            
            $distracteurs_possibles = array_merge($points_possibles, [
                // Ajouter des distracteurs d'autres dénominateurs
                ['num' => 1, 'den' => 4], ['num' => 3, 'den' => 4], 
                ['num' => 1, 'den' => 3], ['num' => 2, 'den' => 3],
                ['num' => 1, 'den' => 6], ['num' => 5, 'den' => 6],
                ['num' => 1, 'den' => 8], ['num' => 3, 'den' => 8], ['num' => 5, 'den' => 8], ['num' => 7, 'den' => 8]
            ]);
            
            $distracteurs = generer_distracteurs($point_e, $distracteurs_possibles, 3);
            
            $svg = generer_svg_droite_fraction($debut, $fin, $subdivision, $point_e['num'] / $point_e['den']);
            
            $qcm = generer_qcm_fractions($point_e, $distracteurs);
            
            $question_html = '<p>Sur cette droite graduée, l\'abscisse du point E est :</p>' . $svg . $qcm['question'];
            $reponse_html = '<p><strong>Réponse ' . $qcm['reponse'] . '</strong></p>';
            $difficulte = 1.5;
            break;
    }
    
    return [
        'type' => 'droite_graduee',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}

/**
 * Génère des distracteurs DIFFÉRENTS de la bonne réponse
 */
function generer_distracteurs($bonne_reponse, $possibilites, $nb_distracteurs) {
    $distracteurs = [];
    $tentatives = 0;
    $max_tentatives = 100;
    
    while (count($distracteurs) < $nb_distracteurs && $tentatives < $max_tentatives) {
        $candidat = $possibilites[array_rand($possibilites)];
        $tentatives++;
        
        // Vérifier que le candidat est différent de la bonne réponse
        if ($candidat['num'] == $bonne_reponse['num'] && $candidat['den'] == $bonne_reponse['den']) {
            continue;
        }
        
        // Vérifier que le candidat n'est pas déjà dans les distracteurs
        $deja_present = false;
        foreach ($distracteurs as $d) {
            if ($d['num'] == $candidat['num'] && $d['den'] == $candidat['den']) {
                $deja_present = true;
                break;
            }
        }
        
        if (!$deja_present) {
            $distracteurs[] = $candidat;
        }
    }
    
    return $distracteurs;
}

/**
 * Génère un QCM avec 4 propositions en fractions
 */
function generer_qcm_fractions($bonne_reponse, $distracteurs) {
    // Créer tableau de toutes les propositions
    $propositions = array_merge([$bonne_reponse], $distracteurs);
    
    // Mélanger
    shuffle($propositions);
    
    // Trouver la position de la bonne réponse
    $lettres = ['A', 'B', 'C', 'D'];
    $bonne_lettre = '';
    
    $qcm_html = '<p>';
    
    foreach ($propositions as $index => $prop) {
        $lettre = $lettres[$index];
        
        // Vérifier si c'est la bonne réponse
        if ($prop['num'] == $bonne_reponse['num'] && $prop['den'] == $bonne_reponse['den']) {
            $bonne_lettre = $lettre;
        }
        
        // Afficher la proposition (padding augmenté car fractions prennent moins de place visuellement que dans ecritures_multiples)
        $qcm_html .= '<span style="padding-right: 40px;"><strong>' . $lettre . '.</strong> ' . frac_html($prop['num'], $prop['den']) . '</span>';
    }
    
    $qcm_html .= '</p>';
    
    return [
        'question' => $qcm_html,
        'reponse' => $bonne_lettre
    ];
}

/**
 * Génère un SVG de droite graduée avec subdivisions intelligentes
 * 
 * @param int $debut Valeur entière de départ (pour les graduations numérotées)
 * @param int $fin Valeur entière de fin (pour les graduations numérotées)
 * @param int $subdivision Nombre de subdivisions entre deux entiers (4, 6, 8, 10)
 * @param float $point_abscisse Position du point E (en valeur décimale)
 * @return string Code SVG
 */
function generer_svg_droite_fraction($debut, $fin, $subdivision, $point_abscisse) {
    // Dimensions
    $largeur = 700;
    $hauteur = 120;
    $marge = 60;
    
    // Ajouter juste quelques graduations de débordement (2 ou 3 petits traits de chaque côté)
    $pas = 1.0 / $subdivision;
    $nb_graduations_debordement = 3; // 3 graduations de débordement
    $debut_graduations = $debut - ($nb_graduations_debordement * $pas);
    $fin_graduations = $fin + ($nb_graduations_debordement * $pas);
    
    // Calcul de l'échelle
    $plage = $fin_graduations - $debut_graduations;
    $echelle = ($largeur - 2 * $marge) / $plage;
    
    // Position Y de la droite
    $y_droite = $hauteur / 2;
    
    // Fonction pour convertir abscisse en position X
    $abscisse_vers_x = function($abscisse) use ($debut_graduations, $echelle, $marge) {
        return $marge + ($abscisse - $debut_graduations) * $echelle;
    };
    
    $svg = '<svg viewBox="0 0 ' . $largeur . ' ' . $hauteur . '" width="' . $largeur . '" height="' . $hauteur . '" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0; max-width:100%; height:auto;">';
    
    // Ligne principale de la droite (SANS flèches)
    $x_debut = $abscisse_vers_x($debut_graduations);
    $x_fin = $abscisse_vers_x($fin_graduations);
    $svg .= '<line x1="' . $x_debut . '" y1="' . $y_droite . '" x2="' . $x_fin . '" y2="' . $y_droite . '" stroke="#333" stroke-width="2.5" />';
    
    // Graduations : quelques traits de débordement de chaque côté
    for ($val = $debut_graduations; $val <= $fin_graduations + 0.001; $val += $pas) {
        $x = $abscisse_vers_x($val);
        
        // Est-ce une valeur entière ?
        $est_entier = (abs($val - round($val)) < 0.001);
        
        // Graduation (trait vertical)
        $hauteur_trait = $est_entier ? 20 : 12; // Plus grand pour entiers
        $epaisseur = $est_entier ? 2.5 : 1.5;
        
        $svg .= '<line x1="' . $x . '" y1="' . ($y_droite - $hauteur_trait/2) . '" x2="' . $x . '" y2="' . ($y_droite + $hauteur_trait/2) . '" stroke="#333" stroke-width="' . $epaisseur . '" />';
        
        // Afficher le nombre SEULEMENT entre debut et fin (pas sur les graduations de débordement)
        if ($est_entier && $val >= $debut && $val <= $fin) {
            $texte = (int)round($val);
            $svg .= '<text x="' . $x . '" y="' . ($y_droite + 38) . '" text-anchor="middle" font-family="Arial" font-size="16" fill="#333">' . $texte . '</text>';
        }
    }
    
    // Point E
    $x_point = $abscisse_vers_x($point_abscisse);
    
    // Cercle rouge pour le point
    $svg .= '<circle cx="' . $x_point . '" cy="' . $y_droite . '" r="6" fill="#e74c3c" stroke="#c0392b" stroke-width="2.5" />';
    
    // Lettre E au-dessus du point
    $svg .= '<text x="' . $x_point . '" y="' . ($y_droite - 18) . '" text-anchor="middle" font-family="Arial" font-size="18" font-weight="bold" fill="#e74c3c">E</text>';
    
    $svg .= '</svg>';
    
    return $svg;
}
