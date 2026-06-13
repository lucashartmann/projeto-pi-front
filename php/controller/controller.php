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

class controller
{



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
            $lista = [];
            if ($proprietarios) {
                foreach ($proprietarios as $proprietario) {
                    $lista[] = [
                        "id" => $proprietario->getId(),
                        "email" => $proprietario->getEmail(),
                        "nome" => $proprietario->getNome(),
                        "cpf_cnpj" => $proprietario->getCpfCnpj(),
                        "rg" => $proprietario->getRg(),
                        "telefones" => [$proprietario->getTelefones()],
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
                        "imoveis" => array_map(function ($imovel) {
                            return [
                                "id" => $imovel->getId(),
                                "valor_venda" => $imovel->getValorVenda(),
                                "valor_aluguel" => $imovel->getValorAluguel(),
                                "categoria" => $imovel->getCategoria() ? $imovel->getCategoria() : null,
                                "status" => $imovel->getStatus() ? $imovel->getStatus() : null,
                                "data_cadastro" => $imovel->getDataCadastro(),
                                "data_modificacao" => $imovel->getDataModificacao()
                            ];
                        }, $proprietario->getImoveis()),
                        "data_cadastro" => $proprietario->getDataCadastro(),
                        "data_modificacao" => $proprietario->getDataModificacao()
                    ];
                }
            }
            return (["status" => "sucesso", "dados" => $lista]);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar proprietários"]);
        }
    }

    function listarUsuarios()
    {
        try {
            $usuarios = Init::getInstance()->getListaUsuarios();
            $lista = [];
            if ($usuarios) {
                foreach ($usuarios as $usuario) {
                    $lista[] = [
                        "id" => $usuario->getId(),
                        "email" => $usuario->getEmail(),
                        "nome" => $usuario->getNome(),
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
                            "complemento" => $usuario->getEndereco()->complemento ?? null
                        ] : null,
                        "data_nascimento" => $usuario->getDataNascimento() ? $usuario->getDataNascimento()->format('d-m-Y') : null,
                        "tipo" => $usuario->getTipo() ?? null,
                        "data_cadastro" => $usuario->getDataCadastro(),
                        "data_modificacao" => $usuario->getDataModificacao()
                    ];
                }
            }
            // error_log("". json_encode($lista));
            return (["status" => "sucesso", "dados" => $lista]);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar usuários"]);
        }
    }

    function atualizarUsuario($dados)
    {
        try {
            $nome = isset($dados['nome']) ? $dados['nome'] : "";
            // $email = isset($dados['email']) ? $dados['email'] : null;
            // $senha = isset($dados['senha']) ? $dados['senha'] : null;
            $dataNascimento = isset($dados['data_nascimento']) && Validacao::validarDataNascimento($dados['data_nascimento']) ? DateTime::createFromFormat('d/m/Y', $dados['data_nascimento']) : null;
            $cpfCnpj = isset($dados['cpf_cnpj']) && Validacao::validarCPF($dados['cpf_cnpj']) ? str_replace(['.', '-', ' '], '', $dados['cpf_cnpj']) : "";
            $rg = isset($dados['rg']) && Validacao::validarRG($dados['rg']) ? $dados['rg'] : "";
            $telefones = isset($dados['telefones']) && Validacao::validarTelefone($dados['telefones']) ? str_replace(['-', '(', ')'], '', $dados['telefones']) : [];
            $tipo = isset($dados['tipo']) ? $dados['tipo'] : null;
            $usuario = Null;
            $creci = isset($dados['creci']) && Validacao::validarCreci($dados['creci']) ? $dados['creci'] : "";
            $salario = isset($dados['salario']) && Validacao::validarSalario($dados['salario']) ? str_replace(['-', 'R$', ' '], '', $dados['salario']) : 0.0;
            $id = isset($dados['id']) ? $dados['id'] : 0;
            $rua = isset($dados['rua']) ? $dados['rua'] : "";
            $numero = isset($dados['numero']) ? $dados['numero'] : 0;
            $bairro = isset($dados['bairro']) ? $dados['bairro'] : "";
            $cidade = isset($dados['cidade']) ? $dados['cidade'] : "";
            $uf = isset($dados['uf']) ? $dados['uf'] : "";
            $cep = isset($dados['cep']) && Validacao::validarCEP($dados['cep']) ? str_replace('-', '', $dados['cep']) : "";
            $complemento = isset($dados['complemento']) ? $dados['complemento'] : "";
            if (!$id) {
                return (["status" => "erro", "mensagem" => "ID do usuário não fornecido"]);
            }
            $usuario = Init::getInstance()->getUsuarioPorId($id);
            if ($tipo == "CORRETOR") {
                $usuario->setCreci($creci);
            } else if ($tipo == "GERENTE") {
                $usuario->setSalario($salario);
            } else if ($tipo == "CAPTADOR") {
                $usuario->setSalario($salario);
            } else if ($tipo == "CLIENTE") {
                $usuario->setNome($nome);
            } else if ($tipo == "PROPRIETARIO") {
                $usuario->setNome($nome);
            } else {
                return (["status" => "erro", "mensagem" => "Tipo de usuário inválido"]);
            }
            $usuario->setNome($nome);
            $usuario->setCpfCnpj($cpfCnpj);
            $usuario->setDataNascimento($dataNascimento);
            $usuario->setRg($rg);
            $usuario->setTelefones($telefones);
            $usuario->setId($id);

            // $endereco = new Endereco(
            //     $endereco['rua'] ?? null,
            //     $endereco['bairro'] ?? null,
            //     $endereco['cep'] ?? null,
            //     $endereco['cidade'] ?? null,
            //     $endereco['uf'] ?? null,
            // );

            // $endereco->setNumero($endereco['numero'] ?? null);
            // $endereco->setComplemento($endereco['complemento'] ?? null);
            // $verificar_endereco = Init::getInstance()->verificarEndereco($endereco);
            // if ($verificar_endereco) {
            //     $endereco = $verificar_endereco;
            // } else {
            //     $endereco = Init::getInstance()->cadastrarEndereco($endereco);
            // }
            // $usuario->setEndereco($endereco);

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

            $nome = isset($data['nome']) ? $data['nome'] : '';
            $email = isset($data['email']) && Validacao::validarEmail($data['email']) ? $data['email'] : '';
            $senha = isset($data['senha']) && Validacao::validarSenha($data['senha']) ? $data['senha'] : "";
            $cpf = isset($data['cpf_cnpj']) && Validacao::validarCPF($data['cpf_cnpj']) ? str_replace(['.', '-', ' '], '', $data['cpf_cnpj']) : "";
            $dataNascimento = isset($data['data_nascimento']) && Validacao::validarDataNascimento($data['data_nascimento']) ? DateTime::createFromFormat('d/m/Y', $data['data_nascimento']) : null;

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

                $dados = [
                    "id" => $usuario->getId(),
                    "nome" => $usuario->getNome(),
                    "email" => $usuario->getEmail(),
                    "cpf_cnpj" => $usuario->getCpfCnpj(),
                    "rg" => $usuario->getRg(),
                    "telefones" => $usuario->getTelefones() ? [$usuario->getTelefones()] : null,
                    "endereco" => $usuario->getEndereco() ? [
                        "rua" => $usuario->getEndereco()->rua ?? null,
                        "numero" => $usuario->getEndereco()->numero ?? null,
                        "bairro" => $usuario->getEndereco()->bairro ?? null,
                        "cidade" => $usuario->getEndereco()->cidade ?? null,
                        "uf" => $usuario->getEndereco()->uf ?? null,
                        "cep" => $usuario->getEndereco()->cep ?? null,
                        "complemento" => $usuario->getEndereco()->complemento ?? null,
                    ] : null,
                    "data_nascimento" => $usuario->getDataNascimento() ? $usuario->getDataNascimento()->format('d-m-Y') : null,
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
                        foreach ($anuncioObj->getImagens() as $imagem) {
                            if ($imagem instanceof Anexo) {
                                $imagens[] =  rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" .  $imagem->getCaminho();
                            }
                        }
                    }
                    $documentos = [];
                    error_log(serialize($anuncioObj->getAnexos()));
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
                        foreach ($anuncioObj->getImagens() as $imagem) {
                            $imagens[] =  rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" .  $imagem->getCaminho();
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
                $status = $imovel->getStatus();

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
                        foreach ($anuncioObj->getImagens() as $imagem) {
                            $imagens[] =  rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/') . "/assets/" .  $imagem->getCaminho();
                        }
                    }
                    $documentos = [];
                    error_log(serialize($anuncioObj->getAnexos()));
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
                $resposta = [
                    "id" => $imovelObj->getId(),
                    "valor_venda" => $imovelObj->getValorVenda(),
                    "valor_condominio" => $imovelObj->getValorCondominio(),
                    "valor_iptu" => $imovelObj->getIptu(),
                    "valor_aluguel" => $imovelObj->getValorAluguel(),
                    "categoria" => $imovelObj->getCategoria() ?? null,
                    "status" => $imovelObj->getStatus() ?? null,
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
                    "estado" => $imovelObj->getEstado() ?? null,
                    "bloco" => $imovelObj->getBloco(),
                    "ano_construcao" => $imovelObj->getAnoConstrucao(),
                    "area_total" => $imovelObj->getAreaTotal(),
                    "area_privativa" => $imovelObj->getAreaPrivativa(),
                    "situacao" => $imovelObj->getSituacao() ?? null,
                    "ocupacao" => $imovelObj->getOcupacao() ?? null,
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
            } else {
                return (["status" => "erro", "mensagem" => "Imovel nao encontrado"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao obter imóvel: " . $e->getMessage()]);
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
            // error_log("Dados recebidos para cadastro de imóvel: " . json_encode($data));
            $id =  array_key_exists("ref", $data) ? $data["ref"] : 0;
            $nomeCondominio = array_key_exists("nome_condominio", $data) ? $data["nome_condominio"] : "";
            $valorVenda = array_key_exists("valor_venda", $data) ? (float)(str_replace(['-', 'R$', ' '], '', $data["valor_venda"]) ?? 0) : 0.0;
            $valorAluguel = array_key_exists("valor_aluguel", $data) ? (float)(str_replace(['-', 'R$', ' '], '', $data["valor_aluguel"]) ?? 0) : 0.0;
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
            error_log("Status recebido: " . ($data["status"] ?? ""));
            if (isset($data["status"]) && str_contains($data["status"], "_")) {
                $lista = explode("_", $data["status"]);
                $status_formatado = ucfirst($lista[0]) . "_" . ucfirst(end($lista));
                $status = Status::tryFrom($status_formatado) ?? null;
            } else {
                isset($data["status"]) ? $status = Status::tryFrom(ucfirst(strtolower($data["status"]))) : null;
            }
            if (!$status) {
                error_log("Status inválido recebido: " . $data["status"]);
                return (["status" => "erro", "mensagem" => "Status inválido"]);
            }
            $iptu = array_key_exists("iptu", $data) ? (float)(str_replace(['-', 'R$', ' '], '', $data["iptu"]) ?? 0) : 0.0;
            $valorCondominio = array_key_exists("valor_condominio", $data) ? (float)(str_replace(['-', 'R$', ' '], '', $data["valor_condominio"]) ?? 0) : 0.0;
            $andar = array_key_exists("andar", $data) ? (int)($data["andar"] ?? 0) : 0;
            $estado = null;
            isset($data["estado_imovel"]) ? $estado = Estado::tryFrom(ucfirst(strtolower($data["estado_imovel"]))) : null;
            $bloco = array_key_exists("bloco", $data) ? $data["bloco"] : "";
            $anoConstrucao = array_key_exists("ano_construcao", $data) ? (int)($data["ano_construcao"] ?? 0) : 0;
            $areaTotal = array_key_exists("area_total", $data) ? (float)(str_replace(['-', 'm2', ' '], '', $data["area_total"]) ?? 0) : 0.0;
            $areaPrivativa = array_key_exists("area_privativa", $data) ? (float)(str_replace(['-', 'm2', ' '], '', $data["area_privativa"]) ?? 0) : 0.0;
            $situacao = null;
            isset($data["situacao"]) ? $situacao = Situacao::tryFrom(ucfirst(strtolower($data["situacao"]))) : null;
            $ocupacao = null;
            isset($data["ocupacao"]) ? $ocupacao = Ocupacao::tryFrom(ucfirst(strtolower($data["ocupacao"]))) : null;
            $proprietarios = array_key_exists("proprietarios", $data) ? $data["proprietarios"] : [];
            $corretor = array_key_exists("corretor", $data) ? (int)$data["corretor"] : null;
            $captador = array_key_exists("captador", $data) ? (int)$data["captador"] : null;
            $cep = array_key_exists("cep", $data) ? $data["cep"] : "";
            $imagens = $_FILES["imagens"] ?? [];
            $documentos = $_FILES["documentos"] ?? [];
            // $video = $_FILES["videos"] ?? [];
            if ($cep) {
                $cep = str_replace("-", "", $cep);
            }
            $rua = array_key_exists("rua", $data) ? $data["rua"] : "";
            $bairro = array_key_exists("bairro", $data) ? $data["bairro"] : "";
            $cidade = array_key_exists("cidade", $data) ? $data["cidade"] : "";
            $titulo = array_key_exists("titulo", $data) ? $data["titulo"] : "";
            $descricao = array_key_exists("descricao", $data) ? $data["descricao"] : "";
            $complemento = array_key_exists("complemento", $data) ? $data["complemento"] : "";
            $uf = array_key_exists("uf", $data) ? $data["uf"] : "";
            $numero = array_key_exists("numero", $data) ? (int)($data["numero"] ?? null) : null;

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
                error_log("Imóvel encontrado para atualização: " . json_encode($imovelObj));
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
                    foreach ($imagens['tmp_name'] as $i => $tmpName) {
                        try {
                            if ($imagens['error'][$i] !== UPLOAD_ERR_OK) {
                                continue;
                            }
                            $caminho = salvarArquivo($tmpName, $cadastrado, 'imagem');
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
                    foreach ($documentos['name'] as $i => $tmpName) {
                        try {
                            if ($documentos['error'][$i] !== UPLOAD_ERR_OK) {
                                continue;
                            }
                            $caminho = salvarArquivo($tmpName, $cadastrado, 'documento');
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
