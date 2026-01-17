<?php
require_once  '../config.php';

// Protection : accès interdit si non connecté
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true) {
    header("Location: ../index.php");
    exit;
}


