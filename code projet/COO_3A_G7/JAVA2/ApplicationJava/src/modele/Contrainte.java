package modele;

public class Contrainte {
    private String type;       // "TAILLE_MAX" ou "MIXITE"
    private String parametres;
    private int priorite;

    public Contrainte(String type, String parametres, int priorite) {
        this.type = type;
        this.parametres = parametres;
        this.priorite = priorite;
    }

    public Contrainte() {}

    public boolean estRespectee(Groupe g) {
        if ("TAILLE_MAX".equals(type)) {
            try {
                int max = Integer.parseInt(parametres);
                return g.getEtudiantsInscrits().size() <= max;
            } catch (NumberFormatException e) {
                return true; 
            }
        }
        
        if ("MIXITE".equals(type)) {
            int nbHommes = 0;
            int nbFemmes = 0;
            
            for (Etudiant e : g.getEtudiantsInscrits()) {
                if ("F".equals(e.getSexe())) nbFemmes++;
                else nbHommes++;
            }
            
            try {
                int ecartMax = Integer.parseInt(parametres);
                return Math.abs(nbHommes - nbFemmes) <= ecartMax;
            } catch (Exception e) {
                return true;
            }
        }
        
        return true; 
    }
    public String getType() { return type; }
    public void setType(String type) { this.type = type; }
    public String getParametres() { return parametres; }
    public void setParametres(String parametres) { this.parametres = parametres; }
    public int getPriorite() { return priorite; }
    public void setPriorite(int priorite) { this.priorite = priorite; }
}