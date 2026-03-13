package vue;

import java.awt.*;
import javax.swing.*;
import javax.swing.border.EmptyBorder;
import javax.swing.border.LineBorder;

public class VueConnexion extends JPanel {
    private JTextField champLogin;
    private JPasswordField champPassword;
    private JButton btnConnexion;

    private final Color SACLAY_PURPLE = new Color(93, 15, 64);
    private final Color LIGHT_GRAY_BG = new Color(240, 240, 240);

    public VueConnexion() {
        this.setLayout(new BorderLayout());

        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setBackground(SACLAY_PURPLE);
        headerPanel.setPreferredSize(new Dimension(800, 60));
        
        JLabel logoLabel = new JLabel("  université PARIS-SACLAY | IUT D'ORSAY");
        logoLabel.setForeground(Color.WHITE);
        logoLabel.setFont(new Font("Arial", Font.BOLD, 14));
        headerPanel.add(logoLabel, BorderLayout.WEST);

        this.add(headerPanel, BorderLayout.NORTH);

        JPanel mainPanel = new JPanel(new GridBagLayout());
        mainPanel.setBackground(LIGHT_GRAY_BG);

        JPanel cardPanel = new JPanel(new GridBagLayout());
        cardPanel.setBackground(Color.WHITE);
        cardPanel.setBorder(BorderFactory.createCompoundBorder(
            new LineBorder(new Color(200, 200, 200), 1, true),
            new EmptyBorder(30, 50, 30, 50)
        ));

        GridBagConstraints gbc = new GridBagConstraints();
        gbc.insets = new Insets(10, 0, 10, 0);
        gbc.fill = GridBagConstraints.HORIZONTAL;
        gbc.gridx = 0;

        JLabel lblTitre = new JLabel("Connexion", SwingConstants.CENTER);
        lblTitre.setFont(new Font("Arial", Font.BOLD, 24));
        gbc.gridy = 0;
        cardPanel.add(lblTitre, gbc);

        JLabel lblLogin = new JLabel("Identifiant");
        lblLogin.setFont(new Font("Arial", Font.PLAIN, 12));
        gbc.gridy = 1;
        gbc.insets = new Insets(20, 0, 5, 0);
        cardPanel.add(lblLogin, gbc);

        champLogin = new JTextField(20);
        champLogin.setPreferredSize(new Dimension(250, 35));
        gbc.gridy = 2;
        gbc.insets = new Insets(0, 0, 10, 0);
        cardPanel.add(champLogin, gbc);

        JLabel lblPass = new JLabel("Mot de passe");
        lblPass.setFont(new Font("Arial", Font.PLAIN, 12));
        gbc.gridy = 3;
        gbc.insets = new Insets(5, 0, 5, 0);
        cardPanel.add(lblPass, gbc);

        champPassword = new JPasswordField(20);
        champPassword.setPreferredSize(new Dimension(250, 35));
        gbc.gridy = 4;
        gbc.insets = new Insets(0, 0, 20, 0);
        cardPanel.add(champPassword, gbc);

        btnConnexion = new JButton("Se Connecter");
        btnConnexion.setActionCommand("LOGIN");
        btnConnexion.setBackground(Color.WHITE);
        btnConnexion.setForeground(Color.BLACK);
        btnConnexion.setFocusPainted(false);
        btnConnexion.setFont(new Font("Arial", Font.BOLD, 14));
        btnConnexion.setPreferredSize(new Dimension(150, 40));
        btnConnexion.setBorder(new LineBorder(Color.GRAY, 1, true));
        
        gbc.gridy = 5;
        gbc.fill = GridBagConstraints.NONE;
        gbc.anchor = GridBagConstraints.CENTER;
        cardPanel.add(btnConnexion, gbc);

        mainPanel.add(cardPanel);
        this.add(mainPanel, BorderLayout.CENTER);
    }

    public JButton getBtnConnexion() { return btnConnexion; }
    public String getLogin() { return champLogin.getText(); }
    public String getPassword() { return new String(champPassword.getPassword()); }
}