
<!--
blogi.html ja send.php tulevat olla xampp > htdocs kansiossa, jolloin ne voi avata osoitteessa localhost/blogi.html -->
<?php
// Tallennetaan blogi ja mahdollinen kuva tiedostojärjestelmään (Kuvat/<id>/...) ja tallennetaan polku tietokantaan
try {
    $dbHost = '127.0.0.1';
    $dbName = 'blogitekstit';
    $dbUser = 'root';
    $dbPass = '';
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $otsikko = isset($_POST['blogTextTitle']) ? $_POST['blogTextTitle'] : '';
    $teksti = isset($_POST['blogText']) ? $_POST['blogText'] : '';
    $pvm = date('Y-m-d');
    $klo = date('H:i:s');

    // Begin transaction so we don't leave orphan DB rows or files
    $pdo->beginTransaction();

    // Insert row without image first
    $sql = 'INSERT INTO blogit (Pvm, Klo, Otsikko, Teksti) VALUES (:pvm, :klo, :otsikko, :teksti)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pvm' => $pvm, ':klo' => $klo, ':otsikko' => $otsikko, ':teksti' => $teksti]);
    $insertId = $pdo->lastInsertId();

    $uploadedPath = null;

    if (!empty($_FILES['blogImg']['tmp_name']) && is_uploaded_file($_FILES['blogImg']['tmp_name'])) {
        // Validate file size and mime type
        $maxBytes = 15 * 1024 * 1024; // 15 MB limit
        $fileSize = $_FILES['blogImg']['size'];
        if ($fileSize > $maxBytes) {
            throw new Exception('Tiedosto ylittää sallitun koon.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['blogImg']['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        if (!array_key_exists($mime, $allowed)) {
            throw new Exception('Tiedostomuoto ei ole sallittu.');
        }

        // Prepare Kuvat directory and per-ID folder
        $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'Kuvat';
        if (!is_dir($baseDir)) {
            if (!mkdir($baseDir, 0755, true) && !is_dir($baseDir)) {
                throw new Exception('Kuvat-kansion luonti epäonnistui.');
            }
        }

        $idDir = $baseDir . DIRECTORY_SEPARATOR . $insertId;
        if (!is_dir($idDir)) {
            if (!mkdir($idDir, 0755, true) && !is_dir($idDir)) {
                throw new Exception('Kansioon kirjoittaminen epäonnistui.');
            }
        }

        // Sanitize original filename and create unique filename
        $origName = isset($_FILES['blogImg']['name']) ? $_FILES['blogImg']['name'] : 'upload';
        $ext = $allowed[$mime];
        $baseName = pathinfo($origName, PATHINFO_FILENAME);
        // Replace unsafe chars
        $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
        $safeBase = substr($safeBase, 0, 200);
        $newFilename = $safeBase . '_' . time() . '.' . $ext;

        $targetRel = 'Kuvat/' . $insertId . '/' . $newFilename; // relative path to store in DB
        $targetPath = $idDir . DIRECTORY_SEPARATOR . $newFilename;

        if (!move_uploaded_file($_FILES['blogImg']['tmp_name'], $targetPath)) {
            throw new Exception('Tiedoston siirto epäonnistui.');
        }

        // Optionally set permissions
        @chmod($targetPath, 0644);

        // Update DB row with the image path
        $updateSql = 'UPDATE blogit SET Kuva = :kuva WHERE ID = :id';
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([':kuva' => $targetRel, ':id' => $insertId]);
        $uploadedPath = $targetRel;
    }

    $pdo->commit();

    // For backward compatibility keep returning 'Toimii'
    echo 'Toimii';
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // If we created a file, try to remove it
    if (!empty($targetPath) && file_exists($targetPath)) {
        @unlink($targetPath);
    }
    http_response_code(500);
    // For debugging you can echo the message; in production consider logging instead
    echo 'Error: ' . $e->getMessage();
}
?>