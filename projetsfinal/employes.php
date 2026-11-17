<?php
require_once 'connexion.php';
$msg = ""; $erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter') {
    $nom    = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $poste  = trim($_POST['poste']);
    $sal    = (float)$_POST['salaire'];
    if (!$nom || !$prenom || !$poste) {
        $erreur = "Nom, prénom et poste sont obligatoires.";
    } else {
        $pdo->prepare("INSERT INTO EMPLOYE (nom, prenom, poste, salaire) VALUES (?,?,?,?)")
            ->execute([$nom, $prenom, $poste, $sal]);
        $msg = "Employé $prenom $nom ajouté !";
    }
}
if (isset($_GET['supprimer'])) {
    try {
        $pdo->prepare("DELETE FROM EMPLOYE WHERE id_employe = ?")->execute([(int)$_GET['supprimer']]);
        $msg = "Employé supprimé.";
    } catch (PDOException $e) {
        $erreur = "Impossible : cet employé a des commandes associées.";
    }
}

$employes = $pdo->query("SELECT * FROM EMPLOYE ORDER BY poste, nom")->fetchAll();
$postes   = ['Chef cuisinier','Sous-chef','Serveur','Serveuse','Caissier','Caissière','Gérant','Barman','Plongeur'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Employés — Admin Taulissa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
     <a href="description.html" class="brand">🍽️ TAULISSA</a>
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
<div class="header"><h1>👥 Gestion du Personnel</h1><p>Ajouter et gérer les employés</p></div>
<div class="admin-layout">
<div class="sidebar">
    <p class="titre">Administration</p>
    <a href="poo.php"> Dashboard</a>
    <a href="commandes.php"> Commandes</a>
    <a href="reservation.php"> Réservations</a>
    <a href="plats.php">Plats</a>
    <a href="client.php"> Clients</a>
    <a href="employes.php" class="actif"> Employés</a>
    <a href="tables.php"> Tables</a>
    <a href="factures.php"> Factures</a>
</div>
<div class="admin-content">
    <?php if ($msg): ?><div class="msg-ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($erreur): ?><div class="msg-err"> <?= htmlspecialchars($erreur) ?></div><?php endif; ?>

    <div class="form-box" style="max-width:100%;">
        <h2>Ajouter un employé</h2>
        <form method="POST">
            <input type="hidden" name="action" value="ajouter">
            <div class="form-row">
                <div class="form-group"><label>Nom *</label><input type="text" name="nom" required></div>
                <div class="form-group"><label>Prénom *</label><input type="text" name="prenom" required></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Poste *</label>
                    <select name="poste" required>
                        <?php foreach ($postes as $p): ?><option><?= $p ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Salaire (FDJ)</label>
                    <input type="number" name="salaire" min="0" step="500" placeholder="55000">
                </div>
            </div>
            <button type="submit" class="btn btn-full">Ajouter l'employé</button>
        </form>
    </div>

    <h2>Liste du personnel (<?= count($employes) ?>)</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Nom</th><th>Prénom</th><th>Poste</th><th>Salaire</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($employes as $e): ?>
                <tr>
                    <td><?= $e['id_employe'] ?></td>
                    <td><?= htmlspecialchars($e['nom']) ?></td>
                    <td><?= htmlspecialchars($e['prenom']) ?></td>
                    <td><?= htmlspecialchars($e['poste']) ?></td>
                    <td><?= number_format($e['salaire'], 0, ',', ' ') ?> FDJ</td>
                    <td>
                        <a href="employes.php?supprimer=<?= $e['id_employe'] ?>"
                           class="btn btn-small btn-rouge"
                           onclick="return confirm('Supprimer cet employé ?')">Supprimer</a>
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
