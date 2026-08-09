<?php

$pasta = "C:\\xampp\\htdocs\\PHP\\projeto-pi-front\\assets\\imoveis";

$qualidade = 80;
$larguraMax = 1920;


$arquivos = scandir($pasta);


foreach ($arquivos as $arquivo) {

    $caminho = $pasta . "\\" . $arquivo;

    if (!is_file($caminho)) {
        continue;
    }

    $ext = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));


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


    $novoArquivo = $pasta . "\\" .
        pathinfo($arquivo, PATHINFO_FILENAME)
        . ".webp";


    imagewebp(
        $imagem,
        $novoArquivo,
        $qualidade
    );


    imagedestroy($imagem);


    unlink($caminho);



    error_log("Convertido: $arquivo -> " . basename($novoArquivo) . PHP_EOL);
}


error_log("\nFinalizado!");
