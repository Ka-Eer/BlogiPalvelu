<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
$user_ID = $_SESSION['user_ID'];
$blog_ID = isset($_POST['blog_ID']) ? intval($_POST['blog_ID']) : 0;

if (!$blog_ID) {
    echo json_encode(['error' => 'No blog_ID']);
    exit;
}

$dbh = new mysqli('localhost', 'root', '', 'blogipalvelu_db');
if ($dbh->connect_error) {
    echo json_encode(['error' => 'DB error']);
    exit;
}

// Tarkista onko jo tykätty
$stmt = $dbh->prepare('SELECT 1 FROM likes WHERE blog_ID=? AND user_ID=?');
$stmt->bind_param('ii', $blog_ID, $user_ID);
$stmt->execute();
$stmt->store_result();
$liked = $stmt->num_rows > 0;
$stmt->close();

if ($liked) {
    // Poista tykkäys
    $stmt = $dbh->prepare('DELETE FROM likes WHERE blog_ID=? AND user_ID=?');
    $stmt->bind_param('ii', $blog_ID, $user_ID);
    $stmt->execute();
    $stmt->close();
    $liked = false;
} else {
    // Lisää tykkäys
    $stmt = $dbh->prepare('INSERT INTO likes (blog_ID, user_ID) VALUES (?, ?)');
    $stmt->bind_param('ii', $blog_ID, $user_ID);
    $stmt->execute();
    $stmt->close();
    $liked = true;
}

// Hae uusi tykkäysmäärä
$stmt = $dbh->prepare('SELECT COUNT(*) FROM likes WHERE blog_ID=?');
$stmt->bind_param('i', $blog_ID);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

echo json_encode(['liked' => $liked, 'count' => $count]);
?>
