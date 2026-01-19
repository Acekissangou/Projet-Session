<?php
require_once '../config.php';

if (!isset($_POST['token'])) {
    echo "<p style='color:red'>Token manquant</p>";
    exit;
}

$token = $_POST['token'];

$stmt = $pdo_init->prepare("
    SELECT 
        r.id,
        r.date_reservation,
        r.heure_debut,
        r.heure_fin,
        r.qr_used,
        u.nom AS nom_user,
        u.prenom AS prenom_user,
        s.nom AS nom_salle,
        s.categorie
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN salles s ON r.salle_id = s.id
    WHERE r.qr_token = ?
");

$stmt->execute([$token]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    echo "<p style='color:red'>❌ QR code invalide</p>";
    exit;
}

if ((int)$reservation['qr_used'] === 1) {
    echo "<p style='color:orange'>⚠️ Cette réservation a déjà été utilisée</p>";
    exit;
}

// Sécurisation anti double scan
$update = $pdo_init->prepare("
    UPDATE reservations 
    SET qr_used = 1
    WHERE qr_token = ? AND qr_used = 0
");
$update->execute([$token]);

if ($update->rowCount() === 0) {
    echo "<p style='color:orange'>⚠️ QR déjà utilisé</p>";
    exit;
}
?>

<h3>✅ Réservation valide</h3>

<ul>
    <li><strong>Utilisateur :</strong>
        <?= htmlspecialchars($reservation['prenom_user'] . ' ' . $reservation['nom_user']) ?>
    </li>
    <li><strong>Salle :</strong>
        <?= htmlspecialchars($reservation['nom_salle']) ?>
        (<?= htmlspecialchars($reservation['categorie']) ?>)
    </li>
    <li><strong>Date :</strong>
        <?= htmlspecialchars($reservation['date_reservation']) ?>
    </li>
    <li><strong>Heure :</strong>
        <?= htmlspecialchars($reservation['heure_debut']) ?>
        →
        <?= htmlspecialchars($reservation['heure_fin']) ?>
    </li>
</ul>
