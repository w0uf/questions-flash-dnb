<?php
// ========================================
// CONFIGURATION PARTAGÉE DES AUTOMATISMES DNB
// Programme officiel octobre 2025
// Source unique de vérité, incluse par :
//   - questions_flash_dnb.php (affichage de la sélection)
//   - qf_dnb_session.php      (session TBI)
//   - qf_dnb_print.php        (génération PDF)
// ========================================

$automatismes_config = [
    'nombres' => [
        'titre' => '🔢 Nombres et Calculs',
        'items' => [
            'tables' => ['nom' => 'Tables de multiplication', 'ready' => true],
            'calcul_mental' => ['nom' => 'Calcul mental (additions, soustractions)', 'ready' => true],
            'priorites' => ['nom' => 'Priorités opératoires (×, ÷ avant +, −)', 'ready' => true],
            'calcul_astucieux' => ['nom' => 'Calcul astucieux (regrouper, développer, factoriser)', 'ready' => true],
            'carres' => ['nom' => 'Carrés de 1 à 12', 'ready' => true],
            'fractions_decimales' => ['nom' => 'Fractions simples ⇄ Décimaux (1/2, 1/4, 3/4...)', 'ready' => true],
            'comparer_decimaux' => ['nom' => 'Comparer et calculer avec décimaux (y compris négatifs)', 'ready' => true],
            'calculer_fractions' => ['nom' => 'Simplifier, comparer, calculer avec fractions (et conversions fraction ⇄ pourcentage)', 'ready' => true ],
            'pourcentages' => ['nom' => 'Pourcentages simples (100%, 50%, 25%, 10%, 1%)', 'ready' => true],
            'ecritures_multiples' => ['nom' => 'Écritures multiples d\'un nombre (1,2 = 12/10 = 6/5...)', 'ready' => true],
            'notation_scientifique' => ['nom' => 'Notation scientifique', 'ready' => true],
            'puissances' => ['nom' => 'Puissances et ordre de grandeur', 'ready' => true],
            'divisibilite' => ['nom' => 'Critères de divisibilité (2, 3, 5, 9)', 'ready' => true],
            'operations_n' => ['nom' => 'Double, triple, moitié, prédécesseur, successeur, carré', 'ready' => true],
            'programme_calcul' => ['nom' => 'Programme de calcul (appliquer, retrouver le départ)', 'ready' => true],
            'expressions_litterales' => ['nom' => 'Simplifier expressions littérales', 'ready' => true],
            'valeur_expression' => ['nom' => 'Calculer valeur expression algébrique (avec puissances)', 'ready' => true],
            'developper_factoriser' => ['nom' => 'Développer et factoriser expression simple', 'ready' => true],
            'equations' => ['nom' => 'Résoudre ax=c, x+b=c, ax+b=c', 'ready' => true],
            'droite_graduee' => ['nom' => 'Lire abscisse et placer point sur droite graduée', 'ready' => true],
        ]
    ],
    'geometrie' => [
        'titre' => '📐 Espace et Géométrie',
        'items' => [
            'repere_orthogonal' => ['nom' => 'Lire et placer coordonnées dans repère', 'ready' => true],
            'codage_figure' => ['nom' => 'Identifier triangles, quadrilatères, médiatrice', 'ready' => true],
            'angles' => ['nom' => 'Reconnaître angles (opposés, adjacents, supplémentaires...)', 'ready' => true],
            'angles_triangle' => ['nom' => 'Somme angles triangle = 180°', 'ready' => true],
            'conversions' => ['nom' => 'Conversions unités (mm, cm, m, km, L, g...)', 'ready' => true],
            'solides' => ['nom' => 'Reconnaître solides (cube, pavé, prisme, cylindre...)', 'ready' => true],
            'perimetre' => ['nom' => 'Périmètre polygone et disque', 'ready' => true],
            'aires' => ['nom' => 'Aires (triangle, rectangle, disque)', 'ready' => true],
            'volumes' => ['nom' => 'Volumes (cube, pavé, prisme, cylindre)', 'ready' => true],
            'pythagore' => ['nom' => 'Théorème de Pythagore', 'ready' => true],
            'thales' => ['nom' => 'Théorème de Thalès', 'ready' => true],
            'cosinus' => ['nom' => 'Cosinus (rapports de longueurs)', 'ready' => true],
            'transformations' => ['nom' => 'Symétries (axiale, centrale), translation', 'ready' => true],
        ]
    ],
    'probabilites' => [
        'titre' => '🎲 Probabilités et Statistiques',
        'items' => [
            'probabilites' => ['nom' => 'Probabilités simples (équiprobabilité)', 'ready' => true],
            'frequences' => ['nom' => 'Exprimer fréquence simple', 'ready' => true],
            'moyenne' => ['nom' => 'Exprimer moyenne', 'ready' => true],
            'mediane' => ['nom' => 'Déterminer médiane (petite série)', 'ready' => true],
            'etendue' => ['nom' => 'Déterminer l\'étendue d\'une série', 'ready' => true],
            'lire_tableaux' => ['nom' => 'Lire tableaux, diagrammes, graphiques', 'ready' => true],
        ]
    ],
    'proportionnalite' => [
        'titre' => '📊 Proportionnalité et Fonctions',
        'items' => [
            'reconnaitre_proportionnalite' => ['nom' => 'Reconnaître si situation donnée est proportionnelle ou non', 'ready' => true],
            'procedures_proportionnalite' => ['nom' => 'Mobiliser procédure adaptée (linéarité, retour à l\'unité)', 'ready' => true],
            'grandeurs_composees' => ['nom' => 'Grandeurs composées (vitesse, distance, durée, débit)', 'ready' => true],
            'pourcentages_augmentation' => ['nom' => 'Appliquer augmentation ou diminution en pourcentage', 'ready' => true],
            'lire_graphique_fonctions' => ['nom' => 'Exploiter graphique (lire valeurs sur axes)', 'ready' => true],
            'image_antecedent' => ['nom' => 'Image et antécédent (notation f(x), tableau, graphique)', 'ready' => true],
        ]
    ],
    'algorithmique' => [
        'titre' => '💻 Algorithmique et Programmation',
        'items' => [
            'algorithmique' => ['nom' => 'Interpréter suite d\'instructions (calcul, déplacement, construction)', 'ready' => true],
        ]
    ]
];

// ---- Map dérivée : clé d'automatisme => thème (source de vérité serveur) ----
$dnb_auto_theme_map = [];
foreach ($automatismes_config as $theme_key => $theme) {
    foreach ($theme['items'] as $auto_key => $auto) {
        $dnb_auto_theme_map[$auto_key] = $theme_key;
    }
}

/**
 * Retourne le thème d'un automatisme d'après la config canonique.
 * Fallback 'nombres' si inconnu (robustesse).
 */
function dnb_theme_of($auto_key) {
    global $dnb_auto_theme_map;
    return $dnb_auto_theme_map[$auto_key] ?? 'nombres';
}

// ========================================================================
// DIFFICULTÉ DE BASE PAR AUTOMATISME (échelle 1,0 très facile → 2,6 dur)
// Sert de clé de tri INTER-automatismes pour l'attribution des points.
// Le difficulte_id renvoyé par chaque générateur départage à l'intérieur
// d'un même automatisme (ordre intra-type conservé).
// Table unique et tunable : ajuster ici pour recalibrer tout le barème.
// ========================================================================
$dnb_difficulte_base = [
    // 🔢 Nombres et Calculs
    'tables'                       => 1.0,
    'calcul_mental'                => 1.1,
    'comparer_decimaux'            => 1.0,
    'operations_n'                 => 1.1,
    'fractions_decimales'          => 1.1,
    'carres'                       => 1.2,
    'divisibilite'                 => 1.2,
    'droite_graduee'               => 1.2,
    'pourcentages'                 => 1.3,
    'expressions_litterales'       => 1.5,
    'valeur_expression'            => 1.6,
    'equations'                    => 1.6,
    'ecritures_multiples'          => 1.6,
    'priorites'                    => 1.4,
    'calcul_astucieux'             => 1.6,
    'programme_calcul'             => 1.7, // 🆕 (à venir)
    'puissances'                   => 1.7, // 🆕 (à venir)
    'notation_scientifique'        => 1.8,
    'developper_factoriser'        => 1.8,
    'calculer_fractions'           => 2.0,
    // 📐 Espace et Géométrie
    'solides'                      => 1.1,
    'angles'                       => 1.2,
    'repere_orthogonal'            => 1.2,
    'codage_figure'                => 1.3,
    'angles_triangle'              => 1.4,
    'conversions'                  => 1.5,
    'perimetre'                    => 1.6,
    'aires'                        => 1.7,
    'transformations'              => 1.9,
    'volumes'                      => 2.4,
    'thales'                       => 2.4,
    'pythagore'                    => 2.5,
    'cosinus'                      => 2.6,
    // 🎲 Probabilités et Statistiques
    'lire_tableaux'                => 1.3,
    'etendue'                      => 1.3, // 🆕 (à venir)
    'probabilites'                 => 1.5,
    'frequences'                   => 1.5,
    'moyenne'                      => 1.6,
    'mediane'                      => 1.7,
    // 📊 Proportionnalité et Fonctions
    'reconnaitre_proportionnalite' => 1.4,
    'lire_graphique_fonctions'     => 1.5,
    'image_antecedent'             => 1.6,
    'procedures_proportionnalite'  => 1.7,
    'pourcentages_augmentation'    => 1.7,
    'grandeurs_composees'          => 1.9, // 🆕 (à venir)
    // 💻 Algorithmique
    'algorithmique'                => 1.6,
];

/**
 * Difficulté de base d'un automatisme (fallback 1.5 si inconnu).
 */
function dnb_difficulte_base($auto_key) {
    global $dnb_difficulte_base;
    return $dnb_difficulte_base[$auto_key] ?? 1.5;
}

/**
 * Détecte le format d'une question à partir de son HTML (runtime, par question).
 * Retourne 'qcm' (~25% au hasard), 'vf' (binaire ~50%) ou 'ouvert'.
 * Un générateur peut forcer le format via $q['format'] (prioritaire).
 */
function dnb_detecter_format($q) {
    if (isset($q['format']) && in_array($q['format'], ['ouvert', 'qcm', 'vf'], true)) {
        return $q['format']; // override explicite du générateur
    }
    $question = $q['question'] ?? '';
    $both     = $question . ' ' . ($q['reponse'] ?? '');

    // QCM : au moins 2 options lettrées <strong>A.</strong> … ou champ radio
    if (preg_match_all('/>\s*[A-D]\.\s*<\/strong>/', $question) >= 2) return 'qcm';
    if (stripos($question, 'type="radio"') !== false) return 'qcm';

    // VF explicite
    if (preg_match('/vrai\s*ou\s*faux/i', $both)) return 'vf';
    if (preg_match('/\bvrai\b/i', $both) && preg_match('/\bfaux\b/i', $both)) return 'vf';

    // Question binaire oui/non (« Ce triangle est-il rectangle ? », « A-t-il raison ? »)
    if (preg_match('/\b(est-il|est-elle|a-t-elle|a-t-il|s\'agit-il|est-ce)\b/i', $question)
        && preg_match('/\b(oui|non|vrai|faux)\b/i', $both)) return 'vf';

    return 'ouvert';
}

/**
 * Attribue les barèmes aux 9 questions d'une session/PDF (logique unique,
 * partagée entre qf_dnb_session.php et qf_dnb_print.php).
 *
 * Règle : les 3 questions à 1 pt sont, PAR PRIORITÉ, à réponse ouverte
 * (non devinables), puis les plus difficiles (base + difficulte_id interne).
 * Une QCM/VF ne monte à 1 pt que s'il n'y a pas assez de questions ouvertes
 * (fallback : on comble avec les QCM/VF les plus difficiles pour garder 9 pts).
 * Les 6 autres valent 0,5 pt. L'ordre d'affichage final est aléatoire.
 *
 * Chaque question doit porter 'auto_key' (clé de l'automatisme) ; à défaut
 * 'type' est utilisé, sinon fallback difficulté 1.5.
 *
 * @param array $questions Liste de questions (chacune : question, reponse, difficulte_id…)
 * @return array           Les questions avec 'format' et 'bareme' renseignés, mélangées
 */
function dnb_attribuer_baremes(array $questions) {
    foreach ($questions as &$q) {
        $q['format'] = dnb_detecter_format($q);
        $auto_key    = $q['auto_key'] ?? ($q['type'] ?? '');
        $base        = dnb_difficulte_base($auto_key);
        $interne     = isset($q['difficulte_id']) ? (float)$q['difficulte_id'] : 1.5;
        // La base domine ; le difficulte_id interne (÷100) ne sert qu'au départage intra-type.
        $q['_diff_eff'] = $base + $interne / 100.0;
        $q['_ouvert']   = ($q['format'] === 'ouvert') ? 1 : 0;
    }
    unset($q);

    // Tri : questions ouvertes d'abord, puis difficulté décroissante
    usort($questions, function ($a, $b) {
        if ($a['_ouvert'] !== $b['_ouvert']) return $b['_ouvert'] <=> $a['_ouvert'];
        return $b['_diff_eff'] <=> $a['_diff_eff'];
    });

    // Les 3 premières -> 1 pt, le reste -> 0,5 pt
    $n = count($questions);
    for ($i = 0; $i < $n; $i++) {
        $questions[$i]['bareme'] = ($i < 3) ? 1 : 0.5;
    }

    // Nettoyage des clés internes
    foreach ($questions as &$q) {
        unset($q['_diff_eff'], $q['_ouvert']);
    }
    unset($q);

    // Ordre d'affichage aléatoire (indépendant du barème)
    shuffle($questions);

    return $questions;
}
