#!/usr/bin/env python3
"""
Générateur de questions flash Thalès - DNB 2026
Génère 100 variantes de questions sur le théorème de Thalès
Configuration : triangles emboîtés uniquement (selon le programme)
"""

import random
import os

# Créer le dossier de sortie
output_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "includes", "qf_thales")
os.makedirs(output_dir, exist_ok=True)

def generer_configuration_thales():
    """Génère une configuration de triangles emboîtés avec Thalès"""
    
    # Triangle ABC avec un point M sur [AB] et N sur [AC]
    # Tels que (MN) // (BC)
    
    # RAPPORTS SIMPLES pour calcul mental (SANS CALCULATRICE)
    k = random.choice([0.5, 1/3, 2/3, 0.25, 0.75, 0.4, 0.6])
    
    # Longueurs du grand triangle (valeurs simples)
    AB = random.choice([6, 8, 9, 10, 12, 15, 18, 20])
    AC = random.choice([6, 8, 9, 10, 12, 15, 18, 20])
    BC = random.choice([6, 8, 9, 10, 12, 15, 18])
    
    # Position de M sur [AB] et N sur [AC]
    AM = AB * k
    AN = AC * k
    MN = BC * k
    
    # S'assurer que les valeurs sont entières ou demi-entières simples
    # Pour éviter les calculs compliqués
    if AM != int(AM) and AM != int(AM) + 0.5:
        # Régénérer avec des valeurs qui donnent des résultats entiers
        AB = random.choice([10, 12, 15, 20])
        AC = random.choice([10, 12, 15, 20])
        BC = random.choice([10, 12, 15])
        AM = AB * k
        AN = AC * k
        MN = BC * k
    
    # Calculer les longueurs restantes
    MB = AB - AM
    NC = AC - AN
    
    return {
        'AB': int(AB) if AB == int(AB) else AB,
        'AC': int(AC) if AC == int(AC) else AC,
        'BC': int(BC) if BC == int(BC) else BC,
        'AM': int(AM) if AM == int(AM) else AM,
        'AN': int(AN) if AN == int(AN) else AN,
        'MN': int(MN) if MN == int(MN) else MN,
        'MB': int(MB) if MB == int(MB) else MB,
        'NC': int(NC) if NC == int(NC) else NC,
        'k': k
    }

def generer_question_thales(num):
    """Génère une question Thalès variante #num"""
    
    config = generer_configuration_thales()
    
    # Type de question : on donne AM, AB, AN et on cherche AC
    # ou on donne AM, AB, MN et on cherche BC
    type_question = random.choice(['chercher_AC', 'chercher_BC', 'chercher_MN'])
    
    if type_question == 'chercher_AC':
        question = f"""<p>Triangle ABC. M est sur [AB], N est sur [AC].</p>
<p>(MN) et (BC) sont parallèles.</p>
<p>AM = {config['AM']} cm, AB = {config['AB']} cm, AN = {config['AN']} cm.</p>
<p>Calculer AC.</p>"""
        
        reponse = f"""<p><strong>AC = {config['AC']} cm</strong></p>"""
    
    elif type_question == 'chercher_BC':
        question = f"""<p>Triangle ABC. M est sur [AB], N est sur [AC].</p>
<p>(MN) et (BC) sont parallèles.</p>
<p>AM = {config['AM']} cm, AB = {config['AB']} cm, MN = {config['MN']} cm.</p>
<p>Calculer BC.</p>"""
        
        reponse = f"""<p><strong>BC = {config['BC']} cm</strong></p>"""
    
    else:  # chercher_MN
        question = f"""<p>Triangle ABC. M est sur [AB], N est sur [AC].</p>
<p>(MN) et (BC) sont parallèles.</p>
<p>AM = {config['AM']} cm, AB = {config['AB']} cm, BC = {config['BC']} cm.</p>
<p>Calculer MN.</p>"""
        
        reponse = f"""<p><strong>MN = {config['MN']} cm</strong></p>"""
    
    return question, reponse

def generer_fichier_html(num, question, reponse):
    """Écrit la question et la réponse dans deux fichiers séparés.

    functions/thales.php lit question_XXX.html et reponse_XXX.html : les deux
    fichiers doivent donc être produits, avec la même numérotation.
    """

    filename_q = f"{output_dir}/question_{num:03d}.html"
    with open(filename_q, 'w', encoding='utf-8') as f:
        f.write(f"<!-- Question Thalès #{num} -->\n{question}\n")

    filename_r = f"{output_dir}/reponse_{num:03d}.html"
    with open(filename_r, 'w', encoding='utf-8') as f:
        f.write(f"{reponse}\n")

    return filename_q

def main():
    """Génère toutes les variantes"""
    print("🔢 Génération des questions Thalès...")
    print(f"📁 Dossier de sortie : {output_dir}")
    
    for i in range(1, 101):
        question, reponse = generer_question_thales(i)
        filename = generer_fichier_html(i, question, reponse)
        
        if i % 10 == 0:
            print(f"✅ {i}/100 questions générées")
    
    print(f"\n🎉 100 variantes générées avec succès dans {output_dir}/")
    print("💡 Utilisez ces fichiers avec : include('includes/qf_thales/question_XXX.html');")

if __name__ == "__main__":
    main()
