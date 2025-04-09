document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('create-team-modal');
    const createTeamButton = document.getElementById('create-team-button');
    const teamNameInput = document.getElementById('team-name-input');
    const pokemonSlots = document.querySelectorAll('.team-pokemon-slot');
    const modalTitle = modal.querySelector('h2.cnt-h2');
    const pokemonPopup = document.getElementById('pokemon-popup'); // Popup de seleção de Pokémon
    const pokemonList = document.getElementById('pokemon-list'); // Lista de Pokémon no popup
    let editingTeamId = null;
    let activeSlot = null; // Slot ativo para substituição

    // Função para limpar os slots e redefinir o formulário
    function resetForm() {
        teamNameInput.value = ''; // Limpa o nome do time
        pokemonSlots.forEach(slot => {
            slot.dataset.pokemonId = null; // Remove o ID do Pokémon do slot
            slot.querySelector('.team-select-pokemon').style.display = 'block'; // Mostra o botão "Selecionar Pokémon"
            const selectedPokemonDiv = slot.querySelector('.team-selected-pokemon');
            selectedPokemonDiv.innerHTML = ''; // Limpa o conteúdo do slot
            slot.querySelectorAll('.team-select-attack').forEach(button => {
                button.textContent = 'Selecionar Ataque'; // Redefine o texto do botão de ataque
                button.dataset.attackId = null; // Remove o ID do ataque
                button.className = 'team-select-attack'; // Remove classes de tipo
            });
        });
        createTeamButton.disabled = true; // Desabilita o botão "Criar Time"
    }

    // Função para abrir o modal de edição
    document.querySelectorAll('.edit').forEach(button => {
        button.addEventListener('click', async (e) => {
            editingTeamId = e.target.dataset.teamId;
            modal.classList.remove('hidden');
            createTeamButton.textContent = 'Salvar Time'; // Altera o texto do botão
            createTeamButton.dataset.mode = 'edit'; // Define o modo como "edit"
            modalTitle.textContent = 'Atualizar Time'; // Altera o título do modal

            try {
                const response = await fetch(`../Controller/get_team.php?teamId=${editingTeamId}`);
                const teamData = await response.json();

                if (teamData.success) {
                    // Preenche o nome do time
                    teamNameInput.value = teamData.team.teaName;

                    // Preenche os slots de Pokémon com os nomes, imagens, tipos e ataques
                    teamData.team.pokemons.forEach(async (pokemon, index) => {
                        const slot = pokemonSlots[index];
                        if (pokemon) {
                            const imageUrl = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.pokId}.png`;

                            // Busca o nome e os tipos do Pokémon usando a PokéAPI
                            const pokeApiResponse = await fetch(`https://pokeapi.co/api/v2/pokemon/${pokemon.pokId}`);
                            const pokeApiData = await pokeApiResponse.json();
                            const pokemonName = pokeApiData.name.charAt(0).toUpperCase() + pokeApiData.name.slice(1);
                            const pokemonTypes = pokeApiData.types
                                .map(type => `<span class="type ${type.type.name}">${type.type.name.charAt(0).toUpperCase() + type.type.name.slice(1)}</span>`)
                                .join(' ');

                            // Formata o ID da Pokédex no formato #000
                            const formattedId = `#${pokemon.pokId.toString().padStart(3, '0')}`;

                            slot.dataset.pokemonId = pokemon.pokId;
                            slot.querySelector('.team-selected-pokemon').innerHTML = `
                                <div class="pokemon-info">
                                    <img src="${imageUrl}" alt="Pokémon ${pokemonName}" class="pokemon-image">
                                    <span class="pokemon-number2">${formattedId}</span>
                                    <p class="pokemon-name">${pokemonName}</p>
                                    <div class="pokemon-types">${pokemonTypes}</div>
                                </div>
                            `;

                            // Adiciona evento de clique à imagem para abrir o popup
                            const pokemonImage = slot.querySelector('.pokemon-image');
                            pokemonImage.addEventListener('click', () => {
                                activeSlot = slot; // Define o slot ativo
                                pokemonPopup.classList.remove('hidden'); // Exibe o popup
                                loadPokemonList(); // Carrega a lista de Pokémon no popup
                            });

                            // Busca os nomes e tipos dos ataques usando a PokéAPI
                            const attackIds = [pokemon.pokAtk1, pokemon.pokAtk2, pokemon.pokAtk3, pokemon.pokAtk4];
                            const attackDetails = await Promise.all(
                                attackIds.map(async (attackId) => {
                                    if (attackId) {
                                        const attackResponse = await fetch(`https://pokeapi.co/api/v2/move/${attackId}`);
                                        const attackData = await attackResponse.json();
                                        return {
                                            name: attackData.name.charAt(0).toUpperCase() + attackData.name.slice(1),
                                            type: attackData.type.name.toLowerCase(),
                                        };
                                    }
                                    return { name: 'Nenhum', type: 'unknown' };
                                })
                            );

                            // Preenche os ataques no slot
                            const attackButtons = slot.querySelectorAll('.team-select-attack');
                            attackButtons.forEach((button, i) => {
                                button.textContent = attackDetails[i].name;
                                button.dataset.attackId = attackIds[i];
                                button.className = `team-select-attack type ${attackDetails[i].type}`;
                            });

                            slot.querySelector('.team-select-pokemon').style.display = 'none';
                        } else {
                            slot.querySelector('.team-selected-pokemon').innerHTML = '';
                            slot.querySelector('.team-select-pokemon').style.display = 'block';
                        }
                    });
                } else {
                    alert('Erro ao carregar os dados do time.');
                }
            } catch (error) {
                console.error('Erro ao carregar os dados do time:', error);
                alert('Erro ao carregar os dados do time.');
            }
        });
    });

    async function loadAttackList(pokemonId) {
        try {
            attackList.innerHTML = '<div class="loading">Carregando Ataques...</div>';
    
            // Busca os ataques do Pokémon
            const attacks = await pokeApi.getPokemonMoves(pokemonId);
            attackList.innerHTML = ''; // Limpa o estado de carregamento
    
            // Obtém os ataques já selecionados para este Pokémon
            const selectedAttacks = Array.from(document.querySelectorAll('.team-select-attack'))
                .map(button => button.dataset.attackId)
                .filter(attackId => attackId !== null);
    
            // Filtra os ataques disponíveis, removendo os já selecionados
            const availableAttacks = attacks.filter(attack => !selectedAttacks.includes(attack.id));
    
            availableAttacks.forEach(attack => {
                const attackItem = document.createElement('button');
                attackItem.className = `attack-item type ${attack.type}`;
                attackItem.textContent = attack.name;
                attackItem.dataset.attackId = attack.id;
    
                // Adiciona evento de clique para selecionar o ataque
                attackItem.addEventListener('click', () => {
                    selectAttack(attack.name, attack.id);
                });
    
                attackList.appendChild(attackItem);
            });
    
            if (availableAttacks.length === 0) {
                attackList.innerHTML = '<div class="no-results">Nenhum ataque disponível.</div>';
            }
        } catch (error) {
            attackList.innerHTML = '<div class="error">Erro ao carregar ataques. Tente novamente.</div>';
            console.error('Erro ao carregar a lista de ataques:', error);
        }
    }

    // Função para carregar a lista de Pokémon no popup
    async function loadPokemonList() {
        try {
            pokemonList.innerHTML = '<div class="loading">Carregando Pokémon...</div>';
            const data = await pokeApi.getPokemonList();
            pokemonList.innerHTML = ''; // Limpa o estado de carregamento

            for (const pokemon of data.results) {
                const pokemonData = await pokeApi.getPokemon(pokemon.id);
                const pokemonCard = document.createElement('div');
                pokemonCard.className = 'pokemon-card2';
                pokemonCard.innerHTML = `
                    <span class="pokemon-number2">#${pokemonData.id.toString().padStart(3, '0')}</span>
                    <img src="${pokemonData.image}" alt="${pokemonData.name}">
                    <h3>${pokemonData.name.charAt(0).toUpperCase() + pokemonData.name.slice(1)}</h3>
                    <div class="pokemon-types">
                        ${pokemonData.types.map(type => `<span class="type ${type}">${type}</span>`).join('')}
                    </div>
                    <div class="pokemon-stats">
                        <p>HP: ${pokemonData.stats.hp}</p>
                        <p>Ataque: ${pokemonData.stats.attack}</p>
                        <p>Defesa: ${pokemonData.stats.defense}</p>
                        <p>Velocidade: ${pokemonData.stats.speed}</p>
                    </div>
                `;
                pokemonCard.addEventListener('click', () => {
                    selectPokemon(pokemonData.id, pokemonData.name, pokemonData.image);
                });
                pokemonList.appendChild(pokemonCard);
            }
        } catch (error) {
            console.error('Erro ao carregar a lista de Pokémon:', error);
            pokemonList.innerHTML = '<div class="error">Erro ao carregar Pokémon. Tente novamente.</div>';
        }
    }

    // Função para selecionar um Pokémon no popup
    function selectPokemon(pokemonId, pokemonName, pokemonImage) {
        if (activeSlot) {
            pokeApi.getPokemon(pokemonId).then(pokemonData => {
                const pokemonTypesHTML = pokemonData.types
                    .map(type => `<span class="type ${type}">${type}</span>`)
                    .join('');

                activeSlot.querySelector('.team-selected-pokemon').innerHTML = `
                    <div class="pokemon-info">
                        <img src="${pokemonImage}" alt="Pokémon ${pokemonName}" class="pokemon-image">
                        <span class="pokemon-number2">#${pokemonId.toString().padStart(3, '0')}</span>
                        <p class="pokemon-name">${pokemonName}</p>
                        <div class="pokemon-types">${pokemonTypesHTML}</div>
                    </div>
                `;
                activeSlot.dataset.pokemonId = pokemonId; 

                // Reseta os ataques para "Selecionar Ataque"
                const slot = activeSlot.closest('.team-pokemon-slot');
                slot.querySelectorAll('.team-select-attack').forEach(button => {
                    button.textContent = 'Selecionar Ataque';
                    button.dataset.attackId = null;
                    button.className = 'team-select-attack'; // Remove classes de tipo
                });

                // Reatribui o evento de clique à nova imagem
                const pokemonImageElement = activeSlot.querySelector('.pokemon-image');
                pokemonImageElement.addEventListener('click', () => {
                    activeSlot = activeSlot; 
                    pokemonPopup.classList.remove('hidden'); 
                    loadPokemonList(); 
                });

                pokemonPopup.classList.add('hidden');
            });
        }
    }

    // Salvar alterações no time
    createTeamButton.addEventListener('click', async (e) => {
        if (createTeamButton.dataset.mode === 'edit') { 
            e.preventDefault();

            const teamName = teamNameInput.value.trim();
            const pokemons = [];
            const attacks = [];

            pokemonSlots.forEach(slot => {
                const pokemonId = slot.dataset.pokemonId;
                if (pokemonId) {
                    pokemons.push(pokemonId);

                    const slotAttacks = [];
                    slot.querySelectorAll('.team-select-attack').forEach(button => {
                        const attackId = button.dataset.attackId;
                        slotAttacks.push(attackId || null);
                    });

                    attacks.push(slotAttacks);
                }
            });

            if (pokemons.length !== 6 || attacks.some(a => a.length !== 4)) {
                alert('Você deve selecionar exatamente 6 Pokémon e 4 ataques para cada um.');
                return;
            }

            try {
                const response = await fetch('../Controller/edit_team.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ teamId: editingTeamId, teamName, pokemons, attacks })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Time atualizado com sucesso!');
                    location.reload();
                } else {
                    alert(result.error || 'Erro ao atualizar o time.');
                }
            } catch (error) {
                console.error('Erro ao salvar o time:', error);
                alert('Erro ao salvar o time. Tente novamente.');
            }
        }
    });

    // Fecha o popup ao clicar no botão de fechar
    document.querySelectorAll('.close-popup').forEach(button => {
        button.addEventListener('click', () => {
            const popup = button.closest('.popup');
            popup.classList.add('hidden');
        });
    });
});