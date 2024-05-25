<?php
/**
 * Author:      Romain Lenoir
 * Date:        21.03.2024
 * Description: This script serves as the frontend interface for managing participant status.
 *              It connects to a MySQL database to retrieve the status of each participant and allows ringing participants, updating their played status accordingly.
 *              The page is designed to dynamically update every 5 seconds to reflect real-time changes in participant status. For students
 */ ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>DOYEN - vue étudiants</title>
    <link rel="icon" href="image.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="image.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="style.css" rel="stylesheet">
</head>
<body onload="initializeStuff()">
<div class="container mt-5">
    <table class="table table-bordered table-hover table-custom" id="participantsTable">
        <thead class="thead-light">
            <tr>
                <th>Nom</th>
                <th>Actuel</th>
                <th>Interaction</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script>
    /**********************************************************************
     * ********************************************************************
     * ******************************* DATA *******************************
     * ********************************************************************
     * ********************************************************************/

    var participants = [
        { name: 'Jonathan Zosso', status: "", blocked: false, played: false, note: ''},
        { name: 'Noémie Capt', status: "", blocked: false, played: false, note: '' },
        { name: 'Géraldine Niffenegger', status: "", blocked: false, played: false, note: '' },
        { name: 'François Monnin', status: "", blocked: false, played: false, note: '' },
        { name: 'Vincent Kuenzi', status: "", blocked: false, played: false, note: '' }
    ];


    // Initialization
    function initializeStuff() {
        // Fetch participant status, block status, and played status
        Promise.all(participants.map(selectAttributsParticipants)).then(createBaseTable).catch(handleError);
    }

    /**********************************************************************
     * ********************************************************************
     * ************************** AJAX functions **************************
     * ********************************************************************
     * ********************************************************************/

    /**
     * Selects all participants attributes.
     */
    function selectAttributsParticipants(participant) {
        return Promise.all([
            selectParticipantStatus(participant),
            selectParticipantBlocked(participant),
            selectParticipantPlayed(participant),
            selectParticipantNote(participant)
        ]);
    }

    /**
     * Fetches the status of a participant.
     * @param {object} participant - The participant object.
     * @returns {Promise} - A promise that resolves when the status is fetched.
     */
    function selectParticipantStatus(participant) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'select_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        participant.status = xhr.responseText;
                        resolve();
                    } else {
                        reject('Error fetching status for ' + participant.name);
                    }
                }
            };
            xhr.send('participant=' + encodeURIComponent(participant.name));
        });
    }

    /**
     * Fetches the blocked status of a participant.
     * @param {object} participant - The participant object.
     * @returns {Promise} - A promise that resolves when the blocked status is fetched.
     */
    function selectParticipantBlocked(participant) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'select_blocked.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        participant.blocked = parseInt(xhr.responseText) === 1;
                        console.log('Blocked status fetched successfully for ' + participant.name);
                        resolve();
                    } else {
                        reject('Error fetching blocked status for ' + participant.name);
                    }
                }
            };
            xhr.send('participant=' + encodeURIComponent(participant.name));
        });
    }

    /**
     * Fetches the played status of a participant.
     * @param {object} participant - The participant object.
     * @returns {Promise} - A promise that resolves when the played status is fetched.
     */
    function selectParticipantPlayed(participant) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'select_played.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        participant.played = parseInt(xhr.responseText) === 1;
                        console.log('Played status fetched successfully for ' + participant.name);
                        resolve();
                    } else {
                        reject('Error fetching played status for ' + participant.name);
                    }
                }
            };
            xhr.send('participant=' + encodeURIComponent(participant.name));
        });
    }
    
    /**
    * Fetches the note status of a participant.
    * @param {object} participant - The participant object.
    * @returns {Promise} - A promise that resolves when the note status is fetched.
    */
    function selectParticipantNote(participant) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'select_note.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        participant.note = xhr.responseText;
                        console.log('Note fetched successfully for ' + participant.name);
                        resolve();
                    } else {
                        reject('Error fetching note for ' + participant.name);
                    }
                }
            };
            xhr.send('participant=' + encodeURIComponent(participant.name));
        });
    }

    /**
     * Updates the played status of a participant.
     * @param {string} username - The username of the participant.
     */
    function updateParticipantPlayed(username) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_played.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Played status updated successfully for ' + username);
                } else {
                    console.error('Error updating played status for ' + username);
                }
            }
        };
        xhr.send('participant=' + encodeURIComponent(username));
    }

    /**********************************************************************
     * ********************************************************************
     * ************************** Event handler ***************************
     * ********************************************************************
     * ********************************************************************/

   /**
    * Rings the participant and updates the played status.
    * @param {HTMLElement} button - The button element clicked to ring.
    */
    function Ringing(button) {
        // Ring the participant and update played status
        var table = document.getElementById('participantsTable');
        var ringingButtons = table.querySelectorAll('.btn-present');

        var row = button.closest('tr');
        var username = row.querySelector('td').textContent.trim();

        ringingButtons.forEach(function(ringingButton) {
            ringingButton.classList.add("button-disabled");
        });

        updateParticipantPlayed(username);
    }

    /**********************************************************************
     * ********************************************************************
     * ************************ HTML manipulation *************************
     * ********************************************************************
     * ********************************************************************/

   /**
    * Creates the base table with participant data.
    */
    function createBaseTable() {
        // Create the table with participant data
        var tableBody = document.getElementById('participantsTable').querySelector('tbody');
        tableBody.innerHTML = '';
        participants.forEach(function(participant) {
            tableBody.innerHTML += createTableRow(participant);
        });
        updateDisabledButtons();
    }

    /**
     * Creates a table row for a participant.
     * @param {object} participant - The participant object.
     * @returns {string} - The HTML markup for the table row.
     */
    function createTableRow(participant, selectedUser) {
    let statusClass = 'status-cell';

    if (participant.status === 'Présent') {
        statusClass += ' status-present';
    } else if (participant.status === 'Occupé') {
        statusClass += ' status-occupied';
    } else if (participant.status === 'Absent') {
        statusClass += ' status-absent';
    }

    var idRow = 'row_' + participant.name.replace(/\s/g, '');
    var idNote = 'note_' + participant.name.replace(/\s/g, '');
    var disabledClass = participant.blocked ? 'disabled' : '';
    var noteContent = "";

    // Vérifie si l'utilisateur actuel sonne
    var userRinging = sessionStorage.getItem('user_ringing');
    if (userRinging && userRinging === participant.name) {
        noteContent = participant.note;
        sessionStorage.removeItem('user_ringing');
    }

    return `
        <tr id="${idRow}" class="${disabledClass}">
            <td>${participant.name}</td>
            <td class="${statusClass}">${participant.status}</td>
            <td><button class="${participant.blocked ? 'btn btn-custom btn-present button-disabled' : 'btn btn-custom btn-present'}" onclick="Ringing(this)">Sonner</button></td>
            <td id="${idNote}">${noteContent}</td>
        </tr>
    `;
}


    /**
     * Updates the disabled state of interaction buttons based on played status.
     */
    function updateDisabledButtons() {
        var userRinging = null;
        var allStopped = true; 

        participants.forEach(function(participant) {
            if (participant.played && userRinging === null) {
                userRinging = participant.name;
            }
            if (participant.played) {
                allStopped = false; 
            }
        });

        participants.forEach(function(participant) {
            var tableRow = document.getElementById('row_' + participant.name.replace(/\s/g, ''));
            var buttons = tableRow.querySelectorAll('button');

            if (!tableRow.classList.contains('disabled')) {
                buttons.forEach(function(btn) {
                    if (participant.name === userRinging) {
                        var note = document.getElementById('note_' + participant.name.replace(/\s/g, ''));
                        note.innerHTML = "Patientez...";
                        note.classList.add("btn-neutral");
                        // Create session for the Ringing stat
                        sessionStorage.setItem('user_ringing', participant.name);
                    }

                    if (userRinging !== null) btn.classList.add('button-disabled');
                    else btn.classList.remove('button-disabled');
                });
            }
        });

        if (allStopped) {
            var userRinging = sessionStorage.getItem('user_ringing');
            if (userRinging) {
                var note = document.getElementById('note_' + userRinging.replace(/\s/g, ''));
                var username = participants.find(p => p.name === userRinging);
                if (username) {
                    //note.innerHTML = participant.note;
                    sessionStorage.removeItem('user_ringing');
                }
            }
        }
    }

    // Automatic refresh
    setInterval(function() {
        initializeStuff();
    }, 10000);

    /**
    * Simple method for write an error in the console inspector
    */
    function handleError() {
        console.error('Error finding all attributs in the database');
    }
</script>
</body>
</html>