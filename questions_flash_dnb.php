<?php
/**
 * Page de sélection des automatismes DNB.
 *
 * Point d'entrée du générateur : l'utilisateur coche les automatismes à
 * travailler, puis lance soit une session interactive (qf_dnb_session.php),
 * soit la génération d'une fiche imprimable (qf_dnb_print.php).
 */
session_start();

require __DIR__ . '/config.php';
require __DIR__ . '/qf_dnb_config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo htmlspecialchars(DNB_SITE_NAME, ENT_QUOTES, 'UTF-8'); ?> — Automatismes</title>
<meta name="description" content="Entraînement aux automatismes du DNB : 44 automatismes, session de 9 questions en 20 minutes, fiche PDF imprimable avec corrigé.">

<link rel="stylesheet" href="assets/dnb.css">

<style>
.selection-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
}
.theme-section {
    background-color: #f8f9fa;
    border: 2px solid #3498db;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}
.theme-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #3498db;
}
.theme-header h2 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.5em;
}
.toggle-all-btn {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9em;
}
.toggle-all-btn:hover {
    background-color: #2980b9;
}
.automatismes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 10px;
}
.automatisme-item {
    display: flex;
    align-items: center;
    padding: 12px;
    background: white;
    border-radius: 5px;
    border: 1px solid #ddd;
}
.automatisme-item.ready:hover {
    background: #f0f8ff;
}
.automatisme-item.disabled {
    background: #f5f5f5;
}
.automatisme-item input[type="checkbox"] {
    width: 20px;
    height: 20px;
    margin-right: 10px;
}
.automatisme-item.ready input[type="checkbox"] {
    cursor: pointer;
}
.automatisme-item label {
    flex-grow: 1;
    margin: 0;
}
.automatisme-item.ready label {
    cursor: pointer;
    color: #333;
}
.automatisme-item.disabled label {
    color: #999;
    cursor: not-allowed;
}
.automatisme-item.disabled label::after {
    content: " (bientôt disponible)";
    font-style: italic;
    font-size: 0.85em;
    color: #666;
}
.config-section {
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
}
.config-section h3 {
    margin-top: 0;
    color: #856404;
}
.dnb-format-info {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}
.format-item {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 200px;
}
.format-icon {
    font-size: 2em;
}
.launch-section {
    text-align: center;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid #ddd;
}
.launch-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 15px 40px;
    font-size: 18px;
    font-weight: bold;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.launch-btn:hover:not(:disabled) {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}
.launch-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}
.print-btn {
    background: #003366;
    color: white;
    border: none;
    padding: 15px 30px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-left: 15px;
}
.print-btn:hover:not(:disabled) {
    background: #004d99;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
}
.print-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}
.info-box {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 5px;
}
.info-box p {
    margin: 10px 0;
}
#selected-count {
    color: #2196f3;
    font-weight: bold;
    font-size: 1.2em;
}
.master-controls {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    background: #eef6ff;
    border: 2px solid #2196f3;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 20px;
}
.master-label {
    font-weight: bold;
    color: #0d47a1;
}
.master-btn {
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.95em;
    font-weight: bold;
    color: #fff;
    transition: background 0.2s ease;
}
.master-all { background: #28a745; }
.master-all:hover { background: #218838; }
.master-none { background: #6c757d; }
.master-none:hover { background: #565e64; }
.master-count {
    margin-left: auto;
    color: #0d47a1;
    font-weight: bold;
}
.master-count #selected-count-inline { font-size: 1.1em; }
</style>

<script>
function toggleTheme(themeClass) {
    let checkboxes = document.querySelectorAll('.' + themeClass + ':not(:disabled)');
    let allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    updateLaunchButton();
}

// Sélectionne (state=true) ou désélectionne (state=false) TOUS les automatismes
function setAll(state) {
    document.querySelectorAll('input[name="auto[]"]:not(:disabled)').forEach(cb => cb.checked = state);
    updateLaunchButton();
}

function validateForm() {
    let checked = document.querySelectorAll('input[name="auto[]"]:checked');
    if (checked.length === 0) {
        alert('⚠️ Veuillez sélectionner au moins un automatisme !');
        return false;
    }
    return true;
}

function printSelection() {
    // Construire un formulaire dédié (ne pas muter le formulaire principal :
    // le swap action/target + reset provoquait un envoi instable « 1 fois sur 2 »)
    let checked = document.querySelectorAll('#dnb-form input[name="auto[]"]:checked');
    if (checked.length === 0) {
        alert('⚠️ Veuillez sélectionner au moins un automatisme !');
        return;
    }
    let temp = document.createElement('form');
    temp.method = 'POST';
    temp.action = 'qf_dnb_print.php';
    temp.target = '_blank';
    temp.style.display = 'none';
    checked.forEach(cb => {
        let inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'auto[]';
        inp.value = cb.value;
        temp.appendChild(inp);
    });
    document.body.appendChild(temp);
    temp.submit();
    document.body.removeChild(temp);
}

window.onload = function() {
    document.querySelectorAll('input[name="auto[]"]').forEach(cb => {
        cb.addEventListener('change', updateLaunchButton);
    });
    updateLaunchButton();
};

function updateLaunchButton() {
    let checkedBoxes = document.querySelectorAll('input[name="auto[]"]:checked');
    let totalBoxes = document.querySelectorAll('input[name="auto[]"]:not(:disabled)');
    let launchBtn = document.getElementById('launch-btn');
    let printBtn = document.getElementById('print-btn');
    let countSpan = document.getElementById('selected-count');

    let countText = checkedBoxes.length + ' / ' + totalBoxes.length;
    countSpan.textContent = countText;
    let inlineSpan = document.getElementById('selected-count-inline');
    if (inlineSpan) inlineSpan.textContent = countText;

    if (checkedBoxes.length === 0) {
        launchBtn.disabled = true;
        launchBtn.textContent = "Sélectionnez au moins un automatisme";
        printBtn.disabled = true;
    } else {
        launchBtn.disabled = false;
        launchBtn.textContent = `🚀 Démarrer la session DNB (${checkedBoxes.length} automatisme(s))`;
        printBtn.disabled = false;
    }
}
</script>
</head>
<body>

<h1 id="titre"><?php echo htmlspecialchars(DNB_SITE_NAME, ENT_QUOTES, 'UTF-8'); ?> — Automatismes</h1>

<div id="contenu">

<div class="info-box">
    <h3>📋 À propos des automatismes DNB</h3>
    <p>Cette page permet de s'entraîner sur les <strong>automatismes susceptibles d'être mobilisés lors de l'épreuve écrite de mathématiques du DNB</strong> (séries générale et professionnelle).</p>
    <p><strong><?php echo htmlspecialchars(DNB_PROGRAMME, ENT_QUOTES, 'UTF-8'); ?></strong></p>
    <p>Sélectionnez les automatismes à travailler, puis lancez l'entraînement ou générez une fiche imprimable.</p>
    <p><strong>Automatismes sélectionnés : <span id="selected-count">0</span></strong></p>
</div>

<form method="POST" action="qf_dnb_session.php" id="dnb-form" onsubmit="return validateForm();">

<!-- CONFIGURATION -->
<div class="config-section">
    <h3>⚙️ Format DNB</h3>
    <div class="dnb-format-info">
        <div class="format-item">
            <span class="format-icon">📝</span>
            <div>
                <strong>9 questions</strong><br>
                <small>Format officiel</small>
            </div>
        </div>
        <div class="format-item">
            <span class="format-icon">⏱️</span>
            <div>
                <strong>20 minutes</strong><br>
                <small>Durée totale</small>
            </div>
        </div>
        <div class="format-item">
            <span class="format-icon">🚫</span>
            <div>
                <strong>Sans calculatrice</strong><br>
                <small>Calcul mental</small>
            </div>
        </div>
    </div>
</div>

<!-- CONTRÔLES GLOBAUX DE SÉLECTION -->
<div class="master-controls">
    <span class="master-label">Sélection rapide :</span>
    <button type="button" class="master-btn master-all" onclick="setAll(true)">✅ Tout sélectionner</button>
    <button type="button" class="master-btn master-none" onclick="setAll(false)">✖️ Tout désélectionner</button>
    <span class="master-count"><span id="selected-count-inline">0 / 0</span> automatismes</span>
</div>

<!-- AUTOMATISMES PAR THÈMES -->
<?php foreach ($automatismes_config as $theme_key => $theme): ?>
<div class="theme-section">
    <div class="theme-header">
        <h2><?php echo $theme['titre']; ?></h2>
        <button type="button" class="toggle-all-btn" onclick="toggleTheme('theme-<?php echo $theme_key; ?>')">
            ☑️ Tout sélectionner / désélectionner
        </button>
    </div>
    <div class="automatismes-grid">
        <?php foreach ($theme['items'] as $key => $auto): ?>
        <div class="automatisme-item <?php echo $auto['ready'] ? 'ready' : 'disabled'; ?>">
            <input
                type="checkbox"
                name="auto[]"
                value="<?php echo $key; ?>"
                id="auto-<?php echo $key; ?>"
                class="theme-<?php echo $theme_key; ?>"
                <?php echo $auto['ready'] ? '' : 'disabled'; ?>
            >
            <label for="auto-<?php echo $key; ?>"><?php echo $auto['nom']; ?></label>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<!-- BOUTONS -->
<div class="launch-section">
    <button type="submit" class="launch-btn" id="launch-btn" disabled>
        Sélectionnez au moins un automatisme
    </button>
    <button type="button" class="print-btn" id="print-btn" disabled onclick="printSelection()">
        🖨️ Générer PDF / Imprimer
    </button>
</div>

</form>

</div>

<div id="footer">
    <p>Générateur d'automatismes DNB — logiciel libre (AGPL-3.0).</p>
</div>

</body>
</html>
