<?php
session_start();
require_once("Classes/Enseignant.php");
require_once("Classes/Cours.php");
require_once("Classes/Cours_text.php");
require_once("Classes/Cours_video.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
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

$statistiques = $enseignant->getStatistiquesCours();

if (isset($_POST['modifier_cours_submit'])) {
    $idCours = $_POST['id_course'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $contenu = $_POST['contenu'] ?? null;
    $type = $_POST['type'];
    $categorie_id = $_POST['categorie_id'];
    
    try {
        $result = $enseignant->modifierCours($idCours, $titre, $description, $contenu, $type, $categorie_id, $_FILES['course_image'], $_FILES['video_file']);

        if ($result) {
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">Cours modifié avec succès!</div>';
            $mesCours = $enseignant->listerMesCours();
        }
    } catch (Exception $e) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Erreur : '.htmlspecialchars($e->getMessage()).'</div>';
    }
}
if (isset($_GET['supprimer_cours'])) {
    $idCours = $_GET['supprimer_cours'];
    try {
        if ($enseignant->supprimerCours($idCours)) {
            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">Cours supprimé avec succès!</div>';
            $mesCours = $enseignant->listerMesCours();
        } else {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Erreur lors de la suppression du cours.</div>';
        }
    } catch (Exception $e) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">Erreur : ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
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
            </ul>
            <div class=" md:flex space-x-4">

                <div class="flex justify-end">
                    <span><?php if(isset($_SESSION['user_name'])){echo $_SESSION['user_name'];}?></span>
                    <form action="logout.php" method="POST">
                        <button type="submit" name="submit"
                            class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            Déconnexion
                        </button>
                    </form>
                </div>

            </div>

        </nav>
        <div id="mobileMenu" class="hidden bg-white shadow-md">
            <ul class="flex flex-col space-y-2 py-4 px-6 text-gray-700">
                <li><a href="index.php" class="hover:text-blue-500">Accueil</a></li>
                <li><a href="allcours.php" class="hover:text-blue-500">Cours</a></li>
                <li><a href="mescours.php" class="hover:text-blue-500">Mes Cours</a></li>
            </ul>

            <div class="flex justify-center">
                <span><?php if(isset($_SESSION['user_name'])){echo $_SESSION['user_name'];}?></span>
                <form action="logout.php" method="POST">
                    <button type="submit" name="submit"
                        class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Déconnexion
                    </button>
                </form>
            </div>

        </div>
    </header>


    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6">Statistiques Globales</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-100 p-4 rounded-lg">
                    <p class="text-lg font-semibold">Nombre de cours créés</p>
                    <p class="text-2xl"><?php echo $statistiques['global']['nombre_cours']; ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <p class="text-lg font-semibold">Nombre d'étudiants inscrits</p>
                    <p class="text-2xl"><?php echo $statistiques['global']['nombre_etudiants_inscrits']; ?></p>
                </div>
            </div>
        </div>

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

                    <input type="file" name="course_image" accept="image/*"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3">



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
                                    <button class="edit-course-btn text-green-600 hover:text-green-900 mr-2"
                                        data-course-id="<?php echo $cours['id_course']; ?>"
                                        data-course-titre="<?php echo htmlspecialchars($cours['titre']); ?>"
                                        data-course-description="<?php echo htmlspecialchars($cours['description']); ?>"
                                        data-course-contenu="<?php echo htmlspecialchars($cours['contenu']); ?>"
                                        data-course-type="<?php echo $cours['type']; ?>"
                                        data-course-categorie="<?php echo $cours['categorie_id']; ?>">
                                        Modifier
                                    </button>
                                    <a href="?supprimer_cours=<?php echo $cours['id_course']; ?>"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?');">
                                        Supprimer
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- Liste des inscrits  -->
        <?php if (isset($_GET['voir_inscrits']) && $inscrits): ?>
        <!-- Section des Statistiques Spécifiques à un Cours -->
        <?php
                $statistiquesCours = $enseignant->getStatistiquesCours($_GET['voir_inscrits']);
                ?>
        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4">Statistiques du Cours</h3>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-100 p-4 rounded-lg">
                    <p class="text-lg font-semibold">Nombre d'inscrits</p>
                    <p class="text-2xl"><?php echo $statistiquesCours['cours']['nombre_inscrits']; ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <p class="text-lg font-semibold">Nouveaux inscrits (1 mois)</p>
                    <p class="text-2xl"><?php echo $statistiquesCours['cours']['nouveaux_inscrits_mois']; ?></p>
                </div>
            </div>
            <h3 class="text-xl font-bold mb-4">Étudiants inscrits</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <!-- Tableau des inscrits -->
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>


    <!-- Modal de modification de cours -->
    <div id="editCourseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Modifier le cours</h3>
                <form id="editCourseForm" method="POST" class="mt-2 space-y-4" action="" enctype="multipart/form-data">
                    <input type="hidden" name="id_course" id="editCourseId">

                    <div>
                        <label for="editTitre" class="block text-sm font-medium text-gray-700">Titre</label>
                        <input type="text" name="titre" id="editTitre" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="editDescription" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="editDescription" required rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                        <select name="categorie_id" id="editCategorie" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <?php foreach($categories as $categorie): ?>
                            <option value="<?php echo $categorie['id_categorie']; ?>">
                                <?php echo htmlspecialchars($categorie['nom']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Image du cours</label>
                        <input type="file" name="course_image" accept="image/*"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type de cours</label>
                        <select name="type" id="editType" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="text">Texte</option>
                            <option value="video">Vidéo</option>
                        </select>
                    </div>

                    <div id="editContenuField">
                        <div id="editTextContent">
                            <label class="block text-sm font-medium text-gray-700">Contenu texte</label>
                            <textarea name="contenu" rows="10"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                        <div id="editVideoContent" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700">Nouvelle vidéo (laisser vide pour
                                conserver l'actuelle)</label>
                            <input type="file" name="video_file" accept="video/mp4,video/avi,video/mov"
                                class="mt-1 block w-full">
                        </div>
                    </div>




                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Annuler</button>
                        <button type="submit" name="modifier_cours_submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    // Gestion des onglets
    function showTab(tabName, event = null) {
        // Cacher tous les contenus
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Afficher le contenu selectionne
        document.getElementById(tabName + '-tab').classList.remove('hidden');

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        if (event) {
            event.currentTarget.classList.remove('border-transparent', 'text-gray-500');
            event.currentTarget.classList.add('border-blue-500', 'text-blue-600');
        }
    }

    window.onload = function() {
        <?php if (isset($_GET['voir_inscrits'])): ?>
        showTab('list', {
            currentTarget: document.querySelector("button[onclick*='list']")
        });
        <?php else: ?>
        showTab('create', {
            currentTarget: document.querySelector("button[onclick*='create']")
        });
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




    function openEditModal(courseId, titre, description, contenu, type, categorieId) {
        // Remplissage des champs de base
        document.getElementById('editCourseId').value = courseId;
        document.getElementById('editTitre').value = titre;
        document.getElementById('editDescription').value = description;
        document.querySelector('textarea[name="contenu"]').value = contenu;
        document.getElementById('editType').value = type;
        document.getElementById('editCategorie').value = categorieId;

        const textContent = document.getElementById('editTextContent');
        const videoContent = document.getElementById('editVideoContent');

        if (type === 'video') {
            textContent.style.display = 'none';
            videoContent.style.display = 'block';
        } else {
            textContent.style.display = 'block';
            videoContent.style.display = 'none';
        }

        document.getElementById('editType').addEventListener('change', function(e) {
            if (e.target.value === 'video') {
                textContent.style.display = 'none';
                videoContent.style.display = 'block';
            } else {
                textContent.style.display = 'block';
                videoContent.style.display = 'none';
            }
        });

        document.getElementById('editCourseModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editCourseModal').classList.add('hidden');
    }

    document.querySelectorAll('.edit-course-btn').forEach(button => {
        button.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            const titre = this.getAttribute('data-course-titre');
            const description = this.getAttribute('data-course-description');
            const contenu = this.getAttribute('data-course-contenu');
            const type = this.getAttribute('data-course-type');
            const categorieId = this.getAttribute('data-course-categorie');

            openEditModal(courseId, titre, description, contenu, type, categorieId);
        });
    });

    document.getElementById('editType').addEventListener('change', function(e) {
        const textContent = document.getElementById('editTextContent');
        const videoContent = document.getElementById('editVideoContent');

        if (e.target.value === 'video') {
            textContent.style.display = 'none';
            videoContent.style.display = 'block';
        } else {
            textContent.style.display = 'block';
            videoContent.style.display = 'none';
        }
    });
    </script>
</body>

</html>