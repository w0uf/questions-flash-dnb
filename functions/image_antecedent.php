<?php
/**
 * Notion de fonction : image, antécédent, notation f(x)
 *
 * Cinq familles de questions, toutes sur le même vocabulaire :
 *   · notation   : traduire f(a) = b en « image » / « antécédent » et retour
 *   · calcul     : f(x) donnée par une expression → calculer une image,
 *                  ou remonter à un antécédent (fonction affine)
 *   · tableau    : lire une image ou LES antécédents dans un tableau de valeurs
 *   · graphique  : lire une image ou LES antécédents sur une courbe
 *
 * ⚠ Contrainte de construction du graphique : la courbe est une ligne brisée
 * dont tous les segments ont un coefficient directeur de +1 ou −1. Toute
 * ordonnée entière est donc atteinte à une abscisse entière — sans quoi une
 * question sur les antécédents aurait des réponses non entières et non lisibles.
 *
 * Retour : ['type', 'difficulte_id', 'question', 'reponse'] (contrat DNB).
 */

function generer_image_antecedent($famille = '') {
    $sous_types = [
        'notation'  => ['not_image', 'not_antecedent', 'not_traduire', 'not_completer'],
        'calcul'    => ['calc_affine', 'calc_carre', 'calc_antecedent'],
        'tableau'   => ['tab_image', 'tab_antecedent'],
        'graphique' => ['graph_image', 'graph_antecedent'],
    ];

    if (isset($sous_types[$famille])) {
        $liste = $sous_types[$famille];
        $type  = $liste[array_rand($liste)];
    } else {
        // Pool mélangé : on repasse par toutes les familles avant de recommencer.
        if (!isset($_SESSION['ia_pool']) || empty($_SESSION['ia_pool'])) {
            $_SESSION['ia_pool'] = array_merge(...array_values($sous_types));
            shuffle($_SESSION['ia_pool']);
        }
        $type = array_shift($_SESSION['ia_pool']);
    }

    switch ($type) {
        case 'not_image':       return ia_notation('image');
        case 'not_antecedent':  return ia_notation('antecedent');
        case 'not_traduire':    return ia_notation('traduire');
        case 'not_completer':   return ia_notation('completer');
        case 'calc_affine':     return ia_calcul_affine();
        case 'calc_carre':      return ia_calcul_carre();
        case 'calc_antecedent': return ia_calcul_antecedent();
        case 'tab_image':       return ia_tableau('image');
        case 'tab_antecedent':  return ia_tableau('antecedent');
        case 'graph_image':     return ia_graphique('image');
        default:                return ia_graphique('antecedent');
    }
}

// ═════════════════════════════════════════════════════════════════════════════
//  Outils communs
// ═════════════════════════════════════════════════════════════════════════════

/** Écrit un relatif entre parenthèses dès qu'il est négatif : 2 × (−3)². */
function ia_par($n) {
    return ($n < 0) ? '(' . ia_moins($n) . ')' : (string)$n;
}

/** Signe moins typographique (U+2212) plutôt que le trait d'union. */
function ia_moins($n) {
    return str_replace('-', '&minus;', (string)$n);
}

/** Énumération lisible : « 2 et 4 », « −3 ; 0 et 2 » (et non « −3 et 0 et 2 »). */
function ia_liste(array $items) {
    if (count($items) <= 1) { return (string)reset($items); }
    $dernier = array_pop($items);
    return implode(' ; ', $items) . ' et ' . $dernier;
}

/** Nom de fonction tiré au hasard, pour ne pas toujours écrire « f ». */
function ia_nom() {
    $noms = ['f', 'g', 'h', 'p', 'v'];
    return $noms[array_rand($noms)];
}

// ═════════════════════════════════════════════════════════════════════════════
//  1. Notation ⇄ vocabulaire
// ═════════════════════════════════════════════════════════════════════════════

function ia_notation($mode) {
    $f = ia_nom();
    $a = rand(-6, 9);
    $b = rand(-8, 20);
    if ($a === $b) { $b = $a + 3; }

    switch ($mode) {
        case 'image':
            $q = '<p>On sait que <strong>' . $f . '(' . ia_moins($a) . ') = ' . ia_moins($b) . '</strong>.</p>'
               . '<p><strong>Quelle est l\'image de ' . ia_moins($a) . ' par la fonction ' . $f . ' ?</strong></p>';
            $r = '<p>L\'image se lit <em>après</em> le signe égal : c\'est le résultat.</p>'
               . '<p>L\'image de ' . ia_moins($a) . ' par ' . $f . ' est <strong>' . ia_moins($b) . '</strong>.</p>';
            $d = 1.2;
            break;

        case 'antecedent':
            $q = '<p>On sait que <strong>' . $f . '(' . ia_moins($a) . ') = ' . ia_moins($b) . '</strong>.</p>'
               . '<p><strong>Donne un antécédent de ' . ia_moins($b) . ' par la fonction ' . $f . '.</strong></p>';
            $r = '<p>L\'antécédent se lit <em>dans</em> la parenthèse : c\'est le nombre de départ.</p>'
               . '<p><strong>' . ia_moins($a) . '</strong> est un antécédent de ' . ia_moins($b) . ' par ' . $f . '.</p>'
               . '<p><small>On dit « <em>un</em> antécédent » et non « l\'antécédent » : un même nombre peut '
               . 'en avoir plusieurs.</small></p>';
            $d = 1.5;
            break;

        case 'traduire':
            $sens = rand(0, 1);
            if ($sens === 0) {
                $q = '<p>« L\'image de ' . ia_moins($a) . ' par la fonction ' . $f . ' est ' . ia_moins($b) . '. »</p>'
                   . '<p><strong>Traduis cette phrase avec la notation ' . $f . '( ) .</strong></p>';
                $r = '<p><strong>' . $f . '(' . ia_moins($a) . ') = ' . ia_moins($b) . '</strong></p>'
                   . '<p>Le nombre de départ va dans la parenthèse, son image après le signe égal.</p>';
            } else {
                $q = '<p>« ' . ia_moins($a) . ' est un antécédent de ' . ia_moins($b) . ' par la fonction ' . $f . '. »</p>'
                   . '<p><strong>Traduis cette phrase avec la notation ' . $f . '( ) .</strong></p>';
                $r = '<p><strong>' . $f . '(' . ia_moins($a) . ') = ' . ia_moins($b) . '</strong></p>'
                   . '<p>L\'antécédent est le nombre de départ : il va dans la parenthèse.</p>';
            }
            $d = 1.8;
            break;

        default: // completer
            $q = '<p>On sait que <strong>' . $f . '(' . ia_moins($a) . ') = ' . ia_moins($b) . '</strong>.</p>'
               . '<p><strong>Complète : ' . ia_moins($b) . ' est l\'…… de ' . ia_moins($a) . ', '
               . 'et ' . ia_moins($a) . ' est un …… de ' . ia_moins($b) . '.</strong></p>';
            $r = '<p>' . ia_moins($b) . ' est l\'<strong>image</strong> de ' . ia_moins($a) . ' par ' . $f . ','
               . ' et ' . ia_moins($a) . ' est un <strong>antécédent</strong> de ' . ia_moins($b) . ' par ' . $f . '.</p>';
            $d = 1.6;
            break;
    }

    return ['type' => 'image_antecedent', 'difficulte_id' => $d, 'question' => $q, 'reponse' => $r];
}

// ═════════════════════════════════════════════════════════════════════════════
//  2. Calcul d'une image / d'un antécédent à partir d'une expression
// ═════════════════════════════════════════════════════════════════════════════

function ia_calcul_affine() {
    $f = ia_nom();
    $a = rand(2, 6) * (rand(0, 1) ? 1 : -1);
    $b = rand(-9, 9);
    $x = rand(-4, 6);

    $expr = ia_moins($a) . 'x' . ($b >= 0 ? ' + ' . $b : ' &minus; ' . abs($b));
    $val  = $a * $x + $b;

    $q = '<p>Soit la fonction ' . $f . ' définie par <strong>' . $f . '(x) = ' . $expr . '</strong>.</p>'
       . '<p><strong>Calcule ' . $f . '(' . ia_moins($x) . ').</strong></p>';

    $produit = $a * $x;
    $r = '<p>On remplace x par ' . ia_moins($x) . ' :</p>'
       . '<p>' . $f . '(' . ia_moins($x) . ') = ' . ia_moins($a) . ' &times; ' . ia_par($x)
       . ($b >= 0 ? ' + ' . $b : ' &minus; ' . abs($b)) . '</p>'
       . '<p>' . $f . '(' . ia_moins($x) . ') = ' . ia_moins($produit)
       . ($b >= 0 ? ' + ' . $b : ' &minus; ' . abs($b)) . ' = <strong>' . ia_moins($val) . '</strong></p>';

    return ['type' => 'image_antecedent', 'difficulte_id' => 1.8, 'question' => $q, 'reponse' => $r];
}

function ia_calcul_carre() {
    $f = ia_nom();
    $x = rand(-5, 5);
    if ($x === 0) { $x = -3; }

    $forme = rand(0, 2);
    if ($forme === 0) {           // f(x) = x² + b
        $b = rand(-9, 9);
        $expr = 'x&sup2;' . ($b >= 0 ? ' + ' . $b : ' &minus; ' . abs($b));
        $val  = $x * $x + $b;
        $etape = ia_par($x) . '&sup2;' . ($b >= 0 ? ' + ' . $b : ' &minus; ' . abs($b));
        $etape2 = ($x * $x) . ($b >= 0 ? ' + ' . $b : ' &minus; ' . abs($b));
    } elseif ($forme === 1) {     // f(x) = a x²
        $a = rand(2, 5);
        $expr = $a . 'x&sup2;';
        $val  = $a * $x * $x;
        $etape = $a . ' &times; ' . ia_par($x) . '&sup2;';
        $etape2 = $a . ' &times; ' . ($x * $x);
    } else {                      // f(x) = x² − a x
        $a = rand(2, 6);
        $expr = 'x&sup2; &minus; ' . $a . 'x';
        $val  = $x * $x - $a * $x;
        $etape = ia_par($x) . '&sup2; &minus; ' . $a . ' &times; ' . ia_par($x);
        $etape2 = ($x * $x) . ' &minus; ' . ia_par($a * $x);
    }

    $q = '<p>Soit la fonction ' . $f . ' définie par <strong>' . $f . '(x) = ' . $expr . '</strong>.</p>'
       . '<p><strong>Calcule ' . $f . '(' . ia_moins($x) . ').</strong></p>';

    $r = '<p>On remplace x par ' . ia_moins($x) . ', en gardant les parenthèses :</p>'
       . '<p>' . $f . '(' . ia_moins($x) . ') = ' . $etape . '</p>'
       . '<p>' . $f . '(' . ia_moins($x) . ') = ' . $etape2 . ' = <strong>' . ia_moins($val) . '</strong></p>';
    if ($x < 0) {
        $r .= '<p><small>⚠️ ' . ia_par($x) . '&sup2; = ' . ($x * $x) . ' : le carré d\'un nombre négatif est positif.</small></p>';
    }

    return ['type' => 'image_antecedent', 'difficulte_id' => 2.2, 'question' => $q, 'reponse' => $r];
}

function ia_calcul_antecedent() {
    $f = ia_nom();
    $a = rand(2, 6) * (rand(0, 1) ? 1 : -1);
    $b = rand(-9, 9);
    $x = rand(-4, 6);              // l'antécédent cherché, entier par construction
    $y = $a * $x + $b;

    $expr = ia_moins($a) . 'x' . ($b >= 0 ? ' + ' . $b : ' &minus; ' . abs($b));

    $q = '<p>Soit la fonction ' . $f . ' définie par <strong>' . $f . '(x) = ' . $expr . '</strong>.</p>'
       . '<p><strong>Quel est l\'antécédent de ' . ia_moins($y) . ' par la fonction ' . $f . ' ?</strong></p>';

    $reste = $y - $b;
    $r = '<p>Chercher un antécédent de ' . ia_moins($y) . ', c\'est résoudre l\'équation '
       . $f . '(x) = ' . ia_moins($y) . ' :</p>'
       . '<p>' . $expr . ' = ' . ia_moins($y) . '</p>'
       . '<p>' . ia_moins($a) . 'x = ' . ia_moins($y) . ($b >= 0 ? ' &minus; ' . $b : ' + ' . abs($b))
       . ' = ' . ia_moins($reste) . '</p>'
       . '<p>x = ' . ia_moins($reste) . ' &divide; ' . ia_par($a) . ' = <strong>' . ia_moins($x) . '</strong></p>'
       . '<p><small>Vérification : ' . $f . '(' . ia_moins($x) . ') = ' . ia_moins($y) . ' ✔</small></p>';

    return ['type' => 'image_antecedent', 'difficulte_id' => 2.4, 'question' => $q, 'reponse' => $r];
}

// ═════════════════════════════════════════════════════════════════════════════
//  3. Tableau de valeurs
// ═════════════════════════════════════════════════════════════════════════════

function ia_tableau($mode) {
    $f = ia_nom();
    $debut = rand(-4, 1);
    $xs = range($debut, $debut + 5);           // 6 colonnes

    // Images : entiers variés, avec une valeur volontairement répétée pour que
    // la question « les antécédents » ait deux réponses.
    $ys = [];
    foreach ($xs as $i => $x) {
        $ys[$i] = rand(-8, 12);
    }
    $i1 = rand(0, 2);
    $i2 = rand(3, 5);
    $ys[$i2] = $ys[$i1];                       // doublon garanti

    $entetes = '';
    $lignes  = '';
    foreach ($xs as $i => $x) {
        $entetes .= '<td style="border:1px solid #999; padding:8px 14px; text-align:center;">' . ia_moins($x) . '</td>';
        $lignes  .= '<td style="border:1px solid #999; padding:8px 14px; text-align:center;">' . ia_moins($ys[$i]) . '</td>';
    }

    $table = '<table style="border-collapse:collapse; margin:1em auto; font-size:1.05rem;">'
           . '<tr><th style="border:1px solid #999; padding:8px 14px; background:#eef4fb;">x</th>' . $entetes . '</tr>'
           . '<tr><th style="border:1px solid #999; padding:8px 14px; background:#eef4fb;">' . $f . '(x)</th>' . $lignes . '</tr>'
           . '</table>';

    if ($mode === 'image') {
        $i = array_rand($xs);
        $q = '<p>Ce tableau donne quelques valeurs de la fonction ' . $f . ' :</p>' . $table
           . '<p><strong>Quelle est l\'image de ' . ia_moins($xs[$i]) . ' par ' . $f . ' ?</strong></p>';
        $r = '<p>On cherche ' . ia_moins($xs[$i]) . ' dans la ligne du haut, on lit dessous :</p>'
           . '<p>' . $f . '(' . ia_moins($xs[$i]) . ') = <strong>' . ia_moins($ys[$i]) . '</strong></p>';
        $d = 1.3;
    } else {
        $cible = $ys[$i1];
        $anteced = [];
        foreach ($xs as $i => $x) {
            if ($ys[$i] === $cible) { $anteced[] = ia_moins($x); }
        }
        $q = '<p>Ce tableau donne quelques valeurs de la fonction ' . $f . ' :</p>' . $table
           . '<p><strong>Quels sont les antécédents de ' . ia_moins($cible) . ' par ' . $f . ' ?</strong></p>';
        $r = '<p>On cherche ' . ia_moins($cible) . ' dans la ligne du bas — attention, il y figure '
           . count($anteced) . ' fois — et on lit au-dessus :</p>'
           . '<p>Les antécédents de ' . ia_moins($cible) . ' sont <strong>' . ia_liste($anteced) . '</strong>.</p>';
        $d = 2.0;
    }

    return ['type' => 'image_antecedent', 'difficulte_id' => $d, 'question' => $q, 'reponse' => $r];
}

// ═════════════════════════════════════════════════════════════════════════════
//  4. Lecture graphique
// ═════════════════════════════════════════════════════════════════════════════

/**
 * Ordonnées entières de la courbe pour x = 0 à 8, en 2 ou 3 portions monotones.
 *
 * $pentes_libres : autorise des pas de ±2, ce qui donne une courbe d'allure plus
 * naturelle. À réserver aux questions sur les IMAGES : avec un pas de 2, le
 * segment traverse une ordonnée entière à une abscisse non entière, ce qui
 * rendrait fausse une question sur les antécédents (cf. tête de fichier).
 */
function ia_profil($pentes_libres = false) {
    for ($essai = 0; $essai < 200; $essai++) {
        $nb_portions = rand(2, 3);
        $longueurs = [];
        $reste = 8;
        for ($i = 0; $i < $nb_portions - 1; $i++) {
            $max = $reste - 2 * ($nb_portions - 1 - $i);
            if ($max < 2) { continue 2; }
            $l = rand(2, $max);
            $longueurs[] = $l;
            $reste -= $l;
        }
        $longueurs[] = $reste;

        $sens = (rand(0, 1) === 0) ? 1 : -1;
        $v = rand(0, 8);
        $vals = [$v];
        $ok = true;
        foreach ($longueurs as $l) {
            for ($k = 0; $k < $l; $k++) {
                $v += $sens * ($pentes_libres ? rand(1, 2) : 1);
                if ($v < 0 || $v > 8) { $ok = false; break 2; }
                $vals[] = $v;
            }
            $sens = -$sens;
        }
        if ($ok && count($vals) === 9) {
            return $vals;
        }
    }
    // Filet de sécurité : une « tente » toujours valide.
    return [0, 1, 2, 3, 4, 3, 2, 1, 0];
}

/**
 * Repère orthogonal responsive (viewBox) portant la courbe.
 * $marques : liste de ['x'=>, 'y'=>, 'sens'=>'image'|'antecedent'] pour tracer
 * les pointillés de lecture dans la correction.
 */
function ia_svg_repere(array $vals, array $marques = [], $nom = 'f') {
    $U = 43; $M = 45; $W = 520;      // 10 unités de -1 à 9 : 45 + 10×43 = 475
    $X = function ($x) use ($U, $M) { return $M + ($x + 1) * $U; };
    $Y = function ($y) use ($U, $M, $W) { return $W - $M - ($y + 1) * $U; };

    // La classe permet aux pages hôtes de contraindre la taille — la fiche PDF
    // du DNB la ramène à 300 px pour ne pas manger la moitié d'une page.
    $svg = '<svg viewBox="0 0 ' . $W . ' ' . $W . '" role="img" class="ia-repere" '
         . 'aria-label="Courbe représentative d\'une fonction dans un repère" '
         . 'style="width:100%; max-width:520px; height:auto; display:block; margin:1em auto; '
         . 'background:#fff; border:1px solid #ddd;">';

    // Quadrillage
    for ($i = -1; $i <= 9; $i++) {
        $svg .= '<line x1="' . $X($i) . '" y1="' . $Y(-1) . '" x2="' . $X($i) . '" y2="' . $Y(9) . '" stroke="#e3e3e3" stroke-width="1"/>';
        $svg .= '<line x1="' . $X(-1) . '" y1="' . $Y($i) . '" x2="' . $X(9) . '" y2="' . $Y($i) . '" stroke="#e3e3e3" stroke-width="1"/>';
    }

    // Pointillés de lecture (sous la courbe pour ne pas la masquer)
    foreach ($marques as $m) {
        $svg .= '<line x1="' . $X($m['x']) . '" y1="' . $Y(0) . '" x2="' . $X($m['x']) . '" y2="' . $Y($m['y'])
              . '" stroke="#2f7ed8" stroke-width="2.5" stroke-dasharray="7 5"/>';
        $svg .= '<line x1="' . $X($m['x']) . '" y1="' . $Y($m['y']) . '" x2="' . $X(0) . '" y2="' . $Y($m['y'])
              . '" stroke="#2f7ed8" stroke-width="2.5" stroke-dasharray="7 5"/>';
    }

    // Axes
    $svg .= '<line x1="' . $X(-1) . '" y1="' . $Y(0) . '" x2="' . ($X(9) + 12) . '" y2="' . $Y(0) . '" stroke="#333" stroke-width="2"/>';
    $svg .= '<line x1="' . $X(0) . '" y1="' . $Y(-1) . '" x2="' . $X(0) . '" y2="' . ($Y(9) - 12) . '" stroke="#333" stroke-width="2"/>';
    $svg .= '<polygon points="' . ($X(9) + 12) . ',' . $Y(0) . ' ' . ($X(9) + 2) . ',' . ($Y(0) - 5) . ' ' . ($X(9) + 2) . ',' . ($Y(0) + 5) . '" fill="#333"/>';
    $svg .= '<polygon points="' . $X(0) . ',' . ($Y(9) - 12) . ' ' . ($X(0) - 5) . ',' . ($Y(9) - 2) . ' ' . ($X(0) + 5) . ',' . ($Y(9) - 2) . '" fill="#333"/>';

    // Graduations
    for ($i = 1; $i <= 8; $i++) {
        $svg .= '<text x="' . $X($i) . '" y="' . ($Y(0) + 20) . '" text-anchor="middle" font-size="15" fill="#333">' . $i . '</text>';
        $svg .= '<text x="' . ($X(0) - 12) . '" y="' . ($Y($i) + 5) . '" text-anchor="end" font-size="15" fill="#333">' . $i . '</text>';
    }
    $svg .= '<text x="' . ($X(0) - 12) . '" y="' . ($Y(0) + 20) . '" text-anchor="end" font-size="15" fill="#333">O</text>';
    $svg .= '<text x="' . ($X(9) + 4) . '" y="' . ($Y(0) + 24) . '" text-anchor="middle" font-size="15" font-style="italic" fill="#333">x</text>';
    $svg .= '<text x="' . ($X(0) - 18) . '" y="' . ($Y(9) - 10) . '" text-anchor="middle" font-size="15" font-style="italic" fill="#333">y</text>';

    // Courbe
    $pts = [];
    foreach ($vals as $x => $y) {
        $pts[] = $X($x) . ',' . $Y($y);
    }
    $svg .= '<polyline points="' . implode(' ', $pts) . '" fill="none" stroke="#c0392b" stroke-width="3.5" stroke-linejoin="round"/>';

    // Étiquette de la courbe, placée du côté où elle laisse de la place.
    $fin = $vals[8];
    $dy  = ($fin >= $vals[7]) ? 26 : -14;   // sous la courbe si elle monte, au-dessus sinon
    $svg .= '<text x="' . ($X(8) + 10) . '" y="' . ($Y($fin) + $dy) . '" font-size="17" fill="#c0392b" '
          . 'font-style="italic">&#119966;<tspan font-size="12" dy="4">' . $nom . '</tspan></text>';

    // Points de lecture par-dessus la courbe
    foreach ($marques as $m) {
        $svg .= '<circle cx="' . $X($m['x']) . '" cy="' . $Y($m['y']) . '" r="6" fill="#2f7ed8"/>';
    }

    $svg .= '</svg>';
    return $svg;
}

function ia_graphique($mode) {
    $f = ia_nom();
    // Pentes libres seulement pour les images : voir ia_profil().
    $vals = ia_profil($mode === 'image');

    if ($mode === 'image') {
        $x = rand(1, 7);
        $y = $vals[$x];

        $q = '<p>Voici la courbe représentative d\'une fonction ' . $f . ' :</p>'
           . ia_svg_repere($vals, [], $f)
           . '<p><strong>Lis graphiquement l\'image de ' . $x . ' par la fonction ' . $f . '.</strong></p>';

        $r = '<p>On part de ' . $x . ' sur l\'axe des abscisses, on monte jusqu\'à la courbe, '
           . 'puis on lit sur l\'axe des ordonnées :</p>'
           . ia_svg_repere($vals, [['x' => $x, 'y' => $y]], $f)
           . '<p>L\'image de ' . $x . ' par ' . $f . ' est <strong>' . $y . '</strong>, '
           . 'autrement dit ' . $f . '(' . $x . ') = <strong>' . $y . '</strong>.</p>';
        $d = 1.4;

    } else {
        // On privilégie une ordonnée atteinte plusieurs fois : c'est tout
        // l'intérêt de la question (« les » antécédents, au pluriel).
        $par_valeur = [];
        foreach ($vals as $x => $y) {
            $par_valeur[$y][] = $x;
        }
        $multiples = array_filter($par_valeur, function ($xs) { return count($xs) > 1; });
        if (!empty($multiples)) {
            $y = array_rand($multiples);
            $xs = $multiples[$y];
        } else {
            $y = array_rand($par_valeur);
            $xs = $par_valeur[$y];
        }

        $marques = [];
        foreach ($xs as $x) {
            $marques[] = ['x' => $x, 'y' => $y];
        }

        $pluriel = count($xs) > 1;
        $q = '<p>Voici la courbe représentative d\'une fonction ' . $f . ' :</p>'
           . ia_svg_repere($vals, [], $f)
           . '<p><strong>Lis graphiquement ' . ($pluriel ? 'les antécédents' : 'l\'antécédent')
           . ' de ' . $y . ' par la fonction ' . $f . '.</strong></p>';

        $r = '<p>On part de ' . $y . ' sur l\'axe des ordonnées, on avance horizontalement, et on note '
           . '<strong>chaque</strong> abscisse où l\'on rencontre la courbe :</p>'
           . ia_svg_repere($vals, $marques, $f)
           . '<p>' . ($pluriel ? 'Les antécédents de ' : 'L\'antécédent de ') . $y . ' par ' . $f
           . ($pluriel ? ' sont <strong>' : ' est <strong>') . ia_liste($xs) . '</strong>.</p>';
        if ($pluriel) {
            $r .= '<p><small>Un nombre peut avoir plusieurs antécédents : la droite horizontale coupe '
                . 'la courbe ' . count($xs) . ' fois.</small></p>';
        }
        $d = $pluriel ? 2.3 : 1.6;
    }

    return ['type' => 'image_antecedent', 'difficulte_id' => $d, 'question' => $q, 'reponse' => $r];
}
