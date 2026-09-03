<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/enderecoDAO.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/endereco.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class CondominioDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }



    public function atualizar(Condominio $condominio)
    {

        try {

            $sql = "
            UPDATE condominio
            SET nome = :nome
            WHERE id = :id
        ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':nome' => $condominio->getNome(),
                ':id' => $condominio->getId()
            ]);

            return true;
        } catch (Exception $e) {
            error_log("condominioDAO::atualizar - Error: " . $e->getMessage());
            throw $e;
        }
    }
    public function cadastrar(Condominio $condominio)
    {
        try {


            $idEndereco = null;
            if ($condominio->getEndereco()) {
                $idEndereco = $condominio->getEndereco()->getId();
            }

            $sql = "
            INSERT INTO condominio (nome, id_endereco) 
            VALUES (?, ?)
        ";

            $stmt = $this->bancoDados->prepare($sql);


            $stmt->execute([
                $condominio->getNome(),
                $idEndereco
            ]);

            return $this->bancoDados->lastInsertId();
        } catch (Exception $e) {

            error_log("condominioDAO::cadastrar - Error: " . $e->getMessage());
            throw $e;
        }
    }


    public function buscarPorId(int $id)
    {
        try {

            $stmt = $this->bancoDados->prepare("
                SELECT 

                condominio.id as condominio_id,
                condominio.nome,
                condominio.id_endereco,
               
                endereco.id as endereco_id,
                endereco.rua,
                endereco.numero,
                endereco.bairro,
                endereco.cep,
                endereco.complemento,
                endereco.cidade,
                endereco.uf 
                
                FROM condominio 
                LEFT JOIN endereco  ON condominio.id_endereco = endereco.id
                WHERE condominio.id = :id_condominio
            ");
            $stmt->execute([':id_condominio' => $id]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe condominio com id {$id}");
            }

            $idCondominio = (int) $registro['condominio_id'];
            $nome = $registro['nome'];
            $enderecoObj = new Endereco(
                $registro['rua'],
                $registro['bairro'],
                $registro['cep'],
                $registro['cidade'],
                $registro['uf']
            );
            $enderecoObj->setId($registro['endereco_id']);
            $enderecoObj->setNumero($registro['numero']);
            $enderecoObj->setComplemento($registro['complemento']);
            $condominio_obj = new Condominio($nome, $enderecoObj);
            $condominio_obj->setId($idCondominio);

            return $condominio_obj;
        } catch (Exception $e) {
            error_log("condominioDAO::buscarPorId - Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function buscarPorEndereco(Endereco $endereco)
    {
        try {
            $stmt = $this->bancoDados->prepare("
                SELECT 
                
                condominio.id as condominio_id,
                condominio.nome,  
                
                endereco.id as endereco_id,
                endereco.rua,
                endereco.numero,
                endereco.bairro,
                endereco.cep,
                endereco.complemento,
                endereco.cidade,
                endereco.uf 

                FROM condominio 

                LEFT JOIN endereco ON condominio.id_endereco = endereco.id

                WHERE endereco.cep = :cep
                AND endereco.numero = :numero
            ");

            $stmt->execute([
                ':cep' => $endereco->getCep(),
                ':numero' => $endereco->getNumero(),
            ]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                return null;
            }

            $idCondominio = (int) $registro['condominio_id'];
            $nome = $registro['nome'];
            $condominio_obj = new Condominio($nome, $endereco);
            $condominio_obj->setId($idCondominio);

            return $condominio_obj;
        } catch (Exception $e) {
            error_log("condominioDAO::buscarPorEndereco - Error: " . $e->getMessage());
            throw $e;
        }
    }
    public function buscarPorImovel(int $id_imovel)
    {
        try {
            $stmt = $this->bancoDados->prepare("
                        SELECT * FROM condominio 
                        WHERE id_imovel = ?
                    ");
            $stmt->execute([$id_imovel]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception(
                    "Não existe condomínio para o imóvel com id $id_imovel"
                );
            }
            $idCondominio = (int) ($registro);
            $nome = $registro[1];
            $idEndereco = $registro[2];
            $enderecoDAO = new EnderecoDAO();
            $enderecoObj = $enderecoDAO->buscarPorId($idEndereco);
            if (!$enderecoObj) {
                throw new Exception(
                    "Não existe endereço com id $idEndereco"
                );
            }
            $condominio_obj = new Condominio();
            $condominio_obj->setId($idCondominio);
            $condominio_obj->setNome($nome);
            $condominio_obj->setEndereco($enderecoObj);
            $stmt = $this->bancoDados->prepare("
                        SELECT * FROM condominio_filtros
                        WHERE id_condominio = ?
                    ");
            $stmt->execute([$idCondominio]);
            $condominio_filtros = $stmt->fetch(PDO::FETCH_ASSOC);
            $lista_condominio_filtros = [];
            if ($condominio_filtros) {
                foreach ($condominio_filtros as $registro) {
                    $idCondominio_filtros = (int) ($registro);
                    $stmt = $this->bancoDados->prepare("
                                SELECT nome FROM filtros
                                WHERE id_filtro = ?
                            ");
                    $stmt->execute([$idCondominio_filtros]);
                    $nome = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($nome) {
                        $lista_condominio_filtros[] = $nome;
                    }
                }
            }
            if ($lista_condominio_filtros) {
                $condominio_obj->setFiltros($lista_condominio_filtros);
            }
            return $condominio_obj;
        } catch (Exception $e) {
            error_log("condominioDAO::buscarPorImovel - Error: " . $e->getMessage());
            throw $e;
        }
    }
    public function listarFiltros()
    {
        try {
            $stmt = $this->bancoDados->prepare("
                        SELECT * FROM filtros_
                ");
            $stmt->execute();
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $lista = [];

            foreach ($registros as $registro) {
                $nome = $registro['nome'] ?? null;
                if ($nome !== null) {
                    $lista[] = $nome;
                }
            }
            return $lista;
        } catch (Exception $e) {
            error_log("condominioDAO::listarFiltros - Error: " . $e->getMessage());
            throw $e;
        }
    }
}
