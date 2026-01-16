<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="./style/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Playwrite+US+Trad+Guides&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Playwrite+US+Trad+Guides&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap');
    </style>
</head>
<body>

<?php
require_once "config.php";

if (isset($_POST['ok'])) {

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $mdp = $_POST['pass'];

    // 🔐 HASH DU MOT DE PASSE
    $hash = password_hash($mdp, PASSWORD_DEFAULT);

    $requete = $pdo_init->prepare(
        "INSERT INTO users (nom, prenom, password)
         VALUES (:nom, :prenom, :pass)"
    );

    $requete->execute([
        ":nom"    => $nom,
        ":prenom" => $prenom,
        ":pass"   => $hash   // 👈 ON STOCKE LE HASH
    ]);

    header("Location: index.php");
    exit;
}
?>

<div class="overlay">

        <img src="./images/logo.jpg" alt="">
    
    <section class="container forms"> 
       
        <div class="form sign">
            <div class="cercle">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div class="form-signup"> 
                <header>YOUPI!</header>
                <p class="phtext">Un nouveau venu, bienvenue à vous</p>
                
                <form action="" method="POST">
                    <div class="champ">
                        <input type="text" placeholder="Nom" class="input" name="nom" required>
                    </div>
                    
                    <div class="champ">
                        <input type="text" placeholder="Prénom" class="input" name="prenom" required>
                    </div>
                    
                    <div class="champ">
                        <input type="password" placeholder="Mot de passe" class="pass" name="pass" required>
                        <i class="fa-solid fa-eye-slash eye-icon"></i>
                    </div>
                    
                    <div class="btn">
                        <input type="submit" value="S'inscrire" name="ok">
                    </div>

                    <div class="register">
                        <span>Déjà un compte ?</span><br>
                        <a href="index.php">SE CONNECTER</a>
                    </div>  
                </form>
            </div>
            
            
        </div>        
        
        
    </section>
</div>
<script src="/js/script.js"></script>
</body>
</html>