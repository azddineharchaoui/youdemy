<?php 
    require_once('../Classes/Categorie.php');
    require_once('../Classes/Cours.php');
    require_once('../Classes/Cours_text.php');
    require_once('../Classes/db.php');
    session_start();
    if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 1){
        header("Location: ../index.php");
    }

    $pdo = DatabaseConnection::getInstance()->getConnection();

    $coursInstance = new Cours_text(0, '', '', '', null, '');
    $totalCours = $coursInstance->countAllCours();

    $categoriesDistribution = $pdo->query("
        SELECT c.nom, COUNT(courses.id_course) AS course_count 
        FROM categories c 
        LEFT JOIN courses ON c.id_categorie = courses.categorie_id 
        GROUP BY c.id_categorie
    ")->fetchAll(PDO::FETCH_ASSOC);

    $mostPopularCourse = $pdo->query("
        SELECT courses.titre, COUNT(inscriptions.etudiant_id) AS student_count 
        FROM courses 
        LEFT JOIN inscriptions ON courses.id_course = inscriptions.course_id 
        GROUP BY courses.id_course 
        ORDER BY student_count DESC 
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);

    $topTeachers = $pdo->query("
        SELECT u.nom, u.prenom, COUNT(DISTINCT i.etudiant_id) AS student_count 
        FROM utilisateurs u 
        JOIN courses c ON u.id_utilisateur = c.enseignant_id 
        LEFT JOIN inscriptions i ON c.id_course = i.course_id 
        WHERE u.role_id = 2 
        GROUP BY u.id_utilisateur 
        ORDER BY student_count DESC 
        LIMIT 3
    ")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    :root {
        --sidebar-width: 250px;
    }

    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        padding-top: 1rem;
        background-color: #2c3e50;
        color: white;
        transition: all 0.3s;
        z-index: 1000;
        overflow-y: auto;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        padding: 2rem;
        transition: all 0.3s;
        width: calc(100% - var(--sidebar-width));
        min-height: 100vh;
    }

    .main-content.active {
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
    }

    .sidebar .nav-link {
        color: #ecf0f1;
        padding: 0.8rem 1rem;
        transition: all 0.3s;
    }

    .sidebar .nav-link:hover {
        background-color: #34495e;
        color: #fff;
    }

    .sidebar .nav-link.active {
        background-color: #3498db;
        color: #fff;
    }

    .sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    .section-content {
        display: none;
        width: 100%;
        padding: 1rem;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;

    }

    .section-content .container {

        max-width: none;
        padding: 0;
    }

    .table-responsive {
        width: 100%;
        margin: 0;
    }

    .section-content.active {
        display: block;
        opacity: 1;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .brand-name {
        font-size: 1.5rem;
        padding: 1rem;
        text-align: center;
        border-bottom: 1px solid #34495e;
        margin-bottom: 1rem;
    }

    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sidebar {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .sidebar.active {
            margin-left: 0;
        }

        .main-content {
            margin-left: 0;
            width: 100%;

        }

        .main-content.active {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }
    }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand-name">
            <i class="fas fa-car"></i> Admin Panel
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="#dashboard" data-section="dashboard">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link" href="../index.php">
                <i class="fa fa-home"></i> Page d'accueil
            </a>
            <a class="nav-link" href="#utilisateurs" data-section="utilisateurs">
                <i class="fa fa-user"></i> Utilisateurs
            </a>
            <a class="nav-link" href="#categories" data-section="categories">
                <i class="fas fa-tags"></i> Catégories
            </a>

            <a class="nav-link" href="#tags" data-section="tags">
                <i class="fas fa-tags"></i> Tags
            </a>

            
        </nav>
    </div>


    <div class="main-content">

        <div id="dashboard" class="section-content active">
            <h2 class="mb-4">Dashboard Vue d'ensemble</h2>
            <div class="row">
                <!-- Total Courses Card -->
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total des Cours</h5>
                            <p class="card-text display-6"><?= $totalCours ?></p>
                        </div>
                    </div>
                </div>

                <!-- Most Popular Course Card -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Cours le plus populaire</h5>
                            <?php if (!empty($mostPopularCourse)): ?>
                            <p class="card-text"><?= htmlspecialchars($mostPopularCourse['titre']) ?></p>
                            <small><?= $mostPopularCourse['student_count'] ?> étudiants inscrits</small>
                            <?php else: ?>
                            <p class="card-text">Aucun cours trouvé</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Category Distribution -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Répartition par Catégorie</h5>
                            <ul class="list-group">
                                <?php foreach ($categoriesDistribution as $category): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($category['nom']) ?>
                                    <span class="badge bg-primary rounded-pill"><?= $category['course_count'] ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Top 3 Teachers -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Top 3 Enseignants</h5>
                            <ul class="list-group">
                                <?php foreach ($topTeachers as $teacher): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($teacher['prenom'] . ' ' . $teacher['nom']) ?>
                                    <span class="badge bg-success rounded-pill"><?= $teacher['student_count'] ?>
                                        étudiants</span>
                                </li>
                                <?php endforeach; ?>
                                <?php if (empty($topTeachers)): ?>
                                <li class="list-group-item">Aucun enseignant trouvé</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="categories" class="section-content">
            <div class="container mt-5">
                <h2>Gestion des Catégories</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom de la Catégorie</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                $categories = Categorie::listerCategories();
                if (count($categories) > 0):
                    foreach ($categories as $categorie):
                ?>
                        <tr>
                            <td><?= $categorie['id_categorie']; ?></td>
                            <td><?= htmlspecialchars($categorie['nom'], ENT_QUOTES); ?></td>
                            <td><?= htmlspecialchars($categorie['description'] ?? 'Non spécifiée', ENT_QUOTES); ?></td>
                            <td>

                                <button type="button" class="btn btn-success btn-sm editCategorieBtn"
                                    data-id="<?= $categorie['id_categorie']; ?>"
                                    data-nom="<?= htmlspecialchars($categorie['nom']); ?>"
                                    data-description="<?= htmlspecialchars($categorie['description'] ?? ''); ?>">
                                    Modifier
                                </button>

                                <!-- Formulaire Supprimer -->
                                <form method="POST" action="manage_categorie.php" class="d-inline">
                                    <input type="hidden" name="delete_categorie"
                                        value="<?= $categorie['id_categorie']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                    endforeach;
                else:
                ?>
                        <tr>
                            <td colspan="4" class="text-center">Aucune catégorie trouvée.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Formulaire Ajouter une Catégorie -->
                <h3>Ajouter une Nouvelle Catégorie</h3>
                <form method="POST" action="manage_categorie.php">
                    <div class="mb-3">
                        <label for="nomCategorie" class="form-label">Nom de la Catégorie</label>
                        <input type="text" name="nom_categorie" id="nomCategorie" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="descriptionCategorie" class="form-label">Description</label>
                        <textarea name="description_categorie" id="descriptionCategorie" class="form-control"
                            rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_categorie" class="btn btn-primary">Ajouter</button>
                </form>
            </div>

            <!-- Modale de modification categorie -->

            <div class="modal fade" id="editCategorieModal" tabindex="-1" aria-labelledby="editCategorieModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="manage_categorie.php">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCategorieModalLabel">Modifier la Catégorie</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id_categorie" id="editCategorieId">
                                <div class="mb-3">
                                    <label for="editNomCategorie" class="form-label">Nom de la Catégorie</label>
                                    <input type="text" name="nom_categorie" id="editNomCategorie" class="form-control"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="editDescriptionCategorie" class="form-label">Description</label>
                                    <textarea name="description_categorie" id="editDescriptionCategorie"
                                        class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" name="edit_categorie" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="utilisateurs" class="section-content">
            <div class="container mt-5">
                <h2>Gestion des Utilisateurs</h2>

                <?php
                    if (isset($_SESSION['message'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']);
                    }
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }

                    // Recuperation des utilisateurs
                    $pdo = DatabaseConnection::getInstance()->getConnection();
                    $sql = "SELECT u.*, r.nom as nom_role
                            FROM utilisateurs u 
                            JOIN roles r ON u.role_id = r.id_role 
                            ORDER BY u.created_at DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id_utilisateur']) ?></td>
                                <td><?= htmlspecialchars($user['nom']) ?></td>
                                <td><?= htmlspecialchars($user['prenom']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['nom_role']) ?></td>
                                <td>
                                    <span class="badge <?= $user['statut'] === 'active' ? 'bg-success' : 
                                    ($user['statut'] === 'suspendu' ? 'bg-danger' : 'bg-warning') ?>">
                                        <?= htmlspecialchars($user['statut']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($user['created_at']) ?></td>
                                <td>
                                    <?php if ($user['role_id'] == 3): // Si c'est un étudiant ?>
                                    <?php if ($user['statut'] !== 'suspendu'): ?>
                                    <form method="POST" action="manage_users.php" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id_utilisateur'] ?>">
                                        <button type="submit" name="ban_user" class="btn btn-danger btn-sm">
                                            Bannir
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <form method="POST" action="manage_users.php" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id_utilisateur'] ?>">
                                        <button type="submit" name="unban_user" class="btn btn-success btn-sm">
                                            Débannir
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($user['role_id'] == 2): ?>
                                    <?php if ($user['statut'] === 'inactive'): ?>
                                    <form method="POST" action="manage_users.php" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id_utilisateur'] ?>">
                                        <button type="submit" name="activate_teacher" class="btn btn-success btn-sm">
                                            Activer
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <div id="tags" class="section-content">
            <div class="container mt-5">
                <h2>Gestion des Tags</h2>
                <?php
                    if (isset($_SESSION['message'])) {
                        echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
                        unset($_SESSION['message']);
                    }
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
                ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom du Tag</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                require_once('../Classes/Tag.php');
                $tags = Tag::listerTags();
                if (count($tags) > 0):
                    foreach ($tags as $tag):
                ?>
                        <tr>
                            <td><?= $tag['id_tag']; ?></td>
                            <td><?= htmlspecialchars($tag['nom'], ENT_QUOTES); ?></td>
                            <td>
                                <button type="button" class="btn btn-success btn-sm editTagBtn"
                                    data-id="<?= $tag['id_tag']; ?>" data-nom="<?= htmlspecialchars($tag['nom']); ?>">
                                    Modifier
                                </button>
                                <form method="POST" action="manage_tag.php" class="d-inline">
                                    <input type="hidden" name="delete_tag" value="<?= $tag['id_tag']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                    endforeach;
                else:
                ?>
                        <tr>
                            <td colspan="3" class="text-center">Aucun tag trouvé.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Formulaire Ajouter un Tag -->

                <h3>Ajouter des Tags</h3>
                <form method="POST" action="manage_tag.php" class="mb-8">
                    <div class="mb-3">
                        <label for="nomTags" class="form-label">Noms des Tags (séparés par des virgules)</label>
                        <input type="text" name="noms_tags" id="nomTags" class="form-control"
                            placeholder="Tag1, Tag2, Tag3" required>
                        <small class="text-muted">Entrez plusieurs tags séparés par des virgules</small>
                    </div>
                    <button type="submit" name="add_multiple_tags" class="btn btn-primary">Ajouter les tags</button>
                </form>
            </div>

            <!-- Modal de modification de tag -->
            <div class="modal fade" id="editTagModal" tabindex="-1" aria-labelledby="editTagModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="manage_tag.php">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editTagModalLabel">Modifier le Tag</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id_tag" id="editTagId">
                                <div class="mb-3">
                                    <label for="editNomTag" class="form-label">Nom du Tag</label>
                                    <input type="text" name="nom_tag" id="editNomTag" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" name="edit_tag" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="cours" class="section-content">
            <h2>Gestion des Cours</h2>
            <!-- Contenu de la section cours -->
        </div>

       

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fonction pour afficher une section
        function showSection(sectionId) {
            // Cacher toutes les sections
            document.querySelectorAll('.section-content').forEach(section => {
                section.classList.remove('active');
            });

            // Désactiver tous les liens
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });

            // Afficher la section demandée
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.add('active');
                const activeLink = document.querySelector(`.nav-link[data-section="${sectionId}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }
        }

        // Gestionnaire d'événements pour les liens de navigation
        document.querySelectorAll('.nav-link[data-section]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.getAttribute('data-section');
                if (sectionId) {
                    showSection(sectionId);
                    if (window.innerWidth < 768) {
                        document.querySelector('.sidebar').classList.remove('active');
                        document.querySelector('.main-content').classList.remove('active');
                    }
                }
            });
        });

        // Bouton de toggle pour mobile
        const toggleBtn = document.createElement('button');
        toggleBtn.classList.add('btn', 'btn-primary', 'd-md-none');
        toggleBtn.style.position = 'fixed';
        toggleBtn.style.top = '1rem';
        toggleBtn.style.left = '1rem';
        toggleBtn.style.zIndex = '1001';
        toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.appendChild(toggleBtn);

        toggleBtn.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        });

        // Gestionnaire d'événements pour les boutons d'édition de catégorie
        document.querySelectorAll('.editCategorieBtn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const nom = button.getAttribute('data-nom');
                const description = button.getAttribute('data-description');

                document.getElementById('editCategorieId').value = id;
                document.getElementById('editNomCategorie').value = nom;
                document.getElementById('editDescriptionCategorie').value = description;

                const editModal = new bootstrap.Modal(document.getElementById(
                    'editCategorieModal'));
                editModal.show();
            });
        });

        // Afficher la section dashboard par défaut
        showSection('dashboard');
    });
    </script>
</body>

</html>