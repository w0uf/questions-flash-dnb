<?php
/**
 * qf_communs.php — briques partagées par les questions flash « à énoncé »
 * (le nombre pensé, le vocabulaire des opérations, ×/÷ par 10…).
 *
 * Extrait de functions/nombre_pense.php le 21/08/2026, quand une deuxième page
 * a eu besoin des mêmes outils. Le préfixe np_ est conservé : ces fonctions
 * étaient déjà en production sous ces noms.
 */

// ── Petits utilitaires de mise en forme ─────────────────────────────────────

/** Tire un élément au hasard dans un tableau. */
function np_pick(array $a) { return $a[array_rand($a)]; }

/** Formate un nombre à la française : 4,5 → « 4,5 » ; 7 → « 7 ». */
function np_nb($x) {
    return rtrim(rtrim(number_format((float)$x, 2, ',', ' '), '0'), ',');
}

/** Formate un montant : « 4,50 € » reste « 4,50 € », « 7 € » reste « 7 € ». */
function np_eur($x) {
    $s = number_format((float)$x, 2, ',', ' ');
    if (substr($s, -3) === ',00') $s = substr($s, 0, -3);   // 7,00 → 7
    return $s . '&nbsp;€';
}

/** Minutes depuis minuit → « 21 h 05 ». */
function np_heure($minutes) {
    $h = intdiv($minutes, 60) % 24;
    $m = $minutes % 60;
    return $h . '&nbsp;h&nbsp;' . str_pad($m, 2, '0', STR_PAD_LEFT);
}

/** Un nombre de minutes → « 1 h 50 » ou « 45 min ». */
function np_duree($minutes) {
    $h = intdiv($minutes, 60);
    $m = $minutes % 60;
    if ($h === 0) return $m . '&nbsp;min';
    if ($m === 0) return $h . '&nbsp;h';
    return $h . '&nbsp;h&nbsp;' . str_pad($m, 2, '0', STR_PAD_LEFT);
}

/**
 * Dessine une chaîne de calcul en flèches :
 *   [ ? ] --× 4--> [ ⬜ ] --− 7--> [ 25 ]
 * $valeurs a toujours un élément de plus que $ops.
 */
function np_fleches(array $valeurs, array $ops, $titre, $classe = '') {
    $h = '<div class="np-chaine ' . $classe . '">'
       . '<span class="np-chaine-titre">' . $titre . '</span>'
       . '<span class="np-suite">';
    foreach ($valeurs as $i => $v) {
        $h .= '<span class="np-case">' . $v . '</span>';
        if (isset($ops[$i])) {
            $h .= '<span class="np-fleche"><span class="np-op">' . $ops[$i] . '</span></span>';
        }
    }
    return $h . '</span></div>';
}

/** Le bloc « réponse » en gros, première ligne de toute correction. */
function np_rep($texte) {
    return '<p class="np-rep">' . $texte . '</p>';
}

/** La ligne de vérification, toujours en dernier. */
function np_verif($texte) {
    return '<p class="np-verif">✔️ Vérification : ' . $texte . '</p>';
}

/** Le pont discret vers le cycle 4 : la même chose écrite avec une lettre. */
function np_equation($texte) {
    return '<p class="np-equation">Avec une lettre, plus tard au collège : ' . $texte . '</p>';
}

/**
 * Élision : « de Tom » mais « d'Inès », « que Tom » mais « qu'Ambre ».
 * Le h est traité comme une consonne : les prénoms sont des noms propres
 * (« de Hugo »), et aucun nom commun de ce générateur ne commence par un h.
 */
function np_voyelle($mot) {
    $c = mb_strtolower(mb_substr($mot, 0, 1, 'UTF-8'), 'UTF-8');
    // Pas de 'y' : dans Yasmine ou Youssef il note une semi-consonne (le yod),
    // qui n'élide pas — on dit « que Yasmine », comme « de yaourt ».
    return in_array($c, ['a', 'e', 'i', 'o', 'u', 'é', 'è', 'ê', 'â', 'î', 'ô', 'û'], true);
}
function np_de($mot)  { return np_voyelle($mot) ? 'd’' . $mot : 'de ' . $mot; }
function np_que($mot) { return np_voyelle($mot) ? 'qu’' . $mot : 'que ' . $mot; }

/** Tire un prénom et renvoie prénom + accords prêts à l'emploi. */
function np_prenom() {
    $f = ['Nawel', 'Léa', 'Inès', 'Manon', 'Zoé', 'Chloé', 'Aya', 'Jade', 'Louise',
          'Mila', 'Fatou', 'Emma', 'Lina', 'Sarah', 'Yasmine', 'Anaïs', 'Clara',
          'Maëlys', 'Ambre', 'Elsa'];
    $m = ['Malik', 'Louis', 'Sami', 'Théo', 'Gabriel', 'Noah', 'Raphaël', 'Adam',
          'Youssef', 'Marius', 'Tom', 'Ismaël', 'Nolan', 'Hugo', 'Arthur', 'Ilyès',
          'Léo', 'Diego', 'Kylian', 'Basile'];
    if (rand(0, 1)) {
        return ['p' => np_pick($f), 'il' => 'elle', 'atil' => 'a-t-elle', 'lui' => 'elle'];
    }
    return ['p' => np_pick($m), 'il' => 'il', 'atil' => 'a-t-il', 'lui' => 'lui'];
}

/** Assemble l'énoncé : la phrase, puis la question. */
function np_enonce($phrase, $question) {
    return '<p class="np-phrase">' . $phrase . '</p>'
         . '<p class="np-demande">' . $question . '</p>';
}
