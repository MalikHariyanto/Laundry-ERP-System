<?php
<?php
require_once '../../config/koneksi.php';

header('Content-Type: application/json');

$jenis = $_GET['jenis'] ?? '';
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';

$sql = "SELECT * FROM keuangan WHERE 1=1";

if ($jenis) {
    $sql .= " AND jenis = '" . mysqli_real_escape_string($conn, $jenis) . "'";
}

if ($start) {
    $sql .= " AND tanggal >= '" . mysqli_real_escape_string($conn, $start) . "'";
}

if ($end) {
    $sql .= " AND tanggal <= '" . mysqli_real_escape_string($conn, $end) . "'";
}

$sql .= " ORDER BY tanggal DESC";

$result = mysqli_query($conn, $sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);