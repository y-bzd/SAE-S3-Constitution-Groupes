package vue;

import java.awt.*;
import javax.swing.*;
import javax.swing.border.EmptyBorder;
import controleur.ControleurApplication;

public class FenetrePrincipale extends JFrame {

    private ControleurApplication controleur;
    
    private JPanel conteneurCentral;
    private CardLayout cardLayout;
    
    private VueConnexion vueConnexion;
    private VueEtudiants vueEtudiants;
    private VueGroupes vueGroupes;
    private VueConfiguration vueConfig;
    
    private JPanel sidebar;
    private final Color SIDEBAR_BG = new Color(50, 50, 50);
    private final Color BUTTON_HOVER = new Color(80, 80, 80);

    public FenetrePrincipale() {
        this.setTitle("Application Gestion Groupes - IUT Orsay");
        this.setSize(1200, 800);
        this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        this.setLocationRelativeTo(null);
        this.setLayout(new BorderLayout());

        conteneurCentral = new JPanel();
        cardLayout = new CardLayout();
        conteneurCentral.setLayout(cardLayout);

        vueConnexion = new VueConnexion();
        vueEtudiants = new VueEtudiants();
        vueGroupes = new VueGroupes();
        vueConfig = new VueConfiguration();
        
        conteneurCentral.add(vueConnexion, "CONNEXION");
        conteneurCentral.add(vueEtudiants, "ETUDIANTS");
        conteneurCentral.add(vueGroupes, "GROUPES");
        conteneurCentral.add(vueConfig, "CONFIG");
        
        this.add(conteneurCentral, BorderLayout.CENTER);
        
        initSidebar();
        sidebar.setVisible(false);
        this.add(sidebar, BorderLayout.WEST);
    }

    private void initSidebar() {
        sidebar = new JPanel();
        sidebar.setBackground(SIDEBAR_BG);
        sidebar.setPreferredSize(new Dimension(220, 700));
        sidebar.setLayout(new BoxLayout(sidebar, BoxLayout.Y_AXIS));
        sidebar.setBorder(new EmptyBorder(20, 0, 0, 0));

        sidebar.add(creerBoutonMenu("Étudiants", "NAV_ETUDIANTS"));
        sidebar.add(Box.createRigidArea(new Dimension(0, 10)));
        sidebar.add(creerBoutonMenu("Gestion des Groupes", "NAV_GROUPES"));
        sidebar.add(Box.createRigidArea(new Dimension(0, 10)));
        sidebar.add(creerBoutonMenu("Configuration", "NAV_CONFIG"));
        
        sidebar.add(Box.createVerticalGlue());
        
        JButton btnDeco = creerBoutonMenu("Déconnexion", "DECONNEXION");
        btnDeco.setBackground(new Color(150, 50, 50));
        sidebar.add(btnDeco);
        sidebar.add(Box.createRigidArea(new Dimension(0, 20)));
    }
    
    private JButton creerBoutonMenu(String texte, String actionCommand) {
        JButton btn = new JButton(texte);
        btn.setActionCommand(actionCommand);
        btn.setAlignmentX(Component.CENTER_ALIGNMENT);
        btn.setMaximumSize(new Dimension(220, 50));
        btn.setBackground(SIDEBAR_BG);
        btn.setForeground(Color.LIGHT_GRAY);
        btn.setFont(new Font("Arial", Font.PLAIN, 16));
        btn.setFocusPainted(false);
        btn.setBorder(new EmptyBorder(10, 20, 10, 20));
        btn.setCursor(new Cursor(Cursor.HAND_CURSOR));
        
        return btn;
    }

    public void setControleur(ControleurApplication ctrl) {
        this.controleur = ctrl;
        
        vueConnexion.getBtnConnexion().addActionListener(ctrl);
        vueGroupes.getBtnLancerAlgo().addActionListener(ctrl);
        vueGroupes.getBtnSauvegarder().addActionListener(ctrl);
        vueConfig.getBtnSauvegarder().addActionListener(ctrl);
        
        for (Component comp : sidebar.getComponents()) {
            if (comp instanceof JButton) {
                ((JButton) comp).addActionListener(ctrl);
            }
        }
    }

    public void afficherEcran(String nomEcran) {
        cardLayout.show(conteneurCentral, nomEcran);
        if (nomEcran.equals("CONNEXION")) {
            sidebar.setVisible(false);
        } else {
            sidebar.setVisible(true);
        }
    }

    public VueConnexion getVueConnexion() { return vueConnexion; }
    public VueEtudiants getVueEtudiants() { return vueEtudiants; }
    public VueGroupes getVueGroupes() { return vueGroupes; }
    public VueConfiguration getVueConfig() { return vueConfig; }
}