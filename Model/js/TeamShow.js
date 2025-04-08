document.addEventListener('DOMContentLoaded', async () => {
    const pokemonSlots = document.querySelectorAll('.pokemon-slot');

    pokemonSlots.forEach(slot => {
        const pokeId = slot.dataset.pokemonId; // Certifique-se de que o atributo data-pokemon-id está definido no HTML

        if (pokeId) {
            fetch(`https://pokeapi.co/api/v2/pokemon/${pokeId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erro ao carregar Pokémon com ID ${pokeId}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(`Dados do Pokémon ${pokeId}:`, data);
                    // Faça algo com os dados do Pokémon
                })
                .catch(error => {
                    console.error('Erro ao carregar os dados do Pokémon:', error);
                });
        } else {
            console.warn('pokeId não definido para este slot.');
        }
    });
});