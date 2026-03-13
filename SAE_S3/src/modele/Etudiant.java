package modele;

import java.util.ArrayList;
import java.util.List;

public class Etudiant {
    private int id;
    private String numeroEtudiant;
    private String nom;
    private String prenom;
    private String genre;
    private String email;
    private String telephone;
    private String rue;
    private String ville;
    private String codePostal;
    private String typeBac;
    private String periodeRedoublement;
    private String parcours;
    
    private List<Note> notes;
    private List<ReponseSondage> reponsesSondages;
    private PreferenceCovoiturage preferenceCovoiturage;

    public Etudiant(int id, String numeroEtudiant, String nom, String prenom, String genre, 
                    String email, String telephone, String rue, String ville, String codePostal, 
                    String typeBac, String periodeRedoublement, String parcours) {
        this.id = id;
        this.numeroEtudiant = numeroEtudiant;
        this.nom = nom;
        this.prenom = prenom;
        this.genre = genre;
        this.email = email;
        this.telephone = telephone;
        this.rue = rue;
        this.ville = ville;
        this.codePostal = codePostal;
        this.typeBac = typeBac;
        this.periodeRedoublement = periodeRedoublement;
        this.parcours = parcours;
        
        this.notes = new ArrayList<>();
        this.reponsesSondages = new ArrayList<>();
        this.preferenceCovoiturage = null;
    }

    public void ajouterNote(Note note) {
        this.notes.add(note);
    }

    public void ajouterReponseSondage(ReponseSondage reponse) {
        this.reponsesSondages.add(reponse);
    }

    public void setPreferenceCovoiturage(PreferenceCovoiturage pref) {
        this.preferenceCovoiturage = pref;
    }

    public double getMoyenne(String matiere) {
        for (int i = 0; i < notes.size(); i++) {
            Note n = notes.get(i);
            if (n.getLibelle().equals(matiere)) {
                return n.getValeur();
            }
        }
        return 0.0;
    }

    public PreferenceCovoiturage getPreferenceCovoiturage() {
        return preferenceCovoiturage;
    }

    public List<ReponseSondage> getReponsesSondages() {
        return reponsesSondages;
    }

	public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public String getNumeroEtudiant() {
		return numeroEtudiant;
	}

	public void setNumeroEtudiant(String numeroEtudiant) {
		this.numeroEtudiant = numeroEtudiant;
	}

	public String getNom() {
		return nom;
	}

	public void setNom(String nom) {
		this.nom = nom;
	}

	public String getPrenom() {
		return prenom;
	}

	public void setPrenom(String prenom) {
		this.prenom = prenom;
	}

	public String getGenre() {
		return genre;
	}

	public void setGenre(String genre) {
		this.genre = genre;
	}

	public String getEmail() {
		return email;
	}

	public void setEmail(String email) {
		this.email = email;
	}

	public String getTelephone() {
		return telephone;
	}

	public void setTelephone(String telephone) {
		this.telephone = telephone;
	}

	public String getRue() {
		return rue;
	}

	public void setRue(String rue) {
		this.rue = rue;
	}

	public String getVille() {
		return ville;
	}

	public void setVille(String ville) {
		this.ville = ville;
	}

	public String getCodePostal() {
		return codePostal;
	}

	public void setCodePostal(String codePostal) {
		this.codePostal = codePostal;
	}

	public String getTypeBac() {
		return typeBac;
	}

	public void setTypeBac(String typeBac) {
		this.typeBac = typeBac;
	}

	public String getPeriodeRedoublement() {
		return periodeRedoublement;
	}

	public void setPeriodeRedoublement(String periodeRedoublement) {
		this.periodeRedoublement = periodeRedoublement;
	}

	public String getParcours() {
		return parcours;
	}

	public void setParcours(String parcours) {
		this.parcours = parcours;
	}

	public List<Note> getNotes() {
		return notes;
	}

	public void setNotes(List<Note> notes) {
		this.notes = notes;
	}

	public void setReponsesSondages(List<ReponseSondage> reponsesSondages) {
		this.reponsesSondages = reponsesSondages;
	}

	public double calculerMoyenne(String matiere) {
		// TODO Auto-generated method stub
		return 0;
	}

    
}