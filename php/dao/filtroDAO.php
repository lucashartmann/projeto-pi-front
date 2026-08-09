<?php

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

    public function cadastrarLista(array $listaNomes, string $tipo = null)
    {
        try {
            foreach ($listaNomes as $nome) {
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
