<?php
/**
 * Automatisme : Reconnaître des solides (cube, pavé droit, prisme droit, cylindre, pyramide, cône)
 * Difficulté : FACILE (range 1.0 - 1.2)
 * Format : QCM 4 propositions (A, B, C, D)
 * Conforme aux attendus officiels DNB 2026 : "Reconnaître des solides"
 */

function generer_solides() {
    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    if (!isset($_SESSION['solides_pool']) || empty($_SESSION['solides_pool'])) {
        $_SESSION['solides_pool'] = [
            // RECONNAISSANCE VISUELLE (8 questions - sans doublons)
            'visuel_cube',
            'visuel_pave',
            'visuel_prisme_triangulaire',
            'visuel_cylindre',
            'visuel_pyramide_carree',
            'visuel_cone',
            'visuel_pyramide_triangulaire',
            'visuel_prisme_pentagonal',
            
            // DESCRIPTION → NOM (5 questions)
            'description_cube',
            'description_cylindre',
            'description_cone',
            'description_pyramide',
            'description_prisme',
        ];
        shuffle($_SESSION['solides_pool']);
    }
    
    $type_question = array_shift($_SESSION['solides_pool']);
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    
    switch ($type_question) {
        // ============================================
        // RECONNAISSANCE VISUELLE - CUBE
        // ============================================
        
        case 'visuel_cube':
            $svg = generer_svg_cube();
            $propositions = [
                'cube' => 'un cube',
                'cylindre' => 'un cylindre',
                'pyramide' => 'une pyramide',
                'cone' => 'un cône'
            ];
            $qcm = generer_qcm_solides($propositions, 'cube');
            $question_html = '<p>Ce solide est :</p>' . $svg . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'visuel_pave':
            $svg = generer_svg_pave();
            $propositions = [
                'pave' => 'un pavé droit',
                'cylindre' => 'un cylindre',
                'pyramide' => 'une pyramide',
                'cone' => 'un cône'
            ];
            $qcm = generer_qcm_solides($propositions, 'pave');
            $question_html = '<p>Ce solide est :</p>' . $svg . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'visuel_prisme_triangulaire':
            $svg = generer_svg_prisme_triangulaire();
            $propositions = [
                'prisme' => 'un prisme droit',
                'pyramide' => 'une pyramide',
                'cylindre' => 'un cylindre',
                'cone' => 'un cône'
            ];
            $qcm = generer_qcm_solides($propositions, 'prisme');
            $question_html = '<p>Ce solide est :</p>' . $svg . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'visuel_cylindre':
            $svg = generer_svg_cylindre();
            $propositions = [
                'cylindre' => 'un cylindre',
                'cone' => 'un cône',
                'pyramide' => 'une pyramide',
                'cube' => 'un cube'
            ];
            $qcm = generer_qcm_solides($propositions, 'cylindre');
            $question_html = '<p>Ce solide est :</p>' . $svg . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'visuel_pyramide_carree':
            $svg = generer_svg_pyramide_carree();
            $propositions = [
                'pyramide' => 'une pyramide',
                'cone' => 'un cône',
                'cylindre' => 'un cylindre',
                'cube' => 'un cube'
            ];
            $qcm = generer_qcm_solides($propositions, 'pyramide');
            $question_html = '<p>Ce solide est :</p>' . $svg . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'visuel_cone':
            $svg = generer_svg_cone();
            $propositions = [
                'cone' => 'un cône',
                'cylindre' => 'un cylindre',
                'pyramide' => 'une pyramide',
                'prisme' => 'un prisme droit'
            ];
            $qcm = generer_qcm_solides($propositions, 'cone');
            $question_html = '<p>Ce solide est :</p>' . $svg . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'visuel_pyramide_triangulaire':
            $svg = generer_svg_pyramide_triangulaire();
            $propositions = [
                'pyramide' => 'une pyramide',
                'prisme' => 'un prisme droit',
                'cone' => 'un cône',
                'cylindre' => 'un cylindre'
            ];
            $qcm = generer_qcm_solides($propositions, 'pyramide');
            $question_html = '<p>Ce solide est :</p>' . $svg . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'visuel_prisme_pentagonal':
            $svg = generer_svg_prisme_pentagonal();
            $propositions = [
                'prisme' => 'un prisme droit',
                'pyramide' => 'une pyramide',
                'cylindre' => 'un cylindre',
                'cone' => 'un cône'
            ];
            $qcm = generer_qcm_solides($propositions, 'prisme');
            $question_html = '<p>Ce solide est :</p>' . $svg . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // DESCRIPTION → NOM
        // ============================================
        
        case 'description_cube':
            $propositions = [
                'cube' => 'un cube',
                'cylindre' => 'un cylindre',
                'pyramide' => 'une pyramide',
                'cone' => 'un cône'
            ];
            $qcm = generer_qcm_solides($propositions, 'cube');
            $question_html = '<p>Ce solide possède 6 faces carrées identiques.</p><p>Ce solide est :</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'description_cylindre':
            $propositions = [
                'cylindre' => 'un cylindre',
                'cone' => 'un cône',
                'pyramide' => 'une pyramide',
                'cube' => 'un cube'
            ];
            $qcm = generer_qcm_solides($propositions, 'cylindre');
            $question_html = '<p>Ce solide possède deux bases circulaires et une face latérale courbe.</p><p>Ce solide est :</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'description_cone':
            $propositions = [
                'cone' => 'un cône',
                'cylindre' => 'un cylindre',
                'pyramide' => 'une pyramide',
                'prisme' => 'un prisme droit'
            ];
            $qcm = generer_qcm_solides($propositions, 'cone');
            $question_html = '<p>Ce solide possède une base circulaire et un sommet.</p><p>Ce solide est :</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'description_pyramide':
            $propositions = [
                'pyramide' => 'une pyramide',
                'prisme' => 'un prisme droit',
                'cone' => 'un cône',
                'cylindre' => 'un cylindre'
            ];
            $qcm = generer_qcm_solides($propositions, 'pyramide');
            $question_html = '<p>Ce solide possède une base polygonale et des faces triangulaires qui se rejoignent en un sommet.</p><p>Ce solide est :</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.1;
            break;
            
        case 'description_prisme':
            $propositions = [
                'prisme' => 'un prisme droit',
                'pyramide' => 'une pyramide',
                'cylindre' => 'un cylindre',
                'cone' => 'un cône'
            ];
            $qcm = generer_qcm_solides($propositions, 'prisme');
            $question_html = '<p>Ce solide possède deux bases polygonales identiques et des faces latérales rectangulaires.</p><p>Ce solide est :</p>' . $qcm['html'];
            $reponse_html = '<p><strong>' . $qcm['bonne_lettre'] . '</strong></p>';
            $difficulte = 1.1;
            break;
    }
    
    return [
        'type' => 'solides',
        'question' => $question_html,
        'reponse' => $reponse_html,
        'difficulte' => $difficulte
    ];
}

// ============================================
// FONCTIONS SVG - GÉNÉRATION DES SOLIDES
// ============================================

function generer_svg_cube() {
    $svg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // 1. Faces (SANS stroke)
    $svg .= '<rect x="60" y="80" width="80" height="80" fill="#e0e0e0" stroke="none"/>';
    $svg .= '<path d="M 140 80 L 180 60 L 180 140 L 140 160 Z" fill="#b0b0b0" stroke="none"/>';
    $svg .= '<path d="M 60 80 L 100 60 L 180 60 L 140 80 Z" fill="#d0d0d0" stroke="none"/>';
    
    // 2. Arêtes CACHÉES (pointillés)
    $svg .= '<line x1="100" y1="60" x2="100" y2="140" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    $svg .= '<line x1="60" y1="160" x2="100" y2="140" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    $svg .= '<line x1="100" y1="140" x2="180" y2="140" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    
    // 3. Arêtes VISIBLES (pleines)
    $svg .= '<rect x="60" y="80" width="80" height="80" fill="none" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="140" y1="80" x2="180" y2="60" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="180" y1="60" x2="180" y2="140" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="180" y1="140" x2="140" y2="160" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="60" y1="80" x2="100" y2="60" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="100" y1="60" x2="180" y2="60" stroke="#000" stroke-width="2"/>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_pave() {
    $svg = '<svg width="220" height="180" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // Faces
    $svg .= '<rect x="40" y="80" width="110" height="70" fill="#e0e0e0" stroke="none"/>';
    $svg .= '<path d="M 150 80 L 190 60 L 190 130 L 150 150 Z" fill="#b0b0b0" stroke="none"/>';
    $svg .= '<path d="M 40 80 L 80 60 L 190 60 L 150 80 Z" fill="#d0d0d0" stroke="none"/>';
    
    // Arêtes cachées
    $svg .= '<line x1="80" y1="60" x2="80" y2="130" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    $svg .= '<line x1="40" y1="150" x2="80" y2="130" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    $svg .= '<line x1="80" y1="130" x2="190" y2="130" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    
    // Arêtes visibles
    $svg .= '<rect x="40" y="80" width="110" height="70" fill="none" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="150" y1="80" x2="190" y2="60" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="190" y1="60" x2="190" y2="130" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="190" y1="130" x2="150" y2="150" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="40" y1="80" x2="80" y2="60" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="80" y1="60" x2="190" y2="60" stroke="#000" stroke-width="2"/>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_prisme_triangulaire() {
    $svg = '<svg width="220" height="180" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // Faces visibles
    $svg .= '<path d="M 60 140 L 100 100 L 140 0 L 100 40 Z" fill="#c8c8c8" stroke="none"/>';
    $svg .= '<path d="M 140 140 L 100 100 L 140 0 L 180 40 Z" fill="#b8b8b8" stroke="none"/>';
    $svg .= '<path d="M 60 140 L 140 140 L 100 100 Z" fill="#e0e0e0" stroke="none"/>';
    
    // 1 SEULE arête cachée (pointillés) : bas du triangle arrière
    $svg .= '<line x1="100" y1="40" x2="180" y2="40" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    
    // Arêtes visibles (pleines)
    // Triangle avant
    $svg .= '<line x1="60" y1="140" x2="140" y2="140" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="140" y1="140" x2="100" y2="100" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="100" y1="100" x2="60" y2="140" stroke="#000" stroke-width="2"/>';
    
    // Verticales
    $svg .= '<line x1="60" y1="140" x2="100" y2="40" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="140" y1="140" x2="180" y2="40" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="100" y1="100" x2="140" y2="0" stroke="#000" stroke-width="2"/>';
    
    // Triangle arrière (2 arêtes)
    $svg .= '<line x1="100" y1="40" x2="140" y2="0" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="180" y1="40" x2="140" y2="0" stroke="#000" stroke-width="2"/>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_cylindre() {
    $svg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // 1. Corps
    $svg .= '<rect x="50" y="50" width="100" height="110" fill="#e8e8e8" stroke="none"/>';
    
    // 2. Arêtes latérales
    $svg .= '<line x1="50" y1="50" x2="50" y2="160" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="150" y1="50" x2="150" y2="160" stroke="#000" stroke-width="2"/>';
    
    // 3. Base supérieure
    $svg .= '<ellipse cx="100" cy="50" rx="50" ry="15" fill="#d5d5d5" stroke="#000" stroke-width="2"/>';
    
    // 4. Base inférieure : remplissage
    $svg .= '<ellipse cx="100" cy="160" rx="50" ry="15" fill="#b8b8b8" stroke="none"/>';
    
    // 5. Arc arrière (pointillés) - par-dessus le remplissage
    $svg .= '<path d="M 50 160 A 50 15 0 0 1 150 160" fill="none" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    
    // 6. Arc avant (trait plein) - EN DERNIER
    $svg .= '<path d="M 50 160 A 50 15 0 0 0 150 160" fill="none" stroke="#000" stroke-width="2"/>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_pyramide_carree() {
    $svg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // Faces
    $svg .= '<path d="M 50 170 L 150 170 L 170 150 L 70 150 Z" fill="#d0d0d0" stroke="none"/>';
    $svg .= '<path d="M 50 170 L 100 40 L 150 170 Z" fill="#e0e0e0" stroke="none"/>';
    $svg .= '<path d="M 150 170 L 100 40 L 170 150 Z" fill="#b0b0b0" stroke="none"/>';
    
    // 3 arêtes CACHÉES (pointillés)
    $svg .= '<line x1="50" y1="170" x2="70" y2="150" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    $svg .= '<line x1="70" y1="150" x2="170" y2="150" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    $svg .= '<line x1="70" y1="150" x2="100" y2="40" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    
    // Arêtes visibles
    $svg .= '<line x1="50" y1="170" x2="150" y2="170" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="150" y1="170" x2="170" y2="150" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="50" y1="170" x2="100" y2="40" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="150" y1="170" x2="100" y2="40" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="170" y1="150" x2="100" y2="40" stroke="#000" stroke-width="2"/>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_cone() {
    $svg = '<svg width="200" height="220" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // 1. Base : remplissage
    $svg .= '<ellipse cx="100" cy="180" rx="60" ry="18" fill="#b8b8b8" stroke="none"/>';
    
    // 2. Arc arrière (pointillés)
    $svg .= '<path d="M 40 180 A 60 18 0 0 1 160 180" fill="none" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    
    // 3. Génératrices (lignes du sommet à la base)
    $svg .= '<line x1="40" y1="180" x2="100" y2="40" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="160" y1="180" x2="100" y2="40" stroke="#000" stroke-width="2"/>';
    
    // 4. Arc avant (trait plein)
    $svg .= '<path d="M 40 180 A 60 18 0 0 0 160 180" fill="none" stroke="#000" stroke-width="2"/>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_pyramide_triangulaire() {
    $svg = '<svg width="200" height="180" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // Base triangulaire
    $svg .= '<path d="M 60 150 L 140 150 L 100 120 Z" fill="#d0d0d0" stroke="none"/>';
    
    // Face AVANT visible
    $svg .= '<path d="M 60 150 L 100 40 L 140 150 Z" fill="#e0e0e0" stroke="none"/>';
    
    // 3 arêtes CACHÉES (pointillés) qui se rejoignent au coin arrière
    $svg .= '<line x1="60" y1="150" x2="100" y2="120" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    $svg .= '<line x1="100" y1="120" x2="140" y2="150" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    $svg .= '<line x1="100" y1="120" x2="100" y2="40" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    
    // Arêtes visibles
    $svg .= '<line x1="60" y1="150" x2="140" y2="150" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="60" y1="150" x2="100" y2="40" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="140" y1="150" x2="100" y2="40" stroke="#000" stroke-width="2"/>';
    
    $svg .= '</svg>';
    return $svg;
}

function generer_svg_prisme_pentagonal() {
    $svg = '<svg width="260" height="200" xmlns="http://www.w3.org/2000/svg" style="margin: 15px auto; display: block;">';
    
    // FACE AVANT (pentagone grisé)
    $svg .= '<path d="M 45 105 L 115 95 L 135 145 L 100 180 L 40 165 Z" fill="#d0d0d0" stroke="none"/>';
    
    // PENTAGONE AVANT (5 arêtes pleines)
    $svg .= '<line x1="45" y1="105" x2="115" y2="95" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="115" y1="95" x2="135" y2="145" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="135" y1="145" x2="100" y2="180" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="100" y1="180" x2="40" y2="165" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="40" y1="165" x2="45" y2="105" stroke="#000" stroke-width="2"/>';
    
    // VERTICALES (3 pleines)
    $svg .= '<line x1="45" y1="105" x2="115" y2="15" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="115" y1="95" x2="185" y2="5" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="135" y1="145" x2="205" y2="55" stroke="#000" stroke-width="2"/>';
    
    // D-D' en POINTILLÉS
    $svg .= '<line x1="100" y1="180" x2="170" y2="90" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    // E-E' en POINTILLÉS
    $svg .= '<line x1="40" y1="165" x2="110" y2="75" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    
    // PENTAGONE ARRIÈRE (2 pleines)
    $svg .= '<line x1="115" y1="15" x2="185" y2="5" stroke="#000" stroke-width="2"/>';
    $svg .= '<line x1="185" y1="5" x2="205" y2="55" stroke="#000" stroke-width="2"/>';
    
    // C'-D' en POINTILLÉS
    $svg .= '<line x1="205" y1="55" x2="170" y2="90" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    // D'-E' en POINTILLÉS
    $svg .= '<line x1="170" y1="90" x2="110" y2="75" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    // E'-A' en POINTILLÉS
    $svg .= '<line x1="110" y1="75" x2="115" y2="15" stroke="#666" stroke-width="1.5" stroke-dasharray="4,4"/>';
    
    $svg .= '</svg>';
    return $svg;
}

// ============================================
// FONCTION QCM TOUJOURS SUR 2 LIGNES (GRILLE 2x2)
// ============================================

function generer_qcm_solides($propositions_data, $bonne_reponse_key) {
    // Mélanger les propositions
    $propositions_array = [];
    foreach ($propositions_data as $key => $texte) {
        $propositions_array[] = ['key' => $key, 'texte' => $texte];
    }
    shuffle($propositions_array);
    
    $lettres = ['A', 'B', 'C', 'D'];
    $bonne_lettre = '';
    
    // FORMAT GRILLE 2x2 (TOUJOURS)
    $qcm_html = '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; max-width: 600px; margin-top: 10px;">';
    foreach ($propositions_array as $index => $prop) {
        $lettre = $lettres[$index];
        if ($prop['key'] == $bonne_reponse_key) {
            $bonne_lettre = $lettre;
        }
        $qcm_html .= '<div style="font-size: 100%;"><strong>' . $lettre . '.</strong> ' . $prop['texte'] . '</div>';
    }
    $qcm_html .= '</div>';
    
    return ['html' => $qcm_html, 'bonne_lettre' => $bonne_lettre];
}
?>
