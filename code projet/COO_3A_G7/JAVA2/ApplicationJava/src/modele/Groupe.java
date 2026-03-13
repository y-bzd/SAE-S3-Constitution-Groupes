package modele;

import java.util.ArrayList;
import java.util.List;

public class Groupe {
    private int idGroupe;
    private String libelle;
    private TypeGroupe type;
    private int capaciteMax;
    private boolean estPublic;
    
    private List<Etudiant> etudiantsInscrits = new ArrayList<>();
    private List<Contrainte> contraintes = new ArrayList<>();

    public Groupe() {}

    public Groupe(String libelle, TypeGroupe type, int capaciteMax) {
        this.libelle = libelle;
        this.type = type;
        this.capaciteMax = capaciteMax;
    }

    public boolean ajouterEtudiant(Etudiant e) {
        if (!estComplet()) {
            etudiantsInscrits.add(e);
            return true;
        }
        return false;
    }

    public void retirerEtudiant(Etudiant e) {
        etudiantsInscrits.remove(e);
    }

    public boolean estComplet() {
        return etudiantsInscrits.size() >= capaciteMax;
    }

    public boolean verifierContraintes() {
        for (Contrainte c : contraintes) {
            if (!c.estRespectee(this)) {
                return false;
            }
        }
        return true;
    }

    public int getIdGroupe() { return idGroupe; }
    public void setIdGroupe(int idGroupe) { this.idGroupe = idGroupe; }
    public String getLibelle() { return libelle; }
    public void setLibelle(String libelle) { this.libelle = libelle; }
    public TypeGroupe getType() { return type; }
    public void setType(TypeGroupe type) { this.type = type; }
    public int getCapaciteMax() { return capaciteMax; }
    public void setCapaciteMax(int capaciteMax) { this.capaciteMax = capaciteMax; }
    public boolean isEstPublic() { return estPublic; }
    public void setEstPublic(boolean estPublic) { this.estPublic = estPublic; }
    public List<Etudiant> getEtudiantsInscrits() { return etudiantsInscrits; }
    public void setEtudiantsInscrits(List<Etudiant> etudiantsInscrits) { this.etudiantsInscrits = etudiantsInscrits; }
    public List<Contrainte> getContraintes() { return contraintes; }
    public void setContraintes(List<Contrainte> contraintes) { this.contraintes = contraintes; }
}