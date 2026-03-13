<div class="form-container" style="max-width: 900px; margin: 0 auto;">
    <h2 style="color: #63003C; border-bottom: 2px solid #63003C; padding-bottom: 10px;">
        Mon Espace Étudiant
    </h2>

    <div style="display: flex; gap: 30px; flex-wrap: wrap; margin-top: 20px;">
        
        <div style="flex: 1; min-width: 300px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; color: #333;">Mes Préférences de Groupe</h3>
            <p style="color: #666; font-size: 0.9em;">Indiquez les collègues avec qui vous souhaitez travailler (Binôme, Projet, Covoiturage...).</p>
            
            <ul style="background: #f8f9fa; padding: 10px; border-radius: 5px; list-style: none; margin-bottom: 15px;">
                <?php if(empty($mesCollegues)): ?>
                    <li style="color: #888; font-style: italic;">Aucun collègue sélectionné.</li>
                <?php else: ?>
                    <?php foreach($mesCollegues as $c): ?>
                        <li style="padding: 8px; border-bottom: 1px solid #eee; display: flex; align-items: center;">
                            <strong><?= htmlspecialchars($c['prenom_etudiant'] . " " . $c['nom_etudiant']) ?></strong>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <?php if(count($mesCollegues) < $limiteAmis): ?>
                <form action="index.php?controleur=controleurEtudiant&action=ajouterCollegue" method="post" style="display: flex; gap: 10px;">
                    <select name="id_collegue" required style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">-- Sélectionner un étudiant --</option>
                        <?php foreach($tous as $e): ?>
                            <option value="<?= $e['id_etudiant'] ?>">
                                <?= htmlspecialchars($e['nom_etudiant'] . " " . $e['prenom_etudiant']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" style="background: #63003C; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        Ajouter
                    </button>
                </form>
            <?php else: ?>
                <div style="padding: 10px; background: #fff3cd; color: #856404; border-radius: 4px; font-size: 0.9em;">
                    Nombre maximum de préférences atteint (<?= $limiteAmis ?> max).
                </div>
            <?php endif; ?>
        </div>

        <div style="flex: 1; min-width: 300px;">
            <h3 style="margin-top: 0; color: #333;">Sondages en cours</h3>
            <?php if(empty($sondages)): ?>
                <p>Aucun sondage actif pour le moment.</p>
            <?php else: ?>
                <?php foreach($sondages as $s): ?>
                    <div style="background: white; border-left: 4px solid #63003C; padding: 15px; margin-bottom: 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <strong style="font-size: 1.1em;"><?= htmlspecialchars($s['critere_sondage']) ?></strong>
                        <div style="margin-top: 5px; font-size: 0.85em; color: gray;">
                            Type : <?= htmlspecialchars($s['type_sondage']) ?>
                        </div>
                        <form action="index.php?controleur=controleurEtudiant&action=validerSondage" method="post" style="margin-top: 10px;">
                            <input type="hidden" name="id_sondage" value="<?= $s['id_sondage'] ?>">
                            <div style="margin: 10px 0;">
                                <?php foreach($s['options'] as $opt): ?>
                                    <div style="margin-bottom: 5px;">
                                        <input type="radio" name="reponse_unique" 
                                            value="<?= $opt['id_option'] ?>" 
                                            id="opt_<?= $opt['id_option'] ?>" required>
                                        <label for="opt_<?= $opt['id_option'] ?>"><?= htmlspecialchars($opt['texte_option']) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="submit" style="font-size: 0.8em; padding: 4px 8px;">Répondre</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>