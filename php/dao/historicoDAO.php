<?php


require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/historico.php';

class HistoricoDAO
{
 private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function listarHistorico(): array
    {
        try {
            $stmt = $this->bancoDados->prepare("
                SELECT * FROM historico_alteracoes
            ");
            $stmt->execute();
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $historicos = [];
            foreach ($registros as $registro) {
                $id = (int) $registro['id'];
                $alteracao = $registro['alteracao'];
                $dataAlteracao = new DateTime($registro['data_alteracao']);
                // Aqui você pode instanciar os objetos Proprietario, Cliente, Imovel e Usuario conforme necessário
                // Por exemplo:
                // $proprietario = new Proprietario(...);
                // $cliente = new Cliente(...);
                // $imovel = new Imovel(...);
                // $usuario = new Usuario(...);

                // Para este exemplo, vamos passar null para esses objetos
                $historicoObj = new Historico($id, $alteracao, $dataAlteracao, null, null, null, null);
                $historicos[] = $historicoObj;
            }
            return $historicos;
        } catch (Exception $e) {
            error_log("ERRO! historicoDAO->listarHistorico: " . $e->getMessage());
            return [];
        }
    }

    public function cadastrarHistorico(Historico $historico): bool
    {
        try {
            $stmt = $this->bancoDados->prepare("
                INSERT INTO historico_alteracoes (id_usuario,id_cliente, id_proprietario, id_imovel, descricao) 
                VALUES (:id_usuario, :id_cliente, :id_proprietario, :id_imovel, :descricao)
            ");
            $stmt->execute([
                ':id_usuario' => $historico->getUsuario() ? $historico->getUsuario()->getId() : null,
                ':id_cliente' => $historico->getCliente() ? $historico->getCliente()->getId() : null,
                ':id_proprietario' => $historico->getProprietario() ? $historico->getProprietario()->getId() : null,
                ':id_imovel' => $historico->getImovel() ? $historico->getImovel()->getId() : null,
                ':descricao' => $historico->getAlteracao(),
            ]);
            return true;
        } catch (Exception $e) {
            error_log("ERRO! historicoDAO->cadastrarHistorico: " . $e->getMessage());
            return false;
        }
    }
}