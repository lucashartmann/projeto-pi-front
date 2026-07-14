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

    public function montarAtendimento($dados)
    {

        $idImovel = $dados['imovel_id'];
        $idCorretor = $dados['atendimento_corretor_id'];
        $idCliente = $dados['cliente_id'];

        if ($idImovel) {
            $imovel = $this->imovelDAO->getImovelPorId($idImovel);
        }

        if ($idCorretor) {
            $corretor = $this->usuarioDAO->getUsuarioPorId($idCorretor);
        }

        if ($idCliente) {
            $cliente = $this->usuarioDAO->getUsuarioPorId($idCliente);
        }

        // TODO: Implementar

        return true;
    }

    public function cadastrarAtendimento($atendimento)
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
            $erro = "ERRO! Banco->cadastrarAtendimento: " . $e->getMessage();
            error_log($erro);
            return False;
        }
    }

    public function getListaAtendimentos()
    {
        try {

            $sql = "
            SELECT * FROM atendimento
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $lista = [];
            foreach ($registros as $registro) {
                $idAtendimento = $registro['id'];
                $imovel = $registro['id_imovel'];
                $corretor = $registro['id_corretor'];
                $comprador = $registro['id_cliente'];
                $status = $registro['status'];
                if ($imovel) {
                    $imovel = $this->imovelDAO->getImovelPorId($imovel);
                }
                if ($corretor) {
                    $corretor = $this->usuarioDAO->getUsuarioPorId($corretor);
                }
                if ($comprador) {
                    $comprador = $this->usuarioDAO->getUsuarioPorId($comprador);
                }
                if ($status) {
                    $status = StatusAtendimento::tryFrom($status);
                }
                $atendimentoObj = new Atendimento();
                $atendimentoObj->setStatus($status);
                $atendimentoObj->setId($idAtendimento);
                $atendimentoObj->setCorretor($corretor);
                $atendimentoObj->setCliente($comprador);
                $atendimentoObj->setImovel($imovel);
                $lista[] = $atendimentoObj;
            }
            return $lista;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getListaAtendimentos: " . $e->getMessage();
            error_log($erro);
            return [];
        }
    }
    public function getAtendimentosPorUsuario(int $idUsuario)
    {
        try {
            $sql = "
            SELECT atendimento.*,  
            
                usuario_corretor.id AS atendimento_corretor_id,
                usuario_corretor.username AS atendimento_corretor_username,
                usuario_corretor.senha AS atendimento_corretor_senha,
                usuario_corretor.email AS atendimento_corretor_email,
                usuario_corretor.nome AS atendimento_corretor_nome,
                usuario_corretor.cpf_cnpj AS atendimento_corretor_cpf_cnpj,
                usuario_corretor.rg AS atendimento_corretor_rg,
                corretor.creci AS atendimento_corretor_creci,

                cliente.id AS cliente_id,
                cliente.username AS cliente_username,
                cliente.senha AS cliente_senha,
                cliente.email AS cliente_email,
                cliente.nome AS cliente_nome,
                cliente.cpf_cnpj AS cliente_cpf_cnpj,
                cliente.rg AS cliente_rg,

                endereco_cliente.id AS endereco_cliente_id,
                endereco_cliente.rua AS endereco_cliente_rua,
                endereco_cliente.numero AS endereco_cliente_numero,
                endereco_cliente.complemento AS endereco_cliente_complemento,
                endereco_cliente.bairro AS endereco_cliente_bairro,
                endereco_cliente.cep AS endereco_cliente_cep,
                endereco_cliente.cidade AS endereco_cliente_cidade,
                endereco_cliente.uf AS endereco_cliente_uf,

                imovel.id AS imovel_id,
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

                endereco_imovel.id AS endereco_id,
                endereco_imovel.rua AS endereco_rua,
                endereco_imovel.numero AS endereco_numero,
                endereco_imovel.complemento AS endereco_complemento,
                endereco_imovel.bairro AS endereco_bairro,
                endereco_imovel.cep AS endereco_cep,
                endereco_imovel.cidade AS endereco_cidade,
                endereco_imovel.uf AS endereco_uf,

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

                usuario_corretor_imovel.id AS corretor_id,
                usuario_corretor_imovel.username AS corretor_username,
                usuario_corretor_imovel.senha AS corretor_senha,
                usuario_corretor_imovel.email AS corretor_email,
                usuario_corretor_imovel.nome AS corretor_nome,
                usuario_corretor_imovel.cpf_cnpj AS corretor_cpf_cnpj,
                usuario_corretor_imovel.rg AS corretor_rg,
                corretor_imovel.creci AS corretor_creci,

                usuario_captador_imovel.id AS captador_id,
                usuario_captador_imovel.username AS captador_username,
                usuario_captador_imovel.senha AS captador_senha,
                usuario_captador_imovel.email AS captador_email,
                usuario_captador_imovel.nome AS captador_nome,
                usuario_captador_imovel.cpf_cnpj AS captador_cpf_cnpj,
                usuario_captador_imovel.rg AS captador_rg,
                captador_imovel.salario AS captador_salario,

                anuncio.id AS anuncio_id,
                anuncio.descricao AS anuncio_descricao,
                anuncio.titulo AS anuncio_titulo

                from atendimento

                LEFT JOIN imovel 
                        ON imovel.id = atendimento.id_imovel

                LEFT JOIN usuario usuario_corretor
                    ON usuario_corretor.id = atendimento.id_corretor

                LEFT JOIN corretor
                    ON corretor.id_usuario = usuario_corretor.id

                LEFT JOIN usuario cliente
                    on cliente.id = atendimento.id_cliente

                LEFT join endereco endereco_cliente
                    ON endereco_cliente.id = cliente.id_endereco

                LEFT JOIN endereco endereco_imovel
                    ON endereco_imovel.id = imovel.id_endereco

                LEFT JOIN condominio 
                    ON condominio.id = imovel.id_condominio

                LEFT JOIN endereco condominio_endereco
                    ON condominio_endereco.id = condominio.id_endereco

                LEFT JOIN usuario usuario_corretor_imovel
                    ON usuario_corretor_imovel.id = imovel.id_corretor

                LEFT JOIN corretor corretor_imovel
                    ON corretor_imovel.id_usuario = usuario_corretor_imovel.id

                LEFT JOIN usuario usuario_captador_imovel
                    ON usuario_captador_imovel.id = imovel.id_captador

                LEFT JOIN captador captador_imovel
                    ON captador_imovel.id_usuario = usuario_captador_imovel.id

                LEFT JOIN anuncio 
                    ON anuncio.id = imovel.id_anuncio

                WHERE atendimento.id_usuario = :id_usuario

            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);
            $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$atendimentos) {
                throw new Exception("Não existem atendimentos para o usuário com id {$idUsuario}");
            }
            $listaAtendimentos = [];
            foreach ($atendimentos as $dados) {
                $atendimento = self::montarAtendimento($dados);
                $listaAtendimentos[] = $atendimento;
            }
            return $listaAtendimentos;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getAtendimentosPorUsuario: " . $e->getMessage();
            error_log($erro);
            return [];
        }
    }
}
