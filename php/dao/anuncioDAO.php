<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/anexoDAO.php';
require_once __DIR__ . '/../model/anuncio.php';

class AnuncioDAO
{
    private Banco $bancoDados;
    private AnexoDAO $anexoDAO;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
        $this->anexoDAO = new AnexoDAO();
    }

    public function cadastrarAnuncio($anuncio)
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

            if ($anuncio->getImagens()) {
                foreach ($anuncio->getImagens() as $img) {
                    $this->anexoDAO->cadastrarAnexo($img);
                }
            }

            if ($anuncio->getVideos()) {
                foreach ($anuncio->getVideos() as $video) {
                    $this->anexoDAO->cadastrarAnexo($video);
                }
            }

            if ($anuncio->getAnexos()) {
                foreach ($anuncio->getAnexos() as $anexo) {
                    $this->anexoDAO->cadastrarAnexo($anexo);
                }
            }

            return $this->bancoDados->lastInsertId();
        } catch (Exception $e) {
            error_log("ERRO! Banco->cadastrarAnuncio: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarAnuncio($anuncio)
    {

        try {

            $this->bancoDados->beginTransaction();


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
                INSERT INTO midia_anuncio (
                    id_anuncio,
                    nome_arquivo,
                    tipo
                ) VALUES (
                    :id_anuncio,
                    :nome_arquivo,
                    :tipo
                )
                ON DUPLICATE KEY UPDATE
                    nome_arquivo = VALUES(nome_arquivo),
                    tipo = VALUES(tipo)
            ";

            $stmt = $this->bancoDados->prepare($sql);
            foreach ($anuncio->getImagens() as $img) {
                $stmt->execute([
                    ':tipo' => 'imagem',
                    ':nome_arquivo' => $img->getCaminho(),
                    ':id_anuncio' => $anuncio->getId(),
                ]);
            }

            foreach ($anuncio->getVideos() as $video) {
                $stmt->execute([
                    ':tipo' => 'video',
                    ':nome_arquivo' => $video->getCaminho(),
                    ':id_anuncio' => $anuncio->getId(),
                ]);
            }

            foreach ($anuncio->getAnexos() as $anexo) {
                $stmt->execute([
                    ':tipo' => 'anexo',
                    ':nome_arquivo' => $anexo->getCaminho(),
                    ':id_anuncio' => $anuncio->getId(),
                ]);
            }


            return $this->bancoDados->commit();
        } catch (Exception $e) {
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            error_log("ERRO Banco->atualizarAnuncio: " . $e->getMessage());
            return false;
        }
    }
    public function getAnuncioPorId($idAnuncio)
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
            $mapaAnexos = $this->anexoDAO->getListaAnexosPorIdAnuncio($idAnuncio);
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
            $erro = "ERRO! Banco->getAnuncioPorId: " . $e->getMessage();
            error_log($erro);
            return NULL;
        }
    }

}