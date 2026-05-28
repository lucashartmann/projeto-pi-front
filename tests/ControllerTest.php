<?php

require_once __DIR__ . '/../php/controller/controller.php';
require_once __DIR__ . '/../php/model/proprietario.php';
require_once __DIR__ . '/../php/model/usuario.php';
require_once __DIR__ . '/../php/model/imovel.php';
require_once __DIR__ . '/../php/model/endereco.php';
require_once __DIR__ . '/../php/model/anuncio.php';
require_once __DIR__ . '/../php/model/__init__.php';

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Init::$imobiliaria = null;
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];
    }

    public function testListarProprietarios()
    {
        $end = new Endereco('Rua A', 'Bairro', '00000000', 'C', 'UF');
        $end->setNumero(10);

        $imovel = new Imovel($end, Status::VENDA, Categoria::CASA);
        $imovel->setId(1);
        $imovel->setValorVenda(100000);
        $imovel->setValorAluguel(1000);
        $imovel->setCategoria(Categoria::CASA);

        $prop = new Proprietario('p@e.com', 'Prop', '00011122233');
        $prop->setId(5);
        $prop->setRg('RG1');
        $prop->setTelefones('1199999');
        $prop->setEndereco($end);
        $prop->setImoveis([$imovel]);

        Init::$imobiliaria = new class($prop) {
            private $p;
            public function __construct($p) { $this->p = $p; }
            public function getListaProprietarios() { return [$this->p]; }
        };

        $ctrl = new controller();
        $res = $ctrl->listarProprietarios();

        $this->assertEquals('sucesso', $res['status']);
        $this->assertIsArray($res['dados']);
        $this->assertEquals('Prop', $res['dados'][0]['nome']);
        $this->assertEquals(1, $res['dados'][0]['imoveis'][0]['id']);
    }

    public function testListarUsuarios()
    {
        $user = new Usuario('u', 's', 'u@e.com', 'Usu', '123', Tipo::ADMINISTRADOR);
        $user->setId(9);
        $user->setRg('RGU');

        Init::$imobiliaria = new class($user) {
            private $u;
            public function __construct($u) { $this->u = $u; }
            public function getListaUsuarios() { return [$this->u]; }
        };

        $ctrl = new controller();
        $res = $ctrl->listarUsuarios();

        $this->assertEquals('sucesso', $res['status']);
        $this->assertEquals('Usu', $res['dados'][0]['nome']);
    }

    public function testVerificarLoginSuccessSetsSession()
    {
        $user = new Usuario('u', 's', 'u@e.com', 'Usu', '123', Tipo::ADMINISTRADOR);
        $user->setId(77);

        Init::$imobiliaria = new class($user) {
            private $u;
            public function __construct($u) { $this->u = $u; }
            public function verificarUsuario($username, $senha) { return $this->u; }
        };

        $ctrl = new controller();
        $res = $ctrl->verificarLogin(['usuario' => 'u', 'senha' => 's']);

        $this->assertEquals('sucesso', $res['status']);
        $this->assertEquals(77, $res['usuario']['id']);
        $this->assertEquals(77, $_SESSION['usuario_id']);
    }

    public function testCarregarUsuarioReturnsErrorWhenNotLogged()
    {
        $ctrl = new controller();
        $res = $ctrl->carregarUsuario();
        $this->assertEquals('erro', $res['status']);
    }

    public function testCarregarUsuarioWhenLogged()
    {
        $user = new Usuario('u', 's', 'u@e.com', 'Usu', '123', Tipo::ADMINISTRADOR);
        $user->setId(88);
        $user->setNome('NomeX');

        Init::$imobiliaria = new class($user) {
            private $u;
            public function __construct($u) { $this->u = $u; }
            public function getUsuarioPorId($id) { return $this->u; }
        };

        $_SESSION = [];
        session_start();
        $_SESSION['usuario_id'] = 88;
        $_SESSION['tipo'] = $user->getTipo();

        $ctrl = new controller();
        $res = $ctrl->carregarUsuario();

        $this->assertEquals('sucesso', $res['status']);
        $this->assertEquals('NomeX', $res['usuario']['nome']);
    }

    public function testGetListaImoveis()
    {
        $end = new Endereco('Rua I', 'B', '11111111', 'C', 'UF');
        $an = new Anuncio();
        $an->setId(3);
        $an->setImagens([101]);

        $imovel = new Imovel($end, Status::ALUGUEL, Categoria::APARTAMENTO);
        $imovel->setId(2);
        $imovel->setAnuncio($an);

        $estoque = new class($imovel) {
            private $i;
            public function __construct($i) { $this->i = $i; }
            public function getListaImoveis() { return [$this->i]; }
        };

        Init::$imobiliaria = new class($estoque) {
            private $e;
            public function __construct($e) { $this->e = $e; }
            public function getEstoque() { return $this->e; }
        };

        $ctrl = new controller();
        $res = $ctrl->getListaImoveis();

        $this->assertIsArray($res);
        $this->assertEquals(2, $res[0]['id']);
        $this->assertArrayHasKey('anuncio', $res[0]);
    }

    public function testApagarImovelSuccess()
    {
        $imovel = new Imovel(null, Status::VENDA, Categoria::CASA);
        $imovel->setId(5);

        Init::$imobiliaria = new class($imovel) {
            private $i;
            public function __construct($i) { $this->i = $i; }
            public function getImovelPorId($id) { return $this->i; }
            public function remover($campo, $valor, $tabela) { return True; }
        };

        $ctrl = new controller();
        $res = $ctrl->apagarImovel(5);

        $this->assertEquals('sucesso', $res['status']);
    }
}
