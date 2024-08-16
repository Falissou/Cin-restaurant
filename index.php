<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Charger le fichier XML des utilisateurs
    $xml = simplexml_load_file('user.xml') or die("Erreur : Impossible de créer l'objet XML");

    // Vérifier les informations de connexion
    foreach ($xml->user as $user) {
        if ($user->username == $username && $user->password == $password) {
            $_SESSION['username'] = (string) $user->username;
            $_SESSION['role'] = (string) $user->role;

            if ($_SESSION['role'] == 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: visitor.php');
            }
            exit();
        }
    }

    $error = "Nom d'utilisateur ou mot de passe incorrect";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Additional custom styles */
        body {
            padding: 50px;
        }
        .form-container {
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="mt-5 mb-4">Connexion</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
            <?php if (isset($error)): ?>
                <p class="mt-3" style="color: red;"><?= $error ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
