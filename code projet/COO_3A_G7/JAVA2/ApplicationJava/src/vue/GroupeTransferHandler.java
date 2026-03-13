package vue;

import java.awt.datatransfer.*;
import javax.swing.*;
import java.util.List;
import modele.Etudiant;
import modele.Groupe;
import controleur.ControleurApplication;

public class GroupeTransferHandler extends TransferHandler {
    
    private ControleurApplication controleur;

    public GroupeTransferHandler(ControleurApplication controleur) {
        this.controleur = controleur;
    }

    private static final DataFlavor FLAVOR_ETUDIANT = new DataFlavor(Etudiant.class, "Etudiant");

    @Override
    protected Transferable createTransferable(JComponent c) {
        JList<Etudiant> list = (JList<Etudiant>) c;
        Etudiant e = list.getSelectedValue();
        if (e == null) return null;
        return new TransferableEtudiant(e);
    }

    @Override
    public int getSourceActions(JComponent c) {
        return MOVE;
    }

    @Override
    public boolean canImport(TransferSupport support) {
        return support.isDataFlavorSupported(FLAVOR_ETUDIANT);
    }

    @Override
    public boolean importData(TransferSupport support) {
        if (!canImport(support)) return false;

        try {
            Etudiant etu = (Etudiant) support.getTransferable().getTransferData(FLAVOR_ETUDIANT);
            
            JList<Etudiant> targetList = (JList<Etudiant>) support.getComponent();
            Groupe groupeCible = (Groupe) targetList.getClientProperty("groupeLie");

            Groupe groupeSource = null;
            for (Groupe g : controleur.getListeGroupes()) {
                if (g.getEtudiantsInscrits().contains(etu)) {
                    groupeSource = g;
                    break;
                }
            }

            if (groupeSource != null && groupeCible != null) {
                controleur.deplacerEtudiant(etu, groupeSource, groupeCible);
                return true;
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    private static class TransferableEtudiant implements Transferable {
        private Etudiant etudiant;
        public TransferableEtudiant(Etudiant e) { this.etudiant = e; }
        public DataFlavor[] getTransferDataFlavors() { return new DataFlavor[]{FLAVOR_ETUDIANT}; }
        public boolean isDataFlavorSupported(DataFlavor flavor) { return flavor.equals(FLAVOR_ETUDIANT); }
        public Object getTransferData(DataFlavor flavor) { return etudiant; }
    }
}