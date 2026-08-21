<?php

class Validacao
{

    public static function validarRG(string $rg)
    {
        $rg = preg_replace('/[^0-9]/', '', $rg);
        return preg_match('/^\d{7,9}$/', $rg);
    }

    public static function validarSenha(string $senha)
    {
        return strlen($senha) >= 6;
    }

    public static function validarCreci(string $creci)
    {
        return preg_match('/^[A-Z]{2}-\d{5}$/', $creci);
    }

    public static function validarCPF(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    public static function validarCNPJ(string $cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        $tamanho = strlen($cnpj) - 2;
        $numeros = substr($cnpj, 0, $tamanho);
        $digitos = substr($cnpj, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }
        $resultado = ($soma % 11 < 2) ? 0 : 11 - ($soma % 11);
        if ($resultado != $digitos[0]) {
            return false;
        }
        $tamanho += 1;
        $numeros = substr($cnpj, 0, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }
        $resultado = ($soma % 11 < 2) ? 0 : 11 - ($soma % 11);
        return ($resultado == $digitos[1]);
    }

    public static function validarTelefone(string $telefone)
    {
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        return preg_match('/^\d{10,11}$/', $telefone);
    }

    public static function validarDataNascimento(string $data)
    {
        $data = DateTime::createFromFormat('d/m/Y', $data);
        if (!$data) {
            return false;
        }
        $hoje = new DateTime();
        $idade = $hoje->diff($data)->y;
        return $idade >= 18;
    }

    public static function validarSalario(string $salario)
    {
        $salario = str_replace(['-', 'R$', ' '], '', $salario);
        return is_numeric($salario) && $salario >= 0;
    }

    public static function validarAnoConstrucao(string $ano)
    {
        $ano = (int) $ano;
        $anoAtual = (int) date('Y');
        return $ano > 1800 && $ano <= $anoAtual;
    }

    public static function validarCEP(string $cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        return preg_match('/^\d{8}$/', $cep);
    }

    public static function validarArea(string $area)
    {
        $area = str_replace(['-', 'm2', ' '], '', $area);
        return is_numeric($area) && $area > 0;
    }

    public static function validarValor(string $valor)
    {
        $valor = str_replace(['-', 'R$', ' '], '', $valor);
        return is_numeric($valor) && $valor >= 0;
    }

    public static function validarEmail(string $email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
