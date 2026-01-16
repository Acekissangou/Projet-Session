<?php
session_start();
require_once '../config.php';

// 🔐 Sécurité : admin seulement
if (!isset($_SESSION['connected']) || $_SESSION['role'] !== 'admin') {
    die("⛔ Accès interdit");
}

// 1️⃣ Vérifier le token
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("❌ QR code invalide");
}

$token = $_GET['token'];

// 2️⃣ Récupérer la réservation
$stmt = $pdo_init->prepare("
    SELECT 
        r.id,
        r.date_reservation,
        r.heure_debut,
        r.heure_fin,
        r.qr_used,
        u.nom,
        s.nom_salle
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN salles s ON r.salle_id = s.id
    WHERE r.qr_token = ?
");

$stmt->execute([$token]);
$reservation = $stmt->fetch();

if (!$reservation) {
    die("❌ Réservation introuvable");
}

// 3️⃣ Vérifier si déjà utilisé
if ($reservation['qr_used'] == 1) {
    die("⚠️ Ce QR code a déjà été utilisé");
}

// 4️⃣ Marquer comme utilisé
$update = $pdo_init->prepare("
    UPDATE reservations SET qr_used = 1 WHERE qr_token = ?
");
$update->execute([$token]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation réservation</title>
    <style>
        body { font-family: Arial; background:#f4f4f4; padding:40px; }
        .box {
            background:#fff;
            padding:30px;
            border-radius:10px;
            max-width:500px;
            margin:auto;
            box-shadow:0 0 15px rgba(0,0,0,.1);
        }
        h2 { color:green; }
    </style>
</head>
<body>

<div class="box">
    <h2>✅ Réservation validée</h2>
    <p><strong>Utilisateur :</strong> <?= htmlspecialchars($reservation['nom']) ?></p>
    <p><strong>Salle :</strong> <?= htmlspecialchars($reservation['nom_salle']) ?></p>
    <p><strong>Date :</strong> <?= $reservation['date_reservation'] ?></p>
    <p><strong>Heure :</strong> <?= $reservation['heure_debut'] ?> - <?= $reservation['heure_fin'] ?></p>
</div>

</body>
</html>
