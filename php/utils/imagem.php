<?php

function imagemParaWebpBlob($caminho, $qualidade = 80, $larguraMax = 1920)
{
    try {
        $info = getimagesize($caminho);

        switch ($info['mime']) {

            case 'image/jpeg':
                $imagem = imagecreatefromjpeg($caminho);
                break;

            case 'image/png':
                $imagem = imagecreatefrompng($caminho);
                break;

            case 'image/webp':
                $imagem = imagecreatefromwebp($caminho);
                break;

            default:
                return false;
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


        ob_start();

        imagewebp($imagem, null, $qualidade);

        $blob = ob_get_clean();

        imagedestroy($imagem);

        return $blob;
    } catch (Exception $e) {
        error_log("Erro ao processar a imagem: " . $e->getMessage());
        return false;
    }
}

function salvarImagem($blob, $id)
{
    $blobConvertido = imagemParaWebpBlob($blob);
    if (!$blobConvertido) {
        return false;
    }
    $nomeArquivo = uniqid(more_entropy: true) . '.webp';
    $caminhoCompleto = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/imoveis/" . $id . "/" . $nomeArquivo;

    if (file_exists($caminhoCompleto)) {
        unlink($caminhoCompleto);
    } else {
        $diretorio = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/imoveis/" . $id;
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }
    }

    file_put_contents($caminhoCompleto, base64_decode($blobConvertido));

    return $caminhoCompleto;
}

function limparPasta($listaImagens, $id)
{
    $listaCaminhos = array_map(function ($anexo) {
        return $anexo->getCaminho();
    }, $listaImagens);
    $diretorio = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/imoveis/" . $id;
    if (file_exists($diretorio)) {
        $arquivos = scandir($diretorio);
        foreach ($arquivos as $arquivo) {
            if ($arquivo !== '.' && $arquivo !== '..') {
                $caminhoCompleto = $diretorio . '/' . $arquivo;
                if (is_file($caminhoCompleto) && !in_array($caminhoCompleto, $listaCaminhos)) {
                    unlink($caminhoCompleto);
                }
            }
        }
    }
}

function obterImagem($caminho)
{
    if (file_exists($caminho)) {
        return base64_encode(file_get_contents($caminho));
    } else {
        return null;
    }
}
