<?php
session_start();
session_destroy();
header('Location: ../noticia/paginaPrincipal.php');
exit();
?>