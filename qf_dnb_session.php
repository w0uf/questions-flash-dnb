<?php
/**
 * ========================================
 * SESSION DNB - TBI MODE
 * ========================================
 * Programme officiel octobre 2025
 * 
 * ⚠️ CE FICHIER EST FIGÉ - NE PAS MODIFIER
 * 
 * Pour ajouter/modifier des automatismes :
 * → Modifier les fichiers dans functions/
 * 
 * ========================================
 */

session_start();

// ========================================
// MODE DEBUG - Affichage des erreurs PHP
// ========================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration partagée (map automatisme => thème, source de vérité serveur)
require_once('qf_dnb_config.php');

// Import des fonctions d'automatismes
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

// ========== INITIALISATION SESSION ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['dnb_active'])) {
    $_SESSION['dnb_automatismes'] = $_POST['auto'] ?? [];

    // Mapping automatisme => thème reconstruit depuis la config canonique
    // (robuste : indépendant de l'ordre/présence des champs POST)
    $_SESSION['dnb_auto_theme_map'] = [];
    foreach ($_SESSION['dnb_automatismes'] as $auto) {
        $_SESSION['dnb_auto_theme_map'][$auto] = dnb_theme_of($auto);
    }
    
    // RÉINITIALISER TOUS LES POOLS pour éviter répétitions
    unset($_SESSION['codage_figures_pool']);
    unset($_SESSION['repere_orthogonal_pool']);
    unset($_SESSION['angles_pool']);
    unset($_SESSION['angles_triangle_pool']);
    unset($_SESSION['conversions_pool']);
    unset($_SESSION['solides_pool']);
    unset($_SESSION['droite_graduee_pool']);
    unset($_SESSION['equations_pool']);
    unset($_SESSION['developper_factoriser_pool']);
    unset($_SESSION['valeur_expression_pool']);
    unset($_SESSION['expressions_litterales_pool']);
    unset($_SESSION['operations_n_pool']);
    unset($_SESSION['divisibilite_pool']);
    unset($_SESSION['notation_scientifique_pool']);
    unset($_SESSION['ecritures_multiples_pool']);
    unset($_SESSION['calculer_fractions_pool']);
    unset($_SESSION['mediane_pool']);
    unset($_SESSION['thales_pool']);
    
    $_SESSION['dnb_nb_questions'] = 9;
    $_SESSION['dnb_timer_total'] = 1200;
    $_SESSION['dnb_start_time'] = time();
    $_SESSION['dnb_questions_faites'] = 0;
    $_SESSION['dnb_active'] = true;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['dnb_active'])) {
    // Incrémenter seulement si on n'a pas encore fini
    if ($_SESSION['dnb_questions_faites'] < $_SESSION['dnb_nb_questions']) {
        $_SESSION['dnb_questions_faites']++;
    }
}

// Vérifier session active
if (!isset($_SESSION['dnb_active']) || empty($_SESSION['dnb_automatismes'])) {
    header('Location: questions_flash_dnb.php');
    exit;
}

// Fin de session ?
if ($_SESSION['dnb_questions_faites'] >= $_SESSION['dnb_nb_questions']) {
    header('Location: qf_dnb_resultats.php');
    exit;
}
// get_unique_question_num() est désormais défini dans functions/utils.php
// (chargé via functions/thales.php) pour être partagé avec qf_dnb_print.php.

// ========== FONCTION PRINCIPALE ==========

function generer_question($auto) {
    switch($auto) {
        case 'carres':
            return generer_carres();
        case 'fractions_decimales':
            return generer_fractions_decimales();
        case 'comparer_decimaux':
            return generer_comparer_decimaux();
        case 'calculer_fractions':
            return generer_calculer_fractions();
        case 'pourcentages':
            return generer_pourcentages();
        case 'ecritures_multiples':
            return generer_ecritures_multiples();
        case 'notation_scientifique':
            return generer_notation_scientifique();
        case 'divisibilite':
            return generer_divisibilite();
        case 'operations_n':
            return generer_operations_n();
        case 'expressions_litterales':
            return generer_expressions_litterales();
        case 'valeur_expression':
            return generer_valeur_expression();
        case 'developper_factoriser':
            return generer_developper_factoriser();
        case 'equations':
            return generer_equations();
        case 'droite_graduee':
            return generer_droite_graduee();
        case 'repere_orthogonal':
            return generer_repere_orthogonal();
        case 'codage_figure':
            return generer_codage_figures();
        case 'angles':
            return generer_angles();
        case 'angles_triangle':
            return generer_angles_triangle();
        case 'conversions':
            return generer_conversions();
        case 'solides':
            return generer_solides();
        case 'aires':
            return generer_aires();
        case 'perimetre':
            return generer_perimetre();
        case 'volumes':
            return generer_volumes();
        case 'pythagore':
            return generer_pythagore();
        case 'thales':
            return generer_thales();
        case 'cosinus':
            return generer_cosinus();
        case 'transformations':
            return generer_transformations();
        case 'probabilites':
            return generer_probabilites();
        case 'frequence':
        case 'frequences':
            return generer_frequence();
        case 'moyenne':
            return generer_moyenne();
        case 'graphiques':
        case 'lire_tableaux':
            return generer_graphiques();
            
        // PROPORTIONNALITÉ ET FONCTIONS
        case 'reconnaitre_proportionnalite':
            return generer_reconnaitre_proportionnalite();
            
        case 'procedures_proportionnalite':
            return generer_procedures_proportionnalite();
            
        case 'pourcentages_augmentation':
            return generer_pourcentages_augmentation();
            
        case 'lire_graphique_fonctions':
            return generer_lire_graphique_fonctions();
            
        // ALGORITHMIQUE
        case 'algorithmique':
            return generer_suites_instructions();
            
        case 'mediane':
            return generer_mediane();
        case 'tables':
            return generer_tables();
        case 'calcul_mental':
            return generer_calcul_mental();
        case 'priorites':
            return generer_priorites();
        case 'puissances':
            return generer_puissances();
        case 'programme_calcul':
            return generer_programme_calcul();
        case 'etendue':
            return generer_etendue();
        case 'grandeurs_composees':
            return generer_grandeurs_composees();
        default:
            return generer_carres();
    }
}

// ========== GÉNÉRATION ET TRI DES 9 QUESTIONS ==========
if (!isset($_SESSION['dnb_questions_pool'])) {
    $automatismes_list = $_SESSION['dnb_automatismes'];
    $nb_automatismes = count($automatismes_list);
    $auto_theme_map = $_SESSION['dnb_auto_theme_map'] ?? [];
    
    // Grouper les automatismes par thème
    $automatismes_par_theme = [];
    foreach ($automatismes_list as $auto) {
        $theme = $auto_theme_map[$auto] ?? 'nombres';
        if (!isset($automatismes_par_theme[$theme])) {
            $automatismes_par_theme[$theme] = [];
        }
        $automatismes_par_theme[$theme][] = $auto;
    }
    
    $automatismes_pool = [];
    
    // STRATÉGIE 1 : Si ≤ 9 automatismes → répartition équitable simple
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
    }
    // STRATÉGIE 2 : Si > 9 automatismes → répartition PONDÉRÉE par thème
    else {
        $nb_themes = count($automatismes_par_theme);
        
        // Calcul proportionnel initial
        $questions_par_theme = [];
        $total_attribue = 0;
        
        foreach ($automatismes_par_theme as $theme => $autos) {
            $proportion = count($autos) / $nb_automatismes;
            $nb_questions = max(1, round($proportion * 9)); // Minimum 1 par thème
            $questions_par_theme[$theme] = $nb_questions;
            $total_attribue += $nb_questions;
        }
        
        // Ajustement si total != 9 (arrondi)
        $diff = 9 - $total_attribue;
        
        if ($diff > 0) {
            // Ajouter les questions manquantes aux thèmes avec le plus d'automatismes
            arsort($automatismes_par_theme);
            foreach ($automatismes_par_theme as $theme => $autos) {
                if ($diff == 0) break;
                $questions_par_theme[$theme]++;
                $diff--;
            }
        } elseif ($diff < 0) {
            // Retirer des questions aux thèmes qui en ont le plus (mais minimum 1)
            arsort($questions_par_theme);
            foreach ($questions_par_theme as $theme => $nb) {
                if ($diff == 0) break;
                if ($nb > 1) {
                    $questions_par_theme[$theme]--;
                    $diff++;
                }
            }
        }
        
        // Générer la pool avec répartition pondérée
        foreach ($questions_par_theme as $theme => $nb_questions) {
            $autos_du_theme = $automatismes_par_theme[$theme];
            
            // Si plus de questions que d'automatismes dans le thème, répartir équitablement
            if ($nb_questions <= count($autos_du_theme)) {
                // Tirer N automatismes différents du thème
                $autos_choisis = array_rand(array_flip($autos_du_theme), $nb_questions);
                if (!is_array($autos_choisis)) $autos_choisis = [$autos_choisis];
                foreach ($autos_choisis as $auto) {
                    $automatismes_pool[] = $auto;
                }
            } else {
                // Plus de questions que d'automatismes : répartir équitablement
                $questions_par_auto_theme = floor($nb_questions / count($autos_du_theme));
                $reste_theme = $nb_questions % count($autos_du_theme);
                
                foreach ($autos_du_theme as $auto) {
                    for ($j = 0; $j < $questions_par_auto_theme; $j++) {
                        $automatismes_pool[] = $auto;
                    }
                }
                
                for ($j = 0; $j < $reste_theme; $j++) {
                    $automatismes_pool[] = $autos_du_theme[array_rand($autos_du_theme)];
                }
            }
        }
        
        // Mélanger pour ordre aléatoire
        shuffle($automatismes_pool);
    }
    
    // Générer les 9 questions avec cette répartition
    $questions_pool = [];
    for ($i = 0; $i < 9; $i++) {
        $automatisme = $automatismes_pool[$i];
        $question = generer_question($automatisme);

        // SÉCURITÉ : difficulte_id et type toujours présents
        if (!isset($question['difficulte_id'])) {
            $question['difficulte_id'] = 1.5;
        }
        if (!isset($question['type'])) {
            $question['type'] = $automatisme;
        }
        $question['auto_key'] = $automatisme; // pour la difficulté de base + barème

        $questions_pool[] = $question;
    }

    // Tri par (format ouvert prioritaire, difficulté) + barème 6×0,5 + 3×1
    // Logique unique partagée avec le PDF (voir qf_dnb_config.php)
    $questions_pool = dnb_attribuer_baremes($questions_pool);

    // Sauvegarder dans la session
    $_SESSION['dnb_questions_pool'] = $questions_pool;
}

// Récupérer la question actuelle
$question_data = $_SESSION['dnb_questions_pool'][$_SESSION['dnb_questions_faites']];

// ========== CALCUL TIMER ==========
$elapsed = time() - $_SESSION['dnb_start_time'];
$remaining = $_SESSION['dnb_timer_total'] - $elapsed;
if ($remaining < 0) $remaining = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Questions Flash DNB - Automatismes</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
}

/* CSS pour les angles avec chapeau */
.angle {
    display: inline-block;
    text-align: center;
    font-family: monospace;
    font-size: 117%;
    line-height: 1;
    margin: 0 0em;
}

.hat {
    display: block;
    font-size: 0.7em;
    transform: scaleX(3.5);
    margin-left: 0.1em;
    margin-bottom: -0.6em;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    background: white;
    border-radius: 15px;
    padding: 0;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    min-height: 90vh;
    display: flex;
    flex-direction: column;
}

.header {
    background: #f8f9fa;
    border-bottom: 2px solid #667eea;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 15px 15px 0 0;
}

.header-left {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.header-center {
    font-size: 24px;
    font-weight: bold;
    color: #667eea;
}

.header-right {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.question-box {
    background: white;
    padding: 60px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow-y: auto;
}

.question-box h2 {
    display: none;
}

.question-box > *:first-child {
    font-size: 26px;
    line-height: 1.8;
    color: #333;
}

.question-box p {
    font-size: 24px;
    line-height: 1.8;
    color: #333;
    margin-bottom: 20px;
}

.answer-inline {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 3px solid #28a745;
}

.answer-inline h3 {
    color: #28a745;
    font-size: 26px;
    margin-bottom: 20px;
}

.answer-inline p {
    font-size: 24px;
    line-height: 1.8;
    color: #155724;
    font-weight: bold;
}

.button-container {
    display: flex;
    gap: 20px;
    justify-content: center;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 0 0 15px 15px;
    border-top: 2px solid #e0e0e0;
}

.btn {
    padding: 18px 35px;
    border: none;
    border-radius: 10px;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-show {
    background: #ffc107;
    color: #333;
}

.btn-show:hover {
    background: #ffb300;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
}

.btn-next {
    background: #28a745;
    color: white;
}

.btn-next:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.btn-quit {
    background: #dc3545;
    color: white;
}

.btn-quit:hover {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="header-left">Automatismes DNB</div>
        <div class="header-center" id="count-down-timer">⏱️ <?php echo gmdate("i:s", $remaining); ?></div>
        <div class="header-right">Question <?php echo $_SESSION['dnb_questions_faites'] + 1; ?> / <?php echo $_SESSION['dnb_nb_questions']; ?></div>
    </div>
    
    <div class="question-box">
        <?php echo $question_data['question']; ?>
        
        <div class="answer-inline" id="answer-box" style="display:none;">
            <h3>✅ Réponse (<?php echo str_replace('.', ',', isset($question_data['bareme']) ? $question_data['bareme'] : '0.5'); ?> point<?php echo (isset($question_data['bareme']) && $question_data['bareme'] > 1) ? 's' : ''; ?>)</h3>
            <?php echo $question_data['reponse']; ?>
        </div>
    </div>
    
    <div class="button-container">
        <button class="btn btn-show" onclick="document.getElementById('answer-box').style.display='block'; this.disabled=true; this.style.opacity='0.5';">
            👁️ Afficher la réponse
        </button>
        
        <form method="POST" action="qf_dnb_session.php" style="display:inline;">
            <button type="submit" class="btn btn-next">
                ➡️ Question suivante
            </button>
        </form>
        
        <button class="btn btn-quit" onclick="if(confirm('Voulez-vous vraiment quitter la session ?')) { window.location.href='qf_dnb_fin_session.php'; }">
            🚪 Quitter la session
        </button>
    </div>
</div>

<script>
function paddedFormat(num) {
    return num < 10 ? "0" + num : num;
}

function startCountDown(duration, element) {
    let timer = duration;
    let minutes, seconds;
    
    setInterval(function () {
        minutes = Math.floor(timer / 60);
        seconds = timer % 60;
        
        element.textContent = "⏱️ " + paddedFormat(minutes) + ":" + paddedFormat(seconds);
        
        if (--timer < 0) {
            window.location.href = 'qf_dnb_resultats.php';
        }
    }, 1000);
}

window.onload = function() {
    let duration = <?php echo $remaining; ?>;
    let element = document.querySelector('#count-down-timer');
    startCountDown(duration, element);
};
</script>

</body>
</html>
