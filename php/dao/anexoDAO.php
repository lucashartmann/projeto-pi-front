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

    public function buscarPorCaminho(String $caminho)
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
            error_log("anexoDAO::buscarPorCaminho - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function listarPorIdAnuncio(int $idAnuncio)
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
            error_log("anexoDAO::listarPorIdAnuncio - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function buscarMidiaPorId(int $id)
    {
        try {
            $stmt = $this->bancoDados->prepare("SELECT midia FROM midia_anuncio WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("anexoDAO::buscarMidiaPorId - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function cadastrar(Anexo $anexo)
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
            error_log("anexoDAO::cadastrar - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function atualizar(Anexo $anexo)
    {
        try {
            $sqlQuery = " 
                    UPDATE midia_anuncio 
                    SET id_anuncio = :id_anuncio, nome_arquivo = :nome_arquivo, tipo = :tipo
                    WHERE id = :id
                    ";
            $stmt = $this->bancoDados->prepare($sqlQuery);

            return $stmt->execute([
                ':id' => $anexo->getId(),
                ':id_anuncio' => $anexo->getId(),
                ':nome_arquivo' => $anexo->getCaminho(),
                ':tipo' => $anexo->getTipo() ? $anexo->getTipo()->value : null
            ]);
        } catch (Exception $e) {
            error_log("anexoDAO::atualizar - Error: " . $e->getMessage());
            throw $e;
        }
    }
}
