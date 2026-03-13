<div class="container-public">
    <h1>Groupes constitués (Accès Public)</h1>
    
    <div class="filtres">
        <form action="index.php" method="GET">
            <input type="hidden" name="controleur" value="controleurPublic">
            <input type="hidden" name="action" value="consulterGroupes">
            
            <label for="selectPromo">Choisir une promotion :</label>
            <select name="idPromo" id="selectPromo" onchange="this.form.submit()">
                <?php foreach($lesPromos as $p): ?>
                    <option value="<?= $p['id_promotion'] ?>" <?= ($p['id_promotion'] == $idPromo) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['libelle_promotion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="liste-groupes-public" style="margin-top:20px; display:flex; flex-wrap:wrap; gap:20px;">
        <?php if(empty($lesGroupes)): ?>
            <p>Aucun groupe publié pour cette promotion.</p>
        <?php else: ?>
            <?php foreach($lesGroupes as $groupe): ?>
                <div class="carte-groupe" style="border:1px solid #ccc; padding:15px; border-radius:5px; min-width:250px;">
                    <h3><?= htmlspecialchars($groupe['libelle_groupe']) ?> <small>(<?= $groupe['type_groupe'] ?>)</small></h3>
                    <p>Capacité : <?= $groupe['capacite_max_groupe'] ?></p>
                    <hr>
                    <ul style="padding-left:20px;">
                        <?php if(!empty($groupe['etudiants'])): ?>
                            <?php foreach($groupe['etudiants'] as $etu): ?>
                                <li><?= htmlspecialchars($etu['prenom_etudiant'] . " " . $etu['nom_etudiant']) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><i>Aucun étudiant</i></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>