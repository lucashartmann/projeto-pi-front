<?php

require_once __DIR__ . '/../php/model/cliente.php';
require_once __DIR__ . '/../php/model/proprietario.php';
require_once __DIR__ . '/../php/model/endereco.php';
require_once __DIR__ . '/../php/model/imovel.php';
require_once __DIR__ . '/../php/model/anuncio.php';
require_once __DIR__ . '/../php/model/anexo.php';
require_once __DIR__ . '/../php/model/funcionario.php';
require_once __DIR__ . '/../php/model/corretor.php';
require_once __DIR__ . '/../php/model/condominio.php';
require_once __DIR__ . '/../php/model/contrato.php';
require_once __DIR__ . '/../php/model/atendimento.php';
require_once __DIR__ . '/../php/model/visita.php';
require_once __DIR__ . '/../php/model/vistoria.php';
require_once __DIR__ . '/../php/model/historico.php';
require_once __DIR__ . '/../php/model/validacao.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class ModelTest extends TestCase
{
    public function testPessoaGettersSetters(): void
    {
        $pessoa = new Pessoa('u@example.com', 'Nome', '12345678901');
        $pessoa->setSenha('pass');
        $this->assertEquals('pass', $pessoa->getSenha());
        $this->assertEquals('u@example.com', $pessoa->getEmail());
        $this->assertEquals('Nome', $pessoa->getNome());
        $this->assertEquals('12345678901', $pessoa->getCpfCnpj());

        $pessoa->setId(42);
        $this->assertEquals(42, $pessoa->getId());

        $pessoa->setRg('RG123');
        $this->assertEquals('RG123', $pessoa->getRg());

        $pessoa->setTelefones(['11999999999']);
        $this->assertEquals(['11999999999'], $pessoa->getTelefones());

        $data = new DateTime('1990-05-01');
        $pessoa->setDataNascimento($data);
        $this->assertInstanceOf(DateTime::class, $pessoa->getDataNascimento());

        $endereco = new Endereco('Rua A', 'Bairro B', '12345678', 'Cidade C', 'UF');
        $pessoa->setEndereco($endereco);
        $this->assertSame($endereco, $pessoa->getEndereco());

        $pessoa->setAtivo(true);
        $this->assertTrue($pessoa->isAtivo());
    }

    public function testClienteInheritsUsuario(): void
    {
        $cliente = new Cliente('c@example.com', 'Cliente', '98765432100');
        $this->assertInstanceOf(Pessoa::class, $cliente);

        $cliente->setTiposImoveisDesejados(['Apartamento']);
        $this->assertEquals(['Apartamento'], $cliente->getTipoImoveisDesejados());

        $cliente->setQuantQuartosDesejado(2);
        $this->assertEquals(2, $cliente->getQuantQuartosDesejado());

        $cliente->setValorMinimo(100000.0);
        $this->assertEquals(100000.0, $cliente->getValorMinimo());

        $cliente->setValorMaximo(500000.0);
        $this->assertEquals(500000.0, $cliente->getValorMaximo());

        $cliente->setTipoInteresse(TipoInteresse::VENDA);
        $this->assertEquals(TipoInteresse::VENDA, $cliente->getTipoInteresse());
    }

    public function testCorretorEFuncionario(): void
    {
        $corretor = new Corretor('corretor@example.com', 'Corretor Teste', '12345678901', 12345);
        $corretor->setSalario(5000.00);
        $corretor->setMatricula('MAT123');
        $corretor->setCargo(Cargo::CORRETOR);

        $this->assertInstanceOf(Funcionario::class, $corretor);
        $this->assertEquals('12345', $corretor->getCreci());
        $this->assertEquals(5000.00, $corretor->getSalario());
        $this->assertEquals('MAT123', $corretor->getMatricula());
        $this->assertEquals(Cargo::CORRETOR, $corretor->getCargo());

        $funcionario = new Funcionario('func@example.com', 'Funcionario Teste', '12345678902', Cargo::ADMIN);
        $this->assertEquals(Cargo::ADMIN, $funcionario->getCargo());
    }

    public function testEnderecoGettersSetters(): void
    {
        $end = new Endereco('Rua X', 'Bairro', '12345678', 'Cidade', 'UF');
        $this->assertEquals('Rua X', $end->getRua());
        $this->assertEquals('Bairro', $end->getBairro());
        $this->assertEquals('12345678', $end->getCep());
        $this->assertEquals('Cidade', $end->getCidade());
        $this->assertEquals('UF', $end->getUf());

        $end->setNumero(100);
        $this->assertEquals(100, $end->getNumero());

        $end->setComplemento('Ap 10');
        $this->assertEquals('Ap 10', $end->getComplemento());

        $end->setId(5);
        $this->assertEquals(5, $end->getId());
    }

    public function testImovelGettersSetters(): void
    {
        $end = new Endereco('Rua Y', 'B', '00000000', 'C', 'UF');
        $imovel = new Imovel($end, Status::VENDA, Categoria::CASA);

        $this->assertEquals(Categoria::CASA, $imovel->getCategoria());
        $this->assertEquals(Status::VENDA, $imovel->getStatus());

        $imovel->setValorVenda(250000.0);
        $this->assertEquals(250000.0, $imovel->getValorVenda());

        $imovel->setValorAluguel(1500.0);
        $this->assertEquals(1500.0, $imovel->getValorAluguel());

        $imovel->setQuantQuartos(3);
        $this->assertEquals(3, $imovel->getQuantQuartos());

        $prop = new Proprietario('p@example.com', 'Prop', '12345678901');
        $imovel->setProprietarios([$prop]);
        $this->assertEquals([$prop], $imovel->getProprietarios());

        $anuncio = new Anuncio();
        $anuncio->setTitulo('Titulo X');
        $imovel->setAnuncio($anuncio);
        $this->assertEquals('Titulo X', $imovel->getAnuncio()->getTitulo());

        $imovel->setEstado(Estado::BOM);
        $this->assertEquals(Estado::BOM, $imovel->getEstado());

        $imovel->setSituacao(Situacao::NOVO);
        $this->assertEquals(Situacao::NOVO, $imovel->getSituacao());

        $imovel->setOcupacao(Ocupacao::DESOCUPADO);
        $this->assertEquals(Ocupacao::DESOCUPADO, $imovel->getOcupacao());
    }

    public function testAnuncioEAnexo(): void
    {
        $an = new Anuncio();
        $an->setTitulo('Meu Anuncio');
        $an->setDescricao('Descricao');

        $this->assertEquals('Meu Anuncio', $an->getTitulo());
        $this->assertEquals('Descricao', $an->getDescricao());

        $anexo = new Anexo(1, 'img/foto.webp', TipoAnexo::IMAGEM);
        $anexo->setLargura(800);
        $anexo->setAltura(600);
        $anexo->setPosicaoX(10);
        $anexo->setPosicaoY(20);

        $this->assertEquals(1, $anexo->getIdAnuncio());
        $this->assertEquals('img/foto.webp', $anexo->getCaminho());
        $this->assertEquals(TipoAnexo::IMAGEM, $anexo->getTipo());
        $this->assertEquals(800, $anexo->getLargura());
        $this->assertEquals(600, $anexo->getAltura());
        $this->assertEquals(10, $anexo->getPosicaoX());
        $this->assertEquals(20, $anexo->getPosicaoY());

        $an->setImagens([$anexo]);
        $this->assertCount(1, $an->getImagens());
    }

    public function testCondominio(): void
    {
        $endereco = new Endereco('Rua Flores', 'Jardins', '12345678', 'Cidade', 'UF');
        $condominio = new Condominio('Condominio Flores', $endereco);
        $condominio->setId(10);
        $condominio->setFiltros(['Piscina', 'Academia']);

        $this->assertEquals(10, $condominio->getId());
        $this->assertEquals('Condominio Flores', $condominio->getNome());
        $this->assertSame($endereco, $condominio->getEndereco());
        $this->assertEquals(['Piscina', 'Academia'], $condominio->getFiltros());
    }

    public function testContrato(): void
    {
        $contrato = new VendaAluguel();
        $contrato->__init__();
        $contrato->setId(1);

        $cliente = new Cliente('cli@example.com', 'Cliente', '12345678901');
        $corretor = new Corretor('cor@example.com', 'Corretor', '12345678902', 1234);
        $imovel = new Imovel(new Endereco('Rua', 'Bairro', '12345678', 'C', 'UF'), Status::VENDA, Categoria::CASA);

        $contrato->setCliente($cliente);
        $contrato->setCorretor($corretor);
        $contrato->setCaptador($corretor);
        $contrato->setImovel($imovel);
        $contrato->setComissaoCaptador(1500.0);
        $contrato->setComissaoCorretor(3000.0);

        $this->assertEquals(1, $contrato->getId());
        $this->assertSame($cliente, $contrato->getCliente());
        $this->assertSame($corretor, $contrato->getCorretor());
        $this->assertSame($corretor, $contrato->getCaptador());
        $this->assertEquals(1500.0, $contrato->getComissaoCaptador());
        $this->assertEquals(3000.0, $contrato->getComissaoCorretor());
        $this->assertIsString($contrato->__toString());
    }

    public function testAtendimento(): void
    {
        $atendimento = new Atendimento();
        $atendimento->setId(1);
        $atendimento->setStatus(StatusAtendimento::EM_ANDAMENTO);

        $this->assertEquals(1, $atendimento->getId());
        $this->assertEquals(StatusAtendimento::EM_ANDAMENTO, $atendimento->getStatus());
        $this->assertIsString($atendimento->__toString());
    }

    public function testVisitaEVistoria(): void
    {
        $visita = new Visita();
        $visita->setId(1);
        $data = new DateTime('2025-01-01 10:00:00');
        $visita->setData($data);
        $this->assertEquals(1, $visita->getId());
        $this->assertSame($data, $visita->getData());
        $this->assertIsString($visita->__toString());

        $vistoria = new Vistoria();
        $vistoria->setId(2);
        $vistoria->setRelatorio('Vistoria concluida sem danos');
        $this->assertEquals(2, $vistoria->getId());
        $this->assertEquals('Vistoria concluida sem danos', $vistoria->getRelatorio());
        $this->assertIsString($vistoria->__toString());
    }

    public function testHistorico(): void
    {
        $historico = new Historico('Alteracao no valor de venda');
        $historico->setId(1);
        $data = new DateTime();
        $historico->setDataAlteracao($data);

        $this->assertEquals(1, $historico->getId());
        $this->assertEquals('Alteracao no valor de venda', $historico->getAlteracao());
        $this->assertSame($data, $historico->getDataAlteracao());
        $this->assertIsString($historico->__toString());
    }

    public function testValidacao(): void
    {
        $this->assertTrue((bool) Validacao::validarCPF('11144477735'));
        $this->assertFalse((bool) Validacao::validarCPF('11111111111'));
        $this->assertFalse((bool) Validacao::validarCPF('123'));

        $this->assertTrue((bool) Validacao::validarRG('12345678'));
        $this->assertFalse((bool) Validacao::validarRG('12'));

        $this->assertTrue((bool) Validacao::validarSenha('123456'));
        $this->assertFalse((bool) Validacao::validarSenha('123'));

        $this->assertTrue((bool) Validacao::validarCreci('RS-12345'));
        $this->assertFalse((bool) Validacao::validarCreci('12345'));

        $this->assertTrue((bool) Validacao::validarTelefone('11987654321'));
        $this->assertFalse((bool) Validacao::validarTelefone('123'));

        $this->assertTrue((bool) Validacao::validarCEP('90650001'));
        $this->assertFalse((bool) Validacao::validarCEP('9065'));

        $this->assertTrue((bool) Validacao::validarEmail('teste@dominio.com'));
        $this->assertFalse((bool) Validacao::validarEmail('emailinvalido'));

        $this->assertTrue((bool) Validacao::validarValor('1500.00'));
        $this->assertTrue((bool) Validacao::validarSalario('3000'));
        $this->assertTrue((bool) Validacao::validarArea('150'));
        $this->assertTrue((bool) Validacao::validarAnoConstrucao('2020'));
    }
}
