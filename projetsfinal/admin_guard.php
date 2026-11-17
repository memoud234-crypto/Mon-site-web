<?php
require_once 'connexion.php';
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}
?>
