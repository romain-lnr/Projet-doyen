<?php
/**
 * Author:      Romain Lenoir
 * Date:        21.03.2024
 * Description: This script updates the status of a participant from a MySQL database based on the provided participant name via POST request.
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

$participant = $_POST['participant'];
$statut = $_POST['status'];

$sql = "UPDATE participants SET statut='$statut' WHERE name='$participant'";

if ($mysqli->query($sql) === TRUE) {
    echo "Statut mis à jour avec succès dans la base de données.";
} else {
    echo "Erreur lors de la mise à jour du statut dans la base de données: " . $mysqli->error;
}

$mysqli->close();
?>
