<?php

require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/proprietarioDAO.php';
require_once __DIR__ . '/../dao/enderecoDAO.php';
require_once __DIR__ . '/../dao/condominioDAO.php';
require_once __DIR__ . '/../dao/anuncioDAO.php';

class ImovelController
{
    private ImovelDAO $imovelDAO;
    private UsuarioDAO $usuarioDAO;
    private ProprietarioDAO $proprietarioDAO;
    private EnderecoDAO $enderecoDAO;
    private CondominioDAO $condominioDAO;

    private AnuncioDAO $anuncioDAO;

    public function __construct()
    {
        $this->imovelDAO = new ImovelDAO();
        $this->usuarioDAO = new UsuarioDAO();
        $this->proprietarioDAO = new ProprietarioDAO();
        $this->enderecoDAO = new EnderecoDAO();
        $this->condominioDAO = new CondominioDAO();
        $this->anuncioDAO = new AnuncioDAO();
    }

    function apagarImovel(int $id)
    {
        try {
            $imovel = $this->imovelDAO->getImovelPorId($id);
            if ($imovel) {
                $remocao = $this->imovelDAO->getConexao()->remover("id", $id, "imovel");
                if ($remocao) {
                    if ($imovel->getAnuncio() && $imovel->getAnuncio()->getImagens()) {
                        limparPasta($imovel->getAnuncio()->getImagens(), $imovel->getId());
                    } else if ($imovel->getAnuncio() && $imovel->getAnuncio()->getAnexos()) {
                        limparPasta($imovel->getAnuncio()->getAnexos(), $imovel->getId());
                    } else if ($imovel->getAnuncio() && $imovel->getAnuncio()->getVideos()) {
                        limparPasta($imovel->getAnuncio()->getVideos(), $imovel->getId());
                    }
                    return (["status" => "sucesso", "mensagem" => "Imóvel removido com sucesso"]);
                } else {
                    return (["status" => "erro", "mensagem" => "Erro ao remover imóvel"]);
                }
            } else {
                return (["status" => "erro", "mensagem" => "Imóvel não encontrado"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => $e->getMessage()]);
        }
    }


    public function cadastrarImovel($data)
    {
        try {
            $id = array_key_exists("ref", $data) ? $data["ref"] : 0;
            $nomeCondominio = array_key_exists("nome_condominio", $data) ? $data["nome_condominio"] : "";

            $valorVenda = array_key_exists("valor_venda", $data) ? $data["valor_venda"] : 0.0;
            $valorVenda = $valorVenda ? str_replace(".", "", $valorVenda ?? 0.0) : 0.0;
            $valorVenda = $valorVenda ? str_replace(",", ".", $valorVenda ?? 0.0) : 0.0;
            $valorVenda = $valorVenda ? (float) trim(str_replace(['-', 'R$'], '', $valorVenda) ?? 0) : 0.0;

            $valorAluguel = array_key_exists("valor_aluguel", $data) ? $data["valor_aluguel"] : 0.0;
            $valorAluguel = $valorAluguel ? str_replace(".", "", $valorAluguel ?? 0.0) : 0.0;
            $valorAluguel = $valorAluguel ? str_replace(",", ".", $valorAluguel ?? 0.0) : 0.0;
            $valorAluguel = $valorAluguel ? (float) trim(str_replace(['-', 'R$'], '', $valorAluguel) ?? 0) : 0.0;

            $quantQuartos = array_key_exists("quantidade_quartos", $data) ? (int) ($data["quantidade_quartos"] ?? 0) : 0;
            $quantSalas = array_key_exists("quantidade_salas", $data) ? (int) ($data["quantidade_salas"] ?? 0) : 0;
            $quantVagas = array_key_exists("quantidade_vagas", $data) ? (int) ($data["quantidade_vagas"] ?? 0) : 0;
            $quantBanheiros = array_key_exists("quantidade_banheiros", $data) ? (int) ($data["quantidade_banheiros"] ?? 0) : 0;
            $quantVarandas = array_key_exists("quantidade_varandas", $data) ? (int) ($data["quantidade_varandas"] ?? 0) : 0;
            $categoria = null;
            if (isset($data["categoria"])) {
                $valor = $data["categoria"];
                $categoria = Categoria::tryFrom($valor);
            }
            $status = null;
            $status = Status::tryFrom($data["status"]) ?? null;
            if (!$status) {
                error_log("Status inválido recebido: " . $data["status"]);
                return (["status" => "erro", "mensagem" => "Status inválido"]);
            }

            $iptu = array_key_exists("iptu", $data) ? $data["iptu"] : 0.0;
            $iptu = $iptu ? str_replace(".", "", $iptu ?? 0.0) : 0.0;
            $iptu = $iptu ? str_replace(",", ".", $iptu ?? 0.0) : 0.0;
            $iptu = $iptu ? (float) trim(str_replace(['-', 'R$'], '', $iptu) ?? 0) : 0.0;

            $valorCondominio = array_key_exists("valor_condominio", $data) ? $data["valor_condominio"] : 0.0;
            $valorCondominio = $valorCondominio ? str_replace(".", "", $valorCondominio ?? 0.0) : 0.0;
            $valorCondominio = $valorCondominio ? str_replace(",", ".", $valorCondominio ?? 0.0) : 0.0;
            $valorCondominio = $valorCondominio ? (float) trim(str_replace(['-', 'R$'], '', $valorCondominio) ?? 0) : 0.0;

            $andar = array_key_exists("andar", $data) ? (int) trim($data["andar"] ?? 0) : 0;
            $estado = null;
            isset($data["estado_imovel"]) ? $estado = Estado::tryFrom($data["estado_imovel"]) : null;
            $bloco = array_key_exists("bloco", $data) ? $data["bloco"] : "";
            $anoConstrucao = array_key_exists("ano_construcao", $data) ? (int) trim($data["ano_construcao"] ?? 0) : 0;

            $areaTotal = array_key_exists("area_total", $data) ? $data["area_total"] : 0.0;
            $areaTotal = $areaTotal ? str_replace(".", "", $areaTotal ?? 0.0) : 0.0;
            $areaTotal = $areaTotal ? str_replace(",", ".", $areaTotal ?? 0.0) : 0.0;
            $areaTotal = $areaTotal ? (float) trim(str_replace('m2', '', $areaTotal) ?? 0) : 0.0;

            $areaPrivativa = array_key_exists("area_privativa", $data) ? $data["area_privativa"] : 0.0;
            $situacao = null;
            $areaPrivativa = $areaPrivativa ? str_replace(".", "", $areaPrivativa ?? 0.0) : 0.0;
            $areaPrivativa = $areaPrivativa ? str_replace(",", ".", $areaPrivativa ?? 0.0) : 0.0;
            $areaPrivativa = $areaPrivativa ? (float) trim(str_replace('m2', '', $areaPrivativa) ?? 0) : 0.0;


            isset($data["situacao"]) ? $situacao = Situacao::tryFrom($data["situacao"]) : null;
            $ocupacao = null;
            isset($data["ocupacao"]) ? $ocupacao = Ocupacao::tryFrom($data["ocupacao"]) : null;
            $proprietarios = array_key_exists("proprietarios", $data) ? $data["proprietarios"] : [];
            $corretor = array_key_exists("corretor", $data) ? (int) $data["corretor"] : null;
            $captador = array_key_exists("captador", $data) ? (int) $data["captador"] : null;
            $cep = array_key_exists("cep", $data) ? $data["cep"] : "";
            $imagens = $_FILES["imagens"] ?? [];
            $documentos = $_FILES["documentos"] ?? [];
            // $video = $_FILES["videos"] ?? [];
            if ($cep) {
                $cep = trim(str_replace("-", "", $cep));
            }
            $rua = array_key_exists("rua", $data) ? $data["rua"] : "";
            $bairro = array_key_exists("bairro", $data) ? $data["bairro"] : "";
            $cidade = array_key_exists("cidade", $data) ? $data["cidade"] : "";
            $titulo = array_key_exists("titulo", $data) ? $data["titulo"] : "";
            $descricao = array_key_exists("descricao", $data) ? $data["descricao"] : "";
            $complemento = array_key_exists("complemento", $data) ? $data["complemento"] : "";
            $uf = array_key_exists("uf", $data) ? $data["uf"] : "";
            $numero = array_key_exists("numero", $data) ? (int) trim($data["numero"] ?? null) : null;

            $enderecoObj = new Endereco($rua, $bairro, $cep, $cidade, $uf);
            $enderecoObj->setNumero($numero);
            $enderecoObj->setComplemento($complemento);
            $enderecoObj->setUf($uf);
            $condominioObj = new Condominio(
                $nomeCondominio,
                $enderecoObj
            );

            if ($corretor) {
                $corretor = $this->usuarioDAO->getUsuarioPorId($corretor) ?? null;
            }

            if ($captador) {
                $captador = $this->usuarioDAO->getUsuarioPorId($captador) ?? null;
            }

            if ($proprietarios) {
                $proprietariosObjs = [];
                foreach ($proprietarios as $proprietario) {
                    if (isset($proprietario["id"])) {
                        $proprietarioObj = $this->proprietarioDAO->getProprietarioPorId($proprietario["id"]);
                        if ($proprietarioObj) {
                            $proprietariosObjs[] = $proprietarioObj;
                        }
                    }
                }
                $proprietarios = $proprietariosObjs;
            }
            //  filtros = data->get("filtros", [])

            $imovelObj = NULL;
            if ($id) {
                $imovelObj = $this->imovelDAO->getImovelPorId($id);
                $imovelObj->setDataModificacao(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
            } else {
                $imovelObj = new Imovel($enderecoObj, $status, $categoria);
            }
            $anuncioObj = new Anuncio();
            $anuncioObj->setTitulo($titulo);
            $anuncioObj->setDescricao($descricao);
            $imovelObj->setValorVenda($valorVenda);
            $imovelObj->setValorAluguel($valorAluguel);
            $imovelObj->setQuantQuartos($quantQuartos);
            $imovelObj->setQuantSalas($quantSalas);
            $imovelObj->setQuantVagas($quantVagas);
            $imovelObj->setQuantBanheiros($quantBanheiros);
            $imovelObj->setQuantVarandas($quantVarandas);
            $imovelObj->setCategoria($categoria);
            $imovelObj->setEndereco($enderecoObj);
            $imovelObj->setStatus($status);
            $imovelObj->setIptu($iptu);
            $imovelObj->setValorCondominio($valorCondominio);
            $imovelObj->setAndar($andar);
            $imovelObj->setEstado($estado);
            $imovelObj->setBloco($bloco);
            $imovelObj->setAnoConstrucao($anoConstrucao);
            $imovelObj->setAreaTotal($areaTotal);
            $imovelObj->setAreaPrivativa($areaPrivativa);
            $imovelObj->setSituacao($situacao);
            $imovelObj->setOcupacao($ocupacao);
            # imovelObj->setCorretor(corretor)
            # imovelObj->setCaptador(captador)
            if (!$imovelObj->getAnuncio() || $imovelObj->getAnuncio() && ($imovelObj->getAnuncio()->getId() === null || $imovelObj->getAnuncio()->getId() === 0)) {
                $imovelObj->setAnuncio($anuncioObj);
            } else {
                $anuncioObj->setId($imovelObj->getAnuncio()->getId());
                $imovelObj->setAnuncio($anuncioObj);
            }
            $imovelObj->setAnuncio($anuncioObj);
            $imovelObj->setCondominio($condominioObj);

            $consultarEndereco = $this->enderecoDAO->verificarEndereco(
                $imovelObj->getEndereco()
            );

            $endereco = NULL;

            if ($consultarEndereco) {
                $endereco = $consultarEndereco;
            } else {
                $cadastroEndereco = $this->enderecoDAO->cadastrarEndereco(
                    $imovelObj->getEndereco()
                );
                if ($cadastroEndereco) {
                    $endereco = $this->enderecoDAO->verificarEndereco(
                        $imovelObj->getEndereco()
                    );
                }
            }

            if (!$endereco) {
                return (["status" => "erro", "mensagem" => "ERRO ao cadastrar imóvel! Problema com o endereço"]);
            } else {
                $imovelObj->getEndereco()->setId($endereco->getId());
                $consultarCondominio = $this->condominioDAO->getCondominioPorIdEndereco(
                    $endereco->getId()
                );

                if (!$consultarCondominio) {
                    $cadastrar = $this->condominioDAO->cadastrarCondominio(
                        $imovelObj->getCondominio()
                    );
                    if ($cadastrar) {
                        $consultarCondominio = $this->condominioDAO->getCondominioPorIdEndereco(
                            $endereco->getId()
                        );
                        if ($consultarCondominio) {
                            $imovelObj->setCondominio($consultarCondominio);
                        } else {
                            $imovelObj->setCondominio(NULL);
                        }
                    }
                } else {
                    $imovelObj->setCondominio($consultarCondominio);
                }


                if ($imovelObj->getAnuncio()->getId() === null || $imovelObj->getAnuncio()->getId() === 0) {
                    $cadastroAnuncio = $this->anuncioDAO->cadastrarAnuncio(
                        $imovelObj->getAnuncio()
                    );
                    if ($cadastroAnuncio) {
                        $imovelObj->getAnuncio()->setId($cadastroAnuncio);
                    }
                } else {

                    $cadastroAnuncio = $this->anuncioDAO->atualizarAnuncio($anuncioObj);
                }

                $imovelObj->setDataCadastro(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
                if ($id) {
                    $atualizado = $this->imovelDAO->atualizarImovel($imovelObj);
                    if ($atualizado) {
                        $cadastrado = $id;
                    } else {
                        $cadastrado = null;
                    }

                    limparPasta($imovelObj->getAnuncio()->getImagens(), $imovelObj->getId());
                    limparPasta($imovelObj->getAnuncio()->getAnexos(), $imovelObj->getId());
                } else {
                    $cadastrado = $this->imovelDAO->cadastrarImovel($imovelObj);
                }
                if ($cadastrado && $cadastroAnuncio && $imagens) {
                    $imagensObjetos = [];
                    foreach ($imagens['tmp_name'] as $i => $nomeTemporario) {
                        try {
                            if ($imagens['error'][$i] !== UPLOAD_ERR_OK) {
                                continue;
                            }
                            $caminho = salvarArquivo($nomeTemporario, $imagens['name'][$i], $cadastrado, 'imagem');
                            if (!$caminho) {
                                continue;
                            }
                            $imagemObj = new Anexo(
                                $cadastroAnuncio,
                                $caminho,
                                TipoAnexo::IMAGEM
                            );
                            $imagensObjetos[] = $imagemObj;
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                    $anuncioObj->setImagens($imagensObjetos);
                    $this->anuncioDAO->atualizarAnuncio($anuncioObj);
                }
                if ($cadastrado && $cadastroAnuncio && $documentos) {
                    $documentosObjetos = [];
                    foreach ($documentos['tmp_name'] as $i => $nomeTemporario) {
                        try {
                            $nomeArquivo = $documentos['name'][$i];
                            error_log('tamanho do arquivo ' . $nomeArquivo . ': ' . filesize($documentos['tmp_name'][$i]) . ' bytes');
                            if ($documentos['error'][$i] !== UPLOAD_ERR_OK) {
                                continue;
                            }
                            $caminho = salvarArquivo($nomeTemporario, $nomeArquivo, $cadastrado, 'documento');
                            if (!$caminho) {
                                continue;
                            }
                            $documentoObj = new Anexo(
                                $cadastroAnuncio,
                                $caminho,
                                TipoAnexo::DOCUMENTO
                            );
                            $documentosObjetos[] = $documentoObj;
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                    $anuncioObj->setAnexos($documentosObjetos);
                    $this->anuncioDAO->atualizarAnuncio($anuncioObj);
                }
                if ($cadastrado) {
                    if ($id) {
                        return (["status" => "sucesso", "mensagem" => "imovel atualizado!\n"]);
                    }
                    return (["status" => "sucesso", "mensagem" => "imovel cadastrado!\n"]);
                } else {
                    return (["status" => "erro", "mensagem" => "ERRO ao cadastrar imóvel"]);
                }
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro interno"]);
        }
    }
    function getListaImoveis()
    {
        try {
            $imoveis = $this->imovelDAO->getListaImoveis();
            if (!$imoveis) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel encontrado"
                ];
            } else {
                $resposta = self::montarJsonImoveis($imoveis);
                return $resposta;
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar imóveis: " . $e->getMessage()]);
        }
    }


    function getListaImoveisDisponiveis()
    {
        try {
            $imoveis = $this->imovelDAO->getListaImoveisDisponiveis();
            // echo $imoveis;
            if (!$imoveis) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel disponível encontrado"
                ];
            } else {
                return self::montarJsonImoveis($imoveis);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar imóveis disponíveis: " . $e->getMessage()]);
        }
    }


    function getImovelPorId($id)
    {
        try {

            $imovelObj = $this->imovelDAO->getImovelPorId((int) $id);

            if (!$imovelObj) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel encontrado com o ID fornecido"
                ];
            } else {
                return self::montarJsonImoveis([$imovelObj]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao obter imóvel: " . $e->getMessage()]);
        }
    }


    function montarJsonImoveis(array $listaImoveis)
    {
        $lista = [];
        foreach ($listaImoveis as $imovel) {
            $endereco = null;
            if ($imovel->getEndereco()) {
                $enderecoObj = $imovel->getEndereco();
                $endereco = [
                    "rua" => $enderecoObj->rua ?? null,
                    "numero" => $enderecoObj->numero ?? null,
                    "bairro" => $enderecoObj->bairro ?? null,
                    "cidade" => $enderecoObj->cidade ?? null,
                    "uf" => $enderecoObj->uf ?? null,
                    "cep" => $enderecoObj->cep ?? null,
                    "complemento" => $enderecoObj->complemento ?? null,
                ];
            }

            $anuncio = null;
            if ($imovel->getAnuncio()) {
                $anuncioObj = $imovel->getAnuncio();
                $imagens = [];
                if ($anuncioObj->getImagens()) {
                    foreach ($anuncioObj->getImagens() as $imagem) {
                        if ($imagem instanceof Anexo) {
                            $imagens[] = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" . $imagem->getCaminho();
                        }
                    }
                }
                $documentos = [];
                if ($anuncioObj->getAnexos()) {
                    foreach ($anuncioObj->getAnexos() as $documento) {
                        if ($documento instanceof Anexo) {
                            $documentos[] = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" . $documento->getCaminho();
                        }
                    }
                }
                $videos = [];
                if ($anuncioObj->getVideos()) {
                    foreach ($anuncioObj->getVideos() as $video) {
                        if ($video instanceof Anexo) {
                            $videos[] = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" . $video->getCaminho();
                        }
                    }
                }
                $anuncio = [
                    "id" => $anuncioObj->getId(),
                    "descricao" => $anuncioObj->getDescricao(),
                    "titulo" => $anuncioObj->getTitulo(),
                    "imagens" => $imagens,
                    "documentos" => $documentos,
                    "videos" => $videos,
                ];
            }

            $categoria = $imovel->getCategoria();
            $status = $imovel->getStatus();

            if ($imovel->getProprietarios()) {
                $proprietarios = [];
                foreach ($imovel->getProprietarios() as $proprietario) {
                    $proprietarios[] = [
                        "id" => $proprietario->getId(),
                        "email" => $proprietario->getEmail(),
                        "nome" => $proprietario->getNome(),
                        "cpf_cnpj" => $proprietario->getCpfCnpj(),
                        "rg" => $proprietario->getRg(),
                        "telefones" => $proprietario->getTelefones() ?? [],
                        "endereco" => $proprietario->getEndereco() ? [
                            "rua" => $proprietario->getEndereco()->rua ?? null,
                            "numero" => $proprietario->getEndereco()->numero ?? null,
                            "bairro" => $proprietario->getEndereco()->bairro ?? null,
                            "cidade" => $proprietario->getEndereco()->cidade ?? null,
                            "uf" => $proprietario->getEndereco()->uf ?? null,
                            "cep" => $proprietario->getEndereco()->cep ?? null,
                            "complemento" => $proprietario->getEndereco()->complemento ?? null
                        ] : null,
                        "data_nascimento" => $proprietario->getDataNascimento() ? $proprietario->getDataNascimento()->format('d-m-Y') : null,
                        "data_cadastro" => $proprietario->getDataCadastro(),
                        "data_modificacao" => $proprietario->getDataModificacao()
                    ];
                }
            }

            if ($imovel->getCondominio()) {
                $condominio = [
                    "nome" => $imovel->getCondominio()->getNome(),
                    "filtros" => $imovel->getCondominio()->getFiltros(),
                ];
            }

            if ($imovel->getCorretor()) {
                $corretor = [
                    "id" => $imovel->getCorretor()->getId(),
                    "email" => $imovel->getCorretor()->getEmail(),
                    "nome" => $imovel->getCorretor()->getNome(),
                    "cpf_cnpj" => $imovel->getCorretor()->getCpfCnpj(),
                    "rg" => $imovel->getCorretor()->getRg(),
                    "telefones" => $imovel->getCorretor()->getTelefones() ?? [],
                    "creci" => $imovel->getCorretor()->getCreci() ?? null,
                    "endereco" => $imovel->getCorretor()->getEndereco() ? [
                        "rua" => $imovel->getCorretor()->getEndereco()->rua ?? null,
                        "numero" => $imovel->getCorretor()->getEndereco()->numero ?? null,
                        "bairro" => $imovel->getCorretor()->getEndereco()->bairro ?? null,
                        "cidade" => $imovel->getCorretor()->getEndereco()->cidade ?? null,
                        "uf" => $imovel->getCorretor()->getEndereco()->uf ?? null,
                        "cep" => $imovel->getCorretor()->getEndereco()->cep ?? null,
                        "complemento" => $imovel->getCorretor()->getEndereco()->complemento ?? null
                    ] : null,
                    "data_nascimento" => $imovel->getCorretor()->getDataNascimento() ? $imovel->getCorretor()->getDataNascimento()->format('d-m-Y') : null,
                    "data_cadastro" => $imovel->getCorretor()->getDataCadastro(),
                    "data_modificacao" => $imovel->getCorretor()->getDataModificacao()
                ];
            }

            if ($imovel->getCaptador()) {
                $captador = [
                    "id" => $imovel->getCaptador()->getId(),
                    "email" => $imovel->getCaptador()->getEmail(),
                    "nome" => $imovel->getCaptador()->getNome(),
                    "cpf_cnpj" => $imovel->getCaptador()->getCpfCnpj(),
                    "rg" => $imovel->getCaptador()->getRg(),
                    "telefones" => $imovel->getCaptador()->getTelefones() ?? [],
                    "salario" => $imovel->getCaptador()->getSalario() ?? null,
                    "endereco" => $imovel->getCaptador()->getEndereco() ? [
                        "rua" => $imovel->getCaptador()->getEndereco()->rua ?? null,
                        "numero" => $imovel->getCaptador()->getEndereco()->numero ?? null,
                        "bairro" => $imovel->getCaptador()->getEndereco()->bairro ?? null,
                        "cidade" => $imovel->getCaptador()->getEndereco()->cidade ?? null,
                        "uf" => $imovel->getCaptador()->getEndereco()->uf ?? null,
                        "cep" => $imovel->getCaptador()->getEndereco()->cep ?? null,
                        "complemento" => $imovel->getCaptador()->getEndereco()->complemento ?? null
                    ] : null,
                    "data_nascimento" => $imovel->getCaptador()->getDataNascimento() ? $imovel->getCaptador()->getDataNascimento()->format('d-m-Y') : null,
                    "data_cadastro" => $imovel->getCaptador()->getDataCadastro(),
                    "data_modificacao" => $imovel->getCaptador()->getDataModificacao()
                ];
            }

            $lista[] = [
                "id" => $imovel->getId(),
                "valor_venda" => $imovel->getValorVenda(),
                "valor_aluguel" => $imovel->getValorAluguel(),
                "categoria" => $categoria,
                "status" => $status,
                "endereco" => $endereco,
                "anuncio" => $anuncio,
                "data_modificacao" => $imovel->getDataModificacao(),
                "data_cadastro" => $imovel->getDataCadastro(),
                "proprietarios" => $proprietarios ?? [],
                "corretor" => $corretor ?? null,
                "captador" => $captador ?? null,
                "valor_condominio" => $imovel->getValorCondominio() ?? null,
                "valor_iptu" => $imovel->getIptu() ?? null,
                "ano_construcao" => $imovel->getAnoConstrucao() ?? null,
                "quantidade_banheiros" => $imovel->getQuantidadeBanheiros() ?? 0,
                "quantidade_salas" => $imovel->getQuantidadeSalas() ?? 0,
                "quantidade_varandas" => $imovel->getQuantidadeVarandas() ?? 0,
                "quantidade_quartos" => $imovel->getQuantidadeQuartos() ?? 0,
                "quantidade_vagas" => $imovel->getQuantidadeVagas() ?? 0,
                "bloco" => $imovel->getBloco() ?? null,
                "andar" => $imovel->getAndar() ?? null,
                "situacao" => $imovel->getSituacao() ?? null,
                "ocupacao" => $imovel->getOcupacao() ?? null,
                "estado" => $imovel->getEstado() ?? null,
                "area_privativa" => $imovel->getAreaPrivativa() ?? 0.00,
                "area_total" => $imovel->getAreaTotal() ?? 0.00,
                "complemento" => $imovel->getComplemento() ?? null,
                "condominio" => $condominio ?? null,
                "filtros" => $imovel->getFiltros()
            ];
        }

        return $lista;
    }

}