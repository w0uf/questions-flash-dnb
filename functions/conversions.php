<?php
/**
 * Automatisme : Conversions d'unités
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.2)
 * Format : 50% QCM (réponse lettre uniquement) / 50% Réponse directe (format complet)
 */

function generer_conversions() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    if (!isset($_SESSION['conversions_pool']) || empty($_SESSION['conversions_pool'])) {
        $_SESSION['conversions_pool'] = [
            // QCM (10 questions)
            'longueur_m_cm_qcm',        // 3,5 m → cm
            'longueur_cm_m_qcm',        // 850 cm → m
            'aire_m2_cm2_qcm',          // 5 m² → cm²
            'aire_cm2_m2_qcm',          // 3500 cm² → m²
            'volume_m3_dm3_qcm',        // 4 m³ → dm³
            'volume_dm3_m3_qcm',        // 2500 dm³ → m³
            'temps_h_min_qcm',          // 2,5 h → min
            'temps_min_s_qcm',          // 3 min 30 s → s
            'capacite_cl_l_qcm',        // 350 cL → L
            'capacite_dl_ml_qcm',       // 8 dL → mL
            
            // Réponse directe (10 questions)
            'longueur_km_m_direct',     // 5 km → m
            'longueur_cm_m_direct',     // 400 cm → m
            'masse_kg_g_direct',        // 3 kg → g
            'masse_g_kg_direct',        // 7500 g → kg
            'capacite_l_ml_direct',     // 4 L → mL
            'capacite_ml_l_direct',     // 250 mL → L
            'temps_min_s_direct',       // 5 min → s
            'temps_jour_h_direct',      // 3 jours → h
            'corresp_dm3_l_direct',     // 250 dm³ → L
            'corresp_m3_l_direct',      // 6 m³ → L
        ];
        shuffle($_SESSION['conversions_pool']);
    }
    
    $type_question = array_shift($_SESSION['conversions_pool']);
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // QCM - LONGUEURS
        // ============================================
        
        case 'longueur_m_cm_qcm':
            $valeur = 3.5;
            $bonne_reponse = 350;
            $propositions = [
                '35' => '35 cm',
                '350' => '350 cm',
                '3500' => '3500 cm',
                '0.35' => '0,35 cm'
            ];
            $qcm = generer_qcm_conversion($propositions, '350');
            $question_html = '<p>Convertir ' . format_nombre($valeur) . ' mètres en centimètres.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'longueur_cm_m_qcm':
            $valeur = 850;
            $bonne_reponse = 8.5;
            $propositions = [
                '85' => '85 m',
                '8.5' => '8,5 m',
                '0.85' => '0,85 m',
                '8500' => '8500 m'
            ];
            $qcm = generer_qcm_conversion($propositions, '8.5');
            $question_html = '<p>Convertir ' . format_nombre($valeur) . ' centimètres en mètres.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // QCM - AIRES
        // ============================================
        
        case 'aire_m2_cm2_qcm':
            $valeur = 5;
            $bonne_reponse = 50000;
            $propositions = [
                '500' => '500 cm<sup>2</sup>',
                '5000' => '5000 cm<sup>2</sup>',
                '50000' => '50000 cm<sup>2</sup>',
                '500000' => '500000 cm<sup>2</sup>'
            ];
            $qcm = generer_qcm_conversion($propositions, '50000');
            $question_html = '<p>Convertir ' . format_nombre($valeur) . ' m<sup>2</sup> en centimètres carrés.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'aire_cm2_m2_qcm':
            $valeur = 3500;
            $bonne_reponse = 0.35;
            $propositions = [
                '35' => '35 m<sup>2</sup>',
                '3.5' => '3,5 m<sup>2</sup>',
                '0.35' => '0,35 m<sup>2</sup>',
                '0.035' => '0,035 m<sup>2</sup>'
            ];
            $qcm = generer_qcm_conversion($propositions, '0.35');
            $question_html = '<p>Convertir ' . format_nombre($valeur) . ' cm<sup>2</sup> en mètres carrés.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        // ============================================
        // QCM - VOLUMES
        // ============================================
        
        case 'volume_m3_dm3_qcm':
            $valeur = 4;
            $bonne_reponse = 4000;
            $propositions = [
                '40' => '40 dm<sup>3</sup>',
                '400' => '400 dm<sup>3</sup>',
                '4000' => '4000 dm<sup>3</sup>',
                '40000' => '40000 dm<sup>3</sup>'
            ];
            $qcm = generer_qcm_conversion($propositions, '4000');
            $question_html = '<p>Convertir ' . format_nombre($valeur) . ' m<sup>3</sup> en décimètres cubes.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.2;
            break;
            
        case 'volume_dm3_m3_qcm':
            $valeur = 2500;
            $bonne_reponse = 2.5;
            $propositions = [
                '25' => '25 m<sup>3</sup>',
                '2.5' => '2,5 m<sup>3</sup>',
                '0.25' => '0,25 m<sup>3</sup>',
                '250' => '250 m<sup>3</sup>'
            ];
            $qcm = generer_qcm_conversion($propositions, '2.5');
            $question_html = '<p>Convertir ' . format_nombre($valeur) . ' dm<sup>3</sup> en mètres cubes.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // QCM - TEMPS
        // ============================================
        
        case 'temps_h_min_qcm':
            $valeur = 2.5;
            $bonne_reponse = 150;
            $propositions = [
                '125' => '125 min',
                '150' => '150 min',
                '180' => '180 min',
                '250' => '250 min'
            ];
            $qcm = generer_qcm_conversion($propositions, '150');
            $question_html = '<p>Convertir ' . format_nombre($valeur) . ' heures en minutes.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'temps_min_s_qcm':
            $valeur_min = 3;
            $valeur_s = 30;
            $bonne_reponse = 210;
            $propositions = [
                '180' => '180 s',
                '210' => '210 s',
                '240' => '240 s',
                '330' => '330 s'
            ];
            $qcm = generer_qcm_conversion($propositions, '210');
            $question_html = '<p>Convertir ' . $valeur_min . ' minutes ' . $valeur_s . ' secondes en secondes.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        // ============================================
        // QCM - CAPACITÉS
        // ============================================
        
        case 'capacite_cl_l_qcm':
            $valeur = 350;
            $bonne_reponse = 3.5;
            $propositions = [
                '35' => '35 L',
                '3.5' => '3,5 L',
                '0.35' => '0,35 L',
                '350' => '350 L'
            ];
            $qcm = generer_qcm_conversion($propositions, '3.5');
            $question_html = '<p>Convertir ' . format_nombre($valeur) . ' centilitres en litres.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'capacite_dl_ml_qcm':
            $valeur = 8;
            $bonne_reponse = 800;
            $propositions = [
                '8' => '8 mL',
                '80' => '80 mL',
                '800' => '800 mL',
                '8000' => '8000 mL'
            ];
            $qcm = generer_qcm_conversion($propositions, '800');
            $question_html = '<p>Convertir ' . format_nombre($valeur) . ' décilitres en millilitres.</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // RÉPONSE DIRECTE - LONGUEURS
        // ============================================
        
        case 'longueur_km_m_direct':
            $question_html = '<p>Convertir 5 kilomètres en mètres.</p>';
            $reponse_html = '<p><strong>5 km = 5000 m</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'longueur_cm_m_direct':
            $question_html = '<p>Convertir 400 centimètres en mètres.</p>';
            $reponse_html = '<p><strong>400 cm = 4 m</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // RÉPONSE DIRECTE - MASSES
        // ============================================
        
        case 'masse_kg_g_direct':
            $question_html = '<p>Convertir 3 kilogrammes en grammes.</p>';
            $reponse_html = '<p><strong>3 kg = 3000 g</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'masse_g_kg_direct':
            $question_html = '<p>Convertir 7500 grammes en kilogrammes.</p>';
            $reponse_html = '<p><strong>7500 g = 7,5 kg</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // RÉPONSE DIRECTE - CAPACITÉS
        // ============================================
        
        case 'capacite_l_ml_direct':
            $question_html = '<p>Convertir 4 litres en millilitres.</p>';
            $reponse_html = '<p><strong>4 L = 4000 mL</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'capacite_ml_l_direct':
            $question_html = '<p>Convertir 250 millilitres en litres.</p>';
            $reponse_html = '<p><strong>250 mL = 0,25 L</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // RÉPONSE DIRECTE - TEMPS
        // ============================================
        
        case 'temps_min_s_direct':
            $question_html = '<p>Convertir 5 minutes en secondes.</p>';
            $reponse_html = '<p><strong>5 min = 300 s</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'temps_jour_h_direct':
            $question_html = '<p>Convertir 3 jours en heures.</p>';
            $reponse_html = '<p><strong>3 jours = 72 h</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // RÉPONSE DIRECTE - CORRESPONDANCES
        // ============================================
        
        case 'corresp_dm3_l_direct':
            $question_html = '<p>Convertir 250 décimètres cubes en litres.</p>';
            $reponse_html = '<p><strong>250 dm<sup>3</sup> = 250 L</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'corresp_m3_l_direct':
            $question_html = '<p>Convertir 6 mètres cubes en litres.</p>';
            $reponse_html = '<p><strong>6 m<sup>3</sup> = 6000 L</strong></p>';
            $difficulte = 1.0;
            break;
    }
    
    return [
        'type' => 'conversions',
        'question' => $question_html,
        'reponse' => $reponse_html,
        'difficulte' => $difficulte
    ];
}

// ============================================
// FONCTION HELPER POUR FORMATER LES NOMBRES
// ============================================

function format_nombre($nombre) {
    // Remplacer le point par une virgule pour l'affichage français
    return str_replace('.', ',', $nombre);
}

// ============================================
// FONCTION QCM ADAPTATIVE (UNE LIGNE OU GRILLE 2x2)
// ============================================

function generer_qcm_conversion($propositions_data, $bonne_reponse_key) {
    // Mélanger les propositions
    $propositions_array = [];
    foreach ($propositions_data as $key => $texte) {
        $propositions_array[] = ['key' => $key, 'texte' => $texte];
    }
    shuffle($propositions_array);
    
    // Calculer la longueur totale (approximation sans HTML)
    $longueur_totale = 0;
    foreach ($propositions_array as $prop) {
        // Retirer les balises HTML pour le calcul
        $texte_brut = strip_tags($prop['texte']);
        $longueur_totale += strlen($texte_brut) + 5; // +5 pour "A. " et espaces
    }
    
    // Décider du format (seuil à 80 caractères)
    $une_ligne = ($longueur_totale < 80);
    
    $lettres = ['A', 'B', 'C', 'D'];
    $bonne_lettre = '';
    
    if ($une_ligne) {
        // FORMAT UNE LIGNE
        $qcm_html = '<p>';
        foreach ($propositions_array as $index => $prop) {
            $lettre = $lettres[$index];
            if ($prop['key'] == $bonne_reponse_key) {
                $bonne_lettre = $lettre;
            }
            $qcm_html .= '<span style="padding-right: 30px; font-size: 95%;"><strong>' . $lettre . '.</strong> ' . $prop['texte'] . '</span>';
        }
        $qcm_html .= '</p>';
    } else {
        // FORMAT DEUX LIGNES (GRILLE 2x2)
        $qcm_html = '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; max-width: 600px;">';
        foreach ($propositions_array as $index => $prop) {
            $lettre = $lettres[$index];
            if ($prop['key'] == $bonne_reponse_key) {
                $bonne_lettre = $lettre;
            }
            $qcm_html .= '<div style="font-size: 95%;"><strong>' . $lettre . '.</strong> ' . $prop['texte'] . '</div>';
        }
        $qcm_html .= '</div>';
    }
    
    return ['html' => $qcm_html, 'bonne_lettre' => $bonne_lettre];
}
?>
