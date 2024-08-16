<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$id = (int)$_GET['id'];
$file = 'cinema.xml';
$films = simplexml_load_file($file);

if ($films === false) {
    die('Erreur lors du chargement des données.');
}

// Vérifier si l'ID est valide
if (!isset($films->film[$id])) {
    die('Film non trouvé.');
}

$film = $films->film[$id];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valider et mettre à jour les données du film
    $film->titre = htmlspecialchars($_POST['titre']);
    $film->duree = htmlspecialchars($_POST['duree']);
    $film->genre = htmlspecialchars($_POST['genre']);
    $film->realisateur = htmlspecialchars($_POST['realisateur']);
    $film->langue = htmlspecialchars($_POST['langue']);
    
    // Gestion des acteurs
    unset($film->acteurs);
    $acteurs = $film->addChild('acteurs');
    $acteurList = explode(',', $_POST['acteurs']);
    foreach ($acteurList as $acteur) {
        $acteurs->addChild('acteur', trim(htmlspecialchars($acteur)));
    }

    $film->annee = htmlspecialchars($_POST['annee']);
    $notes = $film->notes;
    $notes->presse = htmlspecialchars($_POST['note_presse']);
    $notes->spectateurs = htmlspecialchars($_POST['note_spectateurs']);
    $film->description = htmlspecialchars($_POST['description']);
    
    // Gestion des horaires
    unset($film->horaires);
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
    <title>Modifier le Film</title>
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
        <h2 class="mt-5 mb-4">Modifier le Film</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="titre">Titre:</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?= htmlspecialchars($film->titre) ?>" required>
            </div>
            <div class="form-group">
                <label for="duree">Durée:</label>
                <input type="text" class="form-control" id="duree" name="duree" value="<?= htmlspecialchars($film->duree) ?>" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre:</label>
                <input type="text" class="form-control" id="genre" name="genre" value="<?= htmlspecialchars($film->genre) ?>" required>
            </div>
            <div class="form-group">
                <label for="realisateur">Réalisateur:</label>
                <input type="text" class="form-control" id="realisateur" name="realisateur" value="<?= htmlspecialchars($film->realisateur) ?>" required>
            </div>
            <div class="form-group">
                <label for="langue">Langue:</label>
                <input type="text" class="form-control" id="langue" name="langue" value="<?= htmlspecialchars($film->langue) ?>" required>
            </div>
            <div class="form-group">
                <label for="acteurs">Acteurs (séparés par):</label>
                <input type="text" class="form-control" id="acteurs" name="acteurs" value="<?= htmlspecialchars($film->acteurs->acteur) ?>" required>
            </div>
            <div class="form-group">
                <label for="annee">Année:</label>
                <input type="text" class="form-control" id="annee" name="annee" value="<?= htmlspecialchars($film->annee) ?>" required>
            </div>
            <div class="form-group">
                <label for="note_presse">Note Presse:</label>
                <input type="text" class="form-control" id="note_presse" name="note_presse" value="<?= htmlspecialchars($film->notes->presse) ?>" required>
            </div>
            <div class="form-group">
                <label for="note_spectateurs">Note Spectateurs:</label>
                <input type="text" class="form-control" id="note_spectateurs" name="note_spectateurs" value="<?= htmlspecialchars($film->notes->spectateurs) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($film->description) ?></textarea>
            </div>
            <div class="form-group">
                <label for="horaires">Horaires (format: jour:seances séparés par ):</label>
                <input type="text" class="form-control" id="horaires" name="horaires" value="<?= htmlspecialchars(implode('|',array_map(function($horaire) {
                    return "{$horaire->jour}:{$horaire->seances}";
                }, $film->horaires->horaire))) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Modifier Film</button>
        </form>
        <a class="btn btn-outline-secondary mt-3" href="admin.php">Retour</a>
    </div>
</body>
</html>
