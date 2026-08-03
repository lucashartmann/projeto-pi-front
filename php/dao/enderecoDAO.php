<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/endereco.php';

class EnderecoDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function listar()
    {

        try {
            $sql = "SELECT * FROM endereco";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há endereços cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $idEndereco = (int) $registro['id'];
                $rua = $registro['rua'];
                $numero = $registro['numero'] !== null ? (int) $registro['numero'] : null;
                $bairro = $registro['bairro'];
                $cep = $registro['cep'] !== null ? $registro['cep'] : null;
                $complemento = $registro['complemento'];
                $cidade = $registro['cidade'];
                $uf = $registro['uf'];

                $enderecoObj = new Endereco($rua, $bairro, $cep, $cidade, $uf);

                $enderecoObj->setId($idEndereco);
                $enderecoObj->setNumero($numero);
                $enderecoObj->setComplemento($complemento);

                $lista[] = $enderecoObj;
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO! Banco->listar: " . $e->getMessage());
            return [];
        }
    }

    public function verificar($enderecoObj)
    {
        try {

            $sql = "
            SELECT *
            FROM endereco
            WHERE cep = :cep
              AND numero <=> :numero
              AND complemento <=> :complemento
        ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':cep' => $enderecoObj->getCep(),
                ':numero' => $enderecoObj->getNumero(),
                ':complemento' => $enderecoObj->getComplemento()
            ]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe endereço com estes dados.");
            }

            $endereco = new Endereco(
                $registro['rua'],
                $registro['bairro'],
                $registro['cep'],
                $registro['cidade'],
                $registro['uf']
            );

            $endereco->setId((int)$registro['id']);
            $endereco->setNumero($registro['numero']);
            $endereco->setComplemento($registro['complemento']);

            return $endereco;
        } catch (Exception $e) {
            error_log("ERRO! enderecoDAO->verificar: " . $e->getMessage());
            return null;
        }
    }

    public function buscarPorId($id)
    {
        try {


            $stmt = $this->bancoDados->prepare("
            SELECT * FROM endereco 
            WHERE id = ?
        ");
            $stmt->execute([$id]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não há endereço com id {$id}");
            }

            $endereco = new Endereco(
                $registro['rua'],
                $registro['bairro'],
                $registro['cep'],
                $registro['cidade'],
                $registro['uf']
            );

            $endereco->setId((int) $registro['id']);
            $endereco->setNumero((int) $registro['numero']);
            $endereco->setComplemento($registro['complemento']);

            return $endereco;
        } catch (Exception $e) {
            error_log("ERRO! Banco->buscarPorId: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrar($endereco)
    {
        try {


            $sql = "
            INSERT INTO endereco 
            (rua, numero, bairro, cep, complemento, cidade, uf) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

            $stmt = $this->bancoDados->prepare($sql);

            $stmt->execute([
                $endereco->getRua(),
                $endereco->getNumero(),
                $endereco->getBairro(),
                $endereco->getCep(),
                $endereco->getComplemento(),
                $endereco->getCidade(),
                $endereco->getUf()
            ]);

            return $this->bancoDados->lastInsertId();
        } catch (Exception $e) {
            error_log("ERRO! Banco->cadastrar: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar($endereco)
    {
        try {
            $sql = "
            UPDATE endereco 
            SET rua = ?, 
                numero = ?, 
                bairro = ?, 
                cep = ?, 
                complemento = ?, 
                cidade = ?, 
                uf = ? 
            WHERE id = ?
        ";

            $stmt = $this->bancoDados->prepare($sql);

            $stmt->execute([
                $endereco->getRua(),
                $endereco->getNumero(),
                $endereco->getBairro(),
                $endereco->getCep(),
                $endereco->getComplemento(),
                $endereco->getCidade(),
                $endereco->getUf(),
                $endereco->getId()
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO! Banco->atualizar: " . $e->getMessage());
            return false;
        }
    }
}
