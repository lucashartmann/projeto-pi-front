<?php

function imagemParaWebpBlob($caminho, $qualidade = 80, $larguraMax = 1920)
{
    try{
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