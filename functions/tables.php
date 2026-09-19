<?php
/**
 * Automatisme : Tables de multiplication
 * Difficulté : FACILE (range 1.0 - 2.0)
 *
 * Réécrit en août 2026 : la version d'origine était marquée « PLACEHOLDER »
 * et ne produisait qu'un seul type de question, le produit a × b. Or une table
 * n'est sue que si elle est mobilisable dans les trois sens : le produit, le
 * facteur manquant, et le quotient. C'est le facteur manquant qui distingue
 * l'élève qui récite de celui qui sait.
 */

function generer_tables($famille = '') {
    $sous_types = ['produit', 'produit', 'manquant', 'manquant', 'quotient', 'quotient', 'multiple'];

    if (in_array($famille, ['produit', 'manquant', 'quotient', 'multiple'], true)) {
        $type = $famille;
    } else {
        if (!isset($_SESSION['tables_pool']) || empty($_SESSION['tables_pool'])) {
            $_SESSION['tables_pool'] = $sous_types;
            shuffle($_SESSION['tables_pool']);
        }
        $type = array_shift($_SESSION['tables_pool']);
    }

    $a = rand(3, 9);
    $b = rand(3, 9);
    $p = $a * $b;
    $difficulte_id = 1.0 + (($a + $b) / 18);

    switch ($type) {

        case 'manquant':
            // 7 × ? = 56 — le sens qui prépare la division et les équations.
            $q = '<p>Complète : <strong>' . $a . ' × ? = ' . $p . '</strong></p>';
            $r = '<p><strong>' . $b . '</strong></p>'
               . '<p style="font-size:0.9em; color:#666;">On cherche par combien multiplier ' . $a
               . ' pour obtenir ' . $p . ' : c\'est ' . $p . ' &divide; ' . $a . ' = ' . $b . '.</p>';
            $difficulte_id += 0.3;
            break;

        case 'quotient':
            $q = '<p>Calcule : <strong>' . $p . ' &divide; ' . $a . '</strong></p>';
            $r = '<p><strong>' . $b . '</strong></p>'
               . '<p style="font-size:0.9em; color:#666;">Parce que ' . $a . ' × ' . $b . ' = ' . $p
               . '. Diviser, c\'est chercher le facteur manquant.</p>';
            $difficulte_id += 0.4;
            break;

        case 'multiple':
            // « 42 est-il dans la table de 6 ? » : le vocabulaire multiple/diviseur
            // s'installe ici, bien avant l'arithmétique de 3e.
            $vrai = (rand(0, 1) === 1);
            $n = $vrai ? $p : $p + rand(1, $a - 1);
            $q = '<p><strong>' . $n . ' est-il dans la table de ' . $a . ' ?</strong></p>';
            if ($vrai) {
                $r = '<p><strong>Oui</strong></p>'
                   . '<p style="font-size:0.9em; color:#666;">' . $n . ' = ' . $a . ' × ' . $b
                   . ' : ' . $n . ' est un multiple de ' . $a . '.</p>';
            } else {
                $quotient = intdiv($n, $a);
                $reste = $n % $a;
                $r = '<p><strong>Non</strong></p>'
                   . '<p style="font-size:0.9em; color:#666;">' . $n . ' = ' . $a . ' × ' . $quotient
                   . ' + ' . $reste . ' : il reste ' . $reste . ', donc ' . $n
                   . ' n\'est pas un multiple de ' . $a . '.</p>';
            }
            $difficulte_id += 0.5;
            break;

        default: // produit
            $q = '<p>Combien font <strong>' . $a . ' × ' . $b . '</strong> ?</p>';
            $r = '<p><strong>' . $a . ' × ' . $b . ' = ' . $p . '</strong></p>';
            break;
    }

    return [
        'type' => 'tables',
        'difficulte_id' => round($difficulte_id, 2),
        'question' => $q,
        'reponse' => $r
    ];
}
