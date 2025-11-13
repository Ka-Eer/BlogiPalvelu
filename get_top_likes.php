<?php
// get_top_likes.php
// Palauttaa JSON-muodossa 10 eniten tykättyä blogipostausta.

header('Content-Type: application/json; charset=utf-8');

try {
    // MySQL yhdistäminen
    // Oletus XAMPP MySQL asetukset:
    $dbHost = '127.0.0.1';
    //Tietokannan nimi josta haetaan; $dbName = 'blogitekstit';
    $dbName = 'blogitekstit';
    $dbUser = 'root';
    $dbPass = '';
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Haetaan top 10 tykkäysten mukaan
    $sql = 'SELECT ID, Otsikko, Tykkaykset FROM blogit ORDER BY Tykkaykset DESC, Pvm DESC, ID DESC LIMIT 10';
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();

    // Varmista että Tykkaykset on numero
    foreach ($rows as &$r) {
        $r['Tykkaykset'] = isset($r['Tykkaykset']) ? (int)$r['Tykkaykset'] : 0;
    }

    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
