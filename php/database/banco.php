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
                $dbname = "imobiliaria";
                self::$db = new Banco("mysql:host=$servername;dbname=$dbname", $username, $password);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // error_log("Connected successfully");
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
            "CREATE DATABASE IF NOT EXISTS imobiliaria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;",
            "USE imobiliaria;",

            "CREATE TABLE IF NOT EXISTS usuario (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
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
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                numero VARCHAR(11) NOT NULL UNIQUE
            )",

            "CREATE TABLE IF NOT EXISTS endereco (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                rua VARCHAR(255) NOT NULL,
                numero INTEGER(10) NULL,
                bairro VARCHAR(255) NOT NULL,
                cep VARCHAR(8) NOT NULL,
                complemento VARCHAR(100) NULL,
                cidade VARCHAR(255) NOT NULL,
                uf VARCHAR(2) NOT NULL
            )",

            "CREATE TABLE IF NOT EXISTS proprietario (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                email VARCHAR(255) UNIQUE NULL,
                nome VARCHAR(255) NOT NULL,
                cpf_cnpj VARCHAR(14) UNIQUE NULL,
                rg VARCHAR(12) NULL,
                id_endereco INTEGER NULL,
                data_nascimento DATE NULL,
                FOREIGN KEY (id_endereco) REFERENCES endereco(id)
            )",

            "CREATE TABLE IF NOT EXISTS telefone_usuario (
                id_usuario INTEGER,
                id_telefone INTEGER,
                FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE,
                FOREIGN KEY (id_telefone) REFERENCES telefone(id) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS telefone_proprietario (
                id_telefone INTEGER,
                id_proprietario INTEGER,
                FOREIGN KEY (id_telefone) REFERENCES telefone(id) ON DELETE CASCADE,
                FOREIGN KEY (id_proprietario) REFERENCES proprietario(id) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS cliente (
                    id_usuario INTEGER PRIMARY KEY,
                    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS captador (
                    id_usuario INTEGER PRIMARY KEY,
                    salario REAL NULL,
                    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS corretor (
                    id_usuario INTEGER PRIMARY KEY,
                    creci TEXT NULL,
                    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE
            )",


            "CREATE TABLE IF NOT EXISTS anuncio (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                descricao VARCHAR(255) NULL,
                titulo VARCHAR(255) NULL
            )",

            "CREATE TABLE IF NOT EXISTS condominio (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(255) NULL,
                id_endereco INTEGER NULL,
                FOREIGN KEY (id_endereco) REFERENCES endereco(id)
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
                FOREIGN KEY (id_anuncio) REFERENCES anuncio(id),
                FOREIGN KEY (id_endereco) REFERENCES endereco(id),
                FOREIGN KEY (id_corretor) REFERENCES corretor(id_usuario),
                FOREIGN KEY (id_captador) REFERENCES captador(id_usuario),
                FOREIGN KEY (id_condominio) REFERENCES condominio(id)
            )",

            "CREATE TABLE IF NOT EXISTS midia_anuncio (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                id_anuncio INTEGER NULL,
                nome_arquivo VARCHAR(255) NULL,
                tipo VARCHAR(255) NULL,
                FOREIGN KEY (id_anuncio) REFERENCES anuncio(id) ON DELETE CASCADE
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
                    FOREIGN KEY (id_imovel) REFERENCES imovel(id),
                    FOREIGN KEY (id_cliente) REFERENCES cliente(id_usuario),
                    FOREIGN KEY (id_proprietario) REFERENCES proprietario(id),
                    FOREIGN KEY (id_corretor) references corretor(id_usuario)
                    )",

            "CREATE TABLE IF NOT EXISTS gerente (
                    id_usuario INTEGER PRIMARY KEY AUTO_INCREMENT,
                    salario REAL NULL,
                    FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE CASCADE
                )",

            "CREATE TABLE IF NOT EXISTS atendimento (
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    id_imovel INTEGER  NULL,
                    id_corretor INT  NULL,
                    id_cliente INT NULL,
                    status VARCHAR(255) NULL,
                    FOREIGN KEY (id_imovel) REFERENCES imovel(id),
                    FOREIGN KEY (id_corretor) references corretor(id_usuario),
                    FOREIGN KEY (id_cliente) references cliente(id_usuario) ON DELETE CASCADE
                )",
            "CREATE TABLE IF NOT EXISTS filtros_imovel (
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    nome VARCHAR(255) NOT NULL UNIQUE                    
                )",
            "CREATE TABLE IF NOT EXISTS filtros_condominio
                (
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    nome VARCHAR(255) NOT NULL UNIQUE                    
                )",
            "CREATE TABLE IF NOT EXISTS imovel_filtros (
                    id_filtros_imovel INTEGER,
                    id_imovel INTEGER, 
                    FOREIGN KEY (id_filtros_imovel) references filtros_imovel(id) ON DELETE CASCADE,
                    FOREIGN KEY (id_imovel) references imovel(id) ON DELETE CASCADE                
                )",
            "CREATE TABLE IF NOT EXISTS condominio_filtros (
                    id_filtros_condominio INTEGER,
                    id_condominio INTEGER, 
                    FOREIGN KEY (id_filtros_condominio) references filtros_condominio(id) ON DELETE CASCADE,
                    FOREIGN KEY (id_condominio) references condominio(id) ON DELETE CASCADE               
                )",

            "CREATE TABLE IF NOT EXISTS proprietario_imovel (
                    id_proprietario INTEGER NULL,
                    id_imovel INTEGER NULL,
                    FOREIGN KEY (id_proprietario) REFERENCES proprietario(id) ON DELETE CASCADE,
                    FOREIGN KEY (id_imovel) REFERENCES imovel(id) ON DELETE CASCADE                
                )",

            "CREATE TABLE IF NOT EXISTS visita (
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    id_cliente INTEGER NULL,
                    id_imovel INTEGER NULL,
                    id_corretor INTEGER NULL,
                    data_visita DATETIME NULL,
                    status VARCHAR(255) NULL,
                    FOREIGN KEY (id_cliente) references cliente(id_usuario) ON DELETE CASCADE,
                    FOREIGN KEY (id_imovel) references imovel(id) ON DELETE CASCADE,
                    FOREIGN KEY (id_corretor) references corretor(id_usuario) ON DELETE CASCADE
                )",

            "CREATE TABLE IF NOT EXISTS vistoria (
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    id_imovel INTEGER NULL,
                    data_vistoria DATETIME NULL,
                    status VARCHAR(255) NULL,
                    FOREIGN KEY (id_imovel) references imovel(id) ON DELETE CASCADE
                )",
            "CREATE TABLE IF NOT EXISTS relatorio_vistoria (
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    id_vistoria INTEGER NULL,
                    descricao TEXT NULL,
                    FOREIGN KEY (id_vistoria) references vistoria(id) ON DELETE CASCADE
                )"
        ];

        foreach ($queries as $sql) {
            $this->exec($sql);
        }
    }


    public function getProprietarioPorId($id)
    {
        try {
            $sql = "SELECT * FROM proprietario WHERE id = ?";
            $stmt = $this->prepare($sql);
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
            error_log("ERRO Banco->getProprietarioPorId: " . $e->getMessage());
            return null;
        }
    }

    public function getListaVistoriasPorVistoriador($vistoriador)
    {
        $lista = [];
        $vistorias = $this->prepare("SELECT * from vistoria WHERE id_vistoriador = $vistoriador");

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
        $visitas = $this->prepare("SELECT * from visita WHERE id_corretor = $corretor");

        foreach ($visitas as $visita) {
            $novaVisita = new Visita();
            $novaVisita->setId($visita['id_visita']);
            $novaVisita->setImovel($this->getImovelPorId($visita['id_imovel']));
            $novaVisita->setCliente($this->getUsuarioPorId($visita['id_cliente']));
            $lista[] = $novaVisita;
        }

        return $lista;
    }

    public function getMidiaPorId($id)
    {
        $stmt = $this->prepare("SELECT midia FROM midia_anuncio WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }

    public function cadastrarVistoria($vistoria)
    {
        return $this->exec("
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
        return $this->exec("
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
            $sql = "SELECT * FROM usuario WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->execute([$id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception("Não existe usuário com ID $id");
            }
            $idUsuario = $registro['id'] !== null ? (int)$registro['id'] : null;
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
                $tipoUsuario = Tipo::tryFrom($tipoUsuario) ?? null;
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
            $stmt = $this->prepare($sqlQuery);
            $stmt->execute([':id_usuario' => $idUsuario]);
            $registros = $stmt->fetch(PDO::FETCH_ASSOC);
            $telefones = [];
            if ($registros) {
                foreach ($registros as $idTelefone) {
                    $sqlQuery = " 
                            SELECT numero FROM telefone 
                            WHERE id = :id_telefone
                                ";
                    $stmt = $this->prepare($sqlQuery);
                    $stmt->execute([':id_telefone' => $idTelefone]);
                    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            switch ($tipoUsuario) {
                case (Tipo::CORRETOR):
                    $stmt = $this->prepare("
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
                    $stmt = $this->prepare("
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
                    $stmt = $this->prepare("
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

                    # $stmt = $this->prepare("
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
            $stmt = $this->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há endereços cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $idEndereco = (int) $registro['id'];
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
            $stmt = $this->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há proprietários cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $id = (int)$registro['id'];
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
            $stmt = $this->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há usuários cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $id = (int)$registro['id'];
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

                $stmtTel = $this->prepare("
                SELECT t.numero
                FROM telefone_usuario tu
                JOIN telefone t ON t.id = tu.id_telefone
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
            $stmt = $this->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há usuários cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $id = $registro['id'];
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

                $stmtTel = $this->prepare("
                SELECT t.numero
                FROM telefone_usuario tu
                JOIN telefone t ON t.id = tu.id_telefone
                WHERE tu.id_usuario = :id
                ");
                $stmtTel->execute([':id' => $id]);

                $telefones = [];
                while ($row = $stmtTel->fetch(PDO::FETCH_ASSOC)) {
                    $telefones[] = $row['numero'];
                }

                if (!$tipo) {
                    continue;
                }

                switch ($tipo) {

                    case 'CORRETOR':

                        $stmtC = $this->prepare("
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

                        $stmtC = $this->prepare("
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
                        $usuario->setSalario($salario ? (float)$salario : 0.0);
                        break;

                    case 'GERENTE':

                        $stmtC = $this->prepare("
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
                        $usuario->setSalario($salario ? (float)$salario : 0.0);
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
                            Tipo::tryFrom($tipo)
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
            $stmt = $this->prepare($sql);
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
            $id = $this->lastInsertId();
            if ($usuario->getTelefones()) {
                foreach ($usuario->getTelefones() as $telefone) {
                    $sqlQuery = " 
                            INSERT INTO telefone (numero) 
                            VALUES(:numero)
                            ";
                    $stmt = $this->prepare($sqlQuery);
                    $stmt->execute([
                        ':numero' => $telefone,
                    ]);
                    $idTelefone = $this->lastInsertId();
                    $sqlQuery = " 
                            INSERT INTO telefone_usuario (id_usuario, id_telefone) 
                            VALUES(:id_usuario, :id_telefone)
                            ";
                    $stmt = $this->prepare($sqlQuery);
                    $stmt->execute([
                        ':id_usuario' => $id,
                        ':id_telefone' => $idTelefone
                    ]);
                }
            }
            $tipoUsuarioObj = $usuario->getTipo();
            $tipoUsuarioValor = $tipoUsuarioObj ? $tipoUsuarioObj->value : NULL;
            if ($tipoUsuarioValor == "CORRETOR") {
                $stmt = $this->prepare("
                                    INSERT INTO corretor (id_usuario, creci)
                                    VALUES(:id_usuario, :creci)
                                ");
                $stmt->execute([
                    ':id_usuario' => $id,
                    ':creci' => $usuario->getCreci()
                ]);
            } else if ($tipoUsuarioValor == "CAPTADOR") {
                $stmt = $this->prepare("
                                    INSERT INTO captador (id_usuario, salario)
                                    VALUES(:id_usuario, :salario)
                                ");
                $stmt->execute([
                    ':id_usuario' => $id,
                    ':salario' => $usuario->getSalario()
                ]);
            } else if ($tipoUsuarioValor == "GERENTE") {
                $stmt = $this->prepare("
                                    INSERT INTO gerente (id_usuario, salario)
                                    VALUES(:id_usuario, :salario)
                                ");
                $stmt->execute([
                    ':id_usuario' => $id,
                    ':salario' => $usuario->getSalario()
                ]);
            } else if ($tipoUsuarioValor == "CLIENTE") {
                $stmt = $this->prepare("
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
            $stmt = $this->prepare($sqlDeleteQuery);
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
            $stmt = $this->prepare($sqlUpdateQuery);
            $stmt->execute([$valor]);
            $this->commit();
            return True;
        } catch (Exception $e) {
            error_log("ERRO Banco->atualizar $tabela - $valor: " . $e->getMessage());
            return False;
        }
    }

    public function getUsuarioPorCpfCnpj($cpf)
    {
        try {

            $stmt = $this->prepare("
                        SELECT * FROM usuario WHERE cpf_cnpj = ? 
                    ");
            $stmt->execute([$cpf]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception("Não existe usuário com CPF/CNPJ $cpf");
            }
            $idUsuario = $registro['id'] !== null ? (int)$registro['id'] : null;
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
            $stmt = $this->prepare($sqlQuery);
            $stmt->execute([$idUsuario]);
            $registros = $stmt->fetch(PDO::FETCH_ASSOC);
            $telefones = [];
            if ($registros) {
                foreach ($registros as $idTelefone) {
                    $sqlQuery = " 
                            SELECT numero FROM telefone 
                            WHERE id = ?
                                ";
                    $stmt = $this->prepare($sqlQuery);
                    $stmt->execute([$idTelefone]);
                    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            switch ($tipoUsuario) {
                case (Tipo::CORRETOR):
                    $stmt = $this->prepare("
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
                    $stmt = $this->prepare("
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
                    $stmt = $this->prepare("
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

                    # $stmt = $this->prepare("
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

            $stmt = $this->prepare("
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
            $stmt = $this->prepare("
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
                $stmt = $this->prepare($sqlQuery);
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
            $stmt = $this->prepare("
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
            $stmt = $this->prepare("
                        SELECT * FROM condominio_filtros
                        WHERE id_condominio = ?
                    ");
            $stmt->execute([$idCondominio]);
            $condominio_filtros = $stmt->fetch(PDO::FETCH_ASSOC);
            $lista_condominio_filtros = [];
            if ($condominio_filtros) {
                foreach ($condominio_filtros as $registro) {
                    $idCondominio_filtros = (int)($registro);
                    $stmt = $this->prepare("
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
            $stmt = $this->prepare($sqlQuery);
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
            $stmt = $this->prepare($sql);
            $stmt->execute();
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $lista = [];
            foreach ($registros as $registro) {
                $idAtendimento = $registro['id'];
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

            $stmt = $this->prepare("
                        SELECT * FROM anuncio
                        WHERE id = ?
                    ");
            $stmt->execute([$idAnuncio]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception("Não existe anúncio com id $idAnuncio");
            }
            $anuncioObj = new Anuncio();
            $idAnuncio = $registro['id'];
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

    public function cadastrarAnexo($anexo)
    {
        try {
            $sqlQuery = " 
                    INSERT INTO midia_anuncio (id_anuncio, nome_arquivo, tipo) 
                    VALUES(:id_anuncio, :nome_arquivo, :tipo)
                    ";
            $stmt = $this->prepare($sqlQuery);
            $stmt->execute([
                ':id_anuncio' => $anexo->getId(),
                ':nome_arquivo' => $anexo->getCaminho(),
                ':tipo' => $anexo->getTipo() ? $anexo->getTipo()->value : null
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


            $stmt = $this->prepare("
                        SELECT * FROM midia_anuncio 
                        WHERE id_anuncio = :id_anuncio
                    ");
            $stmt->execute([':id_anuncio' => $idAnuncio]);
            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $imagens = [];
            $videos = [];
            $documentos = [];
            foreach ($registros as $registro) {
                // $id = $registro['id_anuncio'];
                $id = $registro['id'];
                $tipo = $registro['tipo'];
                $caminho = $registro['nome_arquivo'];
                if ($tipo == "Imagem") {
                    $anexo = new Anexo($idAnuncio, $caminho, TipoAnexo::IMAGEM);
                    $imagens[] = $anexo;
                } else if ($tipo == "Documento") {
                    $anexo = new Anexo($idAnuncio, $caminho, TipoAnexo::DOCUMENTO);
                    $documentos[] = $anexo;
                } else if ($tipo == "Video") {
                    $anexo = new Anexo($idAnuncio, $caminho, TipoAnexo::VIDEO);
                    $videos[] = $anexo;
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
            $stmt = $this->prepare("
                SELECT * FROM condominio 
                WHERE id_endereco = :id_endereco
            ");

            $stmt->execute([':id_endereco' => $id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe condominio com idEndereco {$id}");
            }

            $idCondominio = (int)$registro['id'];
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

            $stmt = $this->prepare("
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

            $idCondominio = (int)$registro['condominio_id'];
            $nome = $registro['nome'];
            $enderecoObj = new Endereco(
                $registro['rua'],
                $registro['bairro'],
                $registro['cep'],
                $registro['cidade'],
                $registro['uf']
            );
            $enderecoObj->setId($registro['endereco_id']);

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

            $stmt = $this->prepare($sql);
            $stmt->execute([
                ':cep' => $enderecoObj->getCep(),
                ':numero' => $enderecoObj->getNumero()
            ]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Não existe imóvel com este endereço");
            }

            $idEndereco = (int)$registro['id'];
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

            $stmt = $this->prepare("
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


            $idUsuario = (int)$registro['id'];
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

            $stmt = $this->prepare("
            SELECT id_telefone FROM telefone_usuario 
            WHERE id_usuario = ?
        ");
            $stmt->execute([$idUsuario]);

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($registros as $row) {
                $idTelefone = $row['id_telefone'];

                $stmtTel = $this->prepare("
                SELECT numero FROM telefone 
                WHERE id = ?
            ");
                $stmtTel->execute([$idTelefone]);

                $tel = $stmtTel->fetch(PDO::FETCH_ASSOC);
                if ($tel) {
                    $telefones[] = $tel['numero'];
                }
            }

            switch ($tipo) {

                case 'CORRETOR':
                    $stmt = $this->prepare("
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

                    $stmt = $this->prepare("
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

                    $stmt = $this->prepare("
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
                    $tipo = Tipo::tryFrom($tipo) ?? null;
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

            $stmt = $this->prepare($sql);
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

            $stmt = $this->prepare($sql);
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

            $stmt = $this->prepare($sql);
            $stmt->execute([
                $proprietario->getEmail(),
                $proprietario->getNome(),
                $proprietario->getCpfCnpj(),
                $proprietario->getRg(),
                $idEndereco,
                $data
            ]);

            $idProprietario = $this->lastInsertId();

            // Telefones
            if ($proprietario->getTelefones()) {
                foreach ($proprietario->getTelefones() as $telefone) {

                    $stmtTel = $this->prepare("
                    INSERT INTO telefone (numero) VALUES (?)
                ");
                    $stmtTel->execute([$telefone]);

                    $idTelefone = $this->lastInsertId();

                    $stmtRel = $this->prepare("
                    INSERT INTO telefone_proprietario 
                    (id_proprietario, id_telefone) 
                    VALUES (?, ?)
                ");
                    $stmtRel->execute([$idProprietario, $idTelefone]);
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

            $stmt = $this->prepare($sql);
            $stmt->execute([
                $anuncio->getDescricao(),
                $anuncio->getTitulo()
            ]);

            $idAnuncio = $this->lastInsertId();

            // Imagens
            if ($anuncio->getImagens()) {
                foreach ($anuncio->getImagens() as $img) {
                    $this->cadastrarAnexo($img);
                }
            }

            // Vídeos
            if ($anuncio->getVideos()) {
                foreach ($anuncio->getVideos() as $video) {
                    $this->cadastrarAnexo($video);
                }
            }

            // Documentos
            if ($anuncio->getAnexos()) {
                foreach ($anuncio->getAnexos() as $anexo) {
                    $this->cadastrarAnexo($anexo);
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


            $stmt = $this->prepare("
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

            $endereco->setId((int)$registro['id']);
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


            $stmt = $this->prepare("
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

            $proprietario->setId((int)$registro['id']);
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


            $this->beginTransaction();

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


            $stmt = $this->prepare($sql);
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

            $id_imovel = $this->lastInsertId();


            if ($imovel->getProprietarios()) {
                foreach ($imovel->getProprietarios() as $prop) {
                    $stmtProp = $this->prepare("
                    INSERT INTO proprietario_imovel (id_proprietario, id_imovel)
                    VALUES (?, ?)
                ");
                    $stmtProp->execute([
                        $prop->getId(),
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

            $this->commit();

            return true;
        } catch (Exception $e) {
            // $this->rollBack();
            error_log("ERRO! Banco->cadastrarImovel: " . $e->getMessage());
            return false;
        }
    }



    public function atualizarImovel($imovel)
    {

        try {

            $this->beginTransaction();


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

                    $stmt = $this->prepare("
                    DELETE FROM proprietario_imovel
                    WHERE id_proprietario = :id_proprietario
                      AND id_imovel = :id
                ");
                    $stmt->execute([
                        ':id_proprietario' => $p->getId(),
                        ':id' => $imovel->getId()
                    ]);
                }
            }

            foreach ($propsNovos as $p) {
                if (!in_array($p, $propsAntigos)) {

                    $stmt = $this->prepare("
                    INSERT INTO proprietario_imovel (id_proprietario, id_imovel)
                    VALUES (:id_proprietario, :id_imovel)
                ");
                    $stmt->execute([
                        ':id_proprietario' => $p->getId(),
                        ':id_imovel' => $imovel->getId()
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
            WHERE id = :id
        ";

            $stmt = $this->prepare($sql);
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

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            error_log("ERRO Banco->atualizarImovel: " . $e->getMessage());
            return false;
        }
    }

    public function getIdFiltroImovelPorNome($nome)
    {
        try {

            $stmt = $this->prepare("
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

            $stmt = $this->prepare("
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

            $stmt = $this->prepare("
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

            $stmt = $this->prepare("
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

            $stmt = $this->prepare("
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

            $stmt = $this->prepare("
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

            $this->beginTransaction();


            $sql = "
            UPDATE anuncio
            SET descricao = :descricao,
                titulo = :titulo
            WHERE id = :id
        ";

            $stmt = $this->prepare($sql);
            $stmt->execute([
                ':descricao' => $anuncio->getDescricao(),
                ':titulo' => $anuncio->getTitulo(),
                ':id' => $anuncio->getId()
            ]);

            // mídias
            // tratar imagens, vídeos e anexos depois
            // tabela: midia_anuncio

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            error_log("ERRO Banco->atualizarAnuncio: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarCondominio($condominio)
    {

        try {

            $this->beginTransaction();


            $sql = "
            UPDATE condominio
            SET nome = :nome
            WHERE id = :id
        ";

            $stmt = $this->prepare($sql);
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

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            error_log("ERRO Banco->atualizarCondominio: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarUsuario($usuario)
    {
        try {

            $this->beginTransaction();

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

            $stmt = $this->prepare($sql);
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


            $usuarioDb = $usuario->getCpfCnpj() ? $this->getUsuarioPorCpfCnpj(
                $usuario->getCpfCnpj()
            ) : $this->getUsuarioPorId($usuario->getId());

            $telefonesAntigos = $usuarioDb ? ($usuarioDb->getTelefones() ?? []) : [];
            $telefonesNovos = $usuarioDb ? ($usuario->getTelefones() ?? []) : [];



            foreach ($telefonesAntigos as $tel) {
                if (!in_array($tel, $telefonesNovos)) {

                    $stmt = $this->prepare("
                        SELECT id FROM telefone WHERE numero = :numero
                    ");
                    $stmt->execute([':numero' => $tel]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        $id_tel = $row['id_telefone'];

                        $stmt = $this->prepare("
                            DELETE FROM telefone_usuario
                            WHERE id_telefone = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);

                        $stmt = $this->prepare("
                            DELETE FROM telefone
                            WHERE id = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);
                    }
                }
            }


            foreach ($telefonesNovos as $tel) {
                if (!in_array($tel, $telefonesAntigos)) {

                    $stmt = $this->prepare("
                        INSERT INTO telefone (numero) VALUES (:numero)
                    ");
                    $stmt->execute([':numero' => $tel]);

                    $id_tel = $this->lastInsertId();

                    $stmt = $this->prepare("
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

                $stmt = $this->prepare("
                    UPDATE corretor
                    SET creci = :creci
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':creci' => $usuario->getCreci(),
                    ':id' => $usuario->getId()
                ]);
            } elseif ($tipoUsuario === "CAPTADOR") {

                $stmt = $this->prepare("
                    UPDATE captador
                    SET salario = :salario
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':salario' => $usuario->getSalario(),
                    ':id' => $usuario->getId()
                ]);
            } elseif ($tipoUsuario === "GERENTE") {

                $stmt = $this->prepare("
                    UPDATE gerente
                    SET salario = :salario
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':salario' => $usuario->getSalario(),
                    ':id' => $usuario->getId()
                ]);
            }

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            error_log("ERRO Banco->atualizarUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarProprietario($proprietario)
    {

        try {

            $this->beginTransaction();

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

            $stmt = $this->prepare($sql);
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

                    $stmt = $this->prepare("
                        SELECT id FROM telefone WHERE numero = :numero
                    ");
                    $stmt->execute([':numero' => $tel]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        $id_tel = $row['id'];

                        $stmt = $this->prepare("
                            DELETE FROM telefone_proprietario
                            WHERE id_telefone = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);

                        $stmt = $this->prepare("
                            DELETE FROM telefone
                            WHERE id = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);
                    }
                }
            }

            foreach ($telefonesNovos as $tel) {
                if (!in_array($tel, $telefonesAntigos)) {

                    $stmt = $this->prepare("
                        INSERT INTO telefone (numero) VALUES (:numero)
                    ");
                    $stmt->execute([':numero' => $tel]);

                    $id_tel = $this->lastInsertId();

                    $stmt = $this->prepare("
                        INSERT INTO telefone_proprietario (id_proprietario, id_telefone)
                        VALUES (:id_prop, :id_tel)
                    ");
                    $stmt->execute([
                        ':id_prop' => $proprietario->getId(),
                        ':id_tel' => $id_tel
                    ]);
                }
            }

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            error_log("ERRO Banco->atualizarProprietario: " . $e->getMessage());
            return false;
        }
    }

    public function montarImovel($dados, $idImovel)
    {
        try {
            $endereco = null;
            if ($dados['endereco_id']) {
                $endereco = new Endereco(
                    $dados['endereco_rua'],
                    $dados['endereco_bairro'],
                    $dados['endereco_cep'],
                    $dados['endereco_cidade'],
                    $dados['endereco_uf']
                );
                $endereco->setId((int)$dados['endereco_id']);
                $endereco->setNumero($dados['endereco_numero'] !== null ? (int)$dados['endereco_numero'] : null);
                $endereco->setComplemento($dados['endereco_complemento']);
            }

            $corretor = null;
            if ($dados['corretor_id']) {
                $corretor = new Corretor(
                    $dados['corretor_username'],
                    $dados['corretor_senha'],
                    $dados['corretor_email'],
                    $dados['corretor_nome'],
                    $dados['corretor_cpf_cnpj'],
                    (string)($dados['corretor_creci'] ?? '')
                );
                $corretor->setId((int)$dados['corretor_id']);
                $corretor->setRg($dados['corretor_rg'] ?? '');
            }

            $captador = null;
            if ($dados['captador_id']) {
                $captador = new Captador(
                    $dados['captador_username'],
                    $dados['captador_senha'],
                    $dados['captador_email'],
                    $dados['captador_nome'],
                    $dados['captador_cpf_cnpj']
                );
                $captador->setId((int)$dados['captador_id']);
                $captador->setRg($dados['captador_rg'] ?? '');
                if ($dados['captador_salario'] !== null) {
                    $captador->setSalario((float)$dados['captador_salario']);
                }
            }

            $anuncio = null;
            if ($dados['anuncio_id']) {
                $anuncio = new Anuncio();
                $anuncio->setId((int)$dados['anuncio_id']);
                $anuncio->setDescricao($dados['anuncio_descricao'] ?? '');
                $anuncio->setTitulo($dados['anuncio_titulo'] ?? '');

                $stmtAnexos = $this->prepare("
                    SELECT id, nome_arquivo, tipo
                    FROM midia_anuncio
                    WHERE id_anuncio = :id_anuncio
                ");
                $stmtAnexos->execute([':id_anuncio' => $dados['anuncio_id']]);

                $imagens = [];
                $videos = [];
                $documentos = [];

                while ($anexo = $stmtAnexos->fetch(PDO::FETCH_ASSOC)) {
                    $anexoId = (int)$anexo['id'];
                    if ($anexo['tipo'] === 'imagem') {
                        $anexo = new Anexo($dados['anuncio_id'], $anexo['nome_arquivo'], TipoAnexo::IMAGEM);
                        $imagens[] = $anexo;
                    } elseif ($anexo['tipo'] === 'video') {
                        $anexo = new Anexo($dados['anuncio_id'], $anexo['nome_arquivo'], TipoAnexo::VIDEO);
                        $videos[] = $anexo;
                    } elseif ($anexo['tipo'] === 'documento') {
                        $anexo = new Anexo($dados['anuncio_id'], $anexo['nome_arquivo'], TipoAnexo::DOCUMENTO);
                        $documentos[] = $anexo;
                    }
                }

                $anuncio->setImagens($imagens);
                $anuncio->setVideos($videos);
                $anuncio->setAnexos($documentos);
            }

            $condominio = null;
            if ($dados['condominio_id']) {
                $enderecoCondominio = null;
                if ($dados['condominio_endereco_id']) {
                    $enderecoCondominio = new Endereco(
                        $dados['condominio_endereco_rua'],
                        $dados['condominio_endereco_bairro'],
                        $dados['condominio_endereco_cep'],
                        $dados['condominio_endereco_cidade'],
                        $dados['condominio_endereco_uf']
                    );
                    $enderecoCondominio->setId((int)$dados['condominio_endereco_id']);
                    $enderecoCondominio->setNumero($dados['condominio_endereco_numero'] !== null ? (int)$dados['condominio_endereco_numero'] : null);
                    $enderecoCondominio->setComplemento($dados['condominio_endereco_complemento']);
                }

                $condominio = new Condominio(
                    $dados['condominio_nome'],
                    $enderecoCondominio
                );
                $condominio->setId((int)$dados['condominio_id']);
            }

            $dataCadastro = $dados['data_cadastro']
                ? new DateTime($dados['data_cadastro'])
                : null;

            $dataModificacao = $dados['data_modificacao']
                ? new DateTime($dados['data_modificacao'])
                : null;

            $imovelObj = new Imovel($endereco, Status::tryFrom($dados['status']), Categoria::tryFrom($dados['categoria']));

            $imovelObj->setId((int)$dados['id']);
            $imovelObj->setValorVenda($dados['valor_venda'] !== null ? (float)$dados['valor_venda'] : 0);
            $imovelObj->setValorAluguel($dados['valor_aluguel'] !== null ? (float)$dados['valor_aluguel'] : 0);
            $imovelObj->setQuantQuartos($dados['quant_quartos'] !== null ? (int)$dados['quant_quartos'] : 0);
            $imovelObj->setQuantSalas($dados['quant_salas'] !== null ? (int)$dados['quant_salas'] : 0);
            $imovelObj->setQuantVagas($dados['quant_vagas'] !== null ? (int)$dados['quant_vagas'] : 0);
            $imovelObj->setQuantBanheiros($dados['quant_banheiros'] !== null ? (int)$dados['quant_banheiros'] : 0);
            $imovelObj->setQuantVarandas($dados['quant_varandas'] !== null ? (int)$dados['quant_varandas'] : 0);
            $imovelObj->setIptu($dados['iptu'] !== null ? (float)$dados['iptu'] : 0);
            $imovelObj->setValorCondominio($dados['valor_condominio'] !== null ? (float)$dados['valor_condominio'] : 0);
            $imovelObj->setAndar($dados['andar'] !== null ? (int)$dados['andar'] : 0);
            $imovelObj->setEstado($dados['estado'] ? Estado::tryFrom($dados['estado']) : null);
            $imovelObj->setBloco($dados['bloco']);
            $imovelObj->setAnoConstrucao($dados['ano_construcao'] !== null ? (int)$dados['ano_construcao'] : 0);
            $imovelObj->setAreaTotal($dados['area_total'] !== null ? (float)$dados['area_total'] : 0);
            $imovelObj->setAreaPrivativa($dados['area_privativa'] !== null ? (float)$dados['area_privativa'] : 0);
            $imovelObj->setSituacao($dados['situacao'] ? Situacao::tryFrom($dados['situacao']) : null);
            $imovelObj->setOcupacao($dados['ocupacao'] ? Ocupacao::tryFrom($dados['ocupacao']) : null);
            $imovelObj->setCorretor($corretor);
            $imovelObj->setCaptador($captador);
            $imovelObj->setDataCadastro($dataCadastro);
            $imovelObj->setDataModificacao($dataModificacao);
            $imovelObj->setAnuncio($anuncio);
            $imovelObj->setCondominio($condominio);

            $stmt = $this->prepare("
                SELECT
                    p.id,
                    p.email,
                    p.nome,
                    p.cpf_cnpj,
                    p.rg,
                    p.id_endereco,
                    p.data_nascimento,
                    e.rua AS endereco_rua,
                    e.numero AS endereco_numero,
                    e.complemento AS endereco_complemento,
                    e.bairro AS endereco_bairro,
                    e.cep AS endereco_cep,
                    e.cidade AS endereco_cidade,
                    e.uf AS endereco_uf
                FROM proprietario p
                INNER JOIN proprietario_imovel pi
                    ON pi.id_proprietario = p.id
                LEFT JOIN endereco e
                    ON e.id = p.id_endereco
                WHERE pi.id_imovel = :id
            ");
            $stmt->execute([':id' => $idImovel]);

            $proprietarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dataNascimento = $row['data_nascimento']
                    ? DateTime::createFromFormat('Y-m-d', $row['data_nascimento'])
                    : null;

                $prop = new Proprietario(
                    $row['email'],
                    $row['nome'],
                    $row['cpf_cnpj']
                );

                $prop->setId((int)$row['id']);
                $prop->setRg($row['rg']);
                $prop->setDataNascimento($dataNascimento);

                if (!empty($row['id_endereco'])) {
                    $enderecoProprietario = new Endereco(
                        $row['endereco_rua'],
                        $row['endereco_bairro'],
                        $row['endereco_cep'],
                        $row['endereco_cidade'],
                        $row['endereco_uf']
                    );
                    $enderecoProprietario->setId((int)$row['id_endereco']);
                    $enderecoProprietario->setNumero($row['endereco_numero'] !== null ? (int)$row['endereco_numero'] : null);
                    $enderecoProprietario->setComplemento($row['endereco_complemento']);
                    $prop->setEndereco($enderecoProprietario);
                }

                $proprietarios[] = $prop;
            }
            $imovelObj->setProprietarios($proprietarios);

            $stmt = $this->prepare("
                SELECT fi.nome
                FROM imovel_filtros ifi
                JOIN filtros_imovel fi
                    ON fi.id = ifi.id_filtros_imovel
                WHERE ifi.id_imovel = :id
            ");
            $stmt->execute([':id' => $idImovel]);

            $filtros = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $filtros[] = $row['nome'];
            }
            $imovelObj->setFiltros($filtros);

            return $imovelObj;
        } catch (Exception $e) {
            error_log("ERRO! Banco-> montarImovel: " . $e->getMessage());
            return null;
        }
    }

    public function getListaImoveis()
    {

        try {

            $sql = "
            SELECT
                i.*,

                e.id AS endereco_id,
                e.rua AS endereco_rua,
                e.numero AS endereco_numero,
                e.complemento AS endereco_complemento,
                e.bairro AS endereco_bairro,
                e.cep AS endereco_cep,
                e.cidade AS endereco_cidade,
                e.uf AS endereco_uf,

                c.id AS condominio_id,
                c.nome AS condominio_nome,
                ce.id AS condominio_endereco_id,
                ce.rua AS condominio_endereco_rua,
                ce.numero AS condominio_endereco_numero,
                ce.complemento AS condominio_endereco_complemento,
                ce.bairro AS condominio_endereco_bairro,
                ce.cep AS condominio_endereco_cep,
                ce.cidade AS condominio_endereco_cidade,
                ce.uf AS condominio_endereco_uf,

                u_cor.id AS corretor_id,
                u_cor.username AS corretor_username,
                u_cor.senha AS corretor_senha,
                u_cor.email AS corretor_email,
                u_cor.nome AS corretor_nome,
                u_cor.cpf_cnpj AS corretor_cpf_cnpj,
                u_cor.rg AS corretor_rg,
                co.creci AS corretor_creci,

                u_cap.id AS captador_id,
                u_cap.username AS captador_username,
                u_cap.senha AS captador_senha,
                u_cap.email AS captador_email,
                u_cap.nome AS captador_nome,
                u_cap.cpf_cnpj AS captador_cpf_cnpj,
                u_cap.rg AS captador_rg,
                ca.salario AS captador_salario,

                a.id AS anuncio_id,
                a.descricao AS anuncio_descricao,
                a.titulo AS anuncio_titulo

            FROM imovel i

            LEFT JOIN endereco e
                ON e.id = i.id_endereco

            LEFT JOIN condominio c
                ON c.id = i.id_condominio

            LEFT JOIN endereco ce
                ON ce.id = c.id_endereco

            LEFT JOIN usuario u_cor
                ON u_cor.id = i.id_corretor

            LEFT JOIN corretor co
                ON co.id_usuario = u_cor.id

            LEFT JOIN usuario u_cap
                ON u_cap.id = i.id_captador

            LEFT JOIN captador ca
                ON ca.id_usuario = u_cap.id

            LEFT JOIN anuncio a
                ON a.id = i.id_anuncio

            ";

            $stmt = $this->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                throw new Exception("Não há imóveis disponíveis");
            }

            $lista = [];

            foreach ($resultados as $dados) {

                $id = (int)$dados['id'];
                $imovel = $this->montarImovel($dados, $id);
                if ($imovel) {
                    $lista[] = $imovel;
                }
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
            SELECT
                i.*,

                e.id AS endereco_id,
                e.rua AS endereco_rua,
                e.numero AS endereco_numero,
                e.complemento AS endereco_complemento,
                e.bairro AS endereco_bairro,
                e.cep AS endereco_cep,
                e.cidade AS endereco_cidade,
                e.uf AS endereco_uf,

                c.id AS condominio_id,
                c.nome AS condominio_nome,
                ce.id AS condominio_endereco_id,
                ce.rua AS condominio_endereco_rua,
                ce.numero AS condominio_endereco_numero,
                ce.complemento AS condominio_endereco_complemento,
                ce.bairro AS condominio_endereco_bairro,
                ce.cep AS condominio_endereco_cep,
                ce.cidade AS condominio_endereco_cidade,
                ce.uf AS condominio_endereco_uf,

                u_cor.id AS corretor_id,
                u_cor.username AS corretor_username,
                u_cor.senha AS corretor_senha,
                u_cor.email AS corretor_email,
                u_cor.nome AS corretor_nome,
                u_cor.cpf_cnpj AS corretor_cpf_cnpj,
                u_cor.rg AS corretor_rg,
                co.creci AS corretor_creci,

                u_cap.id AS captador_id,
                u_cap.username AS captador_username,
                u_cap.senha AS captador_senha,
                u_cap.email AS captador_email,
                u_cap.nome AS captador_nome,
                u_cap.cpf_cnpj AS captador_cpf_cnpj,
                u_cap.rg AS captador_rg,
                ca.salario AS captador_salario,

                a.id AS anuncio_id,
                a.descricao AS anuncio_descricao,
                a.titulo AS anuncio_titulo

            FROM imovel i

            LEFT JOIN endereco e
                ON e.id = i.id_endereco

            LEFT JOIN condominio c
                ON c.id = i.id_condominio

            LEFT JOIN endereco ce
                ON ce.id = c.id_endereco

            LEFT JOIN usuario u_cor
                ON u_cor.id = i.id_corretor

            LEFT JOIN corretor co
                ON co.id_usuario = u_cor.id

            LEFT JOIN usuario u_cap
                ON u_cap.id = i.id_captador

            LEFT JOIN captador ca
                ON ca.id_usuario = u_cap.id

            LEFT JOIN anuncio a
                ON a.id = i.id_anuncio

            WHERE i.status IN ('Venda', 'Aluguel', 'Venda_Aluguel')
        ";

            $stmt = $this->prepare($sql);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                throw new Exception("Não há imóveis disponíveis");
            }

            $lista = [];

            foreach ($resultados as $dados) {

                $id = (int)$dados['id'];

                $imovel = $this->montarImovel($dados, $id);
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
    public function getImovelPorId($idImovel)
    {
        try {
            $sql = "
            SELECT
                i.*,

                e.id AS endereco_id,
                e.rua AS endereco_rua,
                e.numero AS endereco_numero,
                e.complemento AS endereco_complemento,
                e.bairro AS endereco_bairro,
                e.cep AS endereco_cep,
                e.cidade AS endereco_cidade,
                e.uf AS endereco_uf,

                c.id AS condominio_id,
                c.nome AS condominio_nome,
                ce.id AS condominio_endereco_id,
                ce.rua AS condominio_endereco_rua,
                ce.numero AS condominio_endereco_numero,
                ce.complemento AS condominio_endereco_complemento,
                ce.bairro AS condominio_endereco_bairro,
                ce.cep AS condominio_endereco_cep,
                ce.cidade AS condominio_endereco_cidade,
                ce.uf AS condominio_endereco_uf,

                u_cor.id AS corretor_id,
                u_cor.username AS corretor_username,
                u_cor.senha AS corretor_senha,
                u_cor.email AS corretor_email,
                u_cor.nome AS corretor_nome,
                u_cor.cpf_cnpj AS corretor_cpf_cnpj,
                u_cor.rg AS corretor_rg,
                co.creci AS corretor_creci,

                u_cap.id AS captador_id,
                u_cap.username AS captador_username,
                u_cap.senha AS captador_senha,
                u_cap.email AS captador_email,
                u_cap.nome AS captador_nome,
                u_cap.cpf_cnpj AS captador_cpf_cnpj,
                u_cap.rg AS captador_rg,
                ca.salario AS captador_salario,

                a.id AS anuncio_id,
                a.descricao AS anuncio_descricao,
                a.titulo AS anuncio_titulo

            FROM imovel i

            LEFT JOIN endereco e
                ON e.id = i.id_endereco

            LEFT JOIN condominio c
                ON c.id = i.id_condominio

            LEFT JOIN endereco ce
                ON ce.id = c.id_endereco

            LEFT JOIN usuario u_cor
                ON u_cor.id = i.id_corretor

            LEFT JOIN corretor co
                ON co.id_usuario = u_cor.id

            LEFT JOIN usuario u_cap
                ON u_cap.id = i.id_captador

            LEFT JOIN captador ca
                ON ca.id_usuario = u_cap.id

            LEFT JOIN anuncio a
                ON a.id = i.id_anuncio

            WHERE i.id = :id
        ";

            $stmt = $this->prepare($sql);
            $stmt->execute([':id' => $idImovel]);

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) {
                throw new Exception("Imóvel não encontrado");
            }

            return $this->montarImovel($dados, $idImovel);
        } catch (Exception $e) {
            error_log("ERRO! Banco->getImovelPorId: " . $e->getMessage());
            return null;
        }
    }



    public function getImoveisPorProprietario($idProprietario)
    {
        try {
            $stmt = $this->prepare("SELECT id_imovel FROM proprietario_imovel WHERE id_proprietario = ?");
            $stmt->execute([$idProprietario]);
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
