<?php

require_once __DIR__ . '/../php/model/usuario.php';
require_once __DIR__ . '/../php/model/cliente.php';
require_once __DIR__ . '/../php/model/proprietario.php';
require_once __DIR__ . '/../php/model/endereco.php';
require_once __DIR__ . '/../php/model/imovel.php';
require_once __DIR__ . '/../php/model/anuncio.php';

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ModelTest extends TestCase
{
    public function testUsuarioGettersSetters()
    {
        $usuario = new Usuario('usr', 'pass', 'u@example.com', 'Nome', '123456789', Tipo::ADMINISTRADOR);
        $this->assertEquals('usr', $usuario->getUsername());
        $this->assertEquals('pass', $usuario->getSenha());
        $this->assertEquals('u@example.com', $usuario->getEmail());
        $this->assertEquals('Nome', $usuario->getNome());
        $this->assertEquals('123456789', $usuario->getCpfCnpj());
        $this->assertEquals(Tipo::ADMINISTRADOR, $usuario->getTipo());

        $usuario->setId(42);
        $this->assertEquals(42, $usuario->getId());

        $usuario->setRg('RG123');
        $this->assertEquals('RG123', $usuario->getRg());

        $usuario->setTelefones(['11999999999']);
        $this->assertEquals(['11999999999'], $usuario->getTelefones());

        $data = new DateTime('1990-05-01');
        $usuario->setDataNascimento($data);
        $this->assertInstanceOf(DateTime::class, $usuario->getDataNascimento());
    }

    public function testClienteInheritsUsuario()
    {
        $cliente = new Cliente('cli', 'pass', 'c@example.com', 'Cliente', '987654321');
        $this->assertEquals(Tipo::CLIENTE, $cliente->getTipo());

        $cliente->setTiposImoveisDesejados(['Apartamento']);
        $this->assertEquals(['Apartamento'], $cliente->tipoImoveisDesejado);

        $cliente->setQuantQuartosDesejado(2);
        $this->assertEquals(2, $cliente->quantQuartosDesejado);
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
