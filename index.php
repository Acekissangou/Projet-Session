<?php
session_start();
require_once "config.php";

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = trim($_POST['nom']);
    $mdp = $_POST['pass'];

    // 1️⃣ Vérifier si l'utilisateur existe
    $req = $pdo_init->prepare(
        "SELECT * FROM users WHERE nom = ?"
    );
    $req->execute([$nom]);
    $user = $req->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        
        $error_msg = "Erreur 404 : vous êtes introuvable 😄";

    } else {
        // 2️⃣ Vérifier le mot de passe HASHÉ
        if (!password_verify($mdp, $user['password'])) {
        
            $error_msg = "Erreur 404 : vous êtes introuvable 😄";

        } else {
            // ✅ Connexion OK
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['nom']       = $user['nom'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['connected'] = true;

            // Redirection selon le rôle
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="./style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Playwrite+US+Trad+Guides&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap');
    </style>
</head>
<body>
    <div class="overlay">
        <img src="./images/logo.jpg" alt="">

        <section class="container forms"> 
            
            <div class="form sign">
                <div class="cercle">
                    <i class="fa-solid fa-user"></i>
            </div>
            <div class="form-signup"> 
                <header>BIENVENUE</header>
                <p class="phtext">Connectez vous en remplissant les champs</p>

                <?php if (!empty($error_msg)) { ?>
                <p class="error"><?php echo $error_msg; ?></p>
                <?php } ?>
                
                <form action="" method="POST">
                    <div class="champ">
                        <input type="text" placeholder="Nom" class="input" name="nom" required>
                        
                    </div>
                    
                    <div class="champ">
                        <input type="password" placeholder="Mot de passe" class="pass" name="pass" required>
                        <i class="fa-solid fa-eye-slash eye-icon"></i>
                    </div>
                    
                    <div class="btn">
                        <input type="submit" value="Se connecter">
                    </div>
                    
                    <div class="register">
                        <span>Pas de compte ?</span><br>
                        <a href="register.php">S'INSCRIRE</a>
                    </div>  
                </form>
            </div>
            
            
        </div>        
        
        
    </section>
</div>

<script src="./js/script.js"></script>

</body>
</html>
