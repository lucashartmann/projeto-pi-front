<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/imovelDAO.php';
require_once __DIR__ . '/pessoaDAO.php';
require_once __DIR__ . '/../model/visita.php';
require_once __DIR__ . '/../model/funcionario.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class VisitaDAO
{
    private Banco $bancoDados;

    private String $sql = "
            SELECT
                visita.id,
                visita.id_cliente,
                visita.id_imovel,
                visita.id_corretor,
                visita.data,
                visita.nome,

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

                endereco_corretor.id AS corretor_endereco_id,
                endereco_corretor.rua AS corretor_rua,
                endereco_corretor.numero AS corretor_numero,
                endereco_corretor.complemento AS corretor_complemento,
                endereco_corretor.bairro AS corretor_bairro,
                endereco_corretor.cep AS corretor_cep,
                endereco_corretor.cidade AS corretor_cidade,
                endereco_corretor.uf AS corretor_uf,

                endereco_cliente.id AS cliente_endereco_id,
                endereco_cliente.rua AS cliente_rua,
                endereco_cliente.numero AS cliente_numero,
                endereco_cliente.complemento AS cliente_complemento,
                endereco_cliente.bairro AS cliente_bairro,
                endereco_cliente.cep AS cliente_cep,
                endereco_cliente.cidade AS cliente_cidade,
                endereco_cliente.uf AS cliente_uf,

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

                pessoa_corretor.id as corretor_id,
                pessoa_corretor.email AS corretor_email,
                pessoa_corretor.nome AS corretor_nome,
                pessoa_corretor.cpf_cnpj AS corretor_cpf_cnpj,
                pessoa_corretor.rg AS corretor_rg,
                pessoa_corretor.id_endereco AS corretor_id_endereco,
                pessoa_corretor.data_nascimento AS corretor_data_nascimento,
                pessoa_corretor.data_cadastro AS corretor_data_cadastro,
                pessoa_corretor.data_modificacao AS corretor_data_modificacao,
                usuario_corretor.senha as corretor_senha,
                usuario_corretor.ultimo_login as corretor_ultimo_login,
                usuario_corretor.ativo AS corretor_ativo,
                usuario_corretor.id_pessoa AS corretor_usuario_id,
                funcionario_corretor.id_pessoa AS corretor_funcionario_id,
                funcionario_corretor.salario AS corretor_salario,
                funcionario_corretor.matricula AS corretor_matricula,
                funcionario_corretor.data_admissao AS corretor_data_admissao,
                funcionario_corretor.cargo AS corretor_cargo,
                corretor.creci as corretor_creci,
                corretor.id_funcionario AS corretor_corretor_id,

                pessoa_cliente.id as cliente_id,
                cliente.id_pessoa as cliente_cliente_id,
                pessoa_cliente.email AS cliente_email,
                pessoa_cliente.nome AS cliente_nome,
                pessoa_cliente.cpf_cnpj AS cliente_cpf_cnpj,
                pessoa_cliente.rg AS cliente_rg,
                pessoa_cliente.id_endereco AS cliente_id_endereco,
                pessoa_cliente.data_nascimento AS cliente_data_nascimento,
                pessoa_cliente.data_cadastro AS cliente_data_cadastro,
                pessoa_cliente.data_modificacao AS cliente_data_modificacao,
                usuario_cliente.senha as cliente_senha,
                usuario_cliente.ultimo_login as cliente_ultimo_login,
                usuario_cliente.ativo AS cliente_ativo,
                usuario_cliente.id_pessoa AS cliente_usuario_id,
                

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

                anuncio.id_imovel AS imovel_anuncio_id,
                anuncio.descricao AS imovel_anuncio_descricao,
                anuncio.titulo AS imovel_anuncio_titulo

                FROM visita 

                LEFT JOIN imovel
                ON imovel.id = visita.id_imovel

                LEFT JOIN endereco imovel_endereco
                ON imovel_endereco.id = imovel.id_endereco

            LEFT JOIN condominio 
                ON condominio.id = imovel.id_condominio

            LEFT JOIN anuncio 
                ON anuncio.id_imovel = imovel.id

            LEFT JOIN pessoa pessoa_corretor
                ON pessoa_corretor.id = visita.id_corretor

            LEFT JOIN pessoa pessoa_cliente
                ON pessoa_cliente.id = visita.id_cliente

            LEFT JOIN pessoa imovel_pessoa_corretor
                ON imovel_pessoa_corretor.id = imovel.id_corretor

            LEFT JOIN pessoa imovel_pessoa_captador
                ON imovel_pessoa_captador.id = imovel.id_captador

            LEFT JOIN usuario imovel_usuario_corretor
                ON imovel_usuario_corretor.id_pessoa = imovel_pessoa_corretor.id

            LEFT JOIN usuario imovel_usuario_captador
                ON imovel_usuario_captador.id_pessoa = imovel_pessoa_captador.id

            LEFT JOIN usuario usuario_corretor
                ON usuario_corretor.id_pessoa = pessoa_corretor.id

            LEFT JOIN usuario usuario_cliente
                ON usuario_cliente.id_pessoa = pessoa_cliente.id

            LEFT JOIN funcionario imovel_funcionario_corretor
                ON imovel_funcionario_corretor.id_pessoa = imovel_pessoa_corretor.id

            LEFT JOIN funcionario imovel_funcionario_captador
                ON imovel_funcionario_captador.id_pessoa = imovel_pessoa_captador.id

            LEFT JOIN funcionario funcionario_corretor
                ON funcionario_corretor.id_pessoa = pessoa_corretor.id

            LEFT JOIN corretor imovel_corretor
                ON imovel_corretor.id_funcionario = imovel_funcionario_corretor.id_pessoa

            LEFT JOIN cliente cliente
                ON cliente.id_pessoa = pessoa_cliente.id

            LEFT JOIN corretor corretor
                ON corretor.id_funcionario = funcionario_corretor.id_pessoa

            LEFT JOIN endereco imovel_endereco_captador
                ON imovel_endereco_captador.id = imovel_pessoa_captador.id_endereco

            LEFT JOIN endereco imovel_endereco_corretor
                ON imovel_endereco_corretor.id = imovel_pessoa_corretor.id_endereco

            LEFT JOIN endereco endereco_corretor
                ON endereco_corretor.id = pessoa_corretor.id_endereco

            LEFT JOIN endereco endereco_cliente
                ON endereco_cliente.id = pessoa_cliente.id_endereco
            ";

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }


    public function listarPorCorretor(Funcionario $corretor): array
    {
        try {

            $lista = [];
            $sql = $this->sql . " WHERE visita.id_corretor = :id_corretor";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id_corretor' => $corretor->getId()]);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$registros) {
                return $lista;
            }

            $pessoaDAO = new PessoaDAO();
            $imovelDAO = new ImovelDAO();

            foreach ($registros as $registro) {
                $id = $registro['id'] ?? null;
                $idImovel = $registro['id_imovel'] ?? null;
                $idCliente = $registro['id_cliente'] ?? null;
                $idCorretor = $registro['id_corretor'] ?? null;
                $data = array_key_exists('data', $registro) && $registro['data'] ? new DateTime($registro['data']) : null;
                $relatorio = $registro['relatorio'] ?? null;
                $nome = $registro['nome'] ?? null;
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
                        return strpos($key, 'corretor_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCorretor = array_combine(
                        array_map(function ($key) {
                            return preg_replace('/corretor_/', '', $key, 1);
                        }, array_keys($dadosCorretor)),
                        $dadosCorretor
                    );
                    $corretor = $pessoaDAO->montar($dadosCorretor);
                }
                if ($idCliente) {
                    $dadosCliente = array_filter($registro, function ($key) {
                        return strpos($key, 'cliente_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCliente = array_combine(
                        array_map(function ($key) {
                            return preg_replace('/cliente_/', '', $key, 1);
                        }, array_keys($dadosCliente)),
                        $dadosCliente
                    );
                    $cliente = $pessoaDAO->montar($dadosCliente);
                }
                $visita = new Visita($cliente, $imovel, $corretor, $data, $nome);
                $visita->setId($id);

                $lista[] = $visita;
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO! visitaDAO->listarPorCorretor: " . $e->getMessage());
            throw new Exception("Erro ao listar visitas por corrretor: " . $e->getMessage());
        }
    }

    public function remover(int $id): bool
    {
        try {
            return $this->bancoDados->exec("DELETE FROM visita WHERE id = $id");
        } catch (Exception $e) {
            error_log("ERRO! visitaDAO->remover: " . $e->getMessage());
            throw new Exception("Erro ao remover visita: " . $e->getMessage());
        }
    }

    public function cadastrar(Visita $visita): bool
    {
        try {
            $sql = "
            INSERT INTO visita (id_cliente, id_imovel, id_corretor, data, nome) 
            VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                $visita->getCliente() ? $visita->getCliente()->getId() : null,
                $visita->getImovel() ? $visita->getImovel()->getId() : null,
                $visita->getCorretor() ? $visita->getCorretor()->getId() : null,
                $visita->getData() ? $visita->getData()->format("Y-m-d H:i:s") : null,
                $visita->getNome() ? $visita->getNome() : null
            ]);
            return true;
        } catch (Exception $e) {
            error_log("ERRO! visitaDAO->cadastrar: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar visita: " . $e->getMessage());
        }
    }
}
