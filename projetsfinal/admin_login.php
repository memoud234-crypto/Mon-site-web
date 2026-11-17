<?php
require_once 'connexion.php';
$msg = "";
if (isset($_SESSION['admin'])) {
    header('Location: poo.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $requete = "SELECT * FROM EMPLOYE WHERE email = '$email' AND role = 'admin' LIMIT 1";
    $resultat = mysqli_query($connexion, $requete);
    if ($resultat && mysqli_num_rows($resultat) > 0) {
        $admin = mysqli_fetch_assoc($resultat);
        if ($mot_de_passe == $admin['mot_de_passe']) {
            $_SESSION['admin'] = array(
                'id' => $admin['id_employe'],
                'nom' => $admin['nom'],
                'prenom' => $admin['prenom']
            );
            header('Location: poo.php');
            exit;
        }
    }
    $msg = "Identifiants invalides.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="description.html" class="brand">TAULISSA</a>
    <a href="Accueil.php">Accueil</a>
    <a href="menu.html">Menu</a>
    <a href="reservation.php">Réservation</a>
    <a href="commandes.php">Commande</a>
        <a href="admin_login.php">Admin</a>
</nav>
<div class="header"><h1>Connexion Admin</h1><p>Accès à l'administration</p></div>
<div class="container">
    <div class="form-box">
        <h2>Administrateur</h2>
        <?php if ($msg != "") { ?><div class="msg-err"><?php echo $msg; ?></div><?php } ?>
        <form method="POST">
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Mot de passe</label><input type="password" name="mot_de_passe" required></div>
            <button type="submit" class="btn btn-full">Connexion</button>
        </form>
    </div>
</div>
<footer>&copy; 2026 Taulissa Djibouti</footer>
</body>
</html>
