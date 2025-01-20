<?php 
    require_once('Utilisateur.php');
    require_once('db.php');
    // session_start();

    class Etudiant extends Utilisateur{
        private $status;
        public function __construct($id, $nom, $prenom, $email, $password, $status){
            parent::__construct($id, $nom, $prenom, $email, $password, 3);
            $this->status = $status;
        }

        public function register() {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                if ($pdo === null) {
                    echo "Erreur : la connexion à la base de données ne peut pas être établie !";
                    return false;
                }
                if (empty($this->password)) {
                    echo "Erreur : Le mot de passe est manquant.";
                    return false;
                }
                // Validate email format
                if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Invalid email format");
                }
                // Validate password length
                if (strlen($this->password) < 6) {
                    throw new Exception("Password must be at least 6 characters long");
                }
                
                $sql = "INSERT INTO utilisateurs (nom, prenom, email, password, role_id, statut, created_at) 
                        VALUES (:nom, :prenom, :email, :password, :role_id, :statut, CURRENT_TIMESTAMP)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nom', $this->nom);
                $stmt->bindParam(':prenom', $this->prenom);
                $stmt->bindParam(':email', $this->email);
                $stmt->bindParam(':password', $this->password);
                $stmt->bindParam(':role_id', $this->role_id, PDO::PARAM_INT);
                $stmt->bindParam(':statut', $this->status);
    
                if ($stmt->execute()) {
                    $this->id = $pdo->lastInsertId();
                    $_SESSION['isactive'] = true;  
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
        
        public function getMesCours() {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                
                $sql = "SELECT c.*, 
                               u.nom as nom_enseignant, 
                               u.prenom as prenom_enseignant,
                               i.inscrit_a
                        FROM courses c
                        JOIN inscriptions i ON c.id_course = i.course_id
                        JOIN utilisateurs u ON c.enseignant_id = u.id_utilisateur
                        WHERE i.etudiant_id = :etudiant_id
                        ORDER BY i.inscrit_a DESC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':etudiant_id', $this->id);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            } catch (PDOException $e) {
                error_log("Erreur lors de la récupération des cours : " . $e->getMessage());
                return [];
            }
        }
    
        public function inscrireCours($courseId) {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                
                // Vérifier si l'étudiant n'est pas déjà inscrit
                $checkSql = "SELECT COUNT(*) FROM inscriptions 
                            WHERE etudiant_id = :etudiant_id AND course_id = :course_id";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->bindParam(':etudiant_id', $this->id);
                $checkStmt->bindParam(':course_id', $courseId);
                $checkStmt->execute();
                
                if ($checkStmt->fetchColumn() > 0) {
                    throw new Exception("Vous êtes déjà inscrit à ce cours");
                }
                
                $sql = "INSERT INTO inscriptions (etudiant_id, course_id, inscrit_a) 
                        VALUES (:etudiant_id, :course_id, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':etudiant_id', $this->id);
                $stmt->bindParam(':course_id', $courseId);
                
                return $stmt->execute();
    
            } catch (PDOException $e) {
                error_log("Erreur lors de l'inscription au cours : " . $e->getMessage());
                return false;
            }
        }
    
        public function desinscrireCours($courseId) {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                
                $sql = "DELETE FROM inscriptions 
                        WHERE etudiant_id = :etudiant_id AND course_id = :course_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':etudiant_id', $this->id);
                $stmt->bindParam(':course_id', $courseId);
                
                return $stmt->execute();
    
            } catch (PDOException $e) {
                error_log("Erreur lors de la désinscription du cours : " . $e->getMessage());
                return false;
            }
        }
    }
?>