function fetchUpdateBattle() {
    fetch('../Controller/updateBattle.php')
        .then(response => response.json())
        .then(data => {
            if (data.result) {
                // Hide the wait popup if the second player is ready
                document.querySelector('.wait-popup').classList.remove('hidden');
                // Mostra mensagem de vitória ou derrota e botão para lobby
                const waitPopup = document.querySelector('.wait-popup');
                let win = false;
                try {
                    win = sessionStorage.getItem('win') === '1';
                } catch (e) {}

                // Tenta pegar do PHP via variável global, se quiser mais precisão pode passar pelo backend
                let message = "Batalha finalizada.";
                if (typeof data.win !== "undefined") {
                    message = data.win == 1 ? "Você ganhou :D" : "Você perdeu :(";
                } else if (typeof window.win !== "undefined") {
                    message = window.win == 1 ? "Você ganhou :D" : "Você perdeu :(";
                }

                waitPopup.innerHTML = `
                    <div style="text-align:center; padding: 30px;">
                        <h2>${message}</h2>
                        <a href="lobby.php" style="text-decoration:none;">
                            <button 
                                style="
                                    margin-top:20px;
                                    font-size:1.2em;
                                    padding:10px 30px;
                                    background-color: var(--pokeblue);
                                    color: white;
                                    border: none;
                                    border-radius: 8px;
                                    font-weight: bold;
                                    cursor: pointer;
                                    transition: background-color 0.2s;
                                "
                                onmouseover="this.style.backgroundColor='#1799c2'"
                                onmouseout="this.style.backgroundColor='var(--pokeblue)'"
                            >Voltar para o Lobby</button>
                        </a>
                    </div>
                `;
                waitPopup.classList.remove('hidden');
                clearInterval(interval);
            }
        })
        .catch(error => console.error('Error fetching readiness status:', error));
}

function fetchGetPokemonParams(){
    fetch('../Controller/getPokemonParams.php')
        .then(response => response.json())
        .then(data2 => {
            document.querySelectorAll('.move-btn').forEach(b => b.disabled = false);
            if(data2.res){
                if(data2.player1 == data2.plaCode){
                    //change Img
                    document.querySelector('.img-pokemon-sprite1').src = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${data2.pokIdP1}.png`;
                    document.querySelector('.img-pokemon-sprite2').src = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${data2.pokIdP2}.png`;

                    //change Name
                    document.querySelector('.pokName1').innerHTML = data2.pokName1;
                    document.querySelector('.pokName2').innerHTML = data2.pokName2;

                    //change Attack Names
                    document.querySelector('.atk1').innerHTML = data2.atkName1P1;
                    document.querySelector('.atk2').innerHTML = data2.atkName2P1;
                    document.querySelector('.atk3').innerHTML = data2.atkName3P1;
                    document.querySelector('.atk4').innerHTML = data2.atkName4P1;

                    //change hp
                    const maxHpP1 = data2.maxHpP1;
                    const maxHpP2 = data2.maxHpP2;
                    const hpP1 = data2.hpP1;
                    const hpP2 = data2.hpP2;
                    document.querySelector('.hpP1').innerHTML = hpP1+"/"+maxHpP1;
                    document.querySelector('.hpP2').innerHTML = hpP2+"/"+maxHpP2;

                    //change %hp
                    const percHpP1Set = (hpP1*100) / maxHpP1
                    percHpP1 = Math.round(percHpP1Set);
                    //TODO: Arrumar bug que quando o pokemon morre, a barra de vida trava na cor que ele morreu
                    const barP1 = document.querySelector('.percHpP1');
                    barP1.style.width = percHpP1 + "%";
                    // Reset color first (opcional, mas seguro)
                    barP1.style.backgroundColor = "";
                    if (percHpP1 > 50) {
                        barP1.style.backgroundColor = "limegreen";
                    } else if (percHpP1 > 15 && percHpP1 <= 50) {
                        barP1.style.backgroundColor = "yellow";
                    } else if (percHpP1 <= 15) {
                        barP1.style.backgroundColor = "red";
                    }

                    const percHpP2Set = (hpP2*100) / maxHpP2
                    percHpP2 = Math.round(percHpP2Set);
                  
                    const barP2 = document.querySelector('.percHpP2');
                    barP2.style.width = percHpP2 + "%";
                    // Reset color first (opcional, mas seguro)
                    barP2.style.backgroundColor = "";
                    if (percHpP2 > 50) {
                        barP2.style.backgroundColor = "limegreen";
                    } else if (percHpP2 > 15 && percHpP2 <= 50) {
                        barP2.style.backgroundColor = "yellow";
                    } else if (percHpP2 <= 15) {
                        barP2.style.backgroundColor = "red";
                    }
                }
                if(data2.player2 == data2.plaCode){
                    document.querySelector('.img-pokemon-sprite2').src = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${data2.pokIdP1}.png`;
                    document.querySelector('.img-pokemon-sprite1').src = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${data2.pokIdP2}.png`;

                    //change Name
                    document.querySelector('.pokName2').innerHTML = data2.pokName1;
                    document.querySelector('.pokName1').innerHTML = data2.pokName2;

                    //change Attack Names
                    document.querySelector('.atk1').innerHTML = data2.atkName1P2;
                    document.querySelector('.atk2').innerHTML = data2.atkName2P2;
                    document.querySelector('.atk3').innerHTML = data2.atkName3P2;
                    document.querySelector('.atk4').innerHTML = data2.atkName4P2;

                    //change hp
                    const maxHpP2 = data2.maxHpP1;
                    const maxHpP1 = data2.maxHpP2;
                    const hpP2 = data2.hpP1;
                    const hpP1 = data2.hpP2;
                    document.querySelector('.hpP1').innerHTML = hpP1+"/"+maxHpP1;
                    document.querySelector('.hpP2').innerHTML = hpP2+"/"+maxHpP2;

                    //change %hp
                    const percHpP2Set = (hpP2*100) / maxHpP2
                    percHpP2 = Math.round(percHpP2Set);
                  
                    const barP2 = document.querySelector('.percHpP2');
                    barP2.style.width = percHpP2 + "%";
                    // Reset color first (opcional, mas seguro)
                    barP2.style.backgroundColor = "";
                    if (percHpP2 > 50) {
                        barP2.style.backgroundColor = "limegreen";
                    } else if (percHpP2 > 15 && percHpP2 <= 50) {
                        barP2.style.backgroundColor = "yellow";
                    } else if (percHpP2 <= 15) {
                        barP2.style.backgroundColor = "red";
                    }

                    const percHpP1Set = (hpP1*100) / maxHpP1
                    percHpP1 = Math.round(percHpP1Set);
                    //TODO: Arrumar bug que quando o pokemon morre, a barra de vida trava na cor que ele morreu
                    const barP1 = document.querySelector('.percHpP1');
                    barP1.style.width = percHpP1 + "%";
                    // Reset color first (opcional, mas seguro)
                    barP1.style.backgroundColor = "";
                    if (percHpP1 > 50) {
                        barP1.style.backgroundColor = "limegreen";
                    } else if (percHpP1 > 15 && percHpP1 <= 50) {
                        barP1.style.backgroundColor = "yellow";
                    } else if (percHpP1 <= 15) {
                        barP1.style.backgroundColor = "red";
                    }
                }
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