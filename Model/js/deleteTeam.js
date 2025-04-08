document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete').forEach(button => {
        button.addEventListener('click', async (e) => {
            const teamElement = e.target.closest('.team-grid');
            const teamId = teamElement.id.split('-')[1]; // Obtém o ID do time

            if (!confirm('Tem certeza de que deseja excluir este time? Esta ação não pode ser desfeita.')) {
                return;
            }

            try {
                console.log('Enviando teamId:', teamId); // Log para depuração
                const response = await fetch('../Controller/delete_team.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ teamId })
                });

                const result = await response.json();
                console.log('Resposta do servidor:', result); // Log para depuração
                if (result.success) {
                    alert('Time excluído com sucesso!');
                    teamElement.remove(); // Remove o time da interface
                } else {
                    alert(result.error || 'Erro ao excluir time.');
                }
            } catch (error) {
                console.error('Erro ao excluir time:', error);
                alert('Erro ao excluir time. Tente novamente.');
            }
        });
    });
});