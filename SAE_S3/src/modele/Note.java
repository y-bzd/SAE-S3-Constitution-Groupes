package modele;

public class Note {
    private int idNote;
    private String libelle;
    private double valeur;

    public Note(int idNote, String libelle, double valeur) {
        this.idNote = idNote;
        this.libelle = libelle;
        this.valeur = valeur;
    }

    public String getLibelle() {
        return libelle;
    }

    public double getValeur() {
        return valeur;
    }
    
    public int getIdNote() {
        return idNote;
    }
}