<?php
/**
 * Automatisme : Appliquer les critères de divisibilité par 2, 3, 5, 9
 * Difficulté : FACILE (range 1.0 - 1.5)
 * Format : Vrai ou Faux (1 chance sur 2)
 */

function generer_divisibilite($famille = '') {
    // Filtre optionnel : ne travailler qu'un critère à la fois. Traité avant le
    // pool historique, l'appel sans argument (session DNB) est inchangé.
    if (in_array($famille, ['2', '3', '5', '9'], true)) {
        $variantes = ['div_par_' . $famille . '_vrai', 'div_par_' . $famille . '_faux',
                      'div_par_' . $famille . '_vrai_2'];
        return div_construire($variantes[array_rand($variantes)]);
    }

    // ========================================
    // SYSTÈME DE POOL POUR ÉQUILIBRAGE
    // ========================================
    
    // Si le pool n'existe pas ou est vide, on le crée
    if (!isset($_SESSION['divisibilite_pool']) || empty($_SESSION['divisibilite_pool'])) {
        $_SESSION['divisibilite_pool'] = [
            'div_par_5_vrai',
            'div_par_5_faux',
            'div_par_5_vrai_2',
            'div_par_2_vrai',
            'div_par_2_faux',
            'div_par_2_vrai_2',
            'div_par_3_vrai',
            'div_par_3_faux',
            'div_par_3_vrai_2',
            'div_par_9_vrai',
            'div_par_9_faux',
            'div_par_9_vrai_2'
        ];
        shuffle($_SESSION['divisibilite_pool']);
    }
    
    // Piocher le premier élément du pool
    $type_question = array_shift($_SESSION['divisibilite_pool']);

    return div_construire($type_question);
}

/** Construit la question d'un sous-type (extrait en août 2026). */
function div_construire($type_question) {
    
    // ========================================
    // GÉNÉRER LA QUESTION SELON LE TYPE
    // ========================================
    
    $question_html = '';
    $reponse_html = '';
    $difficulte = 1.0;
    $nombre = 0;
    
    switch ($type_question) {
        // ============================================
        // DIVISIBILITÉ PAR 5 (le plus facile)
        // ============================================
        
        case 'div_par_5_vrai':
        case 'div_par_5_vrai_2':
            // Nombre divisible par 5 (finit par 0 ou 5)
            $dizaines = rand(1, 99);
            $unite = [0, 5][rand(0, 1)];
            $nombre = $dizaines * 10 + $unite;
            
            $question_html = '<p>' . $nombre . ' est divisible par 5.</p><p><strong>Vrai ou Faux ?</strong></p>';
            $reponse_html = '<p><strong>Vrai</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'div_par_5_faux':
            // Nombre NON divisible par 5 (finit par 1, 2, 3, 4, 6, 7, 8, 9)
            $dizaines = rand(1, 99);
            $unite = [1, 2, 3, 4, 6, 7, 8, 9][rand(0, 7)];
            $nombre = $dizaines * 10 + $unite;
            
            $question_html = '<p>' . $nombre . ' est divisible par 5.</p><p><strong>Vrai ou Faux ?</strong></p>';
            $reponse_html = '<p><strong>Faux</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // DIVISIBILITÉ PAR 2 (facile)
        // ============================================
        
        case 'div_par_2_vrai':
        case 'div_par_2_vrai_2':
            // Nombre divisible par 2 (finit par 0, 2, 4, 6, 8)
            $dizaines = rand(1, 99);
            $unite = [0, 2, 4, 6, 8][rand(0, 4)];
            $nombre = $dizaines * 10 + $unite;
            
            $question_html = '<p>' . $nombre . ' est divisible par 2.</p><p><strong>Vrai ou Faux ?</strong></p>';
            $reponse_html = '<p><strong>Vrai</strong></p>';
            $difficulte = 1.0;
            break;
            
        case 'div_par_2_faux':
            // Nombre NON divisible par 2 (finit par 1, 3, 5, 7, 9)
            $dizaines = rand(1, 99);
            $unite = [1, 3, 5, 7, 9][rand(0, 4)];
            $nombre = $dizaines * 10 + $unite;
            
            $question_html = '<p>' . $nombre . ' est divisible par 2.</p><p><strong>Vrai ou Faux ?</strong></p>';
            $reponse_html = '<p><strong>Faux</strong></p>';
            $difficulte = 1.0;
            break;
            
        // ============================================
        // DIVISIBILITÉ PAR 3 (moyen)
        // ============================================
        
        case 'div_par_3_vrai':
        case 'div_par_3_vrai_2':
            // Nombre divisible par 3 (somme des chiffres divisible par 3)
            // On génère un nombre dont la somme des chiffres est un multiple de 3
            
            // Stratégie : générer 2 ou 3 chiffres dont la somme est 3, 6, 9, 12, 15, 18
            $somme_cible = [3, 6, 9, 12, 15, 18][rand(0, 5)];
            
            if ($somme_cible <= 9) {
                // Nombre à 2 chiffres
                $c1 = rand(1, min(9, $somme_cible));
                $c2 = $somme_cible - $c1;
                $nombre = $c1 * 10 + $c2;
            } else {
                // Nombre à 3 chiffres
                $c1 = rand(1, min(9, $somme_cible - 1));
                $reste = $somme_cible - $c1;
                $c2 = rand(0, min(9, $reste));
                $c3 = $reste - $c2;
                if ($c3 > 9) {
                    // Ajustement si c3 trop grand
                    $c2 = $reste - 9;
                    $c3 = 9;
                }
                $nombre = $c1 * 100 + $c2 * 10 + $c3;
            }
            
            $question_html = '<p>' . $nombre . ' est divisible par 3.</p><p><strong>Vrai ou Faux ?</strong></p>';
            $reponse_html = '<p><strong>Vrai</strong></p>';
            $difficulte = 1.2;
            break;
            
        case 'div_par_3_faux':
            // Nombre NON divisible par 3
            // On génère un nombre dont la somme des chiffres n'est PAS un multiple de 3
            
            $somme_cible = [4, 5, 7, 8, 10, 11, 13, 14, 16, 17][rand(0, 9)];
            
            if ($somme_cible <= 9) {
                // Nombre à 2 chiffres
                $c1 = rand(1, min(9, $somme_cible));
                $c2 = $somme_cible - $c1;
                $nombre = $c1 * 10 + $c2;
            } else {
                // Nombre à 3 chiffres
                $c1 = rand(1, min(9, $somme_cible - 1));
                $reste = $somme_cible - $c1;
                $c2 = rand(0, min(9, $reste));
                $c3 = $reste - $c2;
                if ($c3 > 9) {
                    // Ajustement si c3 trop grand
                    $c2 = $reste - 9;
                    $c3 = 9;
                }
                $nombre = $c1 * 100 + $c2 * 10 + $c3;
            }
            
            $question_html = '<p>' . $nombre . ' est divisible par 3.</p><p><strong>Vrai ou Faux ?</strong></p>';
            $reponse_html = '<p><strong>Faux</strong></p>';
            $difficulte = 1.2;
            break;
            
        // ============================================
        // DIVISIBILITÉ PAR 9 (le plus difficile)
        // ============================================
        
        case 'div_par_9_vrai':
        case 'div_par_9_vrai_2':
            // Nombre divisible par 9 (somme des chiffres divisible par 9)
            // On génère un nombre dont la somme des chiffres est 9 ou 18
            
            $somme_cible = [9, 18][rand(0, 1)];
            
            if ($somme_cible == 9) {
                // Nombre à 2 ou 3 chiffres avec somme = 9
                if (rand(0, 1) == 0) {
                    // 2 chiffres
                    $c1 = rand(1, 9);
                    $c2 = 9 - $c1;
                    $nombre = $c1 * 10 + $c2;
                } else {
                    // 3 chiffres
                    $c1 = rand(1, 7);
                    $reste = 9 - $c1;
                    $c2 = rand(0, min(9, $reste));
                    $c3 = $reste - $c2;
                    $nombre = $c1 * 100 + $c2 * 10 + $c3;
                }
            } else {
                // somme = 18 (forcément 3 chiffres)
                $c1 = rand(1, 9);
                $reste = 18 - $c1;
                $c2 = rand(max(0, $reste - 9), min(9, $reste));
                $c3 = $reste - $c2;
                $nombre = $c1 * 100 + $c2 * 10 + $c3;
            }
            
            $question_html = '<p>' . $nombre . ' est divisible par 9.</p><p><strong>Vrai ou Faux ?</strong></p>';
            $reponse_html = '<p><strong>Vrai</strong></p>';
            $difficulte = 1.4;
            break;
            
        case 'div_par_9_faux':
            // Nombre NON divisible par 9
            // Somme des chiffres qui n'est PAS 9, 18 ou 27
            
            $somme_cible = [10, 11, 12, 13, 14, 15, 16, 17, 19, 20][rand(0, 9)];
            
            if ($somme_cible <= 9) {
                // Nombre à 2 chiffres
                $c1 = rand(1, min(9, $somme_cible));
                $c2 = $somme_cible - $c1;
                $nombre = $c1 * 10 + $c2;
            } else {
                // Nombre à 3 chiffres
                $c1 = rand(1, min(9, $somme_cible - 1));
                $reste = $somme_cible - $c1;
                $c2 = rand(max(0, $reste - 9), min(9, $reste));
                $c3 = $reste - $c2;
                $nombre = $c1 * 100 + $c2 * 10 + $c3;
            }
            
            $question_html = '<p>' . $nombre . ' est divisible par 9.</p><p><strong>Vrai ou Faux ?</strong></p>';
            $reponse_html = '<p><strong>Faux</strong></p>';
            $difficulte = 1.4;
            break;
    }
    

    // Justification par le critère (ajoutée en août 2026) : la réponse se
    // limitait à « Vrai » ou « Faux », ce qui n'apprend rien. On explicite le
    // critère sur le nombre tiré — c'est lui, l'automatisme à installer.
    if (preg_match('/div_par_(\d+)_/', $type_question, $m_div) && $nombre > 0) {
        $reponse_html .= div_justification($nombre, (int)$m_div[1]);
    }

    return [
        'type' => 'divisibilite',
        'difficulte_id' => $difficulte,
        'question' => $question_html,
        'reponse' => $reponse_html
    ];
}

/**
 * Rappel du critère de divisibilité, appliqué au nombre proposé.
 */
function div_justification($nombre, $diviseur) {
    $chiffres = str_split((string)$nombre);
    $somme    = array_sum($chiffres);
    $unite    = (int)substr((string)$nombre, -1);
    $style    = '<p style="font-size:0.9em; color:#666;">';

    switch ($diviseur) {
        case 2:
            return $style . 'Critère : un nombre est divisible par 2 si son <strong>chiffre des unités</strong> '
                 . 'est pair. Ici ce chiffre est ' . $unite . ', il est '
                 . ($unite % 2 === 0 ? 'pair' : 'impair') . '.</p>';
        case 5:
            return $style . 'Critère : un nombre est divisible par 5 si son <strong>chiffre des unités</strong> '
                 . 'est 0 ou 5. Ici ce chiffre est ' . $unite . '.</p>';
        case 3:
            return $style . 'Critère : un nombre est divisible par 3 si la <strong>somme de ses chiffres</strong> '
                 . 'l\'est. Ici ' . implode(' + ', $chiffres) . ' = ' . $somme . ', et ' . $somme
                 . ($somme % 3 === 0 ? ' est ' : ' n\'est pas ') . 'dans la table de 3.</p>';
        case 9:
            return $style . 'Critère : un nombre est divisible par 9 si la <strong>somme de ses chiffres</strong> '
                 . 'l\'est. Ici ' . implode(' + ', $chiffres) . ' = ' . $somme . ', et ' . $somme
                 . ($somme % 9 === 0 ? ' est ' : ' n\'est pas ') . 'un multiple de 9.</p>';
    }
    return '';
}
