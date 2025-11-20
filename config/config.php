<?php
include_once __DIR__ . '/../classes/Database.php';
include_once __DIR__ . '/../classes/Autor.php';
include_once __DIR__ . '/../classes/Noticia.php';
include_once __DIR__ . '/../classes/Comentario.php';

$database = new Database(); 
$db = $database->getConnection();
?>