<?php
class Cours_text extends Cours {
    private $contenu;

    public function __construct($idCours, $titre, $description, $dateCreation, $contenu) {
        parent::__construct($idCours, $titre, $description, $dateCreation);
        $this->contenu = $contenu;
        $this->type = 'text';
    }
    public function getContenu(){
        return $this->contenu;
    }
    protected function validerContenu($contenu) {
        return !empty($contenu) && strlen($contenu) >= 50;
    }

    protected function formaterContenu($contenu) {
        return nl2br(htmlspecialchars($contenu));
    }
    
}
?>