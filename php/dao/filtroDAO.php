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

    // "CREATE TABLE IF NOT EXISTS filtro (
    //             id INTEGER PRIMARY KEY AUTO_INCREMENT,
    //             nome VARCHAR(255) NOT NULL UNIQUE                    
    //         )",


    // "CREATE TABLE IF NOT EXISTS imovel_filtros (
    //     id_filtro INTEGER,
    //     id_imovel INTEGER, 
    //     FOREIGN KEY (id_filtro) 
    //         REFERENCES filtro (id) 
    //         ON DELETE CASCADE,
    //     FOREIGN KEY (id_imovel) 
    //         REFERENCES imovel(id) 
    //         ON DELETE CASCADE                
    // )",

    // "CREATE TABLE IF NOT EXISTS condominio_filtros (
    //     id_filtro INTEGER,
    //     id_condominio INTEGER, 
    //     FOREIGN KEY (id_filtro) 
    //         REFERENCES filtro(id) 
    //         ON DELETE CASCADE,
    //     FOREIGN KEY (id_condominio) 
    //         REFERENCES condominio(id) 
    //         ON DELETE CASCADE               
    // )",

    public function cadastrar($tipo = null, $nome)
    {
        try {
            $stmt = $this->bancoDados->prepare("INSERT INTO filtro (nome) VALUES (:nome)");
            $stmt->execute([':nome' => $nome]);
            if ($tipo === 'imovel') {
                $stmt = $this->bancoDados->prepare("INSERT INTO imovel_filtros (id_filtro) VALUES (:id_filtro)");
                $stmt->execute([':id_filtro' => $this->bancoDados->lastInsertId()]);
            } elseif ($tipo === 'condominio') {
                $stmt = $this->bancoDados->prepare("INSERT INTO condominio_filtros (id_filtro) VALUES (:id_filtro)");
                $stmt->execute([':id_filtro' => $this->bancoDados->lastInsertId()]);
            }

            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao cadastrar filtro: " . $e->getMessage());
        }
    }

    public function cadastrarLista($tipo = null, $listaNomes)
    {
        try {
            foreach ($listaNomes as $nome) {
                $stmt = $this->bancoDados->prepare("INSERT INTO filtro (nome) VALUES (:nome)");
                $stmt->execute([':nome' => $nome]);
                if ($tipo === 'imovel') {
                    $stmt = $this->bancoDados->prepare("INSERT INTO imovel_filtros (id_filtro) VALUES (:id_filtro)");
                    $stmt->execute([':id_filtro' => $this->bancoDados->lastInsertId()]);
                } elseif ($tipo === 'condominio') {
                    $stmt = $this->bancoDados->prepare("INSERT INTO condominio_filtros (id_filtro) VALUES (:id_filtro)");
                    $stmt->execute([':id_filtro' => $this->bancoDados->lastInsertId()]);
                }
            }

            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao cadastrar filtro: " . $e->getMessage());
        }
    }

    public function listar($tipo = null)
    {
        try {
            if ($tipo === 'imovel') {
                $stmt = $this->bancoDados->prepare("
                    SELECT f.id, f.nome
                    FROM filtro f
                    JOIN imovel_filtros if ON f.id = if.id_filtro
                ");
            } elseif ($tipo === 'condominio') {
                $stmt = $this->bancoDados->prepare("
                    SELECT f.id, f.nome
                    FROM filtro f
                    JOIN condominio_filtros cf ON f.id = cf.id_filtro
                ");
            } else {
                $stmt = $this->bancoDados->prepare("SELECT id, nome FROM filtro");
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao listar filtros: " . $e->getMessage());
        }
    }
}
