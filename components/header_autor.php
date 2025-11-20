<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>NoticiasGames</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="cabecalho">
        <h1>🎮 NoticiasGames</h1>
        <nav>
            <ul>
                <li><a href="../noticia/paginaPrincipal.php">🏠 Página Principal</a></li>
                <li><a href="../Noticia/minhasNoticias.php">📝 Minhas Notícias</a></li>
                <li><a href="../Noticia/adicionarNoticia.php">➕ Adicionar Notícia</a></li>
                <li><a href="../Autor/logout.php">🚪 Logout (<?php echo $_SESSION['autor_nome']; ?>)</a></li>
            </ul>
        </nav>
    </header>
    <main class="pagina">