<?php
session_start();
require_once("Classes/Enseignant.php");
require_once("Classes/Cours.php");
require_once("Classes/Cours_text.php");
require_once("Classes/Cours_video.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2 || $_SESSION['isactive'] == false) {
    header('Location: index.php');
    exit();
}

$enseignant = new Enseignant($_SESSION['user_id'], '', '', '', '', 'active');
$message = '';

if (isset($_POST['submit_course'])) {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $contenu = isset($_POST['contenu']) ? $_POST['contenu'] : '';
    $categorie_id = $_POST['categorie_id'];
    
    try {
        if ($type === 'video') {
            if ($enseignant->creerCours($type, $titre, $description, '', $_FILES['course_image'], $_FILES['video_file'], $categorie_id)) {
                $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">Cours créé avec succès!</div>';
            }
        } else {
            if ($enseignant->creerCours($type, $titre, $description, $contenu, $_FILES['course_image'], null, $categorie_id)) {
                $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">Cours créé avec succès!</div>';
            }
        }
    } catch (Exception $e) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Erreur : ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

$mesCours = $enseignant->listerMesCours();

$inscrits = [];
if (isset($_GET['voir_inscrits'])) {
    $inscrits = $enseignant->voirInscrits($_GET['voir_inscrits']);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Enseignant - Youdemy</title>
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


    <div class="max-w-7xl mx-auto px-4 py-6">
        <?php echo $message; ?>

        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('create')"
                        class="tab-btn border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                        Créer un cours
                    </button>
                    <button onclick="showTab('list')"
                        class="tab-btn border-b-2 border-transparent px-1 py-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                        Mes cours
                    </button>
                </nav>
            </div>
        </div>

        <div id="create-tab" class="tab-content">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Créer un nouveau cours</h2>
                <form action="" method="POST" class="space-y-6" enctype="multipart/form-data">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Titre du cours</label>
                        <input type="text" name="titre" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <?php 
                        $categories = Cours::getCategories();
                    ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                        <select name="categorie_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach($categories as $categorie): ?>
                            <option value="<?php echo $categorie['id_categorie']; ?>">
                                <?php echo htmlspecialchars($categorie['nom']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" required rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <input type="file" name="course_image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-3">



                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tags</label>
                        <div class="mt-2 space-y-2">
                            <?php
                                require_once("Classes/Tag.php");
                                $tags = Tag::listerTags();
                                foreach($tags as $tag):
                            ?>
                            <div class="flex items-center">
                                <input type="checkbox" name="tags[]"
                                    value="<?php echo htmlspecialchars($tag['id_tag']); ?>"
                                    id="tag_<?php echo htmlspecialchars($tag['id_tag']); ?>"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="tag_<?php echo htmlspecialchars($tag['id_tag']); ?>"
                                    class="ml-2 text-sm text-gray-700">
                                    <?php echo htmlspecialchars($tag['nom']); ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type de cours</label>
                        <select name="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="text">Texte</option>
                            <option value="video">Vidéo</option>
                        </select>
                    </div>
                    <div id="contenu-field">
                        <label class="block text-sm font-medium text-gray-700">Contenu</label>
                        <textarea name="contenu" id="contenu-text" rows="10"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        <input type="file" name="video_file" id="contenu-video" accept="video/mp4,video/avi,video/mov"
                            class="mt-1 block w-full" style="display: none;">
                    </div>
                    <div>
                        <button type="submit" name="submit_course"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Créer le cours
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des cours -->
        <div id="list-tab" class="tab-content hidden">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Mes cours</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Titre
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Catégorie
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre d'inscrits
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($mesCours): foreach ($mesCours as $cours): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($cours['titre']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($cours['categorie_nom'] ?? 'Non catégorisé'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo ucfirst($cours['type']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo $cours['nombre_etudiants']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="?voir_inscrits=<?php echo $cours['id_course']; ?>"
                                        class="text-blue-600 hover:text-blue-900">Voir les inscrits</a>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Liste des inscrits  -->
            <?php if (isset($_GET['voir_inscrits']) && $inscrits): ?>
            <div class="mt-8 bg-white shadow rounded-lg p-6">
                <h3 class="text-xl font-bold mb-4">Étudiants inscrits</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nom
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date d'inscription
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($inscrits as $inscrit): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($inscrit['nom'] . ' ' . $inscrit['prenom']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($inscrit['email']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo date('d/m/Y', strtotime($inscrit['inscrit_a'])); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Gestion des onglets
    function showTab(tabName) {
        // Cacher tous les contenus
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Afficher le contenu sélectionné
        document.getElementById(tabName + '-tab').classList.remove('hidden');

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        event.currentTarget.classList.remove('border-transparent', 'text-gray-500');
        event.currentTarget.classList.add('border-blue-500', 'text-blue-600');
    }

    window.onload = function() {
        <?php if (isset($_GET['voir_inscrits'])): ?>
        showTab('list');
        <?php else: ?>
        showTab('create');
        <?php endif; ?>
    }

    document.querySelector('select[name="type"]').addEventListener('change', function(e) {
        const textArea = document.getElementById('contenu-text');
        const videoInput = document.getElementById('contenu-video');

        if (e.target.value === 'video') {
            textArea.style.display = 'none';
            videoInput.style.display = 'block';
            textArea.removeAttribute('required');
            videoInput.setAttribute('required', 'required');
        } else {
            textArea.style.display = 'block';
            videoInput.style.display = 'none';
            textArea.setAttribute('required', 'required');
            videoInput.removeAttribute('required');
        }
    });

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