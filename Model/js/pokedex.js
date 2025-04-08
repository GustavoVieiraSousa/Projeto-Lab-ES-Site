document.addEventListener('DOMContentLoaded', () => {
    const pokemonList = document.getElementById('pokemon-list2');
    const searchInput = document.getElementById('pokemon-search');
    
    const totalPokemon = 151; // Limitando aos 151 originais
    
    /**
     * Renderiza a lista completa de Pokémon
     */
    async function renderPokemonList() {
      pokemonList.innerHTML = '<div class="loading">Carregando Pokémon...</div>';
      
      try {
        // Busca todos os 151 Pokémon de uma vez
        const data = await pokeApi.getPokemonList(totalPokemon, 0);
        
        pokemonList.innerHTML = '';
        
        data.results.forEach(pokemon => {
          const pokemonCard = createPokemonCard(pokemon);
          pokemonList.appendChild(pokemonCard);
        });
        
      } catch (error) {
        pokemonList.innerHTML = '<div class="error">Erro ao carregar Pokémon. Tente novamente.</div>';
        console.error('Erro ao renderizar lista de Pokémon:', error);
      }
    }
    
    /**
     * Cria um card de Pokémon*/
    
    function createPokemonCard(pokemon) {
      const card = document.createElement('div');
      card.className = 'pokemon-card';
      card.setAttribute('data-id', pokemon.id);
      
      // Inicialmente, só adicionamos a imagem e o nome

      card.innerHTML = `
        <span class="pokemon-number">#${pokemon.id.toString().padStart(3, '0')}</span>
        <img src="${pokemon.image}" alt="${pokemon.name}" loading="lazy">
        <h3>${pokemon.name}</h3>
        <div class="types">Carregando...</div>
      `;
      
      card.addEventListener('click', () => {
        window.location.href = `pokemon.php?id=${pokemon.id}`;
      });
      
      // Carregar detalhes do Pokémon em segundo plano

      loadPokemonDetails(pokemon.id, card);
      
      return card;
    }
    
    /**
     * Carrega detalhes adicionais de um Pokémon*/

    async function loadPokemonDetails(pokemonId, card) {
      try {
        const pokemonData = await pokeApi.getPokemon(pokemonId);
        
        const typesContainer = card.querySelector('.types');
        typesContainer.innerHTML = pokemonData.types
          .map(type => `<span class="type ${type}">${type}</span>`)
          .join('');
        
      } catch (error) {
        console.error(`Erro ao carregar detalhes do Pokémon ${pokemonId}:`, error);
        const typesContainer = card.querySelector('.types');
        typesContainer.innerHTML = '<span class="error">Erro ao carregar</span>';
      }
    }
    
    /**
     * Filtra a lista de Pokémon com base no termo de busca*/

    async function searchPokemon(searchTerm) {
      pokemonList.innerHTML = '<div class="loading">Buscando Pokémon...</div>';
      
      try {
        // Busca todos os Pokémon e filtra no cliente

        const data = await pokeApi.getPokemonList(totalPokemon, 0);
        
        const filteredPokemon = data.results.filter(pokemon => 
          pokemon.name.includes(searchTerm.toLowerCase()) || 
          pokemon.id.toString() === searchTerm
        );
        
        pokemonList.innerHTML = '';
        
        if (filteredPokemon.length === 0) {
          pokemonList.innerHTML = '<div class="no-results">Nenhum Pokémon encontrado.</div>';
          return;
        }
        
        filteredPokemon.forEach(pokemon => {
          const pokemonCard = createPokemonCard(pokemon);
          pokemonList.appendChild(pokemonCard);
        });
        
      } catch (error) {
        pokemonList.innerHTML = '<div class="error">Erro ao buscar Pokémon. Tente novamente.</div>';
        console.error('Erro ao buscar Pokémon:', error);
      }
    }
    // Pesquisa
    searchInput.addEventListener('input', e => {
      const searchTerm = e.target.value.trim();
      
      if (searchTerm.length === 0) {
        renderPokemonList();
      } else if (searchTerm.length >= 2) {
        searchPokemon(searchTerm);
      }
    });
    
    // Inicializar a página com todos os Pokémon
    renderPokemonList();
  });