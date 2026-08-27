<?php

require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/pessoaDAO.php';
require_once __DIR__ . '/../dao/enderecoDAO.php';
require_once __DIR__ . '/../dao/historicoDAO.php';
require_once __DIR__ . '/../dao/condominioDAO.php';
require_once __DIR__ . '/../dao/anuncioDAO.php';
require_once __DIR__ . '/../utils/imagem.php';
require_once __DIR__ . '/../model/anexo.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../services/imovelService.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/funcionario.php';

class ImovelController
{

    function cadastrarClick()
    {
        try {

            $id = $_GET['id'] ?? null;
            if (!$id) {
                return (["status" => "erro", "mensagem" => "ID do imóvel não fornecido"]);
            }
            $imovelDAO = new ImovelDAO();
            $atualizacao = $imovelDAO->atualizarClicks($id);

            if (!$atualizacao) {
                return (["status" => "erro", "mensagem" => "Erro ao atualizar clicks do imóvel"]);
            }

            return (["status" => "sucesso", "mensagem" => "Clicks do imóvel atualizados com sucesso"]);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao atualizar clicks do imóvel: " . $e->getMessage()]);
        }
    }

    function destacar($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (str_contains(",", $id)) {
            $id = (array) $id;
        } else {
            $id = (int) $id;
        }
        $imovelDAO = new ImovelDAO();

        if (is_array($id)) {
            try {
                $resultado  = $imovelDAO->destacarLista($id);
                if (!$resultado) {
                    return (["status" => "erro", "mensagem" => "Erro ao destacar imóveis"]);
                }
                $historicoDAO = new HistoricoDAO();
                if (isset($_SESSION['usuario']) || !empty($_SESSION['usuario'])) {
                    try {
                        foreach ($id as $idImovel) {
                            $usuarioAtual = $_SESSION['usuario'] ?? null;
                            $historico = new Historico(alteracao: "Destacou o imóvel", imovel: $imovelDAO->buscarPorId($idImovel), funcionario: $usuarioAtual);
                            $historicoDAO->cadastrar($historico);
                        }
                    } catch (Exception $e) {
                        error_log("Erro ao registrar histórico de destaque de imóveis: " . $e->getMessage());
                    }
                }
                return (["status" => "sucesso", "mensagem" => "Imóveis destacados com sucesso"]);
            } catch (Exception $e) {
                return (["status" => "erro", "mensagem" => "Erro ao destacar imóveis: " . $e->getMessage()]);
            }
        } else if (is_int($id)) {
            try {
                $resultado = $imovelDAO->destacar($id);
                if (!$resultado) {
                    return (["status" => "erro", "mensagem" => "Erro ao destacar imóvel"]);
                }
                if (isset($_SESSION['usuario']) || !empty($_SESSION['usuario'])) {
                    try {
                        $historicoDAO = new HistoricoDAO();
                        $usuarioAtual = $_SESSION['usuario'] ?? null;
                        $historico = new Historico(alteracao: "Destacou o imóvel", imovel: $imovelDAO->buscarPorId($id), funcionario: $usuarioAtual);
                        $historicoDAO->cadastrar($historico);
                    } catch (Exception $e) {
                        error_log("Erro ao registrar histórico de destaque de imóveis: " . $e->getMessage());
                    }
                }
                return (["status" => "sucesso", "mensagem" => "Imóvel destacado com sucesso"]);
            } catch (Exception $e) {
                return (["status" => "erro", "mensagem" => "Erro ao destacar imóvel: " . $e->getMessage()]);
            }
        }
    }

    function listarDestacados()
    {
        try {
            $imovelDAO = new ImovelDAO();
            $imoveis = $imovelDAO->listarDestacados();
            if (!$imoveis) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel destacado encontrado"
                ];
            } else {
                $resposta = self::montarJson($imoveis);
                return $resposta;
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar imóveis destacados: " . $e->getMessage()]);
        }
    }

    function apagar(int $id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $imovelDAO = new ImovelDAO();
        if (is_array($id)) {
            $listaIDS = $id;
            $id = null;
            try {

                $remocao = $imovelDAO->getConexao()->removerLista("id", $listaIDS, "imovel");
                $historicoDAO = new HistoricoDAO();
                foreach ($listaIDS as $id) {
                    $imovel = $imovelDAO->buscarPorId($id);
                    if (!$imovel) {
                        // return (["status" => "erro", "mensagem" => "Imóvel não encontrado com ID: " . $id]);
                        continue;
                    }
                    if (isset($_SESSION['usuario']) || !empty($_SESSION['usuario'])) {
                        try {
                            $usuarioAtual = $_SESSION['usuario'] ?? null;
                            $historico = new Historico(alteracao: "Removeu o imóvel", imovel: $imovelDAO->buscarPorId($id), funcionario: $usuarioAtual);
                            $historicoDAO->cadastrar($historico);
                        } catch (Exception $e) {
                            error_log("Erro ao registrar histórico de destaque de imóveis: " . $e->getMessage());
                        }
                    }

                    if ($imovel->getAnuncio() && $imovel->getAnuncio()->getImagens()) {
                        limparPasta($imovel->getAnuncio()->getImagens(), $imovel->getId());
                    } else if ($imovel->getAnuncio() && $imovel->getAnuncio()->getAnexos()) {
                        limparPasta($imovel->getAnuncio()->getAnexos(), $imovel->getId());
                    } else if ($imovel->getAnuncio() && $imovel->getAnuncio()->getVideos()) {
                        limparPasta($imovel->getAnuncio()->getVideos(), $imovel->getId());
                    }
                }
                if (!$remocao) {
                    return (["status" => "erro", "mensagem" => "Erro ao remover imóveis"]);
                }
                return (["status" => "sucesso", "mensagem" => "Imóveis removidos com sucesso"]);
            } catch (Exception $e) {
                return (["status" => "erro", "mensagem" => "Erro ao remover imóveis: " . $e->getMessage()]);
            }
        } else if (is_int($id)) {
            try {
                $imovel = $imovelDAO->buscarPorId($id);
                if ($imovel) {
                    $remocao = $imovelDAO->getConexao()->remover("id", $id, "imovel");
                    if ($remocao) {
                        if (isset($_SESSION['usuario']) || !empty($_SESSION['usuario'])) {
                            try {
                                $historicoDAO = new HistoricoDAO();
                                $usuarioAtual = $_SESSION['usuario'] ?? null;
                                $historico = new Historico(alteracao: "Removeu o imóvel", imovel: $imovelDAO->buscarPorId($id), funcionario: $usuarioAtual);
                                $historicoDAO->cadastrar($historico);
                            } catch (Exception $e) {
                                error_log("Erro ao registrar histórico de destaque de imóveis: " . $e->getMessage());
                            }
                        }
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
    }


    public function cadastrar($data)
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
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

            $quantQuartos = array_key_exists("quantidade_quartos", $data) ? (int) trim(($data["quantidade_quartos"] ?? 0)) : 0;
            $quantSuites = array_key_exists("quantidade_suites", $data) ? (int) trim(($data["quantidade_suites"] ?? 0)) : 0;
            $quantSalas = array_key_exists("quantidade_salas", $data) ? (int) trim(($data["quantidade_salas"] ?? 0)) : 0;
            $quantVagas = array_key_exists("quantidade_vagas", $data) ? (int) trim(($data["quantidade_vagas"] ?? 0)) : 0;
            $quantBanheiros = array_key_exists("quantidade_banheiros", $data) ? (int) trim(($data["quantidade_banheiros"] ?? 0)) : 0;
            $quantVarandas = array_key_exists("quantidade_varandas", $data) ? (int) trim(($data["quantidade_varandas"] ?? 0)) : 0;
            $categoria = null;
            if (isset($data["categoria"])) {
                $valor = $data["categoria"];
                $categoria = Categoria::tryFrom($valor);
            }
            $status = null;
            $status = Status::tryFrom($data["status"]) ?? null;
            if (!$status) {
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
            $bloco = array_key_exists("bloco", $data) ? trim($data["bloco"]) : "";
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
            $proprietarios = array_key_exists("proprietarios", $data) ? (array) $data["proprietarios"] : [];
            $idCorretor = array_key_exists("corretor", $data) ? (int) $data["corretor"] : null;
            $idCaptador = array_key_exists("captador", $data) ? (int) $data["captador"] : null;
            $cep = array_key_exists("cep", $data) ? $data["cep"] : "";
            $imagens = $_FILES["imagens"] ?? [];
            $documentos = $_FILES["documentos"] ?? [];
            $videos = $_FILES["videos"] ?? [];
            if ($cep) {
                $cep = trim(str_replace("-", "", $cep));
            }
            $rua = array_key_exists("rua", $data) ? $data["rua"] : "";
            $bairro = array_key_exists("bairro", $data) ? $data["bairro"] : "";
            $cidade = array_key_exists("cidade", $data) ? $data["cidade"] : "";
            $titulo = array_key_exists("titulo", $data) ? $data["titulo"] : "";
            $descricao = array_key_exists("descricao", $data) ? $data["descricao"] : "";
            $complemento = array_key_exists("complemento", $data) ? trim($data["complemento"]) : "";
            $uf = array_key_exists("uf", $data) ? trim($data["uf"]) : "";
            $numero = array_key_exists("numero", $data) ? (int) trim($data["numero"] ?? null) : null;
            $filtrosApartamento = array_key_exists("filtros_apartamento", $data) ? (array)
            str_replace(['[', ']', '"'], '', $data["filtros_apartamento"]) : [];
            $filtrosCondominio = array_key_exists("filtros_condominio", $data) ?  (array) str_replace(['[', ']', '"'], '', $data["filtros_condominio"]) : [];

            $complemento .= $bloco;

            $corretor = null;
            $captador = null;
            $pessoaDAO = new PessoaDAO();

            if ($idCorretor != null && $idCorretor > 0) {
                $corretor = $pessoaDAO->buscarPorId($idCorretor) ?? null;
            }

            if ($idCaptador != null && $idCaptador > 0) {
                $captador = $pessoaDAO->buscarPorId($idCaptador) ?? null;
            }

            if ($proprietarios != null && count($proprietarios) > 0) {
                $proprietariosObjs = [];
                foreach ($proprietarios as $idProprietario) {
                    if (!is_numeric($idProprietario)) {
                        continue;
                    }
                    $proprietarioObj = $pessoaDAO->buscarPorId($idProprietario);
                    if ($proprietarioObj) {
                        $proprietariosObjs[] = $proprietarioObj;
                    }
                }
                $proprietarios = $proprietariosObjs;
            }

            $imovelObj = NULL;
            $anuncioObj = new Anuncio();
            $imovelDAO = new ImovelDAO();
            $enderecoObj = null;
            $condominioObj = null;
            if ($id) {
                $imovelObj = $imovelDAO->buscarPorId($id);
                if (!$imovelObj) {
                    return (["status" => "erro", "mensagem" => "ERRO ao atualizar imóvel! Imóvel não encontrado"]);
                }
                $imovelObj->setDataModificacao(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
                $anuncioObj = $imovelObj->getAnuncio();
                $enderecoObj = $imovelObj->getEndereco();
                $condominioObj = $imovelObj->getCondominio();
                if ($anuncioObj == null) {
                    $anuncioObj = new Anuncio();
                    $anuncioObj->setIdImovel($imovelObj->getId());
                }
            } else {
                $imovelObj = new Imovel($enderecoObj, $status, $categoria);
            }

            if ($enderecoObj == null) {
                $enderecoObj = new Endereco($rua, $bairro, $cep, $cidade, $uf);
            }

            if ($condominioObj == null && ($nomeCondominio || $filtrosCondominio)) {
                $condominioObj = new Condominio(
                    $nomeCondominio,
                    $enderecoObj
                );
                $condominioObj->setFiltros($filtrosCondominio);
            } else if ($condominioObj != null) {
                $condominioObj->setNome($nomeCondominio);
                $condominioObj->setFiltros($filtrosCondominio);
            }

            $enderecoObj->setRua($rua);
            $enderecoObj->setBairro($bairro);
            $enderecoObj->setCep($cep);
            $enderecoObj->setCidade($cidade);
            $enderecoObj->setUf($uf);
            $enderecoObj->setNumero($numero);
            $enderecoObj->setComplemento($complemento);
            $imovelObj->setEndereco($enderecoObj);
            $imovelObj->setProprietarios($proprietarios);
            $imovelObj->setFiltros($filtrosApartamento);
            $anuncioObj->setTitulo($titulo);
            $anuncioObj->setDescricao($descricao);
            $imovelObj->setValorVenda($valorVenda);
            $imovelObj->setValorAluguel($valorAluguel);
            $imovelObj->setQuantQuartos($quantQuartos);
            $imovelObj->setQuantSuites($quantSuites);
            $imovelObj->setQuantSalas($quantSalas);
            $imovelObj->setQuantVagas($quantVagas);
            $imovelObj->setQuantBanheiros($quantBanheiros);
            $imovelObj->setQuantVarandas($quantVarandas);
            $imovelObj->setCategoria($categoria);
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
            $imovelObj->setCorretor($corretor);
            $imovelObj->setCaptador($captador);
            $imovelObj->setAnuncio($anuncioObj);
            $imovelObj->setCondominio($condominioObj);

            if (!$imovelObj->getEndereco()) {
                return (["status" => "erro", "mensagem" => "ERRO ao cadastrar imóvel! Endereço nulo, contate o administrador"]);
            }


            $imovelObj->setDataCadastro(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
            $cadastrado = false;
            $imovelService = new ImovelService();
            if ($id) {
                $imovelService->atualizar($imovelObj);
                $cadastrado = $imovelObj;
                if (isset($_SESSION['usuario']) || !empty($_SESSION['usuario'])) {
                    if ($cadastrado) {
                        try {
                            $historicoDAO = new HistoricoDAO();
                            $usuarioAtual = $_SESSION['usuario'] ?? null;
                            $historico = new Historico(alteracao: "Atualizou o imóvel", imovel: $imovelDAO->buscarPorId($id), funcionario: $usuarioAtual);
                            $historicoDAO->cadastrar($historico);
                        } catch (Exception $e) {
                            error_log("Erro ao registrar histórico de destaque de imóveis: " . $e->getMessage());
                        }
                    }
                }

                limparPasta($imovelObj->getAnuncio()->getImagens(), $imovelObj->getId());
                limparPasta($imovelObj->getAnuncio()->getAnexos(), $imovelObj->getId());
            } else {
                $cadastrado = $imovelService->cadastrar($imovelObj);
                if (isset($_SESSION['usuario']) || !empty($_SESSION['usuario'])) {
                    if ($cadastrado) {
                        try {
                            $historicoDAO = new HistoricoDAO();
                            $usuarioAtual = $_SESSION['usuario'] ?? null;
                            $historico = new Historico(alteracao: "Cadastrou o imóvel", imovel: $imovelDAO->buscarPorId($cadastrado->getId()), funcionario: $usuarioAtual);
                            $historicoDAO->cadastrar($historico);
                        } catch (Exception $e) {
                            error_log("Erro ao registrar histórico de destaque de imóveis: " . $e->getMessage());
                        }
                    }
                }
            }


            if ($cadastrado && $cadastrado->getId() && $cadastrado->getAnuncio() != null && is_array($imagens) && count($imagens) > 0) {
                $imagensObjetos = [];
                $id = $cadastrado->getId();

                foreach ($imagens['tmp_name'] as $i => $nomeTemporario) {
                    try {
                        if ($imagens['error'][$i] !== UPLOAD_ERR_OK) {
                            error_log("Erro ao fazer upload da imagem: " . $imagens['name'][$i] . " - Código de erro: " . $imagens['error'][$i]);
                            continue;
                        }
                        $caminho = salvarArquivo($nomeTemporario, $imagens['name'][$i], $id, 'imagem');
                        if (!$caminho) {
                            error_log("Erro ao salvar a imagem: " . $imagens['name'][$i]);
                            continue;
                        }
                        $imagemObj = new Anexo(
                            $cadastrado->getAnuncio()->getIdImovel(),
                            $caminho,
                            TipoAnexo::IMAGEM
                        );
                        $imagensObjetos[] = $imagemObj;
                    } catch (Exception $e) {
                        error_log("Exceção ao processar a imagem: " . $imagens['name'][$i] . " - Mensagem: " . $e->getMessage());
                        continue;
                    }
                }
                $arquivos = listarArquivos($cadastrado->getId());
                $listaAnexosSalvos = [];
                foreach ($imagensObjetos as $imagem) {
                    $caminho = $imagem->getCaminho();
                    $busca = $cadastrado->getId()  . '/';
                    $posicao = strpos($caminho, $busca);
                    if ($posicao !== false) {
                        $caminhoFormatado = substr(
                            $caminho,
                            $posicao + strlen($busca)
                        );
                        if (in_array($caminhoFormatado, $arquivos)) {
                            $listaAnexosSalvos[] = $imagem;
                        }
                    }
                }
                $cadastrado->getAnuncio()->setImagens($listaAnexosSalvos);
                $imovelService->atualizarAnuncio($cadastrado->getAnuncio());
            }

            if ($cadastrado != null && $cadastrado->getId() && $cadastrado->getAnuncio() != null && is_array($documentos) && count($documentos) > 0) {
                $id = $cadastrado->getId();
                $documentosObjetos = [];
                foreach ($documentos['tmp_name'] as $i => $nomeTemporario) {
                    try {
                        $nomeArquivo = $documentos['name'][$i];

                        if (!is_string($nomeArquivo) || empty($nomeArquivo)) {
                            error_log("Nome do arquivo inválido para o documento: " . $nomeArquivo);
                            continue;
                        }

                        if ($documentos['error'][$i] !== UPLOAD_ERR_OK) {
                            error_log("Erro ao fazer upload do documento: " . $nomeArquivo . " - Código de erro: " . $documentos['error'][$i]);
                            continue;
                        }
                        $caminho = salvarArquivo($nomeTemporario, $nomeArquivo, $id, 'documento');
                        if (!$caminho) {
                            error_log("Erro ao salvar o documento: " . $nomeArquivo);
                            continue;
                        }
                        $documentoObj = new Anexo(
                            $cadastrado->getAnuncio()->getIdImovel(),
                            $caminho,
                            TipoAnexo::DOCUMENTO
                        );
                        $documentosObjetos[] = $documentoObj;
                    } catch (Exception $e) {
                        error_log("Exceção ao processar o documento: " . $documentos['name'][$i] . " - Mensagem: " . $e->getMessage());
                        continue;
                    }
                }
                $arquivos = listarArquivos($cadastrado->getId());
                $listaAnexosSalvos = [];
                foreach ($documentosObjetos as $documento) {
                    $caminho = $documento->getCaminho();
                    $busca = $imovelObj->getId() . '/';
                    $posicao = strpos($caminho, $busca);
                    if ($posicao !== false) {
                        $caminhoFormatado = substr(
                            $caminho,
                            $posicao + strlen($busca)
                        );
                        if (in_array($caminhoFormatado, $arquivos)) {
                            $listaAnexosSalvos[] = $documento;
                        }
                    }
                }
                $cadastrado->getAnuncio()->setAnexos($documentosObjetos);
                $imovelService->atualizarAnuncio($cadastrado->getAnuncio());
            }

            if ($id) {
                return (["status" => "sucesso", "mensagem" => "imovel atualizado!\n"]);
            }
            return (["status" => "sucesso", "mensagem" => "imovel cadastrado!\n"]);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro interno: " . $e->getMessage()]);
        }
    }



    function listar()
    {
        try {
            $imovelDAO = new ImovelDAO();
            $imoveis = $imovelDAO->listar();
            if (!$imoveis) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel encontrado"
                ];
            } else {
                $resposta = self::montarJson($imoveis);
                return $resposta;
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar imóveis: " . $e->getMessage()]);
        }
    }


    function listarDisponiveis()
    {
        try {
            $imovelDAO = new ImovelDAO();
            $imoveis = $imovelDAO->listarDisponiveis();
            if (!$imoveis) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel disponível encontrado"
                ];
            } else {
                return self::montarJson($imoveis);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar imóveis disponíveis: " . $e->getMessage()]);
        }
    }


    function buscarPorId($id)
    {
        try {
            $imovelDAO = new ImovelDAO();
            $imovelObj = $imovelDAO->buscarPorId((int) $id);
            if (!$imovelObj) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel encontrado com o ID fornecido"
                ];
            } else {
                return self::montarJson([$imovelObj])[0];
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao obter imóvel: " . $e->getMessage()]);
        }
    }


    function montarJson(array $listaImoveis)
    {
        $lista = [];
        foreach ($listaImoveis as $imovel) {
            $endereco = null;
            if ($imovel->getEndereco()) {
                $enderecoObj = $imovel->getEndereco();
                $endereco = [
                    "id" => $enderecoObj->getId(),
                    "rua" => $enderecoObj->getRua() ?? null,
                    "numero" => $enderecoObj->getNumero() ?? null,
                    "bairro" => $enderecoObj->getBairro() ?? null,
                    "cidade" => $enderecoObj->getCidade() ?? null,
                    "uf" => $enderecoObj->getUf() ?? null,
                    "cep" => $enderecoObj->getCep() ?? null,
                    "complemento" => $enderecoObj->getComplemento() ?? null,
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
                "quantidade_suites" => $imovel->getQuantSuites() ?? 0,
                "bloco" => $imovel->getBloco() ?? null,
                "andar" => $imovel->getAndar() ?? null,
                "situacao" => $imovel->getSituacao() ?? null,
                "ocupacao" => $imovel->getOcupacao() ?? null,
                "estado" => $imovel->getEstado() ?? null,
                "area_privativa" => $imovel->getAreaPrivativa() ?? 0.00,
                "area_total" => $imovel->getAreaTotal() ?? 0.00,
                "condominio" => $condominio ?? null,
                "filtros" => $imovel->getFiltros(),
                "destacado" => $imovel->isDestacado() ?? false,
                "quant_clicks" => $imovel->getQuantClicks() ?? 0
            ];
        }

        return $lista;
    }
}
