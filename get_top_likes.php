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

    // Haetaan tiedot tietokannasta taulusta blogit top 10 tykkäysten mukaan; ORDER BY Tykkaykset DESC LIMIT 10
    $sql = 'SELECT blog_ID, Otsikko, Tykkaykset FROM blogit ORDER BY Tykkaykset DESC, Pvm DESC, blog_ID DESC LIMIT 10';
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();  // haetaan kaikki rivit taulukkoon

    // Varmista että Tykkaykset on numero
    foreach ($rows as &$r) {
        $r['Tykkaykset'] = isset($r['Tykkaykset']) ? (int)$r['Tykkaykset'] : 0;
        // Muuta ID -> blog_ID myös JSON:iin
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
