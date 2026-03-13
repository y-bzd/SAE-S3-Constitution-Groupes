package controleur;

import java.util.HashMap;
import java.util.Map;
import org.json.JSONArray;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.List;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

import javax.swing.JOptionPane;

import modele.*;
import service.ApiService;
import service.ConnexionService;
import vue.FenetrePrincipale;

public class ControleurApplication implements ActionListener {

    private FenetrePrincipale fenetre;
    private ConnexionService connexionService;
    private Responsable responsableConnecte;
    
    private Promotion promotionActuelle;
    private List<Groupe> listeGroupes;

    public ControleurApplication(FenetrePrincipale fenetre) {
        this.fenetre = fenetre;
        this.connexionService = new ConnexionService();
        this.fenetre.setControleur(this);
        
        this.promotionActuelle = new Promotion(); 
        this.promotionActuelle.setLibelle("Promo Actuelle");
        this.listeGroupes = new ArrayList<>();
    }

    @Override
    public void actionPerformed(ActionEvent e) {
        String command = e.getActionCommand();

        if (command.equals("LOGIN")) {
            traiterConnexion();
        } 
        else if (command.equals("NAV_ETUDIANTS")) {
            chargerEtudiants();
            fenetre.afficherEcran("ETUDIANTS");
        }
        else if (command.equals("NAV_GROUPES")) {
            chargerGroupes();
            fenetre.afficherEcran("GROUPES");
        }
        else if (command.equals("NAV_CONFIG")) {
            fenetre.afficherEcran("CONFIG");
        }
        else if (command.equals("LANCER_ALGO")) {
            fenetre.afficherEcran("CONFIG");
        }
        else if (command.equals("SAUVEGARDER")) {
            sauvegarderRepartition();
        }
        else if (command.equals("LANCER_CONFIG")) {
            executerAlgorithme();
        }
        else if (command.equals("AJOUTER_GROUPE")) {
            ajouterNouveauGroupe();
        }
        else if (command.equals("SUPPRIMER_GROUPE")) {
            if (e.getSource() instanceof javax.swing.JButton) {
                javax.swing.JButton btn = (javax.swing.JButton) e.getSource();
                Groupe groupeASupprimer = (Groupe) btn.getClientProperty("groupeLie");
                supprimerGroupe(groupeASupprimer);
            }
        }
        else if (command.equals("DECONNEXION")) {
            responsableConnecte = null;
            fenetre.afficherEcran("CONNEXION");
        }
    }

    private void executerAlgorithme() {
        if (listeGroupes.isEmpty() || promotionActuelle.getEtudiants().isEmpty()) {
            chargerEtudiants();
            chargerGroupes();
            if (listeGroupes.isEmpty()) {
                JOptionPane.showMessageDialog(fenetre, "Aucune donnée chargée.");
                return;
            }
        }

        String algo = fenetre.getVueConfig().getAlgoSelectionne();
        String matiere = fenetre.getVueConfig().getMatiereSelectionnee();
        int tailleMax = fenetre.getVueConfig().getTailleMax();
        boolean mixite = fenetre.getVueConfig().isMixiteDemandee();

        System.out.println("Algo: " + algo + " | Mixité: " + mixite);

        for (Groupe g : listeGroupes) {
            g.getContraintes().clear(); 
            
            g.getContraintes().add(new Contrainte("TAILLE_MAX", String.valueOf(tailleMax), 1));
            
            if (mixite) {
                g.getContraintes().add(new Contrainte("MIXITE", "10", 2));
            }
        }

        long debut = System.currentTimeMillis();

        for (Groupe g : listeGroupes) g.getEtudiantsInscrits().clear();
        
        List<Etudiant> etudiantsTest = new ArrayList<>();
        if (promotionActuelle.getEtudiants().size() > 0) {
            int limite = Math.min(7, promotionActuelle.getEtudiants().size());
            etudiantsTest = promotionActuelle.getEtudiants().subList(0, limite);
        }
        
        if (algo.contains("Distributeur")) {
            AlgosGlouton.repartirGloutonDistributeur(promotionActuelle, listeGroupes);
            
        } else if (algo.contains("Compensateur")) {
            AlgosGlouton.repartirGloutonCompensateur(promotionActuelle, listeGroupes, matiere);
            
        } else if (algo.contains("Covoiturage")) {
            AlgoGloutonCovoit.algoG1(promotionActuelle.getEtudiants(), listeGroupes);
            
        } else if (algo.contains("Équilibré")) {
            AlgoGloutonCovoit.algoG2(promotionActuelle.getEtudiants(), listeGroupes, matiere);
            
        } else if (algo.contains("Youcef")) {
        	System.out.println("Mode Test : Lancement sur " + etudiantsTest.size() + " étudiants.");
            AlgoForceBrute.lancerForceBrute(etudiantsTest, listeGroupes, matiere);            
        } else if (algo.contains("Yassine")) {
        	System.out.println("Mode Test : Lancement sur " + etudiantsTest.size() + " étudiants.");
            Promotion promoTest = new Promotion();
            promoTest.setEtudiants(etudiantsTest);
            AlgoFB.repartirForceBrute(promoTest, listeGroupes, matiere);
        }
        
        long fin = System.currentTimeMillis();

        double score = AlgosGlouton.calculerScoreDesequilibre(listeGroupes, matiere);
        
        afficherResultatGroupes();
        fenetre.afficherEcran("GROUPES");

        String msg = String.format("Terminé en %d ms.\nScore: %.4f", (fin-debut), score);
        JOptionPane.showMessageDialog(fenetre, msg);
    }
    
    private void afficherResultatGroupes() {
        fenetre.getVueGroupes().nettoyerGroupes();
        
        fenetre.getVueGroupes().setControleur(this);
        
        for (Groupe g : listeGroupes) {
            fenetre.getVueGroupes().ajouterCarteGroupe(g);
        }
        
        fenetre.getVueGroupes().revalidate();
        fenetre.getVueGroupes().repaint();
    }

    private void chargerEtudiants() {
        System.out.println("Chargement des étudiants via l'API...");
        try {
            String jsonReponse = ApiService.envoyerRequete("promotions/1/etudiants", "GET", null);
            JSONArray jsonArray = new JSONArray(jsonReponse);
            
            promotionActuelle.getEtudiants().clear();
            
            Map<Integer, Etudiant> mapIdEtudiant = new HashMap<>();
            Map<Etudiant, List<Integer>> mapAmisTemporaire = new HashMap<>();
            
            for (int i = 0; i < jsonArray.length(); i++) {
                JSONObject jsonEtu = jsonArray.getJSONObject(i);

                Etudiant e = new Etudiant();
                int id = jsonEtu.optInt("id_etudiant", -1); 
                e.setIdUtilisateur(id);

                e.setNumeroEtudiant(jsonEtu.getString("numeroEtudiant"));
                e.setNom(jsonEtu.getString("nom"));
                e.setPrenom(jsonEtu.getString("prenom"));
                e.setTypeBac(jsonEtu.optString("typeBac", "Inconnu"));
                e.setSexe(jsonEtu.optString("sexe", "M"));

                List<Note> listeNotes = new ArrayList<>();
                JSONArray jsonNotes = jsonEtu.optJSONArray("notes");
                if (jsonNotes != null) {
                    for (int j = 0; j < jsonNotes.length(); j++) {
                        JSONObject n = jsonNotes.getJSONObject(j);
                        listeNotes.add(new Note(n.getString("libelle_note"),(float) n.getDouble("valeur_note")));
                    }
                }
                e.setNotes(listeNotes);

                List<Integer> idsAmis = new ArrayList<>();
                JSONArray jsonCollegues = jsonEtu.optJSONArray("collegues");
                if (jsonCollegues != null) {
                    for (int k = 0; k < jsonCollegues.length(); k++) {
                        idsAmis.add(jsonCollegues.getInt(k));
                    }
                }
                mapAmisTemporaire.put(e, idsAmis);
                
                if (id != -1) mapIdEtudiant.put(id, e);

                promotionActuelle.ajouterEtudiant(e);
            }

            for (Etudiant e : promotionActuelle.getEtudiants()) {
                List<Integer> idsAmis = mapAmisTemporaire.get(e);
                if (idsAmis != null) {
                    for (int idAmi : idsAmis) {
                        Etudiant ami = mapIdEtudiant.get(idAmi);
                        if (ami != null) {
                            e.ajouterAmi(ami);
                        }
                    }
                }
            }

            Object[][] data = new Object[promotionActuelle.getEtudiants().size()][5];
            for (int i = 0; i < promotionActuelle.getEtudiants().size(); i++) {
                Etudiant e = promotionActuelle.getEtudiants().get(i);
                data[i][0] = e.getNumeroEtudiant();
                data[i][1] = e.getNom();
                data[i][2] = e.getPrenom();
                data[i][3] = e.getTypeBac();
                data[i][4] = String.format("%.2f", e.calculerMoyenne("Generale"));
            }

            fenetre.getVueEtudiants().mettreAJourTableau(data);

        } catch (Exception e) {
            e.printStackTrace();
            JOptionPane.showMessageDialog(fenetre, "Erreur API Etudiants: " + e.getMessage());
        }
    }
    
    private void chargerGroupes() {
        System.out.println("Chargement des groupes via l'API...");
        try {
            String jsonReponse = ApiService.envoyerRequete("promotions/1/groupes", "GET", null);
            JSONArray jsonArray = new JSONArray(jsonReponse);
            
            listeGroupes.clear();
            fenetre.getVueGroupes().nettoyerGroupes();
            
            fenetre.getVueGroupes().setControleur(this);

            if (jsonArray.length() == 0) {
                 JOptionPane.showMessageDialog(fenetre, "Aucun groupe trouvé (Créez-en d'abord !).");
                 return;
            }

            for (int i = 0; i < jsonArray.length(); i++) {
                JSONObject jsonGrp = jsonArray.getJSONObject(i);
                
                Groupe g = new Groupe();
                g.setIdGroupe(jsonGrp.getInt("id_groupe")); 
                g.setLibelle(jsonGrp.getString("libelle_groupe"));
                g.setCapaciteMax(jsonGrp.getInt("capacite_max_groupe"));
                
                listeGroupes.add(g);
                
                fenetre.getVueGroupes().ajouterCarteGroupe(g);
            }
            
            fenetre.getVueGroupes().revalidate();
            fenetre.getVueGroupes().repaint();

        } catch (Exception e) {
            e.printStackTrace();
            JOptionPane.showMessageDialog(fenetre, "Erreur API Groupes: " + e.getMessage());
        }
    }

    private void traiterConnexion() {
        String login = fenetre.getVueConnexion().getLogin();
        String pwd = fenetre.getVueConnexion().getPassword();
        Responsable resp = connexionService.seConnecter(login, pwd);

        if (resp != null) {
            this.responsableConnecte = resp;
            JOptionPane.showMessageDialog(fenetre, "Bienvenue !");
            chargerEtudiants();
            fenetre.afficherEcran("ETUDIANTS");
        } else {
            JOptionPane.showMessageDialog(fenetre, "Identifiants incorrects.", "Erreur", JOptionPane.ERROR_MESSAGE);
        }
    }
    
    private void sauvegarderRepartition() {
        if (listeGroupes.isEmpty()) return;

        int reponse = JOptionPane.showConfirmDialog(fenetre, 
            "Voulez-vous vraiment enregistrer cette répartition en Base de Données ?", 
            "Confirmation", JOptionPane.YES_NO_OPTION);
            
        if (reponse != JOptionPane.YES_OPTION) return;

        try {
            JSONArray payload = new JSONArray();
            
            for (Groupe g : listeGroupes) {
                for (Etudiant e : g.getEtudiantsInscrits()) {
                    JSONObject assignment = new JSONObject();
                    assignment.put("numeroEtudiant", e.getNumeroEtudiant());
                    assignment.put("idGroupe", g.getIdGroupe());
                    
                    payload.put(assignment);
                }
            }

            System.out.println("Envoi de " + payload.length() + " affectations...");

            ApiService.envoyerRequete("promotions/1/affectations", "PUT", payload.toString());
            
            JOptionPane.showMessageDialog(fenetre, "Sauvegarde réussie !");
            
        } catch (Exception e) {
            e.printStackTrace();
            JOptionPane.showMessageDialog(fenetre, "Erreur sauvegarde : " + e.getMessage());
        }
    }
    
    public void deplacerEtudiant(Etudiant e, Groupe groupeSource, Groupe groupeCible) {
        if (groupeSource == groupeCible) return;

        if (groupeCible.getEtudiantsInscrits().size() >= groupeCible.getCapaciteMax()) {
            JOptionPane.showMessageDialog(fenetre, 
                "Le groupe " + groupeCible.getLibelle() + " est plein !", 
                "Déplacement impossible", JOptionPane.WARNING_MESSAGE);
            return;
        }

        if (!groupeCible.verifierContraintes()) {
        }

        groupeSource.retirerEtudiant(e);
        groupeCible.ajouterEtudiant(e);

        afficherResultatGroupes();
    }
    
    private void ajouterNouveauGroupe() {
        javax.swing.JTextField champNom = new javax.swing.JTextField();
        javax.swing.JTextField champCapa = new javax.swing.JTextField("15");
        
        Object[] message = {
            "Nom du groupe (ex: TD3) :", champNom,
            "Capacité Max :", champCapa
        };

        int option = javax.swing.JOptionPane.showConfirmDialog(fenetre, message, "Nouveau Groupe", javax.swing.JOptionPane.OK_CANCEL_OPTION);
        
        if (option == javax.swing.JOptionPane.OK_OPTION) {
            try {
                String nom = champNom.getText();
                int capa = Integer.parseInt(champCapa.getText());
                
                if (nom.isEmpty()) {
                    JOptionPane.showMessageDialog(fenetre, "Le nom est obligatoire.");
                    return;
                }

                if (responsableConnecte != null) {
                    responsableConnecte.creeGroupe(nom, TypeGroupe.TD, capa);
                    
                    chargerGroupes();
                    
                    JOptionPane.showMessageDialog(fenetre, "Groupe créé avec succès en base de données.");
                } else {
                    JOptionPane.showMessageDialog(fenetre, "Vous devez être connecté pour créer un groupe.");
                }
                
            } catch (NumberFormatException ex) {
                JOptionPane.showMessageDialog(fenetre, "La capacité doit être un nombre entier.");
            }
        }
    }

    private void supprimerGroupe(Groupe g) {
        if (g == null) return;

        if (!g.getEtudiantsInscrits().isEmpty()) {
            int reponse = JOptionPane.showConfirmDialog(fenetre, 
                "Ce groupe contient " + g.getEtudiantsInscrits().size() + " étudiants.\n" +
                "Ils seront retirés du groupe. Continuer ?", 
                "Attention", JOptionPane.YES_NO_OPTION, JOptionPane.WARNING_MESSAGE);
            
            if (reponse != JOptionPane.YES_OPTION) return;
        }

        if (responsableConnecte != null) {
            responsableConnecte.supprimerGroupe(g.getIdGroupe());
            
            chargerGroupes();
        }
    }
    
    public List<Groupe> getListeGroupes() { return listeGroupes; }
    
}