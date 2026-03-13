package service;

import org.json.JSONObject;
import modele.Responsable;

public class ConnexionService {

    public Responsable seConnecter(String login, String mdp) {
        try {
            JSONObject json = new JSONObject();
            json.put("identifiantConnexion", login);
            json.put("hashMdp", mdp);

            String reponse = ApiService.envoyerRequete("auth/login", "POST", json.toString());
            
            JSONObject jsonReponse = new JSONObject(reponse);
            
            String tokenRecu = jsonReponse.getString("token");
            ApiService.setToken(tokenRecu);

            JSONObject userJson = jsonReponse.getJSONObject("utilisateur");
            Responsable resp = new Responsable();
            resp.setIdUtilisateur(userJson.getInt("idUtilisateur"));
            resp.setIdentifiantConnexion(userJson.getString("identifiantConnexion"));
            resp.setPorteeResponsable(userJson.optString("porteeResponsable", ""));
            
            return resp;

        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }
}