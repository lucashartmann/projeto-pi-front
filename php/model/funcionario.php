<?php

require_once __DIR__ . '/pessoa.php';

enum Cargo: string
{
    case ADMIN = "ADMIN";
    case CORRETOR = "CORRETOR";
    case GERENTE = "GERENTE";
    case CAPTADOR = "CAPTADOR";
    case FINANCEIRO = "FINANCEIRO";
    case VISTORIADOR = "VISTORIADOR";
}

class Funcionario extends Pessoa
{

    protected  ?string $matricula;
    protected  ?float $salario;
    protected  ?DateTime $dataAdmissao;
    protected Cargo $cargo;

    public function __construct(?string $email, string $nome, string $cpfCnpj, Cargo $cargo)
    {
        parent::__construct($email, $nome, $cpfCnpj);
        $this->matricula = NULL;
        $this->salario = NULL;
        $this->dataAdmissao = NULL;
        $this->cargo = $cargo;
    }

    public function getCargo(): Cargo
    {
        return $this->cargo;
    }

    public function setCargo(Cargo $value)
    {
        $this->cargo = $value;
    }

    public function getMatricula(): ?string
    {
        return $this->matricula;
    }

    public function getSalario(): ?float
    {
        return $this->salario;
    }

    public function getDataAdmissao(): ?DateTime
    {
        return $this->dataAdmissao;
    }

    public function setMatricula(?string $value)
    {
        $this->matricula = $value;
    }

    public function setSalario(?float $value)
    {
        $this->salario = $value;
    }

    public function setDataAdmissao(?DateTime $value)
    {
        $this->dataAdmissao = $value;
    }

    public function __toString()
    {
        return "Funcionario: { id: " . $this->id . ", nome: " . $this->nome . ", cpfCnpj: " . $this->cpfCnpj . ", email: " . $this->email . ", matricula: " . ($this->matricula ?? 'null') . ", salario: " . ($this->salario ?? 'null') . ", dataAdmissao: " . ($this->dataAdmissao ? $this->dataAdmissao->format('Y-m-d H:i:s') : 'null') . ", cargo: " . $this->cargo->value . " }";
    }
}
