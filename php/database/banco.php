<?php

require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/captador.php';
require_once __DIR__ . '/../model/atendimento.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/vendaAluguel.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/gerente.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/visita.php';
require_once __DIR__ . '/../model/vistoria.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);
class Banco
{
    private $db;

    public function __construct()
    {
        $servername = "127.0.0.1";
        $username = "root";
        $password = "";
        $dbname = "imobiliaria";

        try {
            $this->db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->initTabelas();
            // error_log("Connected successfully";
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return;
        }
    }

    public function getDb()
    {
        return $this->db;
    }

    public function initTabelas()
    {

        $queries = [
            "CREATE DATABASE IF NOT EXISTS imobiliaria;",

            "CREATE TABLE IF NOT EXISTS usuario (
                id_usuario INTEGER PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(255) UNIQUE,
                senha VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE,
                nome VARCHAR(255) NOT NULL,
                cpf_cnpj VARCHAR(14) UNIQUE NOT NULL,
                rg VARCHAR(12),
                id_endereco INTEGER,
                data_nascimento DATE,
                tipo_usuario VARCHAR(50) NOT NULL
            )",

            "CREATE TABLE IF NOT EXISTS telefone (
                id_telefone INTEGER PRIMARY KEY AUTO_INCREMENT,
                numero VARCHAR(11) NOT NULL UNIQUE
            )",

            "CREATE TABLE IF NOT EXISTS endereco (
                id_endereco INTEGER PRIMARY KEY AUTO_INCREMENT,
                rua VARCHAR(255) NOT NULL,
                numero INTEGER(10) NULL,
                bairro VARCHAR(255) NOT NULL,
                cep VARCHAR(8) NOT NULL,
                complemento VARCHAR(100) NULL,
                cidade VARCHAR(255) NOT NULL,
                uf VARCHAR(2) NOT NULL
            )",

            "CREATE TABLE IF NOT EXISTS proprietario (
                id_proprietario INTEGER PRIMARY KEY AUTO_INCREMENT,
                email VARCHAR(255) UNIQUE NULL,
                nome VARCHAR(255) NOT NULL,
                cpf_cnpj VARCHAR(14) UNIQUE NULL,
                rg VARCHAR(12) NULL,
                id_endereco INTEGER NULL,
                data_nascimento DATE NULL,
                FOREIGN KEY (id_endereco) REFERENCES endereco(id_endereco)
            )",

            "CREATE TABLE IF NOT EXISTS telefone_usuario (
                id_usuario INTEGER,
                id_telefone INTEGER,
                FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
                FOREIGN KEY (id_telefone) REFERENCES telefone(id_telefone) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS telefone_proprietario (
                id_telefone INTEGER,
                id_proprietario INTEGER,
                FOREIGN KEY (id_telefone) REFERENCES telefone(id_telefone) ON DELETE CASCADE,
                FOREIGN KEY (id_proprietario) REFERENCES proprietario (id_proprietario) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS cliente (
                    id_usuario INTEGER PRIMARY KEY,
                    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS captador (
                    id_usuario INTEGER PRIMARY KEY,
                    salario REAL NULL,
                    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS corretor (
                    id_usuario INTEGER PRIMARY KEY,
                    creci TEXT NULL,
                    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
            )",


            "CREATE TABLE IF NOT EXISTS anuncio (
                id_anuncio INTEGER PRIMARY KEY AUTO_INCREMENT,
                descricao VARCHAR(255) NULL,
                titulo VARCHAR(255) NULL
            )",

            "CREATE TABLE IF NOT EXISTS condominio (
                id_condominio INTEGER PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(255) NULL,
                id_endereco INTEGER NULL,
                FOREIGN KEY (id_endereco) REFERENCES endereco(id_endereco)
            )",

            "CREATE TABLE IF NOT EXISTS imovel (
                id_imovel INTEGER PRIMARY KEY AUTO_INCREMENT,
                valor_venda REAL NULL,
                valor_aluguel REAL NULL,
                quant_quartos INTEGER NULL,
                quant_salas INTEGER NULL,
                quant_vagas INTEGER NULL,
                quant_banheiros INTEGER NULL,
                quant_varandas INTEGER NULL,
                categoria VARCHAR(255) NOT NULL,
                id_endereco INTEGER NULL,
                status VARCHAR(255) NOT NULL,
                iptu REAL NULL,
                valor_condominio REAL NULL,
                andar INTEGER NULL,
                estado VARCHAR(255) NULL,
                bloco VARCHAR(255) NULL,
                ano_construcao YEAR NULL,
                area_total REAL NULL,
                area_privativa REAL NULL,
                situacao VARCHAR(255) NULL,
                ocupacao VARCHAR(255) NULL,
                id_corretor INT NULL,
                id_captador INT NULL,
                data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                data_modificacao DATETIME NULL,
                id_anuncio INT NULL,
                id_condominio INT NULL,
                FOREIGN KEY (id_anuncio) REFERENCES anuncio (id_anuncio),
                FOREIGN KEY (id_endereco) REFERENCES endereco(id_endereco),
                FOREIGN KEY (id_corretor) REFERENCES corretor(id_usuario),
                FOREIGN KEY (id_captador) REFERENCES captador(id_usuario),
                FOREIGN KEY (id_condominio) REFERENCES condominio (id_condominio)

            )",

            "CREATE TABLE IF NOT EXISTS midia_anuncio (
                id_midia INTEGER PRIMARY KEY AUTO_INCREMENT,
                id_anuncio INTEGER NULL,
                midia LONGBLOB NULL,
                tipo VARCHAR(255) NULL,
                FOREIGN KEY (id_anuncio) REFERENCES anuncio(id_anuncio) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS venda_aluguel (
                    id_venda INTEGER PRIMARY KEY AUTO_INCREMENT,
                    id_cliente INT NULL,
                    cpf_cnpj_proprietario VARCHAR(14) NULL, 
                    id_captador INT NULL,
                    id_corretor INT NULL,
                    data_venda DATE NULL,
                    id_imovel INTEGER  NULL,
                    comissao_captador REAL NULL,
                    comissao_corretor REAL NULL,
                    FOREIGN KEY (id_imovel) REFERENCES imovel (id_imovel),
                    FOREIGN KEY (id_cliente) REFERENCES cliente (id_usuario),
                    FOREIGN KEY (cpf_cnpj_proprietario) references proprietario (cpf_cnpj),
                    FOREIGN KEY (id_corretor) references corretor (id_usuario)
                    )",

            "CREATE TABLE IF NOT EXISTS gerente (
                    id_usuario INTEGER PRIMARY KEY AUTO_INCREMENT,
                    salario REAL NULL,
                    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
                )",

            "CREATE TABLE IF NOT EXISTS atendimento (
                    id_atendimento INTEGER PRIMARY KEY AUTO_INCREMENT,
                    id_imovel INTEGER  NULL,
                    id_corretor INT  NULL,
                    id_cliente INT NULL,
                    status VARCHAR(255) NULL,
                    FOREIGN KEY (id_imovel) REFERENCES imovel (id_imovel),
                    FOREIGN KEY (id_corretor) references corretor (id_usuario),
                    FOREIGN KEY (id_cliente) references cliente (id_usuario) ON DELETE CASCADE
                )",
            "CREATE TABLE IF NOT EXISTS filtros_imovel (
                    id_filtros_imovel INTEGER PRIMARY KEY AUTO_INCREMENT,
                    nome VARCHAR(255) NOT NULL UNIQUE                    
                )",
            "CREATE TABLE IF NOT EXISTS filtros_condominio
                (
                    id_filtros_condominio INTEGER PRIMARY KEY AUTO_INCREMENT,
                    nome VARCHAR(255) NOT NULL UNIQUE                    
                )",
            "CREATE TABLE IF NOT EXISTS imovel_filtros (
                    id_filtros_imovel INTEGER,
                    id_imovel INTEGER, 
                    FOREIGN KEY (id_filtros_imovel) references filtros_imovel (id_filtros_imovel) ON DELETE CASCADE,
                    FOREIGN KEY (id_imovel) references imovel (id_imovel) ON DELETE CASCADE                
                )",
            "CREATE TABLE IF NOT EXISTS condominio_filtros (
                    id_filtros_condominio INTEGER,
                    id_condominio INTEGER, 
                    FOREIGN KEY (id_filtros_condominio) references filtros_condominio (id_filtros_condominio) ON DELETE CASCADE,
                    FOREIGN KEY (id_condominio) references condominio (id_condominio) ON DELETE CASCADE               
                )",

            "CREATE TABLE IF NOT EXISTS proprietario_imovel (
                    cpf_cnpj_proprietario VARCHAR(14) NULL,
                    id_imovel INTEGER NULL,
                    FOREIGN KEY (cpf_cnpj_proprietario) references proprietario (cpf_cnpj) ON DELETE CASCADE,
                    FOREIGN KEY (id_imovel) references imovel (id_imovel) ON DELETE CASCADE                
                )",

            "CREATE TABLE IF NOT EXISTS visita (
                    id_visita INTEGER PRIMARY KEY AUTO_INCREMENT,
                    id_cliente INTEGER NULL,
                    id_imovel INTEGER NULL,
                    id_corretor INTEGER NULL,
                    data_visita DATETIME NULL,
                    status VARCHAR(255) NULL,
                    FOREIGN KEY (id_cliente) references cliente (id_usuario) ON DELETE CASCADE,
                    FOREIGN KEY (id_imovel) references imovel (id_imovel) ON DELETE CASCADE,
                    FOREIGN KEY (id_corretor) references corretor (id_usuario) ON DELETE CASCADE
                )",

            "CREATE TABLE IF NOT EXISTS vistoria (
                    id_vistoria INTEGER PRIMARY KEY AUTO_INCREMENT,
                    id_imovel INTEGER NULL,
                    data_vistoria DATETIME NULL,
                    status VARCHAR(255) NULL,
                    FOREIGN KEY (id_imovel) references imovel (id_imovel) ON DELETE CASCADE
                )",
            "CREATE TABLE IF NOT EXISTS relatorio_vistoria (
                    id_relatorio INTEGER PRIMARY KEY AUTO_INCREMENT,
                    id_vistoria INTEGER NULL,
                    descricao TEXT NULL,
                    FOREIGN KEY (id_vistoria) references vistoria (id_vistoria) ON DELETE CASCADE
                )"
        ];

        foreach ($queries as $sql) {
            $this->db->exec($sql);
        }
    }

    public function getListaVistoriasPorVistoriador($vistoriador)
    {
        $lista = [];
        $vistorias = $this->db->exec("SELECT * from vistoria WHERE id_vistoriador = $vistoriador");

        foreach ($vistorias as $vistoria) {
            $novaVistoria = new Vistoria();
            $novaVistoria->setId($vistoria['id_vistoria']);
            $novaVistoria->setImovel($this->getImovelPorId($vistoria['id_imovel']));
            $lista[] = $novaVistoria;
        }

        return $lista;
    }

    public function getListaVisitasPorCorretor($corretor)
    {
        $lista = [];
        $visitas = $this->db->exec("SELECT * from visita WHERE id_corretor = $corretor");

        foreach ($visitas as $visita) {
            $novaVisita = new Visita();
            $novaVisita->setId($visita['id_visita']);
            $novaVisita->setImovel($this->getImovelPorId($visita['id_imovel']));
            $novaVisita->setCliente($this->getUsuarioPorId($visita['id_cliente']));
            $lista[] = $novaVisita;
        }

        return $lista;
    }

    public function cadastrarVistoria($vistoria)
    {
        return $this->db->exec("
            INSERT INTO vistoria (id_imovel, data_vistoria, status) 
            VALUES (
                " . ($vistoria->getImovel() ? $vistoria->getImovel()->getId() : "NULL") . ",
                '" . ($vistoria->getDataVistoria() ? $vistoria->getDataVistoria()->format("Y-m-d H:i:s") : "NULL") . "',
                '" . ($vistoria->getStatus() ? $vistoria->getStatus()->value : "NULL") . "'
            )
        ");
    }

    public function cadastrarVisita($visita)
    {
        return $this->db->exec("
            INSERT INTO visita (id_cliente, id_imovel, id_corretor, data_visita, status) 
            VALUES (
                " . ($visita->getCliente() ? $visita->getCliente()->getId() : "NULL") . ",
                " . ($visita->getImovel() ? $visita->getImovel()->getId() : "NULL") . ",
                " . ($visita->getCorretor() ? $visita->getCorretor()->getId() : "NULL") . ",
                '" . ($visita->getDataVisita() ? $visita->getDataVisita()->format("Y-m-d H:i:s") : "NULL") . "',
                '" . ($visita->getStatus() ? $visita->getStatus()->value : "NULL") . "'
            )
        ");
    }

    public function getUsuarioPorId($id)
    {
        try {
            $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception("Não existe usuário com ID $id");
            }
            $idUsuario = $registro['id_usuario'] !== null ? (int)$registro['id_usuario'] : null;
            $username = $registro['username'];
            $senha = $registro['senha'];
            $email = $registro['email'];
            $nome = $registro['nome'];
            $cpfCnpj = $registro['cpf_cnpj'];
            $rg = $registro['rg'];
            $endereco = $registro['id_endereco'] !== null ? $this->getEnderecoPorId((int)$registro['id_endereco']) : null;
            $dataNascimento = $registro['data_nascimento'];
            if ($dataNascimento) {
                $dataNascimento = DateTime::createFromFormat('Y-m-d', $dataNascimento);
            }
            $tipoUsuario = $registro['tipo_usuario'];
            if ($tipoUsuario) {
                $tipoUsuario = Tipo::tryFrom($tipoUsuario);
            }
            $usuarioObj = new Usuario(
                $username,
                $senha,
                $email,
                $nome,
                $cpfCnpj,
                $tipoUsuario
            );
            $sqlQuery = " 
                            SELECT id_telefone FROM telefone_usuario 
                            WHERE id_usuario = :id_usuario
                            ";
            $stmt = $this->db->prepare($sqlQuery);
            $stmt->execute([':id_usuario' => $idUsuario]);
            $registros = $stmt->fetch(PDO::FETCH_ASSOC);
            $telefones = [];
            if ($registros) {
                foreach ($registros as $idTelefone) {
                    $sqlQuery = " 
                            SELECT numero FROM telefone 
                            WHERE id_telefone = :id_telefone
                                ";
                    $stmt = $this->db->prepare($sqlQuery);
                    $stmt->execute([':id_telefone' => $idTelefone]);
                    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            switch ($tipoUsuario) {
                case (Tipo::CORRETOR):
                    $stmt = $this->db->prepare("
                                    SELECT creci FROM corretor 
                                    WHERE id_usuario = :id_usuario
                                ");
                    $stmt->execute([':id_usuario' => $idUsuario]);
                    $creci = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($creci) {
                        $creci = (int)($creci);
                    }
                    $usuarioObj = new Corretor(
                        $username,
                        $senha,
                        $email,
                        $nome,
                        $cpfCnpj,
                        $creci
                    );
                    break;

                case (Tipo::CAPTADOR):
                    $usuarioObj = new Captador(
                        $username,
                        $senha,
                        $email,
                        $nome,
                        $cpfCnpj
                    );
                    $stmt = $this->db->prepare("
                                    SELECT salario FROM captador 
                                    WHERE id_usuario = :id_usuario
                                ");
                    $stmt->execute([':id_usuario' => $idUsuario]);
                    $salario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($salario) {
                        $salario = (float)($salario);
                    }
                    $usuarioObj->setSalario($salario);
                    break;

                case (Tipo::GERENTE):
                    $usuarioObj = new Gerente(
                        $username,
                        $senha,
                        $email,
                        $nome,
                        $cpfCnpj
                    );
                    $stmt = $this->db->prepare("
                                    SELECT salario FROM gerente 
                                    WHERE id_usuario = :id_usuario
                                ");
                    $stmt->execute([':id_usuario' => $idUsuario]);
                    $salario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($salario) {
                        $salario = (float)($salario);
                    }
                    $usuarioObj->setSalario($salario);
                    break;

                case (Tipo::ADMINISTRADOR):
                    $usuarioObj = new Usuario(
                        $username,
                        $senha,
                        $email,
                        $nome,
                        $cpfCnpj,
                        $tipoUsuario
                    );
                    break;

                case (Tipo::CLIENTE):
                    $usuarioObj = new Cliente(
                        $username,
                        $senha,
                        $email,
                        $nome,
                        $cpfCnpj
                    );
                    break;

                    # $stmt = $this->db->prepare("
                    #             SELECT * FROM cliente
                    #             WHERE id_usuario = ?
                    #         ", (idUsuario,))
                    # registros = $stmt->fetch(PDO::FETCH_ASSOC)
            }
            $usuarioObj->setId($idUsuario);
            $usuarioObj->setRg($rg);
            $usuarioObj->setEndereco($endereco);
            $usuarioObj->setDataNascimento($dataNascimento);
            $usuarioObj->setTelefones($telefones);
            return $usuarioObj;
        } catch (Exception $e) {
            error_log("ERRO Banco->getUsuarioPorId: " . $e->getMessage());
            return null;
        }
    }

    public function getListaEnderecos()
    {

        try {
            $sql = "SELECT * FROM endereco";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há endereços cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $idEndereco = (int) $registro['id_endereco'];
                $rua = $registro['rua'];
                $numero = $registro['numero'] !== null ? (int)$registro['numero'] : null;
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
            error_log("ERRO! Banco->getListaEnderecos: "  . $e->getMessage());
            return [];
        }
    }

    public function getListaProprietarios()
    {

        try {

            $sql = "SELECT * FROM proprietario";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há proprietários cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $id = (int)$registro['id_proprietario'];
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
            error_log("ERRO Banco->getListaProprietarios: "  . $e->getMessage());
            return [];
        }
    }
    public function getListaClientes()
    {

        try {

            $sql = "SELECT * FROM usuario WHERE tipoUsuario = 'CLIENTE'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há usuários cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $id = (int)$registro['id_usuario'];
                $username = $registro['username'];
                $senha = $registro['senha'];
                $email = $registro['email'];
                $nome = $registro['nome'];
                $cpf = $registro['cpf_cnpj'];
                $rg = $registro['rg'];

                $endereco = null;
                if ($registro['id_endereco']) {
                    $endereco = $this->getEnderecoPorId((int)$registro['id_endereco']);
                }

                $data = $registro['data_nascimento']
                    ? new DateTime($registro['data_nascimento'])
                    : null;

                $cliente = new Cliente($username, $senha, $email, $nome, $cpf);

                $cliente->setId($id);
                $cliente->setRg($rg);
                $cliente->setEndereco($endereco);
                $cliente->setDataNascimento($data);

                $stmtTel = $this->db->prepare("
                SELECT t.numero
                FROM telefone_usuario tu
                JOIN telefone t ON t.id_telefone = tu.id_telefone
                WHERE tu.id_usuario = :id
                ");
                $stmtTel->execute([':id' => $id]);

                $telefones = [];

                while ($row = $stmtTel->fetch(PDO::FETCH_ASSOC)) {
                    $telefones[] = $row['numero'];
                }

                $cliente->setTelefones($telefones);

                $lista[] = $cliente;
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO Banco->getListaClientes: "  . $e->getMessage());
            return [];
        }
    }
    public function getListaUsuarios()
    {

        try {

            $sql = "SELECT * FROM usuario";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há usuários cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $id = $registro['id_usuario'];
                $username = $registro['username'];
                $senha = $registro['senha'];
                $email = $registro['email'];
                $nome = $registro['nome'];
                $cpf = $registro['cpf_cnpj'];
                $rg = $registro['rg'];
                $tipo = $registro['tipo_usuario'];

                $endereco = null;
                if ($registro['id_endereco']) {
                    $endereco = $this->getEnderecoPorId((int)$registro['id_endereco']);
                }

                $data = $registro['data_nascimento']
                    ? new DateTime($registro['data_nascimento'])
                    : null;

                $stmtTel = $this->db->prepare("
                SELECT t.numero
                FROM telefone_usuario tu
                JOIN telefone t ON t.id_telefone = tu.id_telefone
                WHERE tu.id_usuario = :id
                ");
                $stmtTel->execute([':id' => $id]);

                $telefones = [];
                while ($row = $stmtTel->fetch(PDO::FETCH_ASSOC)) {
                    $telefones[] = $row['numero'];
                }

                switch ($tipo) {

                    case 'CORRETOR':

                        $stmtC = $this->db->prepare("
                        SELECT creci FROM corretor WHERE id_usuario = :id
                    ");
                        $stmtC->execute([':id' => $id]);
                        $creci = $stmtC->fetchColumn();

                        $usuario = new Corretor(
                            $username,
                            $senha,
                            $email,
                            $nome,
                            $cpf,
                            $creci
                        );
                        break;

                    case 'CAPTADOR':

                        $stmtC = $this->db->prepare("
                        SELECT salario FROM captador WHERE id_usuario = :id
                    ");
                        $stmtC->execute([':id' => $id]);
                        $salario = $stmtC->fetchColumn();

                        $usuario = new Captador(
                            $username,
                            $senha,
                            $email,
                            $nome,
                            $cpf
                        );
                        $usuario->setSalario($salario ? (float)$salario : null);
                        break;

                    case 'GERENTE':

                        $stmtC = $this->db->prepare("
                        SELECT salario FROM gerente WHERE id_usuario = :id
                    ");
                        $stmtC->execute([':id' => $id]);
                        $salario = $stmtC->fetchColumn();

                        $usuario = new Gerente(
                            $username,
                            $senha,
                            $email,
                            $nome,
                            $cpf
                        );
                        $usuario->setSalario($salario ? (float)$salario : null);
                        break;

                    case 'CLIENTE':

                        $usuario = new Cliente(
                            $username,
                            $senha,
                            $email,
                            $nome,
                            $cpf
                        );
                        break;

                    default:

                        $usuario = new Usuario(
                            $username,
                            $senha,
                            $email,
                            $nome,
                            $cpf,
                            $tipo
                        );
                        break;
                }

                $usuario->setId($id);
                $usuario->setRg($rg);
                $usuario->setEndereco($endereco);
                $usuario->setDataNascimento($data);
                $usuario->setTelefones($telefones);

                $lista[] = $usuario;
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO Banco->getListaUsuarios: "  . $e->getMessage());
            return [];
        }
    }

    public function cadastrarUsuario($usuario)
    {
        try {
            $sql = "
                    INSERT INTO usuario (username, senha, email, nome, cpf_cnpj, rg, id_endereco, data_nascimento, tipo_usuario) 
                    VALUES(:username, :senha, :email, :nome, :cpf_cnpj, :rg, :endereco, :data_nascimento, :tipo)
                ";
            $stmt = $this->db->prepare($sql);
            if ($usuario->getEndereco()) {
                $endereco = $usuario->getEndereco()->getId();
            } else {
                $endereco = NULL;
            }
            if ($usuario->getTipo()) {
                $tipo = $usuario->getTipo()->value;
            } else {
                $tipo = NULL;
            }
            if ($usuario->getDataNascimento()) {
                $dataNascimento = $usuario->getDataNascimento()->format("Y-m-d");
            } else {
                $dataNascimento = NULL;
            }
            $senha_hash = hash('sha256', $usuario->getSenha());
            $stmt->execute([
                ':username' => $usuario->getUsername(),
                ':senha' => $senha_hash,
                ':email' => $usuario->getEmail(),
                ':nome' => $usuario->getNome(),
                ':cpf_cnpj' => $usuario->getCpfCnpj(),
                ':rg' => $usuario->getRg(),
                ':endereco' => $endereco,
                ':data_nascimento' => $dataNascimento,
                ':tipo' => $tipo
            ]);
            $id = $this->db->lastInsertId();
            if ($usuario->getTelefones()) {
                foreach ($usuario->getTelefones() as $telefone) {
                    $sqlQuery = " 
                            INSERT INTO telefone (numero) 
                            VALUES(:numero)
                            ";
                    $stmt = $this->db->prepare($sqlQuery);
                    $stmt->execute([
                        ':numero' => $telefone,
                    ]);
                    $idTelefone = $this->db->lastInsertId();
                    $sqlQuery = " 
                            INSERT INTO telefone_usuario (id_usuario, id_telefone) 
                            VALUES(:id_usuario, :id_telefone)
                            ";
                    $stmt = $this->db->prepare($sqlQuery);
                    $stmt->execute([
                        ':id_usuario' => $id,
                        ':id_telefone' => $idTelefone
                    ]);
                }
            }
            $tipoUsuarioObj = $usuario->getTipo();
            $tipoUsuarioValor = $tipoUsuarioObj ? $tipoUsuarioObj->value : NULL;
            if ($tipoUsuarioValor == "CORRETOR") {
                $stmt = $this->db->prepare("
                                    INSERT INTO corretor (id_usuario, creci)
                                    VALUES(:id_usuario, :creci)
                                ");
                $stmt->execute([
                    ':id_usuario' => $id,
                    ':creci' => $usuario->getCreci()
                ]);
            } else if ($tipoUsuarioValor == "CAPTADOR") {
                $stmt = $this->db->prepare("
                                    INSERT INTO captador (id_usuario, salario)
                                    VALUES(:id_usuario, :salario)
                                ");
                $stmt->execute([
                    ':id_usuario' => $id,
                    ':salario' => $usuario->getSalario()
                ]);
            } else if ($tipoUsuarioValor == "GERENTE") {
                $stmt = $this->db->prepare("
                                    INSERT INTO gerente (id_usuario, salario)
                                    VALUES(:id_usuario, :salario)
                                ");
                $stmt->execute([
                    ':id_usuario' => $id,
                    ':salario' => $usuario->getSalario()
                ]);
            } else if ($tipoUsuarioValor == "CLIENTE") {
                $stmt = $this->db->prepare("
                                    INSERT INTO cliente (id_usuario)
                                    VALUES(:id_usuario)
                                ");
                $stmt->execute([
                    ':id_usuario' => $id,

                ]);
            }
            return True;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->cadastrarUsuario " . $e->getMessage();
            error_log($erro);
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
            $stmt = $this->db->prepare($sqlDeleteQuery);
            $stmt->execute([$valor]);
            return True;
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
            $stmt = $this->db->prepare($sqlUpdateQuery);
            $stmt->execute([$valor]);
            $this->db->commit();
            return True;
        } catch (Exception $e) {
            error_log("ERRO Banco->atualizar $tabela - $valor: " . $e->getMessage());
            return False;
        }
    }

    public function getUsuarioPorCpfCnpj($cpf)
    {
        try {

            $stmt = $this->db->prepare("
                        SELECT * FROM usuario WHERE cpf_cnpj = ? 
                    ");
            $stmt->execute([$cpf]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception("Não existe usuário com CPF/CNPJ $cpf");
            }
            $idUsuario = $registro['id_usuario'] !== null ? (int)$registro['id_usuario'] : null;
            $username = $registro['username'];
            $senha = $registro['senha'];
            $email = $registro['email'];
            $nome = $registro['nome'];
            $cpfCnpj = $registro['cpf_cnpj'];
            $rg = $registro['rg'];
            $endereco = $registro['id_endereco'];
            if ($endereco) {
                $endereco = $this->getEnderecoPorId((int)($registro['id_endereco']));
            }
            $dataNascimento = $registro['data_nascimento'];
            if ($dataNascimento) {

                $dataNascimento = DateTime::createFromFormat('Y-m-d', $dataNascimento);
            }
            $tipoUsuario = $registro['tipo_usuario'];
            if ($tipoUsuario) {
                $tipoUsuario = Tipo::tryFrom($tipoUsuario);
            }
            $usuarioObj = new Usuario(
                $username,
                $senha,
                $email,
                $nome,
                $cpfCnpj,
                $tipoUsuario
            );
            $sqlQuery = " 
                            SELECT id_telefone FROM telefone_usuario 
                            WHERE id_usuario = ?
                            ";
            $stmt = $this->db->prepare($sqlQuery);
            $stmt->execute([$idUsuario]);
            $registros = $stmt->fetch(PDO::FETCH_ASSOC);
            $telefones = [];
            if ($registros) {
                foreach ($registros as $idTelefone) {
                    $sqlQuery = " 
                            SELECT numero FROM telefone 
                            WHERE id_telefone = ?
                                ";
                    $stmt = $this->db->prepare($sqlQuery);
                    $stmt->execute([$idTelefone]);
                    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            switch ($tipoUsuario) {
                case (Tipo::CORRETOR):
                    $stmt = $this->db->prepare("
                                    SELECT creci FROM corretor 
                                    WHERE id_usuario = ?
                                ");
                    $stmt->execute([$idUsuario]);
                    $creci = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($creci) {
                        $creci = (int)($creci);
                    }
                    $usuarioObj = new Corretor(
                        $username,
                        $senha,
                        $email,
                        $nome,
                        $cpfCnpj,
                        $creci
                    );
                    break;

                case (Tipo::CAPTADOR):
                    $usuarioObj = new Captador(
                        $username,
                        $senha,
                        $email,
                        $nome,
                        $cpfCnpj
                    );
                    $stmt = $this->db->prepare("
                                    SELECT salario FROM captador 
                                    WHERE id_usuario = ?
                                ");
                    $stmt->execute([$idUsuario]);
                    $salario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($salario) {
                        $salario = (float)($salario);
                    }
                    $usuarioObj->setSalario($salario);
                    break;

                case (Tipo::GERENTE):
                    $usuarioObj = new Gerente(
                        $username,
                        $senha,
                        $email,
                        $nome,
                        $cpfCnpj
                    );
                    $stmt = $this->db->prepare("
                                    SELECT salario FROM gerente 
                                    WHERE id_usuario = ?
                                ");
                    $stmt->execute([$idUsuario]);
                    $salario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($salario) {
                        $salario = (float)($salario);
                    }
                    $usuarioObj->setSalario($salario);
                    break;

                case (Tipo::CLIENTE):
                    $usuarioObj = new Cliente(
                        $username,
                        $senha,
                        $email,
                        $nome,
                        $cpfCnpj
                    );
                    break;

                    # $stmt = $this->db->prepare("
                    #             SELECT * FROM cliente
                    #             WHERE id_usuario = ?
                    #         ", (idUsuario,))
                    # registros = $stmt->fetch(PDO::FETCH_ASSOC)
            }
            $usuarioObj->setId($idUsuario);
            $usuarioObj->setRg($rg);
            $usuarioObj->setEndereco($endereco);
            $usuarioObj->setDataNascimento($dataNascimento);
            $usuarioObj->setTelefones($telefones);
            return $usuarioObj;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getUsuarioPorCpfCnpj: " . $e->getMessage();
            error_log($erro);
            return NULL;
        }
    }
    public function getListaFiltrosApartamento()
    {
        try {

            $stmt = $this->db->prepare("
                        SELECT * FROM filtros_imovel 
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
            error_log("ERRO! Banco->getListaFiltrosApartamento: " . $e->getMessage());
            return [];
        }
    }

    public function getListaFiltrosCondominio()
    {
        try {
            $stmt = $this->db->prepare("
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
            error_log("ERRO! Banco->getListaFiltrosCondominio: " . $e->getMessage());
            return [];
        }
    }

    public function cadastrarListaFiltros($lista_filtros, $tabela)
    {
        foreach ($lista_filtros as $filtro) {
            try {
                $sqlQuery = " 
                            INSERT INTO $tabela (nome) 
                            VALUES(:nome)
                            ";
                $stmt = $this->db->prepare($sqlQuery);
                $stmt->execute([':nome' => $filtro]);
            } catch (Exception $e) {
                $erro = "ERRO! Banco->cadastrarListaFiltros: " . $e->getMessage();
                // error_log($erro);
                continue;
            }
        }
    }

    public function getCondominioPorIdImovel($id_imovel)
    {
        try {
            $stmt = $this->db->prepare("
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
            $idCondominio = (int)($registro);
            $nome = $registro[1];
            $idEndereco = $registro[2];
            $enderecoObj = $this->getEnderecoPorId($idEndereco);
            if (!$enderecoObj) {
                throw new Exception(
                    "Não existe endereço com id $idEndereco"
                );
            }
            $condominio_obj = new Condominio();
            $condominio_obj->setId($idCondominio);
            $condominio_obj->setNome($nome);
            $condominio_obj->setEndereco($enderecoObj);
            $stmt = $this->db->prepare("
                        SELECT * FROM condominio_filtros
                        WHERE id_condominio = ?
                    ");
            $stmt->execute([$idCondominio]);
            $condominio_filtros = $stmt->fetch(PDO::FETCH_ASSOC);
            $lista_condominio_filtros = [];
            if ($condominio_filtros) {
                foreach ($condominio_filtros as $registro) {
                    $idCondominio_filtros = (int)($registro);
                    $stmt = $this->db->prepare("
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
            $erro = "ERRO! Banco->getCondominioPorIdImovel: " . $e->getMessage();
            error_log($erro);
            return NULL;
        }
    }


    public function cadastrarAtendimento($atendimento)
    {
        try {

            $sqlQuery = " 
                    INSERT INTO atendimento (id_imovel, id_corretor, id_cliente, status) 
                    VALUES(:id_imovel, :id_corretor, :id_cliente, :status)
                    ";
            $corretor_obj = $atendimento->getCorretor();
            if ($corretor_obj) {
                $corretor_obj = $corretor_obj->getId();
            }
            $cliente_obj = $atendimento->getCliente();
            if ($cliente_obj) {
                $cliente_obj = $cliente_obj->getId();
            }

            $imovelObj = $atendimento->getImovel();
            if ($imovelObj) {
                $imovelObj = $imovelObj->getId();
            }
            $status = $atendimento->getStatus();
            if ($status) {
                $status = $status->value;
            }
            $stmt = $this->db->prepare($sqlQuery);
            $stmt->execute([
                ":id_imovel" => $imovelObj,
                ":id_corretor" => $corretor_obj,
                ":id_cliente" => $cliente_obj,
                ":status" => $status
            ]);
            return True;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->cadastrarAtendimento: " . $e->getMessage();
            error_log($erro);
            return False;
        }
    }

    public function getListaAtendimentos()
    {
        try {

            $sql = "
            SELECT * FROM atendimento
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $lista = [];
            foreach ($registros as $registro) {
                $idAtendimento = $registro['id_atendimento'];
                $imovel = $registro['id_imovel'];
                $corretor = $registro['id_corretor'];
                $comprador = $registro['id_cliente'];
                $status = $registro['status'];
                if ($imovel) {
                    $imovel = $this->getImovelPorId($imovel);
                }
                if ($corretor) {
                    $corretor = $this->getUsuarioPorId($corretor);
                }
                if ($comprador) {
                    $comprador = $this->getUsuarioPorId($comprador);
                }
                if ($status) {
                    $status =  StatusAtendimento::tryFrom($status);
                }
                $atendimentoObj = new Atendimento();
                $atendimentoObj->setStatus($status);
                $atendimentoObj->setId($idAtendimento);
                $atendimentoObj->setCorretor($corretor);
                $atendimentoObj->setCliente($comprador);
                $atendimentoObj->setImovel($imovel);
                $lista[] = $atendimentoObj;
            }
            return $lista;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getListaAtendimentos: " . $e->getMessage();
            error_log($erro);
            return [];
        }
    }

    public function getAnuncioPorId($idAnuncio)
    {
        try {

            $stmt = $this->db->prepare("
                        SELECT * FROM anuncio
                        WHERE id_anuncio = ?
                    ");
            $stmt->execute([$idAnuncio]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception("Não existe anúncio com id $idAnuncio");
            }
            $anuncioObj = new Anuncio();
            $idAnuncio = $registro['id_anuncio'];
            if ($idAnuncio) {
                $idAnuncio = (int)($idAnuncio);
            }
            $descricao = $registro['descricao'];
            $titulo = $registro['titulo'];
            $anuncioObj->setId($idAnuncio);
            $anuncioObj->setDescricao($descricao);
            $anuncioObj->setTitulo($titulo);
            $mapaAnexos = $this->getListaAnexosPorIdAnuncio($idAnuncio);
            if ($mapaAnexos && isset($mapaAnexos["Imagens"])) {
                $anuncioObj->setImagens($mapaAnexos["Imagens"]);
            }
            if ($mapaAnexos && isset($mapaAnexos["Videos"])) {
                $anuncioObj->setVideos($mapaAnexos["Videos"]);
            }
            if ($mapaAnexos && isset($mapaAnexos["Documentos"])) {
                $anuncioObj->setAnexos($mapaAnexos["Documentos"]);
            }
            return $anuncioObj;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getAnuncioPorId: " . $e->getMessage();
            error_log($erro);
            return NULL;
        }
    }

    public function cadastrarAnexo($idAnuncio, $blob, $tipo)
    {
        try {
            $sqlQuery = " 
                    INSERT INTO midia_anuncio (id_anuncio, midia, tipo) 
                    VALUES(:id_anuncio, :midia, :tipo)
                    ";
            $stmt = $this->db->prepare($sqlQuery);
            $stmt->execute([
                ':id_anuncio' => $idAnuncio,
                ':midia' => $blob,
                ':tipo' => $tipo
            ]);
            return True;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->cadastrarAnexo: " . $e->getMessage();
            error_log($erro);
            return False;
        }
    }

    public function getListaAnexosPorIdAnuncio($idAnuncio)
    {
        try {


            $stmt = $this->db->prepare("
                        SELECT * FROM midia_anuncio 
                        WHERE id_anuncio = :id_anuncio
                    ");
            $stmt->execute([':id_anuncio' => $idAnuncio]);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $imagens = [];
            $videos = [];
            $documentos = [];
            foreach ($registros as $registro) {
                $id = $registro['id_anuncio'];
                $id = $registro['id_midia'];
                $tipo = $registro['tipo'];
                if ($tipo == "Imagem") {
                    $imagens[] = $id;
                } else if ($tipo == "Documento") {
                    $documentos[] = $id;
                } else if ($tipo == "Video") {
                    $videos[] = $id;
                }
            }
            $mapa = [];
            $mapa["Imagens"] = $imagens;
            $mapa["Videos"] = $videos;
            $mapa["Documentos"] = $documentos;
            return $mapa;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getListaAnexosPorIdAnuncio: " . $e->getMessage();
            error_log($erro);
            return [];
        }
    }

    public function getCondominioPorIdEndereco($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM condominio 
                WHERE id_endereco = :id_endereco
            ");

            $stmt->execute([':id_endereco' => $id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe condominio com idEndereco {$id}");
            }

            $idCondominio = (int)$registro['id_condominio'];
            $nome = $registro['nome'];
            $idEndereco = (int)$registro['id_endereco'];

            $enderecoObj = $this->getEnderecoPorId($idEndereco);

            $condominio_obj = new Condominio($nome, $enderecoObj);
            $condominio_obj->setId($idCondominio);

            return $condominio_obj;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getCondominioPorIdEndereco: "  . $e->getMessage();
            error_log($erro);
            return null;
        }
    }

    public function getCondominioPorId($id)
    {
        try {

            $stmt = $this->db->prepare("
                SELECT * FROM condominio 
                WHERE id_condominio = :id_condominio
            ");
            $stmt->execute([':id_condominio' => $id]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe condominio com id {$id}");
            }

            $idCondominio = (int)$registro['id_condominio'];
            $nome = $registro['nome'];
            $idEndereco = (int)$registro['id_endereco'];

            $enderecoObj = $this->getEnderecoPorId($idEndereco);

            $condominio_obj = new Condominio($nome, $enderecoObj);
            $condominio_obj->setId($idCondominio);

            return $condominio_obj;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getCondominioPorId: "  . $e->getMessage();
            error_log($erro);
            return null;
        }
    }

    public function verificarEndereco($enderecoObj)
    {
        try {

            $sql = "
                SELECT * 
                FROM endereco 
                WHERE cep = :cep
                AND numero = :numero
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':cep' => $enderecoObj->getCep(),
                ':numero' => $enderecoObj->getNumero()
            ]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe imóvel com este endereço");
            }

            $idEndereco = (int)$registro['id_endereco'];
            $rua = $registro['rua'];
            $numero = $registro['numero'] ? (int)$registro['numero'] : null;
            $bairro = $registro['bairro'];
            $cep = $registro['cep'] ? (int)$registro['cep'] : null;
            $complemento = $registro['complemento'];
            $cidade = $registro['cidade'];
            $uf = $registro['uf'];

            $endereco_resultado = new Endereco($rua, $bairro, $cep, $cidade, $uf);
            $endereco_resultado->setId($idEndereco);
            $endereco_resultado->setNumero($numero);
            $endereco_resultado->setComplemento($complemento);

            return $endereco_resultado;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->verificarEndereco: "  . $e->getMessage();
            error_log($erro);
            return null;
        }
    }

    public function verificarUsuario($username, $senha)
    {
        try {

            $stmt = $this->db->prepare("
            SELECT * FROM usuario WHERE username = :username
        ");
            $stmt->execute([':username' => $username]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Usuário não encontrado");
            }

            $senha_hash_banco = $registro['senha'];


            $senha_hash = hash('sha256', $senha);

            if ($senha_hash_banco !== $senha_hash) {
                throw new Exception("Senha errada!");
            }


            $idUsuario = (int)$registro['id_usuario'];
            $username = $registro['username'];
            $email = $registro['email'];
            $nome = $registro['nome'];
            $cpfCnpj = $registro['cpf_cnpj'];
            $rg = $registro['rg'];
            $idEndereco = $registro['id_endereco'];
            $dataNascimento = $registro['data_nascimento'];
            $tipo = $registro['tipo_usuario'];


            $endereco = null;
            if ($idEndereco) {
                $endereco = $this->getEnderecoPorId($idEndereco);
            }


            if ($dataNascimento) {
                $dataNascimento = DateTime::createFromFormat('d-m-Y', $dataNascimento);
            }


            $telefones = [];

            $stmt = $this->db->prepare("
            SELECT id_telefone FROM telefone_usuario 
            WHERE id_usuario = ?
        ");
            $stmt->execute([$idUsuario]);

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($registros as $row) {
                $idTelefone = $row['id_telefone'];

                $stmtTel = $this->db->prepare("
                SELECT numero FROM telefone 
                WHERE id_telefone = ?
            ");
                $stmtTel->execute([$idTelefone]);

                $tel = $stmtTel->fetch(PDO::FETCH_ASSOC);
                if ($tel) {
                    $telefones[] = $tel['numero'];
                }
            }

            switch ($tipo) {

                case 'CORRETOR':
                    $stmt = $this->db->prepare("
                    SELECT creci FROM corretor 
                    WHERE id_usuario = ?
                ");
                    $stmt->execute([$idUsuario]);

                    $creci = $stmt->fetchColumn();
                    $creci = $creci ? (int)$creci : null;

                    $usuarioObj = new Corretor(
                        $username,
                        $senha_hash_banco,
                        $email,
                        $nome,
                        $cpfCnpj,
                        $creci
                    );
                    break;

                case 'CAPTADOR':
                    $usuarioObj = new Captador(
                        $username,
                        $senha_hash_banco,
                        $email,
                        $nome,
                        $cpfCnpj
                    );

                    $stmt = $this->db->prepare("
                    SELECT salario FROM captador 
                    WHERE id_usuario = ?
                ");
                    $stmt->execute([$idUsuario]);

                    $salario = $stmt->fetchColumn();
                    if ($salario) {
                        $usuarioObj->setSalario((float)$salario);
                    }
                    break;

                case 'GERENTE':
                    $usuarioObj = new Gerente(
                        $username,
                        $senha_hash_banco,
                        $email,
                        $nome,
                        $cpfCnpj
                    );

                    $stmt = $this->db->prepare("
                    SELECT salario FROM gerente 
                    WHERE id_usuario = ?
                ");
                    $stmt->execute([$idUsuario]);

                    $salario = $stmt->fetchColumn();
                    if ($salario) {
                        $usuarioObj->setSalario((float)$salario);
                    }
                    break;

                case 'CLIENTE':
                    $usuarioObj = new Cliente(
                        $username,
                        $senha_hash_banco,
                        $email,
                        $nome,
                        $cpfCnpj
                    );
                    break;

                case "ADMIN":
                    $usuarioObj = new Usuario(
                        $username,
                        $senha_hash_banco,
                        $email,
                        $nome,
                        $cpfCnpj,
                        $tipo
                    );
                    break;

                default:
                    throw new Exception("Tipo de usuário desconhecido: $tipo");
            }

            $usuarioObj->setEndereco($endereco);
            $usuarioObj->setDataNascimento($dataNascimento);
            $usuarioObj->setRg($rg);
            $usuarioObj->setId($idUsuario);
            $usuarioObj->setTelefones($telefones);

            return $usuarioObj;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->verificarUsuario: "  . $e->getMessage();
            error_log($erro);
            return null;
        }
    }

    public function cadastrarEndereco($endereco)
    {
        try {


            $sql = "
            INSERT INTO endereco 
            (rua, numero, bairro, cep, complemento, cidade, uf) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $endereco->getRua(),
                $endereco->getNumero(),
                $endereco->getBairro(),
                $endereco->getCep(),
                $endereco->getComplemento(),
                $endereco->getCidade(),
                $endereco->getUf()
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO! Banco->cadastrarEndereco: " . $e->getMessage());
            return false;
        }
    }

    public function cadastrarCondominio($condominio)
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

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $condominio->getNome(),
                $idEndereco
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO! Banco->cadastrarCondominio: " . $e->getMessage());
            return false;
        }
    }

    public function cadastrarProprietario($proprietario)
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

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $proprietario->getEmail(),
                $proprietario->getNome(),
                $proprietario->getCpfCnpj(),
                $proprietario->getRg(),
                $idEndereco,
                $data
            ]);

            $id_proprietario = $this->db->lastInsertId();

            // Telefones
            if ($proprietario->getTelefones()) {
                foreach ($proprietario->getTelefones() as $telefone) {

                    $stmtTel = $this->db->prepare("
                    INSERT INTO telefone (numero) VALUES (?)
                ");
                    $stmtTel->execute([$telefone]);

                    $idTelefone = $this->db->lastInsertId();

                    $stmtRel = $this->db->prepare("
                    INSERT INTO telefone_proprietario 
                    (id_proprietario, id_telefone) 
                    VALUES (?, ?)
                ");
                    $stmtRel->execute([$id_proprietario, $idTelefone]);
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("ERRO! Banco->cadastrarProprietario: " . $e->getMessage());
            return false;
        }
    }

    public function cadastrarAnuncio($anuncio)
    {
        try {


            $sql = "
            INSERT INTO anuncio (descricao, titulo) 
            VALUES (?, ?)
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $anuncio->getDescricao(),
                $anuncio->getTitulo()
            ]);

            $idAnuncio = $this->db->lastInsertId();

            // Imagens
            if ($anuncio->getImagens()) {
                foreach ($anuncio->getImagens() as $img) {
                    $this->cadastrarAnexo($idAnuncio, $img, "Imagem");
                }
            }

            // Vídeos
            if ($anuncio->getVideos()) {
                foreach ($anuncio->getVideos() as $video) {
                    $this->cadastrarAnexo($idAnuncio, $video, "Video");
                }
            }

            // Documentos
            if ($anuncio->getAnexos()) {
                foreach ($anuncio->getAnexos() as $anexo) {
                    $this->cadastrarAnexo($idAnuncio, $anexo, "Documento");
                }
            }

            return $idAnuncio;
        } catch (Exception $e) {
            error_log("ERRO! Banco->cadastrarAnuncio: " . $e->getMessage());
            return false;
        }
    }

    public function getEnderecoPorId($id)
    {
        try {


            $stmt = $this->db->prepare("
            SELECT * FROM endereco 
            WHERE id_endereco = ?
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

            $endereco->setId((int)$registro['id_endereco']);
            $endereco->setNumero((int)$registro['numero']);
            $endereco->setComplemento($registro['complemento']);

            return $endereco;
        } catch (Exception $e) {
            error_log("ERRO! Banco->getEnderecoPorId: " . $e->getMessage());
            return null;
        }
    }

    public function getProprietarioPorCpfCnpj($cpfCnpj)
    {
        try {


            $stmt = $this->db->prepare("
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

            $proprietario->setId((int)$registro['id_proprietario']);
            $proprietario->setDataNascimento($data);
            $proprietario->setRg($registro['rg']);

            return $proprietario;
        } catch (Exception $e) {
            error_log("ERRO! Banco->getProprietarioPorCpfCnpj: " . $e->getMessage());
            return null;
        }
    }


    public function cadastrarImovel($imovel)
    {
        try {


            $this->db->beginTransaction();

            $sql = "
            INSERT INTO imovel (
                valor_venda, valor_aluguel, quant_quartos, quant_salas, quant_vagas,
                quant_banheiros, quant_varandas, categoria, id_endereco, status,
                iptu, valor_condominio, andar, estado, bloco, ano_construcao,
                area_total, area_privativa, situacao, ocupacao,
                id_corretor, id_captador,
                data_cadastro, data_modificacao, id_anuncio, id_condominio
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";


            $categoria = $imovel->getCategoria();
            $categoria = $categoria ? $categoria->value : null;

            $endereco = $imovel->getEndereco();
            $idEndereco = ($endereco && $endereco->getId()) ? $endereco->getId() : null;

            $anuncio = $imovel->getAnuncio();
            $idAnuncio = ($anuncio && $anuncio->getId()) ? $anuncio->getId() : null;

            $status = $imovel->getStatus();
            $status = $status ? $status->value : null;

            $estado = $imovel->getEstado();
            $estado = $estado ? $estado->value : null;

            $situacao = $imovel->getSituacao();
            $situacao = $situacao ? $situacao->value : null;

            $ocupacao = $imovel->getOcupacao();
            $ocupacao = $ocupacao ? $ocupacao->value : null;

            $corretor = $imovel->getCorretor();
            $cpf_corretor = $corretor ? $corretor->getCpfCnpj() : null;

            $captador = $imovel->getCaptador();
            $cpf_captador = $captador ? $captador->getCpfCnpj() : null;

            $condominio = $imovel->getCondominio();
            $idCondominio = $condominio ? $condominio->getId() : null;

            $dataCadastro = $imovel->getDataCadastro();
            if ($dataCadastro instanceof DateTime) {
                $dataCadastro = $dataCadastro->format("d-m-Y");
            }

            $dataModificacao = $imovel->getDataModificacao();
            if ($dataModificacao instanceof DateTime) {
                $dataModificacao = $dataModificacao->format("d-m-Y");
            }


            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $imovel->getValorVenda(),
                $imovel->getValorAluguel(),
                $imovel->getQuantQuartos(),
                $imovel->getQuantSalas(),
                $imovel->getQuantVagas(),
                $imovel->getQuantBanheiros(),
                $imovel->getQuantVarandas(),
                $categoria,
                $idEndereco,
                $status,
                $imovel->getIptu(),
                $imovel->getValorCondominio(),
                $imovel->getAndar(),
                $estado,
                $imovel->getBloco(),
                $imovel->getAnoConstrucao(),
                $imovel->getAreaTotal(),
                $imovel->getAreaPrivativa(),
                $situacao,
                $ocupacao,
                $cpf_corretor,
                $cpf_captador,
                $dataCadastro,
                $dataModificacao,
                $idAnuncio,
                $idCondominio
            ]);

            $id_imovel = $this->db->lastInsertId();


            if ($imovel->getProprietarios()) {
                foreach ($imovel->getProprietarios() as $prop) {
                    $stmtProp = $this->db->prepare("
                    INSERT INTO proprietario_imovel (cpf_cnpj_proprietario, id_imovel)
                    VALUES (?, ?)
                ");
                    $stmtProp->execute([
                        $prop->getCpfCnpj(),
                        $id_imovel
                    ]);
                }
            }


            if ($imovel->getFiltros()) {
                foreach ($imovel->getFiltros() as $filtro) {
                    $idFiltro = $this->getIdFiltroImovelPorNome($filtro);
                    if ($idFiltro) {
                        $this->cadastrarFiltroImovel($id_imovel, $filtro);
                    }
                }
            }

            $this->db->commit();

            return true;
        } catch (Exception $e) {
            // $this->db->rollBack();
            error_log("ERRO! Banco->cadastrarImovel: " . $e->getMessage());
            return false;
        }
    }

    public function getListaImoveis()
    {

        try {

            $sql = "
            SELECT * FROM imovel
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                throw new Exception("Não há imóveis disponíveis");
            }

            $lista = [];

            foreach ($resultados as $dados) {

                $id = (int)$dados['id_imovel'];
                $imovel = $this->getImovelPorId($id);

                $lista[] = $imovel;
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO Banco->getListaImoveis: " . $e->getMessage());
            return [];
        }
    }

    public function getListaImoveisDisponiveis()
    {

        try {

            $sql = "
            SELECT * FROM imovel
            WHERE status IN ('Venda', 'Aluguel', 'Venda_Aluguel')
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                throw new Exception("Não há imóveis disponíveis");
            }

            $lista = [];

            foreach ($resultados as $dados) {

                $id = (int)$dados['id_imovel'];

                $imovel = $this->getImovelPorId($id);
                if ($imovel) {
                    $lista[] = $imovel;
                }
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO Banco->getListaImoveisDisponiveis: " . $e->getMessage());
            return [];
        }
    }

    public function atualizarImovel($imovel)
    {

        try {

            $this->db->beginTransaction();


            $categoria = $imovel->getCategoria();
            $categoria = $categoria ? $categoria->value : null;

            $status = $imovel->getStatus();
            $status = $status ? $status->value : null;

            $estado = $imovel->getEstado();
            $estado = $estado ? $estado->value : null;

            $situacao = $imovel->getSituacao();
            $situacao = $situacao ? $situacao->value : null;

            $ocupacao = $imovel->getOcupacao();
            $ocupacao = $ocupacao ? $ocupacao->value : null;


            $endereco = $imovel->getEndereco();
            $endereco = ($endereco && $endereco->getId()) ? $endereco->getId() : null;

            $anuncio = $imovel->getAnuncio();
            $anuncio = ($anuncio && $anuncio->getId()) ? $anuncio->getId() : null;

            $condominio = $imovel->getCondominio();
            $condominio = $condominio ? $condominio->getId() : null;

            $corretor = $imovel->getCorretor();
            $corretor = $corretor ? $corretor->getCpfCnpj() : null;

            $captador = $imovel->getCaptador();
            $captador = $captador ? $captador->getCpfCnpj() : null;


            $dataCadastro = $imovel->getDataCadastro();
            $dataCadastro = $dataCadastro ? $dataCadastro->format("Y-m-d") : null;

            $dataModificacao = $imovel->getDataModificacao();
            $dataModificacao = $dataModificacao ? $dataModificacao->format("Y-m-d") : null;


            $imovelDb = $this->getImovelPorId($imovel->getId());


            $propsAntigos = $imovelDb ? $imovelDb->getProprietarios() : [];
            $propsNovos = $imovel->getProprietarios() ?: [];

            foreach ($propsAntigos as $p) {
                if (!in_array($p, $propsNovos)) {

                    $stmt = $this->db->prepare("
                    DELETE FROM proprietario_imovel
                    WHERE cpf_cnpj_proprietario = :cpf
                      AND id_imovel = :id
                ");
                    $stmt->execute([
                        ':cpf' => $p->getCpfCnpj(),
                        ':id' => $imovel->getId()
                    ]);
                }
            }

            foreach ($propsNovos as $p) {
                if (!in_array($p, $propsAntigos)) {

                    $stmt = $this->db->prepare("
                    INSERT INTO proprietario_imovel (cpf_cnpj_proprietario, id_imovel)
                    VALUES (:cpf, :id)
                ");
                    $stmt->execute([
                        ':cpf' => $p->getCpfCnpj(),
                        ':id' => $imovel->getId()
                    ]);
                }
            }


            $filtrosAntigos = $imovelDb ? $imovelDb->getFiltros() : [];
            $filtrosNovos = $imovel->getFiltros() ?: [];

            foreach ($filtrosAntigos as $f) {
                if (!in_array($f, $filtrosNovos)) {
                    $id = $this->getIdFiltroImovelPorNome($f);
                    if ($id !== null) {
                        $this->removerFiltroDoImovel($imovel->getId(), $id);
                    }
                }
            }

            foreach ($filtrosNovos as $f) {
                if (!in_array($f, $filtrosAntigos)) {
                    $id = $this->getIdFiltroImovelPorNome($f);
                    if ($id !== null) {
                        $this->cadastrarFiltroImovel($imovel->getId(), $id);
                    }
                }
            }


            $sql = "
            UPDATE imovel SET
                valor_venda = :valor_venda,
                valor_aluguel = :valor_aluguel,
                quant_quartos = :quartos,
                quant_salas = :salas,
                quant_vagas = :vagas,
                quant_banheiros = :banheiros,
                quant_varandas = :varandas,
                categoria = :categoria,
                id_endereco = :endereco,
                status = :status,
                iptu = :iptu,
                valor_condominio = :condominio_valor,
                andar = :andar,
                estado = :estado,
                bloco = :bloco,
                ano_construcao = :ano,
                area_total = :area_total,
                area_privativa = :area_privativa,
                situacao = :situacao,
                ocupacao = :ocupacao,
                id_corretor = :corretor,
                id_captador = :captador,
                data_cadastro = :data_cadastro,
                data_modificacao = :data_modificacao,
                id_anuncio = :anuncio,
                id_condominio = :condominio
            WHERE id_imovel = :id
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':valor_venda' => $imovel->getValorVenda(),
                ':valor_aluguel' => $imovel->getValorAluguel(),
                ':quartos' => $imovel->getQuantQuartos(),
                ':salas' => $imovel->getQuantSalas(),
                ':vagas' => $imovel->getQuantVagas(),
                ':banheiros' => $imovel->getQuantBanheiros(),
                ':varandas' => $imovel->getQuantVarandas(),
                ':categoria' => $categoria,
                ':endereco' => $endereco,
                ':status' => $status,
                ':iptu' => $imovel->getIptu(),
                ':condominio_valor' => $imovel->getValorCondominio(),
                ':andar' => $imovel->getAndar(),
                ':estado' => $estado,
                ':bloco' => $imovel->getBloco(),
                ':ano' => $imovel->getAnoConstrucao(),
                ':area_total' => $imovel->getAreaTotal(),
                ':area_privativa' => $imovel->getAreaPrivativa(),
                ':situacao' => $situacao,
                ':ocupacao' => $ocupacao,
                ':corretor' => $corretor,
                ':captador' => $captador,
                ':data_cadastro' => $dataCadastro,
                ':data_modificacao' => $dataModificacao,
                ':anuncio' => $anuncio,
                ':condominio' => $condominio,
                ':id' => $imovel->getId()
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("ERRO Banco->atualizarImovel: " . $e->getMessage());
            return false;
        }
    }

    public function getIdFiltroImovelPorNome($nome)
    {
        try {

            $stmt = $this->db->prepare("
                SELECT id_filtros_imovel 
                FROM filtros_imovel 
                WHERE nome = :nome
            ");
            $stmt->execute([':nome' => $nome]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? (int)$row['id_filtros_imovel'] : null;
        } catch (Exception $e) {
            error_log("ERRO Banco->getIdFiltroImovelPorNome: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrarFiltroImovel($idImovel, $idFiltro)
    {
        try {

            $stmt = $this->db->prepare("
            INSERT INTO imovel_filtros (id_imovel, id_filtros_imovel)
            VALUES (:id_imovel, :id_filtro)
        ");
            $stmt->execute([
                ':id_imovel' => $idImovel,
                ':id_filtro' => $idFiltro
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO Banco->cadastrarFiltroImovel: " . $e->getMessage());
            return false;
        }
    }

    public function removerFiltroDoImovel($idImovel, $idFiltro)
    {
        try {

            $stmt = $this->db->prepare("
            DELETE FROM imovel_filtros
            WHERE id_imovel = :id_imovel 
              AND id_filtros_imovel = :id_filtro
        ");
            $stmt->execute([
                ':id_imovel' => $idImovel,
                ':id_filtro' => $idFiltro
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO Banco->removerFiltroDoImovel: " . $e->getMessage());
            return false;
        }
    }

    public function getIdFiltroCondominioPorNome($nome)
    {
        try {

            $stmt = $this->db->prepare("
            SELECT id_filtros_condominio 
            FROM filtros_condominio 
            WHERE nome = :nome
        ");
            $stmt->execute([':nome' => $nome]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? (int)$row['id_filtros_condominio'] : null;
        } catch (Exception $e) {
            error_log("ERRO Banco->getIdFiltroCondominioPorNome: " . $e->getMessage());
            return null;
        }
    }

    public function cadastrarFiltroCondominio($idCondominio, $idFiltro)
    {
        try {

            $stmt = $this->db->prepare("
            INSERT INTO condominio_filtros (id_filtros_condominio, id_condominio)
            VALUES (:id_filtro, :id_condominio)
        ");
            $stmt->execute([
                ':id_filtro' => $idFiltro,
                ':id_condominio' => $idCondominio
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO Banco->cadastrarFiltroCondominio: " . $e->getMessage());
            return false;
        }
    }

    public function removerFiltroDoCondominio($idCondominio, $idFiltro)
    {
        try {

            $stmt = $this->db->prepare("
            DELETE FROM condominio_filtros
            WHERE id_condominio = :id_condominio 
              AND id_filtros_condominio = :id_filtro
        ");
            $stmt->execute([
                ':id_condominio' => $idCondominio,
                ':id_filtro' => $idFiltro
            ]);

            return true;
        } catch (Exception $e) {
            error_log("ERRO Banco->removerFiltroDoCondominio: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarAnuncio($anuncio)
    {

        try {

            $this->db->beginTransaction();


            $sql = "
            UPDATE anuncio
            SET descricao = :descricao,
                titulo = :titulo
            WHERE id_anuncio = :id
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':descricao' => $anuncio->getDescricao(),
                ':titulo' => $anuncio->getTitulo(),
                ':id' => $anuncio->getId()
            ]);

            // mídias
            // tratar imagens, vídeos e anexos depois
            // tabela: midia_anuncio

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("ERRO Banco->atualizarAnuncio: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarCondominio($condominio)
    {

        try {

            $this->db->beginTransaction();


            $sql = "
            UPDATE condominio
            SET nome = :nome
            WHERE id_condominio = :id
        ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome' => $condominio->getNome(),
                ':id' => $condominio->getId()
            ]);


            $condominioDb = $this->getCondominioPorId(
                $condominio->getId()
            );

            $filtrosAntigos = $condominioDb ? $condominioDb->getFiltros() : [];
            $filtrosNovos = $condominio->getFiltros() ?: [];


            foreach ($filtrosAntigos as $filtro) {
                if (!in_array($filtro, $filtrosNovos)) {

                    $id = $this->getIdFiltroCondominioPorNome($filtro);

                    if ($id !== null) {
                        $this->removerFiltroDoCondominio(
                            $condominio->getId(),
                            $id
                        );
                    }
                }
            }


            foreach ($filtrosNovos as $filtro) {
                if (!in_array($filtro, $filtrosAntigos)) {

                    $id = $this->getIdFiltroCondominioPorNome($filtro);

                    if ($id !== null) {
                        $this->cadastrarFiltroCondominio(
                            $condominio->getId(),
                            $id
                        );
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("ERRO Banco->atualizarCondominio: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarUsuario($usuario)
    {
        try {

            $this->db->beginTransaction();

            $sql = "
                UPDATE usuario
                SET username = :username,
                    senha = :senha,
                    email = :email,
                    nome = :nome,
                    cpf_cnpj = :cpf,
                    rg = :rg,
                    id_endereco = :endereco,
                    data_nascimento = :data,
                    tipo_usuario = :tipo
                WHERE cpf_cnpj = :cpf_where OR id_usuario = :id
            ";

            $endereco = $usuario->getEndereco();
            $endereco = $endereco ? $endereco->getId() : null;

            $dataNascimento = $usuario->getDataNascimento();
            $dataNascimento = $dataNascimento
                ? $dataNascimento->format("Y-m-d")
                : null;

            $tipoUsuario = $usuario->getTipo();
            $tipoUsuario = $tipoUsuario ? $tipoUsuario->value : null;


            $senha_hash = hash('sha256', $usuario->getSenha());

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':username' => $usuario->getUsername(),
                ':senha' => $senha_hash,
                ':email' => $usuario->getEmail(),
                ':nome' => $usuario->getNome(),
                ':cpf' => $usuario->getCpfCnpj(),
                ':rg' => $usuario->getRg(),
                ':endereco' => $endereco,
                ':data' => $dataNascimento,
                ':tipo' => $tipoUsuario,
                ':cpf_where' => $usuario->getCpfCnpj(),
                ':id' => $usuario->getId()
            ]);


            $usuario_db = $usuario->getCpfCnpj() ? $this->getUsuarioPorCpfCnpj(
                $usuario->getCpfCnpj()
            ) : $this->getUsuarioPorId($usuario->getId());

            $telefonesAntigos = $usuario_db ? ($usuario_db->getTelefones() ?? []) : [];
            $telefonesNovos = $usuario_db ? ($usuario->getTelefones() ?? []) : [];

        

            foreach ($telefonesAntigos as $tel) {
                if (!in_array($tel, $telefonesNovos)) {

                    $stmt = $this->db->prepare("
                        SELECT id_telefone FROM telefone WHERE numero = :numero
                    ");
                    $stmt->execute([':numero' => $tel]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        $id_tel = $row['id_telefone'];

                        $stmt = $this->db->prepare("
                            DELETE FROM telefone_usuario
                            WHERE id_telefone = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);

                        $stmt = $this->db->prepare("
                            DELETE FROM telefone
                            WHERE id_telefone = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);
                    }
                }
            }


            foreach ($telefonesNovos as $tel) {
                if (!in_array($tel, $telefonesAntigos)) {

                    $stmt = $this->db->prepare("
                        INSERT INTO telefone (numero) VALUES (:numero)
                    ");
                    $stmt->execute([':numero' => $tel]);

                    $id_tel = $this->db->lastInsertId();

                    $stmt = $this->db->prepare("
                        INSERT INTO telefone_usuario (id_usuario, id_telefone)
                        VALUES (:id_usuario, :id_tel)
                    ");
                    $stmt->execute([
                        ':id_usuario' => $usuario->getId(),
                        ':id_tel' => $id_tel
                    ]);
                }
            }


            if ($tipoUsuario === "CORRETOR") {

                $stmt = $this->db->prepare("
                    UPDATE corretor
                    SET creci = :creci
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':creci' => $usuario->getCreci(),
                    ':id' => $usuario->getId()
                ]);
            } elseif ($tipoUsuario === "CAPTADOR") {

                $stmt = $this->db->prepare("
                    UPDATE captador
                    SET salario = :salario
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':salario' => $usuario->getSalario(),
                    ':id' => $usuario->getId()
                ]);
            } elseif ($tipoUsuario === "GERENTE") {

                $stmt = $this->db->prepare("
                    UPDATE gerente
                    SET salario = :salario
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':salario' => $usuario->getSalario(),
                    ':id' => $usuario->getId()
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("ERRO Banco->atualizarUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarProprietario($proprietario)
    {

        try {

            $this->db->beginTransaction();

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

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':email' => $proprietario->getEmail(),
                ':nome' => $proprietario->getNome(),
                ':cpf' => $proprietario->getCpfCnpj(),
                ':rg' => $proprietario->getRg(),
                ':endereco' => $endereco,
                ':data' => $dataNascimento,
                ':cpf_where' => $proprietario->getCpfCnpj()
            ]);

            $proprietarioDb = $this->getProprietarioPorCpfCnpj(
                $proprietario->getCpfCnpj()
            );

            $telefonesAntigos = $proprietarioDb ? $proprietarioDb->getTelefones() : [];
            $telefonesNovos = $proprietario->getTelefones() ?: [];

            foreach ($telefonesAntigos as $tel) {
                if (!in_array($tel, $telefonesNovos)) {

                    $stmt = $this->db->prepare("
                        SELECT id_telefone FROM telefone WHERE numero = :numero
                    ");
                    $stmt->execute([':numero' => $tel]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        $id_tel = $row['id_telefone'];

                        $stmt = $this->db->prepare("
                            DELETE FROM telefone_proprietario
                            WHERE id_telefone = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);

                        $stmt = $this->db->prepare("
                            DELETE FROM telefone
                            WHERE id_telefone = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);
                    }
                }
            }

            foreach ($telefonesNovos as $tel) {
                if (!in_array($tel, $telefonesAntigos)) {

                    $stmt = $this->db->prepare("
                        INSERT INTO telefone (numero) VALUES (:numero)
                    ");
                    $stmt->execute([':numero' => $tel]);

                    $id_tel = $this->db->lastInsertId();

                    $stmt = $this->db->prepare("
                        INSERT INTO telefone_proprietario (id_proprietario, id_telefone)
                        VALUES (:id_prop, :id_tel)
                    ");
                    $stmt->execute([
                        ':id_prop' => $proprietario->getId(),
                        ':id_tel' => $id_tel
                    ]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("ERRO Banco->atualizarProprietario: " . $e->getMessage());
            return false;
        }
    }

    public function getImovelPorId($id_imovel)
    {

        try {

            $sql = "SELECT * FROM imovel WHERE id_imovel = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id_imovel]);

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) {
                throw new Exception("Não há imóveis disponíveis");
            }

            $valorVenda = $dados['valor_venda'] !== null ? (float)$dados['valor_venda'] : null;
            $valorAluguel = $dados['valor_aluguel'] !== null ? (float)$dados['valor_aluguel'] : null;

            $quantQuartos = $dados['quant_quartos'] !== null ? (int)$dados['quant_quartos'] : null;
            $quantSalas = $dados['quant_salas'] !== null ? (int)$dados['quant_salas'] : null;
            $quantVagas = $dados['quant_vagas'] !== null ? (int)$dados['quant_vagas'] : null;
            $quantBanheiros = $dados['quant_banheiros'] !== null ? (int)$dados['quant_banheiros'] : null;
            $quantVarandas = $dados['quant_varandas'] !== null ? (int)$dados['quant_varandas'] : null;

            $categoria = $dados['categoria'];
            $status = $dados['status'];
            $estado = $dados['estado'];
            $situacao = $dados['situacao'];
            $ocupacao = $dados['ocupacao'];

            $endereco = null;
            if ($dados['id_endereco']) {
                $endereco = $this->getEnderecoPorId((int)$dados['id_endereco']);
            }

            $corretor = null;
            if ($dados['id_corretor']) {
                $corretor = $this->getUsuarioPorCpfCnpj($dados['id_corretor']);
            }

            $captador = null;
            if ($dados['id_captador']) {
                $captador = $this->getUsuarioPorCpfCnpj($dados['id_captador']);
            }

            $dataCadastro = $dados['data_cadastro'] ? new DateTime($dados['data_cadastro']) : null;
            $dataModificacao = $dados['data_modificacao'] ? new DateTime($dados['data_modificacao']) : null;

            $anuncio = null;
            if ($dados['id_anuncio']) {
                $anuncio = $this->getAnuncioPorId((int)$dados['id_anuncio']);
            }

            $condominio = null;
            if ($dados['id_condominio']) {
                $condominio = $this->getCondominioPorId((int)$dados['id_condominio']);
            }

            $imovelObj = new Imovel($endereco, $status, $categoria);

            $imovelObj->setId((int)$dados['id_imovel']);
            $imovelObj->setValorVenda($valorVenda);
            $imovelObj->setValorAluguel($valorAluguel);
            $imovelObj->setQuantQuartos($quantQuartos);
            $imovelObj->setQuantSalas($quantSalas);
            $imovelObj->setQuantVagas($quantVagas);
            $imovelObj->setQuantBanheiros($quantBanheiros);
            $imovelObj->setQuantVarandas($quantVarandas);
            $imovelObj->setEstado($estado);
            $imovelObj->setSituacao($situacao);
            $imovelObj->setOcupacao($ocupacao);
            $imovelObj->setCorretor($corretor);
            $imovelObj->setCaptador($captador);
            $imovelObj->setDataCadastro($dataCadastro);
            $imovelObj->setDataModificacao($dataModificacao);
            $imovelObj->setAnuncio($anuncio);
            $imovelObj->setCondominio($condominio);

            $stmt = $this->db->prepare("
                SELECT cpf_cnpj_proprietario 
                FROM proprietario_imovel 
                WHERE id_imovel = :id
            ");
            $stmt->execute([':id' => $id_imovel]);

            $proprietarios = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $prop = $this->getProprietarioPorCpfCnpj($row['cpf_cnpj_proprietario']);
                if ($prop) {
                    $proprietarios[] = $prop;
                }
            }
            $imovelObj->setProprietarios($proprietarios);

            $stmt = $this->db->prepare("
                SELECT fi.nome
                FROM imovel_filtros ifi
                JOIN filtros_imovel fi 
                    ON fi.id_filtros_imovel = ifi.id_filtros_imovel
                WHERE ifi.id_imovel = :id
            ");
            $stmt->execute([':id' => $id_imovel]);

            $filtros = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $filtros[] = $row['nome'];
            }

            $imovelObj->setFiltros($filtros);

            return $imovelObj;
        } catch (Exception $e) {
            error_log("ERRO! Banco->getImovelPorId: " . $e->getMessage());
            return null;
        }
    }

    public function getImoveisPorProprietario($cpf)
    {
        try {
            $stmt = $this->db->prepare("SELECT id_imovel FROM proprietario_imovel WHERE cpf_cnpj_proprietario = ?");
            $stmt->execute([$cpf]);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($dados)) {
                throw new Exception("Não há imóveis disponíveis");
            }
            $imoveis = [];
            foreach ($dados as $row) {
                $id = (int)$row['id_imovel'];
                $imovel = $this->getImovelPorId($id);
                if ($imovel) {
                    $imoveis[] = $imovel;
                }
            }
            return $imoveis;
        } catch (Exception $e) {
            $erro = "ERRO! Banco->getImoveisPorProprietario: "  . $e->getMessage();
            error_log($erro);
            return [];
        }
    }
}


//  $valorVenda = $dados['valor_venda'] ? (float)$dados['valor_venda'] : null;
//                 $valorAluguel = $dados['valor_aluguel'] ? (float)$dados['valor_aluguel'] : null;
//                 $quartos = $dados['quant_quartos'] ? (int)$dados['quant_quartos'] : null;
//                 $salas = $dados['quant_salas'] ? (int)$dados['quant_salas'] : null;
//                 $vagas = $dados['quant_vagas'] ? (int)$dados['quant_vagas'] : null;
//                 $banheiros = $dados['quant_banheiros'] ? (int)$dados['quant_banheiros'] : null;
//                 $varandas = $dados['quant_varandas'] ? (int)$dados['quant_varandas'] : null;

//                 $categoria = $dados['categoria'] ?? null;
//                 $status = $dados['status'] ?? null;
//                 $estado = $dados['estado'] ?? null;
//                 $situacao = $dados['situacao'] ?? null;
//                 $ocupacao = $dados['ocupacao'] ?? null;

//                 $endereco = $dados['id_endereco']
//                     ? $this->getEnderecoPorId((int)$dados['id_endereco'])
//                     : null;

//                 $corretor = $dados['id_corretor']
//                     ? $this->getUsuarioPorId($dados['id_corretor'])
//                     : null;

//                 $captador = $dados['id_captador']
//                     ? $this->getUsuarioPorId($dados['id_captador'])
//                     : null;

//                 $anuncio = $dados['id_anuncio']
//                     ? $this->getAnuncioPorId((int)$dados['id_anuncio'])
//                     : null;

//                 $condominio = $dados['id_condominio']
//                     ? $this->getCondominioPorId((int)$dados['id_condominio'])
//                     : null;

//                 $dataCadastro = $dados['data_cadastro']
//                     ? new DateTime($dados['data_cadastro'])
//                     : null;

//                 $dataModificacao = $dados['data_modificacao']
//                     ? new DateTime($dados['data_modificacao'])
//                     : null;


//                 $imovel = new Imovel($endereco, $status, $categoria);

//                 $imovel->setId($id);
//                 $imovel->setValorVenda($valorVenda);
//                 $imovel->setValorAluguel($valorAluguel);
//                 $imovel->setQuantQuartos($quartos);
//                 $imovel->setQuantSalas($salas);
//                 $imovel->setQuantVagas($vagas);
//                 $imovel->setQuantBanheiros($banheiros);
//                 $imovel->setQuantVarandas($varandas);
//                 $imovel->setIptu($dados['iptu']);
//                 $imovel->setValorCondominio($dados['valor_condominio']);
//                 $imovel->setAndar($dados['andar']);
//                 $imovel->setEstado($estado);
//                 $imovel->setBloco($dados['bloco']);
//                 $imovel->setAnoConstrucao($dados['ano_construcao']);
//                 $imovel->setAreaTotal($dados['area_total']);
//                 $imovel->setAreaPrivativa($dados['area_privativa']);
//                 $imovel->setSituacao($situacao);
//                 $imovel->setOcupacao($ocupacao);
//                 $imovel->setCorretor($corretor);
//                 $imovel->setCaptador($captador);
//                 $imovel->setDataCadastro($dataCadastro);
//                 $imovel->setDataModificacao($dataModificacao);
//                 $imovel->setAnuncio($anuncio);
//                 $imovel->setCondominio($condominio);
//                 $stmtP = $this->db->prepare("
//                 SELECT cpf_cnpj_proprietario
//                 FROM proprietario_imovel
//                 WHERE id_imovel = :id
//             ");
//                 $stmtP->execute([':id' => $id]);

//                 $proprietarios = [];

//                 while ($row = $stmtP->fetch(PDO::FETCH_ASSOC)) {
//                     $prop = $this->getProprietarioPorCpfCnpj($row['cpf_cnpj_proprietario']);
//                     if ($prop) {
//                         $proprietarios[] = $prop;
//                     }
//                 }

//                 $imovel->setProprietarios($proprietarios);


//                 $stmtF = $this->db->prepare("
//                 SELECT f.nome
//                 FROM imovel_filtros i
//                 JOIN filtros_imovel f ON f.id_filtros_imovel = i.id_filtros_imovel
//                 WHERE i.id_imovel = :id
//             ");
//                 $stmtF->execute([':id' => $id]);

//                 $filtros = [];

//                 while ($row = $stmtF->fetch(PDO::FETCH_ASSOC)) {
//                     $filtros[] = $row['nome'];
//                 }

//                 $imovel->setFiltros($filtros);
