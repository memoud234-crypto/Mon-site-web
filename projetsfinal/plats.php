<?php
require_once 'connexion.php';
$msg = ""; $erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter') {
    $nom  = trim($_POST['nom_plat']);
    $prix = (float)$_POST['prix'];
    $cat  = trim($_POST['categorie']);
    if (!$nom || $prix <= 0) {
        $erreur = "Le nom et le prix sont obligatoires.";
    } else {
        $pdo->prepare("INSERT INTO PLAT (nom_plat, prix, categorie) VALUES (?,?,?)")
            ->execute([$nom, $prix, $cat]);
        $msg = "Plat « $nom » ajouté !";
    }
}
if (isset($_GET['supprimer'])) {
    try {
        $pdo->prepare("DELETE FROM PLAT WHERE id_plat = ?")->execute([(int)$_GET['supprimer']]);
        $msg = "Plat supprimé.";
    } catch (PDOException $e) {
        $erreur = "Impossible : ce plat est utilisé dans des commandes.";
    }
}

$plats = $pdo->query("SELECT * FROM PLAT ORDER BY categorie, nom_plat")->fetchAll();
$cats  = ['Entrées','Viandes','Burgers','Pates','Pizzas','Sushis','Fruits de mer','Boissons','Desserts','Autre'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Plats — Admin Taulissa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
     <a href="description.html" class="brand"> TAULISSA</a>
    <a href="Accueil.html">← Site</a>
    <a href="poo.php">Dashboard</a>
    <a href="commandes.php">Commandes</a>
    <a href="reservation.php">Réservations</a>
    <a href="plats.php">Plats</a>
    <a href="client.php">Clients</a>
    <a href="employes.php">Employés</a>
    <a href="tables.php">Tables</a>
    <a href="factures.php">Factures</a>
</nav>
<div class="header"><h1> Gestion des Plats</h1><p>Ajouter ou supprimer des plats</p></div>
<div class="admin-layout">
<div class="sidebar">
    <p class="titre">Administration</p>
    <a href="poo.php">Dashboard</a>
    <a href="commandes.php"> Commandes</a>
    <a href="reservation.php"> Réservations</a>
    <a href="plats.php" class="actif"> Plats</a>
    <a href="client.php"> Clients</a>
    <a href="employes.php"> Employés</a>
    <a href="tables.php"> Tables</a>
    <a href="factures.php"> Factures</a>
</div>
<div class="admin-content">
    <?php if ($msg): ?><div class="msg-ok"> <?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($erreur): ?><div class="msg-err"> <?= htmlspecialchars($erreur) ?></div><?php endif; ?>

    <div class="form-box" style="max-width:100%;">
        <h2>Ajouter un plat</h2>
        <form method="POST">
            <input type="hidden" name="action" value="ajouter">
            <div class="form-row">
                <div class="form-group">
                    <label>Nom du plat *</label>
                    <input type="text" name="nom_plat" required placeholder="Ex : Poulet rôti">
                </div>
                <div class="form-group">
                    <label>Prix (FDJ) *</label>
                    <input type="number" name="prix" required min="0" step="50" placeholder="Ex : 2500">
                </div>
            </div>
            <div class="form-group">
                <label>Catégorie</label>
                <select name="categorie">
                    <?php foreach ($cats as $c): ?><option><?= $c ?></option><?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-full">Ajouter le plat</button>
        </form>
    </div>

    <h2>Menu actuel (<?= count($plats) ?> plats)</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Nom du plat</th><th>Catégorie</th><th>Prix</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($plats as $p): ?>
                <tr>
                    <td><?= $p['id_plat'] ?></td>
                    <td><?= htmlspecialchars($p['nom_plat']) ?></td>
                    <td><?= htmlspecialchars($p['categorie']) ?></td>
                    <td style="font-weight:bold; color:#8B0000;"><?= number_format($p['prix'], 0, ',', ' ') ?> FDJ</td>
                    <td>
                        <a href="plats.php?supprimer=<?= $p['id_plat'] ?>"
                           class="btn btn-small btn-rouge"
                           onclick="return confirm('Supprimer ce plat ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<footer>2026 Taulissa Djibouti</footer>
</body>
</html>
