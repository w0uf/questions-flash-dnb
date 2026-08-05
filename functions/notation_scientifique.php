<?php
/**
 * Automatisme : Passer d'une écriture décimale à la notation scientifique
 * Difficulté : FACILE à MOYEN (range 1.0 - 2.0)
 * Format : Réponse directe
 */

/**
 * Affiche une mantisse à la française, sans décimale inutile.
 * Les grands nombres passaient par number_format(…, 1) et donnaient « 5,0 × 10⁶ »
 * là où les petits donnaient « 6 × 10⁻⁴ » : même page, deux conventions.
 */
function ns_mantisse($m) {
    if (abs($m - round($m)) < 1e-9) {
        return (string) round($m);
    }
    return str_replace('.', ',', number_format($m, 1, '.', ''));
}

function generer_notation_scientifique() {
    // ========================================
    // CHARGEMENT DES DIFFICULTÉS DEPUIS JSON
    // ========================================
    
    static $difficultes_config = null;
    if ($difficultes_config === null) {
        $json_path = __DIR__ . '/difficultes.json';
        if (file_exists($json_path)) {
            $json_content = file_get_contents($json_path);
            $config = json_decode($json_content, true);
            $difficultes_config = $config['automatismes']['notation_scientifique']['types'] ?? [];
        } else {
            // Fallback si le JSON n'existe pas
            $difficultes_config = [];
        }
    }
    
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['notation_scientifique_pool']) || empty($_SESSION['notation_scientifique_pool'])) {
        $_SESSION['notation_scientifique_pool'] = [
            'decimal_vers_scientifique_grand_1',    // Ex: 4500
            'decimal_vers_scientifique_grand_2',    // Ex: 85000
            'decimal_vers_scientifique_grand_3',    // Ex: 3200000
            'decimal_vers_scientifique_petit_1',    // Ex: 0,025
            'decimal_vers_scientifique_petit_2',    // Ex: 0,00034
            'decimal_vers_scientifique_petit_3',    // Ex: 0,000007
            'scientifique_vers_decimal_grand_1',    // Ex: 3,2 × 10³
            'scientifique_vers_decimal_grand_2',    // Ex: 5,6 × 10⁵
            'scientifique_vers_decimal_petit_1',    // Ex: 4,5 × 10⁻²
            'scientifique_vers_decimal_petit_2',    // Ex: 7,8 × 10⁻⁴
            'decimal_vers_scientifique_grand_4',    // Ex: 120000
            'decimal_vers_scientifique_petit_4'     // Ex: 0,0056
        ];
        shuffle($_SESSION['notation_scientifique_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['notation_scientifique_pool']);
    
    // ========================================
    // GÉNÉRER LA QUESTION SELON LE TYPE
    // ========================================
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // DÉCIMAL VERS SCIENTIFIQUE - GRANDS NOMBRES
        // ============================================
        
        case 'decimal_vers_scientifique_grand_1':
            // Nombres de l'ordre de 10³ (milliers)
            $mantisse = rand(10, 99) / 10; // 1.0 à 9.9
            $exposant = 3;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . number_format($nombre_decimal, 0, ',', ' ') . ' en notation scientifique.</p>';
            $reponse_html = '<p><strong>' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup></strong></p>';
            $difficulte = $difficultes_config['decimal_vers_scientifique_grand_1']['difficulte'] ?? 1.2;
            break;
            
        case 'decimal_vers_scientifique_grand_2':
            // Nombres de l'ordre de 10⁴ (dizaines de milliers)
            $mantisse = rand(10, 99) / 10;
            $exposant = 4;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . number_format($nombre_decimal, 0, ',', ' ') . ' en notation scientifique.</p>';
            $reponse_html = '<p><strong>' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup></strong></p>';
            $difficulte = $difficultes_config['decimal_vers_scientifique_grand_2']['difficulte'] ?? 1.4;
            break;
            
        case 'decimal_vers_scientifique_grand_3':
            // Nombres de l'ordre de 10⁶ (millions)
            $mantisse = rand(10, 99) / 10;
            $exposant = 6;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . number_format($nombre_decimal, 0, ',', ' ') . ' en notation scientifique.</p>';
            $reponse_html = '<p><strong>' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup></strong></p>';
            $difficulte = $difficultes_config['decimal_vers_scientifique_grand_3']['difficulte'] ?? 1.6;
            break;
            
        case 'decimal_vers_scientifique_grand_4':
            // Nombres de l'ordre de 10⁵ (centaines de milliers)
            $mantisse = rand(10, 99) / 10;
            $exposant = 5;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . number_format($nombre_decimal, 0, ',', ' ') . ' en notation scientifique.</p>';
            $reponse_html = '<p><strong>' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup></strong></p>';
            $difficulte = $difficultes_config['decimal_vers_scientifique_grand_4']['difficulte'] ?? 1.5;
            break;
            
        // ============================================
        // DÉCIMAL VERS SCIENTIFIQUE - PETITS NOMBRES
        // ============================================
        
        case 'decimal_vers_scientifique_petit_1':
            // Nombres de l'ordre de 10⁻² (centièmes)
            $mantisse = rand(10, 99) / 10;
            $exposant = -2;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . str_replace('.', ',', rtrim(rtrim(sprintf('%.8f', $nombre_decimal), '0'), '.')) . ' en notation scientifique.</p>';
            $reponse_html = '<p><strong>' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup></strong></p>';
            $difficulte = $difficultes_config['decimal_vers_scientifique_petit_1']['difficulte'] ?? 1.3;
            break;
            
        case 'decimal_vers_scientifique_petit_2':
            // Nombres de l'ordre de 10⁻⁴ (dix-millièmes)
            $mantisse = rand(10, 99) / 10;
            $exposant = -4;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . str_replace('.', ',', rtrim(rtrim(sprintf('%.8f', $nombre_decimal), '0'), '.')) . ' en notation scientifique.</p>';
            $reponse_html = '<p><strong>' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup></strong></p>';
            $difficulte = $difficultes_config['decimal_vers_scientifique_petit_2']['difficulte'] ?? 1.5;
            break;
            
        case 'decimal_vers_scientifique_petit_3':
            // Nombres de l'ordre de 10⁻⁶ (millionièmes)
            $mantisse = rand(10, 99) / 10;
            $exposant = -6;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . str_replace('.', ',', rtrim(rtrim(sprintf('%.8f', $nombre_decimal), '0'), '.')) . ' en notation scientifique.</p>';
            $reponse_html = '<p><strong>' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup></strong></p>';
            $difficulte = $difficultes_config['decimal_vers_scientifique_petit_3']['difficulte'] ?? 1.8;
            break;
            
        case 'decimal_vers_scientifique_petit_4':
            // Nombres de l'ordre de 10⁻³ (millièmes)
            $mantisse = rand(10, 99) / 10;
            $exposant = -3;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . str_replace('.', ',', rtrim(rtrim(sprintf('%.8f', $nombre_decimal), '0'), '.')) . ' en notation scientifique.</p>';
            $reponse_html = '<p><strong>' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup></strong></p>';
            $difficulte = $difficultes_config['decimal_vers_scientifique_petit_4']['difficulte'] ?? 1.4;
            break;
            
        // ============================================
        // SCIENTIFIQUE VERS DÉCIMAL - GRANDS NOMBRES
        // ============================================
        
        case 'scientifique_vers_decimal_grand_1':
            // 10³
            $mantisse = rand(10, 99) / 10;
            $exposant = 3;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup> en écriture décimale.</p>';
            $reponse_html = '<p><strong>' . number_format($nombre_decimal, 0, ',', ' ') . '</strong></p>';
            $difficulte = $difficultes_config['scientifique_vers_decimal_grand_1']['difficulte'] ?? 1.1;
            break;
            
        case 'scientifique_vers_decimal_grand_2':
            // 10⁵
            $mantisse = rand(10, 99) / 10;
            $exposant = 5;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup> en écriture décimale.</p>';
            $reponse_html = '<p><strong>' . number_format($nombre_decimal, 0, ',', ' ') . '</strong></p>';
            $difficulte = $difficultes_config['scientifique_vers_decimal_grand_2']['difficulte'] ?? 1.5;
            break;
            
        // ============================================
        // SCIENTIFIQUE VERS DÉCIMAL - PETITS NOMBRES
        // ============================================
        
        case 'scientifique_vers_decimal_petit_1':
            // 10⁻²
            $mantisse = rand(10, 99) / 10;
            $exposant = -2;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup> en écriture décimale.</p>';
            $reponse_html = '<p><strong>' . str_replace('.', ',', rtrim(rtrim(sprintf('%.8f', $nombre_decimal), '0'), '.')) . '</strong></p>';
            $difficulte = $difficultes_config['scientifique_vers_decimal_petit_1']['difficulte'] ?? 1.2;
            break;
            
        case 'scientifique_vers_decimal_petit_2':
            // 10⁻⁴
            $mantisse = rand(10, 99) / 10;
            $exposant = -4;
            $nombre_decimal = $mantisse * pow(10, $exposant);
            
            $question_html = '<p>Écrire ' . ns_mantisse($mantisse) . ' × 10<sup>' . $exposant . '</sup> en écriture décimale.</p>';
            $reponse_html = '<p><strong>' . str_replace('.', ',', rtrim(rtrim(sprintf('%.8f', $nombre_decimal), '0'), '.')) . '</strong></p>';
            $difficulte = $difficultes_config['scientifique_vers_decimal_petit_2']['difficulte'] ?? 1.6;
            break;
    }
    
    return [
        'type' => 'notation_scientifique',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}
