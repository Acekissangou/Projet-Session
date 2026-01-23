<?php
session_start();
require_once '../config.php';

// Protection : accès interdit si non connecté
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true) {
    header("Location: ../index.php");
    exit;
}

// Variables pour l'affichage des stats
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
$now = new DateTime(); // ✅ correction : date + heure complètes

// Récupération de l'id de l'user connecté
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
$nomUser = strtoupper($nomUser);
$initiale = strtoupper(substr($nomUser, 0, 1));

// Réception des données de la réservation
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id     = $_SESSION['user_id'];
    $salle_id    = $_POST['salle'];
    $date        = $_POST['date'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin   = $_POST['heure_fin'];

    // DateTime complets
    $debutReservation = new DateTime("$date $heure_debut"); // ✅ correction
    $finReservation   = new DateTime("$date $heure_fin");   // ✅ correction

    // Durée de réservation en minutes
    $interval = $debutReservation->diff($finReservation);
    $duree_minute = ($interval->h * 60) + $interval->i;

    // Récupération des règles de la salle
    $req = $pdo_init->prepare("
        SELECT 
            heure_minimale,
            heure_max,
            delai_reservation,
            heure_limite
        FROM salles
        WHERE id = ?
    ");
    $req->execute([$salle_id]);
    $salle = $req->fetch(PDO::FETCH_ASSOC);

    $heure_minimale    = (int) $salle['heure_minimale'];   // en minutes
    $heure_max         = $salle['heure_max'];              // TIME
    $delai_reservation = (int) $salle['delai_reservation']; // en heures
    $heure_limite      = (int) $salle['heure_limite'];      // en heures

    // Conversion heure max en DateTime
    $heureMaxObj = new DateTime("$date $heure_max");

    // Calcul délai avant réservation (en minutes)
    $diffMinutes = ($debutReservation->getTimestamp() - $now->getTimestamp()) / 60;

    /*  VALIDATIONS  */

    // 1️⃣ Heure de fin après heure de début
    if ($finReservation <= $debutReservation) {
        $message = "❌ L'heure de fin doit être après l'heure de début.";

    // 2️⃣ Date pas dans le passé
    } elseif ($date < $currentDate) {
        $message = "❌ La date de réservation ne peut pas être dans le passé.";

    // 3️⃣ Durée minimale
    } elseif ($duree_minute < $heure_minimale) {

        $h = intdiv($heure_minimale, 60);
        $m = $heure_minimale % 60;

        if ($h > 0 && $m > 0) {
            $message = "❌ Cette salle nécessite une réservation minimale de {$h}h {$m}min.";
        } elseif ($h > 0) {
            $message = "❌ Cette salle nécessite une réservation minimale de {$h}h.";
        } else {
            $message = "❌ Cette salle nécessite une réservation minimale de {$m} minutes.";
        }

    // 4️⃣ Délai minimum avant réservation
    } elseif ($diffMinutes < ($delai_reservation * 60)) {
        $message = "❌ Votre réservation doit être faite au minimum {$delai_reservation}h avant l'heure de début.";

    // 5️⃣ Heure de fermeture
    } elseif ($finReservation > $heureMaxObj) {
        $message = "❌ La fermeture de la salle est prévue pour $heure_max.";

    // 6️⃣ Durée maximale autorisée
    } elseif ($duree_minute > ($heure_limite)) {
        $heure_limite_enHeure = $heure_limite / 60;
        $message = "❌ Cette salle ne peut pas être réservée pour plus de {$heure_limite_enHeure}h.";

    } else {

        // 7️⃣ Vérification chevauchement
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

        $qr_token = bin2hex(random_bytes(16));

        if ($check->rowCount() > 0) {
            $message = "❌ Cette salle est déjà réservée sur ce créneau.";

        } else {

            // Insertion réservation
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

            // Génération QR code
            require_once '../lib/phpqrcode/qrlib.php';
            $qrPath = '../qrCodes/' . $qr_token . '.png';
            QRcode::png($qr_token, $qrPath, QR_ECLEVEL_H, 5);
        }
    }

    header("Location: dashboard.php?message=" . urlencode($message));
    exit;
}

// Récupération des réservations utilisateur
$reqReservations = $pdo_init->prepare("
    SELECT 
        r.id,
        s.nom AS salle_nom,
        r.date_reservation,
        r.heure_debut,
        r.heure_fin,
        r.qr_token,
        r.qr_used
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        
        <div id="overlay"></div>
        
        <main class="container">
            
            <h1>Dashboard</h1>
    <p class="subtitle">Réservez vos salles en toute simplicité.</p>
    
<?php if (!empty($_GET['message'])): ?>
<script>
Swal.fire({
    icon: <?= str_contains($_GET['message'], '❌') ? "'error'" : "'success'" ?>,
    title: <?= str_contains($_GET['message'], '❌') ? "'Erreur'" : "'Succès'" ?>,
    text: <?= json_encode($_GET['message']) ?>,
    confirmButtonText: 'OK'
});
</script>
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
                        <?php if($res['date_reservation'] < $currentDate || ($res['date_reservation'] == $currentDate && $res['heure_fin'] < $currentTime)):?>
                        <p>Qr code éffacé</p>   
                        <?php elseif($res['qr_used'] === 0):?>
                        <button class="qr_code_btn" data-token="<?= $res['qr_token'] ?>"><i class="fa-solid fa-qrcode"></i></button>
                        <?php else:?>
                        <p>Qr code déjà utilisé</p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($res['date_reservation'] > $currentDate || $res['date_reservation'] == $currentDate && $res['heure_debut'] > $currentTime):?>
                        <form action="../Reservation/annuler.php" method="POST" onsubmit="return confirm('Annuler cette réservation ?');">
                            <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                            <button type="submit" class="btn-cancel">
                                Annuler
                            </button>
                        </form>
                        <?php elseif($res['date_reservation'] == $currentDate && $res['heure_debut'] <= $currentTime && $res['heure_fin'] >= $currentTime):?>
                        <form onsubmit="return alert('Vous ne pouvez pas annuler une réservation en cours');">
                            <button type="submit" class="btn-cancel">
                                Annuler
                            </button>
                        </form>
                        <?php else:?>
                            <form action="../Reservation/annuler.php" method="POST" onsubmit="return confirm('Effacer cette réservation ?');">
                            <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                            <button type="submit" class="btn-cancel">
                                Effacer
                            </button>
                        </form> 
                        <?php endif; ?>
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