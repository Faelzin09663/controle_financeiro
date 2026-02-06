<?php
require_once 'config/db.php';

$cats = $pdo->query('SELECT * FROM categorias')->fetchAll();

$sql = "SELECT m.*, c.nome AS categoria_nome, c.tipo AS categoria_tipo 
        FROM movimentacoes m 
        JOIN categorias c ON m.categoria_id = c.id 
        ORDER BY m.data_movimentacao DESC";

$movs = $pdo->query($sql)->fetchAll();

$saldo = 0;
$entradas = 0;
$saidas = 0;

foreach ($movs as $m) {
    $tipo = isset($m['categoria_tipo']) ? $m['categoria_tipo'] : 'despesa';

    if ($tipo == "receita") {
        $saldo += $m['valor'];
        $entradas += $m['valor'];
    } else {
        $saldo -= $m['valor'];
        $saidas += $m['valor'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Gastos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-valor {
            font-size: 2rem;
            font-weight: bold;
        }

        .tipo-receita {
            color: green;
        }

        .tipo-despesa {
            color: red;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Meu Controle Financeiro</h2>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center border-success mb-3">
                    <div class="card-header bg-success text-white">Entradas</div>
                    <div class="card-body">
                        <p class="card-valor text-success">R$ <?php echo number_format($entradas, 2, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-danger mb-3">
                    <div class="card-header bg-danger text-white">Saídas</div>
                    <div class="card-body">
                        <p class="card-valor text-danger">R$ <?php echo number_format($saidas, 2, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-primary mb-3">
                    <div class="card-header bg-primary text-white">Saldo Total</div>
                    <div class="card-body">
                        <p class="card-valor <?php echo $saldo >= 0 ? 'text-primary' : 'text-danger'; ?>">
                            R$ <?php echo number_format($saldo, 2, ',', '.'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body" style="height: 350px;">
                        <h5 class="text-center">Gastos por Categoria</h5>
                        <canvas id="meuGrafico"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body" style="height: 350px;">
                        <h5 class="text-center">Receitas vs. Despesas</h5>
                        <canvas id="graficoBarras"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body" style="height: 300px;">
                        <h5 class="text-center">Evolução do Saldo (Últimos 30 dias)</h5>
                        <canvas id="graficoEvolucao"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-9">
                <button type="button" class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#modalNova">
                    + Nova Movimentação
                </button>
            </div>
            <div class="col-md-3">
                <a href="src/exportar_excel.php" class="btn  btn-dark w-100">
                    📥 Exportar Excel
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movs as $m): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($m['data_movimentacao'])); ?></td>
                                <td><?php echo htmlspecialchars($m['descricao']); ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $m['categoria_nome']; ?></span>
                                </td>
                                <td
                                    class="<?php echo $m['categoria_tipo'] == 'receita' ? 'text-success' : 'text-danger'; ?> fw-bold">
                                    <?php echo $m['categoria_tipo'] == 'despesa' ? '- ' : ''; ?>
                                    R$ <?php echo number_format($m['valor'], 2, ',', '.'); ?>
                                </td>
                                <td>
                                    <a href="src/excluir.php?id=<?php echo $m['id']; ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Tem certeza que quer apagar este item?');">
                                        🗑️
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNova" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="src/adicionar_movimentacao.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Nova Transação</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Descrição</label>
                            <input type="text" name="descricao" class="form-control" required
                                placeholder="Ex: Mercado, Salário...">
                        </div>
                        <div class="mb-3">
                            <label>Valor (R$)</label>
                            <input type="number" name="valor" step="0.01" class="form-control" required
                                placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label>Data</label>
                            <input type="date" name="data" class="form-control" value="<?php echo date('Y-m-d'); ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label>Categoria</label>
                            <select name="categoria" class="form-select" required>
                                <?php foreach ($cats as $c): ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo $c['nome']; ?> (<?php echo ucfirst($c['tipo']); ?>)
                                    </option> <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php
    // DADOS GRÁFICO 1: ROSCA (Gastos)
    $sqlGrafico = "SELECT c.nome, SUM(m.valor) as total 
               FROM movimentacoes m 
               JOIN categorias c ON m.categoria_id = c.id 
               WHERE c.tipo = 'despesa' 
               GROUP BY c.nome";
    $dadosGrafico = $pdo->query($sqlGrafico)->fetchAll(PDO::FETCH_ASSOC);

    $labelsRosca = [];
    $valoresRosca = [];
    foreach ($dadosGrafico as $d) {
        $labelsRosca[] = $d['nome'];
        $valoresRosca[] = $d['total'];
    }

    // DADOS GRÁFICO 3: LINHA (Evolução) - ESTAVA FALTANDO
    $sqlEvo = "SELECT 
               data_movimentacao, 
               SUM(CASE WHEN c.tipo = 'receita' THEN m.valor ELSE -m.valor END) as total_dia
           FROM movimentacoes m
           JOIN categorias c ON m.categoria_id = c.id
           WHERE m.data_movimentacao >= CURDATE() - INTERVAL 30 DAY
           GROUP BY m.data_movimentacao
           ORDER BY m.data_movimentacao ASC";

    $dadosEvo = $pdo->query($sqlEvo)->fetchAll();

    $evoLabels = [];
    $evoValores = [];
    $saldoCorrente = 0;
    foreach ($dadosEvo as $dia) {
        $saldoCorrente += $dia['total_dia'];
        $evoLabels[] = date('d/m', strtotime($dia['data_movimentacao']));
        $evoValores[] = $saldoCorrente;
    }
    ?>

    <script>
        // GRÁFICO 1: ROSCA (Gastos)
        const ctxRosca = document.getElementById('meuGrafico');
        if (ctxRosca && <?php echo json_encode($valoresRosca); ?>.length > 0) {
            new Chart(ctxRosca, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($labelsRosca); ?>,
                    datasets: [{
                        label: 'Gastos por Categoria',
                        data: <?php echo json_encode($valoresRosca); ?>,
                        backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9f40', '#c0392b', '#8e44ad'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        // GRÁFICO 2: BARRAS (Receita vs Despesa) - ESTAVA FALTANDO
        const ctxBar = document.getElementById('graficoBarras');
        if (ctxBar) {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Movimentações no Período'],
                    datasets: [
                        {
                            label: 'Receitas',
                            data: [<?php echo $entradas; ?>],
                            backgroundColor: '#2ecc71'
                        },
                        {
                            label: 'Despesas',
                            data: [<?php echo $saidas; ?>],
                            backgroundColor: '#e74c3c'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // GRÁFICO 3: LINHA (Evolução) - ESTAVA SOLTO
        const ctxEvo = document.getElementById('graficoEvolucao');
        if (ctxEvo && <?php echo json_encode($evoValores); ?>.length > 0) {
            new Chart(ctxEvo, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($evoLabels); ?>,
                    datasets: [{
                        label: 'Saldo Acumulado',
                        data: <?php echo json_encode($evoValores); ?>,
                        fill: false,
                        borderColor: '#3498db',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        }
    </script>

</body>

</html>