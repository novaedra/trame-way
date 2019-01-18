<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

define('NL', "\n");
include('IP4Calc.php');
?>
            <form method="post" action="reseau.php">

                <label for="adresse">Saisir le nom du réseau:</label><br/>
                <input type="text" name="nom_reseau"/></br>

                <label for="adresse">Saisir une adresse IPv4:</label><br/>
                <input type="text" name="adresse"/></br>

                <label for="masque">Saisir le masque de sous réseau</label><br/>
                <select name="masque">
                    <option value="255.0.0.0">255.0.0.0/8</option>
                    <option value="255.128.0.0">255.128.0.0/9</option>
                    <option value="255.192.0.0">255.192.0.0/10</option>
                    <option value="255.224.0.0">255.224.0.0/11</option>
                    <option value="255.240.0.0">255.240.0.0/12</option>
                    <option value="255.248.0.0">255.248.0.0/13</option>
                    <option value="255.252.0.0">255.252.0.0/14</option>
                    <option value="255.254.0.0">255.254.0.0/15</option>
                    <option value="255.255.0.0">255.255.0.0/16</option>
                    <option value="255.255.128.0">255.255.128.0/17</option>
                    <option value="255.255.192.0">255.255.192.0/18</option>
                    <option value="255.255.224.0">255.255.224.0/19</option>
                    <option value="255.255.240.0">255.255.240.0/20</option>
                    <option value="255.255.248.0">255.255.248.0/21</option>
                    <option value="255.255.252.0">255.255.252.0/22</option>
                    <option value="255.255.254.0">255.255.254.0/23</option>
                    <option value="255.255.255.0">255.255.255.0/24</option>
                    <option value="255.255.255.128">255.255.255.128/25</option>
                    <option value="255.255.255.192">255.255.255.192/26</option>
                    <option value="255.255.255.224">255.255.255.224/27</option>
                    <option value="255.255.255.240">255.255.255.240/28</option>
                    <option value="255.255.255.248">255.255.255.248/29</option>
                    <option value="255.255.255.252">255.255.255.252/30</option>
                    <option value="255.255.255.254">255.255.255.254/31</option>
                    <option value="255.255.255.255">255.255.255.255/32</option>
                </select>
                </br>

                <input type="submit" name="calculer" value="calculer"/>
            </form>
<?php
if (!empty($_POST['calculer'])) {

    $addr = trim(strip_tags($_POST['adresse']));
    $mask = trim(strip_tags($_POST['masque']));
    $nom_reseau = trim(strip_tags($_POST['nom_reseau']));


    if (filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {

        $oIP = new IP4Calc($addr, $mask);



        $sql = "INSERT INTO reseau (nom_reseau,ip_saisie,mask,ip_low,ip_high,adrsrx,created_at) VALUES (:nom_reseau,:addr,:mask,:ip_low,:ip_high,:adrrsx , NOW())";
        $query = $pdo -> prepare($sql);
        $query -> bindValue(':nom_reseau', $nom_reseau, PDO::PARAM_STR);
        $query -> bindValue(':addr', $addr, PDO::PARAM_STR);
        $query -> bindValue(':mask', $mask, PDO::PARAM_STR);
        $query -> bindValue(':ip_low', $oIP->get(IP4Calc::MIN_HOST, IP4Calc::QUAD_DOTTED), PDO::PARAM_STR);
        $query -> bindValue(':ip_high', $oIP->get(IP4Calc::MAX_HOST, IP4Calc::QUAD_DOTTED), PDO::PARAM_STR);
        $query -> bindValue(':adrrsx', $oIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED), PDO::PARAM_STR);

        $query -> execute();
    }
    else {
        echo $addr.'Adresse IP invalide verifiez que l\'adresse  entrer  est une adresse valide ';
        br();
        echo '<a href="reseau.php">retour</a>';
    }
}
?>

<form action="reseau.php" method="POST">

<?php
if (!empty($_POST['supprimer'])) {

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

    foreach ($return as $var) { ?>
        <input type="checkbox" name="<?php echo $var['id']; ?>" value="supprime moi vite stp"><?php echo $var['nom_reseau']; ?></input><br/>
    <?php }
}
else {

    $sql = "SELECT * FROM reseau;";
    $query = $pdo -> prepare($sql);
    $query -> execute();
    $return = $query -> fetchALL();

    foreach ($return as $var) { ?>
        <input type="checkbox" name="<?php echo $var['id']; ?>" value="supprime moi vite stp"><?php echo $var['nom_reseau']; ?></input><br/>
    <?php }
}
?>
<input type="submit" name="supprimer" value="Supprimer"></input>
</form>

<?php include ('inc/footer.php');