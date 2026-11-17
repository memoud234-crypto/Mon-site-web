<?php
require_once 'admin_guard.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Menu Admin</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
<a href="description.html" class="brand">TAULISSA</a>
<a href="poo.php">Menu admin</a>
<a href="admin_choix.php?action=ajouter">Ajouter</a>
<a href="admin_choix.php?action=afficher">Afficher</a>
<a href="admin_choix.php?action=rechercher">Rechercher</a>
<a href="admin_logout.php">Quitter</a>
</nav>
<div class="header">
<h1>Espace Administration</h1>
<p>Bienvenue <?php echo $_SESSION['admin']['prenom'] . ' ' . $_SESSION['admin']['nom']; ?></p>
</div>
<div class="container">
    <div class="dashboard-links">
        <a href="admin_choix.php?action=ajouter" class="btn">Ajouter</a>
        <a href="admin_choix.php?action=afficher" class="btn">Afficher</a>
        <a href="admin_choix.php?action=rechercher" class="btn">Rechercher</a>
        <a href="admin_logout.php" class="btn btn-gris">Quitter</a>
    </div>
</div>
<footer>&copy; 2026 Taulissa Djibouti</footer>
</body>
</html>
