<?php
require_once 'admin_guard.php';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$table = isset($_GET['table']) ? $_GET['table'] : '';
$msg = '';

$tables = array(
    'CLIENT' => array('id' => 'id_client', 'search_field' => 'nom', 'search_label' => 'Nom du client', 'fields' => array('nom'=>'text','prenom'=>'text','telephone'=>'text','adresse'=>'text')),
    'EMPLOYE' => array('id' => 'id_employe', 'search_field' => 'nom', 'search_label' => 'Nom de l\'employé', 'fields' => array('nom'=>'text','prenom'=>'text','poste'=>'text','salaire'=>'number','email'=>'email','mot_de_passe'=>'text','role'=>'text')),
    'TABLE_RESTAURANT' => array('id' => 'id_table', 'search_field' => 'capacite', 'search_label' => 'Capacité de la table', 'fields' => array('numero_table'=>'number','capacite'=>'number')),
    'PLAT' => array('id' => 'id_plat', 'search_field' => 'nom_plat', 'search_label' => 'Nom du plat', 'fields' => array('nom_plat'=>'text','prix'=>'number','categorie'=>'text','stock'=>'number')),
    'COMMANDE' => array('id' => 'id_commande', 'search_field' => 'id_commande', 'search_label' => 'ID de la commande', 'fields' => array('date_commande'=>'date','heure_commande'=>'time','statut_commande'=>'text','id_client'=>'number')),
    'RESERVATION' => array('id' => 'id_reservation', 'search_field' => 'id_reservation', 'search_label' => 'ID de la réservation', 'fields' => array('date_reservation'=>'date','heure'=>'time','nombre_personne'=>'number','id_client'=>'number','id_table'=>'number','id_employe'=>'number')),
    'DETAIL_COMMANDE' => array('id' => 'id_commande', 'search_field' => 'id_commande', 'search_label' => 'ID de la commande', 'second_id' => 'id_plat', 'fields' => array('id_plat'=>'number','quantite'=>'number')),
    'FACTURE' => array('id' => 'id_facture', 'search_field' => 'id_facture', 'search_label' => 'ID de la facture', 'fields' => array('date_facture'=>'date','montant_total'=>'number','mode_paiement'=>'text','id_commande'=>'number'))
);

if (!isset($tables[$table])) { die('Table invalide'); }
$idField = $tables[$table]['id'];
$fields = $tables[$table]['fields'];
$searchField = $tables[$table]['search_field'];
$searchLabel = $tables[$table]['search_label'];
$resultat = null;
$donnee = null;

if ($action == 'ajouter' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($table == 'DETAIL_COMMANDE') {
        $id_commande = mysqli_real_escape_string($connexion, $_POST['id_commande']);
        $id_plat = mysqli_real_escape_string($connexion, $_POST['id_plat']);
        $quantite = mysqli_real_escape_string($connexion, $_POST['quantite']);
        $requete = "INSERT INTO DETAIL_COMMANDE (id_commande, id_plat, quantite) VALUES ('$id_commande', '$id_plat', '$quantite')";
    } else {
        $cols = array();
        $vals = array();
        foreach ($fields as $field => $type) {
            $cols[] = $field;
            $vals[] = "'" . mysqli_real_escape_string($connexion, $_POST[$field]) . "'";
        }
        $requete = "INSERT INTO $table (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
    }
    mysqli_query($connexion, $requete);
    $msg = 'Ajout effectué.';
}

if ($action == 'supprimer' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($connexion, $_GET['id']);
    if ($table == 'DETAIL_COMMANDE' && isset($_GET['id_plat'])) {
        $id_plat = mysqli_real_escape_string($connexion, $_GET['id_plat']);
        mysqli_query($connexion, "DELETE FROM DETAIL_COMMANDE WHERE id_commande = '$id' AND id_plat = '$id_plat'");
    } else {
        mysqli_query($connexion, "DELETE FROM $table WHERE $idField = '$id'");
    }
    header("Location: admin_gestion.php?action=afficher&table=$table");
    exit;
}

if ($action == 'afficher') {
    if ($table == 'DETAIL_COMMANDE') {
        $resultat = mysqli_query($connexion, "SELECT d.id_commande, p.nom_plat, d.quantite FROM DETAIL_COMMANDE d LEFT JOIN PLAT p ON d.id_plat = p.id_plat ORDER BY d.id_commande DESC");
    } else {
        $resultat = mysqli_query($connexion, "SELECT * FROM $table ORDER BY $idField DESC");
    }
}

if ($action == 'rechercher' && isset($_POST['mot_cle'])) {
    $mot = mysqli_real_escape_string($connexion, $_POST['mot_cle']);
    if ($table == 'DETAIL_COMMANDE') {
        $resultat = mysqli_query($connexion, "SELECT d.id_commande, p.nom_plat, d.quantite FROM DETAIL_COMMANDE d LEFT JOIN PLAT p ON d.id_plat = p.id_plat WHERE d.id_commande LIKE '%$mot%' ORDER BY d.id_commande DESC");
    } else {
        $resultat = mysqli_query($connexion, "SELECT * FROM $table WHERE $searchField LIKE '%$mot%' ORDER BY $idField DESC");
    }
}

if ($action == 'modifier' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($connexion, $_GET['id']);
    if ($table == 'DETAIL_COMMANDE' && isset($_GET['id_plat'])) {
        $id_plat = mysqli_real_escape_string($connexion, $_GET['id_plat']);
        $res = mysqli_query($connexion, "SELECT * FROM DETAIL_COMMANDE WHERE id_commande = '$id' AND id_plat = '$id_plat' LIMIT 1");
    } else {
        $res = mysqli_query($connexion, "SELECT * FROM $table WHERE $idField = '$id' LIMIT 1");
    }
    if ($res && mysqli_num_rows($res) > 0) { $donnee = mysqli_fetch_assoc($res); }
}

if ($action == 'modifier' && isset($_POST['modifier'])) {
    $id = mysqli_real_escape_string($connexion, $_POST[$idField]);
    if ($table == 'DETAIL_COMMANDE') {
        $id_plat_original = mysqli_real_escape_string($connexion, $_POST['id_plat_original']);
        $quantite = mysqli_real_escape_string($connexion, $_POST['quantite']);
        mysqli_query($connexion, "UPDATE DETAIL_COMMANDE SET quantite = '$quantite' WHERE id_commande = '$id' AND id_plat = '$id_plat_original'");
    } else {
        $parts = array();
        foreach ($fields as $field => $type) {
            $val = mysqli_real_escape_string($connexion, $_POST[$field]);
            $parts[] = "$field = '$val'";
        }
        mysqli_query($connexion, "UPDATE $table SET " . implode(',', $parts) . " WHERE $idField = '$id'");
    }
    header("Location: admin_gestion.php?action=afficher&table=$table");
    exit;
}

$liste_plats = null;
if ($table == 'DETAIL_COMMANDE') {
    $liste_plats = mysqli_query($connexion, "SELECT id_plat, nom_plat FROM PLAT ORDER BY nom_plat ASC");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion Admin</title>
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
<div class="header"><h1><?php echo strtoupper($action) . ' - ' . $table; ?></h1><p>Gestion de la table</p></div>
<div class="container">
<?php if ($msg != '') { ?><div class="msg-ok"><?php echo $msg; ?></div><?php } ?>

<?php if ($action == 'ajouter') { ?>
<div class="form-box"><form method="POST">
<?php if ($table == 'DETAIL_COMMANDE') { ?>
<div class="form-group"><label>id_commande</label><input type="number" name="id_commande" required></div>
<div class="form-group"><label>Nom du plat</label>
<select name="id_plat" required><option value="">Choisir un plat</option><?php while ($p = mysqli_fetch_assoc($liste_plats)) { ?><option value="<?php echo $p['id_plat']; ?>"><?php echo htmlspecialchars($p['nom_plat']); ?></option><?php } ?></select>
</div>
<div class="form-group"><label>quantite</label><input type="number" name="quantite" min="1" required></div>
<?php } else { foreach ($fields as $field => $type) { ?>
<div class="form-group"><label><?php echo $field; ?></label><input type="<?php echo $type; ?>" name="<?php echo $field; ?>" required></div>
<?php }} ?>
<button type="submit" class="btn btn-full">Enregistrer</button>
</form></div>
<?php } ?>

<?php if ($action == 'rechercher') { ?>
<div class="form-box">
<form method="POST">
<div class="form-group"><label><?php echo htmlspecialchars($searchLabel); ?></label><input type="text" name="mot_cle" required></div>
<button type="submit" class="btn btn-full">Rechercher</button>
</form>
</div>
<?php } ?>

<?php if (($action == 'afficher' || ($action == 'rechercher' && $resultat)) && $resultat) { ?>
<div class="table-wrap"><table><thead><tr>
<?php while ($col = mysqli_fetch_field($resultat)) { ?><th><?php echo $col->name; ?></th><?php } ?>
<th>Modifier</th><th>Supprimer</th></tr></thead><tbody>
<?php mysqli_data_seek($resultat,0); while ($row = mysqli_fetch_assoc($resultat)) { ?><tr>
<?php foreach ($row as $v) { ?><td><?php echo htmlspecialchars($v); ?></td><?php } ?>
<td>
<?php if ($table == 'DETAIL_COMMANDE') { $idplat=''; $res= mysqli_query($connexion, "SELECT id_plat FROM PLAT WHERE nom_plat='" . mysqli_real_escape_string($connexion, $row['nom_plat']) . "' LIMIT 1"); if($res && mysqli_num_rows($res)>0){$rp=mysqli_fetch_assoc($res); $idplat=$rp['id_plat'];} ?>
<a class="btn btn-small" href="admin_gestion.php?action=modifier&table=<?php echo $table; ?>&id=<?php echo $row['id_commande']; ?>&id_plat=<?php echo $idplat; ?>">Modifier</a>
<?php } else { ?>
<a class="btn btn-small" href="admin_gestion.php?action=modifier&table=<?php echo $table; ?>&id=<?php echo $row[$idField]; ?>">Modifier</a>
<?php } ?>
</td>
<td>
<?php if ($table == 'DETAIL_COMMANDE') { ?>
<a class="btn btn-rouge btn-small" href="admin_gestion.php?action=supprimer&table=<?php echo $table; ?>&id=<?php echo $row['id_commande']; ?>&id_plat=<?php echo $idplat; ?>" onclick="return confirm('Supprimer ?');">Supprimer</a>
<?php } else { ?>
<a class="btn btn-rouge btn-small" href="admin_gestion.php?action=supprimer&table=<?php echo $table; ?>&id=<?php echo $row[$idField]; ?>" onclick="return confirm('Supprimer ?');">Supprimer</a>
<?php } ?>
</td>
</tr><?php } ?>
</tbody></table></div>
<?php } ?>

<?php if ($donnee && $action == 'modifier') { ?>
<div class="form-box"><form method="POST">
<div class="form-group"><label><?php echo $idField; ?></label><input type="number" name="<?php echo $idField; ?>" value="<?php echo $donnee[$idField]; ?>" readonly></div>
<?php if ($table == 'DETAIL_COMMANDE') { ?>
<div class="form-group"><label>id_plat</label><input type="number" name="id_plat_original" value="<?php echo $donnee['id_plat']; ?>" readonly></div>
<div class="form-group"><label>quantite</label><input type="number" name="quantite" value="<?php echo htmlspecialchars($donnee['quantite']); ?>" required></div>
<?php } else { foreach ($fields as $field => $type) { ?><div class="form-group"><label><?php echo $field; ?></label><input type="<?php echo $type; ?>" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($donnee[$field]); ?>" required></div><?php }} ?>
<button type="submit" name="modifier" class="btn btn-full">Modifier</button>
</form></div>
<?php } ?>
</div>
<footer>&copy; 2026 Taulissa Djibouti</footer>
</body>
</html>
