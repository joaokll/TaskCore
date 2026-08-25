<?php
session_start();

// Caminho para o seu "banco de dados" JSON baseado no seu projeto
$jsonFile = __DIR__ . '/../config/db.json';

// Proteção de Página: Bloqueia acesso direto sem login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Carrega os dados atuais do arquivo JSON
$dadosSistema = [];
if (file_exists($jsonFile)) {
    $dadosSistema = json_decode(file_get_contents($jsonFile), true) ?? [];
}

// Garante que a chave 'tarefas' exista no JSON
if (!isset($dadosSistema['tarefas'])) {
    $dadosSistema['tarefas'] = [];
}

// --- 1. AÇÃO: CREATE (Inserir Tarefa no JSON) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $periodo = $_POST['periodo_reiniciar'];

    if (!empty($titulo)) {
        // Cria uma nova tarefa com uma ID única baseada no timestamp
        $novaTarefa = [
            'id' => uniqid(),
            'titulo' => $titulo,
            'descricao' => $descricao,
            'concluida' => 0, // 0 = Não concluída, 1 = Concluída
            'periodo_reiniciar' => $periodo,
            'usuario_id' => $usuario_id
        ];

        // Adiciona ao array e salva no arquivo JSON
        $dadosSistema['tarefas'][] = $novaTarefa;
        file_put_contents($jsonFile, json_encode($dadosSistema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        header("Location: dashboard.php");
        exit;
    }
}

// --- 2. AÇÃO: ALTERAR STATUS (Marcar / Desmarcar como Concluído) ---
if (isset($_GET['toggle_id'])) {
    $task_id = $_GET['toggle_id'];

    foreach ($dadosSistema['tarefas'] as &$t) {
        // Valida se a tarefa é da ID clicada e pertence ao usuário logado
        if ($t['id'] === $task_id && $t['usuario_id'] === $usuario_id) {
            $t['concluida'] = ($t['concluida'] == 1) ? 0 : 1;
            break;
        }
    }
    
    file_put_contents($jsonFile, json_encode($dadosSistema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: dashboard.php?ordenar_por=" . ($_GET['ordenar_por'] ?? 'nome'));
    exit;
}

// --- 3. FILTRAGEM: PEGAR APENAS TAREFAS DO USUÁRIO LOGADO ---
$tarefasUsuario = array_filter($dadosSistema['tarefas'], function($t) use ($usuario_id) {
    return $t['usuario_id'] === $usuario_id;
});

// --- 4. LÓGICA DE ORDENAÇÃO (Filtros por Nome ou Período) ---
$ordenacao = $_GET['ordenar_por'] ?? 'nome';

usort($tarefasUsuario, function($a, $b) use ($ordenacao) {
    if ($ordenacao === 'periodo') {
        // Define a ordem de peso dos períodos para a ordenação
        $pesos = ['Diário' => 1, 'Semanal' => 2, 'Mensal' => 3, 'Único' => 4];
        $pesoA = $pesos[$a['periodo_reiniciar']] ?? 4;
        $pesoB = $pesos[$b['periodo_reiniciar']] ?? 4;
        return $pesoA <=> $pesoB;
    } else {
        // Ordenação padrão alfabética por Título
        return strcasecmp($a['titulo'], $b['titulo']);
    }
});
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - TaskCore</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <!-- SIDEBAR (Menu Lateral Estilo Notion) -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <span class="workspace-title">🏢 Acme Inc.</span>
            <button class="toggle-btn" onclick="toggleSidebar()">«</button>
        </div>
        
        <nav class="sidebar-menu">
            <a href="dashboard.php" class="active">🏠 Home do Sistema</a>
            <hr class="divider">
            <p class="menu-label">Organizar por:</p>
            <a href="dashboard.php?ordenar_por=nome" class="<?php echo $ordenacao === 'nome' ? 'filter-active' : ''; ?>">🔤 Nome da Atividade</a>
            <a href="dashboard.php?ordenar_por=periodo" class="<?php echo $ordenacao === 'periodo' ? 'filter-active' : ''; ?>">⏱️ Período de Reinício</a>
            <hr class="divider">
            <a href="logout.php" class="logout-link">🚪 Sair da Conta</a>
        </nav>
    </aside>

    <!-- BOTÃO PARA REABRIR A SIDEBAR SE ESTIVER FECHADA -->
    <button id="open-sidebar-btn" class="open-btn" onclick="toggleSidebar()">»</button>

    <!-- CONTEÚDO PRINCIPAL DA PÁGINA -->
    <main id="main-content" class="main-content">
        <header class="content-header">
            <h1>Company Home</h1>
            <p>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?></strong>!</p>
        </header>

        <!-- FORMULÁRIO DE CADASTRO DE TAREFAS -->
        <section class="card-form">
            <h3>✨ Create a new page / Task</h3>
            <form action="dashboard.php?ordenar_por=<?php echo $ordenacao; ?>" method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label>Título da Atividade:</label>
                    <input type="text" name="titulo" placeholder="Ex: Mission, Vision and Values" required>
                </div>

                <div class="form-group">
                    <label>Descrição / Detalhes:</label>
                    <textarea name="descricao" placeholder="Must reads for our company..."></textarea>
                </div>

                <div class="form-group">
                    <label>Período para Reiniciar:</label>
                    <select name="periodo_reiniciar">
                        <option value="Único">Único (Não repete)</option>
                        <option value="Diário">Diário</option>
                        <option value="Semanal">Semanal</option>
                        <option value="Mensal">Mensal</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary">Criar Tarefa</button>
            </form>
        </section>

        <!-- TABELA DE EXIBIÇÃO ESTILO NOTION -->
        <section class="tasks-section">
            <h3>📋 Atividades Recentes</h3>
            <table class="notion-table">
                <thead>
                    <tr>
                        <th width="10%">Status</th>
                        <th>Tarefa</th>
                        <th>Descrição</th>
                        <th width="15%">Reinicialização</th>
                        <th width="12%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tarefasUsuario)): ?>
                        <tr>
                            <td colspan="5" class="empty-row">Nenhuma tarefa adicionada ainda.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tarefasUsuario as $tarefa): ?>
                            <tr class="<?php echo $tarefa['concluida'] ? 'row-done' : ''; ?>">
                                <td class="text-center">
                                    <!-- Link que altera o estado de concluído diretamente no clique -->
                                    <a href="dashboard.php?toggle_id=<?php echo $tarefa['id']; ?>&ordenar_por=<?php echo $ordenacao; ?>" class="checkbox-link">
                                        <?php echo $tarefa['concluida'] ? '🟩' : '⬜'; ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="task-title"><?php echo htmlspecialchars($tarefa['titulo']); ?></span>
                                </td>
                                <td>
                                    <span class="task-desc"><?php echo htmlspecialchars($tarefa['descricao']); ?></span>
                                </td>
                                <td>
                                    <span class="badge-period"><?php echo $tarefa['periodo_reiniciar']; ?></span>
                                </td>
                                <td>
                                    <!-- Envia a ID da string gerada pelo uniqid para exclusão -->
                                    <a href="delete.php?id=<?php echo $tarefa['id']; ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- SCRIPT JAVASCRIPT PARA CONTROLAR A SIDEBAR RETRÁTIL -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const openBtn = document.getElementById('open-sidebar-btn');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            if(sidebar.classList.contains('collapsed')) {
                openBtn.style.display = 'block';
            } else {
                openBtn.style.display = 'none';
            }
        }
    </script>
</body>
</html>
