<?php
session_start();
if (!isset($_SESSION['autor_id'])) {
    header('Location: ../Autor/login.php');
    exit();
}

include_once '../config/config.php';
include_once '../classes/Noticia.php';

$noticia_obj = new Noticia($db);
$erros = [];
$row = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $titulo = $noticia_texto = $autor_fk = $imagem = '';
    
    if (isset($_POST['id'])) {
        $id = trim($_POST['id']);
    }
    
    if (isset($_POST['titulo'])) {
        $titulo = trim($_POST['titulo']);
    }
    
    if (isset($_POST['noticia'])) {
        $noticia_texto = trim($_POST['noticia']);
    }
    
    if (isset($_POST['autor_fk'])) {
        $autor_fk = trim($_POST['autor_fk']);
    }
    
    if (isset($_POST['imagem_atual'])) {
        $imagem = trim($_POST['imagem_atual']);
    }

    if (empty($id)) {
        $erros[] = "ID da notícia não especificado!";
    } else {
        $id_filtrado = filter_var($id, FILTER_VALIDATE_INT);
        if ($id_filtrado === false || $id_filtrado <= 0) {
            $erros[] = "ID da notícia inválido!";
        }
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

    if (empty($autor_fk)) {
        $erros[] = "Autor não especificado!";
    } else {
        $autor_fk_filtrado = filter_var($autor_fk, FILTER_VALIDATE_INT);
        if ($autor_fk_filtrado === false || $autor_fk_filtrado <= 0) {
            $erros[] = "ID do autor inválido!";
        }
    }

    if (isset($_POST['remover_imagem']) && $_POST['remover_imagem'] == '1') {
        if (!empty($imagem)) {
            $target_dir = "../uploads/";
            $imagem_antiga = $target_dir . $imagem;
            if (file_exists($imagem_antiga)) {
                unlink($imagem_antiga);
            }
        }
        $imagem = null;
    }
    elseif (isset($_FILES['nova_imagem']) && $_FILES['nova_imagem']['error'] === UPLOAD_ERR_OK) {
        $nome_arquivo = uniqid() . '_' . basename($_FILES["nova_imagem"]["name"]);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $nome_arquivo;
        
        $check = getimagesize($_FILES["nova_imagem"]["tmp_name"]);
        if ($check === false) {
            $erros[] = "O arquivo não é uma imagem válida!";
        } else {
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($imageFileType, $allowed_types)) {
                $erros[] = "Apenas arquivos JPG, JPEG, PNG, GIF e WEBP são permitidos!";
            }
            
            if ($_FILES["nova_imagem"]["size"] > 5000000) {
                $erros[] = "O arquivo é muito grande! Tamanho máximo: 5MB";
            }
            
            if (file_exists($target_file)) {
                $erros[] = "Já existe um arquivo com esse nome!";
            }
        }

        if (empty($erros)) {
            if (move_uploaded_file($_FILES["nova_imagem"]["tmp_name"], $target_file)) {
                $imagem = $nome_arquivo;
                
                if (!empty($_POST['imagem_atual']) && $_POST['imagem_atual'] != $nome_arquivo) {
                    $imagem_antiga = $target_dir . $_POST['imagem_atual'];
                    if (file_exists($imagem_antiga)) {
                        unlink($imagem_antiga);
                    }
                }
            } else {
                $erros[] = "Erro ao fazer upload da imagem!";
            }
        }
    }

    if (empty($erros)) {
        if ($noticia_obj->atualizar($id_filtrado, $titulo, $noticia_texto, $autor_fk_filtrado, $imagem)) {
            $_SESSION['mensagem'] = "Notícia atualizada com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao atualizar notícia!";
        }
        header('Location: minhasNoticias.php');
        exit();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $id_filtrado = filter_var($id, FILTER_VALIDATE_INT);
    if ($id_filtrado === false || $id_filtrado <= 0) {
        $_SESSION['erro'] = "ID da notícia inválido!";
        header('Location: minhasNoticias.php');
        exit();
    }
    
    $row = $noticia_obj->lerPorIdComAutor($id_filtrado);
    
    if (!$row) {
        $_SESSION['erro'] = "Notícia não encontrada!";
        header('Location: minhasNoticias.php');
        exit();
    }
    
    if ($_SESSION['autor_tipo'] !== 'admin' && $row['autor_fk'] != $_SESSION['autor_id']) {
        $_SESSION['erro'] = "Você não tem permissão para editar esta notícia!";
        header('Location: minhasNoticias.php');
        exit();
    }
} else {
    $_SESSION['erro'] = "ID da notícia não especificado!";
    header('Location: minhasNoticias.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Notícia - NoticiasGames</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../components/header.php'; ?>
    
    <div class="conteiner">
        <h1>✏️ Editar Notícia</h1>
        
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
        
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="aviso sucesso">✅ <?php echo htmlspecialchars($_SESSION['mensagem']); unset($_SESSION['mensagem']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="aviso erro">❌ <?php echo htmlspecialchars($_SESSION['erro']); unset($_SESSION['erro']); ?></div>
        <?php endif; ?>
        
        <div class="conteiner-formulario">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <input type="hidden" name="imagem_atual" value="<?php echo htmlspecialchars($row['imagem']); ?>">
                
                <div class="grupo-formulario">
                    <label for="titulo">📰 Título:</label>
                    <input type="text" name="titulo" id="titulo" 
                           value="<?php echo !empty($titulo) ? htmlspecialchars($titulo) : htmlspecialchars($row['titulo']); ?>" 
                           required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="noticia">📖 Notícia:</label>
                    <textarea name="noticia" id="noticia" rows="10" required><?php echo !empty($noticia_texto) ? htmlspecialchars($noticia_texto) : htmlspecialchars($row['noticia']); ?></textarea>
                </div>
                
                <div class="grupo-formulario">
                    <label for="autor_fk">👤 Autor ID:</label>
                    <input type="number" name="autor_fk" id="autor_fk" 
                           value="<?php echo !empty($autor_fk) ? htmlspecialchars($autor_fk) : htmlspecialchars($row['autor_fk']); ?>" 
                           <?php echo ($_SESSION['autor_tipo'] !== 'admin') ? 'readonly' : ''; ?> required>
                    <?php if ($_SESSION['autor_tipo'] !== 'admin'): ?>
                        <small class="ajuda-formulario">🔒 Você só pode editar suas próprias notícias</small>
                    <?php endif; ?>
                </div>
                
                <div class="grupo-formulario">
                    <label class="titulo-secao-imagem">🖼️ Imagem Atual:</label>
                    <?php if (!empty($row['imagem'])): ?>
                        <div class="preview-imagem">
                            <img src="../uploads/<?php echo htmlspecialchars($row['imagem']); ?>" 
                                 alt="Imagem atual" 
                                 class="imagem-preview">
                            <p class="info-imagem">
                                📁 Arquivo: <?php echo htmlspecialchars($row['imagem']); ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="sem-imagem">
                            <p>📷 Nenhuma imagem cadastrada</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="grupo-formulario">
                    <label for="nova_imagem" class="titulo-secao-imagem">🔄 Alterar Imagem:</label>
                    <input type="file" name="nova_imagem" id="nova_imagem" accept="image/*" class="entrada-arquivo">
                    <small class="ajuda-formulario">
                        📁 Deixe em branco para manter a imagem atual.<br>
                        🖼️ Formatos aceitos: JPG, PNG, GIF, WEBP<br>
                        💾 Tamanho máximo: 5MB
                    </small>
                </div>
                
                <?php if (!empty($row['imagem'])): ?>
                <div class="grupo-formulario">
                    <label class="rotulo-checkbox">
                        <input type="checkbox" name="remover_imagem" value="1" class="entrada-checkbox"> 
                        <span class="texto-checkbox">🗑️ Remover imagem atual</span>
                    </label>
                    <small class="ajuda-formulario">
                        Marque esta opção para remover a imagem atual sem adicionar uma nova
                    </small>
                </div>
                <?php endif; ?>
                
                <div class="acoes-formulario">
                    <button type="submit" class="botao botao-atualizar">
                        💾 Atualizar Notícia
                    </button>
                    <a href="minhasNoticias.php" class="botao botao-perigo">
                        ❌ Cancelar
                    </a>
                </div>
                
                <div class="dica-formulario">
                    <p>💡 <strong>Dica:</strong> Para alterar apenas a imagem, preencha apenas os campos relacionados à imagem.</p>
                </div>
            </form>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>