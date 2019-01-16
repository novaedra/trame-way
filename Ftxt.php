<?php

if (!isset($_POST['valider'])) {

    ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="file">Selectioné un fichier</label>
        <input type="file" name="file"/></br>
        <label for="mot">Mot recherché :</label><br/>
        <input type="text" name="mot" /><br/>
        <input type="submit" value="valider" name="valider"/>
    </form>

    <?php

}

else {


    $mot=trim(strip_tags($_POST['mot']));
    $fichier =$_FILES['file']['name'];



    $resultats =array();
    @ $fp = fopen($fichier, 'r') or die('Ouverture en lecture de "' . $fichier . '" impossible !');
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
