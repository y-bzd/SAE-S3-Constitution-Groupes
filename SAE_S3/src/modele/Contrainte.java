package modele;

public class Contrainte {
    private int idContrainte;
    private String typeContrainte;
    private String parametreContrainte;
    private int prioriteContrainte;

    public Contrainte(int idContrainte, String typeContrainte, String parametreContrainte, int prioriteContrainte) {
        this.idContrainte = idContrainte;
        this.typeContrainte = typeContrainte;
        this.parametreContrainte = parametreContrainte;
        this.prioriteContrainte = prioriteContrainte;
    }

    public int getIdContrainte() {
        return idContrainte;
    }

    public String getTypeContrainte() {
        return typeContrainte;
    }

    public String getParametreContrainte() {
        return parametreContrainte;
    }

    public int getPrioriteContrainte() {
        return prioriteContrainte;
    }

	public void setIdContrainte(int idContrainte) {
		this.idContrainte = idContrainte;
	}

	public void setTypeContrainte(String typeContrainte) {
		this.typeContrainte = typeContrainte;
	}

	public void setParametreContrainte(String parametreContrainte) {
		this.parametreContrainte = parametreContrainte;
	}

	public void setPrioriteContrainte(int prioriteContrainte) {
		this.prioriteContrainte = prioriteContrainte;
	}
}