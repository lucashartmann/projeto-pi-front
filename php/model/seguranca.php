<?php

class Seguranca
{
    public static function sanitizarEntrada($entrada)
    {
        return htmlspecialchars(trim($entrada), ENT_QUOTES, 'UTF-8');
    }
    public static function verificarAcesso()
    {
        if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Acesso negado. Faça login para continuar.']);
            exit();
        } else {
            return;
        }
    }
}