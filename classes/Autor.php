<?php
class Autor
{
    private $conn;
    private $table_name = "autores";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function registrar($nome, $sexo, $fone, $dataNascimento, $email, $senha)
    {
        $query_check = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->execute([$email]);

        if ($stmt_check->fetch()) {
            throw new Exception("Email já está em uso!");
        }

        $query = "INSERT INTO " . $this->table_name . " (nome, sexo, fone, dataNascimento, email, senha) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($senha, PASSWORD_BCRYPT);

        if ($stmt->execute([$nome, $sexo, $fone, $dataNascimento, $email, $hashed_password])) {
            return true;
        }
        return false;
    }

    public function login($email, $senha)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        $autor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($autor && password_verify($senha, $autor['senha'])) {
            return $autor;
        }
        return false;
    }

    public function atualizarSenha($id, $nova_senha)
    {
        $query = "UPDATE " . $this->table_name . " SET senha = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($nova_senha, PASSWORD_BCRYPT);
        return $stmt->execute([$hashed_password, $id]);
    }

    public function ler()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lerPorId($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($id, $nome, $sexo, $fone, $dataNascimento, $email)
    {
        $query_check = "SELECT id FROM " . $this->table_name . " WHERE email = ? AND id != ?";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->execute([$email, $id]);

        if ($stmt_check->fetch()) {
            throw new Exception("Email já está em uso por outro autor!");
        }

        $query = "UPDATE " . $this->table_name . " SET nome = ?, sexo = ?, fone = ?, dataNascimento = ?, email = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute([$nome, $sexo, $fone, $dataNascimento, $email, $id])) {
            return true;
        }
        return false;
    }

    public function deletar($id)
    {
        if ($id == 1) {
            throw new Exception("Não é possível excluir o administrador principal!");
        }

        $query_check = "SELECT email FROM " . $this->table_name . " WHERE id = ?";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->execute([$id]);
        $autor = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($autor && $autor['email'] === 'admin@admin.com') {
            throw new Exception("Não é possível excluir o administrador!");
        }

        $this->conn->beginTransaction();

        try {
            $query_noticias = "DELETE FROM noticias WHERE autor_fk = ?";
            $stmt_noticias = $this->conn->prepare($query_noticias);
            $stmt_noticias->execute([$id]);

            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$id]);

            $this->conn->commit();
            return $result;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception("Erro ao excluir autor: " . $e->getMessage());
        }
    }
}
?>