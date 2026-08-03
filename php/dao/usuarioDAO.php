<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/enderecoDAO.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/gerente.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/captador.php';
require_once __DIR__ . '/../model/corretor.php';

class UsuarioDAO
{
    private Banco $bancoDados;
    private EnderecoDAO $enderecoDAO;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
        $this->enderecoDAO = new EnderecoDAO();
    }

    public function getConexao()
    {
        return $this->bancoDados;
    }

    public function cadastrarMensagem(int $id_usuario, string $mensagem)
    {
        try {
            $sqlInsertQuery = "
                INSERT INTO notificacao (id_usuario, mensagem)
                VALUES (:id_usuario, :mensagem);
            ";
            $stmt = $this->bancoDados->prepare($sqlInsertQuery);
            $stmt->execute([':id_usuario' => $id_usuario, ':mensagem' => $mensagem]);
            return true;
        } catch (Exception $e) {
            error_log("ERRO usuarioDAO->cadastrarMensagem: " . $e->getMessage());
            return false;
        }
    }

    public function listarMensagens(int $id_usuario)
    {
        try {
            $sqlSelectQuery = "
                SELECT * FROM notificacao
                WHERE id_usuario = :id_usuario
                ORDER BY data_notificacao DESC;
            ";
            $stmt = $this->bancoDados->prepare($sqlSelectQuery);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("ERRO usuarioDAO->listarMensagens: " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorCpfCnpj(string $cpf)
    {
        try {

            $stmt = $this->bancoDados->prepare("
                        SELECT * FROM usuario WHERE cpf_cnpj = ? 
                    ");
            $stmt->execute([$cpf]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception("Não existe usuário com CPF/CNPJ $cpf");
            }
            $idUsuario = $registro['id'] !== null ? (int) $registro['id'] : null;
            $username = $registro['username'];
            $senha = $registro['senha'];
            $email = $registro['email'];
            $nome = $registro['nome'];
            $cpfCnpj = $registro['cpf_cnpj'];
            $rg = $registro['rg'];
            $endereco = $registro['id_endereco'];
            $dataCadastro = $registro['data_cadastro'] ? new DateTime($registro['data_cadastro']) : null;
            $dataModificacao = $registro['data_modificacao'] ? new DateTime($registro['data_modificacao']) : null;
            if ($endereco) {
                $endereco = $this->enderecoDAO->buscarPorId((int) ($registro['id_endereco']));
            }
            $dataNascimento = $registro['data_nascimento'];
            if ($dataNascimento) {

                $dataNascimento = DateTime::createFromFormat('Y-m-d', $dataNascimento);
            }
            $tipoUsuario = $registro['tipo'];
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
            $stmt = $this->bancoDados->prepare($sqlQuery);
            $stmt->execute([$idUsuario]);
            $registros = $stmt->fetch(PDO::FETCH_ASSOC);
            $telefones = [];
            if ($registros) {
                foreach ($registros as $idTelefone) {
                    $sqlQuery = " 
                            SELECT numero FROM telefone 
                            WHERE id = ?
                                ";
                    $stmt = $this->bancoDados->prepare($sqlQuery);
                    $stmt->execute([$idTelefone]);
                    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            switch ($tipoUsuario) {
                case (Tipo::CORRETOR):
                    $stmt = $this->bancoDados->prepare("
                                    SELECT creci FROM corretor 
                                    WHERE id_usuario = ?
                                ");
                    $stmt->execute([$idUsuario]);
                    $creci = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($creci) {
                        $creci = (int) ($creci);
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
                    $stmt = $this->bancoDados->prepare("
                                    SELECT salario FROM captador 
                                    WHERE id_usuario = ?
                                ");
                    $stmt->execute([$idUsuario]);
                    $salario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($salario) {
                        $salario = (float) ($salario);
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
                    $stmt = $this->bancoDados->prepare("
                                    SELECT salario FROM gerente 
                                    WHERE id_usuario = ?
                                ");
                    $stmt->execute([$idUsuario]);
                    $salario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($salario) {
                        $salario = (float) ($salario);
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

                    # $stmt = $this->bancoDados->prepare("
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
            $usuarioObj->setDataCadastro($dataCadastro);
            $usuarioObj->setDataModificacao($dataModificacao);
            return $usuarioObj;
        } catch (Exception $e) {
            $erro = "ERRO! usuarioDAO->buscarPorCpfCnpj: " . $e->getMessage();
            error_log($erro);
            return NULL;
        }
    }

    public function atualizar(Usuario $usuario)
    {
        try {

            $this->bancoDados->beginTransaction();

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
                    tipo = :tipo,
                    data_modificacao = NOW(),
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

            $stmt = $this->bancoDados->prepare($sql);
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


            $usuarioDb = $usuario->getCpfCnpj() ? $this->buscarPorCpfCnpj(
                $usuario->getCpfCnpj()
            ) : $this->buscarPorId($usuario->getId());

            $telefonesAntigos = $usuarioDb ? ($usuarioDb->getTelefones() ?? []) : [];
            $telefonesNovos = $usuarioDb ? ($usuario->getTelefones() ?? []) : [];



            foreach ($telefonesAntigos as $tel) {
                if (!in_array($tel, $telefonesNovos)) {

                    $stmt = $this->bancoDados->prepare("
                        SELECT id FROM telefone WHERE numero = :numero
                    ");
                    $stmt->execute([':numero' => $tel]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        $id_tel = $row['id_telefone'];

                        $stmt = $this->bancoDados->prepare("
                            DELETE FROM telefone_usuario
                            WHERE id_telefone = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);

                        $stmt = $this->bancoDados->prepare("
                            DELETE FROM telefone
                            WHERE id = :id
                        ");
                        $stmt->execute([':id' => $id_tel]);
                    }
                }
            }


            foreach ($telefonesNovos as $tel) {
                if (!in_array($tel, $telefonesAntigos)) {

                    $stmt = $this->bancoDados->prepare("
                        INSERT INTO telefone (numero) VALUES (:numero)
                    ");
                    $stmt->execute([':numero' => $tel]);

                    $id_tel = $this->bancoDados->lastInsertId();

                    $stmt = $this->bancoDados->prepare("
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

                $stmt = $this->bancoDados->prepare("
                    UPDATE corretor
                    SET creci = :creci
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':creci' => $usuario->getCreci(),
                    ':id' => $usuario->getId()
                ]);
            } elseif ($tipoUsuario === "CAPTADOR") {

                $stmt = $this->bancoDados->prepare("
                    UPDATE captador
                    SET salario = :salario
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':salario' => $usuario->getSalario(),
                    ':id' => $usuario->getId()
                ]);
            } elseif ($tipoUsuario === "GERENTE") {

                $stmt = $this->bancoDados->prepare("
                    UPDATE gerente
                    SET salario = :salario
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':salario' => $usuario->getSalario(),
                    ':id' => $usuario->getId()
                ]);
            }
            return $this->bancoDados->commit();
        } catch (Exception $e) {
            if ($this->bancoDados->inTransaction()) {
                $this->bancoDados->rollBack();
            }
            error_log("ERRO usuarioDAO->atualizar: " . $e->getMessage());
            return false;
        }
    }


    public function cadastrar(Usuario $usuario)
    {
        try {
            $sql = "
                    INSERT INTO usuario (username, senha, email, nome, cpf_cnpj, rg, id_endereco, data_nascimento, tipo, data_cadastro, data_modificacao) 
                    VALUES(:username, :senha, :email, :nome, :cpf_cnpj, :rg, :endereco, :data_nascimento, :tipo, :data_cadastro, :data_modificacao)
                ";
            $stmt = $this->bancoDados->prepare($sql);
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
            if ($usuario->getDataCadastro()) {
                $dataCadastro = $usuario->getDataCadastro()->format("Y-m-d H:i:s");
            } else {
                $dataCadastro = date("Y-m-d H:i:s");
            }
            if ($usuario->getDataModificacao()) {
                $dataModificacao = $usuario->getDataModificacao()->format("Y-m-d H:i:s");
            } else {
                $dataModificacao = NULL;
            }
            $senha_hash = hash('sha256', $usuario->getSenha());
            $resultado = $stmt->execute([
                ':username' => $usuario->getUsername(),
                ':senha' => $senha_hash,
                ':email' => $usuario->getEmail(),
                ':nome' => $usuario->getNome(),
                ':cpf_cnpj' => $usuario->getCpfCnpj(),
                ':rg' => $usuario->getRg(),
                ':endereco' => $endereco,
                ':data_nascimento' => $dataNascimento,
                ':tipo' => $tipo,
                ':data_cadastro' => $dataCadastro,
                ':data_modificacao' => $dataModificacao
            ]);
            $id = $this->bancoDados->lastInsertId();
            if ($usuario->getTelefones()) {
                foreach ($usuario->getTelefones() as $telefone) {
                    try {
                        $stmt = $this->bancoDados->prepare("
                            SELECT id
                            FROM telefone
                            WHERE numero = :numero
                        ");
                        $stmt->execute([
                            ':numero' => $telefone
                        ]);

                        $idTelefone = $stmt->fetchColumn();

                        if (!$idTelefone) {
                            $stmt = $this->bancoDados->prepare("
                                INSERT INTO telefone (numero)
                                VALUES (:numero)
                            ");
                            $stmt->execute([
                                ':numero' => $telefone
                            ]);

                            $idTelefone = $this->bancoDados->lastInsertId();
                        }

                        $stmt = $this->bancoDados->prepare("
                            SELECT 1
                            FROM telefone_usuario
                            WHERE id_usuario = :id_usuario
                            AND id_telefone = :id_telefone
                        ");
                        $stmt->execute([
                            ':id_usuario' => $id,
                            ':id_telefone' => $idTelefone
                        ]);

                        if (!$stmt->fetchColumn()) {
                            $stmt = $this->bancoDados->prepare("
                                INSERT INTO telefone_usuario (id_usuario, id_telefone)
                                VALUES (:id_usuario, :id_telefone)
                            ");
                            $stmt->execute([
                                ':id_usuario' => $id,
                                ':id_telefone' => $idTelefone
                            ]);
                        }
                    } catch (Exception $e) {
                        // error_log("ERRO usuarioDAO->cadastrar TELEFONE: " . $e->getMessage());
                    }
                }
            }
            $tipoUsuarioObj = $usuario->getTipo();
            $tipoUsuarioValor = $tipoUsuarioObj ? $tipoUsuarioObj->value : NULL;
            switch ($tipoUsuarioValor) {
                case "CORRETOR":
                    try {
                        $stmt = $this->bancoDados->prepare("
                                    INSERT INTO corretor (id_usuario, creci)
                                    VALUES(:id_usuario, :creci)
                                ");
                        $stmt->execute([
                            ':id_usuario' => $id,
                            ':creci' => $usuario->getCreci()
                        ]);
                    } catch (Exception $e) {
                        error_log("ERRO usuarioDAO->cadastrar CORRETOR: " . $e->getMessage());
                        return False;
                    }
                    break;
                case "CAPTADOR":
                    try {
                        $stmt = $this->bancoDados->prepare("
                                        INSERT INTO captador (id_usuario, salario)
                                        VALUES(:id_usuario, :salario)
                                ");
                        $stmt->execute([
                            ':id_usuario' => $id,
                            ':salario' => $usuario->getSalario()
                        ]);
                    } catch (Exception $e) {
                        error_log("ERRO usuarioDAO->cadastrar CAPTADOR: " . $e->getMessage());
                        return False;
                    }
                    break;
                case "GERENTE":
                    try {
                        $stmt = $this->bancoDados->prepare("
                                    INSERT INTO gerente (id_usuario, salario)
                                    VALUES(:id_usuario, :salario)
                                ");
                        $stmt->execute([
                            ':id_usuario' => $id,
                            ':salario' => $usuario->getSalario()
                        ]);
                    } catch (Exception $e) {
                        error_log("ERRO usuarioDAO->cadastrar GERENTE: " . $e->getMessage());
                        return False;
                    }
                    break;
                case "CLIENTE":
                    try {
                        $stmt = $this->bancoDados->prepare("
                                    INSERT INTO cliente (id_usuario)
                                    VALUES(:id_usuario)
                                ");
                        $stmt->execute([
                            ':id_usuario' => $id,
                        ]);
                    } catch (Exception $e) {
                        error_log("ERRO usuarioDAO->cadastrar CLIENTE: " . $e->getMessage());
                        return False;
                    }
                    break;
            }
            return $this->bancoDados->lastInsertId();
        } catch (Exception $e) {
            $erro = "ERRO! usuarioDAO->cadastrar " . $e->getMessage();
            error_log($erro);
            return False;
        }
    }


    public function listar()
    {

        try {

            $sql = "SELECT * FROM usuario";
            $stmt = $this->bancoDados->prepare($sql);
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
                $tipo = $registro['tipo'];
                $dataCadastro = $registro['data_cadastro'] ? new DateTime($registro['data_cadastro']) : null;
                $dataModificacao = $registro['data_modificacao'] ? new DateTime($registro['data_modificacao']) : null;

                $endereco = null;
                if ($registro['id_endereco']) {
                    $endereco = $this->enderecoDAO->buscarPorId((int) $registro['id_endereco']);
                }

                $data = $registro['data_nascimento']
                    ? new DateTime($registro['data_nascimento'])
                    : null;

                $stmtTel = $this->bancoDados->prepare("
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

                        $stmtC = $this->bancoDados->prepare("
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

                        $stmtC = $this->bancoDados->prepare("
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
                        $usuario->setSalario($salario ? (float) $salario : 0.0);
                        break;

                    case 'GERENTE':

                        $stmtC = $this->bancoDados->prepare("
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
                        $usuario->setSalario($salario ? (float) $salario : 0.0);
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
                $usuario->setDataCadastro($dataCadastro);
                $usuario->setDataModificacao($dataModificacao);

                $lista[] = $usuario;
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO usuarioDAO->listar: " . $e->getMessage());
            return [];
        }
    }

    public function listarClientes()
    {

        try {

            $sql = "SELECT * FROM usuario WHERE tipoUsuario = 'CLIENTE'";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($dados)) {
                throw new Exception("Não há usuários cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $id = (int) $registro['id'];
                $username = $registro['username'];
                $senha = $registro['senha'];
                $email = $registro['email'];
                $nome = $registro['nome'];
                $cpf = $registro['cpf_cnpj'];
                $rg = $registro['rg'];

                $endereco = null;
                if ($registro['id_endereco']) {
                    $endereco = $this->enderecoDAO->buscarPorId((int) $registro['id_endereco']);
                }

                $data = $registro['data_nascimento']
                    ? new DateTime($registro['data_nascimento'])
                    : null;

                $cliente = new Cliente($username, $senha, $email, $nome, $cpf);

                $cliente->setId($id);
                $cliente->setRg($rg);
                $cliente->setEndereco($endereco);
                $cliente->setDataNascimento($data);

                $stmtTel = $this->bancoDados->prepare("
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
            error_log("ERRO usuarioDAO->listarClientes: " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT * FROM usuario WHERE id = ?";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([$id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$registro) {
                throw new Exception("Não existe usuário com ID $id");
            }
            $idUsuario = $registro['id'] !== null ? (int) $registro['id'] : null;
            $username = $registro['username'];
            $senha = $registro['senha'];
            $email = $registro['email'];
            $nome = $registro['nome'];
            $cpfCnpj = $registro['cpf_cnpj'];
            $rg = $registro['rg'];
            $dataModificacao = $registro['data_modificacao'] ? new DateTime($registro['data_modificacao']) : null;
            $dataCadastro = $registro['data_cadastro'] ? new DateTime($registro['data_cadastro']) : null;
            $endereco = $registro['id_endereco'] !== null ? $this->enderecoDAO->buscarPorId((int) $registro['id_endereco']) : null;
            $dataNascimento = $registro['data_nascimento'];
            if ($dataNascimento) {
                $dataNascimento = DateTime::createFromFormat('Y-m-d', $dataNascimento);
            }
            $tipoUsuario = $registro['tipo'];
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
            $stmt = $this->bancoDados->prepare($sqlQuery);
            $stmt->execute([':id_usuario' => $idUsuario]);
            $registros = $stmt->fetch(PDO::FETCH_ASSOC);
            $telefones = [];
            if ($registros) {
                foreach ($registros as $idTelefone) {
                    $sqlQuery = " 
                            SELECT numero FROM telefone 
                            WHERE id = :id_telefone
                                ";
                    $stmt = $this->bancoDados->prepare($sqlQuery);
                    $stmt->execute([':id_telefone' => $idTelefone]);
                    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            switch ($tipoUsuario) {
                case (Tipo::CORRETOR):
                    $stmt = $this->bancoDados->prepare("
                                    SELECT creci FROM corretor 
                                    WHERE id_usuario = :id_usuario
                                ");
                    $stmt->execute([':id_usuario' => $idUsuario]);
                    $creci = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($creci) {
                        $creci = (int) ($creci);
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
                    $stmt = $this->bancoDados->prepare("
                                    SELECT salario FROM captador 
                                    WHERE id_usuario = :id_usuario
                                ");
                    $stmt->execute([':id_usuario' => $idUsuario]);
                    $salario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($salario) {
                        $salario = (float) ($salario);
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
                    $stmt = $this->bancoDados->prepare("
                                    SELECT salario FROM gerente 
                                    WHERE id_usuario = :id_usuario
                                ");
                    $stmt->execute([':id_usuario' => $idUsuario]);
                    $salario = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($salario) {
                        $salario = (float) ($salario);
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

                    # $stmt = $this->bancoDados->prepare("
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
            $usuarioObj->setDataCadastro($dataCadastro);
            $usuarioObj->setDataModificacao($dataModificacao);
            return $usuarioObj;
        } catch (Exception $e) {
            error_log("ERRO usuarioDAO->buscarPorId: " . $e->getMessage());
            return null;
        }
    }


    public function verificar($username, $senha, bool $google = false)
    {
        try {

            $stmt = $this->bancoDados->prepare("
            SELECT * FROM usuario WHERE username = :username
        ");
            $stmt->execute([':username' => $username]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Usuário não encontrado");
            }

            $senha_hash_banco = "";

            if (!$google) {

                $senha_hash_banco = $registro['senha'];

                $senha_hash = hash('sha256', $senha);

                if ($senha_hash_banco !== $senha_hash) {
                    throw new Exception("Senha errada!");
                }
            }

            $idUsuario = (int) $registro['id'];
            $username = $registro['username'];
            $email = $registro['email'];
            $nome = $registro['nome'];
            $cpfCnpj = $registro['cpf_cnpj'];
            $rg = $registro['rg'];
            $idEndereco = $registro['id_endereco'];
            $dataNascimento = $registro['data_nascimento'] ? DateTime::createFromFormat('Y-m-d', $registro['data_nascimento']) : null;
            $tipo = $registro['tipo'];
            $dataCadastro = $registro['data_cadastro'] ? new DateTime($registro['data_cadastro']) : null;
            $dataModificacao = $registro['data_modificacao'] ? new DateTime($registro['data_modificacao']) : null;

            $endereco = null;
            if ($idEndereco) {
                $endereco = $this->enderecoDAO->buscarPorId($idEndereco);
            }

            $telefones = [];

            $stmt = $this->bancoDados->prepare("
            SELECT id_telefone FROM telefone_usuario 
            WHERE id_usuario = ?
        ");
            $stmt->execute([$idUsuario]);

            $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($registros as $row) {
                $idTelefone = $row['id_telefone'];

                $stmtTel = $this->bancoDados->prepare("
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
                    $stmt = $this->bancoDados->prepare("
                    SELECT creci FROM corretor 
                    WHERE id_usuario = ?
                ");
                    $stmt->execute([$idUsuario]);

                    $creci = $stmt->fetchColumn();
                    $creci = $creci ? (int) $creci : null;

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

                    $stmt = $this->bancoDados->prepare("
                    SELECT salario FROM captador 
                    WHERE id_usuario = ?
                ");
                    $stmt->execute([$idUsuario]);

                    $salario = $stmt->fetchColumn();
                    if ($salario) {
                        $usuarioObj->setSalario((float) $salario);
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

                    $stmt = $this->bancoDados->prepare("
                    SELECT salario FROM gerente 
                    WHERE id_usuario = ?
                ");
                    $stmt->execute([$idUsuario]);

                    $salario = $stmt->fetchColumn();
                    if ($salario) {
                        $usuarioObj->setSalario((float) $salario);
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
            $usuarioObj->setDataCadastro($dataCadastro);
            $usuarioObj->setDataModificacao($dataModificacao);

            return $usuarioObj;
        } catch (Exception $e) {
            $erro = "ERRO! usuarioDAO->verificar: " . $e->getMessage();
            error_log($erro);
            return null;
        }
    }
}
