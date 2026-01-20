<?php
require_once "../auth.php";
require_once "../config.php";

requireLogin();
requireAdmin();

$uploadDir = "../uploads/salles/";

// ✅ Créer le dossier s’il n’existe pas
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// 🔒 Vérifier qu’on peut écrire dedans
if (!is_writable($uploadDir)) {
    die("Le dossier d'upload n'est pas accessible en écriture");
}

if (!isset($_POST['salle_id'])) {
    die("Salle invalide");
}

$salle_id = (int) $_POST['salle_id'];


$uploadDir = "../uploads/salles/";

foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {

    if ($_FILES['images']['error'][$key] !== 0) {
        continue;
    }

    $extension = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array(strtolower($extension), $allowed)) {
        continue;
    }

    $fileName = uniqid("salle_", true) . "." . $extension;
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($tmpName, $filePath)) {

        $insert = $pdo_init->prepare("
            INSERT INTO salle_images (salle_id, image_path)
            VALUES (?, ?)
        ");
        $insert->execute([
            $salle_id,
            "uploads/salles/" . $fileName
        ]);
    }
}

header("Location: ../Salles/ajouter.php?success=images_added");
exit;