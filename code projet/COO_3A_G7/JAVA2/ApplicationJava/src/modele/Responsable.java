package modele;

import java.util.ArrayList;
import java.util.List;
import org.json.JSONObject;
import service.ApiService;

public class Responsable extends Utilisateur {
    protected String nom;
    protected String prenom;
    private String porteeResponsable;
    private List<String> droits = new ArrayList<>();

    public Responsable() {}

    public void creeGroupe(String libelle, TypeGroupe type, int capacite) {
        try {
            JSONObject jsonGroupe = new JSONObject();
            jsonGroupe.put("libelle", libelle);
            jsonGroupe.put("type", type.name());
            jsonGroupe.put("capaciteMax", capacite);
            jsonGroupe.put("estPublic", false);

            String endpoint = "promotions/1/groupes";
            
            ApiService.envoyerRequete(endpoint, "POST", jsonGroupe.toString());
            
            System.out.println("SUCCÈS : Le groupe " + libelle + " a été créé via l'API.");

        } catch (Exception e) {
            System.err.println("ERREUR lors de la création du groupe " + libelle);
            e.printStackTrace();
        }
    }
    
    public void supprimerGroupe(int idGroupe) {
        try {
            String endpoint = "promotions/1/groupes/" + idGroupe;
            ApiService.envoyerRequete(endpoint, "DELETE", null);
            System.out.println("Groupe " + idGroupe + " supprimé via API.");
        } catch (Exception e) {
            System.err.println("Erreur suppression groupe : " + e.getMessage());
        }
    }
    
    public void ajouterContrainte(String type, String parametres, int priorite) {
        try {
            JSONObject json = new JSONObject();
            json.put("type", type);
            json.put("parametres", parametres);
            json.put("priorite", priorite);

            ApiService.envoyerRequete("promotions/1/contraintes", "POST", json.toString());
            System.out.println("Contrainte ajoutée.");
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
    
    public String getNom() { return nom; }
    public void setNom(String nom) { this.nom = nom; }
    public String getPrenom() { return prenom; }
    public void setPrenom(String prenom) { this.prenom = prenom; }
    public String getPorteeResponsable() { return porteeResponsable; }
    public void setPorteeResponsable(String porteeResponsable) { this.porteeResponsable = porteeResponsable; }
    public List<String> getDroits() { return droits; }
    public void setDroits(List<String> droits) { this.droits = droits; }
}