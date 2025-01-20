<?php
require_once('../Classes/Tag.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_multiple_tags'])) {
            $resultat = Tag::ajouterMultipleTags($_POST['noms_tags']);
            
            $_SESSION['message'] = '';
            if (!empty($resultat['success'])) {
                $_SESSION['message'] .= "Tags ajoutés avec succès : " . implode(', ', $resultat['success']) . "<br>";
            }
            if (!empty($resultat['errors'])) {
                $_SESSION['message'] .= "Erreurs : " . implode(', ', $resultat['errors']);
            }
            
            header('Location: dashboard.php#tags');
            exit;
        } 
        elseif (isset($_POST['edit_tag'])) {
            $tag = new Tag($_POST['id_tag'], $_POST['nom_tag']);
            if ($tag->modifierTag($_POST['id_tag'])) {
                header('Location: dashboard.php#tags');
                exit;
            }
        }
        
        elseif (isset($_POST['delete_tag'])) {
            $tag = new Tag($_POST['delete_tag'], '');
            if ($tag->supprimerTag($_POST['delete_tag'])) {
                header('Location: dashboard.php#tags');
                exit;
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        header('Location: dashboard.php#tags');
        exit;
    }
}

header('Location: dashboard.php#tags');
?>