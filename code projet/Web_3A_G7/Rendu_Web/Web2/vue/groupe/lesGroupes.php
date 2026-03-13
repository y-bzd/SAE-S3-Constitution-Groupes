<h2>Constitution des Groupes</h2>

<?php 
$isAdmin = (isset($_SESSION['utilisateur']) && $_SESSION['utilisateur']['role'] === 'ADMIN');
?>

<?php if ($isAdmin): ?>
<div class="form-container" style="max-width: 100%; margin: 10px 0; padding: 15px; background: #e9ecef;">
    <form action="index.php?controleur=controleurGroupe&action=creerGroupe" method="post" style="display:flex; gap:10px; align-items:center; margin:0;">
        <strong>Nouveau Groupe :</strong>
        <input type="text" name="libelle" placeholder="Nom (ex: TP A)" required style="width:150px; margin:0;">
        <select name="type" style="width:120px; margin:0;">
            <option value="TD">TD</option>
            <option value="TP">TP</option>
            <option value="Projet">Projet</option>
        </select>
        <input type="number" name="capacite" placeholder="Capacité" value="25" required style="width:80px; margin:0;">
        <button type="submit" style="background-color:#28a745; margin:0; width:auto;">+ Créer</button>
    </form>
</div>
<?php endif; ?>

<div class="dashboard-container">

    <div class="col-sans-groupe">
        <h3 style="margin-top:0; font-size:1.1em; color:#495057;">Non affectés (<?= count($sansGroupe) ?>)</h3>
        <div style="max-height: 600px; overflow-y: auto; background:white; border:1px solid #ddd;">
            <?php foreach ($sansGroupe as $e): ?>
                <div class="student-item">
                    <div>
                        <strong><?= htmlspecialchars($e['nom_etudiant'] . ' ' . $e['prenom_etudiant']) ?></strong><br>
                        <span class="badge <?= ($e['genre_etudiant']=='F' || $e['genre_etudiant']=='Femme') ? 'badge-f' : 'badge-h' ?>">
                            <?= $e['genre_etudiant'] ?>
                        </span>
                        <?php if($e['moyenne']): ?>
                            <span class="badge badge-note"><?= number_format($e['moyenne'], 2) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($isAdmin): ?>
                    <form action="index.php?controleur=controleurGroupe&action=affecter" method="POST">
                        <input type="hidden" name="id_etudiant" value="<?= $e['id_etudiant'] ?>">
                        <select name="id_groupe" onchange="this.form.submit()" style="width: 100px; font-size:0.8em; margin:0;">
                            <option value="">Déplacer...</option>
                            <?php foreach ($lesGroupes as $gOpt): ?>
                                <option value="<?= $gOpt['id_groupe'] ?>"><?= htmlspecialchars($gOpt['libelle_groupe']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if(empty($sansGroupe)) echo "<p style='padding:10px; font-style:italic;'>Tous les étudiants sont affectés.</p>"; ?>
        </div>
    </div>

    <?php foreach ($lesGroupes as $g): 
        $remplissage = $g['stats']['nb_etudiants'];
        $max = $g['capacite_max_groupe'];
        $headerColor = ($remplissage > $max) ? "#dc3545" : (($remplissage == $max) ? "#28a745" : "#63003C");
    ?>
    <div class="groupe-card">
        <div class="groupe-header" style="background-color: <?= $headerColor ?>;">
            <div style="font-weight:bold; font-size:1.1em;">
                <?= htmlspecialchars($g['libelle_groupe']) ?>
                
                <?php if($isAdmin): ?>
                    <a href="index.php?controleur=controleurGroupe&action=supprimerGroupe&id_groupe=<?= $g['id_groupe'] ?>" 
                       onclick="return confirm('Supprimer ce groupe et désaffecter les étudiants ?');"
                       style="float:right; color:white; text-decoration:none; margin-left:10px; font-size:0.9em;"
                       title="Supprimer le groupe">
                       X
                    </a>
                <?php endif; ?>

                <span style="float:right; font-size:0.8em; opacity:0.9;">
                    <?= $remplissage ?> / <?= $max ?>
                </span>
            </div>
            <div style="font-size:0.8em; margin-top:5px;">
                <?= htmlspecialchars($g['type_groupe']) ?>
            </div>
        </div>

        <div class="stats-bar">
            <span title="Moyenne générale">Moyenne <?= $g['stats']['moyenne_groupe'] ?></span>
            <span title="Mixité (F/H)">
                <span style="color:#e83e8c;">F:<?= $g['stats']['nb_femmes'] ?></span> / 
                <span style="color:#007bff;">H:<?= $g['stats']['nb_hommes'] ?></span>
            </span>
        </div>

        <div class="stats-bar" style="background:#fff; border-bottom:1px solid #eee; font-size:0.75em; color:#555; display:block;">
            <div title="Nombre de redoublants">
                Redoublants : <strong><?= $g['stats']['nb_redoublants'] ?></strong>
            </div>
            <div title="Répartition des bacs" style="margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                <?= htmlspecialchars($g['stats']['repartition_bacs']) ?>
            </div>
        </div>

        <div style="max-height: 450px; overflow-y: auto;"> 
            <?php foreach ($g['etudiants'] as $etu): ?>
                <div class="student-item">
                    <div>
                        <?= htmlspecialchars($etu['nom_etudiant'] . ' ' . $etu['prenom_etudiant']) ?>
                            
                        <?php 
                            $redoub = trim($etu['periode_redoublement'] ?? '');
                            if(!empty($redoub) && $redoub !== 'Non' && $redoub !== '0'): 
                        ?>
                            <span style="color:orange; font-weight:bold;" title="Redoublant (<?= htmlspecialchars($redoub) ?>)">R</span>
                        <?php endif; ?>

                        <?php if($etu['moyenne']): ?>
                            <small style="color:#888;">(<?= number_format($etu['moyenne'], 1) ?>)</small>
                        <?php endif; ?>
                    </div>
                    <?php if($isAdmin): ?>
                    <form action="index.php?controleur=controleurGroupe&action=affecter" method="POST" style="margin:0;">
                        <input type="hidden" name="id_etudiant" value="<?= $etu['id_etudiant'] ?>">
                        <button type="submit" name="id_groupe" value="" title="Sortir du groupe" 
                                style="background:none; border:none; color:red; cursor:pointer; font-weight:bold;">
                            ✕
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>