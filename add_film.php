<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valider et enregistrer les données du formulaire dans cinema.xml
    $file = 'cinema.xml';
    $films = simplexml_load_file($file);

    if ($films === false) {
        die('Erreur lors du chargement des données.');
    }

    // Création d'un nouveau film
    $film = $films->addChild('film');
    $film->addChild('titre', htmlspecialchars($_POST['titre']));
    $film->addChild('duree', htmlspecialchars($_POST['duree']));
    $film->addChild('genre', htmlspecialchars($_POST['genre']));
    $film->addChild('realisateur', htmlspecialchars($_POST['realisateur']));
    $film->addChild('langue', htmlspecialchars($_POST['langue']));
    
    // Gestion des acteurs
    $acteurs = $film->addChild('acteurs');
    $acteurList = explode(',', $_POST['acteurs']);
    foreach ($acteurList as $acteur) {
        $acteurs->addChild('acteur', trim(htmlspecialchars($acteur)));
    }

    $film->addChild('annee', htmlspecialchars($_POST['annee']));
    $notes = $film->addChild('notes');
    $notes->addChild('presse', htmlspecialchars($_POST['note_presse']));
    $notes->addChild('spectateurs', htmlspecialchars($_POST['note_spectateurs']));
    $film->addChild('description', htmlspecialchars($_POST['description']));
    
    // Gestion des horaires
    $horaires = $film->addChild('horaires');
    $horairesList = explode('|', $_POST['horaires']);
    foreach ($horairesList as $horaire) {
        $parts = explode(':', $horaire);
        $horaireNode = $horaires->addChild('horaire');
        $horaireNode->addChild('jour', trim(htmlspecialchars($parts[0])));
        $horaireNode->addChild('seances', trim(htmlspecialchars($parts[1])));
    }

    // Sauvegarde du fichier XML
    $films->asXML($file);
    header('Location: admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Film</title>
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
        <h2 class="mt-5 mb-4">Ajouter un Film</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="titre">Titre:</label>
                <input type="text" class="form-control" id="titre" name="titre" required>
            </div>
            <div class="form-group">
                <label for="duree">Durée:</label>
                <input type="text" class="form-control" id="duree" name="duree" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre:</label>
                <input type="text" class="form-control" id="genre" name="genre" required>
            </div>
            <div class="form-group">
                <label for="realisateur">Réalisateur:</label>
                <input type="text" class="form-control" id="realisateur" name="realisateur" required>
            </div>
            <div class="form-group">
                <label for="langue">Langue:</label>
                <input type="text" class="form-control" id="langue" name="langue" required>
            </div>
            <div class="form-group">
                <label for="acteurs">Acteurs (séparés par ','):</label>
                <input type="text" class="form-control" id="acteurs" name="acteurs" required>
            </div>
            <div class="form-group">
                <label for="annee">Année:</label>
                <input type="text" class="form-control" id="annee" name="annee" required>
            </div>
            <div class="form-group">
                <label for="note_presse">Note Presse:</label>
                <input type="text" class="form-control" id="note_presse" name="note_presse" required>
            </div>
            <div class="form-group">
                <label for="note_spectateurs">Note Spectateurs:</label>
                <input type="text" class="form-control" id="note_spectateurs" name="note_spectateurs" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="horaires">Horaires (format: jour:seances séparés par '|'):</label>
                <input type="text" class="form-control" id="horaires" name="horaires" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter Film</button>
        </form>
        <a class="btn btn-outline-secondary mt-3" href="admin.php">Retour</a>
    </div>
</body>
</html>
