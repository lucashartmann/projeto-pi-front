<?php

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

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

use function PHPUnit\Framework\isInstanceOf;

#[CoversNothing]
class ImovelTest extends TestCase
{

    public function testInstanciaImovel(): void
    {
        $endereco = new Endereco('Rua Teste', 'Bairro Teste', '12345678', 'Cidade Teste', 'UF');

        $proprietario = new Proprietario('joao@example.com', 'João da Silva', '12345678901');

        $corretor = new Corretor("corretor@example.com", "Corretor Teste", "12345678901", 12323232);

        $captador = new Funcionario('captador@example.com', 'Captador Teste', '12345678901', Cargo::CAPTADOR);

        $anuncio = new Anuncio();

        $condominio = new Condominio('Condominio Test', $endereco);

        $imovel = new Imovel(new Endereco('Rua Teste', 'Bairro Teste', '12345678', 'Cidade Teste', 'UF'), Status::VENDA, Categoria::CASA);


        $imovel->setId(2);
        $imovel->setvalorVenda(2000.500);
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
    }

    public function testCadastro()
    {
        $endereco = new Endereco('Rua Teste', 'Bairro Teste', '12345678', 'Cidade Teste', 'UF');

        $proprietario = new Proprietario('joao@example.com', 'João da Silva', '12345678901');

        $corretor = new Corretor("corretor@example.com", "Corretor Teste", "12345678901", 12323232);

        $captador = new Funcionario('captador@example.com', 'Captador Teste', '12345678901', Cargo::CAPTADOR);

        $anuncio = new Anuncio();

        $condominio = new Condominio('Condominio Test', $endereco);

        $imovel = new Imovel(new Endereco('Rua Teste', 'Bairro Teste', '12345678', 'Cidade Teste', 'UF'), Status::VENDA, Categoria::CASA);

        $imovel->setId(2);
        $imovel->setvalorVenda(2000.500);
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

        $imovelService = new ImovelService();
        $resultado = $imovelService->cadastrar($imovel);
        $this->assertInstanceOf(Imovel::class, $resultado);
    }

    public function testAtualizacao()
    {
        $endereco = new Endereco('Rua Teste', 'Bairro Teste', '12345678', 'Cidade Teste', 'UF');

        $proprietario = new Proprietario('joao@example.com', 'João da Silva', '12345678901');

        $corretor = new Corretor("corretor@example.com", "Corretor Teste", "12345678901", 12323232);

        $captador = new Funcionario('captador@example.com', 'Captador Teste', '12345678901', Cargo::CAPTADOR);

        $anuncio = new Anuncio();

        $condominio = new Condominio('Condominio Test', $endereco);

        $imovel = new Imovel(new Endereco('Rua Teste', 'Bairro Teste', '12345678', 'Cidade Teste', 'UF'), Status::VENDA, Categoria::CASA);

        $imovel->setId(2);
        $imovel->setvalorVenda(2000.500);
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

        $imovelService = new ImovelService();
        $imovelResultado = $imovelService->cadastrar($imovel);

        if (isInstanceOf($imovelResultado, Imovel::class)) {
            $imovel->setId(2);
            $imovel->setvalorVenda(2000.500);
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
            try {
                $imovelService->atualizar($imovel);
            } catch (Exception $e) {
                $this->fail("Falha ao atualizar o imóvel: " . $e->getMessage());
            }
        }
    }

    public function testRemocao() {}
}
