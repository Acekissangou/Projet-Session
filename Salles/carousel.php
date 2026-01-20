<?php
require_once "../auth.php";
require_once "../config.php";
requireLogin();

if (!isset($_GET['salle_id'])) {
    die("Salle non spécifiée");
}

$salle_id = (int) $_GET['salle_id'];

$stmt = $pdo_init->prepare("
    SELECT 
        s.nom AS nom_salle,
        si.image_path
    FROM salle_images si
    JOIN salles s ON s.id = si.salle_id
    WHERE si.salle_id = ?
");

$stmt->execute([$salle_id]);
$images = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image</title>
    <link rel="stylesheet" href="../style/carousel.css">
</head>
<body>
<h2>
    <?= htmlspecialchars($images[0]['nom_salle']) ?>
</h2>

<?php if (count($images) === 0): ?>
    <p>Aucune image disponible pour cette salle.</p>
<?php else: ?>

<div class="carousel">

    <!-- GROUPE PRINCIPAL -->
    <div class="group">
        <?php foreach ($images as $img): ?>
            <div class="card">
                <img
                    src="/<?= htmlspecialchars($img['image_path']) ?>"
                    alt="Image salle"
                >
            </div>
        <?php endforeach; ?>
    </div>

    <!-- GROUPE DUPLIQUÉ (carousel infini) -->
    <div class="group" aria-hidden="true">
        <?php foreach ($images as $img): ?>
            <div class="card">
                <img
                    src="/<?= htmlspecialchars($img['image_path']) ?>"
                    alt="Image salle"
                >
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php endif; ?>


</body>
</html>