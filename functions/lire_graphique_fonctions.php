<?php
/**
 * Automatisme DNB 2026 : Exploiter graphique pour lire valeurs sur axes
 * 
 * 14 questions (7 contextes × 2 types : image + antécédent)
 * Pool se réinitialise automatiquement quand vide
 */

function generer_lire_graphique_fonctions() {
    // Pool 14 questions
    if (!isset($_SESSION['graphique_pool']) || empty($_SESSION['graphique_pool'])) {
        $_SESSION['graphique_pool'] = [
            'temp_image', 'temp_ant',
            'dist_image', 'dist_ant',
            'prix_image', 'prix_ant',
            'pop_image', 'pop_ant',
            'conso_image', 'conso_ant',
            'vit_image', 'vit_ant',
            'gaz_image', 'gaz_ant'
        ];
        shuffle($_SESSION['graphique_pool']);
    }
    
    $type = array_shift($_SESSION['graphique_pool']);
    
    // Séparer contexte et type de question
    list($contexte, $question_type) = explode('_', $type);
    
    switch ($contexte) {
        case 'temp':
            return graphique_temperature($question_type);
        case 'dist':
            return graphique_distance($question_type);
        case 'prix':
            return graphique_prix($question_type);
        case 'pop':
            return graphique_population($question_type);
        case 'conso':
            return graphique_consommation($question_type);
        case 'vit':
            return graphique_vitesse($question_type);
        case 'gaz':
            return graphique_gaz($question_type);
    }
}

// ============================================
// CONTEXTE 1 : TEMPÉRATURE JOURNÉE
// ============================================

function graphique_temperature($type) {
    // Courbe température : peut avoir plusieurs antécédents
    // Points : début froid, monte, pic midi, redescend
    $points = [
        [0, rand(-5, 2)],
        [6, rand(0, 5)],
        [12, rand(15, 20)],
        [18, rand(10, 15)],
        [24, rand(0, 5)]
    ];
    
    $svg = generer_svg_courbe_y_negatif($points, 'Heure', 'Température (°C)', 0, 24, -10, 25, 4, 5);
    
    if ($type === 'image') {
        // Choisir une heure parmi les points
        $point = $points[array_rand($points)];
        $heure = $point[0];
        $temp = $point[1];
        
        $q = '<p>Voici l\'évolution de la température au cours d\'une journée :</p>';
        $q .= $svg;
        $q .= '<p><strong>Quelle est la température à ' . $heure . ' h ?</strong></p>';
        
        $r = '<p><strong>' . $temp . ' °C</strong></p>';
        $diff = 1.0;
        
    } else {
        // Choisir une température et compter ses antécédents
        $temp_cherchee = $points[array_rand([1, 2, 3])][1]; // Éviter 0h et 24h
        $antecedents = [];
        foreach ($points as $p) {
            if ($p[1] == $temp_cherchee) {
                $antecedents[] = $p[0];
            }
        }
        
        if (count($antecedents) > 1) {
            $q = '<p>Voici l\'évolution de la température au cours d\'une journée :</p>';
            $q .= $svg;
            $q .= '<p><strong>À quelle(s) heure(s) la température est-elle de ' . $temp_cherchee . ' °C ?</strong></p>';
            $r = '<p><strong>' . implode(' h et ', $antecedents) . ' h</strong></p>';
            $diff = 1.5;
        } else {
            $q = '<p>Voici l\'évolution de la température au cours d\'une journée :</p>';
            $q .= $svg;
            $q .= '<p><strong>À quelle heure la température est-elle de ' . $temp_cherchee . ' °C ?</strong></p>';
            $r = '<p><strong>' . $antecedents[0] . ' h</strong></p>';
            $diff = 1.2;
        }
    }
    
    return [
        'type' => 'lire_graphique_fonctions',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// CONTEXTE 2 : DISTANCE PARCOURUE
// ============================================

function graphique_distance($type) {
    // Droite croissante : vitesse constante
    $vitesse = [50, 60, 80, 100][array_rand([50, 60, 80, 100])];
    $points = [];
    for ($t = 0; $t <= 5; $t++) {
        $points[] = [$t, $t * $vitesse];
    }
    
    $svg = generer_svg_courbe($points, 'Temps (h)', 'Distance (km)', 0, 5, 0, 500, 1, 100);
    
    if ($type === 'image') {
        $temps = rand(1, 4);
        $distance = $temps * $vitesse;
        
        $q = '<p>Voici la distance parcourue par un véhicule en fonction du temps :</p>';
        $q .= $svg;
        $q .= '<p><strong>Quelle distance a été parcourue après ' . $temps . ' h ?</strong></p>';
        
        $r = '<p><strong>' . $distance . ' km</strong></p>';
        $diff = 1.0;
        
    } else {
        $temps = rand(2, 4);
        $distance = $temps * $vitesse;
        
        $q = '<p>Voici la distance parcourue par un véhicule en fonction du temps :</p>';
        $q .= $svg;
        $q .= '<p><strong>Au bout de combien de temps a-t-on parcouru ' . $distance . ' km ?</strong></p>';
        
        $r = '<p><strong>' . $temps . ' h</strong></p>';
        $diff = 1.2;
    }
    
    return [
        'type' => 'lire_graphique_fonctions',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// CONTEXTE 3 : PRIX / QUANTITÉ
// ============================================

function graphique_prix($type) {
    // Droite croissante : prix au kg
    $prix_kg = [2, 3, 4, 5][array_rand([2, 3, 4, 5])];
    $points = [];
    for ($kg = 0; $kg <= 10; $kg += 2) {
        $points[] = [$kg, $kg * $prix_kg];
    }
    
    $svg = generer_svg_courbe($points, 'Quantité (kg)', 'Prix (€)', 0, 10, 0, 50, 2, 10);
    
    if ($type === 'image') {
        $kg = [2, 4, 6, 8][array_rand([2, 4, 6, 8])];
        $prix = $kg * $prix_kg;
        
        $q = '<p>Voici le prix en fonction de la quantité achetée :</p>';
        $q .= $svg;
        $q .= '<p><strong>Quel est le prix pour ' . $kg . ' kg ?</strong></p>';
        
        $r = '<p><strong>' . $prix . ' €</strong></p>';
        $diff = 1.0;
        
    } else {
        $kg = [4, 6, 8][array_rand([4, 6, 8])];
        $prix = $kg * $prix_kg;
        
        $q = '<p>Voici le prix en fonction de la quantité achetée :</p>';
        $q .= $svg;
        $q .= '<p><strong>Combien de kg peut-on acheter pour ' . $prix . ' € ?</strong></p>';
        
        $r = '<p><strong>' . $kg . ' kg</strong></p>';
        $diff = 1.2;
    }
    
    return [
        'type' => 'lire_graphique_fonctions',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// CONTEXTE 4 : POPULATION
// ============================================

function graphique_population($type) {
    // Courbe croissante - années depuis premier relevé (2000)
    // Petit village : 0 à 200 habitants
    $pop_debut = rand(20, 50);
    $points = [
        [0, $pop_debut],
        [5, $pop_debut + 30],
        [10, $pop_debut + 70],
        [15, $pop_debut + 110],
        [20, $pop_debut + 150]
    ];
    
    $svg = generer_svg_courbe($points, 'Années depuis 2000', 'Population (habitants)', 0, 20, 0, 200, 5, 50);
    
    if ($type === 'image') {
        $point = $points[array_rand([1, 2, 3])]; // Pas les extrêmes
        $annee = $point[0];
        $pop = $point[1];
        
        $q = '<p>Voici l\'évolution de la population d\'un village.</p>';
        $q .= '<p>Le premier relevé a été effectué en 2000 (' . $pop_debut . ' habitants). Un relevé est fait chaque année.</p>';
        $q .= $svg;
        $q .= '<p><strong>Quelle était la population ' . $annee . ' ans après 2000 ?</strong></p>';
        
        $r = '<p><strong>' . $pop . ' habitants</strong></p>';
        $diff = 1.0;
        
    } else {
        $point = $points[array_rand([1, 2, 3])];
        $annee = $point[0];
        $pop = $point[1];
        $annee_reelle = 2000 + $annee;
        
        $q = '<p>Voici l\'évolution de la population d\'un village.</p>';
        $q .= '<p>Le premier relevé a été effectué en 2000 (' . $pop_debut . ' habitants). Un relevé est fait chaque année.</p>';
        $q .= $svg;
        $q .= '<p><strong>En quelle année la population était-elle de ' . $pop . ' habitants ?</strong></p>';
        
        $r = '<p><strong>' . $annee_reelle . '</strong></p>';
        $diff = 1.2;
    }
    
    return [
        'type' => 'lire_graphique_fonctions',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// CONTEXTE 5 : CONSOMMATION
// ============================================

function graphique_consommation($type) {
    // Histogramme consommation par mois
    $mois_labels = ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
    $mois_noms = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    
    // Valeurs consommation (hiver plus élevé)
    $valeurs = [150, 140, 130, 120, 110, 100, 90, 95, 110, 120, 140, 150];
    
    $svg = generer_svg_histogramme($valeurs, $mois_labels, 'Mois', 'Consommation (L)', 0, 180, 30);
    
    if ($type === 'image') {
        $mois_idx = rand(0, 11);
        $conso = $valeurs[$mois_idx];
        
        $q = '<p>Voici la consommation mensuelle d\'eau d\'un foyer :</p>';
        $q .= $svg;
        $q .= '<p><strong>Quelle est la consommation en ' . $mois_noms[$mois_idx] . ' ?</strong></p>';
        
        $r = '<p><strong>' . $conso . ' L</strong></p>';
        $diff = 1.0;
        
    } else {
        // Chercher valeur unique ou double
        $conso_cherchee = 90; // Juillet uniquement
        $mois_trouves = [];
        foreach ($valeurs as $idx => $val) {
            if ($val == $conso_cherchee) {
                $mois_trouves[] = $idx;
            }
        }
        
        if (count($mois_trouves) > 1) {
            $noms = array_map(function($i) use ($mois_noms) { return $mois_noms[$i]; }, $mois_trouves);
            $q = '<p>Voici la consommation mensuelle d\'eau d\'un foyer :</p>';
            $q .= $svg;
            $q .= '<p><strong>En quel(s) mois la consommation est-elle de ' . $conso_cherchee . ' L ?</strong></p>';
            $r = '<p><strong>' . implode(' et ', $noms) . '</strong></p>';
            $diff = 1.5;
        } else {
            $q = '<p>Voici la consommation mensuelle d\'eau d\'un foyer :</p>';
            $q .= $svg;
            $q .= '<p><strong>En quel mois la consommation est-elle de ' . $conso_cherchee . ' L ?</strong></p>';
            $r = '<p><strong>' . $mois_noms[$mois_trouves[0]] . '</strong></p>';
            $diff = 1.2;
        }
    }
    
    return [
        'type' => 'lire_graphique_fonctions',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// CONTEXTE 6 : VITESSE
// ============================================

function graphique_vitesse($type) {
    // Courbe vitesse variable
    $points = [
        [0, 0], [2, 60], [4, 80], [6, 90], 
        [8, 80], [10, 60], [12, 40], [14, 20], [16, 0]
    ];
    
    $svg = generer_svg_courbe($points, 'Temps (min)', 'Vitesse (km/h)', 0, 16, 0, 100, 2, 20);
    
    if ($type === 'image') {
        $point = $points[array_rand([2, 3, 4, 5])];
        $temps = $point[0];
        $vitesse = $point[1];
        
        $q = '<p>Voici l\'évolution de la vitesse d\'un véhicule :</p>';
        $q .= $svg;
        $q .= '<p><strong>Quelle est la vitesse à ' . $temps . ' min ?</strong></p>';
        
        $r = '<p><strong>' . $vitesse . ' km/h</strong></p>';
        $diff = 1.0;
        
    } else {
        // Vitesse 60 km/h : 2 min ET 10 min
        $vitesse = 60;
        $antecedents = [2, 10];
        
        $q = '<p>Voici l\'évolution de la vitesse d\'un véhicule :</p>';
        $q .= $svg;
        $q .= '<p><strong>À quel(s) instant(s) la vitesse est-elle de ' . $vitesse . ' km/h ?</strong></p>';
        
        $r = '<p><strong>' . implode(' min et ', $antecedents) . ' min</strong></p>';
        $diff = 1.5;
    }
    
    return [
        'type' => 'lire_graphique_fonctions',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// CONTEXTE 7 : VOLUME GAZ / TEMPÉRATURE
// ============================================

function graphique_gaz($type) {
    // Droite croissante avec températures NÉGATIVES
    // Volume = 15 + 0.5 * temp
    // À -20°C : 5L, à 0°C : 15L, à 20°C : 25L, à 40°C : 35L
    // (0,0) visible mais droite ne passe pas par l'origine (réaliste)
    $points = [
        [-20, 5], [-10, 10], [0, 15], [10, 20], [20, 25], [30, 30], [40, 35]
    ];
    
    // Pour ce graphique, utiliser version avec axe Y passant par température 0
    $svg = generer_svg_courbe_avec_origine($points, 'Température (°C)', 'Volume (L)', -20, 40, 0, 40, 10, 5);
    
    if ($type === 'image') {
        $point = $points[array_rand([0, 1, 2, 4, 5])]; // Inclut températures négatives !
        $temp = $point[0];
        $volume = $point[1];
        
        $q = '<p>Voici le volume d\'un gaz en fonction de la température :</p>';
        $q .= $svg;
        $q .= '<p><strong>Quel est le volume du gaz à ' . $temp . ' °C ?</strong></p>';
        
        $r = '<p><strong>' . $volume . ' L</strong></p>';
        $diff = 1.0;
        
    } else {
        $point = $points[array_rand([2, 3, 4])]; // Éviter extrêmes
        $temp = $point[0];
        $volume = $point[1];
        
        $q = '<p>Voici le volume d\'un gaz en fonction de la température :</p>';
        $q .= $svg;
        $q .= '<p><strong>À quelle température le volume du gaz est-il de ' . $volume . ' L ?</strong></p>';
        
        $r = '<p><strong>' . $temp . ' °C</strong></p>';
        $diff = 1.2;
    }
    
    return [
        'type' => 'lire_graphique_fonctions',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// GÉNÉRATION HISTOGRAMME
// ============================================

function generer_svg_histogramme($valeurs, $labels, $label_x, $label_y, $y_min, $y_max, $pas_y) {
    $width = 500;
    $height = 400;
    $margin = 60;
    
    $plot_width = $width - 2 * $margin;
    $plot_height = $height - 2 * $margin;
    
    $nb_barres = count($valeurs);
    $largeur_barre = ($plot_width / $nb_barres) * 0.7; // 70% de l'espace pour barre
    $espace = ($plot_width / $nb_barres) * 0.3; // 30% pour espacement
    
    $scale_y = $plot_height / ($y_max - $y_min);
    
    $svg = '<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="' . $width . '" height="' . $height . '" style="border: 1px solid #ddd; background: white; margin: 20px auto; display: block; max-width:100%; height:auto;">';
    
    // Grille horizontale
    for ($y = $y_min; $y <= $y_max; $y += $pas_y) {
        $py = $height - $margin - ($y - $y_min) * $scale_y;
        $svg .= '<line x1="' . $margin . '" y1="' . $py . '" x2="' . ($width - $margin) . '" y2="' . $py . '" stroke="#ccc" stroke-width="1"/>';
    }
    
    // Axe Y (vertical) - à gauche
    $svg .= '<line x1="' . $margin . '" y1="' . $margin . '" x2="' . $margin . '" y2="' . ($height - $margin) . '" stroke="#333" stroke-width="2"/>';
    
    // Axe X (horizontal) - en bas
    $svg .= '<line x1="' . $margin . '" y1="' . ($height - $margin) . '" x2="' . ($width - $margin) . '" y2="' . ($height - $margin) . '" stroke="#333" stroke-width="2"/>';
    
    // Graduations axe Y
    for ($y = $y_min; $y <= $y_max; $y += $pas_y) {
        $py = $height - $margin - ($y - $y_min) * $scale_y;
        $svg .= '<line x1="' . ($margin - 5) . '" y1="' . $py . '" x2="' . $margin . '" y2="' . $py . '" stroke="#333" stroke-width="2"/>';
        // Ne pas afficher le label 0
        if ($y != 0) {
            $svg .= '<text x="' . ($margin - 10) . '" y="' . ($py + 4) . '" text-anchor="end" font-size="12" fill="#333">' . $y . '</text>';
        }
    }
    
    // Ajouter "O" à l'origine (en bas à gauche)
    $svg .= '<text x="' . ($margin - 15) . '" y="' . ($height - $margin + 15) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">O</text>';
    
    // Barres et labels
    foreach ($valeurs as $i => $valeur) {
        $x_centre = $margin + ($i + 0.5) * ($plot_width / $nb_barres);
        $x_barre = $x_centre - $largeur_barre / 2;
        
        $hauteur_barre = ($valeur - $y_min) * $scale_y;
        $y_barre = $height - $margin - $hauteur_barre;
        
        // Barre
        $svg .= '<rect x="' . $x_barre . '" y="' . $y_barre . '" width="' . $largeur_barre . '" height="' . $hauteur_barre . '" fill="#2196F3" stroke="#1976D2" stroke-width="1"/>';
        
        // Label mois
        $svg .= '<text x="' . $x_centre . '" y="' . ($height - $margin + 20) . '" text-anchor="middle" font-size="12" font-weight="bold" fill="#333">' . $labels[$i] . '</text>';
    }
    
    // Labels axes
    $svg .= '<text x="' . ($width / 2) . '" y="' . ($height - 10) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">' . htmlspecialchars($label_x) . '</text>';
    $svg .= '<text x="15" y="' . ($height / 2) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333" transform="rotate(-90 15 ' . ($height / 2) . ')">' . htmlspecialchars($label_y) . '</text>';
    
    $svg .= '</svg>';
    
    return $svg;
}

// ============================================
// GÉNÉRATION SVG - VERSION STANDARD
// ============================================

function generer_svg_courbe($points, $label_x, $label_y, $x_min, $x_max, $y_min, $y_max, $pas_x, $pas_y) {
    $width = 500;
    $height = 400;
    $margin = 60;
    
    $plot_width = $width - 2 * $margin;
    $plot_height = $height - 2 * $margin;
    
    // Fonction conversion coordonnées
    $scale_x = $plot_width / ($x_max - $x_min);
    $scale_y = $plot_height / ($y_max - $y_min);
    
    $svg = '<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="' . $width . '" height="' . $height . '" style="border: 1px solid #ddd; background: white; margin: 20px auto; display: block; max-width:100%; height:auto;">';
    
    // Grille verticale
    for ($x = $x_min; $x <= $x_max; $x += $pas_x) {
        $px = $margin + ($x - $x_min) * $scale_x;
        $svg .= '<line x1="' . $px . '" y1="' . $margin . '" x2="' . $px . '" y2="' . ($height - $margin) . '" stroke="#ccc" stroke-width="1"/>';
    }
    
    // Grille horizontale
    for ($y = $y_min; $y <= $y_max; $y += $pas_y) {
        $py = $height - $margin - ($y - $y_min) * $scale_y;
        $svg .= '<line x1="' . $margin . '" y1="' . $py . '" x2="' . ($width - $margin) . '" y2="' . $py . '" stroke="#ccc" stroke-width="1"/>';
    }
    
    // Axe Y (vertical) - à gauche
    $svg .= '<line x1="' . $margin . '" y1="' . $margin . '" x2="' . $margin . '" y2="' . ($height - $margin) . '" stroke="#333" stroke-width="2"/>';
    
    // Axe X (horizontal) - en bas
    $svg .= '<line x1="' . $margin . '" y1="' . ($height - $margin) . '" x2="' . ($width - $margin) . '" y2="' . ($height - $margin) . '" stroke="#333" stroke-width="2"/>';
    
    // Graduations axe X
    for ($x = $x_min; $x <= $x_max; $x += $pas_x) {
        $px = $margin + ($x - $x_min) * $scale_x;
        $svg .= '<line x1="' . $px . '" y1="' . ($height - $margin) . '" x2="' . $px . '" y2="' . ($height - $margin + 5) . '" stroke="#333" stroke-width="2"/>';
        // Ne pas afficher le label 0
        if ($x != 0) {
            $svg .= '<text x="' . $px . '" y="' . ($height - $margin + 20) . '" text-anchor="middle" font-size="12" fill="#333">' . $x . '</text>';
        }
    }
    
    // Graduations axe Y
    for ($y = $y_min; $y <= $y_max; $y += $pas_y) {
        $py = $height - $margin - ($y - $y_min) * $scale_y;
        $svg .= '<line x1="' . ($margin - 5) . '" y1="' . $py . '" x2="' . $margin . '" y2="' . $py . '" stroke="#333" stroke-width="2"/>';
        // Ne pas afficher le label 0
        if ($y != 0) {
            $svg .= '<text x="' . ($margin - 10) . '" y="' . ($py + 4) . '" text-anchor="end" font-size="12" fill="#333">' . $y . '</text>';
        }
    }
    
    // Ajouter "O" à l'origine (en bas à gauche)
    $svg .= '<text x="' . ($margin - 15) . '" y="' . ($height - $margin + 15) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">O</text>';
    
    // Labels axes
    $svg .= '<text x="' . ($width / 2) . '" y="' . ($height - 10) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">' . htmlspecialchars($label_x) . '</text>';
    $svg .= '<text x="15" y="' . ($height / 2) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333" transform="rotate(-90 15 ' . ($height / 2) . ')">' . htmlspecialchars($label_y) . '</text>';
    
    // Tracer la courbe
    $path = 'M ';
    foreach ($points as $i => $point) {
        $px = $margin + ($point[0] - $x_min) * $scale_x;
        $py = $height - $margin - ($point[1] - $y_min) * $scale_y;
        
        if ($i === 0) {
            $path .= $px . ',' . $py;
        } else {
            $path .= ' L ' . $px . ',' . $py;
        }
    }
    $svg .= '<path d="' . $path . '" fill="none" stroke="#2196F3" stroke-width="3"/>';
    
    // Points
    foreach ($points as $point) {
        $px = $margin + ($point[0] - $x_min) * $scale_x;
        $py = $height - $margin - ($point[1] - $y_min) * $scale_y;
        $svg .= '<circle cx="' . $px . '" cy="' . $py . '" r="5" fill="#FF5722"/>';
    }
    
    $svg .= '</svg>';
    
    return $svg;
}

// ============================================
// GÉNÉRATION SVG - Y peut être négatif (température journée)
// Axe X passe par y=0, Axe Y à gauche (x=0)
// ============================================

function generer_svg_courbe_y_negatif($points, $label_x, $label_y, $x_min, $x_max, $y_min, $y_max, $pas_x, $pas_y) {
    $width = 500;
    $height = 400;
    $margin = 60;
    
    $plot_width = $width - 2 * $margin;
    $plot_height = $height - 2 * $margin;
    
    // Fonction conversion coordonnées
    $scale_x = $plot_width / ($x_max - $x_min);
    $scale_y = $plot_height / ($y_max - $y_min);
    
    $svg = '<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="' . $width . '" height="' . $height . '" style="border: 1px solid #ddd; background: white; margin: 20px auto; display: block; max-width:100%; height:auto;">';
    
    // Grille verticale
    for ($x = $x_min; $x <= $x_max; $x += $pas_x) {
        $px = $margin + ($x - $x_min) * $scale_x;
        $svg .= '<line x1="' . $px . '" y1="' . $margin . '" x2="' . $px . '" y2="' . ($height - $margin) . '" stroke="#ccc" stroke-width="1"/>';
    }
    
    // Grille horizontale
    for ($y = $y_min; $y <= $y_max; $y += $pas_y) {
        $py = $height - $margin - ($y - $y_min) * $scale_y;
        $svg .= '<line x1="' . $margin . '" y1="' . $py . '" x2="' . ($width - $margin) . '" y2="' . $py . '" stroke="#ccc" stroke-width="1"/>';
    }
    
    // Position de l'axe X (passe par y=0)
    $axe_x_y = $height - $margin - (0 - $y_min) * $scale_y;
    
    // Axe Y (vertical) - à gauche (x=0 qui est x_min)
    $svg .= '<line x1="' . $margin . '" y1="' . $margin . '" x2="' . $margin . '" y2="' . ($height - $margin) . '" stroke="#333" stroke-width="2"/>';
    
    // Axe X (horizontal) - passe par température 0
    $svg .= '<line x1="' . $margin . '" y1="' . $axe_x_y . '" x2="' . ($width - $margin) . '" y2="' . $axe_x_y . '" stroke="#333" stroke-width="2"/>';
    
    // Graduations axe X
    for ($x = $x_min; $x <= $x_max; $x += $pas_x) {
        $px = $margin + ($x - $x_min) * $scale_x;
        $svg .= '<line x1="' . $px . '" y1="' . $axe_x_y . '" x2="' . $px . '" y2="' . ($axe_x_y + 5) . '" stroke="#333" stroke-width="2"/>';
        // Ne pas afficher le label 0
        if ($x != 0) {
            $svg .= '<text x="' . $px . '" y="' . ($axe_x_y + 20) . '" text-anchor="middle" font-size="12" fill="#333">' . $x . '</text>';
        }
    }
    
    // Graduations axe Y
    for ($y = $y_min; $y <= $y_max; $y += $pas_y) {
        $py = $height - $margin - ($y - $y_min) * $scale_y;
        $svg .= '<line x1="' . ($margin - 5) . '" y1="' . $py . '" x2="' . $margin . '" y2="' . $py . '" stroke="#333" stroke-width="2"/>';
        // Ne pas afficher le label 0
        if ($y != 0) {
            $svg .= '<text x="' . ($margin - 10) . '" y="' . ($py + 4) . '" text-anchor="end" font-size="12" fill="#333">' . $y . '</text>';
        }
    }
    
    // Ajouter "O" à l'origine (intersection des axes : x=0, y=0)
    $svg .= '<text x="' . ($margin - 15) . '" y="' . ($axe_x_y + 15) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">O</text>';
    
    // Labels axes
    $svg .= '<text x="' . ($width / 2) . '" y="' . ($height - 10) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">' . htmlspecialchars($label_x) . '</text>';
    $svg .= '<text x="15" y="' . ($height / 2) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333" transform="rotate(-90 15 ' . ($height / 2) . ')">' . htmlspecialchars($label_y) . '</text>';
    
    // Tracer la courbe
    $path = 'M ';
    foreach ($points as $i => $point) {
        $px = $margin + ($point[0] - $x_min) * $scale_x;
        $py = $height - $margin - ($point[1] - $y_min) * $scale_y;
        
        if ($i === 0) {
            $path .= $px . ',' . $py;
        } else {
            $path .= ' L ' . $px . ',' . $py;
        }
    }
    $svg .= '<path d="' . $path . '" fill="none" stroke="#2196F3" stroke-width="3"/>';
    
    // Points
    foreach ($points as $point) {
        $px = $margin + ($point[0] - $x_min) * $scale_x;
        $py = $height - $margin - ($point[1] - $y_min) * $scale_y;
        $svg .= '<circle cx="' . $px . '" cy="' . $py . '" r="5" fill="#FF5722"/>';
    }
    
    $svg .= '</svg>';
    
    return $svg;
}

// ============================================
// GÉNÉRATION SVG - VERSION AVEC ORIGINE (pour température en X)
// Axe Y passe par x=0 au lieu d'être à gauche
// ============================================

function generer_svg_courbe_avec_origine($points, $label_x, $label_y, $x_min, $x_max, $y_min, $y_max, $pas_x, $pas_y) {
    $width = 500;
    $height = 400;
    $margin = 60;
    
    $plot_width = $width - 2 * $margin;
    $plot_height = $height - 2 * $margin;
    
    // Fonction conversion coordonnées
    $scale_x = $plot_width / ($x_max - $x_min);
    $scale_y = $plot_height / ($y_max - $y_min);
    
    $svg = '<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="' . $width . '" height="' . $height . '" style="border: 1px solid #ddd; background: white; margin: 20px auto; display: block; max-width:100%; height:auto;">';
    
    // Grille verticale (plus visible)
    for ($x = $x_min; $x <= $x_max; $x += $pas_x) {
        $px = $margin + ($x - $x_min) * $scale_x;
        $svg .= '<line x1="' . $px . '" y1="' . $margin . '" x2="' . $px . '" y2="' . ($height - $margin) . '" stroke="#ccc" stroke-width="1"/>';
    }
    
    // Grille horizontale (plus visible)
    for ($y = $y_min; $y <= $y_max; $y += $pas_y) {
        $py = $height - $margin - ($y - $y_min) * $scale_y;
        $svg .= '<line x1="' . $margin . '" y1="' . $py . '" x2="' . ($width - $margin) . '" y2="' . $py . '" stroke="#ccc" stroke-width="1"/>';
    }
    
    // Position de l'axe Y (passe par x=0)
    $axe_y_x = $margin + (0 - $x_min) * $scale_x;
    
    // Axe Y (vertical) - passe par température 0
    $svg .= '<line x1="' . $axe_y_x . '" y1="' . $margin . '" x2="' . $axe_y_x . '" y2="' . ($height - $margin) . '" stroke="#333" stroke-width="2"/>';
    
    // Axe X (horizontal) - toujours en bas
    $svg .= '<line x1="' . $margin . '" y1="' . ($height - $margin) . '" x2="' . ($width - $margin) . '" y2="' . ($height - $margin) . '" stroke="#333" stroke-width="2"/>';
    
    // Graduations axe X
    for ($x = $x_min; $x <= $x_max; $x += $pas_x) {
        $px = $margin + ($x - $x_min) * $scale_x;
        $svg .= '<line x1="' . $px . '" y1="' . ($height - $margin) . '" x2="' . $px . '" y2="' . ($height - $margin + 5) . '" stroke="#333" stroke-width="2"/>';
        // Ne pas afficher le label 0
        if ($x != 0) {
            $svg .= '<text x="' . $px . '" y="' . ($height - $margin + 20) . '" text-anchor="middle" font-size="12" fill="#333">' . $x . '</text>';
        }
    }
    
    // Graduations axe Y
    for ($y = $y_min; $y <= $y_max; $y += $pas_y) {
        $py = $height - $margin - ($y - $y_min) * $scale_y;
        $svg .= '<line x1="' . ($axe_y_x - 5) . '" y1="' . $py . '" x2="' . $axe_y_x . '" y2="' . $py . '" stroke="#333" stroke-width="2"/>';
        // Ne pas afficher le label 0
        if ($y != 0) {
            $svg .= '<text x="' . ($axe_y_x - 10) . '" y="' . ($py + 4) . '" text-anchor="end" font-size="12" fill="#333">' . $y . '</text>';
        }
    }
    
    // Ajouter "O" à l'origine (intersection des axes)
    $svg .= '<text x="' . ($axe_y_x - 15) . '" y="' . ($height - $margin + 15) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">O</text>';
    
    // Labels axes
    $svg .= '<text x="' . ($width / 2) . '" y="' . ($height - 10) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">' . htmlspecialchars($label_x) . '</text>';
    $svg .= '<text x="15" y="' . ($height / 2) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333" transform="rotate(-90 15 ' . ($height / 2) . ')">' . htmlspecialchars($label_y) . '</text>';
    
    // Tracer la courbe
    $path = 'M ';
    foreach ($points as $i => $point) {
        $px = $margin + ($point[0] - $x_min) * $scale_x;
        $py = $height - $margin - ($point[1] - $y_min) * $scale_y;
        
        if ($i === 0) {
            $path .= $px . ',' . $py;
        } else {
            $path .= ' L ' . $px . ',' . $py;
        }
    }
    $svg .= '<path d="' . $path . '" fill="none" stroke="#2196F3" stroke-width="3"/>';
    
    // Points
    foreach ($points as $point) {
        $px = $margin + ($point[0] - $x_min) * $scale_x;
        $py = $height - $margin - ($point[1] - $y_min) * $scale_y;
        $svg .= '<circle cx="' . $px . '" cy="' . $py . '" r="5" fill="#FF5722"/>';
    }
    
    $svg .= '</svg>';
    
    return $svg;
}
?>
