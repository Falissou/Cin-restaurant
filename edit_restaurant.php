<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Récupérer l'identifiant du restaurant à éditer
if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit();
}
$id = (int)$_GET['id'];

// Charger le fichier XML existant
$file = 'resto.xml';
$restaurants = simplexml_load_file($file);

if ($restaurants === false) {
    die('Erreur lors du chargement des données.');
}

// Trouver le restaurant à éditer
$restaurant = $restaurants->restaurant[$id];

if (!$restaurant) {
    die('Restaurant non trouvé.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mettre à jour les données du restaurant
    $restaurant->coordonnees->nom = htmlspecialchars($_POST['nom']);
    $restaurant->coordonnees->adresse = htmlspecialchars($_POST['adresse']);
    $restaurant->coordonnees->restaurateur = htmlspecialchars($_POST['restaurateur']);
    $restaurant->description->paragraphe = htmlspecialchars($_POST['description1']);

    // Mettre à jour la carte des plats
    unset($restaurant->carte->plat); // Supprimer les plats existants
    foreach ($_POST['partie'] as $key => $value) {
        $plat = $restaurant->carte->addChild('plat');
        $plat->partie = htmlspecialchars($value);
        $plat->prix = htmlspecialchars($_POST['prix'][$key]);
        $plat->description->paragraphe = htmlspecialchars($_POST['description3'][$key]);
    }

    // Mettre à jour les menus
    unset($restaurant->menus->menu); // Supprimer les menus existants
    $menu = $restaurant->menus->addChild('menu');
    $menu->titre = htmlspecialchars($_POST['titre_menu']);
    $menu->description->paragraphe = htmlspecialchars($_POST['description_menu']);
    $menu->prix = htmlspecialchars($_POST['prix_menu']);
    foreach ($_POST['partie_menu'] as $key => $value) {
        $plat = $menu->elements->addChild('plat');
        $plat->partie = htmlspecialchars($value);
        $plat->prix = htmlspecialchars($_POST['prix_menu'][$key]);
        $plat->description->paragraphe = htmlspecialchars($_POST['description_menu'][$key]);
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
    <title>Modifier un Restaurant</title>
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
        <h2 class="mt-5 mb-4">Modifier un Restaurant</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $restaurant->coordonnees->nom; ?>" required>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse:</label>
                <input type="text" class="form-control" id="adresse" name="adresse" value="<?php echo $restaurant->coordonnees->adresse; ?>" required>
            </div>
            <div class="form-group">
                <label for="restaurateur">Restaurateur:</label>
                <input type="text" class="form-control" id="restaurateur" name="restaurateur" value="<?php echo $restaurant->coordonnees->restaurateur; ?>" required>
            </div>
            <div class="form-group">
                <label for="description1">Description (paragraphe 1):</label>
                <textarea class="form-control" id="description1" name="description1" required><?php echo $restaurant->description->paragraphe; ?></textarea>
            </div>
            <div class="form-group">
                <label for="description2">Description (paragraphe 2 avec important):</label>
                <textarea class="form-control" id="description2" name="description2" required><?php echo $restaurant->description->paragraphe->important; ?></textarea>
            </div>
            <div class="form-group" id="carte">
                <label>Carte des plats:</label>
                <?php foreach ($restaurant->carte->plat as $plat): ?>
                    <div class="plat">
                        <div class="form-group">
                            <label>Partie:</label>
                            <input type="text" class="form-control" name="partie[]" value="<?php echo $plat->partie; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Prix:</label>
                            <input type="text" class="form-control" name="prix[]" value="<?php echo $plat->prix; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Description:</label>
                            <textarea class="form-control" name="description3[]" required><?php echo $plat->description->paragraphe; ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button type="button" class="btn btn-primary mb-3" onclick="ajouterPlat()">Ajouter un plat</button>
            </div>
            <div class="form-group">
                <label for="titre_menu">Titre du Menu:</label>
                <input type="text" class="form-control" id="titre_menu" name="titre_menu" value="<?php echo $restaurant->menus->menu->titre; ?>" required>
            </div>
            <div class="form-group">
                <label for="description_menu">Description du Menu:</label>
                <textarea class="form-control" id="description_menu" name="description_menu" required><?php echo $restaurant->menus->menu->description->paragraphe; ?></textarea>
            </div>
            <div class="form-group">
                <label for="prix_menu">Prix du Menu:</label>
                <input type="text" class="form-control" id="prix_menu" name="prix_menu" value="<?php echo $restaurant->menus->menu->prix; ?>" required>
            </div>
            <div class="form-group" id="menu">
                <label>Éléments du Menu:</label>
                <?php foreach ($restaurant->menus->menu->elements->plat as $plat): ?>
                    <div class="plat_menu">
                        <div class="form-group">
                            <label>Partie:</label>
                            <input type="text" class="form-control" name="partie_menu[]" value="<?php echo $plat->partie; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Prix:</label>
                            <input type="text" class="form-control" name="prix_menu[]" value="<?php echo $plat->prix; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Description:</label>
                            <textarea class="form-control" name="description_menu[]" required><?php echo $plat->description->paragraphe; ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button type="button" class="btn btn-primary mb-3" onclick="ajouterPlatMenu()">Ajouter un plat au menu</button>
            </div>
            <button type="submit" class="btn btn-primary">Modifier Restaurant</button>
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
