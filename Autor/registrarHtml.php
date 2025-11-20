<?php
session_start();
include_once '../config/config.php';
include_once '../classes/Autor.php';

$erro = '';
$sucesso = '';
$erros = [];

$nome = $sexo = $fone = $dataNascimento = $email = $senha = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    
    if (isset($_POST['senha'])) {
        $senha = trim($_POST['senha']);
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

    if (empty($senha)) {
        $erros[] = "Preencha o campo Senha!";
    } else {
        if (strlen($senha) < 6) {
            $erros[] = "A senha deve ter pelo menos 6 caracteres!";
        }
    }

    if (empty($erros)) {
        try {
            $autor = new Autor($db);
            $resultado = $autor->registrar($nome, $sexo, $fone, $dataNascimento, $email_filtrado, $senha);
            
            if ($resultado) {
                $sucesso = "Autor registrado com sucesso!";
                $nome = $sexo = $fone = $dataNascimento = $email = $senha = '';
            } else {
                $erro = "Erro ao criar autor!";
            }
        } catch (Exception $e) {
            $erro = "Erro: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Autor - NoticiasGames</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../components/header.php'; ?>
    
    <div class="conteiner">
        <h1>📝 Registrar Autor</h1>
        
        <div class="conteiner-formulario">
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
            
            <?php if ($erro): ?>
                <div class="aviso erro">❌ <?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="aviso sucesso">✅ <?php echo htmlspecialchars($sucesso); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="grupo-formulario">
                    <label for="nome">👤 Nome:</label>
                    <input type="text" name="nome" id="nome" 
                           value="<?php echo !empty($nome) ? htmlspecialchars($nome) : ''; ?>" 
                           required>
                </div>
                
                <div class="grupo-formulario">
                    <label>⚤ Sexo:</label>
                    <div class="grupo-radio">
                        <label for="masculino">
                            <input type="radio" id="masculino" name="sexo" value="M" 
                                   <?php echo ($sexo === 'M') ? 'checked' : ''; ?> required> 👨 Masculino
                        </label>
                        <label for="feminino">
                            <input type="radio" id="feminino" name="sexo" value="F" 
                                   <?php echo ($sexo === 'F') ? 'checked' : ''; ?> required> 👩 Feminino
                        </label>
                    </div>
                </div>
                
                <div class="grupo-formulario">
                    <label for="fone">📞 Telefone:</label>
                    <input type="text" name="fone" id="fone" 
                           value="<?php echo !empty($fone) ? htmlspecialchars($fone) : ''; ?>" 
                           required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="dataNascimento">📅 Data de Nascimento:</label>
                    <input type="date" name="dataNascimento" id="dataNascimento" 
                           value="<?php echo !empty($dataNascimento) ? htmlspecialchars($dataNascimento) : ''; ?>" 
                           required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="email">📧 Email:</label>
                    <input type="email" name="email" id="email" 
                           value="<?php echo !empty($email) ? htmlspecialchars($email) : ''; ?>" 
                           required>
                </div>
                
                <div class="grupo-formulario">
                    <label for="senha">🔒 Senha:</label>
                    <input type="password" name="senha" id="senha" 
                           value="<?php echo !empty($senha) ? htmlspecialchars($senha) : ''; ?>" 
                           required>
                </div>
                
                <button type="submit" class="botao botao-registrar">🚀 Registrar Autor</button>
            </form>
            
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>