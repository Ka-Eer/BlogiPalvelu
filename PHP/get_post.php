<?php
// get_post.php
// palauttaa yhden blogipostauksen JSON-muodossa annettuun id:hen perustuen

header('Content-Type: application/json; charset=utf-8');

try {
	// MySQL yhdistäminen
	// oletus XAMPP MySQL asetukset:
	$dbHost = '127.0.0.1';
	$dbName = 'blogipalvelu_db'; // Tietokannan nimi josta haetaan
	$dbUser = 'root';
	$dbPass = '';
	$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

	// Luo PDO yhteys
	$pdo = new PDO($dsn, $dbUser, $dbPass, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]);

	// Tarkista GET-parametri id
	if (!isset($_GET['id']) || $_GET['id'] === '') {
		http_response_code(400);
		echo json_encode(['error' => 'Missing id parameter']);
		exit;
	}
	// haetaan id GET-parametrista
	$id = $_GET['id'];

	// salli vain numerot (turvallisuus)
	if (!ctype_digit((string)$id)) {
		http_response_code(400);
		echo json_encode(['error' => 'Invalid id parameter']);
		exit;
	}

		// Hakee yhden postauksen ja laskee tykkäykset likes-taulusta (ilman BT1-BT12)
		$sql = 'SELECT b.blog_ID, b.Pvm, b.Klo, b.Otsikko, b.Teksti, b.Kuva, 
				 COUNT(l.user_ID) AS Tykkaykset
			 FROM blogit b
			 LEFT JOIN likes l ON b.blog_ID = l.blog_ID
			 WHERE b.blog_ID = ?
			 GROUP BY b.blog_ID, b.Pvm, b.Klo, b.Otsikko, b.Teksti, b.Kuva
			 LIMIT 1';
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$id]);
	$row = $stmt->fetch();

	// tarkistaa löytyykö rivi
	if (!$row) {
		http_response_code(404);
		echo json_encode(['error' => 'Post not found']);
		exit;
	}

	// käsittelee Kuva-kentän
	if (!empty($row['Kuva'])) { // jos Kuva-kenttä ei ole tyhjä
		$k = $row['Kuva'];
		if (is_string($k)) {
			// Jos Kuva-kenttä on jo polku tai sisältää hakemiston erotin, käytä sellaisenaan
			if (strpos($k, 'Kuvat/') === 0 || strpos($k, '/') !== false) {
				// Jos polku alkaa ilman johtavaa '/', tee siitä juurirelatiivinen
				if (strpos($k, '/') === 0) {
					$row['Kuvasrc'] = $k;
				} else {
					$row['Kuvasrc'] = '/' . $k;
				}
			} elseif (preg_match('/^[0-9A-Za-z_\-\.]+\.[A-Za-z]{2,6}$/', $k) && strlen($k) < 512) {
				// pelkkä tiedostonimi -> juurirelatiivinen polku
				$row['Kuvasrc'] = '/Kuvat/' . $k;
			} else {
				// muu merkkijono, oletetaan binääri
				$row['Kuvasrc'] = 'data:image/*;base64,' . base64_encode($k);
			}
		} else {
			// ei-merkkijono, todennäköisesti BLOB
			$row['Kuvasrc'] = 'data:image/*;base64,' . base64_encode($k);
		}
	} else {
		// jos ei kuvaa, aseta null (samanlainen kuin aiempi käytös)
		$row['Kuvasrc'] = null;
	}

	// Tykkäykset kokonaislukuna (nyt lasketaan likes-taulusta)
	$row['Tykkaykset'] = (int)$row['Tykkaykset'];
	// Muuta ID -> blog_ID myös JSON:iin
	if (isset($row['blog_ID'])) {
		$row['ID'] = $row['blog_ID'];
	}

	// Hae tagit tälle blogille (nimet ja id:t)
	$tagSql = 'SELECT t.tag_ID, t.tag_Nimi FROM blog_tag bt JOIN tagit t ON bt.tag_ID = t.tag_ID WHERE bt.blog_ID = ? ORDER BY t.tag_ID ASC';
	$tagStmt = $pdo->prepare($tagSql);
	$tagStmt->execute([$row['blog_ID']]);
	$tags = $tagStmt->fetchAll();
	$row['Tagit'] = $tags;

	// Palauttaa JSON datan muuttujasta $row
	echo json_encode($row, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) { // käsittelee tietokanta virheet
	http_response_code(500);
	echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) { // käsittelee muut virheet
	http_response_code(500);
	echo json_encode(['error' => $e->getMessage()]);
}

?>
