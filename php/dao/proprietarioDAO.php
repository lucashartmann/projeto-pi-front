<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/proprietario.php';

class ProprietarioDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }


    public function atualizar($proprietario)
    {

        try {

            $this->bancoDados->beginTransaction();

            $sql = "
                UPDATE proprietario
                SET email = :email,
                    nome = :nome,
                    cpf_cnpj = :cpf,
                    rg = :rg,
                    id_endereco = :endereco,
                    data_nascimento = :data
                WHERE cpf_cnpj = :cpf_where
            ";

            $endereco = $proprietario->getEndereco();
            $endereco = $endereco ? $endereco->getId() : null;

            $dataNascimento = $proprietario->getDataNascimento();
            $dataNascimento = $dataNascimento
                ? $dataNascimento->format("Y-m-d")
                : null;

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':email' => $proprietario->getEmail(),
                ':nome' => $proprietario->getNome(),
                ':cpf' => $proprietario->getCpfCnpj(),
                ':rg' => $proprietario->getRg(),
                ':endereco' => $endereco,
                ':data' => $dataNascimento,
                ':cpf_where' => $proprietario->getCpfCnpj()
            ]);

            $proprietarioDb = $this->buscarPorCpfCnpj(
                $proprietario->getCpfCnpj()
            );

            $telefonesAntigos = $proprietarioDb ? $proprietarioDb->getTelefones() : [];
            $telefonesNovos = $proprietario->getTelefones() ?: [];

            foreach ($telefonesAntigos as $tel) {
                if (!in_array($tel, $telefonesNovos)) {

                    $stmt = $this->bancoDados->prepare("
                        SELECT id FROM telefone WHERE numero = :numero
                    ");
                    $stmt->execute([':numero' => $tel]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        $id_tel = $row['id'];

                        $stmt = $this->bancoDados->prepare("
                            DELETE FROM telefone_proprietario
                            WHERE id_telefone = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);

                        $stmt = $this->bancoDados->prepare("
                            DELETE FROM telefone
                            WHERE id = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);
                    }
                }
            }

            foreach ($telefonesNovos as $tel) {
                if (!in_array($tel, $telefonesAntigos)) {

                    $stmt = $this->bancoDados->prepare("
                        INSERT INTO telefone (numero) VALUES (:numero)
                    ");
                    $stmt->execute([':numero' => $tel]);

                    $id_tel = $this->bancoDados->lastInsertId();

                    $stmt = $this->bancoDados->prepare("
                        INSERT INTO telefone_proprietario (id_proprietario, id_telefone)
                        VALUES (:id_prop, :id_tel)
                    ");
                    $stmt->execute([
                        ':id_prop' => $proprietario->getId(),
                        ':id_tel' => $id_tel
                    ]);
                }
            }


            return $this->bancoDados->commit();
        } catch (Exception $e) {
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            error_log("ERRO Banco->atualizar: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorCpfCnpj($cpfCnpj)
    {
        try {


            $stmt = $this->bancoDados->prepare("
            SELECT * FROM proprietario 
            WHERE cpf_cnpj = ?
        ");
            $stmt->execute([$cpfCnpj]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe proprietário com CPF/CNPJ {$cpfCnpj}");
            }

            $data = $registro['data_nascimento'];
            if ($data) {
                $data = DateTime::createFromFormat('d-m-Y', $data);
            }

            $proprietario = new Proprietario(
                $registro['email'],
                $registro['nome'],
                $registro['cpf_cnpj']
            );

            $proprietario->setId((int) $registro['id']);
            $proprietario->setDataNascimento($data);
            $proprietario->setRg($registro['rg']);

            return $proprietario;
        } catch (Exception $e) {
            error_log("ERRO! Banco->buscarPorCpfCnpj: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrar($proprietario)
    {
        try {


            $idEndereco = null;
            if ($proprietario->getEndereco()) {
                $idEndereco = $proprietario->getEndereco()->getId();
            }

            $data = $proprietario->getDataNascimento();
            if ($data) {
                $data = $data->format("d-m-Y");
            }

            $sql = "
            INSERT INTO proprietario 
            (email, nome, cpf_cnpj, rg, id_endereco, data_nascimento) 
            VALUES (?, ?, ?, ?, ?, ?)
        ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                $proprietario->getEmail(),
                $proprietario->getNome(),
                $proprietario->getCpfCnpj(),
                $proprietario->getRg(),
                $idEndereco,
                $data
            ]);

            $idProprietario = $this->bancoDados->lastInsertId();

            // Telefones
            if ($proprietario->getTelefones()) {
                foreach ($proprietario->getTelefones() as $telefone) {

                    $stmtTel = $this->bancoDados->prepare("
                    INSERT INTO telefone (numero) VALUES (?)
                ");
                    $stmtTel->execute([$telefone]);

                    $idTelefone = $this->bancoDados->lastInsertId();

                    $stmtRel = $this->bancoDados->prepare("
                    INSERT INTO telefone_proprietario 
                    (id_proprietario, id_telefone) 
                    VALUES (?, ?)
                ");
                    $stmtRel->execute([$idProprietario, $idTelefone]);
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("ERRO! Banco->cadastrar: " . $e->getMessage());
            return false;
        }
    }

    public function listar()
    {

        try {

            $sql = "SELECT * FROM proprietario";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há proprietários cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $id = (int) $registro['id'];
                $email = $registro['email'];
                $nome = $registro['nome'];
                $cpf = $registro['cpf_cnpj'];
                $rg = $registro['rg'];

                $data = $registro['data_nascimento']
                    ? new DateTime($registro['data_nascimento'])
                    : null;

                $obj = new Proprietario($email, $nome, $cpf);

                $obj->setId($id);
                $obj->setRg($rg);
                $obj->setDataNascimento($data);

                $lista[] = $obj;
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO Banco->listar: " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM proprietario WHERE id = ?";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([$id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception("Não existe proprietário com ID $id");
            }
            $proprietario = new Proprietario(
                $registro['email'],
                $registro['nome'],
                $registro['cpf_cnpj']
            );
            $proprietario->setId($registro['id']);
            return $proprietario;
        } catch (Exception $e) {
            error_log("ERRO Banco->buscarPorId: " . $e->getMessage());
            return null;
        }
    }


}