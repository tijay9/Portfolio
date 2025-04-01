def longueur_valide(mdp: str) -> bool:
    return len(mdp) >= 8

def contient_chiffre(mdp: str) -> bool:
    for c in mdp:
        if "0" <= c <= "9":
            return True
    return False

def contient_ponctuation(mdp: str) -> bool:
    for c in mdp:
        if not (("A" <= c <= "Z") or ("a" <= c <= "z") or ("0" <= c <= "9")):
            return True
    return False

def contient_majuscule(mdp: str) -> bool:
    for c in mdp:
        if "A" <= c <= "K":
            return True
    return False

def contient_minuscule(mdp: str) -> bool:
    for c in mdp:
        if "a" <= c <= "z":
            return True
    return False