<?php
session_start();
require_once '../config.php';

// Protection : accès interdit si non connecté
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true) {
    header("Location: ../index.php");
    exit;
}
// Récupération des catégories pour le filtre
$req = $pdo_init->query("SELECT DISTINCT categorie FROM salles ORDER BY categorie");
$categories = $req->fetchAll();



// Requête de base
$sql = "SELECT * FROM salles";
$params = [];

// Traitement du filtre
if (isset($_GET['categorie']) && $_GET['categorie'] !== '') {
    $sql .= " WHERE categorie = ?";
    $params[] = $_GET['categorie'];
}

// Exécution
$stmt = $pdo_init->prepare($sql);
$stmt->execute($params);
$salles = $stmt->fetchAll();

// Récupération du nom
$nomUser = $_SESSION['nom'];

$roleUser = $_SESSION['role'] ?? 'user'; // sécurité
$roleAffichage = ucfirst($roleUser); // User ou Admin

$dashboardLink = ($roleUser === 'admin')
    ? "../admin/dashboard.php"
    : "../user/dashboard.php";

$nomUser = strtoupper($nomUser); // tout en majuscule

// Première lettre du nom (majuscule)
$initiale = strtoupper(substr($nomUser, 0, 1));

// Requête de base (filtrée ou non)
$sql = "SELECT id, nom, capacite, categorie, description FROM salles";
$params = [];

if (!empty($_GET['categorie'])) {
    $sql .= " WHERE categorie = ?";
    $params[] = $_GET['categorie'];
}

$sql .= " ORDER BY nom";

$stmt = $pdo_init->prepare($sql);
$stmt->execute($params);
$salles = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link rel="stylesheet" href="../style/style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
       <span><?= htmlspecialchars($nomUser) ?></span><div class="avatar-icon"><?= $initiale ?></div> <!-- span qui va contenir le nom de l'user connecté  et div qui va contenir la 1ère lettre du nom de l'user connecté -->
    </div>
</header>

<aside class="sidebar" id="sidebar">

    <i class="fa-solid fa-bars bur-ger" id="menu-btn2"></i>


    <div class="sidebar-logo">
        <span>VotreEspace✨</span>
    </div>
    
    <div class="sidebar-user">
        <div class="avatar-icon-sidebar" id="avatar-icon-sidebar2"><?= $initiale ?></div>
        <span><?= htmlspecialchars($nomUser) ?></span>
        <p><?= htmlspecialchars($roleAffichage) ?></p>

    </div>

<?php
$dashboardLink = ($roleUser === 'admin')
    ? '../admin/dashboard.php'
    : '../user/dashboard.php';

$reservationsLink = ($roleUser === 'admin')
    ? '../admin/dashboard.php#section'
    : '../user/dashboard.php#section';
?>

<nav class="sidebar-menu">
    <a href="<?= $dashboardLink ?>">
        <i class="fa-solid fa-chart-line"></i>
        <span>Dashboard</span>
    </a>

    <a href="../Reservation/ajouter.php" class="active">
        <i class="fa-solid fa-plus"></i>
        <span>Nouvelle réservation</span>
    </a>

    <a href="<?= $reservationsLink ?>">
        <i class="fa-solid fa-calendar-days"></i>
        <span>Mes réservations</span>
    </a>

    <a href="#list-container">
        <i class="fa-solid fa-building"></i>
        <span>Salles</span>
    </a>

    <?php if ($roleUser === 'admin'): ?>
        <a href="../Salles/ajouter.php">
            <i class="fa-solid fa-toolbox"></i>
            <span>Gérer les salles</span>
        </a>
    <?php endif; ?>

    <?php if ($roleUser === 'admin'): ?>
        <a href="../admin/scan_qr.php">
            <i class="fas fa-clipboard-check"></i>
            <span>Valider une réservation</span>
        </a>
    <?php endif; ?>
</nav>


    <div class="sidebar-logout">
        <a href="../logout.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Déconnexion</span>
        </a>
    </aside>
    
    <div class="container">
        <div class="img"></div>
        <h1>VOTRE SALLE VOUS ATTENDS</h1>
    </div>
    <!--formulaire tri par categorie-->
    
    <div class="list-container" id="list-container">
    <form action="" method="GET">
        <!-- <label for="categorie">Filtre par categories</label> -->
        <select name="categorie">
            <option value="">Toutes les catégories</option>
    
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['categorie']) ?>"
                    <?= (isset($_GET['categorie']) && $_GET['categorie'] == $cat['categorie']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['categorie']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    
        <button type="submit">Filtrer</button>
    </form>

        <div class="list">
            <h2>Liste des salles disponibles</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom de la salle</th>
                        <th>Capacité</th>
                        <th>Catégorie</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($salles as $salle): ?>
                <tr>
                <td><?= htmlspecialchars($salle['id']) ?></td>
                <td><?= htmlspecialchars($salle['nom']) ?></td>
                <td><?= htmlspecialchars($salle['capacite']) ?></td>
                <td><?= htmlspecialchars($salle['categorie']) ?></td>
                <td>
                    <?=
                        strlen($salle['description']) > 30
                            ? substr(htmlspecialchars($salle['description']), 0, 30) . '...'
                            : htmlspecialchars($salle['description']);
                    ?>
                        <button class="btn-view" data-description="<?= htmlspecialchars($salle['description']) ?>">Voir plus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <div id="modalDescription" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h3>Description de la salle</h3>
                        <p id="modalText"></p>
                        </div>
                </div>

            <button class="btn-reserver" id="btnReserver">Réserver une salle</button>
            </tbody>
            </table>
        </div>
        
<div class="form-reservation" id="formReservation">
    <form action="<?= $dashboardLink ?>" method="POST">
        <h2>Réserver une salle</h2>
        <label for="salle">Choisir une salle :</label>
        <select name="salle" id="salle">
            <?php foreach($salles as $salle): ?>
                <option value="<?= htmlspecialchars($salle['id']) ?>"><?= htmlspecialchars(strtoupper($salle['nom'])) ?> (Capacité: <?= htmlspecialchars($salle['capacite']) ?>)</option>
                <?php endforeach; ?>
            </select><br>
            
        <label for="date">Date de la réservation :</label>
        <input type="date" id="date" name="date" required><br>
        
        <label for="heure_debut">Heure de début :</label>
        <input type="time" id="heure_debut" name="heure_debut" required><br>
        
        <label for="heure_fin">Heure de fin :</label>
        <input type="time" id="heure_fin" name="heure_fin" required><br>
        
        <button type="submit" class="btn-submit" >Confirmer la réservation</button>
        <button type="button" class="btn-cancel" id="btnCancel">Annuler</button>
        
    </form>
</div>

<script src="../js/formAppear.js"></script>
</body>
</html>