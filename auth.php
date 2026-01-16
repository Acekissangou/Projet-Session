<?php
session_start();

function requireLogin() {
    if (!isset($_SESSION['connected'])) {
        header("Location: login.php");
        exit;
    }
}

function requireUser() {
    if (!isset($_SESSION['connected']) || $_SESSION['role'] !== 'user') {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    if (!isset($_SESSION['connected']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit;
    }
}
