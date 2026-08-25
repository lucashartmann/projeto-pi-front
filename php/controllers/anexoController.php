<?php

require_once __DIR__ . '/../dao/anexoDAO.php';
require_once __DIR__ . '/../model/anexo.php';
require_once __DIR__ . '/../utils/imagem.php';


class AnexoController
{


    public function cadastrar(array $dados)
    {
        try {
            $imagem = $_FILES['imagem'] ?? null;
            $dimensoes = isset($dados['posicoes']) ? json_decode($dados['posicoes'], true) : null;
            error_log("AnexoController::cadastrar - Dados recebidos: " . json_encode($dados));

            if (!$imagem) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhuma imagem enviada"
                ];
            }

            if ($imagem['error'] !== UPLOAD_ERR_OK) {
                error_log(
                    "Erro ao fazer upload da imagem: " .
                        $imagem['name'] .
                        " - Código de erro: " .
                        $imagem['error']
                );

                return [
                    "status" => "erro",
                    "mensagem" => "Erro ao fazer upload da imagem"
                ];
            }

            try {
                $caminho = salvarLogo($imagem['tmp_name']);

                if (!$caminho) {
                    return [
                        "status" => "erro",
                        "mensagem" => "Erro ao salvar a imagem"
                    ];
                }

                $imagemObj = new Anexo(
                    null,
                    $caminho,
                    TipoAnexo::IMAGEM
                );
                $imagemObj->setPosicaoX($dimensoes['posicao_x'] ?? null);
                $imagemObj->setPosicaoY($dimensoes['posicao_y'] ?? null);
            } catch (Exception $e) {
                error_log(
                    "Exceção ao processar a imagem: " .
                        $imagem['name'] .
                        " - Mensagem: " .
                        $e->getMessage()
                );

                return [
                    "status" => "erro",
                    "mensagem" => "Erro ao processar a imagem"
                ];
            }

            $anexoDAO = new AnexoDAO();

            $anexoCadastrado = $anexoDAO->cadastrarOuAtualizar($imagemObj);

            if (!$anexoCadastrado) {
                return [
                    "status" => "erro",
                    "mensagem" => "Erro ao cadastrar anexo"
                ];
            }

            return [
                "status" => "sucesso",
                "anexo" => $anexoCadastrado
            ];
        } catch (Exception $e) {
            error_log("Erro ao cadastrar anexo: " . $e->getMessage());

            return [
                "status" => "erro",
                "mensagem" => "Erro ao cadastrar anexo"
            ];
        }
    }

    public function buscarPorCaminho(string $caminho)
    {
        try {
            $anexoDAO = new AnexoDAO();
            $anexo = $anexoDAO->buscarPorCaminho($caminho);
            return ["status" => "sucesso", "mensagem" => "Anexo encontrado com sucesso", "anexo" => [
                "idAnuncio" => $anexo->getIdAnuncio(),
                "caminho" => $anexo->getCaminho(),
                "tipo" => $anexo->getTipo() ? $anexo->getTipo()->value : null,
                "largura" => $anexo->getLargura(),
                "altura" => $anexo->getAltura(),
                "posicao_x" => $anexo->getPosicaoX(),
                "posicao_y" => $anexo->getPosicaoY()
            ]];
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao buscar anexo: " . $e->getMessage()];
        }
    }
}
