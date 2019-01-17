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

if (!empty($_POST)) {
    print_r($_POST);
}

echo '<form action="reseau.php" method="POST">';

foreach ($return as $var) { ?>
    <input type="checkbox" name="reseau" value="<?php echo $var['id']; ?>"><?php echo $var['nom_reseau']; ?></input><br/>
<?php }

echo '<input type="submit" value="Supprimer"></input>';
echo '</form>';