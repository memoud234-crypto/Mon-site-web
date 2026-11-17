<?php
require_once 'connexion.php';
session_destroy();
session_start();
header('Location: admin_login.php');
exit;
?>
