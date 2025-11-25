<?php // get_latest.php
// hakee uusimmat 8 blogipostausta tietokannasta ja palauttaa JSON muodossa

header('Content-Type: application/json; charset=utf-8');

try {
    // MySQL yhdistäminen
    // oletus XAMPP MySQL asetukset:
    $dbHost = '127.0.0.1';
    $dbName = 'blogitekstit';
    $dbUser = 'root';
    $dbPass = '';
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

    // Luo PDO yhteys
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // hakee uusimmat 8 postausta taulusta blogit päivämäärän ja ajan mukaan; ORDER BY Pvm DESC, Klo DESC LIMIT 8
    $sql = 'SELECT ID, Pvm, Klo, Otsikko, Teksti, Kuva, Tykkaykset FROM blogit ORDER BY Pvm DESC, Klo DESC LIMIT 8';
    $stmt = $pdo->query($sql);
    $rows = [];//taulukko tuloksille
    while ($r = $stmt->fetch()) {   // käy läpi rivit
        // käsittelee Kuva-kentän
        if (!empty($r['Kuva'])) { // jos Kuva-kenttä ei ole tyhjä
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
            // jos ei kuvaa, null
            $r['Kuvasrc'] = null;
        }

        // Tykkäykset kokonaislukuna
        $r['Tykkaykset'] = isset($r['Tykkaykset']) ? (int)$r['Tykkaykset'] : 0;

        $rows[] = $r; // tiedot lisätään taulukkoon
    }

    // Palauttaa JSON datan muuttujasta $rows
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) { // käsittelee tietokanta virheet
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {    // käsittelee muut virheet
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
