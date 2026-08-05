<?php
/**
 * Automatisme DNB 2026 : Mobiliser procédure adaptée pour résoudre problème de proportionnalité
 * 
 * 2 types de questions :
 * - Tableaux à compléter (50%)
 * - Problèmes contextualisés (50%)
 */

function generer_procedures_proportionnalite() {
    // Pool principal : 6 tableaux + 6 problèmes
    if (!isset($_SESSION['procedures_proportionnalite_pool']) || empty($_SESSION['procedures_proportionnalite_pool'])) {
        $_SESSION['procedures_proportionnalite_pool'] = [
            'tableau', 'tableau', 'tableau', 'tableau', 'tableau', 'tableau',
            'probleme', 'probleme', 'probleme', 'probleme', 'probleme', 'probleme'
        ];
        shuffle($_SESSION['procedures_proportionnalite_pool']);
        
        // Sous-pool tableaux : 6 types différents
        $_SESSION['tableaux_procedures_pool'] = [
            'prix_masse', 'prix_quantite', 'recette', 'vitesse', 'echelle', 'volume_prix'
        ];
        shuffle($_SESSION['tableaux_procedures_pool']);
        
        // Sous-pool problèmes : 6 contextes différents
        $_SESSION['problemes_procedures_pool'] = [
            'achat_prix', 'recette_cuisine', 'vitesse_temps', 'photocopies', 'carburant', 'plan_echelle'
        ];
        shuffle($_SESSION['problemes_procedures_pool']);
    }
    
    $type = array_shift($_SESSION['procedures_proportionnalite_pool']);
    
    if ($type === 'tableau') {
        $type_tableau = array_shift($_SESSION['tableaux_procedures_pool']);
        return question_tableau_procedures($type_tableau);
    } else {
        $type_probleme = array_shift($_SESSION['problemes_procedures_pool']);
        return question_probleme_procedures($type_probleme);
    }
}

// ============================================
// TABLEAUX À COMPLÉTER
// ============================================

function question_tableau_procedures($type) {
    switch ($type) {
        case 'prix_masse': return tableau_prix_masse();
        case 'prix_quantite': return tableau_prix_quantite();
        case 'recette': return tableau_recette();
        case 'vitesse': return tableau_vitesse();
        case 'echelle': return tableau_echelle();
        case 'volume_prix': return tableau_volume_prix();
    }
}

function tableau_prix_masse() {
    $produits = [
        ['nom' => 'pommes', 'genre' => 'f'],
        ['nom' => 'tomates', 'genre' => 'f'],
        ['nom' => 'oranges', 'genre' => 'f'],
        ['nom' => 'bananes', 'genre' => 'f'],
        ['nom' => 'poires', 'genre' => 'f'],
        ['nom' => 'raisins', 'genre' => 'm']
    ];
    
    $produit = $produits[array_rand($produits)];
    
    $masse1 = rand(2, 5);
    $k = [2, 3, 4, 5][array_rand([2, 3, 4, 5])];
    $prix1 = $masse1 * $k;
    
    // 40% facile (×2 ou ×3), 60% moyen
    if (rand(1, 10) <= 4) {
        $multiplicateur = [2, 3][array_rand([2, 3])];
        $masse2 = $masse1 * $multiplicateur;
        $diff = 1.2;
    } else {
        $masse2 = rand(6, 12);
        while ($masse2 == $masse1 || ($masse2 % $masse1 == 0 && $masse2 / $masse1 <= 3)) {
            $masse2 = rand(6, 12);
        }
        $diff = 1.5;
    }
    
    $reponse = $masse2 * $k;
    
    $article = ($produit['genre'] == 'f') ? 'de' : 'de';
    
    $q = '<p>Voici un tableau de proportionnalité :</p>';
    $q .= '<table border="1" cellpadding="12" cellspacing="0" style="margin: 20px auto; border-collapse: collapse; font-size: 1.1em;">';
    $q .= '<tr style="background-color: #4CAF50; color: white;">';
    $q .= '<th style="padding: 12px; border: 1px solid #2e7d32;">Masse ' . $article . ' ' . $produit['nom'] . ' (kg)</th>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #2e7d32; font-weight: bold;">' . $masse1 . '</td>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #2e7d32; font-weight: bold;">' . $masse2 . '</td>';
    $q .= '</tr><tr>';
    $q .= '<th style="background-color: #e8f5e9; padding: 12px; border: 1px solid #ccc;">Prix (€)</th>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;">' . $prix1 . '</td>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc; font-weight: bold; color: #d32f2f;">?</td>';
    $q .= '</tr></table>';
    
    $q .= '<p><strong>Quel est le prix de ' . $masse2 . ' kg de ' . $produit['nom'] . ' ?</strong></p>';
    
    $r = '<p><strong>' . $reponse . ' €</strong></p>';
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function tableau_prix_quantite() {
    $objets = [
        ['nom' => 'cahiers', 'genre' => 'm'],
        ['nom' => 'stylos', 'genre' => 'm'],
        ['nom' => 'crayons', 'genre' => 'm'],
        ['nom' => 'gommes', 'genre' => 'f'],
        ['nom' => 'livres', 'genre' => 'm'],
        ['nom' => 'croissants', 'genre' => 'm']
    ];
    
    $objet = $objets[array_rand($objets)];
    
    $quantite1 = rand(2, 5);
    $k = [2, 3, 4, 5][array_rand([2, 3, 4, 5])];
    $prix1 = $quantite1 * $k;
    
    if (rand(1, 10) <= 4) {
        $multiplicateur = [2, 3][array_rand([2, 3])];
        $quantite2 = $quantite1 * $multiplicateur;
        $diff = 1.2;
    } else {
        $quantite2 = rand(6, 12);
        while ($quantite2 == $quantite1 || ($quantite2 % $quantite1 == 0 && $quantite2 / $quantite1 <= 3)) {
            $quantite2 = rand(6, 12);
        }
        $diff = 1.5;
    }
    
    $reponse = $quantite2 * $k;
    
    $q = '<p>Voici un tableau de proportionnalité :</p>';
    $q .= '<table border="1" cellpadding="12" cellspacing="0" style="margin: 20px auto; border-collapse: collapse; font-size: 1.1em;">';
    $q .= '<tr style="background-color: #4CAF50; color: white;">';
    $q .= '<th style="padding: 12px; border: 1px solid #2e7d32;">Nombre de ' . $objet['nom'] . '</th>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #2e7d32; font-weight: bold;">' . $quantite1 . '</td>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #2e7d32; font-weight: bold;">' . $quantite2 . '</td>';
    $q .= '</tr><tr>';
    $q .= '<th style="background-color: #e8f5e9; padding: 12px; border: 1px solid #ccc;">Prix (€)</th>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;">' . $prix1 . '</td>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc; font-weight: bold; color: #d32f2f;">?</td>';
    $q .= '</tr></table>';
    
    $q .= '<p><strong>Quel est le prix de ' . $quantite2 . ' ' . $objet['nom'] . ' ?</strong></p>';
    
    $r = '<p><strong>' . $reponse . ' €</strong></p>';
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function tableau_recette() {
    $ingredients = [
        ['nom' => 'farine', 'unite' => 'g', 'base' => [100, 150, 200, 250]],
        ['nom' => 'sucre', 'unite' => 'g', 'base' => [100, 150, 200]],
        ['nom' => 'beurre', 'unite' => 'g', 'base' => [50, 100, 150]],
        ['nom' => 'chocolat', 'unite' => 'g', 'base' => [100, 150, 200]],
        ['nom' => 'œufs', 'unite' => 'unités', 'base' => [2, 3, 4, 6]]
    ];
    
    $ingredient = $ingredients[array_rand($ingredients)];
    
    // Tirer jusqu'à une réponse ENTIÈRE (proportionnalité exacte, sans décimale
    // arrondie ni float brut « qui fuit » dans le corrigé).
    $tries = 0;
    do {
        $personnes1 = [2, 4, 5, 6][array_rand([2, 4, 5, 6])];
        $quantite1 = $ingredient['base'][array_rand($ingredient['base'])];

        if (rand(1, 10) <= 4) {
            $multiplicateur = [2, 3][array_rand([2, 3])];
            $personnes2 = $personnes1 * $multiplicateur;
            $diff = 1.2;
        } else {
            $personnes2 = rand(8, 12);
            while ($personnes2 == $personnes1 || ($personnes2 % $personnes1 == 0 && $personnes2 / $personnes1 <= 3)) {
                $personnes2 = rand(8, 12);
            }
            $diff = 1.5;
        }

        $reponse = ($quantite1 / $personnes1) * $personnes2;
    } while (abs($reponse - round($reponse)) > 1e-9 && ++$tries < 300);

    // Filet de sécurité : multiplicateur simple => réponse toujours entière
    if (abs($reponse - round($reponse)) > 1e-9) {
        $personnes2 = $personnes1 * 2;
        $reponse = $quantite1 * 2;
        $diff = 1.2;
    }
    $reponse = (int) round($reponse);
    
    $q = '<p>Voici un tableau de proportionnalité pour une recette :</p>';
    $q .= '<table border="1" cellpadding="12" cellspacing="0" style="margin: 20px auto; border-collapse: collapse; font-size: 1.1em;">';
    $q .= '<tr style="background-color: #4CAF50; color: white;">';
    $q .= '<th style="padding: 12px; border: 1px solid #2e7d32;">Nombre de personnes</th>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #2e7d32; font-weight: bold;">' . $personnes1 . '</td>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #2e7d32; font-weight: bold;">' . $personnes2 . '</td>';
    $q .= '</tr><tr>';
    $q .= '<th style="background-color: #e8f5e9; padding: 12px; border: 1px solid #ccc;">Quantité de ' . $ingredient['nom'] . ' (' . $ingredient['unite'] . ')</th>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;">' . $quantite1 . '</td>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc; font-weight: bold; color: #d32f2f;">?</td>';
    $q .= '</tr></table>';
    
    $q .= '<p><strong>Quelle quantité de ' . $ingredient['nom'] . ' faut-il pour ' . $personnes2 . ' personnes ?</strong></p>';
    
    $r = '<p><strong>' . $reponse . ' ' . $ingredient['unite'] . '</strong></p>';
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function tableau_vitesse() {
    $vitesse = [60, 80, 90, 100, 120][array_rand([60, 80, 90, 100, 120])];
    
    $temps = rand(2, 10);
    $distance = $vitesse * $temps;
    
    // Difficulté selon calcul
    if ($vitesse == 100 || $vitesse == 60) {
        $diff = 1.2; // ×100 ou ×60 simple
    } else {
        $diff = 1.5;
    }
    
    $vehicules = ['Une voiture', 'Un train', 'Un bus'];
    $vehicule = $vehicules[array_rand($vehicules)];
    $pronom = ($vehicule == 'Une voiture') ? 'elle' : 'il';
    
    $q = '<p>' . $vehicule . ' roule à vitesse constante de ' . $vitesse . ' km/h.</p>';
    $q .= '<p><strong>Quelle distance parcourt-' . $pronom . ' en ' . $temps . ' h ?</strong></p>';
    
    $r = '<p><strong>' . $distance . ' km</strong></p>';
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function tableau_echelle() {
    // Paires de villes réelles avec distances arrondies
    $paires_villes = [
        ['ville1' => 'Arras', 'ville2' => 'Lille', 'distance_km' => 50],
        ['ville1' => 'Arras', 'ville2' => 'Amiens', 'distance_km' => 60],
        ['ville1' => 'Arras', 'ville2' => 'Paris', 'distance_km' => 200],
        ['ville1' => 'Paris', 'ville2' => 'Lyon', 'distance_km' => 450],
        ['ville1' => 'Lille', 'ville2' => 'Paris', 'distance_km' => 220]
    ];
    
    $paire = $paires_villes[array_rand($paires_villes)];
    
    // Échelles réalistes pour cartes routières
    $echelles = [
        ['texte' => '1/1 000 000', 'facteur' => 10],  // 1 cm = 10 km
        ['texte' => '1/500 000', 'facteur' => 5],      // 1 cm = 5 km
        ['texte' => '1/200 000', 'facteur' => 2]       // 1 cm = 2 km
    ];
    
    $echelle = $echelles[array_rand($echelles)];
    
    // Calculer longueur sur carte pour distance réelle
    $long_carte_cm = $paire['distance_km'] / $echelle['facteur'];
    
    // 50% dans un sens, 50% dans l'autre
    if (rand(0, 1) == 0) {
        // SENS 1 : Longueur carte → Distance réelle
        $q = '<p>Sur une carte de France à l\'échelle ' . $echelle['texte'] . ', la distance entre ' . $paire['ville1'] . ' et ' . $paire['ville2'] . ' est de ' . $long_carte_cm . ' cm.</p>';
        $q .= '<p><strong>À quelle distance réelle cela correspond-il ?</strong></p>';
        
        $r = '<p><strong>' . $paire['distance_km'] . ' km</strong></p>';
        
    } else {
        // SENS 2 : Distance réelle → Longueur carte
        $q = '<p>Sur une carte de France à l\'échelle ' . $echelle['texte'] . ', la distance réelle entre ' . $paire['ville1'] . ' et ' . $paire['ville2'] . ' est de ' . $paire['distance_km'] . ' km.</p>';
        $q .= '<p><strong>Quelle est la longueur sur la carte ?</strong></p>';
        
        $r = '<p><strong>' . $long_carte_cm . ' cm</strong></p>';
    }
    
    // Difficulté selon échelle
    if ($echelle['facteur'] == 10) {
        $diff = 1.2;
    } else if ($echelle['facteur'] == 5) {
        $diff = 1.2;
    } else {
        $diff = 1.5;
    }
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function tableau_volume_prix() {
    $liquides = [
        ['nom' => 'essence', 'genre' => 'f'],
        ['nom' => 'eau', 'genre' => 'f'],
        ['nom' => 'lait', 'genre' => 'm'],
        ['nom' => 'jus', 'genre' => 'm']
    ];
    
    $liquide = $liquides[array_rand($liquides)];
    
    $volume1 = rand(2, 5);
    $k = [2, 3, 4, 5][array_rand([2, 3, 4, 5])];
    $prix1 = $volume1 * $k;
    
    if (rand(1, 10) <= 4) {
        $multiplicateur = [2, 3][array_rand([2, 3])];
        $volume2 = $volume1 * $multiplicateur;
        $diff = 1.2;
    } else {
        $volume2 = rand(6, 12);
        while ($volume2 == $volume1 || ($volume2 % $volume1 == 0 && $volume2 / $volume1 <= 3)) {
            $volume2 = rand(6, 12);
        }
        $diff = 1.5;
    }
    
    $reponse = $volume2 * $k;
    
    $article = ($liquide['genre'] == 'f') ? 'd\'' : 'de';
    
    $q = '<p>Voici un tableau de proportionnalité :</p>';
    $q .= '<table border="1" cellpadding="12" cellspacing="0" style="margin: 20px auto; border-collapse: collapse; font-size: 1.1em;">';
    $q .= '<tr style="background-color: #4CAF50; color: white;">';
    $q .= '<th style="padding: 12px; border: 1px solid #2e7d32;">Volume ' . $article . ' ' . $liquide['nom'] . ' (L)</th>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #2e7d32; font-weight: bold;">' . $volume1 . '</td>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #2e7d32; font-weight: bold;">' . $volume2 . '</td>';
    $q .= '</tr><tr>';
    $q .= '<th style="background-color: #e8f5e9; padding: 12px; border: 1px solid #ccc;">Prix (€)</th>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc;">' . $prix1 . '</td>';
    $q .= '<td style="text-align: center; padding: 12px; border: 1px solid #ccc; font-weight: bold; color: #d32f2f;">?</td>';
    $q .= '</tr></table>';
    
    $q .= '<p><strong>Quel est le prix de ' . $volume2 . ' L ' . $article . ' ' . $liquide['nom'] . ' ?</strong></p>';
    
    $r = '<p><strong>' . $reponse . ' €</strong></p>';
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// PROBLÈMES CONTEXTUALISÉS
// ============================================

function question_probleme_procedures($type) {
    switch ($type) {
        case 'achat_prix': return probleme_achat_prix();
        case 'recette_cuisine': return probleme_recette_cuisine();
        case 'vitesse_temps': return probleme_vitesse_temps();
        case 'photocopies': return probleme_photocopies();
        case 'carburant': return probleme_carburant();
        case 'plan_echelle': return probleme_plan_echelle();
    }
}

function probleme_achat_prix() {
    $produits = [
        ['nom' => 'pommes', 'genre' => 'f'],
        ['nom' => 'cahiers', 'genre' => 'm'],
        ['nom' => 'croissants', 'genre' => 'm'],
        ['nom' => 'stylos', 'genre' => 'm'],
        ['nom' => 'oranges', 'genre' => 'f'],
        ['nom' => 'bananes', 'genre' => 'f'],
        ['nom' => 'crayons', 'genre' => 'm'],
        ['nom' => 'tomates', 'genre' => 'f'],
        ['nom' => 'poires', 'genre' => 'f'],
        ['nom' => 'livres', 'genre' => 'm']
    ];
    
    $produit = $produits[array_rand($produits)];
    
    $quantite1 = rand(2, 5);
    $k = [2, 3, 4, 5][array_rand([2, 3, 4, 5])];
    $prix1 = $quantite1 * $k;
    
    if (rand(1, 10) <= 4) {
        $quantite2 = $quantite1 * [2, 3][array_rand([2, 3])];
        $diff = 1.2;
    } else {
        $quantite2 = rand(6, 12);
        $diff = 1.5;
    }
    
    $reponse = $quantite2 * $k;
    
    $verbe = ($produit['genre'] == 'f') ? 'coûtent' : 'coûtent';
    
    $q = '<p>' . $quantite1 . ' ' . $produit['nom'] . ' ' . $verbe . ' ' . $prix1 . ' €.</p>';
    $q .= '<p><strong>Quel est le prix de ' . $quantite2 . ' ' . $produit['nom'] . ' ?</strong></p>';
    
    $r = '<p><strong>' . $reponse . ' €</strong></p>';
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function probleme_recette_cuisine() {
    $ingredients = [
        ['nom' => 'farine', 'unite' => 'g', 'base' => [100, 150, 200, 250]],
        ['nom' => 'sucre', 'unite' => 'g', 'base' => [100, 150, 200]],
        ['nom' => 'beurre', 'unite' => 'g', 'base' => [50, 100, 150]],
        ['nom' => 'lait', 'unite' => 'mL', 'base' => [200, 250, 300]],
        ['nom' => 'œufs', 'unite' => 'unités', 'base' => [2, 3, 4, 6]],
        ['nom' => 'chocolat', 'unite' => 'g', 'base' => [100, 150, 200]]
    ];
    
    $ingredient = $ingredients[array_rand($ingredients)];
    
    // Tirer jusqu'à une réponse ENTIÈRE (proportionnalité exacte, sans float brut).
    $tries = 0;
    do {
        $personnes1 = [4, 6, 8][array_rand([4, 6, 8])];
        $quantite1 = $ingredient['base'][array_rand($ingredient['base'])];

        if (rand(1, 10) <= 4) {
            $personnes2 = $personnes1 * [2, 3][array_rand([2, 3])];
            $diff = 1.2;
        } else {
            $personnes2 = rand(10, 16);
            while ($personnes2 == $personnes1) {
                $personnes2 = rand(10, 16);
            }
            $diff = 1.5;
        }

        $reponse = ($quantite1 / $personnes1) * $personnes2;
    } while (abs($reponse - round($reponse)) > 1e-9 && ++$tries < 300);

    if (abs($reponse - round($reponse)) > 1e-9) {
        $personnes2 = $personnes1 * 2;
        $reponse = $quantite1 * 2;
        $diff = 1.2;
    }
    $reponse = (int) round($reponse);
    
    $q = '<p>Pour faire un gâteau pour ' . $personnes1 . ' personnes, il faut ' . $quantite1 . ' ' . $ingredient['unite'] . ' de ' . $ingredient['nom'] . '.</p>';
    $q .= '<p><strong>Quelle quantité de ' . $ingredient['nom'] . ' faut-il pour ' . $personnes2 . ' personnes ?</strong></p>';
    
    $r = '<p><strong>' . $reponse . ' ' . $ingredient['unite'] . '</strong></p>';
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function probleme_vitesse_temps() {
    $vehicules = ['voiture', 'train', 'vélo', 'bus'];
    $vehicule = $vehicules[array_rand($vehicules)];
    
    $vitesse = [60, 80, 90, 100, 120][array_rand([60, 80, 90, 100, 120])];
    $temps1 = rand(2, 4);
    $distance1 = $vitesse * $temps1;
    
    if (rand(1, 10) <= 4) {
        $temps2 = $temps1 * [2, 3][array_rand([2, 3])];
        $diff = 1.2;
    } else {
        $temps2 = rand(5, 10);
        while ($temps2 == $temps1) {
            $temps2 = rand(5, 10);
        }
        $diff = 1.5;
    }
    
    $reponse = $vitesse * $temps2;
    
    $article = ($vehicule == 'voiture') ? 'Une' : 'Un';
    $pronom = ($vehicule == 'voiture') ? 'elle' : 'il';
    
    $q = '<p>' . $article . ' ' . $vehicule . ' roule à vitesse constante de ' . $vitesse . ' km/h.</p>';
    $q .= '<p><strong>Quelle distance parcourt-' . $pronom . ' en ' . $temps2 . ' h ?</strong></p>';
    
    $r = '<p><strong>' . $reponse . ' km</strong></p>';
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function probleme_photocopies() {
    $pages1 = [20, 30, 40, 50][array_rand([20, 30, 40, 50])];
    $prix_unitaire = [0.05, 0.10][array_rand([0.05, 0.10])];
    $prix1 = $pages1 * $prix_unitaire;
    
    if (rand(1, 10) <= 4) {
        $pages2 = $pages1 * [2, 3][array_rand([2, 3])];
        $diff = 1.2;
    } else {
        $pages2 = rand(60, 120);
        while ($pages2 == $pages1 || $pages2 % 10 != 0) {
            $pages2 = rand(60, 120);
        }
        $diff = 1.5;
    }
    
    $reponse = $pages2 * $prix_unitaire;
    
    $q = '<p>À la photocopieuse, le prix à payer est proportionnel au nombre de photocopies.</p>';
    $q .= '<p>' . $pages1 . ' photocopies coûtent ' . number_format($prix1, 2, ',', '') . ' €.</p>';
    $q .= '<p><strong>Quel est le prix de ' . $pages2 . ' photocopies ?</strong></p>';
    
    $r = '<p><strong>' . number_format($reponse, 2, ',', '') . ' €</strong></p>';
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function probleme_carburant() {
    // Tirer jusqu'à une réponse ENTIÈRE (proportionnalité exacte, sans float brut).
    $tries = 0;
    do {
        $distance1 = [100, 150, 200][array_rand([100, 150, 200])];
        $litres1 = [5, 6, 8, 10][array_rand([5, 6, 8, 10])];

        if (rand(1, 10) <= 4) {
            $distance2 = $distance1 * [2, 3][array_rand([2, 3])];
            $diff = 1.2;
        } else {
            $distance2 = rand(300, 500);
            while ($distance2 == $distance1 || $distance2 % 50 != 0) {
                $distance2 = rand(300, 500);
            }
            $diff = 1.5;
        }

        $reponse = ($litres1 / $distance1) * $distance2;
    } while (abs($reponse - round($reponse)) > 1e-9 && ++$tries < 300);

    if (abs($reponse - round($reponse)) > 1e-9) {
        $distance2 = $distance1 * 2;
        $reponse = $litres1 * 2;
        $diff = 1.2;
    }
    $reponse = (int) round($reponse);
    
    $q = '<p>Une voiture consomme ' . $litres1 . ' L d\'essence pour parcourir ' . $distance1 . ' km.</p>';
    $q .= '<p><strong>Combien de litres consomme-t-elle pour parcourir ' . $distance2 . ' km ?</strong></p>';
    
    $r = '<p><strong>' . $reponse . ' L</strong></p>';
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}

function probleme_plan_echelle() {
    // Paires de villes réelles avec distances arrondies
    $paires_villes = [
        ['ville1' => 'Arras', 'ville2' => 'Lille', 'distance_km' => 50],
        ['ville1' => 'Arras', 'ville2' => 'Amiens', 'distance_km' => 60],
        ['ville1' => 'Arras', 'ville2' => 'Paris', 'distance_km' => 200],
        ['ville1' => 'Paris', 'ville2' => 'Lyon', 'distance_km' => 450],
        ['ville1' => 'Lille', 'ville2' => 'Paris', 'distance_km' => 220]
    ];
    
    $paire = $paires_villes[array_rand($paires_villes)];
    
    // Échelles réalistes
    $echelles = [
        ['texte' => '1/1 000 000', 'facteur' => 10],  // 1 cm = 10 km
        ['texte' => '1/500 000', 'facteur' => 5],      // 1 cm = 5 km
        ['texte' => '1/200 000', 'facteur' => 2]       // 1 cm = 2 km
    ];
    
    $echelle = $echelles[array_rand($echelles)];
    
    // Calculer la longueur sur carte pour la distance réelle de la paire
    $long_carte_cm = $paire['distance_km'] / $echelle['facteur'];
    
    // Vérifier que c'est un nombre entier simple
    if ($long_carte_cm != floor($long_carte_cm) || $long_carte_cm < 2 || $long_carte_cm > 50) {
        // Si pas bon, forcer 1/1 000 000
        $echelle = $echelles[0];
        $long_carte_cm = $paire['distance_km'] / $echelle['facteur'];
    }
    
    // 50% dans un sens, 50% dans l'autre
    if (rand(0, 1) == 0) {
        // SENS 1 : Longueur carte → Distance réelle
        $q = '<p>Sur une carte de France à l\'échelle ' . $echelle['texte'] . ', la distance entre ' . $paire['ville1'] . ' et ' . $paire['ville2'] . ' est de ' . $long_carte_cm . ' cm.</p>';
        $q .= '<p><strong>À quelle distance réelle cela correspond-il ?</strong></p>';
        
        $r = '<p><strong>' . $paire['distance_km'] . ' km</strong></p>';
    } else {
        // SENS 2 : Distance réelle → Longueur carte
        $q = '<p>Sur une carte de France à l\'échelle ' . $echelle['texte'] . ', la distance réelle entre ' . $paire['ville1'] . ' et ' . $paire['ville2'] . ' est de ' . $paire['distance_km'] . ' km.</p>';
        $q .= '<p><strong>Quelle est la longueur sur la carte ?</strong></p>';
        
        $r = '<p><strong>' . $long_carte_cm . ' cm</strong></p>';
    }
    
    // Difficulté selon calcul
    if ($echelle['facteur'] == 10) {
        $diff = 1.2; // ×10 ou ÷10 très simple
    } else if ($echelle['facteur'] == 5) {
        $diff = 1.2; // ×5 ou ÷5 simple
    } else {
        $diff = 1.5; // ×2 ou ÷2 un peu plus difficile
    }
    
    return [
        'type' => 'procedures_proportionnalite',
        'difficulte_id' => $diff,
        'question' => $q,
        'reponse' => $r
    ];
}
?>
