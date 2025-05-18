document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('create-team-modal');
    const createTeamButton = document.getElementById('create-team-button');
    const teamNameInput = document.getElementById('team-name-input');
    const pokemonSlots = document.querySelectorAll('.team-pokemon-slot');
    const modalTitle = modal.querySelector('h2.cnt-h2');
    const pokemonPopup = document.getElementById('pokemon-popup');
    const pokemonList = document.getElementById('pokemon-list'); 
    let editingTeamId = null;
    let activeSlot = null; 

    // Função limpar pokemons
    function resetForm() {
        teamNameInput.value = ''; 
        pokemonSlots.forEach(slot => {
            slot.dataset.pokemonId = null; 
            slot.querySelector('.team-select-pokemon').style.display = 'block'; 
            const selectedPokemonDiv = slot.querySelector('.team-selected-pokemon');
            selectedPokemonDiv.innerHTML = ''; 
            slot.querySelectorAll('.team-select-attack').forEach(button => {
                button.textContent = 'Selecionar Ataque'; 
                button.dataset.attackId = null; 
                button.className = 'team-select-attack'; 
            });
        });
        createTeamButton.disabled = true; 
    }

    // Função para abrir o modal de edição
    document.querySelectorAll('.edit').forEach(button => {
        button.addEventListener('click', async (e) => {
            editingTeamId = e.target.dataset.teamId;
            modal.classList.remove('hidden');
            createTeamButton.textContent = 'Salvar Time'; 
            createTeamButton.dataset.mode = 'edit';
            modalTitle.textContent = 'Atualizar Time'; 

            try {
                const response = await fetch(`../Controller/get_team.php?teamId=${editingTeamId}`);
                const teamData = await response.json();

                if (teamData.success) {
                    // Preenche o nome do time
                    teamNameInput.value = teamData.team.teaName;

                    // Preenche os slots 
                    teamData.team.pokemons.forEach(async (pokemon, index) => {
                        const slot = pokemonSlots[index];
                        if (pokemon) {
                            const imageUrl = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.pokId}.png`;

                            // Busca o nome e os tipos
                            const pokeApiResponse = await fetch(`https://pokeapi.co/api/v2/pokemon/${pokemon.pokId}`);
                            const pokeApiData = await pokeApiResponse.json();
                            const pokemonName = pokeApiData.name.charAt(0).toUpperCase() + pokeApiData.name.slice(1);
                            const pokemonTypes = pokeApiData.types
                                .map(type => `<span class="type ${type.type.name}">${type.type.name.charAt(0).toUpperCase() + type.type.name.slice(1)}</span>`)
                                .join(' ');

                            // Formata o ID 
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

                            slot.pokemonData = {
                                stats: {
                                hp: pokeApiData.stats[0].base_stat,
                                attack: pokeApiData.stats[1].base_stat,
                                defense: pokeApiData.stats[2].base_stat,
                                specialAttack: pokeApiData.stats[3].base_stat,
                                specialDefense: pokeApiData.stats[4].base_stat,
                                speed: pokeApiData.stats[5].base_stat
                                }   
                            };

                            // Adiciona evento de clique na imagem para abrir o popup
                            const pokemonImage = slot.querySelector('.pokemon-image');
                            pokemonImage.addEventListener('click', () => {
                                activeSlot = slot; 
                                pokemonPopup.classList.remove('hidden'); 
                                loadPokemonList(); 
                            });

                            // Busca os nomes e tipos dos ataques usando a API
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
    
            const attacks = await pokeApi.getPokemonMoves(pokemonId);
            attackList.innerHTML = ''; 
    
            // Obtém os ataques já selecionados para este pokemon
            const selectedAttacks = Array.from(document.querySelectorAll('.team-select-attack'))
                .map(button => button.dataset.attackId)
                .filter(attackId => attackId !== null);
    
            // Filtra os ataques disponíveis
            const availableAttacks = attacks.filter(attack => !selectedAttacks.includes(attack.id));
    
            availableAttacks.forEach(attack => {
                const attackItem = document.createElement('button');
                attackItem.className = `attack-item type ${attack.type}`;
                attackItem.textContent = attack.name;
                attackItem.dataset.attackId = attack.id;
    
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

    // Função para carregar a lista de pokemon no popup
    async function loadPokemonList() {
        try {
            pokemonList.innerHTML = '<div class="loading">Carregando Pokémon...</div>';
            const data = await pokeApi.getPokemonList();
            pokemonList.innerHTML = ''; 

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

    // Função para selecionar um pomemon no popup
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
                activeSlot.pokemonData = pokemonData;

                // Reseta os ataques 
                const slot = activeSlot.closest('.team-pokemon-slot');
                slot.querySelectorAll('.team-select-attack').forEach(button => {
                    button.textContent = 'Selecionar Ataque';
                    button.dataset.attackId = null;
                    button.className = 'team-select-attack'; 
                });

                // Reatribui o evento de clique para a imagem
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

   // Salvar alterações
createTeamButton.addEventListener('click', async (e) => {
    if (createTeamButton.dataset.mode === 'edit') { 
        e.preventDefault();

        const teamName = teamNameInput.value.trim();
        const pokemons = [];
        const attacks = [];

        let formIsValid = true;

        pokemonSlots.forEach(slot => {
            const pokemonId = slot.dataset.pokemonId;
            const pokemonData = slot.pokemonData;

            if (!pokemonId || !pokemonData || !pokemonData.stats) {
                formIsValid = false;
                console.warn('Slot inválido ou incompleto:', slot);
                return;
            }

            function calcStat(base, iv = 31, ev = 252, level = 100, isHP = false) {
                if (isHP) {
                    return Math.floor(((base * 2 + iv + Math.floor(ev / 4)) * level) / 100) + level + 10;
                } else {
                    return Math.floor(((base * 2 + iv + Math.floor(ev / 4)) * level) / 100) + 5;
                }
            }

            pokemons.push({
                pokId: pokemonId,
                pokBasicAttack: calcStat(pokemonData.stats.attack, 31, 252, 100),
                pokSpecialAttack: calcStat(pokemonData.stats.specialAttack, 31, 252, 100),
                pokBasicDefense: calcStat(pokemonData.stats.defense, 31, 252, 100),
                pokSpecialDefense: calcStat(pokemonData.stats.specialDefense, 31, 252, 100),
                pokHp: calcStat(pokemonData.stats.hp, 31, 252, 100, true),
                pokSpeed: calcStat(pokemonData.stats.speed, 31, 252, 100)
            });

            const slotAttacks = [];
            slot.querySelectorAll('.team-select-attack').forEach(button => {
                const attackId = button.dataset.attackId;
                slotAttacks.push(attackId || null);
            });

            attacks.push(slotAttacks);
        });

        if (!formIsValid) {
            alert('Todos os Pokémon e seus dados devem estar completos antes de salvar.');
            return;
        }

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