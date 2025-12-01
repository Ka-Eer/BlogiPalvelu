<?php
// get_galleria.php
// hakee galleriaan tiedot tietokannasta ja palauttaa JSON muodossa

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

		// Hakee galleriaan tiedot ja laskee tykkäykset likes-taulusta
		$sql = 'SELECT b.blog_ID, b.Pvm, b.Klo, b.Otsikko, b.Teksti, b.Kuva, COUNT(l.user_ID) AS Tykkaykset
			FROM blogit b
			LEFT JOIN likes l ON b.blog_ID = l.blog_ID
			GROUP BY b.blog_ID, b.Pvm, b.Klo, b.Otsikko, b.Teksti, b.Kuva
			ORDER BY b.blog_ID ASC';
		$stmt = $pdo->query($sql);
		$rows = [];
		while ($r = $stmt->fetch()) {
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
		// Tykkäykset kokonaislukuna (nyt lasketaan likes-taulusta)
		$r['Tykkaykset'] = (int)$r['Tykkaykset'];
		// Muuta ID -> blog_ID myös JSON:iin
		if (isset($r['blog_ID'])) {
			$r['ID'] = $r['blog_ID'];
		}

		// Hae tagit tälle blogille (nimet ja id:t)
		$tagSql = 'SELECT t.tag_ID, t.tag_Nimi FROM blog_tag bt JOIN tagit t ON bt.tag_ID = t.tag_ID WHERE bt.blog_ID = ? ORDER BY t.tag_ID ASC';
		$tagStmt = $pdo->prepare($tagSql);
		$tagStmt->execute([$r['blog_ID']]);
		$tags = $tagStmt->fetchAll();
		$r['Tagit'] = $tags;
		$rows[] = $r;
	}

	// Palauttaa JSON datan muuttujasta $rows
	echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {// käsittelee tietokanta virheet
	http_response_code(500);
	echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {// käsittelee muut virheet
	http_response_code(500);
	echo json_encode(['error' => $e->getMessage()]);
}

?>
