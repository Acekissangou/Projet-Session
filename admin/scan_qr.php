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
</head>
<body>

<h2>Scanner une réservation</h2>

<div id="reader" style="width:300px;"></div>

<div id="result"></div>

<script src="../js/scan.js"></script>
</body>
</html>
