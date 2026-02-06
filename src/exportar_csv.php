<?php
require_once '../config/db.php';

// Nome do arquivo com data
$filename = "extrato_financeiro_" . date('Y-m-d') . ".csv";

// Configurar headers para download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Criar output stream
$output = fopen('php://output', 'w');

// Adicionar BOM para Excel reconhecer UTF-8 corretamente
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Cabeçalhos das colunas
fputcsv($output, ['Data', 'Descricao', 'Categoria', 'Tipo', 'Valor'], ';');

// Buscar dados
$sql = "SELECT m.data_movimentacao, m.descricao, c.nome as categoria, c.tipo, m.valor 
        FROM movimentacoes m 
        JOIN categorias c ON m.categoria_id = c.id 
        ORDER BY m.data_movimentacao DESC";

$stmt = $pdo->query($sql);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Formatar valores se necessário
    $row['data_movimentacao'] = date('d/m/Y', strtotime($row['data_movimentacao']));
    $row['valor'] = number_format($row['valor'], 2, ',', '.');
    $row['tipo'] = ucfirst($row['tipo']);

    fputcsv($output, $row, ';');
}

fclose($output);
exit;
