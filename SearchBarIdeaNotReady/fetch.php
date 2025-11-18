<?php
header('Content-Type: application/json');

$pdo = new PDO("mysql:host=localhost;dbname=blogitekstit", "root", "");

$stmt = $pdo->prepare("SELECT Otsikko FROM blogit");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data, JSON_PRETTY_PRINT);
?>