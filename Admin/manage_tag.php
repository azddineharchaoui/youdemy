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
            $idTag = $_POST['id_tag'];
            $nouveauNom = trim($_POST['nom_tag']);

            if (empty($nouveauNom)) {
                throw new Exception("Le nom du tag ne peut pas être vide");
            }

            $tag = new Tag($idTag, $nouveauNom);
            if ($tag->modifierTag($idTag)) {
                $_SESSION['message'] = "Tag modifié avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la modification du tag";
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