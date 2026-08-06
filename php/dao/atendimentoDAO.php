<?php


require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/imovelDAO.php';
require_once __DIR__ . '/usuarioDAO.php';
require_once __DIR__ . '/../model/atendimento.php';

class AtendimentoDAO
{
    private Banco $bancoDados;
    private ImovelDAO $imovelDAO;
    private UsuarioDAO $usuarioDAO;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
        $this->imovelDAO = new ImovelDAO();
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function getConexao()
    {
        return $this->bancoDados;
    }

    public function buscarPorId($id)
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
            $erro = "ERRO! atendimentoDAO->buscarPorId: " . $e->getMessage();
            error_log($erro);
            return null;
        }
    }

    public function atualizar($atendimento)
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
            error_log("ERRO! atendimentoDAO->atualizar: " . $e->getMessage());
            return false;
        }
    }

    public function montar($dados)
    {

        $idImovel = $dados['id_imovel'];
        $idCorretor = $dados['id_corretor'];
        $idCliente = $dados['id_cliente'];

        $atendimento = new Atendimento();

        if ($idImovel) {
            $imovel = $this->imovelDAO->buscarPorId($idImovel);
            $atendimento->setImovel($imovel);
        }

        if ($idCorretor) {
            $corretor = $this->usuarioDAO->buscarPorId($idCorretor);
            $atendimento->setCorretor($corretor);
        }

        if ($idCliente) {
            $cliente = $this->usuarioDAO->buscarPorId($idCliente);
            $atendimento->setCliente($cliente);
        }

        $atendimento->setId($dados['id']);
        $atendimento->setStatus(StatusAtendimento::tryFrom($dados['status']));

        return $atendimento;
    }

    public function cadastrar($atendimento)
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
            $erro = "ERRO! atendimentoDAO->cadastrar: " . $e->getMessage();
            error_log($erro);
            return False;
        }
    }

    public function listar()
    {
        try {

            $sql = "
            SELECT
                atendimento.id AS atendimento_id,
                atendimento.id_imovel AS atendimento_id_imovel,
                atendimento.id_corretor AS atendimento_id_corretor,
                atendimento.id_cliente AS atendimento_id_cliente,
                atendimento.status AS atendimento_status

                imovel_endereco.id AS imovel_endereco_id,
                imovel_endereco.rua AS imovel_endereco_rua,
                imovel_endereco.numero AS imovel_endereco_numero,
                imovel_endereco.complemento AS imovel_endereco_complemento,
                imovel_endereco.bairro AS imovel_endereco_bairro,
                imovel_endereco.cep AS imovel_endereco_cep,
                imovel_endereco.cidade AS imovel_endereco_cidade,
                imovel_endereco.uf AS imovel_endereco_uf,

                imovel_condominio.id AS imovel_condominio_id,
                imovel_condominio.nome AS imovel_condominio_nome,
                imovel_condominio.endereco_id AS imovel_condominio_endereco_id,
                imovel_condominio.endereco_rua AS imovel_condominio_endereco_rua,
                imovel_condominio.endereco_numero AS imovel_condominio_endereco_numero,
                imovel_condominio.endereco_complemento AS imovel_condominio_endereco_complemento,
                imovel_condominio.endereco_bairro AS imovel_condominio_endereco_bairro,
                imovel_condominio.endereco_cep AS imovel_condominio_endereco_cep,
                imovel_condominio.endereco_cidade AS imovel_condominio_endereco_cidade,
                imovel_condominio.endereco_uf AS imovel_condominio_endereco_uf,

                imovel_usuario_corretor.id AS imovel_corretor_id,
                imovel_usuario_corretor.username AS imovel_corretor_username,
                imovel_usuario_corretor.senha AS imovel_corretor_senha,
                imovel_usuario_corretor.email AS imovel_corretor_email,
                imovel_usuario_corretor.nome AS imovel_corretor_nome,
                imovel_usuario_corretor.cpf_cnpj AS imovel_corretor_cpf_cnpj,
                imovel_usuario_corretor.rg AS imovel_corretor_rg,
                imovel_corretor.creci AS imovel_corretor_creci,

                imovel_usario_captador.id AS imovel_captador_id,
                imovel_usario_captador.username AS imovel_captador_username,
                imovel_usario_captador.senha AS imovel_captador_senha,
                imovel_usario_captador.email AS imovel_captador_email,
                imovel_usario_captador.nome AS imovel_captador_nome,
                imovel_usario_captador.cpf_cnpj AS imovel_captador_cpf_cnpj,
                imovel_usario_captador.rg AS imovel_captador_rg,
                imovel_captador.salario AS imovel_captador_salario,

                imovel_anuncio.id AS imovel_anuncio_id,
                imovel_anuncio.descricao AS imovel_anuncio_descricao,
                imovel_anuncio.titulo AS imovel_anuncio_titulo

                imovel.id AS imovel_id,
                imovel.valor_venda as imovel_valor_venda 
                imovel.valor_aluguel as imovel_valor_aluguel
                imovel.quant_quartos as imovel_quant_quartos
                imovel.quant_salas as imovel_quant_salas
                imovel.quant_vagas as imovel_quant_vagas
                imovel.quant_banheiros as imovel_quant_banheiros
                imovel.quant_varandas as imovel_quant_varandas
                imovel.categoria as imovel_categoria
                imovel.id_endereco as imovel_id_endereco
                imovel.status as imovel_status
                imovel.iptu as imovel_iptu
                imovel.valor_condominio as imovel_valor_condominio
                imovel.andar as imovel_andar
                imovel.estado as imovel_estado
                imovel.bloco as imovel_bloco
                imovel.ano_construcao as imovel_ano_construcao
                imovel.area_total as imovel_area_total
                imovel.area_privativa as imovel_area_privativa
                imovel.situacao as imovel_situacao
                imovel.ocupacao as imovel_ocupacao
                imovel.id_corretor as imovel_id_corretor
                imovel.id_captador as imovel_id_captador
                imovel.data_cadastro as imovel_data_cadastro
                imovel.data_modificacao as imovel_data_modificacao
                imovel.id_anuncio as imovel_id_anuncio
                imovel.id_condominio as imovel_id_condominio
                imovel.quant_clicks as imovel_quant_clicks
                imovel.destacado as imovel_destacado
               
                atendimento_corretor_usuario.id as atendimento_corretor_id 
                atendimento_corretor_usuario.username as atendimento_corretor_username
                atendimento_corretor_usuario.senha as atendimento_corretor_senha
                atendimento_corretor_usuario.email as atendimento_corretor_email
                atendimento_corretor_usuario.nome as atendimento_corretor_nome
                atendimento_corretor_usuario.cpf_cnpj as atendimento_corretor_cpf_cnpj
                atendimento_corretor_usuario.rg as atendimento_corretor_rg
                atendimento_corretor_usuario.id_endereco as atendimento_corretor_id_endereco
                atendimento_corretor_usuario.data_nascimento as atendimento_corretor_data_nascimento
                atendimento_corretor_usuario.tipo as atendimento_corretor_tipo
                atendimento_corretor_usuario.data_cadastro as atendimento_corretor_data_cadastro
                atendimento_corretor_usuario.data_modificacao as atendimento_corretor_data_modificacao
                atendimento_corretor.creci as atendimento_corretor_creci,
               

                atendimento_cliente_usuario.id as atendimento_cliente_id 
                atendimento_cliente_usuario.username as atendimento_cliente_username
                atendimento_cliente_usuario.senha as atendimento_cliente_senha
                atendimento_cliente_usuario.email as atendimento_cliente_email
                atendimento_cliente_usuario.nome as atendimento_cliente_nome
                atendimento_cliente_usuario.cpf_cnpj as atendimento_cliente_cpf_cnpj
                atendimento_cliente_usuario.rg as atendimento_cliente_rg
                atendimento_cliente_usuario.id_endereco as atendimento_cliente_id_endereco
                atendimento_cliente_usuario.data_nascimento as atendimento_cliente_data_nascimento
                atendimento_cliente_usuario.tipo as atendimento_cliente_tipo
                atendimento_cliente_usuario.data_cadastro as atendimento_cliente_data_cadastro
                atendimento_cliente_usuario.data_modificacao as atendimento_cliente_data_modificacao

           
                FROM atendimento 

                LEFT JOIN imovel  
                    ON imovel.id = atendimento.id_imovel
                    
                LEFT JOIN usuario atendimento_usuario_corretor
                    ON atendimento_usuario_corretor.id = atendimento.id_corretor

                LEFT JOIN corretor atendimento_corretor
                    ON atendimento_corretor.id_usuario = atendimento_usuario_corretor.id

                LEFT JOIN usuario atendimento_usuario_captador
                    ON atendimento_usuario_captador.id = atendimento.id_captador

                LEFT JOIN captador atendimento_captador
                    ON atendimento_captador.id_usuario = atendimento_usuario_captador.id

                LEFT JOIN condominio imovel_condominio
                    ON imovel_condominio.id = imovel.id_condominio

                LEFT JOIN endereco imovel_endereco
                    ON imovel_endereco.id = imovel.id_endereco

                LEFT JOIN usuario imovel_usuario_corretor
                    ON imovel_usuario_corretor.id = imovel.id_corretor

                LEFT JOIN corretor imovel_corretor
                    ON imovel_corretor.id_usuario = imovel_usuario_corretor.id

                LEFT JOIN usuario imovel_usuario_captador
                    ON imovel_usuario_captador.id = imovel.id_captador

                LEFT JOIN captador imovel_captador
                    ON imovel_captador.id_usuario = imovel_usuario_captador.id

                LEFT JOIN anuncio imovel_anuncio
                    ON imovel_anuncio.id = imovel.id_anuncio  
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $lista = [];
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
                    $registroImovel = $registro->filter(function ($key) {
                        return strpos($key, 'imovel_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $imovel = $this->imovelDAO->montar($registroImovel);
                }
                if ($idCorretor) {
                    $registroCorretor = $registro->filter(function ($key) {
                        return strpos($key, 'atendimento_corretor_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $corretor = $this->usuarioDAO->montar($registroCorretor);
                }
                if ($idComprador) {
                    $registroComprador = $registro->filter(function ($key) {
                        return strpos($key, 'atendimento_cliente_') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                    $cliente = $this->usuarioDAO->montar($registroComprador);
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
            $erro = "ERRO! atendimentoDAO->listar: " . $e->getMessage();
            error_log($erro);
            return [];
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
                $atendimento = self::montar($dados);
                $listaAtendimentos[] = $atendimento;
            }
            return $listaAtendimentos;
        } catch (Exception $e) {
            $erro = "ERRO! atendimentoDAO->listarPorUsuario: " . $e->getMessage();
            error_log($erro);
            return [];
        }
    }
}
