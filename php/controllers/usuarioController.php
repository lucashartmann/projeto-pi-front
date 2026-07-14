<?php

require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/proprietarioDAO.php';
require_once __DIR__ . '/../dao/enderecoDAO.php';
class UsuarioController
{

    private $usuarioDAO;

    private $proprietarioDAO;

    private $enderecoDAO;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
        $this->proprietarioDAO = new ProprietarioDAO();
        $this->enderecoDAO = new EnderecoDAO();
    }
    function montarJsonUsuario(array $listaUsuarios)
    {

        if (!$listaUsuarios) {
            return (["status" => "erro", "mensagem" => "Nenhum usuário encontrado"]);
        }
        $lista = [];
        if ($listaUsuarios) {
            foreach ($listaUsuarios as $usuario) {
                if (!$usuario) {
                    continue;
                }
                $lista[] = [
                    "id" => $usuario->getId() ?? null,
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

    function listarUsuarios()
    {
        try {
            $usuarios = $this->usuarioDAO->getListaUsuarios();
            return self::montarJsonUsuario($usuarios);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar usuários"]);
        }
    }

    function atualizarUsuario($dados)
    {
        try {
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
                $usuario = $this->usuarioDAO->getUsuarioPorId($id);
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
                $verificar_endereco = $this->enderecoDAO->verificarEndereco($endereco);
                if ($verificar_endereco) {
                    $endereco = $verificar_endereco;
                } else {
                    $idEndereco = $this->enderecoDAO->cadastrarEndereco($endereco);
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
                    $atualizacao = $this->proprietarioDAO->atualizarProprietario($usuario);
                } else {
                    $atualizacao = $this->usuarioDAO->atualizarUsuario($usuario);
                }
                if ($atualizacao) {
                    return (["status" => "sucesso", "mensagem" => "Usuário atualizado com sucesso"]);
                } else {
                    return (["status" => "erro", "mensagem" => "Erro ao atualizar usuário"]);
                }
            } else {
                $usuario->setDataCadastro(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
                if ($tipo == "PROPRIETARIO") {
                    $atualizacao = $this->proprietarioDAO->cadastrarProprietario($usuario);
                } else {
                    $atualizacao = $this->usuarioDAO->cadastrarUsuario($usuario);
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

    function apagarUsuario(int $id)
    {
        try {
            $usuario = $this->usuarioDAO->getUsuarioPorId($id);
            if ($usuario) {
                $remocao = $this->usuarioDAO->getConexao()->remover("id", $id, "usuario");
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
}