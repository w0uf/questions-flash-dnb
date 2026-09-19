<?php
/**
 * Automatisme : « Calcul astucieux » — se servir des propriétés des opérations
 * pour rendre un calcul faisable de tête.
 * Difficulté : FACILE à MOYEN (range 1.2 - 2.3)
 * Format : réponse ouverte (un nombre)
 *
 * Le point de départ, ce sont les quatre astuces du « coin du futé » de la leçon
 * de 5e (nombre_5.php), prises pour elles-mêmes et tirées au hasard. Trois
 * familles de cycle 4 leur ont été ajoutées le 18/09/2026 : la même gymnastique,
 * sur les nombres que l'élève rencontre plus tard.
 *
 * Sept familles, isolables par l'onglet ?m= de qf_calcul_astucieux.php :
 *   a — regrouper des termes     (on change l'ordre dans une somme)
 *   b — regrouper des facteurs   (on change l'ordre dans un produit)
 *   c — développer               (×9, ×11, ×99 : un facteur voisin d'un compte rond)
 *   d — factoriser               (le même facteur des deux côtés)
 *   e — relatifs                 (deux opposés, deux facteurs négatifs, facteur commun négatif)
 *   f — fractions                (deux fractions qui font 1, une fraction et son inverse, diviser d'abord)
 *   g — puissances de 10         (les nombres ensemble, les puissances ensemble)
 *
 * ⚠️ Tous les calculs décimaux se font en ENTIERS DE CENTIÈMES, jamais en
 * flottants : une somme de 3,45 et 6,55 en double donne 9,999999999999998, et la
 * question « astucieuse » perdrait précisément ce qui fait son sel — que ça
 * tombe rond. Les fractions, elles, restent en couples (numérateur, dénominateur).
 *
 * Chaque tirage renvoie aussi 'verif' et 'verif_rep' : l'expression et la réponse
 * en TEXTE BRUT (× → *, fraction → /, virgule → point, 10^n → 10**n). Elles ne
 * sont jamais affichées ; elles existent pour que le contrôle automatique puisse
 * recalculer le tirage sans désosser le HTML. Le DNB ignore les clés qu'il ne lit pas.
 */

require_once __DIR__ . '/qf_communs.php';   // np_pick, np_rep…
require_once __DIR__ . '/utils.php';        // fraction()

// ── Mise en forme ───────────────────────────────────────────────────────────

/** Un décimal à UNE décimale, jamais rond : 1,5 … 9,5 mais pas 3,0. */
function ca_decimal_1($min, $max) {
    do { $v = rand($min, $max); } while ($v % 10 === 0);
    return $v * 10;
}

/** Entier de centièmes → nombre à la française : 10450 → « 104,5 ». */
function ca_nb($c) {
    $s = number_format($c / 100, 2, ',', '&nbsp;');
    return rtrim(rtrim($s, '0'), ',');       // la virgule protège « 1 200 »
}

/** Le même nombre en texte brut, pour le contrôle : 10450 → « 104.5 ». */
function ca_txt($c) {
    $s = number_format($c / 100, 2, '.', '');
    return rtrim(rtrim($s, '0'), '.');
}

/** Un relatif négatif, entre parenthèses comme on l'écrit au collège. */
function ca_neg($c) { return '(&minus;' . ca_nb($c) . ')'; }

/** L'énoncé : une consigne, une expression en gros. */
function ca_enonce($expression) {
    return '<p class="ca-consigne">Calcule astucieusement :</p>'
         . '<p class="ca-expr">' . $expression . '</p>';
}

/** La suite des égalités, alignées sur le signe =. */
function ca_etapes(array $lignes) {
    $h = '<div class="ca-chaine">';
    foreach ($lignes as $i => $l) {
        $h .= '<p class="ca-ligne"><span class="ca-eq">' . ($i ? '=' : '') . '</span>'
            . '<span class="ca-membre">' . $l . '</span></p>';
    }
    return $h . '</div>';
}

/** Le pourquoi de l'astuce, sous la chaîne de calcul. */
function ca_astuce($texte) {
    return '<p class="ca-astuce">💡 ' . $texte . '</p>';
}

/** Ce qu'il faut retenir, en petit, tout en bas. */
function ca_regle($texte) {
    return '<p class="ca-regle">' . $texte . '</p>';
}

/** PGCD, pour ne proposer que des fractions irréductibles. */
function ca_pgcd($a, $b) { while ($b) { $t = $b; $b = $a % $b; $a = $t; } return $a; }

/** 10 à la puissance n, tel qu'on l'écrit. */
function ca_p10($n) { return '10<sup>' . $n . '</sup>'; }

// ── Le générateur ───────────────────────────────────────────────────────────

function generer_calcul_astucieux($famille = '') {

    $toutes = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];
    if (in_array($famille, $toutes, true)) {
        $type = $famille;
    } else {
        // Pool de session : on épuise les sept familles avant de recommencer,
        // sinon une projection au TBI retombe trois fois de suite sur la même.
        if (empty($_SESSION['calcul_astucieux_pool'])) {
            $_SESSION['calcul_astucieux_pool'] = $toutes;
            shuffle($_SESSION['calcul_astucieux_pool']);
        }
        $type = array_shift($_SESSION['calcul_astucieux_pool']);
    }

    $d = 1.5;

    switch ($type) {

    // ══ A — REGROUPER DES TERMES ════════════════════════════════════════════
    // Deux termes font un compte rond ; le troisième est posé ENTRE eux, pour
    // que l'astuce demande de changer l'ordre et pas seulement de lire.
    case 'a':
        // On tire d'abord l'ordre de grandeur, puis les nombres : sans cela,
        // toutes les questions sortent dans la même dizaine.
        switch (np_pick(['dix', 'cent', 'mille', 'quatre'])) {

        case 'dix':     // la paire fait 10
            $p = rand(11, 89) * 10;  $R = 1000;  $q = $R - $p;
            $m = rand(11, 99) * 10;
            $d = 1.2;
            break;

        case 'cent':    // la paire fait 100, le terme du milieu a deux décimales
            $p = rand(11, 89) * 100; $R = 10000; $q = $R - $p;
            $m = rand(105, 995);
            $d = 1.4;
            break;

        case 'mille':   // la paire fait 1 000
            $p = rand(105, 895) * 100; $R = 100000; $q = $R - $p;
            $m = rand(11, 99) * 10;
            $d = 1.6;
            break;

        case 'quatre':  // deux paires entrelacées : 100 d'un côté, 10 de l'autre
            $p1 = rand(11, 89) * 100; $q1 = 10000 - $p1;
            $p2 = rand(11, 89) * 10;  $q2 = 1000  - $p2;
            $total   = 11000;
            $q_html  = ca_enonce(ca_nb($p1) . ' + ' . ca_nb($p2) . ' + ' . ca_nb($q1) . ' + ' . ca_nb($q2));
            $a_html  = np_rep('<strong>' . ca_nb($total) . '</strong>')
                     . ca_astuce('Deux paires tombent rondes : ' . ca_nb($p1) . ' + ' . ca_nb($q1)
                                 . ' = 100 et ' . ca_nb($p2) . ' + ' . ca_nb($q2) . ' = 10.')
                     . ca_etapes([
                         ca_nb($p1) . ' + ' . ca_nb($p2) . ' + ' . ca_nb($q1) . ' + ' . ca_nb($q2),
                         '(' . ca_nb($p1) . ' + ' . ca_nb($q1) . ') + (' . ca_nb($p2) . ' + ' . ca_nb($q2) . ')',
                         '100 + 10',
                         '<strong>' . ca_nb($total) . '</strong>',
                       ])
                     . ca_regle('Dans une somme, on peut changer l’ordre des termes et les regrouper '
                              . 'comme on veut : le résultat est toujours le même.');
            return ['type' => 'calcul_astucieux', 'famille' => $type, 'difficulte_id' => 1.8,
                    'question' => $q_html, 'reponse' => $a_html,
                    'verif'     => ca_txt($p1) . '+' . ca_txt($p2) . '+' . ca_txt($q1) . '+' . ca_txt($q2),
                    'verif_rep' => ca_txt($total)];
        }

        $total  = $R + $m;
        $q_html = ca_enonce(ca_nb($p) . ' + ' . ca_nb($m) . ' + ' . ca_nb($q));
        $a_html = np_rep('<strong>' . ca_nb($total) . '</strong>')
                . ca_astuce('Le premier et le dernier terme font un compte rond : '
                            . ca_nb($p) . ' + ' . ca_nb($q) . ' = ' . ca_nb($R) . '.')
                . ca_etapes([
                    ca_nb($p) . ' + ' . ca_nb($m) . ' + ' . ca_nb($q),
                    '(' . ca_nb($p) . ' + ' . ca_nb($q) . ') + ' . ca_nb($m),
                    ca_nb($R) . ' + ' . ca_nb($m),
                    '<strong>' . ca_nb($total) . '</strong>',
                  ])
                . ca_regle('Dans une somme, on peut changer l’ordre des termes et les regrouper '
                         . 'comme on veut : le résultat est toujours le même.');
        $verif     = ca_txt($p) . '+' . ca_txt($m) . '+' . ca_txt($q);
        $verif_rep = ca_txt($total);
        break;

    // ══ B — REGROUPER DES FACTEURS ══════════════════════════════════════════
    // Deux facteurs font 10, 100, 1 000 ou 1 ; le troisième est au milieu.
    case 'b':
        // [a, b, produit] en centièmes pour a et b, en entier pour le produit.
        $paires = [
            [200,   500,    10], [500,   200,    10],
            [400,  2500,   100], [2500,  400,   100],
            [2000,  500,   100], [500,  2000,   100],
            [5000,  200,   100], [200,  5000,   100],
            [800, 12500,  1000], [12500, 800,  1000],
            [50,    200,     1], [200,    50,     1],
            [25,    400,     1], [400,    25,     1],
            [20,    500,     1], [500,    20,     1],
        ];
        list($fa, $fb, $P) = np_pick($paires);

        // L'ordre de grandeur du facteur du milieu suit le produit de la paire,
        // pour que le résultat reste dicible : on ne veut pas 1 000 × 47,25.
        if ($P >= 1000)      { $x = rand(105, 995);   $d = 1.9; }
        elseif ($P >= 100)   { $x = rand(11, 99) * 10; $d = 1.6; }
        elseif ($P >= 10)    { $x = rand(105, 4995);  $d = 1.5; }
        else                 { $x = rand(105, 4995);  $d = 1.7; }

        $total  = $P * $x;
        // Quand la paire vaut 1 (2 et 0,5 ; 4 et 0,25), dire « un compte rond »
        // serait à côté : les deux facteurs se neutralisent, le nombre du milieu
        // est déjà la réponse. C'est le tirage le plus frappant de la famille.
        $pourquoi = ($P === 1)
            ? 'Le premier et le dernier facteur se compensent : ' . ca_nb($fa) . ' × ' . ca_nb($fb)
              . ' = 1. Multiplier par 1 ne change rien — le nombre du milieu est déjà la réponse.'
            : 'Le premier et le dernier facteur font un compte rond : '
              . ca_nb($fa) . ' × ' . ca_nb($fb) . ' = ' . $P . '.';

        $q_html = ca_enonce(ca_nb($fa) . ' × ' . ca_nb($x) . ' × ' . ca_nb($fb));
        $a_html = np_rep('<strong>' . ca_nb($total) . '</strong>')
                . ca_astuce($pourquoi)
                . ca_etapes([
                    ca_nb($fa) . ' × ' . ca_nb($x) . ' × ' . ca_nb($fb),
                    '(' . ca_nb($fa) . ' × ' . ca_nb($fb) . ') × ' . ca_nb($x),
                    $P . ' × ' . ca_nb($x),
                    '<strong>' . ca_nb($total) . '</strong>',
                  ])
                . ca_regle('Dans un produit, on peut changer l’ordre des facteurs et les regrouper '
                         . 'comme on veut : le résultat est toujours le même.');
        $verif     = ca_txt($fa) . '*' . ca_txt($x) . '*' . ca_txt($fb);
        $verif_rep = ca_txt($total);
        break;

    // ══ C — DÉVELOPPER ══════════════════════════════════════════════════════
    // Un facteur est voisin d'un compte rond : 12 = 10 + 2, 99 = 100 − 1.
    case 'c':
        // [base, écart signé, k]
        $formes = [
            [10, +1,  11], [10, +2,  12], [10, +3,  13], [10, -1,   9], [10, -2,   8],
            [20, +1,  21], [20, +2,  22], [20, -1,  19], [20, -2,  18],
            [100, +1, 101], [100, +2, 102], [100, -1, 99], [100, -2, 98],
        ];
        list($base, $ecart, $k) = np_pick($formes);
        $e = abs($ecart);

        // L'autre facteur : entier ou décimal, dimensionné d'après la base.
        if ($base === 100) {
            // Une seule décimale ici : « 6,99 × 99 » revient à faire 699 − 6,99,
            // qui n'est plus un calcul de tête — l'astuce se retournerait contre
            // elle-même. Avec 6,5 : 650 − 6,5, et ça passe.
            $n = np_pick([rand(3, 25) * 100, ca_decimal_1(15, 95)]);
            $d = ($n % 100 === 0) ? 1.7 : 2.0;
        } else {
            $n = np_pick([rand(12, 49) * 100, ca_decimal_1(15, 95)]);
            $d = ($n % 100 === 0) ? 1.5 : 1.8;
        }

        // $n est déjà en centièmes : le multiplier par un entier donne
        // directement le produit en centièmes.
        $t_base  = $n * $base;
        $t_ecart = $n * $e;
        $total   = $n * $k;

        $signe   = ($ecart > 0) ? '+' : '&minus;';
        $q_html  = ca_enonce(ca_nb($n) . ' × ' . $k);
        $a_html  = np_rep('<strong>' . ca_nb($total) . '</strong>')
                 . ca_astuce($k . ', c’est ' . $base . ' ' . $signe . ' ' . $e
                             . ' : on multiplie par ' . $base . ', ce qui est immédiat, puis on '
                             . (($ecart > 0) ? 'ajoute' : 'retire') . ' ' . $e . ' fois le nombre.')
                 . ca_etapes([
                     ca_nb($n) . ' × ' . $k,
                     ca_nb($n) . ' × (' . $base . ' ' . $signe . ' ' . $e . ')',
                     ca_nb($n) . ' × ' . $base . ' ' . $signe . ' ' . ca_nb($n) . ' × ' . $e,
                     ca_nb($t_base) . ' ' . $signe . ' ' . ca_nb($t_ecart),
                     '<strong>' . ca_nb($total) . '</strong>',
                   ])
                 . ca_regle('On a coupé le facteur en deux morceaux faciles, et on a multiplié par '
                          . 'chacun d’eux. C’est ce qu’on appelle développer.');
        $verif     = ca_txt($n) . '*' . $k;
        $verif_rep = ca_txt($total);
        break;

    // ══ D — FACTORISER ══════════════════════════════════════════════════════
    // Le même facteur des deux côtés ; ce qui reste tombe rond.
    case 'd':
        $R     = np_pick([1000, 10000]);          // 10 ou 100, en centièmes
        $a     = np_pick([3, 4, 6, 7, 8, 9, 11, 12, 15, 17, 18, 21, 25]);
        $somme = (bool) rand(0, 1);

        if ($somme) {                              // u + v = R
            // Deux décimales pour le complément à 10 (8,43 + 1,57, un classique),
            // une seule pour le complément à 100 : « 16,79 + 83,21 » est juste,
            // mais ce n'est plus du calcul mental.
            $u = ($R === 1000) ? rand(105, 895) : ca_decimal_1(11, 989);
            $v = $R - $u;
            $op = '+';
        } else {                                   // u − v = R
            // On garde v sous 10 : « 149,95 − 49,95 » est juste, mais la
            // soustraction cesse d'être faisable de tête, et c'est tout l'objet.
            $v = rand(105, ($R === 1000) ? 895 : 995);
            $u = $R + $v;
            $op = '&minus;';
        }

        $gauche = (bool) rand(0, 1);               // facteur commun devant ou derrière
        $expr   = $gauche
                ? $a . ' × ' . ca_nb($u) . ' ' . $op . ' ' . $a . ' × ' . ca_nb($v)
                : ca_nb($u) . ' × ' . $a . ' ' . $op . ' ' . ca_nb($v) . ' × ' . $a;
        $signe_txt = ($op === '+') ? '+' : '-';
        $vtxt   = $gauche
                ? $a . '*' . ca_txt($u) . $signe_txt . $a . '*' . ca_txt($v)
                : ca_txt($u) . '*' . $a . $signe_txt . ca_txt($v) . '*' . $a;

        $total  = $a * $R;
        $d      = ($R === 1000) ? 1.8 : 2.0;

        $q_html = ca_enonce($expr);
        $a_html = np_rep('<strong>' . ca_nb($total) . '</strong>')
                . ca_astuce('Le facteur ' . $a . ' est commun aux deux produits. On le met en '
                            . 'facteur, et ce qui reste tombe rond : '
                            . ca_nb($u) . ' ' . $op . ' ' . ca_nb($v) . ' = ' . ca_nb($R) . '.')
                . ca_etapes([
                    $expr,
                    $a . ' × (' . ca_nb($u) . ' ' . $op . ' ' . ca_nb($v) . ')',
                    $a . ' × ' . ca_nb($R),
                    '<strong>' . ca_nb($total) . '</strong>',
                  ])
                . ca_regle('On a factorisé : au lieu de deux multiplications, on n’en fait qu’une. '
                         . 'C’est le chemin inverse de celui qu’on suit pour développer.');
        $verif     = $vtxt;
        $verif_rep = ca_txt($total);
        break;

    // ══ E — NOMBRES RELATIFS (cycle 4) ══════════════════════════════════════
    // La même gymnastique, avec les signes en plus. C'est là qu'elle paie le
    // plus : l'élève qui calcule de gauche à droite additionne des relatifs
    // pour rien.
    case 'e':
        switch (np_pick(['opposes', 'produit', 'commun'])) {

        case 'opposes':     // deux opposés s'annulent, le reste est la réponse
            $o = rand(3, 25) * 100;
            $m = np_pick([rand(105, 4995), rand(2, 40) * 100]);
            $expr  = ca_neg($o) . ' + ' . ca_nb($m) . ' + ' . ca_nb($o);
            $total = $m;
            $d     = 1.6;
            $q_html = ca_enonce($expr);
            $a_html = np_rep('<strong>' . ca_nb($total) . '</strong>')
                    . ca_astuce('Le premier et le dernier terme sont <strong>opposés</strong> : '
                                . ca_neg($o) . ' + ' . ca_nb($o) . ' = 0. Il ne reste que '
                                . ca_nb($m) . '.')
                    . ca_etapes([
                        $expr,
                        '[' . ca_neg($o) . ' + ' . ca_nb($o) . '] + ' . ca_nb($m),
                        '0 + ' . ca_nb($m),
                        '<strong>' . ca_nb($total) . '</strong>',
                      ])
                    . ca_regle('Deux nombres opposés s’annulent : leur somme vaut 0. Les repérer '
                             . 'évite d’additionner des relatifs pour rien.');
            $verif     = '(-' . ca_txt($o) . ')+' . ca_txt($m) . '+' . ca_txt($o);
            $verif_rep = ca_txt($total);
            break;

        case 'produit':     // deux facteurs négatifs, et leur produit tombe rond
            $paires = [[200, 500, 10], [500, 200, 10], [400, 2500, 100], [2500, 400, 100],
                       [2000, 500, 100], [500, 2000, 100], [50, 200, 1], [200, 50, 1]];
            list($fa, $fb, $P) = np_pick($paires);
            $x     = ($P >= 100) ? rand(11, 99) * 10 : rand(105, 4995);
            $total = $P * $x;      // (−) × (+) × (−) = (+)
            $d     = 2.1;
            $expr  = ca_neg($fa) . ' × ' . ca_nb($x) . ' × ' . ca_neg($fb);
            $q_html = ca_enonce($expr);
            $a_html = np_rep('<strong>' . ca_nb($total) . '</strong>')
                    . ca_astuce('Deux facteurs négatifs : le résultat est <strong>positif</strong>. '
                                . 'Et ' . ca_nb($fa) . ' × ' . ca_nb($fb) . ' = ' . $P
                                . ', un compte rond.')
                    . ca_etapes([
                        $expr,
                        '[' . ca_neg($fa) . ' × ' . ca_neg($fb) . '] × ' . ca_nb($x),
                        $P . ' × ' . ca_nb($x),
                        '<strong>' . ca_nb($total) . '</strong>',
                      ])
                    . ca_regle('On compte d’abord les signes « − » : ici il y en a deux, le '
                             . 'résultat est positif. Ensuite seulement on calcule les nombres.');
            $verif     = '(-' . ca_txt($fa) . ')*' . ca_txt($x) . '*(-' . ca_txt($fb) . ')';
            $verif_rep = ca_txt($total);
            break;

        default:            // facteur commun négatif
            $a  = np_pick([3, 4, 6, 7, 8, 9, 11, 12, 15]);
            $u  = rand(105, 895);
            $v  = 1000 - $u;
            $total = -$a * 1000;
            $d  = 2.3;
            $expr = ca_neg($a * 100) . ' × ' . ca_nb($u) . ' + ' . ca_neg($a * 100) . ' × ' . ca_nb($v);
            $q_html = ca_enonce($expr);
            $a_html = np_rep('<strong>&minus;' . ca_nb($a * 1000) . '</strong>')
                    . ca_astuce('Le facteur ' . ca_neg($a * 100) . ' est commun aux deux produits. '
                                . 'Ce qui reste tombe rond : ' . ca_nb($u) . ' + ' . ca_nb($v)
                                . ' = 10. Un facteur négatif multiplié par un nombre positif donne '
                                . 'un résultat <strong>négatif</strong>.')
                    . ca_etapes([
                        $expr,
                        ca_neg($a * 100) . ' × (' . ca_nb($u) . ' + ' . ca_nb($v) . ')',
                        ca_neg($a * 100) . ' × 10',
                        '<strong>&minus;' . ca_nb($a * 1000) . '</strong>',
                      ])
                    . ca_regle('Mettre en facteur marche exactement pareil avec un nombre négatif. '
                             . 'Le signe se décide à la fin, une seule fois.');
            $verif     = '(-' . $a . ')*' . ca_txt($u) . '+(-' . $a . ')*' . ca_txt($v);
            $verif_rep = ca_txt($total);
            break;
        }
        break;

    // ══ F — FRACTIONS (cycle 4) ═════════════════════════════════════════════
    case 'f':
        switch (np_pick(['unite', 'inverse', 'diviser'])) {

        case 'unite':       // deux fractions de même dénominateur qui font 1
            $den = np_pick([3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
            // Fraction irréductible : « 2/4 + 2/4 » est juste, mais s'écrit mal.
            // Si n1/den est irréductible, (den − n1)/den l'est aussi.
            do { $n1 = rand(1, $den - 1); } while (ca_pgcd($n1, $den) !== 1);
            $n2  = $den - $n1;
            $m   = rand(2, 20);
            $total = $m + 1;
            $d   = 1.7;
            $expr = fraction($n1, $den) . ' + ' . $m . ' + ' . fraction($n2, $den);
            $q_html = ca_enonce($expr);
            $a_html = np_rep('<strong>' . $total . '</strong>')
                    . ca_astuce('Les deux fractions ont le même dénominateur, et leurs numérateurs '
                                . "font justement le dénominateur : $n1 + $n2 = $den. Leur somme "
                                . 'vaut donc <strong>1</strong>.')
                    . ca_etapes([
                        $expr,
                        '(' . fraction($n1, $den) . ' + ' . fraction($n2, $den) . ') + ' . $m,
                        fraction($den, $den) . ' + ' . $m,
                        '1 + ' . $m,
                        '<strong>' . $total . '</strong>',
                      ])
                    . ca_regle('Une fraction dont le numérateur est égal au dénominateur vaut 1. '
                             . 'Repérer la paire évite de tout ramener au même dénominateur.');
            $verif     = "$n1/$den+$m+$n2/$den";
            $verif_rep = (string) $total;
            break;

        case 'inverse':     // une fraction et son inverse se rencontrent
            do {
                $p = np_pick([2, 3, 4, 5, 7, 8, 9]);
                $q = np_pick([2, 3, 4, 5, 6, 7, 9, 11]);
            } while ($p === $q || ca_pgcd($p, $q) !== 1);
            $m = rand(2, 30);
            $total = $m;
            $d = 2.0;
            $expr = fraction($p, $q) . ' × ' . $m . ' × ' . fraction($q, $p);
            $q_html = ca_enonce($expr);
            $a_html = np_rep('<strong>' . $total . '</strong>')
                    . ca_astuce('La première et la dernière fraction sont <strong>inverses</strong> '
                                . 'l’une de l’autre : leur produit vaut 1. Multiplier par 1 ne '
                                . 'change rien — le nombre du milieu est déjà la réponse.')
                    . ca_etapes([
                        $expr,
                        '(' . fraction($p, $q) . ' × ' . fraction($q, $p) . ') × ' . $m,
                        '1 × ' . $m,
                        '<strong>' . $total . '</strong>',
                      ])
                    . ca_regle('Deux fractions inverses ont pour produit 1 : on retourne l’une '
                             . 'pour obtenir l’autre. Les repérer dispense de tout calculer.');
            $verif     = "$p/$q*$m*$q/$p";
            $verif_rep = (string) $total;
            break;

        default:            // prendre une fraction d'un nombre : diviser d'abord
            $den = np_pick([3, 4, 5, 6, 7, 8, 9, 12]);
            do { $num = rand(2, $den - 1); } while (ca_pgcd($num, $den) !== 1);
            $k   = rand(2, 15);
            $N   = $den * $k;                 // le dénominateur divise le nombre
            $total = $num * $k;
            $d   = 1.8;
            $expr = fraction($num, $den) . ' × ' . $N;
            $q_html = ca_enonce($expr);
            $a_html = np_rep('<strong>' . $total . '</strong>')
                    . ca_astuce("On divise <strong>d'abord</strong> : $N ÷ $den = $k. On multiplie "
                                . "ensuite : $k × $num = $total. Commencer par la multiplication "
                                . 'obligerait à calculer ' . ($num * $N) . ' ÷ ' . $den . '.')
                    . ca_etapes([
                        $expr,
                        "($N ÷ $den) × $num",
                        "$k × $num",
                        '<strong>' . $total . '</strong>',
                      ])
                    . ca_regle('Multiplier par une fraction, c’est diviser par le dénominateur et '
                             . 'multiplier par le numérateur — et on a le droit de commencer par '
                             . 'celui des deux qui tombe juste.');
            $verif     = "$num/$den*$N";
            $verif_rep = (string) $total;
            break;
        }
        break;

    // ══ G — PUISSANCES DE 10 (cycle 4) ══════════════════════════════════════
    // Les nombres ensemble, les puissances ensemble. C'est la famille B,
    // rejouée sur les grands nombres.
    default:
        // [a, b, produit] : a et b en centièmes, produit en entier (10 ou 100)
        $paires = [[250, 400, 10], [400, 250, 10], [200, 500, 10], [500, 200, 10],
                   [2500, 400, 100], [400, 2500, 100], [2000, 500, 100], [500, 2000, 100],
                   [125, 800, 10], [800, 125, 10]];
        list($fa, $fb, $P) = np_pick($paires);
        $p1   = rand(2, 6);
        $p2   = rand(2, 6);
        $pP   = ($P === 100) ? 2 : 1;          // 100 = 10², 10 = 10¹
        $expo = $p1 + $p2 + $pP;
        $d    = 2.2;

        $expr = ca_nb($fa) . ' × ' . ca_p10($p1) . ' × ' . ca_nb($fb) . ' × ' . ca_p10($p2);
        $q_html = ca_enonce($expr);
        $a_html = np_rep('<strong>' . ca_p10($expo) . '</strong>')
                . ca_astuce('On met les nombres ensemble et les puissances ensemble : '
                            . ca_nb($fa) . ' × ' . ca_nb($fb) . ' = ' . $P . ', et '
                            . ca_p10($p1) . ' × ' . ca_p10($p2) . ' = ' . ca_p10($p1 + $p2)
                            . ' (on ajoute les exposants).')
                . ca_etapes([
                    $expr,
                    '(' . ca_nb($fa) . ' × ' . ca_nb($fb) . ') × (' . ca_p10($p1) . ' × ' . ca_p10($p2) . ')',
                    $P . ' × ' . ca_p10($p1 + $p2),
                    ca_p10($pP) . ' × ' . ca_p10($p1 + $p2),
                    '<strong>' . ca_p10($expo) . '</strong>',
                  ])
                . ca_regle('Dans un produit, on regroupe ce qui se ressemble. Les puissances de 10 '
                         . 'se multiplient en ajoutant les exposants.');
        $verif     = ca_txt($fa) . '*10**' . $p1 . '*' . ca_txt($fb) . '*10**' . $p2;
        $verif_rep = '10**' . $expo;
        break;
    }

    return [
        'type'          => 'calcul_astucieux',
        'famille'       => $type,
        'difficulte_id' => $d,
        'question'      => $q_html,
        'reponse'       => $a_html,
        'verif'         => $verif,       // texte brut, pour le contrôle automatique
        'verif_rep'     => $verif_rep,
    ];
}
