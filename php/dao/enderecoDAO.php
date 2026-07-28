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
                AND numero = :numero
            ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':cep' => $enderecoObj->getCep(),
                ':numero' => $enderecoObj->getNumero()
            ]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe imóvel com este endereço");
            }

            $idEndereco = (int) $registro['id'];
            $rua = $registro['rua'];
            $numero = $registro['numero'] ? (int) $registro['numero'] : 0;
            $bairro = $registro['bairro'];
            $cep = $registro['cep'] ? (int) $registro['cep'] : "";
            $complemento = $registro['complemento'];
            $cidade = $registro['cidade'];
            $uf = $registro['uf'];

            $endereco_resultado = new Endereco($rua, $bairro, $cep, $cidade, $uf);
            $endereco_resultado->setId($idEndereco);
            $endereco_resultado->setNumero($numero);
            $endereco_resultado->setComplemento($complemento);

            return $endereco_resultado;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->verificar: " . $e->getMessage();
            error_log($erro);
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

}