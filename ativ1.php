<?php

$arquivo = 'notas.txt'; // Nome do arquivo onde as notas serão armazenadas

// Função para salvar um aluno no arquivo
function salvarAluno($nome, $nota) {
    global $arquivo;
    
    // Validação: a nota deve estar entre 0 e 10
    if ($nota < 0 || $nota > 10) {
        echo "<p style='color:red;'>Erro: Nota inválida! Deve estar entre 0 e 10.</p>";
        return;
    }

    // Abre o arquivo para escrita no final (append)
    $fp = fopen($arquivo, "a");
    if ($fp) {
        // Escreve os dados no arquivo
        fwrite($fp, "$nome,$nota\n");
        fclose($fp); // Fecha o arquivo
    } else {
        echo "<p style='color:red;'>Erro ao abrir o arquivo.</p>";
    }
}

// Função para listar alunos e calcular a média das notas
function listarAlunos() {
    global $arquivo;

    // Verifica se o arquivo existe e não está vazio
    if (!file_exists($arquivo) || filesize($arquivo) == 0) {
        echo "<p>Nenhum aluno cadastrado.</p>";
        return;
    }
    
    // Lê todo o conteúdo do arquivo
    $fp = fopen($arquivo, "r");
    $conteudo = fread($fp, filesize($arquivo));
    fclose($fp);

    // Converte o conteúdo em um array de linhas
    $linhas = explode("\n", trim($conteudo));
    
    $somaNotas = 0;
    $totalAlunos = count($linhas);

    echo "<ul>";
    foreach ($linhas as $indice => $linha) {
        if (!empty($linha)) { // Verifica se a linha não está vazia
            list($nome, $nota) = explode(',', trim($linha)); // Separa nome e nota
            echo "<li>$nome - Nota: $nota <a href='?editar=$indice'>Editar</a></li>";
            $somaNotas += $nota;
        }
    }
    echo "</ul>";

    // Calcula e exibe a média das notas
    $media = $totalAlunos ? $somaNotas / $totalAlunos : 0;
    echo "<p>Média das notas: " . number_format($media, 2) . "</p>";
}

// Função para excluir todas as notas do arquivo
function excluirNotas() {
    global $arquivo;
    
    // Abre o arquivo no modo de escrita para apagar todo o conteúdo
    $fp = fopen($arquivo, "w");
    fclose($fp);
}

// Função para editar a nota de um aluno específico
function editarNota($indice, $novaNota) {
    global $arquivo;
    
    // Validação: a nota deve estar entre 0 e 10
    if ($novaNota < 0 || $novaNota > 10) {
        echo "<p style='color:red;'>Erro: Nota inválida! Deve estar entre 0 e 10.</p>";
        return;
    }
    
    // Lê todas as linhas do arquivo
    $linhas = file($arquivo);
    
    // Verifica se o índice é válido
    if (!isset($linhas[$indice])) {
        echo "<p style='color:red;'>Erro: Índice inválido.</p>";
        return;
    }
    
    // Obtém o nome do aluno e atualiza a nota
    list($nome, $oldNota) = explode(',', trim($linhas[$indice]));
    $linhas[$indice] = "$nome,$novaNota\n";

    // Abre o arquivo para escrita e sobrescreve com os dados atualizados
    $fp = fopen($arquivo, "w");
    foreach ($linhas as $linha) {
        fwrite($fp, $linha);
    }
    fclose($fp);
}

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nome']) && isset($_POST['nota'])) {
        salvarAluno($_POST['nome'], $_POST['nota']); // Cadastra um novo aluno
    } elseif (isset($_POST['excluir'])) {
        excluirNotas(); // Exclui todas as notas
    } elseif (isset($_POST['editar_indice']) && isset($_POST['nova_nota'])) {
        editarNota($_POST['editar_indice'], $_POST['nova_nota']); // Edita uma nota específica
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Alunos</title>
</head>
<body>
    <h2>Cadastro de Alunos</h2>
    <form method="POST">
        Nome: <input type="text" name="nome" required>
        Nota: <input type="number" name="nota" min="0" max="10" required>
        <button type="submit">Salvar</button>
    </form>
    
    <h2>Lista de Alunos</h2>
    <?php listarAlunos(); ?>
    
    <form method="POST">
        <button type="submit" name="excluir">Excluir Todas as Notas</button>
    </form>

    <?php if (isset($_GET['editar'])) { ?>
        <h2>Editar Nota</h2>
        <form method="POST">
            Nova Nota: <input type="number" name="nova_nota" min="0" max="10" required>
            <input type="hidden" name="editar_indice" value="<?php echo $_GET['editar']; ?>">
            <button type="submit">Atualizar</button>
        </form>
    <?php } ?>
</body>
</html>