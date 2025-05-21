document.addEventListener("DOMContentLoaded", function fetchIsReady() {});

function fetchIsReady() {
    fetch('../Controller/waitBattle.php')
        .then(response => response.json())
        .then(data => {
            if (data.ready) {
                // Hide the wait popup if the second player is ready
                document.querySelector('.wait-popup').classList.add('hidden');
                clearInterval(interval1);
            } else {
                console.log('Waiting for the second player...');
            }
        })
        .catch(error => console.error('Error fetching readiness status:', error));
    }

// Call fetchIsReady every 1000ms
const interval1 = setInterval(fetchIsReady, 1000);