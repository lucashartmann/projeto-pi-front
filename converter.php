<?php

$pasta = "C:\\xampp\\htdocs\\PHP\\projeto-pi-front\\assets";

$qualidade = 80;
$larguraMax = 1920;


$arquivos = scandir($pasta);


foreach ($arquivos as $arquivo) {

    $caminho = $pasta . "\\" . $arquivo;

    if (!is_file($caminho)) {
        continue;
    }

    $ext = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));


    // ignora arquivos que já são webp
    if ($ext === "webp") {
        continue;
    }


    switch ($ext) {

        case "jpg":
        case "jpeg":
            $imagem = imagecreatefromjpeg($caminho);
            break;

        case "png":
            $imagem = imagecreatefrompng($caminho);
            break;

        default:
            continue 2;
    }


    $larguraOriginal = imagesx($imagem);
    $alturaOriginal = imagesy($imagem);


    // redimensiona se passar do limite
    if ($larguraOriginal > $larguraMax) {

        $novaLargura = $larguraMax;

        $novaAltura = intval(
            ($alturaOriginal / $larguraOriginal) * $novaLargura
        );


        $novaImagem = imagecreatetruecolor(
            $novaLargura,
            $novaAltura
        );


        imagecopyresampled(
            $novaImagem,
            $imagem,
            0,
            0,
            0,
            0,
            $novaLargura,
            $novaAltura,
            $larguraOriginal,
            $alturaOriginal
        );


        imagedestroy($imagem);

        $imagem = $novaImagem;
    }


    // novo nome mantendo o original
    $novoArquivo = $pasta . "\\" .
        pathinfo($arquivo, PATHINFO_FILENAME)
        . ".webp";


    imagewebp(
        $imagem,
        $novoArquivo,
        $qualidade
    );


    imagedestroy($imagem);


    // remove o arquivo antigo
    unlink($caminho);


    echo "Convertido: $arquivo -> " . basename($novoArquivo) . PHP_EOL;
}


echo "\nFinalizado!";