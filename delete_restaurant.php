<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Vérifier si l'ID du restaurant à supprimer est passé en paramètre
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

// Vérifier si le restaurant avec l'ID spécifié existe
if (isset($restaurants->restaurant[$id])) {
    // Supprimer le restaurant
    unset($restaurants->restaurant[$id]);

    // Sauvegarder le fichier XML
    $restaurants->asXML($file);
}

// Redirection vers la page d'administration
header('Location: admin.php');
exit();
?>
