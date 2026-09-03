<?php

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class FiltroDAO
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

    public function cadastrar(string $tipo = null, string $nome)
    {
        try {
            $stmt = $this->bancoDados->prepare("INSERT IGNORE INTO filtro (nome) VALUES (:nome)");
            $stmt->execute([':nome' => $nome]);

            $stmt = $this->bancoDados->prepare("SELECT id FROM filtro WHERE nome = :nome");
            $stmt->execute([':nome' => $nome]);
            $idFiltro = $stmt->fetchColumn();

            if ($tipo === 'imovel') {
                $stmt = $this->bancoDados->prepare("INSERT IGNORE INTO imovel_filtros (id_filtro) VALUES (:id_filtro)");
                $stmt->execute([':id_filtro' => $idFiltro]);
            } elseif ($tipo === 'condominio') {
                $stmt = $this->bancoDados->prepare("INSERT IGNORE INTO condominio_filtros (id_filtro) VALUES (:id_filtro)");
                $stmt->execute([':id_filtro' => $idFiltro]);
            }

            return true;
        } catch (Exception $e) {
            error_log("filtroDAO::cadastrar - Error: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar filtro: " . $e->getMessage());
        }
    }

    public function verificar(string $nome, string $tipo, int $id)
    {
        try {
            $stmt = null;
            if ($tipo === 'imovel') {
                $stmt = $this->bancoDados->prepare("SELECT id FROM imovel_filtros WHERE id_filtro = :id_filtro AND id_imovel = :id_imovel");
                $stmt->execute([':id_filtro' => $id, ':id_imovel' => $id]);
            } elseif ($tipo === 'condominio') {
                $stmt = $this->bancoDados->prepare("SELECT id FROM condominio_filtros WHERE id_filtro = :id_filtro AND id_condominio = :id_condominio");
                $stmt->execute([':id_filtro' => $id, ':id_condominio' => $id]);
            }

            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("filtroDAO::verificar - Error: " . $e->getMessage());
            throw new Exception("Erro ao verificar filtro: " . $e->getMessage());
        }
    }


    public function listarPorIdImovel(int $idImovel): array
    {
        try {
            $stmt = $this->bancoDados->prepare(" 
                SELECT
                    f.*
                FROM filtro f
                INNER JOIN imovel_filtros imf
                    ON imf.id_filtro = f.id
                WHERE imf.id_imovel = :id_imovel;
            ");

            $stmt->execute([':id_imovel' => (int) $idImovel]);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $filtros = [];
            foreach ($dados as $registro) {
                $filtro = $registro["nome"];
                if ($filtro !== null) {
                    $filtros[] = $filtro;
                }
            }

            return $filtros;
        } catch (Exception $e) {
            error_log('ERRO! FiltroDAO->listarPorIdImovel: ' . $e->getMessage());
            throw new Exception('Erro ao listar filtros do imóvel: ' . $e->getMessage());
        }
    }

    public function removerDoImovel($filtroExistente, $idImovel)
    {
        try {
            $stmt = $this->bancoDados->prepare("
                DELETE FROM imovel_filtros
                WHERE id_filtro = :id_filtro AND id_imovel = :id_imovel
            ");
            $stmt->execute([
                ':id_filtro' => $filtroExistente,
                ':id_imovel' => $idImovel
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO! FiltroDAO->removerDoImovel: " . $e->getMessage());
            throw new Exception("Erro ao remover filtro do imóvel: " . $e->getMessage());
        }
    }

    public function cadastrarAosFiltros(Imovel | Condominio $imovel)
    {
        try {
            if ($imovel->getFiltros()) {
                foreach ($imovel->getFiltros() as $filtro) {
                    $stmt = $this->bancoDados->prepare("INSERT IGNORE INTO filtro (nome) VALUES (:nome)");
                    $stmt->execute([':nome' => $filtro]);

                    $stmt = $this->bancoDados->prepare("SELECT id FROM filtro WHERE nome = :nome");
                    $stmt->execute([':nome' => $filtro]);
                    $idFiltro = $stmt->fetchColumn();

                    if ($imovel instanceof Imovel) {
                        $stmt = $this->bancoDados->prepare("INSERT IGNORE INTO imovel_filtros (id_filtro, id_imovel) VALUES (:id_filtro, :id_imovel)");
                        $stmt->execute([':id_filtro' => $idFiltro, ':id_imovel' => $imovel->getId()]);
                    } elseif ($imovel instanceof Condominio) {
                        $stmt = $this->bancoDados->prepare("INSERT IGNORE INTO condominio_filtros (id_filtro, id_condominio) VALUES (:id_filtro, :id_condominio)");
                        $stmt->execute([':id_filtro' => $idFiltro, ':id_condominio' => $imovel->getId()]);
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("filtroDAO::cadastrarAosFiltros - Error: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar filtros ao imóvel: " . $e->getMessage());
        }
    }

    public function cadastrarLista(array $listaNomes, string $tipo = null, int $id = null)
    {
        try {
            foreach ($listaNomes as $nome) {
                $stmt = $this->bancoDados->prepare("INSERT IGNORE INTO filtro (nome) VALUES (:nome)");
                $stmt->execute([':nome' => $nome]);

                $stmt = $this->bancoDados->prepare("SELECT id FROM filtro WHERE nome = :nome");
                $stmt->execute([':nome' => $nome]);
                $idFiltro = $stmt->fetchColumn();

                if ($tipo === 'imovel' && $id !== null) {
                    $stmt = $this->bancoDados->prepare("INSERT IGNORE INTO imovel_filtros (id_filtro, id_imovel) VALUES (:id_filtro, :id_imovel)");
                    $stmt->execute([':id_filtro' => $idFiltro, ':id_imovel' => $id]);
                } elseif ($tipo === 'condominio' && $id !== null) {
                    $stmt = $this->bancoDados->prepare("INSERT IGNORE INTO condominio_filtros (id_filtro, id_condominio) VALUES (:id_filtro, :id_condominio)");
                    $stmt->execute([':id_filtro' => $idFiltro, ':id_condominio' => $id]);
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("filtroDAO::cadastrarLista - Error: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar filtro: " . $e->getMessage());
        }
    }

    public function listar(string $tipo = null)
    {
        try {
            if ($tipo === 'imovel') {
                $stmt = $this->bancoDados->prepare("
                    SELECT f.id, f.nome
                    FROM filtro f
                    JOIN imovel_filtros ifi ON f.id = ifi.id_filtro
                ");
            } elseif ($tipo === 'condominio') {
                $stmt = $this->bancoDados->prepare("
                    SELECT f.id, f.nome
                    FROM filtro f
                    JOIN condominio_filtros cfi ON f.id = cfi.id_filtro
                ");
            } else {
                $stmt = $this->bancoDados->prepare("SELECT id, nome FROM filtro");
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("filtroDAO::listar - Error: " . $e->getMessage());
            throw new Exception("Erro ao listar filtros: " . $e->getMessage());
        }
    }

    public  function listarPorId(int $id, string $tipo = null)
    {
        try {
            if ($tipo === 'imovel') {
                $stmt = $this->bancoDados->prepare("
                    SELECT f.id, f.nome
                    FROM filtro f
                    JOIN imovel_filtros ifi ON f.id = ifi.id_filtro
                    WHERE ifi.id_imovel = :id
                ");
            } elseif ($tipo === 'condominio') {
                $stmt = $this->bancoDados->prepare("
                    SELECT f.id, f.nome
                    FROM filtro f
                    JOIN condominio_filtros cfi ON f.id = cfi.id_filtro
                    WHERE cfi.id_condominio = :id
                ");
            } else {
                throw new Exception("Tipo inválido para listar filtros por ID.");
            }

            $stmt->execute([':id' => $id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("filtroDAO::listarPorId - Error: " . $e->getMessage());
            throw new Exception("Erro ao listar filtros por ID: " . $e->getMessage());
        }
    }
}
