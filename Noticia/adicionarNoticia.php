<?php
session_start();
if (!isset($_SESSION['autor_id'])) {
    header('Location: ../Autor/login.php');
    exit();
}

include_once '../config/config.php';
include_once '../classes/Noticia.php';

$erros = [];
$titulo = $noticia_texto = $imagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['titulo'])) {
        $titulo = trim($_POST['titulo']);
    }
    
    if (isset($_POST['noticia'])) {
        $noticia_texto = trim($_POST['noticia']);
    }
    
    $autor_fk = $_SESSION['autor_id'];
    
    if (isset($_FILES['imagem']) && $_FILES['imagem']['name']) {
        $imagem = $_FILES['imagem']['name'];
    }

    if (empty($titulo)) {
        $erros[] = "Preencha o campo Título!";
    } else {
        if (strlen($titulo) < 5) {
            $erros[] = "O título deve ter pelo menos 5 caracteres!";
        }
    }

    if (empty($noticia_texto)) {
        $erros[] = "Preencha o campo Notícia!";
    } else {
        if (strlen($noticia_texto) < 10) {
            $erros[] = "A notícia deve ter pelo menos 10 caracteres!";
        }
    }

    if (!empty($imagem)) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["imagem"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["imagem"]["tmp_name"]);
        if ($check === false) {
            $erros[] = "O arquivo não é uma imagem válida!";
        }
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            $erros[] = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos!";
        }
        
        if ($_FILES["imagem"]["size"] > 5000000) {
            $erros[] = "O arquivo é muito grande! Tamanho máximo: 5MB";
        }
        
        if (file_exists($target_file)) {
            $erros[] = "Já existe um arquivo com esse nome!";
        }
    }

    if (empty($erros)) {
        $noticia = new Noticia($db);
        
        if (!empty($imagem)) {
            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                $imagem_path = $imagem;
            } else {
                $erros[] = "Erro ao fazer upload da imagem!";
                $imagem_path = '';
            }
        } else {
            $imagem_path = '';
        }
        
        if (empty($erros)) {
            $resultado = $noticia->registrar($titulo, $noticia_texto, $autor_fk, $imagem_path);
            
            if ($resultado) {
                $_SESSION['mensagem'] = "Notícia adicionada com sucesso!";
                header('Location: minhasNoticias.php');
                exit();
            } else {
                $erros[] = "Erro ao adicionar notícia!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Notícia - NoticiasGames</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../components/header.php'; ?>
    
    <div class="conteiner">
        <h1>📝 Adicionar Notícia</h1>
        
        <?php if (!empty($erros)): ?>
            <div class="aviso erro">
                <strong>❌ Erros encontrados:</strong>
                <ul>
                    <?php foreach ($erros as $erro_item): ?>
                        <li><?php echo htmlspecialchars($erro_item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="conteiner-formulario">
            <form method="POST" enctype="multipart/form-data">
                <div class="grupo-formulario">
                    <label for="titulo">📰 Título:</label>
                    <input type="text" name="titulo" id="titulo" 
                           value="<?php echo !empty($titulo) ? htmlspecialchars($titulo) : ''; ?>" 
                           required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="noticia">📖 Notícia:</label>
                    <textarea name="noticia" id="noticia" required><?php echo !empty($noticia_texto) ? htmlspecialchars($noticia_texto) : ''; ?></textarea>
                </div>
                
                <div class="grupo-formulario">
                    <label for="imagem">🖼️ Imagem:</label>
                    <input type="file" name="imagem" id="imagem" class="entrada-arquivo">
                    <small class="ajuda-formulario">📁 Formatos: JPG, JPEG, PNG, GIF | Tamanho máximo: 5MB</small>
                </div>
                
                <button type="submit" class="botao botao-adicionar-noticia">🚀 Adicionar Notícia</button>
            </form>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>