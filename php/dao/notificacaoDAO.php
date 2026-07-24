<?php


require_once __DIR__ . '/../database/banco.php';

class NotificacaoDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function cadastrarNotificacao($usuario, $mensagem, $tipo = null)
    {
        try {
            $stmt = $this->bancoDados->prepare("
                INSERT INTO notificacao (id_usuario, mensagem, tipo, lida)
                VALUES (:usuario, :mensagem, :tipo, 0)
            ");

            $stmt->execute([
                ':usuario' => $usuario->getId(),
                ':mensagem' => $mensagem,
                ':tipo' => $tipo
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO! NotificacaoDAO->cadastrarNotificacao: " . $e->getMessage());
            return false;
        }
    }


    public function marcarComoLido($listaIds, $usuario)
    {
        try {
            foreach ($listaIds as $id) {
                $stmt = $this->bancoDados->prepare("
                    UPDATE notificacao
                    SET lida = 1
                    WHERE id = :id AND id_usuario = :id_usuario
                ");

                $stmt->execute([':id' => $id, ':id_usuario' => $usuario->getId()]);
            }

            return true;
        } catch (Exception $e) {
            error_log("ERRO! NotificacaoDAO->marcarComoLida: " . $e->getMessage());
            return false;
        }
    }

    public function getNotificacoesPorUsuario($usuario)
    {
        try {
            $stmt = $this->bancoDados->prepare("
                SELECT * FROM notificacao
                WHERE id_usuario = :id
                ORDER BY id DESC
            ");

            $stmt->execute([':id' => $usuario->getId()]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ERRO! NotificacaoDAO->getNotificacoesPorUsuario: " . $e->getMessage());
            return [];
        }
    }
}
