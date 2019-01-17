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
    ?><input type="checkbox" name="reseau" value="<?php echo $var['id']; ?>"><?php echo $var['nom_reseau']; ?></input> <?php
}
