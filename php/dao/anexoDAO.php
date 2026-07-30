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

    public function buscarPorCaminho($caminho)
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
            $erro = "ERRO! Banco->buscarPorCaminho: " . $e->getMessage();
            error_log($erro);
            return null;
        }
    }

    public function listarPorIdAnuncio($idAnuncio)
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
            $erro = "ERRO! Banco->listarPorIdAnuncio: " . $e->getMessage();
            error_log($erro);
            return [];
        }
    }

    public function buscarMidiaPorId($id)
    {
        $stmt = $this->bancoDados->prepare("SELECT midia FROM midia_anuncio WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }

    public function cadastrar($anexo)
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
            $erro = "ERRO! Banco->cadastrar: " . $e->getMessage();
            error_log($erro);
            return False;
        }
    }
}
