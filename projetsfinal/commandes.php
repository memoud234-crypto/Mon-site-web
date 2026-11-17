<?php
require_once 'connexion.php';
$erreur = "";
$plats = mysqli_query($connexion, "SELECT id_plat, nom_plat, prix, stock FROM PLAT ORDER BY id_plat ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $date_commande = $_POST['date_commande'];
    $heure_commande = $_POST['heure_commande'];
    $id_plat = $_POST['id_plat'];
    $quantite = (int)$_POST['quantite'];

    $aujourdhui = date('Y-m-d');
    $heureActuelle = date('H:i');

    if ($nom == '' || $prenom == '' || $telephone == '' || $date_commande == '' || $heure_commande == '' || $id_plat == '' || $quantite <= 0) {
        $erreur = "Veuillez remplir tous les champs.";
    } elseif (!djibouti_phone_valide($telephone)) {
        $erreur = "Téléphone invalide. Format attendu : +25377358804 ou 77358804.";
    } elseif ($date_commande < $aujourdhui) {
        $erreur = "La date de commande doit être supérieure ou égale à aujourd'hui.";
    } elseif ($heure_commande < '07:00' || $heure_commande > '23:00') {
        $erreur = "L'heure de commande doit être entre 07:00 et 23:00.";
    } elseif ($date_commande == $aujourdhui && $heure_commande < $heureActuelle) {
        $erreur = "L'heure de commande ne peut pas être passée pour aujourd'hui.";
    } else {
        $stockReq = mysqli_query($connexion, "SELECT stock FROM PLAT WHERE id_plat = '" . mysqli_real_escape_string($connexion, $id_plat) . "' LIMIT 1");
        if (!$stockReq || mysqli_num_rows($stockReq) == 0) {
            $erreur = "Plat introuvable.";
        } else {
            $stockRow = mysqli_fetch_assoc($stockReq);
            if ($quantite > (int)$stockRow['stock']) {
                $erreur = "On n'a pas assez de plats en stock.";
            } else {
                $cherche = mysqli_query($connexion, "SELECT id_client FROM CLIENT WHERE telephone = '" . mysqli_real_escape_string($connexion, $telephone) . "' LIMIT 1");
                if (mysqli_num_rows($cherche) > 0) {
                    $ligne = mysqli_fetch_assoc($cherche);
                    $id_client = $ligne['id_client'];
                } else {
                    mysqli_query($connexion, "INSERT INTO CLIENT (nom, prenom, telephone, adresse) VALUES ('" . mysqli_real_escape_string($connexion, $nom) . "', '" . mysqli_real_escape_string($connexion, $prenom) . "', '" . mysqli_real_escape_string($connexion, $telephone) . "', '" . mysqli_real_escape_string($connexion, $adresse) . "')");
                    $id_client = mysqli_insert_id($connexion);
                }

                $requete1 = "INSERT INTO COMMANDE (date_commande, heure_commande, statut_commande, id_client) VALUES ('$date_commande', '$heure_commande', 'En attente', '$id_client')";
                if (mysqli_query($connexion, $requete1)) {
                    $id_commande = mysqli_insert_id($connexion);
                    $requete2 = "INSERT INTO DETAIL_COMMANDE (id_commande, id_plat, quantite) VALUES ('$id_commande', '$id_plat', '$quantite')";
                    if (mysqli_query($connexion, $requete2)) {
                        $nouveauStock = ((int)$stockRow['stock']) - $quantite;
                        mysqli_query($connexion, "UPDATE PLAT SET stock = '$nouveauStock' WHERE id_plat = '$id_plat'");
                        set_flash_message('success', 'Commande enregistrée avec succès.');
                        header('Location: Accueil.php');
                        exit;
                    } else {
                        $erreur = "Erreur sur le détail de commande.";
                    }
                } else {
                    $erreur = "Erreur lors de la commande.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande — Taulissa</title>
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
<div class="header">
    <h1>Commande</h1>
    <p>Inscription puis commande</p>
</div>
<div class="container">
    <div class="form-box">
        <h2>Nouvelle commande</h2>
        <?php if ($erreur != "") { ?><div class="msg-err"><?php echo $erreur; ?></div><?php } ?>
        <form method="POST">
            <div class="form-row">
                <div class="form-group"><label>Nom *</label><input type="text" name="nom" required></div>
                <div class="form-group"><label>Prénom *</label><input type="text" name="prenom" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Téléphone *</label><input type="text" name="telephone" pattern="^(\+253\d{8}|\d{8})$" required></div>
                <div class="form-group"><label>Adresse</label><input type="text" name="adresse"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Date commande *</label><input type="date" name="date_commande" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" required></div>
                <div class="form-group"><label>Heure commande *</label><input type="time" name="heure_commande" min="07:00" max="23:00" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Plat *</label>
                    <select name="id_plat" required>
                        <option value="">Choisir un plat</option>
                        <?php while ($p = mysqli_fetch_assoc($plats)) { ?>
                        <option value="<?php echo $p['id_plat']; ?>"><?php echo $p['nom_plat'] . ' - ' . number_format($p['prix'],0,',',' ') . ' FDJ'; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group"><label>Quantité *</label><input type="number" name="quantite" min="1" required></div>
            </div>
            <button type="submit" class="btn btn-full">Valider la commande</button>
        </form>
    </div>
</div>
<footer>&copy; 2026 Taulissa Djibouti</footer>
</body>
</html>
