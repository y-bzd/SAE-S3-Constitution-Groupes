package modele;

public class Utilisateur {
    protected int idUtilisateur;
    protected String identifiantConnexion;
    protected String hashMdp;
    protected Role role;

    public Utilisateur() {}

    public boolean seConnecter() {
        return this.idUtilisateur > 0;
    }

    public int getIdUtilisateur() { return idUtilisateur; }
    public void setIdUtilisateur(int idUtilisateur) { this.idUtilisateur = idUtilisateur; }
    public String getIdentifiantConnexion() { return identifiantConnexion; }
    public void setIdentifiantConnexion(String identifiantConnexion) { this.identifiantConnexion = identifiantConnexion; }
    public String getHashMdp() { return hashMdp; }
    public void setHashMdp(String hashMdp) { this.hashMdp = hashMdp; }
    public Role getRole() { return role; }
    public void setRole(Role role) { this.role = role; }


}