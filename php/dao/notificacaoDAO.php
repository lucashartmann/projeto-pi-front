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
                INSERT INTO notificacoes (id_usuario, mensagem, tipo, lida)
                VALUES (:usuario, :mensagem, :tipo, 0)
            ");

            $stmt->execute([
                ':usuario' => $usuario,
                ':mensagem' => $mensagem,
                ':tipo' => $tipo
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO! NotificacaoDAO->cadastrarNotificacao: " . $e->getMessage());
            return false;
        }
    }

    public function marcarComoLida($id)
    {
        try {
            $stmt = $this->bancoDados->prepare("
                UPDATE notificacoes
                SET lida = 1
                WHERE id = :id
            ");

            $stmt->execute([':id' => $id]);

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
                SELECT * FROM notificacoes
                WHERE id_usuario = :id OR tipo = :tipo
                ORDER BY id DESC
            ");

            $stmt->execute([':id' => $usuario->getId(), ':tipo' => $usuario->getTipo() ?? null]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ERRO! NotificacaoDAO->getNotificacoesPorUsuario: " . $e->getMessage());
            return [];
        }
    }
}
