<?php
/**
 * Automatisme DNB 2026 : Appliquer augmentation ou diminution en pourcentage
 * 
 * 12 contextes différents garantis (6 augmentations + 6 diminutions)
 */

function generer_pourcentages_augmentation($famille = '') {
    // Trois familles (les deux dernières écrites en août 2026) :
    //   appliquer  → les 12 contextes d'origine, sens direct
    //   retrouver  → le taux d'évolution, ou la valeur de départ
    //   successives→ deux évolutions enchaînées (le fameux « +10 % puis −10 % »)
    $par_famille = [
        'appliquer'   => ['aug_prix', 'aug_population', 'aug_salaire', 'aug_production',
                          'aug_effectif', 'aug_loyer', 'dim_soldes', 'dim_population',
                          'dim_consommation', 'dim_production', 'dim_effectif', 'dim_prix'],
        'retrouver'   => ['taux_evolution', 'valeur_initiale'],
        'successives' => ['successives'],
    ];
    if (isset($par_famille[$famille])) {
        $liste = $par_famille[$famille];
        return pa_construire($liste[array_rand($liste)]);
    }

    // Pool : les 12 contextes d'origine + les 3 nouveaux sous-types
    if (!isset($_SESSION['pourcentages_pool']) || empty($_SESSION['pourcentages_pool'])) {
        $_SESSION['pourcentages_pool'] = [
            'aug_prix', 'aug_population', 'aug_salaire', 'aug_production', 'aug_effectif', 'aug_loyer',
            'dim_soldes', 'dim_population', 'dim_consommation', 'dim_production', 'dim_effectif', 'dim_prix',
            'taux_evolution', 'taux_evolution', 'valeur_initiale', 'valeur_initiale', 'successives'
        ];
        shuffle($_SESSION['pourcentages_pool']);
    }
    
    $type = array_shift($_SESSION['pourcentages_pool']);

    return pa_construire($type);
}

/** Aiguillage d'un sous-type (extrait en août 2026). */
function pa_construire($type) {
    switch ($type) {
        case 'taux_evolution':  return pa_taux_evolution();
        case 'valeur_initiale': return pa_valeur_initiale();
        case 'successives':     return pa_successives();
    }

    // Séparer augmentation/diminution et contexte
    list($action, $contexte) = explode('_', $type);
    
    if ($action === 'aug') {
        return question_augmentation($contexte);
    } else {
        return question_diminution($contexte);
    }
}

/**
 * Retrouver le TAUX d'évolution à partir des deux valeurs.
 * Valeurs choisies pour que le pourcentage tombe rond.
 */
function pa_taux_evolution() {
    $taux = [10, 20, 25, 40, 50, -10, -20, -25, -50][rand(0, 8)];
    $coef = 1 + $taux / 100;
    // valeur de départ multiple de 20 : l'arrivée reste entière
    $depart = 20 * rand(2, 15);
    $arrivee = $depart * $coef;

    $contextes = [
        ['Le prix d\'un article', 'passe de', '€'],
        ['Le nombre d\'adhérents d\'un club', 'passe de', ''],
        ['La population d\'un village', 'passe de', 'habitants'],
    ];
    [$sujet, $verbe, $unite] = $contextes[array_rand($contextes)];
    $u = $unite ? ' ' . $unite : '';

    $q = '<p>' . $sujet . ' ' . $verbe . ' <strong>' . pa_nb($depart) . $u . '</strong> à <strong>'
       . pa_nb($arrivee) . $u . '</strong>.</p>'
       . '<p><strong>Quel est le pourcentage ' . ($taux > 0 ? 'd\'augmentation' : 'de diminution')
       . ' ?</strong></p>';

    $r = '<p><strong>' . ($taux > 0 ? 'Une augmentation de ' : 'Une diminution de ') . abs($taux) . ' %</strong></p>'
       . '<p style="font-size:0.9em; color:#666;">On calcule le coefficient multiplicateur : '
       . pa_nb($arrivee) . ' &divide; ' . pa_nb($depart) . ' = ' . number_format($coef, 2, ',', '') . '.</p>'
       . '<p style="font-size:0.9em; color:#666;">Ce coefficient vaut 1 ' . ($taux > 0 ? '+' : '&minus;') . ' '
       . abs($taux) . '/100, ce qui correspond à une ' . ($taux > 0 ? 'hausse' : 'baisse') . ' de '
       . abs($taux) . ' %.</p>';

    return ['type' => 'pourcentages_augmentation', 'difficulte_id' => 2.0, 'question' => $q, 'reponse' => $r];
}

/**
 * Retrouver la VALEUR INITIALE : on divise par le coefficient, on ne retire pas
 * le pourcentage — c'est l'erreur que la question vise.
 */
function pa_valeur_initiale() {
    $taux = [10, 20, 25, 50, -10, -20, -25][rand(0, 6)];
    $coef = 1 + $taux / 100;
    $depart  = 20 * rand(2, 12);
    $arrivee = $depart * $coef;

    $q = '<p>Après une ' . ($taux > 0 ? 'augmentation' : 'réduction') . ' de <strong>' . abs($taux)
       . ' %</strong>, un article coûte <strong>' . pa_nb($arrivee) . ' €</strong>.</p>'
       . '<p><strong>Quel était son prix avant ?</strong></p>';

    $r = '<p><strong>' . pa_nb($depart) . ' €</strong></p>'
       . '<p style="font-size:0.9em; color:#666;">Le coefficient multiplicateur est '
       . number_format($coef, 2, ',', '') . '. Pour revenir en arrière, on <strong>divise</strong> par ce coefficient :</p>'
       . '<p style="font-size:0.9em; color:#666;">' . pa_nb($arrivee) . ' &divide; '
       . number_format($coef, 2, ',', '') . ' = ' . pa_nb($depart) . ' €.</p>'
       . '<p style="font-size:0.9em; color:#666;">⚠️ On ne retire pas ' . abs($taux) . ' % du prix affiché : '
       . 'le pourcentage porte sur le prix de départ, qui est justement l\'inconnue.</p>';

    return ['type' => 'pourcentages_augmentation', 'difficulte_id' => 2.4, 'question' => $q, 'reponse' => $r];
}

/**
 * Deux évolutions successives : les coefficients se MULTIPLIENT, les
 * pourcentages ne s'additionnent pas.
 */
function pa_successives() {
    $paires = [
        [10, -10], [20, -20], [50, -50], [25, -20], [-10, 10], [20, 10], [-25, 25],
    ];
    [$t1, $t2] = $paires[array_rand($paires)];
    $c1 = 1 + $t1 / 100;
    $c2 = 1 + $t2 / 100;
    $depart = 100 * rand(1, 6);
    $inter  = $depart * $c1;
    $final  = $inter * $c2;
    $global = round(($c1 * $c2 - 1) * 100, 2);

    $mot = function ($t) { return ($t > 0 ? 'augmente de ' : 'diminue de ') . abs($t) . ' %'; };

    $q = '<p>Un article coûte <strong>' . pa_nb($depart) . ' €</strong>. Son prix ' . $mot($t1)
       . ', puis le nouveau prix ' . $mot($t2) . '.</p>'
       . '<p><strong>Quel est le prix final ?</strong></p>';

    $r = '<p><strong>' . pa_nb($final) . ' €</strong></p>'
       . '<p style="font-size:0.9em; color:#666;">Les coefficients se <strong>multiplient</strong> : '
       . number_format($c1, 2, ',', '') . ' × ' . number_format($c2, 2, ',', '') . ' = '
       . number_format($c1 * $c2, 4, ',', '') . '.</p>'
       . '<p style="font-size:0.9em; color:#666;">' . pa_nb($depart) . ' × '
       . number_format($c1 * $c2, 4, ',', '') . ' = ' . pa_nb($final) . ' €.</p>'
       . '<p style="font-size:0.9em; color:#666;">⚠️ Les pourcentages, eux, ne s\'additionnent pas : '
       . 'l\'évolution globale est de ' . ($global >= 0 ? '+' : '&minus;') . ' ' . pa_nb(abs($global)) . ' %'
       . ($t1 == -$t2 ? ', et non 0 % — on ne revient pas au prix de départ' : '') . '.</p>';

    return ['type' => 'pourcentages_augmentation', 'difficulte_id' => 2.6, 'question' => $q, 'reponse' => $r];
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    
    // Accord corrigé en août 2026 : la phrase disait « Une veste … Il est soldé »
    // et « Des chaussures … coûte ». Chaque article porte donc son verbe et son
    // pronom.
    $articles = [
        ['Un pantalon',    'coûte', 'Il est soldé'],
        ['Une veste',      'coûte', 'Elle est soldée'],
        ['Un pull',        'coûte', 'Il est soldé'],
        ['Des chaussures', 'coûtent', 'Elles sont soldées'],
        ['Un manteau',     'coûte', 'Il est soldé'],
    ];
    [$article, $verbe, $pronom] = $articles[array_rand($articles)];

    $q = '<p>' . $article . ' ' . $verbe . ' ' . $valeur_init . ' €. ' . $pronom
       . ' avec une réduction de ' . $pourcent['valeur'] . ' %.</p>';
    $q .= '<p><strong>Quel est le nouveau prix ?</strong></p>';
    
    $r = '<p><strong>' . number_format($resultat, 2, ',', '') . ' €</strong></p>';
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
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
    $r .= pa_detail($valeur_init, $pourcent, $resultat);
    
    return [
        'type' => 'pourcentages_augmentation',
        'difficulte_id' => $pourcent['diff'],
        'question' => $q,
        'reponse' => $r
    ];
}

/**
 * Détail de la méthode par coefficient multiplicateur, ajouté aux corrections
 * en août 2026 : elles se limitaient au résultat, ce qui n'apprend pas la
 * technique attendue au brevet.
 */
function pa_detail($valeur_init, $pourcent, $resultat) {
    $p    = $pourcent['valeur'];
    $coef = $pourcent['coef'];
    $hausse = ($coef > 1);
    $signe  = $hausse ? '+' : '&minus;';
    $calcul = $hausse ? '1 + ' . $p . '/100' : '1 &minus; ' . $p . '/100';
    return '<p style="font-size:0.9em; color:#666;">Coefficient multiplicateur : '
         . $calcul . ' = <strong>' . number_format($coef, 2, ',', '') . '</strong> ('
         . $signe . ' ' . $p . ' %).</p>'
         . '<p style="font-size:0.9em; color:#666;">On multiplie la valeur de départ : '
         . pa_nb($valeur_init) . ' × ' . number_format($coef, 2, ',', '') . ' = ' . pa_nb($resultat) . '.</p>';
}

/** Écriture française, sans zéros inutiles. */
function pa_nb($n) {
    if (abs($n - round($n)) < 0.0001) return (string)round($n);
    return rtrim(rtrim(number_format($n, 2, ',', ' '), '0'), ',');
}

?>
