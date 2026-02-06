<?php
// src/adicionar_movimentacao.php
require_once '../config/db.php'; // Puxa a conexão que criamos antes

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor']; // O valor vem do input
    $data = $_POST['data'];
    $categoria_id = $_POST['categoria'];
    
    // Proteção básica: Se for despesa, garantimos que salva negativo? 
    // Ou deixamos positivo e tratamos na exibição? Vamos salvar o valor bruto.
    
    try {
        $sql = "INSERT INTO movimentacoes (descricao, valor, data_movimentacao, categoria_id) 
                VALUES (:desc, :val, :data, :cat)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':desc' => $descricao,
            ':val' => $valor,
            ':data' => $data,
            ':cat' => $categoria_id
        ]);

        // Sucesso! Volta para o painel
        header('Location: ../index.php?status=sucesso');
    } catch (PDOException $e) {
        echo "Erro ao salvar: " . $e->getMessage();
    }
}