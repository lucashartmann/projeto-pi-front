<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/anexo.php';
require_once __DIR__ . '/../dao/anuncioDAO.php';
require_once __DIR__ . '/../dao/anexoDAO.php';


class AnuncioService {

    private AnuncioDAO $anuncioDAO;
    private AnexoDAO $anexoDAO;
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
        $this->anuncioDAO = new AnuncioDAO();
    }

    public function cadastrar(Anuncio $anuncio): int
    {
        $this->bancoDados->beginTransaction();

        try {
            $idAnuncio = $this->anuncioDAO->cadastrar($anuncio);
            $anuncio->setId($idAnuncio);

            if ($anuncio->getAnexos() !== null && count($anuncio->getAnexos()) > 0) {
                foreach ($anuncio->getAnexos() as $anexo) {
                    $anexo->setAnuncio($anuncio);
                    $this->anexoDAO->cadastrar($anexo);
                }
            }

            $this->bancoDados->commit();
            return $idAnuncio;
        } catch (Exception $e) {
            $this->bancoDados->rollBack();
            throw new Exception("Erro ao cadastrar anúncio: " . $e->getMessage());
        }
    }

    public function atualizar(Anuncio $anuncio): void
    {
        $this->bancoDados->beginTransaction();

        try {
            $this->anuncioDAO->atualizar($anuncio);

            if ($anuncio->getAnexos() !== null && count($anuncio->getAnexos()) > 0) {
                foreach ($anuncio->getAnexos() as $anexo) {
                    $anexo->setAnuncio($anuncio);
                    if ($anexo->getId() === null) {
                        $this->anexoDAO->cadastrar($anexo);
                    } else {
                        $this->anexoDAO->atualizar($anexo);
                    }
                }
            }

            $this->bancoDados->commit();
        } catch (Exception $e) {
            $this->bancoDados->rollBack();
            throw new Exception("Erro ao atualizar anúncio: " . $e->getMessage());
        }
    }
}