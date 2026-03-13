package service;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.nio.charset.StandardCharsets;

public class ApiService {

    private static final String BASE_URL = "https://projets.iut-orsay.fr/saes3-ybouzid1/api/"; 
    
    private static String token = null;

    public static void setToken(String nouveauToken) {
        token = nouveauToken;
    }

    public static String envoyerRequete(String endpoint, String method, String jsonInputString) throws Exception {
        
        URL url = new URL(BASE_URL + endpoint);
        HttpURLConnection conn = (HttpURLConnection) url.openConnection();
        
        conn.setRequestMethod(method);
        conn.setRequestProperty("Content-Type", "application/json; utf-8");
        conn.setRequestProperty("Accept", "application/json");
        conn.setDoInput(true);
        
        if (token != null) {
            conn.setRequestProperty("Authorization", "Bearer " + token);
        }

        if (jsonInputString != null && !jsonInputString.isEmpty()) {
            conn.setDoOutput(true);
            try (OutputStream os = conn.getOutputStream()) {
                byte[] input = jsonInputString.getBytes(StandardCharsets.UTF_8);
                os.write(input, 0, input.length);
            }
        }

        int responseCode = conn.getResponseCode();
        
        InputStream inputStream;
        if (responseCode >= 200 && responseCode < 300) {
            inputStream = conn.getInputStream();
        } else {
            inputStream = conn.getErrorStream();
        }

        if (inputStream == null) {
            return "";
        }

        StringBuilder response = new StringBuilder();
        try (BufferedReader br = new BufferedReader(new InputStreamReader(inputStream, StandardCharsets.UTF_8))) {
            String responseLine;
            while ((responseLine = br.readLine()) != null) {
                response.append(responseLine.trim());
            }
        }

        if (responseCode >= 300) {
            throw new Exception("Erreur API (" + responseCode + ") : " + response.toString());
        }

        return response.toString();
    }
}