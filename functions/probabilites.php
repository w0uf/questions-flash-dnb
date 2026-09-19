<?php
/**
 * Automatisme DNB 2026 : Probabilités (équiprobabilité)
 * SANS QCM - Réponse directe en fraction irréductible ou décimale
 */

require_once __DIR__ . '/utils.php';   // fraction() — dépendance explicite

function generer_probabilites($famille = '') {
    // Filtre optionnel de famille, traité avant le pool historique : l'appel
    // sans argument (session DNB) est inchangé.
    $par_famille = [
        'de'      => ['de_issue', 'de_evenement'],
        'sac'     => ['sac_boules'],
        'roue'    => ['roue_secteurs'],
        'lettres' => ['lettres_mot', 'lettres_deux_mots'],
    ];
    if (isset($par_famille[$famille])) {
        $liste = $par_famille[$famille];
        return proba_construire($liste[array_rand($liste)]);
    }

    if (!isset($_SESSION['probabilites_pool']) || empty($_SESSION['probabilites_pool'])) {
        $_SESSION['probabilites_pool'] = [
            'de_issue', 'de_issue',
            'de_evenement', 'de_evenement', 'de_evenement',
            'sac_boules', 'sac_boules', 'sac_boules',
            'roue_secteurs', 'roue_secteurs',
            'lettres_mot', 'lettres_mot',
            'lettres_deux_mots'
        ];
        shuffle($_SESSION['probabilites_pool']);
    }
    
    $type = array_shift($_SESSION['probabilites_pool']);

    return proba_construire($type);
}

/** Aiguillage d'un sous-type vers son générateur (extrait en août 2026). */
function proba_construire($type) {
    switch ($type) {
        case 'de_issue': return generer_proba_de_issue();
        case 'de_evenement': return generer_proba_de_evenement();
        case 'sac_boules': return generer_proba_sac();
        case 'roue_secteurs': return generer_proba_roue();
        case 'lettres_mot': return generer_proba_lettres();
        case 'lettres_deux_mots': return generer_proba_lettres_deux_mots();
    }
}

function generer_proba_de_issue() {
    $des = [[6, 1.0], [8, 1.2], [10, 1.3], [12, 1.4], [20, 1.5]];
    list($faces, $diff) = $des[array_rand($des)];
    $nb = rand(1, $faces);
    
    $q = '<p>On lance un dé équilibré à ' . $faces . ' faces numérotées de 1 à ' . $faces . '.</p>';
    $q .= '<p><strong>Quelle est la probabilité d\'obtenir ' . $nb . ' ?</strong></p>';
    
    // 50% pourcentage, 50% fraction
    $format_pourcent = (rand(0,1) == 1) && in_array($faces, [2, 4, 5, 10, 20, 25, 50, 100]);
    
    if ($format_pourcent) {
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse en POURCENTAGE.</strong></p>';
        $pourcent = (1 / $faces) * 100;
        $r = '<p><strong>' . rtrim(rtrim(number_format($pourcent, 1, ',', ''), '0'), ',') . ' %</strong></p>';
    } else {
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse sous forme de FRACTION IRRÉDUCTIBLE.</strong></p>';
        $r = '<p><strong>' . fraction(1, $faces) . '</strong></p>';
    }
    
    return ['type' => 'probabilites', 'difficulte_id' => $diff, 'question' => $q, 'reponse' => $r];
}

function generer_proba_de_evenement() {
    $evts = [
        ['un nombre pair', [2,4,6], 3],
        ['un nombre impair', [1,3,5], 3],
        ['un nombre strictement supérieur à 4', [5,6], 2],
        ['un nombre inférieur ou égal à 2', [1,2], 2],
        ['un multiple de 3', [3,6], 2],
        ['un nombre strictement supérieur à 2', [3,4,5,6], 4]
    ];
    
    list($desc, $issues, $nb) = $evts[array_rand($evts)];
    
    $q = '<p>On lance un dé équilibré à 6 faces numérotées de 1 à 6.</p>';
    $q .= '<p><strong>Quelle est la probabilité d\'obtenir ' . $desc . ' ?</strong></p>';
    
    $pgcd = gcd($nb, 6);
    $num = $nb / $pgcd;
    $denom = 6 / $pgcd;
    
    // 50% pourcentage, 50% fraction
    $format_pourcent = (rand(0,1) == 1) && in_array($denom, [2, 4, 5, 10, 20, 25, 50, 100]);
    
    if ($format_pourcent) {
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse en POURCENTAGE.</strong></p>';
        $pourcent = ($num / $denom) * 100;
        $r = '<p><strong>' . rtrim(rtrim(number_format($pourcent, 1, ',', ''), '0'), ',') . ' %</strong></p>';
    } else {
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse sous forme de FRACTION IRRÉDUCTIBLE.</strong></p>';
        $r = '<p><strong>' . fraction($num, $denom) . '</strong></p>';
    }
    
    $r .= '<p style="font-size: 0.9em; color: #666;">Issues : ' . implode(', ', $issues) . '</p>';
    
    return ['type' => 'probabilites', 'difficulte_id' => 1.6, 'question' => $q, 'reponse' => $r];
}

function generer_proba_sac() {
    // Configurations variées
    $cfgs = [
        // Total 10
        [[1,9], 'rouge', 'bleu'], [[2,8], 'rouge', 'bleu'], [[3,7], 'rouge', 'bleu'],
        [[4,6], 'rouge', 'bleu'], [[5,5], 'rouge', 'bleu'], [[6,4], 'rouge', 'bleu'],
        [[7,3], 'rouge', 'bleu'], [[8,2], 'rouge', 'bleu'], [[9,1], 'rouge', 'bleu'],
        
        // Total 4
        [[1,3], 'rouge', 'bleu'], [[2,2], 'rouge', 'bleu'], [[3,1], 'rouge', 'bleu'],
        
        // Total 5
        [[1,4], 'rouge', 'bleu'], [[2,3], 'rouge', 'bleu'], [[3,2], 'rouge', 'bleu'],
        
        // Total 20
        [[5,15], 'rouge', 'bleu'], [[10,10], 'rouge', 'bleu'],
        
        // Autres
        [[3,5], 'rouge', 'bleu'], [[4,6], 'rouge', 'vert'], [[2,4,3], 'rouge', 'bleu', 'vert']
    ];
    
    $cfg = $cfgs[array_rand($cfgs)];
    $boules = array_shift($cfg);
    $couleurs_noms = $cfg;
    
    // Construire config
    $config = [];
    foreach ($boules as $i => $nb) {
        $config[$couleurs_noms[$i]] = $nb;
    }
    
    $couleurs = array_keys($config);
    $cible = $couleurs[array_rand($couleurs)];
    $total = array_sum($config);
    $nb = $config[$cible];
    
    $q = '<p>Un sac contient ';
    $parts = [];
    foreach ($config as $c => $n) {
        $parts[] = $n . ' boule' . ($n > 1 ? 's' : '') . ' ' . proba_couleur_accordee($c, $n);
    }
    $q .= implode(', ', $parts) . '.</p>';
    $q .= '<p><strong>On tire une boule au hasard. Quelle est la probabilité de tirer une boule '
        . proba_couleur_accordee($cible, 1) . ' ?</strong></p>';
    
    // Simplifier
    $pgcd = gcd($nb, $total);
    $num = $nb / $pgcd;
    $denom = $total / $pgcd;
    
    // 50% pourcentage, 50% fraction
    $format_pourcent = (rand(0,1) == 1) && in_array($denom, [2, 4, 5, 10, 20, 25, 50, 100]);
    
    if ($format_pourcent) {
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse en POURCENTAGE.</strong></p>';
        $pourcent = ($num / $denom) * 100;
        $resp = '<p><strong>' . rtrim(rtrim(number_format($pourcent, 1, ',', ''), '0'), ',') . ' %</strong></p>';
    } else {
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse sous forme de FRACTION IRRÉDUCTIBLE.</strong></p>';
        $resp = '<p><strong>' . fraction($num, $denom) . '</strong></p>';
    }
    
    return ['type' => 'probabilites', 'difficulte_id' => 1.5, 'question' => $q, 'reponse' => $resp];
}

function generer_proba_roue() {
    $cfgs = [
        [8, 3, 2, 3, 1.5],
        [12, 4, 3, 5, 1.7],
        [10, 2, 3, 5, 1.6],
        [6, 2, 2, 2, 1.4]
    ];
    
    list($total, $r, $b, $v, $diff) = $cfgs[array_rand($cfgs)];
    
    $couleurs = ['rouge' => $r, 'bleu' => $b, 'vert' => $v];
    $cible = array_rand($couleurs);
    $nb = $couleurs[$cible];
    
    $svg = generer_svg_roue($total, $r, $b, $v, $cible);
    
    $q = '<div style="text-align: center;">' . $svg;
    $q .= '<p style="margin-top: 20px;">On fait tourner cette roue.</p>';
    $q .= '<p><strong>Quelle est la probabilité que la flèche s\'arrête sur un secteur ' . $cible . ' ?</strong></p>';
    
    $pgcd = gcd($nb, $total);
    $num = $nb / $pgcd;
    $denom = $total / $pgcd;
    
    // 50% pourcentage, 50% fraction
    $format_pourcent = (rand(0,1) == 1) && in_array($denom, [2, 4, 5, 10, 20, 25, 50, 100]);
    
    if ($format_pourcent) {
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse en POURCENTAGE.</strong></p></div>';
        $pourcent = ($num / $denom) * 100;
        $resp = '<p><strong>' . rtrim(rtrim(number_format($pourcent, 1, ',', ''), '0'), ',') . ' %</strong></p>';
    } else {
        $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse sous forme de FRACTION IRRÉDUCTIBLE.</strong></p></div>';
        $resp = '<p><strong>' . fraction($num, $denom) . '</strong></p>';
    }
    
    return ['type' => 'probabilites', 'difficulte_id' => $diff, 'question' => $q, 'reponse' => $resp];
}

function generer_proba_lettres() {
    $mots = [
        ['BATEAU', 'R', 0], ['SOLEIL', 'A', 0], ['MAISON', 'E', 0],
        ['MAMAN', 'M', [2,5]], ['PAPA', 'A', [1,2]], ['BANANE', 'A', [1,2]],
        ['ANANAS', 'A', [1,2]], ['CINEMA', 'A', [1,6]], ['BONBON', 'O', [1,3]]
    ];
    
    list($mot, $lettre, $proba) = $mots[array_rand($mots)];
    
    $q = '<p>On tire au hasard une lettre du mot <strong>' . $mot . '</strong>.</p>';
    $q .= '<p><strong>Quelle est la probabilité d\'obtenir la lettre ' . $lettre . ' ?</strong></p>';
    $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse sous forme de FRACTION IRRÉDUCTIBLE.</strong></p>';
    
    if ($proba === 0) {
        $resp = '<p><strong>0</strong></p>';
    } else {
        $resp = '<p><strong>' . fraction($proba[0], $proba[1]) . '</strong></p>';
    }
    
    return ['type' => 'probabilites', 'difficulte_id' => 1.4, 'question' => $q, 'reponse' => $resp];
}

function generer_proba_lettres_deux_mots() {
    // Liste de mots courants
    $mots = [
        'CHAT', 'CHIEN', 'SOURIS', 'LAPIN', 'OISEAU', 'POULE', 'VACHE', 'MOUTON',
        'MAISON', 'JARDIN', 'FLEUR', 'ARBRE', 'SOLEIL', 'LUNE', 'ETOILE', 'NUAGE',
        'TABLE', 'CHAISE', 'LIT', 'PORTE', 'FENETRE', 'LAMPE', 'LIVRE', 'STYLO',
        'PAIN', 'EAU', 'LAIT', 'FROMAGE', 'POMME', 'BANANE', 'ORANGE', 'FRAISE',
        'ROUGE', 'BLEU', 'VERT', 'JAUNE', 'NOIR', 'BLANC', 'ROSE', 'GRIS',
        'LUNDI', 'MARDI', 'SAMEDI', 'JANVIER', 'MARS', 'AVRIL', 'JUILLET', 'OCTOBRE',
        'UN', 'DEUX', 'TROIS', 'QUATRE', 'CINQ', 'SIX', 'SEPT', 'HUIT', 'NEUF', 'DIX',
        'ECOLE', 'CLASSE', 'CAHIER', 'CRAYON', 'GOMME', 'REGLE', 'COLLE', 'CISEAU',
        'VOITURE', 'VELO', 'TRAIN', 'AVION', 'BATEAU', 'MOTO', 'BUS', 'CAMION',
        'CHAUD', 'FROID', 'GRAND', 'PETIT', 'LONG', 'COURT', 'HAUT', 'BAS',
        'PAPA', 'MAMAN', 'BEBE', 'FILLE', 'GARCON', 'FAMILLE', 'AMI', 'COPAIN',
        'PLAGE', 'MER', 'SABLE', 'VAGUE', 'COQUILLAGE', 'CHATEAU', 'PELLE', 'SEAU',
        'FOOT', 'TENNIS', 'PISCINE', 'SKI', 'VELO', 'COURSE', 'SAUT', 'JEU'
    ];
    
    // Tirer deux mots au hasard
    shuffle($mots);
    $mot1 = $mots[0];
    $mot2 = $mots[1];
    
    // S'assurer que les mots sont différents
    while ($mot1 === $mot2) {
        shuffle($mots);
        $mot2 = $mots[1];
    }
    
    // Calculer la probabilité
    list($num, $denom, $lettres_communes) = calculer_proba_lettres($mot1, $mot2);
    
    $q = '<p>On tire au hasard une lettre du mot <strong>' . $mot1 . '</strong>.</p>';
    $q .= '<p><strong>Quelle est la probabilité d\'obtenir une lettre du mot ' . $mot2 . ' ?</strong></p>';
    $q .= '<p style="font-size: 1.1em; margin-top: 15px;"><strong style="background-color: #ffffcc; padding: 5px;">⚠️ Donner la réponse sous forme de FRACTION IRRÉDUCTIBLE.</strong></p>';
    
    if ($num == 0) {
        $resp = '<p><strong>0</strong></p>';
        $resp .= '<p style="font-size: 0.9em; color: #666;">Aucune lettre commune.</p>';
    } elseif ($num == $denom) {
        $resp = '<p><strong>1</strong></p>';
        $resp .= '<p style="font-size: 0.9em; color: #666;">Toutes les lettres de ' . $mot1 . ' sont dans ' . $mot2 . '.</p>';
    } else {
        $pgcd = gcd($num, $denom);
        $resp = '<p><strong>' . fraction($num/$pgcd, $denom/$pgcd) . '</strong></p>';
        $resp .= '<p style="font-size: 0.9em; color: #666;">Lettres communes : ' . implode(', ', $lettres_communes) . '</p>';
    }
    
    return ['type' => 'probabilites', 'difficulte_id' => 1.9, 'question' => $q, 'reponse' => $resp];
}

/**
 * Calculer la probabilité d'obtenir une lettre du mot2 en tirant dans mot1
 * Retourne [numérateur, dénominateur, lettres_communes]
 */
function calculer_proba_lettres($mot1, $mot2) {
    // Convertir en tableaux de lettres uniques
    $lettres_mot2 = array_unique(str_split($mot2));
    $lettres_mot1 = str_split($mot1);
    
    // Compter combien de lettres de mot1 sont dans mot2
    $nb_favorables = 0;
    $lettres_communes = [];
    
    foreach ($lettres_mot1 as $lettre) {
        if (in_array($lettre, $lettres_mot2)) {
            $nb_favorables++;
            if (!in_array($lettre, $lettres_communes)) {
                $lettres_communes[] = $lettre;
            }
        }
    }
    
    $total = count($lettres_mot1);
    
    sort($lettres_communes);
    
    return [$nb_favorables, $total, $lettres_communes];
}

function generer_svg_roue($total, $r, $b, $v, $cible) {
    $svg = '<svg viewBox="0 0 350 350" width="350" height="350" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block; max-width:100%; height:auto;">';
    
    $cx = 175; $cy = 175; $rayon = 120;
    $cols = ['rouge' => '#ff0000', 'bleu' => '#0066cc', 'vert' => '#00aa00'];
    
    // Créer secteurs
    $secteurs = [];
    foreach (['rouge' => $r, 'bleu' => $b, 'vert' => $v] as $c => $n) {
        for ($i = 0; $i < $n; $i++) $secteurs[] = $c;
    }
    shuffle($secteurs);
    
    // Trouver index de la couleur cible
    $idx = array_search($cible, $secteurs);
    if ($idx === false) $idx = 0;
    
    // Rotation pour que la flèche pointe sur la couleur cible
    $angle_par_sect = 360 / $total;
    $rotation = -90 - ($idx * $angle_par_sect) - ($angle_par_sect / 2);
    
    // Dessiner
    foreach ($secteurs as $i => $couleur) {
        $a1 = deg2rad($rotation + $i * $angle_par_sect);
        $a2 = deg2rad($rotation + ($i + 1) * $angle_par_sect);
        
        $x1 = $cx + $rayon * cos($a1);
        $y1 = $cy + $rayon * sin($a1);
        $x2 = $cx + $rayon * cos($a2);
        $y2 = $cy + $rayon * sin($a2);
        
        $large = ($angle_par_sect > 180) ? 1 : 0;
        
        $svg .= '<path d="M ' . $cx . ' ' . $cy . ' L ' . $x1 . ' ' . $y1 . 
                ' A ' . $rayon . ' ' . $rayon . ' 0 ' . $large . ' 1 ' . $x2 . ' ' . $y2 . 
                ' Z" fill="' . $cols[$couleur] . '" stroke="#000" stroke-width="2"/>';
    }
    
    $svg .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="8" fill="#333"/>';
    $svg .= '<polygon points="175,40 170,60 180,60" fill="#000"/>';
    $svg .= '</svg>';
    
    return $svg;
}

function gcd($a, $b) {
    return $b ? gcd($b, $a % $b) : $a;
}

/**
 * Accord d'un adjectif de couleur avec « boule », nom féminin.
 * La version d'origine ajoutait le « s » du pluriel mais jamais le « e » du
 * féminin : elle écrivait « 5 boules bleus » et « une boule bleu ».
 */
function proba_couleur_accordee($couleur, $n) {
    $feminin = [
        'rouge' => 'rouge',   'jaune' => 'jaune',   'orange' => 'orange',
        'bleu'  => 'bleue',   'vert'  => 'verte',   'noir'   => 'noire',
        'blanc' => 'blanche', 'gris'  => 'grise',   'violet' => 'violette',
    ];
    $mot = $feminin[$couleur] ?? $couleur;
    if ($n > 1 && substr($mot, -1) !== 's') {
        $mot .= 's';
    }
    return $mot;
}

?>
