<?php
/**
 * Automatisme : Dans le plan muni d'un repère orthogonal, lire les coordonnées d'un point, placer un point de coordonnées données
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.5)
 * Format : QCM 4 propositions (A, B, C, D) pour lire, Réponse directe pour placer
 */

function generer_repere_orthogonal() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['repere_orthogonal_pool']) || empty($_SESSION['repere_orthogonal_pool'])) {
        $_SESSION['repere_orthogonal_pool'] = [
            // LIRE coordonnées : entières positives (4 questions)
            'lire_entiers_positifs_1',
            'lire_entiers_positifs_2',
            'lire_entiers_positifs_3',
            'lire_entiers_positifs_4',
            
            // LIRE coordonnées : avec nombres négatifs (4 questions)
            'lire_avec_negatifs_1',
            'lire_avec_negatifs_2',
            'lire_avec_negatifs_3',
            'lire_avec_negatifs_4',
            
            // LIRE coordonnées : avec demi-entiers (4 questions)
            'lire_demi_entiers_1',
            'lire_demi_entiers_2',
            'lire_demi_entiers_3',
            'lire_demi_entiers_4'
        ];
        shuffle($_SESSION['repere_orthogonal_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['repere_orthogonal_pool']);
    
    // ========================================
    // GÉNÉRER LA QUESTION SELON LE TYPE
    // ========================================
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // LIRE : Entiers positifs
        // ============================================
        
        case 'lire_entiers_positifs_1':
        case 'lire_entiers_positifs_2':
        case 'lire_entiers_positifs_3':
        case 'lire_entiers_positifs_4':
            // Repère de -1 à 6 en x, -1 à 6 en y
            $x_min = -1;
            $x_max = 6;
            $y_min = -1;
            $y_max = 6;
            
            // Point avec coordonnées entières positives (éviter 0,0 et les axes)
            $x_point = rand(1, 5);
            $y_point = rand(1, 5);
            
            // Label aléatoire parmi M,N,P,R,S,T
            $labels_possibles = ['M', 'N', 'P', 'R', 'S', 'T'];
            $label = $labels_possibles[array_rand($labels_possibles)];
            
            // Générer distracteurs intelligents
            $distracteurs = generer_distracteurs_coordonnees($x_point, $y_point, [
                'inverser' => true,      // (y, x) au lieu de (x, y)
                'oppose_x' => true,      // (-x, y) - opposé de l'abscisse
                'oppose_y' => true,      // (x, -y) - opposé de l'ordonnée
                'decaler' => true        // (x±1, y±1)
            ]);
            
            $svg = generer_svg_repere($x_min, $x_max, $y_min, $y_max, $x_point, $y_point, $label);
            
            $qcm = generer_qcm_coordonnees($x_point, $y_point, $distracteurs);
            
            $question_html = '<p>Quelles sont les coordonnées du point ' . $label . ' dans ce repère orthogonal ?</p>' . $svg . $qcm['question'];
            $reponse_html = '<p><strong>Réponse ' . $qcm['reponse'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // LIRE : Avec nombres négatifs
        // ============================================
        
        case 'lire_avec_negatifs_1':
        case 'lire_avec_negatifs_2':
        case 'lire_avec_negatifs_3':
        case 'lire_avec_negatifs_4':
            // Repère de -4 à 4 en x et y
            $x_min = -4;
            $x_max = 4;
            $y_min = -4;
            $y_max = 4;
            
            // Point avec au moins une coordonnée négative
            $quadrant = rand(1, 3); // 1=(-,+), 2=(-,-), 3=(+,-)
            
            if ($quadrant == 1) {
                $x_point = rand(-3, -1);
                $y_point = rand(1, 3);
            } elseif ($quadrant == 2) {
                $x_point = rand(-3, -1);
                $y_point = rand(-3, -1);
            } else {
                $x_point = rand(1, 3);
                $y_point = rand(-3, -1);
            }
            
            // Label aléatoire
            $labels_possibles = ['M', 'N', 'P', 'R', 'S', 'T'];
            $label = $labels_possibles[array_rand($labels_possibles)];
            
            // Distracteurs intelligents
            $distracteurs = generer_distracteurs_coordonnees($x_point, $y_point, [
                'inverser' => true,
                'oppose_x' => true,
                'oppose_y' => true,
                'decaler' => true        // Réactivé pour garantir assez de distracteurs
            ]);
            
            $svg = generer_svg_repere($x_min, $x_max, $y_min, $y_max, $x_point, $y_point, $label);
            
            $qcm = generer_qcm_coordonnees($x_point, $y_point, $distracteurs);
            
            $question_html = '<p>Quelles sont les coordonnées du point ' . $label . ' dans ce repère orthogonal ?</p>' . $svg . $qcm['question'];
            $reponse_html = '<p><strong>Réponse ' . $qcm['reponse'] . '</strong></p>';
            $difficulte = 1.3;
            break;
            
        // ============================================
        // LIRE : Avec demi-entiers (0.5, 1.5, 2.5...)
        // ============================================
        
        case 'lire_demi_entiers_1':
        case 'lire_demi_entiers_2':
        case 'lire_demi_entiers_3':
        case 'lire_demi_entiers_4':
            // Repère de -1 à 5 en x et y
            $x_min = -1;
            $x_max = 5;
            $y_min = -1;
            $y_max = 5;
            
            // Au moins une coordonnée demi-entière
            $x_point = rand(1, 4) + 0.5;
            $y_point = rand(0, 1) == 0 ? rand(1, 4) : (rand(1, 4) + 0.5);
            
            // Label aléatoire
            $labels_possibles = ['M', 'N', 'P', 'R', 'S', 'T'];
            $label = $labels_possibles[array_rand($labels_possibles)];
            
            // Distracteurs intelligents
            $distracteurs = generer_distracteurs_coordonnees($x_point, $y_point, [
                'inverser' => true,
                'arrondir' => true,      // Arrondir à l'entier proche
                'decaler_demi' => true   // ±0.5
            ]);
            
            $svg = generer_svg_repere($x_min, $x_max, $y_min, $y_max, $x_point, $y_point, $label);
            
            $qcm = generer_qcm_coordonnees($x_point, $y_point, $distracteurs);
            
            $question_html = '<p>Quelles sont les coordonnées du point ' . $label . ' dans ce repère orthogonal ?</p>' . $svg . $qcm['question'];
            $reponse_html = '<p><strong>Réponse ' . $qcm['reponse'] . '</strong></p>';
            $difficulte = 1.4;
            break;
            
        // ============================================
        // PLACER : Description textuelle
        // ============================================
        
    }
    
    return [
        'type' => 'repere_orthogonal',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}

/**
 * Génère des distracteurs intelligents pour les coordonnées
 */
function generer_distracteurs_coordonnees($x, $y, $strategies) {
    $distracteurs = [];
    
    // Stratégie 1 : Inverser x et y
    if (!empty($strategies['inverser']) && $x != $y) {
        $distracteurs[] = ['x' => $y, 'y' => $x];
    }
    
    // Stratégie 2 : Opposé de x (changer le signe de x)
    if (!empty($strategies['oppose_x'])) {
        $distracteurs[] = ['x' => -$x, 'y' => $y];
    }
    
    // Stratégie 3 : Opposé de y (changer le signe de y)
    if (!empty($strategies['oppose_y'])) {
        $distracteurs[] = ['x' => $x, 'y' => -$y];
    }
    
    // Stratégie 4 : Décaler de ±1
    if (!empty($strategies['decaler'])) {
        $distracteurs[] = ['x' => $x + 1, 'y' => $y];
        $distracteurs[] = ['x' => $x, 'y' => $y + 1];
        $distracteurs[] = ['x' => $x - 1, 'y' => $y];
    }
    
    // Stratégie 5 : Arrondir (pour demi-entiers)
    if (!empty($strategies['arrondir'])) {
        $distracteurs[] = ['x' => round($x), 'y' => $y];
        $distracteurs[] = ['x' => $x, 'y' => round($y)];
    }
    
    // Stratégie 6 : Décaler de ±0.5 (pour demi-entiers)
    if (!empty($strategies['decaler_demi'])) {
        $distracteurs[] = ['x' => $x + 0.5, 'y' => $y];
        $distracteurs[] = ['x' => $x, 'y' => $y + 0.5];
    }
    
    // Ne garder que les distracteurs différents de la bonne réponse
    $distracteurs_valides = [];
    foreach ($distracteurs as $d) {
        if ($d['x'] != $x || $d['y'] != $y) {
            $distracteurs_valides[] = $d;
        }
    }
    
    // Sélectionner 3 distracteurs uniques
    $distracteurs_finaux = [];
    $tentatives = 0;
    while (count($distracteurs_finaux) < 3 && $tentatives < 50 && count($distracteurs_valides) > 0) {
        $candidat = $distracteurs_valides[array_rand($distracteurs_valides)];
        
        // Vérifier que ce distracteur n'est pas déjà présent
        $deja_present = false;
        foreach ($distracteurs_finaux as $d) {
            if ($d['x'] == $candidat['x'] && $d['y'] == $candidat['y']) {
                $deja_present = true;
                break;
            }
        }
        
        if (!$deja_present) {
            $distracteurs_finaux[] = $candidat;
        }
        
        $tentatives++;
    }
    
    // SÉCURITÉ : Si on n'a pas 3 distracteurs, en générer de nouveaux
    $tentatives_secours = 0;
    while (count($distracteurs_finaux) < 3 && $tentatives_secours < 100) {
        // Générer un distracteur aléatoire dans une plage raisonnable
        $decalage_possible = [-3, -2, -1, 1, 2, 3];
        $nouveau_x = $x + $decalage_possible[array_rand($decalage_possible)];
        $nouveau_y = $y + $decalage_possible[array_rand($decalage_possible)];
        
        // Vérifier qu'il est différent de la bonne réponse
        if ($nouveau_x == $x && $nouveau_y == $y) {
            $tentatives_secours++;
            continue;
        }
        
        // Vérifier qu'il n'est pas déjà présent dans distracteurs_finaux
        $deja_dans_finaux = false;
        foreach ($distracteurs_finaux as $d) {
            if ($d['x'] == $nouveau_x && $d['y'] == $nouveau_y) {
                $deja_dans_finaux = true;
                break;
            }
        }
        
        if ($deja_dans_finaux) {
            $tentatives_secours++;
            continue;
        }
        
        // Vérifier qu'il n'est pas déjà présent dans distracteurs_valides (les autres candidats)
        $deja_dans_valides = false;
        foreach ($distracteurs_valides as $d) {
            if ($d['x'] == $nouveau_x && $d['y'] == $nouveau_y) {
                $deja_dans_valides = true;
                break;
            }
        }
        
        if ($deja_dans_valides) {
            $tentatives_secours++;
            continue;
        }
        
        // Si toutes les vérifications passent, ajouter ce distracteur
        $distracteurs_finaux[] = ['x' => $nouveau_x, 'y' => $nouveau_y];
        $tentatives_secours++;
    }
    
    return $distracteurs_finaux;
}

/**
 * Génère un QCM avec 4 propositions de coordonnées
 */
function generer_qcm_coordonnees($x_correct, $y_correct, $distracteurs) {
    // Créer tableau de toutes les propositions
    $propositions = array_merge([['x' => $x_correct, 'y' => $y_correct]], $distracteurs);
    
    // Mélanger
    shuffle($propositions);
    
    // Trouver la position de la bonne réponse
    $lettres = ['A', 'B', 'C', 'D'];
    $bonne_lettre = '';
    
    $qcm_html = '<p>';
    
    foreach ($propositions as $index => $prop) {
        $lettre = $lettres[$index];
        
        // Vérifier si c'est la bonne réponse
        if ($prop['x'] == $x_correct && $prop['y'] == $y_correct) {
            $bonne_lettre = $lettre;
        }
        
        // Affichage avec virgule française
        $x_affichage = str_replace('.', ',', $prop['x']);
        $y_affichage = str_replace('.', ',', $prop['y']);
        
        // Format (x ; y)
        $qcm_html .= '<span style="padding-right: 20px;"><strong>' . $lettre . '.</strong> (' . $x_affichage . ' ; ' . $y_affichage . ')</span>';
    }
    
    $qcm_html .= '</p>';
    
    return [
        'question' => $qcm_html,
        'reponse' => $bonne_lettre
    ];
}

/**
 * Génère un SVG de repère orthogonal
 * 
 * @param int $x_min Valeur minimale de x
 * @param int $x_max Valeur maximale de x
 * @param int $y_min Valeur minimale de y
 * @param int $y_max Valeur maximale de y
 * @param float|null $x_point Abscisse du point (null si pas de point)
 * @param float|null $y_point Ordonnée du point (null si pas de point)
 * @param string|null $label Lettre du point (A, B, C...)
 * @return string Code SVG
 */
function generer_svg_repere($x_min, $x_max, $y_min, $y_max, $x_point = null, $y_point = null, $label = null) {
    // Dimensions
    $largeur = 550;
    $hauteur = 550;
    $marge = 60;
    
    // Extension des graduations (3 graduations de débordement)
    $nb_graduations_debordement = 3;
    $pas_grad = 0.5; // Demi-unité
    
    $x_min_grad = $x_min - ($nb_graduations_debordement * $pas_grad);
    $x_max_grad = $x_max + ($nb_graduations_debordement * $pas_grad);
    $y_min_grad = $y_min - ($nb_graduations_debordement * $pas_grad);
    $y_max_grad = $y_max + ($nb_graduations_debordement * $pas_grad);
    
    // Calcul de l'échelle
    $plage_x = $x_max_grad - $x_min_grad;
    $plage_y = $y_max_grad - $y_min_grad;
    $echelle_x = ($largeur - 2 * $marge) / $plage_x;
    $echelle_y = ($hauteur - 2 * $marge) / $plage_y;
    
    // Fonctions de conversion
    $conv_x = function($x) use ($x_min_grad, $echelle_x, $marge) {
        return $marge + ($x - $x_min_grad) * $echelle_x;
    };
    
    $conv_y = function($y) use ($y_min_grad, $echelle_y, $marge, $hauteur) {
        // Y inversé en SVG (haut = positif en maths, bas = positif en SVG)
        return $hauteur - $marge - ($y - $y_min_grad) * $echelle_y;
    };
    
    $svg = '<svg width="' . $largeur . '" height="' . $hauteur . '" xmlns="http://www.w3.org/2000/svg" style="margin: 15px 0;">';
    
    // Position de l'origine (0, 0)
    $x_origine = $conv_x(0);
    $y_origine = $conv_y(0);
    
    // ========================================
    // GRILLE EN POINTILLÉS (lignes verticales et horizontales)
    // ========================================
    
    // Lignes verticales de la grille (à chaque unité entière, jusqu'aux bords)
    for ($x = ceil($x_min_grad); $x <= floor($x_max_grad); $x += 1) {
        if ($x == 0) continue; // Pas de grille sur l'axe Y
        $pos_x = $conv_x($x);
        $svg .= '<line x1="' . $pos_x . '" y1="' . $conv_y($y_min_grad) . '" x2="' . $pos_x . '" y2="' . $conv_y($y_max_grad) . '" stroke="#bbb" stroke-width="1.2" stroke-dasharray="4,4" />';
    }
    
    // Lignes horizontales de la grille (à chaque unité entière, jusqu'aux bords)
    for ($y = ceil($y_min_grad); $y <= floor($y_max_grad); $y += 1) {
        if ($y == 0) continue; // Pas de grille sur l'axe X
        $pos_y = $conv_y($y);
        $svg .= '<line x1="' . $conv_x($x_min_grad) . '" y1="' . $pos_y . '" x2="' . $conv_x($x_max_grad) . '" y2="' . $pos_y . '" stroke="#bbb" stroke-width="1.2" stroke-dasharray="4,4" />';
    }
    
    // ========================================
    // AXES PRINCIPAUX (sans flèches)
    // ========================================
    
    // Axe des abscisses (horizontal)
    $x_debut = $conv_x($x_min_grad);
    $x_fin = $conv_x($x_max_grad);
    $svg .= '<line x1="' . $x_debut . '" y1="' . $y_origine . '" x2="' . $x_fin . '" y2="' . $y_origine . '" stroke="#333" stroke-width="2.5" />';
    
    // Axe des ordonnées (vertical)
    $y_debut = $conv_y($y_min_grad);
    $y_fin = $conv_y($y_max_grad);
    $svg .= '<line x1="' . $x_origine . '" y1="' . $y_debut . '" x2="' . $x_origine . '" y2="' . $y_fin . '" stroke="#333" stroke-width="2.5" />';
    
    // ========================================
    // GRADUATIONS SUR AXE X (avec débordement)
    // ========================================
    
    for ($x = $x_min_grad; $x <= $x_max_grad + 0.001; $x += $pas_grad) {
        $pos_x = $conv_x($x);
        
        // Est-ce un entier ?
        $est_entier = (abs($x - round($x)) < 0.001);
        
        // Graduation (trait vertical)
        $hauteur_trait = $est_entier ? 16 : 10;
        $epaisseur = $est_entier ? 2.5 : 1.5;
        
        $svg .= '<line x1="' . $pos_x . '" y1="' . ($y_origine - $hauteur_trait/2) . '" x2="' . $pos_x . '" y2="' . ($y_origine + $hauteur_trait/2) . '" stroke="#333" stroke-width="' . $epaisseur . '" />';
        
        // Afficher le nombre seulement pour les entiers dans la zone principale (pas le débordement)
        if ($est_entier && $x >= $x_min && $x <= $x_max && abs($x) > 0.001) {
            $svg .= '<text x="' . $pos_x . '" y="' . ($y_origine + 28) . '" text-anchor="middle" font-family="Arial" font-size="14" fill="#333">' . (int)round($x) . '</text>';
        }
    }
    
    // ========================================
    // GRADUATIONS SUR AXE Y (avec débordement)
    // ========================================
    
    for ($y = $y_min_grad; $y <= $y_max_grad + 0.001; $y += $pas_grad) {
        $pos_y = $conv_y($y);
        
        // Est-ce un entier ?
        $est_entier = (abs($y - round($y)) < 0.001);
        
        // Graduation (trait horizontal)
        $largeur_trait = $est_entier ? 16 : 10;
        $epaisseur = $est_entier ? 2.5 : 1.5;
        
        $svg .= '<line x1="' . ($x_origine - $largeur_trait/2) . '" y1="' . $pos_y . '" x2="' . ($x_origine + $largeur_trait/2) . '" y2="' . $pos_y . '" stroke="#333" stroke-width="' . $epaisseur . '" />';
        
        // Afficher le nombre seulement pour les entiers dans la zone principale (pas le débordement)
        if ($est_entier && $y >= $y_min && $y <= $y_max && abs($y) > 0.001) {
            $svg .= '<text x="' . ($x_origine - 25) . '" y="' . ($pos_y + 5) . '" text-anchor="middle" font-family="Arial" font-size="14" fill="#333">' . (int)round($y) . '</text>';
        }
    }
    
    // ========================================
    // ORIGINE O
    // ========================================
    
    $svg .= '<text x="' . ($x_origine - 18) . '" y="' . ($y_origine + 22) . '" text-anchor="middle" font-family="Arial" font-size="15" font-weight="bold" fill="#333">O</text>';
    
    // ========================================
    // POINT (si présent)
    // ========================================
    
    if ($x_point !== null && $y_point !== null && $label !== null) {
        $pos_x_point = $conv_x($x_point);
        $pos_y_point = $conv_y($y_point);
        
        // Cercle rouge pour le point
        $svg .= '<circle cx="' . $pos_x_point . '" cy="' . $pos_y_point . '" r="6" fill="#e74c3c" stroke="#c0392b" stroke-width="2.5" />';
        
        // Label du point (décalé intelligemment selon la position)
        $decalage_x = 15;
        $decalage_y = -12;
        
        $svg .= '<text x="' . ($pos_x_point + $decalage_x) . '" y="' . ($pos_y_point + $decalage_y) . '" text-anchor="middle" font-family="Arial" font-size="18" font-weight="bold" fill="#e74c3c">' . $label . '</text>';
    }
    
    $svg .= '</svg>';
    
    return $svg;
}
