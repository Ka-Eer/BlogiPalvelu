<?php // get_latest.php
// hakee uusimmat 8 blogipostausta tietokannasta ja palauttaa JSON muodossa

header('Content-Type: application/json; charset=utf-8');

// Yhdistää MySQL tietokantaan
$dbh = mysqli_connect('localhost', 'root', '', 'blogitekstit');
if (!$dbh) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to connect to MySQL']);
    exit;
}

//  Hakee uusimmat 8 blogipostausta
$sql = "SELECT ID, Pvm, Klo, Otsikko, Teksti, Kuva, Tykkaykset FROM blogit ORDER BY Pvm DESC, Klo DESC LIMIT 8"; // Muuta tarvittaessa rajausta tai järjestystä (ORDER BY, LIMIT)
$res = mysqli_query($dbh, $sql);
if (!$res) {
    http_response_code(500);
    echo json_encode(['error' => 'DB query failed']);
    exit;
}

$rows = [];
while ($row = mysqli_fetch_assoc($res)) {
    // Varmistaa UTF-8 turvallisen uotput ja normalisoi null kuvan
    $row['Otsikko'] = isset($row['Otsikko']) ? $row['Otsikko'] : '';
    $row['Teksti'] = isset($row['Teksti']) ? $row['Teksti'] : '';
    if (!empty($row['Kuva'])) {
        $k = $row['Kuva'];
        // Kuva-kenttä voi sisältää:
        // - jo valmiiksi tallennetun polun kuten 'Kuvat/123/filename.jpg'
        // - pelkän tiedostonimen kuten 'filename.jpg'
        // - binääridataa (BLOB)
        if (is_string($k)) {
            // Jos kenttä alkaa 'Kuvat/' tai sisältää hakemiston erotin, käytä sitä sellaisenaan
            if (strpos($k, 'Kuvat/') === 0 || strpos($k, '/') !== false) {
                // jos polku alkaa ilman johtavaa '/', lisää se jotta URL on aina juurirelatiivinen
                if (strpos($k, '/') === 0) {
                    $row['Kuvasrc'] = $k;
                } else {
                    $row['Kuvasrc'] = '/' . $k;
                }
            } elseif (preg_match('/^[0-9A-Za-z_\-\.]+\.[A-Za-z]{2,6}$/', $k) && strlen($k) < 512) {
                // yksinkertainen tiedostonimi ilman polkua -> tee juurirelatiivinen polku
                $row['Kuvasrc'] = '/Kuvat/' . $k;
            } else {
                // muut merkkijonot, mahdollista että tietokannassa on binääri tallennettuna
                $row['Kuvasrc'] = 'data:image/*;base64,' . base64_encode($k);
            }
        } else {
            // ei-merkkijono (todennäköisesti binääri)
            $row['Kuvasrc'] = 'data:image/*;base64,' . base64_encode($k);
        }
    } else {
        //määritä placeholder jos ei ole kuvaa
        $row['Kuvasrc'] = 'Kuvat/Placeholder2.png';
    }
    // Varmistaa että Tykkäykset on kokonaisluku
    $row['Tykkaykset'] = isset($row['Tykkaykset']) ? (int)$row['Tykkaykset'] : 0;
    $rows[] = $row;
}

echo json_encode($rows, JSON_UNESCAPED_UNICODE);
?>
