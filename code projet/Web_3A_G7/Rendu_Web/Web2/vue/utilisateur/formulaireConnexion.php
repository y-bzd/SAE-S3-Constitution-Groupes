<div class="form-container">
    <?php if(isset($erreur)) { echo "<div class='alert'>$erreur</div>"; } ?>
    
    <form action="index.php?controleur=controleurUtilisateur&action=connecter" method="post">
        <label for="login">Identifiant :</label>
        <input type="text" name="login" id="login" required placeholder="Ex: responsable1">
        
        <label for="mdp">Mot de passe :</label>
        <input type="password" name="mdp" id="mdp" required>
        
        <button type="submit">Se connecter</button>
    </form>
</div>