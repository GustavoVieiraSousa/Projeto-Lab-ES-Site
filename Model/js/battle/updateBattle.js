function fetchUpdateBattle() {
    fetch('../Controller/updateBattle.php')
        .then(response => response.json())
        .then(data => {
            if (data.result) {
                //caso o player ganhe, perca, etc...
            } else {
                console.log('Waiting for the second player...');
            }
        })
        .catch(error => console.error('Error fetching readiness status:', error));
}

// Call fetchIsReady every 10s
setInterval(fetchUpdateBattle, 10000);