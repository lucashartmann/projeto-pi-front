<?php


require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/anexo.php';

class AnexoDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function getAnexoPorCaminho($caminho)
    {
        try {
            $stmt = $this->bancoDados->prepare("
                SELECT * FROM midia_anuncio 
                WHERE nome_arquivo = :caminho
            ");

            $stmt->execute([':caminho' => $caminho]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe anexo com caminho {$caminho}");
            }

            $idAnuncio = (int) $registro['id_anuncio'];
            $tipo = $registro['tipo'];
            $id = (int) $registro['id'];

            $anexoObj = new Anexo($idAnuncio, $caminho, TipoAnexo::tryFrom($tipo));

            return $anexoObj;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getAnexoPorCaminho: " . $e->getMessage();
            error_log($erro);
            return null;
        }
    }

    public function getListaAnexosPorIdAnuncio($idAnuncio)
    {
        try {


            $stmt = $this->bancoDados->prepare("
                        SELECT * FROM midia_anuncio 
                        WHERE id_anuncio = :id_anuncio
                    ");
            $stmt->execute([':id_anuncio' => $idAnuncio]);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $imagens = [];
            $videos = [];
            $documentos = [];
            foreach ($registros as $registro) {
                // $id = $registro['id_anuncio'];
                $id = $registro['id'];
                $tipo = $registro['tipo'];
                $caminho = $registro['nome_arquivo'];
                if ($tipo == "imagem") {
                    $anexo = new Anexo($idAnuncio, $caminho, TipoAnexo::IMAGEM);
                    $imagens[] = $anexo;
                } else if ($tipo == "anexo") {
                    $anexo = new Anexo($idAnuncio, $caminho, TipoAnexo::DOCUMENTO);
                    $documentos[] = $anexo;
                } else if ($tipo == "video") {
                    $anexo = new Anexo($idAnuncio, $caminho, TipoAnexo::VIDEO);
                    $videos[] = $anexo;
                }
            }
            $mapa = [];
            $mapa["Imagens"] = $imagens;
            $mapa["Videos"] = $videos;
            $mapa["Documentos"] = $documentos;
            return $mapa;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getListaAnexosPorIdAnuncio: " . $e->getMessage();
            error_log($erro);
            return [];
        }
    }

    public function getMidiaPorId($id)
    {
        $stmt = $this->bancoDados->prepare("SELECT midia FROM midia_anuncio WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }

    public function cadastrarAnexo($anexo)
    {
        try {
            $sqlQuery = " 
                    INSERT IGNORE INTO midia_anuncio (id_anuncio, nome_arquivo, tipo) 
                    VALUES(:id_anuncio, :nome_arquivo, :tipo)
                    ";
            $stmt = $this->bancoDados->prepare($sqlQuery);

            return $stmt->execute([
                ':id_anuncio' => $anexo->getId(),
                ':nome_arquivo' => $anexo->getCaminho(),
                ':tipo' => $anexo->getTipo() ? $anexo->getTipo()->value : null
            ]);
        } catch (Exception $e) {
            $erro = "ERRO! Banco->cadastrarAnexo: " . $e->getMessage();
            error_log($erro);
            return False;
        }
    }

}