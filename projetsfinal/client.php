<?php
require_once 'connexion.php';
$msg = "";
$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);

    if ($nom == '' || $prenom == '' || $telephone == '') {
        $erreur = "Veuillez remplir les champs obligatoires.";
    } else {
        $verif = mysqli_query($connexion, "SELECT id_client FROM CLIENT WHERE telephone = '$telephone'");
        if (mysqli_num_rows($verif) > 0) {
            $erreur = "Ce numéro existe déjà.";
        } else {
            $requete = "INSERT INTO CLIENT (nom, prenom, telephone, adresse) VALUES ('$nom', '$prenom', '$telephone', '$adresse')";
            if (mysqli_query($connexion, $requete)) {
                $msg = "Inscription enregistrée avec succès.";
            } else {
                $erreur = "Erreur lors de l'enregistrement.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Client — Taulissa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="description.html" class="brand">TAULISSA</a>
    <a href="Accueil.html">Accueil</a>
    <a href="menu.html">Menu</a>
    <a href="reservation.php">Réservation</a>
    <a href="commandes.php">Commande</a>
        <a href="admin_login.php">Admin</a>
</nav>
<div class="header">
    <h1>Inscription Client</h1>
    <p>Créez votre fiche client</p>
</div>
<div class="container">
    <div class="form-box">
        <h2>Formulaire client</h2>
        <?php if ($msg != "") { ?><div class="msg-ok"><?php echo $msg; ?></div><?php } ?>
        <?php if ($erreur != "") { ?><div class="msg-err"><?php echo $erreur; ?></div><?php } ?>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="nom" required>
                </div>
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Téléphone *</label>
                    <input type="text" name="telephone" required>
                </div>
                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" name="adresse">
                </div>
            </div>
            <button type="submit" class="btn btn-full">S'inscrire</button>
        </form>
    </div>
</div>
<footer>&copy; 2026 Taulissa Djibouti</footer>
</body>
</html>
