<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trame-Way</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300" type="text/css" />
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
</head>
<body>

<?php
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');
?>

<div class="container">
    <div class="fichier"><a class="bloc_lien" href="fichier.php" title="Analyser à partir d'un fichier"><img src="inc/img/file.png" alt="fichier"><br/><p class="option">Analyser des fichiers JSON</p></a></div>
    <div class="capture"><a class="bloc_lien" href="capture.php" title="Effectuer une capture"><img src="inc/img/network.png" alt="réseau"><br/><p class="option">Commencer une capture</p></a></div>
</div>
<div class="clear"></div>

<?php include ('inc/footer.php'); ?>
