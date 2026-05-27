<?php

require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/captador.php';
require_once __DIR__ . '/../model/atendimento.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/vendaAluguel.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/gerente.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/__init__.php';
require_once __DIR__ . '/../model/validacao.php';
require_once __DIR__ . '/../model/seguranca.php';
require_once __DIR__ . '/../utils/caminho_xamp.php';



use PHPUnit\Framework\TestCase;

class ProprietarioServiceTest extends TestCase
{
    public function testListarProprietariosComSucesso()
    {
        $endereco = new stdClass();
        $endereco->rua = "Rua A";
        $endereco->numero = "123";
        $endereco->bairro = "Centro";
        $endereco->cidade = "SP";
        $endereco->uf = "SP";
        $endereco->cep = "00000-000";
        $endereco->complemento = "Ap 1";

        $imovel = $this->createMock(Imovel::class);
        $imovel->method('getId')->willReturn(1);
        $imovel->method('getValorVenda')->willReturn(500000);
        $imovel->method('getValorAluguel')->willReturn(2000);
        $imovel->method('getCategoria')->willReturn("Casa");
        $imovel->method('getStatus')->willReturn("Disponível");
        $imovel->method('getDataCadastro')->willReturn("2025-01-01");
        $imovel->method('getDataModificacao')->willReturn("2025-01-02");

        $proprietario = $this->createMock(Proprietario::class);

        $proprietario->method('getId')->willReturn(10);
        $proprietario->method('getEmail')->willReturn("teste@email.com");
        $proprietario->method('getNome')->willReturn("João");
        $proprietario->method('getCpfCnpj')->willReturn("123456789");
        $proprietario->method('getRg')->willReturn("999999");
        $proprietario->method('getTelefones')->willReturn("11999999999");
        $proprietario->method('getEndereco')->willReturn($endereco);
        $proprietario->method('getDataNascimento')
            ->willReturn(new DateTime('2000-01-01'));
        $proprietario->method('getImoveis')
            ->willReturn([$imovel]);

        $proprietario->method('getDataCadastro')
            ->willReturn("2025-01-01");

        $proprietario->method('getDataModificacao')
            ->willReturn("2025-01-02");

        // Mock do Init
        $initMock = $this->createMock(Init::class);

        $initMock->method('getListaProprietarios')
            ->willReturn([$proprietario]);

        // Aqui depende de como seu Init::getInstance() funciona
        // Pode precisar ajustar no projeto real

        $service = new ProprietarioService();

        $resultado = $service->listarProprietarios();

        $this->assertEquals("sucesso", $resultado["status"]);
        $this->assertCount(1, $resultado["dados"]);
        $this->assertEquals("João", $resultado["dados"][0]["nome"]);
        $this->assertEquals("Casa", $resultado["dados"][0]["imoveis"][0]["categoria"]);
    }
}