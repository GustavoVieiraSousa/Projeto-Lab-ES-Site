document.addEventListener('DOMContentLoaded', async () => {
    const modal = document.getElementById('create-team-modal');
    const addTeamButton = document.getElementById('add-team-button');
    const cancelTeamButton = document.getElementById('cancel-team-button');
    const pokemonPopup = document.getElementById('pokemon-popup');
    const pokemonList = document.getElementById('pokemon-list');
    const attackPopup = document.getElementById('attack-popup');
    const attackList = document.getElementById('attack-list');
    const createTeamButton = document.getElementById('create-team-button');
    const selectedPokemons = new Array(6).fill(null);
    let activeAttackButton = null;

    // Função para limpar os slots e o array de Pokémon selecionados
    function resetSlots() {
        selectedPokemons.fill(null);
        document.querySelectorAll('.team-pokemon-slot').forEach(slot => {
            slot.querySelector('.team-select-pokemon').style.display = 'block';
            const selectedPokemonDiv = slot.querySelector('.team-selected-pokemon');
            selectedPokemonDiv.innerHTML = '';
            slot.querySelectorAll('.team-select-attack').forEach(button => {
                button.textContent = 'Selecionar Ataque';
                button.dataset.attackId = null;
            });
        });
        updateCreateTeamButtonState();
    }

    // Fecha o popup ao clicar no botão de sair
    document.querySelectorAll('.close-popup').forEach(button => {
        button.addEventListener('click', () => {
            const popup = button.closest('.popup');
            popup.classList.add('hidden'); // Oculta o popup
        });
    });

    addTeamButton.addEventListener('click', () => {
        modal.classList.remove('hidden');
        createTeamButton.textContent = 'Criar Time'; // Define o texto do botão
        createTeamButton.dataset.mode = 'create'; // Define o modo como "create"
        resetSlots(); // Limpa os slots para um novo time
    });

    // Fecha o modal ao clicar no botão "Cancelar"
    cancelTeamButton.addEventListener('click', () => {
        resetSlots();
        modal.classList.add('hidden');
    });

    // Abre o popup de seleção de Pokémon
    document.querySelectorAll('.team-select-pokemon').forEach(button => {
        button.addEventListener('click', (e) => {
            const slot = e.target.dataset.slot;
            const selectedPokemonDiv = document.getElementById(`selected-pokemon-${slot}`);
            selectedPokemonDiv.classList.add('active'); // Marca o slot como ativo
            pokemonPopup.classList.remove('hidden'); // Exibe o popup
        });
    });

    document.getElementById('team-name-input').addEventListener('input', updateCreateTeamButtonState);

    // Carrega a lista de Pokémon ao carregar a página
    async function loadPokemonList() {
        try {
            pokemonList.innerHTML = '<div class="loading">Carregando Pokémon...</div>';
            const data = await pokeApi.getPokemonList();
            pokemonList.innerHTML = ''; 
            for (const pokemon of data.results) {
                const pokemonData = await pokeApi.getPokemon(pokemon.id);
                const pokemonCard = createPokemonCard(pokemonData);
                pokemonList.appendChild(pokemonCard);
            }
        } catch (error) {
            pokemonList.innerHTML = '<div class="error">Erro ao carregar Pokémon. Tente novamente.</div>';
            console.error('Erro ao carregar a lista de Pokémon:', error);
        }
    }

    // Função para criar um card de Pokémon
    function createPokemonCard(pokemon) {
        const card = document.createElement('div');
        card.className = 'pokemon-card2';
        card.setAttribute('data-id', pokemon.id);
    
        // Adiciona as informações do Pokémon, incluindo os tipos e status
        card.innerHTML = `
            <div class="pokemon-info">
                <span class="pokemon-number2">#${pokemon.id.toString().padStart(3, '0')}</span>
                <img src="${pokemon.image}" alt="${pokemon.name}" loading="lazy">
                <h3>${pokemon.name}</h3>
                <div class="pokemon-types">
                    ${pokemon.types.map(type => `<span class="type ${type}">${type}</span>`).join('')}
                </div>
                <div class="pokemon-stats">
                    <p>HP: ${pokemon.stats.hp}</p>
                    <p>Ataque: ${pokemon.stats.attack}</p>
                    <p>Defesa: ${pokemon.stats.defense}</p>
                    <p>Velocidade: ${pokemon.stats.speed}</p>
                </div>
            </div>
        `;
    
        // Adiciona o evento de clique para selecionar o Pokémon
        card.addEventListener('click', () => {
            selectPokemon(pokemon.id, pokemon.name, pokemon.image);
            updateCreateTeamButtonState();
        });
    
        return card;
    }
    // Seleciona um Pokémon e adiciona ao slot
    function selectPokemon(pokemonId, pokemonName, pokemonImage) {
        const activeSlot = document.querySelector('.team-selected-pokemon.active');
        if (activeSlot) {
            // Obter os tipos do Pokémon
            pokeApi.getPokemon(pokemonId).then(pokemonData => {
                const typesHTML = pokemonData.types
                    .map(type => `<span class="type ${type}">${type}</span>`)
                    .join('');

                // Atualizar o slot com os tipos e informações do Pokémon
                activeSlot.innerHTML = `
                    <div class="pokemon-info">
                        <img src="${pokemonImage}" alt="${pokemonName}" class="pokemon-image">
                        <div class="pokemon-details">
                            <span class="pokemon-lumber">#${pokemonId.toString().padStart(3, '0')}</span>
                            <p class="pokemon-name">${pokemonName}</p>
                            <div class="pokemon-types">${typesHTML}</div>
                        </div>
                    </div>
                `;

                activeSlot.classList.remove('active'); // Remove a classe ativa após selecionar
                const slot = activeSlot.closest('.team-pokemon-slot');
                slot.dataset.pokemonId = pokemonId; // Salva o ID do Pokémon no slot

                // Esconde o botão "Selecionar Pokémon"
                const selectButton = slot.querySelector('.team-select-pokemon');
                if (selectButton) {
                    selectButton.style.display = 'none';
                }

                // Permite reabrir o popup ao clicar na imagem ou no slot
                slot.querySelector('.pokemon-info').addEventListener('click', () => {
                    activeSlot.classList.add('active'); // Marca o slot como ativo novamente
                    pokemonPopup.classList.remove('hidden'); // Reabre o popup
                });

                pokemonPopup.classList.add('hidden'); // Oculta o popup
            });
        }
    }

    // Abre o popup de seleção de ataques
    document.querySelectorAll('.team-select-attack').forEach(button => {
        button.addEventListener('click', async (e) => {
            const slot = e.target.closest('.team-pokemon-slot');
            const pokemonId = slot.dataset.pokemonId;

            if (!pokemonId) {
                alert('Selecione um Pokémon antes de escolher ataques.');
                return;
            }

            activeAttackButton = e.target; // Define o botão ativo
            attackPopup.classList.remove('hidden'); // Exibe o popup de ataques
            await loadAttackList(pokemonId); // Carrega a lista de ataques
        });
    });

    // Carrega a lista de ataques no popup
    async function loadAttackList(pokemonId) {
        try {
            attackList.innerHTML = '<div class="loading">Carregando Ataques...</div>';

            // Busca os ataques do Pokémon
            const attacks = await pokeApi.getPokemonMoves(pokemonId);
            attackList.innerHTML = ''; // Limpa o estado de carregamento

            attacks.forEach(attack => {
                const attackItem = document.createElement('button');
                attackItem.className = `attack-item type ${attack.type}`; // Adiciona a classe de tipo
                attackItem.textContent = attack.name;
                attackItem.dataset.attackId = attack.id;

                // Adiciona evento de clique para selecionar o ataque
                attackItem.addEventListener('click', () => {
                    selectAttack(attack.name, attack.id);
                    updateCreateTeamButtonState();
                });

                attackList.appendChild(attackItem);
            });
        } catch (error) {
            attackList.innerHTML = '<div class="error">Erro ao carregar ataques. Tente novamente.</div>';
            console.error('Erro ao carregar a lista de ataques:', error);
        }
    }

    // Seleciona um ataque e atualiza o botão
    function selectAttack(attackName, attackId) {
        if (activeAttackButton) {
            activeAttackButton.textContent = attackName;
            pokeApi.getAttackType(attackId).then(attackType => {
                activeAttackButton.className = `team-select-attack type ${attackType}`;
                activeAttackButton.dataset.attackId = attackId; // Salva o ID do ataque no botão
                attackPopup.classList.add('hidden'); // Oculta o popup
            }).catch(error => {
                console.error('Erro ao buscar o tipo do ataque:', error);
                alert('Erro ao selecionar o ataque. Tente novamente.');
            });
        }
    }

    // Habilita ou desabilita o botão "Criar Time"
    function updateCreateTeamButtonState() {
        const allSlotsFilled = Array.from(document.querySelectorAll('.team-pokemon-slot')).every(slot => {
            return slot.dataset.pokemonId; // Verifica se todos os slots têm um Pokémon selecionado
        });

        const allAttacksSelected = Array.from(document.querySelectorAll('.team-pokemon-slot')).every(slot => {
            const attackButtons = slot.querySelectorAll('.team-select-attack');
            return Array.from(attackButtons).every(button => button.dataset.attackId); // Verifica se todos os ataques foram selecionados
        });

        const teamName = document.getElementById('team-name-input').value.trim(); // Verifica se o nome do time foi preenchido

        // Habilita o botão apenas se todos os slots, ataques e o nome do time estiverem preenchidos
        createTeamButton.disabled = !(allSlotsFilled && allAttacksSelected && teamName.length > 0);
    }

    
    // Coleta os dados do time e envia ao servidor
    document.getElementById('create-team-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const teamName = document.getElementById('team-name-input').value;
        const pokemons = [];
        const attacks = [];

        document.querySelectorAll('.team-pokemon-slot').forEach(slot => {
            const pokemonId = slot.dataset.pokemonId;
            if (pokemonId) {
                pokemons.push(pokemonId);

                const slotAttacks = [];
                slot.querySelectorAll('.team-select-attack').forEach(button => {
                    const attackId = button.dataset.attackId;
                    slotAttacks.push(attackId || null); // Adiciona o ID do ataque ou null
                });

                attacks.push(slotAttacks);
            }
        });

        if (pokemons.length !== 6 || attacks.some(a => a.length !== 4)) {
            alert('Você deve selecionar exatamente 6 Pokémon e 4 ataques para cada um.');
            return;
        }

        try {
            const response = await fetch('../Controller/add_team.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ teamName, pokemons, attacks })
            });

            const result = await response.json();
            if (result.success) {
                alert('Time criado com sucesso!');
                location.reload(); // Recarrega a página
            } else {
                console.error('Erro do servidor:', result.error);
                alert(result.error || 'Erro ao criar time.');
            }
        } catch (error) {
            console.error('Erro ao enviar dados do time:', error);
            alert('Erro ao criar time. Tente novamente.');
        }
    });

    // Inicializa a lista de Pokémon
    loadPokemonList();
});