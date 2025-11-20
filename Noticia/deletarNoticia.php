<?php
session_start();
if (!isset($_SESSION['autor_id'])) {
    header('Location: ../Autor/login.php');
    exit();
}

include_once '../config/config.php';
include_once '../classes/Noticia.php';

$noticia = new Noticia($db);
$erros = [];

if (isset($_GET['id'])) {
    $id = '';
    
    if (isset($_GET['id'])) {
        $id = trim($_GET['id']);
    }

    if (empty($id)) {
        $erros[] = "ID da notícia não especificado!";
    } else {
        $id_filtrado = filter_var($id, FILTER_VALIDATE_INT);
        if ($id_filtrado === false || $id_filtrado <= 0) {
            $erros[] = "ID da notícia inválido!";
        }
    }

    $usuario_eh_admin = ($_SESSION['autor_id'] == 1);

    if (empty($erros)) {
        $noticia_data = $noticia->lerPorIdComAutor($id_filtrado);
        
        if (!$noticia_data) {
            $erros[] = "Notícia não encontrada!";
        } else {
            $noticia_pertence_ao_usuario = ($noticia_data['autor_fk'] == $_SESSION['autor_id']);
            
            if (!$usuario_eh_admin && !$noticia_pertence_ao_usuario) {
                $erros[] = "Você não tem permissão para excluir esta notícia!";
            }
        }
    }

    if (empty($erros)) {
        if ($noticia->deletar($id_filtrado)) {
            $_SESSION['mensagem'] = "Notícia excluída com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao excluir notícia!";
        }
    } else {
        $_SESSION['erro'] = implode(" ", $erros);
    }
    
    if ($usuario_eh_admin) {
        header('Location: ../admin/portalAdmin.php');
    } else {
        header('Location: minhasNoticias.php');
    }
    exit();
} else {
    $usuario_eh_admin = ($_SESSION['autor_id'] == 1);
    
    $_SESSION['erro'] = "ID da notícia não especificado!";
    
    if ($usuario_eh_admin) {
        header('Location: ../admin/portalAdmin.php');
    } else {
        header('Location: minhasNoticias.php');
    }
    exit();
}
?>