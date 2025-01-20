<?php
session_start();
require_once("Classes/Etudiant.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: index.php');
    exit();
}

if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
    header('Location: allcours.php');
    exit();
}

$course_id = (int)$_POST['course_id'];
$etudiant = new Etudiant($_SESSION['user_id'], '', '', '', '', 'active');

try {
    if ($etudiant->inscrireCours($course_id)) {
        header("Location: cours.php?id=" . $course_id . "&success=1");
    } else {
        header("Location: cours.php?id=" . $course_id . "&error=1");
    }
} catch (Exception $e) {
    header("Location: cours.php?id=" . $course_id . "&error=" . urlencode($e->getMessage()));
}

exit();
?>