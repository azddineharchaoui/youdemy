<?php
session_start();
require_once("Classes/Cours.php");
require_once("Classes/Cours_text.php");
require_once("Classes/Cours_video.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id_cours = (int)$_GET['id'];
$pdo = DatabaseConnection::getInstance()->getConnection();

// Récupérer les détails du cours
$sql = "SELECT c.*, u.nom as enseignant_nom, u.prenom as enseignant_prenom, cat.nom as categorie_nom, 
               GROUP_CONCAT(t.nom SEPARATOR ', ') as tags 
        FROM courses c 
        JOIN utilisateurs u ON c.enseignant_id = u.id_utilisateur 
        LEFT JOIN categories cat ON c.categorie_id = cat.id_categorie
        LEFT JOIN course_tags ct ON c.id_course = ct.course_id
        LEFT JOIN tags t ON ct.tag_id = t.id_tag
        WHERE c.id_course = :id
        GROUP BY c.id_course";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id_cours);
$stmt->execute();
$cours = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cours) {
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($cours['titre']); ?> - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <!-- Navbar -->

    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto flex justify-between items-center py-4 px-6">
            <div class="text-2xl font-extrabold text-blue-600">Youdemy</div>
            <button id="menuToggle" class="md:hidden text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
            <ul id="navLinks" class="hidden md:flex space-x-6 text-gray-700">
                <li><a href="#home" class="hover:text-blue-500">Accueil</a></li>
                <li><a href="allcours.php" class="hover:text-blue-500">Cours</a></li>
                <li><a href="mescours.php" class="hover:text-blue-500">Mes Cours</a></li>
            </ul>
            <div class="hidden md:flex space-x-4">
                <?php 
    if(!isset($_SESSION['user_id'])){
            ?>
                <button id="openLogin" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Connexion
                </button>
                <button id="openRegister" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    Inscription
                </button>
                <?php }else { 
        
            ?>
                <div class="flex justify-end">
                    <span><?php if(isset($_SESSION['user_name'])){echo $_SESSION['user_name'];}?></span>
                    <form action="logout.php" method="POST">
                        <button type="submit" name="submit"
                            class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            Déconnexion
                        </button>
                    </form>
                </div>
                <?php }
            ?>
            </div>

        </nav>
        <div id="mobileMenu" class="hidden bg-white shadow-md">
            <ul class="flex flex-col space-y-2 py-4 px-6 text-gray-700">
                <li><a href="index.php" class="hover:text-blue-500">Accueil</a></li>
                <li><a href="allcours.php" class="hover:text-blue-500">Cours</a></li>
                <li><a href="mescours.php" class="hover:text-blue-500">Mes Cours</a></li>
            </ul>
            <?php 
    if(!isset($_SESSION['user_id'])){
            ?>
            <button id="openLoginMobile" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Connexion
            </button>
            <button id="openRegisterMobile" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                Inscription
            </button>
            <?php }else { 
        
            ?>
            <div class="flex justify-center">
                <span><?php if(isset($_SESSION['user_name'])){echo $_SESSION['user_name'];}?></span>
                <form action="logout.php" method="POST">
                    <button type="submit" name="submit"
                        class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Déconnexion
                    </button>
                </form>
            </div>
            <?php }
            ?>
        </div>
    </header>

    <?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Succès!</strong>
        <span class="block sm:inline">Vous êtes maintenant inscrit à ce cours.</span>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Erreur!</strong>
        <span class="block sm:inline">
            <?php 
            echo isset($_GET['error']) && $_GET['error'] !== '1' 
                ? htmlspecialchars($_GET['error']) 
                : "Une erreur est survenue lors de l'inscription."; 
            ?>
        </span>
    </div>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($cours['titre']); ?></h1>

            <div class="mb-6">
                <p class="text-gray-600">Par
                    <?php echo htmlspecialchars($cours['enseignant_prenom'] . ' ' . $cours['enseignant_nom']); ?></p>
                <p class="text-gray-600">Créé le <?php echo date('d/m/Y', strtotime($cours['created_at'])); ?></p>
            </div>

            <div class="prose max-w-none mb-6">
                <h2 class="text-xl font-semibold mb-2">Description</h2>
                <p><?php echo nl2br(htmlspecialchars($cours['description'])); ?></p>
            </div>
            <div class="prose max-w-none mb-6">
                <?php if (!empty($cours['categorie_nom'])): ?>
                <p class="text-gray-600 mb-4">
                    <span class="font-semibold">Catégorie:</span>
                    <?php echo htmlspecialchars($cours['categorie_nom']); ?>
                </p>
                <?php endif; ?>

                <?php if (!empty($cours['tags'])): ?>
                <p class="text-gray-600 mb-4">
                    <span class="font-semibold">Tags:</span>
                    <?php 
                        $tags = explode(', ', $cours['tags']);
                        foreach ($tags as $tag):
                    ?>
                    <span
                        class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2">
                        <?php echo htmlspecialchars($tag); ?>
                    </span>
                    <?php endforeach; ?>
                </p>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 3): ?>
                <form action="inscription_cours.php" method="POST" class="mt-4">
                    <input type="hidden" name="course_id" value="<?php echo $cours['id_course']; ?>">
                    <button type="submit"
                        class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition-colors">
                        S'inscrire au cours
                    </button>
                </form>
                <?php elseif (!isset($_SESSION['user_id'])): ?>
                <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                    <p class="text-gray-700">Connectez-vous pour vous inscrire à ce cours.</p>
                    <button id="openLogin" class="mt-2 bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                        Se connecter
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Contenu du cours</h2>
                <?php if ($cours['type'] === 'video'): ?>
                <div class="aspect-w-16 aspect-h-9">
                    <video controls width="100%" class="w-full h-[70vh] rounded-lg">
                        <?php
                            $extension = strtolower(pathinfo($cours['contenu'], PATHINFO_EXTENSION));
                            $Types = [
                                'mp4' => 'video/mp4',
                                'avi' => 'video/x-msvideo',
                                'mov' => 'video/quicktime'
                            ];
                            $Type = isset($Types[$extension]) ? $Types[$extension] : 'video/mp4';
                        ?>
                        <source type="<?php echo $Type ; ?>" src="<?php echo $cours['contenu']; ?> ">
                        <p class="text-gray-600 mt-2">Votre navigateur ne supporte pas la lecture de cette vidéo.</p>
                    </video>
                </div>
                <?php else: ?>
                <div class="prose max-w-none">
                    <?php echo nl2br(htmlspecialchars($cours['contenu'])); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
    const menuToggle = document.getElementById('menuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const navLinks = document.getElementById('navLinks');
    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
    const modalBackground = document.getElementById('modalBackground');
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const openLogin = document.getElementById('openLogin');
    const openRegister = document.getElementById('openRegister');
    const closeLoginModal = document.getElementById('closeLoginModal');
    const closeRegisterModal = document.getElementById('closeRegisterModal');
    const openRegisterFromLogin = document.getElementById('openRegisterFromLogin');
    const openLoginFromRegister = document.getElementById('openLoginFromRegister');
    const openRegisterMobile = document.getElementById("openRegisterMobile");
    const openLoginMobile = document.getElementById("openLoginMobile");

    openLogin.addEventListener('click', () => {
        modalBackground.classList.remove('hidden');
        loginModal.classList.remove('hidden');
        registerModal.classList.add('hidden');
    });

    openLoginMobile.addEventListener('click', () => {
        modalBackground.classList.remove('hidden');
        loginModal.classList.remove('hidden');
        registerModal.classList.add('hidden');
    });

    openRegister.addEventListener('click', () => {
        modalBackground.classList.remove('hidden');
        registerModal.classList.remove('hidden');
        loginModal.classList.add('hidden');
    });

    openRegisterMobile.addEventListener('click', () => {
        modalBackground.classList.remove('hidden');
        registerModal.classList.remove('hidden');
        loginModal.classList.add('hidden');
    });


    closeLoginModal.addEventListener('click', () => {
        modalBackground.classList.add('hidden');
        loginModal.classList.add('hidden');
    });

    closeRegisterModal.addEventListener('click', () => {
        modalBackground.classList.add('hidden');
        registerModal.classList.add('hidden');
    });

    openRegisterFromLogin.addEventListener('click', () => {
        loginModal.classList.add('hidden');
        registerModal.classList.remove('hidden');
    });

    openLoginFromRegister.addEventListener('click', () => {
        registerModal.classList.add('hidden');
        loginModal.classList.remove('hidden');
    });

    modalBackground.addEventListener('click', (e) => {
        if (e.target === modalBackground) {
            modalBackground.classList.add('hidden');
            loginModal.classList.add('hidden');
            registerModal.classList.add('hidden');
        }
    });
    </script>

</body>

</html>