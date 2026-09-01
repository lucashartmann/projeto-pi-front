<?php

require_once __DIR__ . '/TestDatabase.php';
require_once __DIR__ . '/../php/model/imovel.php';
require_once __DIR__ . '/../php/dao/imovelDAO.php';
require_once __DIR__ . '/../php/controllers/imovelController.php';
require_once __DIR__ . '/../php/services/imovelService.php';
require_once __DIR__ . '/../php/model/endereco.php';
require_once __DIR__ . '/../php/model/proprietario.php';
require_once __DIR__ . '/../php/model/corretor.php';
require_once __DIR__ . '/../php/model/funcionario.php';
require_once __DIR__ . '/../php/model/anuncio.php';
require_once __DIR__ . '/../php/model/condominio.php';
require_once __DIR__ . '/../php/model/anexo.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class ImovelTest extends TestCase
{
    protected function setUp(): void
    {
        TestDatabase::setupTestDatabase();
        TestDatabase::reset();
    }

    private function criarEstruturaBase(): array
    {
        $endereco = new Endereco('Rua Teste', 'Bairro Teste', '12345678', 'Cidade Teste', 'UF');
        $endereco->setNumero(100);

        $proprietario = new Proprietario('joao@example.com', 'Joao da Silva', '12345678901');
        $proprietario->setSenha('senha123');
        $pessoaService = new PessoaService();
        $proprietario = $pessoaService->cadastrar($proprietario);

        $corretor = new Corretor('corretor@example.com', 'Corretor Teste', '12345678902', 12323232);
        $corretor->setSenha('senha123');
        $corretor = $pessoaService->cadastrar($corretor);

        $captador = new Funcionario('captador@example.com', 'Captador Teste', '12345678903', Cargo::CAPTADOR);
        $captador->setSenha('senha123');
        $captador = $pessoaService->cadastrar($captador);

        return [$endereco, $proprietario, $corretor, $captador];
    }

    public function testInstanciaImovel(): void
    {
        $endereco = new Endereco('Rua Teste', 'Bairro Teste', '12345678', 'Cidade Teste', 'UF');
        $proprietario = new Proprietario('joao@example.com', 'Joao da Silva', '12345678901');
        $corretor = new Corretor('corretor@example.com', 'Corretor Teste', '12345678901', 12323232);
        $captador = new Funcionario('captador@example.com', 'Captador Teste', '12345678901', Cargo::CAPTADOR);
        $anuncio = new Anuncio();
        $condominio = new Condominio('Condominio Test', $endereco);

        $imovel = new Imovel($endereco, Status::VENDA, Categoria::CASA);
        $imovel->setId(2);
        $imovel->setValorVenda(2000.50);
        $imovel->setValorAluguel(2300.54);
        $imovel->setQuantQuartos(2);
        $imovel->setQuantSalas(2);
        $imovel->setQuantVagas(2);
        $imovel->setQuantBanheiros(2);
        $imovel->setQuantVarandas(2);
        $imovel->setQuantSuites(2);
        $imovel->setCategoria(Categoria::CASA);
        $imovel->setEndereco($endereco);
        $imovel->setStatus(Status::VENDA);
        $imovel->setIptu(143.50);
        $imovel->setValorCondominio(906.50);
        $imovel->setAndar(12);
        $imovel->setEstado(Estado::OTIMO);
        $imovel->setBloco('B');
        $imovel->setAnoConstrucao(2009);
        $imovel->setAreaTotal(232.43);
        $imovel->setAreaPrivativa(45.67);
        $imovel->setSituacao(Situacao::NOVO);
        $imovel->setOcupacao(Ocupacao::DESOCUPADO);
        $imovel->setProprietarios([$proprietario]);
        $imovel->setCorretor($corretor);
        $imovel->setCaptador($captador);
        $imovel->setDataCadastro(DateTime::createFromFormat('Y-m-d', '2023-01-01'));
        $imovel->setDataModificacao(DateTime::createFromFormat('Y-m-d', '2023-01-02'));
        $imovel->setAnuncio($anuncio);
        $imovel->setCondominio($condominio);
        $imovel->setFiltros(['Piscina', 'Churrasqueira']);
        $imovel->setDestacado(true);
        $imovel->setQuantClicks(200320);

        $this->assertInstanceOf(Imovel::class, $imovel);
        $this->assertEquals(2, $imovel->getId());
        $this->assertEquals(2000.50, $imovel->getValorVenda());
        $this->assertEquals(2300.54, $imovel->getValorAluguel());
        $this->assertEquals(2, $imovel->getQuantQuartos());
        $this->assertEquals(Categoria::CASA, $imovel->getCategoria());
        $this->assertEquals(Status::VENDA, $imovel->getStatus());
        $this->assertTrue($imovel->isDestacado());
        $this->assertEquals(200320, $imovel->getQuantClicks());
    }

    public function testCadastro(): void
    {
        [$endereco, $proprietario, $corretor, $captador] = $this->criarEstruturaBase();

        $anuncio = new Anuncio();
        $anuncio->setTitulo('Casa Teste');
        $anuncio->setDescricao('Descricao Casa Teste');

        $imovel = new Imovel($endereco, Status::VENDA, Categoria::CASA);
        $imovel->setValorVenda(350000.00);
        $imovel->setValorAluguel(1800.00);
        $imovel->setQuantQuartos(3);
        $imovel->setQuantSalas(1);
        $imovel->setQuantVagas(2);
        $imovel->setQuantBanheiros(2);
        $imovel->setQuantVarandas(1);
        $imovel->setQuantSuites(1);
        $imovel->setIptu(500.00);
        $imovel->setValorCondominio(300.00);
        $imovel->setEstado(Estado::BOM);
        $imovel->setAnoConstrucao(2015);
        $imovel->setAreaTotal(120.00);
        $imovel->setAreaPrivativa(100.00);
        $imovel->setSituacao(Situacao::USADO);
        $imovel->setOcupacao(Ocupacao::DESOCUPADO);
        $imovel->setProprietarios([$proprietario]);
        $imovel->setCorretor($corretor);
        $imovel->setCaptador($captador);
        $imovel->setAnuncio($anuncio);

        $imovelService = new ImovelService();
        $resultado = $imovelService->cadastrar($imovel);

        $this->assertInstanceOf(Imovel::class, $resultado);
        $this->assertNotNull($resultado->getId());
        $this->assertGreaterThan(0, $resultado->getId());
    }

    public function testAtualizacao(): void
    {
        [$endereco, $proprietario, $corretor, $captador] = $this->criarEstruturaBase();

        $anuncio = new Anuncio();
        $anuncio->setTitulo('Titulo Inicial');
        $anuncio->setDescricao('Descricao Inicial');

        $imovel = new Imovel($endereco, Status::VENDA, Categoria::CASA);
        $imovel->setValorVenda(200000.00);
        $imovel->setQuantQuartos(2);
        $imovel->setProprietarios([$proprietario]);
        $imovel->setCorretor($corretor);
        $imovel->setCaptador($captador);
        $imovel->setAnuncio($anuncio);

        $imovelService = new ImovelService();
        $cadastrado = $imovelService->cadastrar($imovel);

        $cadastrado->setValorVenda(250000.00);
        $cadastrado->setQuantQuartos(3);
        $cadastrado->setStatus(Status::ALUGUEL);
        $cadastrado->setValorAluguel(1500.00);

        $imovelService->atualizar($cadastrado);

        $imovelDAO = new ImovelDAO();
        $recuperado = $imovelDAO->buscarPorId($cadastrado->getId());

        $this->assertNotNull($recuperado);
        $this->assertEquals(250000.00, $recuperado->getValorVenda());
        $this->assertEquals(3, $recuperado->getQuantQuartos());
        $this->assertEquals(Status::ALUGUEL, $recuperado->getStatus());
    }

    public function testRemocao(): void
    {
        [$endereco, $proprietario, $corretor, $captador] = $this->criarEstruturaBase();

        $anuncio = new Anuncio();
        $anuncio->setTitulo('Titulo Imovel');
        $imovel = new Imovel($endereco, Status::VENDA, Categoria::APARTAMENTO);
        $imovel->setValorVenda(180000.00);
        $imovel->setProprietarios([$proprietario]);
        $imovel->setCorretor($corretor);
        $imovel->setCaptador($captador);
        $imovel->setAnuncio($anuncio);

        $imovelService = new ImovelService();
        $cadastrado = $imovelService->cadastrar($imovel);
        $id = $cadastrado->getId();

        $this->assertNotNull($id);

        $imovelService->remover($id);

        $imovelDAO = new ImovelDAO();
        try {
            $imovelDAO->buscarPorId($id);
            $this->fail();
        } catch (Exception $e) {
            $this->assertStringContainsString('Imóvel não encontrado', $e->getMessage());
        }
    }

    public function testBuscarPorId(): void
    {
        [$endereco, $proprietario, $corretor, $captador] = $this->criarEstruturaBase();

        $anuncio = new Anuncio();
        $anuncio->setTitulo('Apartamento Centro');
        $anuncio->setDescricao('Otimo apartamento no centro');

        $imovel = new Imovel($endereco, Status::ALUGUEL, Categoria::APARTAMENTO);
        $imovel->setValorAluguel(1200.00);
        $imovel->setQuantQuartos(1);
        $imovel->setProprietarios([$proprietario]);
        $imovel->setCorretor($corretor);
        $imovel->setCaptador($captador);
        $imovel->setAnuncio($anuncio);

        $imovelService = new ImovelService();
        $cadastrado = $imovelService->cadastrar($imovel);

        $imovelDAO = new ImovelDAO();
        $recuperado = $imovelDAO->buscarPorId($cadastrado->getId());

        $this->assertNotNull($recuperado);
        $this->assertEquals($cadastrado->getId(), $recuperado->getId());
        $this->assertEquals(Categoria::APARTAMENTO, $recuperado->getCategoria());
        $this->assertEquals(Status::ALUGUEL, $recuperado->getStatus());
    }

    public function testBuscarTodos(): void
    {
        [$endereco, $proprietario, $corretor, $captador] = $this->criarEstruturaBase();

        $anuncio1 = new Anuncio();
        $anuncio1->setTitulo('Casa 1');
        $imovel1 = new Imovel($endereco, Status::VENDA, Categoria::CASA);
        $imovel1->setValorVenda(100000.00);
        $imovel1->setProprietarios([$proprietario]);
        $imovel1->setAnuncio($anuncio1);

        $endereco2 = new Endereco('Rua Outra', 'Bairro Outro', '87654321', 'Cidade Outra', 'UF');
        $anuncio2 = new Anuncio();
        $anuncio2->setTitulo('Apto 2');
        $imovel2 = new Imovel($endereco2, Status::ALUGUEL, Categoria::APARTAMENTO);
        $imovel2->setValorAluguel(1000.00);
        $imovel2->setProprietarios([$proprietario]);
        $imovel2->setAnuncio($anuncio2);

        $imovelService = new ImovelService();
        $imovelService->cadastrar($imovel1);
        $imovelService->cadastrar($imovel2);

        $imovelDAO = new ImovelDAO();
        $lista = $imovelDAO->listar();

        $this->assertIsArray($lista);
        $this->assertGreaterThanOrEqual(2, count($lista));
    }

    public function testDestacarEClicks(): void
    {
        [$endereco, $proprietario, $corretor, $captador] = $this->criarEstruturaBase();

        $anuncio = new Anuncio();
        $anuncio->setTitulo('Imovel Clicks');
        $imovel = new Imovel($endereco, Status::VENDA, Categoria::CASA);
        $imovel->setValorVenda(220000.00);
        $imovel->setProprietarios([$proprietario]);
        $imovel->setAnuncio($anuncio);

        $imovelService = new ImovelService();
        $cadastrado = $imovelService->cadastrar($imovel);
        $id = $cadastrado->getId();

        $imovelDAO = new ImovelDAO();
        $resClicks = $imovelDAO->atualizarClicks($id);
        $this->assertTrue($resClicks);

        $resDestacar = $imovelDAO->destacar($id);
        $this->assertTrue($resDestacar);

        $recuperado = $imovelDAO->buscarPorId($id);
        $this->assertNotNull($recuperado);
        $this->assertEquals(1, $recuperado->getQuantClicks());
        $this->assertTrue($recuperado->isDestacado());
    }

    public function testFavoritar(): void
    {
        [$endereco, $proprietario, $corretor, $captador] = $this->criarEstruturaBase();

        $cliente = new Cliente('cliente_fav@example.com', 'Cliente Favorito', '99988877766');
        $cliente->setSenha('senha123');
        $pessoaService = new PessoaService();
        $clienteCadastrado = $pessoaService->cadastrar($cliente);

        $anuncio = new Anuncio();
        $anuncio->setTitulo('Imovel Favorito');
        $imovel = new Imovel($endereco, Status::VENDA, Categoria::CASA);
        $imovel->setValorVenda(300000.00);
        $imovel->setProprietarios([$proprietario]);
        $imovel->setAnuncio($anuncio);

        $imovelService = new ImovelService();
        $cadastrado = $imovelService->cadastrar($imovel);

        $imovelDAO = new ImovelDAO();
        $resAdd = $imovelDAO->favoritar($clienteCadastrado->getId(), $cadastrado->getId());
        $this->assertNotNull($resAdd);

        $resRemove = $imovelDAO->favoritar($clienteCadastrado->getId(), $cadastrado->getId());
        $this->assertNotNull($resRemove);
    }
}
