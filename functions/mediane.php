<?php
/**
 * Automatisme : Médiane d'une série
 * Difficulté : FACILE pour 5 valeurs (1.5), MOYEN pour 7 valeurs (2.0),
 *              DIFFICILE pour un effectif pair (2.5) : il faut prendre la
 *              demi-somme des deux valeurs centrales.
 */

function generer_mediane() {
    // Effectif tiré dans un pool : autant de séries impaires (5, 7) que de
    // séries paires (4, 6). Le cas pair est le point d'échec classique — il
    // n'était pas généré avant août 2026.
    if (!isset($_SESSION['mediane_effectifs']) || empty($_SESSION['mediane_effectifs'])) {
        $_SESSION['mediane_effectifs'] = [5, 7, 5, 7, 4, 6, 4, 6];
        shuffle($_SESSION['mediane_effectifs']);
    }
    $nb_valeurs = array_shift($_SESSION['mediane_effectifs']);
    $pair       = ($nb_valeurs % 2 === 0);
    $difficulte = $pair ? 2.5 : (($nb_valeurs == 5) ? 1.5 : 2.0);

    // Contextes variés avec narratifs cohérents
    $contextes = [
        [
            'phrases' => [
                'Les températures relevées à ' . $nb_valeurs . ' moments de la journée sont (en °C) : <br>',
                'Les températures mesurées dans ' . $nb_valeurs . ' villes sont (en °C) : <br>',
                'Les températures relevées pendant ' . $nb_valeurs . ' jours sont (en °C) : <br>'
            ],
            'unite' => '°C',
            'min' => -5,
            'max' => 35
        ],
        [
            'phrases' => [
                'Les notes obtenues par Lucas en mathématiques ce trimestre sont : <br>',
                'Léa a obtenu ces notes aux contrôles : <br>',
                'Les notes de Nathan en français sont : <br>'
            ],
            'unite' => '',
            'min' => 0,
            'max' => 20
        ],
        [
            'phrases' => [
                'Les âges des joueurs de l\'équipe de basket sont (en années) : <br>',
                'Les participants au stage ont pour âges (en années) : <br>',
                'Les âges des membres du club sont (en années) : <br>'
            ],
            'unite' => 'ans',
            'min' => 12,
            'max' => 75
        ],
        [
            'phrases' => [
                'Les temps de trajet école-maison de ' . $nb_valeurs . ' élèves sont (en minutes) : <br>',
                'Les durées de ' . $nb_valeurs . ' films sont (en minutes) : <br>',
                'Les temps de course de ' . $nb_valeurs . ' athlètes sont (en minutes) : <br>'
            ],
            'unite' => 'min',
            'min' => 5,
            'max' => 90
        ],
        [
            'phrases' => [
                'Les masses de ' . $nb_valeurs . ' bagages enregistrés sont (en kg) : <br>',
                'Les poids des chiots de la portée sont (en kg) : <br>',
                'Les masses de ' . $nb_valeurs . ' colis à expédier sont (en kg) : <br>'
            ],
            'unite' => 'kg',
            'min' => 1,
            'max' => 50
        ],
        [
            'phrases' => [
                'Les prix de ' . $nb_valeurs . ' bandes dessinées à la librairie sont (en €) : <br>',
                'Les tickets de cinéma dans ' . $nb_valeurs . ' villes coûtent (en €) : <br>',
                'Les prix de ' . $nb_valeurs . ' jeux de société sont (en €) : <br>'
            ],
            'unite' => '€',
            'min' => 5,
            'max' => 45
        ]
    ];
    
    $contexte = $contextes[array_rand($contextes)];
    $phrase = $contexte['phrases'][array_rand($contexte['phrases'])];
    
    // Générer le type de série (espacée, serrée, ou mixte)
    $type_serie = rand(0, 2);
    $valeurs = [];
    
    switch($type_serie) {
        case 0: // Valeurs très espacées
            $plage = $contexte['max'] - $contexte['min'];
            $ecart_min = max(5, intval($plage / ($nb_valeurs * 2)));
            $tentatives = 0;
            
            while(count($valeurs) < $nb_valeurs && $tentatives < 100) {
                $nouvelle = rand($contexte['min'], $contexte['max']);
                $ok = true;
                foreach($valeurs as $v) {
                    if(abs($nouvelle - $v) < $ecart_min) {
                        $ok = false;
                        break;
                    }
                }
                if($ok && !in_array($nouvelle, $valeurs)) {
                    $valeurs[] = $nouvelle;
                }
                $tentatives++;
            }
            break;
            
        case 1: // Valeurs serrées
            $plage_reduite = min(15, intval(($contexte['max'] - $contexte['min']) / 2));
            $debut = rand($contexte['min'], $contexte['max'] - $plage_reduite);
            
            while(count($valeurs) < $nb_valeurs) {
                $nouvelle = rand($debut, $debut + $plage_reduite);
                if(!in_array($nouvelle, $valeurs)) {
                    $valeurs[] = $nouvelle;
                }
            }
            break;
            
        case 2: // Mix aléatoire
            while(count($valeurs) < $nb_valeurs) {
                $nouvelle = rand($contexte['min'], $contexte['max']);
                if(!in_array($nouvelle, $valeurs)) {
                    $valeurs[] = $nouvelle;
                }
            }
            break;
    }
    
    // Filet de sécurité : si une contrainte d'écart (case 0) a empêché de
    // remplir toute la série, compléter avec des valeurs distinctes quelconques.
    // Évite un index indéfini et une médiane fausse sur les plages étroites.
    $garde = 0;
    while (count($valeurs) < $nb_valeurs && $garde++ < 1000) {
        $nouvelle = rand($contexte['min'], $contexte['max']);
        if (!in_array($nouvelle, $valeurs)) {
            $valeurs[] = $nouvelle;
        }
    }

    // Calculer la médiane : valeur centrale si l'effectif est impair,
    // demi-somme des deux valeurs centrales s'il est pair.
    $valeurs_triees = $valeurs;
    sort($valeurs_triees);

    if ($pair) {
        $i_gauche = intval($nb_valeurs / 2) - 1;   // ex. 6 valeurs → rangs 3 et 4
        $i_droite = intval($nb_valeurs / 2);
        $v_gauche = $valeurs_triees[$i_gauche];
        $v_droite = $valeurs_triees[$i_droite];
        $mediane  = ($v_gauche + $v_droite) / 2;
        $centrales = [$i_gauche, $i_droite];
    } else {
        $i_centre  = intval($nb_valeurs / 2);
        $mediane   = $valeurs_triees[$i_centre];
        $centrales = [$i_centre];
    }

    $unite = !empty($contexte['unite']) ? ' ' . $contexte['unite'] : '';

    // Libellé de l'unité tel qu'il se lit dans la question (« en années »,
    // pas « en ans ») : l'unité attendue doit figurer dans l'énoncé.
    $unites_question = [
        '°C'  => '°C',
        'ans' => 'années',
        'min' => 'minutes',
        'kg'  => 'kg',
        '€'   => '€',
    ];
    if (!isset($unites_question[$contexte['unite']])) {
        $unites_question[$contexte['unite']] = $contexte['unite'];
    }

    // Formater la liste de valeurs avec des séparateurs " ; "
    $liste_valeurs = implode(' ; ', $valeurs);

    // Série rangée dans l'ordre croissant, valeur(s) centrale(s) mises en avant :
    // c'est l'étape que les élèves oublient le plus souvent.
    $liste_triee = [];
    foreach ($valeurs_triees as $i => $v) {
        $liste_triee[] = in_array($i, $centrales, true)
            ? '<strong style="color:#c0392b;">' . $v . '</strong>'
            : $v;
    }

    $reponse  = '<p>On range d\'abord la série dans l\'ordre croissant :<br>'
              . implode(' ; ', $liste_triee) . '</p>';
    if ($pair) {
        $reponse .= '<p>L\'effectif est <strong>pair</strong> (' . $nb_valeurs . ' valeurs) : '
                  . 'la médiane est la demi-somme des deux valeurs centrales.<br>'
                  . '(' . $v_gauche . ' + ' . $v_droite . ') &divide; 2 = '
                  . med_formater_nombre($mediane) . '</p>';
    } else {
        $reponse .= '<p>L\'effectif est <strong>impair</strong> (' . $nb_valeurs . ' valeurs) : '
                  . 'la médiane est la valeur du milieu, celle de rang '
                  . (intval($nb_valeurs / 2) + 1) . '.</p>';
    }
    $reponse .= '<p>La médiane est <strong>' . med_formater_nombre($mediane) . $unite . '</strong></p>';

    return [
        'type' => 'mediane',
        'difficulte_id' => $difficulte,
        'question' => '<p>' . $phrase . $liste_valeurs . '</p><p>Que vaut la médiane de cette série'
                    . (!empty($contexte['unite']) ? ', <strong>en ' . $unites_question[$contexte['unite']] . '</strong>' : '')
                    . ' ?</p>',
        'reponse' => $reponse
    ];
}

/**
 * Une médiane d'effectif pair peut valoir un demi-entier (ex. 12,5).
 * Fonction locale pour rester utilisable même si functions/moyenne.php
 * (qui expose formater_nombre) n'est pas chargé.
 */
if (!function_exists('med_formater_nombre')) {
    function med_formater_nombre($n) {
        if ($n == intval($n)) {
            return intval($n);
        }
        return str_replace('.', ',', rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.'));
    }
}
