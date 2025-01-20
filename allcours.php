<?php
session_start();
require_once("Classes/Cours.php");
require_once("Classes/Cours_text.php");
require_once("Classes/Cours_video.php");
$notVisiteur = isset($_SESSION['user_id']);

$coursesPerPage = 6; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $coursesPerPage; 

$courseObj = new Cours_text(null, "", "", "", "", "");

$courses = [];
$totalCourses = 0;

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $courses = $courseObj->rechercherCours($_GET['search'], $coursesPerPage, $offset);
    $totalCourses = $courseObj->countCoursBySearch($_GET['search']);
} else {
    $courses = $courseObj->listerTousCours($coursesPerPage, $offset);
    $totalCourses = $courseObj->countAllCours();
}

$totalPages = ceil($totalCourses / $coursesPerPage); // Calcul du nombre total de pages
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les cours - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-b from-blue-50 via-white to-gray-100 font-sans">
    <!-- Header/Navbar -->
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
                <li><a href="index.php" class="hover:text-blue-500">Accueil</a></li>
                <li><a href="allcours.php" class="hover:text-blue-500">Cours</a></li>
                <?php
                if(isset($_SESSION['user_id'])){
            ?>
                <li><a href="mescours.php" class="hover:text-blue-500">Mes Cours</a></li>
                <?php } ?>
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
                <?php 
    if(isset($_SESSION['user_id'])){
            ?>
                <li><a href="mescours.php" class="hover:text-blue-500">Mes Cours</a></li>
                <?php } ?>
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
    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">

        <div id="modalBackground"
            class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50">
            <div id="loginModal" class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md hidden relative">
                <button id="closeLoginModal" class="absolute top-4 right-4 text-gray-500 hover:text-red-500">
                    ✕
                </button>
                <h2 class="text-3xl font-extrabold text-blue-600 mb-6 text-center">Connexion</h2>
                <form Action="login.php" method="POST">
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Adresse Email</label>
                        <input type="email" name="email" placeholder="Entrez votre email"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Mot de Passe</label>
                        <input type="password" name="password" placeholder="Entrez votre mot de passe"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                    </div>
                    <button type="submit" name="submit"
                        class="w-full bg-blue-600 text-white font-bold px-4 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                        Se Connecter
                    </button>
                </form>
                <p class="text-sm text-center text-gray-600 mt-4">
                    Vous n'avez pas de compte ?
                    <button id="openRegisterFromLogin" class="text-blue-500 hover:underline">
                        Inscrivez-vous
                    </button>
                </p>
            </div>

            <!-- Register Modal -->
            <div id="registerModal" class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md hidden relative">
                <!-- Close Button -->
                <button id="closeRegisterModal" class="absolute top-4 right-4 text-gray-500 hover:text-red-500">
                    ✕
                </button>
                <h2 class="text-3xl font-extrabold text-green-600 mb-6 text-center">Inscription</h2>
                <form Action="register.php" method="POST">
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Nom </label>
                        <input type="text" name="nom" placeholder="Entrez votre nom"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-400" />
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Prenom </label>
                        <input type="text" name="prenom" placeholder="Entrez votre prenom"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-400" />
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Adresse Email</label>
                        <input type="email" name="email" placeholder="Entrez votre email"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-400" />
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Mot de Passe</label>
                        <input type="password" name="password" placeholder="Entrez votre mot de passe"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-400" />
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">type d'utilisateur</label>
                        <select name="type_user" id="type_user" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="2">Enseignant</option>
                            <option value="3">Etudiant</option>
                        </select>
                    </div>
                    <button type="submit" name="submit"
                        class="w-full bg-green-600 text-white font-bold px-4 py-3 rounded-lg hover:bg-green-700 transition duration-200">
                        S'inscrire
                    </button>
                </form>
                <p class="text-sm text-center text-gray-600 mt-4">
                    Vous avez déjà un compte ?
                    <button id="openLoginFromRegister" class="text-green-500 hover:underline">
                        Connectez-vous
                    </button>
                </p>
            </div>
        </div>


        <h1 class="mb-4 text-xl font-extrabold text-gray-900 md:text-2xl lg:text-3xl dark:text-dark text-center">Les
            cours disponibles </h1>
        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto mb-8">
            <form action="" method="GET" class="flex gap-4">
                <input type="text" name="search" placeholder="Rechercher un cours..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Rechercher
                </button>
            </form>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
    if ($courses && count($courses) > 0) {
        foreach ($courses as $course) {
            ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <?php if (!empty($course['image_url'])): ?>
                <div class="w-full h-48 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($course['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($course['titre']); ?>" class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <?php endif; ?>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($course['titre']); ?>
                    </h3>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($course['description']); ?></p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">
                            Type: <?php echo ucfirst($course['type']); ?>
                        </span>
                        <?php if($notVisiteur): ?>
                        <a href="cours.php?id=<?php echo $course['id_course']; ?>"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Voir le cours
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="col-span-full text-center text-gray-600">Aucun cours trouvé.</div>';
    }
    ?>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-8">
            <?php if ($totalPages > 1): ?>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Précédent
                </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>"
                    class="px-4 py-2 <?php echo $i == $page ? 'bg-blue-700' : 'bg-blue-500'; ?> text-white rounded-lg hover:bg-blue-600">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Suivant
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-10 mt-16">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <div class="text-2xl font-extrabold text-white">Youdemy</div>
                    <p class="mt-2 text-gray-400">Votre plateforme de cours online.</p>
                </div>
                <ul class="flex space-x-4">
                    <li><a href="#" class="text-gray-400 hover:text-white">Mentions Légales</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Politique de Confidentialité</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Nous Contacter</a></li>
                </ul>
            </div>
            <div class="text-center mt-8 text-gray-500">© 2025 Youdemy. Tous droits réservés.</div>
        </div>
    </footer>

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