<?php
/**
 * Automatisme : Calcul mental (additions, soustractions, astuces)
 * Difficulté : FACILE à MOYEN (range 1.0 - 2.2)
 *
 * Réécrit en août 2026 : la version d'origine était marquée « PLACEHOLDER » et
 * tirait deux nombres au hasard entre 10 et 99. Un calcul mental tiré au hasard
 * n'entraîne aucune stratégie — on ne fait que mesurer la vitesse de l'algorithme
 * posé mentalement. Les sous-types ci-dessous sont construits AUTOUR d'une
 * stratégie, rappelée dans la correction : c'est elle qu'il s'agit d'installer.
 */

function generer_calcul_mental($famille = '') {
    $sous_types = [
        'complement_100', 'complement_100',
        'ajouter_proche_dizaine', 'ajouter_proche_dizaine',
        'addition_passage', 'addition_passage',
        'soustraction', 'soustraction',
        'double_moitie', 'double_moitie',
        'multiplier_astuce', 'multiplier_astuce',
    ];

    if (in_array($famille, ['complement_100', 'ajouter_proche_dizaine', 'addition_passage',
                            'soustraction', 'double_moitie', 'multiplier_astuce'], true)) {
        $type = $famille;
    } else {
        if (!isset($_SESSION['calcul_mental_pool']) || empty($_SESSION['calcul_mental_pool'])) {
            $_SESSION['calcul_mental_pool'] = $sous_types;
            shuffle($_SESSION['calcul_mental_pool']);
        }
        $type = array_shift($_SESSION['calcul_mental_pool']);
    }

    switch ($type) {

        // Complément à 100 (ou à 1000) : la base des rendus de monnaie.
        case 'complement_100':
            $cible = (rand(0, 3) === 0) ? 1000 : 100;
            $a = ($cible === 100) ? rand(11, 89) : rand(110, 890);
            $b = $cible - $a;
            $q = '<p>Complète : <strong>' . $a . ' + ? = ' . $cible . '</strong></p>';
            $r = '<p><strong>' . $b . '</strong></p>'
               . '<p style="font-size:0.9em; color:#666;">Astuce : on complète d\'abord jusqu\'à la '
               . 'dizaine supérieure, puis jusqu\'à ' . $cible . '.</p>';
            $d = 1.3;
            break;

        // Ajouter 9, 11, 19, 21 : on ajoute la dizaine, puis on ajuste.
        case 'ajouter_proche_dizaine':
            $a = rand(24, 178);
            $b = [9, 11, 19, 21, 29][rand(0, 4)];
            $dizaine = ($b % 10 === 9) ? $b + 1 : $b - 1;
            $ajust   = ($b % 10 === 9) ? '&minus; 1' : '+ 1';
            $q = '<p>Calcule : <strong>' . $a . ' + ' . $b . '</strong></p>';
            $r = '<p><strong>' . ($a + $b) . '</strong></p>'
               . '<p style="font-size:0.9em; color:#666;">Astuce : ' . $a . ' + ' . $dizaine . ' = '
               . ($a + $dizaine) . ', puis ' . $ajust . ' → ' . ($a + $b) . '.</p>';
            $d = 1.5;
            break;

        case 'addition_passage':
            $a = rand(23, 78);
            $b = rand(23, 78);
            $q = '<p>Calcule : <strong>' . $a . ' + ' . $b . '</strong></p>';
            $r = '<p><strong>' . ($a + $b) . '</strong></p>'
               . '<p style="font-size:0.9em; color:#666;">Astuce : on additionne les dizaines '
               . '(' . (intdiv($a, 10) * 10) . ' + ' . (intdiv($b, 10) * 10) . ' = '
               . (intdiv($a, 10) * 10 + intdiv($b, 10) * 10) . '), puis les unités ('
               . ($a % 10) . ' + ' . ($b % 10) . ' = ' . ($a % 10 + $b % 10) . ').</p>';
            $d = 1.4;
            break;

        case 'soustraction':
            $a = rand(52, 145);
            $b = rand(18, 49);
            $q = '<p>Calcule : <strong>' . $a . ' &minus; ' . $b . '</strong></p>';
            $r = '<p><strong>' . ($a - $b) . '</strong></p>'
               . '<p style="font-size:0.9em; color:#666;">Astuce : on peut avancer depuis ' . $b
               . ' jusqu\'à ' . $a . ' plutôt que reculer — soustraire, c\'est aussi chercher l\'écart.</p>';
            $d = 1.6;
            break;

        case 'double_moitie':
            if (rand(0, 1) === 0) {
                $a = rand(12, 98);
                $q = '<p>Quel est le <strong>double</strong> de ' . $a . ' ?</p>';
                $r = '<p><strong>' . (2 * $a) . '</strong></p>'
                   . '<p style="font-size:0.9em; color:#666;">On double les dizaines et les unités séparément : '
                   . (2 * intdiv($a, 10) * 10) . ' + ' . (2 * ($a % 10)) . ' = ' . (2 * $a) . '.</p>';
            } else {
                $a = rand(6, 49) * 2;   // pair : la moitié est entière
                $q = '<p>Quelle est la <strong>moitié</strong> de ' . $a . ' ?</p>';
                $r = '<p><strong>' . ($a / 2) . '</strong></p>'
                   . '<p style="font-size:0.9em; color:#666;">Prendre la moitié, c\'est diviser par 2 : '
                   . $a . ' &divide; 2 = ' . ($a / 2) . '.</p>';
            }
            $d = 1.3;
            break;

        default: // multiplier_astuce
            $a = rand(12, 48);
            $cas = rand(0, 2);
            if ($cas === 0) {
                $q = '<p>Calcule : <strong>' . $a . ' × 5</strong></p>';
                $r = '<p><strong>' . ($a * 5) . '</strong></p>'
                   . '<p style="font-size:0.9em; color:#666;">Astuce : multiplier par 5, c\'est multiplier '
                   . 'par 10 puis prendre la moitié — ' . ($a * 10) . ' &divide; 2 = ' . ($a * 5) . '.</p>';
            } elseif ($cas === 1) {
                $q = '<p>Calcule : <strong>' . $a . ' × 4</strong></p>';
                $r = '<p><strong>' . ($a * 4) . '</strong></p>'
                   . '<p style="font-size:0.9em; color:#666;">Astuce : multiplier par 4, c\'est doubler deux fois — '
                   . $a . ' → ' . ($a * 2) . ' → ' . ($a * 4) . '.</p>';
            } else {
                $q = '<p>Calcule : <strong>' . $a . ' × 9</strong></p>';
                $r = '<p><strong>' . ($a * 9) . '</strong></p>'
                   . '<p style="font-size:0.9em; color:#666;">Astuce : multiplier par 9, c\'est multiplier par 10 '
                   . 'puis retirer une fois le nombre — ' . ($a * 10) . ' &minus; ' . $a . ' = ' . ($a * 9) . '.</p>';
            }
            $d = 2.0;
            break;
    }

    return [
        'type' => 'calcul_mental',
        'difficulte_id' => $d,
        'question' => $q,
        'reponse' => $r
    ];
}
