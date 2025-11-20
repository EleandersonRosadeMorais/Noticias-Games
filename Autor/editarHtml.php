<?php
session_start();
if (!isset($_SESSION['autor_id'])) {
    header('Location: ../noticia/paginaPrincipal.php');
    exit();
}

include_once '../config/config.php';
include_once '../classes/Autor.php';

$autor = new Autor($db);
$erros = [];
$row = [];

if (isset($_GET['id'])) {
    $id_get = $_GET['id'];
    if ($id_get == 1) {
        $_SESSION['erro'] = "Acesso negado! O administrador principal não pode ser editado.";
        header('Location: ../admin/portalAdmin.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $nome = $sexo = $fone = $dataNascimento = $email = $nova_senha = $confirmar_senha = '';
    
    if (isset($_POST['id'])) {
        $id = trim($_POST['id']);
    }
    
    if (isset($_POST['nome'])) {
        $nome = trim($_POST['nome']);
    }
    
    if (isset($_POST['sexo'])) {
        $sexo = trim($_POST['sexo']);
    }
    
    if (isset($_POST['fone'])) {
        $fone = trim($_POST['fone']);
    }
    
    if (isset($_POST['dataNascimento'])) {
        $dataNascimento = trim($_POST['dataNascimento']);
    }
    
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
    }
    
    if (isset($_POST['nova_senha'])) {
        $nova_senha = trim($_POST['nova_senha']);
    }
    
    if (isset($_POST['confirmar_senha'])) {
        $confirmar_senha = trim($_POST['confirmar_senha']);
    }

    if (empty($id)) {
        $erros[] = "ID do autor não especificado!";
    } else {
        $id_filtrado = filter_var($id, FILTER_VALIDATE_INT);
        if ($id_filtrado === false || $id_filtrado <= 0) {
            $erros[] = "ID do autor inválido!";
        } elseif ($id_filtrado == 1) {
            $erros[] = "Não é possível editar o administrador principal!";
        }
    }

    if (empty($nome)) {
        $erros[] = "Preencha o campo Nome!";
    } else {
        if (strlen($nome) < 2) {
            $erros[] = "O nome deve ter pelo menos 2 caracteres!";
        }
    }

    if (empty($sexo)) {
        $erros[] = "Selecione o Sexo!";
    } else {
        if (!in_array($sexo, ['M', 'F'])) {
            $erros[] = "Sexo inválido!";
        }
    }

    if (empty($fone)) {
        $erros[] = "Preencha o campo Telefone!";
    } else {
        $fone_limpo = preg_replace('/[^0-9]/', '', $fone);
        if (strlen($fone_limpo) < 10) {
            $erros[] = "Telefone inválido! Deve conter pelo menos 10 dígitos.";
        }
    }

    if (empty($dataNascimento)) {
        $erros[] = "Preencha o campo Data de Nascimento!";
    } else {
        $data_filtrada = filter_var($dataNascimento, FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => '/^\d{4}-\d{2}-\d{2}$/']
        ]);
        if ($data_filtrada === false) {
            $erros[] = "Data de Nascimento em formato inválido!";
        } else {
            $data_obj = DateTime::createFromFormat('Y-m-d', $dataNascimento);
            $hoje = new DateTime();
            if (!$data_obj || $data_obj > $hoje) {
                $erros[] = "Data de Nascimento inválida!";
            }
        }
    }

    if (empty($email)) {
        $erros[] = "Preencha o campo Email!";
    } else {
        $email_filtrado = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($email_filtrado === false) {
            $erros[] = "Email inválido!";
        }
    }

    if (!empty($nova_senha)) {
        if (strlen($nova_senha) < 6) {
            $erros[] = "A nova senha deve ter pelo menos 6 caracteres!";
        }
        
        if (empty($confirmar_senha)) {
            $erros[] = "Confirme a nova senha!";
        } elseif ($nova_senha !== $confirmar_senha) {
            $erros[] = "As senhas não coincidem!";
        }
    }

    if (empty($erros)) {
        try {
            if ($autor->atualizar($id_filtrado, $nome, $sexo, $fone, $dataNascimento, $email_filtrado)) {
                $_SESSION['mensagem'] = "Autor atualizado com sucesso!";
                
                if (!empty($nova_senha)) {
                    if ($autor->atualizarSenha($id_filtrado, $nova_senha)) {
                        $_SESSION['mensagem'] = "Autor e senha atualizados com sucesso!";
                    } else {
                        $_SESSION['erro'] = "Dados atualizados, mas erro ao alterar senha!";
                    }
                }
            } else {
                $_SESSION['erro'] = "Erro ao atualizar autor!";
            }
        } catch (Exception $e) {
            $_SESSION['erro'] = "Erro: " . $e->getMessage();
        }
        
        header('Location: ../admin/portalAdmin.php');
        exit();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $id_filtrado = filter_var($id, FILTER_VALIDATE_INT);
    if ($id_filtrado === false || $id_filtrado <= 0) {
        $_SESSION['erro'] = "ID do autor inválido!";
        header('Location: ../admin/portalAdmin.php');
        exit();
    }
    
    if ($id_filtrado == 1) {
        $_SESSION['erro'] = "Acesso negado! O administrador principal não pode ser editado.";
        header('Location: ../admin/portalAdmin.php');
        exit();
    }
    
    $row = $autor->lerPorId($id_filtrado);
    
    if (!$row) {
        $_SESSION['erro'] = "Autor não encontrado!";
        header('Location: ../admin/portalAdmin.php');
        exit();
    }
} else {
    $_SESSION['erro'] = "ID do autor não especificado!";
    header('Location: ../admin/portalAdmin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Autor - NoticiasGames</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../components/header_admin.php'; ?>
    
    <div class="conteiner">
        <h1>✏️ Editar Autor</h1>
        
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
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                
                <h3 class="titulo-secao">👤 Informações Pessoais</h3>
                
                <div class="grupo-formulario">
                    <label for="nome">👤 Nome:</label>
                    <input type="text" name="nome" id="nome" 
                           value="<?php echo !empty($nome) ? htmlspecialchars($nome) : htmlspecialchars($row['nome']); ?>" 
                           required>
                </div>
                
                <div class="grupo-formulario">
                    <label>⚤ Sexo:</label>
                    <div class="grupo-radio">
                        <label for="masculino_editar">
                            <input type="radio" id="masculino_editar" name="sexo" value="M" 
                                   <?php echo ((!empty($sexo) ? $sexo : $row['sexo']) === 'M') ? 'checked' : ''; ?> required> 👨 Masculino
                        </label>
                        <label for="feminino_editar">
                            <input type="radio" id="feminino_editar" name="sexo" value="F" 
                                   <?php echo ((!empty($sexo) ? $sexo : $row['sexo']) === 'F') ? 'checked' : ''; ?> required> 👩 Feminino
                        </label>
                    </div>
                </div>
                
                <div class="grupo-formulario">
                    <label for="fone">📞 Telefone:</label>
                    <input type="text" name="fone" id="fone" 
                           value="<?php echo !empty($fone) ? htmlspecialchars($fone) : htmlspecialchars($row['fone']); ?>" 
                           required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="dataNascimento">📅 Data de Nascimento:</label>
                    <input type="date" name="dataNascimento" id="dataNascimento" 
                           value="<?php echo !empty($dataNascimento) ? htmlspecialchars($dataNascimento) : htmlspecialchars($row['dataNascimento']); ?>" 
                           required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="email">📧 Email:</label>
                    <input type="email" name="email" id="email" 
                           value="<?php echo !empty($email) ? htmlspecialchars($email) : htmlspecialchars($row['email']); ?>" 
                           required>
                </div>

                <h3 class="titulo-secao">🔐 Alterar Senha (Opcional)</h3>
                
                <?php if ($row['id'] != $_SESSION['autor_id']): ?>
                    <div class="aviso-admin">
                        <p><strong>⚡ Modo Administrador:</strong> Você está alterando os dados de outro autor.</p>
                    </div>
                <?php endif; ?>
                
                <div class="grupo-formulario">
                    <label for="nova_senha">Nova Senha:</label>
                    <input type="password" name="nova_senha" id="nova_senha" 
                           value="<?php echo !empty($nova_senha) ? htmlspecialchars($nova_senha) : ''; ?>" 
                           placeholder="Deixe em branco para manter a senha atual">
                    <small class="ajuda-formulario">🔒 Mínimo 6 caracteres</small>
                </div>
                
                <div class="grupo-formulario">
                    <label for="confirmar_senha">Confirmar Nova Senha:</label>
                    <input type="password" name="confirmar_senha" id="confirmar_senha" 
                           value="<?php echo !empty($confirmar_senha) ? htmlspecialchars($confirmar_senha) : ''; ?>" 
                           placeholder="Confirme a nova senha">
                </div>
                
                <div class="acoes-formulario">
                    <button type="submit" class="botao">💾 Atualizar Dados</button>
                    <a href="../admin/portalAdmin.php" class="botao botao-perigo">❌ Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>