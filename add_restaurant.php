<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Charger le fichier XML existant
    $file = 'resto.xml';
    $restaurants = simplexml_load_file($file);

    if ($restaurants === false) {
        die('Erreur lors du chargement des données.');
    }

    // Ajouter un nouveau restaurant
    $restaurant = $restaurants->addChild('restaurant');

    // Ajouter les coordonnées du restaurant
    $coordonnees = $restaurant->addChild('coordonnees');
    $coordonnees->addChild('nom', htmlspecialchars($_POST['nom']));
    $coordonnees->addChild('adresse', htmlspecialchars($_POST['adresse']));
    $coordonnees->addChild('restaurateur', htmlspecialchars($_POST['restaurateur']));

    // Ajouter la description du restaurant
    $description = $restaurant->addChild('description');
    $description->addChild('paragraphe', htmlspecialchars($_POST['description1']));
    $description->addChild('paragraphe')->addChild('important', htmlspecialchars($_POST['description2']));

    // Ajouter la carte des plats
    $carte = $restaurant->addChild('carte');
    foreach ($_POST['partie'] as $key => $value) {
        $plat = $carte->addChild('plat');
        $plat->addChild('partie', htmlspecialchars($value));
        $plat->addChild('prix', htmlspecialchars($_POST['prix'][$key]));
        $plat->addChild('description')->addChild('paragraphe', htmlspecialchars($_POST['description3'][$key]));
    }

    // Ajouter les menus
    $menus = $restaurant->addChild('menus');
    $menu = $menus->addChild('menu');
    $menu->addChild('titre', htmlspecialchars($_POST['titre_menu']));
    $menu->addChild('description')->addChild('paragraphe', htmlspecialchars($_POST['description_menu']));
    $menu->addChild('prix', htmlspecialchars($_POST['prix_menu']));
    $elements = $menu->addChild('elements');
    foreach ($_POST['partie_menu'] as $key => $value) {
        $plat = $elements->addChild('plat');
        $plat->addChild('partie', htmlspecialchars($value));
        $plat->addChild('prix', htmlspecialchars($_POST['prix_menu'][$key]));
        $plat->addChild('description')->addChild('paragraphe', htmlspecialchars($_POST['description_menu'][$key]));
    }

    // Sauvegarder le fichier XML
    $restaurants->asXML($file);

    header('Location: admin.php'); // Redirection vers la page d'administration
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Restaurant</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Additional custom styles */
        body {
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5 mb-4">Ajouter un Restaurant</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse:</label>
                <input type="text" class="form-control" id="adresse" name="adresse" required>
            </div>
            <div class="form-group">
                <label for="restaurateur">Restaurateur:</label>
                <input type="text" class="form-control" id="restaurateur" name="restaurateur" required>
            </div>
            <div class="form-group">
                <label for="description1">Description (paragraphe 1):</label>
                <textarea class="form-control" id="description1" name="description1" required></textarea>
            </div>
            <div class="form-group">
                <label for="description2">Description (paragraphe 2 avec important):</label>
                <textarea class="form-control" id="description2" name="description2" required></textarea>
            </div>
            <div class="form-group" id="carte">
                <label>Carte des plats:</label>
                <button type="button" class="btn btn-primary mb-3" onclick="ajouterPlat()">Ajouter un plat</button>
            </div>
            <div class="form-group">
                <label for="titre_menu">Titre du Menu:</label>
                <input type="text" class="form-control" id="titre_menu" name="titre_menu" required>
            </div>
            <div class="form-group">
                <label for="description_menu">Description du Menu:</label>
                <textarea class="form-control" id="description_menu" name="description_menu" required></textarea>
            </div>
            <div class="form-group">
                <label for="prix_menu">Prix du Menu:</label>
                <input type="text" class="form-control" id="prix_menu" name="prix_menu" required>
            </div>
            <div class="form-group" id="menu">
                <label>Éléments du Menu:</label>
                <button type="button" class="btn btn-primary mb-3" onclick="ajouterPlatMenu()">Ajouter un plat au menu</button>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter Restaurant</button>
        </form>
        <a class="btn btn-outline-secondary mt-3" href="admin.php">Retour</a>
    </div>

    <script>
        // Fonction pour ajouter un nouveau plat à la carte
        function ajouterPlat() {
            var html = '<div class="plat">';
            html += '<div class="form-group">';
            html += '<label>Partie:</label>';
            html += '<input type="text" class="form-control" name="partie[]" required>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<label>Prix:</label>';
            html += '<input type="text" class="form-control" name="prix[]" required>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<label>Description:</label>';
            html += '<textarea class="form-control" name="description3[]" required></textarea>';
            html += '</div>';
            html += '</div>';
            document.getElementById('carte').insertAdjacentHTML('beforeend', html);
        }

        // Fonction pour ajouter un nouveau plat au menu
        function ajouterPlatMenu() {
            var html = '<div class="plat_menu">';
            html += '<div class="form-group">';
            html += '<label>Partie:</label>';
            html += '<input type="text" class="form-control" name="partie_menu[]" required>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<label>Prix:</label>';
            html += '<input type="text" class="form-control" name="prix_menu[]" required>';
            html += '</div>';
            html += '<div class="form-group">';
            html += '<label>Description:</label>';
            html += '<textarea class="form-control" name="description_menu[]" required></textarea>';
            html += '</div>';
            html += '</div>';
            document.getElementById('menu').insertAdjacentHTML('beforeend', html);
        }
    </script>
</body>
</html>
