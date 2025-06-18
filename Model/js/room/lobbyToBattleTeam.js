document.addEventListener('DOMContentLoaded', () => {
    const selectTeamButton = document.getElementById('select-team-button');

    // Função para abrir o modal de edição

    document.querySelectorAll('.select').forEach(button => {
        button.addEventListener('click', async (e) => {
            teamId = e.target.dataset.teamId;

            try {
                const editResponse = await fetch('../Controller/getBattle.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ teamId })
                });

                const editResult = await editResponse.json();
                if (!editResult.success) {
                    alert('Erro ao enviar os dados para o editBattle.php.');                    
                }

                // Cria um formulário virtual
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../Controller/editBattle.php';
        
                // Adiciona o campo com o valor da string
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'teamId';
                input.value = teamId;
                form.appendChild(input);
        
                // Adiciona o formulário ao documento e o envia
                document.body.appendChild(form);
                form.submit();

            } catch (error) {
                console.error('Erro ao enviar os dados para o editBattle.php:', error);
                alert('Erro ao enviar os dados para o editBattle.php.');
            }
        });
    });
});