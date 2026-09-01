<?php

require_once __DIR__ . '/TestDatabase.php';
require_once __DIR__ . '/../php/controllers/pessoaController.php';
require_once __DIR__ . '/../php/controllers/imovelController.php';
require_once __DIR__ . '/../php/controllers/historicoController.php';
require_once __DIR__ . '/../php/controllers/atendimentoController.php';
require_once __DIR__ . '/../php/model/endereco.php';
require_once __DIR__ . '/../php/model/pessoa.php';
require_once __DIR__ . '/../php/model/funcionario.php';
require_once __DIR__ . '/../php/model/corretor.php';
require_once __DIR__ . '/../php/model/cliente.php';
require_once __DIR__ . '/../php/model/proprietario.php';
require_once __DIR__ . '/../php/model/anuncio.php';
require_once __DIR__ . '/../php/model/condominio.php';
require_once __DIR__ . '/../php/model/anexo.php';
require_once __DIR__ . '/../php/model/imovel.php';
require_once __DIR__ . '/../php/model/historico.php';
require_once __DIR__ . '/../php/model/atendimento.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class ControllerTest extends TestCase
{
    protected function setUp(): void
    {
        TestDatabase::setupTestDatabase();
        TestDatabase::reset();
    }

    public function testPessoaControllerMontarJson(): void
    {
        $controller = new PessoaController();

        $cliente = new Cliente('teste@dominio.com', 'Nome Teste', '12345678901');
        $cliente->setId(10);
        $endereco = new Endereco('Rua Teste', 'Bairro', '12345678', 'Cidade', 'UF');
        $cliente->setEndereco($endereco);

        $json = $controller->montarJson([$cliente]);

        $this->assertIsArray($json);
        $this->assertCount(1, $json);
        $this->assertEquals(10, $json[0]['id']);
        $this->assertEquals('Nome Teste', $json[0]['nome']);
        $this->assertEquals('teste@dominio.com', $json[0]['email']);
        $this->assertEquals('CLIENTE', $json[0]['tipo']);
    }

    public function testPessoaControllerMontarJsonListaVazia(): void
    {
        $controller = new PessoaController();
        $resultado = $controller->montarJson([]);
        $this->assertIsArray($resultado);
        $this->assertEquals('erro', $resultado['status']);
    }

    public function testImovelControllerMontarJson(): void
    {
        $controller = new ImovelController();

        $endereco = new Endereco('Rua Imovel', 'Bairro Imovel', '98765432', 'Cidade', 'UF');
        $imovel = new Imovel($endereco, Status::VENDA, Categoria::CASA);
        $imovel->setId(20);
        $imovel->setValorVenda(300000.00);
        $imovel->setQuantQuartos(3);

        $json = $controller->montarJson([$imovel]);

        $this->assertIsArray($json);
        $this->assertCount(1, $json);
        $this->assertEquals(20, $json[0]['id']);
        $this->assertEquals(300000.00, $json[0]['valor_venda']);
        $this->assertEquals(3, $json[0]['quantidade_quartos']);
        $this->assertEquals(Categoria::CASA, $json[0]['categoria']);
    }

    public function testImovelControllerMontarJsonListaVazia(): void
    {
        $controller = new ImovelController();
        $resultado = $controller->montarJson([]);
        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    public function testImovelControllerCadastrarClickSemId(): void
    {
        $controller = new ImovelController();
        unset($_GET['id']);
        $resultado = $controller->cadastrarClick();
        $this->assertIsArray($resultado);
        $this->assertEquals('erro', $resultado['status']);
    }

    public function testHistoricoControllerMontarJson(): void
    {
        $controller = new HistoricoController();

        $historico = new Historico('Cadastro realizado', new DateTime('2025-01-01 10:00:00'));
        $historico->setId(1);

        $json = $controller->montarJson([$historico]);

        $this->assertIsArray($json);
        $this->assertCount(1, $json);
        $this->assertEquals(1, $json[0]['id']);
        $this->assertEquals('Cadastro realizado', $json[0]['alteracao']);
        $this->assertEquals('2025-01-01 10:00:00', $json[0]['data']);
    }

    public function testHistoricoControllerMontarJsonVazio(): void
    {
        $controller = new HistoricoController();
        $resultado = $controller->montarJson([]);
        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    public function testAtendimentoControllerMontarJson(): void
    {
        $controller = new AtendimentoController();

        $atendimento = new Atendimento();
        $atendimento->setId(5);
        $atendimento->setStatus(StatusAtendimento::EM_ANDAMENTO);
        $data = new DateTime('2025-02-01 14:00:00');
        $atendimento->setDataCadastro($data);

        $json = $controller->montarJson([$atendimento]);

        $this->assertIsArray($json);
        $this->assertCount(1, $json);
        $this->assertEquals(5, $json[0]['id']);
        $this->assertEquals('2025-02-01 14:00:00', $json[0]['data_cadastro']);
    }
}
