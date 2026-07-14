<?php

require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/usuarioController.php';
require_once __DIR__ . '/imovelController.php';
require_once __DIR__ . '/../utils/env.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
// use function PHPUnit\Framework\isInstanceOf;

class loginController
{
    private UsuarioDAO $usuarioDAO;
    private ImovelDAO $imovelDAO;
    private UsuarioController $usuarioController;

    private ImovelController $imovelController;

    public function __construct()
    {
        $this->usuarioDAO = new UsuarioDAO();
        $this->imovelDAO = new ImovelDAO();
        $this->usuarioController = new UsuarioController();
        $this->imovelController = new ImovelController();
    }

    function carregarFavoritos()
    {
        try {
            session_start();
            if (!isset($_SESSION['usuario_id'])) {
                return (["status" => "erro", "mensagem" => "Usuário não logado"]);
            }
            $idCliente = $_SESSION['usuario_id'];
            $imoveisFavoritos = $this->imovelDAO->getImoveisFavoritos($idCliente);
            if (!$imoveisFavoritos) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel favorito encontrado para o usuário"
                ];
            } else {
                return $this->imovelController->montarJsonImoveis($imoveisFavoritos);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao carregar favoritos: " . $e->getMessage()]);
        }
    }

    function favoritarImoveis($data)
    {
        try {
            $body = file_get_contents("php://input");
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return (["status" => "erro", "mensagem" => "JSON inválido"]);
            }
            session_start();
            $usuario = null;
            if (isset($_SESSION['usuario_id'])) {
                $usuario = $this->usuarioDAO->getUsuarioPorId($_SESSION['usuario_id']);
            } else {
                return (["status" => "erro", "mensagem" => "Usuário não logado"]);
            }

            if (!($usuario instanceof Cliente)) {
                return (["status" => "erro", "mensagem" => "Usuário não é um cliente"]);
            }

            $idCliente = $usuario ? $usuario->getId() : null;
            $idImoveis = $data['id_imoveis'] ?? null;
            if (!$idCliente || !is_array($idImoveis)) {
                return (["status" => "erro", "mensagem" => "ID do cliente ou lista de imóveis inválidos"]);
            }

            // if ($idImoveis == array_column($usuario->getImoveisFavoritos(), 'id')) {
            //     return (["status" => "sucesso", "mensagem" => "Imóveis já favoritados"]);
            // }
            $resultado = $this->imovelDAO->cadastrarImoveisCliente($idCliente, $idImoveis);
            if ($resultado) {
                return (["status" => "sucesso", "mensagem" => "Imóveis favoritados com sucesso"]);
            } else {
                return (["status" => "erro", "mensagem" => "Erro ao favoritar imóveis"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao favoritar imóveis: " . $e->getMessage()]);
        }
    }

    function recuperarSenha($data)
    {
        try {
            $email = $data['email'] ?? '';
            if (!$email || !Validacao::validarEmail($email)) {
                return ([
                    "status" => "erro",
                    "mensagem" => "Email inválido ou não fornecido"
                ]);
            }
            $mail = new PHPMailer(true);
            try {
                loadEnv(__DIR__ . '/../../.env');
                $mail->isSMTP();
                $mail->Host = $_ENV['SMTP_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_USERNAME'];
                $mail->Password = $_ENV['SMTP_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $_ENV['PORT'];
                $mail->setFrom($_ENV['SMTP_USERNAME'], 'Summit');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
                $mail->Subject = 'Recuperação de Senha';
                $mail->Body = getArquivo();
                $mail->send();
                return ([
                    "status" => "sucesso",
                    "mensagem" => "Instruções enviadas para o email"
                ]);
            } catch (Exception $e) {
                error_log("Erro ao enviar email: " . $mail->ErrorInfo);
                return ([
                    "status" => "erro",
                    "mensagem" => "Erro ao configurar o PHPMailer"
                ]);
            }
        } catch (Exception $e) {
            return ([
                "status" => "erro",
                "mensagem" => "Erro ao recuperar senha: "
            ]);
        }
    }

    function deslogar()
    {
        try {
            session_start();
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                try {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params['httponly']);
                } catch (Exception $e) {
                    return;
                }
            }
            session_destroy();
            return (["status" => "sucesso", "mensagem" => "Usuário deslogado com sucesso"]);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao deslogar: " . $e->getMessage()]);
        }
    }

    function carregarUsuario()
    {
        try {
            session_start();
            if (isset($_SESSION['usuario_id'])) {
                $usuario = $this->usuarioDAO->getUsuarioPorId($_SESSION['usuario_id']);

                $dados = $this->usuarioController->montarJsonUsuario([$usuario]);

                return ([
                    "status" => "sucesso",
                    "tipo" => $_SESSION['tipo'],
                    "usuario" => is_array($dados) ? $dados[0] : null
                ]);
            } else {
                return ([
                    "status" => "erro",
                    "mensagem" => "Usuário não logado"
                ]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao carregar usuário: " . $e->getMessage()]);
        }
    }

    function verificarLogin($data)
    {

        try {
            session_start();

            $usuario = $data['usuario'] ?? '';
            $senha = $data['senha'] ?? '';
            $consulta = null;

            if (array_key_exists('credential', $data)) {
                $token = $data["credential"];
                $client = new Google_Client([
                    'client_id' => '158912931156-6opb8fsg8d9iscqfuqnm606128e5nceq.apps.googleusercontent.com'
                ]);

                $payload = $client->verifyIdToken($token);

                if ($payload) {

                    $email = $payload['email'];
                    $nome = $payload['name'];
                    $foto = $payload['picture'];

                    $consulta = $this->usuarioDAO->verificarUsuario($email, "", true);

                    if (!$consulta) {
                        $resultado = $this->usuarioController->atualizarUsuario([
                            "nome" => $nome,
                            "email" => $email,
                            "username" => $email,
                            "senha" => "",
                            "tipo" => "CLIENTE"
                        ]);
                        return $resultado;
                    }
                }
            } else {

                if (!$usuario || !$senha) {
                    return (["status" => "erro", "mensagem" => "Usuário ou senha não fornecidos"]);
                }

                $consulta = $this->usuarioDAO->verificarUsuario($usuario, $senha);
            }

            if ($consulta) {
                $_SESSION['usuario_id'] = $consulta->getId();
                $_SESSION['tipo'] = $consulta->getTipo() ?? NULL;
                return ([
                    "status" => "sucesso",
                    "usuario" => [
                        "id" => $consulta->getId(),
                        "nome" => $consulta->getNome(),
                        "tipo" => $consulta->getTipo() ? $consulta->getTipo()->value : null,
                    ]
                ]);
            } else {
                return (["status" => "erro", "mensagem" => "Usuário ou senha incorretos"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            return (["status" => "erro", "mensagem" => "Erro ao verificar login: " . $e->getMessage()]);
        }
    }
}