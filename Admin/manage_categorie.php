<?php
require_once('../Classes/Categorie.php');

if (isset($_POST['add_categorie'])) {
    $nom = $_POST['nom_categorie'];
    $description = $_POST['description_categorie'] ?? '';

    $categorie = new Categorie(null, $nom, $description);
    if ($categorie->ajouterCategorie()) {
        echo "<div class='alert alert-success'>Catégorie ajoutée avec succès.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de l'ajout de la catégorie.</div>";
    }
    header("Location: dashboard.php");

}

if (isset($_POST['edit_categorie'])) {
    $id = $_POST['id_categorie'];
    $nom = $_POST['nom_categorie'];
    $description = $_POST['description_categorie'];

    $categorie = new Categorie($id, $nom, $description);
    if ($categorie->modifierCategorie($id)) {
        echo "<div class='alert alert-success'>Catégorie modifiée avec succès.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la modification de la catégorie.</div>";
    }
    header("Location: dashboard.php");
}

if (isset($_POST['delete_categorie'])) {
    $id = $_POST['delete_categorie'];
    $cat = new Categorie();
    if ($cat->supprimerCategorie($id)) {
        echo "<div class='alert alert-success'>Catégorie supprimée avec succès.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la suppression de la catégorie.</div>";
    }
    header("Location: dashboard.php");
}
?>
