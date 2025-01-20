<?php 
require_once("db.php");

class Tag {
    private $idTag;
    private $nom;

    public function __construct($id, $nom) {
        $this->idTag = $id;
        $this->nom = $nom;
    }

    public function ajouterTag() {
        try {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            
            // Vérifier si le tag existe deja
            $checkSql = "SELECT COUNT(*) FROM tags WHERE nom = :nom";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->bindParam(':nom', $this->nom);
            $checkStmt->execute();
            
            if ($checkStmt->fetchColumn() > 0) {
                throw new Exception("Ce tag existe déjà");
            }

            $sql = "INSERT INTO tags (nom) VALUES (:nom)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $this->nom);
            
            if ($stmt->execute()) {
                $this->idTag = $pdo->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout du tag : " . $e->getMessage());
        }
    }

    public function modifierTag($idTag) {
        try {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            
            $sql = "UPDATE tags SET nom = :nom WHERE id_tag = :id_tag";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':id_tag', $idTag);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la modification du tag : " . $e->getMessage());
        }
    }

    public function supprimerTag($idTag) {
        try {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            
            // Supprimer d abord les relations dans la table associatif
            $sqlLiaison = "DELETE FROM course_tags WHERE tag_id = :id_tag";
            $stmtLiaison = $pdo->prepare($sqlLiaison);
            $stmtLiaison->bindParam(':id_tag', $idTag);
            $stmtLiaison->execute();
            
            // Puis supprimer le tag
            $sql = "DELETE FROM tags WHERE id_tag = :id_tag";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id_tag', $idTag);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression du tag : " . $e->getMessage());
        }
    }

    public static function listerTags() {
        try {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            
            $sql = "SELECT * FROM tags ORDER BY nom";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des tags : " . $e->getMessage());
        }
    }

    public static function getTagParId($idTag) {
        try {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            
            $sql = "SELECT * FROM tags WHERE id_tag = :id_tag";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id_tag', $idTag);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération du tag : " . $e->getMessage());
        }
    }

    public static function ajouterTagsCours($idCours, $tagIds) {
        try {
            if (empty($idCours)) {
                throw new Exception("L'identifiant du cours ne peut pas être vide");
            }
    
            if (!is_array($tagIds) || empty($tagIds)) {
                return true; 
            }
    
            $pdo = DatabaseConnection::getInstance()->getConnection();
            
            $sqlDelete = "DELETE FROM course_tags WHERE course_id = :course_id";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->bindParam(':course_id', $idCours);
            $stmtDelete->execute();
            
            $sql = "INSERT INTO course_tags (course_id, tag_id) VALUES (:course_id, :tag_id)";
            $stmt = $pdo->prepare($sql);
            
            // Insérer chaque tag
            foreach ($tagIds as $tagId) {
                $stmt->bindParam(':course_id', $idCours);
                $stmt->bindParam(':tag_id', $tagId);
                
                if (!$stmt->execute()) {
                    throw new Exception("Erreur lors de l'ajout du tag ID: $tagId au cours ID: $idCours");
                }
            }
            
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout des tags au cours : " . $e->getMessage());
        }
    }

    public static function getTagsCours($idCours) {
        try {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            
            $sql = "SELECT t.* FROM tags t 
                    INNER JOIN course_tags ct ON t.id_tag = ct.tag_id 
                    WHERE ct.course_id = :course_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':course_id', $idCours);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des tags du cours : " . $e->getMessage());
        }
    }

    public static function ajouterMultipleTags($tagsString) {
        $pdo = DatabaseConnection::getInstance()->getConnection();
        $resultat = [
            'success' => [],
            'errors' => []
        ];

        $tags = array_map('trim', explode(',', $tagsString));
        $tags = array_filter($tags); // Supprime les entrées vides

        foreach ($tags as $tagNom) {
            try {
                // Vérifier si le tag existe déjà
                $checkSql = "SELECT COUNT(*) FROM tags WHERE nom = :nom";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->bindParam(':nom', $tagNom);
                $checkStmt->execute();
                
                if ($checkStmt->fetchColumn() > 0) {
                    $resultat['errors'][] = "Le tag '$tagNom' existe déjà";
                    continue;
                }

                $sql = "INSERT INTO tags (nom) VALUES (:nom)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nom', $tagNom);
                
                if ($stmt->execute()) {
                    $resultat['success'][] = $tagNom;
                }
            } catch (PDOException $e) {
                $resultat['errors'][] = "Erreur lors de l'ajout du tag '$tagNom': " . $e->getMessage();
            }
        }

        return $resultat;
    }

}
?>