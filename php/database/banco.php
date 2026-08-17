<?php

error_reporting(E_ALL);

class Banco extends PDO
{

    private static ?Banco $db = null;

    public function __construct(?string $dsn, ?string $username, ?string $password)
    {
        parent::__construct($dsn, $username, $password);
        $this->initTabelas();
    }

    public static function getInstance()
    {
        if (!self::$db) {
            try {
                $servername = "127.0.0.1";
                $username = "root";
                $password = "";
                self::$db = new Banco("mysql:host=$servername", $username, $password);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return self::$db;
            } catch (PDOException $e) {
                error_log($e->getMessage());
                return null;
            }
        }
        return self::$db;
    }

    public function initTabelas()
    {

        $queries = [
            "CREATE DATABASE IF NOT EXISTS imobiliaria 
            CHARACTER SET utf8mb4 
            COLLATE utf8mb4_unicode_ci;",

            "USE imobiliaria;",

            "CREATE TABLE IF NOT EXISTS telefone (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                numero VARCHAR(13) NOT NULL UNIQUE
            )",

            "CREATE TABLE IF NOT EXISTS endereco (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                rua VARCHAR(255) NOT NULL,
                numero INTEGER(10) NULL,
                bairro VARCHAR(255) NOT NULL,
                cep VARCHAR(8) NOT NULL,
                complemento VARCHAR(100) NULL,
                cidade VARCHAR(255) NOT NULL,
                uf VARCHAR(2) NOT NULL,
                unique(cep, numero, complemento)
            )",

            "CREATE TABLE IF NOT EXISTS pessoa (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                email VARCHAR(255) UNIQUE,
                nome VARCHAR(255) NOT NULL,
                cpf_cnpj VARCHAR(14) UNIQUE,
                rg VARCHAR(12),
                id_endereco INTEGER,
                data_nascimento DATE,
                data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_modificacao DATETIME NULL,
                FOREIGN KEY (id_endereco) 
                    REFERENCES endereco(id) 
                    ON DELETE SET NULL
            )",

            "CREATE TABLE IF NOT EXISTS funcionario (
                id_pessoa INTEGER PRIMARY KEY,
                matricula VARCHAR(255) NULL UNIQUE,
                salario REAL NULL,
                data_admissao DATE NULL,
                cargo ENUM ('ADMIN', 'CORRETOR', 'GERENTE', 'CAPTADOR', 'FINANCEIRO', 'VISTORIADOR') NULL,
                FOREIGN KEY (id_pessoa) 
                    REFERENCES pessoa(id) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS usuario (
                id_pessoa INTEGER UNIQUE NOT NULL,
                senha VARCHAR(255) NOT NULL,
                ultimo_login DATETIME NULL,
                ativo BOOLEAN DEFAULT TRUE,
                FOREIGN KEY (id_pessoa) 
                    REFERENCES pessoa(id) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS corretor (
                id_funcionario INTEGER PRIMARY KEY,
                creci TEXT NULL,
                FOREIGN KEY (id_funcionario) 
                    REFERENCES funcionario(id_pessoa) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS proprietario (
                id_pessoa INTEGER PRIMARY KEY,
                FOREIGN KEY (id_pessoa) 
                    REFERENCES pessoa(id) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS telefone_pessoa(
                id_pessoa INTEGER,
                id_telefone INTEGER,
                UNIQUE(id_pessoa, id_telefone),
                FOREIGN KEY (id_pessoa) 
                    REFERENCES pessoa(id) 
                    ON DELETE CASCADE,
                FOREIGN KEY (id_telefone) 
                    REFERENCES telefone(id) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS cliente (
                id_pessoa INTEGER PRIMARY KEY,
                tipo_interesse ENUM('Venda', 'Aluguel', 'Venda e Aluguel') NULL,
                valor_minimo REAL NULL,
                valor_maximo REAL NULL,
                FOREIGN KEY (id_pessoa) 
                    REFERENCES pessoa(id) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS condominio (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(255) NULL,
                id_endereco INTEGER NULL,
                FOREIGN KEY (id_endereco) 
                    REFERENCES endereco(id)
            )",

            "CREATE TABLE IF NOT EXISTS imovel (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                valor_venda REAL NULL,
                valor_aluguel REAL NULL,
                quant_quartos INTEGER NULL,
                quant_salas INTEGER NULL,
                quant_vagas INTEGER NULL,
                quant_banheiros INTEGER NULL,
                quant_varandas INTEGER NULL,
                categoria ENUM('Sala Comercial', 'Apartamento', 'Casa', 'Loja', 'Galpão', 'Cobertura', 'Loft', 'Studio', 'Depósito', 'Pavilhão', 'Prédio Comercial', 'Ponto Comercial', 'Empreendimento', 'Casa em Condomínio', 'Sobrado', 'Sítio', 'Terreno', 'Kitnet', 'Chácara', 'Fazenda') NOT NULL,
                id_endereco INTEGER NULL UNIQUE,
                status ENUM('Venda', 'Aluguel', 'Venda e Aluguel', 'Alugado', 'Vendido', 'Pendente') NOT NULL,
                iptu REAL NULL,
                valor_condominio REAL NULL,
                andar INTEGER NULL,
                estado ENUM('Bom', 'Ótimo', 'Regular') NULL,
                bloco VARCHAR(255) NULL,
                ano_construcao YEAR NULL,
                area_total REAL NULL,
                area_privativa REAL NULL,
                situacao ENUM('Em Construção', 'Novo', 'Usado') NULL,
                ocupacao ENUM('Desocupado', 'Inquilino', 'Proprietário') NULL,
                id_corretor INT NULL,
                id_captador INT NULL,
                data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_modificacao DATETIME NULL,
                id_condominio INT NULL,
                quant_clicks INTEGER DEFAULT 0,
                destacado BOOLEAN DEFAULT FALSE,
                FOREIGN KEY (id_endereco) 
                    REFERENCES endereco(id),
                FOREIGN KEY (id_corretor) 
                    REFERENCES corretor(id_funcionario),
                FOREIGN KEY (id_captador) 
                    REFERENCES funcionario(id_pessoa),
                FOREIGN KEY (id_condominio) 
                    REFERENCES condominio(id)
            )",


            "CREATE TABLE IF NOT EXISTS anuncio (
                id_imovel INTEGER PRIMARY KEY,
                descricao TEXT NULL,
                titulo VARCHAR(255) NULL,
                FOREIGN KEY (id_imovel) 
                    REFERENCES imovel(id) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS favoritos (
                id_cliente INTEGER,
                id_imovel INTEGER,
                UNIQUE(id_cliente, id_imovel),
                FOREIGN KEY (id_cliente) 
                    REFERENCES cliente(id_pessoa) 
                    ON DELETE CASCADE,
                FOREIGN KEY (id_imovel) 
                    REFERENCES imovel(id) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS midia_anuncio (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                id_anuncio INTEGER NULL,
                nome_arquivo VARCHAR(255) NULL,
                tipo ENUM('imagem', 'video', 'documento') NULL,
                UNIQUE(id_anuncio, nome_arquivo, tipo),
                FOREIGN KEY (id_anuncio) 
                    REFERENCES anuncio(id_imovel) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS venda_aluguel (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                id_cliente INT NULL,
                id_proprietario INTEGER,
                id_captador INT NULL,
                id_corretor INT NULL,
                data_venda DATE NULL,
                id_imovel INTEGER  NULL,
                comissao_captador REAL NULL,
                comissao_corretor REAL NULL,
                FOREIGN KEY (id_imovel) 
                    REFERENCES imovel(id),
                FOREIGN KEY (id_cliente) 
                    REFERENCES cliente(id_pessoa),
                FOREIGN KEY (id_proprietario) 
                    REFERENCES proprietario(id_pessoa),
                FOREIGN KEY (id_captador) 
                    REFERENCES funcionario(id_pessoa),
                FOREIGN KEY (id_corretor) 
                    REFERENCES corretor(id_funcionario)
            )",

            "CREATE TABLE IF NOT EXISTS atendimento (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                id_imovel INTEGER  NULL,
                id_corretor INT  NULL,
                id_cliente INT NULL,
                status ENUM('Em Andamento', 'Pendente') NULL,
                FOREIGN KEY (id_imovel) 
                    REFERENCES imovel(id),
                FOREIGN KEY (id_corretor) 
                    REFERENCES corretor(id_funcionario),
                FOREIGN KEY (id_cliente) 
                    REFERENCES cliente(id_pessoa) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS filtro (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(255) NOT NULL UNIQUE                    
            )",


            "CREATE TABLE IF NOT EXISTS imovel_filtros (
                id_filtro INTEGER,
                id_imovel INTEGER, 
                FOREIGN KEY (id_filtro) 
                    REFERENCES filtro (id) 
                    ON DELETE CASCADE,
                FOREIGN KEY (id_imovel) 
                    REFERENCES imovel(id) 
                    ON DELETE CASCADE                
            )",

            "CREATE TABLE IF NOT EXISTS condominio_filtros (
                id_filtro INTEGER,
                id_condominio INTEGER, 
                FOREIGN KEY (id_filtro) 
                    REFERENCES filtro(id) 
                    ON DELETE CASCADE,
                FOREIGN KEY (id_condominio) 
                    REFERENCES condominio(id) 
                    ON DELETE CASCADE               
            )",

            "CREATE TABLE IF NOT EXISTS proprietario_imovel (
                id_proprietario INTEGER NOT NULL,
                id_imovel INTEGER NOT NULL,
                PRIMARY KEY (id_proprietario, id_imovel),
                FOREIGN KEY (id_proprietario)
                    REFERENCES proprietario(id_pessoa)
                    ON DELETE CASCADE,
                FOREIGN KEY (id_imovel)
                    REFERENCES imovel(id)
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS visita (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                id_cliente INTEGER NULL,
                id_imovel INTEGER NULL,
                id_corretor INTEGER NULL,
                data_visita DATETIME NULL,
                status VARCHAR(255) NULL,
                FOREIGN KEY (id_cliente) 
                    REFERENCES cliente(id_pessoa) 
                    ON DELETE CASCADE,
                FOREIGN KEY (id_imovel) 
                    REFERENCES imovel(id) 
                    ON DELETE CASCADE,
                FOREIGN KEY (id_corretor) 
                    REFERENCES corretor(id_funcionario) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS vistoria (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                id_imovel INTEGER NULL,
                data_vistoria DATETIME NULL,
                status VARCHAR(255) NULL,
                FOREIGN KEY (id_imovel) 
                    REFERENCES imovel(id) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS relatorio_vistoria (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                id_vistoria INTEGER NULL,
                descricao TEXT NULL,
                FOREIGN KEY (id_vistoria) 
                    REFERENCES vistoria(id) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS notificacao (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                id_usuario INTEGER NULL,
                mensagem TEXT NULL,
                tipo VARCHAR(255) NULL,
                lida BOOLEAN DEFAULT FALSE,
                data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_usuario) 
                    REFERENCES usuario(id_pessoa) 
                    ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS historico_alteracoes (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                id_funcionario INTEGER NULL,
                id_cliente INTEGER NULL,
                id_imovel INTEGER NULL,
                descricao TEXT NULL,
                data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_funcionario) 
                    REFERENCES funcionario(id) 
                    ON DELETE CASCADE,
                FOREIGN KEY (id_cliente) 
                    REFERENCES pessoa(id) 
                    ON DELETE CASCADE,
                FOREIGN KEY (id_imovel) 
                    REFERENCES imovel(id) 
                    ON DELETE CASCADE
            )",
        ];

        foreach ($queries as $sql) {
            $this->exec($sql);
        }
    }

    public function removerLista($campoDesejado, $listaIDS, $tabela)
    {
        try {
            $sqlDeleteQuery = "
                DELETE FROM $tabela
                WHERE $campoDesejado in IN (" . implode(',', array_map('intval', $listaIDS)) . ")";
            $stmt = $this->prepare($sqlDeleteQuery);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("ERRO Banco->removerLista $tabela - " . implode(',', $listaIDS) . ": " . $e->getMessage());
            return False;
        }
    }

    public function remover($campoDesejado, $valor, $tabela)
    {
        try {
            $sqlDeleteQuery = "
                DELETE FROM $tabela
                WHERE $campoDesejado = ?;
                ";
            $stmt = $this->prepare($sqlDeleteQuery);

            return $stmt->execute([$valor]);
        } catch (Exception $e) {
            error_log("ERRO Banco->remover $tabela - $valor: " . $e->getMessage());
            return False;
        }
    }

    public function atualizar($campoDesejado, $valor, $tabela)
    {
        try {
            $sqlUpdateQuery = "
                UPDATE $tabela
                SET $campoDesejado = ?
                ";
            $stmt = $this->prepare($sqlUpdateQuery);
            $stmt->execute([$valor]);

            return $this->commit();
        } catch (Exception $e) {
            error_log("ERRO Banco->atualizar $tabela - $valor: " . $e->getMessage());
            return False;
        }
    }
}
