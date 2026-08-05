<?php
/**
 * Automatisme DNB 2026 : Appliquer augmentation ou diminution en pourcentage
 * 
 * 12 contextes différents garantis (6 augmentations + 6 diminutions)
 */

function generer_pourcentages_augmentation() {
    // Pool 12 contextes différents
    if (!isset($_SESSION['pourcentages_pool']) || empty($_SESSION['pourcentages_pool'])) {
        $_SESSION['pourcentages_pool'] = [
            'aug_prix', 'aug_population', 'aug_salaire', 'aug_production', 'aug_effectif', 'aug_loyer',
            'dim_soldes', 'dim_population', 'dim_consommation', 'dim_production', 'dim_effectif', 'dim_prix'
        ];
        shuffle($_SESSION['pourcentages_pool']);
    }
    
    $type = array_shift($_SESSION['pourcentages_pool']);
    
    // Séparer augmentation/diminution et contexte
    list($action, $contexte) = explode('_', $type);
    
    if ($action === 'aug') {
        return question_augmentation($contexte);
    } else {
        return question_diminution($contexte);
    }
}

// ============================================
// AUGMENTATIONS
// ============================================

function question_augmentation($contexte) {
    // Pourcentages selon difficulté
    $pourcentages = [
        'facile' => [
            ['valeur' => 10, 'coef' => 1.10, 'diff' => 1.0],
            ['valeur' => 50, 'coef' => 1.50, 'diff' => 1.0],
            ['valeur' => 100, 'coef' => 2.00, 'diff' => 1.0]
        ],
        'moyen' => [
            ['valeur' => 20, 'coef' => 1.20, 'diff' => 1.2],
            ['valeur' => 25, 'coef' => 1.25, 'diff' => 1.2],
            ['valeur' => 75, 'coef' => 1.75, 'diff' => 1.2],
            ['valeur' => 90, 'coef' => 1.90, 'diff' => 1.2]
        ],
        'difficile' => [
            ['valeur' => 30, 'coef' => 1.30, 'diff' => 1.5],
            ['valeur' => 40, 'coef' => 1.40, 'diff' => 1.5]
        ]
    ];
    
    // Répartition : 40% facile, 40% moyen, 20% difficile
    $rand = rand(1, 10);
    if ($rand <= 4) {
        $niveau = 'facile';
    } elseif ($rand <= 8) {
        $niveau = 'moyen';
    } else {
        $niveau = 'difficile';
    }
    
    $pourcent = $pourcentages[$niveau][array_rand($pourcentages[$niveau])];
    
    switch ($contexte) {
        case 'prix':
            return aug_prix($pourcent);
        case 'population':
            return aug_population($pourcent);
        case 'salaire':
            return aug_salaire($pourcent);
        case 'production':
            return aug_production($pourcent);
        case 'effectif':
            return aug_effectif($pourcent);
        case 'loyer':
            return aug_loyer($pourcent);
    }
}

function aug_prix($pourcent) {
    $valeur_init = rand(20, 150);
    $resultat = $valeur_init * $pourcent['coef'];
    
    $articles = ['Un article', 'Un livre', 'Un jouet', 'Un téléphone', 'Un ordinateur', 'Une montre'];
    $article = $articles[array_rand($articles)];
    
    $q = '<p>' . $article . ' coûte ' . $valeur_init . ' €. Son prix augmente de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Quel est le nouveau prix ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 2, ',', '') . ' €</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

function aug_population($pourcent) {
    $valeur_init = rand(5, 20) * 1000; // Multiples de 1000
    $resultat = $valeur_init * $pourcent['coef'];
    
    $villes = ['Une ville', 'Un village', 'Une commune'];
    $ville = $villes[array_rand($villes)];
    
    $q = '<p>' . $ville . ' compte ' . number_format($valeur_init, 0, ',', ' ') . ' habitants. Sa population augmente de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Combien d\'habitants compte-t-elle maintenant ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 0, ',', ' ') . ' habitants</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

function aug_salaire($pourcent) {
    $valeur_init = rand(15, 30) * 100; // Multiples de 100
    $resultat = $valeur_init * $pourcent['coef'];
    
    $q = '<p>Un salaire mensuel est de ' . number_format($valeur_init, 0, ',', ' ') . ' €. Il augmente de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Quel est le nouveau salaire ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 2, ',', ' ') . ' €</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

function aug_production($pourcent) {
    $valeur_init = rand(200, 800);
    $resultat = $valeur_init * $pourcent['coef'];
    
    $produits = ['voitures', 'ordinateurs', 'téléphones', 'articles'];
    $produit = $produits[array_rand($produits)];
    
    $q = '<p>Une usine produit ' . $valeur_init . ' ' . $produit . ' par mois. Sa production augmente de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Quelle est la nouvelle production mensuelle ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 0, ',', ' ') . ' ' . $produit . '</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

function aug_effectif($pourcent) {
    $valeur_init = rand(200, 600);
    $resultat = $valeur_init * $pourcent['coef'];
    
    $etablissements = [
        ['nom' => 'Un collège', 'type' => 'élèves'],
        ['nom' => 'Une école', 'type' => 'élèves'],
        ['nom' => 'Un lycée', 'type' => 'élèves']
    ];
    $etab = $etablissements[array_rand($etablissements)];
    
    $q = '<p>' . $etab['nom'] . ' compte ' . $valeur_init . ' ' . $etab['type'] . '. L\'effectif augmente de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Quel est le nouvel effectif ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 0, ',', ' ') . ' ' . $etab['type'] . '</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

function aug_loyer($pourcent) {
    $valeur_init = rand(400, 1000);
    $resultat = $valeur_init * $pourcent['coef'];
    
    $q = '<p>Un loyer mensuel est de ' . $valeur_init . ' €. Il augmente de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Quel est le nouveau loyer ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 2, ',', '') . ' €</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

// ============================================
// DIMINUTIONS
// ============================================

function question_diminution($contexte) {
    // Pourcentages selon difficulté
    $pourcentages = [
        'facile' => [
            ['valeur' => 10, 'coef' => 0.90, 'diff' => 1.0],
            ['valeur' => 50, 'coef' => 0.50, 'diff' => 1.0],
            ['valeur' => 100, 'coef' => 0.00, 'diff' => 1.0]
        ],
        'moyen' => [
            ['valeur' => 20, 'coef' => 0.80, 'diff' => 1.2],
            ['valeur' => 25, 'coef' => 0.75, 'diff' => 1.2],
            ['valeur' => 75, 'coef' => 0.25, 'diff' => 1.2],
            ['valeur' => 90, 'coef' => 0.10, 'diff' => 1.2]
        ],
        'difficile' => [
            ['valeur' => 30, 'coef' => 0.70, 'diff' => 1.5],
            ['valeur' => 40, 'coef' => 0.60, 'diff' => 1.5]
        ]
    ];
    
    // Répartition : 40% facile, 40% moyen, 20% difficile
    $rand = rand(1, 10);
    if ($rand <= 4) {
        $niveau = 'facile';
    } elseif ($rand <= 8) {
        $niveau = 'moyen';
    } else {
        $niveau = 'difficile';
    }
    
    $pourcent = $pourcentages[$niveau][array_rand($pourcentages[$niveau])];
    
    switch ($contexte) {
        case 'soldes':
            return dim_soldes($pourcent);
        case 'population':
            return dim_population($pourcent);
        case 'consommation':
            return dim_consommation($pourcent);
        case 'production':
            return dim_production($pourcent);
        case 'effectif':
            return dim_effectif($pourcent);
        case 'prix':
            return dim_prix($pourcent);
    }
}

function dim_soldes($pourcent) {
    $valeur_init = rand(40, 200);
    $resultat = $valeur_init * $pourcent['coef'];
    
    $articles = ['Un pantalon', 'Une veste', 'Un pull', 'Des chaussures', 'Un manteau'];
    $article = $articles[array_rand($articles)];
    
    $q = '<p>' . $article . ' coûte ' . $valeur_init . ' €. Il est soldé avec une réduction de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Quel est le nouveau prix ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 2, ',', '') . ' €</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

function dim_population($pourcent) {
    $valeur_init = rand(8, 25) * 1000;
    $resultat = $valeur_init * $pourcent['coef'];
    
    $q = '<p>Une ville comptait ' . number_format($valeur_init, 0, ',', ' ') . ' habitants. Sa population diminue de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Combien d\'habitants reste-t-il ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 0, ',', ' ') . ' habitants</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

function dim_consommation($pourcent) {
    $valeur_init = rand(100, 500);
    $resultat = $valeur_init * $pourcent['coef'];
    
    $types = [
        ['nom' => 'eau', 'unite' => 'L'],
        ['nom' => 'électricité', 'unite' => 'kWh'],
        ['nom' => 'gaz', 'unite' => 'm³']
    ];
    $type = $types[array_rand($types)];
    
    $q = '<p>Une consommation mensuelle d\'' . $type['nom'] . ' est de ' . $valeur_init . ' ' . $type['unite'] . '. Elle diminue de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Quelle est la nouvelle consommation ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 2, ',', '') . ' ' . $type['unite'] . '</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

function dim_production($pourcent) {
    $valeur_init = rand(400, 1000);
    $resultat = $valeur_init * $pourcent['coef'];
    
    $produits = ['pièces', 'articles', 'composants'];
    $produit = $produits[array_rand($produits)];
    
    $q = '<p>Une usine produit ' . $valeur_init . ' ' . $produit . ' par jour. Sa production diminue de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Quelle est la nouvelle production quotidienne ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 0, ',', ' ') . ' ' . $produit . '</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

function dim_effectif($pourcent) {
    $valeur_init = rand(80, 300);
    $resultat = $valeur_init * $pourcent['coef'];
    
    $orgas = [
        ['nom' => 'Un club de sport', 'type' => 'membres'],
        ['nom' => 'Une association', 'type' => 'adhérents'],
        ['nom' => 'Un club de théâtre', 'type' => 'membres']
    ];
    $orga = $orgas[array_rand($orgas)];
    
    $q = '<p>' . $orga['nom'] . ' comptait ' . $valeur_init . ' ' . $orga['type'] . '. L\'effectif diminue de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Combien de ' . $orga['type'] . ' restent-ils ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 0, ',', ' ') . ' ' . $orga['type'] . '</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

function dim_prix($pourcent) {
    $valeur_init = rand(50, 200);
    $resultat = $valeur_init * $pourcent['coef'];
    
    $produits = ['Le prix de l\'essence', 'Le prix du pain', 'Le prix des fruits', 'Le prix du carburant'];
    $produit = $produits[array_rand($produits)];
    
    $q = '<p>' . $produit . ' était de ' . number_format($valeur_init, 2, ',', '') . ' €. Il diminue de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Quel est le nouveau prix ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 2, ',', '') . ' €</strong></p>';
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}
?>
