<?php
define('FICHIER', 'trames/trames.json');
if (!isset($_POST['valider'])) {

    ?>
    <form method="POST">
        <label for="mot">Mot recherché :</label><br/>
        <input type="text" name="mot" /><br/>
        <input type="submit" value="valider" name="valider"/>
    </form>

    <?php

}

else {


    $mot=$_POST['mot'];;


    $resultats =array();
    @ $fp = fopen(FICHIER, 'r') or die('Ouverture en lecture de "' . FICHIER . '" impossible !');
    while (!feof($fp)) {
        $ligne = fgets($fp, 1024);
        if (preg_match('|\b' . preg_quote($_POST['mot']) . '\b|i', $ligne)) {
            $resultats[] = $ligne;
        }
    }
    fclose($fp);
    $nb = count($resultats);
    if ($nb > 0) {
        echo "'$mot' trouvé $nb fois :";
        echo '<ul>';
        foreach ($resultats as $v) {
            echo "<li>$v</li>";

        }
        echo '</ul>';
    } else {
        die("Ce nom n'est pas présent !");
    }

}
