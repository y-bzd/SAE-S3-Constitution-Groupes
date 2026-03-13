package modele;

import java.util.ArrayList;
import java.util.List;

public class Promotion {
    private int id;
    private String libelle;
    private List<Groupe> groupes = new ArrayList<>();
    private List<Etudiant> etudiants = new ArrayList<>();

    public Promotion() {}

    public void ajouterEtudiant(Etudiant e) {
        this.etudiants.add(e);
    }

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }
    public String getLibelle() { return libelle; }
    public void setLibelle(String libelle) { this.libelle = libelle; }
    public List<Groupe> getGroupes() { return groupes; }
    public void setGroupes(List<Groupe> groupes) { this.groupes = groupes; }
    public List<Etudiant> getEtudiants() { return etudiants; }
    public void setEtudiants(List<Etudiant> etudiants) { this.etudiants = etudiants; }
}