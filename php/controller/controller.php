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
require_once __DIR__ . '/../model/anexo.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/__init__.php';
require_once __DIR__ . '/../model/validacao.php';
require_once __DIR__ . '/../model/seguranca.php';
require_once __DIR__ . '/../utils/caminho_xamp.php';
require_once __DIR__ . '/../utils/imagem.php';
require_once __DIR__ . '/../utils/email.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use function PHPUnit\Framework\isInstanceOf;

class controller
{


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
                            $imagens[] =  rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" .  $imagem->getCaminho();
                        }
                    }
                }
                $documentos = [];
                if ($anuncioObj->getAnexos()) {
                    foreach ($anuncioObj->getAnexos() as $documento) {
                        if ($documento instanceof Anexo) {
                            $documentos[] =  rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" .  $documento->getCaminho();
                        }
                    }
                }
                $videos = [];
                if ($anuncioObj->getVideos()) {
                    foreach ($anuncioObj->getVideos() as $video) {
                        if ($video instanceof Anexo) {
                            $videos[] =  rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" .  $video->getCaminho();
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
                "condominio" => $condominio ?? null
            ];
        }

        return $lista;
    }

    function montarJsonUsuario(array $listaUsuarios)
    {

        if (!$listaUsuarios) {
            return (["status" => "erro", "mensagem" => "Nenhum usuário encontrado"]);
        }
        $lista = [];
        if ($listaUsuarios) {
            foreach ($listaUsuarios as $usuario) {
                $lista[] = [
                    "id" => $usuario->getId(),
                    "email" => $usuario->getEmail(),
                    "nome" => $usuario->getNome(),
                    "cpf_cnpj" => $usuario->getCpfCnpj(),
                    "rg" => $usuario->getRg(),
                    "username" => $usuario->getUsername() ?? null,
                    "telefones" => [$usuario->getTelefones()],
                    "endereco" => $usuario->getEndereco() ? [
                        "rua" => $usuario->getEndereco()->rua ?? null,
                        "numero" => $usuario->getEndereco()->numero ?? null,
                        "bairro" => $usuario->getEndereco()->bairro ?? null,
                        "cidade" => $usuario->getEndereco()->cidade ?? null,
                        "uf" => $usuario->getEndereco()->uf ?? null,
                        "cep" => $usuario->getEndereco()->cep ?? null,
                        "complemento" => $usuario->getEndereco()->complemento ?? null
                    ] : null,
                    "creci" => $usuario instanceof Corretor ? $usuario->getCreci() ?? null : null,
                    "salario" => method_exists($usuario, 'getSalario') ? $usuario->getSalario() ?? null : null,
                    "data_nascimento" => $usuario->getDataNascimento() ? $usuario->getDataNascimento()->format('d-m-Y') : null,
                    "tipo" => $usuario->getTipo() ?? null,
                    "data_cadastro" => $usuario->getDataCadastro() ? $usuario->getDataCadastro() : null,
                    "data_modificacao" => $usuario->getDataModificacao() ? $usuario->getDataModificacao() : null,
                    "imoveis" => array_map(function ($imovel) {
                        return [
                            "id" => $imovel->getId(),
                            "valor_venda" => $imovel->getValorVenda(),
                            "valor_aluguel" => $imovel->getValorAluguel(),
                            "categoria" => $imovel->getCategoria() ? $imovel->getCategoria() : null,
                            "status" => $imovel->getStatus() ? $imovel->getStatus() : null,
                            "data_cadastro" => $imovel->getDataCadastro(),
                            "data_modificacao" => $imovel->getDataModificacao(),
                            "anuncio" => [
                                "id" => $imovel->getAnuncio()->getId(),
                                "descricao" => $imovel->getAnuncio()->getDescricao(),
                                "titulo" => $imovel->getAnuncio()->getTitulo(),
                                "imagens" => $imovel->getAnuncio()->getImagens() ? array_map(function ($imagem) {
                                    return rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" . $imagem->getCaminho();
                                }, $imovel->getAnuncio()->getImagens()) : [],
                                "documentos" => $imovel->getAnuncio()->getAnexos() ? array_map(function ($documento) {
                                    return rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" . $documento->getCaminho();
                                }, $imovel->getAnuncio()->getAnexos()) : [],
                                "videos" => $imovel->getAnuncio()->getVideos() ? array_map(function ($video) {
                                    return rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" . $video->getCaminho();
                                }, $imovel->getAnuncio()->getVideos()) : [],
                            ]
                        ];
                    }, $usuario instanceof Proprietario ? $usuario->getImoveis() ?? [] : []),
                ];
            }
        }
        return ($lista);
    }

    function carregarFavoritos()
    {
        try {
            session_start();
            if (!isset($_SESSION['usuario_id'])) {
                return (["status" => "erro", "mensagem" => "Usuário não logado"]);
            }
            $idCliente = $_SESSION['usuario_id'];
            $imoveisFavoritos = Init::getInstance()->getImoveisFavoritos($idCliente);
            if (!$imoveisFavoritos) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel favorito encontrado para o usuário"
                ];
            } else {
                return self::montarJsonImoveis($imoveisFavoritos);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao carregar favoritos: " . $e->getMessage()]);
        }
    }

    function favoritarImoveis($data)
    {
        try {
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return (["status" => "erro", "mensagem" => "JSON inválido"]);
            }
            if (!Init::getInstance()->usuarioAtual) {
                return (["status" => "erro", "mensagem" => "Usuário não logado"]);
            }
            $idCliente = Init::getInstance()->usuarioAtual->getId();
            $idImoveis = $data['id_imoveis'] ?? null;
            if (!$idCliente || !is_array($idImoveis)) {
                return (["status" => "erro", "mensagem" => "ID do cliente ou lista de imóveis inválidos"]);
            }
            $resultado = Init::getInstance()->cadastrarImoveisCliente($idCliente, $idImoveis);
            if ($resultado) {
                return (["status" => "sucesso", "mensagem" => "Imóveis favoritados com sucesso"]);
            } else {
                return (["status" => "erro", "mensagem" => "Erro ao favoritar imóveis"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao favoritar imóveis: " . $e->getMessage()]);
        }
    }

    function recuperarSenha($data)
    {
        try {
            $email = $data['email'] ?? '';
            if (!$email || !Validacao::validarEmail($email)) {
                return [
                    "status" => "erro",
                    "mensagem" => "Email inválido ou não fornecido"
                ];
            }
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'seuemail@outlook.com';
            $mail->Password = 'suasenha';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('seuemail@outlook.com', 'Summit');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de Senha';
            $mail->Body = getArquivo();
            $mail->send();
            return [
                "status" => "sucesso",
                "mensagem" => "Instruções enviadas para o email"
            ];
        } catch (Exception $e) {
            return [
                "status" => "erro",
                "mensagem" => $e->getMessage()
            ];
        }
    }
    function listarProprietarios()
    {
        try {
            $proprietarios = Init::getInstance()->getListaProprietarios();
            return self::montarJsonUsuario($proprietarios);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar proprietários"]);
        }
    }

    function listarUsuarios()
    {
        try {
            $usuarios = Init::getInstance()->getListaUsuarios();
            return self::montarJsonUsuario($usuarios);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar usuários"]);
        }
    }

    function atualizarUsuario($dados)
    {
        try {
            error_log("Dados recebidos para atualizar usuário: " . json_encode($dados));
            $nome = array_key_exists('nome', $dados) ? $dados['nome'] : "";
            $email = array_key_exists('email', $dados) ? $dados['email'] : "";
            $senha = array_key_exists('senha', $dados) ? $dados['senha'] : "";
            $username = $email;
            $dataNascimento = array_key_exists('data_nascimento', $dados) && Validacao::validarDataNascimento($dados['data_nascimento']) ? DateTime::createFromFormat('d/m/Y', $dados['data_nascimento']) : null;
            $cpfCnpj = array_key_exists('cpf_cnpj', $dados) && Validacao::validarCPF($dados['cpf_cnpj']) ? str_replace(['.', '-', ' '], '', $dados['cpf_cnpj']) : "";
            $rg = array_key_exists('rg', $dados) && Validacao::validarRG($dados['rg']) ? $dados['rg'] : "";
            $telefones = array_key_exists('telefones', $dados) && Validacao::validarTelefone($dados['telefones']) ? str_replace(['-', '(', ')'], '', $dados['telefones']) : [];
            $tipo = array_key_exists('tipo', $dados) ? $dados['tipo'] : null;
            $usuario = Null;
            $creci = array_key_exists('creci', $dados) && Validacao::validarCreci($dados['creci']) ? $dados['creci'] : "";
            $salario = array_key_exists('salario', $dados) && Validacao::validarSalario($dados['salario']) ? str_replace(['-', 'R$', ' '], '', $dados['salario']) : 0.0;
            $id = array_key_exists('id', $dados) ? $dados['id'] : 0;
            $rua = array_key_exists('rua', $dados) ? $dados['rua'] : "";
            $numero = array_key_exists('numero', $dados) ? $dados['numero'] : 0;
            $bairro = array_key_exists('bairro', $dados) ? $dados['bairro'] : "";
            $cidade = array_key_exists('cidade', $dados) ? $dados['cidade'] : "";
            $uf = array_key_exists('uf', $dados) ? $dados['uf'] : "";
            $cep = array_key_exists('cep', $dados) && Validacao::validarCEP($dados['cep']) ? str_replace('-', '', $dados['cep']) : "";
            $complemento = array_key_exists('complemento', $dados) ? $dados['complemento'] : "";

            if ($id > 0) {
                $usuario = Init::getInstance()->getUsuarioPorId($id);
            } else {
                switch ($tipo) {
                    case "CORRETOR":
                        $usuario = new Corretor($username, $senha, $email, $nome, $cpfCnpj, $creci);
                        break;
                    case "GERENTE":
                        $usuario = new Gerente($username, $senha, $email, $nome, $cpfCnpj);
                        $usuario->setSalario($salario);
                        break;
                    case "CAPTADOR":
                        $usuario = new Captador($username, $senha, $email, $nome, $cpfCnpj);
                        $usuario->setSalario($salario);
                        break;
                    case "CLIENTE":
                        $usuario = new Cliente($username, $senha, $email, $nome, $cpfCnpj);
                        $usuario->setNome($nome);
                        break;
                    case "PROPRIETARIO":
                        $usuario = new Proprietario($email, $nome, $cpfCnpj);
                        $usuario->setNome($nome);
                        break;
                    case "FINANCEIRO":
                        $usuario = new Usuario($username, $senha, $email, $nome, $cpfCnpj, Tipo::FINANCEIRO);
                        // $usuario->setSalario($salario);
                        break;
                    case "VISTORIADOR":
                        $usuario = new Usuario($username, $senha, $email, $nome, $cpfCnpj, Tipo::VISTORIADOR);
                        break;
                    case "ADMINISTRADOR":
                        $usuario = new Usuario($username, $senha, $email, $nome, $cpfCnpj, Tipo::ADMINISTRADOR);
                        break;
                    default:
                        return (["status" => "erro", "mensagem" => "Tipo de usuário inválido"]);
                }
            }

            $usuario->setNome($nome);
            $usuario->setCpfCnpj($cpfCnpj);
            $usuario->setDataNascimento($dataNascimento);
            $usuario->setRg($rg);
            $usuario->setTelefones($telefones);

            if ($cep) {
                $endereco = new Endereco(
                    $rua ?? "",
                    $bairro ?? "",
                    $cep ?? "",
                    $cidade ?? "",
                    $uf ?? "",
                );
                $endereco->setNumero($numero ?? null);
                $endereco->setComplemento($complemento ?? null);
                $verificar_endereco = Init::getInstance()->verificarEndereco($endereco);
                if ($verificar_endereco) {
                    $endereco = $verificar_endereco;
                } else {
                    $idEndereco = Init::getInstance()->cadastrarEndereco($endereco);
                    $endereco->setId($idEndereco);
                }
            } else {
                $endereco = null;
            }


            $usuario->setEndereco($endereco);
            $atualizacao = null;
            $usuario->setDataModificacao(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
            if ($id > 0) {
                if ($tipo == "PROPRIETARIO") {
                    $atualizacao = Init::getInstance()->atualizarProprietario($usuario);
                } else {
                    $atualizacao = Init::getInstance()->atualizarUsuario($usuario);
                }
                if ($atualizacao) {
                    return (["status" => "sucesso", "mensagem" => "Usuário atualizado com sucesso"]);
                } else {
                    return (["status" => "erro", "mensagem" => "Erro ao atualizar usuário"]);
                }
            } else {
                $usuario->setDataCadastro(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
                if ($tipo == "PROPRIETARIO") {
                    $atualizacao = Init::getInstance()->cadastrarProprietario($usuario);
                } else {
                    $atualizacao = Init::getInstance()->cadastrarUsuario($usuario);
                }
                if ($atualizacao) {
                    return (["status" => "sucesso", "mensagem" => "Usuário cadastrado com sucesso"]);
                } else {
                    return (["status" => "erro", "mensagem" => "Erro ao cadastrar usuário"]);
                }
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao atualizar usuário: " . $e->getMessage()]);
        }
    }

    function deslogar()
    {
        try {
            session_start();
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                try {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params['httponly']);
                } catch (Exception $e) {
                    return;
                }
            }
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

                $dados = self::montarJsonUsuario([$usuario]);

                return ([
                    "status" => "sucesso",
                    "tipo" => $_SESSION['tipo'],
                    "usuario" =>  is_array($dados) ? $dados[0] : null
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
                    "tipo" => $consulta->getTipo() ? $consulta->getTipo()->value : null,
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
                        "status" =>  $atendimento->getStatus() ? $atendimento->getStatus() : NULL,
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
            if (!$imoveis) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel encontrado"
                ];
            } else {
                $resposta =  self::montarJsonImoveis($imoveis);
                return $resposta;
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar imóveis: " . $e->getMessage()]);
        }
    }


    function getListaImoveisDisponiveis()
    {
        try {
            $imoveis = Init::getInstance()->getEstoque()->getListaImoveisDisponiveis();
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

            $imovelObj = Init::getInstance()->getImovelPorId((int)$id);

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

    function apagarUsuario(int $id)
    {
        try {
            $usuario = Init::getInstance()->getUsuarioPorId($id);
            if ($usuario) {
                $remocao = Init::getInstance()->remover("id", $id, "usuario");
                if ($remocao) {
                    return (["status" => "sucesso", "mensagem" => "Usuário removido com sucesso"]);
                } else {
                    return (["status" => "erro", "mensagem" => "Erro ao remover usuário"]);
                }
            } else {
                return (["status" => "erro", "mensagem" => "Usuário não encontrado"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => $e->getMessage()]);
        }
    }


    function apagarImovel(int $id)
    {
        try {
            $imovel = Init::getInstance()->getImovelPorId($id);
            if ($imovel) {
                $remocao = Init::getInstance()->remover("id", $id, "imovel");
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
            error_log("Dados recebidos para cadastro de imóvel: " . json_encode($data));
            $id =  array_key_exists("ref", $data) ? $data["ref"] : 0;
            $nomeCondominio = array_key_exists("nome_condominio", $data) ? $data["nome_condominio"] : "";

            $valorVenda = array_key_exists("valor_venda", $data) ? $data["valor_venda"] : 0.0;
            $valorVenda = $valorVenda ? str_replace(".", "", $valorVenda ?? 0.0) : 0.0;
            $valorVenda = $valorVenda ? str_replace(",", ".", $valorVenda ?? 0.0) : 0.0;
            $valorVenda = $valorVenda ? (float)trim(str_replace(['-', 'R$'], '', $valorVenda) ?? 0) : 0.0;

            $valorAluguel = array_key_exists("valor_aluguel", $data) ? $data["valor_aluguel"] : 0.0;
            $valorAluguel = $valorAluguel ? str_replace(".", "", $valorAluguel ?? 0.0) : 0.0;
            $valorAluguel = $valorAluguel ? str_replace(",", ".", $valorAluguel ?? 0.0) : 0.0;
            $valorAluguel = $valorAluguel ? (float)trim(str_replace(['-', 'R$'], '', $valorAluguel) ?? 0) : 0.0;

            $quantQuartos = array_key_exists("quantidade_quartos", $data) ? (int)($data["quantidade_quartos"] ?? 0) : 0;
            $quantSalas = array_key_exists("quantidade_salas", $data) ? (int)($data["quantidade_salas"] ?? 0) : 0;
            $quantVagas = array_key_exists("quantidade_vagas", $data) ? (int)($data["quantidade_vagas"] ?? 0) : 0;
            $quantBanheiros = array_key_exists("quantidade_banheiros", $data) ? (int)($data["quantidade_banheiros"] ?? 0) : 0;
            $quantVarandas = array_key_exists("quantidade_varandas", $data) ? (int)($data["quantidade_varandas"] ?? 0) : 0;
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
            $iptu = $iptu ? (float)trim(str_replace(['-', 'R$'], '', $iptu) ?? 0) : 0.0;

            $valorCondominio = array_key_exists("valor_condominio", $data) ? $data["valor_condominio"] : 0.0;
            $valorCondominio = $valorCondominio ? str_replace(".", "", $valorCondominio ?? 0.0) : 0.0;
            $valorCondominio = $valorCondominio ? str_replace(",", ".", $valorCondominio ?? 0.0) : 0.0;
            $valorCondominio = $valorCondominio ? (float)trim(str_replace(['-', 'R$'], '', $valorCondominio) ?? 0) : 0.0;

            $andar = array_key_exists("andar", $data) ? (int)trim($data["andar"] ?? 0) : 0;
            $estado = null;
            isset($data["estado_imovel"]) ? $estado = Estado::tryFrom($data["estado_imovel"]) : null;
            $bloco = array_key_exists("bloco", $data) ? $data["bloco"] : "";
            $anoConstrucao = array_key_exists("ano_construcao", $data) ? (int)trim($data["ano_construcao"] ?? 0) : 0;

            $areaTotal = array_key_exists("area_total", $data) ? $data["area_total"] : 0.0;
            $areaTotal = $areaTotal ? str_replace(".", "", $areaTotal ?? 0.0) : 0.0;
            $areaTotal = $areaTotal ? str_replace(",", ".", $areaTotal ?? 0.0) : 0.0;
            $areaTotal = $areaTotal ? (float)trim(str_replace('m2', '', $areaTotal) ?? 0) : 0.0;

            $areaPrivativa = array_key_exists("area_privativa", $data) ? $data["area_privativa"] : 0.0;
            $situacao = null;
            $areaPrivativa = $areaPrivativa ? str_replace(".", "", $areaPrivativa ?? 0.0) : 0.0;
            $areaPrivativa = $areaPrivativa ? str_replace(",", ".", $areaPrivativa ?? 0.0) : 0.0;
            $areaPrivativa = $areaPrivativa ? (float)trim(str_replace('m2', '', $areaPrivativa) ?? 0) : 0.0;


            isset($data["situacao"]) ? $situacao = Situacao::tryFrom($data["situacao"]) : null;
            $ocupacao = null;
            isset($data["ocupacao"]) ? $ocupacao = Ocupacao::tryFrom($data["ocupacao"]) : null;
            $proprietarios = array_key_exists("proprietarios", $data) ? $data["proprietarios"] : [];
            $corretor = array_key_exists("corretor", $data) ? (int)$data["corretor"] : null;
            $captador = array_key_exists("captador", $data) ? (int)$data["captador"] : null;
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
            $numero = array_key_exists("numero", $data) ? (int)trim($data["numero"] ?? null) : null;

            $enderecoObj = new Endereco($rua, $bairro, $cep, $cidade, $uf);
            $enderecoObj->setNumero($numero);
            $enderecoObj->setComplemento($complemento);
            $enderecoObj->setUf($uf);
            $condominioObj = new Condominio(
                $nomeCondominio,
                $enderecoObj
            );

            if ($corretor) {
                $corretor = Init::getInstance()->getUsuarioPorId($corretor) ?? null;
            }

            if ($captador) {
                $captador = Init::getInstance()->getUsuarioPorId($captador) ?? null;
            }

            if ($proprietarios) {
                $proprietariosObjs = [];
                foreach ($proprietarios as $proprietario) {
                    if (isset($proprietario["id"])) {
                        $proprietarioObj = Init::getInstance()->getProprietarioPorId($proprietario["id"]);
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
                $imovelObj = Init::getInstance()->getImovelPorId($id);
                $imovelObj->setDataModificacao(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
                // error_log("Imóvel encontrado para atualização: " . json_encode($imovelObj));
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

            if (!$endereco) {
                return (["status" => "erro", "mensagem" => "ERRO ao cadastrar imóvel! Problema com o endereço"]);
            } else {
                $imovelObj->getEndereco()->setId($endereco->getId());
                $consultarCondominio = Init::getInstance()->getCondominioPorIdEndereco(
                    $endereco->getId()
                );

                if (!$consultarCondominio) {
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


                if ($imovelObj->getAnuncio()->getId() === null || $imovelObj->getAnuncio()->getId() === 0) {
                    $cadastroAnuncio = Init::getInstance()->getEstoque()->cadastrarAnuncio(
                        $imovelObj->getAnuncio()
                    );
                    if ($cadastroAnuncio) {
                        $imovelObj->getAnuncio()->setId($cadastroAnuncio);
                    }
                } else {

                    $cadastroAnuncio = Init::getInstance()
                        ->getEstoque()
                        ->atualizarAnuncio($anuncioObj);
                }

                $imovelObj->setDataCadastro(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
                if ($id) {
                    $atualizado = Init::getInstance()->getEstoque()->atualizarImovel($imovelObj);
                    if ($atualizado) {
                        $cadastrado = $id;
                    } else {
                        $cadastrado = null;
                    }

                    limparPasta($imovelObj->getAnuncio()->getImagens(), $imovelObj->getId());
                    limparPasta($imovelObj->getAnuncio()->getAnexos(), $imovelObj->getId());
                } else {
                    $cadastrado = Init::getInstance()->getEstoque()->cadastrarImovel($imovelObj);
                }
                if ($cadastrado && $cadastroAnuncio && $imagens) {
                    $imagensObjetos = [];
                    error_log('quantidade de imagens recebidas: ' . count($imagens['tmp_name']));
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
                    Init::getInstance()
                        ->getEstoque()
                        ->atualizarAnuncio($anuncioObj);
                }
                if ($cadastrado && $cadastroAnuncio && $documentos) {
                    $documentosObjetos = [];
                    error_log('quantidade de documentos recebidos: ' . count($documentos['tmp_name']));
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
                    Init::getInstance()
                        ->getEstoque()
                        ->atualizarAnuncio($anuncioObj);
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

    public function salvarLogin(string $username, string $senha, string $email)
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
