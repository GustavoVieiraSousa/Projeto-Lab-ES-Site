function fetchRoomList() {
    fetch('../Controller/roomListUpdate.php')
        .then(response => response.json())
        .then(data => {
            const roomList = document.getElementById('room-list');
            roomList.innerHTML = ''; // Clear the current list

            data.forEach(room => {
                const roomItem = document.createElement('li');
                roomItem.innerHTML = `
                    <strong>Room ID:</strong> ${room.rooCode}<br>
                    <strong>Player 1 ID:</strong> ${room.rooPlaCode1}<br>
                    <strong>Player 2 ID:</strong> ${room.rooPlaCode2}<br>
                    <input type="submit" id="enterRoom" name="editRoom" value="Entrar na Sala"/><br><br>
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

setInterval(fetchRoomList, 500);
fetchRoomList();