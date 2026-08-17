<?php


require_once __DIR__ . '/../database/banco.php';

class NotificacaoDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function cadastrar(Pessoa $usuario, String $mensagem, String $tipo = null): bool
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
            error_log("ERRO! notificacaoDAO->cadastrar: " . $e->getMessage());
            throw $e;
        }
    }


    public function marcarComoLido(array $listaIds, Pessoa $usuario): bool
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
            error_log("ERRO! notificacaoDAO->marcarComoLido: " . $e->getMessage());
            throw $e;
        }
    }

    public function listarPorUsuario(Pessoa $usuario): array
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
            error_log("ERRO! notificacaoDAO->buscarPorUsuario: " . $e->getMessage());
            throw $e;
        }
    }
}
