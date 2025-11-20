<?php
session_start();
if (!isset($_SESSION['autor_id']) || $_SESSION['autor_tipo'] !== 'admin') {
    header('Location: ../noticia/paginaPrincipal.php');
    exit();
}

include_once '../config/config.php';
include_once '../classes/Noticia.php';
include_once '../classes/Autor.php';
include_once '../classes/Comentario.php';

$noticia = new Noticia($db);
$autor = new Autor($db);
$comentario = new Comentario($db);

if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    if ($noticia->deletar($id)) {
        $_SESSION['mensagem'] = "Notícia excluída com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao excluir notícia!";
    }
    header('Location: portalAdmin.php');
    exit();
}

if (isset($_GET['deletar_autor'])) {
    $id = $_GET['deletar_autor'];
    
    if ($id == 1) {
        $_SESSION['erro'] = "Não é possível excluir a conta do administrador principal!";
    } else {
        if ($autor->deletar($id)) {
            $_SESSION['mensagem'] = "Autor excluído com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao excluir autor!";
        }
    }
    header('Location: portalAdmin.php');
    exit();
}

$todas_noticias = $noticia->lerComAutor();
$todos_autores = $autor->ler();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Portal Admin - NoticiasGames</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../components/header_admin.php'; ?>

    <div class="conteiner">
        <h1>🎮 Portal Administrativo</h1>
        
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="aviso sucesso">✅ <?php echo $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="aviso erro">❌ <?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
        <?php endif; ?>
        
        <h2>📰 Todas as Notícias</h2>
        
        <?php if ($todas_noticias->rowCount() > 0): ?>
            <div class="grade-noticias">
                <?php while ($row = $todas_noticias->fetch(PDO::FETCH_ASSOC)): ?>
                    <article class="cartao-noticia">
                        <h2>
                            <a href="../Noticia/noticiaCompleta.php?id=<?php echo $row['id']; ?>">
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
                            <a href="../Noticia/editarNoticiaHtml.php?id=<?php echo $row['id']; ?>" class="botao">✏️ Editar</a>
                            <a href="?deletar=<?php echo $row['id']; ?>" 
                               class="botao botao-perigo"
                               onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">🗑️ Deletar</a>
                            <a href="../Noticia/noticiaCompleta.php?id=<?php echo $row['id']; ?>" class="botao">👁️ Ver Completa</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="texto-centralizado">
                <p>📝 Nenhuma notícia publicada ainda.</p>
            </div>
        <?php endif; ?>

        <h2>👥 Autores Cadastrados</h2>
        <div class="grade-autores">
            <?php while ($autor_row = $todos_autores->fetch(PDO::FETCH_ASSOC)): ?>
                <?php if ($autor_row['id'] != 1): ?>
                <div class="cartao-autor">
                    <div class="cabecalho-autor">
                        <h3><?php echo htmlspecialchars($autor_row['nome']); ?></h3>
                        <span class="etiqueta-autor"><?php echo $autor_row['sexo'] === 'M' ? '👨 Masculino' : '👩 Feminino'; ?></span>
                    </div>
                    
                    <div class="dados-autor">
                        <p><strong>📧 Email:</strong> <?php echo htmlspecialchars($autor_row['email']); ?></p>
                        <p><strong>📞 Telefone:</strong> <?php echo htmlspecialchars($autor_row['fone']); ?></p>
                        <p><strong>🆔 ID:</strong> <?php echo $autor_row['id']; ?></p>
                    </div>
                    
                    <div class="acoes-autor">
                        <a href="../Autor/editarHtml.php?id=<?php echo $autor_row['id']; ?>" class="botao">✏️ Editar</a>
                        <a href="?deletar_autor=<?php echo $autor_row['id']; ?>" 
                           class="botao botao-perigo"
                           onclick="return confirm('Tem certeza que deseja excluir este autor? TODAS as notícias deste autor também serão excluídas!')">
                           🗑️ Deletar
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>