<?php

require_once __DIR__ . '/../dao/enderecoDAO.php';
require_once __DIR__ . '/../dao/anuncioDAO.php';
require_once __DIR__ . '/../dao/condominioDAO.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/anuncioService.php';



class ImovelService
{


    private Banco $bancoDados;
    private EnderecoDAO $enderecoDAO;
    private AnuncioDAO $anuncioDAO;
    private CondominioDAO $condominioDAO;
    private AnuncioService $anuncioService;
    public function __construct()
    {
        $this->enderecoDAO = new EnderecoDAO();
        $this->anuncioDAO = new AnuncioDAO();
        $this->condominioDAO = new CondominioDAO();
        $this->anuncioService = new AnuncioService();
        $this->bancoDados = Banco::getInstance();
    }

    public function cadastrar($imovel)
    {
        $this->bancoDados->beginTransaction();

        try {
            if ($imovel->getEndereco() !== null) {
                if ($this->enderecoDAO->verificar($imovel->getEndereco())) {
                    throw new Exception("Já existe um imóvel cadastrado com este endereço");
                }
                $idEndereco = $this->enderecoDAO->cadastrar($imovel->getEndereco());
                $imovel->getEndereco()->setId($idEndereco);
            }
            if ($imovel->getAnuncio() !== null) {
                $idAnuncio = $this->anuncioService->cadastrar($imovel->getAnuncio());
                $imovel->getAnuncio()->setId($idAnuncio);
            }

            $condominioConsulta = $this->condominioDAO->buscarPorEndereco($imovel->getEndereco());
            if ($condominioConsulta !== null) {
                if ($imovel->getCondominio() == null) {
                    $imovel->setCondominio($condominioConsulta);
                } else {
                    $imovel->getCondominio()->setId($condominioConsulta->getId());
                    $this->condominioDAO->atualizar($imovel->getCondominio());
                }
            } else {
                if ($imovel->getCondominio() !== null) {
                    $idCondominio = $this->condominioDAO->cadastrar($imovel->getCondominio());
                    $imovel->getCondominio()->setId($idCondominio);
                }
            }

            $idImovel = $this->anuncioDAO->cadastrar($imovel);
            $imovel->setId($idImovel);

            $this->bancoDados->commit();

            return $imovel;
        } catch (Exception $e) {
            $this->bancoDados->rollBack();
            throw $e;
        }
    }

    public function atualizar($imovel)
    {
        $this->bancoDados->beginTransaction();

        try {
            $this->bancoDados->commit();
        } catch (Exception $e) {

            $this->bancoDados->rollBack();
            throw $e;
        }
    }
}
