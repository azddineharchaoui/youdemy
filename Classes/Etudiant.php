<?php 
    require_once('Utilisateur.php');
    require_once('db.php');

    class Etudiant extends User{
        private $status;
        public function __construct($id, $fname, $lname, $email, $password, $role_id, $status){
            parent::__construct($id, $fname, $lname, $email, $password, $role_id);
            $this->status = $status;
        }

        public function register() {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                if ($pdo === null) {
                    echo "Erreur : la connexion à la bases de données ne peut pas être établie !";
                    return false;
                }
                if (empty($this->password)) {
                    echo "Erreur : Le mot de passe est manquant.";
                    return false;
                }
                $sql = "INSERT INTO utilisateurs (nom, prenom, email, password, role_id, statut, created_at) VALUES (:nom, :prenom, :email, :password, :id_role, 'active', CURRENT_TIMESTAMP)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nom', $this->nom);
                $stmt->bindParam(':prenom', $this->prenom);
                $stmt->bindParam(':email', $this->email);
                $stmt->bindParam(':password', $this->password);
                $stmt->bindParam(':role_id', $this->role_id, PDO::PARAM_INT);
    
                if ($stmt->execute()) {
                    $this->id = $pdo->lastInsertId();
                    return true;
                }
            } catch (PDOException $e) {
                echo "Erreur d'inscription: " . $e->getMessage();
                return false;
            } catch (Exception $e) {
                echo "Erreur: " . $e->getMessage();
                return false;
            }
        }
    }

        
    

?>