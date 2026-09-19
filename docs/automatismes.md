# Automatismes disponibles

Liste générée depuis `qf_dnb_config.php`.

La colonne *difficulté* est la difficulté de base servant de clé de tri entre
automatismes lors de l’attribution du barème (échelle 1,0 très facile → 2,6 difficile) ;
elle se règle dans la table `$dnb_difficulte_base` de `qf_dnb_config.php`.

La clé est la valeur envoyée par la case à cocher du formulaire. Le générateur
correspondant vit dans `functions/` — le nom du fichier suit la clé, à quelques
exceptions près (`codage_figure` → `codage_figures.php`, `frequences` →
`frequence.php`, `lire_tableaux` → `graphiques.php`) ; le tableau de correspondance
exact est la fonction `generer_question()` de `qf_dnb_session.php`.

## 🔢 Nombres et Calculs

| Clé | Automatisme | Difficulté |
| --- | --- | --- |
| `tables` | Tables de multiplication | 1,0 |
| `calcul_mental` | Calcul mental (additions, soustractions) | 1,1 |
| `priorites` | Priorités opératoires (×, ÷ avant +, −) | 1,4 |
| `calcul_astucieux` | Calcul astucieux (regrouper, développer, factoriser) | 1,6 |
| `carres` | Carrés de 1 à 12 | 1,2 |
| `fractions_decimales` | Fractions simples ⇄ Décimaux (1/2, 1/4, 3/4...) | 1,1 |
| `comparer_decimaux` | Comparer et calculer avec décimaux (y compris négatifs) | 1,0 |
| `calculer_fractions` | Simplifier, comparer, calculer avec fractions (et conversions fraction ⇄ pourcentage) | 2,0 |
| `pourcentages` | Pourcentages simples (100%, 50%, 25%, 10%, 1%) | 1,3 |
| `ecritures_multiples` | Écritures multiples d'un nombre (1,2 = 12/10 = 6/5...) | 1,6 |
| `notation_scientifique` | Notation scientifique | 1,8 |
| `puissances` | Puissances et ordre de grandeur | 1,7 |
| `divisibilite` | Critères de divisibilité (2, 3, 5, 9) | 1,2 |
| `operations_n` | Double, triple, moitié, prédécesseur, successeur, carré | 1,1 |
| `programme_calcul` | Programme de calcul (appliquer, retrouver le départ) | 1,7 |
| `expressions_litterales` | Simplifier expressions littérales | 1,5 |
| `valeur_expression` | Calculer valeur expression algébrique (avec puissances) | 1,6 |
| `developper_factoriser` | Développer et factoriser expression simple | 1,8 |
| `equations` | Résoudre ax=c, x+b=c, ax+b=c | 1,6 |
| `droite_graduee` | Lire abscisse et placer point sur droite graduée | 1,2 |

## 📐 Espace et Géométrie

| Clé | Automatisme | Difficulté |
| --- | --- | --- |
| `repere_orthogonal` | Lire et placer coordonnées dans repère | 1,2 |
| `codage_figure` | Identifier triangles, quadrilatères, médiatrice | 1,3 |
| `angles` | Reconnaître angles (opposés, adjacents, supplémentaires...) | 1,2 |
| `angles_triangle` | Somme angles triangle = 180° | 1,4 |
| `conversions` | Conversions unités (mm, cm, m, km, L, g...) | 1,5 |
| `solides` | Reconnaître solides (cube, pavé, prisme, cylindre...) | 1,1 |
| `perimetre` | Périmètre polygone et disque | 1,6 |
| `aires` | Aires (triangle, rectangle, disque) | 1,7 |
| `volumes` | Volumes (cube, pavé, prisme, cylindre) | 2,4 |
| `pythagore` | Théorème de Pythagore | 2,5 |
| `thales` | Théorème de Thalès | 2,4 |
| `cosinus` | Cosinus (rapports de longueurs) | 2,6 |
| `transformations` | Symétries (axiale, centrale), translation | 1,9 |

## 🎲 Probabilités et Statistiques

| Clé | Automatisme | Difficulté |
| --- | --- | --- |
| `probabilites` | Probabilités simples (équiprobabilité) | 1,5 |
| `frequences` | Exprimer fréquence simple | 1,5 |
| `moyenne` | Exprimer moyenne | 1,6 |
| `mediane` | Déterminer médiane (petite série) | 1,7 |
| `etendue` | Déterminer l'étendue d'une série | 1,3 |
| `lire_tableaux` | Lire tableaux, diagrammes, graphiques | 1,3 |

## 📊 Proportionnalité et Fonctions

| Clé | Automatisme | Difficulté |
| --- | --- | --- |
| `reconnaitre_proportionnalite` | Reconnaître si situation donnée est proportionnelle ou non | 1,4 |
| `procedures_proportionnalite` | Mobiliser procédure adaptée (linéarité, retour à l'unité) | 1,7 |
| `grandeurs_composees` | Grandeurs composées (vitesse, distance, durée, débit) | 1,9 |
| `pourcentages_augmentation` | Appliquer augmentation ou diminution en pourcentage | 1,7 |
| `lire_graphique_fonctions` | Exploiter graphique (lire valeurs sur axes) | 1,5 |
| `image_antecedent` | Image et antécédent (notation f(x), tableau, graphique) | 1,6 |

## 💻 Algorithmique et Programmation

| Clé | Automatisme | Difficulté |
| --- | --- | --- |
| `algorithmique` | Interpréter suite d'instructions (calcul, déplacement, construction) | 1,6 |

**Total : 46 automatismes** répartis en 5 thèmes.
