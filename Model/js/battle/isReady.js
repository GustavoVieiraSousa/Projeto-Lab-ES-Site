document.addEventListener("DOMContentLoaded", function fetchIsReady() {});

function fetchIsReady() {
    fetch('../Controller/waitBattle.php')
        .then(response => response.json())
        .then(data => {
            if (data.ready) {
                // Hide the wait popup if the second player is ready
                document.querySelector('.wait-popup').classList.add('hidden');
                fetch('../Controller/setPokemonMaxHp.php');
                clearInterval(interval1);

                // Emit a custom event to signal completion
                document.dispatchEvent(new Event('isReadyComplete'));
            } else {
                console.log('Waiting for the second player...');
            }
        })
        .catch(error => console.error('Error fetching readiness status:', error));
    }

// Call fetchIsReady every 1000ms
const interval1 = setInterval(fetchIsReady, 1000);