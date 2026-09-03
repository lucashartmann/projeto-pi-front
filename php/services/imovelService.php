<?php

require_once __DIR__ . '/../dao/enderecoDAO.php';
require_once __DIR__ . '/../dao/anuncioDAO.php';
require_once __DIR__ . '/../dao/condominioDAO.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/anexoDAO.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../database/banco.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class ImovelService
{

    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function remover(int $idImovel): void
    {
        $this->bancoDados->beginTransaction();

        try {
            $imovelDAO = new ImovelDAO();
            $imovelDAO->remover($idImovel);

            $this->bancoDados->commit();
        } catch (Exception $e) {
            error_log("ERRO ImovelService->remover: " . $e->getMessage());
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            throw $e;
        }
    }

    public function cadastrar(Imovel $imovel): Imovel
    {
        $this->bancoDados->beginTransaction();

        try {
            if ($imovel->getEndereco() !== null) {
                $enderecoDAO = new EnderecoDAO();
                $verificar = $enderecoDAO->verificar($imovel->getEndereco());
                if ($verificar) {
                    $imovel->getEndereco()->setId($verificar->getId());
                } else {
                    $imovel->getEndereco()->setId(
                        $enderecoDAO->cadastrar($imovel->getEndereco())
                    );
                }
            }

            if ($imovel->getAnuncio()->getAnexos() !== null && count($imovel->getAnuncio()->getAnexos()) > 0) {
                $anexoDAO = new AnexoDAO();
                foreach ($imovel->getAnuncio()->getAnexos() as $anexo) {
                    $anexo->setIdAnuncio($imovel->getId());
                    $anexoDAO->cadastrar($anexo);
                }
            }

            $condominioDAO = new CondominioDAO();
            $condominioConsulta = $condominioDAO->buscarPorEndereco($imovel->getEndereco());
            if ($condominioConsulta !== null) {
                if ($imovel->getCondominio() == null) {
                    $imovel->setCondominio($condominioConsulta);
                } else {
                    $imovel->getCondominio()->setId($condominioConsulta->getId());
                    $condominioDAO->atualizar($imovel->getCondominio());
                }
            } else {
                if ($imovel->getCondominio() !== null) {
                    $idCondominio = $condominioDAO->cadastrar($imovel->getCondominio());
                    $imovel->getCondominio()->setId($idCondominio);
                }
            }

            $imovelDAO = new ImovelDAO();
            $idImovel = $imovelDAO->cadastrar($imovel);
            $imovel->setId($idImovel);

            $anuncioDAO = new AnuncioDAO();
            $imovel->getAnuncio()->setIdImovel($idImovel);
            $idAnuncio = $anuncioDAO->cadastrar($imovel->getAnuncio());
            $imovel->getAnuncio()->setId($idAnuncio);

            $proprietarioImovelDAO = new ProprietarioImovelDAO();
            if ($imovel->getProprietarios()) {
                foreach ($imovel->getProprietarios() as $proprietario) {
                    $proprietarioImovelDAO->cadastrar($proprietario->getId(), $imovel->getId());
                }
            }

            $filtroDAO = new FiltroDAO();
            if ($imovel->getFiltros()) {
                $filtroDAO->cadastrarAosFiltros($imovel);
            }

            $this->bancoDados->commit();

            return $imovel;
        } catch (Exception $e) {
            error_log("ERRO ImovelService->cadastrar: " . $e->getMessage());
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            throw $e;
        }
    }

    public function atualizar(Imovel $imovel): void
    {
        $this->bancoDados->beginTransaction();


        try {

            $enderecoDAO = new EnderecoDAO();

            if ($imovel->getEndereco() !== null) {
                $verificar = $enderecoDAO->verificar($imovel->getEndereco());
                if ($verificar) {
                    $idEndereco = $verificar->getId();
                    $imovel->getEndereco()->setId($idEndereco);
                } else {
                    $imovel->getEndereco()->setId($enderecoDAO->cadastrar($imovel->getEndereco()));
                }
            }

            error_log("Endereço: " . $imovel->getEndereco()->getRua() . ", " . $imovel->getEndereco()->getNumero() . ", " . $imovel->getEndereco()->getBairro() . ", " . $imovel->getEndereco()->getCep() . ", " . $imovel->getEndereco()->getComplemento() . ", " . $imovel->getEndereco()->getCidade() . ", " . $imovel->getEndereco()->getUf() . ", " . $imovel->getEndereco()->getComplemento());

            $anuncioDAO = new AnuncioDAO();
            $anuncioDAO->atualizar($imovel->getAnuncio());


            $anexoDAO = new AnexoDAO();
            $anexos = array_merge(
                $imovel->getAnuncio()->getImagens() ?? [],
                $imovel->getAnuncio()->getVideos() ?? [],
                $imovel->getAnuncio()->getAnexos() ?? []
            );
            $mapa = $anexoDAO->listarPorIdAnuncio($imovel->getId());
            $anexosExistentes = array_merge(
                $mapa["Imagens"],
                $mapa["Videos"],
                $mapa["Documentos"]
            );

            foreach ($anexos as $a) {
                $existe = false;

                foreach ($anexosExistentes as $ae) {
                    if (
                        $a->getCaminho() === $ae->getCaminho() &&
                        $a->getTipoAnexo() === $ae->getTipoAnexo()
                    ) {
                        $existe = true;
                        break;
                    }
                }

                if (!$existe) {
                    $a->setIdAnuncio($imovel->getId());
                    $anexoDAO->cadastrar($a);
                }
            }

            foreach ($anexosExistentes as $ae) {
                $existe = false;

                foreach ($anexos as $a) {
                    if (
                        $ae->getCaminho() === $a->getCaminho() &&
                        $ae->getTipoAnexo() === $a->getTipoAnexo()
                    ) {
                        $existe = true;
                        break;
                    }
                }

                if (!$existe) {
                    $anexoDAO->getConexao()->remover(
                        "id",
                        $ae->getId(),
                        "midia_anuncio"
                    );
                }
            }


            $condominioDAO = new CondominioDAO();
            $condominioConsulta = $condominioDAO->buscarPorEndereco($imovel->getEndereco());

            if ($condominioConsulta !== null) {
                if ($imovel->getCondominio() == null) {
                    $imovel->setCondominio($condominioConsulta);
                } else {
                    $imovel->getCondominio()->setId($condominioConsulta->getId());
                    $condominioDAO->atualizar($imovel->getCondominio());
                }
            } else {
                if ($imovel->getCondominio() !== null) {
                    $imovel->getCondominio()->setId($condominioDAO->cadastrar($imovel->getCondominio()));
                }
            }

            $proprietarioImovelDAO = new ProprietarioImovelDAO();
            $proprietariosExistentes = $proprietarioImovelDAO->listarPorIdImovel($imovel->getId());
            $proprietariosNovos = $imovel->getProprietarios();

            $idsExistentes = array_map(
                fn($p) => $p->getId(),
                $proprietariosExistentes
            );

            foreach ($proprietariosNovos as $proprietario) {
                if (!in_array($proprietario->getId(), $idsExistentes)) {
                    $proprietarioImovelDAO->cadastrar(
                        $proprietario->getId(),
                        $imovel->getId()
                    );
                }
            }

            $idsNovos = array_map(
                fn($p) => $p->getId(),
                $proprietariosNovos
            );

            foreach ($proprietariosExistentes as $proprietarioExistente) {
                if (!in_array($proprietarioExistente->getId(), $idsNovos)) {
                    $proprietarioImovelDAO->remover(
                        $proprietarioExistente->getId(),
                        $imovel->getId()
                    );
                }
            }


            $filtroDAO = new FiltroDAO();
            $filtrosExistentes = $filtroDAO->listarPorIdImovel($imovel->getId());
            $filtrosNovos = $imovel->getFiltros();
            foreach ($filtrosNovos as $filtro) {
                if (!in_array($filtro, $filtrosExistentes)) {
                    $filtroDAO->cadastrarAosFiltros($imovel);
                }
            }

            foreach ($filtrosExistentes as $filtroExistente) {
                if (!in_array($filtroExistente, $filtrosNovos)) {
                    $filtroDAO->removerDoImovel($filtroExistente, $imovel->getId());
                }
            }


            $imovelDAO = new ImovelDAO();
            $imovelDAO->atualizar($imovel);


            $this->bancoDados->commit();
        } catch (Exception $e) {
            error_log("ERRO ImovelService->atualizar: " . $e->getMessage());
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            throw $e;
        }
    }

    public function atualizarAnuncio(Anuncio $anuncio): void
    {
        $this->bancoDados->beginTransaction();

        try {
            $anuncioDAO = new AnuncioDAO();
            $anuncioDAO->atualizar($anuncio);

            $anexoDAO = new AnexoDAO();
            $anexos = array_merge(
                $anuncio->getImagens() ?? [],
                $anuncio->getVideos() ?? [],
                $anuncio->getAnexos() ?? []
            );
            $mapa = $anexoDAO->listarPorIdAnuncio($anuncio->getIdImovel());
            $anexosExistentes = array_merge(
                $mapa["Imagens"],
                $mapa["Videos"],
                $mapa["Documentos"]
            );

            foreach ($anexos as $a) {
                $existe = false;

                foreach ($anexosExistentes as $ae) {
                    if (
                        $a->getCaminho() === $ae->getCaminho() &&
                        $a->getTipoAnexo() === $ae->getTipoAnexo()
                    ) {
                        $existe = true;
                        break;
                    }
                }

                if (!$existe) {
                    $a->setIdAnuncio($anuncio->getIdImovel());
                    $a->setAnuncio($anuncio);
                    $anexoDAO->cadastrar($a);
                }
            }

            foreach ($anexosExistentes as $ae) {
                $existe = false;

                foreach ($anexos as $a) {
                    if (
                        $ae->getCaminho() === $a->getCaminho() &&
                        $ae->getTipoAnexo() === $a->getTipoAnexo()
                    ) {
                        $existe = true;
                        break;
                    }
                }

                if (!$existe) {
                    $anexoDAO->getConexao()->remover(
                        "id",
                        $ae->getId(),
                        "midia_anuncio"
                    );
                }
            }

            $this->bancoDados->commit();
        } catch (Exception $e) {
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            throw new Exception("Erro ao atualizar anúncio: " . $e->getMessage());
        }
    }
}
