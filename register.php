<?php
require_once("./Classes/Etudiant.php");
require_once("./Classes/Enseignant.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Validation des entrées
    if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['type_user'])) {
        echo "Tous les champs sont obligatoires.";
        exit;
    }

    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $type_user = $_POST['type_user'];
    $status = 'active';

    try {
        if ($type_user == '3') {
            $user = new Etudiant(null, $nom, $prenom, $email, $password, $status);
        } elseif ($type_user == '2') {
            $user = new Enseignant(null, $nom, $prenom, $email, $password, $status);
        } else {
            throw new Exception("Type d'utilisateur non valide");
        }

        if ($user->register()) {
            session_start();
            $_SESSION['user_id'] = $user->get_id();
            $_SESSION['role_id'] = $user->get_role_id();
            $_SESSION['user_name'] = $user->get_nom();

            if ($_SESSION['role_id'] == 2) {
                header("Location: ./enseignantPage.php");
                exit;
            } else {
                header("Location: ./allcours.php");
                exit;
            }
        } else {
            throw new Exception("Échec de l'enregistrement");
        }
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>