<?php
/**
 * Author:      Romain Lenoir
 * Date:        21.03.2024
 * Description: This script connects to a MySQL database to retrieve the status of a participant based on the provided participant name via POST request.
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

    $sql = "SELECT statut FROM participants WHERE name='$participant'";

    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $statut = $row['statut'];
        echo $statut;
    } else {
        echo "Statut non trouvé pour le participant " . $participant;
    }
} else {
    echo "Nom du participant non spécifiés dans la requête POST";
}

$mysqli->close();
?>
