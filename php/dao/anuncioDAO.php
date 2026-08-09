<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/anexoDAO.php';
require_once __DIR__ . '/../model/anuncio.php';

class AnuncioDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function cadastrar(Anuncio $anuncio)
    {
        try {


            $sql = "
            INSERT INTO anuncio (descricao, titulo) 
            VALUES (?, ?)
        ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                $anuncio->getDescricao(),
                $anuncio->getTitulo()
            ]);

            return $this->bancoDados->lastInsertId();
        } catch (Exception $e) {
            error_log("anuncioDAO::cadastrar - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function atualizar(Anuncio $anuncio)
    {

        try {


            $sql = "
            UPDATE anuncio
            SET descricao = :descricao,
                titulo = :titulo
            WHERE id = :id
        ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':descricao' => $anuncio->getDescricao(),
                ':titulo' => $anuncio->getTitulo(),
                ':id' => $anuncio->getId()
            ]);

            $sql = "
                DELETE FROM midia_anuncio
                WHERE id_anuncio = :id_anuncio
            ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_anuncio' => $anuncio->getId()
            ]);

            return true;
        } catch (Exception $e) {
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            error_log("anuncioDAO::atualizar - Error: " . $e->getMessage());
            throw $e;
        }
    }
    public function buscarPorId(int $idAnuncio)
    {
        try {

            $stmt = $this->bancoDados->prepare("
                        SELECT * FROM anuncio
                        WHERE id = ?
                    ");
            $stmt->execute([$idAnuncio]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception("Não existe anúncio com id $idAnuncio");
            }
            $anuncioObj = new Anuncio();
            $idAnuncio = $registro['id'];
            if ($idAnuncio) {
                $idAnuncio = (int) ($idAnuncio);
            }
            $descricao = $registro['descricao'];
            $titulo = $registro['titulo'];
            $anuncioObj->setId($idAnuncio);
            $anuncioObj->setDescricao($descricao);
            $anuncioObj->setTitulo($titulo);
            $anexoDAO = new AnexoDAO();
            $mapaAnexos = $anexoDAO->listarPorIdAnuncio($idAnuncio);
            if ($mapaAnexos && isset($mapaAnexos["Imagens"])) {
                $anuncioObj->setImagens($mapaAnexos["Imagens"]);
            }
            if ($mapaAnexos && isset($mapaAnexos["Videos"])) {
                $anuncioObj->setVideos($mapaAnexos["Videos"]);
            }
            if ($mapaAnexos && isset($mapaAnexos["Documentos"])) {
                $anuncioObj->setAnexos($mapaAnexos["Documentos"]);
            }
            return $anuncioObj;
        } catch (Exception $e) {
            error_log("anuncioDAO::buscarPorId - Error: " . $e->getMessage());
            throw $e;
        }
    }
}
