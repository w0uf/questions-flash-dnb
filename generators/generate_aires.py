#!/usr/bin/env python3
"""
Générateur de questions flash Aires - DNB 2026 - VERSION 2
===========================================================
Génère 100 questions avec SVG sur :
- Triangles quelconques (40%) : normal, obtus gauche, obtus droite
- Rectangles (30%)
- Disques (20%) - aire exacte avec π
- Carrés (10%)

CONTRAINTES :
- Toutes dimensions ≤ 12
- Aires entières ou demi-entières
- Disques : réponse en forme exacte (ex: 25π cm²)
"""

import random
import os

OUTPUT_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "includes", "qf_aires")
os.makedirs(OUTPUT_DIR, exist_ok=True)


# ========================================
# SVG GENERATORS
# ========================================

def generer_svg_triangle(base, hauteur, cote_inutile, labels, type_triangle):
    """
    Génère SVG triangle quelconque avec hauteur tracée - FORMAT 600x600

    type_triangle:
    - 'normal': H sur [AB], 2 côtés inutiles
    - 'obtus_gauche': H à gauche de A, angle en A obtus, 1 côté inutile (BC)
    - 'obtus_droite': H à droite de B, angle en B obtus, 1 côté inutile (AC)
    """

    A, B, C = labels

    # Taille fixe du canvas
    canvas_size = 600
    margin_vertical = 50  # Marge réduite pour haut/bas
    margin_horizontal = 80  # Marge pour les labels gauche/droite

    # Zone disponible
    zone_h = canvas_size - 2 * margin_vertical
    zone_w = canvas_size - 2 * margin_horizontal

    if type_triangle == 'normal':
        # === TRIANGLE NORMAL : H sur [AB] ===
        # Calculer l'échelle optimale pour remplir l'espace
        scale = min(zone_w / base, zone_h / hauteur)

        base_px = int(base * scale)
        hauteur_px = int(hauteur * scale)

        # Centrer le triangle
        Ax = (canvas_size - base_px) / 2
        Ay = (canvas_size + hauteur_px) / 2 + 15
        Bx, By = Ax + base_px, Ay

        # C au-dessus, position aléatoire
        ratio_x = random.uniform(0.25, 0.75)
        Cx = Ax + base_px * ratio_x
        Cy = Ay - hauteur_px

        # H sur [AB]
        Hx = Cx
        Hy = Ay

        # Positionnement intelligent du label hauteur
        milieu_x = (Ax + Bx) / 2
        if Cx < milieu_x:
            hauteur_label_x = Hx + 15
        else:
            hauteur_label_x = Hx - 70

        # 2 côtés inutiles (générés aléatoirement)
        cote_gauche, cote_droit = cote_inutile  # tuple de 2 valeurs

        svg = f'''<svg width="{canvas_size}" height="{canvas_size}" xmlns="http://www.w3.org/2000/svg">
    <!-- Triangle {A}{B}{C} NORMAL -->
    <line x1="{Ax}" y1="{Ay}" x2="{Bx}" y2="{By}" stroke="black" stroke-width="3"/>
    <line x1="{Ax}" y1="{Ay}" x2="{Cx}" y2="{Cy}" stroke="black" stroke-width="3"/>
    <line x1="{Bx}" y1="{By}" x2="{Cx}" y2="{Cy}" stroke="black" stroke-width="3"/>

    <line x1="{Cx}" y1="{Cy}" x2="{Hx}" y2="{Hy}" stroke="red" stroke-width="2.5" stroke-dasharray="5,4"/>
    <rect x="{Hx}" y="{Hy - 15}" width="15" height="15" fill="none" stroke="red" stroke-width="2.5"/>

    <!-- Labels sommets -->
    <text x="{Ax - 20}" y="{Ay + 20}" font-size="20" fill="black" font-weight="bold">{A}</text>
    <text x="{Bx + 10}" y="{By + 20}" font-size="20" fill="black" font-weight="bold">{B}</text>
    <text x="{Cx - 8}" y="{Cy - 10}" font-size="20" fill="black" font-weight="bold">{C}</text>

    <!-- Labels dimensions (toutes en bleu) -->
    <text x="{(Ax + Bx) / 2}" y="{Ay + 35}" font-size="18" fill="blue" font-weight="bold" text-anchor="middle">{base} cm</text>
    <text x="{hauteur_label_x}" y="{(Cy + Hy) / 2 + 6}" font-size="18" fill="blue" font-weight="bold">{hauteur} cm</text>
    <text x="{(Ax + Cx) / 2 - 40}" y="{(Ay + Cy) / 2 + 6}" font-size="18" fill="blue" font-weight="bold">{cote_gauche} cm</text>
    <text x="{(Bx + Cx) / 2 + 15}" y="{(By + Cy) / 2 + 6}" font-size="18" fill="blue" font-weight="bold">{cote_droit} cm</text>
</svg>'''

    elif type_triangle == 'obtus_gauche':
        # === TRIANGLE OBTUS À GAUCHE : H à gauche de A ===
        # Pour les triangles obtus, la largeur totale inclut le prolongement
        offset_gauche = hauteur * 0.6
        largeur_totale = base + offset_gauche

        scale = min(zone_w / largeur_totale, zone_h / hauteur)

        base_px = int(base * scale)
        hauteur_px = int(hauteur * scale)
        offset_gauche_px = int(offset_gauche * scale)

        # Positionner pour que tout soit visible
        Ax = margin_horizontal + offset_gauche_px
        Ay = (canvas_size + hauteur_px) / 2 + 15
        Bx, By = Ax + base_px, Ay

        # C au-dessus et décalé à GAUCHE pour angle obtus en A
        Cx = Ax - offset_gauche_px
        Cy = Ay - hauteur_px

        # H : pied de la perpendiculaire de C sur (AB), à gauche de A
        Hx = Cx
        Hy = Ay

        # 1 seul côté inutile : BC (côté droit)
        cote_droit = cote_inutile  # valeur unique

        svg = f'''<svg width="{canvas_size}" height="{canvas_size}" xmlns="http://www.w3.org/2000/svg">
    <!-- Triangle {A}{B}{C} OBTUS GAUCHE -->
    <line x1="{Ax}" y1="{Ay}" x2="{Bx}" y2="{By}" stroke="black" stroke-width="3"/>
    <line x1="{Ax}" y1="{Ay}" x2="{Cx}" y2="{Cy}" stroke="black" stroke-width="3"/>
    <line x1="{Bx}" y1="{By}" x2="{Cx}" y2="{Cy}" stroke="black" stroke-width="3"/>

    <!-- Prolongement base -->
    <line x1="{Hx}" y1="{Hy}" x2="{Ax}" y2="{Ay}" stroke="gray" stroke-width="2" stroke-dasharray="4,4"/>

    <line x1="{Cx}" y1="{Cy}" x2="{Hx}" y2="{Hy}" stroke="red" stroke-width="2.5" stroke-dasharray="5,4"/>
    <rect x="{Hx}" y="{Hy - 15}" width="15" height="15" fill="none" stroke="red" stroke-width="2.5"/>

    <!-- Labels sommets -->
    <text x="{Ax - 20}" y="{Ay + 20}" font-size="20" fill="black" font-weight="bold">{A}</text>
    <text x="{Bx + 10}" y="{By + 20}" font-size="20" fill="black" font-weight="bold">{B}</text>
    <text x="{Cx - 8}" y="{Cy - 10}" font-size="20" fill="black" font-weight="bold">{C}</text>
    <text x="{Hx - 8}" y="{Hy + 20}" font-size="20" fill="black" font-weight="bold">H</text>

    <!-- Labels dimensions (toutes en bleu) -->
    <text x="{(Ax + Bx) / 2}" y="{Ay + 35}" font-size="18" fill="blue" font-weight="bold" text-anchor="middle">{base} cm</text>
    <text x="{Hx - 70}" y="{(Cy + Hy) / 2 + 6}" font-size="18" fill="blue" font-weight="bold">{hauteur} cm</text>
    <text x="{(Bx + Cx) / 2 + 15}" y="{(By + Cy) / 2 + 6}" font-size="18" fill="blue" font-weight="bold">{cote_droit} cm</text>
</svg>'''

    else:  # obtus_droite
        # === TRIANGLE OBTUS À DROITE : H à droite de B ===
        offset_droite = hauteur * 0.6
        largeur_totale = base + offset_droite

        scale = min(zone_w / largeur_totale, zone_h / hauteur)

        base_px = int(base * scale)
        hauteur_px = int(hauteur * scale)
        offset_droite_px = int(offset_droite * scale)

        # Centrer horizontalement en tenant compte du prolongement
        Ax = (canvas_size - base_px - offset_droite_px) / 2
        Ay = (canvas_size + hauteur_px) / 2 + 15
        Bx, By = Ax + base_px, Ay

        # C au-dessus et décalé à DROITE pour angle obtus en B
        Cx = Bx + offset_droite_px
        Cy = Ay - hauteur_px

        # H : pied de la perpendiculaire de C sur (AB), à droite de B
        Hx = Cx
        Hy = Ay

        # 1 seul côté inutile : AC (côté gauche)
        cote_gauche = cote_inutile  # valeur unique

        svg = f'''<svg width="{canvas_size}" height="{canvas_size}" xmlns="http://www.w3.org/2000/svg">
    <!-- Triangle {A}{B}{C} OBTUS DROITE -->
    <line x1="{Ax}" y1="{Ay}" x2="{Bx}" y2="{By}" stroke="black" stroke-width="3"/>
    <line x1="{Ax}" y1="{Ay}" x2="{Cx}" y2="{Cy}" stroke="black" stroke-width="3"/>
    <line x1="{Bx}" y1="{By}" x2="{Cx}" y2="{Cy}" stroke="black" stroke-width="3"/>

    <!-- Prolongement base -->
    <line x1="{Bx}" y1="{By}" x2="{Hx}" y2="{Hy}" stroke="gray" stroke-width="2" stroke-dasharray="4,4"/>

    <line x1="{Cx}" y1="{Cy}" x2="{Hx}" y2="{Hy}" stroke="red" stroke-width="2.5" stroke-dasharray="5,4"/>
    <rect x="{Hx}" y="{Hy - 15}" width="15" height="15" fill="none" stroke="red" stroke-width="2.5"/>

    <!-- Labels sommets -->
    <text x="{Ax - 20}" y="{Ay + 20}" font-size="20" fill="black" font-weight="bold">{A}</text>
    <text x="{Bx + 10}" y="{By + 20}" font-size="20" fill="black" font-weight="bold">{B}</text>
    <text x="{Cx + 8}" y="{Cy - 10}" font-size="20" fill="black" font-weight="bold">{C}</text>
    <text x="{Hx + 8}" y="{Hy + 20}" font-size="20" fill="black" font-weight="bold">H</text>

    <!-- Labels dimensions (toutes en bleu) -->
    <text x="{(Ax + Bx) / 2}" y="{Ay + 35}" font-size="18" fill="blue" font-weight="bold" text-anchor="middle">{base} cm</text>
    <text x="{Hx + 15}" y="{(Cy + Hy) / 2 + 6}" font-size="18" fill="blue" font-weight="bold">{hauteur} cm</text>
    <text x="{(Ax + Cx) / 2 - 40}" y="{(Ay + Cy) / 2 + 6}" font-size="18" fill="blue" font-weight="bold">{cote_gauche} cm</text>
</svg>'''

    return svg


def generer_svg_rectangle(longueur, largeur):
    """Génère SVG rectangle"""

    # Taille fixe du canvas
    canvas_size = 600
    margin = 80
    max_size = canvas_size - 2 * margin  # Zone disponible : 440x440 px

    # Calculer les dimensions du rectangle en gardant les proportions
    ratio = longueur / largeur

    if longueur >= largeur:
        # Rectangle horizontal ou carré
        w = max_size
        h = max_size / ratio
    else:
        # Rectangle vertical
        h = max_size
        w = max_size * ratio

    # Centrer le rectangle
    x = (canvas_size - w) / 2
    y = (canvas_size - h) / 2

    svg = f'''<svg width="{canvas_size}" height="{canvas_size}" xmlns="http://www.w3.org/2000/svg">
    <!-- Rectangle -->
    <rect x="{x}" y="{y}" width="{w}" height="{h}" fill="none" stroke="black" stroke-width="2.5"/>

    <!-- Labels dimensions -->
    <text x="{x + w / 2}" y="{y + h + 35}" font-size="18" fill="blue" font-weight="bold" text-anchor="middle">{longueur} cm</text>
    <text x="{x - 35}" y="{y + h / 2}" font-size="18" fill="blue" font-weight="bold" text-anchor="middle" transform="rotate(-90, {x - 35}, {y + h / 2})">{largeur} cm</text>


</svg>'''

    return svg

    return svg


def generer_svg_carre(cote):
    """Génère SVG carré"""

    # Taille fixe du canvas et du carré
    canvas_size = 500
    margin = 80
    taille = canvas_size - 2 * margin  # Carré fixe de 440x440 px

    x, y = margin, margin

    svg = f'''<svg width="{canvas_size}" height="{canvas_size}" xmlns="http://www.w3.org/2000/svg">
    <!-- Carré -->
    <rect x="{x}" y="{y}" width="{taille}" height="{taille}" fill="none" stroke="black" stroke-width="2.5"/>

    <!-- Labels dimensions -->
    <text x="{x + taille / 2}" y="{y + taille + 35}" font-size="18" fill="blue" font-weight="bold" text-anchor="middle">{cote} cm</text>
    


</svg>'''

    return svg


def generer_svg_disque(rayon, montrer_diametre=False):
    """Génère SVG disque avec rayon ou diamètre"""

    # Taille fixe du canvas et du disque
    canvas_size = 500
    margin = 50
    max_radius = (canvas_size - 2 * margin) / 2  # Rayon max du disque dans le SVG

    cx, cy = canvas_size / 2, canvas_size / 2
    r = max_radius  # Rayon fixe pour l'affichage

    if montrer_diametre:
        # Afficher le diamètre
        diametre = rayon * 2
        svg = f'''<svg width="{canvas_size}" height="{canvas_size}" xmlns="http://www.w3.org/2000/svg">
    <!-- Cercle -->
    <circle cx="{cx}" cy="{cy}" r="{r}" fill="none" stroke="black" stroke-width="2.5"/>

    <!-- Diamètre -->
    <line x1="{cx - r}" y1="{cy}" x2="{cx + r}" y2="{cy}" stroke="blue" stroke-width="2"/>

    <!-- Points extrémités -->
    <circle cx="{cx - r}" cy="{cy}" r="3" fill="blue"/>
    <circle cx="{cx + r}" cy="{cy}" r="3" fill="blue"/>

    <!-- Centre -->
    <circle cx="{cx}" cy="{cy}" r="2" fill="black"/>

    <!-- Label diamètre -->
    <text x="{cx}" y="{cy - 15}" font-size="18" fill="blue" font-weight="bold" text-anchor="middle">d = {diametre} cm</text>


</svg>'''
    else:
        # Afficher le rayon
        svg = f'''<svg width="{canvas_size}" height="{canvas_size}" xmlns="http://www.w3.org/2000/svg">
    <!-- Cercle -->
    <circle cx="{cx}" cy="{cy}" r="{r}" fill="none" stroke="black" stroke-width="2.5"/>

    <!-- Rayon -->
    <line x1="{cx}" y1="{cy}" x2="{cx + r}" y2="{cy}" stroke="red" stroke-width="2"/>

    <!-- Point sur cercle -->
    <circle cx="{cx + r}" cy="{cy}" r="3" fill="red"/>

    <!-- Centre -->
    <circle cx="{cx}" cy="{cy}" r="2" fill="black"/>

    <!-- Label rayon -->
    <text x="{cx + r / 2}" y="{cy - 15}" font-size="18" fill="red" font-weight="bold" text-anchor="middle">r = {rayon} cm</text>


</svg>'''

    return svg

# ========================================
# QUESTION GENERATORS
# ========================================

def generer_question_triangle():
    """Triangle quelconque avec hauteur - 3 TYPES"""

    # Labels variables
    labels_choices = [
        ['A', 'B', 'C'],
        ['D', 'E', 'F'],
        ['L', 'M', 'N'],
        ['R', 'S', 'T'],
        ['P', 'Q', 'R']
    ]
    labels = random.choice(labels_choices)
    A, B, C = labels

    # Choisir le type : 50% normal, 25% obtus gauche, 25% obtus droite
    rand = random.random()
    if rand < 0.5:
        type_triangle = 'normal'
    elif rand < 0.75:
        type_triangle = 'obtus_gauche'
    else:
        type_triangle = 'obtus_droite'

    # Base paire pour faciliter les calculs
    base = random.choice([6, 8, 10, 12])

    # Hauteur pour aire entière ou demi-entière
    hauteur = random.randint(3, 12)

    aire = (base * hauteur) / 2

    # Générer côtés inutiles selon le type
    if type_triangle == 'normal':
        # 2 côtés inutiles DIFFÉRENTS de la base
        cote1 = random.randint(max(4, hauteur - 2), min(12, hauteur + base - 2))
        while cote1 == base:
            cote1 = random.randint(max(4, hauteur - 2), min(12, hauteur + base - 2))

        cote2 = random.randint(max(4, hauteur - 2), min(12, hauteur + base - 2))
        while cote2 == base or cote2 == cote1:
            cote2 = random.randint(max(4, hauteur - 2), min(12, hauteur + base - 2))

        cote_inutile = (cote1, cote2)

    else:
        # 1 seul côté inutile DIFFÉRENT de la base
        cote = random.randint(max(4, hauteur - 2), min(12, hauteur + base - 2))
        while cote == base:
            cote = random.randint(max(4, hauteur - 2), min(12, hauteur + base - 2))

        cote_inutile = cote

    svg = generer_svg_triangle(base, hauteur, cote_inutile, labels, type_triangle)

    question = f'''<div class="question-aires">
{svg}
<p><strong>Calculer l'aire du triangle {A}{B}{C}.</strong></p>
</div>'''

    # Formater l'aire
    if aire == int(aire):
        aire_str = str(int(aire))
    else:
        aire_str = str(aire).replace('.', ',')

    reponse = f'<p><strong>Aire = {aire_str} cm&sup2;</strong></p>'

    return question, reponse, 1.5


def generer_question_rectangle():
    """Rectangle"""

    longueur = random.randint(5, 12)
    largeur = random.randint(3, 10)

    # Éviter le carré
    while longueur == largeur:
        largeur = random.randint(3, 10)

    aire = longueur * largeur

    svg = generer_svg_rectangle(longueur, largeur)

    question = f'''<div class="question-aires">
{svg}
<p><strong>Calculer l'aire de ce rectangle.</strong></p>
</div>'''

    reponse = f'<p><strong>Aire = {aire} cm&sup2;</strong></p>'

    return question, reponse, 1.2


def generer_question_carre():
    """Carré"""

    cote = random.randint(3, 12)
    aire = cote * cote

    svg = generer_svg_carre(cote)

    question = f'''<div class="question-aires">
{svg}
<p><strong>Calculer l'aire de ce carr&eacute;.</strong></p>
</div>'''

    reponse = f'<p><strong>Aire = {aire} cm&sup2;</strong></p>'

    return question, reponse, 1.1


def generer_question_disque():
    """Disque - aire EXACTE avec π"""

    # 50% rayon, 50% diamètre
    montrer_diametre = random.choice([True, False])

    if montrer_diametre:
        # Diamètre pair
        diametre = random.choice([4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24])
        rayon = diametre // 2

        svg = generer_svg_disque(rayon, montrer_diametre=True)

        question = f'''<div class="question-aires">
{svg}
<p><strong>Calculer l'aire EXACTE de ce disque.</strong></p>
</div>'''
    else:
        # Rayon entier
        rayon = random.randint(2, 12)

        svg = generer_svg_disque(rayon, montrer_diametre=False)

        question = f'''<div class="question-aires">
{svg}
<p><strong>Calculer l'aire EXACTE de ce disque.</strong></p>
</div>'''

    aire_coef = rayon * rayon

    reponse = f'<p><strong>Aire = {aire_coef}&pi; cm&sup2;</strong></p>'

    return question, reponse, 1.8


# ========================================
# MAIN GENERATION
# ========================================

def generer_toutes_questions():
    """Génère les 100 questions"""
    questions = []

    print("🔢 Génération de 100 questions Aires...")
    print("  - 40 triangles (50% normal, 25% obtus gauche, 25% obtus droite)")
    print("  - 30 rectangles")
    print("  - 20 disques")
    print("  - 10 carrés")

    # 40 triangles
    for i in range(40):
        q, r, d = generer_question_triangle()
        questions.append({'question': q, 'reponse': r, 'difficulte': d})

    # 30 rectangles
    for i in range(30):
        q, r, d = generer_question_rectangle()
        questions.append({'question': q, 'reponse': r, 'difficulte': d})

    # 20 disques
    for i in range(20):
        q, r, d = generer_question_disque()
        questions.append({'question': q, 'reponse': r, 'difficulte': d})

    # 10 carrés
    for i in range(10):
        q, r, d = generer_question_carre()
        questions.append({'question': q, 'reponse': r, 'difficulte': d})

    # Mélanger
    random.shuffle(questions)

    return questions


def sauvegarder_questions(questions):
    """Sauvegarde les questions"""
    print(f"\n📁 Sauvegarde dans {OUTPUT_DIR}/...")

    for i, q in enumerate(questions, 1):
        # Question
        with open(f"{OUTPUT_DIR}/question_{i:03d}.html", 'w', encoding='utf-8') as f:
            f.write(q['question'])

        # Réponse
        with open(f"{OUTPUT_DIR}/reponse_{i:03d}.html", 'w', encoding='utf-8') as f:
            f.write(q['reponse'])

    print(f"✅ {len(questions)} questions sauvegardées !")


if __name__ == "__main__":
    print("=" * 60)
    print("GÉNÉRATEUR AIRES - VERSION 2 (avec SVG)")
    print("=" * 60)

    questions = generer_toutes_questions()
    sauvegarder_questions(questions)

    print("\n" + "=" * 60)
    print("✅ TERMINÉ !")
    print("=" * 60)
    print(f"\nFichiers: {OUTPUT_DIR}/")
    print("  - 100 questions avec SVG")
    print("  - 100 réponses")