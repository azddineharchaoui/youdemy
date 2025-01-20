<?php 
    require_once("db.php");
    class Utilisateur{
        protected $id; 
        protected $nom;
        protected $prenom;
        protected $email;
        protected $password;
        protected $role_id;

        public function __construct($id, $nom, $prenom, $email, $password, $role_id){
            $this->id = $id;
            $this->nom = $nom;
            $this->prenom = $prenom;
            $this->email = $email;
            $this->password = $password;
            $this->role_id = $role_id;
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
        public function get_prenom(){
            return $this->prenom;
        }
        public function set_prenom($prenom){
            $this->prenom = $prenom;
        }
        public function get_email(){
            return $this->email;
        }
        public function set_email($email){
            $this->email = $email;
        }
        public function get_password(){
            return $this->password;
        }
        public function set_password($password){
            $this->password = $password;
        }
        public function get_role_id(){
            return $this->role_id;
        }
        public function set_role_id($role_id){
            $this->role_id = $role_id;
        }

        public static function login($email, $password) {
            $pdo = DatabaseConnection::getInstance()->getConnection();
            if (!$pdo) {
                echo "Erreur de connexion à la base de données.";
                return null;
            }
            
            $query = "SELECT u.id_utilisateur, u.nom, u.password , u.statut, r.id_role 
                      FROM utilisateurs u 
                      INNER JOIN roles r ON u.role_id = r.id_role
                      WHERE u.email = :email";
        
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
        
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if (password_verify($password, $user['password'])) {
                    if($user['statut'] == 'active'){
                    session_start();
                    $_SESSION['user_id'] = $user['id_utilisateur'];
                    $_SESSION['role_id'] = $user['id_role'];
                    $_SESSION['user_name'] = $user['nom'];
                    $_SESSION['isactive'] = true;
        
                    if ($_SESSION['role_id'] == 1) {
                        header("Location: ./Admin/dashboard.php");
                    } else if ($_SESSION['role_id'] == 2){
                        header("Location: ./enseignantPage.php");
                    } else {
                        header("Location: ./allcours.php");
                    }
                }else if ($user['statut'] == 'inactive'){
                    $_SESSION['isactive'] = false;
                    header("Location: index.php");

                    echo "<script>alert('VOtre compte est inactive');</script>";
                } else if ($user['statut'] == 'suspendu'){
                    $_SESSION['isactive'] = false;
                    header("Location: index.php");

                    echo "<script>alert('VOtre compte est suspendu');</script>";
                }
                } else {
                    echo "<script>alert('Mot de passe incorrect. Veuillez réessayer.');</script>";
                    header("Refresh: 0; URL=index.php");
                }
            } else {
                echo "<script>alert('Adresse e-mail introuvable. Veuillez vérifier vos informations.');</script>";
                header("Refresh: 0; URL=index.php");
            }
        }

        public static function logout() {
            session_start();
        
            if (isset($_SESSION['user_id'])) {
                session_unset();
                session_destroy();
                header("Location: ./index.php");  
                exit();
            }
        }
        
    }
?>