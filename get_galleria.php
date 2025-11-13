<?php
// get_galleria.php
// hakee galleriaan tiedot tietokannasta ja palauttaa JSON muodossa

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

    // Hakee galleriaan tiedot tietokannan taulusta blogit
    $stmt = $pdo->query('SELECT ID, Pvm, Klo, Otsikko, Teksti, Kuva, Tykkaykset FROM blogit ORDER BY ID ASC');
    $rows = [];
    while ($r = $stmt->fetch()) {
        // jos Kuva on binääri, tallenna se base64-muodossa data URL:ien käyttöä varten asiakaspuolella
        if (!empty($r['Kuva'])) {
            // MySQL blobit voivat palautua merkkijonona; varmista base64 koodaus
            $r['Kuva'] = base64_encode($r['Kuva']);
        } else {
            $r['Kuva'] = null;
        }
        $rows[] = $r;
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
