<?php
<?php
require_once '../../controller/FinanceController.php';
require_once '../../config/koneksi.php';

header('Content-Type: application/json');

$controller = new FinanceController($conn);
$period = $_GET['period'] ?? 'monthly';

switch($period) {
    case 'quarterly':
        $data = $controller->getQuarterlyData();
        break;
    case 'yearly':
        $data = $controller->getYearlyData();
        break;
    default:
        $data = $controller->getMonthlyData();
}

$result = [
    'labels' => [],
    'pendapatan' => [],
    'pengeluaran' => [],
    'laba_rugi' => []
];

while($row = mysqli_fetch_assoc($data)) {
    if($period === 'monthly') {
        $result['labels'][] = date('M', mktime(0, 0, 0, $row['bulan'], 1));
    } else {
        $result['labels'][] = $row[$period === 'quarterly' ? 'quarter' : 'tahun'];
    }
    $result['pendapatan'][] = (float)$row['pendapatan'];
    $result['pengeluaran'][] = (float)$row['pengeluaran'];
    $result['laba_rugi'][] = (float)$row['laba_rugi'];
}

echo json_encode($result);