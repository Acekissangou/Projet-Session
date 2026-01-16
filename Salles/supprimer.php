<?php
session_start();
require_once '../config.php';

// Sécurité
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true) {
    header("Location: ../index.php");
    exit;
}

// Vérification rôle admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}


if (!isset($_GET['id'])) exit;

$id = intval($_GET['id']);

$del = $pdo_init->prepare("DELETE FROM salles WHERE id = ?");
$del->execute([$id]);

header("Location: ../Salles/ajouter.php");
exit;
