<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>NoticiasGames - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="cabecalho">
        <h1>🎮 NoticiasGames - Admin</h1>
        <nav>
            <ul>
                <li><a href="../noticia/paginaPrincipal.php">🏠 Página Principal</a></li>
                <li><a href="../admin/portalAdmin.php">📰 Todas Notícias</a></li>
                <li><a href="../Autor/registrarHtml.php">👥 Registrar Autor</a></li>
                <li><a href="../Autor/logout.php">🚪 Logout (<?php echo $_SESSION['autor_nome']; ?>)</a></li>
            </ul>
        </nav>
    </header>
    <main class="pagina">