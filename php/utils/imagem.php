<?php

function imagemParaWebpBlob($caminho, $qualidade = 80, $larguraMax = 1920)
{
    try {
        $info = getimagesize($caminho);

        switch ($info['mime']) {

            case 'image/jpeg':
                $imagemOriginal = imagecreatefromjpeg($caminho);
                break;

            case 'image/png':
                $imagemOriginal = imagecreatefrompng($caminho);
                break;

            case 'image/webp':
                $imagemOriginal = imagecreatefromwebp($caminho);
                break;

            default:
                return false;
        }

        $larguraOriginal = imagesx($imagemOriginal);
        $alturaOriginal = imagesy($imagemOriginal);


        $imagem = imagecreatetruecolor(
            $larguraOriginal,
            $alturaOriginal
        );


        imagealphablending($imagem, false);
        imagesavealpha($imagem, true);

        imagecopy(
            $imagem,
            $imagemOriginal,
            0,
            0,
            0,
            0,
            $larguraOriginal,
            $alturaOriginal
        );

        imagedestroy($imagemOriginal);

        if ($larguraOriginal > $larguraMax) {

            $novaLargura = $larguraMax;

            $novaAltura = intval(
                ($alturaOriginal / $larguraOriginal) * $novaLargura
            );

            $novaImagem = imagecreatetruecolor(
                $novaLargura,
                $novaAltura
            );

            imagealphablending($novaImagem, false);
            imagesavealpha($novaImagem, true);

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

        error_log(
            "Erro ao processar a imagem: " . $e->getMessage()
        );

        return false;
    }
}



function listarArquivos($id)
{
    $diretorio = str_replace("\\php\\utils", "\\assets\\imoveis\\", __DIR__) . $id;
    if (file_exists($diretorio) && is_dir($diretorio)) {
        return scandir($diretorio);
    }
    return [];
}

function salvarArquivo($nomeTemporario, $nomeArquivo, $id, $tipo)
{
    $blob = null;
    $novoNomeArquivo = "";
    if ($tipo == 'imagem') {
        $blob = imagemParaWebpBlob($nomeTemporario);
        $novoNomeArquivo = uniqid(more_entropy: true) . '.webp';
    } else if ($tipo == 'documento') {
        $novoNomeArquivo = uniqid(more_entropy: true) . "_" . $nomeArquivo;
        $blob = file_get_contents($nomeTemporario);
    }

    if (!$blob || !$novoNomeArquivo) {
        return false;
    }


    $caminhoCompleto = str_replace("\\php\\utils", "\\assets\\imoveis\\", __DIR__) . $id . "/" . $novoNomeArquivo;

    $diretorio = str_replace("\\php\\utils", "\\assets\\imoveis\\", __DIR__) . $id;
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0755, true);
    }

    if (file_exists($caminhoCompleto)) {
        unlink($caminhoCompleto);
    }

    $caminhoParaSalvar = "imoveis/" . $id . "/" . $novoNomeArquivo;

    $is_save = file_put_contents($caminhoCompleto, $blob);

    if ($is_save === false) {
        error_log("Erro ao salvar a imagem: " . $caminhoCompleto);
        return false;
    }

    return $caminhoParaSalvar;
}

function limparPasta($listaAnexos, $id)
{
    $listaCaminhos = array_map(function ($anexo) {
        return $anexo->getCaminho();
    }, $listaAnexos);
    $diretorio = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/imoveis/" . $id;
    error_log($diretorio);
    if (file_exists($diretorio) && is_dir($diretorio)) {
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
