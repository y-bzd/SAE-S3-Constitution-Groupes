package main;

import javax.swing.SwingUtilities;
import controleur.ControleurApplication;
import vue.FenetrePrincipale;

public class Main {
    public static void main(String[] args) {
        SwingUtilities.invokeLater(new Runnable() {
            @Override
            public void run() {
                FenetrePrincipale fenetre = new FenetrePrincipale();
                
                new ControleurApplication(fenetre);
                
                fenetre.setVisible(true);
            }
        });
    }
}