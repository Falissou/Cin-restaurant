<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

function loadFilms() {
    $file = 'cinema.xml';
    if (file_exists($file)) {
        return simplexml_load_file($file);
    }
    return false;
}

function loadRestaurants() {
    $file = 'resto.xml';
    if (file_exists($file)) {
        return simplexml_load_file($file);
    }
    return false;
}

$films = loadFilms();
$restaurants = loadRestaurants();

if ($films === false || $restaurants === false) {
    die("Erreur lors du chargement des données.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestion des Films et Restaurants</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Additional custom styles */
        body {
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .list-group-item a {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5 mb-4">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> (Admin)</h2>

        <div class="mb-4">
            <h3>Liste des Films</h3>
            <a class="btn btn-primary mb-3" href="add_film.php">Ajouter un Film</a>
            <ul class="list-group">
                <?php foreach ($films->film as $index => $film): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($film->titre) ?> (<?= htmlspecialchars($film->annee) ?>)
                        <div>
                            <a href="edit_film.php?id=<?= $index ?>" class="btn btn-sm btn-secondary">Modifier</a>
                            <a href="delete_film.php?id=<?= $index ?>" class="btn btn-sm btn-danger">Supprimer</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div>
            <h3>Liste des Restaurants</h3>
            <a class="btn btn-primary mb-3" href="add_restaurant.php">Ajouter un Restaurant</a>
            <ul class="list-group">
                <?php foreach ($restaurants->restaurant as $index => $restaurant): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($restaurant->nom) ?>
                        <div>
                            <a href="edit_restaurant.php?id=<?= $index ?>" class="btn btn-sm btn-secondary">Modifier</a>
                            <a href="delete_restaurant.php?id=<?= $index ?>" class="btn btn-sm btn-danger">Supprimer</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <a class="btn btn-outline-danger mt-4" href="logout.php">Se déconnecter</a>
    </div>
</body>
</html>
