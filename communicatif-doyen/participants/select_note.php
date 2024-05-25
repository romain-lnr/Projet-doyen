<?php
/**
 * Author:      Romain Lenoir
 * Date:        21.03.2024
 * Description: This script retrieves the note status of a participant from a MySQL database based on the provided participant name via POST request.
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

    $sql = "SELECT Note FROM participants WHERE name='$participant'";

    $result = $mysqli->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo $row['Note']; 
    } else {
        echo "Erreur lors de la récupération de la note depuis la base de données: " . $mysqli->error;
    }
} else {
    echo "Nom du participant non spécifié dans la requête POST";
}

$mysqli->close();
?>
