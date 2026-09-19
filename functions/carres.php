<?php
/**
 * Automatisme : carrés et racines carrées (calcul mental 4e-3e).
 * Difficulté : FACILE à MOYEN (range 1.0 - 2.4)
 * Format : réponse ouverte — un nombre, un encadrement, ou oui/non
 *
 * Historique : ce générateur ne produisait que les carrés de 1 à 12, pour la
 * session DNB et l'onglet « Carrés » de qf_calcul_mental.php. Son troisième type
 * de question — « quel nombre positif élevé au carré donne 144 ? » — était déjà
 * une racine carrée, mais sans le symbole. Le 21/08/2026 il gagne une page
 * autonome (qf_carres_racines.php), la notation √, et les propriétés.
 *
 * ⚠️ COMPATIBILITÉ DNB : appelé sans argument, il se comporte EXACTEMENT comme
 * avant — mêmes 36 combinaisons (12 nombres × 3 formulations), même anti-doublon
 * $_SESSION['dnb_carres_used'], mêmes formules de difficulté. Les familles
 * nouvelles ne sont accessibles qu'en passant explicitement $famille.
 */

/** Les carrés qu'un élève doit reconnaître : 1 à 20, plus les repères ronds. */
function ca_reperes() {
    $n = range(1, 20);
    return array_merge($n, [25, 30, 50, 100]);
}

/** Racine carrée entière, ou null si le nombre n'est pas un carré parfait. */
function ca_racine_entiere($n) {
    $r = (int)round(sqrt($n));
    return ($r * $r === $n) ? $r : null;
}

/** Une fraction à deux étages, pour montrer l'écriture avec barre. */
function ca_frac($n, $d) {
    return '<span class="fr"><span class="fr-n">' . $n . '</span>'
         . '<span class="fr-d">' . $d . '</span></span>';
}

/** L'avertissement commun aux cas où « ça marche » : constat, pas règle. */
function ca_avertissement() {
    return '<p>⚠️ À retenir comme un <strong>constat</strong>, pas comme une règle à appliquer '
         . 'partout : la démonstration générale est au programme de seconde. Ce qu’il faut garder '
         . 'du cycle 4, c’est que <strong>somme et différence d’un côté, produit et quotient de '
         . 'l’autre, ne se comportent pas pareil</strong>.</p>';
}

/** Le symbole racine, avec son trait sur le contenu.
 *  Le &radic; est enveloppé : sa hampe est trop courte dans la plupart des
 *  polices pour rejoindre le trait, le CSS de la page l'étire (.rac-s). */
function ca_rac($contenu) {
    return '<span class="rac"><span class="rac-s">&radic;</span>'
         . '<span class="rac-in">' . $contenu . '</span></span>';
}

/**
 * @param string $famille  '' = comportement DNB historique ; sinon une famille
 *                         de la page : c (carré), r (racine), a (√(a²) et (√a)²),
 *                         e (encadrer), v (somme ou produit).
 * @param string $pool_key clé de session, pour qu'une page n'entame pas le pool
 *                         d'une session DNB ouverte dans un autre onglet.
 */
function generer_carres($famille = '', $pool_key = 'carres_page_pool') {

    // ══ CHEMIN HISTORIQUE DU DNB — inchangé ═════════════════════════════════
    if ($famille === '') {
        if (!isset($_SESSION['dnb_carres_used'])) {
            $_SESSION['dnb_carres_used'] = [];
        }
        $all_combinations = [];
        for ($i = 1; $i <= 12; $i++) {
            for ($t = 1; $t <= 3; $t++) { $all_combinations[] = [$i, $t]; }
        }
        $available = array_filter($all_combinations, function ($combo) {
            return !in_array($combo, $_SESSION['dnb_carres_used']);
        });
        if (empty($available)) {
            $_SESSION['dnb_carres_used'] = [];
            $available = $all_combinations;
        }
        $available = array_values($available);
        $chosen = $available[array_rand($available)];
        list($n, $type_question) = $chosen;
        $_SESSION['dnb_carres_used'][] = $chosen;
        $carre = $n * $n;

        if ($type_question == 1) {
            $question = '<p>Calculer <strong>' . $n . '²</strong></p>';
            $reponse = '<p><strong>' . $n . '² = ' . $carre . '</strong></p>';
            $difficulte_id = 1.0 + ($n / 15);
        } elseif ($type_question == 2) {
            $question = '<p>Quel est le carré de <strong>' . $n . '</strong> ?</p>';
            $reponse = '<p>Le carré de <strong>' . $n . '</strong> est <strong>' . $carre . '</strong></p>';
            $difficulte_id = 1.1 + ($n / 14);
        } else {
            $question = '<p>Quel nombre <strong>positif</strong> &eacute;lev&eacute; au carr&eacute; donne <strong>' . $carre . '</strong> ?</p>';
            $reponse = '<p><strong>' . $n . '</strong> &eacute;lev&eacute; au carr&eacute; donne <strong>' . $carre . '</strong></p>';
            $difficulte_id = 1.3 + ($n / 12);
        }
        return ['type' => 'carres', 'difficulte_id' => $difficulte_id,
                'question' => $question, 'reponse' => $reponse];
    }

    // ══ LES FAMILLES DE LA PAGE ═════════════════════════════════════════════
    require_once __DIR__ . '/qf_communs.php';   // np_pick, np_rep, np_verif, np_enonce…

    $toutes = ['c', 'r', 'a', 'e', 'v'];
    if (in_array($famille, $toutes, true)) {
        $type = $famille;
    } else {                                    // '*' = mélange des cinq
        if (empty($_SESSION[$pool_key])) {
            $_SESSION[$pool_key] = $toutes;
            shuffle($_SESSION[$pool_key]);
        }
        $type = array_shift($_SESSION[$pool_key]);
    }

    $d = 1.5;

    switch ($type) {

    // ══ C — LE CARRÉ ════════════════════════════════════════════════════════
    case 'c':
        $n = np_pick(ca_reperes());
        $q = np_enonce('Calcule <strong>' . $n . '²</strong>.', 'Donne le résultat.');
        $astuce = '';
        if ($n % 10 === 0) {
            $u = intdiv($n, 10);
            $astuce = "<p>💡 $n = $u &times; 10, donc {$n}² = " . ($u * $u) . ' &times; 100 = <strong>'
                    . ($n * $n) . '</strong> : on carre le chiffre, puis on ajoute deux zéros.</p>';
        } elseif ($n % 10 === 5) {
            $dz = intdiv($n, 10);
            $astuce = "<p>💡 Un nombre qui finit par 5 : son carré finit toujours par <strong>25</strong>, "
                    . "et devant on écrit $dz &times; " . ($dz + 1) . ' = ' . ($dz * ($dz + 1))
                    . '. D’où <strong>' . ($n * $n) . '</strong>.</p>';
        }
        $r = np_rep('<strong>' . ($n * $n) . '</strong>')
           . "<p>Le carré de $n, c’est $n &times; $n = <strong>" . ($n * $n) . '</strong>.</p>'
           . $astuce
           . '<p>⚠️ « Au carré » ne veut pas dire « fois 2 » : ' . $n . '² = ' . ($n * $n)
           . ', alors que ' . $n . ' &times; 2 = ' . (2 * $n) . '.</p>';
        $d = ($n > 20) ? 1.8 : 1.0 + $n / 20;
        break;

    // ══ R — LA RACINE CARRÉE ════════════════════════════════════════════════
    case 'r':
        $n = np_pick(ca_reperes());
        $carre = $n * $n;
        $q = np_enonce('Calcule ' . ca_rac($carre) . '.', 'Donne le résultat.');
        $r = np_rep('<strong>' . $n . '</strong>')
           . '<p>' . ca_rac($carre) . ', c’est le nombre <strong>positif</strong> dont le carré '
           . "vaut $carre. Or $n &times; $n = $carre, donc " . ca_rac($carre) . " = <strong>$n</strong>.</p>"
           . '<p>Chercher une racine carrée, c’est <strong>remonter</strong> la table des carrés : '
           . 'c’est l’opération inverse du carré.</p>'
           . np_verif("{$n}² = $carre ✔");
        $d = ($n > 20) ? 2.0 : 1.3 + $n / 20;
        break;

    // ══ A — √(a²) ET (√a)² ══════════════════════════════════════════════════
    case 'a':
        if (rand(0, 1)) {
            $a = rand(2, 20);
            $q = np_enonce('Calcule ' . ca_rac($a . '²') . '.', 'Donne le résultat.');
            $r = np_rep("<strong>$a</strong>")
               . '<p>Inutile de calculer ' . $a . '² : la racine carrée <strong>défait</strong> le '
               . 'carré. Le nombre positif dont le carré vaut ' . $a . '² est ' . $a . ' lui-même.</p>'
               . '<p class="ca-regle">' . ca_rac('a²') . ' = a, pour tout nombre a positif.</p>'
               . '<p><em>La vérification si l’on y tient : ' . $a . '² = ' . ($a * $a) . ', et '
               . ca_rac($a * $a) . ' = ' . $a . '.</em></p>';
            $d = 2.0;
        } else {
            $a = np_pick([2, 3, 5, 6, 7, 8, 10, 11, 13, 15, 17, 20, 26, 30, 40, 50]);
            $q = np_enonce('Calcule <strong>(' . ca_rac($a) . ')²</strong>.', 'Donne le résultat.');
            $r = np_rep("<strong>$a</strong>")
               . '<p>' . ca_rac($a) . ' est le nombre positif dont le carré vaut ' . $a . ' : '
               . 'l’élever au carré redonne donc <strong>' . $a . '</strong>, exactement.</p>'
               . '<p class="ca-regle">(' . ca_rac('a') . ')² = a, pour tout nombre a positif.</p>'
               . '<p>⚠️ Et ce, même si ' . ca_rac($a) . ' ne « tombe pas juste ». Pas besoin '
               . 'd’en chercher une valeur approchée : les deux opérations s’annulent.</p>';
            $d = 2.3;
        }
        break;

    // ══ E — ENCADRER UNE RACINE ═════════════════════════════════════════════
    case 'e':
        do {
            $n = rand(2, 400);
        } while (ca_racine_entiere($n) !== null);
        $k = (int)floor(sqrt($n));
        $q = np_enonce('Entre quels <strong>entiers consécutifs</strong> se trouve '
                       . ca_rac($n) . ' ?',
                       'Réponds sous la forme … &lt; ' . ca_rac($n) . ' &lt; …');
        $r = np_rep('<strong>' . $k . ' &lt; ' . ca_rac($n) . ' &lt; ' . ($k + 1) . '</strong>')
           . "<p>On encadre $n par les deux carrés parfaits qui l’entourent :</p>"
           . '<p class="ca-regle">' . ($k * $k) . " &lt; $n &lt; " . (($k + 1) * ($k + 1)) . '</p>'
           . "<p>c’est-à-dire {$k}² &lt; $n &lt; " . ($k + 1) . '². En prenant la racine carrée de '
           . 'chaque membre — ce qui conserve l’ordre entre nombres positifs — on obtient '
           . '<strong>' . $k . ' &lt; ' . ca_rac($n) . ' &lt; ' . ($k + 1) . '</strong>.</p>'
           . '<p>💡 C’est la seule façon d’estimer une racine sans calculatrice : on cherche '
           . 'entre quels carrés de la table le nombre se glisse.</p>';
        $d = 2.4;
        break;

    // ══ V — SOMME, DIFFÉRENCE, PRODUIT, QUOTIENT ════════════════════════════
    // Le tableau complet : la racine ne se distribue PAS sur l'addition ni sur
    // la soustraction, mais sur le produit et le quotient le calcul retombe
    // juste. Les quatre cas côte à côte, c'est ce qui fait tenir la distinction.
    case 'v':
        // Les couples pythagoriciens : sans eux, la racine de la somme (ou de
        // la différence) de deux carrés ne tombe pas juste.
        $PYTH = [[3, 4, 5], [6, 8, 10], [5, 12, 13], [9, 12, 15], [8, 15, 17],
                 [12, 16, 20], [7, 24, 25], [20, 21, 29]];

        switch (np_pick(['somme', 'difference', 'produit', 'quotient'])) {

        case 'somme':
            list($a, $b, $c) = np_pick($PYTH);
            $q = np_enonce('Ces deux nombres sont-ils égaux ? &nbsp;'
                           . ca_rac($a * $a . ' + ' . $b * $b) . ' &nbsp;et&nbsp; '
                           . ca_rac($a * $a) . ' + ' . ca_rac($b * $b),
                           'Réponds par oui ou non, en calculant les deux.');
            $r = np_rep('<strong>NON</strong>')
               . '<p class="ca-regle">' . ca_rac($a * $a . ' + ' . $b * $b) . ' = '
               . ca_rac($a * $a + $b * $b) . ' = <strong>' . $c . '</strong></p>'
               . '<p class="ca-regle">' . ca_rac($a * $a) . ' + ' . ca_rac($b * $b)
               . " = $a + $b = <strong>" . ($a + $b) . '</strong></p>'
               . "<p>$c d’un côté, " . ($a + $b) . ' de l’autre : la racine carrée '
               . '<strong>ne se distribue pas sur l’addition</strong>. On calcule d’abord '
               . 'ce qui est <strong>sous</strong> le trait, ensuite seulement la racine.</p>'
               . '<p>💡 C’est exactement ce que dit le théorème de Pythagore : l’hypoténuse d’un '
               . "triangle de côtés $a et $b mesure $c, et non " . ($a + $b) . ' — sinon le '
               . 'chemin direct serait aussi long que le détour.</p>';
            $d = 2.4;
            break;

        case 'difference':
            list($a, $b, $c) = np_pick($PYTH);
            $q = np_enonce('Ces deux nombres sont-ils égaux ? &nbsp;'
                           . ca_rac($c * $c . ' &minus; ' . $a * $a) . ' &nbsp;et&nbsp; '
                           . ca_rac($c * $c) . ' &minus; ' . ca_rac($a * $a),
                           'Réponds par oui ou non, en calculant les deux.');
            $r = np_rep('<strong>NON</strong>')
               . '<p class="ca-regle">' . ca_rac($c * $c . ' &minus; ' . $a * $a) . ' = '
               . ca_rac($c * $c - $a * $a) . ' = <strong>' . $b . '</strong></p>'
               . '<p class="ca-regle">' . ca_rac($c * $c) . ' &minus; ' . ca_rac($a * $a)
               . " = $c &minus; $a = <strong>" . ($c - $a) . '</strong></p>'
               . "<p>$b d’un côté, " . ($c - $a) . ' de l’autre : la soustraction se comporte '
               . 'comme l’addition, <strong>la racine ne s’y distribue pas</strong>.</p>'
               . '<p>💡 Là encore c’est Pythagore : dans un triangle rectangle d’hypoténuse '
               . "$c et de côté $a, l’autre côté mesure $b.</p>";
            $d = 2.4;
            break;

        case 'produit':
            do { $a = rand(2, 9); $b = rand(2, 9); } while ($a === $b);
            $q = np_enonce('Ces deux nombres sont-ils égaux ? &nbsp;'
                           . ca_rac($a * $a . ' &times; ' . $b * $b) . ' &nbsp;et&nbsp; '
                           . ca_rac($a * $a) . ' &times; ' . ca_rac($b * $b),
                           'Réponds par oui ou non, en calculant les deux.');
            $r = np_rep('<strong>OUI</strong>')
               . '<p class="ca-regle">' . ca_rac($a * $a . ' &times; ' . $b * $b) . ' = '
               . ca_rac($a * $a * $b * $b) . ' = <strong>' . ($a * $b) . '</strong></p>'
               . '<p class="ca-regle">' . ca_rac($a * $a) . ' &times; ' . ca_rac($b * $b)
               . " = $a &times; $b = <strong>" . ($a * $b) . '</strong></p>'
               . '<p>Cette fois les deux calculs donnent <strong>le même résultat</strong>. '
               . 'Sur un <strong>produit</strong>, la racine carrée retombe juste — alors que '
               . 'sur une somme ou une différence, non.</p>' . ca_avertissement();
            $d = 2.2;
            break;

        case 'quotient':
            $b = rand(2, 6);
            $k = rand(2, 6);
            $a = $b * $k;                        // pour que le quotient tombe juste
            $q = np_enonce('Ces deux nombres sont-ils égaux ? &nbsp;'
                           . ca_rac($a * $a . ' &divide; ' . $b * $b) . ' &nbsp;et&nbsp; '
                           . ca_rac($a * $a) . ' &divide; ' . ca_rac($b * $b),
                           'Réponds par oui ou non, en calculant les deux.');
            $r = np_rep('<strong>OUI</strong>')
               . '<p class="ca-regle">' . ca_rac($a * $a . ' &divide; ' . $b * $b) . ' = '
               . ca_rac($a * $a / ($b * $b)) . ' = <strong>' . $k . '</strong></p>'
               . '<p class="ca-regle">' . ca_rac($a * $a) . ' &divide; ' . ca_rac($b * $b)
               . " = $a &divide; $b = <strong>$k</strong></p>"
               . '<p>Comme pour le produit, les deux chemins mènent au même nombre. '
               . 'Avec une barre de fraction, cela s’écrit :</p>'
               . '<p class="ca-regle">' . ca_rac(ca_frac($a * $a, $b * $b)) . ' = '
               . ca_frac(ca_rac($a * $a), ca_rac($b * $b)) . ' = ' . ca_frac($a, $b)
               . ' = <strong>' . $k . '</strong></p>' . ca_avertissement();
            $d = 2.3;
            break;
        }
        break;
    }

    return [
        'type'          => 'carres',
        'famille'       => $type,
        'difficulte_id' => $d,
        'question'      => $q,
        'reponse'       => $r,
    ];
}
