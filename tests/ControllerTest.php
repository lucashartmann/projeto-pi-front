<?php

require_once __DIR__ . '/../php/controllers/pessoaController.php';
require_once __DIR__ . '/../php/controllers/imovelController.php';
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

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class ControllerTest extends TestCase
{
    public function testPessoaControllerSerializesProprietario(): void
    {
        $endereco = new Endereco('Rua A', 'Bairro', '00000000', 'Cidade', 'UF');
        $endereco->setNumero(10);

        $imovel = new Imovel($endereco, Status::VENDA, Categoria::CASA);
        $imovel->setId(1);

        $proprietario = new Proprietario('p@example.com', 'Prop', '00011122233');
        $proprietario->setId(5);
        $proprietario->setImoveis([$imovel]);

        $resultado = (new PessoaController())->montarJson([$proprietario]);

        $this->assertSame('Prop', $resultado[0]['nome']);
        $this->assertSame(1, $resultado[0]['imoveis'][0][0]['id']);
    }

    public function testImovelControllerSerializesAdvertisementAndAddress(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/php/api/imoveis.php';
        $endereco = new Endereco('Rua I', 'Bairro', '11111111', 'Cidade', 'UF');
        $anuncio = new Anuncio();
        $anuncio->setId(3);
        $anuncio->setTitulo('Apartamento central');
        $anuncio->setImagens([
            new Anexo(2, 'imoveis/imovel_1.webp', TipoAnexo::IMAGEM),
        ]);

        $imovel = new Imovel($endereco, Status::ALUGUEL, Categoria::APARTAMENTO);
        $imovel->setId(2);
        $imovel->setAnuncio($anuncio);

        $resultado = (new ImovelController())->montarJson([$imovel]);

        $this->assertSame(2, $resultado[0]['id']);
        $this->assertSame('Bairro', $resultado[0]['endereco']['bairro']);
        $this->assertSame('Apartamento central', $resultado[0]['anuncio']['titulo']);
        $this->assertCount(1, $resultado[0]['anuncio']['imagens']);
    }
}
