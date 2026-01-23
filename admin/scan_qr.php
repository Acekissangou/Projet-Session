<?php
session_start();

if (!isset($_SESSION['connected']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Scan QR Code</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <link rel="stylesheet" href="../style/scan_qr.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<h2>Scanner une réservation</h2>
<p class="innt">Présenter le code QR devant la caméra</p>

<div id="reader" style="width:300px;"></div>

<div id="result">
    <p>RESULTATS</p>
</div>

<a href="../salles/ajouter.php" class="btn-retour">
    <i class="fa-solid fa-arrow-left"></i>
    Retour
</a>


<script src="../js/scan.js"></script>
</body>
</html>
