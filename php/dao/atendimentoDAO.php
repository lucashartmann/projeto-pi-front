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
                    $imovel = $this->imovelDAO->buscarPorId($imovel);
                }
                if ($corretor) {
                    $corretor = $this->usuarioDAO->buscarPorId($corretor);
                }
                if ($comprador) {
                    $comprador = $this->usuarioDAO->buscarPorId($comprador);
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
