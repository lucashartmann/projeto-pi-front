<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/funcionario.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/telefoneDAO.php';
require_once __DIR__ . '/anexoDAO.php';
require_once __DIR__ . '/filtroDAO.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class ProprietarioImovelDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }


    private function normalizarId(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '' && ctype_digit($value)) {
            return (int) $value;
        }

        if (is_object($value) && method_exists($value, 'getId')) {
            return (int) $value->getId();
        }

        return null;
    }

    private function montarEndereco(array $dados): ?Endereco
    {
        if (!isset($dados['id_endereco']) || $dados['id_endereco'] === null) {
            return null;
        }

        $endereco = new Endereco(
            (string) ($dados['rua'] ?? $dados['endereco_rua'] ?? ''),
            (string) ($dados['bairro'] ?? $dados['endereco_bairro'] ?? ''),
            (string) ($dados['cep'] ?? $dados['endereco_cep'] ?? ''),
            (string) ($dados['cidade'] ?? $dados['endereco_cidade'] ?? ''),
            (string) ($dados['uf'] ?? $dados['endereco_uf'] ?? '')
        );

        $endereco->setId((int) $dados['id_endereco']);
        $numero = $dados['numero'] ?? $dados['endereco_numero'] ?? null;
        $endereco->setNumero($numero !== null ? (int) $numero : null);

        $complemento = $dados['complemento'] ?? $dados['endereco_complemento'] ?? null;
        $endereco->setComplemento($complemento !== null ? (string) $complemento : '');

        return $endereco;
    }

    private function montarImovel(array $dados, bool $carregarProprietarios = false): ?Imovel
    {
        try {
            if (!isset($dados['id'])) {
                throw new Exception('ID do imóvel não fornecido');
            }

            $endereco = null;
            if (!empty($dados['endereco_id'])) {
                $endereco = new Endereco(
                    (string) ($dados['endereco_rua'] ?? ''),
                    (string) ($dados['endereco_bairro'] ?? ''),
                    (string) ($dados['endereco_cep'] ?? ''),
                    (string) ($dados['endereco_cidade'] ?? ''),
                    (string) ($dados['endereco_uf'] ?? '')
                );
                $endereco->setId((int) $dados['endereco_id']);
                $endereco->setNumero($dados['endereco_numero'] !== null ? (int) $dados['endereco_numero'] : null);
                $endereco->setComplemento($dados['endereco_complemento'] !== null ? (string) $dados['endereco_complemento'] : '');
            }

            $imovel = new Imovel(
                $endereco,
                Status::tryFrom((string) ($dados['status'] ?? '')) ?? Status::PENDENTE,
                Categoria::tryFrom((string) ($dados['categoria'] ?? '')) ?? Categoria::APARTAMENTO
            );

            $imovel->setId((int) $dados['id']);
            $imovel->setValorVenda($dados['valor_venda'] !== null ? (float) $dados['valor_venda'] : 0);
            $imovel->setValorAluguel($dados['valor_aluguel'] !== null ? (float) $dados['valor_aluguel'] : 0);
            $imovel->setQuantQuartos($dados['quant_quartos'] !== null ? (int) $dados['quant_quartos'] : 0);
            $imovel->setQuantSalas($dados['quant_salas'] !== null ? (int) $dados['quant_salas'] : 0);
            $imovel->setQuantVagas($dados['quant_vagas'] !== null ? (int) $dados['quant_vagas'] : 0);
            $imovel->setQuantBanheiros($dados['quant_banheiros'] !== null ? (int) $dados['quant_banheiros'] : 0);
            $imovel->setQuantVarandas($dados['quant_varandas'] !== null ? (int) $dados['quant_varandas'] : 0);
            $imovel->setQuantSuites($dados['quant_suites'] !== null ? (int) $dados['quant_suites'] : 0);
            $imovel->setIptu($dados['iptu'] !== null ? (float) $dados['iptu'] : 0);
            $imovel->setValorCondominio($dados['valor_condominio'] !== null ? (float) $dados['valor_condominio'] : 0);
            $imovel->setAndar($dados['andar'] !== null ? (int) $dados['andar'] : 0);
            $imovel->setEstado(isset($dados['estado']) && $dados['estado'] !== null ? Estado::tryFrom($dados['estado']) : null);
            $imovel->setBloco((string) ($dados['bloco'] ?? ''));
            $imovel->setAnoConstrucao($dados['ano_construcao'] !== null ? (int) $dados['ano_construcao'] : 0);
            $imovel->setAreaTotal($dados['area_total'] !== null ? (float) $dados['area_total'] : 0);
            $imovel->setAreaPrivativa($dados['area_privativa'] !== null ? (float) $dados['area_privativa'] : 0);
            $imovel->setSituacao(isset($dados['situacao']) && $dados['situacao'] !== null ? Situacao::tryFrom($dados['situacao']) : null);
            $imovel->setOcupacao(isset($dados['ocupacao']) && $dados['ocupacao'] !== null ? Ocupacao::tryFrom($dados['ocupacao']) : null);
            $imovel->setDataCadastro(isset($dados['data_cadastro']) && $dados['data_cadastro'] !== null ? new DateTime($dados['data_cadastro']) : null);
            $imovel->setDataModificacao(isset($dados['data_modificacao']) && $dados['data_modificacao'] !== null ? new DateTime($dados['data_modificacao']) : null);
            $imovel->setDestacado((bool) ($dados['destacado'] ?? false));
            $imovel->setQuantClicks($dados['quant_clicks'] !== null ? (int) $dados['quant_clicks'] : 0);

            $anuncio = new Anuncio();
            $anuncio->setIdImovel((int) $dados['id']);
            $anuncio->setDescricao((string) ($dados['anuncio_descricao'] ?? ''));
            $anuncio->setTitulo((string) ($dados['anuncio_titulo'] ?? ''));
            $anexoDAO = new AnexoDAO();
            $anexos = $anexoDAO->listarPorIdAnuncio((int) $dados['id']);
            $anuncio->setImagens($anexos['Imagens'] ?? []);
            $anuncio->setVideos($anexos['Videos'] ?? []);
            $anuncio->setAnexos($anexos['Documentos'] ?? []);

            $imovel->setAnuncio($anuncio);


            if (!empty($dados['condominio_id'])) {
                $condominio = new Condominio((string) ($dados['condominio_nome'] ?? ''), $endereco);
                $condominio->setId((int) $dados['condominio_id']);
                $imovel->setCondominio($condominio);
            }

            if (!empty($dados['corretor_id'])) {
                $corretor = new Corretor(
                    (string) ($dados['corretor_email'] ?? ''),
                    (string) ($dados['corretor_nome'] ?? ''),
                    (string) ($dados['corretor_cpf_cnpj'] ?? ''),
                    (string) ($dados['corretor_creci'] ?? '')
                );
                $corretor->setId((int) $dados['corretor_id']);
                $corretor->setSenha((string) ($dados['corretor_senha'] ?? ''));
                $corretor->setRg((string) ($dados['corretor_rg'] ?? ''));
                $imovel->setCorretor($corretor);
            }

            if (!empty($dados['captador_id'])) {
                $captador = new Funcionario(
                    (string) ($dados['captador_email'] ?? ''),
                    (string) ($dados['captador_nome'] ?? ''),
                    (string) ($dados['captador_cpf_cnpj'] ?? ''),
                    Cargo::CAPTADOR
                );
                $captador->setId((int) $dados['captador_id']);
                $captador->setSenha((string) ($dados['captador_senha'] ?? ''));
                $captador->setRg((string) ($dados['captador_rg'] ?? ''));
                if (isset($dados['captador_salario']) && $dados['captador_salario'] !== null) {
                    $captador->setSalario((float) $dados['captador_salario']);
                }
                $imovel->setCaptador($captador);
            }

            if ($carregarProprietarios) {
                $imovel->setProprietarios($this->listarPorIdImovel((int) $dados['id']));
            }

            $filtroDAO = new FiltroDAO();
            $imovel->setFiltros($filtroDAO->listarPorId((int) $dados['id'], 'imovel') ?: []);

            return $imovel;
        } catch (Exception $e) {
            error_log('ERRO! ProprietarioImovelDAO->montarImovel: ' . $e->getMessage());
            throw $e;
        }
    }

    private function montarProprietario(array $registro): ?Proprietario
    {
        try {
            if (!isset($registro['id'])) {
                throw new Exception('ID do proprietário não fornecido');
            }

            $proprietario = new Proprietario(
                (string) ($registro['email'] ?? ''),
                (string) ($registro['nome'] ?? ''),
                (string) ($registro['cpf_cnpj'] ?? '')
            );

            $proprietario->setId((int) $registro['id']);
            $proprietario->setRg((string) ($registro['rg'] ?? ''));

            if (isset($registro['data_nascimento']) && $registro['data_nascimento'] !== null) {
                $proprietario->setDataNascimento(new DateTime($registro['data_nascimento']));
            }

            if (isset($registro['data_cadastro']) && $registro['data_cadastro'] !== null) {
                $proprietario->setDataCadastro(new DateTime($registro['data_cadastro']));
            }

            if (isset($registro['data_modificacao']) && $registro['data_modificacao'] !== null) {
                $proprietario->setDataModificacao(new DateTime($registro['data_modificacao']));
            }
            $telefoneDAO = new TelefoneDAO();
            $proprietario->setTelefones($telefoneDAO->listarPorPessoa((int) $registro['id']) ?: []);
            $proprietario->setEndereco($this->montarEndereco($registro));

            if (isset($registro['senha']) && $registro['senha'] !== null) {
                $proprietario->setSenha((string) $registro['senha']);
            }

            if (isset($registro['ultimo_login']) && $registro['ultimo_login'] !== null) {
                $proprietario->setUltimoLogin(new DateTime($registro['ultimo_login']));
            }

            if (isset($registro['ativo']) && $registro['ativo'] !== null) {
                $proprietario->setAtivo((bool) $registro['ativo']);
            }

            return $proprietario;
        } catch (Exception $e) {
            error_log('ERRO! ProprietarioImovelDAO->montarProprietario: ' . $e->getMessage());
            throw new Exception('Erro ao montar proprietário: ' . $e->getMessage());
        }
    }

    public  function listarPorProprietario(int $idProprietario): array
    {
        try {
            $idProprietario = $this->normalizarId($idProprietario);
            if ($idProprietario === null) {
                return [];
            }

            $stmt = $this->bancoDados->prepare(" 
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
            
                u_cor.id_pessoa AS corretor_id,
            
                u_cor.senha AS corretor_senha,
                    p_cor.email AS corretor_email,
                    p_cor.nome AS corretor_nome,
                    p_cor.cpf_cnpj AS corretor_cpf_cnpj,
                    p_cor.rg AS corretor_rg,
                co.creci AS corretor_creci,

                u_cap.id_pessoa AS captador_id,
          
                u_cap.senha AS captador_senha,
                    p_cap.email AS captador_email,
                    p_cap.nome AS captador_nome,
                    p_cap.cpf_cnpj AS captador_cpf_cnpj,
                    p_cap.rg AS captador_rg,
                ca.salario AS captador_salario,

                a.descricao AS anuncio_descricao,
                a.titulo AS anuncio_titulo

                FROM proprietario_imovel pi

                LEFT JOIN imovel i
                    ON i.id = pi.id_imovel

                LEFT JOIN endereco e
                    ON e.id = i.id_endereco

                LEFT JOIN condominio c
                    ON c.id = i.id_condominio

                LEFT JOIN usuario u_cor
                    ON u_cor.id_pessoa = i.id_corretor

                LEFT JOIN pessoa p_cor
                    ON p_cor.id = u_cor.id_pessoa

                LEFT JOIN corretor co
                    ON co.id_funcionario = u_cor.id_pessoa

                LEFT JOIN usuario u_cap
                    ON u_cap.id_pessoa = i.id_captador

                LEFT JOIN pessoa p_cap
                    ON p_cap.id = u_cap.id_pessoa

                LEFT JOIN funcionario ca
                    ON ca.id_pessoa = u_cap.id_pessoa

                LEFT JOIN anuncio a
                    ON a.id_imovel = i.id  
                WHERE pi.id_proprietario = :id_proprietario
            ");

            $stmt->execute([':id_proprietario' => $idProprietario]);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $imoveis = [];
            foreach ($dados as $row) {
                $imovel = $this->montarImovel($row, false);
                if ($imovel !== null) {
                    $imoveis[] = $imovel;
                }
            }

            return $imoveis;
        } catch (Exception $e) {
            error_log('ERRO! ProprietarioImovelDAO->listarPorProprietario: ' . $e->getMessage());
            throw new Exception('Erro ao listar imóveis do proprietário: ' . $e->getMessage());
        }
    }

    public function remover(int $idProprietario, int $idImovel): bool
    {
        try {
            $stmt = $this->bancoDados->prepare("
                DELETE FROM proprietario_imovel
                WHERE id_proprietario = :id_proprietario AND id_imovel = :id_imovel
            ");
            $stmt->execute([
                ':id_proprietario' => $idProprietario,
                ':id_imovel' => $idImovel
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO! ProprietarioImovelDAO->remover: " . $e->getMessage());
            throw new Exception("Erro ao remover proprietário do imóvel: " . $e->getMessage());
        }
    }

    public function cadastrar(int $idProprietario, int $idImovel): bool
    {
        try {
            $stmt = $this->bancoDados->prepare("
                INSERT INTO proprietario_imovel (id_proprietario, id_imovel)
                VALUES (:id_proprietario, :id_imovel)
            ");
            $stmt->execute([
                ':id_proprietario' => $idProprietario,
                ':id_imovel' => $idImovel
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO! ProprietarioImovelDAO->cadastrar: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar proprietário ao imóvel: " . $e->getMessage());
        }
    }

    public  function listarPorIdImovel(int $idImovel): array
    {
        try {
            $stmt = $this->bancoDados->prepare(" 
                SELECT
                    p.*,
                    e.id AS id_endereco,
                    e.rua AS rua,
                    e.numero AS numero,
                    e.complemento AS complemento,
                    e.bairro AS bairro,
                    e.cep AS cep,
                    e.cidade AS cidade,
                    e.uf AS uf
                FROM pessoa p
                INNER JOIN proprietario_imovel pi
                    ON pi.id_proprietario = p.id
                LEFT JOIN endereco e
                    ON e.id = p.id_endereco
                WHERE pi.id_imovel = :id_imovel
            ");

            $stmt->execute([':id_imovel' => (int) $idImovel]);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $proprietarios = [];
            foreach ($dados as $registro) {
                $proprietario = $this->montarProprietario($registro);
                if ($proprietario !== null) {
                    $proprietarios[] = $proprietario;
                }
            }

            return $proprietarios;
        } catch (Exception $e) {
            error_log('ERRO! ProprietarioImovelDAO->listarPorIdImovel: ' . $e->getMessage());
            throw new Exception('Erro ao listar proprietários do imóvel: ' . $e->getMessage());
        }
    }
}
