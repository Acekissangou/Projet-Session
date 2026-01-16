<?php
session_start();
require_once '../config.php';

// Sécurité
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $reservation_id = $_POST['reservation_id'];
    $user_id = $_SESSION['user_id'];

    // Vérifier que la réservation appartient bien à l'utilisateur
    $check = $pdo_init->prepare("
        SELECT id FROM reservations
        WHERE id = ? AND user_id = ?
    ");
    $check->execute([$reservation_id, $user_id]);

    if ($check->rowCount() === 1) {

        // Suppression
        $delete = $pdo_init->prepare("
            DELETE FROM reservations WHERE id = ?
        ");
        $delete->execute([$reservation_id]);
    }
}


if ($_SESSION['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit;
}else{
    header("Location: ../user/dashboard.php");
    exit;
}
