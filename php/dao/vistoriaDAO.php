<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/imovelDAO.php';
require_once __DIR__ . '/../model/vistoria.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class VistoriaDAO
{
    private Banco $bancoDados;

    private String $sql = "
            SELECT
                vistoria.id,
                vistoria.id_imovel,
                vistoria.id_vistoriador,
                vistoria.data,
                vistoria.relatorio,
                vistoria.nome,

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

                endereco_vistoriador.id AS vistoriador_endereco_id,
                endereco_vistoriador.rua AS vistoriador_rua,
                endereco_vistoriador.numero AS vistoriador_numero,
                endereco_vistoriador.complemento AS vistoriador_complemento,
                endereco_vistoriador.bairro AS vistoriador_bairro,
                endereco_vistoriador.cep AS vistoriador_cep,
                endereco_vistoriador.cidade AS vistoriador_cidade,
                endereco_vistoriador.uf AS vistoriador_uf,

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

                pessoa_vistoriador.id as vistoriador_id,
                pessoa_vistoriador.email AS vistoriador_email,
                pessoa_vistoriador.nome AS vistoriador_nome,
                pessoa_vistoriador.cpf_cnpj AS vistoriador_cpf_cnpj,
                pessoa_vistoriador.rg AS vistoriador_rg,
                pessoa_vistoriador.id_endereco AS vistoriador_id_endereco,
                pessoa_vistoriador.data_nascimento AS vistoriador_data_nascimento,
                pessoa_vistoriador.data_cadastro AS vistoriador_data_cadastro,
                pessoa_vistoriador.data_modificacao AS vistoriador_data_modificacao,
                usuario_vistoriador.senha as vistoriador_senha,
                usuario_vistoriador.ultimo_login as vistoriador_ultimo_login,
                usuario_vistoriador.ativo AS vistoriador_ativo,
                usuario_vistoriador.id_pessoa AS vistoriador_usuario_id,
                funcionario_vistoriador.id_pessoa AS vistoriador_funcionario_id,
                funcionario_vistoriador.salario AS vistoriador_salario,
                funcionario_vistoriador.matricula AS vistoriador_matricula,
                funcionario_vistoriador.data_admissao AS vistoriador_data_admissao,
                funcionario_vistoriador.cargo AS vistoriador_cargo,

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

                FROM vistoria 

                LEFT JOIN imovel
                ON imovel.id = vistoria.id_imovel

                LEFT JOIN endereco imovel_endereco
                ON imovel_endereco.id = imovel.id_endereco

            LEFT JOIN condominio 
                ON condominio.id = imovel.id_condominio

            LEFT JOIN anuncio 
                ON anuncio.id_imovel = imovel.id

            LEFT JOIN pessoa pessoa_vistoriador
                ON pessoa_vistoriador.id = vistoria.id_vistoriador

            LEFT JOIN pessoa imovel_pessoa_corretor
                ON imovel_pessoa_corretor.id = imovel.id_corretor

            LEFT JOIN pessoa imovel_pessoa_captador
                ON imovel_pessoa_captador.id = imovel.id_captador

            LEFT JOIN usuario imovel_usuario_corretor
                ON imovel_usuario_corretor.id_pessoa = imovel_pessoa_corretor.id

            LEFT JOIN usuario imovel_usuario_captador
                ON imovel_usuario_captador.id_pessoa = imovel_pessoa_captador.id

            LEFT JOIN usuario usuario_vistoriador
                ON usuario_vistoriador.id_pessoa = pessoa_vistoriador.id

            LEFT JOIN funcionario imovel_funcionario_corretor
                ON imovel_funcionario_corretor.id_pessoa = imovel_pessoa_corretor.id

            LEFT JOIN funcionario imovel_funcionario_captador
                ON imovel_funcionario_captador.id_pessoa = imovel_pessoa_captador.id

            LEFT JOIN funcionario funcionario_vistoriador
                ON funcionario_vistoriador.id_pessoa = pessoa_vistoriador.id

            LEFT JOIN corretor imovel_corretor
                ON imovel_corretor.id_funcionario = imovel_funcionario_corretor.id_pessoa

            LEFT JOIN endereco imovel_endereco_captador
                ON imovel_endereco_captador.id = imovel_pessoa_captador.id_endereco

            LEFT JOIN endereco imovel_endereco_corretor
                ON imovel_endereco_corretor.id = imovel_pessoa_corretor.id_endereco

            LEFT JOIN endereco endereco_vistoriador
                ON endereco_vistoriador.id = pessoa_vistoriador.id_endereco

            ";

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function remover(int $id): bool
    {
        try {
            return $this->bancoDados->exec("DELETE FROM vistoria WHERE id = $id");
        } catch (Exception $e) {
            error_log("ERRO! vistoriaDAO->remover: " . $e->getMessage());
            throw new Exception("Erro ao remover vistoria: " . $e->getMessage());
        }
    }

    public function listarPorVistoriador(Funcionario $vistoriador)
    {
        try {

            $lista = [];
            $sql = $this->sql . " WHERE id_vistoriador = :id_vistoriador";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id_vistoriador' => $vistoriador->getId()]);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$registros) {
                return $lista;
            }

            $pessoaDAO = new PessoaDAO();
            $imovelDAO = new ImovelDAO();

            foreach ($registros as $registro) {
                $id = $registro['id'] ?? null;
                $idImovel = $registro['id_imovel'] ?? null;
                $idVistoriador = $registro['id_vistoriador'] ?? null;
                $data = $registro['data'] ? new DateTime($registro['data']) : null;
                $relatorio = $registro['relatorio'] ?? null;
                $nome = $registro['nome'] ?? null;
                $imovel = null;
                $vistoriador = null;
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
                $vistoriador = null;

                if ($idVistoriador) {
                    $dadosVistoriador = array_filter($registro, function ($key) {
                        return strpos($key, 'vistoriador_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosVistoriador = array_combine(
                        array_map(function ($key) {
                            return preg_replace('/vistoriador_/', '', $key, 1);
                        }, array_keys($dadosVistoriador)),
                        $dadosVistoriador
                    );
                    $vistoriador = $pessoaDAO->montar($dadosVistoriador);
                }
                $vistoria = new Vistoria($vistoriador, $imovel, $data, $relatorio, $nome);
                $vistoria->setId($id);

                $lista[] = $vistoria;
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO! vistoriaDAO->listarPorVistoriador: " . $e->getMessage());
            throw new Exception("Erro ao listar vistorias por vistoriador: " . $e->getMessage());
        }
    }

    public function cadastrar(Vistoria $vistoria): bool
    {
        try {
            $sql = "
            INSERT INTO vistoria (id_imovel, id_vistoriador, data, relatorio, nome) 
            VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                $vistoria->getImovel() ? $vistoria->getImovel()->getId() : null,
                $vistoria->getVistoriador() ? $vistoria->getVistoriador()->getId() : null,
                $vistoria->getData() ? $vistoria->getData()->format("Y-m-d H:i:s") : null,
                $vistoria->getRelatorio() ? $vistoria->getRelatorio() : null,
                $vistoria->getNome() ? $vistoria->getNome() : null
            ]);
            return true;
        } catch (Exception $e) {
            error_log("ERRO! vistoriaDAO->cadastrar: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar vistoria: " . $e->getMessage());
        }
    }
}
