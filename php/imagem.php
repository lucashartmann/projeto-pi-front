<?php

require_once(__DIR__ . '/database/banco.php');

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    exit;
}

$banco = Banco::getInstance();

$img = $banco->getMidiaPorId($id);

if (!$img) {
    http_response_code(404);
    exit;
}

header("Content-Type: image/webp");
echo $img;
