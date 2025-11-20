<?php
session_start();
include_once '../config/config.php';
include_once '../classes/Noticia.php';
include_once '../classes/Comentario.php';

if (!isset($_GET['id'])) {
    header('Location: paginaPrincipal.php');
    exit();
}

$noticia = new Noticia($db);
$comentario_obj = new Comentario($db);

$noticia_completa = $noticia->lerPorIdComAutor($_GET['id']);

if (!$noticia_completa) {
    header('Location: paginaPrincipal.php');
    exit();
}

if (isset($_GET['deletar_comentario']) && isset($_SESSION['autor_tipo']) && $_SESSION['autor_tipo'] === 'admin') {
    $comentario_id = $_GET['deletar_comentario'];
    if ($comentario_obj->deletar($comentario_id)) {
        $_SESSION['mensagem'] = "Comentário excluído com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao excluir comentário!";
    }
    header("Location: noticiaCompleta.php?id=" . $noticia_completa['id']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'] ?? '';
    $comentario_texto = $_POST['comentario'];
    $noticia_id = $_POST['noticia_id'];

    if ($comentario_obj->adicionar($nome, $email, $comentario_texto, $noticia_id)) {
        $_SESSION['mensagem'] = "Comentário adicionado com sucesso!";
        header("Location: noticiaCompleta.php?id=$noticia_id");
        exit();
    } else {
        $erro_comentario = "Erro ao adicionar comentário!";
    }
}

$comentarios = $comentario_obj->lerPorNoticia($noticia_completa['id']);
$total_comentarios = $comentario_obj->contarPorNoticia($noticia_completa['id']);
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo $noticia_completa['titulo']; ?> - NoticiasGames</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="conteiner">
        <article class="cartao-noticia noticia-completa">
            <h1><?php echo htmlspecialchars($noticia_completa['titulo']); ?></h1>

            <div class="info-noticia info-noticia-completa">
                <strong>📝 Autor:</strong> <?php echo htmlspecialchars($noticia_completa['autor_nome']); ?> |
                <strong>📅 Publicado em:</strong>
                <?php echo date('d/m/Y', strtotime($noticia_completa['dataCriacao'])); ?>
            </div>

            <?php if (!empty($noticia_completa['imagem'])): ?>
                <div class="conteiner-imagem">
                    <img src="../uploads/<?php echo $noticia_completa['imagem']; ?>"
                        alt="<?php echo htmlspecialchars($noticia_completa['titulo']); ?>" class="imagem-noticia-completa">
                </div>
            <?php endif; ?>

            <div class="conteudo-noticia">
                <?php
                $conteudoFormatado = nl2br(htmlspecialchars($noticia_completa['noticia']));
                echo $conteudoFormatado;
                ?>
            </div>
        </article>

        <section class="secao-comentarios">
            <h2>💬 Comentários (<?php echo $total_comentarios; ?>)</h2>

            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="aviso sucesso">✅ <?php echo $_SESSION['mensagem'];
                unset($_SESSION['mensagem']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['erro'])): ?>
                <div class="aviso erro">❌ <?php echo $_SESSION['erro'];
                unset($_SESSION['erro']); ?></div>
            <?php endif; ?>

            <?php if (isset($erro_comentario)): ?>
                <div class="aviso erro">❌ <?php echo $erro_comentario; ?></div>
            <?php endif; ?>

            <div class="conteiner-formulario">
                <h3 class="titulo-formulario-comentario">💭 Deixe seu comentário</h3>
                <form method="POST">
                    <input type="hidden" name="noticia_id" value="<?php echo $noticia_completa['id']; ?>">

                    <div class="grupo-formulario">
                        <label for="nome">👤 Nome:*</label>
                        <input type="text" name="nome" required>
                    </div>

                    <div class="grupo-formulario">
                        <label for="email">📧 Email (opcional):</label>
                        <input type="email" name="email">
                    </div>

                    <div class="grupo-formulario">
                        <label for="comentario">💬 Comentário:*</label>
                        <textarea name="comentario" class="area-texto-comentario" required></textarea>
                    </div>

                    <button type="submit" class="botao botao-comentario">📤 Enviar Comentário</button>
                </form>
            </div>

            <?php if ($total_comentarios > 0): ?>
                <div class="lista-comentarios">
                    <?php while ($comentario = $comentarios->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="comentario">
                            <div class="cabecalho-comentario">
                                <strong class="autor-comentario">👤
                                    <?php echo htmlspecialchars($comentario['nome']); ?></strong>
                                <small class="data-comentario">
                                    em <?php echo date('d/m/Y H:i', strtotime($comentario['dataComentario'])); ?>
                                </small>

                                <?php if (isset($_SESSION['autor_tipo']) && $_SESSION['autor_tipo'] === 'admin'): ?>
                                    <a href="?id=<?php echo $noticia_completa['id']; ?>&deletar_comentario=<?php echo $comentario['id']; ?>"
                                        class="botao botao-perigo botao-excluir-comentario"
                                        onclick="return confirm('Tem certeza que deseja excluir este comentário?')">
                                        🗑️
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="texto-comentario">
                                <?php echo nl2br(htmlspecialchars($comentario['comentario'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="comentarios-vazios">
                    <p>📝 Seja o primeiro a comentar esta notícia!</p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <?php include '../components/footer.php'; ?>
</body>

</html>