<?php

require_once __DIR__ . '/../dao/enderecoDAO.php';
require_once __DIR__ . '/../dao/anuncioDAO.php';
require_once __DIR__ . '/../dao/condominioDAO.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/anexoDAO.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../database/banco.php';

class ImovelService
{

    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function cadastrar(Imovel $imovel): Imovel
    {
        $this->bancoDados->beginTransaction();

        try {
            if ($imovel->getEndereco() !== null) {
                $enderecoDAO = new EnderecoDAO();
                $verificar = $enderecoDAO->verificar($imovel->getEndereco());
                if ($verificar) {
                    $idEndereco = $verificar->getId();
                    $imovel->getEndereco()->setId($idEndereco);
                } else {
                    $idEndereco = $enderecoDAO->cadastrar($imovel->getEndereco());
                    $imovel->getEndereco()->setId($idEndereco);
                }
            }

            $anuncioDAO = new AnuncioDAO();
            $idAnuncio = $anuncioDAO->cadastrar($imovel->getAnuncio());
            $imovel->getAnuncio()->setId($idAnuncio);


            if ($imovel->getAnuncio()->getAnexos() !== null && count($imovel->getAnuncio()->getAnexos()) > 0) {
                $anexoDAO = new AnexoDAO();
                foreach ($imovel->getAnuncio()->getAnexos() as $anexo) {
                    $anexo->setId($imovel->getAnuncio()->getId());
                    $anexoDAO->cadastrar($anexo);
                }
            }

            $imovel->getAnuncio()->setId($idAnuncio);

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


            // if ($imovel->getProprietarios()) {
            //     foreach ($imovel->getProprietarios() as $prop) {
            //         $stmtProp = bancoDados->prepare("
            //         INSERT IGNORE INTO proprietario_imovel (id_proprietario, id_imovel)
            //         VALUES (?, ?)
            //     ");
            //         $stmtProp->execute([
            //             $prop->getId(),
            //             $idImovel
            //         ]);
            //     }
            // }


            // if ($imovel->getFiltros()) {
            //     foreach ($imovel->getFiltros() as $filtro) {
            //         $stmt = bancoDados->prepare("
            //             INSERT IGNORE INTO filtro (nome)
            //             VALUES (:nome)
            //         ");
            //         $stmt->execute([':nome' => $filtro]);

            //         $stmt = bancoDados->prepare("
            //             SELECT id
            //             FROM filtro
            //             WHERE nome =  :nome
            //         ");
            //         $stmt->execute([':nome' => $filtro]);

            //         $idFiltro = $stmt->fetchColumn();

            //         $stmt = bancoDados->prepare("
            //             INSERT IGNORE INTO imovel_filtros (id_imovel, id_filtro)
            //             VALUES (:id_imovel, :id_filtro)
            //         ");
            //         $stmt->execute([
            //             ':id_imovel' => $idImovel,
            //             ':id_filtro' => $idFiltro
            //         ]);
            //     }
            // }

            $imovelDAO = new ImovelDAO();
            $idImovel = $imovelDAO->cadastrar($imovel);
            $imovel->setId($idImovel);

            $this->bancoDados->commit();

            return $imovel;
        } catch (Exception $e) {
            error_log("ERRO ImovelService->cadastrar: " . $e->getMessage());
            $this->bancoDados->rollBack();
            throw $e;
        }
    }

    public function atualizar(Imovel $imovel): void
    {
        $this->bancoDados->beginTransaction();


        try {
            $anuncioDAO = new AnuncioDAO();
            $anuncioDAO->atualizar($imovel->getAnuncio());

            if (($imovel->getAnuncio()->getAnexos() !== null && count($imovel->getAnuncio()->getAnexos()) > 0) || ($imovel->getAnuncio()->getImagens() !== null && count($imovel->getAnuncio()->getImagens()) > 0) || ($imovel->getAnuncio()->getVideos() !== null && count($imovel->getAnuncio()->getVideos()) > 0)) {
                $anexoDAO = new AnexoDAO();
                $anexos = $imovel->getAnuncio()->getImagens() + $imovel->getAnuncio()->getVideos() + $imovel->getAnuncio()->getAnexos();
                foreach ($anexos as $anexo) {
                    if ($anexo->getIdAnuncio() === null) {
                        $anexo->setIdAnuncio($imovel->getAnuncio()->getId());
                        $anexoDAO->cadastrar($anexo);
                    } else {
                        $anexoDAO->atualizar($anexo);
                    }
                }
            }

            $this->bancoDados->commit();
        } catch (Exception $e) {
            error_log("ERRO ImovelService->atualizar: " . $e->getMessage());
            $this->bancoDados->rollBack();
            throw $e;
        }
    }

    public function atualizarAnuncio(Anuncio $anuncio): void
    {
        $this->bancoDados->beginTransaction();

        try {
            $anuncioDAO = new AnuncioDAO();
            $anuncioDAO->atualizar($anuncio);

            if (($anuncio->getAnexos() !== null && count($anuncio->getAnexos()) > 0) || ($anuncio->getImagens() !== null && count($anuncio->getImagens()) > 0) || ($anuncio->getVideos() !== null && count($anuncio->getVideos()) > 0)) {
                $anexoDAO = new AnexoDAO();
                $anexos = $anuncio->getImagens() + $anuncio->getVideos() + $anuncio->getAnexos();
                foreach ($anexos as $anexo) {
                    if ($anexo->getIdAnuncio() === null) {
                        $anexo->setIdAnuncio($anuncio->getId());
                        $anexoDAO->cadastrar($anexo);
                    } else {
                        $anexoDAO->atualizar($anexo);
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
