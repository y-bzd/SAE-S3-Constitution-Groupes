package modele;

import java.util.ArrayList;
import java.util.List;

public class Etudiant {
    private String numeroEtudiant;
    private String nom;
    private String prenom;
    private String email;
    private String parcours;
    private String typeBac;
    private boolean estRedoublant;
    private String sexe;
    private int idUtilisateur;

    private List<Note> notes = new ArrayList<>();
    private List<Groupe> groupes = new ArrayList<>();
	 private List<Etudiant> mesAmis = new ArrayList<>();


    public Etudiant() {}

    public void consulterInfos() {
        System.out.println("Infos: " + prenom + " " + nom + " - Moyenne: " + calculerMoyenne("Generale"));
    }

    public float calculerMoyenne(String matiere) {
        if (notes.isEmpty()) return 0;
        float somme = 0;
        int count = 0;
        for (Note n : notes) {
            if (matiere.equals("Generale") || n.getLibelle().contains(matiere)) {
                somme += n.getValeur();
                count++;
            }
        }
        return count > 0 ? somme / count : 0;
    }
    
	 public void ajouterAmi(Etudiant e) {
	     if (!this.mesAmis.contains(e)) {
	         this.mesAmis.add(e);
	     }
	 }
	 
	 public List<Etudiant> getMesAmis() { return mesAmis;}
	 
    public String getNumeroEtudiant() { return numeroEtudiant; }
    public void setNumeroEtudiant(String numeroEtudiant) { this.numeroEtudiant = numeroEtudiant; }
    public String getNom() { return nom; }
    public void setNom(String nom) { this.nom = nom; }
    public String getPrenom() { return prenom; }
    public void setPrenom(String prenom) { this.prenom = prenom; }
    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }
    public String getParcours() { return parcours; }
    public void setParcours(String parcours) { this.parcours = parcours; }
    public String getTypeBac() { return typeBac; }
    public void setTypeBac(String typeBac) { this.typeBac = typeBac; }
    public boolean isEstRedoublant() { return estRedoublant; }
    public void setEstRedoublant(boolean estRedoublant) { this.estRedoublant = estRedoublant; }
    public String getSexe() { return sexe; }
    public void setSexe(String sexe) { this.sexe = sexe; }
    public List<Note> getNotes() { return notes; }
    public void setNotes(List<Note> notes) { this.notes = notes; }
    public List<Groupe> getGroupes() { return groupes; }
    public void setGroupes(List<Groupe> groupes) { this.groupes = groupes; }
    public int getIdUtilisateur() { return idUtilisateur; }
    public void setIdUtilisateur(int idUtilisateur) { this.idUtilisateur = idUtilisateur; }
}