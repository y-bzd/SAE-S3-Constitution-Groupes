package modele;

import java.util.*;

public class AlgoGloutonCovoit {

    public static void algoG1(List<Etudiant> etudiants, List<Groupe> groupes) {        
        System.out.println("Execution AlgoGloutonCovoit (Mode Équilibré)...");

        for (Groupe g : groupes) g.getEtudiantsInscrits().clear();

        List<List<Etudiant>> entitesAPlacer = formerBlocs(etudiants);
        
        entitesAPlacer.sort((l1, l2) -> l2.size() - l1.size());

        int totalEtudiants = etudiants.size();
        
        if (groupes.isEmpty()) return;

        int cibleIdeale = (int) Math.ceil((double) totalEtudiants / groupes.size());
        
        System.out.println("Objectif par groupe : " + cibleIdeale + " étudiants.");

        int indexGroupe = 0;
        
        for (List<Etudiant> entite : entitesAPlacer) {
            boolean place = false;
            int essais = 0;

            while (!place && essais < groupes.size()) {
                Groupe g = groupes.get(indexGroupe);
                
                int effectifActuel = g.getEtudiantsInscrits().size();
                int tailleBloc = entite.size();
                
                boolean placePhysique = (effectifActuel + tailleBloc <= g.getCapaciteMax());
                boolean placeLogique = (effectifActuel + tailleBloc <= cibleIdeale);

                if (placePhysique && (placeLogique || essais > groupes.size() / 2)) {
                    for (Etudiant e : entite) {
                        g.ajouterEtudiant(e);
                    }
                    place = true;
                } else {
                    indexGroupe = (indexGroupe + 1) % groupes.size();
                    essais++;
                }
            }
            
            if (!place) {
                System.err.println("Pas de place optimale pour le groupe de " + entite.get(0).getNom());
            }
        }
    }

    public static void algoG2(List<Etudiant> etudiants, List<Groupe> groupes, String matiereRef) {
        System.out.println("Execution AlgoGloutonCovoit (Niveau/Snake)...");

        for (Groupe g : groupes) g.getEtudiantsInscrits().clear();

        List<List<Etudiant>> entitesAPlacer = formerBlocs(etudiants);

        entitesAPlacer.sort((e1, e2) -> {
            double moy1 = calculerMoyenneListe(e1, matiereRef);
            double moy2 = calculerMoyenneListe(e2, matiereRef);
            return Double.compare(moy2, moy1);
        });

        int indexGroupe = 0;
        int direction = 1; 

        for (List<Etudiant> entite : entitesAPlacer) {
            boolean place = false;
            int essais = 0;
            int tempIndex = indexGroupe; 

            while (!place && essais < groupes.size()) {
                Groupe g = groupes.get(tempIndex);

                boolean capaciteOK = (g.getEtudiantsInscrits().size() + entite.size() <= g.getCapaciteMax());

                if (capaciteOK) {
                    for (Etudiant e : entite) {
                        g.ajouterEtudiant(e);
                    }
                    place = true;
                } else {
                    tempIndex = (tempIndex + 1) % groupes.size();
                    essais++;
                }
            }

            if (place) {
                indexGroupe += direction;
                if (indexGroupe >= groupes.size()) {
                    indexGroupe = groupes.size() - 1;
                    direction = -1;
                } else if (indexGroupe < 0) {
                    indexGroupe = 0;
                    direction = 1;
                }
            } else {
                 System.out.println("Pas de place pour " + entite.get(0).getNom());
            }
        }
    }

    public static List<List<Etudiant>> formerBlocs(List<Etudiant> etudiants) {
        List<List<Etudiant>> resultat = new ArrayList<>();
        Set<Etudiant> dejaTraites = new HashSet<>(); 

        for (Etudiant e : etudiants) {
            if (dejaTraites.contains(e)) continue;

            List<Etudiant> groupeAmi = new ArrayList<>();
            groupeAmi.add(e);
            dejaTraites.add(e);

            for (Etudiant ami : e.getMesAmis()) {
                if (!dejaTraites.contains(ami)) {
                    groupeAmi.add(ami);
                    dejaTraites.add(ami);
                    System.out.println("Groupe formé : " + e.getNom() + " avec " + ami.getNom());
                }
            }
            resultat.add(groupeAmi);
        }
        return resultat;
    }

    private static double calculerMoyenneListe(List<Etudiant> liste, String matiere) {
        double somme = 0;
        int count = 0;
        for (Etudiant e : liste) {
            double note = e.calculerMoyenne(matiere);
            if (note >= 0) {
                somme += note;
                count++;
            }
        }
        return (count == 0) ? 0 : somme / count ;
    }
}