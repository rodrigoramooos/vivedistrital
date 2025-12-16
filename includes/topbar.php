<!-- Barra Superior com autenticação -->
<div class="top-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
  <div class="search-box flex-grow-1 position-relative">
    <input type="text" id="searchInput" placeholder="Pesquise pela equipa..." autocomplete="off">
    <div id="searchResults" class="search-results"></div>
  </div>
  <div class="d-flex align-items-center gap-3">
    <?php if (isLoggedIn()): ?>
      <?php $loggedUser = getLoggedUser(); ?>
      
      <!-- Menu do Utilizador -->
      <div class="dropdown user-dropdown-wrapper">
        <button class="user-menu-btn" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-user"></i>
          <span class="user-name"><?php echo htmlspecialchars($loggedUser['username']); ?></span>
          <i class="fas fa-chevron-down"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userMenuDropdown">
          <div class="user-menu-list">
            <?php if (isAdmin()): ?>
            <a href="<?php echo url('admin.php'); ?>" class="user-menu-item" style="text-decoration: none;">
              <i class="fas fa-shield-alt"></i>
              <div class="user-menu-item-content">
                <strong>Painel Admin</strong>
              </div>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo url('logout.php'); ?>" class="user-menu-item logout-item" style="text-decoration: none;">
              <i class="fas fa-sign-out-alt"></i>
              <div class="user-menu-item-content">
                <strong>Terminar Sessão</strong>
              </div>
            </a>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Botões de Login e Registo (não logado) -->
      <div class="d-flex gap-2">
        <a href="<?php echo url('login.php'); ?>" class="btn btn-primary">
          <i class="fas fa-sign-in-alt"></i> Login
        </a>
        <a href="<?php echo url('registo.php'); ?>" class="btn btn-primary">
          <i class="fas fa-user-plus"></i> Criar conta
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;
    let currentFocus = -1;

    // Função para pesquisar clubes
    function pesquisarClubes(termo) {
        if (termo.length < 2) {
            searchResults.innerHTML = '';
            searchResults.classList.remove('show');
            return;
        }

        // Fazer requisição AJAX
        fetch('<?php echo url('api/pesquisar-clubes.php'); ?>?q=' + encodeURIComponent(termo))
            .then(response => response.json())
            .then(data => {
                mostrarResultados(data);
            })
            .catch(error => {
                console.error('Erro ao pesquisar:', error);
                searchResults.innerHTML = '';
                searchResults.classList.remove('show');
            });
    }

    // Função para mostrar resultados
    function mostrarResultados(clubes) {
        if (clubes.length === 0) {
            searchResults.innerHTML = '<div class="search-item no-results">Nenhum clube encontrado</div>';
            searchResults.classList.add('show');
            return;
        }

        let html = '';
        clubes.forEach(clube => {
            html += `
                <a href="<?php echo url('clube-detalhe.php'); ?>?id=${clube.codigo}" class="search-item">
                    <img src="<?php echo url(''); ?>${clube.logo}" alt="${clube.nome}" class="search-item-logo">
                    <div class="search-item-info">
                        <strong>${clube.nome}</strong>
                        <span>FUTEBOL, PORTUGAL</span>
                    </div>
                </a>
            `;
        });

        searchResults.innerHTML = html;
        searchResults.classList.add('show');
        currentFocus = -1;
    }

    // Event listener para input
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const termo = e.target.value.trim();
        
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
    function addActive(items) {
        if (!items || items.length === 0) return;
        removeActive(items);
        
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        
        items[currentFocus].classList.add('active');
    }

    // Remover classe active de todos os itens
    function removeActive(items) {
        for (let i = 0; i < items.length; i++) {
            items[i].classList.remove('active');
        }
    }

    // Mostrar resultados ao focar no input (se já tiver texto)
    searchInput.addEventListener('focus', function() {
        if (searchInput.value.trim().length >= 2) {
            pesquisarClubes(searchInput.value.trim());
        }
    });
});
</script>

