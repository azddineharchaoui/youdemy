<?php 
    require_once('Cours.php');
    class Cours_video extends Cours {
        protected $contenu;
        private $formatsSupportes = ['mp4', 'avi', 'mov']; // Formats vidéo supportés
    
        public function __construct($idCours, $titre, $description, $dateCreation, $contenu) {
            parent::__construct($idCours, $titre, $description, $dateCreation);
            $this->contenu = $contenu;
            $this->type = 'video';
        }
        public function getContenu(){
            return $this->contenu;
        }
        protected function validerContenu($contenu) {
            $extension = strtolower(pathinfo($contenu, PATHINFO_EXTENSION));
            return in_array($extension, $this->formatsSupportes);
        }
    
        protected function formaterContenu($contenu) {
            return '<video controls width="100%">
                        <source src="' . htmlspecialchars($contenu) . '" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>';
        }
    }
    ?>