package modele;

import java.util.ArrayList;

public class Promotion {
    private int id;
    private String libelle;
    private ArrayList<Groupe> groupes;
    private ArrayList<Etudiant> etudiantsInscrits;

    public Promotion(int id, String libelle) {
        this.id = id;
        this.libelle = libelle;
        this.groupes = new ArrayList<>();
        this.etudiantsInscrits = new ArrayList<>();
    }

    public void ajouterGroupe(Groupe g) {
        this.groupes.add(g);
    }

    public void inscrireEtudiant(Etudiant e) {
        this.etudiantsInscrits.add(e);
    }

    public ArrayList<Groupe> getGroupes() {
        return groupes;
    }

    public ArrayList<Etudiant> getEtudiantsInscrits() {
        return etudiantsInscrits;
    }

	public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public String getLibelle() {
		return libelle;
	}

	public void setLibelle(String libelle) {
		this.libelle = libelle;
	}

	public void setGroupes(ArrayList<Groupe> groupes) {
		this.groupes = groupes;
	}

	public void setEtudiantsInscrits(ArrayList<Etudiant> etudiantsInscrits) {
		this.etudiantsInscrits = etudiantsInscrits;
	}
}