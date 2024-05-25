<?php
/**
 * Author:      Romain Lenoir
 * Date:        21.03.2024
 * Description: This script updates the blocked status of a participant from a MySQL database based on the provided participant name via POST request.
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
$blocked = $_POST['blocked'];

$blocked = ($blocked === '1' ? true : false);

$sql = "UPDATE participants SET blocked=? WHERE name=?";
$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    die("Erreur de préparation de la requête: " . $mysqli->error);
}

$stmt->bind_param("is", $blocked, $participant);

if ($stmt->execute()) {
    echo "Statut mis à jour avec succès dans la base de données.";
} else {
    echo "Erreur lors de la mise à jour du statut dans la base de données: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
