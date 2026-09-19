<?php
/**
 * Automatisme DNB 2026 : Lire et interpréter tableaux, diagrammes et graphiques
 */

function generer_graphiques($famille = '') {
    // Filtre optionnel de famille pour une page hôte, traité avant le pool
    // historique : l'appel sans argument (session DNB) est inchangé.
    if (in_array($famille, ['barres', 'tableau', 'circulaire', 'courbe'], true)) {
        switch ($famille) {
            case 'barres':     return graph_barres(null);
            case 'tableau':    return graph_tableau(null);
            case 'circulaire': return graph_circulaire(null);
            case 'courbe':     return graph_courbe(null);
        }
    }

    if (!isset($_SESSION['graphiques_pool']) || empty($_SESSION['graphiques_pool'])) {
        // ÉTAPE 1 : Tirer au sort barres OU courbe pour chaque contexte temporel
        $contextes_temporels = ['livres', 'ventes', 'temperatures', 'visiteurs'];
        $pool_temporel = [];
        
        foreach ($contextes_temporels as $ctx) {
            // 50% barres, 50% courbe pour chaque contexte
            $type = (rand(0, 1) == 0) ? 'barres' : 'courbe';
            $pool_temporel[] = $type . '_' . $ctx;
        }
        
        // ÉTAPE 2 : Construire le pool complet
        $_SESSION['graphiques_pool'] = array_merge(
            $pool_temporel, // 4 contextes temporels (barres OU courbe)
            [
                // Tableau - 3 contextes fixes
                'tableau_notes', 'tableau_ages', 'tableau_couleurs',
                // Circulaire - 5 contextes de répartition fixes
                'circulaire_transport', 'circulaire_ages', 'circulaire_matieres', 
                'circulaire_sports', 'circulaire_fruits'
            ]
        );
        
        shuffle($_SESSION['graphiques_pool']);
    }
    
    $type_contexte = array_shift($_SESSION['graphiques_pool']);
    
    // Séparer type et contexte
    $parts = explode('_', $type_contexte);
    $type = $parts[0];
    $contexte = isset($parts[1]) ? $parts[1] : null;
    
    switch ($type) {
        case 'barres': return graph_barres($contexte);
        case 'tableau': return graph_tableau($contexte);
        case 'circulaire': return graph_circulaire($contexte);
        case 'courbe': return graph_courbe($contexte);
    }
}

// ============================================
// TYPE 1 : DIAGRAMME EN BARRES
// ============================================

function graph_barres($contexte_impose = null) {
    $contextes = [
        'livres' => [
            'titre' => 'Nombre de livres lus par mois',
            'categories' => ['Janvier', 'Février', 'Mars', 'Avril'],
            'unite' => ' livres',
            'garder_ordre' => true,
            'questions' => [
                ['type' => 'lecture', 'var' => 'mois', 'verbe' => 'ont été lus en'],
                ['type' => 'max', 'singulier' => 'mois', 'verbe' => 'a eu le plus de livres lus'],
                ['type' => 'ordre_croissant', 'nom' => 'mois', 'critere' => 'nombre de livres'],
                ['type' => 'ordre_croissant', 'nom' => 'mois', 'critere' => 'nombre de livres'],
                ['type' => 'ordre_decroissant', 'nom' => 'mois', 'critere' => 'nombre de livres'],
                ['type' => 'vrai_faux', 'debut' => 0, 'fin' => 2, 'texte' => 'Le nombre de livres lus augmente de {debut} à {fin}.'],
                ['type' => 'total', 'nom' => 'livres ont été lus']
            ]
        ],
        'ventes' => [
            'titre' => 'Ventes par trimestre',
            'categories' => ['T1', 'T2', 'T3', 'T4'],
            'unite' => ' articles',
            'garder_ordre' => true,
            'questions' => [
                ['type' => 'lecture', 'var' => 'trimestre', 'verbe' => 'ont été vendus au'],
                ['type' => 'max', 'singulier' => 'trimestre', 'verbe' => 'a les meilleures ventes'],
                ['type' => 'ordre_croissant', 'nom' => 'trimestres', 'critere' => 'ventes'],
                ['type' => 'ordre_decroissant', 'nom' => 'trimestres', 'critere' => 'ventes'],
                ['type' => 'ordre_decroissant', 'nom' => 'trimestres', 'critere' => 'ventes'],
                ['type' => 'vrai_faux', 'debut' => 0, 'fin' => 2, 'texte' => 'Les ventes augmentent de {debut} à {fin}.'],
                ['type' => 'total', 'nom' => 'd\'articles ont été vendus']
            ]
        ],
        'temperatures' => [
            'titre' => 'Température moyenne par mois',
            'categories' => ['Janvier', 'Février', 'Mars', 'Avril'],
            'unite' => ' °C',
            'garder_ordre' => true,
            'questions' => [
                ['type' => 'lecture', 'var' => 'mois', 'verbe' => 'Quelle est la température moyenne au mois de'],
                ['type' => 'max', 'singulier' => 'mois', 'verbe' => 'a la température la plus élevée'],
                ['type' => 'min', 'singulier' => 'mois', 'verbe' => 'a la température la plus basse'],
                ['type' => 'ordre_croissant', 'nom' => 'mois', 'critere' => 'température'],
                ['type' => 'ordre_decroissant', 'nom' => 'mois', 'critere' => 'température'],
                ['type' => 'vrai_faux', 'debut' => 0, 'fin' => 2, 'texte' => 'La température augmente de {debut} à {fin}.']
            ]
        ],
        'visiteurs' => [
            'titre' => 'Nombre de visiteurs par mois au musée',
            'categories' => ['Janvier', 'Février', 'Mars', 'Avril'],
            'unite' => ' visiteurs',
            'garder_ordre' => true,
            'questions' => [
                ['type' => 'lecture', 'var' => 'mois', 'verbe' => 'Combien de visiteurs au mois de'],
                ['type' => 'max', 'singulier' => 'mois', 'verbe' => 'a eu le plus de visiteurs'],
                ['type' => 'ordre_croissant', 'nom' => 'mois', 'critere' => 'nombre de visiteurs'],
                ['type' => 'ordre_decroissant', 'nom' => 'mois', 'critere' => 'nombre de visiteurs'],
                ['type' => 'vrai_faux', 'debut' => 0, 'fin' => 2, 'texte' => 'Le nombre de visiteurs augmente de {debut} à {fin}.'],
                ['type' => 'total', 'nom' => 'visiteurs ont visité le musée']
            ]
        ]
    ];
    
    if ($contexte_impose && isset($contextes[$contexte_impose])) {
        $ctx = $contextes[$contexte_impose];
    } else {
        $ctx = $contextes[array_rand($contextes)];
    }
    
    $nb = rand(3, 4);
    $cats = $ctx['categories'];
    
    if (!isset($ctx['garder_ordre']) || !$ctx['garder_ordre']) {
        shuffle($cats);
    }
    $cats = array_slice($cats, 0, $nb);
    
    // Valeurs TOUTES DIFFÉRENTES
    $valeurs = [];
    $valeurs_disponibles = range(6, 24);
    shuffle($valeurs_disponibles);
    for ($i = 0; $i < $nb; $i++) {
        $valeurs[] = $valeurs_disponibles[$i];
    }
    
    $donnees = array_combine($cats, $valeurs);
    
    $question_data = $ctx['questions'][array_rand($ctx['questions'])];
    
    $q = '<p>' . $ctx['titre'] . ' :</p>';
    $q .= svg_barres($donnees, $ctx['titre']);
    
    $diff = 1.2;
    
    switch ($question_data['type']) {
        case 'lecture':
            $cat_cible = $cats[array_rand($cats)];
            $verbe = isset($question_data['verbe']) ? $question_data['verbe'] . ' ' : '';
            
            if (strpos($verbe, 'Quelle est') !== false || strpos($verbe, 'Combien de') !== false) {
                $q .= '<p><strong>' . $verbe . strtolower($cat_cible) . ' ?</strong></p>';
            } elseif (strpos($verbe, 'ont répondu') !== false) {
                $q .= '<p><strong>Combien d\'' . trim($ctx['unite']) . ' ' . $verbe . '"' . $cat_cible . '" ?</strong></p>';
            } else {
                $q .= '<p><strong>Combien d\'' . trim($ctx['unite']) . ' ' . $verbe . strtolower($cat_cible) . ' ?</strong></p>';
            }
            
            $r = '<p><strong>' . $donnees[$cat_cible] . $ctx['unite'] . '</strong></p>';
            $diff = 1.0;
            break;
            
        case 'max':
            $verbe = isset($question_data['verbe']) ? $question_data['verbe'] : 'est le plus choisi';
            $q .= '<p><strong>Quel ' . $question_data['singulier'] . ' ' . $verbe . ' ?</strong></p>';
            $max_val = max($valeurs);
            $cat_max = array_search($max_val, $donnees);
            $r = '<p><strong>' . $cat_max . '</strong> (' . $max_val . $ctx['unite'] . ')</p>';
            $diff = 1.4;
            break;
            
        case 'min':
            $verbe = isset($question_data['verbe']) ? $question_data['verbe'] : 'est le moins choisi';
            $q .= '<p><strong>Quel ' . $question_data['singulier'] . ' ' . $verbe . ' ?</strong></p>';
            $min_val = min($valeurs);
            $cat_min = array_search($min_val, $donnees);
            $r = '<p><strong>' . $cat_min . '</strong> (' . $min_val . $ctx['unite'] . ')</p>';
            $diff = 1.4;
            break;
            
        case 'ordre_croissant':
            $critere = $question_data['critere'];
            $accord = graph_accord($critere, 'croissant');
            $q .= '<p><strong>Classe ces ' . $question_data['nom'] . ' par ordre de ' . $critere . ' ' . $accord . '.</strong></p>';
            asort($donnees);
            $ordre = array_keys($donnees);
            $r = '<p><strong>' . implode(', ', $ordre) . '</strong></p>';
            $diff = 1.7;
            break;
            
        case 'ordre_decroissant':
            $critere = $question_data['critere'];
            $accord = graph_accord($critere, 'décroissant');
            $q .= '<p><strong>Classe ces ' . $question_data['nom'] . ' par ordre de ' . $critere . ' ' . $accord . '.</strong></p>';
            arsort($donnees);
            $ordre = array_keys($donnees);
            $r = '<p><strong>' . implode(', ', $ordre) . '</strong></p>';
            $diff = 1.7;
            break;
            
        case 'total':
            $q .= '<p><strong>Combien de ' . $question_data['nom'] . ' au total ?</strong></p>';
            $total = array_sum($valeurs);
            $r = '<p><strong>' . $total . $ctx['unite'] . '</strong></p>';
            $diff = 1.5;
            break;
            
        case 'vrai_faux':
            $idx_debut = $question_data['debut'];
            $idx_fin = $question_data['fin'];
            $cat_debut = $cats[$idx_debut];
            $cat_fin = $cats[$idx_fin];
            
            $question_txt = str_replace(['{debut}', '{fin}'], [strtolower($cat_debut), strtolower($cat_fin)], $question_data['texte']);
            $q .= '<p><strong>' . ucfirst($question_txt) . '</strong></p>';
            $q .= '<p><em>Répondre par Vrai ou Faux.</em></p>';
            
            // Vérifier si c'est croissant
            $val_debut = $donnees[$cat_debut];
            $val_fin = $donnees[$cat_fin];
            $est_croissant = true;
            $cats_array = array_keys($donnees);
            for ($i = $idx_debut; $i < $idx_fin; $i++) {
                if ($donnees[$cats_array[$i]] > $donnees[$cats_array[$i + 1]]) {
                    $est_croissant = false;
                    break;
                }
            }
            
            $reponse = $est_croissant ? 'Vrai' : 'Faux';
            $r = '<p><strong>' . $reponse . '</strong></p>';
            $diff = 1.0;
            break;
    }
    
    return ['type' => 'graphiques', 'difficulte_id' => $diff, 'question' => $q, 'reponse' => $r];
}

function svg_barres($donnees, $titre) {
    $nb = count($donnees);
    $largeur = 450;
    $hauteur = 350;
    $marge_gauche = 50;
    $marge_bas = 80;
    $marge_haut = 40;
    
    $zone_largeur = $largeur - $marge_gauche - 30;
    $zone_hauteur = $hauteur - $marge_haut - $marge_bas;
    
    $max = max(array_values($donnees));
    $echelle_max = ceil($max / 5) * 5;
    
    $largeur_barre = ($zone_largeur / $nb) * 0.6;
    $espacement = ($zone_largeur / $nb);
    
    $couleurs = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#ffa07a', '#98d8c8'];
    
    $svg = '<svg viewBox="0 0 ' . $largeur . ' ' . $hauteur . '" width="' . $largeur . '" height="' . $hauteur . '" xmlns="http://www.w3.org/2000/svg" style="display: block; margin: 20px auto; max-width:100%; height:auto;">';
    
    // Axes
    $svg .= '<line x1="' . $marge_gauche . '" y1="' . $marge_haut . '" x2="' . $marge_gauche . '" y2="' . ($hauteur - $marge_bas) . '" stroke="#333" stroke-width="2"/>';
    $svg .= '<line x1="' . $marge_gauche . '" y1="' . ($hauteur - $marge_bas) . '" x2="' . ($largeur - 20) . '" y2="' . ($hauteur - $marge_bas) . '" stroke="#333" stroke-width="2"/>';
    
    // Graduations axe Y
    for ($i = 0; $i <= 4; $i++) {
        $val = $echelle_max * $i / 4;
        $y = ($hauteur - $marge_bas) - ($zone_hauteur * $i / 4);
        $svg .= '<line x1="' . ($marge_gauche - 5) . '" y1="' . $y . '" x2="' . $marge_gauche . '" y2="' . $y . '" stroke="#333" stroke-width="1"/>';
        $svg .= '<text x="' . ($marge_gauche - 10) . '" y="' . ($y + 5) . '" text-anchor="end" font-size="12" fill="#333">' . intval($val) . '</text>';
    }
    
    // Barres
    $index = 0;
    foreach ($donnees as $cat => $val) {
        $x = $marge_gauche + ($index * $espacement) + ($espacement - $largeur_barre) / 2;
        $hauteur_barre = ($val / $echelle_max) * $zone_hauteur;
        $y = ($hauteur - $marge_bas) - $hauteur_barre;
        
        $couleur = $couleurs[$index % count($couleurs)];
        
        $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $largeur_barre . '" height="' . $hauteur_barre . '" fill="' . $couleur . '" stroke="#333" stroke-width="1"/>';
        $svg .= '<text x="' . ($x + $largeur_barre/2) . '" y="' . ($y - 5) . '" text-anchor="middle" font-size="14" font-weight="bold" fill="#333">' . $val . '</text>';
        
        // Label sur 2 lignes si trop long
        $label = $cat;
        if (strlen($label) > 10) {
            $mots = explode(' ', $label);
            if (count($mots) > 1) {
                $svg .= '<text x="' . ($x + $largeur_barre/2) . '" y="' . ($hauteur - $marge_bas + 20) . '" text-anchor="middle" font-size="11" fill="#333">' . $mots[0] . '</text>';
                $svg .= '<text x="' . ($x + $largeur_barre/2) . '" y="' . ($hauteur - $marge_bas + 35) . '" text-anchor="middle" font-size="11" fill="#333">' . $mots[1] . '</text>';
            } else {
                $svg .= '<text x="' . ($x + $largeur_barre/2) . '" y="' . ($hauteur - $marge_bas + 20) . '" text-anchor="middle" font-size="11" fill="#333">' . $label . '</text>';
            }
        } else {
            $svg .= '<text x="' . ($x + $largeur_barre/2) . '" y="' . ($hauteur - $marge_bas + 20) . '" text-anchor="middle" font-size="13" fill="#333">' . $label . '</text>';
        }
        
        $index++;
    }
    
    $svg .= '</svg>';
    return $svg;
}

// ============================================
// TYPE 2 : TABLEAU
// ============================================

function graph_tableau($contexte_impose = null) {
    $contextes = [
        'notes' => [
            'titre' => 'Note obtenue',
            'categories' => ['8', '10', '12', '14', '16'],
            'garder_ordre' => true,
            'questions' => [
                ['type' => 'lecture', 'question' => 'Combien d\'élèves ont obtenu la note {cat} ?'],
                ['type' => 'max', 'question' => 'Quelle note a été obtenue par le plus d\'élèves ?'],
                ['type' => 'min', 'question' => 'Quelle note a été obtenue par le moins d\'élèves ?'],
                ['type' => 'ordre_croissant', 'question' => 'Classe ces notes par ordre croissant d\'effectifs.'],
                ['type' => 'ordre_decroissant', 'question' => 'Classe ces notes par ordre décroissant d\'effectifs.'],
                ['type' => 'total', 'question' => 'Combien d\'élèves ont été évalués ?']
            ]
        ],
        'ages' => [
            'titre' => 'Âge',
            'categories' => ['10 ans', '11 ans', '12 ans', '13 ans'],
            'garder_ordre' => true,
            'questions' => [
                ['type' => 'lecture', 'question' => 'Combien d\'enfants ont {cat} ?'],
                ['type' => 'max', 'question' => 'Quel âge est le plus représenté ?'],
                ['type' => 'ordre_croissant', 'question' => 'Classe ces âges par ordre croissant d\'effectifs.'],
                ['type' => 'ordre_decroissant', 'question' => 'Classe ces âges par ordre décroissant d\'effectifs.'],
                ['type' => 'total', 'question' => 'Combien d\'enfants au total ?']
            ]
        ],
        'couleurs' => [
            'titre' => 'Couleur préférée',
            'categories' => ['Rouge', 'Bleu', 'Vert', 'Jaune'],
            'questions' => [
                ['type' => 'lecture', 'question' => 'Combien de personnes préfèrent {cat} ?'],
                ['type' => 'max', 'question' => 'Quelle couleur est la plus choisie ?'],
                ['type' => 'min', 'question' => 'Quelle couleur est la moins choisie ?'],
                ['type' => 'ordre_croissant', 'question' => 'Classe ces couleurs par ordre croissant d\'effectifs.'],
                ['type' => 'ordre_decroissant', 'question' => 'Classe ces couleurs par ordre décroissant d\'effectifs.']
            ]
        ]
    ];
    
    if ($contexte_impose && isset($contextes[$contexte_impose])) {
        $ctx = $contextes[$contexte_impose];
    } else {
        $ctx = $contextes[array_rand($contextes)];
    }
    
    $nb = rand(3, 4);
    $cats = $ctx['categories'];
    
    if (!isset($ctx['garder_ordre']) || !$ctx['garder_ordre']) {
        shuffle($cats);
    }
    $cats = array_slice($cats, 0, $nb);
    
    // Effectifs TOUS DIFFÉRENTS
    $effectifs = [];
    $valeurs_disponibles = range(5, 20);
    shuffle($valeurs_disponibles);
    for ($i = 0; $i < $nb; $i++) {
        $effectifs[] = $valeurs_disponibles[$i];
    }
    
    $q = '<p>Voici les résultats d\'une enquête :</p>';
    $q .= '<table border="1" cellpadding="12" cellspacing="0" style="margin: 20px auto; border-collapse: collapse; font-size: 1.1em;">';
    $q .= '<tr style="background-color: #4CAF50; color: white;"><th style="padding: 12px; border: 1px solid #2e7d32;">' . $ctx['titre'] . '</th>';
    foreach ($cats as $c) {
        $q .= '<th style="padding: 12px; border: 1px solid #2e7d32;">' . $c . '</th>';
    }
    $q .= '</tr><tr><th style="background-color: #e8f5e9; padding: 12px; border: 1px solid #ccc;">Effectif</th>';
    foreach ($effectifs as $e) {
        $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;">' . $e . '</td>';
    }
    $q .= '</tr></table>';
    
    $question_data = $ctx['questions'][array_rand($ctx['questions'])];
    $diff = 1.3;
    
    switch ($question_data['type']) {
        case 'lecture':
            $idx = array_rand($cats);
            $question_txt = str_replace('{cat}', strtolower($cats[$idx]), $question_data['question']);
            $q .= '<p><strong>' . $question_txt . '</strong></p>';
            $r = '<p><strong>' . $effectifs[$idx] . '</strong></p>';
            $diff = 1.1;
            break;
            
        case 'max':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $max_idx = array_search(max($effectifs), $effectifs);
            $r = '<p><strong>' . $cats[$max_idx] . '</strong> (' . $effectifs[$max_idx] . ')</p>';
            $diff = 1.4;
            break;
            
        case 'min':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $min_idx = array_search(min($effectifs), $effectifs);
            $r = '<p><strong>' . $cats[$min_idx] . '</strong> (' . $effectifs[$min_idx] . ')</p>';
            $diff = 1.4;
            break;
            
        case 'ordre_croissant':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $donnees = array_combine($cats, $effectifs);
            asort($donnees);
            $ordre = array_keys($donnees);
            $r = '<p><strong>' . implode(', ', $ordre) . '</strong></p>';
            $diff = 1.7;
            break;
            
        case 'ordre_decroissant':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $donnees = array_combine($cats, $effectifs);
            arsort($donnees);
            $ordre = array_keys($donnees);
            $r = '<p><strong>' . implode(', ', $ordre) . '</strong></p>';
            $diff = 1.7;
            break;
            
        case 'total':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $total = array_sum($effectifs);
            $r = '<p><strong>' . $total . '</strong></p>';
            $diff = 1.5;
            break;
    }
    
    return ['type' => 'graphiques', 'difficulte_id' => $diff, 'question' => $q, 'reponse' => $r];
}

// ============================================
// TYPE 3 : DIAGRAMME CIRCULAIRE
// ============================================

function graph_circulaire($contexte_impose = null) {
    $contextes = [
        'transport' => [
            'titre' => 'Comment les élèves viennent au collège',
            'categories' => ['Bus', 'Voiture', 'Vélo', 'À pied'],
            'questions' => [
                ['type' => 'lecture', 'question' => 'Quel pourcentage d\'élèves viennent {prep} {cat} ?', 'prep' => 'en'],
                ['type' => 'max', 'question' => 'Quel moyen de transport est le plus utilisé ?'],
                ['type' => 'ordre_croissant', 'question' => 'Classe ces moyens de transport par pourcentage croissant.'],
                ['type' => 'ordre_decroissant', 'question' => 'Classe ces moyens de transport par pourcentage décroissant.'],
                ['type' => 'ordre_decroissant', 'question' => 'Classe ces moyens de transport par pourcentage décroissant.']
            ]
        ],
        'ages' => [
            'titre' => 'Répartition des âges',
            'categories' => ['10 ans', '11 ans', '12 ans', '13 ans'],
            'questions' => [
                ['type' => 'lecture', 'question' => 'Quel pourcentage représente les enfants de {cat} ?', 'prep' => ''],
                ['type' => 'max', 'question' => 'Quel âge représente le plus grand pourcentage ?'],
                ['type' => 'ordre_croissant', 'question' => 'Classe ces âges par pourcentage croissant.'],
                ['type' => 'ordre_croissant', 'question' => 'Classe ces âges par pourcentage croissant.'],
                ['type' => 'ordre_decroissant', 'question' => 'Classe ces âges par pourcentage décroissant.']
            ]
        ],
        'matieres' => [
            'titre' => 'Matières préférées',
            'categories' => ['Maths', 'Français', 'Sport', 'Arts'],
            'articles' => ['les', 'le', 'le', 'les'],
            'questions' => [
                ['type' => 'lecture', 'question' => 'Quel pourcentage pour {article} {cat} ?', 'prep' => '', 'guillemets' => true],
                ['type' => 'max', 'question' => 'Quelle matière est la plus appréciée ?'],
                ['type' => 'min', 'question' => 'Quelle matière est la moins appréciée ?'],
                ['type' => 'ordre_croissant', 'question' => 'Classe ces matières par pourcentage croissant.'],
                ['type' => 'ordre_decroissant', 'question' => 'Classe ces matières par pourcentage décroissant.']
            ]
        ],
        'sports' => [
            'titre' => 'Sports préférés dans la classe',
            'categories' => ['Football', 'Tennis', 'Natation', 'Basket'],
            'articles' => ['le', 'le', 'la', 'le'],
            'questions' => [
                ['type' => 'lecture', 'question' => 'Quel pourcentage d\'élèves préfèrent {article} {cat} ?', 'prep' => ''],
                ['type' => 'max', 'question' => 'Quel sport est le plus apprécié ?'],
                ['type' => 'min', 'question' => 'Quel sport est le moins apprécié ?'],
                ['type' => 'ordre_croissant', 'question' => 'Classe ces sports par pourcentage croissant.'],
                ['type' => 'ordre_decroissant', 'question' => 'Classe ces sports par pourcentage décroissant.']
            ]
        ],
        'fruits' => [
            'titre' => 'Fruits préférés',
            'categories' => ['Pomme', 'Banane', 'Orange', 'Fraise'],
            'articles' => ['la', 'la', 'l\'', 'la'],
            'questions' => [
                ['type' => 'lecture', 'question' => 'Quel pourcentage pour {article}{cat} ?', 'prep' => '', 'guillemets' => true],
                ['type' => 'max', 'question' => 'Quel fruit est le plus apprécié ?'],
                ['type' => 'min', 'question' => 'Quel fruit est le moins apprécié ?'],
                ['type' => 'ordre_croissant', 'question' => 'Classe ces fruits par pourcentage croissant.'],
                ['type' => 'ordre_decroissant', 'question' => 'Classe ces fruits par pourcentage décroissant.']
            ]
        ]
    ];
    
    if ($contexte_impose && isset($contextes[$contexte_impose])) {
        $ctx = $contextes[$contexte_impose];
    } else {
        $ctx = $contextes[array_rand($contextes)];
    }
    
    $nb = rand(3, 4);
    $cats = $ctx['categories'];
    shuffle($cats);
    $cats = array_slice($cats, 0, $nb);
    
    // Pourcentages TOUS DIFFÉRENTS
    $pourcentages = [];
    if ($nb == 3) {
        $pcts = [[40, 35, 25], [50, 30, 20], [45, 35, 20]];
        $pourcentages = $pcts[array_rand($pcts)];
    } else {
        $pcts = [[35, 30, 20, 15], [40, 25, 20, 15], [45, 25, 20, 10]];
        $pourcentages = $pcts[array_rand($pcts)];
    }
    
    $q = '<p>' . $ctx['titre'] . ' :</p>';
    $q .= svg_circulaire($cats, $pourcentages);
    
    $question_data = $ctx['questions'][array_rand($ctx['questions'])];
    $diff = 1.4;
    
    switch ($question_data['type']) {
        case 'lecture':
            $idx = array_rand($cats);
            $prep = isset($question_data['prep']) ? $question_data['prep'] . ' ' : '';
            
            // Gérer l'article si présent
            $article = '';
            if (isset($ctx['articles'])) {
                $article = $ctx['articles'][$idx];
                // Si article avec espace (le, la, les) ajouter espace
                if (!in_array($article, ['l\''])) {
                    $article .= ' ';
                }
            }
            
            if (isset($question_data['guillemets']) && $question_data['guillemets']) {
                $question_txt = str_replace(['{article}', '{cat}'], [$article, strtolower($cats[$idx])], $question_data['question']);
            } else {
                $question_txt = str_replace(['{article}', '{prep}', '{cat}'], [$article, $prep, strtolower($cats[$idx])], $question_data['question']);
            }
            
            $q .= '<p><strong>' . ucfirst($question_txt) . '</strong></p>';
            $r = '<p><strong>' . $pourcentages[$idx] . ' %</strong></p>';
            $diff = 1.2;
            break;
            
        case 'max':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $max_idx = array_search(max($pourcentages), $pourcentages);
            $r = '<p><strong>' . $cats[$max_idx] . '</strong> (' . $pourcentages[$max_idx] . ' %)</p>';
            $diff = 1.5;
            break;
            
        case 'min':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $min_idx = array_search(min($pourcentages), $pourcentages);
            $r = '<p><strong>' . $cats[$min_idx] . '</strong> (' . $pourcentages[$min_idx] . ' %)</p>';
            $diff = 1.5;
            break;
            
        case 'ordre_croissant':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $donnees = array_combine($cats, $pourcentages);
            asort($donnees);
            $ordre = array_keys($donnees);
            $r = '<p><strong>' . implode(', ', $ordre) . '</strong></p>';
            $diff = 1.8;
            break;
            
        case 'ordre_decroissant':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $donnees = array_combine($cats, $pourcentages);
            arsort($donnees);
            $ordre = array_keys($donnees);
            $r = '<p><strong>' . implode(', ', $ordre) . '</strong></p>';
            $diff = 1.8;
            break;
    }
    
    return ['type' => 'graphiques', 'difficulte_id' => $diff, 'question' => $q, 'reponse' => $r];
}

function svg_circulaire($categories, $pourcentages) {
    $largeur = 500;
    $hauteur = 350;
    $cx = 200;
    $cy = 175;
    $rayon = 120;
    
    $couleurs = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#ffa07a', '#98d8c8'];
    
    $svg = '<svg viewBox="0 0 ' . $largeur . ' ' . $hauteur . '" width="' . $largeur . '" height="' . $hauteur . '" xmlns="http://www.w3.org/2000/svg" style="display: block; margin: 20px auto; max-width:100%; height:auto;">';
    
    $angle_depart = -90;
    
    foreach ($categories as $i => $cat) {
        $pct = $pourcentages[$i];
        $angle = $pct * 360 / 100;
        
        $angle_fin = $angle_depart + $angle;
        
        $x1 = $cx + $rayon * cos(deg2rad($angle_depart));
        $y1 = $cy + $rayon * sin(deg2rad($angle_depart));
        $x2 = $cx + $rayon * cos(deg2rad($angle_fin));
        $y2 = $cy + $rayon * sin(deg2rad($angle_fin));
        
        $large_arc = ($angle > 180) ? 1 : 0;
        
        $svg .= '<path d="M ' . $cx . ' ' . $cy . ' L ' . $x1 . ' ' . $y1 . ' A ' . $rayon . ' ' . $rayon . ' 0 ' . $large_arc . ' 1 ' . $x2 . ' ' . $y2 . ' Z" ';
        $svg .= 'fill="' . $couleurs[$i] . '" stroke="#fff" stroke-width="3"/>';
        
        $angle_milieu = $angle_depart + $angle / 2;
        $text_rayon = $rayon * 0.65;
        $text_x = $cx + $text_rayon * cos(deg2rad($angle_milieu));
        $text_y = $cy + $text_rayon * sin(deg2rad($angle_milieu));
        
        $svg .= '<text x="' . $text_x . '" y="' . $text_y . '" text-anchor="middle" dominant-baseline="middle" font-size="16" font-weight="bold" fill="#fff">' . $pct . '%</text>';
        
        $angle_depart = $angle_fin;
    }
    
    // Légende
    $legende_x = 360;
    $legende_y = 60;
    
    foreach ($categories as $i => $cat) {
        $y = $legende_y + $i * 35;
        $svg .= '<rect x="' . $legende_x . '" y="' . $y . '" width="25" height="25" fill="' . $couleurs[$i] . '" stroke="#333" stroke-width="1"/>';
        $svg .= '<text x="' . ($legende_x + 35) . '" y="' . ($y + 17) . '" font-size="14" fill="#333">' . $cat . '</text>';
    }
    
    $svg .= '</svg>';
    return $svg;
}

// ============================================
// TYPE 4 : GRAPHIQUE COURBE
// ============================================

function graph_courbe($contexte_impose = null) {
    $contextes = [
        'livres' => [
            'titre' => 'Évolution du nombre de livres lus',
            'categories' => ['Janvier', 'Février', 'Mars', 'Avril'],
            'unite' => ' livres',
            'questions' => [
                ['type' => 'lecture', 'question' => 'Combien de livres ont été lus en {mois} ?'],
                ['type' => 'max', 'question' => 'En quel mois a-t-on lu le plus de livres ?'],
                ['type' => 'min', 'question' => 'En quel mois a-t-on lu le moins de livres ?'],
                ['type' => 'tendance', 'question' => 'Entre janvier et avril, le nombre de livres lus augmente-t-il ou diminue-t-il ?'],
                ['type' => 'vrai_faux', 'debut' => 0, 'fin' => 2, 'texte' => 'Le nombre de livres lus augmente de {debut} à {fin}.'],
                ['type' => 'variation', 'mois1' => 'Janvier', 'mois2' => 'Mars']
            ]
        ],
        'ventes' => [
            'titre' => 'Évolution des ventes par trimestre',
            'categories' => ['T1', 'T2', 'T3', 'T4'],
            'unite' => ' articles',
            'questions' => [
                ['type' => 'lecture', 'question' => 'Combien d\'articles ont été vendus au {trim} ?'],
                ['type' => 'max', 'question' => 'Quel trimestre a les meilleures ventes ?'],
                ['type' => 'min', 'question' => 'Quel trimestre a les ventes les plus faibles ?'],
                ['type' => 'tendance', 'question' => 'Entre T1 et T4, les ventes augmentent-elles ou diminuent-elles ?'],
                ['type' => 'vrai_faux', 'debut' => 0, 'fin' => 2, 'texte' => 'Les ventes augmentent de {debut} à {fin}.'],
                ['type' => 'variation', 'trim1' => 'T1', 'trim2' => 'T3']
            ]
        ],
        'temperatures' => [
            'titre' => 'Évolution de la température moyenne',
            'categories' => ['Janvier', 'Février', 'Mars', 'Avril'],
            'unite' => ' °C',
            'questions' => [
                ['type' => 'lecture', 'question' => 'Quelle est la température au mois de {mois} ?'],
                ['type' => 'max', 'question' => 'En quel mois la température est-elle la plus élevée ?'],
                ['type' => 'min', 'question' => 'En quel mois la température est-elle la plus basse ?'],
                ['type' => 'tendance', 'question' => 'Entre janvier et avril, la température augmente-t-elle ou diminue-t-elle ?'],
                ['type' => 'vrai_faux', 'debut' => 0, 'fin' => 2, 'texte' => 'La température augmente de {debut} à {fin}.'],
                ['type' => 'variation', 'mois1' => 'Janvier', 'mois2' => 'Avril']
            ]
        ],
        'visiteurs' => [
            'titre' => 'Évolution du nombre de visiteurs au musée',
            'categories' => ['Janvier', 'Février', 'Mars', 'Avril'],
            'unite' => ' visiteurs',
            'questions' => [
                ['type' => 'lecture', 'question' => 'Combien de visiteurs au mois de {mois} ?'],
                ['type' => 'max', 'question' => 'En quel mois y a-t-il eu le plus de visiteurs ?'],
                ['type' => 'min', 'question' => 'En quel mois y a-t-il eu le moins de visiteurs ?'],
                ['type' => 'tendance', 'question' => 'Entre janvier et avril, le nombre de visiteurs augmente-t-il ou diminue-t-il ?'],
                ['type' => 'vrai_faux', 'debut' => 0, 'fin' => 2, 'texte' => 'Le nombre de visiteurs augmente de {debut} à {fin}.'],
                ['type' => 'variation', 'mois1' => 'Février', 'mois2' => 'Avril']
            ]
        ]
    ];
    
    if ($contexte_impose && isset($contextes[$contexte_impose])) {
        $ctx = $contextes[$contexte_impose];
    } else {
        $ctx = $contextes[array_rand($contextes)];
    }
    
    $cats = $ctx['categories'];
    $nb = count($cats);
    
    // Valeurs TOUTES DIFFÉRENTES
    $valeurs = [];
    $valeurs_disponibles = range(6, 24);
    shuffle($valeurs_disponibles);
    for ($i = 0; $i < $nb; $i++) {
        $valeurs[] = $valeurs_disponibles[$i];
    }
    
    $donnees = array_combine($cats, $valeurs);
    
    $question_data = $ctx['questions'][array_rand($ctx['questions'])];
    
    $q = '<p>' . $ctx['titre'] . ' :</p>';
    $q .= svg_courbe($donnees, $ctx['titre'], $ctx['unite']);
    
    $diff = 1.2;
    
    switch ($question_data['type']) {
        case 'lecture':
            $cat_cible = $cats[array_rand($cats)];
            $placeholder = '{mois}';
            if (isset($question_data['question']) && strpos($question_data['question'], '{trim}') !== false) {
                $placeholder = '{trim}';
            }
            $question_txt = str_replace($placeholder, strtolower($cat_cible), $question_data['question']);
            $q .= '<p><strong>' . ucfirst($question_txt) . '</strong></p>';
            $r = '<p><strong>' . $donnees[$cat_cible] . $ctx['unite'] . '</strong></p>';
            $diff = 1.0;
            break;
            
        case 'max':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $max_val = max($valeurs);
            $cat_max = array_search($max_val, $donnees);
            $r = '<p><strong>' . $cat_max . '</strong> (' . $max_val . $ctx['unite'] . ')</p>';
            $diff = 1.4;
            break;
            
        case 'min':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $min_val = min($valeurs);
            $cat_min = array_search($min_val, $donnees);
            $r = '<p><strong>' . $cat_min . '</strong> (' . $min_val . $ctx['unite'] . ')</p>';
            $diff = 1.4;
            break;
            
        case 'tendance':
            $q .= '<p><strong>' . $question_data['question'] . '</strong></p>';
            $premiere_val = $valeurs[0];
            $derniere_val = $valeurs[count($valeurs) - 1];
            $tendance = ($derniere_val > $premiere_val) ? 'Augmente' : 'Diminue';
            $r = '<p><strong>' . $tendance . '</strong></p>';
            $diff = 1.8;
            break;
            
        case 'variation':
            $mois1 = isset($question_data['mois1']) ? $question_data['mois1'] : (isset($question_data['trim1']) ? $question_data['trim1'] : $cats[0]);
            $mois2 = isset($question_data['mois2']) ? $question_data['mois2'] : (isset($question_data['trim2']) ? $question_data['trim2'] : $cats[2]);
            
            $val1 = $donnees[$mois1];
            $val2 = $donnees[$mois2];
            $diff_val = abs($val2 - $val1);
            
            $q .= '<p><strong>De combien varie la valeur entre ' . $mois1 . ' et ' . $mois2 . ' ?</strong></p>';
            $r = '<p><strong>' . $diff_val . $ctx['unite'] . '</strong></p>';
            $r .= '<p style="font-size: 0.9em; color: #666;">' . $val2 . ' − ' . $val1 . ' = ' . $diff_val . '</p>';
            $diff = 1.6;
            break;
            
        case 'vrai_faux':
            $idx_debut = $question_data['debut'];
            $idx_fin = $question_data['fin'];
            $cat_debut = $cats[$idx_debut];
            $cat_fin = $cats[$idx_fin];
            
            $question_txt = str_replace(['{debut}', '{fin}'], [strtolower($cat_debut), strtolower($cat_fin)], $question_data['texte']);
            $q .= '<p><strong>' . ucfirst($question_txt) . '</strong></p>';
            $q .= '<p><em>Répondre par Vrai ou Faux.</em></p>';
            
            // Vérifier si c'est strictement croissant
            $est_croissant = true;
            for ($i = $idx_debut; $i < $idx_fin; $i++) {
                if ($valeurs[$i] > $valeurs[$i + 1]) {
                    $est_croissant = false;
                    break;
                }
            }
            
            $reponse = $est_croissant ? 'Vrai' : 'Faux';
            $r = '<p><strong>' . $reponse . '</strong></p>';
            $diff = 1.0;
            break;
    }
    
    return ['type' => 'graphiques', 'difficulte_id' => $diff, 'question' => $q, 'reponse' => $r];
}

function svg_courbe($donnees, $titre, $unite) {
    $largeur = 450;
    $hauteur = 350;
    $marge_gauche = 50;
    $marge_bas = 60;
    $marge_haut = 40;
    $marge_droite = 30;
    
    $zone_largeur = $largeur - $marge_gauche - $marge_droite;
    $zone_hauteur = $hauteur - $marge_haut - $marge_bas;
    
    $nb = count($donnees);
    $max = max(array_values($donnees));
    $echelle_max = ceil($max / 5) * 5;
    
    $svg = '<svg viewBox="0 0 ' . $largeur . ' ' . $hauteur . '" width="' . $largeur . '" height="' . $hauteur . '" xmlns="http://www.w3.org/2000/svg" style="display: block; margin: 20px auto; max-width:100%; height:auto;">';
    
    // Grille horizontale légère
    for ($i = 0; $i <= 4; $i++) {
        $y = ($hauteur - $marge_bas) - ($zone_hauteur * $i / 4);
        $svg .= '<line x1="' . $marge_gauche . '" y1="' . $y . '" x2="' . ($largeur - $marge_droite) . '" y2="' . $y . '" stroke="#e0e0e0" stroke-width="1"/>';
    }
    
    // Axes
    $svg .= '<line x1="' . $marge_gauche . '" y1="' . $marge_haut . '" x2="' . $marge_gauche . '" y2="' . ($hauteur - $marge_bas) . '" stroke="#333" stroke-width="2"/>';
    $svg .= '<line x1="' . $marge_gauche . '" y1="' . ($hauteur - $marge_bas) . '" x2="' . ($largeur - $marge_droite) . '" y2="' . ($hauteur - $marge_bas) . '" stroke="#333" stroke-width="2"/>';
    
    // Graduations axe Y
    for ($i = 0; $i <= 4; $i++) {
        $val = $echelle_max * $i / 4;
        $y = ($hauteur - $marge_bas) - ($zone_hauteur * $i / 4);
        $svg .= '<line x1="' . ($marge_gauche - 5) . '" y1="' . $y . '" x2="' . $marge_gauche . '" y2="' . $y . '" stroke="#333" stroke-width="1"/>';
        $svg .= '<text x="' . ($marge_gauche - 10) . '" y="' . ($y + 5) . '" text-anchor="end" font-size="12" fill="#333">' . intval($val) . '</text>';
    }
    
    // Construire la ligne (chemin)
    $espacement_x = $zone_largeur / ($nb - 1);
    $points = [];
    $index = 0;
    
    foreach ($donnees as $cat => $val) {
        $x = $marge_gauche + ($index * $espacement_x);
        $y = ($hauteur - $marge_bas) - (($val / $echelle_max) * $zone_hauteur);
        $points[] = [$x, $y, $cat, $val];
        $index++;
    }
    
    // Tracer la courbe (ligne brisée)
    $path = 'M ' . $points[0][0] . ' ' . $points[0][1];
    for ($i = 1; $i < count($points); $i++) {
        $path .= ' L ' . $points[$i][0] . ' ' . $points[$i][1];
    }
    $svg .= '<path d="' . $path . '" stroke="#2196F3" stroke-width="3" fill="none"/>';
    
    // Points sur la courbe
    foreach ($points as $p) {
        list($x, $y, $cat, $val) = $p;
        
        // Point
        $svg .= '<circle cx="' . $x . '" cy="' . $y . '" r="5" fill="#2196F3" stroke="#fff" stroke-width="2"/>';
        
        // Valeur au-dessus
        $svg .= '<text x="' . $x . '" y="' . ($y - 10) . '" text-anchor="middle" font-size="13" font-weight="bold" fill="#333">' . $val . '</text>';
        
        // Label en dessous
        $svg .= '<text x="' . $x . '" y="' . ($hauteur - $marge_bas + 20) . '" text-anchor="middle" font-size="12" fill="#333">' . $cat . '</text>';
    }
    
    $svg .= '</svg>';
    return $svg;
}

/**
 * Accord de « croissant / décroissante » avec le critère de classement.
 * L'accord se fait sur le nom NOYAU, c'est-à-dire le PREMIER mot du critère —
 * et non sur le dernier, comme le faisait la version d'origine : « nombre de
 * livres » regardait « livres » et produisait « par ordre de nombre de livres
 * croissante ».
 */
function graph_accord($critere, $forme) {
    $mots  = explode(' ', trim($critere));
    $noyau = $mots[0];

    // Pluriel : « ventes » → « par ordre de ventes croissantes »
    $pluriel = (mb_substr($noyau, -1) === 's');
    $base    = $pluriel ? mb_substr($noyau, 0, -1) : $noyau;

    $masculin_en_e = ['nombre', 'groupe', 'type', 'chiffre', 'pourcentage', 'volume', 'effectif'];
    $feminin = (mb_substr($base, -1) === 'e' && !in_array($base, $masculin_en_e, true));

    return $forme . ($feminin ? 'e' : '') . ($pluriel ? 's' : '');
}

?>
