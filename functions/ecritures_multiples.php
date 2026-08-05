<?php
/**
 * Automatisme : Écritures multiples d'un nombre
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.8)
 * Format : QCM avec 4 propositions (A, B, C, D)
 */

require_once(__DIR__ . '/utils.php');

function generer_ecritures_multiples() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['ecritures_multiples_pool']) || empty($_SESSION['ecritures_multiples_pool'])) {
        $_SESSION['ecritures_multiples_pool'] = [
            'nombre_0_5',      // 0,5 = 1/2 = 50%
            'nombre_0_25',     // 0,25 = 1/4 = 25%
            'nombre_0_75',     // 0,75 = 3/4 = 75%
            'nombre_1_2',      // 1,2 = 6/5 = 120%
            'nombre_1_5',      // 1,5 = 3/2 = 150%
            'nombre_0_2',      // 0,2 = 1/5 = 20%
            'nombre_0_4',      // 0,4 = 2/5 = 40%
            'nombre_0_6',      // 0,6 = 3/5 = 60%
            'nombre_0_8',      // 0,8 = 4/5 = 80%
            'nombre_2_5',      // 2,5 = 5/2 = 250%
            'nombre_1_25',     // 1,25 = 5/4 = 125%
            'nombre_0_1'       // 0,1 = 1/10 = 10%
        ];
        shuffle($_SESSION['ecritures_multiples_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['ecritures_multiples_pool']);
    
    // ========================================
    // DÉFINITION DES NOMBRES ET LEURS ÉCRITURES
    // ========================================
    
    $nombres = [
        'nombre_0_5' => [
            'decimal' => '0,5',
            'fraction_simple' => frac_html(1, 2),
            'fraction_decimale' => frac_html(5, 10),
            'pourcentage' => '50 %',
            'somme' => null,
            'difficulte' => 1.0
        ],
        'nombre_0_25' => [
            'decimal' => '0,25',
            'fraction_simple' => frac_html(1, 4),
            'fraction_decimale' => frac_html(25, 100),
            'pourcentage' => '25 %',
            'somme' => null,
            'difficulte' => 1.1
        ],
        'nombre_0_75' => [
            'decimal' => '0,75',
            'fraction_simple' => frac_html(3, 4),
            'fraction_decimale' => frac_html(75, 100),
            'pourcentage' => '75 %',
            'somme' => null,
            'difficulte' => 1.2
        ],
        'nombre_1_2' => [
            'decimal' => '1,2',
            'fraction_simple' => frac_html(6, 5),
            'fraction_decimale' => frac_html(12, 10),
            'pourcentage' => '120 %',
            'somme' => '1 + ' . frac_html(1, 5),
            'difficulte' => 1.4
        ],
        'nombre_1_5' => [
            'decimal' => '1,5',
            'fraction_simple' => frac_html(3, 2),
            'fraction_decimale' => frac_html(15, 10),
            'pourcentage' => '150 %',
            'somme' => '1 + ' . frac_html(1, 2),
            'difficulte' => 1.3
        ],
        'nombre_0_2' => [
            'decimal' => '0,2',
            'fraction_simple' => frac_html(1, 5),
            'fraction_decimale' => frac_html(2, 10),
            'pourcentage' => '20 %',
            'somme' => null,
            'difficulte' => 1.1
        ],
        'nombre_0_4' => [
            'decimal' => '0,4',
            'fraction_simple' => frac_html(2, 5),
            'fraction_decimale' => frac_html(4, 10),
            'pourcentage' => '40 %',
            'somme' => null,
            'difficulte' => 1.2
        ],
        'nombre_0_6' => [
            'decimal' => '0,6',
            'fraction_simple' => frac_html(3, 5),
            'fraction_decimale' => frac_html(6, 10),
            'pourcentage' => '60 %',
            'somme' => null,
            'difficulte' => 1.2
        ],
        'nombre_0_8' => [
            'decimal' => '0,8',
            'fraction_simple' => frac_html(4, 5),
            'fraction_decimale' => frac_html(8, 10),
            'pourcentage' => '80 %',
            'somme' => null,
            'difficulte' => 1.3
        ],
        'nombre_2_5' => [
            'decimal' => '2,5',
            'fraction_simple' => frac_html(5, 2),
            'fraction_decimale' => frac_html(25, 10),
            'pourcentage' => '250 %',
            'somme' => '2 + ' . frac_html(1, 2),
            'difficulte' => 1.6
        ],
        'nombre_1_25' => [
            'decimal' => '1,25',
            'fraction_simple' => frac_html(5, 4),
            'fraction_decimale' => frac_html(125, 100),
            'pourcentage' => '125 %',
            'somme' => '1 + ' . frac_html(1, 4),
            'difficulte' => 1.5
        ],
        'nombre_0_1' => [
            'decimal' => '0,1',
            'fraction_simple' => frac_html(1, 10),
            'fraction_decimale' => frac_html(10, 100),
            'pourcentage' => '10 %',
            'somme' => null,
            'difficulte' => 1.0
        ]
    ];
    
    // Récupérer le nombre sélectionné
    $nombre = $nombres[$type_question];
    
    // ========================================
    // CONSTRUIRE LE QCM
    // ========================================
    
    // Collecter toutes les écritures possibles pour ce nombre
    $ecritures_possibles = [];
    if ($nombre['decimal']) $ecritures_possibles[] = ['type' => 'decimal', 'valeur' => $nombre['decimal']];
    if ($nombre['fraction_simple']) $ecritures_possibles[] = ['type' => 'fraction_simple', 'valeur' => $nombre['fraction_simple']];
    if ($nombre['fraction_decimale']) $ecritures_possibles[] = ['type' => 'fraction_decimale', 'valeur' => $nombre['fraction_decimale']];
    if ($nombre['pourcentage']) $ecritures_possibles[] = ['type' => 'pourcentage', 'valeur' => $nombre['pourcentage']];
    if ($nombre['somme']) $ecritures_possibles[] = ['type' => 'somme', 'valeur' => $nombre['somme']];
    
    // Choisir l'écriture de départ (celle de la question)
    $index_depart = rand(0, count($ecritures_possibles) - 1);
    $ecriture_depart = $ecritures_possibles[$index_depart];
    
    // Retirer l'écriture de départ des possibilités
    unset($ecritures_possibles[$index_depart]);
    $ecritures_possibles = array_values($ecritures_possibles);
    
    // Choisir l'écriture correcte (la bonne réponse) parmi les restantes
    $index_correct = rand(0, count($ecritures_possibles) - 1);
    $ecriture_correcte = $ecritures_possibles[$index_correct];
    
    // Retirer l'écriture correcte
    unset($ecritures_possibles[$index_correct]);
    $ecritures_possibles = array_values($ecritures_possibles);
    
    // ========================================
    // GÉNÉRER LES DISTRACTEURS (mauvaises réponses)
    // ========================================
    
    $distracteurs = [];
    
    // NE PAS prendre d'autres écritures du même nombre (sinon plusieurs bonnes réponses)
    // On prend uniquement des écritures d'autres nombres
    
    // Compléter avec des écritures d'autres nombres
    $tous_les_nombres = array_keys($nombres);
    shuffle($tous_les_nombres);
    
    foreach ($tous_les_nombres as $autre_nombre_key) {
        if (count($distracteurs) >= 3) break;
        if ($autre_nombre_key === $type_question) continue; // Pas le même nombre
        
        $autre_nombre = $nombres[$autre_nombre_key];
        
        // Choisir une écriture aléatoire de cet autre nombre
        $ecritures_autres = [];
        if ($autre_nombre['decimal']) $ecritures_autres[] = ['type' => 'decimal', 'valeur' => $autre_nombre['decimal']];
        if ($autre_nombre['fraction_simple']) $ecritures_autres[] = ['type' => 'fraction_simple', 'valeur' => $autre_nombre['fraction_simple']];
        if ($autre_nombre['fraction_decimale']) $ecritures_autres[] = ['type' => 'fraction_decimale', 'valeur' => $autre_nombre['fraction_decimale']];
        if ($autre_nombre['pourcentage']) $ecritures_autres[] = ['type' => 'pourcentage', 'valeur' => $autre_nombre['pourcentage']];
        
        if (count($ecritures_autres) > 0) {
            $distracteurs[] = $ecritures_autres[array_rand($ecritures_autres)];
        }
    }
    
    // Mélanger les 4 propositions (1 correcte + 3 distracteurs)
    $propositions = array_merge([$ecriture_correcte], $distracteurs);
    shuffle($propositions);
    
    // Trouver la position de la bonne réponse
    $lettres = ['A', 'B', 'C', 'D'];
    $lettre_correcte = '';
    
    foreach ($propositions as $index => $prop) {
        if ($prop === $ecriture_correcte) {
            $lettre_correcte = $lettres[$index];
            break;
        }
    }
    
    // ========================================
    // CONSTRUIRE LA QUESTION ET LA RÉPONSE
    // ========================================
    
    $question_html = '<p>Quelle est une autre écriture de ' . $ecriture_depart['valeur'] . ' ?</p>';
    $question_html .= '<p>';
    foreach ($propositions as $index => $prop) {
        $question_html .= '<span style="padding-right: 20px;"><strong>' . $lettres[$index] . '.</strong> ' . $prop['valeur'] . '</span>';
    }
    $question_html .= '</p>';
    
    return [
        'type' => 'ecritures_multiples',
        'difficulte_id' => $nombre['difficulte'],
        'question' => $question_html,
        'reponse' => '<p><strong>' . $lettre_correcte . '</strong></p>'
    ];
}
