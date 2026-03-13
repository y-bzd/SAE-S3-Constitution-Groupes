<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pagetitle; ?> - IUT Orsay</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <h2>Gestion Groupes - IUT Paris-Saclay</h2>
        </div>
        <nav>
            <ul>
                <?php if(isset($_SESSION['utilisateur'])) { 
                    $role = $_SESSION['utilisateur']['role'];
                ?>
                    <?php if($role === 'ADMIN' || $role === 'ENSEIGNANT') { ?>
                        <li><a href="index.php?controleur=controleurResponsable&action=gestionEtudiants">Liste Promotion</a></li>
                        <li><a href="index.php?controleur=controleurGroupe&action=lireGroupes">Gestion Groupes</a></li>
                    <?php } ?>

                    <?php if($role === 'ETUDIANT') { ?>
                        <li><a href="index.php?controleur=controleurEtudiant&action=lireEtudiants">Annuaire</a></li>
                        
                        <li><a href="index.php?controleur=controleurPublic&action=consulterGroupes">Groupes Publiés</a></li>
                        
                        <li><a href="index.php?controleur=controleurEtudiant&action=monEspace">Mon Espace</a></li>
                    <?php } ?>
                    
                    <?php if($role === 'ADMIN') { ?>
                        <li><a href="index.php?controleur=controleurResponsable&action=panel" style="color:#ffdd00;">Admin Panel</a></li>
                    <?php } ?>
                    
                    <li><a href="index.php?controleur=controleurUtilisateur&action=deconnecter">Déconnexion</a></li>
                
                <?php } else { ?>
                    <li><a href="index.php?controleur=controleurUtilisateur&action=afficherFormulaireConnexion">Connexion</a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>
    <main>
        <h1><?php echo $pagetitle; ?></h1>