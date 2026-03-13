<h2>Espace Responsable</h2>

<div style="display: flex; flex-wrap: wrap; gap: 20px;">
    
    <div class="form-container" style="flex: 1; min-width: 300px;">
        <h3>1. Importer des Notes</h3>
        <p style="font-size:0.9em; color:#666;">Format CSV attendu : NumEtudiant;Matiere;Note</p>
        <form action="index.php?controleur=controleurResponsable&action=traiterImport" method="post" enctype="multipart/form-data">
            <input type="file" name="csv" required>
            <button type="submit">Importer le fichier</button>
        </form>
    </div>

    <div class="form-container" style="flex: 1; min-width: 300px;">
        <h3>2. Exporter les Données</h3>
        <p style="font-size:0.9em; color:#666;">Télécharger l'intégralité des données étudiants (Infos persos + Pédago).</p>
        <a href="index.php?controleur=controleurResponsable&action=exportCsv">
            <button style="background-color:#007bff;">Télécharger CSV Complet</button>
        </a>
    </div>

</div>

<div class="form-container" style="max-width: 100%; margin-top: 20px;">
    <h3>3. Configuration des Contraintes de Groupes</h3>
    <p style="font-size:0.9em;">Ces contraintes seront utilisées par l'algorithme de répartition.</p>
    
    <table style="width:100%; margin-bottom:15px; border-collapse: collapse;">
        <tr style="background:#eee;">
            <th style="padding:8px; border:1px solid #ddd;">Type</th>
            <th style="padding:8px; border:1px solid #ddd;">Paramètres</th>
            <th style="padding:8px; border:1px solid #ddd;">Action</th>
        </tr>
        <?php if(!empty($lesContraintes)): ?>
            <?php foreach($lesContraintes as $c): ?>
            <tr>
                <td style="padding:8px; border:1px solid #ddd;"><?= htmlspecialchars($c['type_contrainte']) ?></td>
                <td style="padding:8px; border:1px solid #ddd;"><?= htmlspecialchars($c['parametres_contrainte']) ?></td>
                <td style="padding:8px; border:1px solid #ddd; text-align:center;">
                    <a href="index.php?controleur=controleurResponsable&action=supprimerContrainte&id=<?= $c['id_contrainte'] ?>" 
                       style="color:red; text-decoration:none;">[Supprimer]</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" style="padding:8px; text-align:center;">Aucune contrainte définie.</td></tr>
        <?php endif; ?>
    </table>

    <form action="index.php?controleur=controleurResponsable&action=ajouterContrainte" method="post" style="background:#f9f9f9; padding:15px; border:1px solid #eee;">
        <h4 style="margin-top:0;">Ajouter une nouvelle contrainte</h4>
        <div style="display:flex; gap:10px;">
            <select name="type" style="flex:1;">
                <option value="Max_Etu">Effectif Max par groupe</option>
                <option value="Min_Etu">Effectif Min par groupe</option>
                <option value="Mixite">Mixité (Genre)</option>
                <option value="Max_Amis">Limite Covoiturage</option>
                <option value="Niveau">Niveau Homogène (Moyenne)</option>
                <option value="Logiciel">Logiciel Requis</option>
                <option value="Covoiturage">Covoiturage Prioritaire</option>
            </select>
            <input type="text" name="param" placeholder="Valeur (ex: 28, Java...)" required style="flex:2;">
            <button type="submit" style="flex:1; background-color: #28a745; margin:0;">Ajouter</button>
        </div>
    </form>
</div>

<div class="form-container" style="max-width: 100%;">
    <h3>4. Gestion des Sondages</h3>
    
    <?php if(!empty($lesSondages)): ?>
        <table style="width:100%; margin-bottom:20px;">
            <thead>
                <tr style="background:#e9ecef;">
                    <th style="padding:10px;">Question</th>
                    <th style="padding:10px; width:100px;">Type</th>
                    <th style="padding:10px; width:150px; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($lesSondages as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['critere_sondage']) ?></td>
                        <td><span style="background:#eee; padding:2px 6px; border-radius:3px; font-size:0.8em;"><?= htmlspecialchars($s['type_sondage']) ?></span></td>
                        <td style="text-align:center;">
                            <a href="index.php?controleur=controleurResponsable&action=voirResultats&id_sondage=<?= $s['id_sondage'] ?>"
                               style="background:#17a2b8; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:0.9em; display:inline-block;">
                            Voir Résultats
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <form action="index.php?controleur=controleurResponsable&action=creerSondage" method="post" style="background:#f8f9fa; padding:20px; border:1px solid #ddd; border-radius:5px;">
        <h4 style="margin-top:0; color:#495057;">Nouveau Sondage</h4>
        <div style="display:flex; flex-wrap:wrap; gap:15px;">
            <div style="flex:1; min-width:250px;">
                <label>Question :</label>
                <input type="text" name="titre" required placeholder="Ex: Choix de la majeure ?" style="margin-top:5px;">
            </div>
            <div style="flex:1; min-width:250px;">
                <label>Options (séparées par virgules) :</label>
                <input type="text" name="options" placeholder="Ex: Big Data, Cyber-Sécurité, Dév Web" style="margin-top:5px;">
            </div>
        </div>
        <button type="submit" style="margin-top:10px; width:auto;">Publier le sondage</button>
    </form>
</div>

<div class="form-container" style="border-top: 5px solid #63003C;">
    <h3>Génération Automatique des Groupes</h3>
    <p>Sélectionnez l'algorithme Java (porté en PHP) à exécuter :</p>
    
    <form action="index.php?controleur=controleurResponsable&action=genererAuto" method="post" 
          style="background:#fff3cd; padding:15px; border:1px solid #ffeeba;">
        
        <input type="hidden" name="token_csrf" value="<?= $_SESSION['token_csrf'] ?? '' ?>">

        <label for="algo">Choix de l'algorithme :</label>
        <select name="algo" id="algo" required style="font-weight:bold;">
            <optgroup label="Algorithmes Gloutons (Rapides)">
                <option value="g_distributeur">1. Glouton Distributeur</option>
                <option value="g_compensateur">2. Glouton Compensateur</option>
                <option value="g_covoit_equilibre">3. Glouton Covoit' (Mode Équilibre)</option>
                <option value="g_covoit_niveau">4. Glouton Covoit' (Mode Serpent)</option>
            </optgroup>
            <optgroup label="Algorithmes Force Brute (Lents - Optimaux)">
                <option value="fb_simple">5. Force Brute Simple (Yassine)</option>
                <option value="fb_blocs">6. Force Brute par Blocs (Youcef)</option>
            </optgroup>
        </select>
        
        <button type="submit" style="background-color:#d39e00; color:white; font-weight:bold; margin-top:10px;">
            Lancer la génération
        </button>
        <p style="font-size:0.8em; color:#666; margin-top:5px;">
            Les algorithmes "Covoit" respectent les préférences d'amis saisies dans l'Espace Étudiant.
        </p>
    </form>
</div>