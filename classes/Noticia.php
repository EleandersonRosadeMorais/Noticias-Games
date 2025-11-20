<?php
class Noticia
{
    private $conn;
    private $table_name = "noticias";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function registrar($titulo, $noticia, $autor_fk, $imagem)
    {
        $query = "INSERT INTO " . $this->table_name . " (titulo, noticia, autor_fk, imagem) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$titulo, $noticia, $autor_fk, $imagem]);
    }

    public function lerComAutor()
    {
        $query = "SELECT n.*, a.nome as autor_nome 
                  FROM " . $this->table_name . " n 
                  INNER JOIN autores a ON n.autor_fk = a.id 
                  ORDER BY n.dataCriacao DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lerPorIdAutorComNome($idAutor)
    {
        $query = "SELECT n.*, a.nome as autor_nome 
                  FROM " . $this->table_name . " n 
                  INNER JOIN autores a ON n.autor_fk = a.id 
                  WHERE n.autor_fk = ? 
                  ORDER BY n.dataCriacao DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$idAutor]);
        return $stmt;
    }

    public function lerPorIdComAutor($id)
    {
        $query = "SELECT n.*, a.nome as autor_nome 
                  FROM " . $this->table_name . " n 
                  INNER JOIN autores a ON n.autor_fk = a.id 
                  WHERE n.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($id, $titulo, $noticia, $autor_fk, $imagem)
    {
        if ($imagem === null || $imagem === '') {
            $query_current = "SELECT imagem FROM " . $this->table_name . " WHERE id = ?";
            $stmt_current = $this->conn->prepare($query_current);
            $stmt_current->execute([$id]);
            $current = $stmt_current->fetch(PDO::FETCH_ASSOC);
            $imagem = $current['imagem'];
        }

        $query = "UPDATE " . $this->table_name . " SET titulo = ?, noticia = ?, autor_fk = ?, imagem = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$titulo, $noticia, $autor_fk, $imagem, $id]);
    }

    public function deletar($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>