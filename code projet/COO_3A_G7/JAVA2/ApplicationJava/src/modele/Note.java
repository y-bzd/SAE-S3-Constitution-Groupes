package modele;

import java.util.Date;

public class Note {
    private String libelle;
    private float valeur;
    private Date dateImportation;

    public Note() {}

    public Note(String libelle, float valeur) {
        this.libelle = libelle;
        this.valeur = valeur;
        this.dateImportation = new Date();
    }

    public String getLibelle() { return libelle; }
    public void setLibelle(String libelle) { this.libelle = libelle; }
    public float getValeur() { return valeur; }
    public void setValeur(float valeur) { this.valeur = valeur; }
    public Date getDateImportation() { return dateImportation; }
    public void setDateImportation(Date dateImportation) { this.dateImportation = dateImportation; }
}