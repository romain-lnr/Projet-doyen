<?php
/**
 * Author:      Romain Lenoir
 * Date:        21.03.2024
 * Description: This script updates the note of a participant in the MySQL database based on the provided participant name and new note via POST request.
 */

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "communicatif-doyen";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");

if (isset($_POST['participant']) && isset($_POST['new_note'])) {
    $participant = $mysqli->real_escape_string($_POST['participant']);
    $new_note = $mysqli->real_escape_string($_POST['new_note']);

    $sql = "UPDATE participants SET Note='$new_note' WHERE name='$participant'";

    if ($mysqli->query($sql) === TRUE) {
        echo "Note updated successfully for " . $participant;
    } else {
        echo "Error updating note: " . $mysqli->error;
    }
} else {
    echo "Participant name or new note not specified in the POST request";
}

$mysqli->close();
?>
