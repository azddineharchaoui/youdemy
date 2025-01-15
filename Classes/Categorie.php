<?php 
    require_once('db.php');

    class Categorie{
        private $id; 
        private $nom; 
        private $description;
        public function __construct($id = null, $nom = null, $description = null){
            $this->id = $id; 
            $this->nom = $nom; 
            $this->description = $description;
        }
        public function get_id(){
            return $this->id;
        }
        public function set_id($id){
            $this->id = $id;
        }
        public function get_nom(){
            return $this->nom;
        }
        public function set_nom($nom){
            $this->nom = $nom;
        }
        public function get_description(){
            return $this->description;
        }
        public function set_description($description){
            $this->description = $description;
        }

        public function ajouterCategorie() {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            if (!$pdo) {
                echo "Erreur de connexion à la base de données.";
                return false;
            }
            $query = "INSERT INTO categories (nom, description) VALUES (:nom, :description)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':description', $this->description);
            if ($stmt->execute()) {
                $this->id = $pdo->lastInsertId(); 
                return true;
            } else {
                return false;
            }
        }
    
        public function modifierCategorie($id) {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            if (!$pdo) {
                echo "Erreur de connexion à la base de données.";
                return false;
            }
            $query = "UPDATE categories SET nom = :nom, description = :description WHERE id_categorie = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
    
        public function supprimerCategorie($id) {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            if (!$pdo) {
                echo "Erreur de connexion à la base de données.";
                return false;
            }
            $query = "DELETE FROM categories WHERE id_categorie = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
    
        public static function listerCategories() {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            if (!$pdo) {
                echo "Erreur de connexion à la base de données.";
                return [];
            }
            $query = "SELECT id_categorie, nom, description FROM categories";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
    }

?>