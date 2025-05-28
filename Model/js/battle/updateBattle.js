function fetchUpdateBattle() {
    fetch('../Controller/updateBattle.php')
        .then(response => response.json())
        .then(data => {
            if (data.result) {
                // Hide the wait popup if the second player is ready
                document.querySelector('.wait-popup').classList.remove('hidden');
                // alert("Acabou a batalha");
                clearInterval(interval);
            }
        })
        .catch(error => console.error('Error fetching readiness status:', error));
}

// Call fetchIsReady every 10s
const interval = setInterval(fetchUpdateBattle, 3000);