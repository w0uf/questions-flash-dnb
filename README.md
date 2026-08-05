# Questions Flash DNB — générateur d'automatismes

Générateur d'**automatismes de mathématiques pour le DNB** (Diplôme national du brevet,
séries générale et professionnelle), conforme au format de l'épreuve :
**9 questions, 20 minutes, sans calculatrice**.

Deux usages à partir d'une même sélection d'automatismes :

- **session interactive** — une question à la fois, minuteur de 20 minutes, écran de fin ;
- **fiche imprimable** — les 9 questions avec barème et zone de réponse, **corrigé en page 2**,
  à imprimer ou à enregistrer en PDF depuis le navigateur.

Les questions sont **générées aléatoirement à chaque tirage** : deux élèves n'obtiennent
pas la même feuille, et un même élève peut s'entraîner indéfiniment. **44 automatismes**
sont disponibles, répartis en 5 thèmes (nombres et calculs, espace et géométrie,
probabilités et statistiques, proportionnalité et fonctions, algorithmique).

Version en production : <https://site2wouf.fr/questions_flash_dnb.php>

## Installation

Aucune dépendance : ni base de données, ni bibliothèque tierce, ni compte, ni clé d'API.
Il suffit d'un PHP 8 avec les sessions activées.

```bash
git clone <url-du-depot> questions-flash-dnb
cd questions-flash-dnb
php -S localhost:8000
```

Puis ouvrir <http://localhost:8000/questions_flash_dnb.php>.

Pour une mise en ligne : déposer le dossier dans la racine web (ou un sous-dossier)
d'un hébergement PHP classique. Aucune écriture disque n'est nécessaire ;
l'état d'une session d'entraînement vit uniquement dans `$_SESSION`.

### Personnalisation

Tout est dans [`config.php`](config.php) : nom affiché, URL publique, logo de la fiche
imprimable, intitulé du programme de référence. L'habillage des pages se limite à
[`assets/dnb.css`](assets/dnb.css), volontairement sobre et sans police ni CDN distant :
remplacez-le par votre feuille de style si vous intégrez le générateur à un site existant.

Pour retirer ou ajouter un automatisme dans l'interface, éditer `$automatismes_config`
dans [`qf_dnb_config.php`](qf_dnb_config.php) (mettre `'ready' => false` masque un
automatisme sans supprimer son générateur).

## Organisation du dépôt

```
questions_flash_dnb.php   Page d'accueil : sélection des automatismes (point d'entrée)
qf_dnb_config.php         Configuration partagée : liste des automatismes, difficultés, barème
qf_dnb_session.php        Session interactive : tirage des 9 questions, minuteur
qf_dnb_resultats.php      Écran de fin de session
qf_dnb_fin_session.php    Abandon de session (destruction de $_SESSION)
qf_dnb_print.php          Fiche imprimable (questions + corrigé page 2)
config.php                Réglages de l'instance (nom, logo, URL)
assets/dnb.css            Habillage minimal des pages

functions/                Un fichier par automatisme : generer_xxx() renvoie une question
functions/utils.php       Fractions HTML, tirage sans répétition

includes/qf_pythagore/    Corpus pré-générés : 100 questions + 100 réponses en HTML/SVG
includes/qf_thales/       (les trois automatismes à figures utilisent un corpus plutôt
includes/qf_aires/         qu'une génération à la volée)

generators/               Scripts Python 3 ayant produit les corpus de includes/
difficultes.json          Documentation de l'échelle de difficulté
```

### Comment une question est produite

Chaque fichier de `functions/` expose une fonction `generer_<automatisme>()` qui renvoie
un tableau :

```php
[
    'question'      => '<p>Calculer 7 × 8</p>',  // HTML (SVG inline pour les figures)
    'reponse'       => '<p><strong>56</strong></p>',
    'type'          => 'tables',
    'difficulte_id' => 1.0,   // départage à l'intérieur d'un même automatisme
    'format'        => 'ouvert',  // facultatif : 'ouvert' | 'qcm' | 'vf'
]
```

`qf_dnb_session.php` et `qf_dnb_print.php` partagent la même logique : répartition des
9 questions entre les automatismes cochés (pondérée par thème au-delà de 9 automatismes),
puis attribution du barème par `dnb_attribuer_baremes()` (`qf_dnb_config.php`).

**Règle du barème** : 3 questions à 1 point et 6 à 0,5 point, soit 6 points. Les questions
à 1 point sont en priorité des questions **à réponse ouverte** — une question Vrai/Faux ou
un QCM, devinables au hasard, ne sont surévalués que s'il n'y a pas assez de questions
ouvertes dans le tirage. Le format est détecté à l'exécution (`dnb_detecter_format()`),
la difficulté vient de la table `$dnb_difficulte_base`.

### Régénérer les corpus de figures

```bash
python3 generators/generate_all.py   # ou un générateur à la fois
```

Les scripts écrivent dans `includes/qf_<automatisme>/` (chemins relatifs au script,
donc lançables depuis n'importe où). Ils ne dépendent que de la bibliothèque standard.

Deux réserves d'honnêteté sur ce dossier :

- `generate_pythagore.py` et `generate_aires.py` reproduisent bien les corpus livrés ;
- `generate_thales.py` est une **version antérieure** du générateur Thalès : le corpus
  `includes/qf_thales/` livré ici a été produit par un script plus récent qui n'a pas été
  conservé. Le script fourni produit des questions Thalès valides et exploitables par
  `functions/thales.php`, mais d'une autre facture (énoncés sans figure SVG).
  Le relancer **écrase** le corpus actuel.

## Dépendances externes

**Aucune.** Le générateur ne charge ni CDN, ni police distante, ni bibliothèque
JavaScript, ni moteur de rendu PDF :

| Besoin | Solution retenue |
| --- | --- |
| Rendu mathématique | HTML/CSS à la main (`frac_html()` dans `functions/utils.php`) — pas de MathJax ni KaTeX |
| Figures géométriques | SVG écrit à la main ou généré en Python, inclus dans le HTML |
| PDF | Impression navigateur (CSS `@media print`, `page-break-before`) — pas de TCPDF/FPDF/wkhtmltopdf |
| Interactivité | JavaScript natif, sans framework |
| Stockage | Sessions PHP uniquement — pas de base de données |
| Serveur | PHP 8 (testé sur 8.4), aucune extension particulière |

Côté client, tout fonctionne sans JavaScript pour la fiche imprimable ; la page de
sélection utilise JS pour les cases à cocher et l'ouverture de la fiche.

## Vie privée

Aucune donnée personnelle n'est collectée, stockée ni transmise. Pas de compte, pas de
cookie de mesure d'audience, pas d'appel réseau vers un tiers. Le seul cookie est celui
de session PHP, qui contient l'avancement de l'entraînement en cours et disparaît à la
fin de la session.

Cette version d'export a été **purgée** de tout élément propre au site d'origine :
identifiants de régie publicitaire, mesure d'audience, chat, réseaux sociaux, dons,
métadonnées personnelles de l'auteur. Le dépôt ne contient aucun mot de passe, aucun
identifiant de base de données, aucune clé d'API ni chemin de configuration sensible —
le générateur n'en utilise aucun.

## Licence

[GNU AGPL v3](LICENSE) — vous pouvez utiliser, modifier et redistribuer ce code, y
compris pour un service en ligne, à condition d'en republier les sources modifiées.

Auteur : Laurent Petitprez (Wouf) — [site2wouf.fr](https://site2wouf.fr)

Les énoncés produits sont des exercices de mathématiques de collège ; ils s'appuient sur
le programme officiel d'octobre 2025 mais ne reprennent aucun sujet d'examen existant.
