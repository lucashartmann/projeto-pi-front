<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/enderecoDAO.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/endereco.php';

class CondominioDAO
{
    private Banco $bancoDados;
    private EnderecoDAO $enderecoDAO;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
        $this->enderecoDAO = new EnderecoDAO();
    }

    public function getIdFiltroCondominioPorNome($nome)
    {
        try {

            $stmt = $this->bancoDados->prepare("
            SELECT id 
            FROM filtros_condominio 
            WHERE nome = :nome
        ");
            $stmt->execute([':nome' => $nome]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? (int) $row['id'] : null;
        } catch (Exception $e) {
            error_log("ERRO condominioDAO->getIdFiltroCondominioPorNome: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrarFiltro($idCondominio, $idFiltro)
    {
        try {

            $stmt = $this->bancoDados->prepare("
            INSERT INTO condominio_filtros (id_filtros_condominio, id_condominio)
            VALUES (:id_filtro, :id_condominio)
        ");


            return $stmt->execute([
                ':id_filtro' => $idFiltro,
                ':id_condominio' => $idCondominio
            ]);
        } catch (Exception $e) {
            error_log("ERRO condominioDAO->cadastrarFiltro: " . $e->getMessage());
            return false;
        }
    }

    public function removerFiltro($idCondominio, $idFiltro)
    {
        try {

            $stmt = $this->bancoDados->prepare("
            DELETE FROM condominio_filtros
            WHERE id_condominio = :id_condominio 
              AND id_filtros_condominio = :id_filtro
        ");


            return $stmt->execute([
                ':id_condominio' => $idCondominio,
                ':id_filtro' => $idFiltro
            ]);
        } catch (Exception $e) {
            error_log("ERRO condominioDAO->removerFiltro: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar($condominio)
    {

        try {

            $this->bancoDados->beginTransaction();


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


            $condominioDb = $this->buscarPorId(
                $condominio->getId()
            );

            $filtrosAntigos = $condominioDb ? $condominioDb->getFiltros() : [];
            $filtrosNovos = $condominio->getFiltros() ?: [];


            foreach ($filtrosAntigos as $filtro) {
                if (!in_array($filtro, $filtrosNovos)) {
                    $id = $this->getIdFiltroCondominioPorNome($filtro);
                    if ($id !== null) {
                        $this->removerFiltro($condominio->getId(), $id);
                    }
                }
            }


            foreach ($filtrosNovos as $filtro) {
                if (!in_array($filtro, $filtrosAntigos)) {
                    $id = $this->getIdFiltroCondominioPorNome($filtro);
                    if ($id !== null) {
                        $this->cadastrarFiltro($condominio->getId(), $id);
                    }
                }
            }
            $this->bancoDados->commit();

            return true;
        } catch (Exception $e) {
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            error_log("ERRO condominioDAO->atualizar: " . $e->getMessage());
            return false;
        }
    }
    public function cadastrar($condominio)
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

            $idCondominio = $this->bancoDados->lastInsertId();

            if ($condominio->getFiltros()) {
                foreach ($condominio->getFiltros() as $filtro) {
                    $stmt = $this->bancoDados->prepare("
                        INSERT IGNORE INTO filtros_condominio (nome)
                        VALUES (:nome)
                    ");
                    $stmt->execute([':nome' => $filtro]);

                    $stmt = $this->bancoDados->prepare("
                        SELECT id
                        FROM filtros_condominio
                        WHERE nome = :nome
                    ");
                    $stmt->execute([':nome' => $filtro]);

                    $idFiltro = $stmt->fetchColumn();

                    $stmt = $this->bancoDados->prepare("
                        INSERT INTO condominio_filtros (id_condominio, id_filtros_condominio)
                        VALUES (:id_condominio, :id_filtro)
                    ");
                    $stmt->execute([
                        ':id_condominio' => $idCondominio,
                        ':id_filtro' => $idFiltro
                    ]);
                }
            }

            return $this->bancoDados->lastInsertId();
        } catch (Exception $e) {
            error_log("ERRO! condominioDAO->cadastrar: " . $e->getMessage());
            return false;
        }
    }


    public function buscarPorId($id)
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
            $erro = "ERRO! condominioDAO->buscarPorId: " . $e->getMessage();
            error_log($erro);
            return null;
        }
    }
    public function buscarPorEndereco($endereco)
    {
        try {
            $stmt = $this->bancoDados->prepare("
                SELECT *,  
                
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
                AND endereco.numero <=> :numero
            ");

            $stmt->execute([
                ':cep' => $endereco->getCep(),
                ':numero' => $endereco->getNumero(),
            ]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe condominio com idEndereco {$endereco->getId()}");
            }

            $idCondominio = (int) $registro['id'];
            $nome = $registro['nome'];
            $condominio_obj = new Condominio($nome, $endereco);
            $condominio_obj->setId($idCondominio);

            return $condominio_obj;
        } catch (Exception $e) {
            $erro = "ERRO! condominioDAO->buscarPorEndereco: " . $e->getMessage();
            error_log($erro);
            return null;
        }
    }
    public function buscarPorImovel($id_imovel)
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
            $enderecoObj = $this->enderecoDAO->buscarPorId($idEndereco);
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
                                SELECT nome FROM filtros_condominio
                                WHERE id_filtros_condominio = ?
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
            $erro = "ERRO! condominioDAO->buscarPorImovel: " . $e->getMessage();
            error_log($erro);
            return NULL;
        }
    }
    public function listarFiltros()
    {
        try {
            $stmt = $this->bancoDados->prepare("
                        SELECT * FROM filtros_condominio 
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
            error_log("ERRO! condominioDAO->listarFiltros: " . $e->getMessage());
            return [];
        }
    }
}
