<?php

require_once __DIR__ . '/TestDatabase.php';
require_once __DIR__ . '/../php/model/endereco.php';
require_once __DIR__ . '/../php/model/pessoa.php';
require_once __DIR__ . '/../php/model/funcionario.php';
require_once __DIR__ . '/../php/model/corretor.php';
require_once __DIR__ . '/../php/model/cliente.php';
require_once __DIR__ . '/../php/model/proprietario.php';
require_once __DIR__ . '/../php/model/anuncio.php';
require_once __DIR__ . '/../php/model/anexo.php';
require_once __DIR__ . '/../php/model/condominio.php';
require_once __DIR__ . '/../php/model/imovel.php';
require_once __DIR__ . '/../php/services/imovelService.php';
require_once __DIR__ . '/../php/services/pessoaService.php';
require_once __DIR__ . '/../php/dao/imovelDAO.php';
require_once __DIR__ . '/../php/dao/pessoaDAO.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class ServiceTest extends TestCase
{
    protected function setUp(): void
    {
        TestDatabase::setupTestDatabase();
        TestDatabase::reset();
    }

    public function testServiceClassesExposeExpectedMethods(): void
    {
        $this->assertTrue(method_exists(ImovelService::class, 'cadastrar'));
        $this->assertTrue(method_exists(ImovelService::class, 'atualizar'));
        $this->assertTrue(method_exists(ImovelService::class, 'remover'));
        $this->assertTrue(method_exists(ImovelService::class, 'atualizarAnuncio'));
        $this->assertTrue(method_exists(PessoaService::class, 'cadastrar'));
        $this->assertTrue(method_exists(PessoaService::class, 'atualizar'));
    }

    public function testPessoaServiceCadastrarEAtualizarCliente(): void
    {
        $service = new PessoaService();
        $cliente = new Cliente('cli@teste.com', 'Nome Cliente', '11122233344');
        $cliente->setSenha('senha123');
        $endereco = new Endereco('Rua Teste', 'Bairro Teste', '12345678', 'Cidade', 'UF');
        $cliente->setEndereco($endereco);
        $cliente->setTelefones(['11999998888']);
        $cliente->setTipoInteresse(TipoInteresse::VENDA);
        $cliente->setValorMinimo(100000.0);
        $cliente->setValorMaximo(300000.0);

        $cadastrado = $service->cadastrar($cliente);

        $this->assertNotNull($cadastrado->getId());
        $this->assertGreaterThan(0, $cadastrado->getId());

        $cadastrado->setNome('Nome Cliente Alterado');
        $cadastrado->setValorMaximo(400000.0);
        $service->atualizar($cadastrado);

        $dao = new PessoaDAO();
        $buscado = $dao->buscarPorId($cadastrado->getId());

        $this->assertNotNull($buscado);
        $this->assertEquals('Nome Cliente Alterado', $buscado->getNome());
    }

    public function testPessoaServiceCadastrarCorretorEFuncionario(): void
    {
        $service = new PessoaService();
        $corretor = new Corretor('corretor@teste.com', 'Nome Corretor', '22233344455', 99887);
        $corretor->setSenha('senha123');
        $corretor->setSalario(4500.00);
        $corretor->setCargo(Cargo::CORRETOR);

        $cadastrado = $service->cadastrar($corretor);

        $this->assertNotNull($cadastrado->getId());
        $this->assertGreaterThan(0, $cadastrado->getId());

        $funcionario = new Funcionario('func@teste.com', 'Nome Funcionario', '33344455566', Cargo::ADMIN);
        $funcionario->setSenha('senha123');
        $funcionario->setSalario(6000.00);

        $funcCadastrado = $service->cadastrar($funcionario);

        $this->assertNotNull($funcCadastrado->getId());
        $this->assertGreaterThan(0, $funcCadastrado->getId());
    }

    public function testPessoaServiceCadastrarProprietario(): void
    {
        $service = new PessoaService();
        $prop = new Proprietario('prop@teste.com', 'Nome Proprietario', '44455566677');
        $prop->setSenha('senha123');

        $cadastrado = $service->cadastrar($prop);

        $this->assertNotNull($cadastrado->getId());
        $this->assertGreaterThan(0, $cadastrado->getId());
    }

    public function testImovelServiceFluxoCompleto(): void
    {
        $pessoaService = new PessoaService();
        $proprietario = new Proprietario('prop_imovel@teste.com', 'Proprietario Imovel', '55566677788');
        $proprietario->setSenha('senha123');
        $proprietario = $pessoaService->cadastrar($proprietario);

        $endereco = new Endereco('Rua Service', 'Bairro Service', '98765432', 'Porto Alegre', 'RS');
        $anuncio = new Anuncio();
        $anuncio->setTitulo('Titulo Service');
        $anuncio->setDescricao('Descricao Service');

        $imovel = new Imovel($endereco, Status::VENDA, Categoria::APARTAMENTO);
        $imovel->setValorVenda(450000.00);
        $imovel->setQuantQuartos(3);
        $imovel->setQuantBanheiros(2);
        $imovel->setProprietarios([$proprietario]);
        $imovel->setAnuncio($anuncio);

        $imovelService = new ImovelService();
        $imovelCadastrado = $imovelService->cadastrar($imovel);

        $this->assertNotNull($imovelCadastrado->getId());
        $this->assertGreaterThan(0, $imovelCadastrado->getId());

        $imovelCadastrado->setValorVenda(480000.00);
        $imovelCadastrado->setQuantQuartos(4);
        $imovelService->atualizar($imovelCadastrado);

        $imovelDAO = new ImovelDAO();
        $recuperado = $imovelDAO->buscarPorId($imovelCadastrado->getId());

        $this->assertNotNull($recuperado);
        $this->assertEquals(480000.00, $recuperado->getValorVenda());
        $this->assertEquals(4, $recuperado->getQuantQuartos());

        $imovelService->remover($imovelCadastrado->getId());
        try {
            $imovelDAO->buscarPorId($imovelCadastrado->getId());
            $this->fail();
        } catch (Exception $e) {
            $this->assertStringContainsString('Imóvel não encontrado', $e->getMessage());
        }
    }
}
