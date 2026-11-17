<?php
$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "";
$basedonnee = "restaurant_taulissa";

$connexion = mysqli_connect($serveur, $utilisateur, $motdepasse, $basedonnee);
if (!$connexion) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}
mysqli_set_charset($connexion, 'utf8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function djibouti_phone_valide($telephone) {
    return preg_match('/^(\\+253\\d{8}|\\d{8})$/', $telephone);
}

function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = array('type' => $type, 'message' => $message);
}

function display_flash_message() {
    if (!empty($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        $class = $flash['type'] == 'success' ? 'msg-ok' : 'msg-err';
        echo '<div class="' . $class . '">' . htmlspecialchars($flash['message']) . '</div>';
        unset($_SESSION['flash_message']);
    }
}
?>
