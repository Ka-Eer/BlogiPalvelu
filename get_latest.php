<?php
header('Content-Type: application/json; charset=utf-8');

$dbh = mysqli_connect('localhost', 'root', '', 'blogitekstit');
if (!$dbh) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to connect to MySQL']);
    exit;
}

$sql = "SELECT ID, Pvm, Klo, Otsikko, Teksti, Kuva, Tykkaykset FROM blogit ORDER BY Pvm DESC, Klo DESC LIMIT 8";
$res = mysqli_query($dbh, $sql);
if (!$res) {
    http_response_code(500);
    echo json_encode(['error' => 'DB query failed']);
    exit;
}

$rows = [];
while ($row = mysqli_fetch_assoc($res)) {
    // Ensure UTF-8 safe output and normalize null image
    $row['Otsikko'] = isset($row['Otsikko']) ? $row['Otsikko'] : '';
    $row['Teksti'] = isset($row['Teksti']) ? $row['Teksti'] : '';
    $row['Kuvasrc'] = (!empty($row['Kuva'])) ? 'Kuvat/' . $row['Kuva'] : 'Kuvat/Placeholder2.png';
    $row['Tykkaykset'] = isset($row['Tykkaykset']) ? (int)$row['Tykkaykset'] : 0;
    $rows[] = $row;
}

echo json_encode($rows, JSON_UNESCAPED_UNICODE);
?>
