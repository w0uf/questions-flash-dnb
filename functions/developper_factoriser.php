<?php
/**
 * Automatisme : Développer et factoriser une expression simple
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.5)
 * Format : Réponse directe (expression développée ou factorisée)
 */

function generer_developper_factoriser($famille = '') {
    // Filtre optionnel de famille, traité AVANT la logique de pool historique :
    // une page hôte peut ainsi cibler un type de travail sans que le tirage du
    // DNB (sans argument) ne change d'un iota.
    $par_famille = [
        'developper'  => ['developper_simple_1', 'developper_simple_moins', 'developper_oppose',
                          'developper_parentheses_moins'],
        'double'      => ['developper_double_1', 'developper_double_moins', 'developper_carre_1'],
        'factoriser'  => ['factoriser_simple_1', 'factoriser_nombre', 'factoriser_mixte',
                          'factoriser_diff_carres_1'],
    ];
    if (isset($par_famille[$famille])) {
        $liste = $par_famille[$famille];
        return df_construire($liste[array_rand($liste)]);
    }

    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================

    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['developper_factoriser_pool']) || empty($_SESSION['developper_factoriser_pool'])) {
        $_SESSION['developper_factoriser_pool'] = [
            // Sous-types ajoutés en août 2026 : le fichier ne produisait que des
            // « + ». Or c'est le signe moins — dans la parenthèse, devant la
            // parenthèse, ou devant le facteur — qui fait échouer les élèves.
            'developper_simple_moins',
            'developper_oppose',
            'developper_parentheses_moins',
            'developper_double_moins',
            'factoriser_nombre',
            'factoriser_mixte',

            // DÉVELOPPER : a(bx + c) = abx + ac (simple distributivité)
            'developper_simple_1',
            'developper_simple_2',
            
            // DÉVELOPPER : (ax + b)(cx + d) (double distributivité)
            'developper_double_1',
            'developper_double_2',
            'developper_double_3',
            
            // DÉVELOPPER : (ax + b)² (identité remarquable)
            'developper_carre_1',
            'developper_carre_2',
            
            // FACTORISER : ax² + bx = x(ax + b)
            'factoriser_simple_1',
            'factoriser_simple_2',
            
            // FACTORISER : a²x² - b² = (ax - b)(ax + b) (différence de carrés)
            'factoriser_diff_carres_1',
            'factoriser_diff_carres_2',
            'factoriser_diff_carres_3'
        ];
        shuffle($_SESSION['developper_factoriser_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['developper_factoriser_pool']);

    return df_construire($type_question);
}

/**
 * Construit la question correspondant à un sous-type.
 * (Extrait de generer_developper_factoriser() en août 2026 pour être appelable
 * aussi bien par le pool du DNB que par le filtre de famille d'une page QF.)
 */
function df_construire($type_question) {
    // Variables disponibles
    $variables = ['a', 'b', 'c', 'd', 'p', 'q', 'x', 'y'];
    
    // ========================================
    // GÉNÉRER LA QUESTION SELON LE TYPE
    // ========================================
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // DÉVELOPPER SIMPLE : a(bx + c) = abx + ac
        // ============================================
        
        case 'developper_simple_1':
        case 'developper_simple_2':
            $var = $variables[array_rand($variables)];
            $a = rand(2, 5);
            $b = rand(2, 6);
            $c = rand(2, 9);
            
            $resultat_coef = $a * $b;
            $resultat_const = $a * $c;
            
            // Affichage sans coefficient 1
            $terme_var = ($b == 1) ? $var : $b . $var;
            $terme_resultat = ($resultat_coef == 1) ? $var : $resultat_coef . $var;
            
            $question_html = '<p>Développer ' . $a . '(' . $terme_var . ' + ' . $c . ')</p>';
            $reponse_html = '<p><strong>' . $terme_resultat . ' + ' . $resultat_const . '</strong></p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // DÉVELOPPER DOUBLE : (ax + b)(cx + d)
        // ============================================
        
        case 'developper_double_1':
        case 'developper_double_2':
        case 'developper_double_3':
            $var = $variables[array_rand($variables)];
            $a = rand(2, 4);
            $b = rand(1, 5);
            $c = rand(2, 4);
            $d = rand(1, 5);
            
            // Calcul : ac·x² + (ad + bc)·x + bd
            $coef_x2 = $a * $c;
            $coef_x = $a * $d + $b * $c;
            $coef_const = $b * $d;
            
            // Affichage des termes sans coefficient 1
            $terme_x2 = ($coef_x2 == 1) ? $var . '²' : $coef_x2 . $var . '²';
            $terme_x = ($coef_x == 1) ? $var : $coef_x . $var;
            
            // Affichage des facteurs
            $fact1_var = ($a == 1) ? $var : $a . $var;
            $fact2_var = ($c == 1) ? $var : $c . $var;
            
            // Construction de l'expression développée
            $expression = $terme_x2 . ' + ' . $terme_x . ' + ' . $coef_const;
            
            $question_html = '<p>Développer (' . $fact1_var . ' + ' . $b . ')(' . $fact2_var . ' + ' . $d . ')</p>';
            $reponse_html = '<p><strong>' . $expression . '</strong></p>';
            $difficulte = 1.5;
            break;
            
        // ============================================
        // DÉVELOPPER CARRÉ : (ax + b)²
        // ============================================
        
        case 'developper_carre_1':
        case 'developper_carre_2':
            $var = $variables[array_rand($variables)];
            $a = rand(2, 4);
            $b = rand(2, 5);
            
            // Calcul : a²x² + 2ab·x + b²
            $coef_x2 = $a * $a;
            $coef_x = 2 * $a * $b;
            $coef_const = $b * $b;
            
            // Affichage des termes sans coefficient 1
            $terme_x2 = ($coef_x2 == 1) ? $var . '²' : $coef_x2 . $var . '²';
            $terme_x = ($coef_x == 1) ? $var : $coef_x . $var;
            
            // Affichage du facteur
            $fact_var = ($a == 1) ? $var : $a . $var;
            
            $question_html = '<p>Développer (' . $fact_var . ' + ' . $b . ')²</p>';
            $reponse_html = '<p><strong>' . $terme_x2 . ' + ' . $terme_x . ' + ' . $coef_const . '</strong></p>';
            $difficulte = 1.5;
            break;
            
        // ============================================
        // FACTORISER SIMPLE : ax² + bx = x(ax + b)
        // ============================================
        
        case 'factoriser_simple_1':
        case 'factoriser_simple_2':
            $var = $variables[array_rand($variables)];
            // a et b premiers entre eux : sans cela, ax² + bx = x(ax + b) n'est
            // PAS la factorisation complète (3a² + 3a valait « a(3a + 3) », alors
            // que 3a(a + 1) est tout aussi juste — deux réponses possibles).
            do {
                $a = rand(2, 6);
                $b = rand(2, 9);
            } while (df_pgcd($a, $b) != 1);


            // Affichage sans coefficient 1
            $terme_x2 = ($a == 1) ? $var . '²' : $a . $var . '²';
            $terme_x = ($b == 1) ? $var : $b . $var;
            $fact_x = ($a == 1) ? $var : $a . $var;
            
            $question_html = '<p>Factoriser ' . $terme_x2 . ' + ' . $terme_x . '</p>';
            $reponse_html = '<p><strong>' . $var . '(' . $fact_x . ' + ' . $b . ')</strong></p>';
            $difficulte = 1.4;
            break;
            
        // ============================================
        // FACTORISER DIFFÉRENCE DE CARRÉS : a²x² - b² = (ax - b)(ax + b)
        // ============================================
        
        case 'factoriser_diff_carres_1':
        case 'factoriser_diff_carres_2':
        case 'factoriser_diff_carres_3':
            $var = $variables[array_rand($variables)];
            
            // Choisir a et b tels que a² et b² soient des carrés parfaits, et
            // PREMIERS ENTRE EUX : sinon la factorisation n'est pas complète
            // (4b² − 4 donnait « (2b − 2)(2b + 2) », alors que 4(b − 1)(b + 1)
            // est aussi juste — la question aurait deux réponses).
            do {
                $a = rand(1, 4); // a peut être 1 pour avoir x² - 16
                $b = rand(2, 5);
            } while (df_pgcd($a, $b) != 1);


            $a_carre = $a * $a;
            $b_carre = $b * $b;
            
            // Affichage sans coefficient 1 pour a²x²
            if ($a == 1) {
                $terme_x2 = $var . '²';
                $fact_var = $var;
            } else {
                $terme_x2 = ($a_carre == 1) ? $var . '²' : $a_carre . $var . '²';
                $fact_var = ($a == 1) ? $var : $a . $var;
            }
            
            $question_html = '<p>Factoriser ' . $terme_x2 . ' &minus; ' . $b_carre . '</p>';
            $reponse_html = '<p><strong>(' . $fact_var . ' &minus; ' . $b . ')(' . $fact_var . ' + ' . $b . ')</strong></p>'
                          . '<p style="font-size:0.9em; color:#666;">C\'est une différence de deux carrés : '
                          . $terme_x2 . ' &minus; ' . $b_carre . ' = ' . ($a == 1 ? '' : '(' . $fact_var . ')') . ($a == 1 ? $var . '²' : '²')
                          . ' &minus; ' . $b . '², de la forme a² &minus; b² = (a &minus; b)(a + b).</p>';
            $difficulte = 1.5;
            break;

        // ============================================
        // DÉVELOPPER avec un MOINS dans la parenthèse : a(bx − c)
        // ============================================

        case 'developper_simple_moins':
            $var = $variables[array_rand($variables)];
            $a = rand(2, 6);
            $b = rand(2, 6);
            $c = rand(2, 9);

            $terme_var = ($b == 1) ? $var : $b . $var;
            $res_coef  = $a * $b;
            $res_const = $a * $c;
            $terme_res = ($res_coef == 1) ? $var : $res_coef . $var;

            $question_html = '<p>Développer ' . $a . '(' . $terme_var . ' &minus; ' . $c . ')</p>';
            $reponse_html  = '<p><strong>' . $terme_res . ' &minus; ' . $res_const . '</strong></p>'
                           . '<p style="font-size:0.9em; color:#666;">' . $a . ' × ' . $terme_var . ' = ' . $terme_res
                           . ' &nbsp;et&nbsp; ' . $a . ' × ' . $c . ' = ' . $res_const . '. Le facteur multiplie '
                           . '<strong>les deux</strong> termes, et le signe &minus; est conservé.</p>';
            $difficulte = 1.3;
            break;

        // ============================================
        // DÉVELOPPER avec un facteur NÉGATIF : −a(bx + c)
        // ============================================

        case 'developper_oppose':
            $var = $variables[array_rand($variables)];
            $a = rand(2, 5);
            $b = rand(2, 6);
            $c = rand(2, 9);
            $signe_interne = (rand(0, 1) === 0) ? '+' : '&minus;';

            $terme_var = ($b == 1) ? $var : $b . $var;
            $res_coef  = $a * $b;
            $res_const = $a * $c;
            $terme_res = ($res_coef == 1) ? $var : $res_coef . $var;

            // −a(bx + c) = −abx − ac      /      −a(bx − c) = −abx + ac
            $signe_const = ($signe_interne === '+') ? '&minus;' : '+';

            $question_html = '<p>Développer &minus;' . $a . '(' . $terme_var . ' ' . $signe_interne . ' ' . $c . ')</p>';
            $reponse_html  = '<p><strong>&minus;' . $terme_res . ' ' . $signe_const . ' ' . $res_const . '</strong></p>'
                           . '<p style="font-size:0.9em; color:#666;">Le facteur est <strong>négatif</strong> : il change '
                           . 'le signe des deux termes. &minus;' . $a . ' × ' . $terme_var . ' = &minus;' . $terme_res
                           . ' et &minus;' . $a . ' × ' . ($signe_interne === '+' ? '' : '(&minus;') . $c
                           . ($signe_interne === '+' ? '' : ')') . ' = ' . $signe_const . ' ' . $res_const . '.</p>';
            $difficulte = 1.7;
            break;

        // ============================================
        // SUPPRIMER une parenthèse précédée d'un MOINS
        // ============================================

        case 'developper_parentheses_moins':
            $var = $variables[array_rand($variables)];
            $a = rand(3, 9);
            $b = rand(2, 9);
            $c = rand(2, 6);
            $d = rand(2, 9);

            $coef_final = $a - $c;
            $const_final = $b + $d;
            $terme_final = ($coef_final == 1) ? $var : (($coef_final == -1) ? '&minus;' . $var : $coef_final . $var);

            $question_html = '<p>Réduire ' . $a . $var . ' + ' . $b . ' &minus; (' . $c . $var . ' &minus; ' . $d . ')</p>';
            $reponse_html  = '<p><strong>' . $terme_final . ' + ' . $const_final . '</strong></p>'
                           . '<p style="font-size:0.9em; color:#666;">Le signe &minus; devant la parenthèse change '
                           . '<strong>tous</strong> les signes à l\'intérieur : &minus;(' . $c . $var . ' &minus; ' . $d . ') '
                           . 'devient &minus;' . $c . $var . ' + ' . $d . '.</p>'
                           . '<p style="font-size:0.9em; color:#666;">On réduit ensuite : ' . $a . $var . ' &minus; ' . $c . $var
                           . ' = ' . $terme_final . ' et ' . $b . ' + ' . $d . ' = ' . $const_final . '.</p>';
            $difficulte = 1.8;
            break;

        // ============================================
        // DOUBLE DISTRIBUTIVITÉ avec un MOINS : (ax + b)(cx − d)
        // ============================================

        case 'developper_double_moins':
            $var = $variables[array_rand($variables)];
            $a = rand(2, 4);
            $b = rand(1, 5);
            $c = rand(2, 4);
            $d = rand(1, 5);

            // (ax + b)(cx − d) = ac·x² + (bc − ad)·x − bd
            $coef_x2 = $a * $c;
            $coef_x  = $b * $c - $a * $d;
            $const   = -$b * $d;

            $terme_x2 = ($coef_x2 == 1) ? $var . '²' : $coef_x2 . $var . '²';
            $fact1 = ($a == 1) ? $var : $a . $var;
            $fact2 = ($c == 1) ? $var : $c . $var;

            if ($coef_x == 0) {
                $milieu = '';
            } elseif ($coef_x > 0) {
                $milieu = ' + ' . (($coef_x == 1) ? $var : $coef_x . $var);
            } else {
                $milieu = ' &minus; ' . ((abs($coef_x) == 1) ? $var : abs($coef_x) . $var);
            }

            $question_html = '<p>Développer (' . $fact1 . ' + ' . $b . ')(' . $fact2 . ' &minus; ' . $d . ')</p>';
            $reponse_html  = '<p><strong>' . $terme_x2 . $milieu . ' &minus; ' . abs($const) . '</strong></p>'
                           . '<p style="font-size:0.9em; color:#666;">Quatre produits : '
                           . $fact1 . ' × ' . $fact2 . ' = ' . $terme_x2 . ' ; '
                           . $fact1 . ' × (&minus;' . $d . ') = &minus;' . ($a * $d) . $var . ' ; '
                           . $b . ' × ' . $fact2 . ' = ' . ($b * $c) . $var . ' ; '
                           . $b . ' × (&minus;' . $d . ') = &minus;' . ($b * $d) . '.</p>'
                           . '<p style="font-size:0.9em; color:#666;">On réduit les deux termes en ' . $var . ' : '
                           . '&minus;' . ($a * $d) . $var . ' + ' . ($b * $c) . $var . ' = '
                           . ($coef_x == 0 ? '0' : (($coef_x > 0 ? '' : '&minus;') . ((abs($coef_x) == 1) ? $var : abs($coef_x) . $var)))
                           . '.</p>';
            $difficulte = 1.9;
            break;

        // ============================================
        // FACTORISER par un NOMBRE : 12x + 18 = 6(2x + 3)
        // ============================================

        case 'factoriser_nombre':
            $var = $variables[array_rand($variables)];
            $k = [2, 3, 4, 5, 6, 7][rand(0, 5)];        // facteur commun
            $p = rand(2, 9);
            $q = rand(2, 9);
            if ($p == $q) { $q = $p + 1; }
            // On évite que p et q aient encore un facteur commun : la
            // factorisation doit être complète, sans quoi il y aurait deux réponses.
            $g = df_pgcd($p, $q);
            $p = intdiv($p, $g);
            $q = intdiv($q, $g);
            if ($p == $q) { $q = $p + 1; }

            $terme1 = ($k * $p) . $var;
            $terme2 = $k * $q;
            $interne = (($p == 1) ? $var : $p . $var) . ' + ' . $q;

            $question_html = '<p>Factoriser ' . $terme1 . ' + ' . $terme2 . '</p>';
            $reponse_html  = '<p><strong>' . $k . '(' . $interne . ')</strong></p>'
                           . '<p style="font-size:0.9em; color:#666;">Le facteur commun est ' . $k . ' : '
                           . $terme1 . ' = ' . $k . ' × ' . (($p == 1) ? $var : $p . $var)
                           . ' et ' . $terme2 . ' = ' . $k . ' × ' . $q . '.</p>';
            $difficulte = 1.6;
            break;

        // ============================================
        // FACTORISER par un MONÔME : 6x² + 9x = 3x(2x + 3)
        // ============================================

        case 'factoriser_mixte':
            $var = $variables[array_rand($variables)];
            $k = [2, 3, 4, 5][rand(0, 3)];
            $p = rand(2, 7);
            $q = rand(2, 9);
            $g = df_pgcd($p, $q);
            $p = intdiv($p, $g);
            $q = intdiv($q, $g);

            $terme1 = ($k * $p) . $var . '²';
            $terme2 = ($k * $q) . $var;
            $facteur = $k . $var;
            $interne = (($p == 1) ? $var : $p . $var) . ' + ' . $q;

            $question_html = '<p>Factoriser ' . $terme1 . ' + ' . $terme2 . '</p>';
            $reponse_html  = '<p><strong>' . $facteur . '(' . $interne . ')</strong></p>'
                           . '<p style="font-size:0.9em; color:#666;">Les deux termes contiennent ' . $k . ' et '
                           . $var . ' : le facteur commun est ' . $facteur . '.</p>'
                           . '<p style="font-size:0.9em; color:#666;">' . $terme1 . ' = ' . $facteur . ' × '
                           . (($p == 1) ? $var : $p . $var) . ' et ' . $terme2 . ' = ' . $facteur . ' × ' . $q . '.</p>';
            $difficulte = 2.0;
            break;
    }
    
    return [
        'type' => 'developper_factoriser',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}

/** PGCD, pour garantir des factorisations complètes (donc une seule réponse). */
function df_pgcd($a, $b) {
    $a = abs($a); $b = abs($b);
    while ($b != 0) {
        $t = $b; $b = $a % $b; $a = $t;
    }
    return ($a == 0) ? 1 : $a;
}
