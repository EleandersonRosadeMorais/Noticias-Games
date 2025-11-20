<?php
session_start();
include_once '../config/config.php';
include_once '../classes/Noticia.php';
include_once '../classes/Comentario.php';

$noticia = new Noticia($db);
$comentario = new Comentario($db);
$noticias = $noticia->lerComAutor();
?>
<!DOCTYPE html>
<html>
<head>
    <title>NoticiasGames - Página Principal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="conteiner">
        <h1>🎮 Últimas Notícias do Mundo dos Games</h1>

        <?php if ($noticias->rowCount() > 0): ?>
            <div class="grade-noticias">
                <?php while ($row = $noticias->fetch(PDO::FETCH_ASSOC)): ?>
                    <article class="cartao-noticia">
                        <h2>
                            <a href="noticiaCompleta.php?id=<?php echo $row['id']; ?>">
                                <?php echo $row['titulo']; ?>
                            </a>
                        </h2>
                        
                        <div class="info-noticia">
                            <strong>📝 Por:</strong> <?php echo $row['autor_nome']; ?> | 
                            <strong>📅 Em:</strong> <?php echo date('d/m/Y', strtotime($row['dataCriacao'])); ?>
                        </div>
                        
                        <?php if (!empty($row['imagem'])): ?>
                            <img src="../uploads/<?php echo $row['imagem']; ?>" alt="<?php echo $row['titulo']; ?>" class="imagem-noticia">
                        <?php endif; ?>
                        
                        <div class="resumo-noticia">
                            <?php echo strip_tags($row['noticia']); ?>
                        </div>
                        
                        <div class="estatisticas-noticia">
                            💬 Comentários: <?php echo $comentario->contarPorNoticia($row['id']); ?>
                        </div>
                        
                        <a href="noticiaCompleta.php?id=<?php echo $row['id']; ?>" class="botao botao-ler-mais">📖 Ler Mais</a>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="estado-vazio">
                <p>📝 Nenhuma notícia publicada ainda.</p>
                <?php if (isset($_SESSION['autor_id'])): ?>
                    <a href="adicionarNoticia.php" class="botao botao-principal">📝 Seja o primeiro a publicar!</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>