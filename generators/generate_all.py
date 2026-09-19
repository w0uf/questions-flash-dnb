#!/usr/bin/env python3
"""
Script maître - Génère toutes les questions flash DNB 2026
Lance tous les générateurs en séquence
"""

import subprocess
import os
import sys

def run_generator(script_name):
    """Execute un générateur Python"""
    print(f"\n{'='*60}")
    print(f"🚀 Lancement de {script_name}")
    print(f"{'='*60}\n")
    
    result = subprocess.run([sys.executable, script_name], 
                          capture_output=False, 
                          text=True)
    
    if result.returncode == 0:
        print(f"\n✅ {script_name} terminé avec succès")
    else:
        print(f"\n❌ Erreur dans {script_name}")
        return False
    
    return True

def main():
    """Lance tous les générateurs"""
    print("╔═══════════════════════════════════════════════════════════╗")
    print("║  GÉNÉRATEUR MAÎTRE - Questions Flash DNB 2026             ║")
    print("╚═══════════════════════════════════════════════════════════╝")
    
    # Liste des générateurs à exécuter
    generators = [
        'generate_pythagore.py',
        'generate_thales.py',
        'generate_aires.py',
    ]
    
    success_count = 0
    total = len(generators)
    
    for generator in generators:
        if run_generator(generator):
            success_count += 1
    
    print(f"\n{'='*60}")
    print(f"📊 RÉSULTAT FINAL : {success_count}/{total} générateurs réussis")
    print(f"{'='*60}\n")
    
    if success_count == total:
        print("🎉 Toutes les questions ont été générées avec succès !")
        print("\n📂 Structure créée :")
        print("   └── includes/")
        print("       ├── qf_pythagore/ (100 variantes)")
        print("       ├── qf_thales/ (100 variantes)")
        print("       └── qf_aires/ (100 variantes)")
        print("\n💡 Vous pouvez maintenant utiliser ces includes dans vos pages PHP.")
    else:
        print("⚠️  Certains générateurs ont échoué. Vérifiez les erreurs ci-dessus.")
        return 1
    
    return 0

if __name__ == "__main__":
    exit(main())
