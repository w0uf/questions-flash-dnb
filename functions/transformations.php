<?php
/**
 * Automatisme DNB 2026 : Mobiliser les propriétés de la symétrie axiale, centrale et de la translation
 * Difficulté : FACILE à MOYEN (range 1.5 - 2.5)
 * FORMAT SANS CALCULATRICE
 * 
 * Types de questions :
 * - Reconnaissance de transformation (25%)
 * - Propriétés théoriques (25%)
 * - Image de point sur quadrillage (25%)
 * - Identifier axe/centre (17%)
 * - Regard critique (8%)
 */

function generer_transformations() {
    // Anti-doublon : pool de types
    if (!isset($_SESSION['transformations_pool']) || empty($_SESSION['transformations_pool'])) {
        $_SESSION['transformations_pool'] = [
            // Reconnaissance (3)
            'reconnaissance_axiale',
            'reconnaissance_centrale',
            'reconnaissance_translation',
            
            // Propriétés (3)
            'propriete_axiale',
            'propriete_centrale',
            'propriete_translation',
            
            // Image de point (3)
            'image_axiale',
            'image_centrale',
            'image_translation',
            
            // Identifier symétries (2)
            'identifier_symetries',
            'identifier_symetries',
            
            // Regard critique (1)
            'regard_critique'
        ];
        shuffle($_SESSION['transformations_pool']);
    }
    
    $type = array_shift($_SESSION['transformations_pool']);
    
    switch ($type) {
        case 'reconnaissance_axiale':
            return generer_reconnaissance_transformation('axiale');
        case 'reconnaissance_centrale':
            return generer_reconnaissance_transformation('centrale');
        case 'reconnaissance_translation':
            return generer_reconnaissance_transformation('translation');
            
        case 'propriete_axiale':
            return generer_propriete_transformation('axiale');
        case 'propriete_centrale':
            return generer_propriete_transformation('centrale');
        case 'propriete_translation':
            return generer_propriete_transformation('translation');
            
        case 'image_axiale':
            return generer_image_point('axiale');
        case 'image_centrale':
            return generer_image_point('centrale');
        case 'image_translation':
            return generer_image_point('translation');
            
        case 'identifier_symetries':
            return generer_identifier_symetries();
            
        case 'regard_critique':
            return generer_regard_critique_transformation();
    }
}

/**
 * Type 1 : Reconnaissance de transformation sur quadrillage
 */
function generer_reconnaissance_transformation($type_correct) {
    // Générer deux figures simples (triangles) sur quadrillage
    // avec la transformation demandée
    
    $types_transformation = [
        'axiale' => 'une symétrie axiale',
        'centrale' => 'une symétrie centrale',
        'translation' => 'une translation'
    ];
    
    // Générer SVG avec deux figures
    $svg = generer_svg_deux_figures_transformation($type_correct);
    
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;"><strong>Quelle transformation permet de passer de la figure bleue à la figure verte ?</strong></p>';
    $question .= '</div>';
    
    // Propositions
    $propositions = [
        'axiale' => 'Une symétrie axiale',
        'centrale' => 'Une symétrie centrale',
        'translation' => 'Une translation',
        'aucune' => 'Aucune'
    ];
    
    $qcm = generer_qcm_transformations($propositions, $type_correct);
    $question .= $qcm['html'];
    
    $reponse = '<p><strong>' . $qcm['bonne_lettre'] . ' – ' . $types_transformation[$type_correct] . '</strong></p>';
    
    return [
        'type' => 'transformations',
        'difficulte_id' => 1.8,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Type 2 : Propriétés théoriques des transformations
 */
function generer_propriete_transformation($type) {
    global $PRENOMS_QUESTIONS;
    
    if (!isset($PRENOMS_QUESTIONS)) {
        $PRENOMS_QUESTIONS = [
            'garcons' => ['Pierre', 'Paul', 'Tom', 'Lucas', 'Hugo'],
            'filles' => ['Marie', 'Léa', 'Emma', 'Chloé', 'Sarah']
        ];
    }
    
    $variantes = [
        'axiale' => [
            [
                'question' => '<p>A\' est l\'image de A par symétrie d\'axe (d).</p><p><strong>Que peut-on dire de la droite (d) et du segment [AA\'] ?</strong></p>',
                'propositions' => [
                    'bonne' => '(d) est perpendiculaire à [AA\']',
                    'erreur1' => '(d) est parallèle à [AA\']',
                    'erreur2' => '(d) passe par A',
                    'erreur3' => '(d) passe par A\''
                ],
                'reponse' => 'La droite (d) est perpendiculaire au segment [AA\']',
                'difficulte' => 1.8
            ],
            [
                'question' => '<p>B\' est l\'image de B par symétrie d\'axe (d).</p><p><strong>Que peut-on dire du point d\'intersection de (d) et [BB\'] ?</strong></p>',
                'propositions' => [
                    'bonne' => 'C\'est le milieu de [BB\']',
                    'erreur1' => 'C\'est le point B',
                    'erreur2' => 'C\'est le point B\'',
                    'erreur3' => 'Il n\'y a pas d\'intersection'
                ],
                'reponse' => 'Le point d\'intersection de (d) et [BB\'] est le milieu de [BB\']',
                'difficulte' => 2.0
            ]
        ],
        
        'centrale' => [
            [
                'question' => '<p>M\' est l\'image de M par symétrie de centre O.</p><p><strong>Que peut-on dire du point O ?</strong></p>',
                'propositions' => [
                    'bonne' => 'O est le milieu de [MM\']',
                    'erreur1' => 'O est sur la droite (MM\') mais pas au milieu',
                    'erreur2' => 'O est l\'image de M',
                    'erreur3' => 'OM = 2 × OM\''
                ],
                'reponse' => 'O est le milieu de [MM\']',
                'difficulte' => 1.7
            ],
            [
                'question' => '<p>P\' est l\'image de P par symétrie de centre O.</p><p><strong>Quelle est l\'image du point O par cette symétrie ?</strong></p>',
                'propositions' => [
                    'bonne' => 'Le point O lui-même',
                    'erreur1' => 'Le point P',
                    'erreur2' => 'Le point P\'',
                    'erreur3' => 'Un autre point'
                ],
                'reponse' => 'Le point O est son propre image (point invariant)',
                'difficulte' => 1.9
            ]
        ],
        
        'translation' => [
            [
                'question' => '<p>La translation qui transforme A en B transforme aussi C en D.</p><p><strong>Que peut-on dire du quadrilatère ABDC ?</strong></p>',
                'propositions' => [
                    'bonne' => 'ABDC est un parallélogramme',
                    'erreur1' => 'ABDC est un rectangle',
                    'erreur2' => 'ABDC est un losange',
                    'erreur3' => 'On ne peut rien dire'
                ],
                'reponse' => 'ABDC est un parallélogramme',
                'difficulte' => 2.1
            ],
            [
                'question' => '<p>E\' est l\'image de E par la translation qui transforme F en G.</p><p><strong>Que peut-on dire des segments [EE\'] et [FG] ?</strong></p>',
                'propositions' => [
                    'bonne' => 'Ils ont la même longueur et sont parallèles',
                    'erreur1' => 'Ils ont la même longueur mais pas forcément parallèles',
                    'erreur2' => 'Ils sont parallèles mais pas de même longueur',
                    'erreur3' => 'Ils sont perpendiculaires'
                ],
                'reponse' => 'Les segments [EE\'] et [FG] ont la même longueur et sont parallèles',
                'difficulte' => 2.2
            ]
        ]
    ];
    
    $variante = $variantes[$type][array_rand($variantes[$type])];
    
    $qcm = generer_qcm_transformations($variante['propositions'], 'bonne');
    
    $question = $variante['question'] . $qcm['html'];
    $reponse = '<p><strong>' . $qcm['bonne_lettre'] . ' – ' . $variante['reponse'] . '</strong></p>';
    
    return [
        'type' => 'transformations',
        'difficulte_id' => $variante['difficulte'],
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Type 3 : Image de point sur quadrillage
 */
function generer_image_point($type) {
    $variantes = [
        'axiale' => [
            'axe' => 'Ox',
            'point_depart' => [3, 2],
            'point_image' => [3, -2],
            'question_text' => 'l\'axe des abscisses',
            'difficulte' => 1.6
        ],
        'centrale' => [
            'centre' => [0, 0],
            'point_depart' => [2, 3],
            'point_image' => [-2, -3],
            'question_text' => 'le centre O',
            'difficulte' => 1.7
        ],
        'translation' => [
            'point_a' => [1, 1],
            'point_b' => [3, 4],
            'point_depart' => [2, 2],
            'point_image' => [4, 5],
            'question_text' => 'la translation qui transforme A(1 ; 1) en B(3 ; 4)',
            'difficulte' => 2.0
        ]
    ];
    
    $var = $variantes[$type];
    
    // Générer SVG avec repère et point
    $svg = generer_svg_repere_avec_point($type, $var);
    
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    
    if ($type == 'axiale') {
        $question .= '<p style="margin-top: 20px;">Le point M a pour coordonnées (' . $var['point_depart'][0] . ' ; ' . $var['point_depart'][1] . ').</p>';
        $question .= '<p><strong>Quelles sont les coordonnées de son image M\' par symétrie d\'axe ' . $var['question_text'] . ' ?</strong></p>';
    } elseif ($type == 'centrale') {
        $question .= '<p style="margin-top: 20px;">Le point N a pour coordonnées (' . $var['point_depart'][0] . ' ; ' . $var['point_depart'][1] . ').</p>';
        $question .= '<p><strong>Quelles sont les coordonnées de son image N\' par symétrie de centre O ?</strong></p>';
    } else {
        $question .= '<p style="margin-top: 20px;">Le point P a pour coordonnées (' . $var['point_depart'][0] . ' ; ' . $var['point_depart'][1] . ').</p>';
        $question .= '<p><strong>Quelles sont les coordonnées de son image P\' par ' . $var['question_text'] . ' ?</strong></p>';
    }
    
    $question .= '</div>';
    
    // Générer distracteurs
    $bonne = '(' . $var['point_image'][0] . ' ; ' . $var['point_image'][1] . ')';
    
    $propositions = generer_distracteurs_transformations($var['point_depart'], $var['point_image'], $type);
    $propositions['bonne'] = $bonne;
    
    $qcm = generer_qcm_transformations($propositions, 'bonne');
    $question .= $qcm['html'];
    
    $reponse = '<p><strong>' . $qcm['bonne_lettre'] . ' ' . $bonne . '</strong></p>';
    
    return [
        'type' => 'transformations',
        'difficulte_id' => $var['difficulte'],
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Type 4 : Identifier les symétries présentes dans une lettre
 */
function generer_identifier_symetries() {
    // Banque de lettres avec leurs propriétés
    $lettres = [
        // 2 axes
        'H' => ['axes' => 2, 'centre' => true],
        'I' => ['axes' => 2, 'centre' => true],
        'O' => ['axes' => 4, 'centre' => true], // infini d'axes mais on dira 2+
        'X' => ['axes' => 2, 'centre' => true],
        
        // 1 axe vertical
        'A' => ['axes' => 1, 'centre' => false],
        'M' => ['axes' => 1, 'centre' => false],
        'T' => ['axes' => 1, 'centre' => false],
        'U' => ['axes' => 1, 'centre' => false],
        'V' => ['axes' => 1, 'centre' => false],
        'W' => ['axes' => 1, 'centre' => false],
        'Y' => ['axes' => 1, 'centre' => false],
        
        // 1 axe horizontal
        'D' => ['axes' => 1, 'centre' => false],
        'C' => ['axes' => 1, 'centre' => false],
        
        // Centre seulement
        'N' => ['axes' => 0, 'centre' => true],
        'S' => ['axes' => 0, 'centre' => true],
        'Z' => ['axes' => 0, 'centre' => true],
        
        // Aucune symétrie
        'F' => ['axes' => 0, 'centre' => false],
        'G' => ['axes' => 0, 'centre' => false],
        'J' => ['axes' => 0, 'centre' => false],
        'L' => ['axes' => 0, 'centre' => false],
        'P' => ['axes' => 0, 'centre' => false],
        'Q' => ['axes' => 0, 'centre' => false],
        'R' => ['axes' => 0, 'centre' => false],
    ];
    
    // Pool anti-doublon pour les lettres
    if (!isset($_SESSION['lettres_symetrie_pool']) || empty($_SESSION['lettres_symetrie_pool'])) {
        $_SESSION['lettres_symetrie_pool'] = array_keys($lettres);
        shuffle($_SESSION['lettres_symetrie_pool']);
    }
    
    // Types de questions
    $types_questions = [
        ['type' => 'admet_2_axes', 'condition' => function($l) { return $l['axes'] >= 2; }],
        ['type' => 'admet_1_axe', 'condition' => function($l) { return $l['axes'] == 1; }],
        ['type' => 'admet_centre', 'condition' => function($l) { return $l['centre']; }],
        ['type' => 'admet_axe_et_centre', 'condition' => function($l) { return $l['axes'] > 0 && $l['centre']; }],
    ];
    
    // Choisir un type de question
    $type_q = $types_questions[array_rand($types_questions)];
    
    // Trouver une lettre qui correspond OU qui ne correspond pas
    $reponse_voulue = (rand(0, 1) == 1); // true = OUI, false = NON
    
    // Chercher dans le pool une lettre qui convient
    $lettre_choisie = null;
    $index_trouve = null;
    
    foreach ($_SESSION['lettres_symetrie_pool'] as $index => $lettre) {
        $props = $lettres[$lettre];
        $correspond = $type_q['condition']($props);
        if ($correspond == $reponse_voulue) {
            $lettre_choisie = $lettre;
            $index_trouve = $index;
            break;
        }
    }
    
    // Si aucune lettre ne correspond, inverser la réponse voulue
    if ($lettre_choisie === null) {
        $reponse_voulue = !$reponse_voulue;
        foreach ($_SESSION['lettres_symetrie_pool'] as $index => $lettre) {
            $props = $lettres[$lettre];
            $correspond = $type_q['condition']($props);
            if ($correspond == $reponse_voulue) {
                $lettre_choisie = $lettre;
                $index_trouve = $index;
                break;
            }
        }
    }
    
    // Retirer la lettre du pool
    if ($index_trouve !== null) {
        array_splice($_SESSION['lettres_symetrie_pool'], $index_trouve, 1);
    }
    
    // Générer la question
    $svg = generer_svg_lettre($lettre_choisie);
    
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;"><strong>';
    
    switch ($type_q['type']) {
        case 'admet_2_axes':
            $question .= 'Cette lettre admet au moins 2 axes de symétrie.';
            break;
        case 'admet_1_axe':
            $question .= 'Cette lettre admet exactement 1 axe de symétrie.';
            break;
        case 'admet_centre':
            $question .= 'Cette lettre admet un centre de symétrie.';
            break;
        case 'admet_axe_et_centre':
            $question .= 'Cette lettre admet un axe ET un centre de symétrie.';
            break;
    }
    
    $question .= '</strong></p></div>';
    
    $propositions = [
        'oui' => 'OUI',
        'non' => 'NON'
    ];
    
    $bonne_cle = $reponse_voulue ? 'oui' : 'non';
    
    $qcm = generer_qcm_transformations($propositions, $bonne_cle);
    $question .= $qcm['html'];
    
    $reponse_texte = $reponse_voulue ? 'OUI' : 'NON';
    $reponse = '<p><strong>' . $qcm['bonne_lettre'] . ' – ' . $reponse_texte . '</strong></p>';
    
    return [
        'type' => 'transformations',
        'difficulte_id' => 2.0,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * SVG : Lettres majuscules
 */
function generer_svg_lettre($lettre) {
    $svg = '<svg width="400" height="350" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    $svg .= generer_quadrillage_svg(400, 350, 20);
    
    $stroke_width = 12;
    $color = '#0066cc';
    
    switch ($lettre) {
        case 'A':
            $svg .= '<path d="M 120 280 L 200 80 L 280 280" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg .= '<line x1="150" y1="210" x2="250" y2="210" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'C':
            $svg .= '<path d="M 280 100 Q 200 80 150 130 Q 100 180 100 220 Q 100 260 150 310 Q 200 360 280 340" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'D':
            $svg .= '<path d="M 120 80 L 120 280 L 200 280 Q 280 280 280 180 Q 280 80 200 80 Z" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linejoin="round"/>';
            break;
        case 'F':
            $svg .= '<path d="M 120 280 L 120 80 L 260 80" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg .= '<line x1="120" y1="160" x2="230" y2="160" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'G':
            $svg .= '<path d="M 280 100 Q 200 80 150 130 Q 100 180 100 220 Q 100 260 150 310 Q 200 360 260 340 L 260 200 L 200 200" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            break;
        case 'H':
            $svg .= '<line x1="120" y1="80" x2="120" y2="280" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            $svg .= '<line x1="280" y1="80" x2="280" y2="280" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            $svg .= '<line x1="120" y1="180" x2="280" y2="180" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'I':
            $svg .= '<line x1="200" y1="80" x2="200" y2="280" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            $svg .= '<line x1="150" y1="80" x2="250" y2="80" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            $svg .= '<line x1="150" y1="280" x2="250" y2="280" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'J':
            $svg .= '<path d="M 250 80 L 250 240 Q 250 280 200 280 Q 150 280 150 240" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'L':
            $svg .= '<path d="M 140 80 L 140 280 L 260 280" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            break;
        case 'M':
            $svg .= '<path d="M 100 280 L 100 80 L 200 180 L 300 80 L 300 280" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            break;
        case 'N':
            $svg .= '<path d="M 120 280 L 120 80 L 280 280 L 280 80" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            break;
        case 'O':
            $svg .= '<ellipse cx="200" cy="180" rx="80" ry="100" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '"/>';
            break;
        case 'P':
            $svg .= '<path d="M 120 280 L 120 80 L 220 80 Q 280 80 280 130 Q 280 180 220 180 L 120 180" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            break;
        case 'Q':
            $svg .= '<ellipse cx="200" cy="170" rx="80" ry="90" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '"/>';
            $svg .= '<line x1="250" y1="230" x2="290" y2="280" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'R':
            $svg .= '<path d="M 120 280 L 120 80 L 220 80 Q 280 80 280 130 Q 280 170 230 175 L 280 280" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg .= '<line x1="120" y1="175" x2="220" y2="175" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'S':
            $svg .= '<path d="M 270 110 Q 200 80 150 110 Q 120 130 140 150 Q 160 170 200 180 Q 240 190 260 210 Q 280 230 250 250 Q 200 280 130 250" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'T':
            $svg .= '<line x1="120" y1="80" x2="280" y2="80" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            $svg .= '<line x1="200" y1="80" x2="200" y2="280" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'U':
            $svg .= '<path d="M 120 80 L 120 220 Q 120 280 200 280 Q 280 280 280 220 L 280 80" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'V':
            $svg .= '<path d="M 100 80 L 200 280 L 300 80" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            break;
        case 'W':
            $svg .= '<path d="M 90 80 L 130 280 L 200 160 L 270 280 L 310 80" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            break;
        case 'X':
            $svg .= '<line x1="120" y1="80" x2="280" y2="280" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            $svg .= '<line x1="280" y1="80" x2="120" y2="280" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'Y':
            $svg .= '<path d="M 120 80 L 200 180 L 280 80" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            $svg .= '<line x1="200" y1="180" x2="200" y2="280" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round"/>';
            break;
        case 'Z':
            $svg .= '<path d="M 120 80 L 280 80 L 120 280 L 280 280" fill="none" stroke="' . $color . '" stroke-width="' . $stroke_width . '" stroke-linecap="round" stroke-linejoin="round"/>';
            break;
    }
    
    $svg .= '</svg>';
    return $svg;
}

/**
 * Type 5 : Regard critique
 */
function generer_regard_critique_transformation() {
    global $PRENOMS_QUESTIONS;
    
    if (!isset($PRENOMS_QUESTIONS)) {
        $PRENOMS_QUESTIONS = [
            'garcons' => ['Pierre', 'Paul', 'Tom', 'Lucas'],
            'filles' => ['Marie', 'Léa', 'Emma', 'Chloé']
        ];
    }
    
    $variantes = [
        [
            'type' => 'faux_axiale',
            'prenom' => $PRENOMS_QUESTIONS['garcons'][array_rand($PRENOMS_QUESTIONS['garcons'])],
            'affirmation' => 'dit que le point A\' est l\'image de A par symétrie d\'axe (d)',
            'raison' => '(d) n\'est pas perpendiculaire à [AA\']',
            'genre' => 'il'
        ],
        [
            'type' => 'faux_centrale',
            'prenom' => $PRENOMS_QUESTIONS['filles'][array_rand($PRENOMS_QUESTIONS['filles'])],
            'affirmation' => 'dit que B\' est l\'image de B par symétrie de centre O',
            'raison' => 'O n\'est pas le milieu de [BB\']',
            'genre' => 'elle'
        ]
    ];
    
    $var = $variantes[array_rand($variantes)];
    
    $svg = generer_svg_erreur_transformation($var['type']);
    
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">' . $var['prenom'] . ' ' . $var['affirmation'] . '.</p>';
    $question .= '<p><strong>A-t-' . $var['genre'] . ' raison ?</strong></p>';
    $question .= '</div>';
    
    $propositions = [
        'non' => 'Non, ' . $var['genre'] . ' s\'est tromp' . ($var['genre'] == 'il' ? 'é' : 'ée'),
        'oui' => 'Oui, ' . $var['genre'] . ' a raison',
        'impossible' => 'On ne peut pas savoir',
        'partiel' => 'C\'est presque correct'
    ];
    
    $qcm = generer_qcm_transformations($propositions, 'non');
    $question .= $qcm['html'];
    
    $reponse = '<p><strong>' . $qcm['bonne_lettre'] . ' – Non</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">' . $var['raison'] . '</p>';
    
    return [
        'type' => 'transformations',
        'difficulte_id' => 2.4,
        'question' => $question,
        'reponse' => $reponse
    ];
}

// ============================================
// FONCTIONS SVG
// ============================================

/**
 * SVG : Deux figures avec transformation
 */
function generer_svg_deux_figures_transformation($type) {
    $svg = '<svg width="500" height="400" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // Quadrillage
    $svg .= generer_quadrillage_svg(500, 400, 25);
    
    if ($type == 'axiale') {
        // Triangle SCALENE bleu et son symétrique vert par rapport à un axe vertical
        $svg .= '<polygon points="100,200 150,100 180,200" fill="none" stroke="#0066cc" stroke-width="3"/>';
        
        // Axe de symétrie
        $svg .= '<line x1="250" y1="50" x2="250" y2="350" stroke="#ff0000" stroke-width="2" stroke-dasharray="5,5"/>';
        $svg .= '<text x="255" y="80" font-size="16" fill="#ff0000">(d)</text>';
        
        // Image verte (symétrie, pas de translation possible)
        $svg .= '<polygon points="400,200 350,100 320,200" fill="none" stroke="#00aa00" stroke-width="3"/>';
        
    } elseif ($type == 'centrale') {
        // Triangle SCALENE bleu et son symétrique vert par rapport à un centre
        $svg .= '<polygon points="120,130 180,150 140,210" fill="none" stroke="#0066cc" stroke-width="3"/>';
        
        // FAUX axe diagonal (pour piéger)
        $svg .= '<line x1="80" y1="80" x2="420" y2="320" stroke="#cccccc" stroke-width="1.5" stroke-dasharray="5,5"/>';
        $svg .= '<text x="430" y="330" font-size="14" fill="#999">(d)</text>';
        
        // Centre O (la vraie transformation)
        $svg .= '<circle cx="250" cy="200" r="5" fill="#ff0000"/>';
        $svg .= '<text x="260" y="210" font-size="16" fill="#ff0000" font-weight="bold">O</text>';
        
        // Image verte (symétrie centrale)
        $svg .= '<polygon points="380,270 320,250 360,190" fill="none" stroke="#00aa00" stroke-width="3"/>';
        
    } else { // translation
        // Triangle SCALENE bleu et son translaté vert
        $svg .= '<polygon points="100,240 160,200 120,290" fill="none" stroke="#0066cc" stroke-width="3"/>';
        
        // Image verte (translation)
        $svg .= '<polygon points="300,140 360,100 320,190" fill="none" stroke="#00aa00" stroke-width="3"/>';
    }
    
    $svg .= '</svg>';
    return $svg;
}

/**
 * SVG : Repère avec point pour image
 */
function generer_svg_repere_avec_point($type, $var) {
    $svg = '<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // Repère centré
    $cx = 200; $cy = 200; $unite = 30;
    
    // Quadrillage
    for ($i = -6; $i <= 6; $i++) {
        $x = $cx + $i * $unite;
        $y = $cy + $i * $unite;
        $color = ($i == 0) ? '#000' : '#ddd';
        $width = ($i == 0) ? '2' : '1';
        $svg .= '<line x1="' . $x . '" y1="50" x2="' . $x . '" y2="350" stroke="' . $color . '" stroke-width="' . $width . '"/>';
        $svg .= '<line x1="50" y1="' . $y . '" x2="350" y2="' . $y . '" stroke="' . $color . '" stroke-width="' . $width . '"/>';
    }
    
    // Point de départ
    $px = $cx + $var['point_depart'][0] * $unite;
    $py = $cy - $var['point_depart'][1] * $unite;
    $svg .= '<circle cx="' . $px . '" cy="' . $py . '" r="5" fill="#0066cc"/>';
    
    $lettre = ($type == 'axiale') ? 'M' : (($type == 'centrale') ? 'N' : 'P');
    $svg .= '<text x="' . ($px + 10) . '" y="' . ($py - 5) . '" font-size="16" fill="#0066cc" font-weight="bold">' . $lettre . '</text>';
    
    // Axe ou centre selon le type
    if ($type == 'axiale') {
        $svg .= '<text x="360" y="205" font-size="14" fill="#000">x</text>';
        $svg .= '<text x="205" y="45" font-size="14" fill="#000">y</text>';
    } elseif ($type == 'centrale') {
        $svg .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="4" fill="#ff0000"/>';
        $svg .= '<text x="' . ($cx + 10) . '" y="' . ($cy - 5) . '" font-size="14" fill="#ff0000" font-weight="bold">O</text>';
    } else {
        // Points A et B pour la translation
        $ax = $cx + $var['point_a'][0] * $unite;
        $ay = $cy - $var['point_a'][1] * $unite;
        $bx = $cx + $var['point_b'][0] * $unite;
        $by = $cy - $var['point_b'][1] * $unite;
        
        $svg .= '<circle cx="' . $ax . '" cy="' . $ay . '" r="4" fill="#666"/>';
        $svg .= '<text x="' . ($ax - 20) . '" y="' . ($ay + 5) . '" font-size="14" fill="#666" font-weight="bold">A</text>';
        $svg .= '<circle cx="' . $bx . '" cy="' . $by . '" r="4" fill="#666"/>';
        $svg .= '<text x="' . ($bx + 10) . '" y="' . ($by + 5) . '" font-size="14" fill="#666" font-weight="bold">B</text>';
    }
    
    $svg .= '</svg>';
    return $svg;
}

/**
 * SVG : Figure avec axe de symétrie
 */
function generer_svg_figure_avec_axe($orientation) {
    $svg = '<svg width="400" height="300" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // Quadrillage
    $svg .= generer_quadrillage_svg(400, 300, 20);
    
    if ($orientation == 'vertical') {
        // Axe vertical au milieu
        $svg .= '<line x1="200" y1="50" x2="200" y2="250" stroke="#ff0000" stroke-width="2" stroke-dasharray="5,5"/>';
        
        // Figure symétrique : deux triangles rectangles
        $svg .= '<polygon points="120,150 160,100 160,150" fill="#cce5ff" stroke="#0066cc" stroke-width="2.5"/>';
        $svg .= '<polygon points="280,150 240,100 240,150" fill="#cce5ff" stroke="#0066cc" stroke-width="2.5"/>';
        
        // Deux cercles symétriques
        $svg .= '<circle cx="130" cy="200" r="15" fill="#ffcccc" stroke="#cc0000" stroke-width="2"/>';
        $svg .= '<circle cx="270" cy="200" r="15" fill="#ffcccc" stroke="#cc0000" stroke-width="2"/>';
        
    } else { // diagonale
        // Axe diagonal
        $svg .= '<line x1="80" y1="80" x2="320" y2="220" stroke="#ff0000" stroke-width="2" stroke-dasharray="5,5"/>';
        
        // Rectangle avec axe diagonal
        $svg .= '<rect x="140" y="100" width="120" height="80" fill="#cce5ff" stroke="#0066cc" stroke-width="2.5" transform="rotate(30 200 140)"/>';
    }
    
    $svg .= '</svg>';
    return $svg;
}

/**
 * SVG : Figure avec centre de symétrie
 */
function generer_svg_figure_avec_centre() {
    $svg = '<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // Quadrillage
    $svg .= generer_quadrillage_svg(300, 300, 20);
    
    // Centre O au milieu (le bon) - MAINTENANT EN NOIR
    $svg .= '<circle cx="150" cy="150" r="5" fill="#000"/>';
    $svg .= '<text x="160" y="155" font-size="16" fill="#000" font-weight="bold">O</text>';
    
    // Points A, B, C (distracteurs) - EN NOIR AUSSI
    $svg .= '<circle cx="100" cy="100" r="4" fill="#000"/>';
    $svg .= '<text x="85" y="95" font-size="16" fill="#000" font-weight="bold">A</text>';
    
    $svg .= '<circle cx="200" cy="100" r="4" fill="#000"/>';
    $svg .= '<text x="210" y="105" font-size="16" fill="#000" font-weight="bold">B</text>';
    
    $svg .= '<circle cx="150" cy="80" r="4" fill="#000"/>';
    $svg .= '<text x="135" y="75" font-size="16" fill="#000" font-weight="bold">C</text>';
    
    // Figure avec symétrie centrale (S) - deux courbes symétriques par rapport à O
    $svg .= '<path d="M 100 120 Q 80 140 100 160" fill="none" stroke="#0066cc" stroke-width="3"/>';
    $svg .= '<path d="M 200 180 Q 220 160 200 140" fill="none" stroke="#0066cc" stroke-width="3"/>';
    
    $svg .= '</svg>';
    return $svg;
}

/**
 * SVG : Erreur de transformation
 */
function generer_svg_erreur_transformation($type) {
    $svg = '<svg width="350" height="250" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    $svg .= generer_quadrillage_svg(350, 250, 20);
    
    if ($type == 'faux_axiale') {
        // Point A
        $svg .= '<circle cx="100" cy="100" r="4" fill="#0066cc"/>';
        $svg .= '<text x="85" y="95" font-size="14" fill="#0066cc" font-weight="bold">A</text>';
        
        // Point A' (pas vraiment symétrique)
        $svg .= '<circle cx="250" cy="130" r="4" fill="#00aa00"/>';
        $svg .= '<text x="260" y="135" font-size="14" fill="#00aa00" font-weight="bold">A\'</text>';
        
        // Axe (d) qui n'est PAS perpendiculaire à [AA']
        $svg .= '<line x1="150" y1="50" x2="180" y2="200" stroke="#ff0000" stroke-width="1.5" stroke-dasharray="5,5"/>';
        $svg .= '<text x="185" y="70" font-size="14" fill="#ff0000">(d)</text>';
        
    } else { // faux_centrale
        // Point B
        $svg .= '<circle cx="80" cy="100" r="4" fill="#0066cc"/>';
        $svg .= '<text x="65" y="95" font-size="14" fill="#0066cc" font-weight="bold">B</text>';
        
        // Point B' (O n'est PAS le milieu)
        $svg .= '<circle cx="240" cy="150" r="4" fill="#00aa00"/>';
        $svg .= '<text x="250" y="155" font-size="14" fill="#00aa00" font-weight="bold">B\'</text>';
        
        // Centre O (pas au milieu de [BB'])
        $svg .= '<circle cx="180" cy="120" r="4" fill="#ff0000"/>';
        $svg .= '<text x="190" y="125" font-size="14" fill="#ff0000" font-weight="bold">O</text>';
    }
    
    $svg .= '</svg>';
    return $svg;
}

/**
 * Générer quadrillage SVG
 */
function generer_quadrillage_svg($width, $height, $step) {
    $svg = '';
    for ($x = 0; $x <= $width; $x += $step) {
        $svg .= '<line x1="' . $x . '" y1="0" x2="' . $x . '" y2="' . $height . '" stroke="#e0e0e0" stroke-width="0.5"/>';
    }
    for ($y = 0; $y <= $height; $y += $step) {
        $svg .= '<line x1="0" y1="' . $y . '" x2="' . $width . '" y2="' . $y . '" stroke="#e0e0e0" stroke-width="0.5"/>';
    }
    return $svg;
}

/**
 * Générer distracteurs pour coordonnées (transformations)
 */
function generer_distracteurs_transformations($depart, $image, $type) {
    $x = $depart[0];
    $y = $depart[1];
    $x_img = $image[0];
    $y_img = $image[1];
    
    $propositions = [];
    
    if ($type == 'axiale') {
        $propositions['erreur1'] = '(' . $x_img . ' ; ' . $y . ')'; // y non inversé
        $propositions['erreur2'] = '(' . (-$x) . ' ; ' . $y . ')'; // x inversé au lieu de y (symétrie d'axe Oy)
        $propositions['erreur3'] = '(' . (-$x) . ' ; ' . (-$y) . ')'; // les deux inversés (symétrie centrale)
    } elseif ($type == 'centrale') {
        $propositions['erreur1'] = '(' . $x . ' ; ' . $y . ')'; // pas changé
        $propositions['erreur2'] = '(' . $x_img . ' ; ' . $y . ')'; // x inversé seulement
        $propositions['erreur3'] = '(' . $x . ' ; ' . $y_img . ')'; // y inversé seulement
    } else {
        $dx = $x_img - $x;
        $dy = $y_img - $y;
        $propositions['erreur1'] = '(' . ($x + $dx) . ' ; ' . $y . ')'; // déplacement horizontal seulement
        $propositions['erreur2'] = '(' . $x . ' ; ' . ($y + $dy) . ')'; // déplacement vertical seulement
        $propositions['erreur3'] = '(' . ($x - $dx) . ' ; ' . ($y - $dy) . ')'; // déplacement inverse
    }
    
    return $propositions;
}

/**
 * Générer QCM pour transformations (sur 2 lignes)
 */
function generer_qcm_transformations($propositions_data, $bonne_reponse_key) {
    $propositions_array = [];
    foreach ($propositions_data as $key => $texte) {
        $propositions_array[] = ['key' => $key, 'texte' => $texte];
    }
    shuffle($propositions_array);
    
    $lettres = ['A', 'B', 'C', 'D'];
    $bonne_lettre = '';
    
    $qcm_html = '<p>';
    
    foreach ($propositions_array as $index => $prop) {
        $lettre = $lettres[$index];
        if ($prop['key'] == $bonne_reponse_key) {
            $bonne_lettre = $lettre;
        }
        // Afficher en ligne avec largeur fixe
        $qcm_html .= '<span style="display: inline-block; width: 50%; vertical-align: top;"><strong>' . $lettre . '.</strong> ' . $prop['texte'] . '</span>';
        
        // Saut de ligne après B
        if ($index == 1) {
            $qcm_html .= '<br>';
        }
    }
    
    $qcm_html .= '</p>';
    
    return ['html' => $qcm_html, 'bonne_lettre' => $bonne_lettre];
}

?>
