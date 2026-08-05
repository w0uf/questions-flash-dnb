<?php
/**
 * Automatisme : Médiane d'une série
 * Difficulté : FACILE pour 5 valeurs (1.5), MOYEN pour 7 valeurs (2.0)
 */

function generer_mediane() {
    // 50% de chance d'avoir 5 ou 7 valeurs
    $nb_valeurs = (rand(0, 1) == 0) ? 5 : 7;
    $difficulte = ($nb_valeurs == 5) ? 1.5 : 2.0;
    
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
                'Les prix de ' . $nb_valeurs . ' jeux de société sont (en €)
                '
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

    // Calculer la médiane (série de taille impaire : valeur centrale)
    $valeurs_triees = $valeurs;
    sort($valeurs_triees);
    $mediane = $valeurs_triees[intval($nb_valeurs / 2)];
    
    // Formater la liste de valeurs avec des séparateurs " ; "
    $liste_valeurs = implode(' ; ', $valeurs);
    
    // Construire la réponse avec l'unité si elle existe
    $reponse_text = 'La médiane est <strong>' . $mediane . '</strong>';
    if(!empty($contexte['unite'])) {
        $reponse_text .= ' ' . $contexte['unite'];
    }
    
    return [
        'type' => 'mediane',
        'difficulte_id' => $difficulte,
        'question' => '<p>' . $phrase . $liste_valeurs . '</p><p>Que vaut la médiane de cette série ?</p>',
        'reponse' => '<p>' . $reponse_text . '</p>'
    ];
}
