package vue;

import java.awt.*;
import javax.swing.*;
import javax.swing.border.EmptyBorder;
import javax.swing.border.LineBorder;

public class VueConfiguration extends JPanel {

    private JComboBox<String> comboAlgo;
    private JTextField champMatiere;
    private JSpinner spinnerTailleMax;
    private JCheckBox chkMixite;
    private JButton btnSauvegarder;

    private final Color SACLAY_PURPLE = new Color(93, 15, 64);
    private final Color BG_GRAY = new Color(240, 240, 240);

    public VueConfiguration() {
        this.setLayout(new BorderLayout());
        this.setBackground(BG_GRAY);

        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setBackground(SACLAY_PURPLE);
        headerPanel.setBorder(new EmptyBorder(15, 20, 15, 20));
        JLabel lblTitre = new JLabel("Configuration Avancée");
        lblTitre.setForeground(Color.WHITE);
        lblTitre.setFont(new Font("Arial", Font.BOLD, 22));
        headerPanel.add(lblTitre, BorderLayout.WEST);
        this.add(headerPanel, BorderLayout.NORTH);

        JPanel contentPanel = new JPanel(new GridBagLayout());
        contentPanel.setBackground(BG_GRAY);
        GridBagConstraints gbc = new GridBagConstraints();
        gbc.insets = new Insets(10, 10, 10, 10);
        gbc.fill = GridBagConstraints.HORIZONTAL;

        JPanel card = new JPanel(new GridBagLayout());
        card.setBackground(Color.WHITE);
        card.setBorder(BorderFactory.createCompoundBorder(
            new LineBorder(Color.LIGHT_GRAY, 1, true),
            new EmptyBorder(30, 50, 30, 50)
        ));

        gbc.gridx = 0; gbc.gridy = 0;
        card.add(new JLabel("Algorithme :"), gbc);
        String[] algos = { 
        	    "Distributeur", 
        	    "Compensateur", 
        	    "Covoiturage", 
        	    "Équilibré", 
        	    "Force Brute (Youcef)",
        	    "Force Brute (Yassine)"
        };        
        comboAlgo = new JComboBox<>(algos);
        comboAlgo.setPreferredSize(new Dimension(200, 30));
        gbc.gridx = 1;
        card.add(comboAlgo, gbc);

        gbc.gridx = 0; gbc.gridy = 1;
        card.add(new JLabel("Matière Ref :"), gbc);
        champMatiere = new JTextField("Generale");
        champMatiere.setPreferredSize(new Dimension(200, 30));
        gbc.gridx = 1;
        card.add(champMatiere, gbc);

        gbc.gridx = 0; gbc.gridy = 2;
        card.add(new JLabel("Taille Max Groupe :"), gbc);
        spinnerTailleMax = new JSpinner(new SpinnerNumberModel(15, 1, 100, 1)); 
        gbc.gridx = 1;
        card.add(spinnerTailleMax, gbc);

        gbc.gridx = 0; gbc.gridy = 3;
        gbc.gridwidth = 2;
        chkMixite = new JCheckBox("Forcer la parité H/F (Écart max 2)");
        chkMixite.setBackground(Color.WHITE);
        chkMixite.setFont(new Font("Arial", Font.PLAIN, 12));
        card.add(chkMixite, gbc);

        btnSauvegarder = new JButton("Lancer le Calcul");
        btnSauvegarder.setBackground(new Color(40, 167, 69)); 
        btnSauvegarder.setForeground(Color.WHITE);
        btnSauvegarder.setFont(new Font("Arial", Font.BOLD, 14));
        btnSauvegarder.setPreferredSize(new Dimension(200, 40));
        btnSauvegarder.setActionCommand("LANCER_CONFIG");
        
        gbc.gridx = 0; gbc.gridy = 4; gbc.gridwidth = 2; gbc.anchor = GridBagConstraints.CENTER;
        gbc.insets = new Insets(20, 0, 0, 0);
        card.add(btnSauvegarder, gbc);

        contentPanel.add(card);
        this.add(contentPanel, BorderLayout.CENTER);
    }

    public String getAlgoSelectionne() { return (String) comboAlgo.getSelectedItem(); }
    public String getMatiereSelectionnee() { return champMatiere.getText(); }
    public int getTailleMax() { return (int) spinnerTailleMax.getValue(); }
    
    public boolean isMixiteDemandee() { return chkMixite.isSelected(); }
    
    public JButton getBtnSauvegarder() { return btnSauvegarder; }
}