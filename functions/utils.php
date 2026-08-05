<?php
/**
 * Fonctions utilitaires pour les automatismes DNB 2026
 */

/**
 * Affiche une fraction en HTML avec mise en forme verticale
 * @param int|string $a Numérateur
 * @param int|string $b Dénominateur
 * @param string $couleur Couleur de la barre de fraction
 * @return string HTML de la fraction
 */
function frac_html($a, $b, $couleur = "black") {
    if ($b == 1 || $b == "1") {
        return (string)$a;
    }
    
    $a = (string)$a;
    $b = (string)$b;
    
    if (strlen($b) > strlen($a)) {
        return '<span style="display: inline-block;
        vertical-align: middle; 
        margin: 0 0.2em 0.4ex;
        text-align: center;">
    <span style="display: block; padding-top: 0.15em;">' . $a . '</span>
    <span style="display: none;">/</span>
    <span style="border-top: 1px solid ' . $couleur . '; display: block; padding-top: 0.15em;">' . $b . '</span>
    </span>';
    }
    
    return '<span style="display: inline-block;
        vertical-align: middle; 
        margin: 0 0.2em 0.4ex;
        text-align: center;">
    <span style="display: block; border-bottom: 1px solid ' . $couleur . '; padding-bottom: 0.15em;">' . $a . '</span>
    <span style="display: none;">/</span>
    <span style="display: block; padding-top: 0.15em;">' . $b . '</span>
    </span>';
}

/**
 * Tire un numéro de question non répété pour un automatisme donné, en
 * s'appuyant sur un pool stocké en session ($_SESSION['dnb_questions_used']).
 * Quand tous les numéros ont été utilisés, le pool est réinitialisé.
 *
 * Défini ici (et non dans qf_dnb_session.php) pour être disponible aussi
 * lors de la génération PDF (qf_dnb_print.php).
 *
 * @param string $auto_type    Clé de l'automatisme (ex. 'thales')
 * @param int    $max_questions Nombre total de questions disponibles
 * @return int   Numéro tiré (1..$max_questions)
 */
function get_unique_question_num($auto_type, $max_questions) {
    if (!isset($_SESSION['dnb_questions_used'])) {
        $_SESSION['dnb_questions_used'] = [];
    }
    if (!isset($_SESSION['dnb_questions_used'][$auto_type])) {
        $_SESSION['dnb_questions_used'][$auto_type] = [];
    }

    // Numéros encore disponibles
    $available = array_diff(range(1, $max_questions), $_SESSION['dnb_questions_used'][$auto_type]);

    // Tout utilisé -> reset
    if (empty($available)) {
        $_SESSION['dnb_questions_used'][$auto_type] = [];
        $available = range(1, $max_questions);
    }

    $num = $available[array_rand($available)];
    $_SESSION['dnb_questions_used'][$auto_type][] = $num;

    return $num;
}
