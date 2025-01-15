<?php
include_once('./Classes/Utilisateur.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    Utilisateur::logout();
    header("Location: index.php");
}
?>