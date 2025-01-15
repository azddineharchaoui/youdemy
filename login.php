<?php

require_once("./Classes/Utilisateur.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    if (isset($_POST['email'], $_POST['password'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        Utilisateur::login($email, $password);
        
    } else {
        echo "Veuillez remplir tous les champs.";
        header("Location: index.php");
    }
}
?>