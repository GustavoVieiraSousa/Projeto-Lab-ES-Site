document.addEventListener('DOMContentLoaded', async () => {
  // Pegar o ID do Pokémon da URL
  const urlParams = new URLSearchParams(window.location.search);
  const pokemonId = urlParams.get('id');
  
  if (!pokemonId) {
    window.location.href = 'pokedex.html';
    return;
  }
  
  const pokemonDetails = document.getElementById('pokemon-details');
  
  
  async function loadPokemonDetails(id) {
    try {
     
      pokemonDetails.innerHTML = '<div class="loading">Carregando detalhes do Pokémon...</div>';
      
      // Buscar dados do Pokémon e da espécie

      const pokemon = await pokeApi.getPokemon(id);
      const species = await pokeApi.getPokemonSpecies(id);
  
      // Atualizar o título da página

      document.title = `${pokemon.name.charAt(0).toUpperCase() + pokemon.name.slice(1)} - PokéParty`;
      
      // Calcular estatísticas para nível 100

      const calculateLv100Stat = (baseStat) => {
        return Math.floor(((2 * baseStat + 31 + 63) * 100) / 100 + 5);
      };
      
      const calculateHP = (baseHP) => {
        return Math.floor(((2 * baseHP + 31 + 63) * 100) / 100 + 100 + 10);
      };
      
      // Estrutura HTML dos detalhes

      const detailsHTML = `
        <div class="pokemon-details-card">
          <div class="pokemon-header">
            <h2 class="pokemon-name">${pokemon.name.charAt(0).toUpperCase() + pokemon.name.slice(1)}</h2>
            <span class="pokemon-number">#${pokemon.id.toString().padStart(3, '0')}</span>
          </div>
          
          <div class="pokemon-info-grid">
            <div class="pokemon-image">
              <img src="${pokemon.officialArtwork || pokemon.image}" alt="${pokemon.name}">
              <div class="pokemon-types">
                ${pokemon.types.map(type => `<span class="type ${type}">${type}</span>`).join('')}
              </div>
            </div>
            
            <div class="pokemon-info">
              <div class="pokemon-description">
                <h3>Descrição</h3>
                <p>${species.description || 'Nenhuma descrição disponível.'}</p>
              </div>
              
              <div class="pokemon-attributes">
                <div class="attribute">
                  <span class="attribute-label">Altura:</span>
                  <span class="attribute-value">${pokemon.height} m</span>
                </div>
                <div class="attribute">
                  <span class="attribute-label">Peso:</span>
                  <span class="attribute-value">${pokemon.weight} kg</span>
                </div>
                <div class="attribute">
                  <span class="attribute-label">Habilidades:</span>
                  <span class="attribute-value">${pokemon.abilities.join(', ')}</span>
                </div>
                <div class="attribute">
                  <span class="attribute-label">Categoria:</span>
                  <span class="attribute-value">${species.genus || 'Desconhecido'}</span>
                </div>
                <div class="attribute">
                  <span class="attribute-label">Habitat:</span>
                  <span class="attribute-value">${species.habitat || 'Desconhecido'}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="pokemon-stats">
            <h3>Estatísticas (Nível 100)</h3>
            <div class="stats-grid">
              <div class="stat-item">
                <div class="stat-label">HP</div>
                <div class="stat-bar">
                  <div class="stat-fill hp" style="width: ${Math.min(100, pokemon.stats.hp / 2)}%"></div>
                </div>
                <div class="stat-value">${calculateHP(pokemon.stats.hp)}</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Ataque</div>
                <div class="stat-bar">
                  <div class="stat-fill attack" style="width: ${Math.min(100, pokemon.stats.attack / 2)}%"></div>
                </div>
                <div class="stat-value">${calculateLv100Stat(pokemon.stats.attack)}</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Defesa</div>
                <div class="stat-bar">
                  <div class="stat-fill defense" style="width: ${Math.min(100, pokemon.stats.defense / 2)}%"></div>
                </div>
                <div class="stat-value">${calculateLv100Stat(pokemon.stats.defense)}</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Ataque Esp.</div>
                <div class="stat-bar">
                  <div class="stat-fill special-attack" style="width: ${Math.min(100, pokemon.stats.specialAttack / 2)}%"></div>
                </div>
                <div class="stat-value">${calculateLv100Stat(pokemon.stats.specialAttack)}</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Defesa Esp.</div>
                <div class="stat-bar">
                  <div class="stat-fill special-defense" style="width: ${Math.min(100, pokemon.stats.specialDefense / 2)}%"></div>
                </div>
                <div class="stat-value">${calculateLv100Stat(pokemon.stats.specialDefense)}</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Velocidade</div>
                <div class="stat-bar">
                  <div class="stat-fill speed" style="width: ${Math.min(100, pokemon.stats.speed / 2)}%"></div>
                </div>
                <div class="stat-value">${calculateLv100Stat(pokemon.stats.speed)}</div>
              </div>
            </div>
          </div>
          
          <div class="pokemon-actions">
            <button class="btn back-to-pokedex" onclick="location.href='pokedex.html'">Voltar para Pokédex</button>
          </div>
        </div>
      `;
      
      // Exibir os detalhes

      pokemonDetails.innerHTML = detailsHTML;
      
    } catch (error) {
      console.error('Erro ao carregar detalhes do Pokémon:', error);
      pokemonDetails.innerHTML = '<div class="error">Erro ao carregar detalhes do Pokémon. <a href="/View/pokedex.html">Voltar para Pokédex</a></div>';
    }
  }
  
  // Carregar detalhes do Pokémon
  
  loadPokemonDetails(pokemonId);
});
