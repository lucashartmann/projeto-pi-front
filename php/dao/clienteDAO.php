<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/cliente.php';

class ClienteDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function getConexao()
    {
        return $this->bancoDados;
    }

    public function cadastrar(Cliente $cliente)
    {
        try {
            $sql = "
                INSERT INTO cliente (id_pessoa, tipo_interesse, valor_minimo, valor_maximo)
                VALUES (:id_pessoa, :tipo_interesse, :valor_minimo, :valor_maximo)
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_pessoa' => $cliente->getId(),
                ':tipo_interesse' => $cliente->getTipoInteresse() ? $cliente->getTipoInteresse()->value : null,
                ':valor_minimo' => $cliente->getValorMinimo(),
                ':valor_maximo' => $cliente->getValorMaximo()
            ]);
        } catch (Exception $e) {
            throw new Exception("Erro ao cadastrar cliente: " . $e->getMessage());
        }
    }

    public function atualizar(Cliente $cliente)
    {
        try {
            $sql = "
                UPDATE cliente
                SET tipo_interesse = :tipo_interesse,
                    valor_minimo = :valor_minimo,
                    valor_maximo = :valor_maximo
                WHERE id_pessoa = :id_pessoa
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_pessoa' => $cliente->getId(),
                ':tipo_interesse' => $cliente->getTipoInteresse() ? $cliente->getTipoInteresse()->value : null,
                ':valor_minimo' => $cliente->getValorMinimo(),
                ':valor_maximo' => $cliente->getValorMaximo()
            ]);
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar cliente: " . $e->getMessage());
        }
    }
}