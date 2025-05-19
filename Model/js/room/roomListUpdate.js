function fetchRoomList() {
    fetch('../Controller/roomListUpdate.php')
        .then(response => response.json())
        .then(data => {
            const roomList = document.getElementById('room-list');
            roomList.innerHTML = ''; // Clear the current list

            data.forEach(room => {
                const roomItem = document.createElement('tr');
                roomItem.innerHTML = `
                    <th class="room-id">${room.rooCode}</th>
                    <th class="room-player1">${room.rooPlaCode1}</th>
                    <th class="room-player2">${room.rooPlaCode2}</th>
                    <th><input type="submit" class="room-input" id="enterRoom" name="editRoom" value="Entrar na Sala"/></th>
                `;
                const enterRoomButton = roomItem.querySelector('input[id="enterRoom"]');
                enterRoomButton.addEventListener('click', function() {
                    const roomCode = room.rooCode;
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../Controller/editRoom.php';

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'editRoom';

                    const input2 = document.createElement('input');
                    input2.type = 'hidden';
                    input2.name = 'roomCode';
                    input2.value = roomCode;

                    form.appendChild(input2);
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                });
                roomList.appendChild(roomItem);
            });
        })
        .catch(error => console.error('Error fetching room list:', error));
}

setInterval(fetchRoomList, 1000);