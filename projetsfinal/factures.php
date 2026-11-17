<?php
require_once 'connexion.php';
$msg = ""; $erreur = "";

// Générer une facture pour une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'generer') {
    $id_commande = (int)$_POST['id_commande'];

    // Vérifier si facture déjà existante
    $stmt = $pdo->prepare("SELECT id_facture FROM FACTURE WHERE id_commande = ?");
    $stmt->execute([$id_commande]);
    if ($stmt->fetch()) {
        $erreur = "Une facture existe déjà pour la commande #$id_commande.";
    } else {
        // Calculer le montant total
        $stmt = $pdo->prepare("
            SELECT SUM(p.prix * dc.quantite) AS total
            FROM DETAIL_COMMANDE dc
            JOIN PLAT p ON dc.id_plat = p.id_plat
            WHERE dc.id_commande = ?
        ");
        $stmt->execute([$id_commande]);
        $total = $stmt->fetchColumn() ?: 0;

        if ($total <= 0) {
            $erreur = "Cette commande n'a aucun plat enregistré. Impossible de générer une facture.";
        } else {
            // Insérer la facture
            $pdo->prepare("INSERT INTO FACTURE (date_facture, montant_total, id_commande) VALUES (CURDATE(), ?, ?)")
                ->execute([$total, $id_commande]);
            // Marquer la commande comme Servi
            $pdo->prepare("UPDATE COMMANDE SET statut_commande = 'Servi' WHERE id_commande = ?")
                ->execute([$id_commande]);
            $msg = "Facture générée ! Montant : " . number_format($total, 0, ',', ' ') . " FDJ";
        }
    }
}

// Commandes qui n'ont pas encore de facture
$sans_facture = $pdo->query("
    SELECT cmd.id_commande, cmd.date_commande, cmd.statut_commande,
           c.nom, c.prenom, t.numero_table,
           (SELECT SUM(p.prix * dc.quantite)
            FROM DETAIL_COMMANDE dc JOIN PLAT p ON dc.id_plat = p.id_plat
            WHERE dc.id_commande = cmd.id_commande) AS total
    FROM COMMANDE cmd
    JOIN CLIENT c ON cmd.id_client = c.id_client
    JOIN TABLE_RESTAURANT t ON cmd.id_table = t.id_table
    WHERE cmd.id_commande NOT IN (SELECT id_commande FROM FACTURE)
    ORDER BY cmd.id_commande DESC
")->fetchAll();

// Toutes les factures émises
$factures = $pdo->query("
    SELECT f.id_facture, f.date_facture, f.montant_total,
           f.id_commande, c.nom, c.prenom, t.numero_table
    FROM FACTURE f
    JOIN COMMANDE cmd ON f.id_commande = cmd.id_commande
    JOIN CLIENT c ON cmd.id_client = c.id_client
    JOIN TABLE_RESTAURANT t ON cmd.id_table = t.id_table
    ORDER BY f.id_facture DESC
")->fetchAll();

$total_general = $pdo->query("SELECT COALESCE(SUM(montant_total), 0) FROM FACTURE")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Factures — Admin Taulissa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
        <a href="description.html" class="brand">TAULISSA</a>
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

<div class="header">
    <h1> Factures</h1>
    <p>Total des recettes : <strong><?= number_format($total_general, 0, ',', ' ') ?> FDJ</strong></p>
</div>
<div class="admin-layout">
<div class="sidebar">
    <p class="titre">Administration</p>
    <a href="poo.php"> Dashboard</a>
    <a href="commandes.php"> Commandes</a>
    <a href="reservation.php"> Réservations</a>
    <a href="plats.php">Plats</a>
    <a href="client.php">Clients</a>
    <a href="employes.php"> Employés</a>
    <a href="tables.php"> Tables</a>
    <a href="factures.php" class="actif">Factures</a>
</div>
<div class="admin-content">
    <?php if ($msg): ?><div class="msg-ok"> <?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($erreur): ?><div class="msg-err"> <?= htmlspecialchars($erreur) ?></div><?php endif; ?>

    <!-- Générer une facture -->
    <?php if ($sans_facture): ?>
    <div class="form-box" style="max-width:650px;">
        <h2>Générer une facture</h2>
        <p style="color:#666; font-size:13px; margin-bottom:14px;">
            ℹ️ Sélectionne une commande terminée pour émettre sa facture.
            Le montant est calculé automatiquement depuis les plats commandés.
        </p>
        <form method="POST">
            <input type="hidden" name="action" value="generer">
            <div class="form-group">
                <label>Choisir une commande *</label>
                <select name="id_commande" required>
                    <option value="">-- Choisir une commande --</option>
                    <?php foreach ($sans_facture as $cmd): ?>
                    <option value="<?= $cmd['id_commande'] ?>">
                        Commande #<?= $cmd['id_commande'] ?>
                        — <?= htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']) ?>
                        — Table <?= $cmd['numero_table'] ?>
                        — <?= $cmd['total'] ? number_format($cmd['total'], 0, ',', ' ') . ' FDJ' : 'sans plats' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-full">Émettre la facture</button>
        </form>
    </div>
    <?php else: ?>
    <div class="msg-ok"> Toutes les commandes ont déjà une facture.</div>
    <?php endif; ?>

    <!-- Liste des factures -->
    <h2>Toutes les factures (<?= count($factures) ?>)</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>N° Facture</th>
                    <th>Commande</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Table</th>
                    <th>Montant total</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($factures): foreach ($factures as $f): ?>
                <tr>
                    <td><strong>F-<?= str_pad($f['id_facture'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                    <td>#<?= $f['id_commande'] ?></td>
                    <td><?= date('d/m/Y', strtotime($f['date_facture'])) ?></td>
                    <td><?= htmlspecialchars($f['prenom'] . ' ' . $f['nom']) ?></td>
                    <td>Table <?= $f['numero_table'] ?></td>
                    <td style="font-weight:bold; color:#8B0000; font-size:15px;">
                        <?= number_format($f['montant_total'], 0, ',', ' ') ?> FDJ
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" style="text-align:center; color:#999; padding:20px;">Aucune facture.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</div>
<footer>2026 Taulissa Djibouti</footer>
</body>
</html>
