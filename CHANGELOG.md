# Journal des versions

Historique repris de la page en production
(<https://site2wouf.fr/questions_flash_dnb.php>), dont ce dépôt est l'export.

## 1.2.0 — 12 juillet 2026

- **Nouveaux automatismes** (37 → 44) : puissances et ordre de grandeur, programme de
  calcul, étendue d'une série, grandeurs composées (vitesse, distance, durée, débit).
  Les automatismes « Tables de multiplication », « Calcul mental » et « Priorités
  opératoires » deviennent également sélectionnables.
- **Distribution des points repensée** : les 3 questions à 1 point sont en priorité des
  questions à réponse ouverte — une question Vrai/Faux ou un QCM, plus faciles à deviner,
  ne sont plus surévalués — puis les plus difficiles. Le total réel de la feuille
  (6 points = 6 × 0,5 + 3 × 1) est désormais affiché correctement.
- **Sélection simplifiée** : boutons « Tout sélectionner » / « Tout désélectionner »
  globaux et compteur du nombre d'automatismes choisis.
- **Corrections** : plantage possible de l'automatisme « Algorithmique » (figures
  Scratch) ; erreur d'affichage et médiane parfois incorrecte sur l'automatisme
  « Médiane » ; échec de la génération de la fiche imprimable lorsque « Thalès » était
  sélectionné.

## 1.1.2 — 18 juin 2026

- L'automatisme « Fractions simples ⇄ Décimaux » demande désormais explicitement une
  *fraction irréductible* (et non plus une « fraction simple »).
- L'automatisme « Symétries » (coordonnées de l'image d'un point) pouvait proposer deux
  distracteurs identiques pour la symétrie d'axe Ox ; les quatre propositions sont
  désormais toujours distinctes.

## 1.1.1 — 16 juin 2026

- Correction de l'automatisme « Ranger des décimaux » : le tirage pouvait produire des
  valeurs en double, donnant un corrigé incorrect (ex. 3,2 < 9 < 9,3 < 9,3). Les
  4 nombres tirés sont désormais toujours distincts.

## 1.1.0 — 10 juin 2026

- Génération d'une fiche d'entraînement imprimable : même sélection d'automatismes que la
  session, format de l'épreuve DNB (9 questions avec barème), corrigé en page 2. Bouton
  « Générer PDF / Imprimer » ajouté à la page de sélection.

## 1.0.0 beta — 29 décembre 2025

- Première version : sélection des automatismes par thème, session de 9 questions en
  20 minutes avec minuteur.

---

## Notes sur l'export

Ce dépôt est un extrait autonome de la page en production. Par rapport à celle-ci :

- l'habillage du site d'origine a été retiré (mesure d'audience, publicité, chat,
  boutons de partage, citations, pied de page, métadonnées personnelles) et remplacé par
  `assets/dnb.css` + `config.php` ;
- deux générateurs inutilisés ont été écartés (`functions/fraction_pourcentages.php`,
  `functions/thales_avec_json.php`) ;
- `generators/generate_thales.py` a été corrigé pour écrire les fichiers
  `reponse_XXX.html` attendus par `functions/thales.php`, et les trois générateurs
  résolvent désormais leur dossier de sortie relativement au script.
