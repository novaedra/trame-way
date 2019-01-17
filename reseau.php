<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

$sql = "SELECT * FROM reseau ";
$query = $pdo -> prepare($sql);
$return = $query -> fetch();

tab($return);
