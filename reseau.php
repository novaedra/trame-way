<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

$sql = "SELECT * FROM reseau;";
$query = $pdo -> prepare($sql);
$query -> execute();
$return = $query -> fetchALL();

tab($return);

echo '<form method="post">';

foreach ($return as $var) {
    echo '<input type="chechbox" name="reseau" value="'.$var[0]['id'].'">'.$var[0]['id'].': Nom du réseau : '.$var[0]['nom_reseau'].'ip : '.$var[0]['ip_saisie'].' masque : '.$var[0]['mask'].'ip - : '.$var[0]['ip_low'].' ip + : '.$var[0]['ip_high'].' date :'.$var[0]['created_at'].'</input>';
}
