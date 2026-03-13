<table>
    <thead>
        <tr>
            <th>Numéro</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Bac</th>
            <th>Moyenne</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lesEtudiants as $etu) { ?>
            <tr>
                <td><?php echo htmlspecialchars($etu['numeroEtudiant']); ?></td>
                <td><?php echo htmlspecialchars($etu['nom']); ?></td>
                <td><?php echo htmlspecialchars($etu['prenom']); ?></td>
                <td><?php echo htmlspecialchars($etu['typeBac'] ?? 'N/A'); ?></td>
                <td>
                    <?php 
                        if (!empty($etu['notes'])) {
                            $somme = 0;
                            foreach($etu['notes'] as $n) $somme += $n['valeur_note'];
                            echo number_format($somme / count($etu['notes']), 2);
                        } else {
                            echo "-";
                        }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>