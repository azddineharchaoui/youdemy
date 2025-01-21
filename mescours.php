<?php
session_start();
require_once("Classes/Etudiant.php");
require_once("Classes/Cours.php");

// Vérifier si l'utilisateur est connecté et est un etudiant
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3 ) {
    header('Location: index.php');
    exit();
}

$etudiant = new Etudiant($_SESSION['user_id'], '', '', '', '', 'active');
$mesCours = $etudiant->getMesCours();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - Youdemy</title>
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
                </form>            </div>
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
                </form>            </div>
            <?php }
            ?>
        </div>
    </header>
    
    
    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Mes Cours</h1>

        <?php if (empty($mesCours)): ?>
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <p class="text-gray-500">Vous n'êtes inscrit à aucun cours pour le moment.</p>
                <a href="allcours.php" class="mt-4 inline-block bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                    Découvrir les cours
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($mesCours as $cours): ?>
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">
                                <?php echo htmlspecialchars($cours['titre']); ?>
                            </h2>
                            <p class="text-gray-500 text-sm mb-4">
                                Par <?php echo htmlspecialchars($cours['nom_enseignant'] . ' ' . $cours['prenom_enseignant']); ?>
                            </p>
                            <p class="text-gray-600 mb-4">
                                <?php echo substr(htmlspecialchars($cours['description']), 0, 150) . '...'; ?>
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">
                                    Type: <?php echo ucfirst($cours['type']); ?>
                                </span>
                                <a href="cours.php?id=<?php echo $cours['id_course']; ?>" 
                                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    Continuer
                                </a>
                            </div>
                        </div>
                        <?php if ($cours['type'] === 'video'): ?>
                        <div class="px-6 py-3 bg-gray-50 border-t">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3.586l2.293-2.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 9.586V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-gray-500">Cours vidéo</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

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