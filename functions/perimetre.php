<?php
/**
 * Automatisme DNB 2026 : Déterminer le périmètre d'un polygone, d'un cercle
 * Difficulté : FACILE (range 1.0 - 2.0)
 * 
 * Types de figures :
 * - Rectangle (20%)
 * - Carré (15%)
 * - Triangle quelconque (20%)
 * - Triangle équilatéral (15%)
 * - Pentagone régulier (10%)
 * - Hexagone régulier (10%)
 * - Cercle (10%)
 */

function generer_perimetre() {
    // Anti-doublon : tracker les combinaisons utilisées
    if (!isset($_SESSION['dnb_perimetre_used'])) {
        $_SESSION['dnb_perimetre_used'] = [];
    }
    
    // Définir toutes les combinaisons possibles
    // Format : [type_figure, niveau_difficulte]
    // type_figure : 1=rectangle, 2=carré, 3=triangle quelconque, 4=triangle équilatéral, 5=pentagone, 6=hexagone, 7=cercle
    // niveau_difficulte : 1=facile (entiers simples), 2=moyen (décimaux ou grands nombres)
    $all_combinations = [];
    for ($type = 1; $type <= 7; $type++) {
        for ($niveau = 1; $niveau <= 2; $niveau++) {
            $all_combinations[] = [$type, $niveau];
        }
    }
    
    // Filtrer les combinaisons déjà utilisées
    $available = array_filter($all_combinations, function($combo) {
        return !in_array($combo, $_SESSION['dnb_perimetre_used']);
    });
    
    // Si toutes utilisées, reset
    if (empty($available)) {
        $_SESSION['dnb_perimetre_used'] = [];
        $available = $all_combinations;
    }
    
    // Tirer une combinaison
    $available = array_values($available);
    $chosen = $available[array_rand($available)];
    list($type_figure, $niveau) = $chosen;
    
    // Marquer comme utilisée
    $_SESSION['dnb_perimetre_used'][] = $chosen;
    
    // Générer la question selon le type
    switch ($type_figure) {
        case 1:
            return generer_perimetre_rectangle($niveau);
        case 2:
            return generer_perimetre_carre($niveau);
        case 3:
            return generer_perimetre_triangle($niveau);
        case 4:
            return generer_perimetre_triangle_equilateral($niveau);
        case 5:
            return generer_perimetre_pentagone($niveau);
        case 6:
            return generer_perimetre_hexagone($niveau);
        case 7:
            return generer_perimetre_disque($niveau); // Fonction nommée disque mais génère cercle
    }
}

/**
 * Rectangle : P = 2(L + l)
 */
function generer_perimetre_rectangle($niveau) {
    if ($niveau == 1) {
        // Facile : entiers simples
        $longueur = rand(4, 12);
        $largeur = rand(2, $longueur - 1);
        $difficulte_id = 1.0 + ($longueur / 20);
    } else {
        // Moyen : décimaux
        $longueur = rand(5, 15) + 0.5;
        $largeur = rand(2, 8) + 0.5;
        $difficulte_id = 1.3 + ($longueur / 30);
    }
    
    $perimetre = 2 * ($longueur + $largeur);
    
    // Labels aléatoires
    $labels = [
        ['A', 'B', 'C', 'D'],
        ['E', 'F', 'G', 'H'],
        ['M', 'N', 'P', 'Q'],
        ['R', 'S', 'T', 'U']
    ];
    $label_set = $labels[array_rand($labels)];
    
    // SVG du rectangle
    $svg_width = 400;
    $svg_height = 300;
    $rect_width = 200;
    $rect_height = $rect_width * ($largeur / $longueur);
    $x = ($svg_width - $rect_width) / 2;
    $y = ($svg_height - $rect_height) / 2;
    
    $svg = '<svg viewBox="0 0 ' . $svg_width . ' ' . $svg_height . '" width="' . $svg_width . '" height="' . $svg_height . '" xmlns="http://www.w3.org/2000/svg" style="max-width:100%; height:auto;">';
    
    // Rectangle
    $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $rect_width . '" height="' . $rect_height . '" ';
    $svg .= 'fill="none" stroke="#2563eb" stroke-width="2"/>';
    
    // Labels des sommets
    $svg .= '<text x="' . ($x - 15) . '" y="' . ($y - 5) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[0] . '</text>';
    $svg .= '<text x="' . ($x + $rect_width + 5) . '" y="' . ($y - 5) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[1] . '</text>';
    $svg .= '<text x="' . ($x + $rect_width + 5) . '" y="' . ($y + $rect_height + 20) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[2] . '</text>';
    $svg .= '<text x="' . ($x - 15) . '" y="' . ($y + $rect_height + 20) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[3] . '</text>';
    
    // Cotes
    $longueur_str = ($niveau == 1) ? $longueur : number_format($longueur, 1, ',', ' ');
    $largeur_str = ($niveau == 1) ? $largeur : number_format($largeur, 1, ',', ' ');
    
    $svg .= '<text x="' . ($x + $rect_width / 2 - 20) . '" y="' . ($y - 15) . '" font-size="14" fill="#dc2626">' . $longueur_str . ' cm</text>';
    $svg .= '<text x="' . ($x + $rect_width + 15) . '" y="' . ($y + $rect_height / 2 + 5) . '" font-size="14" fill="#dc2626">' . $largeur_str . ' cm</text>';
    
    $svg .= '</svg>';
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>périmètre</strong> du rectangle <strong>' . $label_set[0] . $label_set[1] . $label_set[2] . $label_set[3] . '</strong> ?</p>';
    $question .= '</div>';
    
    // Réponse
    if ($niveau == 1) {
        $perimetre_str = $perimetre;
        $calcul = "2 × ($longueur + $largeur) = 2 × " . ($longueur + $largeur) . " = $perimetre";
    } else {
        // Vérifier si le résultat est entier pour éviter 30,0
        $perimetre_str = ($perimetre == floor($perimetre)) ? (int)$perimetre : number_format($perimetre, 1, ',', ' ');
        $longueur_str = ($longueur == floor($longueur)) ? (int)$longueur : number_format($longueur, 1, ',', ' ');
        $largeur_str = ($largeur == floor($largeur)) ? (int)$largeur : number_format($largeur, 1, ',', ' ');
        $calcul = "2 × (" . $longueur_str . " + " . $largeur_str . ") = " . $perimetre_str;
    }
    
    $reponse = '<p>Le périmètre du rectangle est : <strong>' . $perimetre_str . ' cm</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Calcul : ' . $calcul . ' cm</p>';
    
    return [
        'type' => 'perimetre',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Carré : P = 4c
 */
function generer_perimetre_carre($niveau) {
    if ($niveau == 1) {
        // Facile : entiers simples
        $cote = rand(3, 10);
        $difficulte_id = 1.0 + ($cote / 15);
    } else {
        // Moyen : décimaux
        $cote = rand(4, 12) + 0.5;
        $difficulte_id = 1.3 + ($cote / 20);
    }
    
    $perimetre = 4 * $cote;
    
    // Labels aléatoires
    $labels = [
        ['A', 'B', 'C', 'D'],
        ['E', 'F', 'G', 'H'],
        ['I', 'J', 'K', 'L'],
        ['M', 'N', 'P', 'Q']
    ];
    $label_set = $labels[array_rand($labels)];
    
    // SVG du carré
    $svg_width = 400;
    $svg_height = 300;
    $carre_size = 150;
    $x = ($svg_width - $carre_size) / 2;
    $y = ($svg_height - $carre_size) / 2;
    
    $svg = '<svg viewBox="0 0 ' . $svg_width . ' ' . $svg_height . '" width="' . $svg_width . '" height="' . $svg_height . '" xmlns="http://www.w3.org/2000/svg" style="max-width:100%; height:auto;">';
    
    // Carré
    $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $carre_size . '" height="' . $carre_size . '" ';
    $svg .= 'fill="none" stroke="#2563eb" stroke-width="2"/>';
    
    // Labels des sommets
    $svg .= '<text x="' . ($x - 15) . '" y="' . ($y - 5) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[0] . '</text>';
    $svg .= '<text x="' . ($x + $carre_size + 5) . '" y="' . ($y - 5) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[1] . '</text>';
    $svg .= '<text x="' . ($x + $carre_size + 5) . '" y="' . ($y + $carre_size + 20) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[2] . '</text>';
    $svg .= '<text x="' . ($x - 15) . '" y="' . ($y + $carre_size + 20) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[3] . '</text>';
    
    // Cote
    $cote_str = ($niveau == 1) ? $cote : number_format($cote, 1, ',', ' ');
    $svg .= '<text x="' . ($x + $carre_size / 2 - 15) . '" y="' . ($y - 15) . '" font-size="14" fill="#dc2626">' . $cote_str . ' cm</text>';
    
    $svg .= '</svg>';
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>périmètre</strong> du carré <strong>' . $label_set[0] . $label_set[1] . $label_set[2] . $label_set[3] . '</strong> ?</p>';
    $question .= '</div>';
    
    // Réponse
    if ($niveau == 1) {
        $perimetre_str = $perimetre;
        $calcul = "4 × $cote = $perimetre";
    } else {
        // Vérifier si le résultat est entier pour éviter 30,0
        $perimetre_str = ($perimetre == floor($perimetre)) ? (int)$perimetre : number_format($perimetre, 1, ',', ' ');
        $cote_str = ($cote == floor($cote)) ? (int)$cote : number_format($cote, 1, ',', ' ');
        $calcul = "4 × " . $cote_str . " = " . $perimetre_str;
    }
    
    $reponse = '<p>Le périmètre du carré est : <strong>' . $perimetre_str . ' cm</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Calcul : ' . $calcul . ' cm</p>';
    
    return [
        'type' => 'perimetre',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Triangle : P = a + b + c
 */
function generer_perimetre_triangle($niveau) {
    if ($niveau == 1) {
        // Facile : entiers simples
        $a = rand(4, 10);
        $b = rand(3, 9);
        $c = rand(4, 10);
        $difficulte_id = 1.1 + (($a + $b + $c) / 50);
    } else {
        // Moyen : décimaux
        $a = rand(4, 12) + 0.5;
        $b = rand(3, 10) + 0.5;
        $c = rand(4, 11) + 0.5;
        $difficulte_id = 1.4 + (($a + $b + $c) / 60);
    }
    
    $perimetre = $a + $b + $c;
    
    // Labels aléatoires
    $labels = [
        ['A', 'B', 'C'],
        ['D', 'E', 'F'],
        ['L', 'M', 'N'],
        ['P', 'Q', 'R'],
        ['S', 'T', 'U']
    ];
    $label_set = $labels[array_rand($labels)];
    
    // SVG du triangle
    $svg_width = 400;
    $svg_height = 300;
    
    // Points du triangle (scalène quelconque)
    $x1 = 100;
    $y1 = 220;
    $x2 = 300;
    $y2 = 220;
    $x3 = 180;
    $y3 = 80;
    
    $svg = '<svg viewBox="0 0 ' . $svg_width . ' ' . $svg_height . '" width="' . $svg_width . '" height="' . $svg_height . '" xmlns="http://www.w3.org/2000/svg" style="max-width:100%; height:auto;">';
    
    // Triangle
    $svg .= '<polygon points="' . $x1 . ',' . $y1 . ' ' . $x2 . ',' . $y2 . ' ' . $x3 . ',' . $y3 . '" ';
    $svg .= 'fill="none" stroke="#2563eb" stroke-width="2"/>';
    
    // Labels des sommets
    $svg .= '<text x="' . ($x1 - 20) . '" y="' . ($y1 + 5) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[0] . '</text>';
    $svg .= '<text x="' . ($x2 + 10) . '" y="' . ($y2 + 5) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[1] . '</text>';
    $svg .= '<text x="' . ($x3 - 5) . '" y="' . ($y3 - 10) . '" font-size="16" fill="#1e40af" font-weight="bold">' . $label_set[2] . '</text>';
    
    // Cotes
    $a_str = ($niveau == 1) ? $a : number_format($a, 1, ',', ' ');
    $b_str = ($niveau == 1) ? $b : number_format($b, 1, ',', ' ');
    $c_str = ($niveau == 1) ? $c : number_format($c, 1, ',', ' ');
    
    // Côté AB (base)
    $svg .= '<text x="' . (($x1 + $x2) / 2 - 15) . '" y="' . ($y1 + 25) . '" font-size="14" fill="#dc2626">' . $c_str . ' cm</text>';
    // Côté BC
    $svg .= '<text x="' . (($x2 + $x3) / 2 + 10) . '" y="' . (($y2 + $y3) / 2) . '" font-size="14" fill="#dc2626">' . $a_str . ' cm</text>';
    // Côté CA
    $svg .= '<text x="' . (($x1 + $x3) / 2 - 40) . '" y="' . (($y1 + $y3) / 2) . '" font-size="14" fill="#dc2626">' . $b_str . ' cm</text>';
    
    $svg .= '</svg>';
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>périmètre</strong> du triangle <strong>' . $label_set[0] . $label_set[1] . $label_set[2] . '</strong> ?</p>';
    $question .= '</div>';
    
    // Réponse
    if ($niveau == 1) {
        $perimetre_str = $perimetre;
        $calcul = "$a + $b + $c = $perimetre";
    } else {
        // Vérifier si le résultat est entier pour éviter 30,0
        $perimetre_str = ($perimetre == floor($perimetre)) ? (int)$perimetre : number_format($perimetre, 1, ',', ' ');
        $a_str = ($a == floor($a)) ? (int)$a : number_format($a, 1, ',', ' ');
        $b_str = ($b == floor($b)) ? (int)$b : number_format($b, 1, ',', ' ');
        $c_str = ($c == floor($c)) ? (int)$c : number_format($c, 1, ',', ' ');
        $calcul = $a_str . " + " . $b_str . " + " . $c_str . " = " . $perimetre_str;
    }
    
    $reponse = '<p>Le périmètre du triangle est : <strong>' . $perimetre_str . ' cm</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Calcul : ' . $calcul . ' cm</p>';
    
    return [
        'type' => 'perimetre',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Triangle équilatéral : P = 3c
 */
function generer_perimetre_triangle_equilateral($niveau) {
    if ($niveau == 1) {
        // Facile : entiers simples
        $cote = rand(3, 10);
        $difficulte_id = 1.0 + ($cote / 15);
    } else {
        // Moyen : décimaux
        $cote = rand(4, 12) + 0.5;
        $difficulte_id = 1.3 + ($cote / 20);
    }
    
    $perimetre = 3 * $cote;
    
    // Labels aléatoires
    $labels = [
        ['A', 'B', 'C'],
        ['D', 'E', 'F'],
        ['L', 'M', 'N'],
        ['P', 'Q', 'R']
    ];
    $label_set = $labels[array_rand($labels)];
    
    // SVG du triangle équilatéral
    $svg_width = 400;
    $svg_height = 300;
    
    // Triangle équilatéral centré
    $side = 180;
    $height = $side * sqrt(3) / 2;
    $x1 = 200;
    $y1 = 220;
    $x2 = $x1 + $side;
    $y2 = $y1;
    $x3 = $x1 + $side / 2;
    $y3 = $y1 - $height;
    
    $svg = '<svg viewBox="0 0 ' . $svg_width . ' ' . $svg_height . '" width="' . $svg_width . '" height="' . $svg_height . '" xmlns="http://www.w3.org/2000/svg" style="max-width:100%; height:auto;">';
    
    // Triangle
    $svg .= '<polygon points="' . $x1 . ',' . $y1 . ' ' . $x2 . ',' . $y2 . ' ' . $x3 . ',' . $y3 . '" ';
    $svg .= 'fill="none" stroke="#000" stroke-width="2"/>';
    
    // Labels des sommets
    $svg .= '<text x="' . ($x1 - 20) . '" y="' . ($y1 + 5) . '" font-size="16" fill="#000" font-weight="bold">' . $label_set[0] . '</text>';
    $svg .= '<text x="' . ($x2 + 10) . '" y="' . ($y2 + 5) . '" font-size="16" fill="#000" font-weight="bold">' . $label_set[1] . '</text>';
    $svg .= '<text x="' . ($x3 - 5) . '" y="' . ($y3 - 10) . '" font-size="16" fill="#000" font-weight="bold">' . $label_set[2] . '</text>';
    
    // Un seul côté annoté (puisque triangle équilatéral)
    $cote_str = ($niveau == 1) ? $cote : (($cote == floor($cote)) ? (int)$cote : number_format($cote, 1, ',', ' '));
    $svg .= '<text x="' . (($x1 + $x2) / 2 - 15) . '" y="' . ($y1 + 25) . '" font-size="14" fill="#000">' . $cote_str . ' cm</text>';
    
    $svg .= '</svg>';
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>périmètre</strong> du triangle équilatéral <strong>' . $label_set[0] . $label_set[1] . $label_set[2] . '</strong> ?</p>';
    $question .= '</div>';
    
    // Réponse
    if ($niveau == 1) {
        $perimetre_str = $perimetre;
        $calcul = "3 × $cote = $perimetre";
    } else {
        $perimetre_str = ($perimetre == floor($perimetre)) ? (int)$perimetre : number_format($perimetre, 1, ',', ' ');
        $cote_str = ($cote == floor($cote)) ? (int)$cote : number_format($cote, 1, ',', ' ');
        $calcul = "3 × " . $cote_str . " = " . $perimetre_str;
    }
    
    $reponse = '<p>Le périmètre du triangle équilatéral est : <strong>' . $perimetre_str . ' cm</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Calcul : ' . $calcul . ' cm</p>';
    
    return [
        'type' => 'perimetre',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Pentagone régulier : P = 5c
 */
function generer_perimetre_pentagone($niveau) {
    if ($niveau == 1) {
        // Facile : entiers simples
        $cote = rand(3, 8);
        $difficulte_id = 1.2 + ($cote / 12);
    } else {
        // Moyen : décimaux
        $cote = rand(4, 10) + 0.5;
        $difficulte_id = 1.5 + ($cote / 15);
    }
    
    $perimetre = 5 * $cote;
    
    // SVG du pentagone régulier
    $svg_width = 400;
    $svg_height = 300;
    $center_x = 200;
    $center_y = 160;
    $radius = 90;
    
    $svg = '<svg viewBox="0 0 ' . $svg_width . ' ' . $svg_height . '" width="' . $svg_width . '" height="' . $svg_height . '" xmlns="http://www.w3.org/2000/svg" style="max-width:100%; height:auto;">';
    
    // Calculer les 5 sommets
    $points = [];
    for ($i = 0; $i < 5; $i++) {
        $angle = ($i * 72 - 90) * M_PI / 180; // -90 pour commencer en haut
        $x = $center_x + $radius * cos($angle);
        $y = $center_y + $radius * sin($angle);
        $points[] = "$x,$y";
    }
    
    // Pentagone
    $svg .= '<polygon points="' . implode(' ', $points) . '" ';
    $svg .= 'fill="none" stroke="#000" stroke-width="2"/>';
    
    // Un seul côté annoté
    $cote_str = ($niveau == 1) ? $cote : (($cote == floor($cote)) ? (int)$cote : number_format($cote, 1, ',', ' '));
    $svg .= '<text x="' . ($center_x - 20) . '" y="' . ($center_y + $radius + 30) . '" font-size="14" fill="#000">' . $cote_str . ' cm</text>';
    
    $svg .= '</svg>';
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>périmètre</strong> de ce pentagone régulier ?</p>';
    $question .= '</div>';
    
    // Réponse
    if ($niveau == 1) {
        $perimetre_str = $perimetre;
        $calcul = "5 × $cote = $perimetre";
    } else {
        $perimetre_str = ($perimetre == floor($perimetre)) ? (int)$perimetre : number_format($perimetre, 1, ',', ' ');
        $cote_str = ($cote == floor($cote)) ? (int)$cote : number_format($cote, 1, ',', ' ');
        $calcul = "5 × " . $cote_str . " = " . $perimetre_str;
    }
    
    $reponse = '<p>Le périmètre du pentagone régulier est : <strong>' . $perimetre_str . ' cm</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Calcul : ' . $calcul . ' cm</p>';
    
    return [
        'type' => 'perimetre',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Hexagone régulier : P = 6c
 */
function generer_perimetre_hexagone($niveau) {
    if ($niveau == 1) {
        // Facile : entiers simples
        $cote = rand(3, 8);
        $difficulte_id = 1.2 + ($cote / 12);
    } else {
        // Moyen : décimaux
        $cote = rand(4, 10) + 0.5;
        $difficulte_id = 1.5 + ($cote / 15);
    }
    
    $perimetre = 6 * $cote;
    
    // SVG de l'hexagone régulier
    $svg_width = 400;
    $svg_height = 300;
    $center_x = 200;
    $center_y = 150;
    $radius = 90;
    
    $svg = '<svg viewBox="0 0 ' . $svg_width . ' ' . $svg_height . '" width="' . $svg_width . '" height="' . $svg_height . '" xmlns="http://www.w3.org/2000/svg" style="max-width:100%; height:auto;">';
    
    // Calculer les 6 sommets
    $points = [];
    for ($i = 0; $i < 6; $i++) {
        $angle = ($i * 60 - 90) * M_PI / 180; // -90 pour commencer en haut
        $x = $center_x + $radius * cos($angle);
        $y = $center_y + $radius * sin($angle);
        $points[] = "$x,$y";
    }
    
    // Hexagone
    $svg .= '<polygon points="' . implode(' ', $points) . '" ';
    $svg .= 'fill="none" stroke="#000" stroke-width="2"/>';
    
    // Un seul côté annoté - positionné à droite, centré verticalement
    $cote_str = ($niveau == 1) ? $cote : (($cote == floor($cote)) ? (int)$cote : number_format($cote, 1, ',', ' '));
    $svg .= '<text x="' . ($center_x + $radius + 15) . '" y="' . ($center_y + 5) . '" font-size="14" fill="#000">' . $cote_str . ' cm</text>';
    
    $svg .= '</svg>';
    
    // Question
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>périmètre</strong> de cet hexagone régulier ?</p>';
    $question .= '</div>';
    
    // Réponse
    if ($niveau == 1) {
        $perimetre_str = $perimetre;
        $calcul = "6 × $cote = $perimetre";
    } else {
        $perimetre_str = ($perimetre == floor($perimetre)) ? (int)$perimetre : number_format($perimetre, 1, ',', ' ');
        $cote_str = ($cote == floor($cote)) ? (int)$cote : number_format($cote, 1, ',', ' ');
        $calcul = "6 × " . $cote_str . " = " . $perimetre_str;
    }
    
    $reponse = '<p>Le périmètre de l\'hexagone régulier est : <strong>' . $perimetre_str . ' cm</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Calcul : ' . $calcul . ' cm</p>';
    
    return [
        'type' => 'perimetre',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

/**
 * Cercle : C = 2πr ou C = πd
 */
function generer_perimetre_disque($niveau) {
    // Choisir entre rayon et diamètre
    $use_rayon = rand(0, 1) == 1;
    
    if ($niveau == 1) {
        // Facile : petits nombres entiers
        if ($use_rayon) {
            $rayon = rand(2, 6);
            $diametre = 2 * $rayon;
        } else {
            $diametre = rand(4, 12);
            $rayon = $diametre / 2;
        }
        $difficulte_id = 1.2 + ($rayon / 10);
    } else {
        // Moyen : décimaux ou plus grands
        if ($use_rayon) {
            $rayon = rand(3, 8) + 0.5;
            $diametre = 2 * $rayon;
        } else {
            $diametre = rand(5, 15) + 0.5;
            $rayon = $diametre / 2;
        }
        $difficulte_id = 1.5 + ($rayon / 12);
    }
    
    // SVG du cercle - Taille fixe, l'élève doit interpréter le dessin
    $svg_width = 400;
    $svg_height = 300;
    $center_x = $svg_width / 2;
    $center_y = $svg_height / 2;
    
    // Cercle de taille fixe (rayon visuel constant)
    $circle_radius = 100;
    
    $svg = '<svg viewBox="0 0 ' . $svg_width . ' ' . $svg_height . '" width="' . $svg_width . '" height="' . $svg_height . '" xmlns="http://www.w3.org/2000/svg" style="max-width:100%; height:auto;">';
    
    // Cercle - trait noir simple
    $svg .= '<circle cx="' . $center_x . '" cy="' . $center_y . '" r="' . $circle_radius . '" ';
    $svg .= 'fill="none" stroke="#000" stroke-width="2"/>';
    
    if ($use_rayon) {
        // Afficher le rayon - trait noir du centre vers le bord droit
        $svg .= '<line x1="' . $center_x . '" y1="' . $center_y . '" x2="' . ($center_x + $circle_radius) . '" y2="' . $center_y . '" stroke="#000" stroke-width="2"/>';
        $rayon_str = ($niveau == 1) ? $rayon : (($rayon == floor($rayon)) ? (int)$rayon : number_format($rayon, 1, ',', ' '));
        // Label du rayon au-dessus du trait
        $svg .= '<text x="' . ($center_x + $circle_radius / 2 - 20) . '" y="' . ($center_y - 10) . '" font-size="20" fill="#000" font-weight="bold">' . $rayon_str . ' cm</text>';
    } else {
        // Afficher le diamètre - trait noir horizontal traversant le cercle
        $svg .= '<line x1="' . ($center_x - $circle_radius) . '" y1="' . $center_y . '" x2="' . ($center_x + $circle_radius) . '" y2="' . $center_y . '" stroke="#000" stroke-width="2"/>';
        $diametre_str = ($niveau == 1) ? $diametre : (($diametre == floor($diametre)) ? (int)$diametre : number_format($diametre, 1, ',', ' '));
        // Label du diamètre au-dessus du trait
        $svg .= '<text x="' . ($center_x - 30) . '" y="' . ($center_y - 10) . '" font-size="20" fill="#000" font-weight="bold">' . $diametre_str . ' cm</text>';
    }
    
    $svg .= '</svg>';
    
    // Question - sans référence à rayon ou diamètre, l'élève doit interpréter
    $question = '<div style="text-align: center;">';
    $question .= $svg;
    $question .= '<p style="margin-top: 20px;">Quel est le <strong>périmètre</strong> de ce cercle ?</p>';
    $question .= '<p style="font-size: 0.9em; color: #666; margin-top: 5px;">(Donner la valeur exacte avec π)</p>';
    $question .= '</div>';
    
    // Réponse exacte avec π
    if ($use_rayon) {
        $rayon_str = ($niveau == 1) ? $rayon : (($rayon == floor($rayon)) ? (int)$rayon : number_format($rayon, 1, ',', ' '));
        
        // Simplifier 2πr si possible
        if ($rayon == 1) {
            $formule_exacte = '2π';
            $calcul = '2π × 1 = 2π';
        } else if ($niveau == 1) {
            $formule_exacte = (2 * $rayon == 2) ? '2π' : (2 * $rayon) . 'π';
            $calcul = "2π × $rayon = " . (2 * $rayon) . 'π';
        } else {
            $double_rayon = 2 * $rayon;
            $double_rayon_str = ($double_rayon == floor($double_rayon)) ? (int)$double_rayon : number_format($double_rayon, 1, ',', ' ');
            $formule_exacte = $double_rayon_str . 'π';
            $calcul = '2π × ' . $rayon_str . ' = ' . $double_rayon_str . 'π';
        }
    } else {
        $diametre_str = ($niveau == 1) ? $diametre : (($diametre == floor($diametre)) ? (int)$diametre : number_format($diametre, 1, ',', ' '));
        
        if ($diametre == 1) {
            $formule_exacte = 'π';
            $calcul = 'π × 1 = π';
        } else if ($niveau == 1) {
            $formule_exacte = $diametre . 'π';
            $calcul = "π × $diametre = " . $diametre . 'π';
        } else {
            $formule_exacte = $diametre_str . 'π';
            $calcul = 'π × ' . $diametre_str . ' = ' . $diametre_str . 'π';
        }
    }
    
    $reponse = '<p>Le périmètre du cercle est : <strong>' . $formule_exacte . ' cm</strong></p>';
    $reponse .= '<p style="font-size: 0.9em; color: #666;">Calcul : ' . $calcul . ' cm</p>';
    
    return [
        'type' => 'perimetre',
        'difficulte_id' => $difficulte_id,
        'question' => $question,
        'reponse' => $reponse
    ];
}

?>
