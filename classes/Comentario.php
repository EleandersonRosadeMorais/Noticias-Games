<?php
class Comentario
{
    private $conn;
    private $table_name = "comentarios";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function adicionar($nome, $email, $comentario, $noticia_fk)
    {
        $query = "INSERT INTO " . $this->table_name . " (nome, email, comentario, noticia_fk) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nome, $email, $comentario, $noticia_fk]);
    }

    public function lerPorNoticia($noticia_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE noticia_fk = ? ORDER BY dataComentario DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$noticia_id]);
        return $stmt;
    }

    public function contarPorNoticia($noticia_id)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE noticia_fk = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$noticia_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function deletar($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>