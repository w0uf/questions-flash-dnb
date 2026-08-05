<?php
/**
 * Automatisme DNB 2026 : Reconnaître si situation donnée est proportionnelle ou non
 * 
 * 3 types de questions :
 * - Tableaux (40%)
 * - Graphiques (30%)
 * - Contextes (30%)
 */

function generer_reconnaitre_proportionnalite() {
    if (!isset($_SESSION['reconnaitre_proportionnalite_pool']) || empty($_SESSION['reconnaitre_proportionnalite_pool'])) {
        $_SESSION['reconnaitre_proportionnalite_pool'] = [
            // Tableaux (5 questions = 40%)
            'tableau', 'tableau', 'tableau', 'tableau', 'tableau',
            // Graphiques (3 questions = 30%)
            'graphique', 'graphique', 'graphique',
            // Contextes (4 questions = 30%)
            'contexte', 'contexte', 'contexte', 'contexte'
        ];
        shuffle($_SESSION['reconnaitre_proportionnalite_pool']);
    }
    
    $type = array_shift($_SESSION['reconnaitre_proportionnalite_pool']);
    
    switch ($type) {
        case 'tableau': return question_tableau_proportionnalite();
        case 'graphique': return question_graphique_proportionnalite();
        case 'contexte': return question_contexte_proportionnalite();
    }
}

// ============================================
// TYPE 1 : TABLEAU
// ============================================

function question_tableau_proportionnalite() {
    // 50% VRAI, 50% FAUX
    $est_proportionnel = (rand(0, 1) == 1);
    
    $nb_colonnes = rand(3, 4);
    
    // Contextes variés
    $contextes = [
        ['ligne1' => 'Distance (km)', 'ligne2' => 'Temps (min)'],
        ['ligne1' => 'Masse (kg)', 'ligne2' => 'Prix (€)'],
        ['ligne1' => 'Nombre de litres', 'ligne2' => 'Prix (€)'],
        ['ligne1' => 'Longueur (m)', 'ligne2' => 'Prix (€)'],
        ['ligne1' => 'Nombre d\'objets', 'ligne2' => 'Prix total (€)'],
        ['ligne1' => 'Temps (h)', 'ligne2' => 'Distance (km)'],
    ];
    
    $ctx = $contextes[array_rand($contextes)];
    
    if ($est_proportionnel) {
        // Générer tableau proportionnel
        $k = rand(2, 5); // Coefficient de proportionnalité
        
        $x_values = [];
        $y_values = [];
        
        for ($i = 0; $i < $nb_colonnes; $i++) {
            $x = rand(2, 10);
            // Éviter doublons
            while (in_array($x, $x_values)) {
                $x = rand(2, 10);
            }
            $x_values[] = $x;
            $y_values[] = $x * $k;
        }
        
        // Trier par x croissant pour clarté
        array_multisort($x_values, $y_values);
        
    } else {
        // Générer tableau NON proportionnel
        $k_base = rand(2, 5);
        
        $x_values = [];
        $y_values = [];
        
        for ($i = 0; $i < $nb_colonnes; $i++) {
            $x = rand(2, 10);
            while (in_array($x, $x_values)) {
                $x = rand(2, 10);
            }
            $x_values[] = $x;
            
            // Première valeur suit le coefficient
            if ($i == 0) {
                $y_values[] = $x * $k_base;
            } else {
                // Modifier légèrement le coefficient pour les autres
                $variation = rand(-2, 2);
                if ($variation == 0) $variation = 1;
                $y_values[] = $x * $k_base + $variation;
            }
        }
        
        array_multisort($x_values, $y_values);
    }
    
    // Construire le tableau HTML
    $q = '<p>Voici un tableau de valeurs :</p>';
    $q .= '<table border="1" cellpadding="12" cellspacing="0" style="margin: 20px auto; border-collapse: collapse; font-size: 1.1em;">';
    $q .= '<tr style="background-color: #4CAF50; color: white;">';
    $q .= '<th style="padding: 12px; border: 1px solid #2e7d32;">' . $ctx['ligne1'] . '</th>';
    foreach ($x_values as $x) {
        $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #2e7d32; color: white; font-weight: bold;">' . $x . '</td>';
    }
    $q .= '</tr><tr>';
    $q .= '<th style="background-color: #e8f5e9; padding: 12px; border: 1px solid #ccc;">' . $ctx['ligne2'] . '</th>';
    foreach ($y_values as $y) {
        $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;">' . $y . '</td>';
    }
    $q .= '</tr></table>';
    
    $q .= '<p><strong>Ce tableau est-il un tableau de proportionnalité ?</strong></p>';
    $q .= '<p><em>Répondre par Vrai ou Faux.</em></p>';
    
    $r = '<p><strong>' . ($est_proportionnel ? 'Vrai' : 'Faux') . '</strong></p>';
    
    return [
        'type' => 'reconnaitre_proportionnalite',
        'difficulte_id' => 1.0,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// TYPE 2 : GRAPHIQUE
// ============================================

function question_graphique_proportionnalite() {
    // Pool graphiques : 1 VRAI, 2 FAUX
    if (!isset($_SESSION['graphiques_proportionnalite_pool']) || empty($_SESSION['graphiques_proportionnalite_pool'])) {
        $_SESSION['graphiques_proportionnalite_pool'] = [
            'droite_origine',  // VRAI
            'droite_decalee',  // FAUX
            'parabole'         // FAUX
        ];
        shuffle($_SESSION['graphiques_proportionnalite_pool']);
    }
    
    $type = array_shift($_SESSION['graphiques_proportionnalite_pool']);
    
    $est_proportionnel = ($type === 'droite_origine');
    
    $q = '<p>Voici la représentation graphique de la relation entre deux grandeurs :</p>';
    
    switch ($type) {
        case 'droite_origine':
            $q .= svg_graphique_droite_origine();
            break;
        case 'droite_decalee':
            $q .= svg_graphique_droite_decalee();
            break;
        case 'parabole':
            $q .= svg_graphique_parabole();
            break;
    }
    
    $q .= '<p><strong>Ce graphique représente-t-il une situation de proportionnalité ?</strong></p>';
    $q .= '<p><em>Répondre par Vrai ou Faux.</em></p>';
    
    $r = '<p><strong>' . ($est_proportionnel ? 'Vrai' : 'Faux') . '</strong></p>';
    
    $diff = ($type === 'parabole') ? 1.0 : 1.2;
    
    return [
        'type' => 'reconnaitre_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function svg_graphique_droite_origine() {
    $largeur = 400;
    $hauteur = 300;
    $marge = 40;
    
    $k = rand(2, 4); // Coefficient
    
    // Points : (0,0), (2, 2k), (4, 4k), (6, 6k)
    $points = [
        [0, 0],
        [2, 2 * $k],
        [4, 4 * $k],
        [6, 6 * $k]
    ];
    
    $max_x = 8;
    $max_y = 8 * $k;
    
    $svg = '<svg width="' . $largeur . '" height="' . $hauteur . '" xmlns="http://www.w3.org/2000/svg" style="display: block; margin: 20px auto; border: 1px solid #ccc;">';
    
    // Axes
    $svg .= '<line x1="' . $marge . '" y1="' . ($hauteur - $marge) . '" x2="' . ($largeur - $marge) . '" y2="' . ($hauteur - $marge) . '" stroke="#333" stroke-width="2"/>';
    $svg .= '<line x1="' . $marge . '" y1="' . $marge . '" x2="' . $marge . '" y2="' . ($hauteur - $marge) . '" stroke="#333" stroke-width="2"/>';
    
    // Grille légère
    for ($i = 0; $i <= 8; $i++) {
        $x = $marge + ($i / 8) * ($largeur - 2 * $marge);
        $svg .= '<line x1="' . $x . '" y1="' . $marge . '" x2="' . $x . '" y2="' . ($hauteur - $marge) . '" stroke="#ddd" stroke-width="1"/>';
    }
    
    // Tracer la droite
    $x1_svg = $marge;
    $y1_svg = $hauteur - $marge;
    $x2_svg = $marge + ($largeur - 2 * $marge);
    $y2_svg = $marge;
    
    $svg .= '<line x1="' . $x1_svg . '" y1="' . $y1_svg . '" x2="' . $x2_svg . '" y2="' . $y2_svg . '" stroke="#2196F3" stroke-width="2"/>';
    
    // Points
    foreach ($points as $p) {
        list($x, $y) = $p;
        $x_svg = $marge + ($x / $max_x) * ($largeur - 2 * $marge);
        $y_svg = ($hauteur - $marge) - ($y / $max_y) * ($hauteur - 2 * $marge);
        
        $svg .= '<circle cx="' . $x_svg . '" cy="' . $y_svg . '" r="5" fill="#FF5722" stroke="#fff" stroke-width="2"/>';
    }
    
    $svg .= '</svg>';
    return $svg;
}

function svg_graphique_droite_decalee() {
    $largeur = 400;
    $hauteur = 300;
    $marge = 40;
    
    $k = rand(2, 3);
    $b = rand(3, 6); // Ordonnée à l'origine (≠ 0)
    
    // Points : (0, b), (2, 2k+b), (4, 4k+b), (6, 6k+b)
    $points = [
        [0, $b],
        [2, 2 * $k + $b],
        [4, 4 * $k + $b],
        [6, 6 * $k + $b]
    ];
    
    $max_x = 8;
    $max_y = 8 * $k + $b + 2;
    
    $svg = '<svg width="' . $largeur . '" height="' . $hauteur . '" xmlns="http://www.w3.org/2000/svg" style="display: block; margin: 20px auto; border: 1px solid #ccc;">';
    
    // Axes
    $svg .= '<line x1="' . $marge . '" y1="' . ($hauteur - $marge) . '" x2="' . ($largeur - $marge) . '" y2="' . ($hauteur - $marge) . '" stroke="#333" stroke-width="2"/>';
    $svg .= '<line x1="' . $marge . '" y1="' . $marge . '" x2="' . $marge . '" y2="' . ($hauteur - $marge) . '" stroke="#333" stroke-width="2"/>';
    
    // Grille
    for ($i = 0; $i <= 8; $i++) {
        $x = $marge + ($i / 8) * ($largeur - 2 * $marge);
        $svg .= '<line x1="' . $x . '" y1="' . $marge . '" x2="' . $x . '" y2="' . ($hauteur - $marge) . '" stroke="#ddd" stroke-width="1"/>';
    }
    
    // Tracer la droite (de x=0 à x=8)
    $y1 = $b;
    $y2 = 8 * $k + $b;
    
    $x1_svg = $marge;
    $y1_svg = ($hauteur - $marge) - ($y1 / $max_y) * ($hauteur - 2 * $marge);
    $x2_svg = $marge + ($largeur - 2 * $marge);
    $y2_svg = ($hauteur - $marge) - ($y2 / $max_y) * ($hauteur - 2 * $marge);
    
    $svg .= '<line x1="' . $x1_svg . '" y1="' . $y1_svg . '" x2="' . $x2_svg . '" y2="' . $y2_svg . '" stroke="#2196F3" stroke-width="2"/>';
    
    // Points
    foreach ($points as $p) {
        list($x, $y) = $p;
        $x_svg = $marge + ($x / $max_x) * ($largeur - 2 * $marge);
        $y_svg = ($hauteur - $marge) - ($y / $max_y) * ($hauteur - 2 * $marge);
        
        $svg .= '<circle cx="' . $x_svg . '" cy="' . $y_svg . '" r="5" fill="#FF5722" stroke="#fff" stroke-width="2"/>';
    }
    
    $svg .= '</svg>';
    return $svg;
}

function svg_graphique_parabole() {
    $largeur = 400;
    $hauteur = 300;
    $marge = 40;
    
    $a = rand(1, 2); // Coefficient
    
    // Points : (0, 0), (1, a), (2, 4a), (3, 9a), (4, 16a)
    $points = [
        [0, 0],
        [1, $a],
        [2, 4 * $a],
        [3, 9 * $a],
        [4, 16 * $a]
    ];
    
    $max_x = 5;
    $max_y = 20 * $a;
    
    $svg = '<svg width="' . $largeur . '" height="' . $hauteur . '" xmlns="http://www.w3.org/2000/svg" style="display: block; margin: 20px auto; border: 1px solid #ccc;">';
    
    // Axes
    $svg .= '<line x1="' . $marge . '" y1="' . ($hauteur - $marge) . '" x2="' . ($largeur - $marge) . '" y2="' . ($hauteur - $marge) . '" stroke="#333" stroke-width="2"/>';
    $svg .= '<line x1="' . $marge . '" y1="' . $marge . '" x2="' . $marge . '" y2="' . ($hauteur - $marge) . '" stroke="#333" stroke-width="2"/>';
    
    // Grille
    for ($i = 0; $i <= 8; $i++) {
        $x = $marge + ($i / 8) * ($largeur - 2 * $marge);
        $svg .= '<line x1="' . $x . '" y1="' . $marge . '" x2="' . $x . '" y2="' . ($hauteur - $marge) . '" stroke="#ddd" stroke-width="1"/>';
    }
    
    // Tracer la parabole (courbe lisse)
    $path = '';
    for ($x = 0; $x <= 5; $x += 0.2) {
        $y = $a * $x * $x;
        $x_svg = $marge + ($x / $max_x) * ($largeur - 2 * $marge);
        $y_svg = ($hauteur - $marge) - ($y / $max_y) * ($hauteur - 2 * $marge);
        
        if ($path === '') {
            $path = 'M ' . $x_svg . ' ' . $y_svg;
        } else {
            $path .= ' L ' . $x_svg . ' ' . $y_svg;
        }
    }
    
    $svg .= '<path d="' . $path . '" stroke="#2196F3" stroke-width="2" fill="none"/>';
    
    // Points
    foreach ($points as $p) {
        list($x, $y) = $p;
        $x_svg = $marge + ($x / $max_x) * ($largeur - 2 * $marge);
        $y_svg = ($hauteur - $marge) - ($y / $max_y) * ($hauteur - 2 * $marge);
        
        $svg .= '<circle cx="' . $x_svg . '" cy="' . $y_svg . '" r="5" fill="#FF5722" stroke="#fff" stroke-width="2"/>';
    }
    
    $svg .= '</svg>';
    return $svg;
}

// ============================================
// TYPE 3 : CONTEXTE
// ============================================

function question_contexte_proportionnalite() {
    $contextes_vrai = [
        [
            'texte' => null, // Généré dynamiquement
            'question' => null, // Généré dynamiquement
            'reponse' => null, // Dépend du type
            'type' => 'achat_dynamique'
        ],
        [
            'texte' => 'Une voiture roule à vitesse constante de 90 km/h.',
            'question' => 'La distance parcourue est-elle proportionnelle au temps de trajet ?',
            'reponse' => true
        ],
        [
            'texte' => 'Pour faire un gâteau, il faut 200 g de farine pour 4 personnes.',
            'question' => 'Si on double le nombre de personnes, la masse de farine est-elle proportionnelle au nombre de personnes ?',
            'reponse' => true
        ],
        [
            'texte' => 'Du tissu coûte 12 € le mètre.',
            'question' => 'Le prix total est-il proportionnel à la longueur de tissu achetée ?',
            'reponse' => true
        ],
        [
            'texte' => '',
            'question' => 'Le périmètre d\'un carré est-il proportionnel à la longueur de son côté ?',
            'reponse' => true
        ],
        [
            'texte' => '',
            'question' => 'Le périmètre d\'un cercle est-il proportionnel à son rayon ?',
            'reponse' => true
        ],
        [
            'texte' => '',
            'question' => 'Le périmètre d\'un cercle est-il proportionnel à son diamètre ?',
            'reponse' => true
        ],
        [
            'texte' => 'Sur un plan à l\'échelle 1/100, 1 cm sur le plan représente 1 m en réalité.',
            'question' => 'La longueur réelle est-elle proportionnelle à la longueur sur le plan ?',
            'reponse' => true
        ],
        [
            'texte' => 'Un robinet débite 15 litres d\'eau par minute.',
            'question' => 'Le volume d\'eau écoulé est-il proportionnel à la durée d\'ouverture du robinet ?',
            'reponse' => true
        ]
    ];
    
    $contextes_faux = [
        [
            'texte' => null, // Généré dynamiquement
            'question' => null, // Généré dynamiquement
            'reponse' => false,
            'type' => 'age_dynamique'
        ],
        [
            'texte' => '',
            'question' => 'L\'aire d\'un carré est-elle proportionnelle à la longueur de son côté ?',
            'reponse' => false
        ],
        [
            'texte' => '',
            'question' => 'L\'aire d\'un disque est-elle proportionnelle à son rayon ?',
            'reponse' => false
        ],
        [
            'texte' => '',
            'question' => 'L\'aire d\'un disque est-elle proportionnelle à son diamètre ?',
            'reponse' => false
        ],
        [
            'texte' => 'Un taxi facture 5 € de prise en charge puis 2 € par kilomètre parcouru.',
            'question' => 'Le prix total de la course est-il proportionnel à la distance parcourue ?',
            'reponse' => false
        ],
        [
            'texte' => '',
            'question' => 'Le volume d\'un cube est-il proportionnel à la longueur de son arête ?',
            'reponse' => false
        ],
        [
            'texte' => 'Dans un magasin, plus on achète d\'articles, plus le prix unitaire diminue (tarif dégressif).',
            'question' => 'Le prix total est-il proportionnel au nombre d\'articles achetés ?',
            'reponse' => false
        ],
        [
            'texte' => '',
            'question' => 'L\'aire d\'un carré est-elle proportionnelle à son périmètre ?',
            'reponse' => false
        ]
    ];
    
    // 50% VRAI, 50% FAUX
    if (rand(0, 1) == 1) {
        $contexte = $contextes_vrai[array_rand($contextes_vrai)];
    } else {
        $contexte = $contextes_faux[array_rand($contextes_faux)];
    }
    
    // Générer dynamiquement si type âge
    if (isset($contexte['type']) && $contexte['type'] === 'age_dynamique') {
        $prenoms = ['Lucas', 'Tom', 'Hugo', 'Léo', 'Louis', 'Arthur', 'Jules', 'Paul', 'Théo', 'Noah'];
        shuffle($prenoms);
        $prenom1 = $prenoms[0];
        $prenom2 = $prenoms[1];
        
        $ecart = rand(2, 10);
        
        $contexte['texte'] = $prenom1 . ' et ' . $prenom2 . ' sont frères. Ils ont ' . $ecart . ' ans d\'écart.';
        $contexte['question'] = 'Leurs âges sont-ils proportionnels ?';
    }
    
    // Générer dynamiquement si type achat
    if (isset($contexte['type']) && $contexte['type'] === 'achat_dynamique') {
        $produits = [
            ['nom' => 'pommes', 'feminin' => true],
            ['nom' => 'poires', 'feminin' => true],
            ['nom' => 'bananes', 'feminin' => true],
            ['nom' => 'noix', 'feminin' => true],
            ['nom' => 'figues', 'feminin' => true],
            ['nom' => 'oranges', 'feminin' => true],
            ['nom' => 'abricots', 'feminin' => false],
            ['nom' => 'kiwis', 'feminin' => false]
        ];
        
        $produit = $produits[array_rand($produits)];
        $prix = rand(215, 498) / 100; // Entre 2,15€ et 4,98€
        $prix_format = number_format($prix, 2, ',', '');
        
        // 50% prix fixe (VRAI), 50% offre promotionnelle (FAUX)
        if (rand(0, 1) == 1) {
            // Prix fixe - VRAI
            $article = $produit['feminin'] ? 'des' : 'des';
            $contexte['texte'] = 'Un commerçant vend ' . $article . ' ' . $produit['nom'] . ' à ' . $prix_format . ' € le kilogramme.';
            $article_masse = $produit['feminin'] ? 'la masse de ' . $produit['nom'] . ' achetée' : 'la masse de ' . $produit['nom'] . ' achetés';
            $contexte['question'] = 'Le prix total est-il proportionnel à ' . $article_masse . ' ?';
            $contexte['reponse'] = true;
        } else {
            // Offre promotionnelle - FAUX
            $nb_achetes = rand(3, 5);
            $nb_offerts = 1;
            $article = $produit['feminin'] ? 'des' : 'des';
            $contexte['texte'] = 'Un commerçant vend ' . $article . ' ' . $produit['nom'] . ' à ' . $prix_format . ' € le kg. Il offre ' . $nb_offerts . ' kg pour ' . $nb_achetes . ' kg achetés.';
            $contexte['question'] = 'Le prix total est-il proportionnel à la masse totale obtenue ?';
            $contexte['reponse'] = false;
        }
    }
    
    $q = '<p>' . $contexte['texte'] . '</p>';
    $q .= '<p><strong>' . $contexte['question'] . '</strong></p>';
    $q .= '<p><em>Répondre par Vrai ou Faux.</em></p>';
    
    $r = '<p><strong>' . ($contexte['reponse'] ? 'Vrai' : 'Faux') . '</strong></p>';
    
    return [
        'type' => 'reconnaitre_proportionnalite',
        'difficulte_id' => 1.4,
        'question' => $q,
        'reponse' => $r
    ];
}
?>
