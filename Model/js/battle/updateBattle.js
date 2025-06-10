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

function fetchGetPokemonParams(){
    fetch('../Controller/getPokemonParams.php')
        .then(response => response.json())
        .then(data2 => {
            if(data2.res){
                //alterar os pokemons na tela e os ataques AQUI
                //TODO: FAZER ALTERAR OS VISUAIS
                
            }
            else{

            }
        })
        .catch(error => console.error('Error fetching readiness status:', error));
}

function fetchAllFunctions(){
    fetchUpdateBattle();
    fetchGetPokemonParams();
}

fetchGetPokemonParams();
// Call all the functions every 10s
const interval = setInterval(fetchAllFunctions, 10000);