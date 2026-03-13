<?php
$isReadOnly = ($_SESSION['utilisateur']['role'] !== 'ADMIN');
?>

<h2>Gestion de la Promotion</h2>

<?php if (!$isReadOnly): ?>
<div class="form-container" style="max-width:100%; margin-bottom:20px;">
    <h3>Ajouter un étudiant</h3>
    <form action="index.php?controleur=controleurResponsable&action=ajouterEtudiant" method="post" style="display:flex; gap:10px; flex-wrap:wrap;">
        <input type="text" name="numero" placeholder="N° Etu" required style="flex:1;">
        <input type="text" name="nom" placeholder="Nom" required style="flex:2;">
        <input type="text" name="prenom" placeholder="Prénom" required style="flex:2;">
        <input type="email" name="email" placeholder="Email" required style="flex:2;">
        <input type="text" name="bac" placeholder="Bac" style="flex:1;">
        <button type="submit" style="width:auto; background:#28a745;">Ajouter</button>
    </form>
</div>
<?php else: ?>
    <div class="alert" style="background-color:#e2e3e5; color:#383d41; border-color:#d6d8db;">
        Mode Enseignant : Lecture seule. Vous ne pouvez pas modifier les données.
    </div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>N°</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Bac</th>
            <?php if (!$isReadOnly): ?><th>Actions</th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lesEtudiants as $e): ?>
        <tr>
            <?php if (!$isReadOnly): ?>
                <form action="index.php?controleur=controleurResponsable&action=editerEtudiant" method="post">
                    <input type="hidden" name="id_etudiant" value="<?= $e['id_etudiant'] ?>">
                    
                    <td><input type="text" name="numero" value="<?= htmlspecialchars($e['numeroEtudiant']) ?>" style="width:60px;"></td>
                    <td><input type="text" name="nom" value="<?= htmlspecialchars($e['nom']) ?>"></td>
                    <td><input type="text" name="prenom" value="<?= htmlspecialchars($e['prenom']) ?>"></td>
                    <td><input type="text" name="email" value="<?= htmlspecialchars($e['email']) ?>"></td>
                    <td><input type="text" name="bac" value="<?= htmlspecialchars($e['typeBac']) ?>" style="width:50px;"></td>
                    <td style="white-space:nowrap;">
                        <button type="submit" style="padding:5px; font-size:0.8em; width:auto; cursor:pointer;">Sauvegarder</button>
                        <a href="index.php?controleur=controleurResponsable&action=supprimerEtudiant&id=<?= $e['id_etudiant'] ?>" 
                           onclick="return confirm('Confirmer suppression ?')" 
                           style="color:white; background:red; padding:5px 8px; text-decoration:none; border-radius:4px;">Supprimer</a>
                    </td>
                </form>
            <?php else: ?>
                <td><?= htmlspecialchars($e['numeroEtudiant']) ?></td>
                <td><?= htmlspecialchars($e['nom']) ?></td>
                <td><?= htmlspecialchars($e['prenom']) ?></td>
                <td><?= htmlspecialchars($e['email']) ?></td>
                <td><?= htmlspecialchars($e['typeBac']) ?></td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>