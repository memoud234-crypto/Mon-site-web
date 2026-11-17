<?php
require_once 'admin_guard.php';
$action = isset($_GET['action']) ? $_GET['action'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Choix Table</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
<a href="description.html" class="brand">TAULISSA</a>
<a href="poo.php">Menu admin</a>
<a href="admin_logout.php">Quitter</a>
</nav>
<div class="header"><h1><?php echo ucfirst($action); ?></h1><p>Choisir une table</p></div>
<div class="container">
    <div class="dashboard-links">
        <a href="admin_gestion.php?action=<?php echo $action; ?>&table=CLIENT" class="btn">CLIENT</a>
        <a href="admin_gestion.php?action=<?php echo $action; ?>&table=EMPLOYE" class="btn">EMPLOYE</a>
        <a href="admin_gestion.php?action=<?php echo $action; ?>&table=TABLE_RESTAURANT" class="btn">TABLE</a>
        <a href="admin_gestion.php?action=<?php echo $action; ?>&table=PLAT" class="btn">PLAT</a>
        <a href="admin_gestion.php?action=<?php echo $action; ?>&table=COMMANDE" class="btn">COMMANDE</a>
        <a href="admin_gestion.php?action=<?php echo $action; ?>&table=RESERVATION" class="btn">RESERVATION</a>
        <a href="admin_gestion.php?action=<?php echo $action; ?>&table=FACTURE" class="btn">FACTURE</a>
    </div>
</div>
<footer>&copy; 2026 Taulissa Djibouti</footer>
</body>
</html>
