<?php
$host = "localhost";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("CREATE DATABASE IF NOT EXISTS bdcrud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->exec("USE bdcrud");

    $conn->exec("CREATE TABLE IF NOT EXISTS autores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        sexo CHAR(1) NOT NULL,
        fone VARCHAR(15) NOT NULL,
        dataNascimento DATE NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL
    )");

    $conn->exec("CREATE TABLE IF NOT EXISTS noticias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        noticia TEXT NOT NULL,
        dataCriacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        autor_fk INT NOT NULL,
        imagem VARCHAR(500),
        FOREIGN KEY (autor_fk) REFERENCES autores(id)
    )");

    $conn->exec("CREATE TABLE IF NOT EXISTS comentarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        comentario TEXT NOT NULL,
        dataComentario DATETIME DEFAULT CURRENT_TIMESTAMP,
        noticia_fk INT NOT NULL,
        FOREIGN KEY (noticia_fk) REFERENCES noticias(id) ON DELETE CASCADE
    )");

    $senha_hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT IGNORE INTO autores (nome, sexo, fone, dataNascimento, email, senha) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Administrador', 'M', '000000000', '2000-01-01', 'admin@admin.com', $senha_hash]);

    echo "🎉 Banco de dados criado com sucesso!<br>";
    echo "📊 Tabelas criadas: autores, noticias, comentarios<br>";
    echo "👑 Admin criado: Email: admin@admin.com | Senha: admin123<br>";
    echo "⚠️ <strong>Execute este arquivo apenas uma vez!</strong>";

} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>