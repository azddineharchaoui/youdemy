<?php 
    require_once('Utilisateur.php');
    require_once('db.php');
    require_once("fileUploader.php");
    require_once("Tag.php");

    class Enseignant extends Utilisateur{
        private $status;
        public function __construct($id, $nom, $prenom, $email, $password, $status){
            parent::__construct($id, $nom, $prenom, $email, $password, 2);
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

        private function validerImage($image) {
            if ($image['error'] !== UPLOAD_ERR_OK) return false;
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedType = finfo_file($fileInfo, $image['tmp_name']);
            finfo_close($fileInfo);
        
            return in_array($detectedType, $allowedTypes) && $image['size'] <= 5000000;
        }
        
        private function sauvegarderImage($image) {
            $uploadDir = 'uploads/courses/';
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                return $targetPath;
            }
            return false;
        }
        
        
        public function creerCours($type, $titre, $description, $contenu, $image = null, $video_file = null, $categorie_id) {
            try {
                if ($this->status !== 'active') {
                    throw new Exception("L'enseignant n'est pas actif et ne peut pas créer de cours");
                }
        
                $image_file = isset($_FILES['course_image']) ? $_FILES['course_image'] : null;
        
                if ($type === 'video' && isset($video_file)) {
                    $uploader = new FileUploader();
                    $contenu = $uploader->upload($video_file);
                }
        
                if ($type === 'video') {
                    $cours = new Cours_video(null, $titre, $description, date('Y-m-d H:i:s'), $image_file, $contenu);
                } elseif ($type === 'text') {
                    $cours = new Cours_text(null, $titre, $description, date('Y-m-d H:i:s'), $image_file, $contenu);
                } else {
                    throw new Exception("Type de cours non valide");
                }
        
                if ($cours->ajouterCours($image_file)) {
                    $pdo = DatabaseConnection::getInstance()->getConnection();
                    
                    // Mettre a jour l enseignant_id et la categorie
                    $sql = "UPDATE courses SET enseignant_id = :enseignant_id, categorie_id = :categorie_id WHERE id_course = :id_course";
                    $stmt = $pdo->prepare($sql);
                    $id = $cours->get_id();
                    $stmt->bindParam(':enseignant_id', $this->id);
                    $stmt->bindParam(':categorie_id', $categorie_id);
                    $stmt->bindParam(':id_course', $id);
                    
                    if ($stmt->execute()) {
                        // Gerer les tags si presents
                        if (isset($_POST['tags']) && is_array($_POST['tags'])) {
                            $sqlTag = "INSERT INTO course_tags (course_id, tag_id) VALUES (:course_id, :tag_id)";
                            $stmtTag = $pdo->prepare($sqlTag);
                            
                            foreach ($_POST['tags'] as $tag_id) {
                                $stmtTag->bindParam(':course_id', $id);
                                $stmtTag->bindParam(':tag_id', $tag_id);
                                $stmtTag->execute();
                            }
                        }
                        return true;
                    }
                }
                return false;
        
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
        public function listerMesCours() {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                
                $sql = "SELECT c.*, 
                        COUNT(i.etudiant_id) as nombre_etudiants,
                        cat.nom as categorie_nom
                        FROM courses c 
                        LEFT JOIN inscriptions i ON c.id_course = i.course_id
                        LEFT JOIN categories cat ON c.categorie_id = cat.id_categorie
                        WHERE c.enseignant_id = :enseignant_id 
                        GROUP BY c.id_course 
                        ORDER BY c.created_at DESC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':enseignant_id', $this->id);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            } catch (PDOException $e) {
                echo "Erreur lors de la récupération des cours : " . $e->getMessage();
                return false;
            }
        }
    
        public function voirInscrits($id_cours) {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                
                // Vérifier si le cours appartient à cet enseignant
                $checkSql = "SELECT id_course FROM courses 
                            WHERE id_course = :id_cours 
                            AND enseignant_id = :enseignant_id";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->bindParam(':id_cours', $id_cours);
                $checkStmt->bindParam(':enseignant_id', $this->id);
                $checkStmt->execute();
                
                if ($checkStmt->rowCount() === 0) {
                    throw new Exception("Ce cours ne vous appartient pas ou n'existe pas");
                }
                
                $sql = "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, i.inscrit_a
                        FROM inscriptions i
                        JOIN utilisateurs u ON i.etudiant_id = u.id_utilisateur
                        WHERE i.course_id = :id_cours
                        ORDER BY i.inscrit_a DESC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_cours', $id_cours);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            } catch (Exception $e) {
                echo "Erreur lors de la récupération des inscrits : " . $e->getMessage();
                return false;
            }
        }
    
        public function getStatistiquesCours($id_cours = null) {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                
                $statistiques = [];
        
                $sqlGlobal = "SELECT 
                                COUNT(DISTINCT c.id_course) as nombre_cours,
                                COUNT(DISTINCT i.etudiant_id) as nombre_etudiants_inscrits
                              FROM courses c
                              LEFT JOIN inscriptions i ON c.id_course = i.course_id
                              WHERE c.enseignant_id = :enseignant_id";
                
                $stmtGlobal = $pdo->prepare($sqlGlobal);
                $stmtGlobal->bindParam(':enseignant_id', $this->id);
                $stmtGlobal->execute();
                $statistiques['global'] = $stmtGlobal->fetch(PDO::FETCH_ASSOC);
        
                if ($id_cours !== null) {
                    $sqlCours = "SELECT 
                                    COUNT(DISTINCT i.etudiant_id) as nombre_inscrits,
                                    c.created_at as date_creation,
                                    (SELECT COUNT(*) FROM inscriptions 
                                     WHERE course_id = :id_cours 
                                     AND inscrit_a >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) as nouveaux_inscrits_mois
                                  FROM courses c
                                  LEFT JOIN inscriptions i ON c.id_course = i.course_id
                                  WHERE c.id_course = :id_cours 
                                  AND c.enseignant_id = :enseignant_id";
                    
                    $stmtCours = $pdo->prepare($sqlCours);
                    $stmtCours->bindParam(':id_cours', $id_cours);
                    $stmtCours->bindParam(':enseignant_id', $this->id);
                    $stmtCours->execute();
                    $statistiques['cours'] = $stmtCours->fetch(PDO::FETCH_ASSOC);
                }
        
                return $statistiques;
        
            } catch (PDOException $e) {
                echo "Erreur lors de la récupération des statistiques : " . $e->getMessage();
                return false;
            }
        }
        public function modifierCours($idCours, $titre, $description, $contenu, $type, $categorie_id, $course_image, $video_file) {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                
                $checkSql = "SELECT id_course, type, image_url, contenu FROM courses 
                            WHERE id_course = :idCours 
                            AND enseignant_id = :enseignant_id";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->bindParam(':idCours', $idCours);
                $checkStmt->bindParam(':enseignant_id', $this->id);
                $checkStmt->execute();
                $coursData = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$coursData) {
                    throw new Exception("Ce cours ne vous appartient pas ou n'existe pas.");
                }
        
                $image_path = $coursData['image_url'];
                if ($course_image['error'] === UPLOAD_ERR_OK) {
                    if (!$this->validerImage($course_image)) {
                        throw new Exception("Image invalide. Formats acceptés : JPEG, PNG, GIF. Taille max : 5MB");
                    }
                    $image_path = $this->sauvegarderImage($course_image);
                }
        
                if ($coursData['type'] === 'video' && $video_file['error'] === UPLOAD_ERR_OK) {
                    $uploader = new FileUploader();
                    $contenu = $uploader->upload($video_file);
                }
        
                $sql = "UPDATE courses 
                        SET titre = :titre,
                            description = :description,
                            contenu = :contenu,
                            image_url = :image_url,
                            categorie_id = :categorie_id
                        WHERE id_course = :idCours";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':titre', $titre);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':contenu', $contenu);
                $stmt->bindParam(':image_url', $image_path);
                $stmt->bindParam(':categorie_id', $categorie_id);
                $stmt->bindParam(':idCours', $idCours);
        
                if ($stmt->execute()) {
                    return true;
                }
                return false;
        
            } catch (Exception $e) {
                throw new Exception("Erreur lors de la modification : " . $e->getMessage());
            }
        }
        public function supprimerCours($idCours) {
            try {
                $pdo = DatabaseConnection::getInstance()->getConnection();
                $sql = "SELECT type FROM courses WHERE id_course = :idCours";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':idCours', $idCours, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if (!$result) {
                    throw new Exception("Cours non trouvé.");
                }
        
                $type = $result['type'];
        
                if ($type === 'text') {
                    $cours = new Cours_text($idCours, '', '', date('Y-m-d H:i:s'), null, '');
                } elseif ($type === 'video') {
                    $cours = new Cours_video($idCours, '', '', date('Y-m-d H:i:s'), null, '');
                } else {
                    throw new Exception("Type de cours non valide.");
                }
        
                return $cours->supprimerCours($idCours);
            } catch (Exception $e) {
                throw new Exception("Erreur lors de la suppression du cours : " . $e->getMessage());
            }
        }
    }
?>