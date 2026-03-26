<?php

require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/captador.php';
require_once __DIR__ . '/../model/atendimento.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/venda_aluguel.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/gerente.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/proprietario.php';

class Banco {
    private $db;

    public function __construct(){       
        $this->db = new PDO('sqlite:data/imobiliaria.db');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->init_tabelas();
    }

    public function init_tabelas(){

        $queries = [

            "CREATE TABLE IF NOT EXISTS usuario (
                id_usuario INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE,
                senha TEXT NOT NULL,
                email TEXT UNIQUE,
                nome TEXT NOT NULL,
                cpf_cnpj TEXT UNIQUE NOT NULL,
                rg TEXT,
                id_endereco INTEGER,
                data_nascimento TEXT,
                tipo_usuario TEXT NOT NULL
            )",

            "CREATE TABLE IF NOT EXISTS telefone (
                id_telefone INTEGER PRIMARY KEY AUTOINCREMENT,
                numero TEXT NOT NULL UNIQUE
            )",

            "CREATE TABLE IF NOT EXISTS telefone_usuario (
                id_usuario INTEGER,
                id_telefone INTEGER,
                FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
                FOREIGN KEY (id_telefone) REFERENCES telefone(id_telefone) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS endereco (
                id_endereco INTEGER PRIMARY KEY AUTOINCREMENT,
                rua TEXT NOT NULL,
                numero INTEGER,
                bairro TEXT NOT NULL,
                cep TEXT NOT NULL,
                complemento TEXT,
                cidade TEXT NOT NULL,
                uf TEXT NOT NULL
            )",

            "CREATE TABLE IF NOT EXISTS proprietario (
                id_proprietario INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT UNIQUE,
                nome TEXT NOT NULL,
                cpf_cnpj TEXT UNIQUE,
                rg TEXT,
                id_endereco INTEGER,
                data_nascimento TEXT
            )",

            "CREATE TABLE IF NOT EXISTS anuncio (
                id_anuncio INTEGER PRIMARY KEY AUTOINCREMENT,
                descricao TEXT,
                titulo TEXT       
            )",

            "CREATE TABLE IF NOT EXISTS imovel (
                id_imovel INTEGER PRIMARY KEY AUTOINCREMENT,
                valor_venda REAL,
                valor_aluguel REAL,
                quant_quartos INTEGER,
                quant_salas INTEGER,
                quant_vagas INTEGER,
                quant_banheiros INTEGER,
                quant_varandas INTEGER,
                categoria TEXT NOT NULL,
                id_endereco INTEGER,
                status TEXT NOT NULL,
                iptu REAL,
                valor_condominio REAL,
                andar INTEGER,
                estado TEXT,
                bloco TEXT,
                ano_construcao INTEGER,
                area_total REAL,
                area_privativa REAL,
                situacao TEXT,
                ocupacao TEXT,
                data_cadastro TEXT,
                data_modificacao TEXT,
                id_anuncio INTEGER,
                FOREIGN KEY (id_anuncio) REFERENCES anuncio(id_anuncio),
                FOREIGN KEY (id_endereco) REFERENCES endereco(id_endereco)
            )",

            "CREATE TABLE IF NOT EXISTS midia_anuncio (
                id_anuncio INTEGER,
                midia BLOB,
                tipo TEXT,
                FOREIGN KEY (id_anuncio) REFERENCES anuncio(id_anuncio) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS condominio (
                id_condominio INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT,
                id_endereco INTEGER,
                FOREIGN KEY (id_endereco) REFERENCES endereco(id_endereco)
            )"

        ];

        foreach ($queries as $sql) {
            $this->db->exec($sql);
        }
    }


    public function get_lista_enderecos(){

        try {
            $sql = "SELECT * FROM endereco";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$dados) {
                throw new Exception("Não há endereços cadastrados");
            }

            $lista = [];

            foreach ($dados as $registro) {

                $id_endereco = (int) $registro['id_endereco'];
                $rua = $registro['rua'];
                $numero = $registro['numero'] !== null ? (int)$registro['numero'] : null;
                $bairro = $registro['bairro'];
                $cep = $registro['cep'] !== null ? $registro['cep'] : null;
                $complemento = $registro['complemento'];
                $cidade = $registro['cidade'];
                $uf = $registro['uf'];

                $endereco_obj = new Endereco($rua, $bairro, $cep, $cidade, $uf);

                $endereco_obj->set_id($id_endereco);
                $endereco_obj->set_numero($numero);
                $endereco_obj->set_complemento($complemento);

                $lista[] = $endereco_obj;
            }

            return $lista;

        } catch (Exception $e) {
            echo "ERRO! Banco.get_lista_enderecos: " . $e->getMessage();
            return [];
        }
    }

    public function get_lista_proprietarios(){

    try {

        $sql = "SELECT * FROM proprietario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$dados) {
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

            $obj->set_id($id);
            $obj->set_rg($rg);
            $obj->set_data_nascimento($data);

            $lista[] = $obj;
        }

        return $lista;

    } catch (Exception $e) {
        echo "ERRO Banco.get_lista_proprietarios: " . $e->getMessage();
        return [];
    }
}
public function get_lista_clientes(){

    try {

        $sql = "SELECT * FROM usuario WHERE tipo_usuario = 'CLIENTE'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$dados) {
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
                $endereco = $this->get_endereco_por_id((int)$registro['id_endereco']);
            }

            $data = $registro['data_nascimento'] 
                ? new DateTime($registro['data_nascimento']) 
                : null;

            $cliente = new Cliente($username, $senha, $email, $nome, $cpf);

            $cliente->set_id($id);
            $cliente->set_rg($rg);
            $cliente->set_endereco($endereco);
            $cliente->set_data_nascimento($data);

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

            $cliente->set_telefones($telefones);

            $lista[] = $cliente;
        }

        return $lista;

    } catch (Exception $e) {
        echo "ERRO Banco.get_lista_clientes: " . $e->getMessage();
        return [];
    }
}
 public function get_lista_usuarios(){

    try {

        $sql = "SELECT * FROM usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$dados) {
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
            $tipo = $registro['tipo_usuario'];

            $endereco = null;
            if ($registro['id_endereco']) {
                $endereco = $this->get_endereco_por_id((int)$registro['id_endereco']);
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
                        $username, $senha, $email, $nome, $cpf, $creci
                    );
                    break;

                case 'CAPTADOR':

                    $stmtC = $this->db->prepare("
                        SELECT salario FROM captador WHERE id_usuario = :id
                    ");
                    $stmtC->execute([':id' => $id]);
                    $salario = $stmtC->fetchColumn();

                    $usuario = new Captador(
                        $username, $senha, $email, $nome, $cpf
                    );
                    $usuario->set_salario($salario ? (float)$salario : null);
                    break;

                case 'GERENTE':

                    $stmtC = $this->db->prepare("
                        SELECT salario FROM gerente WHERE id_usuario = :id
                    ");
                    $stmtC->execute([':id' => $id]);
                    $salario = $stmtC->fetchColumn();

                    $usuario = new Gerente(
                        $username, $senha, $email, $nome, $cpf
                    );
                    $usuario->set_salario($salario ? (float)$salario : null);
                    break;

                case 'CLIENTE':

                    $usuario = new Cliente(
                        $username, $senha, $email, $nome, $cpf
                    );
                    break;

                default:

                    $usuario = new Usuario(
                        $username, $senha, $email, $nome, $cpf, $tipo
                    );
                    break;
            }

            $usuario->set_id($id);
            $usuario->set_rg($rg);
            $usuario->set_endereco($endereco);
            $usuario->set_data_nascimento($data);
            $usuario->set_telefones($telefones);

            $lista[] = $usuario;
        }

        return $lista;

    } catch (Exception $e) {
        echo "ERRO Banco.get_lista_usuarios: " . $e->getMessage();
        return [];
    }
}

    public function cadastrar_usuario($usuario){
        try {
                $sql ="
                    INSERT INTO usuario (username, senha, email, nome, cpf_cnpj, rg, id_endereco, data_nascimento, tipo_usuario) 
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                if ($usuario.get_endereco()) {
                    $endereco = $usuario.get_endereco().get_id();
                } else {
                    $endereco = NULL;
                }
                if ($usuario.get_tipo()) {
                    $tipo = $usuario.get_tipo().value;
                } else {
                    $tipo = NULL;
                }
                if ($usuario.get_data_nascimento()) {
                    $data_nascimento = $usuario.get_data_nascimento().strftime("%d-%m-%Y");
                }else{
                    $data_nascimento = NULL;
                }
                $senha_hash = hash('sha256', $usuario.get_senha());
                $stmt->execute([
                    ':username' => $usuario.get_username(),
                    ':senha' => $senha_hash,
                    ':email' => $usuario.get_email(),
                    ':nome' => $usuario.get_nome(),
                    ':cpf_cnpj' => $usuario.get_cpf_cnpj(),
                    ':rg' => $usuario.get_rg(),
                    ':endereco' => $endereco,
                    ':data_nascimento' => $data_nascimento,
                    ':tipo' => $tipo
                ]);
                $id = $this->db->lastInsertId();
                if ($usuario.get_telefones()){
                    foreach ($usuario.get_telefones() as $telefone){
                        $sql_query = " 
                            INSERT INTO telefone (numero) 
                            VALUES(?)
                            ";
                        $stmt = $db->prepare($sql_query);
                        $stmt->execute([
                            ':numero' => $telefone,
                        ]);
                        $id_telefone = cursor.lastrowid;
                        $sql_query = " 
                            INSERT INTO telefone_usuario (id_usuario, id_telefone) 
                            VALUES(?, ?)
                            ";
                        $stmt = $db->prepare($sql_query);
                            $stmt->execute([
                            ':id_usuario' => $telefone,
                            ':id_telefone' => $id_telefone
                        ]);
                    }
                }
                $tipo_usuario_obj = $usuario.get_tipo();
                $tipo_usuario_valor = $tipo_usuario_obj ? $tipo_usuario_obj->value($tipo_usuario_obj) : NULL;
                if ($tipo_usuario_valor == "CORRETOR") {
                        $stmt = $db->prepare("
                                    INSERT INTO corretor (id_usuario, creci)
                                    VALUES(?, ?)
                                ");
                        $stmt->execute([
                            ':id_usuario' => $id,
                            ':creci' => $usuario.get_creci()
                        ]);
                }else if ($tipo_usuario_valor == "CAPTADOR") {
                        $stmt = $db->prepare("
                                    INSERT INTO captador (id_usuario, salario)
                                    VALUES(?, ?)
                                ");
                        $stmt->execute([
                            ':id_usuario' => $id,
                            ':salario' => $usuario.get_salario()
                        ]);
                }else if ($tipo_usuario_valor == "GERENTE") {
                        $stmt = $db->prepare("
                                    INSERT INTO gerente (id_usuario, salario)
                                    VALUES(?, ?)
                                ");
                        $stmt->execute([
                            ':id_usuario' => $id,
                            ':salario' => $usuario.get_salario()
                        ]);
                }else if ($tipo_usuario_valor == "CLIENTE") {
                        $stmt = $db->prepare("
                                    INSERT INTO cliente (id_usuario)
                                    VALUES(?)
                                ");
                        $stmt->execute([
                            ':id_usuario' => $id,
                            
                        ]);
                }
                return True;
        } catch (Exception $e) {
            // erro = "ERRO! Banco.cadastrar_usuario{ {e}";
            print(erro);
            return False;
        }
    }
               

    public function remover($campo_desejado, $valor, $tabela){
        try{
                $sql_delete_query = "
                DELETE FROM $tabela
                WHERE $campo_desejado = ?;
                ";
                $stmt = $db->prepare($sql_delete_query);
                $stmt->execute([
                    ":$campo_desejado" => $valor
                ]);
                return True;
        } catch (Exception $e) {
            // print("ERRO Banco.remover {tabela} - {valor} {e}")
            return False;
        }
    }

    public function atualizar($campo_desejado, $valor, $tabela){
        try {
                $sql_update_query = "
                UPDATE $tabela
                SET $campo_desejado = ?
                ";
                $stmt = $db->prepare($sql_update_query);
                $stmt->execute([
                        ":$campo_desejado" => $valor,
                    ]);
                $db->commit();
                return True;
        } catch (Exception $e) {
            // print("ERRO Banco.atualizar {tabela} - {valor} {e}")
            return False;
        }
    }

    public function get_usuario_por_cpf_cnpj($cpf){
        try{
          
                $stmt = $db->prepare("
                        SELECT * FROM usuario WHERE cpf_cnpj = ? 
                    ");
                $stmt->execute([
                        ':cpf_cnpj' => $cpf
                    ]);
                $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$registro) {
                    throw new Exception("Não existe usuário com CPF/CNPJ $cpf");
                }
                $id_usuario = $registro['id_usuario'] !== null ? (int)$registro['id_usuario'] : null;
                $username = $registro['username'];
                $senha = $registro['senha'];
                $email = $registro['email'];
                $nome = $registro['nome'];
                $cpf_cnpj = $registro['cpf_cnpj'];
                $rg = $registro['rg'];
                $endereco = $registro['endereco'];
                if ($endereco){
                    $endereco = $this.get_endereco_por_id(int($registro['id_endereco']));
                }
                $data_nascimento = $registro['data_nascimento'];
                if ($data_nascimento){
                    $data_nascimento = datetime.strptime(
                        $data_nascimento, "%d-%m-%Y");
                }
                $tipo_usuario = $registro['tipo_usuario'];
                if ($tipo_usuario){
                    $tipo_usuario = usuario.Tipo($tipo_usuario);
                }
                $usuario_obj = usuario.Usuario(
                    $username, $senha, $email, $nome, $cpf_cnpj, $tipo_usuario);
                $sql_query = " 
                            SELECT id_telefone FROM telefone_usuario 
                            WHERE id_usuario = ?
                            ";
                $stmt = $db->prepare($sql_query);
                $stmt->execute([
                        ':id_usuario' => $id_usuario
                    ]);
                $registros = $stmt->fetch(PDO::FETCH_ASSOC);
                $telefones = [];
                if ($registros){
                    foreach ($registros as $id_telefone){
                        $sql_query = " 
                            SELECT numero FROM telefone 
                            WHERE id_telefone = ?
                                ";
                        $stmt = $db->prepare($sql_query);
                        $stmt->execute([
                                ':id_telefone' => $id_telefone
                            ]);
                        $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                }
                switch (tipo_usuario){
                    case ($usuario.Tipo.CORRETOR):
                        $stmt = $db->prepare("
                                    SELECT creci FROM corretor 
                                    WHERE id_usuario = ?
                                ");
                         $stmt->execute([':id_usuario' => $id_usuario]);
                        $creci = $stmt->fetch(PDO::FETCH_ASSOC)[0];
                        if ($creci){
                            $creci = int($creci);
                        }
                        $usuario_obj = corretor.Corretor(
                            $username, $senha, $email, $nome, $cpf_cnpj, $creci);
                        break;
                    
                    case ($usuario.Tipo.CAPTADOR):
                        $usuario_obj = captador.Captador(
                            $username, $senha, $email, $nome, $cpf_cnpj);
                        $stmt = $db->prepare("
                                    SELECT salario FROM captador 
                                    WHERE id_usuario = ?
                                ");
                        $stmt->execute([':id_usuario' => $id_usuario]);
                        $salario = $stmt->fetch(PDO::FETCH_ASSOC)[0];
                        if ($salario){
                            $salario = float($salario);
                        }
                        $usuario_obj->set_salario($salario);
                        break;
                    
                    case $usuario.Tipo.GERENTE:
                        $usuario_obj = gerente.Gerente(
                            $username, $senha, $email, $nome, $cpf_cnpj);
                        $stmt = $db->prepare("
                                    SELECT salario FROM gerente 
                                    WHERE id_usuario = ?
                                ");
                        $stmt->execute([':id_usuario' => $id_usuario]);
                        $salario = $stmt->fetch(PDO::FETCH_ASSOC)[0];
                        if ($salario){
                            $salario = float($salario);
                        }
                        $usuario_obj->set_salario($salario);
                        break;
                            
                    case $usuario.Tipo.CLIENTE:
                        $usuario_obj = cliente.Cliente(
                            $username, $senha, $email, $nome, $cpf_cnpj);
                            break;
                            
                        # $stmt = $db->prepare("
                        #             SELECT * FROM cliente
                        #             WHERE id_usuario = ?
                        #         ", (id_usuario,))
                        # registros = $stmt->fetch(PDO::FETCH_ASSOC)
                }
                $usuario_obj.set_id($id_usuario);
                $usuario_obj.set_rg($rg);
                $usuario_obj.set_endereco($endereco);
                $usuario_obj.set_data_nascimento($data_nascimento);
                $usuario_obj.set_telefones($telefones);
                return $usuario_obj;
        }catch (Exception $e) {
            $erro = "ERRO! Banco.get_usuario_por_cpf_cnpj{ {e}";
            print($erro);
            return NULL;
        }
    }
    public function get_lista_filtros_apartamento(){
        try{
         
                $stmt = $db->prepare("
                        SELECT * FROM filtros_imovel 
                ");
                $registros = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$registros){
                    throw new Exception("Não existe filtros");
                }

                $lista = [];

                foreach ($registros as $registro){
                    $nome = $registro[1];
                    $lista[] = $nome;
                }
                return $lista;

        }catch (Exception $e){
            print("ERRO! Banco.get_lista_filtros_apartamento{ {e}");
            return [];
        }
    }

    public function get_lista_filtros_condominio(){
        try{
                $stmt = $db->prepare("
                        SELECT * FROM filtros_condominio 
                ");
                $registros = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$registros){
                    throw new Exception("Não existe filtros");
                }

                $lista = [];

                foreach ($registros as $registro){
                    $nome = $registro[1];
                    $lista[] = $nome;
                }
                return $lista;

        }catch (Exception $e){
            print("ERRO! Banco.get_lista_filtros_apartamento{ {e}");
            return [];
        }
    }

    public function cadastrar_lista_filtros($lista_filtros, $tabela){
        try{
            foreach ($lista_filtros as $filtro){
                    $sql_query = " 
                        INSERT INTO {tabela} (nome) 
                        VALUES(?)
                        ";
                    $stmt = $db->prepare(sql_query);
                    $stmt->execute([':nome' => $filtro]);
                return True;
            }
        }catch (Exception $e){
            $erro = "ERRO! Banco.cadastrar_lista_filtros " . $e->getMessage();
            print($erro);
            return False;
        }
    }

    public function get_condominio_por_id_imovel($id_imovel){
        try{
                $stmt = $db->prepare("
                        SELECT * FROM condominio 
                        WHERE id_imovel = ?
                    ");
                $stmt->execute([':id_imovel' => $id_imovel]);
                $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$registro){
                    throw new Exception(
                        "Não existe condomínio para o imóvel com id $id_imovel");
                }
                $id_condominio = int($registro[0]);
                $nome = $registro[1];
                $id_endereco = $registro[2];
                $endereco_obj = $this->get_endereco_por_id($id_endereco);
                if (!$endereco_obj){
                    throw new Exception(
                        "Não existe endereço com id $id_endereco");
                }
                $condominio_obj = new condominio.Condominio();
                $condominio_obj->set_id($id_condominio);
                $condominio_obj->set_nome($nome);
                $condominio_obj->set_endereco($endereco_obj);
                $stmt = $db->prepare("
                        SELECT * FROM condominio_filtros
                        WHERE id_condominio = ?
                    ");
                $stmt->execute([':id_condominio' => $id_condominio]);
                $condominio_filtros = $stmt->fetch(PDO::FETCH_ASSOC);
                $lista_condominio_filtros = [];
                if ($condominio_filtros){
                    foreach ($dados as $registro){
                        $id_condominio_filtros = int($registro[0]);
                        $stmt = $db->prepare("
                                SELECT nome FROM filtros_condominio
                                WHERE id_filtros_condominio = ?
                            ");
                        $stmt->execute([':id_filtros_condominio' => $id_condominio_filtros]);
                        $nome = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($nome){
                            $lista_condominio_filtros[] = $nome;
                        }
                    }
                }
                if ($lista_condominio_filtros){
                    $condominio_obj->set_filtros($lista_condominio_filtros);
                }
                return $condominio_obj;
        } catch (Exception $e) {
            $erro = "ERRO! Banco.get_condominio_por_id_imovel ". $e->getMessage();
            print($erro);
            return NULL;
        }
    }

    public function atualizar_proprietario($proprietario){
        try{
                $sql_update_query = "
                UPDATE proprietario
                SET email = ?,
                    nome = ?,
                    cpf_cnpj = ?,
                    rg = ?,
                    id_endereco = ?,
                    data_nascimento = ?
                WHERE cpf_cnpj = ?;
                ";
                $endereco = $proprietario.get_endereco();
                if ($endereco) {
                    $endereco = $endereco.get_id();
                }
                $data_nascimento = $proprietario.get_data_nascimento();
                if ($data_nascimento) {
                    $data_nascimento = $data_nascimento.strftime("%d-%m-%Y");
                }
                $dados = [$proprietario.get_email(), $proprietario.get_nome(), $proprietario.get_cpf_cnpj(), $proprietario.get_rg(), $endereco, $data_nascimento, $proprietario.get_cpf_cnpj()];
                $stmt = $db->prepare($sql_update_query, $dados);
                // $stmt->execute([':id' => $id_imovel]);
                return True;
        }catch (Exception $e) {
            print("ERRO Banco.atualizar_proprietario {e}");
            return False;
        }
    }

    public function cadastrar_atendimento($atendimento){
        try{
            
                $sql_query = " 
                    INSERT INTO atendimento (id_imovel, cpf_cnpj_corretor, cpf_cnpj_comprador, status) 
                    VALUES(?, ?, ?, ?)
                    ";
                $corretor_obj = $atendimento.get_corretor();
                if ($corretor_obj){
                    $corretor_obj = $corretor_obj.get_cpf_cnpj();
                }
                $cliente_obj = $atendimento.get_cliente();
                if ($cliente_obj){
                    $cliente_obj = $cliente_obj.get_cpf_cnpj();
                }

                $imovel_obj = $atendimento.get_imovel();
                if ($imovel_obj){
                    $imovel_obj = $imovel_obj.get_id();
                }
                $status = $atendimento.get_status();
                if ($status){
                    $status = $status.value;
                }
                $stmt = $db->prepare($sql_query);
                $stmt->execute([
                ":id_imovel" => $id_imovel,
                ":cpf_cnpj_corretor" => $corretor_obj,
                ":cpf_cnpj_comprador" => $cliente_obj,
                ":status" => $status
                ]);
                return True;
        } catch (Exception $e){
            $erro = "ERRO! Banco.cadastrar_atendimento" + $e.getMessage();
            print($erro);
            return False;
        }
    }

    public function get_lista_atendimentos() {
        try{
            
                $stmt = $db->prepare("
                        SELECT * FROM atendimento 
                ");
                $registros = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$registros) {
                    throw new Exception("Não há atendimentos cadastrados");
                }
                $lista = [];
                foreach ($registros as $registro) {
                    $id_atendimento = int($registro[0]);
                    $imovel = $registro[1];
                    $corretor = $registro[2];
                    $comprador = $registro[3];
                    $status = $registro[4];
                    if ($imovel) {
                        $imovel = $this.get_imovel_por_id(int($imovel));
                    }
                    if ($corretor){
                        $corretor = $this.get_usuario_por_cpf_cnpj($corretor);
                    }
                    if ($comprador){
                        $comprador = $this.get_usuario_por_cpf_cnpj($comprador);
                    }
                    if ($status){
                        $status = atendimento.Status($status);
                    }
                    $atendimento_obj = atendimento.Atendimento();
                    $atendimento_obj.set_status($status);
                    $atendimento_obj.set_id($id_atendimento);
                    $atendimento_obj.set_corretor($corretor);
                    $atendimento_obj.set_cliente($comprador);
                    $atendimento_obj.set_imovel($imovel);
                    $lista.append($atendimento_obj);
                }
                return $lista;
        } catch (Exception $e){
            $erro = "ERRO! Banco.get_lista_atendimentos" . $e->getMessage();
            print($erro);
            return [];
        }
    }

    public function get_anuncio_por_id($id_anuncio){
        try{
            
                $stmt = $db->prepare("
                        SELECT * FROM anuncio
                        WHERE id_anuncio = ?
                    ");
                $stmt->execute([':id_anuncio' => $id_anuncio]);
                $registro = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$registro) {
                    throw new Exception("Não existe anúncio com id {id_anuncio}");
                }
                $anuncio_obj = anuncio.Anuncio();
                $id_anuncio = $registro[0];
                if ($id_anuncio){
                    $id_anuncio = int($id_anuncio);
                }
                $descricao = $registro[1];
                $titulo = $registro[2];
                $anuncio_obj->set_id($id_anuncio);
                $anuncio_obj->set_descricao($descricao);
                $anuncio_obj->set_titulo($titulo);
                $mapa_anexos = $this->get_lista_anexos_por_id_anuncio($id_anuncio);
                if ($mapa_anexos && isset($mapa_anexos["Imagens"])) {
                    $anuncio_obj->set_imagens($mapa_anexos["Imagens"]);
                }
                if ($mapa_anexos && isset($mapa_anexos["Videos"])) {
                    $anuncio_obj->set_videos($mapa_anexos["Videos"]);
                }
                if ($mapa_anexos && isset($mapa_anexos["Documentos"])) {
                    $anuncio_obj->set_anexos($mapa_anexos["Documentos"]);
                }
                return $anuncio_obj;
        }catch (Exception $e){
            $erro = "ERRO! Banco.get_anuncio_por_id" . $e->getMessage();
            print($erro);
            return NULL;
        }
    }

    public function cadastrar_anexo($id_anuncio, $blob, $tipo){
        try{
                $sql_query = " 
                    INSERT INTO midia_anuncio (id_anuncio, midia, tipo) 
                    VALUES(?, ?, ?)
                    ";
                $stmt = $db->prepare($sql_query);
                $stmt->execute([$id_anuncio, $blob, $tipo]);
                return True;
        } catch (Exception $e){
            $erro = "ERRO! Banco.cadastrar_anexo" . $e->getMessage();
            print($erro);
            return False;
        }
    }

    public function get_lista_anexos_por_id_anuncio($id_anuncio){
        try{

            
                $stmt = $db->prepare("
                        SELECT * FROM midia_anuncio 
                        WHERE id_anuncio = ?
                    ");
                $stmt->execute([$id_anuncio]);
                $registros = $stmt->fetch(PDO::FETCH_ASSOC);
                $imagens = [];
                $videos = [];
                $documentos = [];
                if (!$registros){
                    throw new Exception("Não há midias_imóveis cadastrados");
                }
                foreach ($registro as $registros){
                    $id = registro[0];
                    $blob = io.BytesIO(registro[1]);
                    $tipo = registro[2];
                    if ($tipo == "Imagem"){
                        $imagens.append($blob);
                    }
                    else if ($tipo == "Documento"){
                        $documentos.append($blob);
                    }
                    else if ($tipo == "Video"){
                        $videos.append($blob);
                    }

                }
                $mapa = dict();
                $mapa["Imagens"] = $imagens;
                $mapa["Videos"] = $videos;
                $mapa["Documentos"] = $documentos;
                return $mapa;
        } catch (Exception $e){
            $erro = "ERRO! Banco.get_lista_anexos{ {e}";
            print($erro);
            return [];
        }
                    }

    public function get_condominio_por_id_endereco(id){
        try{

            
                    $stmt = $db->prepare("
                                SELECT * FROM condominio 
                                WHERE id_endereco = ?
                            ", (id,))
                registro = $stmt->fetch(PDO::FETCH_ASSOC)
                if not registro{
                    raise Exception(
                        "Não existe condominio com id_endereco {id}")
                id_condominio = int(registro[0])
                nome = registro[1]
                id_endereco = int(registro[2])
                endereco_obj = $this.get_endereco_por_id(id_endereco)
                condominio_obj = condominio.Condominio(nome, endereco_obj)
                condominio_obj.set_id(id_condominio)
                return condominio_obj
        except Exception as e{
            erro = "ERRO! Banco.get_condominio_por_id_endereco{ {e}"
            print(erro)
            return NULL

    public function get_condominio_por_id(id){
        try{
            
                    $stmt = $db->prepare("
                                SELECT * FROM condominio 
                                WHERE id_condominio = ?
                            ", (id,))
                registro = $stmt->fetch(PDO::FETCH_ASSOC)
                if not registro{
                    raise Exception(
                        "Não existe condominio com id {id}")
                id_condominio = int(registro[0])
                nome = registro[1]
                id_endereco = int(registro[2])
                endereco_obj = $this.get_endereco_por_id(id_endereco)
                condominio_obj = condominio.Condominio(nome, endereco_obj)
                condominio_obj.set_id(id_condominio)
                return condominio_obj
        except Exception as e{
            erro = "ERRO! Banco.get_condominio_por_id{ {e}"
            print(erro)
            return NULL

    public function verificar_endereco(endereco_obj){
        try{
            
                    sql = "
                    SELECT * 
                    FROM endereco 
                    WHERE cep = ?
                    AND numero = ?;
                "
                $stmt = $db->prepare(
                    sql, (endereco_obj.get_cep(), endereco_obj.get_numero()))
                registro = $stmt->fetch(PDO::FETCH_ASSOC)
                if not registro{
                    raise Exception("Não existe imóvel com este endereço")
                if registro{
                    id_endereco = int(registro[0])
                    rua = registro[1]
                    numero = registro[2]
                    if numero{
                        numero = int(numero)
                    bairro = registro[3]
                    cep = registro[4]
                    if cep{
                        cep = int(cep)
                    complemento = registro[5]
                    cidade = registro[6]
                    uf = registro[7]
                    endereco_resultado = endereco.Endereco(
                        rua, bairro, cep, cidade, uf)
                    endereco_resultado.set_id(id_endereco)
                    endereco_resultado.set_numero(numero)
                    endereco_resultado.set_complemento(complemento)
                    return endereco_resultado
                else{
                    return NULL
        except Exception as e{
            erro = "ERRO! Banco.verificar_endereco{ {e}"
            print(erro)
            return NULL

    public function verificar_usuario(username, senha){
        try{
            
                    $stmt = $db->prepare("
                                SELECT * FROM usuario WHERE username = ?
                            ", (username,))
                registro = $stmt->fetch(PDO::FETCH_ASSOC)
                print(registro)
                if not registro{
                    raise Exception("Usuário não encontrado")
                senha_hash_banco = registro[2]
                senha_hash = hashlib.sha256(
                    senha.encode('utf-8')).hexdigest()
                if senha_hash_banco == senha_hash{
                    id_usuario = int(registro[0])
                    username = registro[1]
                    senha = registro[2]
                    email = registro[3]
                    nome = registro[4]
                    cpf_cnpj = registro[5]
                    rg = registro[6]
                    endereco = registro[7]
                    if endereco{
                        endereco = $this.get_endereco_por_id(endereco)
                    data_nascimento = registro[8]
                    if data_nascimento{
                        data_nascimento = datetime.strptime(
                            data_nascimento, "%d-%m-%Y")
                    tipo = registro[9]
                    if tipo{
                        tipo = usuario.Tipo(tipo)
                    usuario_obj = usuario.Usuario(
                        username, senha_hash_banco, email, nome, cpf_cnpj, tipo)
                    sql_query = " 
                                SELECT id_telefone FROM telefone_usuario 
                                WHERE id_usuario = ?
                                "
                    $stmt = $db->prepare(sql_query, (id_usuario,))
                    registros = $stmt->fetch(PDO::FETCH_ASSOC)
                    telefones = []
                    if registros{
                        for id_telefone in registros{
                            sql_query = " 
                                    SELECT numero FROM telefone 
                                    WHERE id_telefone = ?
                                        "
                            $stmt = $db->prepare(sql_query, (id_telefone,))
                            registro = $stmt->fetch(PDO::FETCH_ASSOC)
                    match tipo{
                        case usuario.Tipo.CORRETOR{
                            $stmt = $db->prepare("
                                        SELECT creci FROM corretor 
                                        WHERE id_usuario = ?
                                    ", (id_usuario,))
                            creci = $stmt->fetch(PDO::FETCH_ASSOC)[0]
                            if creci{
                                creci = int(creci)
                            usuario_obj = corretor.Corretor(
                                username, senha_hash_banco, email, nome, cpf_cnpj, creci)
                        case usuario.Tipo.CAPTADOR{
                            usuario_obj = captador.Captador(
                                username, senha_hash_banco, email, nome, cpf_cnpj)
                            $stmt = $db->prepare("
                                        SELECT salario FROM captador 
                                        WHERE id_usuario = ?
                                    ", (id_usuario,))
                            salario = $stmt->fetch(PDO::FETCH_ASSOC)[0]
                            if salario{
                                salario = float(salario)
                            usuario_obj.set_salario(salario)
                        case usuario.Tipo.GERENTE{
                            usuario_obj = gerente.Gerente(
                                username, senha_hash_banco, email, nome, cpf_cnpj)
                            $stmt = $db->prepare("
                                        SELECT salario FROM gerente 
                                        WHERE id_usuario = ?
                                    ", (id_usuario,))
                            salario = $stmt->fetch(PDO::FETCH_ASSOC)[0]
                            if salario{
                                salario = float(salario)
                            usuario_obj.set_salario(salario)
                        case usuario.Tipo.CLIENTE{
                            usuario_obj = cliente.Cliente(
                                username, senha_hash_banco, email, nome, cpf_cnpj)
                            # $stmt = $db->prepare("
                            #         SELECT * FROM cliente
                            #         WHERE id_usuario = ?
                            #     ", (id_usuario,))
                            # registros = $stmt->fetch(PDO::FETCH_ASSOC)
                    usuario_obj.set_endereco(endereco)
                    usuario_obj.set_data_nascimento(data_nascimento)
                    usuario_obj.set_rg(rg)
                    usuario_obj.set_id(id_usuario)
                    usuario_obj.set_telefones(telefones)
                    return usuario_obj
                else{
                    raise Exception("Senha errada!")

        except Exception as e{
            erro = "ERRO! Banco.verificar_usuario{ {e}"
            print(erro)
            return NULL

    public function cadastrar_endereco(endereco){
        try{
            
                    sql_query = " 
                    INSERT INTO endereco (rua, numero, bairro, cep, complemento, cidade, uf) 
                    VALUES(?, ?, ?, ?, ?, ?, ?)
                    "
                $stmt = $db->prepare(sql_query, (
                    endereco.get_rua(),
                    endereco.get_numero(),
                    endereco.get_bairro(),
                    endereco.get_cep(),
                    endereco.get_complemento(),
                    endereco.get_cidade(),
                    endereco.get_uf(),
                ))
                conexao.commit()
                return True
        except Exception as e{
            erro = "ERRO! Banco.cadastrar_endereco{ {e}"
            print(erro)
            return False

    public function cadastrar_condominio(condominio){
        try{
            
                    endereco_obj = condominio.get_endereco()
                if endereco_obj{
                    endereco_obj = condominio.get_endereco().get_id()
                sql_query = " 
                    INSERT INTO condominio (nome, id_endereco) 
                    VALUES(?, ?)
                    "
                $stmt = $db->prepare(sql_query, (
                    condominio.get_nome(),
                    endereco_obj
                ))
                conexao.commit()
                return True
        except Exception as e{
            erro = "ERRO! Banco.cadastrar_condominio{ {e}"
            print(erro)
            return False

    public function cadastrar_proprietario(proprietario){
        # TODO{ cadastrar telefones
        try{
            
    
                endereco = proprietario.get_endereco()
                if endereco{
                    endereco = endereco.get_id()
                data = proprietario.get_data_nascimento()
                if data{
                    data = data.strftime("%d-%m-%Y")
                sql_query = " 
                    INSERT INTO proprietario (email, nome, cpf_cnpj, rg, id_endereco, data_nascimento) 
                    VALUES(?, ?, ?, ?, ?, ?)
                    "
                $stmt = $db->prepare(sql_query, (
                    proprietario.get_email(),
                    proprietario.get_nome(),
                    proprietario.get_cpf_cnpj(),
                    proprietario.get_rg(),
                    endereco,
                    data
                ))
                id = cursor.lastrowid
                if proprietario.get_telefones(){
                    for telefone in proprietario.get_telefones(){
                        sql_query = " 
                            INSERT INTO telefone (numero) 
                            VALUES(?)
                            "
                        $stmt = $db->prepare(sql_query, (telefone,))
                        id_telefone = cursor.lastrowid
                        sql_query = " 
                            INSERT INTO telefone_proprietario (id_proprietario, id_telefone) 
                            VALUES(?, ?)
                            "
                        $stmt = $db->prepare(sql_query, (id, id_telefone))
                conexao.commit()
                return True
        except Exception as e{
            erro = "ERRO! Banco.cadastrar_proprietario{ {e}"
            print(erro)
            return False

    public function cadastrar_anuncio(anuncio){
        try{
            
    
                sql_query = " 
                    INSERT INTO anuncio (descricao, titulo) 
                    VALUES(?, ?)
                    "
                $stmt = $db->prepare(sql_query, (
                    anuncio.get_descricao(),
                    anuncio.get_titulo(),
                ))
                conexao.commit()

                id_anuncio = cursor.lastrowid

                if anuncio.get_imagens(){
                    for imagem in anuncio.get_imagens(){
                        $this.cadastrar_anexo(id_anuncio, imagem, "Imagem")

                if anuncio.get_videos(){
                    for video in anuncio.get_videos(){
                        $this.cadastrar_anexo(id_anuncio, video, "Video")

                if anuncio.get_anexos(){
                    for anexo in anuncio.get_anexos(){
                        $this.cadastrar_anexo(id_anuncio, anexo, "Documento")

                return cursor.lastrowid
        except Exception as e{
            erro = "ERRO! Banco.cadastrar_anuncio{ {e}"
            print(erro)
            return False

    public function get_endereco_por_id(id){
        try{
            

                    $stmt = $db->prepare("
                        SELECT * FROM endereco 
                        WHERE id_endereco = ?
                    ", (id,))
                registros = $stmt->fetch(PDO::FETCH_ASSOC)
                if not registros{
                    raise Exception(
                        "Não há endereços cadastrados com id {id}")
                id_endereco = int(registros[0])
                rua = registros[1]
                numero = int(registros[2])
                bairro = registros[3]
                cep = registros[4]
                complemento = registros[5]
                cidade = registros[6]
                uf = registros[7]
                endereco_obj = endereco.Endereco(
                    rua, bairro, cep, cidade, uf)
                endereco_obj.set_id(id_endereco)
                endereco_obj.set_numero(numero)
                endereco_obj.set_complemento(complemento)
                return endereco_obj
        except Exception as e{
            erro = "ERRO! Banco.get_endereco_por_id{ {e}"
            print(erro)
            return NULL

    public function get_proprietario_por_cpf_cnpj(cpf_cnpj){
        try{
            

                    $stmt = $db->prepare("
                        SELECT * FROM proprietario 
                        WHERE cpf_cnpj = ?
                    ", (cpf_cnpj,))
                registro = $stmt->fetch(PDO::FETCH_ASSOC)
                if not registro{
                    raise Exception(
                        "Não existe proprietario com CPF/CNPJ{ {cpf_cnpj}")
                id_proprietario = int(registro[0])
                email = registro[1]
                nome = registro[2]
                cpf_cnpj = registro[3]
                rg = registro[4]
                data_nascimento = registro[5]
                if data_nascimento{
                    data_nascimento = datetime.strptime(
                        data_nascimento, "%d-%m-%Y")
                proprietario_obj = proprietario.Proprietario(email, nome, cpf_cnpj)
                proprietario_obj.set_id(id_proprietario)
                proprietario_obj.set_data_nascimento(data_nascimento)
                proprietario_obj.set_rg(rg)
                return proprietario_obj
        except Exception as e{
            erro = "ERRO! Banco.get_proprietario_por_cpf_cnpj{ {e}"
            print(erro)
            return NULL

    public function get_imovel_por_id(id){
        try{
            
                    $stmt = $db->prepare(
                    f'SELECT * FROM imovel WHERE id_imovel = ?', (id,))
                dados = $stmt->fetch(PDO::FETCH_ASSOC)
                if not dados{
                    raise Exception("Não existe imóvel com id {id}")
                id_imovel = int(dados[0])
                valor_venda = dados[1]
                if valor_venda{
                    valor_venda = float(valor_venda)
                valor_aluguel = dados[2]
                if valor_aluguel{
                    valor_aluguel = float(valor_aluguel)
                quant_quartos = dados[3]
                if quant_quartos{
                    quant_quartos = int(quant_quartos)
                quant_salas = dados[4]
                if quant_salas{
                    quant_salas = int(quant_salas)
                quant_vagas = dados[5]
                if quant_vagas{
                    quant_vagas = int(quant_vagas)
                quant_banheiros = dados[6]
                if quant_banheiros{
                    quant_banheiros = int(quant_banheiros)
                quant_varandas = dados[7]
                if quant_varandas{
                    quant_varandas = int(quant_varandas)
                categoria = dados[8]
                if categoria{
                    categoria = imovel.Categoria(categoria)
                id_endereco = dados[9]
                endereco = NULL
                if id_endereco{
                    endereco = $this.get_endereco_por_id(int(id_endereco))
                status = dados[10]
                if status{
                    status = imovel.Status(status)
                iptu = dados[11]
                if iptu{
                    iptu = float(iptu)
                valor_condominio = dados[12]
                if valor_condominio{
                    valor_condominio = float(valor_condominio)
                andar = dados[13]
                if andar{
                    andar = int(andar)
                estado = dados[14]
                if estado{
                    estado = imovel.Estado(estado)
                bloco = dados[15]
                ano_construcao = dados[16]
                if ano_construcao{
                    ano_construcao = int(ano_construcao)
                area_total = dados[17]
                if area_total{
                    area_total = float(area_total)
                area_privativa = dados[18]
                if area_privativa{
                    area_privativa = float(area_privativa)
                situacao = dados[19]
                if situacao{
                    situacao = imovel.Situacao(situacao)
                ocupacao = dados[20]
                if ocupacao{
                    ocupacao = imovel.Ocupacao(ocupacao)
                cpf_cnpj_corretor = dados[21]
                corretor = NULL
                if cpf_cnpj_corretor{
                    corretor = $this.get_usuario_por_cpf_cnpj(cpf_cnpj_corretor)
                cpf_cnpj_captador = dados[22]
                captador = NULL
                if cpf_cnpj_captador{
                    captador = $this.get_usuario_por_cpf_cnpj(cpf_cnpj_captador)
                data_cadastro = dados[23]
                if data_cadastro{
                    data_cadastro = datetime.strptime(
                        data_cadastro, "%d-%m-%Y")
                data_modificacao = dados[24]
                if data_modificacao{
                    data_modificacao = datetime.strptime(
                        data_modificacao, "%d-%m-%Y")
                id_anuncio = dados[25]
                anuncio = NULL
                if id_anuncio{
                    anuncio = $this.get_anuncio_por_id(int(id_anuncio))

                id_condominio = dados[26]
                condominio = NULL
                if id_condominio{
                    condominio = $this.get_condominio_por_id(int(id_condominio))
                imovel_obj = imovel.Imovel(endereco, status, categoria)
                imovel_obj.set_id(id_imovel)
                imovel_obj.set_valor_venda(valor_venda)
                imovel_obj.set_valor_aluguel(valor_aluguel)
                imovel_obj.set_quant_quartos(quant_quartos)
                imovel_obj.set_quant_salas(quant_salas)
                imovel_obj.set_quant_vagas(quant_vagas)
                imovel_obj.set_quant_banheiros(quant_banheiros)
                imovel_obj.set_quant_varandas(quant_varandas)
                imovel_obj.set_categoria(categoria)
                imovel_obj.set_endereco(endereco)
                imovel_obj.set_status(status)
                imovel_obj.set_iptu(iptu)
                imovel_obj.set_valor_condominio(valor_condominio)
                imovel_obj.set_andar(andar)
                imovel_obj.set_estado(estado)
                imovel_obj.set_bloco(bloco)
                imovel_obj.set_ano_construcao(ano_construcao)
                imovel_obj.set_area_total(area_total)
                imovel_obj.set_area_privativa(area_privativa)
                imovel_obj.set_situacao(situacao)
                imovel_obj.set_ocupacao(ocupacao)
                imovel_obj.set_corretor(corretor)
                imovel_obj.set_captador(captador)
                imovel_obj.set_data_cadastro(data_cadastro)
                imovel_obj.set_data_modificacao(data_modificacao)
                imovel_obj.set_anuncio(anuncio)
                imovel_obj.set_condominio(condominio)
                $stmt = $db->prepare("
                        SELECT cpf_cnpj_proprietario FROM proprietario_imovel 
                        WHERE id_imovel = ?
                    ", (id_imovel,))
                cpf_cnpj_proprietarios = $stmt->fetch(PDO::FETCH_ASSOC)
                proprietarios = []
                if cpf_cnpj_proprietarios{
                    for cpf_cnpj in cpf_cnpj_proprietarios{
                        cpf_cnpj = cpf_cnpj[0]
                        proprietario = $this.get_proprietario_por_cpf_cnpj(
                            cpf_cnpj)
                        if proprietario{
                            proprietarios.append(proprietario)
                imovel_obj.set_proprietarios(proprietarios)
                $stmt = $db->prepare("
                        SELECT id_filtros_imovel FROM imovel_filtros 
                        WHERE id_imovel = ?
                    ", (id_imovel,))
                lista_ids = $stmt->fetch(PDO::FETCH_ASSOC)
                filtros = []
                if lista_ids{
                    for id in lista_ids{
                        id = int(id[0])
                        $stmt = $db->prepare("
                            SELECT nome FROM filtros_imovel 
                            WHERE id_filtros_imovel = ?
                        ", (id,))
                        nome_filtro = $stmt->fetch(PDO::FETCH_ASSOC)[0]
                        if nome_filtro{
                            filtros.append(nome_filtro)
                imovel_obj.set_filtros(filtros)

                return imovel_obj
        except Exception as e{
            erro = "ERRO! Banco.get_imovel_por_id{ {e}"
            print(erro)
            return NULL

    public function cadastrar_imovel(imovel){
        try{

            
                    $sql_query = " 
                INSERT INTO imovel(valor_venda, valor_aluguel, quant_quartos, quant_salas, quant_vagas, quant_banheiros, quant_varandas, categoria, id_endereco, status, iptu, valor_condominio, andar, estado, bloco, ano_construcao, area_total, area_privativa, situacao, ocupacao, cpf_cnpj_corretor, cpf_cnpj_captador, data_cadastro, data_modificacao, id_anuncio, id_condominio) 
                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                                    ";

                if ($imovel.get_filtros()){
                    foreach ($imovel.get_filtros()as $filtro){
                        print($imovel.get_filtros());
                        $id = $this.get_id_filtro_imovel_por_nome($filtro);
                        print($id);
                        if ($id){
                            $this.cadastrar_filtro_imovel(
                                $imovel.get_id(), $filtro);
                        }
                    }
                } 
                $categoria = $imovel.get_categoria();
                if ($categoria){
                    $categoria = $categoria.value;
                }
                $endereco_obj = $imovel.get_endereco();
                if ($endereco_obj){
                    if ($endereco_obj.get_id() != NULL){
                        $endereco_obj = $endereco_obj.get_id();
                    }else{
                        $endereco_obj = NULL;}
                    }
                }
                anuncio_obj = $imovel.get_anuncio();
                if ($anuncio_obj){
                    if ($anuncio_obj.get_id() != NULL){
                        $anuncio_obj = $anuncio_obj.get_id();}
                    else{
                        $anuncio_obj = NULL;
                    }                  
                }
                if ($imovel.get_proprietarios()){
                    foreach ($imovel.get_proprietarios() as $proprietario){
                        $sql_query = "
                                    INSERT INTO proprietario_imovel (cpf_cnpj_proprietario, id_imovel)
                                    VALUES(?, ?)
                                    ";
                        $stmt = $db->prepare(sql_query, ($proprietario.get_cpf_cnpj(),
                                                   $imovel.get_id()));
                    }
                }

                $status = $imovel.get_status();
                if ($status){
                    $status = $status.value;
                }
                $estado = $imovel.get_estado();
                if ($estado){
                    $estado = $estado.value;
                }
                $situacao = $imovel.get_situacao();
                if ($situacao){
                    $situacao = $situacao.value;
                }
                $ocupacao = $imovel.get_ocupacao();
                if ($ocupacao){
                    $ocupacao = $ocupacao.value;
                }
                $corretor_obj = $imovel.get_corretor();
                if ($corretor_obj){
                    $corretor_obj = $corretor_obj.get_cpf_cnpj();
                }
                $captador_obj = $imovel.get_captador();
                if ($captador_obj){
                    $captador_obj = $captador_obj.get_cpf_cnpj();
                }
                $condominio_obj = $imovel.get_condominio();
                if ($condominio_obj){
                    $condominio_obj = $condominio_obj.get_id();
                }
                $data_cadastro = $imovel.get_data_cadastro();
                if ($data_cadastro and isinstance($data_cadastro, datetime)){
                    $data_cadastro = $data_cadastro.strftime("%d-%m-%Y");
                }
                $data_modificacao = $imovel.get_data_modificacao();
                if ($data_modificacao and isinstance($data_modificacao, datetime)){
                    $data_modificacao = $data_modificacao.strftime("%d-%m-%Y");
                }

                $stmt = $db->prepare(sql_query, (
                    $imovel.get_valor_venda(),
                    $imovel.get_valor_aluguel(),
                    $imovel.get_quant_quartos(),
                    $imovel.get_quant_salas(),
                    $imovel.get_quant_vagas(),
                    $imovel.get_quant_banheiros(),
                    $imovel.get_quant_varandas(),
                    $categoria,
                    $endereco_obj,
                    $status,
                    $imovel.get_iptu(),
                    $imovel.get_valor_condominio(),
                    $imovel.get_andar(),
                    $estado,
                    $imovel.get_bloco(),
                    $imovel.get_ano_construcao(),
                    $imovel.get_area_total(),
                    $imovel.get_area_privativa(),
                    $situacao,
                    $ocupacao,
                    $corretor_obj,
                    $captador_obj,
                    $data_cadastro,
                    $data_modificacao,
                    $anuncio_obj,
                    $condominio_obj
                ));
                conexao.commit();
                return True;
        } catch (Exception $e){
            $erro = "ERRO! Banco.cadastrar_imovel{ {e}";
            print(erro);
            return False;
        }
                }

    public function get_lista_imoveis(){

    try {

        $sql = "
            SELECT * FROM imovel
            
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$resultados) {
            throw new Exception("Não há imóveis disponíveis");
        }

        $lista = [];

        foreach ($resultados as $dados) {

            $id = (int)$dados['id_imovel'];

            $valor_venda = $dados['valor_venda'] ? (float)$dados['valor_venda'] : null;
            $valor_aluguel = $dados['valor_aluguel'] ? (float)$dados['valor_aluguel'] : null;
            $quartos = $dados['quant_quartos'] ? (int)$dados['quant_quartos'] : null;
            $salas = $dados['quant_salas'] ? (int)$dados['quant_salas'] : null;
            $vagas = $dados['quant_vagas'] ? (int)$dados['quant_vagas'] : null;
            $banheiros = $dados['quant_banheiros'] ? (int)$dados['quant_banheiros'] : null;
            $varandas = $dados['quant_varandas'] ? (int)$dados['quant_varandas'] : null;

            $categoria = $dados['categoria'] ?? null;
            $status = $dados['status'] ?? null;
            $estado = $dados['estado'] ?? null;
            $situacao = $dados['situacao'] ?? null;
            $ocupacao = $dados['ocupacao'] ?? null;

            $endereco = $dados['id_endereco'] 
                ? $this->get_endereco_por_id((int)$dados['id_endereco']) 
                : null;

            $corretor = $dados['cpf_cnpj_corretor'] 
                ? $this->get_usuario_por_cpf_cnpj($dados['cpf_cnpj_corretor']) 
                : null;

            $captador = $dados['cpf_cnpj_captador'] 
                ? $this->get_usuario_por_cpf_cnpj($dados['cpf_cnpj_captador']) 
                : null;

            $anuncio = $dados['id_anuncio'] 
                ? $this->get_anuncio_por_id((int)$dados['id_anuncio']) 
                : null;

            $condominio = $dados['id_condominio'] 
                ? $this->get_condominio_por_id((int)$dados['id_condominio']) 
                : null;

            $data_cadastro = $dados['data_cadastro'] 
                ? new DateTime($dados['data_cadastro']) 
                : null;

            $data_modificacao = $dados['data_modificacao'] 
                ? new DateTime($dados['data_modificacao']) 
                : null;

            
            $imovel = new Imovel($endereco, $status, $categoria);

            $imovel->set_id($id);
            $imovel->set_valor_venda($valor_venda);
            $imovel->set_valor_aluguel($valor_aluguel);
            $imovel->set_quant_quartos($quartos);
            $imovel->set_quant_salas($salas);
            $imovel->set_quant_vagas($vagas);
            $imovel->set_quant_banheiros($banheiros);
            $imovel->set_quant_varandas($varandas);
            $imovel->set_iptu($dados['iptu']);
            $imovel->set_valor_condominio($dados['valor_condominio']);
            $imovel->set_andar($dados['andar']);
            $imovel->set_estado($estado);
            $imovel->set_bloco($dados['bloco']);
            $imovel->set_ano_construcao($dados['ano_construcao']);
            $imovel->set_area_total($dados['area_total']);
            $imovel->set_area_privativa($dados['area_privativa']);
            $imovel->set_situacao($situacao);
            $imovel->set_ocupacao($ocupacao);
            $imovel->set_corretor($corretor);
            $imovel->set_captador($captador);
            $imovel->set_data_cadastro($data_cadastro);
            $imovel->set_data_modificacao($data_modificacao);
            $imovel->set_anuncio($anuncio);
            $imovel->set_condominio($condominio);

         
            $stmtP = $this->db->prepare("
                SELECT cpf_cnpj_proprietario
                FROM proprietario_imovel
                WHERE id_imovel = :id
            ");
            $stmtP->execute([':id' => $id]);

            $proprietarios = [];

            while ($row = $stmtP->fetch(PDO::FETCH_ASSOC)) {
                $prop = $this->get_proprietario_por_cpf_cnpj($row['cpf_cnpj_proprietario']);
                if ($prop) {
                    $proprietarios[] = $prop;
                }
            }

            $imovel->set_proprietarios($proprietarios);

 
            $stmtF = $this->db->prepare("
                SELECT f.nome
                FROM imovel_filtros i
                JOIN filtros_imovel f ON f.id_filtros_imovel = i.id_filtros_imovel
                WHERE i.id_imovel = :id
            ");
            $stmtF->execute([':id' => $id]);

            $filtros = [];

            while ($row = $stmtF->fetch(PDO::FETCH_ASSOC)) {
                $filtros[] = $row['nome'];
            }

            $imovel->set_filtros($filtros);

            $lista[] = $imovel;
        }

        return $lista;

    } catch (Exception $e) {
        echo "ERRO Banco.get_lista_imoveis: " . $e->getMessage();
        return [];
    }
}

public function get_lista_imoveis_disponiveis(){

    try {

        $sql = "
            SELECT * FROM imovel
            WHERE status IN ('Venda', 'Aluguel', 'Venda_Aluguel')
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$resultados) {
            throw new Exception("Não há imóveis disponíveis");
        }

        $lista = [];

        foreach ($resultados as $dados) {

            $id = (int)$dados['id_imovel'];

           
            $valor_venda = $dados['valor_venda'] ? (float)$dados['valor_venda'] : null;
            $valor_aluguel = $dados['valor_aluguel'] ? (float)$dados['valor_aluguel'] : null;
            $quartos = $dados['quant_quartos'] ? (int)$dados['quant_quartos'] : null;
            $salas = $dados['quant_salas'] ? (int)$dados['quant_salas'] : null;
            $vagas = $dados['quant_vagas'] ? (int)$dados['quant_vagas'] : null;
            $banheiros = $dados['quant_banheiros'] ? (int)$dados['quant_banheiros'] : null;
            $varandas = $dados['quant_varandas'] ? (int)$dados['quant_varandas'] : null;

            
            $categoria = $dados['categoria'] ?? null;
            $status = $dados['status'] ?? null;
            $estado = $dados['estado'] ?? null;
            $situacao = $dados['situacao'] ?? null;
            $ocupacao = $dados['ocupacao'] ?? null;

           
            $endereco = $dados['id_endereco'] 
                ? $this->get_endereco_por_id((int)$dados['id_endereco']) 
                : null;

            $corretor = $dados['cpf_cnpj_corretor'] 
                ? $this->get_usuario_por_cpf_cnpj($dados['cpf_cnpj_corretor']) 
                : null;

            $captador = $dados['cpf_cnpj_captador'] 
                ? $this->get_usuario_por_cpf_cnpj($dados['cpf_cnpj_captador']) 
                : null;

            $anuncio = $dados['id_anuncio'] 
                ? $this->get_anuncio_por_id((int)$dados['id_anuncio']) 
                : null;

            $condominio = $dados['id_condominio'] 
                ? $this->get_condominio_por_id((int)$dados['id_condominio']) 
                : null;

            
            $data_cadastro = $dados['data_cadastro'] 
                ? new DateTime($dados['data_cadastro']) 
                : null;

            $data_modificacao = $dados['data_modificacao'] 
                ? new DateTime($dados['data_modificacao']) 
                : null;

            
            $imovel = new Imovel($endereco, $status, $categoria);

            $imovel->set_id($id);
            $imovel->set_valor_venda($valor_venda);
            $imovel->set_valor_aluguel($valor_aluguel);
            $imovel->set_quant_quartos($quartos);
            $imovel->set_quant_salas($salas);
            $imovel->set_quant_vagas($vagas);
            $imovel->set_quant_banheiros($banheiros);
            $imovel->set_quant_varandas($varandas);
            $imovel->set_iptu($dados['iptu']);
            $imovel->set_valor_condominio($dados['valor_condominio']);
            $imovel->set_andar($dados['andar']);
            $imovel->set_estado($estado);
            $imovel->set_bloco($dados['bloco']);
            $imovel->set_ano_construcao($dados['ano_construcao']);
            $imovel->set_area_total($dados['area_total']);
            $imovel->set_area_privativa($dados['area_privativa']);
            $imovel->set_situacao($situacao);
            $imovel->set_ocupacao($ocupacao);
            $imovel->set_corretor($corretor);
            $imovel->set_captador($captador);
            $imovel->set_data_cadastro($data_cadastro);
            $imovel->set_data_modificacao($data_modificacao);
            $imovel->set_anuncio($anuncio);
            $imovel->set_condominio($condominio);

           
            $stmtP = $this->db->prepare("
                SELECT cpf_cnpj_proprietario
                FROM proprietario_imovel
                WHERE id_imovel = :id
            ");
            $stmtP->execute([':id' => $id]);

            $proprietarios = [];

            while ($row = $stmtP->fetch(PDO::FETCH_ASSOC)) {
                $prop = $this->get_proprietario_por_cpf_cnpj($row['cpf_cnpj_proprietario']);
                if ($prop) {
                    $proprietarios[] = $prop;
                }
            }

            $imovel->set_proprietarios($proprietarios);

       
            $stmtF = $this->db->prepare("
                SELECT f.nome
                FROM imovel_filtros i
                JOIN filtros_imovel f ON f.id_filtros_imovel = i.id_filtros_imovel
                WHERE i.id_imovel = :id
            ");
            $stmtF->execute([':id' => $id]);

            $filtros = [];

            while ($row = $stmtF->fetch(PDO::FETCH_ASSOC)) {
                $filtros[] = $row['nome'];
            }

            $imovel->set_filtros($filtros);

            $lista[] = $imovel;
        }

        return $lista;

    } catch (Exception $e) {
        echo "ERRO Banco.get_lista_imoveis_disponiveis: " . $e->getMessage();
        return [];
    }
}

    public function atualizar_imovel($imovel){

    try {

        $this->db->beginTransaction();

        
        $categoria = $imovel->get_categoria();
        $categoria = $categoria ? $categoria->value : null;

        $status = $imovel->get_status();
        $status = $status ? $status->value : null;

        $estado = $imovel->get_estado();
        $estado = $estado ? $estado->value : null;

        $situacao = $imovel->get_situacao();
        $situacao = $situacao ? $situacao->value : null;

        $ocupacao = $imovel->get_ocupacao();
        $ocupacao = $ocupacao ? $ocupacao->value : null;

        
        $endereco = $imovel->get_endereco();
        $endereco = ($endereco && $endereco->get_id()) ? $endereco->get_id() : null;

        $anuncio = $imovel->get_anuncio();
        $anuncio = ($anuncio && $anuncio->get_id()) ? $anuncio->get_id() : null;

        $condominio = $imovel->get_condominio();
        $condominio = $condominio ? $condominio->get_id() : null;

        $corretor = $imovel->get_corretor();
        $corretor = $corretor ? $corretor->get_cpf_cnpj() : null;

        $captador = $imovel->get_captador();
        $captador = $captador ? $captador->get_cpf_cnpj() : null;

        
        $data_cadastro = $imovel->get_data_cadastro();
        $data_cadastro = $data_cadastro ? $data_cadastro->format("Y-m-d") : null;

        $data_modificacao = $imovel->get_data_modificacao();
        $data_modificacao = $data_modificacao ? $data_modificacao->format("Y-m-d") : null;

        
        $imovel_db = $this->get_imovel_por_id($imovel->get_id());

       
        $props_antigos = $imovel_db ? $imovel_db->get_proprietarios() : [];
        $props_novos = $imovel->get_proprietarios() ?: [];

        foreach ($props_antigos as $p) {
            if (!in_array($p, $props_novos)) {

                $stmt = $this->db->prepare("
                    DELETE FROM proprietario_imovel
                    WHERE cpf_cnpj_proprietario = :cpf
                      AND id_imovel = :id
                ");
                $stmt->execute([
                    ':cpf' => $p->get_cpf_cnpj(),
                    ':id' => $imovel->get_id()
                ]);
            }
        }

        foreach ($props_novos as $p) {
            if (!in_array($p, $props_antigos)) {

                $stmt = $this->db->prepare("
                    INSERT INTO proprietario_imovel (cpf_cnpj_proprietario, id_imovel)
                    VALUES (:cpf, :id)
                ");
                $stmt->execute([
                    ':cpf' => $p->get_cpf_cnpj(),
                    ':id' => $imovel->get_id()
                ]);
            }
        }

     
        $filtros_antigos = $imovel_db ? $imovel_db->get_filtros() : [];
        $filtros_novos = $imovel->get_filtros() ?: [];

        foreach ($filtros_antigos as $f) {
            if (!in_array($f, $filtros_novos)) {
                $id = $this->get_id_filtro_imovel_por_nome($f);
                if ($id !== null) {
                    $this->remover_filtro_do_imovel($imovel->get_id(), $id);
                }
            }
        }

        foreach ($filtros_novos as $f) {
            if (!in_array($f, $filtros_antigos)) {
                $id = $this->get_id_filtro_imovel_por_nome($f);
                if ($id !== null) {
                    $this->cadastrar_filtro_imovel($imovel->get_id(), $id);
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
                cpf_cnpj_corretor = :corretor,
                cpf_cnpj_captador = :captador,
                data_cadastro = :data_cadastro,
                data_modificacao = :data_modificacao,
                id_anuncio = :anuncio,
                id_condominio = :condominio
            WHERE id_imovel = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':valor_venda' => $imovel->get_valor_venda(),
            ':valor_aluguel' => $imovel->get_valor_aluguel(),
            ':quartos' => $imovel->get_quant_quartos(),
            ':salas' => $imovel->get_quant_salas(),
            ':vagas' => $imovel->get_quant_vagas(),
            ':banheiros' => $imovel->get_quant_banheiros(),
            ':varandas' => $imovel->get_quant_varandas(),
            ':categoria' => $categoria,
            ':endereco' => $endereco,
            ':status' => $status,
            ':iptu' => $imovel->get_iptu(),
            ':condominio_valor' => $imovel->get_valor_condominio(),
            ':andar' => $imovel->get_andar(),
            ':estado' => $estado,
            ':bloco' => $imovel->get_bloco(),
            ':ano' => $imovel->get_ano_construcao(),
            ':area_total' => $imovel->get_area_total(),
            ':area_privativa' => $imovel->get_area_privativa(),
            ':situacao' => $situacao,
            ':ocupacao' => $ocupacao,
            ':corretor' => $corretor,
            ':captador' => $captador,
            ':data_cadastro' => $data_cadastro,
            ':data_modificacao' => $data_modificacao,
            ':anuncio' => $anuncio,
            ':condominio' => $condominio,
            ':id' => $imovel->get_id()
        ]);

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollBack();
        echo "ERRO Banco.atualizar_imovel: " . $e->getMessage();
        return false;
    }
}

    public function get_id_filtro_imovel_por_nome($nome){
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
            echo "ERRO Banco.get_id_filtro_imovel_por_nome: " . $e->getMessage();
            return null;
        }
    }

    public function cadastrar_filtro_imovel($id_imovel, $id_filtro){
    try {

        $stmt = $this->db->prepare("
            INSERT INTO imovel_filtros (id_imovel, id_filtros_imovel)
            VALUES (:id_imovel, :id_filtro)
        ");
        $stmt->execute([
            ':id_imovel' => $id_imovel,
            ':id_filtro' => $id_filtro
        ]);

        return true;

    } catch (Exception $e) {
        echo "ERRO Banco.cadastrar_filtro_imovel: " . $e->getMessage();
        return false;
    }
}

public function remover_filtro_do_imovel($id_imovel, $id_filtro){
    try {

        $stmt = $this->db->prepare("
            DELETE FROM imovel_filtros
            WHERE id_imovel = :id_imovel 
              AND id_filtros_imovel = :id_filtro
        ");
        $stmt->execute([
            ':id_imovel' => $id_imovel,
            ':id_filtro' => $id_filtro
        ]);

        return true;

    } catch (Exception $e) {
        echo "ERRO Banco.remover_filtro_do_imovel: " . $e->getMessage();
        return false;
    }
}

public function get_id_filtro_condominio_por_nome($nome){
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
        echo "ERRO Banco.get_id_filtro_condominio_por_nome: " . $e->getMessage();
        return null;
    }
}

public function cadastrar_filtro_condominio($id_condominio, $id_filtro){
    try {

        $stmt = $this->db->prepare("
            INSERT INTO condominio_filtros (id_filtros_condominio, id_condominio)
            VALUES (:id_filtro, :id_condominio)
        ");
        $stmt->execute([
            ':id_filtro' => $id_filtro,
            ':id_condominio' => $id_condominio
        ]);

        return true;

    } catch (Exception $e) {
        echo "ERRO Banco.cadastrar_filtro_condominio: " . $e->getMessage();
        return false;
    }
}

public function remover_filtro_do_condominio($id_condominio, $id_filtro){
    try {

        $stmt = $this->db->prepare("
            DELETE FROM condominio_filtros
            WHERE id_condominio = :id_condominio 
              AND id_filtros_condominio = :id_filtro
        ");
        $stmt->execute([
            ':id_condominio' => $id_condominio,
            ':id_filtro' => $id_filtro
        ]);

        return true;

    } catch (Exception $e) {
        echo "ERRO Banco.remover_filtro_do_condominio: " . $e->getMessage();
        return false;
    }
}





   public function atualizar_anuncio($anuncio){

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
            ':descricao' => $anuncio->get_descricao(),
            ':titulo' => $anuncio->get_titulo(),
            ':id' => $anuncio->get_id()
        ]);

        // mídias
        // tratar imagens, vídeos e anexos depois
        // tabela: midia_anuncio

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollBack();
        echo "ERRO Banco.atualizar_anuncio: " . $e->getMessage();
        return false;
    }
}

 public function atualizar_condominio($condominio){

    try {

        $this->db->beginTransaction();

        
        $sql = "
            UPDATE condominio
            SET nome = :nome
            WHERE id_condominio = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome' => $condominio->get_nome(),
            ':id' => $condominio->get_id()
        ]);

        
        $condominio_db = $this->get_condominio_por_id(
            $condominio->get_id()
        );

        $filtros_antigos = $condominio_db ? $condominio_db->get_filtros() : [];
        $filtros_novos = $condominio->get_filtros() ?: [];

        
        foreach ($filtros_antigos as $filtro) {
            if (!in_array($filtro, $filtros_novos)) {

                $id = $this->get_id_filtro_condominio_por_nome($filtro);

                if ($id !== null) {
                    $this->remover_filtro_do_condominio(
                        $condominio->get_id(),
                        $id
                    );
                }
            }
        }

       
        foreach ($filtros_novos as $filtro) {
            if (!in_array($filtro, $filtros_antigos)) {

                $id = $this->get_id_filtro_condominio_por_nome($filtro);

                if ($id !== null) {
                    $this->cadastrar_filtro_condominio(
                        $condominio->get_id(),
                        $filtro
                    );
                }
            }
        }

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollBack();
        echo "ERRO Banco.atualizar_condominio: " . $e->getMessage();
        return false;
    }
}

   public function atualizar_usuario($usuario){
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
                WHERE cpf_cnpj = :cpf_where
            ";

            $endereco = $usuario->get_endereco();
            $endereco = $endereco ? $endereco->get_id() : null;

            $data_nascimento = $usuario->get_data_nascimento();
            $data_nascimento = $data_nascimento 
                ? $data_nascimento->format("Y-m-d") 
                : null;

            $tipo_usuario = $usuario->get_tipo();
            $tipo_usuario = $tipo_usuario ? $tipo_usuario->value : null;

           
            $senha_hash = hash('sha256', $usuario->get_senha());

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':username' => $usuario->get_username(),
                ':senha' => $senha_hash,
                ':email' => $usuario->get_email(),
                ':nome' => $usuario->get_nome(),
                ':cpf' => $usuario->get_cpf_cnpj(),
                ':rg' => $usuario->get_rg(),
                ':endereco' => $endereco,
                ':data' => $data_nascimento,
                ':tipo' => $tipo_usuario,
                ':cpf_where' => $usuario->get_cpf_cnpj()
            ]);

            
            $usuario_db = $this->get_usuario_por_cpf_cnpj(
                $usuario->get_cpf_cnpj()
            );

            $telefones_antigos = $usuario_db ? $usuario_db->get_telefones() : [];
            $telefones_novos = $usuario->get_telefones() ?: [];

            
            foreach ($telefones_antigos as $tel) {
                if (!in_array($tel, $telefones_novos)) {

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

           
            foreach ($telefones_novos as $tel) {
                if (!in_array($tel, $telefones_antigos)) {

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
                        ':id_usuario' => $usuario->get_id(),
                        ':id_tel' => $id_tel
                    ]);
                }
            }

            
            if ($tipo_usuario === "CORRETOR") {

                $stmt = $this->db->prepare("
                    UPDATE corretor
                    SET creci = :creci
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':creci' => $usuario->get_creci(),
                    ':id' => $usuario->get_id()
                ]);

            } elseif ($tipo_usuario === "CAPTADOR") {

                $stmt = $this->db->prepare("
                    UPDATE captador
                    SET salario = :salario
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':salario' => $usuario->get_salario(),
                    ':id' => $usuario->get_id()
                ]);

            } elseif ($tipo_usuario === "GERENTE") {

                $stmt = $this->db->prepare("
                    UPDATE gerente
                    SET salario = :salario
                    WHERE id_usuario = :id
                ");
                $stmt->execute([
                    ':salario' => $usuario->get_salario(),
                    ':id' => $usuario->get_id()
                ]);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            echo "ERRO Banco.atualizar_usuario: " . $e->getMessage();
            return false;
        }
    }

    public function atualizar_proprietario($proprietario){

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

            $endereco = $proprietario->get_endereco();
            $endereco = $endereco ? $endereco->get_id() : null;

            $data_nascimento = $proprietario->get_data_nascimento();
            $data_nascimento = $data_nascimento 
                ? $data_nascimento->format("Y-m-d") 
                : null;

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':email' => $proprietario->get_email(),
                ':nome' => $proprietario->get_nome(),
                ':cpf' => $proprietario->get_cpf_cnpj(),
                ':rg' => $proprietario->get_rg(),
                ':endereco' => $endereco,
                ':data' => $data_nascimento,
                ':cpf_where' => $proprietario->get_cpf_cnpj()
            ]);

            $proprietario_db = $this->get_proprietario_por_cpf_cnpj(
                $proprietario->get_cpf_cnpj()
            );

            $telefones_antigos = $proprietario_db ? $proprietario_db->get_telefones() : [];
            $telefones_novos = $proprietario->get_telefones() ?: [];

            foreach ($telefones_antigos as $tel) {
                if (!in_array($tel, $telefones_novos)) {

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

            foreach ($telefones_novos as $tel) {
                if (!in_array($tel, $telefones_antigos)) {

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
                        ':id_prop' => $proprietario->get_id(),
                        ':id_tel' => $id_tel
                    ]);
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            echo "ERRO Banco.atualizar_proprietario: " . $e->getMessage();
            return false;
        }
    }

    public function get_imovel_por_id($id_imovel){

        try {

            $sql = "SELECT * FROM imovel WHERE id_imovel = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id_imovel]);

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) {
                throw new Exception("Não há imóveis disponíveis");
            }

            $valor_venda = $dados['valor_venda'] !== null ? (float)$dados['valor_venda'] : null;
            $valor_aluguel = $dados['valor_aluguel'] !== null ? (float)$dados['valor_aluguel'] : null;

            $quant_quartos = $dados['quant_quartos'] !== null ? (int)$dados['quant_quartos'] : null;
            $quant_salas = $dados['quant_salas'] !== null ? (int)$dados['quant_salas'] : null;
            $quant_vagas = $dados['quant_vagas'] !== null ? (int)$dados['quant_vagas'] : null;
            $quant_banheiros = $dados['quant_banheiros'] !== null ? (int)$dados['quant_banheiros'] : null;
            $quant_varandas = $dados['quant_varandas'] !== null ? (int)$dados['quant_varandas'] : null;

            $categoria = $dados['categoria'];
            $status = $dados['status'];
            $estado = $dados['estado'];
            $situacao = $dados['situacao'];
            $ocupacao = $dados['ocupacao'];

            $endereco = null;
            if ($dados['id_endereco']) {
                $endereco = $this->get_endereco_por_id((int)$dados['id_endereco']);
            }

            $corretor = null;
            if ($dados['cpf_cnpj_corretor']) {
                $corretor = $this->get_usuario_por_cpf_cnpj($dados['cpf_cnpj_corretor']);
            }

            $captador = null;
            if ($dados['cpf_cnpj_captador']) {
                $captador = $this->get_usuario_por_cpf_cnpj($dados['cpf_cnpj_captador']);
            }

            $data_cadastro = $dados['data_cadastro'] ? new DateTime($dados['data_cadastro']) : null;
            $data_modificacao = $dados['data_modificacao'] ? new DateTime($dados['data_modificacao']) : null;

            $anuncio = null;
            if ($dados['id_anuncio']) {
                $anuncio = $this->get_anuncio_por_id((int)$dados['id_anuncio']);
            }

            $condominio = null;
            if ($dados['id_condominio']) {
                $condominio = $this->get_condominio_por_id((int)$dados['id_condominio']);
            }

            $imovel_obj = new Imovel($endereco, $status, $categoria);

            $imovel_obj->set_id((int)$dados['id_imovel']);
            $imovel_obj->set_valor_venda($valor_venda);
            $imovel_obj->set_valor_aluguel($valor_aluguel);
            $imovel_obj->set_quant_quartos($quant_quartos);
            $imovel_obj->set_quant_salas($quant_salas);
            $imovel_obj->set_quant_vagas($quant_vagas);
            $imovel_obj->set_quant_banheiros($quant_banheiros);
            $imovel_obj->set_quant_varandas($quant_varandas);
            $imovel_obj->set_estado($estado);
            $imovel_obj->set_situacao($situacao);
            $imovel_obj->set_ocupacao($ocupacao);
            $imovel_obj->set_corretor($corretor);
            $imovel_obj->set_captador($captador);
            $imovel_obj->set_data_cadastro($data_cadastro);
            $imovel_obj->set_data_modificacao($data_modificacao);
            $imovel_obj->set_anuncio($anuncio);
            $imovel_obj->set_condominio($condominio);

            $stmt = $this->db->prepare("
                SELECT cpf_cnpj_proprietario 
                FROM proprietario_imovel 
                WHERE id_imovel = :id
            ");
            $stmt->execute([':id' => $id_imovel]);

            $proprietarios = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $prop = $this->get_proprietario_por_cpf_cnpj($row['cpf_cnpj_proprietario']);
                if ($prop) {
                    $proprietarios[] = $prop;
                }
            }

            $imovel_obj->set_proprietarios($proprietarios);

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

            $imovel_obj->set_filtros($filtros);

            return $imovel_obj;

        } catch (Exception $e) {
            echo "ERRO! Banco.get_imovel_por_id: " . $e->getMessage();
            return null;
        }
    }

    public function get_imoveis_por_proprietario($cpf) {
        try {
            $stmt = $this->db->prepare("SELECT id_imovel FROM proprietario_imovel WHERE cpf_cnpj_proprietario = ?");
            $stmt->execute([$cpf]);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$dados) {
                throw new Exception("Não há imóveis disponíveis");
            }
            $imoveis = [];
            foreach ($dados as $row) {
                $id = (int)$row['id_imovel'];
                $imovel = $this->get_imovel_por_id($id);
                if ($imovel) {
                    $imoveis[] = $imovel;
                }
            }
            return $imoveis;
        } catch (Exception $e) {
            $erro = "ERRO! Banco.get_imoveis_por_proprietario: " . $e->getMessage();
            error_log($erro);
            return [];
        }
    }
?>