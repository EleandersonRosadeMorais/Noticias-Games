<?php
session_start();
include_once '../config/config.php';
include_once '../classes/Autor.php';

$autor = new Autor($db);
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        
        if ($dados_autor = $autor->login($email, $senha)) {
            $_SESSION['autor_id'] = $dados_autor['id'];
            $_SESSION['autor_nome'] = $dados_autor['nome'];
            
            if ($dados_autor['email'] === 'admin@admin.com') {
                $_SESSION['autor_tipo'] = 'admin';
                header('Location: ../admin/portalAdmin.php');
            } else {
                $_SESSION['autor_tipo'] = 'autor';
                header('Location: ../Noticia/minhasNoticias.php');
            }
            exit();
        } else {
            $mensagem_erro = "Credenciais inválidas!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - NoticiasGames</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../components/header.php'; ?>
    
    <div class="conteiner">
        <div class="conteiner-formulario">
            <h1>🔐 LOGIN</h1>
            <form method="POST">
                <div class="grupo-formulario">
                    <label for="email">📧 Email:</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="senha">🔒 Senha:</label>
                    <input type="password" name="senha" required>
                </div>
                
                <button type="submit" name="login" class="botao botao-login">🚀 Login</button>
            </form>
            
            <?php if ($mensagem_erro): ?>
                <div class="aviso erro">❌ <?php echo $mensagem_erro; ?></div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>