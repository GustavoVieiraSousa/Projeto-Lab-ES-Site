function fetchIsReady() {
    fetch('../Controller/waitBattle.php')
        .then(response => response.json())
        .then(data => {
            if (data.ready) {
                // Hide the wait popup if the second player is ready
                document.querySelector('.wait-popup').classList.add('hidden');
            } else {
                console.log('Waiting for the second player...');
            }
        })
        .catch(error => console.error('Error fetching readiness status:', error));
}

// Call fetchIsReady every 1000ms
setInterval(fetchIsReady, 1000);