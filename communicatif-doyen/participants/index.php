<?php
/**
 * Author:      Romain Lenoir
 * Date:        21.03.2024
 * Description: This script serves as the frontend interface for managing participant status.
 *              It connects to a MySQL database to retrieve the status of each participant and allows ringing participants, updating their played status accordingly.
 *              The page is designed to dynamically update every 5 seconds to reflect real-time changes in participant status. For deans
 */ ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>DOYEN - vue participants</title>
    <link rel="icon" href="image.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="image.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<audio id="audio" src="son.mp3"></audio>
<div id="myModal" class="modal">
    <div class="modal-content">
        <p>Qui êtes-vous ? :</p>
        <select id="userSelect">
            <option value="not item"></option>
            <option value="Jonathan Zosso">Jonathan Zosso</option>
            <option value="Noémie Capt">Noémie Capt</option>
            <option value="Géraldine Niffenegger">Géraldine Niffenegger</option>
            <option value="François Monnin">François Monnin</option>
            <option value="Vincent Kuenzi">Vincent Kuenzi</option>
        </select>
        <p style="color: red;">Important :</p>
        <p>Cette page incorpore un système d'audio lorsque un élève sonne sur votre profil, <em>il vous faut interagir au moins une fois avec le site pour que le son émane de vos écrans</em></p>
        <button id="btn_modal" onclick="initializeStuff()">Confirmer</button>
    </div>
</div>
<div class="container mt-5">
    <table class="table table-bordered table-hover table-custom" id="participantsTable">
        <thead class="thead-light">
            <tr>
                <th>Nom</th>
                <th>Actuel</th>
                <th colspan="3">Status</th>
                <th colspan="2" id="chmp_interaction">Interaction</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script>
   /**
    * Initializes the modal by displaying it.
    */
    function showModal() {
        document.getElementById('myModal').style.display = "block";
    }

   /**
    * Closes the modal.
    */
    function closeModal() {
        document.getElementById('myModal').style.display = "none";
    }

    var participants = [
        { name: 'Jonathan Zosso', status: "", blocked: false, played: false, note: "" },
        { name: 'Noémie Capt', status: "", blocked: false, played: false, note: ""  },
        { name: 'Géraldine Niffenegger', status: "", blocked: false, played: false, note: "" },
        { name: 'François Monnin', status: "", blocked: false, played: false, note: ""  },
        { name: 'Vincent Kuenzi', status: "", blocked: false, played: false, note: "" }
    ];

    window.onload = function() {
        let selectedUserValue = '';

        document.getElementById('userSelect').addEventListener('change', function() {
            selectedUserValue = this.value;
        });

        document.getElementById('btn_modal').addEventListener('click', function() {
            if (selectedUserValue !== '') {
                localStorage.setItem('selectedUser', selectedUserValue);
            }
        });
        var lastSelectedUser = localStorage.getItem('selectedUser');

        if (lastSelectedUser) {
            document.getElementById('userSelect').value = lastSelectedUser;
            Promise.all(participants.map(selectAttributsParticipants)).then(createBaseTable).catch(handleError);
        } else {
            showModal();
        }
    };

    // Initialize 
    function initializeStuff() {
        return Promise.all(participants.map(selectAttributsParticipants)).then(createBaseTable).catch(handleError);
    }

    /**********************************************************************
     * ********************************************************************
     * ************************** AJAX functions **************************
     * ********************************************************************
     * ********************************************************************/

   /**
    * Fetches all attributes of participants.
    * @param {object} participant - The participant object.
    * @returns {Promise} - A promise that resolves when all attributes are fetched.
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
    * Updates the status of a participant in the database.
    * @param {object} username - The participant object containing the name.
    * @param {string} status_user - The new status of the participant.
    */
    function updateParticipantStatus(username, status_user) {
        var xhr = new XMLHttpRequest();
        
        xhr.open('POST', 'update_status.php', true);
        
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Status updated successfully in the database.');
                } else {
                    console.error('Error updating status in the database.');
                }
            }
        };
        
        xhr.send('participant=' + encodeURIComponent(username.name) + '&status=' + encodeURIComponent(status_user));
    }

   /**
    * Updates the blocked status of a participant in the database.
    * @param {object} username - The participant object containing the name.
    * @param {boolean} blocked - The new blocked status (true or false).
    */
    function updateParticipantBlocked(username, blocked) {
        var xhr = new XMLHttpRequest();
        
        xhr.open('POST', 'update_blocked.php', true);
        
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Status updated successfully in the database.');
                } else {
                    console.error('Error updating status in the database.');
                }
            }
        };
        
        xhr.send('participant=' + encodeURIComponent(username.name) + '&blocked=' + (blocked ? '1' : '0'));
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

    /**
    * Updates the note status of a participant.
    * @param {string} username - The username of the participant.
    */
function updateParticipantNote(username, note) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_note.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log('Note updated successfully for ' + username);
                var participant = participants.find(p => p.name === username);
                if (participant) {
                    participant.note = note;
                }
                createBaseTable();
            } else {
                console.error('Error updating note for ' + username);
            }
        }
    };
    xhr.send('participant=' + encodeURIComponent(username) + '&new_note=' + encodeURIComponent(note));
}


    /**********************************************************************
     * ********************************************************************
     * ************************** Event handler ***************************
     * ********************************************************************
     * ********************************************************************/

   /**
    * Stops the ringing sound and updates the played status.
    * @param {object} button - The button element that triggered the action.
    */
    function stopRinging(button) {
        var row = button.closest('tr');
        var username = row.querySelector('td').textContent.trim();

        updateParticipantPlayed(username);
        button.classList.add('button-disabled');
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
        var selectedUser = document.getElementById('userSelect').value;
        participants.sort(function(a, b) {
            return a.name === selectedUser ? -1 : b.name === selectedUser ? 1 : 0;
        });
        var tableBody = document.getElementById('participantsTable').querySelector('tbody');
        tableBody.innerHTML = '';
        participants.forEach(function(participant) {
            tableBody.innerHTML += createTableRow(participant, selectedUser);
        });

        updateDisabledButtons();
        if (document.getElementById('myModal').style.display) { 
            closeModal();
        }   
        participants.forEach(function(participant) {
            if (participant.played && participant.name === selectedUser) {
                var audio = document.getElementById('audio');
                if (audio.readyState >= 2) { 
                    audio.play()   
                } else {
                    console.error('L\'audio n\'est pas encore prêt.');
                }
            } 
        }); 
    }

   /**
    * Creates the HTML markup for a table row.
    * @param {object} participant - The participant object.
    * @param {string} selectedUser - The currently selected user.
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

        var id = 'row_' + participant.name.replace(/\s/g, '');
        var disabledClass = participant.blocked ? 'disabled' : '';
        var readonlyAttribute = participant.played ? '' : 'readonly';

        return `
            <tr ${participant.name === selectedUser ? 'id="' + id + '" class="selected ' + disabledClass + '"' : 'id="' + id + '" class="' + disabledClass + '"'}>
                <td>${participant.name}</td>
                <td class="${statusClass}">${participant.status}</td>
                <td><button class="btn btn-custom btn-present" onclick="updateStatus(this, 'Présent')">Présent</button></td>
                <td><button class="btn btn-custom btn-absent" onclick="updateStatus(this, 'Absent')">Absent</button></td>
                <td><button class="btn btn-custom btn-occupied" onclick="updateStatus(this, 'Occupé')">Occupé</button></td>
                <td><button class="${!participant.played ? 'btn btn-custom btn-neutral button-disabled' : 'btn btn-custom btn-neutral'}" onclick="stopRinging(this)">Arrêter</button></td>
                <td><textarea class="${!participant.played ? 'disabled' : ''}" ${readonlyAttribute} onchange="updateParticipantNote('${participant.name}', this.value)">${participant.note}</textarea></td>

            </tr>
        `;
    }

   /**
    * Updates the status of a participant in the database.
    * @param {object} button - The button element.
    * @param {string} status - The new status to be updated.
    * @param {object} username - The participant object containing the name.
    */
    function UpdateStatusInDB(button, status, username) {
        if (username) {
            username.status = status;
            updateParticipantStatus(username, status);
        }
    }

   /**
    * Updates the status of a participant based on button click.
    * @param {object} button - The button element.
    * @param {string} status - The new status.
    */
    function updateStatus(button, status) {
        var tableRow = button.closest('tr');
        var buttons = tableRow.querySelectorAll('button:not(.btn-neutral)');
            
        buttons.forEach(function(btn) {
            if (btn === button) {
                button.classList.add('button-disabled');
                var statusCell = tableRow.querySelector('.status-cell');
                statusCell.textContent = status;
                statusCell.classList.remove('status-present', 'status-absent', 'status-occupied');
                if (status === 'Présent') {
                    statusCell.classList.add('status-present');
                } else if (status === 'Absent') {
                    statusCell.classList.add('status-absent');
                } else if (status === 'Occupé') {
                    statusCell.classList.add('status-occupied');
                }
            } else {
                btn.classList.remove('button-disabled');
                btn.style.pointerEvents = 'auto';
            }
        });
        var closestParticipant = findClosestParticipant(button);
        var tableRow = button.closest('tr');
        if (tableRow.classList.contains('disabled')) {
            tableRow.classList.remove('disabled');
            updateParticipantBlocked(closestParticipant, false); 
        }
        UpdateStatusInDB(button, status, closestParticipant);
    }

   /**
    * Updates the disabled state of interaction buttons based on participant status.
    */
    function updateDisabledButtons() {
        participants.forEach(function(participant) {
            var tableRow = document.getElementById('row_' + participant.name.replace(/\s/g, ''));
            var buttons = tableRow.querySelectorAll('button');
            buttons.forEach(function(btn) {
                if (btn.textContent.trim() === participant.status) {
                    if (!tableRow.classList.contains('disabled')) {
                        btn.classList.add('button-disabled');
                    }
                }
            });
        });
    }

    /**********************************************************************
     * ********************************************************************
     * ************************* Help management **************************
     * ********************************************************************
     * ********************************************************************/

   /**
    * Finds the participant associated with the button clicked.
    * @param {object} button - The button element.
    * @returns {object} - The participant object.
    */
    function findClosestParticipant(button) {
        var tr = button.closest('tr');
        var participantName = tr.querySelector('td').textContent.trim();
        return participants.find(function(participant) {
            return participant.name === participantName;
        });
    }

   // Automatic refresh
    setInterval(function() {
        var lastSelectedUser = localStorage.getItem('selectedUser');

        // if nobody is selected in the modal => re-ask
        if (!lastSelectedUser) return;

        // if nobody is ringing => deactivate the sound
        if (audio && !audio.paused) { 
            var isPlayedArray = []; 
            participants.forEach(function(participant) {
                isPlayedArray.push(participant.played); 
            });

            var isallFalse = isPlayedArray.every(function(value) {
                return value === false; 
            });
            if (isallFalse) {
                audio.pause();
                audio.currentTime = 0;
            }
        }
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

