<?php

require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/funcionario.php';

class Seguranca
{
    public static function sanitizarEntrada($entrada)
    {
        return htmlspecialchars(trim($entrada), ENT_QUOTES, 'UTF-8');
    }

    public static function verificarAcesso()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Acesso negado. Faça login para continuar.'
            ]);
        }
    }
}
