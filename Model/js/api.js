class PokeAPI {
    constructor() {
      this.baseUrl = 'https://pokeapi.co/api/v2';
    }
  
    /**
     * Busca um Pokémon por ID ou nome*/

    async getPokemon(idOrName) {
      try {
        const response = await fetch(`${this.baseUrl}/pokemon/${idOrName}`);
        const data = await response.json();
        
        // Formata os dados do Pokémon
        const formattedPokemon = {
          id: data.id,
          name: data.name,
          image: data.sprites.front_default,
          officialArtwork: data.sprites.other['official-artwork']?.front_default,
          types: data.types.map(type => type.type.name),
          height: data.height / 10, // Convertendo para metros
          weight: data.weight / 10, // Convertendo para kg
          abilities: data.abilities.map(ability => ability.ability.name),
          stats: {
            hp: data.stats[0].base_stat,
            attack: data.stats[1].base_stat,
            defense: data.stats[2].base_stat,
            specialAttack: data.stats[3].base_stat,
            specialDefense: data.stats[4].base_stat,
            speed: data.stats[5].base_stat,
          }
        };
        
        return formattedPokemon;
      } catch (error) {
        console.error(`Erro ao buscar Pokémon ${idOrName}:`, error);
        throw error;
      }
    }
    
    async getAllpokemon(){ const response = await fetch('https://pokeapi.co/api/v2/pokemon?limit=151');
      if (!response.ok) {
          throw new Error('Erro ao carregar os Pokémon');
      }
      const data = await response.json();
      return data.results.map((pokemon, index) => ({
          id: index + 1,
          name: pokemon.name
      }));
    }

    

    async getPokemonMoves(pokemonId) {
      const response = await fetch(`https://pokeapi.co/api/v2/pokemon/${pokemonId}`);
      if (!response.ok) {
          throw new Error('Erro ao carregar os ataques do Pokémon');
      }
      const data = await response.json();
  
      // Busca os detalhes de cada ataque para obter o tipo
      const moves = await Promise.all(
          data.moves.map(async move => {
              const moveResponse = await fetch(move.move.url);
              const moveData = await moveResponse.json();
              return {
                  id: move.move.url.split('/').filter(Boolean).pop(),
                  name: move.move.name,
                  type: moveData.type.name // Obtém o tipo do ataque
              };
          })
      );
  
      return moves;
  }
    async getAttackType(attackId) {
      try {
        const response = await fetch(`${this.baseUrl}/move/${attackId}`);
        const data = await response.json();
        return data.type.name; // Retorna o tipo do ataque
    } catch (error) {
        console.error(`Erro ao buscar o tipo do ataque ${attackId}:`, error);
        throw error;
    }
  }
    
    /**
     * Busca informações da espécie de um Pokémon*/
    
    async getPokemonSpecies(id) {
      try {
        const response = await fetch(`${this.baseUrl}/pokemon-species/${id}`);
        const data = await response.json();
        
        // Encontra a descrição em português, se disponível
        let description = '';
        for (const entry of data.flavor_text_entries) {
          if (entry.language.name === 'pt-br' || entry.language.name === 'pt') {
            description = entry.flavor_text.replace(/\f/g, ' ');
            break;
          }
        }
        
        // Se não encontrar em português, usa a descrição em inglês
        if (!description) {
          for (const entry of data.flavor_text_entries) {
            if (entry.language.name === 'en') {
              description = entry.flavor_text.replace(/\f/g, ' ');
              break;
            }
          }
        }
        
        const speciesData = {
          description,
          genus: data.genera.find(g => g.language.name === 'pt-br' || g.language.name === 'pt' || g.language.name === 'en')?.genus || '',
          habitat: data.habitat?.name || 'unknown',
          isBaby: data.is_baby,
          isLegendary: data.is_legendary,
          isMythical: data.is_mythical,
          evolutionChainUrl: data.evolution_chain?.url
        };
        
        return speciesData;
      } catch (error) {
        console.error(`Erro ao buscar espécie do Pokémon ${id}:`, error);
        throw error;
      }
    }
  
    
    async getPokemonList() {
      try {
        // Busca todos os 151 Pokémon originais de uma vez
        const response = await fetch(`${this.baseUrl}/pokemon?limit=151&offset=0`);
        const data = await response.json();
        
        // Adiciona a URL da imagem e o ID a cada Pokémon na lista
        const enhancedResults = data.results.map(pokemon => {
          const id = pokemon.url.split('/').filter(Boolean).pop();
          return {
            ...pokemon,
            id: parseInt(id),
            image: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${id}.png`
          };
        });
        
        return {
          ...data,
          results: enhancedResults
        };
      } catch (error) {
        console.error('Erro ao buscar lista de Pokémon:', error);
        throw error;
      }
    }

    
    
  }
  
  // Exporta uma instância única da API
  const pokeApi = new PokeAPI();