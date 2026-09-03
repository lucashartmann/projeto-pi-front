<?php


require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/historico.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class HistoricoDAO
{
    private Banco $bancoDados;

    private $sql = "
                SELECT 
                 
                anuncio.id_imovel AS imovel_anuncio_id,
                anuncio.descricao AS imovel_anuncio_descricao,
                anuncio.titulo AS imovel_anuncio_titulo,

                condominio.id AS imovel_condominio_id,
                condominio.nome AS imovel_condominio_nome,

                imovel.id AS imovel_id,
                imovel.valor_venda AS imovel_valor_venda,
                imovel.valor_aluguel AS imovel_valor_aluguel,
                imovel.quant_quartos AS imovel_quant_quartos,
                imovel.quant_salas AS imovel_quant_salas,
                imovel.quant_vagas AS imovel_quant_vagas,
                imovel.quant_banheiros AS imovel_quant_banheiros,
                imovel.quant_varandas AS imovel_quant_varandas,
                imovel.quant_suites AS imovel_quant_suites,
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

                imovel_endereco_captador.id AS imovel_captador_endereco_id,
                imovel_endereco_captador.rua AS imovel_captador_rua,
                imovel_endereco_captador.numero AS imovel_captador_numero,
                imovel_endereco_captador.complemento AS imovel_captador_complemento,
                imovel_endereco_captador.bairro AS imovel_captador_bairro,
                imovel_endereco_captador.cep AS imovel_captador_cep,
                imovel_endereco_captador.cidade AS imovel_captador_cidade,
                imovel_endereco_captador.uf AS imovel_captador_uf,

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
                imovel_usuario_captador.ultimo_login as imovel_captador_ultimo_login,
                imovel_usuario_captador.ativo AS imovel_captador_ativo,
                imovel_usuario_captador.id_pessoa AS imovel_captador_usuario_id,
                imovel_funcionario_captador.id_pessoa AS imovel_captador_funcionario_id,
                imovel_funcionario_captador.salario AS imovel_captador_salario,
                imovel_funcionario_captador.matricula AS imovel_captador_matricula,
                imovel_funcionario_captador.data_admissao AS imovel_captador_data_admissao,
                imovel_funcionario_captador.cargo AS imovel_captador_cargo,

                historico.id,
                historico.descricao,
                historico.data,

                historico_pessoa_cliente.id as historico_cliente_cliente_id,
                historico_pessoa_cliente.email AS historico_cliente_email,
                historico_pessoa_cliente.nome AS historico_cliente_nome,
                historico_pessoa_cliente.cpf_cnpj AS historico_cliente_cpf_cnpj,
                historico_pessoa_cliente.rg AS historico_cliente_rg,
                historico_pessoa_cliente.id_endereco AS historico_cliente_id_endereco,
                historico_pessoa_cliente.data_nascimento AS historico_cliente_data_nascimento,
                historico_pessoa_cliente.data_cadastro AS historico_cliente_data_cadastro,
                historico_pessoa_cliente.data_modificacao AS historico_cliente_data_modificacao,
                historico_usuario_cliente.senha as historico_cliente_senha,
                historico_usuario_cliente.ultimo_login as historico_cliente_ultimo_login,
                historico_usuario_cliente.ativo AS historico_cliente_ativo,
                historico_usuario_cliente.id_pessoa AS historico_cliente_usuario_id,

                historico_pessoa_funcionario.id as historico_funcionario_id,
                historico_pessoa_funcionario.email AS historico_funcionario_email,
                historico_pessoa_funcionario.nome AS historico_funcionario_nome,
                historico_pessoa_funcionario.cpf_cnpj AS historico_funcionario_cpf_cnpj,
                historico_pessoa_funcionario.rg AS historico_funcionario_rg,
                historico_pessoa_funcionario.id_endereco AS historico_funcionario_id_endereco,
                historico_pessoa_funcionario.data_nascimento AS historico_funcionario_data_nascimento,
                historico_pessoa_funcionario.data_cadastro AS historico_funcionario_data_cadastro,
                historico_pessoa_funcionario.data_modificacao AS historico_funcionario_data_modificacao,
                historico_usuario_funcionario.senha as historico_funcionario_senha,
                historico_usuario_funcionario.ultimo_login as historico_funcionario_ultimo_login,
                historico_usuario_funcionario.ativo AS historico_funcionario_ativo,
                historico_usuario_funcionario.id_pessoa AS historico_funcionario_usuario_id,
                historico_funcionario.id_pessoa AS historico_funcionario_funcionario_id,
                historico_funcionario.salario AS historico_funcionario_salario,
                historico_funcionario.matricula AS historico_funcionario_matricula,
                historico_funcionario.data_admissao AS historico_funcionario_data_admissao,
                historico_funcionario.cargo AS historico_funcionario_cargo,
                historico_corretor.creci as historico_funcionario_creci,
                historico_corretor.id_funcionario AS historico_funcionario_corretor_id,
    

                historico_endereco_funcionario.id AS historico_funcionario_endereco_id,
                historico_endereco_funcionario.rua AS historico_funcionario_rua,
                historico_endereco_funcionario.numero AS historico_funcionario_numero,
                historico_endereco_funcionario.complemento AS historico_funcionario_complemento,
                historico_endereco_funcionario.bairro AS historico_funcionario_bairro,
                historico_endereco_funcionario.cep AS historico_funcionario_cep,
                historico_endereco_funcionario.cidade AS historico_funcionario_cidade,
                historico_endereco_funcionario.uf AS historico_funcionario_uf,

                historico_endereco_cliente.id AS historico_cliente_endereco_id,
                historico_endereco_cliente.rua AS historico_cliente_rua,
                historico_endereco_cliente.numero AS historico_cliente_numero,
                historico_endereco_cliente.complemento AS historico_cliente_complemento,
                historico_endereco_cliente.bairro AS historico_cliente_bairro,
                historico_endereco_cliente.cep AS historico_cliente_cep,
                historico_endereco_cliente.cidade AS historico_cliente_cidade,
                historico_endereco_cliente.uf AS historico_cliente_uf

              
                FROM historico_alteracoes as historico

                LEFT JOIN imovel imovel
                ON imovel.id = historico.id_imovel

                LEFT JOIN endereco imovel_endereco
                ON imovel_endereco.id = imovel.id_endereco

                LEFT JOIN condominio condominio
                    ON condominio.id = imovel.id_condominio

                LEFT JOIN anuncio anuncio
                    ON anuncio.id_imovel = imovel.id

                LEFT JOIN pessoa historico_pessoa_cliente
                    ON historico_pessoa_cliente.id = historico.id_cliente

                LEFT JOIN pessoa historico_pessoa_funcionario
                    ON historico_pessoa_funcionario.id = historico.id_funcionario

                LEFT JOIN pessoa imovel_pessoa_corretor
                    ON imovel_pessoa_corretor.id = imovel.id_corretor

                LEFT JOIN pessoa imovel_pessoa_captador
                    ON imovel_pessoa_captador.id = imovel.id_captador

                LEFT JOIN usuario imovel_usuario_corretor
                    ON imovel_usuario_corretor.id_pessoa = imovel_pessoa_corretor.id

                LEFT JOIN usuario imovel_usuario_captador
                    ON imovel_usuario_captador.id_pessoa = imovel_pessoa_captador.id

                LEFT JOIN usuario historico_usuario_cliente
                    ON historico_usuario_cliente.id_pessoa = historico_pessoa_cliente.id

                LEFT JOIN usuario historico_usuario_funcionario
                    ON historico_usuario_funcionario.id_pessoa = historico_pessoa_funcionario.id

                LEFT JOIN funcionario imovel_funcionario_corretor
                    ON imovel_funcionario_corretor.id_pessoa = imovel_pessoa_corretor.id

                LEFT JOIN funcionario imovel_funcionario_captador
                    ON imovel_funcionario_captador.id_pessoa = imovel_pessoa_captador.id

                LEFT JOIN funcionario historico_funcionario
                    ON historico_funcionario.id_pessoa = historico_pessoa_funcionario.id

                LEFT JOIN corretor imovel_corretor
                    ON imovel_corretor.id_funcionario = imovel_funcionario_corretor.id_pessoa

                LEFT JOIN corretor historico_corretor
                    ON historico_corretor.id_funcionario = historico_funcionario.id_pessoa

                LEFT JOIN endereco imovel_endereco_captador
                    ON imovel_endereco_captador.id = imovel_pessoa_captador.id_endereco

                LEFT JOIN endereco imovel_endereco_corretor
                    ON imovel_endereco_corretor.id = imovel_pessoa_corretor.id_endereco

                LEFT JOIN endereco historico_endereco_cliente
                    ON historico_endereco_cliente.id = historico_pessoa_cliente.id_endereco

                LEFT JOIN endereco historico_endereco_funcionario
                    ON historico_endereco_funcionario.id = historico_pessoa_funcionario.id_endereco
            ";


    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function montar(array $registro): ?Historico
    {
        try {
            $pessoaDAO = new PessoaDAO();
            $imovelDAO = new ImovelDAO();
            $idImovel = $registro['imovel_id'] ?? null;
            $idCaptadorImovel = $registro['imovel_captador_id'] ?? null;
            $idCorretorImovel = $registro['imovel_corretor_id'] ?? null;
            $idFuncionario = $registro['historico_funcionario_id'] ?? null;
            $idCliente = $registro['historico_cliente_id'] ?? null;

            $cliente = null;
            $imovel = null;
            $funcionario = null;
            $captadorImovel = null;
            $corretorImovel = null;

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
                    if ($idCorretorImovel) {
                        $dadosCorretor = array_filter($registro, function ($key) {
                            return strpos($key, 'imovel_corretor_') === 0;
                        }, ARRAY_FILTER_USE_KEY);
                        $dadosCorretor = array_combine(
                            array_map(function ($key) {
                                return preg_replace('/imovel_corretor_/', '', $key, 1);
                            }, array_keys($dadosCorretor)),
                            $dadosCorretor
                        );
                        try {
                            $corretorImovel = $pessoaDAO->montar($dadosCorretor);
                        } catch (Exception $e) {
                            error_log("ERRO! imovelDAO->listarDisponiveis: " . $e->getMessage());
                        }
                        if ($corretorImovel) {
                            $imovel->setCorretor($corretorImovel);
                        }
                    }

                    if ($idCaptadorImovel) {
                        $dadosCaptador = array_filter($registro, function ($key) {
                            return strpos($key, 'imovel_captador_') === 0;
                        }, ARRAY_FILTER_USE_KEY);
                        $dadosCaptador = array_combine(
                            array_map(function ($key) {
                                return str_replace('imovel_captador_', '', $key);
                            }, array_keys($dadosCaptador)),
                            $dadosCaptador
                        );
                        try {
                            $captadorImovel = $pessoaDAO->montar($dadosCaptador);
                        } catch (Exception $e) {
                            error_log("ERRO! imovelDAO->listarDisponiveis: " . $e->getMessage());
                        }

                        if ($captadorImovel) {
                            $imovel->setCaptador($captadorImovel);
                        }
                    }
                }
            }

            if ($idFuncionario) {
                $dadosFuncionario = array_filter($registro, function ($key) {
                    return strpos($key, 'historico_funcionario_') === 0;
                }, ARRAY_FILTER_USE_KEY);
                $dadosFuncionario = array_combine(
                    array_map(function ($key) {
                        return preg_replace('/historico_funcionario_/', '', $key, 1);
                    }, array_keys($dadosFuncionario)),
                    $dadosFuncionario
                );
                $funcionario = $pessoaDAO->montar($dadosFuncionario);
            }
            if ($idCliente) {
                $dadosCliente = array_filter($registro, function ($key) {
                    return strpos($key, 'historico_cliente_') === 0;
                }, ARRAY_FILTER_USE_KEY);
                $dadosCliente = array_combine(
                    array_map(function ($key) {
                        return preg_replace('/historico_cliente_/', '', $key, 1);
                    }, array_keys($dadosCliente)),
                    $dadosCliente
                );
                $cliente = $pessoaDAO->montar($dadosCliente);
            }

            $historicoObj = new Historico($registro['descricao']);
            $historicoObj->setId($registro['id']);
            $historicoObj->setFuncionario($funcionario);
            $historicoObj->setCliente($cliente);
            $historicoObj->setImovel($imovel);
            $historicoObj->setDataAlteracao($registro['data']
                ? new DateTime($registro['data'])
                : null);
            return $historicoObj;
        } catch (Exception $e) {
            error_log("historicoDAO::montar - Error: " . $e->getMessage());
            return null;
        }
    }

    public function listarPorIdImovel(int $id): array
    {
        try {
            $sql = $this->sql . " WHERE historico.id_imovel = :id";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id' => $id]);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $lista = [];

            foreach ($registros as $registro) {
                if ($registro && $registro['id'] !== null) {
                    $historicoObj = $this->montar($registro);
                    if ($historicoObj) {
                        $lista[] = $historicoObj;
                    }
                }
            }
            return $lista;
        } catch (Exception $e) {
            error_log("historicoDAO::listarPorIdImovel - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function listarPorIdCliente(int $id): array
    {
        try {
            $sql = $this->sql . " WHERE historico.id_cliente = :id";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id' => $id]);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $lista = [];

            foreach ($registros as $registro) {
                if ($registro && $registro['id'] !== null) {
                    $historicoObj = $this->montar($registro);
                    if ($historicoObj) {
                        $lista[] = $historicoObj;
                    }
                }
            }
            return $lista;
        } catch (Exception $e) {
            error_log("historicoDAO::listarPorIdCliente - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function listar(): array
    {
        try {
            $stmt = $this->bancoDados->prepare($this->sql);
            $stmt->execute();
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $lista = [];

            foreach ($registros as $registro) {
                if ($registro && $registro['id'] !== null) {
                    $historicoObj = $this->montar($registro);
                    if ($historicoObj) {
                        $lista[] = $historicoObj;
                    }
                }
            }
            return $lista;
        } catch (Exception $e) {
            error_log("historicoDAO::listar - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function cadastrar(Historico $historico): bool
    {
        try {
            $stmt = $this->bancoDados->prepare("
                INSERT INTO historico_alteracoes (id_funcionario,id_cliente, id_imovel, descricao) 
                VALUES (:id_funcionario, :id_cliente, :id_imovel, :descricao)
            ");
            $stmt->execute([
                ':id_funcionario' => $historico->getFuncionario() ? $historico->getFuncionario()->getId() : null,
                ':id_cliente' => $historico->getCliente() ? $historico->getCliente()->getId() : null,
                ':id_imovel' => $historico->getImovel() ? $historico->getImovel()->getId() : null,
                ':descricao' => $historico->getAlteracao(),
            ]);
            return true;
        } catch (Exception $e) {
            error_log("historicoDAO::cadastrar - Error: " . $e->getMessage());
            throw $e;
        }
    }
}
