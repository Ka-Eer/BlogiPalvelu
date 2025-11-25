<?php
// php tuo html sivut palvelimelle

// XAMPP Apache palvelin
// C:\xampp\htdocs kansion sisältö korvattu GitHub repo sisällöllä

$pages = [
    '' => 'index.html',
    'index' => 'index.html',
    'blogi' => 'blogi.html',
    'blogit' => 'blogit.html',
    'galleria' => 'galleria.html',
];

$req = isset($_GET['page']) ? (string) $_GET['page'] : '';

if (array_key_exists($req, $pages)) {
    $file = __DIR__ . DIRECTORY_SEPARATOR . $pages[$req];
    if (is_readable($file)) {
        header('Content-Type: text/html; charset=utf-8');
        // välttää tiedoston lataaminen kokonaan muistiin ennen lähettämistä
        readfile($file);
        exit;
    } else {    // tiedostoa ei löydy
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>File not found: " . htmlspecialchars($pages[$req]) . "</p>";
        exit;
    }
}
