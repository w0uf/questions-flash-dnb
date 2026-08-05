<?php
/**
 * Automatisme : Programme de calcul (appliquer / retrouver le nombre de départ)
 * Difficulté : FACILE à MOYEN (range 1.0 - 1.6)
 * Format : réponse ouverte (nombre pur)
 *
 * Promotion en automatisme dédié du générateur si_generer_programme_calcul()
 * qui vivait uniquement à l'intérieur de « Algorithmique ».
 */

require_once(__DIR__ . '/algorithmique.php'); // fournit si_generer_programme_calcul()

function generer_programme_calcul() {
    $q = si_generer_programme_calcul();
    $q['type'] = 'programme_calcul'; // réétiqueter pour la difficulté de base + barème
    return $q;
}
