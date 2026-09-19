<?php
/**
 * Automatisme DNB 2026 : Reconnaître une situation d'application du cosinus
 * Difficulté : MOYEN (range 2.0 - 2.8)
 * FORMAT SANS CALCULATRICE
 * 
 * Types de questions :
 * - Identifier le côté adjacent à un angle (15%)
 * - Identifier l'hypoténuse (15%)
 * - Écrire l'égalité du cosinus (25%)
 * - QCM : quelle égalité permet de calculer une longueur ? (15%)
 * - Regard critique : détecter une erreur (20%)
 * - Valeur du cosinus : cohérence (10%)
 */

// Liste de prénoms avec genre pour les questions
$PRENOMS_QUESTIONS = [
    'garcons' => ['Pierre', 'Paul', 'Tom', 'Lucas', 'Hugo', 'Louis', 'Nathan', 'Théo', 'Arthur', 'Léo'],
    'filles' => ['Marie', 'Léa', 'Emma', 'Chloé', 'Sarah', 'Julie', 'Manon', 'Clara', 'Laura', 'Camille']
];

// Vérifier si angle_chapeau n'est pas déjà définie (définie dans angles_triangle.php)
if (!function_exists('angle_chapeau')) {
    function angle_chapeau($lettres) {
        return '<span class="angle"><span class="hat">^</span>' . $lettres . '</span>';
    }
}

// Fonction pour afficher une fraction
if (!function_exists('fraction')) {
    function fraction($numerateur, $denominateur) {
        return '<span style="display: inline-block; vertical-align: middle; text-align: center;">' .
               '<span style="display: block; border-bottom: 1px solid #000; padding: 0 5px;">' . $numerateur . '</span>' .
               '<span style="display: block; padding: 0 5px;">' . $denominateur . '</span>' .
               '</span>';
    }
}

function generer_cosinus() {
    // Anti-doublon : pool de types
    if (!isset($_SESSION['cosinus_pool']) || empty($_SESSION['cosinus_pool'])) {
        $_SESSION['cosinus_pool'] = [
            'identifier_adjacent',
            'identifier_adjacent',
            'identifier_hypotenuse',
            'identifier_hypotenuse',
            'ecrire_egalite',
            'ecrire_egalite',
            'ecrire_egalite',
            'qcm_egalite',
            'qcm_egalite',
            'regard_critique',
            'regard_critique',
            'valeur_cosinus'
        ];
        shuffle($_SESSION['cosinus_pool']);
    }
    
    $type = array_shift($_SESSION['cosinus_pool']);
    
    switch ($type) {
        case 'identifier_adjacent':
            return generer_cosinus_identifier_adjacent();
        case 'identifier_hypotenuse':
            return generer_cosinus_identifier_hypotenuse();
        case 'ecrire_egalite':
            return generer_cosinus_ecrire_egalite();
        case 'qcm_egalite':
            return generer_cosinus_qcm_egalite();
        case 'regard_critique':
            return generer_cosinus_regard_critique();
        case 'valeur_cosinus':
            return generer_cosinus_valeur_cosinus();
    }
}

/**
 * Type 1 : Identifier le côté adjacent à un angle aigu
 */
function generer_cosinus_identifier_adjacent() {
    // Labels aléatoires
    $labels_possibles = [
        ['A', 'B', 'C'],
        ['E', 'F', 'G'],
        ['M', 'N', 'P'],
        ['R', 'S', 'T']
    ];
    $labels = $labels_possibles[array_rand($labels_possibles)];
    
    // Position de l'angle droit et de l'angle considéré
    $position_angle = rand(0, 1); // 0=gauche, 1=droite
    
    if ($position_angle == 0) {
        // Triangle ABC rectangle en B, angle en A
        $sommet_angle = $labels[0];
        $sommet_droit = $labels[1];
        $sommet_oppose = $labels[2];
        $cote_adjacent = $sommet_angle . $sommet_droit;
        $hypotenuse = $sommet_angle . $sommet_oppose;
    } else {
        // Triangle ABC rectangle en B, angle en C
        $sommet_angle = $labels[2];
        $sommet_droit = $labels[1];
        $sommet_oppose = $labels[0];
        $cote_adjacent = $sommet_angle . $sommet_droit;
        $hypotenuse = $sommet_angle . $sommet_oppose;
    }
    
    // SVG
    $svg = generer_svg_triangle_rectangle_simple($labels[0], $labels[1], $labels[2], $position_angle);
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Le triangle ' . $labels[0] . $labels[1] . $labels[2] . ' est rectangle en ' . $sommet_droit . '.</p>';
    $question .= '<p><strong>Quel est le côté adjacent à l\'angle ' . angle_chapeau($sommet_angle) . ' ?</strong></p>';
    $question .= '</div>';
    
    // Réponse
    $reponse = '<p>Le côté adjacent à l\'angle ' . angle_chapeau($sommet_angle) . ' est <strong>[' . $cote_adjacent . ']</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Le côté adjacent à un angle est le côté qui "touche" l\'angle et qui n\'est pas l\'hypoténuse.</p>';
    
    return [
        'type' => 'cosinus',
        'difficulte_id' => 2.0,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Type 2 : Identifier l'hypoténuse
 */
function generer_cosinus_identifier_hypotenuse() {
    $labels_possibles = [
        ['A', 'B', 'C'],
        ['E', 'F', 'G'],
        ['M', 'N', 'P'],
        ['R', 'S', 'T']
    ];
    $labels = $labels_possibles[array_rand($labels_possibles)];
    
    $position_angle = rand(0, 1);
    
    if ($position_angle == 0) {
        $sommet_angle = $labels[0];
        $sommet_droit = $labels[1];
        $sommet_oppose = $labels[2];
        $hypotenuse = $sommet_angle . $sommet_oppose;
    } else {
        $sommet_angle = $labels[2];
        $sommet_droit = $labels[1];
        $sommet_oppose = $labels[0];
        $hypotenuse = $sommet_angle . $sommet_oppose;
    }
    
    $svg = generer_svg_triangle_rectangle_simple($labels[0], $labels[1], $labels[2], $position_angle);
    
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Le triangle ' . $labels[0] . $labels[1] . $labels[2] . ' est rectangle en ' . $sommet_droit . '.</p>';
    $question .= '<p><strong>Quel est l\'hypoténuse de ce triangle ?</strong></p>';
    $question .= '</div>';
    
    $reponse = '<p>L\'hypoténuse est <strong>[' . $hypotenuse . ']</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">L\'hypoténuse est le côté opposé à l\'angle droit (le plus long côté du triangle rectangle).</p>';
    
    return [
        'type' => 'cosinus',
        'difficulte_id' => 2.1,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Type 3 : Écrire l'égalité du cosinus
 */
function generer_cosinus_ecrire_egalite() {
    $labels_possibles = [
        ['A', 'B', 'C'],
        ['E', 'F', 'G'],
        ['M', 'N', 'P'],
        ['R', 'S', 'T']
    ];
    $labels = $labels_possibles[array_rand($labels_possibles)];
    
    $position_angle = rand(0, 1);
    
    if ($position_angle == 0) {
        $sommet_angle = $labels[0];
        $sommet_droit = $labels[1];
        $sommet_oppose = $labels[2];
        $cote_adjacent = $sommet_angle . $sommet_droit;
        $hypotenuse = $sommet_angle . $sommet_oppose;
    } else {
        $sommet_angle = $labels[2];
        $sommet_droit = $labels[1];
        $sommet_oppose = $labels[0];
        $cote_adjacent = $sommet_angle . $sommet_droit;
        $hypotenuse = $sommet_angle . $sommet_oppose;
    }
    
    $svg = generer_svg_triangle_rectangle_simple($labels[0], $labels[1], $labels[2], $position_angle);
    
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Le triangle ' . $labels[0] . $labels[1] . $labels[2] . ' est rectangle en ' . $sommet_droit . '.</p>';
    $question .= '<p><strong>Compléter l\'égalité :</strong></p>';
    $question .= '<p style="font-size: 1.1em;">cos(' . angle_chapeau($sommet_angle) . ') = ' . fraction('...', '...') . '</p>';
    $question .= '</div>';
    
    $reponse = '<p><strong>cos(' . angle_chapeau($sommet_angle) . ') = ' . fraction($cote_adjacent, $hypotenuse) . '</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Dans un triangle rectangle :</p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">cos(angle) = ' . fraction('côté adjacent', 'hypoténuse') . '</p>';
    
    return [
        'type' => 'cosinus',
        'difficulte_id' => 2.3,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Type 4 : QCM - Quelle égalité permet de calculer une longueur ?
 */
function generer_cosinus_qcm_egalite() {
    $labels_possibles = [
        ['A', 'B', 'C'],
        ['E', 'F', 'G'],
        ['M', 'N', 'P']
    ];
    $labels = $labels_possibles[array_rand($labels_possibles)];
    
    $position_angle = rand(0, 1);
    
    if ($position_angle == 0) {
        $sommet_angle = $labels[0];
        $sommet_droit = $labels[1];
        $sommet_oppose = $labels[2];
        $cote_adjacent = $sommet_angle . $sommet_droit;
        $cote_oppose_angle = $sommet_droit . $sommet_oppose;
        $hypotenuse = $sommet_angle . $sommet_oppose;
    } else {
        $sommet_angle = $labels[2];
        $sommet_droit = $labels[1];
        $sommet_oppose = $labels[0];
        $cote_adjacent = $sommet_angle . $sommet_droit;
        $cote_oppose_angle = $sommet_droit . $sommet_oppose;
        $hypotenuse = $sommet_angle . $sommet_oppose;
    }
    
    // Choisir quel côté on veut calculer
    $type_calcul = rand(0, 1); // 0=adjacent, 1=hypoténuse
    
    if ($type_calcul == 0) {
        $cote_cherche = $cote_adjacent;
    } else {
        $cote_cherche = $hypotenuse;
    }
    
    // Propositions avec erreurs classiques
    $propositions = [
        'bonne' => 'cos(' . angle_chapeau($sommet_angle) . ') = ' . fraction($cote_adjacent, $hypotenuse),
        'erreur1' => 'cos(' . angle_chapeau($sommet_angle) . ') = ' . fraction($cote_oppose_angle, $hypotenuse), // opposé au lieu d'adjacent
        'erreur2' => 'cos(' . angle_chapeau($sommet_angle) . ') = ' . fraction($hypotenuse, $cote_adjacent), // fraction inversée
        'erreur3' => 'cos(' . angle_chapeau($sommet_oppose) . ') = ' . fraction($cote_oppose_angle, $hypotenuse) // mauvais angle + mauvais côté
    ];
    
    $qcm = generer_qcm_cosinus($propositions, 'bonne');
    
    $svg = generer_svg_triangle_rectangle_simple($labels[0], $labels[1], $labels[2], $position_angle);
    
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Le triangle ' . $labels[0] . $labels[1] . $labels[2] . ' est rectangle en ' . $sommet_droit . '.</p>';
    $question .= '<p><strong>Quelle égalité permet de calculer ' . $cote_cherche . ' ?</strong></p>';
    $question .= $qcm['html'];
    $question .= '</div>';
    
    $reponse = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
    
    return [
        'type' => 'cosinus',
        'difficulte_id' => 2.5,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Type 5 : Regard critique - détecter une erreur
 */
function generer_cosinus_regard_critique() {
    global $PRENOMS_QUESTIONS;
    
    $labels = ['A', 'B', 'C'];
    
    // Créer une situation avec erreur
    $type_erreur = rand(1, 3);
    
    switch ($type_erreur) {
        case 1:
            // Erreur : angle obtus trouvé
            $prenom = $PRENOMS_QUESTIONS['filles'][array_rand($PRENOMS_QUESTIONS['filles'])];
            $question = '<p>' . $prenom . ' calcule un angle dans un triangle rectangle.</p>';
            $question .= '<p>Elle trouve : <strong>' . angle_chapeau('A') . ' = 125°</strong></p>';
            $question .= '<p><strong>Que peut-on dire de ce résultat ?</strong></p>';
            
            $reponse = '<p><strong>Ce résultat est impossible.</strong></p>';
            $reponse .= '<p style="font-size: 0.9em; color: #666;">Dans un triangle rectangle, les angles aigus sont compris entre 0° et 90°.</p>';
            $reponse .= '<p style="font-size: 0.9em; color: #666;">Un angle de 125° est obtus, donc impossible dans un triangle rectangle.</p>';
            $difficulte = 2.6;
            break;
            
        case 2:
            // Erreur : mauvaise identification adjacent/opposé
            $position = rand(0, 1);
            if ($position == 0) {
                $prenom = $PRENOMS_QUESTIONS['garcons'][array_rand($PRENOMS_QUESTIONS['garcons'])];
                $question = '<p>Dans le triangle ABC rectangle en B, ' . $prenom . ' écrit :</p>';
                $question .= '<p><strong>cos(' . angle_chapeau('A') . ') = ' . fraction('BC', 'AC') . '</strong></p>';
                $question .= '<p><strong>A-t-il raison ?</strong></p>';
                
                $reponse = '<p><strong>Non, ' . $prenom . ' s\'est trompé.</strong></p>';
                $reponse .= '<p style="font-size: 0.9em; color: #666;">BC est le côté opposé à l\'angle ' . angle_chapeau('A') . ', pas le côté adjacent.</p>';
                $reponse .= '<p style="font-size: 0.9em; color: #666;">La bonne égalité est : cos(' . angle_chapeau('A') . ') = ' . fraction('AB', 'AC') . '</p>';
            } else {
                $prenom = $PRENOMS_QUESTIONS['filles'][array_rand($PRENOMS_QUESTIONS['filles'])];
                $question = '<p>Dans le triangle EFG rectangle en F, ' . $prenom . ' écrit :</p>';
                $question .= '<p><strong>cos(' . angle_chapeau('E') . ') = ' . fraction('EG', 'FG') . '</strong></p>';
                $question .= '<p><strong>A-t-elle raison ?</strong></p>';
                
                $reponse = '<p><strong>Non, ' . $prenom . ' s\'est trompée.</strong></p>';
                $reponse .= '<p style="font-size: 0.9em; color: #666;">EG est l\'hypoténuse, elle ne peut pas être au numérateur.</p>';
                $reponse .= '<p style="font-size: 0.9em; color: #666;">La bonne égalité est : cos(' . angle_chapeau('E') . ') = ' . fraction('EF', 'EG') . '</p>';
            }
            $difficulte = 2.7;
            break;
            
        case 3:
            // Erreur : triangle non rectangle
            $prenom = $PRENOMS_QUESTIONS['garcons'][array_rand($PRENOMS_QUESTIONS['garcons'])];
            $question = '<p>' . $prenom . ' veut utiliser le cosinus dans le triangle ABC où :</p>';
            $question .= '<p>' . angle_chapeau('A') . ' = 50°, ' . angle_chapeau('B') . ' = 60°, ' . angle_chapeau('C') . ' = 70°</p>';
            $question .= '<p><strong>Peut-il utiliser le cosinus ?</strong></p>';
            
            $reponse = '<p><strong>Non, il ne peut pas utiliser le cosinus.</strong></p>';
            $reponse .= '<p style="font-size: 0.9em; color: #666;">Le triangle ABC n\'est pas rectangle (aucun angle droit).</p>';
            $reponse .= '<p style="font-size: 0.9em; color: #666;">Le cosinus ne s\'utilise que dans un triangle rectangle.</p>';
            $difficulte = 2.8;
            break;
    }
    
    return [
        'type' => 'cosinus',
        'difficulte_id' => $difficulte,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Type 6 : Valeur du cosinus - cohérence
 */
function generer_cosinus_valeur_cosinus() {
    global $PRENOMS_QUESTIONS;
    
    // Type de valeur : 1 = supérieure à 1, 2 = entre 0 et 1
    $type_valeur = rand(1, 2);
    
    if ($type_valeur == 1) {
        // Valeur > 1 (impossible)
        $valeurs_impossibles = [1.05, 1.2, 1.37, 1.5, 1.8, 2.0];
        $valeur = $valeurs_impossibles[array_rand($valeurs_impossibles)];
        
        $prenom = $PRENOMS_QUESTIONS['garcons'][array_rand($PRENOMS_QUESTIONS['garcons'])];
        
        $propositions = [
            'faux' => 'Il s\'est trompé',
            'bonne' => 'Il a trouvé la bonne réponse',
            'unite' => 'Il a oublié l\'unité',
            'savoir' => 'On ne peut pas savoir'
        ];
        
        $question = '<p>' . $prenom . ' a trouvé que le cosinus d\'un angle aigu était environ égal à <strong>' . $valeur . '</strong></p>';
        $question .= '<p><strong>Que peut-on dire ?</strong></p>';
        
        $qcm = generer_qcm_cosinus($propositions, 'faux');
        $question .= $qcm['html'];
        
        $reponse = '<p><strong>' . $qcm['bonne_lettre'] . ' – Il s\'est trompé</strong></p>';
        $reponse .= '<p style="font-size: 0.9em; color: #666;">Le cosinus d\'un angle aigu est toujours compris entre 0 et 1.</p>';
        $reponse .= '<p style="font-size: 0.9em; color: #666;">Une valeur de ' . $valeur . ' est impossible pour un cosinus.</p>';
        
    } else {
        // Valeur entre 0 et 1 (possible, mais on ne peut pas savoir si c'est juste)
        $valeurs_possibles = [0.2, 0.35, 0.5, 0.64, 0.71, 0.87, 0.92];
        $valeur = $valeurs_possibles[array_rand($valeurs_possibles)];
        
        $prenom = $PRENOMS_QUESTIONS['filles'][array_rand($PRENOMS_QUESTIONS['filles'])];
        
        $propositions = [
            'faux' => 'Elle s\'est trompée',
            'bonne' => 'Elle a trouvé la bonne réponse',
            'unite' => 'Elle a oublié l\'unité',
            'savoir' => 'On ne peut pas savoir'
        ];
        
        $question = '<p>' . $prenom . ' a trouvé que le cosinus d\'un angle aigu était environ égal à <strong>' . $valeur . '</strong></p>';
        $question .= '<p><strong>Que peut-on dire ?</strong></p>';
        
        $qcm = generer_qcm_cosinus($propositions, 'savoir');
        $question .= $qcm['html'];
        
        $reponse = '<p><strong>' . $qcm['bonne_lettre'] . ' – On ne peut pas savoir</strong></p>';
        $reponse .= '<p style="font-size: 0.9em; color: #666;">Le cosinus d\'un angle aigu est toujours compris entre 0 et 1.</p>';
        $reponse .= '<p style="font-size: 0.9em; color: #666;">La valeur ' . $valeur . ' est cohérente, mais on ne peut pas savoir si c\'est le bon résultat sans connaître l\'angle ou les longueurs du triangle.</p>';
    }
    
    return [
        'type' => 'cosinus',
        'difficulte_id' => 2.4,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * SVG triangle rectangle simple
 */
function generer_svg_triangle_rectangle_simple($label_A, $label_B, $label_C, $position) {
    $svg = '<svg viewBox="0 0 320 250" width="320" height="250" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block; max-width:100%; height:auto;">';
    
    if ($position == 0) {
        // A en bas-gauche, B en bas-droite (angle droit), C en haut-droite
        $ax = 50; $ay = 210;
        $bx = 200; $by = 210;
        $cx = 200; $cy = 70;
        
        // Marque angle droit en B
        $svg .= '<rect x="190" y="200" width="10" height="10" fill="none" stroke="#000" stroke-width="1.5"/>';
        
        // Triangle
        $svg .= '<line x1="' . $ax . '" y1="' . $ay . '" x2="' . $bx . '" y2="' . $by . '" stroke="#000" stroke-width="2.5"/>';
        $svg .= '<line x1="' . $bx . '" y1="' . $by . '" x2="' . $cx . '" y2="' . $cy . '" stroke="#000" stroke-width="2.5"/>';
        $svg .= '<line x1="' . $cx . '" y1="' . $cy . '" x2="' . $ax . '" y2="' . $ay . '" stroke="#000" stroke-width="2.5"/>';
        
        // Labels sommets
        $svg .= '<text x="' . ($ax - 15) . '" y="' . ($ay + 5) . '" font-size="20" fill="#000" font-weight="bold">' . $label_A . '</text>';
        $svg .= '<text x="' . ($bx + 5) . '" y="' . ($by + 20) . '" font-size="20" fill="#000" font-weight="bold">' . $label_B . '</text>';
        $svg .= '<text x="' . ($cx + 10) . '" y="' . ($cy + 5) . '" font-size="20" fill="#000" font-weight="bold">' . $label_C . '</text>';
        
    } else {
        // A en bas-droite, B en bas-gauche (angle droit), C en haut-gauche
        $ax = 270; $ay = 210;
        $bx = 120; $by = 210;
        $cx = 120; $cy = 70;
        
        // Marque angle droit en B
        $svg .= '<rect x="120" y="200" width="10" height="10" fill="none" stroke="#000" stroke-width="1.5"/>';
        
        // Triangle
        $svg .= '<line x1="' . $ax . '" y1="' . $ay . '" x2="' . $bx . '" y2="' . $by . '" stroke="#000" stroke-width="2.5"/>';
        $svg .= '<line x1="' . $bx . '" y1="' . $by . '" x2="' . $cx . '" y2="' . $cy . '" stroke="#000" stroke-width="2.5"/>';
        $svg .= '<line x1="' . $cx . '" y1="' . $cy . '" x2="' . $ax . '" y2="' . $ay . '" stroke="#000" stroke-width="2.5"/>';
        
        // Labels sommets
        $svg .= '<text x="' . ($ax + 10) . '" y="' . ($ay + 5) . '" font-size="20" fill="#000" font-weight="bold">' . $label_A . '</text>';
        $svg .= '<text x="' . ($bx - 30) . '" y="' . ($by + 20) . '" font-size="20" fill="#000" font-weight="bold">' . $label_B . '</text>';
        $svg .= '<text x="' . ($cx - 30) . '" y="' . ($cy + 5) . '" font-size="20" fill="#000" font-weight="bold">' . $label_C . '</text>';
    }
    
    $svg .= '</svg>';
    return $svg;
}

/**
 * Générer un QCM pour cosinus
 */
function generer_qcm_cosinus($propositions_data, $bonne_reponse_key) {
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
        // Afficher en ligne avec largeur fixe pour aligner verticalement
        $qcm_html .= '<span style="display: inline-block; width: 50%; vertical-align: top;"><strong>' . $lettre . '.</strong> ' . $prop['texte'] . '</span>';
        
        // Saut de ligne après B (index 1)
        if ($index == 1) {
            $qcm_html .= '<br>';
        }
    }
    
    $qcm_html .= '</p>';
    
    return ['html' => $qcm_html, 'bonne_lettre' => $bonne_lettre];
}

?>
