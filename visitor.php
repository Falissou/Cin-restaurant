<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'visitor') {
    header('Location: index.php');
    exit();
}

function loadFilms() {
    return simplexml_load_file('cinema.xml');
}

function loadRestaurants() {
    return simplexml_load_file('resto.xml');
}

$films = loadFilms();
$restaurants = loadRestaurants();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Visiteur - Affichage des Données</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5 mb-4">Bienvenue, <?= $_SESSION['username'] ?> (Visiteur)</h2>

        <h3>Liste des Films</h3>
        <ul class="list-group mb-4">
            <?php foreach ($films->film as $film): ?>
                <li class="list-group-item">
                    <h5><?= $film->titre ?> (<?= $film->annee ?>) - <?= $film->genre ?> - Réalisateur: <?= $film->realisateur ?></h5>
                    <ul>
                        <li>Durée: <?= $film->duree ?></li>
                        <li>Langue: <?= $film->langue ?></li>
                        <li>Description: <?= $film->description ?></li>
                        <li>Acteurs: <?= implode(', ', iterator_to_array($film->acteurs->acteur)) ?></li>
                        <li>Horaires: <?= implode(' | ', iterator_to_array($film->horaires->horaire)) ?></li>
                        <?php if (!empty($film->note_presse)): ?>
                            <li>Note Presse: <?= $film->note_presse ?></li>
                        <?php endif; ?>
                        <?php if (!empty($film->note_spectateurs)): ?>
                            <li>Note Spectateurs: <?= $film->note_spectateurs ?></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>

        <h3>Liste des Restaurants</h3>
        <ul class="list-group mb-4">
            <?php foreach ($restaurants->restaurant as $restaurant): ?>
                <li class="list-group-item">
                    <h5><?= $restaurant->nom ?> - <?= $restaurant->adresse ?></h5>
                    <ul>
                        <li>Restaurateur: <?= $restaurant->nom_restaurateur ?></li>
                        <li>Description: <?= $restaurant->description ?></li>
                        <li>Carte:
                            <ul>
                                <?php foreach ($restaurant->carte->plat as $plat): ?>
                                    <li><?= $plat->nom ?> (<?= $plat->partie ?>) - <?= $plat->prix ?> <?= $plat->prix['devise'] ?>: <?= $plat->description ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li>Menus:
                            <ul>
                                <?php foreach ($restaurant->menus->menu as $menu): ?>
                                    <li><?= $menu->titre ?> - <?= $menu->prix ?> <?= $menu->prix['devise'] ?>: <?= $menu->description ?>
                                        <ul>
                                            <?php foreach ($menu->elements->element as $element): ?>
                                                <li><?= $element ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>

        <a class="btn btn-danger" href="logout.php">Se déconnecter</a>
    </div>

    <!-- Bootstrap JS and dependencies (optional, if required for your site) -->
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
</body>
</html>
