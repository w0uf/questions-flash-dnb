<?php
/**
 * Automatisme : calculer la valeur d'une expression littérale (cycle 4).
 * Difficulté : FACILE à MOYEN (range 1.1 - 2.2)
 * Format : réponse ouverte (un nombre), ou oui/non pour la famille « solution »
 *
 * Historique : ce générateur n'alimentait que la session d'entraînement au DNB
 * et ne renvoyait qu'un résultat nu. Le 21/08/2026 il gagne une page autonome
 * (qf_valeur_expression.php), donc des corrections DÉTAILLÉES — la substitution
 * étape par étape — et deux familles supplémentaires.
 *
 * ⚠️ COMPATIBILITÉ DNB : appelé sans argument, il se comporte EXACTEMENT comme
 * avant — même pool de 12 tirages, mêmes types, mêmes plages de valeurs. Les
 * deux familles nouvelles (valeur négative, tester une solution) ne sont
 * accessibles qu'en passant explicitement $famille : la difficulté d'une session
 * DNB en cours n'est pas modifiée.
 */

/** Un nombre relatif tel qu'on l'écrit DANS un calcul : (−2), ou 5. */
function ve_nb($x) {
    $s = str_replace('.', ',', (string)(0 + $x));
    return $x < 0 ? '(&minus;' . ltrim($s, '-') . ')' : $s;
}

/** Un nombre écrit seul (résultat) : −2 ou 5. */
function ve_res($x) {
    $s = str_replace('.', ',', (string)(0 + $x));
    return str_replace('-', '&minus;', $s);
}

/** « + 5 » ou « − 5 », pour accrocher une constante à une expression. */
function ve_suite($c) {
    return ($c >= 0) ? ' + ' . $c : ' &minus; ' . abs($c);
}

/** Le terme en la variable, sans coefficient 1 : 3a, a, 3a². */
function ve_terme($coef, $var, $suffixe = '') {
    return ($coef == 1 ? '' : $coef) . $var . $suffixe;
}

/** L'encadré du haut de correction. */
function ve_rep($t) { return '<p class="np-rep"><strong>' . $t . '</strong></p>'; }

/** Le rappel de méthode, au bas de chaque correction. */
function ve_methode() {
    return '<p class="np-equation">Méthode : on <strong>remplace</strong> la lettre par sa valeur, '
         . 'sans oublier les signes × sous-entendus, puis on <strong>calcule</strong> en respectant '
         . 'les priorités — parenthèses, puis carrés, puis multiplications, puis additions.</p>';
}

/**
 * @param string $famille  '' = comportement DNB historique ; '*' = mélange des six
 *                         familles de la page ; sinon une famille précise
 *                         (simple, parenthese, carre, trinome, negatif, solution).
 * @param string $pool_key clé de session du pool, pour qu'une page n'entame pas
 *                         le pool d'une session DNB ouverte dans un autre onglet.
 */
function generer_valeur_expression($famille = '', $pool_key = 'valeur_expression_pool') {

    $variables = ['a', 'b', 'c', 'd', 'p', 'q', 'x'];

    // ── Choix du type ───────────────────────────────────────────────────────
    $familles_page = ['simple', 'parenthese', 'carre', 'trinome', 'negatif', 'solution'];

    if (in_array($famille, $familles_page, true)) {
        $type = $famille;
        $decimal = ($famille === 'simple' || $famille === 'parenthese') ? (rand(0, 3) === 0) : false;
    } elseif ($famille === '*') {
        if (empty($_SESSION[$pool_key])) {
            $_SESSION[$pool_key] = $familles_page;
            shuffle($_SESSION[$pool_key]);
        }
        $type = array_shift($_SESSION[$pool_key]);
        $decimal = ($type === 'simple' || $type === 'parenthese') ? (rand(0, 3) === 0) : false;
    } else {
        // ── Chemin historique du DNB : pool de 12, à l'identique ────────────
        if (!isset($_SESSION['valeur_expression_pool']) || empty($_SESSION['valeur_expression_pool'])) {
            $_SESSION['valeur_expression_pool'] = [
                'simple_entier_1', 'simple_entier_2', 'simple_decimal',
                'parenthese_entier_1', 'parenthese_entier_2', 'parenthese_decimal',
                'puissance_1', 'puissance_2', 'puissance_3',
                'puissance_termes_1', 'puissance_termes_2', 'puissance_termes_3',
            ];
            shuffle($_SESSION['valeur_expression_pool']);
        }
        $cle = array_shift($_SESSION['valeur_expression_pool']);
        $decimal = (substr($cle, -7) === 'decimal');
        if (strpos($cle, 'simple') === 0)          $type = 'simple';
        elseif (strpos($cle, 'parenthese') === 0)  $type = 'parenthese';
        elseif (strpos($cle, 'puissance_termes') === 0) $type = 'trinome';
        else                                       $type = 'carre';
        // La variable x n'existait pas dans la version d'origine ; on la garde
        // hors du tirage DNB pour ne rien changer à ce que les élèves y voient.
        $variables = ['a', 'b', 'c', 'd', 'p', 'q'];
    }

    $var = $variables[array_rand($variables)];
    $d = 1.1;

    switch ($type) {

    // ══ ax + b ══════════════════════════════════════════════════════════════
    case 'simple':
        if ($decimal) {
            $coef = [2, 4, 6, 8][rand(0, 3)];
            $cst  = rand(1, 8);
            $val  = [0.5, 1.5, 2.5][rand(0, 2)];
            $d = 1.2;
        } else {
            $coef = rand(2, 5);
            $cst  = rand(-5, 10); if ($cst == 0) $cst = rand(1, 5);
            $val  = rand(2, 5);
            $d = 1.1;
        }
        $prod = $coef * $val;
        $res  = $prod + $cst;
        $expr = ve_terme($coef, $var) . ve_suite($cst);

        $q = '<p class="np-phrase">Calcule <strong>' . $expr . '</strong> pour <strong>'
           . $var . ' = ' . ve_res($val) . '</strong>.</p>'
           . '<p class="np-demande">Donne le résultat.</p>';
        $r = ve_rep(ve_res($res))
           . '<p>On remplace ' . $var . ' par ' . ve_res($val) . ' — et l’on n’oublie pas que '
           . '<strong>' . ve_terme($coef, $var) . ' signifie ' . $coef . ' × ' . $var . '</strong> :</p>'
           . '<p class="ve-calcul">' . $coef . ' × ' . ve_nb($val) . ve_suite($cst)
           . ' = ' . ve_res($prod) . ve_suite($cst) . ' = <strong>' . ve_res($res) . '</strong></p>'
           . ve_methode();
        break;

    // ══ a(bx + c) ═══════════════════════════════════════════════════════════
    case 'parenthese':
        if ($decimal) {
            $ext = rand(2, 4); $int = [2, 4, 6][rand(0, 2)]; $cst = rand(1, 4); $val = 0.5;
            $d = 1.3;
        } else {
            $ext = rand(2, 4); $int = rand(2, 4); $cst = rand(1, 5); $val = rand(2, 3);
            $d = 1.2;
        }
        $dedans = $int * $val + $cst;
        $res    = $ext * $dedans;
        $expr   = $ext . '(' . ve_terme($int, $var) . ' + ' . $cst . ')';

        $q = '<p class="np-phrase">Calcule <strong>' . $expr . '</strong> pour <strong>'
           . $var . ' = ' . ve_res($val) . '</strong>.</p>'
           . '<p class="np-demande">Donne le résultat.</p>';
        $r = ve_rep(ve_res($res))
           . '<p>⚠️ On commence par ce qui est <strong>entre parenthèses</strong> — et le nombre '
           . 'collé devant la parenthèse est un facteur : ' . $ext . '( … ) signifie '
           . $ext . ' × ( … ).</p>'
           . '<p class="ve-calcul">' . $ext . ' × (' . $int . ' × ' . ve_nb($val) . ' + ' . $cst . ') = '
           . $ext . ' × (' . ve_res($int * $val) . ' + ' . $cst . ') = '
           . $ext . ' × ' . ve_res($dedans) . ' = <strong>' . ve_res($res) . '</strong></p>'
           . ve_methode();
        break;

    // ══ ax² + b ═════════════════════════════════════════════════════════════
    case 'carre':
        $coef = rand(2, 4);
        $cst  = rand(-3, 8); if ($cst == 0) $cst = rand(1, 5);
        $val  = rand(2, 4);
        $carre = $val * $val;
        $prod  = $coef * $carre;
        $res   = $prod + $cst;
        $expr  = ve_terme($coef, $var, '²') . ve_suite($cst);
        $d = 1.3;

        $q = '<p class="np-phrase">Calcule <strong>' . $expr . '</strong> pour <strong>'
           . $var . ' = ' . $val . '</strong>.</p>'
           . '<p class="np-demande">Donne le résultat.</p>';
        $r = ve_rep(ve_res($res))
           . '<p>⚠️ Le carré porte sur la <strong>lettre seule</strong> : dans ' . $coef . $var . '², '
           . 'on élève ' . $var . ' au carré, <em>puis</em> on multiplie par ' . $coef . '. On ne '
           . 'calcule pas (' . $coef . ' × ' . $val . ')².</p>'
           . '<p class="ve-calcul">' . $coef . ' × ' . $val . '² ' . trim(ve_suite($cst)) . ' = '
           . $coef . ' × ' . $carre . ve_suite($cst) . ' = ' . ve_res($prod) . ve_suite($cst)
           . ' = <strong>' . ve_res($res) . '</strong></p>'
           . ve_methode();
        break;

    // ══ ax² + bx + c ════════════════════════════════════════════════════════
    case 'trinome':
        $ca  = rand(1, 3);
        $cb  = rand(2, 4);
        $cst = rand(-3, 6); if ($cst == 0) $cst = rand(1, 4);
        $val = rand(2, 3);
        $t1  = $ca * $val * $val;
        $t2  = $cb * $val;
        $res = $t1 + $t2 + $cst;
        $expr = ve_terme($ca, $var, '²') . ' + ' . ve_terme($cb, $var) . ve_suite($cst);
        $d = 1.4;

        $q = '<p class="np-phrase">Calcule <strong>' . $expr . '</strong> pour <strong>'
           . $var . ' = ' . $val . '</strong>.</p>'
           . '<p class="np-demande">Donne le résultat.</p>';
        $r = ve_rep(ve_res($res))
           . '<p>On remplace la lettre <strong>partout où elle apparaît</strong>, ici deux fois :</p>'
           // Coefficient 1 : on n'écrit pas « 1 × 3² », l'expression de départ
           // n'affiche pas ce 1 non plus.
           . '<p class="ve-calcul">' . ($ca == 1 ? '' : $ca . ' × ') . $val . '² + '
           . $cb . ' × ' . $val . ve_suite($cst)
           . ' = ' . ($ca == 1 ? '' : $ca . ' × ') . ($val * $val) . ' + ' . $t2 . ve_suite($cst)
           . ($ca == 1 ? '' : ' = ' . $t1 . ' + ' . $t2 . ve_suite($cst))
           . ' = <strong>' . ve_res($res) . '</strong></p>'
           . ve_methode();
        break;

    // ══ VALEUR NÉGATIVE — le piège des parenthèses ══════════════════════════
    case 'negatif':
        $avec_carre = (bool)rand(0, 1);
        $val = -rand(2, 5);
        if ($avec_carre) {
            $coef = rand(2, 4);
            $cst  = rand(1, 8);
            $carre = $val * $val;
            $prod  = $coef * $carre;
            $res   = $prod + $cst;
            $expr  = ve_terme($coef, $var, '²') . ' + ' . $cst;
            $detail = '<p>⚠️ Il faut écrire la valeur <strong>entre parenthèses</strong> : '
                    . $var . '² devient ' . ve_nb($val) . '², et non &minus;' . abs($val) . '².</p>'
                    . '<p>' . ve_nb($val) . '² = ' . ve_nb($val) . ' × ' . ve_nb($val) . ' = '
                    . $carre . ' : <strong>le carré d’un nombre négatif est positif</strong>.</p>'
                    . '<p class="ve-calcul">' . $coef . ' × ' . ve_nb($val) . '² + ' . $cst . ' = '
                    . $coef . ' × ' . $carre . ' + ' . $cst . ' = ' . $prod . ' + ' . $cst
                    . ' = <strong>' . ve_res($res) . '</strong></p>';
            $d = 2.2;
        } else {
            $coef = rand(2, 5);
            $cst  = rand(1, 10);
            $prod = $coef * $val;
            $res  = $prod + $cst;
            $expr = ve_terme($coef, $var) . ' + ' . $cst;
            $detail = '<p>⚠️ On écrit la valeur <strong>entre parenthèses</strong> : '
                    . $coef . ' × ' . ve_nb($val) . ', sinon on obtient une écriture illisible '
                    . 'comme « ' . $coef . ' × &minus;' . abs($val) . ' ».</p>'
                    . '<p class="ve-calcul">' . $coef . ' × ' . ve_nb($val) . ' + ' . $cst . ' = '
                    . ve_res($prod) . ' + ' . $cst . ' = <strong>' . ve_res($res) . '</strong></p>'
                    . '<p>Signes différents, produit négatif : ' . $coef . ' × ' . ve_nb($val)
                    . ' = ' . ve_res($prod) . '.</p>';
            $d = 1.9;
        }

        $q = '<p class="np-phrase">Calcule <strong>' . $expr . '</strong> pour <strong>'
           . $var . ' = ' . ve_res($val) . '</strong>.</p>'
           . '<p class="np-demande">Donne le résultat, signe compris.</p>';
        $r = ve_rep(ve_res($res)) . $detail . ve_methode();
        break;

    // ══ TESTER UNE VALEUR ═══════════════════════════════════════════════════
    case 'solution':
        $coef = rand(2, 5);
        $cst  = rand(-6, 9); if ($cst == 0) $cst = rand(1, 5);
        $val  = rand(2, 6);
        $juste = (bool)rand(0, 1);
        $vraie = $coef * $val + $cst;
        // Pas de np_pick ici : ce fichier est chargé par la session DNB, on le
        // garde sans dépendance à qf_communs.php.
        $ecarts = [-3, -2, -1, 1, 2, 3];
        $cible  = $juste ? $vraie : $vraie + $ecarts[array_rand($ecarts)];
        $expr = ve_terme($coef, $var) . ve_suite($cst);
        $d = 2.0;

        $q = '<p class="np-phrase">Le nombre <strong>' . $val . '</strong> est-il solution de '
           . '<strong>' . $expr . ' = ' . ve_res($cible) . '</strong> ?</p>'
           . '<p class="np-demande">Réponds par oui ou non, en le justifiant.</p>';
        $r = ve_rep($juste ? 'OUI' : 'NON')
           . '<p>Tester une valeur, c’est calculer <strong>séparément</strong> les deux membres et '
           . 'les comparer. Ici le membre de gauche vaut :</p>'
           . '<p class="ve-calcul">' . $coef . ' × ' . ve_nb($val) . ve_suite($cst) . ' = '
           . ve_res($coef * $val) . ve_suite($cst) . ' = <strong>' . ve_res($vraie) . '</strong></p>'
           . '<p>' . ($juste
                ? 'On retrouve bien ' . ve_res($cible) . ' : l’égalité est <strong>vraie</strong> '
                  . 'pour ' . $var . ' = ' . $val . ', donc ' . $val . ' est solution.'
                : 'On trouve ' . ve_res($vraie) . ', et non ' . ve_res($cible)
                  . ' : l’égalité est <strong>fausse</strong>, donc ' . $val . ' n’est pas solution.')
           . '</p>'
           . '<p>💡 Tester ne demande <strong>aucune résolution</strong> : on remplace, on calcule, '
           . 'on compare. C’est aussi le meilleur moyen de vérifier une équation déjà résolue.</p>';
        break;
    }

    return [
        'type'          => 'valeur_expression',
        'famille'       => $type,
        'difficulte_id' => $d,
        'question'      => $q,
        'reponse'       => $r,
    ];
}
