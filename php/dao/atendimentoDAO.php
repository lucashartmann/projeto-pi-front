<?php


require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/imovelDAO.php';
require_once __DIR__ . '/pessoaDAO.php';
require_once __DIR__ . '/../model/atendimento.php';

class AtendimentoDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function getConexao()
    {
        return $this->bancoDados;
    }

    public function buscarPorId(int $id)
    {
        try {
            $stmt = $this->bancoDados->prepare("
                SELECT * FROM atendimento 
                WHERE id = :id
            ");
            $stmt->execute([':id' => $id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe atendimento com ID {$id}");
            }

            return $this->montar($registro);
        } catch (Exception $e) {
            error_log("atendimentoDAO::buscarPorId - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function atualizar(Atendimento $atendimento)
    {
        try {
            $stmt = $this->bancoDados->prepare("
                UPDATE atendimento 
                SET id_imovel = :id_imovel, id_corretor = :id_corretor, id_cliente = :id_cliente, status = :status
                WHERE id = :id
            ");

            $imovelObj = $atendimento->getImovel();
            if ($imovelObj) {
                $imovelObj = $imovelObj->getId();
            }
            $corretorObj = $atendimento->getCorretor();
            if ($corretorObj) {
                $corretorObj = $corretorObj->getId();
            }
            $clienteObj = $atendimento->getCliente();
            if ($clienteObj) {
                $clienteObj = $clienteObj->getId();
            }
            $status = $atendimento->getStatus();
            if ($status) {
                $status = $status->value;
            }

            return $stmt->execute([
                ':id_imovel' => $imovelObj,
                ':id_corretor' => $corretorObj,
                ':id_cliente' => $clienteObj,
                ':status' => $status,
                ':id' => $atendimento->getId()
            ]);
        } catch (Exception $e) {
            error_log("atendimentoDAO::atualizar - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function montar(array $dados)
    {

        $idImovel = $dados['id_imovel'];
        $idCorretor = $dados['id_corretor'];
        $idCliente = $dados['id_cliente'];

        $pessoaDAO = new PessoaDAO();

        $atendimento = new Atendimento();

        if ($idImovel) {
            $imovelDAO = new ImovelDAO();
            $imovel = $imovelDAO->buscarPorId($idImovel);
            $atendimento->setImovel($imovel);
        }

        if ($idCorretor) {
            $corretor = $pessoaDAO->buscarPorId($idCorretor);
            $atendimento->setCorretor($corretor);
        }

        if ($idCliente) {
            $cliente = $pessoaDAO->buscarPorId($idCliente);
            $atendimento->setCliente($cliente);
        }

        $atendimento->setId($dados['id']);
        $atendimento->setStatus(StatusAtendimento::tryFrom($dados['status']));

        return $atendimento;
    }

    public function cadastrar(Atendimento $atendimento)
    {
        try {

            $sqlQuery = " 
                    INSERT INTO atendimento (id_imovel, id_corretor, id_cliente, status) 
                    VALUES(:id_imovel, :id_corretor, :id_cliente, :status)
                    ";
            $corretor_obj = $atendimento->getCorretor();
            if ($corretor_obj) {
                $corretor_obj = $corretor_obj->getId();
            }
            $cliente_obj = $atendimento->getCliente();
            if ($cliente_obj) {
                $cliente_obj = $cliente_obj->getId();
            }

            $imovelObj = $atendimento->getImovel();
            if ($imovelObj) {
                $imovelObj = $imovelObj->getId();
            }
            $status = $atendimento->getStatus();
            if ($status) {
                $status = $status->value;
            }
            $stmt = $this->bancoDados->prepare($sqlQuery);

            return $stmt->execute([
                ":id_imovel" => $imovelObj,
                ":id_corretor" => $corretor_obj,
                ":id_cliente" => $cliente_obj,
                ":status" => $status
            ]);
        } catch (Exception $e) {
            error_log("atendimentoDAO::cadastrar - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function listar()
    {
        try {



            $sql = "
            SELECT
                atendimento.id,
                atendimento.id_imovel,
                atendimento.id_corretor,
                atendimento.id_cliente,
                atendimento.status,

                imovel.id AS imovel_id,
                imovel.valor_venda AS imovel_valor_venda,
                imovel.valor_aluguel AS imovel_valor_aluguel,
                imovel.quant_quartos AS imovel_quant_quartos,
                imovel.quant_salas AS imovel_quant_salas,
                imovel.quant_vagas AS imovel_quant_vagas,
                imovel.quant_banheiros AS imovel_quant_banheiros,
                imovel.quant_varandas AS imovel_quant_varandas,
                imovel.categoria AS imovel_categoria,
                imovel.id_endereco AS imovel_id_endereco,
                imovel.status AS imovel_status,
                imovel.iptu AS imovel_iptu,
                imovel.valor_condominio AS imovel_valor_condominio,
                imovel.andar AS imovel_andar,
                imovel.estado AS imovel_estado,
                imovel.bloco AS imovel_bloco,
                imovel.ano_construcao AS imovel_ano_construcao,
                imovel.area_total AS imovel_area_total,
                imovel.area_privativa AS imovel_area_privativa,
                imovel.situacao AS imovel_situacao,
                imovel.ocupacao AS imovel_ocupacao,
                imovel.id_corretor AS imovel_id_corretor,
                imovel.id_captador AS imovel_id_captador,
                imovel.data_cadastro AS imovel_data_cadastro,
                imovel.data_modificacao AS imovel_data_modificacao,
                imovel.id_condominio AS imovel_id_condominio,
                imovel.quant_clicks AS imovel_quant_clicks,
                imovel.destacado AS imovel_destacado,
              
                imovel_endereco.id AS imovel_endereco_id,
                imovel_endereco.rua AS imovel_endereco_rua,
                imovel_endereco.numero AS imovel_endereco_numero,
                imovel_endereco.complemento AS imovel_endereco_complemento,
                imovel_endereco.bairro AS imovel_endereco_bairro,
                imovel_endereco.cep AS imovel_endereco_cep,
                imovel_endereco.cidade AS imovel_endereco_cidade,
                imovel_endereco.uf AS imovel_endereco_uf,

                imovel_endereco_corretor.id AS imovel_corretor_endereco_id,
                imovel_endereco_corretor.rua AS imovel_corretor_rua,
                imovel_endereco_corretor.numero AS imovel_corretor_numero,
                imovel_endereco_corretor.complemento AS imovel_corretor_complemento,
                imovel_endereco_corretor.bairro AS imovel_corretor_bairro,
                imovel_endereco_corretor.cep AS imovel_corretor_cep,
                imovel_endereco_corretor.cidade AS imovel_corretor_cidade,
                imovel_endereco_corretor.uf AS imovel_corretor_uf,

                atendimento_endereco_corretor.id AS atendimento_corretor_endereco_id,
                atendimento_endereco_corretor.rua AS atendimento_corretor_rua,
                atendimento_endereco_corretor.numero AS atendimento_corretor_numero,
                atendimento_endereco_corretor.complemento AS atendimento_corretor_complemento,
                atendimento_endereco_corretor.bairro AS atendimento_corretor_bairro,
                atendimento_endereco_corretor.cep AS atendimento_corretor_cep,
                atendimento_endereco_corretor.cidade AS atendimento_corretor_cidade,
                atendimento_endereco_corretor.uf AS atendimento_corretor_uf,

                atendimento_endereco_cliente.id AS atendimento_cliente_endereco_id,
                atendimento_endereco_cliente.rua AS atendimento_cliente_rua,
                atendimento_endereco_cliente.numero AS atendimento_cliente_numero,
                atendimento_endereco_cliente.complemento AS atendimento_cliente_complemento,
                atendimento_endereco_cliente.bairro AS atendimento_cliente_bairro,
                atendimento_endereco_cliente.cep AS atendimento_cliente_cep,
                atendimento_endereco_cliente.cidade AS atendimento_cliente_cidade,
                atendimento_endereco_cliente.uf AS atendimento_cliente_uf,

                imovel_endereco_captador.id AS imovel_captador_endereco_id,
                imovel_endereco_captador.rua AS imovel_captador_rua,
                imovel_endereco_captador.numero AS imovel_captador_numero,
                imovel_endereco_captador.complemento AS imovel_captador_complemento,
                imovel_endereco_captador.bairro AS imovel_captador_bairro,
                imovel_endereco_captador.cep AS imovel_captador_cep,
                imovel_endereco_captador.cidade AS imovel_captador_cidade,
                imovel_endereco_captador.uf AS imovel_captador_uf,

                condominio.id AS imovel_condominio_id,
                condominio.nome AS imovel_condominio_nome,

                imovel_pessoa_corretor.id as imovel_corretor_id,
                imovel_pessoa_corretor.email AS imovel_corretor_email,
                imovel_pessoa_corretor.nome AS imovel_corretor_nome,
                imovel_pessoa_corretor.cpf_cnpj AS imovel_corretor_cpf_cnpj,
                imovel_pessoa_corretor.rg AS imovel_corretor_rg,
                imovel_pessoa_corretor.id_endereco AS imovel_corretor_id_endereco,
                imovel_pessoa_corretor.data_nascimento AS imovel_corretor_data_nascimento,
                imovel_pessoa_corretor.data_cadastro AS imovel_corretor_data_cadastro,
                imovel_pessoa_corretor.data_modificacao AS imovel_corretor_data_modificacao,
                imovel_usuario_corretor.senha as imovel_corretor_senha,
                imovel_usuario_corretor.ultimo_login as imovel_corretor_ultimo_login,
                imovel_usuario_corretor.ativo AS imovel_corretor_ativo,
                imovel_usuario_corretor.id_pessoa AS imovel_corretor_usuario_id,
                imovel_funcionario_corretor.id_pessoa AS imovel_corretor_funcionario_id,
                imovel_funcionario_corretor.salario AS imovel_corretor_salario,
                imovel_funcionario_corretor.matricula AS imovel_corretor_matricula,
                imovel_funcionario_corretor.data_admissao AS imovel_corretor_data_admissao,
                imovel_funcionario_corretor.cargo AS imovel_corretor_cargo,
                imovel_corretor.creci as imovel_corretor_creci,
                imovel_corretor.id_funcionario AS imovel_corretor_corretor_id,

                atendimento_pessoa_corretor.id as atendimento_corretor_id,
                atendimento_pessoa_corretor.email AS atendimento_corretor_email,
                atendimento_pessoa_corretor.nome AS atendimento_corretor_nome,
                atendimento_pessoa_corretor.cpf_cnpj AS atendimento_corretor_cpf_cnpj,
                atendimento_pessoa_corretor.rg AS atendimento_corretor_rg,
                atendimento_pessoa_corretor.id_endereco AS atendimento_corretor_id_endereco,
                atendimento_pessoa_corretor.data_nascimento AS atendimento_corretor_data_nascimento,
                atendimento_pessoa_corretor.data_cadastro AS atendimento_corretor_data_cadastro,
                atendimento_pessoa_corretor.data_modificacao AS atendimento_corretor_data_modificacao,
                atendimento_usuario_corretor.senha as atendimento_corretor_senha,
                atendimento_usuario_corretor.ultimo_login as atendimento_corretor_ultimo_login,
                atendimento_usuario_corretor.ativo AS atendimento_corretor_ativo,
                atendimento_usuario_corretor.id_pessoa AS atendimento_corretor_usuario_id,
                atendimento_funcionario_corretor.id_pessoa AS atendimento_corretor_funcionario_id,
                atendimento_funcionario_corretor.salario AS atendimento_corretor_salario,
                atendimento_funcionario_corretor.matricula AS atendimento_corretor_matricula,
                atendimento_funcionario_corretor.data_admissao AS atendimento_corretor_data_admissao,
                atendimento_funcionario_corretor.cargo AS atendimento_corretor_cargo,
                atendimento_corretor.creci as atendimento_corretor_creci,
                atendimento_corretor.id_funcionario AS atendimento_corretor_corretor_id,

                imovel_pessoa_captador.id as imovel_captador_id,
                imovel_pessoa_captador.email AS imovel_captador_email,
                imovel_pessoa_captador.nome AS imovel_captador_nome,
                imovel_pessoa_captador.cpf_cnpj AS imovel_captador_cpf_cnpj,
                imovel_pessoa_captador.rg AS imovel_captador_rg,
                imovel_pessoa_captador.id_endereco AS imovel_captador_id_endereco,
                imovel_pessoa_captador.data_nascimento AS imovel_captador_data_nascimento,
                imovel_pessoa_captador.data_cadastro AS imovel_captador_data_cadastro,
                imovel_pessoa_captador.data_modificacao AS imovel_captador_data_modificacao,
                imovel_usuario_captador.senha as imovel_captador_senha,
                imovel_usuario_captador.ultimo_login as captador_ultimo_login,
                imovel_usuario_captador.ativo AS imovel_captador_ativo,
                imovel_usuario_captador.id_pessoa AS imovel_captador_usuario_id,
                imovel_funcionario_captador.id_pessoa AS imovel_captador_funcionario_id,
                imovel_funcionario_captador.salario AS imovel_captador_salario,
                imovel_funcionario_captador.matricula AS imovel_captador_matricula,
                imovel_funcionario_captador.data_admissao AS imovel_captador_data_admissao,
                imovel_funcionario_captador.cargo AS imovel_captador_cargo,

                atendimento_pessoa_cliente.id as atendimento_cliente_id,
                atendimento_pessoa_cliente.email AS atendimento_cliente_email,
                atendimento_pessoa_cliente.nome AS atendimento_cliente_nome,
                atendimento_pessoa_cliente.cpf_cnpj AS atendimento_cliente_cpf_cnpj,
                atendimento_pessoa_cliente.rg AS atendimento_cliente_rg,
                atendimento_pessoa_cliente.id_endereco AS atendimento_cliente_id_endereco,
                atendimento_pessoa_cliente.data_nascimento AS atendimento_cliente_data_nascimento,
                atendimento_pessoa_cliente.data_cadastro AS atendimento_cliente_data_cadastro,
                atendimento_pessoa_cliente.data_modificacao AS atendimento_cliente_data_modificacao,
                atendimento_usuario_cliente.senha as atendimento_cliente_senha,
                atendimento_usuario_cliente.ultimo_login as atendimento_cliente_ultimo_login,
                atendimento_usuario_cliente.ativo AS atendimento_cliente_ativo,
                atendimento_usuario_cliente.id_pessoa AS atendimento_cliente_usuario_id,

                anuncio.id_imovel AS imovel_anuncio_id,
                anuncio.descricao AS imovel_anuncio_descricao,
                anuncio.titulo AS imovel_anuncio_titulo

                FROM atendimento 

                LEFT JOIN imovel
                ON imovel.id = atendimento.id_imovel

                LEFT JOIN endereco imovel_endereco
                ON imovel_endereco.id = imovel.id_endereco

            LEFT JOIN condominio 
                ON condominio.id = imovel.id_condominio

            LEFT JOIN anuncio 
                ON anuncio.id_imovel = imovel.id

            LEFT JOIN pessoa atendimento_pessoa_corretor
                ON atendimento_pessoa_corretor.id = atendimento.id_corretor

            LEFT JOIN pessoa atendimento_pessoa_cliente
                ON atendimento_pessoa_cliente.id = atendimento.id_cliente

            LEFT JOIN pessoa imovel_pessoa_corretor
                ON imovel_pessoa_corretor.id = imovel.id_corretor

            LEFT JOIN pessoa imovel_pessoa_captador
                ON imovel_pessoa_captador.id = imovel.id_captador

            LEFT JOIN usuario imovel_usuario_corretor
                ON imovel_usuario_corretor.id_pessoa = imovel_pessoa_corretor.id

            LEFT JOIN usuario imovel_usuario_captador
                ON imovel_usuario_captador.id_pessoa = imovel_pessoa_captador.id

            LEFT JOIN usuario atendimento_usuario_corretor
                ON atendimento_usuario_corretor.id_pessoa = atendimento_pessoa_corretor.id

            LEFT JOIN usuario atendimento_usuario_cliente
                ON atendimento_usuario_cliente.id_pessoa = atendimento_pessoa_cliente.id

            LEFT JOIN funcionario imovel_funcionario_corretor
                ON imovel_funcionario_corretor.id_pessoa = imovel_pessoa_corretor.id

            LEFT JOIN funcionario imovel_funcionario_captador
                ON imovel_funcionario_captador.id_pessoa = imovel_pessoa_captador.id

            LEFT JOIN funcionario atendimento_funcionario_corretor
                ON atendimento_funcionario_corretor.id_pessoa = atendimento_pessoa_corretor.id

            LEFT JOIN corretor imovel_corretor
                ON imovel_corretor.id_funcionario = imovel_funcionario_corretor.id_pessoa

            LEFT JOIN corretor atendimento_corretor
                ON atendimento_corretor.id_funcionario = atendimento_funcionario_corretor.id_pessoa

            LEFT JOIN endereco imovel_endereco_captador
                ON imovel_endereco_captador.id = imovel_pessoa_captador.id_endereco

            LEFT JOIN endereco imovel_endereco_corretor
                ON imovel_endereco_corretor.id = imovel_pessoa_corretor.id_endereco

            LEFT JOIN endereco atendimento_endereco_cliente
                ON atendimento_endereco_cliente.id = atendimento_pessoa_cliente.id_endereco

            LEFT JOIN endereco atendimento_endereco_corretor
                ON atendimento_endereco_corretor.id = atendimento_pessoa_corretor.id_endereco

                
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $lista = [];
            $pessoaDAO = new PessoaDAO();
            $imovelDAO = new ImovelDAO();
            foreach ($registros as $registro) {
                $idAtendimento = $registro['id'];
                $idImovel = $registro['id_imovel'];
                $idCorretor = $registro['id_corretor'];
                $idComprador = $registro['id_cliente'];
                $status = $registro['status'];
                $imovel = null;
                $corretor = null;
                $cliente = null;
                if ($idImovel) {
                    $dadosImovel = array_filter($registro, function ($key) {
                        return strpos($key, 'imovel_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosImovel = array_combine(
                        array_map(function ($key) {
                            return preg_replace('/imovel_/', '', $key, 1);
                        }, array_keys($dadosImovel)),
                        $dadosImovel
                    );
                    $imovel = $imovelDAO->montar($dadosImovel);
                    if ($imovel) {
                        $dadosCorretor = array_filter($registro, function ($key) {
                            return strpos($key, 'imovel_corretor_') === 0;
                        }, ARRAY_FILTER_USE_KEY);
                        $dadosCorretor = array_combine(
                            array_map(function ($key) {
                                return preg_replace('/imovel_corretor_/', '', $key, 1);
                            }, array_keys($dadosCorretor)),
                            $dadosCorretor
                        );
                        $dadosCaptador = array_filter($registro, function ($key) {
                            return strpos($key, 'imovel_captador_') === 0;
                        }, ARRAY_FILTER_USE_KEY);
                        $dadosCaptador = array_combine(
                            array_map(function ($key) {
                                return str_replace('imovel_captador_', '', $key);
                            }, array_keys($dadosCaptador)),
                            $dadosCaptador
                        );
                        $corretor = null;
                        $captador = null;
                        try {
                            if ($dadosCorretor['id'] !== null) {
                                $corretor = $pessoaDAO->montar($dadosCorretor);
                            }
                        } catch (Exception $e) {
                            error_log("ERRO! imovelDAO->listarDisponiveis: " . $e->getMessage());
                        }
                        try {
                            if ($dadosCaptador['id'] !== null) {
                                $captador = $pessoaDAO->montar($dadosCaptador);
                            }
                        } catch (Exception $e) {
                            error_log("ERRO! imovelDAO->listarDisponiveis: " . $e->getMessage());
                        }

                        if ($corretor) {
                            $imovel->setCorretor($corretor);
                        }
                        if ($captador) {
                            $imovel->setCaptador($captador);
                        }
                    }
                }
                $corretor = null;
                $cliente = null;
                if ($idCorretor) {
                    $dadosCorretor = array_filter($registro, function ($key) {
                        return strpos($key, 'atendimento_corretor_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCorretor = array_combine(
                        array_map(function ($key) {
                            return preg_replace('/atendimento_corretor_/', '', $key, 1);
                        }, array_keys($dadosCorretor)),
                        $dadosCorretor
                    );
                    $corretor = $pessoaDAO->montar($dadosCorretor);
                }
                if ($idComprador) {
                    $dadosComprador = array_filter($registro, function ($key) {
                        return strpos($key, 'atendimento_cliente_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosComprador = array_combine(
                        array_map(function ($key) {
                            return preg_replace('/atendimento_cliente_/', '', $key, 1);
                        }, array_keys($dadosComprador)),
                        $dadosComprador
                    );
                    $cliente = $pessoaDAO->montar($dadosComprador);
                }
                if ($status) {
                    $status = StatusAtendimento::tryFrom($status);
                }
                $atendimentoObj = new Atendimento();
                $atendimentoObj->setStatus($status);
                $atendimentoObj->setId($idAtendimento);
                $atendimentoObj->setCorretor($corretor);
                $atendimentoObj->setCliente($cliente);
                $atendimentoObj->setImovel($imovel);
                $lista[] = $atendimentoObj;
            }
            return $lista;
        } catch (Exception $e) {
            error_log("atendimentoDAO::listar - Error: " . $e->getMessage());
            throw $e;
        }
    }
    public function listarPorUsuario(int $idUsuario)
    {
        try {
            $sql = "
            SELECT * FROM atendimento WHERE id_cliente = :id_usuario
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);
            $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$atendimentos) {
                throw new Exception("Não existem atendimentos para o usuário com id {$idUsuario}");
            }
            $listaAtendimentos = [];
            foreach ($atendimentos as $dados) {
                $atendimento = $this->montar($dados);
                $listaAtendimentos[] = $atendimento;
            }
            return $listaAtendimentos;
        } catch (Exception $e) {
            error_log("atendimentoDAO::listarPorUsuario - Error: " . $e->getMessage());
            throw $e;
        }
    }
}
