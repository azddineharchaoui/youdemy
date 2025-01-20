<?php 
    require_once("db.php");

    abstract class Cours {
        protected $idCours;
        protected $titre;
        protected $description;
        protected $dateCreation;
        protected $type; 
        protected $image;
    
        public function __construct($idCours, $titre, $description, $dateCreation, $image) {
            $this->idCours = $idCours;
            $this->titre = $titre;
            $this->description = $description;
            $this->dateCreation = $dateCreation;
            $this->image = $image;
        }
        public function get_id() {
            return $this->idCours;
        }

        protected function validerImage($image) {
            if ($image['error'] !== UPLOAD_ERR_OK) {
                return false;
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedType = finfo_file($fileInfo, $image['tmp_name']);
            finfo_close($fileInfo);

            if (!in_array($detectedType, $allowedTypes)) {
                return false;
            }

            if ($image['size'] > 5000000) { // 5MB max
                return false;
            }

            return true;
        }

        protected function sauvegarderImage($image) {
            $uploadDir = 'uploads/courses/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $uniqueName = uniqid() . '.' . $extension;
            $uploadFile = $uploadDir . $uniqueName;

            if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
                return $uploadFile;
            }
            return false;
        }

        public function countAllCours() {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                $sql = "SELECT COUNT(*) as total FROM courses";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['total'];
            } catch (PDOException $e) {
                echo "Erreur lors du comptage des cours : " . $e->getMessage();
                return 0;
            }
        }
        
        public function countCoursBySearch($mot) {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                $sql = "SELECT COUNT(*) as total FROM courses WHERE titre LIKE :mot OR description LIKE :mot";
                $stmt = $pdo->prepare($sql);
                $search = "%$mot%";
                $stmt->bindParam(':mot', $search);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['total'];
            } catch (PDOException $e) {
                echo "Erreur lors du comptage des cours : " . $e->getMessage();
                return 0;
            }
        }

        public function rechercherCours($mot, $limit = null, $offset = null) {
    try {
        $pdo = DatabaseConnection::getInstance()->getConnection();
        $sql = "SELECT * FROM courses WHERE titre LIKE :mot OR description LIKE :mot";
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $pdo->prepare($sql);
        $search = "%$mot%";
        $stmt->bindParam(':mot', $search);
        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la recherche : " . $e->getMessage();
        return false;
    }
}
    


public function listerTousCours($limit = null, $offset = null) {
    try {
        $pdo = DatabaseConnection::getInstance()->getConnection();
        $sql = "SELECT * FROM courses ORDER BY created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $pdo->prepare($sql);
        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des cours : " . $e->getMessage();
        return false;
    }
}
        abstract protected function validerContenu($contenu);
        abstract protected function formaterContenu($contenu);
        abstract public function getContenu();

        public function ajouterCours($image = null) {
            try {
                $contenu = $this->getContenu();
                if (!$this->validerContenu($contenu)) {
                    if ($this->type === 'text') {
                        throw new Exception("Le contenu du cours texte doit contenir au moins 10 caractères.");
                    } else {
                        throw new Exception("Le contenu vidéo doit être une URL valide ou un fichier au format mp4, avi ou mov.");
                    }
                }
        
                // Gestion de l'image
                $image_path = null;
                if ($image !== null) {
                    if (!$this->validerImage($image)) {
                        throw new Exception("L'image n'est pas valide. Formats acceptés : JPEG, PNG, GIF. Taille max : 5MB");
                    }
                    
                    $image_path = $this->sauvegarderImage($image);
                    if (!$image_path) {
                        throw new Exception("Erreur lors de la sauvegarde de l'image.");
                    }
                }
        
                $contenuFormate = $this->formaterContenu($contenu);
                $pdo = DatabaseConnection::getInstance()->getConnection();
        
                $checkSql = "SELECT COUNT(*) FROM courses WHERE titre = :titre";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->bindParam(':titre', $this->titre);
                $checkStmt->execute();
                
                if ($checkStmt->fetchColumn() > 0) {
                    throw new Exception("Un cours avec ce titre existe déjà");
                }
        
                $sql = "INSERT INTO courses (titre, description, contenu, type, image_url, created_at) 
                        VALUES (:titre, :description, :contenu, :type, :image_url, NOW())";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':titre', $this->titre);
                $stmt->bindParam(':description', $this->description);
                $stmt->bindParam(':contenu', $contenuFormate);
                $stmt->bindParam(':type', $this->type);
                $stmt->bindParam(':image_url', $image_path);
                
                if ($stmt->execute()) {
                    $this->idCours = $pdo->lastInsertId();
                    return true;
                }
                return false;
            } catch (PDOException $e) {
                echo "Erreur lors de l'ajout du cours : " . $e->getMessage();
                return false;
            }
        }
        public function modifierCours($idCours,$titre, $description, $contenu) {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                
                $sql = "UPDATE courses 
                        SET titre = :titre, 
                            description = :description, 
                            contenu = :contenu 
                        WHERE id_course = :idCours";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':titre', $titre);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':contenu', $contenu);
                $stmt->bindParam(':idCours', $idCours, PDO::PARAM_INT);
                
                return $stmt->execute();
            } catch (PDOException $e) {
                echo "Erreur lors de la modification du cours : " . $e->getMessage();
                return false;
            }
        }

        public function supprimerCours($idCours) {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                
                //Supprimer les inscriptions associées
                $sqlInscriptions = "DELETE FROM inscriptions WHERE course_id = :idCours";
                $stmtInscriptions = $pdo->prepare($sqlInscriptions);
                $stmtInscriptions->bindParam(':idCours', $idCours, PDO::PARAM_INT);
                $stmtInscriptions->execute();
                
                //Supprimer les tags associés
                $sqlTags = "DELETE FROM course_tags WHERE course_id = :idCours";
                $stmtTags = $pdo->prepare($sqlTags);
                $stmtTags->bindParam(':idCours', $idCours, PDO::PARAM_INT);
                $stmtTags->execute();
                
                //Supprimer le cours
                $sqlCours = "DELETE FROM courses WHERE id_course = :idCours";
                $stmtCours = $pdo->prepare($sqlCours);
                $stmtCours->bindParam(':idCours', $idCours, PDO::PARAM_INT);
                
                return $stmtCours->execute();
            } catch (PDOException $e) {
                echo "Erreur lors de la suppression du cours : " . $e->getMessage();
                return false;
            }
        }

        public function afficherCours($idCours) {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                
                $sql = "SELECT * FROM courses WHERE id_course = :idCours";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':idCours', $idCours, PDO::PARAM_INT);
                $stmt->execute();
                
                $cours = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($cours) {
                    $cours['contenu'] = $this->formaterContenu($cours['contenu']);
                }
                return $cours;
                
            } catch (PDOException $e) {
                echo "Erreur lors de l'affichage du cours : " . $e->getMessage();
                return false;
            }
        }
        public static function getCategories() {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                $sql = "SELECT * FROM categories ORDER BY nom";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return [];
            }
        }
    }
?>