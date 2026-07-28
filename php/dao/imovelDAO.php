<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/anexo.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/captador.php';
require_once __DIR__ . '/../model/corretor.php';

class ImovelDAO
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

    public function destacar($idImovel)
    {
        try {
            $sql = "UPDATE imovel SET destacado = NOT destacado WHERE id = :id";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id' => $idImovel]);
            return true;
        } catch (Exception $e) {
            error_log("ERRO! Banco->destacar: " . $e->getMessage());
            return false;
        }
    }

    public function listarDestacados()
    {
        try {
            $sql = "SELECT * FROM imovel d WHERE d.destacado = 1 AND d.tipo != 'Pendente'";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                throw new Exception("Não há imóveis destacados");
            }

            $imoveisDestacados = [];
            foreach ($resultados as $row) {
                $idImovel = (int) $row['id_imovel'];
                $imovel = $this->montar($row, $idImovel);
                if ($imovel) {
                    $imoveisDestacados[] = $imovel;
                }
            }

            return $imoveisDestacados;
        } catch (Exception $e) {
            error_log("ERRO! Banco->listarDestacados: " . $e->getMessage());
            return [];
        }
    }

    public function montar($dados, $idImovel)
    {
        try {

            $endereco = null;
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

            $corretor = null;
            if ($dados['corretor_id']) {
                $corretor = new Corretor(
                    $dados['corretor_username'],
                    $dados['corretor_senha'],
                    $dados['corretor_email'],
                    $dados['corretor_nome'],
                    $dados['corretor_cpf_cnpj'],
                    (string) ($dados['corretor_creci'] ?? '')
                );
                $corretor->setId((int) $dados['corretor_id']);
                $corretor->setRg($dados['corretor_rg'] ?? '');
            }

            $captador = null;
            if ($dados['captador_id']) {
                $captador = new Captador(
                    $dados['captador_username'],
                    $dados['captador_senha'],
                    $dados['captador_email'],
                    $dados['captador_nome'],
                    $dados['captador_cpf_cnpj']
                );
                $captador->setId((int) $dados['captador_id']);
                $captador->setRg($dados['captador_rg'] ?? '');
                if ($dados['captador_salario'] !== null) {
                    $captador->setSalario((float) $dados['captador_salario']);
                }
            }

            $anuncio = null;
            if ($dados['anuncio_id']) {
                $anuncio = new Anuncio();
                $anuncio->setId((int) $dados['anuncio_id']);
                $anuncio->setDescricao($dados['anuncio_descricao'] ?? '');
                $anuncio->setTitulo($dados['anuncio_titulo'] ?? '');

                $stmtAnexos = $this->bancoDados->prepare("
                    SELECT id, id_anuncio, nome_arquivo, tipo 
                    FROM midia_anuncio
                    WHERE id_anuncio = :id_anuncio
                ");
                $idAnuncio = (int) $dados['anuncio_id'];
                $stmtAnexos->execute([':id_anuncio' => $idAnuncio]);
                $imagens = [];
                $videos = [];
                $documentos = [];

                foreach ($stmtAnexos->fetchAll(PDO::FETCH_ASSOC) as $anexo) {
                    if ($anexo['tipo'] === null) {
                        continue;
                    }

                    $tipoNormalizado = strtolower($anexo['tipo']);

                    if ($tipoNormalizado === "imagem") {
                        $anexo_obj = new Anexo($idAnuncio, $anexo['nome_arquivo'], TipoAnexo::IMAGEM);
                        $imagens[] = $anexo_obj;
                    } else if ($tipoNormalizado === "anexo") {
                        $anexo_obj = new Anexo($idAnuncio, $anexo['nome_arquivo'], TipoAnexo::DOCUMENTO);
                        $documentos[] = $anexo_obj;
                    } else if ($tipoNormalizado === "video") {
                        $anexo_obj = new Anexo($idAnuncio, $anexo['nome_arquivo'], TipoAnexo::VIDEO);
                        $videos[] = $anexo_obj;
                    }
                }

                $anuncio->setImagens($imagens);
                $anuncio->setVideos($videos);
                $anuncio->setAnexos($documentos);
            }

            $condominio = null;
            if ($dados['condominio_id']) {
                $enderecoCondominio = null;
                if ($dados['condominio_endereco_id']) {
                    $enderecoCondominio = new Endereco(
                        $dados['condominio_endereco_rua'],
                        $dados['condominio_endereco_bairro'],
                        $dados['condominio_endereco_cep'],
                        $dados['condominio_endereco_cidade'],
                        $dados['condominio_endereco_uf']
                    );
                    $enderecoCondominio->setId((int) $dados['condominio_endereco_id']);
                    $enderecoCondominio->setNumero($dados['condominio_endereco_numero'] !== null ? (int) $dados['condominio_endereco_numero'] : null);
                    $enderecoCondominio->setComplemento($dados['condominio_endereco_complemento']);
                }

                $condominio = new Condominio(
                    $dados['condominio_nome'],
                    $enderecoCondominio
                );
                $condominio->setId((int) $dados['condominio_id']);
            }

            $dataCadastro = $dados['data_cadastro']
                ? new DateTime($dados['data_cadastro'])
                : null;

            $dataModificacao = $dados['data_modificacao']
                ? new DateTime($dados['data_modificacao'])
                : null;

            $imovelObj = new Imovel($endereco, Status::tryFrom($dados['status']), Categoria::tryFrom($dados['categoria']));

            $imovelObj->setId((int) $dados['id']);
            $imovelObj->setValorVenda($dados['valor_venda'] !== null ? (float) $dados['valor_venda'] : 0);
            $imovelObj->setValorAluguel($dados['valor_aluguel'] !== null ? (float) $dados['valor_aluguel'] : 0);
            $imovelObj->setQuantQuartos($dados['quant_quartos'] !== null ? (int) $dados['quant_quartos'] : 0);
            $imovelObj->setQuantSalas($dados['quant_salas'] !== null ? (int) $dados['quant_salas'] : 0);
            $imovelObj->setQuantVagas($dados['quant_vagas'] !== null ? (int) $dados['quant_vagas'] : 0);
            $imovelObj->setQuantBanheiros($dados['quant_banheiros'] !== null ? (int) $dados['quant_banheiros'] : 0);
            $imovelObj->setQuantVarandas($dados['quant_varandas'] !== null ? (int) $dados['quant_varandas'] : 0);
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
            $imovelObj->setCorretor($corretor);
            $imovelObj->setCaptador($captador);
            $imovelObj->setDataCadastro($dataCadastro);
            $imovelObj->setDataModificacao($dataModificacao);
            $imovelObj->setAnuncio($anuncio);
            $imovelObj->setCondominio($condominio);

            $stmt = $this->bancoDados->prepare("
                SELECT
                    p.id,
                    p.email,
                    p.nome,
                    p.cpf_cnpj,
                    p.rg,
                    p.id_endereco,
                    p.data_nascimento,
                    e.rua AS endereco_rua,
                    e.numero AS endereco_numero,
                    e.complemento AS endereco_complemento,
                    e.bairro AS endereco_bairro,
                    e.cep AS endereco_cep,
                    e.cidade AS endereco_cidade,
                    e.uf AS endereco_uf
                FROM proprietario p
                INNER JOIN proprietario_imovel pi
                    ON pi.id_proprietario = p.id
                LEFT JOIN endereco e
                    ON e.id = p.id_endereco
                WHERE pi.id_imovel = :id
            ");
            $stmt->execute([':id' => $idImovel]);

            $proprietarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dataNascimento = $row['data_nascimento']
                    ? DateTime::createFromFormat('Y-m-d', $row['data_nascimento'])
                    : null;

                $prop = new Proprietario(
                    $row['email'],
                    $row['nome'],
                    $row['cpf_cnpj']
                );

                $prop->setId((int) $row['id']);
                $prop->setRg($row['rg']);
                $prop->setDataNascimento($dataNascimento);

                if (!empty($row['id_endereco'])) {
                    $enderecoProprietario = new Endereco(
                        $row['endereco_rua'],
                        $row['endereco_bairro'],
                        $row['endereco_cep'],
                        $row['endereco_cidade'],
                        $row['endereco_uf']
                    );
                    $enderecoProprietario->setId((int) $row['id_endereco']);
                    $enderecoProprietario->setNumero($row['endereco_numero'] !== null ? (int) $row['endereco_numero'] : null);
                    $enderecoProprietario->setComplemento($row['endereco_complemento']);
                    $prop->setEndereco($enderecoProprietario);
                }

                $proprietarios[] = $prop;
            }
            $imovelObj->setProprietarios($proprietarios);

            $stmt = $this->bancoDados->prepare("
                SELECT fi.nome
                FROM imovel_filtros ifi
                JOIN filtros_imovel fi
                    ON fi.id = ifi.id_filtros_imovel
                WHERE ifi.id_imovel = :id
            ");
            $stmt->execute([':id' => $idImovel]);

            $filtros = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $filtros[] = $row['nome'];
            }
            $imovelObj->setFiltros($filtros);

            return $imovelObj;
        } catch (Exception $e) {
            error_log("ERRO! Banco-> montar: " . $e->getMessage());
            return null;
        }
    }

    public function listar()
    {

        try {

            $sql = "
            SELECT
                i.*,

                e.id AS endereco_id,
                e.rua AS endereco_rua,
                e.numero AS endereco_numero,
                e.complemento AS endereco_complemento,
                e.bairro AS endereco_bairro,
                e.cep AS endereco_cep,
                e.cidade AS endereco_cidade,
                e.uf AS endereco_uf,

                c.id AS condominio_id,
                c.nome AS condominio_nome,
                ce.id AS condominio_endereco_id,
                ce.rua AS condominio_endereco_rua,
                ce.numero AS condominio_endereco_numero,
                ce.complemento AS condominio_endereco_complemento,
                ce.bairro AS condominio_endereco_bairro,
                ce.cep AS condominio_endereco_cep,
                ce.cidade AS condominio_endereco_cidade,
                ce.uf AS condominio_endereco_uf,

                u_cor.id AS corretor_id,
                u_cor.username AS corretor_username,
                u_cor.senha AS corretor_senha,
                u_cor.email AS corretor_email,
                u_cor.nome AS corretor_nome,
                u_cor.cpf_cnpj AS corretor_cpf_cnpj,
                u_cor.rg AS corretor_rg,
                co.creci AS corretor_creci,

                u_cap.id AS captador_id,
                u_cap.username AS captador_username,
                u_cap.senha AS captador_senha,
                u_cap.email AS captador_email,
                u_cap.nome AS captador_nome,
                u_cap.cpf_cnpj AS captador_cpf_cnpj,
                u_cap.rg AS captador_rg,
                ca.salario AS captador_salario,

                a.id AS anuncio_id,
                a.descricao AS anuncio_descricao,
                a.titulo AS anuncio_titulo

            FROM imovel i

            LEFT JOIN endereco e
                ON e.id = i.id_endereco

            LEFT JOIN condominio c
                ON c.id = i.id_condominio

            LEFT JOIN endereco ce
                ON ce.id = c.id_endereco

            LEFT JOIN usuario u_cor
                ON u_cor.id = i.id_corretor

            LEFT JOIN corretor co
                ON co.id_usuario = u_cor.id

            LEFT JOIN usuario u_cap
                ON u_cap.id = i.id_captador

            LEFT JOIN captador ca
                ON ca.id_usuario = u_cap.id

            LEFT JOIN anuncio a
                ON a.id = i.id_anuncio

            ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                throw new Exception("Não há imóveis disponíveis");
            }

            $lista = [];

            foreach ($resultados as $dados) {

                $id = (int) $dados['id'];
                $imovel = $this->montar($dados, $id);
                if ($imovel) {
                    $lista[] = $imovel;
                }
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO Banco->listar: " . $e->getMessage());
            return [];
        }
    }

    public function listarDisponiveis()
    {

        try {

            $sql = "
            SELECT
                i.*,

                e.id AS endereco_id,
                e.rua AS endereco_rua,
                e.numero AS endereco_numero,
                e.complemento AS endereco_complemento,
                e.bairro AS endereco_bairro,
                e.cep AS endereco_cep,
                e.cidade AS endereco_cidade,
                e.uf AS endereco_uf,

                c.id AS condominio_id,
                c.nome AS condominio_nome,
                ce.id AS condominio_endereco_id,
                ce.rua AS condominio_endereco_rua,
                ce.numero AS condominio_endereco_numero,
                ce.complemento AS condominio_endereco_complemento,
                ce.bairro AS condominio_endereco_bairro,
                ce.cep AS condominio_endereco_cep,
                ce.cidade AS condominio_endereco_cidade,
                ce.uf AS condominio_endereco_uf,

                u_cor.id AS corretor_id,
                u_cor.username AS corretor_username,
                u_cor.senha AS corretor_senha,
                u_cor.email AS corretor_email,
                u_cor.nome AS corretor_nome,
                u_cor.cpf_cnpj AS corretor_cpf_cnpj,
                u_cor.rg AS corretor_rg,
                co.creci AS corretor_creci,

                u_cap.id AS captador_id,
                u_cap.username AS captador_username,
                u_cap.senha AS captador_senha,
                u_cap.email AS captador_email,
                u_cap.nome AS captador_nome,
                u_cap.cpf_cnpj AS captador_cpf_cnpj,
                u_cap.rg AS captador_rg,
                ca.salario AS captador_salario,

                a.id AS anuncio_id,
                a.descricao AS anuncio_descricao,
                a.titulo AS anuncio_titulo

            FROM imovel i

            LEFT JOIN endereco e
                ON e.id = i.id_endereco

            LEFT JOIN condominio c
                ON c.id = i.id_condominio

            LEFT JOIN endereco ce
                ON ce.id = c.id_endereco

            LEFT JOIN usuario u_cor
                ON u_cor.id = i.id_corretor

            LEFT JOIN corretor co
                ON co.id_usuario = u_cor.id

            LEFT JOIN usuario u_cap
                ON u_cap.id = i.id_captador

            LEFT JOIN captador ca
                ON ca.id_usuario = u_cap.id

            LEFT JOIN anuncio a
                ON a.id = i.id_anuncio

            WHERE i.status IN ('Venda', 'Aluguel', 'Venda e Aluguel')
        ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                throw new Exception("Não há imóveis disponíveis");
            }

            $lista = [];

            foreach ($resultados as $dados) {

                $id = (int) $dados['id'];

                $imovel = $this->montar($dados, $id);
                if ($imovel) {
                    $lista[] = $imovel;
                }
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO Banco->listarDisponiveis: " . $e->getMessage());
            return [];
        }
    }
    public function buscarPorId($idImovel)
    {
        try {
            $sql = "
            SELECT
                i.*,

                e.id AS endereco_id,
                e.rua AS endereco_rua,
                e.numero AS endereco_numero,
                e.complemento AS endereco_complemento,
                e.bairro AS endereco_bairro,
                e.cep AS endereco_cep,
                e.cidade AS endereco_cidade,
                e.uf AS endereco_uf,

                c.id AS condominio_id,
                c.nome AS condominio_nome,
                ce.id AS condominio_endereco_id,
                ce.rua AS condominio_endereco_rua,
                ce.numero AS condominio_endereco_numero,
                ce.complemento AS condominio_endereco_complemento,
                ce.bairro AS condominio_endereco_bairro,
                ce.cep AS condominio_endereco_cep,
                ce.cidade AS condominio_endereco_cidade,
                ce.uf AS condominio_endereco_uf,

                u_cor.id AS corretor_id,
                u_cor.username AS corretor_username,
                u_cor.senha AS corretor_senha,
                u_cor.email AS corretor_email,
                u_cor.nome AS corretor_nome,
                u_cor.cpf_cnpj AS corretor_cpf_cnpj,
                u_cor.rg AS corretor_rg,
                co.creci AS corretor_creci,

                u_cap.id AS captador_id,
                u_cap.username AS captador_username,
                u_cap.senha AS captador_senha,
                u_cap.email AS captador_email,
                u_cap.nome AS captador_nome,
                u_cap.cpf_cnpj AS captador_cpf_cnpj,
                u_cap.rg AS captador_rg,
                ca.salario AS captador_salario,

                a.id AS anuncio_id,
                a.descricao AS anuncio_descricao,
                a.titulo AS anuncio_titulo

            FROM imovel i

            LEFT JOIN endereco e
                ON e.id = i.id_endereco

            LEFT JOIN condominio c
                ON c.id = i.id_condominio

            LEFT JOIN endereco ce
                ON ce.id = c.id_endereco

            LEFT JOIN usuario u_cor
                ON u_cor.id = i.id_corretor

            LEFT JOIN corretor co
                ON co.id_usuario = u_cor.id

            LEFT JOIN usuario u_cap
                ON u_cap.id = i.id_captador

            LEFT JOIN captador ca
                ON ca.id_usuario = u_cap.id

            LEFT JOIN anuncio a
                ON a.id = i.id_anuncio

            WHERE i.id = :id
        ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id' => $idImovel]);

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) {
                throw new Exception("Imóvel não encontrado");
            }

            return $this->montar($dados, $idImovel);
        } catch (Exception $e) {
            error_log("ERRO! Banco->buscarPorId: " . $e->getMessage());
            return null;
        }
    }

    public function listarPorProprietario($idProprietario)
    {
        try {
            $stmt = $this->bancoDados->prepare("SELECT id_imovel FROM proprietario_imovel WHERE id_proprietario = ?");
            $stmt->execute([$idProprietario]);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($dados)) {
                throw new Exception("Não há imóveis disponíveis");
            }
            $imoveis = [];
            foreach ($dados as $row) {
                $id = (int) $row['id_imovel'];
                $imovel = $this->buscarPorId($id);
                if ($imovel) {
                    $imoveis[] = $imovel;
                }
            }
            return $imoveis;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->listarPorProprietario: " . $e->getMessage();
            error_log($erro);
            return [];
        }
    }

    function listarFavoritos(int $idCliente)
    {
        try {
            $sql = "
            SELECT

            imovel_cliente.*,

            imovel.id AS id,
            imovel.valor_venda AS valor_venda,
            imovel.valor_aluguel AS valor_aluguel,
            imovel.quant_quartos AS quant_quartos,
            imovel.quant_salas AS quant_salas,
            imovel.quant_vagas AS quant_vagas,
            imovel.quant_banheiros AS quant_banheiros,
            imovel.quant_varandas AS quant_varandas,
            imovel.categoria AS categoria,
            imovel.id_endereco AS id_endereco,
            imovel.status AS status,
            imovel.iptu AS iptu,
            imovel.valor_condominio AS valor_condominio,
            imovel.andar AS andar,
            imovel.estado AS estado,
            imovel.bloco AS bloco,
            imovel.ano_construcao AS ano_construcao,
            imovel.area_total AS area_total,
            imovel.area_privativa AS area_privativa,
            imovel.situacao AS situacao,
            imovel.ocupacao AS ocupacao,
            imovel.id_corretor AS id_corretor,
            imovel.id_captador AS id_captador,
            imovel.data_cadastro AS data_cadastro,
            imovel.data_modificacao AS data_modificacao,
            imovel.id_anuncio AS id_anuncio,
            imovel.id_condominio AS id_condominio,

            endereco.id AS endereco_id,
            endereco.rua AS endereco_rua,
            endereco.numero AS endereco_numero,
            endereco.complemento AS endereco_complemento,
            endereco.bairro AS endereco_bairro,
            endereco.cep AS endereco_cep,
            endereco.cidade AS endereco_cidade,
            endereco.uf AS endereco_uf,

            condominio.id AS condominio_id,
            condominio.nome AS condominio_nome,
            condominio_endereco.id AS condominio_endereco_id,
            condominio_endereco.rua AS condominio_endereco_rua,
            condominio_endereco.numero AS condominio_endereco_numero,
            condominio_endereco.complemento AS condominio_endereco_complemento,
            condominio_endereco.bairro AS condominio_endereco_bairro,
            condominio_endereco.cep AS condominio_endereco_cep,
            condominio_endereco.cidade AS condominio_endereco_cidade,
            condominio_endereco.uf AS condominio_endereco_uf,

            usuario_corretor.id AS corretor_id,
            usuario_corretor.username AS corretor_username,
            usuario_corretor.senha AS corretor_senha,
            usuario_corretor.email AS corretor_email,
            usuario_corretor.nome AS corretor_nome,
            usuario_corretor.cpf_cnpj AS corretor_cpf_cnpj,
            usuario_corretor.rg AS corretor_rg,
            corretor.creci AS corretor_creci,

            usuario_captador.id AS captador_id,
            usuario_captador.username AS captador_username,
            usuario_captador.senha AS captador_senha,
            usuario_captador.email AS captador_email,
            usuario_captador.nome AS captador_nome,
            usuario_captador.cpf_cnpj AS captador_cpf_cnpj,
            usuario_captador.rg AS captador_rg,
            captador.salario AS captador_salario,

            anuncio.id AS anuncio_id,
            anuncio.descricao AS anuncio_descricao,
            anuncio.titulo AS anuncio_titulo

            FROM imovel_cliente

            LEFT join imovel 
                ON imovel_cliente.id_imovel = imovel.id 

            LEFT JOIN endereco
                ON endereco.id = imovel.id_endereco

            LEFT JOIN condominio
                ON condominio.id = imovel.id_condominio

            LEFT JOIN endereco condominio_endereco
                ON condominio_endereco.id = condominio.id_endereco

            LEFT JOIN usuario usuario_corretor
                ON usuario_corretor.id = imovel.id_corretor

            LEFT JOIN corretor
                ON corretor.id_usuario = usuario_corretor.id

            LEFT JOIN usuario usuario_captador
                ON usuario_captador.id = imovel.id_captador

            LEFT JOIN captador
                ON captador.id_usuario = usuario_captador.id

            LEFT JOIN anuncio 
                ON anuncio.id = imovel.id_anuncio

            WHERE id_cliente = :id

            ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id' => $idCliente]);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($dados)) {
                throw new Exception("Não há imóveis favoritados para o cliente especificado");
            }

            $lista = [];

            foreach ($dados as $registro) {
                $idImovel = (int) $registro['id'];
                $imovel = $this->montar($registro, $idImovel);
                if ($imovel) {
                    $lista[] = $imovel;
                }
            }
            return $lista;
        } catch (Exception $e) {
            error_log("ERRO Banco->listarFavoritos: " . $e->getMessage());
            return [];
        }
    }

    public function cadastrarImoveisCliente(int $idCliente, array $idImoveis): bool
    {
        $idImoveis = array_map('intval', $idImoveis);

        try {
            $this->bancoDados->beginTransaction();

            error_log("Imóveis a serem cadastrados para o cliente {$idCliente}: " . implode(',', $idImoveis));

            if (empty($idImoveis)) {
                $stmt = $this->bancoDados->prepare("
                DELETE FROM imovel_cliente
                WHERE id_cliente = :id_cliente
            ");

                $resultado = $stmt->execute([
                    ':id_cliente' => $idCliente
                ]);

                $this->bancoDados->commit();
                return $resultado;
            }

            $stmt = $this->bancoDados->prepare("
            DELETE FROM imovel_cliente
            WHERE id_cliente = :id_cliente
            AND id_imovel NOT IN (" . implode(',', $idImoveis) . ")
        ");

            $resultado = $stmt->execute([
                ':id_cliente' => $idCliente
            ]);

            error_log("Resultado da exclusão de imóveis do cliente: " . ($resultado ? "Sucesso" : "Falha"));

            $stmt = $this->bancoDados->prepare("
            INSERT IGNORE INTO imovel_cliente (id_cliente, id_imovel)
            VALUES (:id_cliente, :id_imovel)
        ");

            foreach ($idImoveis as $idImovel) {
                $stmt->execute([
                    ':id_cliente' => $idCliente,
                    ':id_imovel' => $idImovel
                ]);
            }

            $this->bancoDados->commit();
            return true;
        } catch (Exception $e) {
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }

            error_log("ERRO Banco->cadastrarImoveisCliente: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar($imovel)
    {

        try {

            $this->bancoDados->beginTransaction();
            $categoria = $imovel->getCategoria();
            $categoria = $categoria ? $categoria->value : null;
            $status = $imovel->getStatus();
            $status = $status ? $status->value : null;
            $estado = $imovel->getEstado();
            $estado = $estado ? $estado->value : null;
            $situacao = $imovel->getSituacao();
            $situacao = $situacao ? $situacao->value : null;
            $ocupacao = $imovel->getOcupacao();
            $ocupacao = $ocupacao ? $ocupacao->value : null;
            $endereco = $imovel->getEndereco();
            $endereco = ($endereco && $endereco->getId()) ? $endereco->getId() : null;
            $anuncio = $imovel->getAnuncio();
            $anuncio = ($anuncio && $anuncio->getId()) ? $anuncio->getId() : null;
            $condominio = $imovel->getCondominio();
            $condominio = $condominio ? $condominio->getId() : null;
            $corretor = $imovel->getCorretor();
            $corretor = $corretor ? $corretor->getCpfCnpj() : null;
            $captador = $imovel->getCaptador();
            $captador = $captador ? $captador->getCpfCnpj() : null;
            $dataCadastro = $imovel->getDataCadastro();
            $dataCadastro = $dataCadastro ? $dataCadastro->format("Y-m-d") : null;
            $dataModificacao = $imovel->getDataModificacao();
            $dataModificacao = $dataModificacao ? $dataModificacao->format("Y-m-d") : null;
            $imovelDb = $this->buscarPorId($imovel->getId());
            $propsAntigos = $imovelDb ? $imovelDb->getProprietarios() : [];
            $propsNovos = $imovel->getProprietarios() ?: [];

            foreach ($propsAntigos as $p) {
                if (!in_array($p, $propsNovos)) {

                    $stmt = $this->bancoDados->prepare("
                    DELETE FROM proprietario_imovel
                    WHERE id_proprietario = :id_proprietario
                      AND id_imovel = :id
                ");
                    $stmt->execute([
                        ':id_proprietario' => $p->getId(),
                        ':id' => $imovel->getId()
                    ]);
                }
            }

            foreach ($propsNovos as $p) {
                if (!in_array($p, $propsAntigos)) {

                    $stmt = $this->bancoDados->prepare("
                    INSERT INTO proprietario_imovel (id_proprietario, id_imovel)
                    VALUES (:id_proprietario, :id_imovel)
                ");
                    $stmt->execute([
                        ':id_proprietario' => $p->getId(),
                        ':id_imovel' => $imovel->getId()
                    ]);
                }
            }


            $filtrosAntigos = $imovelDb ? $imovelDb->getFiltros() : [];
            $filtrosNovos = $imovel->getFiltros() ?: [];

            foreach ($filtrosAntigos as $f) {
                if (!in_array($f, $filtrosNovos)) {
                    $id = $this->getIdFiltroImovelPorNome($f);
                    if ($id !== null) {
                        $this->removerFiltro($imovel->getId(), $id);
                    }
                }
            }

            foreach ($filtrosNovos as $f) {
                if (!in_array($f, $filtrosAntigos)) {
                    $id = $this->getIdFiltroImovelPorNome($f);
                    if ($id !== null) {
                        $this->cadastrarFiltro($imovel->getId(), $id);
                    }
                }
            }


            $sql = "
            UPDATE imovel SET
                valor_venda = :valor_venda,
                valor_aluguel = :valor_aluguel,
                quant_quartos = :quartos,
                quant_salas = :salas,
                quant_vagas = :vagas,
                quant_banheiros = :banheiros,
                quant_varandas = :varandas,
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
                id_anuncio = :anuncio,
                id_condominio = :condominio
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
                ':categoria' => $categoria,
                ':endereco' => $endereco,
                ':status' => $status,
                ':iptu' => $imovel->getIptu(),
                ':condominio_valor' => $imovel->getValorCondominio(),
                ':andar' => $imovel->getAndar(),
                ':estado' => $estado,
                ':bloco' => $imovel->getBloco(),
                ':ano' => $imovel->getAnoConstrucao(),
                ':area_total' => $imovel->getAreaTotal(),
                ':area_privativa' => $imovel->getAreaPrivativa(),
                ':situacao' => $situacao,
                ':ocupacao' => $ocupacao,
                ':corretor' => $corretor,
                ':captador' => $captador,
                ':data_cadastro' => $dataCadastro,
                ':data_modificacao' => $dataModificacao,
                ':anuncio' => $anuncio,
                ':condominio' => $condominio,
                ':id' => $imovel->getId()
            ]);
            return $this->bancoDados->commit();
        } catch (Exception $e) {
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            error_log("ERRO Banco->atualizar: " . $e->getMessage());
            return false;
        }
    }


    public function cadastrar($imovel)
    {
        try {


            $this->bancoDados->beginTransaction();

            $sql = "
            INSERT INTO imovel (
                valor_venda, valor_aluguel, quant_quartos, quant_salas, quant_vagas,
                quant_banheiros, quant_varandas, categoria, id_endereco, status,
                iptu, valor_condominio, andar, estado, bloco, ano_construcao,
                area_total, area_privativa, situacao, ocupacao,
                id_corretor, id_captador,
                data_cadastro, data_modificacao, id_anuncio, id_condominio
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";


            $categoria = $imovel->getCategoria();
            $categoria = $categoria ? $categoria->value : null;

            $endereco = $imovel->getEndereco();
            $idEndereco = ($endereco && $endereco->getId()) ? $endereco->getId() : null;

            $anuncio = $imovel->getAnuncio();
            $idAnuncio = ($anuncio && $anuncio->getId()) ? $anuncio->getId() : null;

            $status = $imovel->getStatus();
            $status = $status ? $status->value : null;

            $estado = $imovel->getEstado();
            $estado = $estado ? $estado->value : null;

            $situacao = $imovel->getSituacao();
            $situacao = $situacao ? $situacao->value : null;

            $ocupacao = $imovel->getOcupacao();
            $ocupacao = $ocupacao ? $ocupacao->value : null;

            $corretor = $imovel->getCorretor();
            $cpf_corretor = $corretor ? $corretor->getId() : null;

            $captador = $imovel->getCaptador();
            $cpf_captador = $captador ? $captador->getId() : null;

            $condominio = $imovel->getCondominio();
            $idCondominio = $condominio ? $condominio->getId() : null;

            $dataCadastro = $imovel->getDataCadastro();
            if ($dataCadastro) {
                $dataCadastro = $dataCadastro->format("Y-m-d H:i:s");
            } else {
                $dataCadastro = date("Y-m-d H:i:s");
            }

            $dataModificacao = $imovel->getDataModificacao();
            if ($dataModificacao) {
                $dataModificacao = $dataModificacao->format("Y-m-d H:i:s");
            } else {
                $dataModificacao = null;
            }


            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                $imovel->getValorVenda(),
                $imovel->getValorAluguel(),
                $imovel->getQuantQuartos(),
                $imovel->getQuantSalas(),
                $imovel->getQuantVagas(),
                $imovel->getQuantBanheiros(),
                $imovel->getQuantVarandas(),
                $categoria,
                $idEndereco,
                $status,
                $imovel->getIptu(),
                $imovel->getValorCondominio(),
                $imovel->getAndar(),
                $estado,
                $imovel->getBloco(),
                $imovel->getAnoConstrucao(),
                $imovel->getAreaTotal(),
                $imovel->getAreaPrivativa(),
                $situacao,
                $ocupacao,
                $cpf_corretor,
                $cpf_captador,
                $dataCadastro,
                $dataModificacao,
                $idAnuncio,
                $idCondominio
            ]);

            $idImovel = $this->bancoDados->lastInsertId();


            if ($imovel->getProprietarios()) {
                foreach ($imovel->getProprietarios() as $prop) {
                    $stmtProp = $this->bancoDados->prepare("
                    INSERT INTO proprietario_imovel (id_proprietario, id_imovel)
                    VALUES (?, ?)
                ");
                    $stmtProp->execute([
                        $prop->getId(),
                        $idImovel
                    ]);
                }
            }


            if ($imovel->getFiltros()) {
                foreach ($imovel->getFiltros() as $filtro) {
                    $stmt = $this->bancoDados->prepare("
                        INSERT IGNORE INTO filtros_imovel (nome)
                        VALUES (:nome)
                    ");
                    $stmt->execute([':nome' => $filtro]);

                    $stmt = $this->bancoDados->prepare("
                        SELECT id
                        FROM filtros_imovel
                        WHERE nome = :nome
                    ");
                    $stmt->execute([':nome' => $filtro]);

                    $idFiltro = $stmt->fetchColumn();

                    $stmt = $this->bancoDados->prepare("
                        INSERT INTO imovel_filtros (id_imovel, id_filtros_imovel)
                        VALUES (:id_imovel, :id_filtro)
                    ");
                    $stmt->execute([
                        ':id_imovel' => $idImovel,
                        ':id_filtro' => $idFiltro
                    ]);
                }
            }


            $this->bancoDados->commit();

            return $this->bancoDados->lastInsertId();
        } catch (Exception $e) {
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            error_log("ERRO! Banco->cadastrar: " . $e->getMessage());
            return false;
        }
    }


    public function getIdFiltroImovelPorNome($nome)
    {
        try {

            $stmt = $this->bancoDados->prepare("
                SELECT id_filtros_imovel 
                FROM filtros_imovel 
                WHERE nome = :nome
            ");
            $stmt->execute([':nome' => $nome]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? (int) $row['id_filtros_imovel'] : null;
        } catch (Exception $e) {
            error_log("ERRO Banco->getIdFiltroImovelPorNome: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrarFiltro($idImovel, $idFiltro)
    {
        try {

            $stmt = $this->bancoDados->prepare("
            INSERT INTO imovel_filtros (id_imovel, id_filtros_imovel)
            VALUES (:id_imovel, :id_filtro)
        ");

            return $stmt->execute([
                ':id_imovel' => $idImovel,
                ':id_filtro' => $idFiltro
            ]);;
        } catch (Exception $e) {
            error_log("ERRO Banco->cadastrarFiltro: " . $e->getMessage());
            return false;
        }
    }

    public function removerFiltro($idImovel, $idFiltro)
    {
        try {

            $stmt = $this->bancoDados->prepare("
            DELETE FROM imovel_filtros
            WHERE id_imovel = :id_imovel 
              AND id_filtros_imovel = :id_filtro
        ");
            return $stmt->execute([
                ':id_imovel' => $idImovel,
                ':id_filtro' => $idFiltro
            ]);
        } catch (Exception $e) {
            error_log("ERRO Banco->removerFiltro: " . $e->getMessage());
            return false;
        }
    }
}
