package modele;

import java.util.*;

public class AlgoForceBrute {

    private static double ecartMin = 1000.0; 
    private static int[] meilleureConfig;

    public static void lancerForceBrute(List<Etudiant> etudiants, List<Groupe> groupes, String matiere) {
        System.out.println("--- Début Force Brute ---");
        
        List<List<Etudiant>> blocs = AlgoGloutonCovoit.formerBlocs(etudiants); 
        
        ecartMin = 1000.0;
        meilleureConfig = new int[blocs.size()];
        int[] configActuelle = new int[blocs.size()];

        testerCombinaison(0, blocs, groupes, configActuelle, matiere);

        if (ecartMin < 1000.0) {
            System.out.println("Meilleure solution trouvée (Ecart : " + String.format("%.2f", ecartMin) + ")");
            appliquerConfig(blocs, groupes, meilleureConfig);
        } else {
            System.out.println("Aucune solution trouvée.");
        }
    }

    private static void testerCombinaison(int indexBloc, List<List<Etudiant>> blocs, List<Groupe> groupes, int[] config, String matiere) {
        if (indexBloc == blocs.size()) {
            double ecart = calculerEcart(groupes, matiere);
            if (ecart < ecartMin) {
                ecartMin = ecart;
                System.arraycopy(config, 0, meilleureConfig, 0, config.length);
            }
            return;
        }

        List<Etudiant> leBloc = blocs.get(indexBloc);

        for (int i = 0; i < groupes.size(); i++) {
            Groupe g = groupes.get(i);

            if (g.getEtudiantsInscrits().size() + leBloc.size() <= g.getCapaciteMax()) {
                
                for (Etudiant e : leBloc) g.ajouterEtudiant(e);
                config[indexBloc] = i;

                testerCombinaison(indexBloc + 1, blocs, groupes, config, matiere);

                for (Etudiant e : leBloc) g.retirerEtudiant(e);
            }
        }
    }

    private static double calculerEcart(List<Groupe> groupes, String matiere) {
        double min = 20;
        double max = 0;
        boolean groupeVide = false;

        for (Groupe g : groupes) {
            if (g.getEtudiantsInscrits().isEmpty()) groupeVide = true;
            double m = AlgosGlouton.calculerScoreDesequilibre(List.of(g), matiere);
            if (m < min) min = m;
            if (m > max) max = m;
        }
        if (groupeVide) return 500.0; 
        return max - min;
    }

    private static void appliquerConfig(List<List<Etudiant>> blocs, List<Groupe> groupes, int[] config) {
        for (Groupe g : groupes) g.getEtudiantsInscrits().clear();
        for (int i = 0; i < blocs.size(); i++) {
            int numGroupe = config[i];
            for (Etudiant e : blocs.get(i)) {
                groupes.get(numGroupe).ajouterEtudiant(e);
            }
        }
    }
}