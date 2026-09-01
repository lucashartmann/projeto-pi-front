<?php

require_once __DIR__ . '/TestDatabase.php';
require_once __DIR__ . '/../php/dao/enderecoDAO.php';
require_once __DIR__ . '/../php/dao/pessoaDAO.php';
require_once __DIR__ . '/../php/dao/condominioDAO.php';
require_once __DIR__ . '/../php/dao/filtroDAO.php';
require_once __DIR__ . '/../php/dao/anuncioDAO.php';
require_once __DIR__ . '/../php/dao/notificacaoDAO.php';
require_once __DIR__ . '/../php/dao/historicoDAO.php';
require_once __DIR__ . '/../php/dao/atendimentoDAO.php';
require_once __DIR__ . '/../php/dao/telefoneDAO.php';
require_once __DIR__ . '/../php/services/pessoaService.php';
require_once __DIR__ . '/../php/model/endereco.php';
require_once __DIR__ . '/../php/model/pessoa.php';
require_once __DIR__ . '/../php/model/condominio.php';
require_once __DIR__ . '/../php/model/anuncio.php';
require_once __DIR__ . '/../php/model/historico.php';
require_once __DIR__ . '/../php/model/atendimento.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class DaoTest extends TestCase
{
    protected function setUp(): void
    {
        TestDatabase::setupTestDatabase();
        TestDatabase::reset();
    }

    public function testEnderecoDAOCadastrarEBuscar(): void
    {
        $dao = new EnderecoDAO();
        $endereco = new Endereco('Rua DAO', 'Bairro DAO', '12345678', 'Porto Alegre', 'RS');
        $endereco->setNumero(123);
        $endereco->setComplemento('Apto 101');

        $id = (int) $dao->cadastrar($endereco);
        $this->assertGreaterThan(0, $id);

        $buscado = $dao->buscarPorId($id);
        $this->assertNotNull($buscado);
        $this->assertEquals('Rua DAO', $buscado->getRua());
        $this->assertEquals(123, $buscado->getNumero());
        $this->assertEquals('Apto 101', $buscado->getComplemento());

        $verificado = $dao->verificar($endereco);
        $this->assertNotNull($verificado);
        $this->assertEquals($id, $verificado->getId());
    }

    public function testPessoaDAOCadastrarEBuscar(): void
    {
        $dao = new PessoaDAO();
        $pessoa = new Pessoa('pessoa_dao@teste.com', 'Nome Pessoa DAO', '99887766554');
        $pessoa->setRg('12345678');

        $id = (int) $dao->cadastrar($pessoa);
        $this->assertGreaterThan(0, $id);

        $buscado = $dao->buscarPorId($id);
        $this->assertNotNull($buscado);
        $this->assertEquals('Nome Pessoa DAO', $buscado->getNome());
        $this->assertEquals('pessoa_dao@teste.com', $buscado->getEmail());
    }

    public function testCondominioDAOCadastrarEBuscar(): void
    {
        $endDao = new EnderecoDAO();
        $end = new Endereco('Rua Condominio', 'Bairro C', '11223344', 'Caxias do Sul', 'RS');
        $idEnd = (int) $endDao->cadastrar($end);
        $end->setId($idEnd);

        $condominioDao = new CondominioDAO();
        $condominio = new Condominio('Condominio Alpha', $end);

        $idCond = (int) $condominioDao->cadastrar($condominio);
        $this->assertGreaterThan(0, $idCond);

        $buscado = $condominioDao->buscarPorId($idCond);
        $this->assertNotNull($buscado);
        $this->assertEquals('Condominio Alpha', $buscado->getNome());
    }

    public function testFiltroDAOCadastrar(): void
    {
        $filtroDao = new FiltroDAO();
        $resultado = $filtroDao->cadastrar(null, 'Piscina Aquecida');
        $this->assertTrue($resultado);
    }

    public function testTelefoneDAOCadastrar(): void
    {
        $pessoaDao = new PessoaDAO();
        $pessoa = new Pessoa('tel_teste@teste.com', 'Pessoa Tel', '77788899900');
        $idPessoa = (int) $pessoaDao->cadastrar($pessoa);
        $pessoa->setId($idPessoa);
        $pessoa->setTelefones(['51999998888']);

        $telDao = new TelefoneDAO();
        $telDao->cadastrar($pessoa);

        $telefones = $telDao->listarPorPessoa($idPessoa);
        $this->assertIsArray($telefones);
        $this->assertContains('51999998888', $telefones);
    }

    public function testHistoricoDAOCadastrar(): void
    {
        $pessoaDao = new PessoaDAO();
        $pessoa = new Pessoa('hist@teste.com', 'Pessoa Hist', '11133355577');
        $idPessoa = (int) $pessoaDao->cadastrar($pessoa);
        $pessoa->setId($idPessoa);

        $histDao = new HistoricoDAO();
        $historico = new Historico('Atualizacao cadastral', new DateTime(), null, $pessoa, null, null);

        $idHist = $histDao->cadastrar($historico);
        $this->assertNotNull($idHist);
    }

    public function testAtendimentoDAOCadastrarEBuscar(): void
    {
        $pessoaService = new PessoaService();
        $cliente = new Cliente('cli_atend@teste.com', 'Cliente Atendimento', '33355577799');
        $cliente->setSenha('senha123');
        $clienteCadastrado = $pessoaService->cadastrar($cliente);

        $atendDao = new AtendimentoDAO();
        $atendimento = new Atendimento();
        $atendimento->setCliente($clienteCadastrado);
        $atendimento->setStatus(StatusAtendimento::PENDENTE);

        $idAtend = $atendDao->cadastrar($atendimento);
        $this->assertNotNull($idAtend);
    }
}
