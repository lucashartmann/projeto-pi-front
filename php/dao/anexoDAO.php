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

    public function getConexao()
    {
        return $this->bancoDados;
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
            $posicao_x = $registro['posicao_x'];
            $posicao_y = $registro['posicao_y'];

            $anexoObj = new Anexo($idAnuncio, $caminho, TipoAnexo::tryFrom($tipo));
            $anexoObj->setPosicaoX($posicao_x);
            $anexoObj->setPosicaoY($posicao_y);

            return $anexoObj;
        } catch (Exception $e) {
            error_log("anexoDAO::buscarPorCaminho - Error: " . $e->getMessage());
            throw $e;
        }
    }


    public function listarPorIdAnuncio(int $idAnuncio): array
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
                $posicao_x = $registro['posicao_x'];
                $posicao_y = $registro['posicao_y'];
                if ($tipo == "imagem") {
                    $anexo = new Anexo($idAnuncio, $caminho, TipoAnexo::IMAGEM);
                    $anexo->setPosicaoX($posicao_x);
                    $anexo->setPosicaoY($posicao_y);
                    $imagens[] = $anexo;
                } else if ($tipo == "anexo") {
                    $anexo = new Anexo($idAnuncio, $caminho, TipoAnexo::DOCUMENTO);
                    $anexo->setPosicaoX($posicao_x);
                    $anexo->setPosicaoY($posicao_y);
                    $documentos[] = $anexo;
                } else if ($tipo == "video") {
                    $anexo = new Anexo($idAnuncio, $caminho, TipoAnexo::VIDEO);
                    $anexo->setPosicaoX($posicao_x);
                    $anexo->setPosicaoY($posicao_y);
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
                    INSERT IGNORE INTO midia_anuncio (id_anuncio, nome_arquivo, tipo, posicao_x, posicao_y) 
                    VALUES(:id_anuncio, :nome_arquivo, :tipo, :posicao_x, :posicao_y)
                    ";
            $stmt = $this->bancoDados->prepare($sqlQuery);

            return $stmt->execute([
                ':id_anuncio' => $anexo->getIdAnuncio(),
                ':nome_arquivo' => $anexo->getCaminho(),
                ':tipo' => $anexo->getTipo() ? $anexo->getTipo()->value : null,
                ':posicao_x' => $anexo->getPosicaoX(),
                ':posicao_y' => $anexo->getPosicaoY()
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
                    SET id_anuncio = :id_anuncio, nome_arquivo = :nome_arquivo, tipo = :tipo, posicao_x = :posicao_x, posicao_y = :posicao_y
                    WHERE id = :id
                    ";
            $stmt = $this->bancoDados->prepare($sqlQuery);

            return $stmt->execute([
                ':id' => $anexo->getIdAnuncio(),
                ':id_anuncio' => $anexo->getIdAnuncio(),
                ':nome_arquivo' => $anexo->getCaminho(),
                ':tipo' => $anexo->getTipo() ? $anexo->getTipo()->value : null,
                ':posicao_x' => $anexo->getPosicaoX(),
                ':posicao_y' => $anexo->getPosicaoY()
            ]);
        } catch (Exception $e) {
            error_log("anexoDAO::atualizar - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function cadastrarOuAtualizar(Anexo $anexo)
    {
        try {
            $sqlQuery = " 
                    INSERT INTO midia_anuncio (
                        id_anuncio,
                        nome_arquivo,
                        tipo,
                        posicao_x,
                        posicao_y
                    )
                    VALUES (
                        :id_anuncio,
                        :nome_arquivo,
                        :tipo,
                        :posicao_x,
                        :posicao_y
                    )
                    ON DUPLICATE KEY UPDATE
                        tipo = VALUES(tipo),
                        posicao_x = VALUES(posicao_x),
                        posicao_y = VALUES(posicao_y);
                    ";
            $stmt = $this->bancoDados->prepare($sqlQuery);

            return $stmt->execute([
                ':id_anuncio' => $anexo->getIdAnuncio(),
                ':nome_arquivo' => $anexo->getCaminho(),
                ':tipo' => $anexo->getTipo() ? $anexo->getTipo()->value : null,
                ':posicao_x' => $anexo->getPosicaoX(),
                ':posicao_y' => $anexo->getPosicaoY()
            ]);
        } catch (Exception $e) {
            error_log("anexoDAO::cadastrarOuAtualizar - Error: " . $e->getMessage());
            throw $e;
        }
    }
}
