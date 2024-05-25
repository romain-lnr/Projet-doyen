<?php
/**
 * Author:      Romain Lenoir
 * Date:        21.03.2024
 * Description: This script retrieves the blocked status of a participant from a MySQL database based on the provided participant name via POST request.
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

    $sql = "SELECT blocked FROM participants WHERE name='$participant'";

    $result = $mysqli->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo $row['blocked'];
    } else {
        echo "Erreur lors de la récupération du statut bloqué depuis la base de données: " . $mysqli->error;
    }
} else {
    echo "Nom du participant non spécifiés dans la requête POST";
}

$mysqli->close();
?>
