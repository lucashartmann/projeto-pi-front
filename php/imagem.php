<?php

require_once(__DIR__ . '/database/banco.php');

$banco = new Banco();
$pdo = $banco->getDb();

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    exit;
}

$stmt = $pdo->prepare("SELECT midia FROM midia_anuncio WHERE id_midia = ?");
$stmt->execute([$id]);

$img = $stmt->fetchColumn();

if (!$img) {
    http_response_code(404);
    exit;
}

header("Content-Type: image/jpeg");
echo $img;