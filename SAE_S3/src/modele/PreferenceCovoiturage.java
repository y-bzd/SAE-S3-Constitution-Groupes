package modele;

import java.util.ArrayList;
import java.util.List;

public class PreferenceCovoiturage {
    private int id;
    private List<Etudiant> etudiantsConcernes;

    public PreferenceCovoiturage(int id) {
        this.id = id;
        this.etudiantsConcernes = new ArrayList<>();
    }

    public void ajouterEtudiant(Etudiant e) {
        this.etudiantsConcernes.add(e);
    }

    public List<Etudiant> getEtudiantsConcernes() {
        return etudiantsConcernes;
    }

    public int getId() {
        return id;
    }

	public void setId(int id) {
		this.id = id;
	}

	public void setEtudiantsConcernes(List<Etudiant> etudiantsConcernes) {
		this.etudiantsConcernes = etudiantsConcernes;
	}
}