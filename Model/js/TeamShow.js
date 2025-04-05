document.addEventListener("DOMContentLoaded", () => {
    // Seleciona todos os slots de Pokémon
    const pokemonSlots = document.querySelectorAll(".pokemon-slot");

    // Itera sobre cada slot
    pokemonSlots.forEach(slot => {
        // Obtém os IDs do time e do Pokémon
        const teamId = slot.dataset.teamId;
        const pokemonId = slot.dataset.pokemonId;

        // Faz uma requisição à PokéAPI para obter os dados do Pokémon
        fetch(`https://pokeapi.co/api/v2/pokemon/${pokemonId}`)
            .then(response => response.json())
            .then(data => {
                // Atualiza o slot com a imagem e o nome do Pokémon
                const img = slot.querySelector("img");
                img.src = data.sprites.front_default; // Imagem do Pokémon
                img.alt = data.name; // Nome do Pokémon
                slot.title = `Time: ${teamId}, Pokémon: ${data.name}`; // Tooltip com informações
            })
            .catch(error => console.error("Erro ao carregar Pokémon:", error));
    });
});