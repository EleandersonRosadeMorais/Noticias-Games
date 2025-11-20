<?php
session_start();
include_once '../config/config.php';
include_once '../classes/Noticia.php';
include_once '../classes/Comentario.php';

if (!isset($_SESSION['autor_id'])) {
    header('Location: ../Autor/login.php');
    exit();
}

$noticia = new Noticia($db);
$comentario = new Comentario($db);

if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    if ($noticia->deletar($id)) {
        $_SESSION['mensagem'] = "Notícia excluída com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao excluir notícia!";
    }
    header('Location: minhasNoticias.php');
    exit();
}

$dados = $noticia->lerPorIdAutorComNome($_SESSION['autor_id']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Notícias - NoticiasGames</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="conteiner">
        <h1>📝 Minhas Notícias</h1>
        
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="aviso sucesso">✅ <?php echo $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="aviso erro">❌ <?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
        <?php endif; ?>
        
        <?php if ($dados->rowCount() > 0): ?>
            <div class="grade-noticias">
                <?php while ($row = $dados->fetch(PDO::FETCH_ASSOC)): ?>
                    <article class="cartao-noticia">
                        <h2>
                            <a href="noticiaCompleta.php?id=<?php echo $row['id']; ?>">
                                <?php echo htmlspecialchars($row['titulo']); ?>
                            </a>
                        </h2>
                        
                        <div class="info-noticia">
                            <strong>📝 Por:</strong> <?php echo htmlspecialchars($row['autor_nome']); ?> | 
                            <strong>📅 Em:</strong> <?php echo date('d/m/Y', strtotime($row['dataCriacao'])); ?>
                        </div>
                        
                        <?php if (!empty($row['imagem'])): ?>
                            <img src="../uploads/<?php echo $row['imagem']; ?>" alt="<?php echo htmlspecialchars($row['titulo']); ?>" class="imagem-noticia">
                        <?php endif; ?>
                        
                        <div class="resumo-noticia">
                            <?php echo strip_tags($row['noticia']); ?>
                        </div>
                        
                        <div class="estatisticas-noticia">
                            💬 Comentários: <?php echo $comentario->contarPorNoticia($row['id']); ?>
                        </div>
                        
                        <div class="acoes-noticia">
                            <a href="editarNoticiaHtml.php?id=<?php echo $row['id']; ?>" class="botao botao-editar">✏️ Editar</a>
                            <a href="?deletar=<?php echo $row['id']; ?>" 
                               class="botao botao-perigo"
                               onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">🗑️ Deletar</a>
                            <a href="noticiaCompleta.php?id=<?php echo $row['id']; ?>" class="botao botao-ver">👁️ Ver Completa</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="estado-vazio">
                <p>📝 Você ainda não possui notícias cadastradas.</p>
                <a href="adicionarNoticia.php" class="botao botao-principal">📝 Adicionar Primeira Notícia</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>