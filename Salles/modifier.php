<?php
session_start();
require_once '../config.php';

$id = $_GET['id'] ?? null;

$req = $pdo_init->prepare("SELECT * FROM salles WHERE id = ?");
$req->execute([$id]);
$salle = $req->fetch();

if (!$salle) exit("Salle introuvable");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $capacite = $_POST['capacite'];
    $categrorie = $_POST['categorie'];
    $description = $_POST['description'];

    $update = $pdo_init->prepare(
        "UPDATE salles SET nom = ?, capacite = ?, categorie = ?, description = ? WHERE id = ?"
    );
    $update->execute([$nom, $capacite, $categrorie, $description, $id]);

    header("Location: ../Salles/ajouter.php");
    exit;
}
?>

<form method="POST">
    <input type="text" name="nom" value="<?= $salle['nom'] ?>">
    <input type="number" name="capacite" value="<?= $salle['capacite'] ?>">
    <input type="text" name="categorie" value="<?= $salle['categorie'] ?>">
    <input type="text" name ="description" value="<?= $salle['description'] ?>">
    <button type="submit">Modifier</button>
</form>
