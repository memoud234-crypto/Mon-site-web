<?php
require_once 'connexion.php';
$msg = ""; $erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter') {
    $num = (int)$_POST['numero_table'];
    $cap = (int)$_POST['capacite'];
    if ($num <= 0 || $cap <= 0) {
        $erreur = "Numéro et capacité sont obligatoires.";
    } else {
        $pdo->prepare("INSERT INTO TABLE_RESTAURANT (numero_table, capacite) VALUES (?,?)")
            ->execute([$num, $cap]);
        $msg = "Table $num ajoutée !";
    }
}
if (isset($_GET['supprimer'])) {
    try {
        $pdo->prepare("DELETE FROM TABLE_RESTAURANT WHERE id_table = ?")->execute([(int)$_GET['supprimer']]);
        $msg = "Table supprimée.";
    } catch (PDOException $e) {
        $erreur = "Impossible : cette table a des commandes ou réservations.";
    }
}

$tables = $pdo->query("SELECT * FROM TABLE_RESTAURANT ORDER BY numero_table")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tables — Admin Taulissa</title>
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
<div class="header"><h1>🪑 Gestion des Tables</h1><p>Ajouter et gérer les tables du restaurant</p></div>
<div class="admin-layout">
<div class="sidebar">
    <p class="titre">Administration</p>
    <a href="poo.php">📊 Dashboard</a>
    <a href="commandes.php">🧾 Commandes</a>
    <a href="reservation.php">📅 Réservations</a>
    <a href="plats.php">🍽️ Plats</a>
    <a href="client.php">👤 Clients</a>
    <a href="employes.php">👥 Employés</a>
    <a href="tables.php" class="actif">🪑 Tables</a>
    <a href="factures.php">💰 Factures</a>
</div>
<div class="admin-content">
    <?php if ($msg): ?><div class="msg-ok">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($erreur): ?><div class="msg-err">❌ <?= htmlspecialchars($erreur) ?></div><?php endif; ?>

    <div class="form-box" style="max-width:500px;">
        <h2>Ajouter une table</h2>
        <form method="POST">
            <input type="hidden" name="action" value="ajouter">
            <div class="form-group">
                <label>Numéro de table *</label>
                <input type="number" name="numero_table" required min="1" placeholder="Ex : 11">
            </div>
            <div class="form-group">
                <label>Capacité (nombre de personnes) *</label>
                <input type="number" name="capacite" required min="1" max="30" placeholder="Ex : 4">
            </div>
            <button type="submit" class="btn btn-full">Ajouter la table</button>
        </form>
    </div>

    <h2>Tables du restaurant (<?= count($tables) ?>)</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Numéro</th><th>Capacité</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($tables as $t): ?>
                <tr>
                    <td><?= $t['id_table'] ?></td>
                    <td>Table <?= $t['numero_table'] ?></td>
                    <td><?= $t['capacite'] ?> personnes</td>
                    <td>
                        <a href="tables.php?supprimer=<?= $t['id_table'] ?>"
                           class="btn btn-small btn-rouge"
                           onclick="return confirm('Supprimer la table <?= $t['numero_table'] ?> ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<footer>&copy; 2026 Taulissa Djibouti</footer>
</body>
</html>
