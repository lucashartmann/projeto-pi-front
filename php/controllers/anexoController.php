<?php

require_once __DIR__ . '/../dao/anexoDAO.php';
require_once __DIR__ . '/../model/anexo.php';
require_once __DIR__ . '/../utils/imagem.php';


class AnexoController
{


    public function cadastrar(array $dados): array
    {
        try {
            $imagem = $_FILES['imagem'] ?? null;
            if ($imagem) {
                try {
                    if ($imagem['error'] !== UPLOAD_ERR_OK) {
                        error_log("Erro ao fazer upload da imagem: " . $imagem['name'] . " - Código de erro: " . $imagem['error']);
                    }
                    $caminho = salvarLogo($imagem['tmp_name']);
                    if (!$caminho) {
                        error_log("Erro ao salvar a imagem: " . $imagem['name']);
                    }
                    $imagemObj = new Anexo(
                        null,
                        $caminho,
                        TipoAnexo::IMAGEM
                    );
                } catch (Exception $e) {
                    error_log("Exceção ao processar a imagem: " . $imagem['name'] . " - Mensagem: " . $e->getMessage());
                }

                $anexoDAO = new AnexoDAO();
                $anexoCadastrado = $anexoDAO->cadastrar($imagemObj);
                if (!$anexoCadastrado) {
                    return ["status" => "erro", "mensagem" => "Erro ao cadastrar anexo"];
                }
            }
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao cadastrar anexo: " . $e->getMessage()];
        }
    }

    public function buscarPorCaminho(string $caminho): array
    {
        try {
            $anexoDAO = new AnexoDAO();
            $anexo = $anexoDAO->buscarPorCaminho($caminho);
            return ["status" => "sucesso", "mensagem" => "Anexo encontrado com sucesso", "anexo" => $anexo];
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao buscar anexo: " . $e->getMessage()];
        }
    }
}
