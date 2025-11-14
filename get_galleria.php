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
        // Kuva voi olla joko tiedostonimi tai binääridata (BLOB). Palautetaan asiakaspuolelle kenttä Kuvasrc:
        if (!empty($r['Kuva'])) {
            $k = $r['Kuva'];
            if (is_string($k)) {
                // Jos Kuva-kenttä on jo polku tai sisältää hakemiston erotin, käytä sellaisenaan
                if (strpos($k, 'Kuvat/') === 0 || strpos($k, '/') !== false) {
                    // Jos polku ei ala '/', tee siitä juurirelatiivinen
                    if (strpos($k, '/') === 0) {
                        $r['Kuvasrc'] = $k;
                    } else {
                        $r['Kuvasrc'] = '/' . $k;
                    }
                } elseif (preg_match('/^[0-9A-Za-z_\-\.]+\.[A-Za-z]{2,6}$/', $k) && strlen($k) < 512) {
                    // pelkkä tiedostonimi -> juurirelatiivinen polku
                    $r['Kuvasrc'] = '/Kuvat/' . $k;
                } else {
                    // muu merkkijono, oletetaan binääri
                    $r['Kuvasrc'] = 'data:image/*;base64,' . base64_encode($k);
                }
            } else {
                // ei-merkkijono, todennäköisesti BLOB
                $r['Kuvasrc'] = 'data:image/*;base64,' . base64_encode($k);
            }
        } else {
            $r['Kuvasrc'] = null;
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
