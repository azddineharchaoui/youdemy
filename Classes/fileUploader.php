<?php
class FileUploader {
    private $uploadDir;
    private $allowedTypes = ['video/mp4', 'video/avi', 'video/mov'];
    private $maxSize = 10 * 1024 * 1024; // 10MB

    public function __construct() {
        $this->uploadDir = dirname(__DIR__) . '/uploads/';
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload($file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            throw new Exception("Aucun fichier n'a été uploadé");
        }

        if (!in_array($file['type'], $this->allowedTypes)) {
            throw new Exception("Type de fichier non autorisé");
        }

        if ($file['size'] > $this->maxSize) {
            throw new Exception("Le fichier est trop volumineux (max 10MB)");
        }
        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $this->uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/' . $fileName;
        }

        throw new Exception("Erreur lors de l'upload du fichier");
    }
}