package vue;

import java.awt.*;
import javax.swing.*;
import javax.swing.border.EmptyBorder;
import javax.swing.table.DefaultTableModel;
import javax.swing.table.JTableHeader;

public class VueEtudiants extends JPanel {
    private JTable tableEtudiants;
    private DefaultTableModel modeleTable;
    private JButton btnImport;

    private final Color SACLAY_PURPLE = new Color(93, 15, 64);
    private final Color TABLE_HEADER_BG = new Color(40, 40, 40);

    public VueEtudiants() {
        this.setLayout(new BorderLayout());

        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setBackground(SACLAY_PURPLE);
        headerPanel.setBorder(new EmptyBorder(15, 20, 15, 20));

        JLabel lblTitre = new JLabel("Gestion Des Etudiants");
        lblTitre.setForeground(Color.WHITE);
        lblTitre.setFont(new Font("Arial", Font.BOLD, 22));
        headerPanel.add(lblTitre, BorderLayout.WEST);


        this.add(headerPanel, BorderLayout.NORTH);

        String[] colonnes = {"Numéro", "Nom", "Prénom", "Bac", "Moyenne"};
        Object[][] donnees = {};

        modeleTable = new DefaultTableModel(donnees, colonnes) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false;
            }
        };

        tableEtudiants = new JTable(modeleTable);
        tableEtudiants.setRowHeight(30);
        tableEtudiants.setFont(new Font("Arial", Font.PLAIN, 14));
        tableEtudiants.setFillsViewportHeight(true);

        JTableHeader header = tableEtudiants.getTableHeader();
        header.setBackground(TABLE_HEADER_BG);
        header.setForeground(Color.WHITE);
        header.setFont(new Font("Arial", Font.BOLD, 14));
        header.setPreferredSize(new Dimension(100, 35));

        tableEtudiants.setBackground(new Color(60, 63, 65));
        tableEtudiants.setForeground(Color.WHITE);
        tableEtudiants.setSelectionBackground(SACLAY_PURPLE);
        tableEtudiants.setSelectionForeground(Color.WHITE);
        tableEtudiants.setGridColor(new Color(100, 100, 100));

        JScrollPane scrollPane = new JScrollPane(tableEtudiants);
        scrollPane.getViewport().setBackground(new Color(60, 63, 65));
        
        this.add(scrollPane, BorderLayout.CENTER);
    }

    public void mettreAJourTableau(Object[][] data) {
        modeleTable.setRowCount(0);
        for (Object[] ligne : data) {
            modeleTable.addRow(ligne);
        }
    }
    
    public JButton getBtnImport() { return btnImport; }
}