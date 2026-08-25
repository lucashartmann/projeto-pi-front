<?php

require_once __DIR__ . '/../php/model/cliente.php';
require_once __DIR__ . '/../php/model/proprietario.php';
require_once __DIR__ . '/../php/model/endereco.php';
require_once __DIR__ . '/../php/model/imovel.php';
require_once __DIR__ . '/../php/model/anuncio.php';
require_once __DIR__ . '/../php/model/funcionario.php';
require_once __DIR__ . '/../php/model/corretor.php';
require_once __DIR__ . '/../php/model/condominio.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class ModelTest extends TestCase
{
    public function testPessoaGettersSetters()
    {
        $pessoa = new Pessoa('u@example.com', 'Nome', '123456789');
        $pessoa->setSenha('pass');
        $this->assertEquals('pass', $pessoa->getSenha());
        $this->assertEquals('u@example.com', $pessoa->getEmail());
        $this->assertEquals('Nome', $pessoa->getNome());
        $this->assertEquals('123456789', $pessoa->getCpfCnpj());

        $pessoa->setId(42);
        $this->assertEquals(42, $pessoa->getId());

        $pessoa->setRg('RG123');
        $this->assertEquals('RG123', $pessoa->getRg());

        $pessoa->setTelefones(['11999999999']);
        $this->assertEquals(['11999999999'], $pessoa->getTelefones());

        $data = new DateTime('1990-05-01');
        $pessoa->setDataNascimento($data);
        $this->assertInstanceOf(DateTime::class, $pessoa->getDataNascimento());
    }

    public function testClienteInheritsUsuario()
    {
        $cliente = new Cliente('cli', 'pass', 'c@example.com', 'Cliente', '987654321');
        $this->assertInstanceOf(Pessoa::class, $cliente);

        $cliente->setTiposImoveisDesejados(['Apartamento']);
        $this->assertEquals(['Apartamento'], $cliente->getTipoImoveisDesejados());

        $cliente->setQuantQuartosDesejado(2);
        $this->assertEquals(2, $cliente->getQuantQuartosDesejado());
    }

    public function testEnderecoGettersSetters()
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
    }

    public function testImovelGettersSetters()
    {
        $end = new Endereco('Rua Y', 'B', '00000000', 'C', 'UF');
        $imovel = new Imovel($end, Status::VENDA, Categoria::CASA);

        $this->assertEquals(Categoria::CASA, $imovel->getCategoria());
        $this->assertEquals(Status::VENDA, $imovel->getStatus());

        $imovel->setValorVenda(250000);
        $this->assertEquals(250000, $imovel->getValorVenda());

        $imovel->setValorAluguel(1500);
        $this->assertEquals(1500, $imovel->getValorAluguel());

        $imovel->setQuantQuartos(3);
        $this->assertEquals(3, $imovel->getQuantQuartos());

        $imovel->setProprietarios(['prop1']);
        $this->assertEquals(['prop1'], $imovel->getProprietarios());

        $anuncio = new Anuncio();
        $anuncio->setTitulo('Título X');
        $imovel->setAnuncio($anuncio);
        $this->assertEquals('Título X', $imovel->getAnuncio()->getTitulo());
    }

    public function testAnuncioGettersSetters()
    {
        $an = new Anuncio();
        $an->setTitulo('Meu Anúncio');
        $an->setDescricao('Descrição');
        $an->setImagens(['img1', 'img2']);

        $this->assertEquals('Meu Anúncio', $an->getTitulo());
        $this->assertEquals('Descrição', $an->getDescricao());
        $this->assertEquals(['img1', 'img2'], $an->getImagens());
    }

    public function testProprietarioGettersSetters()
    {
        $prop = new Proprietario('p@example.com', 'Prop', '00011122233');
        $this->assertEquals('p@example.com', $prop->getEmail());
        $this->assertEquals('Prop', $prop->getNome());
        $this->assertEquals('00011122233', $prop->getCpfCnpj());

        $prop->setRg('RG999');
        $this->assertEquals('RG999', $prop->getRg());

        $prop->setImoveis(['im1']);
        $this->assertEquals(['im1'], $prop->getImoveis());
    }
}
