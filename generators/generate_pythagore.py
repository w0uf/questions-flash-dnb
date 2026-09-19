#!/usr/bin/env python3
"""
Générateur de questions flash Pythagore - DNB 2026
Génère 100 variantes de questions sur le théorème de Pythagore
"""

import random
import math
import os

# Créer le dossier de sortie
output_dir = "../includes/qf_pythagore"
os.makedirs(output_dir, exist_ok=True)

def generer_svg_triangle_rectangle(cote1, cote2, hypotenuse, labels, chercher):
    """Génère un SVG de triangle rectangle avec labels"""
    A, B, C = labels
    
    # Triangle rectangle en A
    # A en bas à gauche (angle droit)
    # B en bas à droite (bout de AB)
    # C en haut à gauche (bout de AC)
    
    # Échelle : environ 8 pixels par cm (ajusté pour tenir dans 300x220)
    scale = min(180 / max(cote1, cote2), 8)
    
    Ax, Ay = 50, 190
    Bx, By = 50 + int(cote2 * scale), 190
    Cx, Cy = 50, 190 - int(cote1 * scale)
    
    # Labels des côtés (avec ? pour celui à chercher)
    label_AB = "?" if chercher == "AB" else f"{cote2} cm"
    label_AC = "?" if chercher == "AC" else f"{cote1} cm"
    label_BC = "?" if chercher == "BC" else f"{hypotenuse} cm"
    
    svg = f'''<svg width="320" height="240" xmlns="http://www.w3.org/2000/svg">
    <!-- Triangle rectangle en {A} -->
    <line x1="{Ax}" y1="{Ay}" x2="{Bx}" y2="{By}" stroke="black" stroke-width="2.5"/>
    <line x1="{Ax}" y1="{Ay}" x2="{Cx}" y2="{Cy}" stroke="black" stroke-width="2.5"/>
    <line x1="{Bx}" y1="{By}" x2="{Cx}" y2="{Cy}" stroke="black" stroke-width="2.5"/>
    
    <!-- Marque angle droit -->
    <rect x="{Ax}" y="{Ay-12}" width="12" height="12" fill="none" stroke="black" stroke-width="2"/>
    
    <!-- Labels des sommets -->
    <text x="{Ax-15}" y="{Ay+15}" font-size="20" font-weight="bold">{A}</text>
    <text x="{Bx+10}" y="{By+15}" font-size="20" font-weight="bold">{B}</text>
    <text x="{Cx-15}" y="{Cy-5}" font-size="20" font-weight="bold">{C}</text>
    
    <!-- Labels des côtés -->
    <text x="{(Ax+Bx)/2-15}" y="{(Ay+By)/2+25}" font-size="16" fill="blue" font-weight="bold">{label_AB}</text>
    <text x="{(Ax+Cx)/2-35}" y="{(Ay+Cy)/2+5}" font-size="16" fill="blue" font-weight="bold">{label_AC}</text>
    <text x="{(Bx+Cx)/2+15}" y="{(By+Cy)/2+5}" font-size="16" fill="red" font-weight="bold">{label_BC}</text>
</svg>'''
    
    return svg


def generer_triangle_pythagoricien():
    """Génère un triangle pythagoricien (triplet de Pythagore)"""
    # Triplets de Pythagore classiques (SANS CALCULATRICE)
    triplets_base = [
        (3, 4, 5),
        (5, 12, 13),
        (8, 15, 17),
        (7, 24, 25),
        (6, 8, 10),   # = 2×(3,4,5)
        (9, 12, 15),  # = 3×(3,4,5)
        (12, 16, 20), # = 4×(3,4,5)
        (10, 24, 26), # = 2×(5,12,13)
    ]
    
    # Choisir un triplet aléatoire
    triplet = random.choice(triplets_base)
    
    a = triplet[0]
    b = triplet[1]
    c = triplet[2]
    
    return a, b, c

def generer_question_pythagore(num):
    """Génère une question Pythagore variante #num"""
    
    # UNIQUEMENT triplets pythagoriciens (SANS CALCULATRICE)
    a, b, c = generer_triangle_pythagoricien()
    
    # Trouver la valeur maximale (qui pourrait être la réponse)
    max_val = max(a, b, c)
    
    # Si max > 12, créer un tableau ÉTENDU pour ne pas orienter
    tableau_carres = ""
    if max_val > 12:
        # Tableau de 13 jusqu'à max+5 (pour ne pas donner d'indice direct)
        fin_tableau = max_val + 5
        
        # Construire le tableau sur 2 lignes de 4 colonnes
        tableau_html = "<table style='border-collapse: collapse; margin: 15px auto;'>"
        
        # Première ligne : 13 à 16
        tableau_html += "<tr>"
        for n in range(13, min(17, fin_tableau + 1)):
            tableau_html += f"<td style='border: 1px solid black; padding: 8px; text-align: center;'><strong>{n}² = {n*n}</strong></td>"
        tableau_html += "</tr>"
        
        # Deuxième ligne : 17 à 20
        if fin_tableau >= 17:
            tableau_html += "<tr>"
            for n in range(17, min(21, fin_tableau + 1)):
                tableau_html += f"<td style='border: 1px solid black; padding: 8px; text-align: center;'><strong>{n}² = {n*n}</strong></td>"
            tableau_html += "</tr>"
        
        # Troisième ligne si nécessaire : 21 à 24
        if fin_tableau >= 21:
            tableau_html += "<tr>"
            for n in range(21, min(25, fin_tableau + 1)):
                tableau_html += f"<td style='border: 1px solid black; padding: 8px; text-align: center;'><strong>{n}² = {n*n}</strong></td>"
            tableau_html += "</tr>"
        
        # Quatrième ligne si nécessaire : 25 à 28
        if fin_tableau >= 25:
            tableau_html += "<tr>"
            for n in range(25, min(29, fin_tableau + 1)):
                tableau_html += f"<td style='border: 1px solid black; padding: 8px; text-align: center;'><strong>{n}² = {n*n}</strong></td>"
            tableau_html += "</tr>"
        
        tableau_html += "</table>"
        tableau_carres = f"<p style='text-align: center;'><em>Rappel :</em></p>{tableau_html}"
    
    # Type de question : chercher l'hypoténuse ou un côté de l'angle droit
    type_question = random.choice(['hypotenuse', 'cote'])
    
    if type_question == 'hypotenuse':
        # On donne les deux côtés, on cherche l'hypoténuse
        labels = ['A', 'B', 'C']
        svg = generer_svg_triangle_rectangle(a, b, c, labels, chercher='BC')
        
        question = f"""<div class="question-pythagore">
{svg}
{tableau_carres}
<p>Triangle ABC rectangle en A.</p>
<p>AB = {b} cm et AC = {a} cm.</p>
<p><strong>Calculer BC.</strong></p>
</div>"""
        
        reponse = f"""<p><strong>BC = {c} cm</strong></p>"""
    
    else:  # cote
        # On donne l'hypoténuse et un côté, on cherche l'autre côté
        # Triangle rectangle en D
        # DE = côté horizontal = b
        # DF = côté vertical = a (à chercher)
        # EF = hypoténuse = c
        labels = ['D', 'E', 'F']
        svg = generer_svg_triangle_rectangle(a, b, c, labels, chercher='AC')
        
        question = f"""<div class="question-pythagore">
{svg}
{tableau_carres}
<p>Triangle DEF rectangle en D.</p>
<p>DE = {b} cm et EF = {c} cm.</p>
<p><strong>Calculer DF.</strong></p>
</div>"""
        
        reponse = f"""<p><strong>DF = {a} cm</strong></p>"""
    
    return question, reponse

def generer_fichier_html(num, question, reponse):
    """Génère les fichiers HTML pour une question (question + réponse séparées)"""
    
    # Fichier question
    filename_q = f"{output_dir}/question_{num:03d}.html"
    with open(filename_q, 'w', encoding='utf-8') as f:
        f.write(question)
    
    # Fichier réponse
    filename_r = f"{output_dir}/reponse_{num:03d}.html"
    with open(filename_r, 'w', encoding='utf-8') as f:
        f.write(reponse)
    
    return filename_q, filename_r

def main():
    """Génère toutes les variantes"""
    print("🔢 Génération des questions Pythagore...")
    print(f"📁 Dossier de sortie : {output_dir}")
    
    for i in range(1, 101):
        question, reponse = generer_question_pythagore(i)
        filename_q, filename_r = generer_fichier_html(i, question, reponse)
        
        if i % 10 == 0:
            print(f"✅ {i}/100 questions générées")
    
    print(f"\n🎉 100 variantes générées avec succès dans {output_dir}/")
    print(f"💡 200 fichiers créés : question_XXX.html + reponse_XXX.html")
    print("💡 Utilisez avec : include('includes/qf_pythagore/question_XXX.html');")

if __name__ == "__main__":
    main()
