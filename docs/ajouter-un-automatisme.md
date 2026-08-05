# Ajouter un automatisme

Un automatisme = un fichier PHP dans `functions/` + trois lignes ailleurs. Aucune base
de données, aucun enregistrement à faire : tout se branche par la configuration.

## 1. Écrire le générateur

Créer `functions/mon_automatisme.php` avec une fonction qui renvoie **un tableau** :

```php
<?php
function generer_mon_automatisme() {
    $a = rand(2, 9);
    $b = rand(2, 9);

    return [
        'question'      => "<p>Calculer $a × $b.</p>",
        'reponse'       => '<p><strong>' . ($a * $b) . '</strong></p>',
        'type'          => 'mon_automatisme',
        'difficulte_id' => 1.0,
    ];
}
```

Clés attendues :

| Clé | Rôle |
| --- | --- |
| `question` | HTML de l'énoncé. SVG inline pour une figure, `frac_html($a, $b)` (voir `functions/utils.php`) pour une fraction. |
| `reponse` | HTML de la réponse attendue, affichée en session et dans le corrigé. |
| `type` | Clé de l'automatisme, utile au débogage. |
| `difficulte_id` | Départage **à l'intérieur** de l'automatisme (une variante dure = valeur plus haute). La difficulté *entre* automatismes vient de `$dnb_difficulte_base`. |
| `format` | Facultatif : `'ouvert'`, `'qcm'` ou `'vf'`. Sans lui, le format est deviné à partir du HTML (`dnb_detecter_format()`). Le renseigner évite toute ambiguïté. |

Deux règles apprises à l'usage :

- **Toujours préciser l'unité attendue** dans l'énoncé (vitesse, distance, durée, débit,
  étendue…), sans quoi une réponse juste peut être écrite sous une forme imprévue.
- **Vérifier l'unicité des valeurs tirées** quand la question porte sur un rangement, une
  comparaison ou un choix de distracteurs : deux valeurs identiques rendent le corrigé faux.

Pour éviter les répétitions d'une question à l'autre au sein d'une même session, deux
approches sont utilisées dans le dépôt : un *pool* mélangé en session
(`$_SESSION['<type>_pool']`, voir `functions/calculer_fractions.php`) ou
`get_unique_question_num()` de `functions/utils.php` pour les corpus numérotés.

## 2. Déclarer l'automatisme

Dans `qf_dnb_config.php` :

```php
// 1) dans $automatismes_config, sous le thème qui convient
'mon_automatisme' => ['nom' => 'Libellé affiché à l’élève', 'ready' => true],

// 2) dans $dnb_difficulte_base, sa difficulté de base (1,0 → 2,6)
'mon_automatisme' => 1.4,
```

`'ready' => false` affiche l'automatisme grisé, marqué « bientôt disponible ».

## 3. Le brancher

Ajouter le `require_once` et le `case` dans **les deux** points d'entrée —
`qf_dnb_session.php` (fonction `generer_question()`) et `qf_dnb_print.php`
(fonction `generer_question_print()`) :

```php
require_once('functions/mon_automatisme.php');
// …
case 'mon_automatisme':  return generer_mon_automatisme();
```

Oublier `qf_dnb_print.php` est l'erreur classique : la session marche, la fiche
imprimable retombe sur l'automatisme par défaut.

## 4. Vérifier

```bash
php -l functions/mon_automatisme.php
php -S localhost:8000
```

Puis, en ne cochant que le nouvel automatisme, générer plusieurs fiches d'affilée :
les 9 questions viennent alors toutes de votre générateur, ce qui met vite en évidence
les tirages dégénérés, les doublons et les corrigés faux.

## Corpus pré-générés

Les automatismes à figures (Pythagore, Thalès, aires) ne calculent pas leur énoncé : ils
tirent un fichier dans `includes/qf_<automatisme>/` (100 paires
`question_XXX.html` / `reponse_XXX.html`). C'est le bon choix quand la figure demande un
placement soigné. Les scripts de `generators/` produisent ces corpus ; voir le README.
