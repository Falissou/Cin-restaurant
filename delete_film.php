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

// Supprimer le film
unset($films->film[$id]);

// Sauvegarde du fichier XML
$films->asXML($file);

header('Location: admin.php');
exit();
?>
