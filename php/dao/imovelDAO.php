<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/anexo.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/funcionario.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../dao/anexoDAO.php';
require_once __DIR__ . '/proprietarioImovelDAO.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class ImovelDAO
{
    private Banco $bancoDados;

    private $sql = " 
            SELECT
                imovel.*,

                endereco.id AS endereco_id,
                endereco.rua AS endereco_rua,
                endereco.numero AS endereco_numero,
                endereco.complemento AS endereco_complemento,
                endereco.bairro AS endereco_bairro,
                endereco.cep AS endereco_cep,
                endereco.cidade AS endereco_cidade,
                endereco.uf AS endereco_uf,

                endereco_corretor.id AS corretor_endereco_id,
                endereco_corretor.rua AS corretor_rua,
                endereco_corretor.numero AS corretor_numero,
                endereco_corretor.complemento AS corretor_complemento,
                endereco_corretor.bairro AS corretor_bairro,
                endereco_corretor.cep AS corretor_cep,
                endereco_corretor.cidade AS corretor_cidade,
                endereco_corretor.uf AS corretor_uf,

                endereco_captador.id AS captador_endereco_id,
                endereco_captador.rua AS captador_rua,
                endereco_captador.numero AS captador_numero,
                endereco_captador.complemento AS captador_complemento,
                endereco_captador.bairro AS captador_bairro,
                endereco_captador.cep AS captador_cep,
                endereco_captador.cidade AS captador_cidade,
                endereco_captador.uf AS captador_uf,

                condominio.id AS condominio_id,
                condominio.nome AS condominio_nome,

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

                pessoa_captador.id as captador_id,
                pessoa_captador.email AS captador_email,
                pessoa_captador.nome AS captador_nome,
                pessoa_captador.cpf_cnpj AS captador_cpf_cnpj,
                pessoa_captador.rg AS captador_rg,
                pessoa_captador.id_endereco AS captador_id_endereco,
                pessoa_captador.data_nascimento AS captador_data_nascimento,
                pessoa_captador.data_cadastro AS captador_data_cadastro,
                pessoa_captador.data_modificacao AS captador_data_modificacao,
                usuario_captador.senha as captador_senha,
                usuario_captador.ultimo_login as captador_ultimo_login,
                usuario_captador.ativo AS captador_ativo,
                usuario_captador.id_pessoa AS captador_usuario_id,
                funcionario_captador.id_pessoa AS captador_funcionario_id,
                funcionario_captador.salario AS captador_salario,
                funcionario_captador.matricula AS captador_matricula,
                funcionario_captador.data_admissao AS captador_data_admissao,
                funcionario_captador.cargo AS captador_cargo,

                anuncio.descricao AS anuncio_descricao,
                anuncio.titulo AS anuncio_titulo

            FROM imovel 

            LEFT JOIN endereco 
                ON endereco.id = imovel.id_endereco

            LEFT JOIN condominio 
                ON condominio.id = imovel.id_condominio

            LEFT JOIN anuncio 
                ON anuncio.id_imovel = imovel.id

            LEFT JOIN pessoa pessoa_corretor
                ON pessoa_corretor.id = imovel.id_corretor

            LEFT JOIN pessoa pessoa_captador
                ON pessoa_captador.id = imovel.id_captador

            LEFT JOIN usuario usuario_corretor
                ON usuario_corretor.id_pessoa = pessoa_corretor.id

            LEFT JOIN usuario usuario_captador
                ON usuario_captador.id_pessoa = pessoa_captador.id

            LEFT JOIN funcionario funcionario_corretor
                ON funcionario_corretor.id_pessoa = pessoa_corretor.id

            LEFT JOIN funcionario funcionario_captador
                ON funcionario_captador.id_pessoa = pessoa_captador.id

            LEFT JOIN corretor
                ON corretor.id_funcionario = funcionario_corretor.id_pessoa

            LEFT JOIN endereco endereco_captador
                ON endereco_captador.id = pessoa_captador.id_endereco

            LEFT JOIN endereco endereco_corretor
                ON endereco_corretor.id = pessoa_corretor.id_endereco
                ";

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function getConexao()
    {
        return $this->bancoDados;
    }

    public function atualizarClicks(int $idImovel): bool
    {
        try {
            $sql = "UPDATE imovel SET quant_clicks = quant_clicks + 1 WHERE id = :id";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id' => $idImovel
            ]);
            return true;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->atualizarClicks: " . $e->getMessage());
            throw $e;
        }
    }

    public function destacarLista(array $listaIDS): bool
    {
        try {
            $sql = "UPDATE imovel SET destacado = NOT destacado WHERE id IN (" . implode(',', array_map('intval', $listaIDS)) . ")";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->destacarLista: " . $e->getMessage());
            throw $e;
        }
    }

    public function destacar(int $idImovel): bool
    {
        try {
            $sql = "UPDATE imovel SET destacado = NOT destacado WHERE id = :id";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id' => $idImovel]);
            return true;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->destacar: " . $e->getMessage());
            throw $e;
        }
    }

    public function favoritar(int $idCliente, array|int $idImoveis)
    {
        try {
            // if (empty($idImoveis)) {
            //     throw new Exception("Nenhum ID de imóvel fornecido para favoritar.");
            // }
            // if (empty($idCliente)) {
            //     throw new Exception("ID do cliente não fornecido para favoritar.");
            // }

            if (!is_array($idImoveis)) {
                $idImovel = (int) $idImoveis;
                if (!is_int($idImovel)) {
                    error_log("ID do imóvel inválido: " . json_encode($idImovel));
                    throw new Exception("ID do imóvel inválido.");
                }
                $sql = "SELECT * FROM favoritos WHERE id_cliente = :idCliente AND id_imovel = :idImovel";
                $stmt = $this->bancoDados->prepare($sql);
                $stmt->execute([
                    ':idCliente' => $idCliente,
                    ':idImovel' => $idImovel
                ]);
                $favoritoExistente = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($favoritoExistente) {
                    $sqlDelete = "DELETE FROM favoritos WHERE id_cliente = :idCliente AND id_imovel = :idImovel";
                    $stmtDelete = $this->bancoDados->prepare($sqlDelete);
                    $stmtDelete->execute([
                        ':idCliente' => $idCliente,
                        ':idImovel' => $idImovel
                    ]);
                } else {
                    $sqlInsert = "INSERT INTO favoritos (id_cliente, id_imovel) VALUES (:idCliente, :idImovel)";
                    $stmtInsert = $this->bancoDados->prepare($sqlInsert);
                    $stmtInsert->execute([
                        ':idCliente' => $idCliente,
                        ':idImovel' => $idImovel
                    ]);
                }
            } else {
                foreach ($idImoveis as $idImovel) {
                    if (!is_int($idImovel)) {
                        error_log("ID do imóvel inválido: " . json_encode($idImovel));
                        continue;
                    }
                    $sql = "SELECT * FROM favoritos WHERE id_cliente = :idCliente AND id_imovel = :idImovel";
                    $stmt = $this->bancoDados->prepare($sql);
                    $stmt->execute([
                        ':idCliente' => $idCliente,
                        ':idImovel' => $idImovel
                    ]);
                    $favoritoExistente = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($favoritoExistente) {
                        $sqlDelete = "DELETE FROM favoritos WHERE id_cliente = :idCliente AND id_imovel = :idImovel";
                        $stmtDelete = $this->bancoDados->prepare($sqlDelete);
                        $stmtDelete->execute([
                            ':idCliente' => $idCliente,
                            ':idImovel' => $idImovel
                        ]);
                    } else {
                        $sqlInsert = "INSERT INTO favoritos (id_cliente, id_imovel) VALUES (:idCliente, :idImovel)";
                        $stmtInsert = $this->bancoDados->prepare($sqlInsert);
                        $stmtInsert->execute([
                            ':idCliente' => $idCliente,
                            ':idImovel' => $idImovel
                        ]);
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->favoritar: " . $e->getMessage());
            throw $e;
        }
    }

    public function remover(int $id)
    {
        try {
            $sql = "DELETE FROM imovel WHERE id = :id";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->remover: " . $e->getMessage());
            throw $e;
        }
    }

    public  function montar(array $dados): ?Imovel
    {
        try {
            $idImovel = (int) $dados['id'];
            if (!isset($dados['id'])) {
                throw new Exception("ID do imóvel não fornecido");
            }

            $endereco = null;

            if (!isset($dados['endereco_id'])) {
                throw new Exception("ID do endereço não fornecido");
            }

            if ($dados['endereco_id']) {
                $endereco = new Endereco(
                    $dados['endereco_rua'],
                    $dados['endereco_bairro'],
                    $dados['endereco_cep'],
                    $dados['endereco_cidade'],
                    $dados['endereco_uf']
                );
                $endereco->setId((int) $dados['endereco_id']);
                $endereco->setNumero($dados['endereco_numero'] !== null ? (int) $dados['endereco_numero'] : null);
                $endereco->setComplemento($dados['endereco_complemento']);
            }

            $anuncio = new Anuncio();
            $anuncio->setIdImovel((int) $dados['id']);
            $anuncio->setDescricao($dados['anuncio_descricao'] ?? '');
            $anuncio->setTitulo($dados['anuncio_titulo'] ?? '');
            $anexoDAO = new AnexoDAO();
            $anexos = $anexoDAO->listarPorIdAnuncio($dados['id']);
            $anuncio->setImagens($anexos['Imagens'] ?? null);
            $anuncio->setVideos($anexos['Videos'] ?? null);
            $anuncio->setAnexos($anexos['Documentos'] ?? null);

            $imovelObj = new Imovel($endereco, Status::tryFrom($dados['status']), Categoria::tryFrom($dados['categoria']));

            $condominio = null;
            if ($dados['condominio_id']) {
                $copiaEndereco = $endereco ? clone $endereco : null;
                $copiaEndereco ? $copiaEndereco->setComplemento('') : null;
                $condominio = new Condominio(
                    $dados['condominio_nome'],
                    $copiaEndereco
                );
                $condominio->setId((int) $dados['condominio_id']);
            }

            $imovelObj->setId((int) $dados['id']);
            $imovelObj->setValorVenda($dados['valor_venda'] !== null ? (float) $dados['valor_venda'] : 0);
            $imovelObj->setValorAluguel($dados['valor_aluguel'] !== null ? (float) $dados['valor_aluguel'] : 0);
            $imovelObj->setQuantQuartos($dados['quant_quartos'] !== null ? (int) $dados['quant_quartos'] : 0);
            $imovelObj->setQuantSalas($dados['quant_salas'] !== null ? (int) $dados['quant_salas'] : 0);
            $imovelObj->setQuantVagas($dados['quant_vagas'] !== null ? (int) $dados['quant_vagas'] : 0);
            $imovelObj->setQuantBanheiros($dados['quant_banheiros'] !== null ? (int) $dados['quant_banheiros'] : 0);
            $imovelObj->setQuantVarandas($dados['quant_varandas'] !== null ? (int) $dados['quant_varandas'] : 0);
            $imovelObj->setQuantSuites($dados['quant_suites'] !== null ? (int) $dados['quant_suites'] : 0);
            $imovelObj->setIptu($dados['iptu'] !== null ? (float) $dados['iptu'] : 0);
            $imovelObj->setValorCondominio($dados['valor_condominio'] !== null ? (float) $dados['valor_condominio'] : 0);
            $imovelObj->setAndar($dados['andar'] !== null ? (int) $dados['andar'] : 0);
            $imovelObj->setEstado($dados['estado'] ? Estado::tryFrom($dados['estado']) : null);
            $imovelObj->setBloco($dados['bloco']);
            $imovelObj->setAnoConstrucao($dados['ano_construcao'] !== null ? (int) $dados['ano_construcao'] : 0);
            $imovelObj->setAreaTotal($dados['area_total'] !== null ? (float) $dados['area_total'] : 0);
            $imovelObj->setAreaPrivativa($dados['area_privativa'] !== null ? (float) $dados['area_privativa'] : 0);
            $imovelObj->setSituacao($dados['situacao'] ? Situacao::tryFrom($dados['situacao']) : null);
            $imovelObj->setOcupacao($dados['ocupacao'] ? Ocupacao::tryFrom($dados['ocupacao']) : null);
            $imovelObj->setDataCadastro($dados['data_cadastro']
                ? new DateTime($dados['data_cadastro'])
                : null);
            $imovelObj->setDataModificacao($dados['data_modificacao']
                ? new DateTime($dados['data_modificacao'])
                : null);
            $imovelObj->setAnuncio($anuncio);
            $imovelObj->setCondominio($condominio);
            $imovelObj->setDestacado((bool) $dados['destacado']);
            $imovelObj->setQuantClicks($dados['quant_clicks'] !== null ? (int) $dados['quant_clicks'] : 0);
            $proprietarioImovelDAO = new ProprietarioImovelDAO();
            $imovelObj->setProprietarios($proprietarioImovelDAO->listarPorIdImovel($dados['id']) ?? []);
            $filtroDAO = new FiltroDAO();
            $imovelObj->setFiltros($filtroDAO->listarPorIdImovel($dados['id']) ?? []);
            return $imovelObj;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->montar: " . $e->getMessage());
            return null;
        }
    }


    public function listarDestacados(): array
    {
        try {
            $sql = $this->sql . "WHERE imovel.destacado = 1 AND imovel.status != 'Pendente'";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                return [];
            }

            $imoveisDestacados = [];
            $pessoaDAO = new PessoaDAO();
            foreach ($resultados as $row) {
                $imovel = $this->montar($row);
                if ($imovel) {
                    $dadosCorretor = array_filter($row, function ($key) {
                        return strpos($key, 'corretor_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCorretor = array_combine(
                        array_map(function ($key) {
                            return preg_replace('/corretor_/', '', $key, 1);
                        }, array_keys($dadosCorretor)),
                        $dadosCorretor
                    );
                    $dadosCaptador = array_filter($row, function ($key) {
                        return strpos($key, 'captador_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCaptador = array_combine(
                        array_map(function ($key) {
                            return str_replace('captador_', '', $key);
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
                    $imoveisDestacados[] = $imovel;
                }
            }

            return $imoveisDestacados;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->listarDestacados: " . $e->getMessage());
            throw $e;
        }
    }


    public function listar(): array
    {

        try {
            $stmt = $this->bancoDados->prepare($this->sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                return [];
            }

            $lista = [];
            $pessoaDAO = new PessoaDAO();

            foreach ($resultados as $dados) {
                $imovel = $this->montar($dados);
                if ($imovel) {
                    $dadosCorretor = array_filter($dados, function ($key) {
                        return strpos($key, 'corretor_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCorretor = array_combine(
                        array_map(function ($key) {
                            return preg_replace('/corretor_/', '', $key, 1);
                        }, array_keys($dadosCorretor)),
                        $dadosCorretor
                    );
                    $dadosCaptador = array_filter($dados, function ($key) {
                        return strpos($key, 'captador_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCaptador = array_combine(
                        array_map(function ($key) {
                            return str_replace('captador_', '', $key);
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
                    $lista[] = $imovel;
                }
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->listar: " . $e->getMessage());
            throw $e;
        }
    }

    public function listarDisponiveis(): array
    {

        try {

            $sql = $this->sql . "WHERE imovel.status IN ('Venda', 'Aluguel', 'Venda e Aluguel')";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                return [];
            }

            $lista = [];
            $pessoaDAO = new PessoaDAO();
            foreach ($resultados as $dados) {
                $imovel = $this->montar($dados);
                if ($imovel) {
                    $dadosCorretor = array_filter($dados, function ($key) {
                        return strpos($key, 'corretor_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCorretor = array_combine(
                        array_map(function ($key) {
                            return preg_replace('/corretor_/', '', $key, 1);
                        }, array_keys($dadosCorretor)),
                        $dadosCorretor
                    );
                    $dadosCaptador = array_filter($dados, function ($key) {
                        return strpos($key, 'captador_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCaptador = array_combine(
                        array_map(function ($key) {
                            return str_replace('captador_', '', $key);
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

                    $lista[] = $imovel;
                }
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->listarDisponiveis: " . $e->getMessage());
            throw $e;
        }
    }
    public  function buscarPorId(int $idImovel): ?Imovel
    {
        try {
            $sql = $this->sql . "WHERE imovel.id = :id";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id' => $idImovel]);

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) {
                throw new Exception("Imóvel não encontrado");
            }
            $pessoaDAO = new PessoaDAO();
            $dadosCorretor = array_filter($dados, function ($key) {
                return strpos($key, 'corretor_') === 0;
            }, ARRAY_FILTER_USE_KEY);
            $dadosCorretor = array_combine(
                array_map(function ($key) {
                    return preg_replace('/corretor_/', '', $key, 1);
                }, array_keys($dadosCorretor)),
                $dadosCorretor
            );
            $dadosCaptador = array_filter($dados, function ($key) {
                return strpos($key, 'captador_') === 0;
            }, ARRAY_FILTER_USE_KEY);
            $dadosCaptador = array_combine(
                array_map(function ($key) {
                    return str_replace('captador_', '', $key);
                }, array_keys($dadosCaptador)),
                $dadosCaptador
            );
            $corretor = null;
            $captador = null;
            try {
                if (isset($dadosCorretor['id']) && $dadosCorretor['id'] !== null) {
                    $corretor = $pessoaDAO->montar($dadosCorretor);
                }
            } catch (Exception $e) {
                error_log("ERRO! imovelDAO->listarDisponiveis: " . $e->getMessage());
            }
            try {
                if (isset($dadosCaptador['id']) && $dadosCaptador['id'] !== null) {
                    $captador = $pessoaDAO->montar($dadosCaptador);
                }
            } catch (Exception $e) {
                error_log("ERRO! imovelDAO->listarDisponiveis: " . $e->getMessage());
            }
            $imovel = $this->montar($dados);

            if ($corretor) {
                $imovel->setCorretor($corretor);
            }
            if ($captador) {
                $imovel->setCaptador($captador);
            }

            return $imovel;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->buscarPorId: " . $e->getMessage());
            throw $e;
        }
    }


    public function listarFavoritos(int $idCliente): array
    {
        try {
            $sql = str_replace("FROM imovel", "FROM favoritos LEFT JOIN imovel ON favoritos.id_imovel = imovel.id", $this->sql) .  "WHERE id_cliente = :id";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id' => $idCliente]);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                return [];
            }

            $lista = [];
            $pessoaDAO = new PessoaDAO();

            foreach ($dados as $registro) {
                $imovel = $this->montar($registro);
                if ($imovel) {
                    $dadosCorretor = array_filter($dados, function ($key) {
                        return strpos($key, 'corretor_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCorretor = array_combine(
                        array_map(function ($key) {
                            return preg_replace('/corretor_/', '', $key, 1);
                        }, array_keys($dadosCorretor)),
                        $dadosCorretor
                    );
                    $dadosCaptador = array_filter($dados, function ($key) {
                        return strpos($key, 'captador_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $dadosCaptador = array_combine(
                        array_map(function ($key) {
                            return str_replace('captador_', '', $key);
                        }, array_keys($dadosCaptador)),
                        $dadosCaptador
                    );
                    $corretor = null;
                    $captador = null;
                    try {
                        if (isset($dadosCorretor['id']) && $dadosCorretor['id'] !== null) {
                            $corretor = $pessoaDAO->montar($dadosCorretor);
                        }
                    } catch (Exception $e) {
                        error_log("ERRO! imovelDAO->listarDisponiveis: " . $e->getMessage());
                    }
                    try {
                        if (isset($dadosCaptador['id']) && $dadosCaptador['id'] !== null) {
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
                    $lista[] = $imovel;
                }
            }
            return $lista;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->listarFavoritos: " . $e->getMessage());
            throw $e;
        }
    }


    public  function atualizar(Imovel $imovel): bool
    {

        try {
            $sql = "
            UPDATE imovel SET
                valor_venda = :valor_venda,
                valor_aluguel = :valor_aluguel,
                quant_quartos = :quartos,
                quant_salas = :salas,
                quant_vagas = :vagas,
                quant_banheiros = :banheiros,
                quant_varandas = :varandas,
                quant_suites = :quant_suites,
                categoria = :categoria,
                id_endereco = :endereco,
                status = :status,
                iptu = :iptu,
                valor_condominio = :condominio_valor,
                andar = :andar,
                estado = :estado,
                bloco = :bloco,
                ano_construcao = :ano,
                area_total = :area_total,
                area_privativa = :area_privativa,
                situacao = :situacao,
                ocupacao = :ocupacao,
                id_corretor = :corretor,
                id_captador = :captador,
                data_cadastro = :data_cadastro,
                data_modificacao = :data_modificacao,
                id_condominio = :condominio,
                destacado = :destacado,
                quant_clicks = :quant_clicks
            WHERE id = :id
        ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':valor_venda' => $imovel->getValorVenda(),
                ':valor_aluguel' => $imovel->getValorAluguel(),
                ':quartos' => $imovel->getQuantQuartos(),
                ':salas' => $imovel->getQuantSalas(),
                ':vagas' => $imovel->getQuantVagas(),
                ':banheiros' => $imovel->getQuantBanheiros(),
                ':varandas' => $imovel->getQuantVarandas(),
                ':quant_suites' => $imovel->getQuantSuites(),
                ':categoria' => $imovel->getCategoria() ? $imovel->getCategoria()->value : null,
                ':endereco' => $imovel->getEndereco() ? $imovel->getEndereco()->getId() : null,
                ':status' => $imovel->getStatus() ? $imovel->getStatus()->value : null,
                ':iptu' => $imovel->getIptu(),
                ':condominio_valor' => $imovel->getValorCondominio(),
                ':andar' => $imovel->getAndar(),
                ':estado' => $imovel->getEstado() ? $imovel->getEstado()->value : null,
                ':bloco' => $imovel->getBloco(),
                ':ano' => $imovel->getAnoConstrucao(),
                ':area_total' => $imovel->getAreaTotal(),
                ':area_privativa' => $imovel->getAreaPrivativa(),
                ':situacao' => $imovel->getSituacao() ? $imovel->getSituacao()->value : null,
                ':ocupacao' => $imovel->getOcupacao() ? $imovel->getOcupacao()->value : null,
                ':corretor' => $imovel->getCorretor() ? $imovel->getCorretor()->getId() : null,
                ':captador' => $imovel->getCaptador() ? $imovel->getCaptador()->getId() : null,
                ':data_cadastro' => $imovel->getDataCadastro() ? $imovel->getDataCadastro()->format("Y-m-d") : null,
                ':data_modificacao' => $imovel->getDataModificacao() ? $imovel->getDataModificacao()->format("Y-m-d") : null,
                ':condominio' => $imovel->getCondominio() ? $imovel->getCondominio()->getId() : null,
                ':destacado' => $imovel->isDestacado() ? 1 : 0,
                ':quant_clicks' => $imovel->getQuantClicks(),
                ':id' => $imovel->getId()
            ]);
            return true;
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->atualizar: " . $e->getMessage());
            throw $e;
        }
    }


    public  function cadastrar(Imovel $imovel): int
    {
        try {
            $sql = "
            INSERT INTO imovel (
                valor_venda, valor_aluguel, quant_quartos, quant_salas, quant_vagas,
                quant_banheiros, quant_varandas, quant_suites, categoria, id_endereco, status,
                iptu, valor_condominio, andar, estado, bloco, ano_construcao,
                area_total, area_privativa, situacao, ocupacao,
                id_corretor, id_captador,
                data_cadastro, data_modificacao, id_condominio, destacado, quant_clicks
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";


            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                $imovel->getValorVenda(),
                $imovel->getValorAluguel(),
                $imovel->getQuantQuartos(),
                $imovel->getQuantSalas(),
                $imovel->getQuantVagas(),
                $imovel->getQuantBanheiros(),
                $imovel->getQuantVarandas(),
                $imovel->getQuantSuites(),
                $imovel->getCategoria() ? $imovel->getCategoria()->value : null,
                $imovel->getEndereco() ? $imovel->getEndereco()->getId() : null,
                $imovel->getStatus() ? $imovel->getStatus()->value : null,
                $imovel->getIptu(),
                $imovel->getValorCondominio(),
                $imovel->getAndar(),
                $imovel->getEstado() ? $imovel->getEstado()->value : null,
                $imovel->getBloco(),
                $imovel->getAnoConstrucao(),
                $imovel->getAreaTotal(),
                $imovel->getAreaPrivativa(),
                $imovel->getSituacao() ? $imovel->getSituacao()->value : null,
                $imovel->getOcupacao() ? $imovel->getOcupacao()->value : null,
                $imovel->getCorretor() ? $imovel->getCorretor()->getId() : null,
                $imovel->getCaptador() ? $imovel->getCaptador()->getId() : null,
                $imovel->getDataCadastro() ? $imovel->getDataCadastro()->format("Y-m-d H:i:s") : date("Y-m-d H:i:s"),
                $imovel->getDataModificacao() ? $imovel->getDataModificacao()->format("Y-m-d H:i:s") : null,
                $imovel->getCondominio() ? $imovel->getCondominio()->getId() : null,
                $imovel->isDestacado() ? 1 : 0,
                $imovel->getQuantClicks()
            ]);

            return $this->bancoDados->lastInsertId();
        } catch (Exception $e) {
            error_log("ERRO! imovelDAO->cadastrar: " . $e->getMessage());
            throw $e;
        }
    }
}
