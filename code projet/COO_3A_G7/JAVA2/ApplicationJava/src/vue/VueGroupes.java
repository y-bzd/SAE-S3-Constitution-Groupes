package vue;

import java.awt.*;
import java.util.List;
import javax.swing.*;
import javax.swing.border.EmptyBorder;
import javax.swing.border.LineBorder;

import controleur.*;

public class VueGroupes extends JPanel {

    private JButton btnLancerAlgo;
    private JButton btnSauvegarder;
    private JButton btnAjouter;
    
    private JPanel panelConteneurGroupes;
    private ControleurApplication controleurRef;
    private final Color SACLAY_PURPLE = new Color(93, 15, 64);
    private final Color BG_GRAY = new Color(240, 240, 240);
    private final Color STATUS_OK = new Color(40, 167, 69);
    private final Color STATUS_WARN = new Color(220, 53, 69);

    public VueGroupes() {
        this.setLayout(new BorderLayout());
        this.setBackground(BG_GRAY);

        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setBackground(SACLAY_PURPLE);
        headerPanel.setBorder(new EmptyBorder(15, 20, 15, 20));

        JLabel lblTitre = new JLabel("Répartition des Groupes");
        lblTitre.setForeground(Color.WHITE);
        lblTitre.setFont(new Font("Arial", Font.BOLD, 22));
        headerPanel.add(lblTitre, BorderLayout.WEST);

        JPanel panelBoutons = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 0));
        panelBoutons.setOpaque(false);

        btnAjouter = new JButton("+ Nouveau Groupe");
        btnAjouter.setBackground(new Color(0, 123, 255)); // Bleu
        btnAjouter.setForeground(Color.WHITE);
        btnAjouter.setFocusPainted(false);
        btnAjouter.setFont(new Font("Arial", Font.BOLD, 14));
        btnAjouter.setActionCommand("AJOUTER_GROUPE");
        panelBoutons.add(btnAjouter);

        btnLancerAlgo = new JButton("Lancer Algorithme");
        btnLancerAlgo.setBackground(new Color(60, 0, 40));
        btnLancerAlgo.setForeground(Color.WHITE);
        btnLancerAlgo.setFocusPainted(false);
        btnLancerAlgo.setFont(new Font("Arial", Font.BOLD, 14));
        btnLancerAlgo.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createLineBorder(Color.WHITE, 1),
                new EmptyBorder(8, 20, 8, 20)
        ));
        btnLancerAlgo.setActionCommand("LANCER_ALGO");
        panelBoutons.add(btnLancerAlgo);
        
        btnSauvegarder = new JButton("Sauvegarder");
        btnSauvegarder.setBackground(new Color(40, 167, 69)); // Vert
        btnSauvegarder.setForeground(Color.WHITE);
        btnSauvegarder.setFocusPainted(false);
        btnSauvegarder.setFont(new Font("Arial", Font.BOLD, 14));
        btnSauvegarder.setActionCommand("SAUVEGARDER");
        panelBoutons.add(btnSauvegarder);
        
        headerPanel.add(panelBoutons, BorderLayout.EAST);
        
        this.add(headerPanel, BorderLayout.NORTH);

        panelConteneurGroupes = new JPanel(new FlowLayout(FlowLayout.LEFT, 20, 20));
        panelConteneurGroupes.setBackground(BG_GRAY);

        JScrollPane scrollPane = new JScrollPane(panelConteneurGroupes);
        scrollPane.setBorder(null);
        scrollPane.getVerticalScrollBar().setUnitIncrement(16);
        this.add(scrollPane, BorderLayout.CENTER);
    }

    public void nettoyerGroupes() {
        panelConteneurGroupes.removeAll();
        panelConteneurGroupes.revalidate();
        panelConteneurGroupes.repaint();
    }
    
    public void ajouterCarteGroupe(modele.Groupe groupe) {
        
        int nbActuel = groupe.getEtudiantsInscrits().size();
        int capaciteMax = groupe.getCapaciteMax();
        boolean estValide = nbActuel <= capaciteMax;

        Color themeColor = estValide ? STATUS_OK : STATUS_WARN;
        String iconSymbol = estValide ? "✔" : "⚠";

        JPanel card = new JPanel(new BorderLayout());
        card.setPreferredSize(new Dimension(240, 350));
        card.setBackground(Color.WHITE);
        card.setBorder(BorderFactory.createCompoundBorder(
            new LineBorder(themeColor, 3, true),
            new EmptyBorder(10, 10, 10, 10)
        ));

        JPanel headerCard = new JPanel(new BorderLayout());
        headerCard.setBackground(Color.WHITE);
        headerCard.setBorder(new EmptyBorder(0, 0, 10, 0));

        JPanel infoPanel = new JPanel(new GridLayout(2, 1));
        infoPanel.setBackground(Color.WHITE);
        JLabel lblNom = new JLabel(groupe.getLibelle() + "  " + iconSymbol);
        lblNom.setFont(new Font("Arial", Font.BOLD, 16));
        JLabel lblCapa = new JLabel(nbActuel + "/" + capaciteMax + " Etudiants");
        infoPanel.add(lblNom);
        infoPanel.add(lblCapa);
        headerCard.add(infoPanel, BorderLayout.CENTER);

        JButton btnSupprimer = new JButton("X");
        btnSupprimer.setBackground(new Color(220, 53, 69)); // Rouge
        btnSupprimer.setForeground(Color.WHITE);
        btnSupprimer.setMargin(new Insets(2, 8, 2, 8));
        btnSupprimer.setFocusPainted(false);
        btnSupprimer.setFont(new Font("Arial", Font.BOLD, 12));
        btnSupprimer.setActionCommand("SUPPRIMER_GROUPE");
        
        btnSupprimer.putClientProperty("groupeLie", groupe);
        
        if (controleurRef != null) {
            btnSupprimer.addActionListener(controleurRef);
        }
        
        headerCard.add(btnSupprimer, BorderLayout.EAST);
        card.add(headerCard, BorderLayout.NORTH);

        DefaultListModel<modele.Etudiant> listModel = new DefaultListModel<>();
        for (modele.Etudiant e : groupe.getEtudiantsInscrits()) {
            listModel.addElement(e);
        }

        JList<modele.Etudiant> jList = new JList<>(listModel);
        jList.putClientProperty("groupeLie", groupe);
        jList.setDragEnabled(true);
        jList.setDropMode(DropMode.INSERT);
        if (controleurRef != null) {
            jList.setTransferHandler(new GroupeTransferHandler(controleurRef));
        }

        jList.setCellRenderer(new DefaultListCellRenderer() {
            @Override
            public Component getListCellRendererComponent(JList<?> list, Object value, int index, boolean isSelected, boolean cellHasFocus) {
                modele.Etudiant e = (modele.Etudiant) value;
                JLabel label = (JLabel) super.getListCellRendererComponent(list, e.getPrenom() + " " + e.getNom(), index, isSelected, cellHasFocus);
                label.setBorder(BorderFactory.createCompoundBorder(
                    new EmptyBorder(2, 0, 2, 0),
                    BorderFactory.createLineBorder(Color.LIGHT_GRAY, 1)
                ));
                return label;
            }
        });

        JScrollPane scrollListe = new JScrollPane(jList);
        scrollListe.setBorder(null);
        card.add(scrollListe, BorderLayout.CENTER);

        panelConteneurGroupes.add(card);
    }

    public JButton getBtnLancerAlgo() { return btnLancerAlgo; }
    public JButton getBtnSauvegarder() { return btnSauvegarder; }
    public JButton getBtnAjouter() { return btnAjouter; }
    
    public void setControleur(ControleurApplication ctrl) { 
        this.controleurRef = ctrl;
        if (btnAjouter != null) {
            for (java.awt.event.ActionListener al : btnAjouter.getActionListeners()) {
                btnAjouter.removeActionListener(al);
            }
            btnAjouter.addActionListener(ctrl);
        }    
    }
}