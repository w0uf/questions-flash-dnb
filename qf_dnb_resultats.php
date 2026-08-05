<?php
session_start();

if (!isset($_SESSION['dnb_active'])) {
    header('Location: questions_flash_dnb.php');
    exit;
}

$nb_questions = $_SESSION['dnb_nb_questions'];
$automatismes = $_SESSION['dnb_automatismes'];

// Détruire la session
session_destroy();
?><!DOCTYPE html>
<html lang="fr">
<head>
<title>Fin de session — Automatismes DNB</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="assets/dnb.css">

<style>
.results-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 30px;
    text-align: center;
}
.results-box {
    background-color: #d4edda;
    border: 3px solid #28a745;
    border-radius: 15px;
    padding: 40px;
    margin-bottom: 30px;
}
.results-box h2 {
    color: #155724;
    font-size: 2.5em;
    margin-bottom: 20px;
}
.stats {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin: 20px 0;
}
.stats p {
    font-size: 1.3em;
    margin: 10px 0;
}
.button-container {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 30px;
}
.btn {
    padding: 15px 30px;
    font-size: 1.2em;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
}
.btn-primary {
    background-color: #3498db;
    color: white;
}
.btn-primary:hover {
    background-color: #2980b9;
}
.btn-success {
    background-color: #28a745;
    color: white;
}
.btn-success:hover {
    background-color: #218838;
}
</style>
</head>
<body>

<h1 id="titre">Fin de session — Automatismes DNB</h1>

<div class="results-container">

<div class="results-box">
    <h2>🎉 Session terminée !</h2>
    
    <div class="stats">
        <p>📊 <strong><?php echo $nb_questions; ?> questions</strong> effectuées</p>
        <p>🎯 <strong><?php echo count($automatismes); ?> automatisme(s)</strong> travaillé(s)</p>
    </div>
    
    <p style="font-size:1.2em; color:#666; margin-top:20px;">
        Bravo pour votre entraînement ! 💪<br>
        Continuez à vous entraîner régulièrement pour maîtriser tous les automatismes du DNB.
    </p>
</div>

<div class="button-container">
    <a href="questions_flash_dnb.php" class="btn btn-success">
        🔄 Nouvelle session
    </a>
</div>

</div>

<div id="footer">
    <p>Générateur d'automatismes DNB — logiciel libre (AGPL-3.0).</p>
</div>

</body>
</html>
