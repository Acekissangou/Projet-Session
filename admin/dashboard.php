<?php
session_start();
require_once '../config.php';

// Protection : accès interdit si non connecté
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true) {
    header("Location: ../index.php");
    exit;
}

//Variables pour l'affichage des stats
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

//Récupération de l'id de l'user connecté
$userId = $_SESSION['user_id'];

$reqReservations = $pdo_init->prepare(
    "SELECT COUNT(*) FROM reservations WHERE user_id = ?"
);
$reqReservations->execute([$userId]);
$nbReservations = $reqReservations->fetchColumn();

$reqSalles = $pdo_init->query(
    "SELECT COUNT(*) FROM salles"
);
$nbSalles = $reqSalles->fetchColumn();

// Récupération du nom
$nomUser = $_SESSION['nom'];

$nomUser = strtoupper($nomUser); // tout en majuscule

// Première lettre du nom (majuscule)
$initiale = strtoupper(substr($nomUser, 0, 1));

// reception des données de la reservation venant du formulaire dans ajouter.php

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id      = $_SESSION['user_id'];
    $salle_id     = $_POST['salle'];
    $date         = $_POST['date'];
    $heure_debut  = $_POST['heure_debut'];
    $heure_fin    = $_POST['heure_fin'];

    // Variable pour verfication
    $heure_debut0 = new datetime($heure_debut);
    $heure_fin0 = new datetime($heure_fin);
    $interval0 = $heure_debut0->diff($heure_fin0);

    //Convertir tout en minute
    $duree_minute = ($interval0->h * 60) + $interval0->i;

    // Requêtepo recuperer l'heure minimale
    $reqMin = $pdo_init->prepare("
        SELECT heure_minimale
        FROM salles
        WHERE id= ?
    ");
    $reqMin->execute([$salle_id]);
    $heure_minimale = (int) $reqMin->fetchColumn();
    $heure_enHeure = $heure_minimale/60;


    // 1️⃣ Vérifier que l'heure de fin est après l'heure de début
    if ($heure_fin <= $heure_debut) {
        $message = "❌ L'heure de fin doit être après l'heure de début.";
    } elseif ($date < $currentDate) {
        // 1️⃣ Vérifier que la date n'est pas dans le passé
        $message = "❌ La date de réservation ne peut pas être dans le passé.";
    } elseif($duree_minute < $heure_minimale){
        if($heure_minimale >= 60) {
            $message = "❌ Cette salle nécessite une reservations minimale de $heure_enHeure h.";
            } else{
                $message = "❌ Cette salle nécessite une reservations minimale de $heure_minimale minute.";
            }
    }
    else {

        // 2️⃣ Vérifier si la salle est déjà réservée sur ce créneau
        $check = $pdo_init->prepare("
            SELECT id FROM reservations
            WHERE salle_id = ?
            AND date_reservation = ?
            AND (
                heure_debut < ?
                AND heure_fin > ?
            )
        ");
        $check->execute([
            $salle_id,
            $date,
            $heure_fin,
            $heure_debut
        ]);
            // Token pour QR code
        $qr_token = bin2hex(random_bytes(16)); // token sécurisé

        if ($check->rowCount() > 0) {
            $message = "❌ Cette salle est déjà réservée sur ce créneau.";
        } else {

            // 3️⃣ Insertion de la réservation
            $insert = $pdo_init->prepare("
                INSERT INTO reservations 
                (user_id, salle_id, date_reservation, heure_debut, heure_fin, qr_token, qr_used)
                VALUES (?, ?, ?, ?, ?, ?, 0)
            ");

            $insert->execute([
                $user_id,
                $salle_id,
                $date,
                $heure_debut,
                $heure_fin,
                $qr_token
            ]);

            $message = "✅ Réservation effectuée avec succès !";
            // Generation du code QR
            require_once '../lib/phpqrcode/qrlib.php';
            $qrPath = '../qrCodes/' . $qr_token . '.png';
            QRcode::png(
                $qr_token,
                $qrPath, QR_ECLEVEL_H, 5
            );
        }
    }

    header("Location: dashboard.php?message=" . urlencode($message));
    exit;
}

// Recuperation des données pour l'affichage des reservations de l'utilisateur

$reqReservations = $pdo_init->prepare("
    SELECT 
        r.id,
        s.nom AS salle_nom,
        r.date_reservation,
        r.heure_debut,
        r.heure_fin,
        r.qr_token
    FROM reservations r
    JOIN salles s ON r.salle_id = s.id
    WHERE r.user_id = ?
    ORDER BY r.date_reservation DESC, r.heure_debut DESC
");
$reqReservations->execute([$_SESSION['user_id']]);
$reservations = $reqReservations->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../style/style0.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap');
    </style>
</head>
<body>

<header class="topbar">
    <i class="fa-solid fa-bars bur-ger" id="menu-btn"></i>
    <span class="title"><img src="../images/logo.jpg" alt="" height="200px" width="200px"></span>
    <div class="top-icons">
        <!-- <i class="fa-solid fa-magnifying-glass"></i> -->
        <div class="avatar-icon"><?= $initiale ?></div> <!-- span qui va contenir le nom de l'user connecté  et div qui va contenir la 1ère lettre du nom de l'user connecté -->
        <span><?= htmlspecialchars($nomUser) ?></span>
    </div>
    <!-- <span class="deco"><a href="../logout.php">Déconnexion<i class="fa-solid fa-right-from-bracket icon-deco"></i></a></span> -->
</header>


<aside class="sidebar" id="sidebar">

    <i class="fa-solid fa-bars bur-ger" id="menu-btn2"></i>


    <div class="sidebar-logo">
        <span>VotreEspace✨</span>
    </div>
    
    <div class="sidebar-user">
        <div class="avatar-icon-sidebar"><?= $initiale ?></div>
        <span><?= htmlspecialchars($nomUser) ?></span>
        <p>Admin</p>
    </div>

    <nav class="sidebar-menu">
        <a href="../admin/dashboard.php" class="active">
            <i class="fa-solid fa-chart-line"></i>
            <span>Dashboard</span>
        </a>

        <a href="../Reservation/ajouter.php">
                <i class="fa-solid fa-plus"></i>
                <span>Nouvelle réservation</span>
            </a>

            <a href="#section">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Mes réservations</span>
            </a>

            <a href="../Reservation/ajouter.php#list-container">
                <i class="fa-solid fa-building"></i>
                <span>Salles</span>
            </a>

            <a href="../Salles/ajouter.php">
                <i class="fa-solid fa-toolbox"></i>
                <span>Gérer les salles</span>
            </a>

            <a href="../admin/scan_qr.php">
                <i class="fas fa-clipboard-check"></i>
                <span>Valider une réservation</span>
            </a>
    </nav>

    <div class="sidebar-logout">
        <a href="../logout.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Déconnexion</span>
        </a>
</aside>

<main class="container">

    <h1>Dashboard</h1>
    <p class="subtitle">Réservez vos salles en toute simplicité.</p>
    
<?php if (!empty($_GET['message'])): ?>
    <?php 
        $isError = str_contains($_GET['message'], '❌'); // si le message contient ❌ → erreur
        $msgClass = $isError ? 'error' : 'success';
    ?>
    <p class="message <?= $msgClass ?>">
        <?= htmlspecialchars($_GET['message']) ?>
    </p>
<?php endif; ?>


    <a href="../Reservation/ajouter.php">
        <button class="btn-primary">
            <i class="fa-solid fa-circle-plus btn-plus"></i> Ajouter une réservation
        </button>
    </a>

    <div class="stats">
        <div class="card">
            <div class="png"></div>
            <i class="fa-solid fa-calendar-check"></i>
            <h2><?= $nbReservations ?></h2> <!--remplacé par le nombre de réservations de l'user-->
            <span>Votre nombres de reservations</span>
        </div>

        <div class="card">
            <div class="png1"></div>
            <i class="fa-solid fa-door-open"></i>
            <h2><?= $nbSalles ?></h2>  <!--remplacé par le nombre de salles dans la Bd-->
            <span>Notre nombres salle libres</span>
        </div>
    </div>

    
<div class="section" id="section">
    <h3>Mes réservations</h3>

    <?php if (empty($reservations)): ?>
        <p class="reservation-vide">Aucune réservation pour le moment.🥲</p>
    <?php else: ?>

    <table class="table-reservations">
        <thead>
            <tr>
                <th>Salle</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Statut</th>
                <th>Code Qr d'accès</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $res): ?>
                <tr>
                    <td><?= htmlspecialchars($res['salle_nom']) ?></td>
                    <td><?= htmlspecialchars($res['date_reservation']) ?></td>
                    <td>
                        <?= htmlspecialchars($res['heure_debut']) ?>
                        -
                        <?= htmlspecialchars($res['heure_fin']) ?>
                    </td>
                    <td>
                        <?php

                            $heureDebut = new DateTime($res['heure_debut']);
                            $heureFin   = new DateTime($res['heure_fin']);
                            $maintenant = new DateTime($currentTime);

                            /* PASSÉE */
                            if (
                                $res['date_reservation'] < $currentDate
                                || ($res['date_reservation'] == $currentDate && $res['heure_fin'] < $currentTime)
                            ) {
                                echo '<span class="status-past">Passée</span>';

                            /* EN COURS */
                            } elseif (
                                $res['date_reservation'] == $currentDate
                                && $res['heure_debut'] <= $currentTime
                                && $res['heure_fin'] >= $currentTime
                            ) {
                                echo '<span class="status-today">En cours</span>';

                            /* À VENIR AUJOURD’HUI */
                            } elseif ($res['date_reservation'] == $currentDate && $res['heure_debut'] > $currentTime) {
                                
                                $interval = $maintenant->diff($heureDebut);
                                echo '<span class="status-today">Aujourd\'hui dans '
                                . $interval->format('%Hh %Im')
                                . '</span>';
                                
                                /* À VENIR */
                                } else {
                                    echo '<span class="status-upcoming">À venir</span>';
                                    }
                                    ?>
                    </td>
                    <td>
                        <button class="qr_code_btn" data-token="<?= $res['qr_token'] ?>"><i class="fa-solid fa-qrcode"></i></button>
                    </td>
                    <td>
                        <form action="../Reservation/annuler.php" method="POST" onsubmit="return confirm('Annuler cette réservation ?');">
                            <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                            <button type="submit" class="btn-cancel">
                                Annuler
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>
</div>

<div id="qrModal" class="qr-modal">
    <div class="qr-modal-content">
        <span class="qr-close">&times;</span>
        <h3>QR Code de la réservation</h3>
        <p>Ce code Qr vous sera demandé pour accéder à la salle <br> en plus d'une pièce d'identité</p>
        <img id="qrImage" src="" alt="QR code">
    </div>
</div>

</section>

</main>
<script src="../js/userIcon.js"></script>
</body>
</html>