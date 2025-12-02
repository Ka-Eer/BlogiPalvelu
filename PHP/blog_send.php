<?php
// Tallennetaan blogin tietokantaan blogitekstit > blogit tauluun

try {
	//oletus XAMPP MySQL asetukset:
	$dbHost = '127.0.0.1';
	//Tietokannan nimi josta haetaan; $dbName = 'blogitekstit';
	$dbName = 'blogipalvelu_db';
	$dbUser = 'root';
	$dbPass = '';
	$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

	// Luo PDO yhteys
	$pdo = new PDO($dsn, $dbUser, $dbPass, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]);

	// aloittaa transaktion ENNEN insertiä
	$pdo->beginTransaction();

	// Haetaan POST datat
	$otsikko = isset($_POST['blogTextTitle']) ? $_POST['blogTextTitle'] : '';
	$teksti = isset($_POST['blogText']) ? $_POST['blogText'] : '';
	$pvm = date('Y-m-d');
	$klo = date('H:i:s');



	// Insert blogipostaus tietokantaan ilman kuvaa ja tageja
	$sql = 'INSERT INTO blogit (Pvm, Klo, Otsikko, Teksti) VALUES (:pvm, :klo, :otsikko, :teksti)';
	$stmt = $pdo->prepare($sql);
	$stmt->execute([':pvm' => $pvm, ':klo' => $klo, ':otsikko' => $otsikko, ':teksti' => $teksti]);

	// Hae juuri luodun rivin ID
	$insertId = $pdo->lastInsertId();

	// Tallenna tagit blog_tag-liitostauluun
	if (!empty($_POST['tags']) && is_array($_POST['tags'])) {
		$tagStmt = $pdo->prepare('INSERT INTO blog_tag (blog_ID, tag_ID) VALUES (?, ?)');
		foreach ($_POST['tags'] as $tagId) {
			// Varmista että tagId on numero
			if (is_numeric($tagId)) {
				$tagStmt->execute([$insertId, (int)$tagId]);
			}
		}
	}

	$uploadedPath = null;

	// Kuvan lähettäminen
	if (!empty($_FILES['blogImg']['tmp_name']) && is_uploaded_file($_FILES['blogImg']['tmp_name'])) {
		// validoi tiedoston koko ja tyyppi
		$maxBytes = 15 * 1024 * 1024; // 15 MB limit
		$fileSize = $_FILES['blogImg']['size'];
		if ($fileSize > $maxBytes) {//jos tiedosto on liian suuri
			throw new Exception('Tiedosto ylittää sallitun koon.');
		}

		// tarkista tiedoston tyyppi
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mime = $finfo->file($_FILES['blogImg']['tmp_name']);
		$allowed = [ // sallitut tiedostotyypit
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/gif' => 'gif',
			'image/webp' => 'webp'
		];
		if (!array_key_exists($mime, $allowed)) {//jos väärä tiedostotyyppi
			throw new Exception('Tiedostomuoto ei ole sallittu.');
		}


		// valmistaa Kuvat hakemiston ja per-ID kansion projektin juureen
		// Aseta polku /Kuvat/Blogit/{ID}/
		$baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Kuvat' . DIRECTORY_SEPARATOR . 'Blogit';
		if (!is_dir($baseDir)) {
			if (!mkdir($baseDir, 0755, true) && !is_dir($baseDir)) {
				throw new Exception('Kuvat/Blogit-kansion luonti epäonnistui.');
			}
		}

		// Luo kansio kuvalle käyttäen juuri lisätyn rivin ID:tä
		$idDir = $baseDir . DIRECTORY_SEPARATOR . $insertId;
		if (!is_dir($idDir)) {
			if (!mkdir($idDir, 0755, true) && !is_dir($idDir)) {
				throw new Exception('Kansioon kirjoittaminen epäonnistui.');
			}
		}

		// Siivoaa alkuperäisen tiedostonimen ja luo sille uuden uniikin nimen
		$origName = isset($_FILES['blogImg']['name']) ? $_FILES['blogImg']['name'] : 'upload';
		$ext = $allowed[$mime];
		$baseName = pathinfo($origName, PATHINFO_FILENAME);
		// Korvaa epäkelvolliset merkit tiedostonimessä
		$safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
		$safeBase = substr($safeBase, 0, 200);
		$newFilename = $safeBase . '_' . time() . '.' . $ext;

		$targetRel = 'Kuvat/Blogit/' . $insertId . '/' . $newFilename;//relatiivinen polku tietokantaan
		$targetPath = $idDir . DIRECTORY_SEPARATOR . $newFilename;

		if (!move_uploaded_file($_FILES['blogImg']['tmp_name'], $targetPath)) {
			throw new Exception('Tiedoston siirto epäonnistui.');
		}

		// valinnaisesti aseta käyttöoikeudet
		@chmod($targetPath, 0644);

		// päivitä tietokantarivi kuvan polulla
		$updateSql = 'UPDATE blogit SET Kuva = :kuva WHERE blog_ID = :id';
		$updateStmt = $pdo->prepare($updateSql);
		$updateStmt->execute([':kuva' => $targetRel, ':id' => $insertId]);
		$uploadedPath = $targetRel;
	}

	// commit transaktio
	$pdo->commit();

	// taaksepäin yhteensopivuuden vuoksi palautetaan 'Toimii'
	echo 'Toimii';
} catch (Exception $e) { // katkaise transaktio virhetilanteessa
	if (isset($pdo) && $pdo->inTransaction()) {
		$pdo->rollBack();
	}
	// jos tiedosto luotiin ennen virhettä, poista se
	if (!empty($targetPath) && file_exists($targetPath)) {
		@unlink($targetPath);
	}
	http_response_code(500);
	// Virheilmoitus voidaan näyttää tai kirjata logiin
	echo 'Error: ' . $e->getMessage();
}
?>
