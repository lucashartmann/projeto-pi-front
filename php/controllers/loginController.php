<?php

require_once __DIR__ . '/../dao/pessoaDAO.php';
require_once __DIR__ . '/../dao/notificacaoDAO.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/atendimentoDAO.php';
require_once __DIR__ . '/pessoaController.php';
require_once __DIR__ . '/imovelController.php';
require_once __DIR__ . '/atendimentoController.php';
require_once __DIR__ . '/../utils/env.php';
require_once __DIR__ . '/../utils/email.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/funcionario.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class loginController
{

    function marcarComoLido($dados)
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['usuario'])) {
                return (["status" => "erro", "mensagem" => "Usuário não logado"]);
            }
            $usuario = $_SESSION['usuario'];
            if (!$usuario) {
                return (["status" => "erro", "mensagem" => "Usuário não encontrado"]);
            }
            $listaIds = array_map(function ($notificacao) {
                return $notificacao['id'];
            }, $dados['notificacoes']);
            $notificacaoDAO = new NotificacaoDAO();
            $resultado = $notificacaoDAO->marcarComoLido($listaIds, $usuario);
            if ($resultado) {
                return (["status" => "sucesso", "mensagem" => "Notificações marcadas como lidas com sucesso"]);
            } else {
                return (["status" => "erro", "mensagem" => "Erro ao marcar notificações como lidas"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao marcar notificações como lidas: " . $e->getMessage()]);
        }
    }

    function montarJsonNotificacoes($notificacoes)
    {
        $jsonNotificacoes = [];
        foreach ($notificacoes as $notificacao) {
            $jsonNotificacoes[] = [
                "id" => $notificacao["id"],
                "texto" => $notificacao["mensagem"] ?? $notificacao["texto"] ?? "",
                "tipo" => $notificacao["tipo"],
                "lida" => $notificacao["lida"],
                "data" => $notificacao["data"]
            ];
        }
        return [
            "status" => "sucesso",
            "notificacoes" => $jsonNotificacoes
        ];
    }

    function carregarNotificacoes()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['usuario'])) {
                return (["status" => "erro", "mensagem" => "Usuário não logado"]);
            }
            $usuario = $_SESSION['usuario'];
            if (!$usuario) {
                return (["status" => "erro", "mensagem" => "Usuário não encontrado"]);
            }
            $notificacaoDAO = new NotificacaoDAO();
            $notificacoes = $notificacaoDAO->listarPorUsuario($usuario);
            if (!$notificacoes) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhuma notificação encontrada para o usuário"
                ];
            } else {
                return $this->montarJsonNotificacoes($notificacoes);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao carregar notificações: " . $e->getMessage()]);
        }
    }

    function carregarAtendimentos()
    {

        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['usuario'])) {
                return (["status" => "erro", "mensagem" => "Usuário não logado"]);
            }
            $atendimentoDAO = new AtendimentoDAO();
            $atendimentos = $atendimentoDAO->listarPorUsuario($_SESSION['usuario']->getId());
            if (!$atendimentos) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum atendimento encontrado para o usuário"
                ];
            } else {
                $atendimentoController = new AtendimentoController();
                return $atendimentoController->montarJson($atendimentos);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao carregar atendimentos: " . $e->getMessage()]);
        }
    }

    function carregarFavoritos()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['usuario'])) {
                return (["status" => "erro", "mensagem" => "Usuário não logado"]);
            }
            $imovelDAO = new ImovelDAO();
            $imoveisFavoritos = $imovelDAO->listarFavoritos($_SESSION['usuario']->getId());
            if (!$imoveisFavoritos) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhum imóvel favorito encontrado para o usuário"
                ];
            } else {
                $imovelController = new ImovelController();
                return $imovelController->montarJson($imoveisFavoritos);
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
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $usuario = null;
            if (isset($_SESSION['usuario'])) {
                $usuario = $_SESSION['usuario'];
            } else {
                return (["status" => "erro", "mensagem" => "Usuário não logado"]);
            }

            if (!($usuario instanceof Cliente)) {
                return (["status" => "erro", "mensagem" => "Usuário não é um cliente"]);
            }

            $idCliente = $usuario ? $usuario->getId() : null;
            $idImoveis = $data['id_imoveis'] ?? null;
            if (!$idCliente) {
                return (["status" => "erro", "mensagem" => "ID do cliente ou lista de imóveis inválidos"]);
            }

            if (is_array($idImoveis) && !is_numeric($idImoveis) && count($idImoveis) === 0) {
                return (["status" => "erro", "mensagem" => "Lista de imóveis vazia"]);
            }

            if (!is_array($idImoveis) && !is_numeric($idImoveis)) {
                return (["status" => "erro", "mensagem" => "ID do imóvel inválido"]);
            }

            $imovelDAO = new ImovelDAO();
            $resultado = $imovelDAO->favoritar($idCliente, $idImoveis);
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
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
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
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION['usuario'])) {
                $usuario = $_SESSION['usuario'];
                $pessoaController = new PessoaController();
                $dados = $pessoaController->montarJson([$usuario]);

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
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $usuario = $data['usuario'] ?? '';
            $senha = $data['senha'] ?? '';
            $consulta = null;
            $usuarioDAO = new UsuarioDAO();
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

                    $consulta = $usuarioDAO->verificar($email, "", true);

                    if (!$consulta) {
                        $pessoaController = new PessoaController();
                        $resultado = $pessoaController->atualizar([
                            "nome" => $nome,
                            "email" => $email,
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

                $consulta = $usuarioDAO->verificar($usuario, $senha);
            }

            if ($consulta) {
                $_SESSION['usuario'] = $consulta;
                if ($consulta instanceof Cliente) {
                    $_SESSION['tipo'] = "CLIENTE";
                } else if ($consulta  instanceof Corretor) {
                    $_SESSION['tipo'] = "CORRETOR";
                } else if ($consulta instanceof Funcionario) {
                    $_SESSION['tipo'] = $consulta->getCargo() ?? NULL;
                } else if ($consulta instanceof Proprietario) {
                    $_SESSION['tipo'] = "PROPRIETARIO";
                }
                return ([
                    "status" => "sucesso",
                    "usuario" => [
                        "id" => $consulta->getId(),
                        "nome" => $consulta->getNome(),
                        "tipo" => $_SESSION['tipo'],
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
