<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');
?>

<form action="reseau.php" method="POST">

<?php
if (!empty($_POST)) {

    foreach ($_POST as $key => $value) {
        $key = trim(strip_tags($key));
        if (is_numeric($key)) {

            $sql = "DELETE FROM reseau WHERE id=:id;";
            $query = $pdo -> prepare($sql);
            $query->bindValue(':id', $key, PDO::PARAM_INT);
            $query -> execute();
        }
    }

    $sql = "SELECT * FROM reseau;";
    $query = $pdo -> prepare($sql);
    $query -> execute();
    $return = $query -> fetchALL();
    tab($return);

    foreach ($return as $var) { ?>
        <input type="checkbox" name="<?php echo $var['id']; ?>" value="supprime moi vite stp"><?php echo $var['nom_reseau']; ?></input><br/>
    <?php }
}
else {

    $sql = "SELECT * FROM reseau;";
    $query = $pdo -> prepare($sql);
    $query -> execute();
    $return = $query -> fetchALL();
    tab($return);

    foreach ($return as $var) { ?>
        <input type="checkbox" name="<?php echo $var['id']; ?>" value="supprime moi vite stp"><?php echo $var['nom_reseau']; ?></input><br/>
    <?php }
}
?>
<input type="submit" value="Supprimer"></input>
</form>