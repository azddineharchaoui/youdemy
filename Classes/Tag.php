<?php 

    require_once("db.php");
    class Tag{
        private $idTag;
        private $nom;
        public function __construct($id, $nom){
            $this->idTag = $id;
            $this->nom = $nom;
        }
        public function ajouterTag(){

        }
        public function modifierTag($idTag){

        }
        public function supprimerTag($idTag){

        }
        public function listerTags(){

        }
        public function avoirTagParId(idTag){
            
        }
    }

?>