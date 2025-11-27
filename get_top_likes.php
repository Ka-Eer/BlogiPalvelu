<?php
// get_top_likes.php
// Palauttaa JSON-muodossa 10 eniten tykättyä blogipostausta.

header('Content-Type: application/json; charset=utf-8');

try {
    // MySQL yhdistäminen
    // Oletus XAMPP MySQL asetukset:
    $dbHost = '127.0.0.1';
    $dbName = 'blogipalvelu_db';
    $dbUser = 'root';
    $dbPass = '';
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

    // Luo PDO yhteys
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Haetaan blogit ja lasketaan tykkäykset likes-taulusta
    $sql = 'SELECT b.blog_ID, b.Otsikko, COUNT(l.user_ID) AS Tykkaykset
            FROM blogit b
            LEFT JOIN likes l ON b.blog_ID = l.blog_ID
            GROUP BY b.blog_ID, b.Otsikko
            ORDER BY Tykkaykset DESC, b.Pvm DESC, b.blog_ID DESC
            LIMIT 10';
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();
    // Muuta ID -> blog_ID myös JSON:iin
    foreach ($rows as &$r) {
        $r['Tykkaykset'] = (int)$r['Tykkaykset'];
        if (isset($r['blog_ID'])) {
            $r['ID'] = $r['blog_ID'];
        }
    }

    // Palauttaa JSON datan muuttujasta $rows
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) { // käsittelee tietokanta virheet
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) { // käsittelee muut virheet
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
