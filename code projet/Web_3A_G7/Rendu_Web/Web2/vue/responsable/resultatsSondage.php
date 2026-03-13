<div class="form-container" style="max-width:900px; margin:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #63003C; padding-bottom:10px;">
        <h2 style="margin:0;">Résultats : <?= htmlspecialchars($resultats['info']['critere_sondage']) ?></h2>
        <a href="index.php?controleur=controleurResponsable&action=panel" style="background:#6c757d; color:white; padding:5px 10px; text-decoration:none; border-radius:4px;">← Retour</a>
    </div>

    <div style="display:flex; gap:30px; margin-top:30px; flex-wrap:wrap;">
        
        <div style="flex:1; min-width:300px;">
            <h3 style="color:#63003C;">Synthèse des votes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Option</th>
                        <th style="width:80px; text-align:center;">Votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($resultats['stats'])): ?>
                        <tr><td colspan="2" style="font-style:italic;">Aucun vote enregistré.</td></tr>
                    <?php else: ?>
                        <?php foreach($resultats['stats'] as $stat): ?>
                        <tr>
                            <td><?= htmlspecialchars($stat['texte_option']) ?></td>
                            <td style="text-align:center; font-weight:bold; font-size:1.1em;"><?= $stat['nb_votes'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="flex:1; min-width:300px;">
            <h3 style="color:#63003C;">Détail par étudiant</h3>
            <div style="max-height:400px; overflow-y:auto; border:1px solid #ccc; background:white;">
                <table style="margin-bottom:0;">
                    <thead>
                        <tr style="position:sticky; top:0; background:#eee;">
                            <th>Étudiant</th>
                            <th>Choix</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($resultats['details'])): ?>
                            <tr><td colspan="2" style="padding:15px;">Aucune donnée.</td></tr>
                        <?php else: ?>
                            <?php foreach($resultats['details'] as $det): ?>
                            <tr>
                                <td><?= htmlspecialchars($det['nom_etudiant'] . ' ' . $det['prenom_etudiant']) ?></td>
                                <td style="color:#555;"><?= htmlspecialchars($det['texte_option']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>