<?php

require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/captador.php';
require_once __DIR__ . '/../model/atendimento.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/vendaAluguel.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/gerente.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/__init__.php';

class controller
{

    function atualizarUsuario($dados)
    {
        try {
            $nome = $dados['nome'] ?? null;
            $email = $dados['email'] ?? null;
            $senha = $dados['senha'] ?? null;
            $dataNascimento = isset($dados['data_nascimento']) ? DateTime::createFromFormat('Y-m-d', $dados['data_nascimento']) : null;
            $cpfCnpj = $dados['cpf_cnpj'] ?? null;
            $rg = $dados['rg'] ?? null;
            $telefones = $dados['telefones'] ?? null;
            $endereco = $dados['endereco'] ?? null;
            $tipo = $dados['tipo'] ?? null;
            $usuario = Null;
            $creci = $dados['creci'] ?? null;
            $salario = $dados['salario'] ?? null;
            $id = $dados['id'] ?? null;
            if (!$id) {
                return (["status" => "erro", "mensagem" => "ID do usuário não fornecido"]);
            }
            if ($tipo == "CORRETOR") {
                $usuario = new Corretor($email, $senha, $email, $nome, $cpfCnpj, $creci);
            } else if ($tipo == "GERENTE") {
                $usuario = new Gerente($email, $senha, $email, $nome, $cpfCnpj);
            } else if ($tipo == "CAPTADOR") {
                $usuario = new Captador($email, $senha, $email, $nome, $cpfCnpj);
            } else if ($tipo == "CLIENTE") {
                $usuario = new Cliente($email, $senha, $email, $nome, $cpfCnpj);
            } else if ($tipo == "PROPRIETARIO") {
                $usuario = new Proprietario($email, $nome, $cpfCnpj);
            } else {
                return (["status" => "erro", "mensagem" => "Tipo de usuário inválido"]);
            }

            $usuario->setDataNascimento($dataNascimento);
            $usuario->setRg($rg);
            $usuario->setTelefones($telefones);
            $usuario->setId($id);

            $endereco = new Endereco(
                $endereco['rua'] ?? null,
                $endereco['bairro'] ?? null,
                $endereco['cep'] ?? null,
                $endereco['cidade'] ?? null,
                $endereco['uf'] ?? null,
            );

            $endereco->setNumero($endereco['numero'] ?? null);
            $endereco->setComplemento($endereco['complemento'] ?? null);
            $verificar_endereco = Init::getInstance()->verificarEndereco($endereco);
            if ($verificar_endereco) {
                $endereco = $verificar_endereco;
            } else {
                $endereco = Init::getInstance()->cadastrarEndereco($endereco);
            }
            $usuario->setEndereco($endereco);

            $atualizacao = Init::getInstance()->atualizarUsuario($usuario);
            if ($atualizacao) {
                return (["status" => "sucesso", "mensagem" => "Usuário atualizado com sucesso"]);
            } else {
                return (["status" => "erro", "mensagem" => "Erro ao atualizar usuário"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao atualizar usuário: " . $e->getMessage()]);
        }
    }

    function cadastrarUsuario($data)
    {
        try {

            $nome = $data['nome'] ?? '';
            $email = $data['email'] ?? '';
            $senha = $data['senha'] ?? '';
            $cpf = $data['cpf'] ?? '';
            $dataNascimento = $data['data_nascimento'] ? DateTime::createFromFormat('Y-m-d', $data['data_nascimento']) : null;

            $usuario = new Cliente($email, $senha, $email, $nome, $cpf);
            $usuario->setDataNascimento($dataNascimento);

            $resultado = Init::getInstance()->cadastrarUsuario($usuario);
            if ($resultado) {
                $login = $this->verificarLogin(['usuario' => $email, 'senha' => $senha]);
                if ($login['status'] === 'sucesso') {
                    return (["status" => "sucesso", "mensagem" => "Usuário cadastrado com sucesso"]);
                } else {
                    return (["status" => "erro", "mensagem" => "Usuário cadastrado, mas falha ao logar automaticamente"]);
                }
            } else {
                return (["status" => "erro", "mensagem" => "Erro ao cadastrar usuário"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao cadastrar usuário: " . $e->getMessage()]);
        }
    }

    function deslogar()
    {
        try {
            session_start();
            session_destroy();
            return (["status" => "sucesso", "mensagem" => "Usuário deslogado com sucesso"]);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao deslogar: " . $e->getMessage()]);
        }
    }

    function carregarUsuario()
    {
        try {
            session_start();
            if (isset($_SESSION['usuario_id'])) {
                $usuario = Init::getInstance()->getUsuarioPorId($_SESSION['usuario_id']);

                $dados = [
                    "id" => $usuario->getId(),
                    "nome" => $usuario->getNome(),
                    "email" => $usuario->getEmail(),
                    "cpf_cnpj" => $usuario->getCpfCnpj(),
                    "rg" => $usuario->getRg(),
                    "telefones" => [$usuario->getTelefones()],
                    "endereco" => $usuario->getEndereco() ? [
                        "rua" => $usuario->getEndereco()->rua ?? null,
                        "numero" => $usuario->getEndereco()->numero ?? null,
                        "bairro" => $usuario->getEndereco()->bairro ?? null,
                        "cidade" => $usuario->getEndereco()->cidade ?? null,
                        "uf" => $usuario->getEndereco()->uf ?? null,
                        "cep" => $usuario->getEndereco()->cep ?? null,
                        "complemento" => $usuario->getEndereco()->complemento ?? null,
                    ] : null,
                    "data_nascimento" => $usuario->getDataNascimento(),
                    "tipo" => $usuario->getTipo() ?? null,
                    "data_cadastro" => $usuario->getDataCadastro(),
                    "data_modificacao" => $usuario->getDataModificacao()
                ];

                if ($usuario->getTipo() == "CORRETOR") {
                    $dados["creci"] = $usuario->getCreci();
                } elseif (in_array($usuario->getTipo(), ["GERENTE", "CAPTADOR", "FINANCEIRO"])) {
                    $dados["salario"] = $usuario->getSalario();
                }

                return ([
                    "status" => "sucesso",
                    "tipo" => $_SESSION['tipo'],
                    "usuario" => $dados
                ]);
            } else {
                return ([
                    "status" => "erro",
                    "mensagem" => "Usuário não logado"
                ]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao carregar usuário: " . $e->getMessage()]);
        }
    }


    function verificarLogin($data)
    {

        try {
            session_start();

            $usuario = $data['usuario'] ?? '';
            $senha = $data['senha'] ?? '';

            if (!$usuario || !$senha) {
                return (["status" => "erro", "mensagem" => "Usuário ou senha não fornecidos"]);
            }

            $consulta = Init::getInstance()->verificarUsuario($usuario, $senha);

            if ($consulta) {
                $_SESSION['usuario_id'] = $consulta->getId();
                $_SESSION['tipo'] = $consulta->getTipo() ?? NULL;
                return (["status" => "sucesso", "usuario" => [
                    "id" => $consulta->getId(),
                    "nome" => $consulta->getNome(),
                    "tipo" => $consulta->getTipo(),
                ]]);
            } else {
                return (["status" => "erro", "mensagem" => "Usuário ou senha incorretos"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            return (["status" => "erro", "mensagem" => "Erro ao verificar login: " . $e->getMessage()]);
        }
    }


    function listarAtendimentos()
    {
        try {
            $atendimentos = Init::getInstance()->getListaAtendimentos();
            $lista = [];
            if ($atendimentos) {
                foreach ($atendimentos as $atendimento) {
                    $lista[] = [
                        "id" => $atendimento->getid(),
                        "corretor" => $atendimento->getCorretor() ? $atendimento->getCorretor()->getNome() : NULL,
                        "cliente" =>  $atendimento->getCliente() ? [
                            "id" => $atendimento->getCliente()->getid(),
                            "nome" => $atendimento->getCliente()->getNome(),
                            # "idade" => $atendimento->getCliente()->getidade(),
                            "telefones" => [$atendimento->getCliente()->getTelefones()],
                            "email" => $atendimento->getCliente()->getEmail(),
                        ] : NULL,
                        "imovel" => $atendimento->getImovel() ? [
                            "id" => $atendimento->getImovel()->getid(),
                            "titulo" => $atendimento->getImovel()->getAnuncio()->getTitulo() ?: NULL,
                        ] : NULL,
                        "status" =>  $atendimento->getStatus() ? $atendimento->getStatus()->value : NULL,
                    ];
                }
            }
            return $lista;
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar atendimentos: " . $e->getMessage()]);
        }
    }

    function getListaImoveis()
    {
        try {
            $imoveis = Init::getInstance()->getEstoque()->getListaImoveis();

            $lista = [];
            foreach ($imoveis as $imovel) {
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
                        foreach ($anuncioObj->getImagens() as $idImagem) {
                            $imagens[] = "/projeto-pi-front/php/imagem.php?id=" . $idImagem;
                        }
                    }
                    $anuncio = [
                        "id" => $anuncioObj->getId(),
                        "descricao" => $anuncioObj->getDescricao(),
                        "titulo" => $anuncioObj->getTitulo(),
                        "imagens" => $imagens
                    ];
                }

                $categoria = $imovel->getCategoria();
                if (is_object($categoria) && isset($categoria->value)) {
                    $categoria = $categoria->value;
                }

                $status = $imovel->getStatus();
                if (is_object($status) && isset($status->value)) {
                    $status = $status->value;
                }

                $lista[] = [
                    "id" => $imovel->getId(),
                    "valor_venda" => $imovel->getValorVenda(),
                    "valor_aluguel" => $imovel->getValorAluguel(),
                    "categoria" => $categoria,
                    "status" => $status,
                    "endereco" => $endereco,
                    "anuncio" => $anuncio
                ];
            }

            return ($lista);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar imóveis: " . $e->getMessage()]);
        }
    }


    function getListaImoveisDisponiveis()
    {
        try {
            $imoveis = Init::getInstance()->getEstoque()->getListaImoveisDisponiveis();
            // echo $imoveis;
            $lista = [];
            foreach ($imoveis as $imovel) {
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
                        foreach ($anuncioObj->getImagens() as $idImagem) {
                            $imagens[] = "/projeto-pi-front/php/imagem.php?id=" . $idImagem;
                        }
                    }
                    $anuncio = [
                        "id" => $anuncioObj->getId(),
                        "descricao" => $anuncioObj->getDescricao(),
                        "titulo" => $anuncioObj->getTitulo(),
                        "imagens" => $imagens
                    ];
                }

                $categoria = $imovel->getCategoria();
                if (is_object($categoria) && isset($categoria->value)) {
                    $categoria = $categoria->value;
                }

                $status = $imovel->getStatus();
                if (is_object($status) && isset($status->value)) {
                    $status = $status->value;
                }

                $lista[] = [
                    "id" => $imovel->getId(),
                    "valor_venda" => $imovel->getValorVenda(),
                    "valor_aluguel" => $imovel->getValorAluguel(),
                    "categoria" => $categoria,
                    "status" => $status,
                    "endereco" => $endereco,
                    "anuncio" => $anuncio
                ];
            }

            return $lista;
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar imóveis disponíveis: " . $e->getMessage()]);
        }
    }


    function getImovelPorId($id)
    {
        try {
            // echo $id;
            // logging->info(f"Requisição para obter imóvel com ID => {id}")
            $imovelObj = Init::getInstance()->getImovelPorId((int)$id);

            if ($imovelObj) {
                $anuncio = null;
                if ($imovelObj->getAnuncio()) {
                    $anuncioObj = $imovelObj->getAnuncio();
                    $imagens = [];
                    if ($anuncioObj->getImagens()) {
                        foreach ($anuncioObj->getImagens() as $idImagem) {
                            $imagens[] = "/projeto-pi-front/php/imagem.php?id=" . $idImagem;
                        }
                    }
                    $anuncio = [
                        "id" => $anuncioObj->getId(),
                        "descricao" => $anuncioObj->getDescricao(),
                        "titulo" => $anuncioObj->getTitulo(),
                        "imagens" => $imagens
                    ];
                }
                $resposta = [
                    "id" => $imovelObj->getId(),
                    "valor_venda" => $imovelObj->getValorVenda(),
                    "valor_condominio" => $imovelObj->getValorCondominio(),
                    "valor_iptu" => $imovelObj->getValorIptu(),
                    "valor_aluguel" => $imovelObj->getValorAluguel(),
                    "categoria" => $imovelObj->getCategoria()->value ?? null,
                    "status" => $imovelObj->getStatus()->value ?? null,
                    "endereco" => $imovelObj->getEndereco() ? [
                        "rua" => $imovelObj->getEndereco()->rua ?? null,
                        "numero" => $imovelObj->getEndereco()->numero ?? null,
                        "bairro" => $imovelObj->getEndereco()->bairro ?? null,
                        "cidade" => $imovelObj->getEndereco()->cidade ?? null,
                        "uf" => $imovelObj->getEndereco()->uf ?? null,
                        "cep" => $imovelObj->getEndereco()->cep ?? null,
                        "complemento" => $imovelObj->getEndereco()->complemento ?? null
                    ] : null,
                    "anuncio" => $anuncio,
                    "quantidade_quartos" => $imovelObj->getQuantidadeQuartos(),
                    "quant_salas" => $imovelObj->getQuantidadeSalas(),
                    "quant_vagas" => $imovelObj->getQuantidadeVagas(),
                    "quant_banheiros" => $imovelObj->getQuantidadeBanheiros(),
                    "quant_varandas" => $imovelObj->getQuantidadeVarandas(),
                    "andar" => $imovelObj->getAndar(),
                    "estado" => $imovelObj->getEstado()->value ?? null,
                    "bloco" => $imovelObj->getBloco(),
                    "ano_construcao" => $imovelObj->getAnoConstrucao(),
                    "area_total" => $imovelObj->getAreaTotal(),
                    "area_privativa" => $imovelObj->getAreaPrivativa(),
                    "situacao" => $imovelObj->getSituacao()->value ?? null,
                    "ocupacao" => $imovelObj->getOcupacao()->value ?? null,
                    "proprietarios" => $imovelObj->getProprietarios() ? array_map(function ($proprietario) {
                        return [
                            "id" => $proprietario->getId(),
                            "email" => $proprietario->getEmail(),
                            "nome" => $proprietario->getNome(),
                            "cpf_cnpj" => $proprietario->getCpfCnpj(),
                            "rg" => $proprietario->getRg(),
                            "telefones" => [$proprietario->getTelefones()],
                            "endereco" => $proprietario->getEndereco(),
                            "data_nascimento" => $proprietario->getDataNascimento(),
                            "imoveis" => $proprietario->getImoveis(),
                            "data_cadastro" => $proprietario->getDataCadastro(),
                            "data_modificacao" => $proprietario->getDataModificacao()
                        ];
                    }, $imovelObj->getProprietarios()) : [],
                    "corretor" => $imovelObj->getCorretor() ? ["username" => $imovelObj->getCorretor()->getUsername(), "senha" => $imovelObj->getCorretor()->getSenha(), "email" => $imovelObj->getCorretor()->getEmail(), "nome" => $imovelObj->getCorretor()->getNome(), "cpf_cnpj" => $imovelObj->getCorretor()->getCpfCnpj(), "tipo" => $imovelObj->getCorretor()->getTipo()] : null,
                    "captador" => $imovelObj->getCaptador() ? ["username" => $imovelObj->getCaptador()->getUsername(), "senha" => $imovelObj->getCaptador()->getSenha(), "email" => $imovelObj->getCaptador()->getEmail(), "nome" => $imovelObj->getCaptador()->getNome(), "cpf_cnpj" => $imovelObj->getCaptador()->getCpfCnpj(), "tipo" => $imovelObj->getCaptador()->getTipo()] : null,
                    "data_cadastro" => $imovelObj->getDataCadastro(),
                    "data_modificacao" => $imovelObj->getDataModificacao(),
                    "condominio" => $imovelObj->getCondominio() ? ["id" => $imovelObj->getCondominio()->getId(), "nome" => $imovelObj->getCondominio()->getNome(), "filtros" => [$imovelObj->getCondominio()->getFiltros()]] : null,
                    "filtros" => [$imovelObj->getFiltros()],
                    "complemento" => $imovelObj->getAnuncio() ? $imovelObj->getComplemento() : null
                ];

                return ($resposta);
                return;
            } else {
                return (["status" => "erro", "mensagem" => "Imovel nao encontrado"]);
                return;
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao obter imóvel: " . $e->getMessage()]);
        }
    }


    function apagarImovel($id)
    {
        try {
            $imovel = Init::getInstance()->getImovelPorId($id);
            if ($imovel) {
                $remocao = Init::getInstance()->remover("id_imovel", $id, "imovel");
                if ($remocao) {
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

            $id =  array_key_exists("ref", $data) ? $data["ref"] : null;
            $nomeCondominio = array_key_exists("nome_condominio", $data) ? $data["nome_condominio"] : null;
            $valorVenda = array_key_exists("valor_venda", $data) ? (float)($data["valor_venda"] ?? 0) : null;
            $valorAluguel = array_key_exists("valor_aluguel", $data) ? (float)($data["valor_aluguel"] ?? 0) : null;
            $quantQuartos = array_key_exists("quant_quartos", $data) ? (int)($data["quant_quartos"] ?? 0) : null;
            $quantSalas = array_key_exists("quant_salas", $data) ? (int)($data["quant_salas"] ?? 0) : null;
            $quantVagas = array_key_exists("quant_vagas", $data) ? (int)($data["quant_vagas"] ?? 0) : null;
            $quantBanheiros = array_key_exists("quant_banheiros", $data) ? (int)($data["quant_banheiros"] ?? 0) : null;
            $quantVarandas = array_key_exists("quant_varandas", $data) ? (int)($data["quant_varandas"] ?? 0) : null;
            $categoria = null;
            if (isset($data["categoria"])) {
                $valor = ucfirst(strtolower($data["categoria"]));
                $categoria = Categoria::tryFrom($valor);
            }
            $status = null;
            isset($data["status"]) ? $status = Status::tryFrom(ucfirst(strtolower($data["status"]))) : null;
            $iptu = array_key_exists("iptu", $data) ? (float)($data["iptu"] ?? 0) : null;
            $valorCondominio = array_key_exists("valor_condominio", $data) ? (float)($data["valor_condominio"] ?? 0) : null;
            $andar = array_key_exists("andar", $data) ? (int)($data["andar"] ?? 0) : null;
            $estado = null;
            isset($data["estado"]) ? $estado = Estado::tryFrom(ucfirst(strtolower($data["estado"]))) : null;
            $bloco = array_key_exists("bloco", $data) ? $data["bloco"] : null;
            $anoConstrucao = array_key_exists("ano_construcao", $data) ? (int)($data["ano_construcao"] ?? 0) : null;
            $areaTotal = array_key_exists("area_total", $data) ? (float)($data["area_total"] ?? 0) : null;
            $areaPrivativa = array_key_exists("area_privativa", $data) ? (float)($data["area_privativa"] ?? 0) : null;
            $situacao = null;
            isset($data["situacao"]) ? $situacao = Situacao::tryFrom(ucfirst(strtolower($data["situacao"]))) : null;
            $ocupacao = null;
            isset($data["ocupacao"]) ? $ocupacao = Ocupacao::tryFrom(ucfirst(strtolower($data["ocupacao"]))) : null;
            # proprietarios = data["proprietarios"]
            # corretor = data["corretor"]
            # captador = data["captador"]
            $cep = array_key_exists("cep", $data) ? (int)($data["cep"] ?? null) : null;
            if ($cep) {
                $cep = str_replace("-", "", $cep);
            }
            $rua = array_key_exists("rua", $data) ? $data["rua"] : null;
            $bairro = array_key_exists("bairro", $data) ? $data["bairro"] : null;
            $cidade = array_key_exists("cidade", $data) ? $data["cidade"] : null;
            $titulo = array_key_exists("titulo", $data) ? $data["titulo"] : null;
            $descricao = array_key_exists("descricao", $data) ? $data["descricao"] : null;
            $complemento = array_key_exists("complemento", $data) ? $data["complemento"] : null;
            $uf = array_key_exists("uf", $data) ? $data["uf"] : null;
            $numero = array_key_exists("numero", $data) ? (int)($data["numero"] ?? null) : null;
            $anuncioObj = new Anuncio();
            $anuncioObj->setTitulo($titulo);
            $anuncioObj->setDescricao($descricao);
            $enderecoObj = new Endereco($rua, $bairro, $cep, $cidade, $estado);
            $enderecoObj->setNumero($numero);
            $enderecoObj->setComplemento($complemento);
            $enderecoObj->setUf($uf);
            $condominioObj = new Condominio(
                $nomeCondominio,
                $enderecoObj
            );
            # imagens = anuncio->get("imagens", [])
            # imagensBytes = []
            # for imagem in imagens =>
            #     try =>
            #         imagemBytes = base64->b64decode(imagem)
            #         imagensBytes->append(imagemBytes)
            #     catch (base64->binascii->Error, ValueError) =>
            #         continue
            # anuncioObj->setImagens(imagensBytes)
            # condominio = data->get("condominio")
            # filtros = data->get("filtros", [])

            $imovelObj = NULL;
            if ($id) {
                $imovelObj = Init::getInstance()->getImovelPorId($id);
                $imovelObj->setDataModificacao(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
            } else {
                $imovelObj = new Imovel($enderecoObj, $status, $categoria);
            }

            $imovelObj->setId($id);
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
            $imovelObj->setAnuncio($anuncioObj);
            $imovelObj->setCondominio($condominioObj);

            $consultarEndereco = Init::getInstance()->verificarEndereco(
                $imovelObj->getEndereco()
            );

            $endereco = NULL;

            if ($consultarEndereco) {
                $endereco = $consultarEndereco;
            } else {
                $cadastroEndereco = Init::getInstance()->cadastrarEndereco(
                    $imovelObj->getEndereco()
                );
                if ($cadastroEndereco) {
                    $endereco = Init::getInstance()->verificarEndereco(
                        $imovelObj->getEndereco()
                    );
                }
            }

            if (! $endereco) {
                return (["status" => "erro", "mensagem" => "ERRO ao cadastrar imóvel! Problema com o endereço"]);
            } else {
                $imovelObj->getEndereco()->setId($endereco->getId());
                $consultarCondominio = Init::getInstance()->getCondominioPorIdEndereco(
                    $endereco->getId()
                );

                if (! $consultarCondominio) {
                    $cadastrar = Init::getInstance()->cadastrarCondominio(
                        $imovelObj->getCondominio()
                    );
                    if ($cadastrar) {
                        $consultarCondominio = Init::getInstance()->getCondominioPorIdEndereco(
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

                $cadastroAnuncio = Init::getInstance()->getEstoque()->cadastrarAnuncio(
                    $imovelObj->getAnuncio()
                );
                if ($cadastroAnuncio != False) {
                    $imovelObj->getAnuncio()->setId($cadastroAnuncio);
                }
                $imovelObj->setDataCadastro(new DateTime());
                $cadastrado = Init::getInstance()->getEstoque()->cadastrarImovel($imovelObj);
                if ($cadastrado == True) {
                    return (["status" => "sucesso", "mensagem" => "imovel cadastrado!\n"]);
                } else {
                    return (["status" => "erro", "mensagem" => "ERRO ao cadastrar imóvel"]);
                }
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro interno"]);
        }
    }


    public function salvarLogin($username, $senha, $email)
    {
        $umUsuario = new Cliente(
            $nome = "",
            $cpf = "",
            $rg = "",
            $telefone = "",
            $email = ""
        );

        $umUsuario->setUsername($username);
        $umUsuario->setSenha($senha);
        $umUsuario->setEmail($email);

        $consulta = Init::getInstance()->cadastrarUsuario($umUsuario);

        if ($consulta) {
            Init::$usuarioAtual = $umUsuario;
            return (["status" => "sucesso", "mensagem" => "Cadastro realizado com sucesso"]);
        } else {
            return (["status" => "erro", "mensagem" => "ERRO ao cadastrar. Tente Novamente"]);
        }
    }
}
