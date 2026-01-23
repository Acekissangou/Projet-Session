<?php
require_once "../auth.php";
requireLogin();
requireAdmin();

if (!isset($_GET['salle_id'])) {
    die("Salle non spécifiée");
}

$salle_id = (int) $_GET['salle_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/ajout_image.css">
    <title>Ajouter Images</title>
</head>
<body>
    <h2>AJOUTER DES IMAGES A LA SALLE</h2>

<form action="upload_images.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="salle_id" value="<?= $salle_id ?>">

    <input type="file" name="images[]" multiple accept="image/*" required>

    <button type="submit">Uploader</button>
</form>

</body>
</html>