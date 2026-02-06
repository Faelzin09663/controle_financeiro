<?php
require_once '../config/db.php';

// Verifica se veio um ID na URL (ex: excluir.php?id=5)
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepara o comando para deletar
    $stmt = $pdo->prepare("DELETE FROM movimentacoes WHERE id = :id");
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        // Se deu certo, volta pra home
        header("Location: ../index.php?msg=deletado");
    } else {
        echo "Erro ao excluir.";
    }
}
?>