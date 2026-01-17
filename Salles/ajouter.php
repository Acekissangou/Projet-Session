<?php
session_start();
require_once '../config.php';

// Sécurité
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true) {
    header("Location: ../index.php");
    exit;
}

// Vérification rôle admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}

// Récupération du nom
$nomUser = $_SESSION['nom'];

$nomUser = strtoupper($nomUser); // tout en majuscule

// Première lettre du nom (majuscule)
$initiale = strtoupper(substr($nomUser, 0, 1));

$roleUser = $_SESSION['role'] ?? 'user'; // sécurité
$roleAffichage = ucfirst($roleUser); // User ou Admin

$dashboardLink = ($roleUser === 'admin')
    ? "../admin/dashboard.php"
    : "../user/dashboard.php";

// Initialisation message
$message = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $capacite = intval($_POST['capacite']);
    $categorie = trim($_POST['categorie']);
    $descriptions = trim($_POST['descriptions'] ?? '');

    if (empty($nom) || $capacite <= 0 || empty($categorie)) {
        $message = "❌ Tous les champs sont obligatoires et la capacité doit être > 0.";
    } else {
        $insert = $pdo_init->prepare("INSERT INTO salles (nom, capacite, categorie, description) VALUES (?, ?, ?, ?)");
        $insert->execute([$nom, $capacite, $categorie, $descriptions]);
        $message = "✅ Salle ajoutée avec succès.";
    }
}

// Récupération des salles
$req = $pdo_init->query("SELECT * FROM salles ORDER BY id DESC");
$salles = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des salles - MeetSpace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../style/style4.css">
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

    <a href="../Reservation/ajouter.php">
        <i class="fa-solid fa-plus"></i>
        <span>Nouvelle réservation</span>
    </a>

    <a href="<?= $reservationsLink ?>">
        <i class="fa-solid fa-calendar-days"></i>
        <span>Mes réservations</span>
    </a>

    <a href="../Reservation/ajouter.php#list-container">
        <i class="fa-solid fa-building"></i>
        <span>Salles</span>
    </a>

    <?php if ($roleUser === 'admin'): ?>
        <a href="../Salles/ajouter.php" class="active">
            <i class="fa-solid fa-toolbox"></i>
            <span>Gérer les salles</span>
        </a>
    <?php endif; ?>

    <?php if ($roleUser === 'admin'): ?>
        <a href="../admin/scan_qr.php">
            <i class="fas fa-clipboard-check"></i>
            <span>Valider une reservation</span>
        </a>
    <?php endif; ?>

    
</nav>


    <div class="sidebar-logout">
        <a href="../logout.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Déconnexion</span>
        </a>
</aside>


<h1>Gestion des salles</h1>

<?php if (!empty($message)): ?>
    <p id="flash-message" class="message <?= strpos($message, '❌') === false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<button class="btn btn-add" onclick="toggleForm()">
    <i class="fa-solid fa-plus"></i> Ajouter une salle
</button>

<div class="form-add" id="formSalle">
    <form method="POST" action="">
        <input type="text" name="nom" placeholder="Nom de la salle" required>
        <input type="number" name="capacite" placeholder="Capacité" required min="1">
        <input type="text" name="categorie" placeholder="Catégorie de la salle" required>
        <input type="text" name="descriptions" placeholder="Descriptions" required>
        <button type="submit" class="btn btn-add">
            <i class="fa-solid fa-floppy-disk"></i> Enregistrer
        </button>
    </form>
</div>

<?php if (empty($salles)): ?>
    <p class="vide">😕 Pas de salle enregistrée</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Capacité</th>
                <th>Catégorie</th>
                <th>Descriptions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salles as $salle): ?>
                <tr>
                    <td><?= htmlspecialchars($salle['nom']) ?></td>
                    <td><?= htmlspecialchars($salle['capacite']) ?></td>
                    <td><?= htmlspecialchars($salle['categorie']) ?></td>
                    <td>
                        <?=
                        strlen($salle['description']) > 40
                            ? substr(htmlspecialchars($salle['description']), 0, 40) . '...'
                            : htmlspecialchars($salle['description']);
                        ?>
                        <button class="btn-view" data-description="<?= htmlspecialchars($salle['description']) ?>">Voir plus</button>
                    </td>
                    <td>
                        <a href="../Salles/modifier.php?id=<?= $salle['id'] ?>" class="btn btn-edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <a href="../Salles/supprimer.php?id=<?= $salle['id'] ?>" class="btn btn-del"
                           onclick="return confirm('Supprimer cette salle ?')">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<div id="modalDescription" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Description de la salle</h3>
        <p id="modalText"></p>
    </div>
</div>

<?php endif; ?>

<script>
const menuBtn = document.getElementById("menu-btn");
const menuBtn2 = document.getElementById("menu-btn2");
const sidebar = document.getElementById("sidebar");

// Sécurité : on vérifie l'existence
if (menuBtn && sidebar) {
    menuBtn.addEventListener("click", () => {
        sidebar.classList.toggle("closed");
    });
}

if (menuBtn2 && sidebar) {
    menuBtn2.addEventListener("click", () => {
        sidebar.classList.toggle("closed");
    });
}

function toggleForm() {
    const form = document.getElementById('formSalle');
    if (form) {
        form.classList.toggle('show');
    }
}

const flashMessage = document.getElementById("flash-message");

if (flashMessage) {
    setTimeout(() => {
        flashMessage.classList.add("hide");
    }, 1500);
}

// Modal description
    const modal = document.getElementById("modalDescription");
    const modalText = document.getElementById("modalText");
    const closeBtn = document.querySelector(".close");

    document.querySelectorAll(".btn-view").forEach(button => {
        button.addEventListener("click", () => {
            modalText.textContent = button.dataset.description;
            modal.style.display = "block";
        });
    });

    closeBtn.onclick = () => {
        modal.style.display = "none";
    };

    window.onclick = (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    };

</script>


</body>
</html>
