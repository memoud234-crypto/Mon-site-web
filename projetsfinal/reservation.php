<?php
require_once 'connexion.php';
$erreur = "";
$tables = mysqli_query($connexion, "SELECT * FROM TABLE_RESTAURANT WHERE capacite BETWEEN 2 AND 10 ORDER BY capacite, numero_table");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $date = $_POST['date_reservation'];
    $heure = $_POST['heure'];
    $id_table = $_POST['id_table'];
    $nombre = $_POST['nombre_personne'];

    $aujourdhui = date('Y-m-d');
    $heureActuelle = date('H:i');

    if ($nom == '' || $prenom == '' || $telephone == '' || $date == '' || $heure == '' || $id_table == '' || $nombre == '') {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } elseif (!djibouti_phone_valide($telephone)) {
        $erreur = "Téléphone invalide. Format attendu : +25377358804 ou 77358804.";
    } elseif ($date < $aujourdhui) {
        $erreur = "La date de réservation doit être supérieure ou égale à aujourd'hui.";
    } elseif ($heure < '07:00' || $heure > '23:00') {
        $erreur = "L'heure de réservation doit être entre 07:00 et 23:00.";
    } elseif ($date == $aujourdhui && $heure < $heureActuelle) {
        $erreur = "L'heure de réservation ne peut pas être passée pour aujourd'hui.";
    } else {
        $verif_table = mysqli_query($connexion, "SELECT id_reservation FROM RESERVATION WHERE id_table = '$id_table' AND date_reservation = '$date' AND heure = '$heure'");
        if (mysqli_num_rows($verif_table) > 0) {
            $erreur = "Table occupée.";
        } else {
            $cherche = mysqli_query($connexion, "SELECT id_client FROM CLIENT WHERE telephone = '" . mysqli_real_escape_string($connexion, $telephone) . "' LIMIT 1");
            if (mysqli_num_rows($cherche) > 0) {
                $ligne = mysqli_fetch_assoc($cherche);
                $id_client = $ligne['id_client'];
            } else {
                mysqli_query($connexion, "INSERT INTO CLIENT (nom, prenom, telephone, adresse) VALUES ('" . mysqli_real_escape_string($connexion, $nom) . "', '" . mysqli_real_escape_string($connexion, $prenom) . "', '" . mysqli_real_escape_string($connexion, $telephone) . "', '" . mysqli_real_escape_string($connexion, $adresse) . "')");
                $id_client = mysqli_insert_id($connexion);
            }

            $requete = "INSERT INTO RESERVATION (date_reservation, heure, nombre_personne, id_client, id_table) VALUES ('$date', '$heure', '$nombre', '$id_client', '$id_table')";
            if (mysqli_query($connexion, $requete)) {
                set_flash_message('success', 'Réservation enregistrée avec succès.');
                header('Location: Accueil.php');
                exit;
            } else {
                $erreur = "Erreur lors de la réservation.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation — Taulissa</title>
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
    <h1>Réservation</h1>
    <p>Inscription puis réservation</p>
</div>
<div class="container">
    <div class="form-box">
        <h2>Formulaire</h2>
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
                <div class="form-group"><label>Date *</label><input type="date" name="date_reservation" min="<?php echo date('Y-m-d'); ?>" required></div>
                <div class="form-group"><label>Heure *</label><input type="time" name="heure" min="07:00" max="23:00" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Table *</label>
                    <select name="id_table" id="id_table" required onchange="mettreCapacite()">
                        <option value="">Choisir une table</option>
                        <?php while ($t = mysqli_fetch_assoc($tables)) { ?>
                        <option value="<?php echo $t['id_table']; ?>" data-capacite="<?php echo $t['capacite']; ?>">Table capacité <?php echo $t['capacite']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <input type="hidden" name="nombre_personne" id="nombre_personne">
            <button type="submit" class="btn btn-full">Confirmer la réservation</button>
        </form>
    </div>
</div>
<script>
function mettreCapacite() {
    var table = document.getElementById('id_table');
    var option = table.options[table.selectedIndex];
    var capacite = option.getAttribute('data-capacite');
    document.getElementById('nombre_personne').value = capacite ? capacite : '';
}
</script>
<footer>&copy; 2026 Taulissa Djibouti</footer>
</body>
</html>
