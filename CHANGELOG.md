# Journal des versions

Historique repris de la page en production
(<https://site2wouf.fr/questions_flash_dnb.php>), dont ce dépôt est l'export.

## 1.3.0 — 19 septembre 2026

- **Nouveaux automatismes** (44 → 46) : « Image et antécédent » (notation f(x), lecture
  dans un tableau et sur un graphique) et « Calcul astucieux » (regrouper des termes ou
  des facteurs, développer, factoriser, et les mêmes astuces sur les relatifs, les
  fractions et les puissances de 10).
- **Générateurs enrichis** depuis la version 1.2.1, tous au bénéfice de la session et de
  la fiche imprimable : angles formés par deux parallèles et une sécante (correspondants
  et alternes-internes, revenus au programme) ; pyramide, cône et boule ajoutés aux
  volumes ; symétrie d'axe (Oy) et coordonnées de l'image d'un point, qui étaient codées
  en dur ; six sous-types autour du signe « moins » pour développer et factoriser ;
  trois sous-types de pourcentages d'évolution (retrouver le taux, remonter à la valeur
  initiale, deux évolutions successives) ; tables et calcul mental, qui étaient des
  ébauches, entièrement réécrits ; critères de divisibilité appliqués au nombre plutôt
  que répondus par vrai ou faux.
- **Corrections** : deux factorisations incomplètes acceptaient deux réponses justes
  (`3a² + 3a`, `4b² − 4`) ; la médiane ne tirait jamais d'effectif pair ; l'automatisme
  « Probabilités » dépendait d'une fonction définie dans un autre fichier et plantait
  hors session complète ; accords de genre et de nombre dans plusieurs énoncés.
- **Affichage** : les 100 figures d'aires ont reçu un `viewBox`, sans lequel elles
  étaient rognées sur téléphone ; le repère des questions « image / antécédent » est
  plafonné sur la fiche imprimable, où il occupait toute la largeur.

## 1.2.1 — 5 août 2026

- Préparation de l'archivage et de la citation du logiciel : ajout de `CITATION.cff`
  (auteur, licence, projet), compatible avec la fonction de citation de GitHub et
  l'intégration Zenodo. Le fonctionnement de l'application est inchangé.

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
