<?php
if (isset($_SESSION['autor_id'])) {
    if ($_SESSION['autor_tipo'] === 'admin') {
        include 'header_admin.php';
    } else {
        include 'header_autor.php';
    }
} else {
    include 'header_publico.php';
}
?>