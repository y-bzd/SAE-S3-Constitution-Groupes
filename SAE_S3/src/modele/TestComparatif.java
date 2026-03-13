package modele;

import java.util.ArrayList;
import java.util.List;
import java.util.Random;

public class TestComparatif {

    public static void main(String[] args) {
        // Paramètres de simulation
        int nbIterations = 100;
        String matiereRef = "Java";
        
        // Résultats cumulés pour la moyenne
        double totalYassine1 = 0;
        double totalYassine2 = 0;
        double totalYoucef1 = 0;
        double totalYoucef2 = 0;

        System.out.println("Lancement des tests comparatifs (" + nbIterations + " itérations)...");

        for (int i = 0; i < nbIterations; i++) {
            
            // 1. Génération données (Promo + Etudiants + Amis)
            Promotion promo = genererDonneesTest(40, matiereRef);
            
            // Création de 2 groupes de 20 places
            List<Groupe> groupes = new ArrayList<>();
            groupes.add(new Groupe(1, "G1", "TP", 20));
            groupes.add(new Groupe(2, "G2", "TP", 20));

            // 2. Tests Algorithmes Yassine
            // Algo 1 : Distributeur
            AlgosGlouton_Rharrabti_Yassine.repartirGloutonDistributeur(promo, groupes);
            totalYassine1 += calculScore(groupes, matiereRef);
            
            // Algo 2 : Compensateur
            AlgosGlouton_Rharrabti_Yassine.repartirGloutonCompensateur(promo, groupes, matiereRef);
            totalYassine2 += calculScore(groupes, matiereRef);

            // 3. Tests Algorithmes Youcef
            // On récupère la liste des étudiants inscrits pour ces algos
            List<Etudiant> listeEtudiants = promo.getEtudiantsInscrits();

            // Algo 1 : Logistique (Covoiturage)
            AlgoGloutonCovoit_Bouzid_Youcef.algoG1(listeEtudiants, groupes);
            totalYoucef1 += calculScore(groupes, matiereRef);

            // Algo 2 : Snake (Covoiturage + Niveau)
            AlgoGloutonCovoit_Bouzid_Youcef.algoG2(listeEtudiants, groupes, matiereRef);
            totalYoucef2 += calculScore(groupes, matiereRef);
        }

        // Affichage console
        System.out.println("\n--- RESULTATS MOYENS (Score de variance : plus bas = mieux) ---");
        System.out.println("Yassine (Distributeur) : " + String.format("%.4f", totalYassine1 / nbIterations));
        System.out.println("Yassine (Compensateur) : " + String.format("%.4f", totalYassine2 / nbIterations));
        System.out.println("Youcef  (Logistique)   : " + String.format("%.4f", totalYoucef1 / nbIterations));
        System.out.println("Youcef  (Snake)        : " + String.format("%.4f", totalYoucef2 / nbIterations));
    }

    // Génère une promo aléatoire avec notes et amis
    public static Promotion genererDonneesTest(int nbEtu, String matiere) {
        Promotion p = new Promotion(1, "Promo S3");
        Random rand = new Random();
        List<Etudiant> tempListe = new ArrayList<>();

        for (int i = 0; i < nbEtu; i++) {
            Etudiant e = new Etudiant(
                i, "E"+i, "Nom"+i, "Prenom"+i, 
                rand.nextBoolean() ? "H" : "F", 
                "mail@test.fr", "0600000000", "Rue", "Ville", "00000", "Bac", "Non", "Parcours"
            );

            // Ajout note entre 5 et 20
            double val = 5 + (15 * rand.nextDouble());
            e.ajouterNote(new Note(i, matiere, val));

            p.inscrireEtudiant(e);
            tempListe.add(e);
        }

        // Création aléatoire de groupes de covoiturage (30% des étudiants)
        int idPref = 1;
        for (Etudiant e : tempListe) {
            // Si pas encore d'amis et coup de chance
            if (e.getPreferenceCovoiturage() == null && rand.nextDouble() < 0.3) {
                Etudiant ami = null;
                // Cherche un ami libre
                for(Etudiant candid : tempListe) {
                    if(candid != e && candid.getPreferenceCovoiturage() == null) {
                        ami = candid;
                        break;
                    }
                }
                
                if (ami != null) {
                    PreferenceCovoiturage pref = new PreferenceCovoiturage(idPref++);
                    pref.ajouterEtudiant(e);
                    pref.ajouterEtudiant(ami);
                    e.setPreferenceCovoiturage(pref);
                    ami.setPreferenceCovoiturage(pref);
                }
            }
        }
        return p;
    }

    // Calcule la variance des moyennes entre les groupes
    public static double calculScore(List<Groupe> groupes, String matiere) {
        double sommeMoy = 0;
        int nb = 0;

        for (Groupe g : groupes) {
            if (g.getEffectif() > 0) {
                sommeMoy += g.getMoyenneGroupe(matiere);
                nb++;
            }
        }
        if (nb == 0) return 0;
        
        double moyGlobale = sommeMoy / nb;
        double score = 0;

        for (Groupe g : groupes) {
            if (g.getEffectif() > 0) {
                double diff = g.getMoyenneGroupe(matiere) - moyGlobale;
                score += diff * diff;
            }
        }
        return score;
    }
}