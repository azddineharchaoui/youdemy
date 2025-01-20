CREATE DATABASE youdemy;
use youdemy;
CREATE TABLE roles (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    nom ENUM('etudiant', 'enseignant', 'admin') NOT NULL
);

CREATE TABLE utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL ,
    prenom VARCHAR(255) NOT NULL ,
    email VARCHAR(255) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    statut ENUM('active', 'inactive', 'suspendu') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id_role) ON DELETE CASCADE
);

CREATE TABLE categories (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

CREATE TABLE tags (
    id_tag INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE courses (
    id_course INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(100),
    type ENUM('video', 'text'),
    contenu TEXT NOT NULL,
    categorie_id INT,
    enseignant_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id_categorie) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (enseignant_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE course_tags (
    course_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (course_id, tag_id),
    FOREIGN KEY (course_id) REFERENCES courses(id_course) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id_tag) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE inscriptions (
    course_id INT NOT NULL,
    etudiant_id INT NOT NULL,
    inscrit_a TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (course_id, etudiant_id),
    FOREIGN KEY (course_id) REFERENCES courses(id_course) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (etudiant_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE
);
