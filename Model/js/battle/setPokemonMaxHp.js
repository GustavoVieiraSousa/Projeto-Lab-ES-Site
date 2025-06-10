fetch('../Controller/setPokemonMaxHp.php')
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
