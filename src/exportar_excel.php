<?php
require_once '../config/db.php';

// Definir nome do arquivo
$arquivo = 'extrato_financeiro_' . date('Y-m-d') . '.xls';

// Configurar cabeçalhos para forçar download como Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$arquivo\"");
header("Cache-Control: max-age=0");

// Buscar dados
$sql = "SELECT m.data_movimentacao, m.descricao, c.nome as categoria, c.tipo, m.valor 
        FROM movimentacoes m 
        JOIN categorias c ON m.categoria_id = c.id 
        ORDER BY m.data_movimentacao DESC";
$stmt = $pdo->query($sql);
?>
<meta charset="utf-8">
<table border="1">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>Data</th>
            <th>Descrição</th>
            <th>Categoria</th>
            <th>Tipo</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td>
                    <?php echo date('d/m/Y', strtotime($row['data_movimentacao'])); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($row['descricao']); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($row['categoria']); ?>
                </td>
                <td>
                    <?php echo ucfirst($row['tipo']); ?>
                </td>
                <td style="color: <?php echo $row['tipo'] == 'receita' ? 'green' : 'red'; ?>;">
                    R$
                    <?php echo number_format($row['valor'], 2, ',', '.'); ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>