<?php
/**
 * Fiche imprimable : 9 questions au format de l'épreuve + corrigé page 2.
 * Rendu HTML/CSS destiné à l'impression navigateur (« Enregistrer en PDF »).
 * Aucune bibliothèque PDF n'est nécessaire.
 */
session_start();

require_once(__DIR__ . '/config.php');
require_once('qf_dnb_config.php');
require_once('functions/carres.php');
require_once('functions/fractions.php');
require_once('functions/comparer_decimaux.php');
require_once('functions/calculer_fractions.php');
require_once('functions/pourcentages.php');
require_once('functions/ecritures_multiples.php');
require_once('functions/notation_scientifique.php');
require_once('functions/divisibilite.php');
require_once('functions/operations_n.php');
require_once('functions/expressions_litterales.php');
require_once('functions/valeur_expression.php');
require_once('functions/developper_factoriser.php');
require_once('functions/equations.php');
require_once('functions/droite_graduee.php');
require_once('functions/repere_orthogonal.php');
require_once('functions/codage_figures.php');
require_once('functions/angles.php');
require_once('functions/angles_triangle.php');
require_once('functions/conversions.php');
require_once('functions/solides.php');
require_once('functions/aires.php');
require_once('functions/perimetre.php');
require_once('functions/volumes.php');
require_once('functions/pythagore.php');
require_once('functions/thales.php');
require_once('functions/cosinus.php');
require_once('functions/transformations.php');
require_once('functions/probabilites.php');
require_once('functions/frequence.php');
require_once('functions/moyenne.php');
require_once('functions/graphiques.php');
require_once('functions/mediane.php');
require_once('functions/reconnaitre_proportionnalite.php');
require_once('functions/procedures_proportionnalite.php');
require_once('functions/pourcentages_augmentation.php');
require_once('functions/lire_graphique_fonctions.php');
require_once('functions/algorithmique.php');
require_once('functions/tables.php');
require_once('functions/calcul_mental.php');
require_once('functions/priorites.php');
require_once('functions/puissances.php');
require_once('functions/programme_calcul.php');
require_once('functions/etendue.php');
require_once('functions/grandeurs_composees.php');

$automatismes_list = $_POST['auto'] ?? [];

if (empty($automatismes_list)) {
    header('Location: questions_flash_dnb.php');
    exit;
}

// Mapping automatisme → thème reconstruit depuis la config canonique
$auto_theme_map = [];
foreach ($automatismes_list as $auto) {
    $auto_theme_map[$auto] = dnb_theme_of($auto);
}

function generer_question_print($auto) {
    switch ($auto) {
        case 'carres':               return generer_carres();
        case 'fractions_decimales':  return generer_fractions_decimales();
        case 'comparer_decimaux':    return generer_comparer_decimaux();
        case 'calculer_fractions':   return generer_calculer_fractions();
        case 'pourcentages':         return generer_pourcentages();
        case 'ecritures_multiples':  return generer_ecritures_multiples();
        case 'notation_scientifique':return generer_notation_scientifique();
        case 'divisibilite':         return generer_divisibilite();
        case 'operations_n':         return generer_operations_n();
        case 'expressions_litterales':return generer_expressions_litterales();
        case 'valeur_expression':    return generer_valeur_expression();
        case 'developper_factoriser':return generer_developper_factoriser();
        case 'equations':            return generer_equations();
        case 'droite_graduee':       return generer_droite_graduee();
        case 'repere_orthogonal':    return generer_repere_orthogonal();
        case 'codage_figure':        return generer_codage_figures();
        case 'angles':               return generer_angles();
        case 'angles_triangle':      return generer_angles_triangle();
        case 'conversions':          return generer_conversions();
        case 'solides':              return generer_solides();
        case 'aires':                return generer_aires();
        case 'perimetre':            return generer_perimetre();
        case 'volumes':              return generer_volumes();
        case 'pythagore':            return generer_pythagore();
        case 'thales':               return generer_thales();
        case 'cosinus':              return generer_cosinus();
        case 'transformations':      return generer_transformations();
        case 'probabilites':         return generer_probabilites();
        case 'frequence':
        case 'frequences':           return generer_frequence();
        case 'moyenne':              return generer_moyenne();
        case 'graphiques':
        case 'lire_tableaux':        return generer_graphiques();
        case 'reconnaitre_proportionnalite': return generer_reconnaitre_proportionnalite();
        case 'procedures_proportionnalite':  return generer_procedures_proportionnalite();
        case 'pourcentages_augmentation':    return generer_pourcentages_augmentation();
        case 'lire_graphique_fonctions':     return generer_lire_graphique_fonctions();
        case 'algorithmique':        return generer_suites_instructions();
        case 'mediane':              return generer_mediane();
        case 'tables':               return generer_tables();
        case 'calcul_mental':        return generer_calcul_mental();
        case 'priorites':            return generer_priorites();
        case 'puissances':           return generer_puissances();
        case 'programme_calcul':     return generer_programme_calcul();
        case 'etendue':              return generer_etendue();
        case 'grandeurs_composees':  return generer_grandeurs_composees();
        default:                     return generer_carres();
    }
}

// ── Construire le pool de 9 questions (même logique que la session) ──
$nb_automatismes = count($automatismes_list);
$automatismes_par_theme = [];
foreach ($automatismes_list as $auto) {
    $theme = $auto_theme_map[$auto] ?? 'nombres';
    $automatismes_par_theme[$theme][] = $auto;
}

$automatismes_pool = [];

if ($nb_automatismes <= 9) {
    $questions_par_auto = floor(9 / $nb_automatismes);
    $reste = 9 % $nb_automatismes;
    foreach ($automatismes_list as $auto) {
        for ($j = 0; $j < $questions_par_auto; $j++) {
            $automatismes_pool[] = $auto;
        }
    }
    for ($j = 0; $j < $reste; $j++) {
        $automatismes_pool[] = $automatismes_list[array_rand($automatismes_list)];
    }
    shuffle($automatismes_pool);
} else {
    $nb_themes = count($automatismes_par_theme);
    $questions_par_theme = [];
    $total_attribue = 0;
    foreach ($automatismes_par_theme as $theme => $autos) {
        $proportion = count($autos) / $nb_automatismes;
        $nb_q = max(1, round($proportion * 9));
        $questions_par_theme[$theme] = $nb_q;
        $total_attribue += $nb_q;
    }
    $diff = 9 - $total_attribue;
    if ($diff > 0) {
        arsort($automatismes_par_theme);
        foreach ($automatismes_par_theme as $theme => $autos) {
            if ($diff == 0) break;
            $questions_par_theme[$theme]++;
            $diff--;
        }
    } elseif ($diff < 0) {
        arsort($questions_par_theme);
        foreach ($questions_par_theme as $theme => $nb) {
            if ($diff == 0) break;
            if ($nb > 1) { $questions_par_theme[$theme]--; $diff++; }
        }
    }
    foreach ($questions_par_theme as $theme => $nb_q) {
        $autos_du_theme = $automatismes_par_theme[$theme];
        if ($nb_q <= count($autos_du_theme)) {
            $choisis = array_rand(array_flip($autos_du_theme), $nb_q);
            if (!is_array($choisis)) $choisis = [$choisis];
            foreach ($choisis as $a) { $automatismes_pool[] = $a; }
        } else {
            $qpa = floor($nb_q / count($autos_du_theme));
            $reste_t = $nb_q % count($autos_du_theme);
            foreach ($autos_du_theme as $a) {
                for ($j = 0; $j < $qpa; $j++) $automatismes_pool[] = $a;
            }
            for ($j = 0; $j < $reste_t; $j++) $automatismes_pool[] = $autos_du_theme[array_rand($autos_du_theme)];
        }
    }
    shuffle($automatismes_pool);
}

// ── Générer les 9 questions, puis barème via la logique partagée ──
$questions = [];
for ($i = 0; $i < 9; $i++) {
    $auto = $automatismes_pool[$i];
    $q = generer_question_print($auto);
    if (!isset($q['difficulte_id'])) $q['difficulte_id'] = isset($q['difficulte']) ? $q['difficulte'] : 1.5;
    $q['auto_key'] = $auto;
    $questions[] = $q;
}
// Tri (format ouvert prioritaire, difficulté) + barème 6×0,5 + 3×1 (voir qf_dnb_config.php)
$questions = dnb_attribuer_baremes($questions);

$mois_fr = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
$date_fr = date('j') . ' ' . $mois_fr[(int)date('n')] . ' ' . date('Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex, nofollow">
<title> </title>
<style>
/* ── Reset impression ── */
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Times New Roman', Times, serif;
    font-size: 11pt;
    color: #000;
    background: #fff;
    padding: 1.5cm 2cm;
}

/* ── Header DNB ── */
.dnb-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #000;
    padding-bottom: 8px;
    margin-bottom: 18px;
}
.dnb-header img {
    height: 60px;
    width: auto;
}
.dnb-header-center {
    text-align: center;
    flex: 1;
    padding: 0 15px;
}
.dnb-header-center h1 {
    font-size: 15pt;
    font-weight: bold;
    letter-spacing: 0.05em;
}
.dnb-header-center .sous-titre {
    font-size: 9pt;
    color: #333;
    margin-top: 2px;
}
.dnb-header-right {
    text-align: right;
    font-size: 9pt;
    color: #444;
}

/* ── Infos format ── */
.format-bar {
    display: flex;
    gap: 30px;
    font-size: 9.5pt;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 6px 14px;
    margin-bottom: 18px;
    background: #f9f9f9;
}
.format-bar span::before { margin-right: 4px; }

/* ── Espace identité ── */
.identite {
    display: flex;
    gap: 30px;
    margin-bottom: 18px;
    font-size: 10pt;
}
.identite-field {
    flex: 1;
    border-bottom: 1px solid #000;
    padding-bottom: 2px;
}
.identite-field label {
    font-weight: bold;
    margin-right: 6px;
}

/* ── Questions ── */
.question-row {
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 10px 14px;
    margin-bottom: 12px;
    page-break-inside: avoid;
}
.question-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 6px;
}
.question-num {
    font-weight: bold;
    font-size: 11pt;
}
.question-bareme {
    font-size: 9pt;
    color: #555;
    font-style: italic;
}
.question-content {
    font-size: 11pt;
    line-height: 1.6;
}
.question-content p { margin: 4px 0; }
.question-content svg { max-width: 100%; height: auto; }

/* ── Fractions / angles inline ── */
.angle { display: inline-block; text-align: center; font-family: monospace; font-size: 117%; line-height: 1; }
.hat { display: block; font-size: 0.7em; transform: scaleX(3.5); margin-left: 0.1em; margin-bottom: -0.6em; }

/* ── Zone réponse ── */
.reponse-zone {
    margin-top: 8px;
    padding-top: 6px;
    border-top: 1px dashed #aaa;
    font-size: 10pt;
    color: #444;
    min-height: 28px;
}
.reponse-zone::after {
    content: " ......................................................";
    color: #bbb;
}

/* ── Total points ── */
.total-bar {
    text-align: right;
    font-size: 10pt;
    font-weight: bold;
    border-top: 2px solid #000;
    padding-top: 6px;
    margin-top: 10px;
}

/* ── Corrigé (page 2) ── */
.page-corrige {
    page-break-before: always;
    padding-top: 1cm;
}
.corrige-title {
    font-size: 13pt;
    font-weight: bold;
    border-bottom: 2px solid #000;
    padding-bottom: 6px;
    margin-bottom: 16px;
}
.corrige-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10.5pt;
}
.corrige-table th {
    background: #f0f0f0;
    border: 1px solid #999;
    padding: 6px 10px;
    text-align: center;
}
.corrige-table td {
    border: 1px solid #ccc;
    padding: 7px 10px;
    vertical-align: top;
    line-height: 1.6;
}
.corrige-table td:first-child {
    text-align: center;
    font-weight: bold;
    width: 60px;
    background: #fafafa;
}
.corrige-table td .bareme-tag {
    font-size: 8.5pt;
    color: #666;
    font-style: italic;
    display: block;
    margin-top: 2px;
}

/* ── Bouton impression (masqué à l'impression) ── */
.no-print {
    margin-bottom: 20px;
    text-align: center;
}
.btn-print {
    background: #003366;
    color: #fff;
    border: none;
    padding: 10px 28px;
    font-size: 13pt;
    border-radius: 6px;
    cursor: pointer;
    font-family: Arial, sans-serif;
}
.btn-print:hover { background: #004d99; }

@media print {
    .no-print { display: none !important; }
    body { padding: 0; }
    .question-row { border-color: #999; }
}
</style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Imprimer / Enregistrer en PDF</button>
    &nbsp;&nbsp;
    <a href="questions_flash_dnb.php" style="font-family:Arial;font-size:11pt;color:#555;">← Retour à la sélection</a>
    <p style="font-family:Arial;font-size:10pt;color:#888;margin-top:10px;">
        💡 Dans la boîte d'impression, décochez <strong>« En-têtes et pieds de page »</strong> pour supprimer la date et l'URL affichées par le navigateur.
    </p>
</div>

<!-- ── Header ── -->
<div class="dnb-header">
    <?php if (DNB_LOGO !== ''): ?><img src="<?php echo htmlspecialchars(DNB_LOGO); ?>" alt=""><?php endif; ?>
    <div class="dnb-header-center">
        <h1>Automatismes — DNB</h1>
        <div class="sous-titre"><?php echo htmlspecialchars(DNB_PROGRAMME); ?> · Sans calculatrice</div>
    </div>
    <div class="dnb-header-right">
        <?php if (DNB_SITE_URL !== ''): ?><?php echo htmlspecialchars(DNB_SITE_URL); ?><br><?php endif; ?>
        <?php echo htmlspecialchars($date_fr); ?>
    </div>
</div>

<!-- ── Format ── -->
<div class="format-bar">
    <span>📝 9 questions</span>
    <span>⏱️ 20 minutes</span>
    <span>📊 3 pts (6 × 0,5 pt) + 3 pts (3 × 1 pt) = 6 pts</span>
</div>

<!-- ── Identité ── -->
<div class="identite">
    <div class="identite-field"><label>Nom :</label></div>
    <div class="identite-field"><label>Prénom :</label></div>
    <div class="identite-field"><label>Classe :</label></div>
</div>

<!-- ── Questions ── -->
<?php foreach ($questions as $i => $q): ?>
<div class="question-row">
    <div class="question-header">
        <span class="question-num">Question <?php echo $i + 1; ?></span>
        <span class="question-bareme"><?php echo str_replace('.', ',', $q['bareme']); ?> point<?php echo $q['bareme'] > 1 ? 's' : ''; ?></span>
    </div>
    <div class="question-content">
        <?php echo $q['question']; ?>
    </div>
    <div class="reponse-zone">Réponse :</div>
</div>
<?php endforeach; ?>

<div class="total-bar">/ 6 points</div>

<!-- ── Corrigé ── -->
<div class="page-corrige">

<div class="dnb-header">
    <?php if (DNB_LOGO !== ''): ?><img src="<?php echo htmlspecialchars(DNB_LOGO); ?>" alt=""><?php endif; ?>
    <div class="dnb-header-center">
        <h1>Corrigé — Automatismes DNB</h1>
        <div class="sous-titre"><?php echo htmlspecialchars(DNB_PROGRAMME); ?></div>
    </div>
    <div class="dnb-header-right"><?php if (DNB_SITE_URL !== ''): ?><?php echo htmlspecialchars(DNB_SITE_URL); ?><?php endif; ?></div>
</div>

<table class="corrige-table">
    <thead>
        <tr>
            <th>N°</th>
            <th>Réponse attendue</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($questions as $i => $q): ?>
        <tr>
            <td><?php echo $i + 1; ?></td>
            <td>
                <?php echo $q['reponse']; ?>
                <span class="bareme-tag"><?php echo str_replace('.', ',', $q['bareme']); ?> pt<?php echo $q['bareme'] > 1 ? 's' : ''; ?></span>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</div>

</body>
</html>
