<?php 
    require_once('Cours.php');
    class Cours_video extends Cours {
        protected $contenu;
        private $formatsSupportes = ['mp4', 'avi', 'mov'];
    
        public function __construct($idCours, $titre, $description, $dateCreation, $image, $contenu) {
            parent::__construct($idCours, $titre, $description, $dateCreation, $image);
            $this->contenu = $contenu;
            $this->type = 'video';
        }
    
        public function getContenu() {
            return $this->contenu;
        }
    
        protected function validerContenu($contenu) {
            if (empty($contenu)) return false;
            $extension = strtolower(pathinfo($contenu, PATHINFO_EXTENSION));
            return in_array($extension, $this->formatsSupportes);
        }
    
        protected function formaterContenu($contenu) {
            return htmlspecialchars($contenu);
        }
    }
    ?>