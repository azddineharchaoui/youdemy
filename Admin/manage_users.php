<?php
session_start();
require_once('../Classes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = DatabaseConnection::getInstance()->getConnection();
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if (isset($_POST['ban_user'])) {
        try {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET statut = 'suspendu' WHERE id_utilisateur = ? AND role_id = 3");
            if ($stmt->execute([$user_id])) {
                $_SESSION['message'] = "L'utilisateur a été banni avec succès.";
                $_SESSION['isactive'] = false;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors du bannissement de l'utilisateur : " . $e->getMessage();
        }
    }

    if (isset($_POST['unban_user'])) {
        try {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET statut = 'active' WHERE id_utilisateur = ? AND role_id = 3");
            if ($stmt->execute([$user_id])) {
                $_SESSION['isactive'] = true;
                $_SESSION['message'] = "L'utilisateur a été debanni avec succes.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors du débannissement de l'utilisateur : " . $e->getMessage();
        }
    }

    if (isset($_POST['activate_teacher'])) {
        try {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET statut = 'active' WHERE id_utilisateur = ? AND role_id = 2");
            if ($stmt->execute([$user_id])) {
                $_SESSION['isactive'] = true;
                $_SESSION['message'] = "Le compte enseignant a été activé avec succès.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de l'activation du compte enseignant : " . $e->getMessage();
        }
    }

    header('Location: dashboard.php');
    exit();
}
?>