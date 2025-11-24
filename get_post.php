<?php
// get_post.php
// Palauttaa yhden blogipostauksen JSON-muodossa annettuun id:hen perustuen

header('Content-Type: application/json; charset=utf-8');

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

    // Hae id GET-parametrista ja validoi
    if (!isset($_GET['id']) || $_GET['id'] === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id parameter']);
        exit;
    }

    $id = $_GET['id'];
    // salli vain numerot
    if (!ctype_digit((string)$id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid id parameter']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT ID, Pvm, Klo, Otsikko, Teksti, Kuva, Tykkaykset, BT1, BT2, BT3, BT4, BT5, BT6, BT7, BT8, BT9, BT10, BT11 FROM blogit WHERE ID = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => 'Post not found']);
        exit;
    }

    // Muodosta Kuvasrc kuten muissa endpointissa
    if (!empty($row['Kuva'])) {
        $k = $row['Kuva'];
        if (is_string($k)) {
            if (strpos($k, 'Kuvat/') === 0 || strpos($k, '/') !== false) {
                if (strpos($k, '/') === 0) {
                    $row['Kuvasrc'] = $k;
                } else {
                    $row['Kuvasrc'] = '/' . $k;
                }
            } elseif (preg_match('/^[0-9A-Za-z_\-\.]+\.[A-Za-z]{2,6}$/', $k) && strlen($k) < 512) {
                $row['Kuvasrc'] = '/Kuvat/' . $k;
            } else {
                $row['Kuvasrc'] = 'data:image/*;base64,' . base64_encode($k);
            }
        } else {
            $row['Kuvasrc'] = 'data:image/*;base64,' . base64_encode($k);
        }
    } else {
        $row['Kuvasrc'] = null;
    }

    $row['Tykkaykset'] = isset($row['Tykkaykset']) ? (int)$row['Tykkaykset'] : 0;

    echo json_encode($row, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>