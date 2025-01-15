<?php 
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gradient-to-b from-blue-50 via-white to-gray-100 font-sans">
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
                <li><a href="#about" class="hover:text-blue-500">À propos</a></li>
                <li><a href="#notre-public-cible" class="hover:text-blue-500">Notre Public</a></li>
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
                <li><a href="#about" class="hover:text-blue-500">À propos</a></li>
                <li><a href="#notre-public-cible" class="hover:text-blue-500">Notre Public</a></li>
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




    <section id="home" class="relative bg-cover bg-center   text-white" style="background-image: url('./images/bg.avif'); background-size: cover; /* Adapte l'image à l'écran */
  background-position: center; 
  background-repeat: no-repeat;
  height: 100vh; 
  margin: 0;">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-between py-40 px-6 lg:px-16 ">
            <div class="lg:max-w-lg">
                <h1 class="text-5xl font-extrabold leading-tight mb-4">
                    Etudiez en toute tranquilité avec des  <span class="text-white">cours online  de votre choix</span>
                </h1>
                <p class="text-lg mb-6">Découvrez des cours des professeurs du monde entier</p>
                <a href="#offers"
                    class="inline-block px-8 py-3 bg-green-400 text-blue-900 font-bold rounded-lg hover:bg-green-500">
                    Explorez les Cours
                </a>
            </div>
            <div class="mt-10 md:mt-0">
            </div>
        </div>

    </section>


    <div id="modalBackground" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50">
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
                    </select >
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
    <section id="about" class="py-16 bg-gray-50">
        <div class="container mx-auto px-6 lg:px-16 text-center">
            <h2 class="text-3xl font-bold text-blue-600 mb-6">Pourquoi Choisir Youdemy ?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg">
                    <img src="images/offres.png" alt="icon" class="mx-auto mb-4" style="width: 160px; height: 160px;">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Offres Exclusives</h3>
                    <p class="text-gray-600">Bénéficiez de promotions uniques pour les meilleures cours.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg">
                    <img src="images/247.png" alt="icon" class="mx-auto mb-4" style="width: 160px; height: 160px;">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Support 24/7</h3>
                    <p class="text-gray-600">Notre équipe est disponible à tout moment pour répondre à vos besoins.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg">
                    <img src="images/flexible.png" alt="icon" class="mx-auto mb-4" style="width: 160px; height: 160px;">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Flexibilité Maximale</h3>
                    <p class="text-gray-600">Réservez selon vos préférences avec des options flexibles.</p>
                </div>
            </div>
        </div>
    </section>
    <section id="notre-public-cible" class="py-16 bg-blue-50">
        <div class="container mx-auto px-6 lg:px-16 text-center">
            <h2 class="text-3xl font-bold text-blue-600 mb-6">Notre Public Cible</h2>
            <p class="text-lg text-gray-700 mb-8">
                Youdemy répond aux besoins variés de nombreux etudiants, en offrant un support de cours online robuste et bien structuré 
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg">
                    <img src="images/etudiants.png" alt="Solo Travelers" class="mx-auto mb-4" style="width: 160px; height: 160px;">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Etudiants</h3>
                    <p class="text-gray-600">
                        Idéal pour les etudiants qui veulent avoir des certificats en parallèle avec leurs formations.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg">
                    <img src="images/enseignants.png" alt="Couples" class="mx-auto mb-4" style="width: 160px; height: 160px;">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Enseignants</h3>
                    <p class="text-gray-600">
                        Idéale pour les enseignants qui veulent partager le contenu de leurs cours , et améliorer la visibilité et la qualité de leur enseignement.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg">
                    <img src="images/freelancers.png" alt="Corporate Travelers" class="mx-auto mb-4" style="width: 160px; height: 160px;">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Freelancers</h3>
                    <p class="text-gray-600">
                        Adéquat pour les freelancers qui veulent développer leurs portfolio
                    </p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-800 text-gray-300 py-10">
        <div class="container mx-auto px-6 lg:px-16">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <div class="text-2xl font-extrabold text-white">Youdemy</div>
                    <p class="mt-2 text-gray-400">Vootre platforme de cours online.</p>
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