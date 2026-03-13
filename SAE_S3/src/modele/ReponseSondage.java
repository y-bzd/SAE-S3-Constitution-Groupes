package modele;

public class ReponseSondage {
    private int id;
    private String nomSondage;
    private String valeurReponse;

    public ReponseSondage(int id, String nomSondage, String valeurReponse) {
        this.id = id;
        this.nomSondage = nomSondage;
        this.valeurReponse = valeurReponse;
    }

    public String getNomSondage() {
        return nomSondage;
    }

    public String getValeurReponse() {
        return valeurReponse;
    }

	public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public void setNomSondage(String nomSondage) {
		this.nomSondage = nomSondage;
	}

	public void setValeurReponse(String valeurReponse) {
		this.valeurReponse = valeurReponse;
	}
}