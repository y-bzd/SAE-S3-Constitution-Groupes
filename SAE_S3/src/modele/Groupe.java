package modele;

import java.util.ArrayList;
import java.util.List;

public class Groupe {
    private int idGroupe;
    private String libelleGroupe;
    private String typeGroupe;
    private int capaciteMaxGroupe;
    private boolean groupeRenduPublic;
    private List<Etudiant> etudiants;

    public Groupe(int idGroupe, String libelleGroupe, String typeGroupe, int capaciteMaxGroupe) {
        this.idGroupe = idGroupe;
        this.libelleGroupe = libelleGroupe;
        this.typeGroupe = typeGroupe;
        this.capaciteMaxGroupe = capaciteMaxGroupe;
        this.groupeRenduPublic = false;
        this.etudiants = new ArrayList<>();
    }

    public boolean ajouterEtudiant(Etudiant e) {
        if (etudiants.size() < capaciteMaxGroupe) {
            etudiants.add(e);
            return true;
        }
        return false;
    }

    public boolean supprimerEtudiant(Etudiant e) {
        return etudiants.remove(e);
    }

    public int getEffectif() {
        return etudiants.size();
    }

    public double getMoyenneGroupe(String matiere) {
        if (etudiants.isEmpty()) return 0.0;
        
        double somme = 0;
        for (int i = 0; i < etudiants.size(); i++) {
            Etudiant e = etudiants.get(i);
            somme += e.getMoyenne(matiere);
        }
        return somme / etudiants.size();
    }

    public int compterParGenre(String genreCherche) {
        int compte = 0;
        for (int i = 0; i < etudiants.size(); i++) {
            Etudiant e = etudiants.get(i);
            if (e.getGenre().equals(genreCherche)) {
                compte++;
            }
        }
        return compte;
    }

    public int getIdGroupe() {
        return idGroupe;
    }

    public String getLibelleGroupe() {
        return libelleGroupe;
    }

    public String getTypeGroupe() {
        return typeGroupe;
    }

    public int getCapaciteMaxGroupe() {
        return capaciteMaxGroupe;
    }

    public boolean isGroupeRenduPublic() {
        return groupeRenduPublic;
    }

    public void setGroupeRenduPublic(boolean groupeRenduPublic) {
        this.groupeRenduPublic = groupeRenduPublic;
    }

    public List<Etudiant> getEtudiants() {
        return etudiants;
    }

	public void setIdGroupe(int idGroupe) {
		this.idGroupe = idGroupe;
	}

	public void setLibelleGroupe(String libelleGroupe) {
		this.libelleGroupe = libelleGroupe;
	}

	public void setTypeGroupe(String typeGroupe) {
		this.typeGroupe = typeGroupe;
	}

	public void setCapaciteMaxGroupe(int capaciteMaxGroupe) {
		this.capaciteMaxGroupe = capaciteMaxGroupe;
	}

	public void setEtudiants(List<Etudiant> etudiants) {
		this.etudiants = etudiants;
	}

	public boolean verifierContraintes() {
		// TODO Auto-generated method stub
		return false;
	}
}