<!-- Barra Superior com autenticação -->
<div class="top-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
  <div class="search-box flex-grow-1 position-relative">
    <input type="text" id="searchInput" placeholder="Pesquise pela equipa..." autocomplete="off"> <!-- autocomplete="off" para desativar sugestões do navegador, searchInput para identificar o campo de pesquisa -->
    <div id="searchResults" class="search-results"></div> <!-- Div para mostrar resultados da pesquisa -->
  </div>
  <div class="d-flex align-items-center gap-3">
    <?php if (isLoggedIn()): ?>
      <?php $loggedUser = getLoggedUser(); // getLoggedUser() obtém os dados do utilizador logado ?>
      
      <!-- Menu do Utilizador -->
      <div class="dropdown user-dropdown-wrapper">
        <button class="user-menu-btn" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-user"></i>
          <span class="user-name"><?php echo htmlspecialchars($loggedUser['username']); ?></span>
          <i class="fas fa-chevron-down"></i> <!-- Ícone de seta para baixo -->
        </button>
        <div class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userMenuDropdown">
          <div class="user-menu-list">
            <?php if (isAdmin()): // isAdmin() verifica se o utilizador é administrador ?>
            <a href="/vivedistrital/admin/admin.php" class="user-menu-item" style="text-decoration: none;">
              <i class="fas fa-shield-alt"></i>
              <div class="user-menu-item-content">
                <strong>Painel Admin</strong>
              </div>
            </a>
            <?php endif; ?>
            
            <a href="/vivedistrital/logout.php" class="user-menu-item logout-item" style="text-decoration: none;">
              <i class="fas fa-sign-out-alt"></i>
              <div class="user-menu-item-content">
                <strong>Terminar Sessão</strong>
              </div>
            </a>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Botões de Login e Registo (não logado, daí o php else) -->
      <div class="d-flex gap-2">
        <a href="/vivedistrital/login.php" class="btn btn-primary">
          <i class="fas fa-sign-in-alt"></i> Login
        </a>
        <a href="/vivedistrital/registo.php" class="btn btn-primary">
          <i class="fas fa-user-plus"></i> Criar conta
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() { // addEventListener para garantir que o DOM está carregado antes de executar o script
    const searchInput = document.getElementById('searchInput'); // const searchInput para referenciar o campo de pesquisa
    const searchResults = document.getElementById('searchResults'); // const searchResults para referenciar a div de resultados
    let searchTimeout; // let searchTimeout para controlar o debounce (tempo de espera antes de fazer a pesquisa)
    let currentFocus = -1; // -1 indica que nenhum item está selecionado inicialmente;

    // Função para pesquisar clubes
    function pesquisarClubes(termo) { // termo é o texto pesquisado
        if (termo.length < 2) { // Se o termo tiver menos de 2 caracteres, limpar resultados e sair
            searchResults.innerHTML = '';
            searchResults.classList.remove('show');
            return;
        }

        // Fazer requisição AJAX, para que a página não precise de recarregar
        fetch('/vivedistrital/api/pesquisar-clubes.php?q=' + encodeURIComponent(termo)) // encodeURIComponent para garantir que o termo é seguro para URL
            .then(response => response.json()) // Esperar resposta em JSON
            .then(data => { // data contém os clubes retornados pela API
                mostrarResultados(data); // mostrarResultados para exibir os clubes encontrados
            })
            .catch(error => {
                console.error('Erro ao pesquisar:', error);
                searchResults.innerHTML = '';
                searchResults.classList.remove('show');
            });
    }

    // Função para mostrar resultados
    function mostrarResultados(clubes) {
        if (clubes.length === 0) { // Se não houver clubes encontrados (=== 0)
            searchResults.innerHTML = '<div class="search-item no-results">Nenhum clube encontrado</div>';
            searchResults.classList.add('show'); // show para exibir a div de resultados
            return;
        }

        let html = ''; // let html para construir o HTML dos resultados
        clubes.forEach(clube => { // Iterar cada clube retornado
            html += `
                <a href="/vivedistrital/clube-detalhe.php?id=${clube.codigo}" class="search-item">
                    <img src="/vivedistrital/${clube.logo}" alt="${clube.nome}" class="search-item-logo">
                    <div class="search-item-info">
                        <strong>${clube.nome}</strong>
                        <span>DIVISÃO DE ELITE COIMBRA</span> 
                    </div>
                </a>
            `;
        });

        searchResults.innerHTML = html; // innerHTML é usado para definir o conteúdo HTML da div de resultados
        searchResults.classList.add('show');
        currentFocus = -1; // -1 para que nenhum clube esteja já selecionado
    }

    // Event listener para input
    searchInput.addEventListener('input', function(e) { // function(e) é o evento de input, usado em JS para referenciar o evento
        clearTimeout(searchTimeout);
        const termo = e.target.value.trim(); // e.target.value.trim para obter o valor do input e remover espaços em branco
        
        // Debounce: esperar 300ms após parar de digitar
        searchTimeout = setTimeout(() => {
            pesquisarClubes(termo);
        }, 300);
    });

    // Fechar resultados ao clicar fora
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('show');
            currentFocus = -1;
        }
    });

    // Navegação por teclado (setas e Enter)
    searchInput.addEventListener('keydown', function(e) {
        const items = searchResults.getElementsByClassName('search-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentFocus++;
            addActive(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentFocus--;
            addActive(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (currentFocus > -1 && items[currentFocus]) {
                items[currentFocus].click();
            }
        } else if (e.key === 'Escape') {
            searchResults.classList.remove('show');
            currentFocus = -1;
        }
    });

    // Adicionar classe active ao item selecionado
    function addActive(items) { // addActive é usado para destacar o item selecionado
        if (!items || items.length === 0) return; // !items || items.length === 0 é uma verificação de segurança, para que não tente adicionar classe se não houver itens, || quer dizer "ou"
        removeActive(items);
        
        if (currentFocus >= items.length) currentFocus = 0; // 0 para voltar ao primeiro item
        if (currentFocus < 0) currentFocus = items.length - 1; // items.length - 1 para ir para o último item
        
        items[currentFocus].classList.add('active');
    }

    // Remover classe active de todos os itens
    function removeActive(items) {
        for (let i = 0; i < items.length; i++) { // Loop para remover a classe active de todos os itens
            items[i].classList.remove('active'); // remove active (o item deixa de estar destacado)
        }
    }

    // Mostrar resultados ao focar no input (se já tiver texto)
    searchInput.addEventListener('focus', function() { // focus é o evento quando o input é selecionado
        if (searchInput.value.trim().length >= 2) { // Se já tiver texto com 2 ou mais caracteres
            pesquisarClubes(searchInput.value.trim()); // chamar pesquisarClubes para mostrar resultados
        }
    });
});
</script>

