<?php
/**
 * Author:      Romain Lenoir
 * Date:        21.03.2024
 * Description: This script updates the played status of a participant from a MySQL database based on the provided participant name via POST request.
 */

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "communicatif-doyen";

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");

if(isset($_POST['participant'])) {
    $participant = $_POST['participant'];

    $sql = "UPDATE participants SET played = 1 WHERE name = '$participant'";

    if ($mysqli->query($sql) === TRUE) {
        echo "La colonne 'played' a été mise à jour avec succès pour le participant $participant";
    } else {
        echo "Erreur lors de la mise à jour de la colonne 'played': " . $mysqli->error;
    }
} else {
    echo "Nom du participant non spécifiés dans la requête POST";
}

$mysqli->close();
?>
