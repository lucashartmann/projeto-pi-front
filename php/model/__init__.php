<?php

require_once __DIR__ . '/cliente.php';
require_once __DIR__ . '/imobiliaria.php';
require_once __DIR__ . '/corretor.php';
require_once __DIR__ . '/imovel.php';
require_once __DIR__ . '/captador.php';
require_once __DIR__ . '/atendimento.php';
require_once __DIR__ . '/endereco.php';
require_once __DIR__ . '/anuncio.php';
require_once __DIR__ . '/vendaAluguel.php';
require_once __DIR__ . '/condominio.php';
require_once __DIR__ . '/gerente.php';
require_once __DIR__ . '/usuario.php';
require_once __DIR__ . '/proprietario.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

class Init
{



    public static $imobiliaria;
    public static $usuarioAtual;
    private static $filtrosImovel = [
        "Aceita Pet",
        "Churrasqueira",
        "Armarios Embutidos",
        "Cozinha Americana",
        "Área de Serviço",
        "Suíte Master",
        "Banheiro com Janela",
        "Piscina",
        "Lareira",
        "Ar Condicionado",
        "Semi Mobiliado",
        "Mobiliado",
        "Dependência de Empregada",
        "Despensa",
        "Depósito"
    ];

    private static $filtrosCondominio = [
        "Churrasqueira Coletiva",
        "Piscina",
        "Piscina Infantil",
        "Piscina Aquecida",
        "Quiosque",
        "Sauna",
        "Quadra de Esportes",
        "Jardim",
        "Salão de Festas",
        "Academia",
        "Sala de Jogos",
        "Playground",
        "Brinquedoteca",
        "Vaga Coberta",
        "Estacionamento",
        "Vaga para Visitantes",
        "Mercado",
        "Mesa de Sinuca",
        "Mesa de Ping Pong",
        "Mesa de Pebolim",
        "Quadra de Tenis",
        "Quadra de Futebol",
        "Quadra de Basquete",
        "Quadra de Volei",
        "Quadra de Areia",
        "Bicicletario",
        "Heliponto",
        "Elevador de Serviço"
    ];

    public static function getInstance()
    {
        if (self::$imobiliaria === null) {
            self::initialize();
        }
        return self::$imobiliaria;
    }

    public static function initialize()
    {

        $consultarCondominio = NULL;
        $consultaUm = NULL;
        $consultaDois = NULL;
        $cadastroCondominio = NULL;
        $consultarCondominio = NULL;
        $blob = NULL;
        $blob2 = NULL;
        $blob3 = NULL;
        $condominioDois = NULL;
        $cadastroAnuncio = NULL;
        $cadastroAnuncio2 = NULL;

        self::$imobiliaria = new Imobiliaria("GameStart", "00000000000");

        if (empty(self::$imobiliaria->getListaFiltrosApartamento())) {
            self::$imobiliaria->cadastrarListaFiltros(self::$filtrosImovel, "filtros_imovel");
        }
        if (empty(self::$imobiliaria->getListaFiltrosCondominio())) {
            self::$imobiliaria->cadastrarListaFiltros(self::$filtrosCondominio, "filtros_condominio");
        }

        $vistoriadorUm = new Usuario(
            username: "vistoriador",
            senha: "123",
            email: "vistoriador@example.com",
            nome: "Carlos",
            cpfCnpj: "54624242424",
            tipo: Tipo::VISTORIADOR
        );

        $vistoriadorDois = new Usuario(
            username: "vistoriador2",
            senha: "123",
            email: "vistoriador2@example.com",
            nome: "Carlos Dois",
            cpfCnpj: "12323232323",
            tipo: Tipo::VISTORIADOR
        );

        $financeiroUm = new Usuario(
            username: "financeiro",
            senha: "123",
            email: "financeiro@example.com",
            nome: "Fernanda",
            cpfCnpj: "42424242424",
            tipo: Tipo::FINANCEIRO
        );

        $financeiroDois = new Usuario(
            username: "financeiro2",
            senha: "123",
            email: "financeiro2@example.com",
            nome: "Fernanda Dois",
            cpfCnpj: "34543345345",
            tipo: Tipo::FINANCEIRO
        );

        $administrador = new Usuario(
            username: "administrador",
            senha: "123",
            email: "admin@example.com",
            nome: "Lucas",
            cpfCnpj: "00000000000",
            tipo: Tipo::ADMINISTRADOR
        );
        $administradorDois = new Usuario(
            username: "admin2",
            senha: "123",
            email: "admin2@example.com",
            nome: "Felipe",
            cpfCnpj: "11111111111",
            tipo: Tipo::ADMINISTRADOR
        );
        $gerenteUm = new Gerente(
            username: "gerente",
            senha: "123",
            email: "gerente@example.com",
            nome: "Pedro",
            cpfCnpj: "22222222222"
        );
        $gerenteDois = new Gerente(
            username: "gerente2",
            senha: "123",
            email: "gerente2@example.com",
            nome: "Rosangela",
            cpfCnpj: "33333333333"
        );
        $comprador = new Cliente(
            username: "cliente",
            senha: "123",
            email: "cliente@example.com",
            nome: "Marcela",
            cpfCnpj: "44444444444"
        );
        $compradorDois = new Cliente(
            username: "cliente2",
            senha: "123",
            email: "cliente2@example.com",
            nome: "Rute Dois",
            cpfCnpj: "77777777777"
        );
        $captadorUm = new Captador(
            username: "captador",
            senha: "123",
            email: "captador@example.com",
            nome: "Ana",
            cpfCnpj: "55555555555"
        );
        $captadorDois = new Captador(
            username: "captador2",
            senha: "123",
            email: "captador2@example.com",
            nome: "Ana Dois",
            cpfCnpj: "88888888888"
        );
        $corretorUm = new Corretor(
            username: "corretor",
            senha: "123",
            email: "corretor@example.com",
            nome: "João",
            cpfCnpj: "66666666666",
            creci: "123456"
        );
        $corretorDois = new Corretor(
            username: "corretor2",
            senha: "123",
            email: "corretor2@example.com",
            nome: "Elisabeth",
            cpfCnpj: "99999999999",
            creci: "654321"
        );

        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('99999999999')) {
            self::$imobiliaria->cadastrarUsuario($corretorDois);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('66666666666')) {
            self::$imobiliaria->cadastrarUsuario($corretorUm);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('00000000000')) {
            self::$imobiliaria->cadastrarUsuario($administrador);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('22222222222')) {
            self::$imobiliaria->cadastrarUsuario($gerenteUm);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('44444444444')) {
            self::$imobiliaria->cadastrarUsuario($comprador);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('55555555555')) {
            self::$imobiliaria->cadastrarUsuario($captadorUm);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('66666666666')) {
            self::$imobiliaria->cadastrarUsuario($corretorUm);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('11111111111')) {
            self::$imobiliaria->cadastrarUsuario($administradorDois);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('33333333333')) {
            self::$imobiliaria->cadastrarUsuario($gerenteDois);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('77777777777')) {
            self::$imobiliaria->cadastrarUsuario($compradorDois);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('88888888888')) {
            self::$imobiliaria->cadastrarUsuario($captadorDois);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('99999999999')) {
            self::$imobiliaria->cadastrarUsuario($corretorDois);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('54624242424')) {
            self::$imobiliaria->cadastrarUsuario($vistoriadorUm);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('12323232323')) {
            self::$imobiliaria->cadastrarUsuario($vistoriadorDois);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('42424242424')) {
            self::$imobiliaria->cadastrarUsuario($financeiroUm);
        }
        if (!self::$imobiliaria->getUsuarioPorCpfCnpj('34543345345')) {
            self::$imobiliaria->cadastrarUsuario($financeiroDois);
        }

        $proprietarioUm = new Proprietario(
            email: "proprietario@example.com",
            nome: "Maria",
            cpfCnpj: "00000000000"
        );

        $proprietarioDois = new Proprietario(
            email: "proprietario2@example.com",
            nome: "Joaquim",
            cpfCnpj: "11111111111"
        );

        if (!self::$imobiliaria->getProprietarioPorCpfCnpj('00000000000')) {
            self::$imobiliaria->cadastrarProprietario($proprietarioUm);
        }

        if (!self::$imobiliaria->getProprietarioPorCpfCnpj('11111111111')) {
            self::$imobiliaria->cadastrarProprietario($proprietarioDois);
        }


        $enderecoUm = new Endereco(
            "Rua A",
            "Centro",
            12345678,
            "Cidade X",
            "Estado Y"
        );
        $enderecoUm->setNumero("123");

        $enderecoDois = new Endereco(
            "Rua B",
            "Bairro Z",
            87654321,
            "Cidade W",
            "Estado V"
        );
        $enderecoDois->setNumero("456");

        $consultaUm = self::$imobiliaria->verificarEndereco($enderecoUm);
        $consultaDois = self::$imobiliaria->verificarEndereco($enderecoDois);

        if (!$consultaUm) {
            self::$imobiliaria->cadastrarEndereco($enderecoUm);
            $consultaUm = self::$imobiliaria->verificarEndereco($enderecoUm);
        }
        if (!$consultaDois) {
            self::$imobiliaria->cadastrarEndereco($enderecoDois);
            $consultaDois = self::$imobiliaria->verificarEndereco($enderecoDois);
        }


        $condominioUm = new Condominio("Way", $consultaUm);
        if ($consultaUm && !self::$imobiliaria->getCondominioPorIdEndereco($consultaUm->getId())) {
            $cadastroCondominio = self::$imobiliaria->cadastrarCondominio($condominioUm);

            if ($cadastroCondominio) {
                $consultarCondominio = self::$imobiliaria->getCondominioPorIdEndereco(
                    $consultaUm->getId()
                );
            }
        } else if ($consultaUm && self::$imobiliaria->getCondominioPorIdEndereco($consultaUm->getId())) {
            $consultarCondominio = self::$imobiliaria->getCondominioPorIdEndereco(
                $consultaUm->getId()
            );
        } else {
            $consultarCondominio = NULL;
        }


        $anuncioUm = new Anuncio();

        // echo file_get_contents("../assets/apartament.jpg");

        try {
            $blob = file_get_contents("../../assets/apartament.jpg");
        } catch (Exception $e) {
            echo "Erro ao ler os arquivos de imagem: " . $e->getMessage();
        }

        try {
            $blob2 = file_get_contents("../../assets/campo.jpg");
        } catch (Exception $e) {
            echo "Erro ao ler os arquivos de imagem: " . $e->getMessage();
        }


        if ($blob && $blob2) {
            $anuncioUm->setImagens([$blob, $blob, $blob2, $blob2, $blob]);
        }

        $anuncioUm->setTitulo("Apartamento de 1 quarto, venda ou aluguel");
        $anuncioUm->setDescricao("Imóvel com uma posição privilegiada, próximo a parques, shoppings e com fácil acesso ao transporte público-> O apartamento possui uma sala aconchegante, cozinha funcional, banheiro moderno e um quarto confortável-> Ideal para quem busca praticidade e qualidade de vida->");

        $anuncioDois = new Anuncio();

        try {
            $blob3 = file_get_contents("../../assets/patio.jpg");
        } catch (Exception $e) {
            echo "Erro ao ler os arquivos de imagem: " . $e->getMessage();
        }
        if ($blob3) {
            $anuncioDois->setImagens([$blob3, $blob3, $blob3, $blob3, $blob3]);
        }

        $anuncioDois->setTitulo("Apartamento de 2 quartos, venda ou aluguel");
        $anuncioDois->setDescricao("Imóvel localizado no centro da cidade, próximo a escolas, supermercados e com fácil acesso ao transporte público-> O apartamento possui uma sala ampla, cozinha americana, banheiro social e um quarto espaçoso-> Ideal para quem busca conforto e praticidade->");

        $imovelUm = new Imovel(
            endereco: $consultaUm,
            status: Status::VENDA_ALUGUEL,
            categoria: Categoria::APARTAMENTO
        );
        $imovelUm->setValorAluguel(1500);
        $imovelUm->setValorVenda(300000);
        $imovelDois = new Imovel(
            endereco: $consultaUm,
            status: Status::ALUGUEL,
            categoria: Categoria::APARTAMENTO
        );
        $imovelDois->setValorAluguel(2000);
        $imovelTres = new Imovel(
            endereco: $consultaUm,
            status: Status::VENDIDO,
            categoria: Categoria::LOFT
        );

        if (!self::$imobiliaria->getAnuncioPorId(1)) {
            $cadastroAnuncio = self::$imobiliaria->getEstoque()->cadastrarAnuncio($anuncioUm);
        }

        if (!self::$imobiliaria->getAnuncioPorId(2)) {
            $cadastroAnuncio2 = self::$imobiliaria->getEstoque()->cadastrarAnuncio($anuncioDois);
        }

        if ($cadastroAnuncio && !self::$imobiliaria->getImovelPorId(1)) {
            $anuncioUm->setId($cadastroAnuncio);
            $imovelUm->setAnuncio($anuncioUm);
            if ($consultarCondominio) {
                $imovelUm->setCondominio($consultarCondominio);
            }
            self::$imobiliaria->getEstoque()->cadastrarImovel($imovelUm);
        }

        if ($cadastroAnuncio2 && !self::$imobiliaria->getImovelPorId(2)) {
            $anuncioDois->setId($cadastroAnuncio2);
            $imovelDois->setAnuncio($anuncioDois);
            if ($consultarCondominio) {
                $imovelDois->setCondominio($consultarCondominio);
            }
            self::$imobiliaria->getEstoque()->cadastrarImovel($imovelDois);
        }

        if ($cadastroAnuncio && !self::$imobiliaria->getImovelPorId(3)) {
            $anuncioUm->setId($cadastroAnuncio);
            $imovelTres->setAnuncio($anuncioUm);
            if ($consultarCondominio) {
                $imovelTres->setCondominio($consultarCondominio);
            }
            self::$imobiliaria->getEstoque()->cadastrarImovel($imovelTres);
        }

        $condominioDois = new Condominio("Premium", $consultaDois);

        if ($consultaDois && !self::$imobiliaria->getCondominioPorIdEndereco($consultaDois->getId())) {
            $cadastroCondominio2 = self::$imobiliaria->cadastrarCondominio($condominioDois);

            if ($cadastroCondominio2) {
                $condominioDois = self::$imobiliaria->getCondominioPorIdEndereco(
                    $consultaDois->getId()
                );
            }
        } else if ($consultaDois && self::$imobiliaria->getCondominioPorIdEndereco($consultaDois->getId())) {
            $condominioDois = self::$imobiliaria->getCondominioPorIdEndereco(
                $consultaDois->getId()
            );
        } else {
            $condominioDois = NULL;
        }

        $imovelQuatro = new Imovel(
            endereco: $consultaDois,
            status: Status::PENDENTE,
            categoria: Categoria::TERRENO
        );
        $imovelCinco = new Imovel(
            endereco: $consultaDois,
            status: Status::VENDA_ALUGUEL,
            categoria: Categoria::CASA
        );

        if ($cadastroAnuncio && !self::$imobiliaria->getImovelPorId(4)) {
            $anuncioUm->setId($cadastroAnuncio);
            $imovelQuatro->setAnuncio($anuncioUm);
            if ($condominioDois) {
                $imovelQuatro->setCondominio($condominioDois);
            }
            self::$imobiliaria->getEstoque()->cadastrarImovel($imovelQuatro);
        }

        if ($cadastroAnuncio && !self::$imobiliaria->getImovelPorId(5)) {
            $anuncioUm->setId($cadastroAnuncio);
            $imovelCinco->setAnuncio($anuncioUm);
            if ($condominioDois) {
                $imovelCinco->setCondominio($condominioDois);
            }
            self::$imobiliaria->getEstoque()->cadastrarImovel($imovelCinco);
        }

        $atendimentoUm = new Atendimento();
        $atendimentoDois = new Atendimento();
        $atendimentoUm->setStatus(StatusAtendimento::EM_ANDAMENTO);
        $atendimentoDois->setStatus(StatusAtendimento::PENDENTE);

        if (empty(self::$imobiliaria->getListaAtendimentos())) {
            $compradorAtendimento = self::$imobiliaria->getUsuarioPorCpfCnpj("44444444444");
            $corretorAtendimento = self::$imobiliaria->getUsuarioPorCpfCnpj("66666666666");
            $imovelAtendimento = self::$imobiliaria->getEstoque()->getListaImoveis()[0];
            $compradorAtendimentoDois = self::$imobiliaria->getUsuarioPorCpfCnpj("77777777777");
            $corretorAtendimentoDois = self::$imobiliaria->getUsuarioPorCpfCnpj("99999999999");
            $imovelAtendimentoDois = self::$imobiliaria->getEstoque()->getListaImoveis()[1];
            $atendimentoUm->setCliente($compradorAtendimento);
            $atendimentoUm->setCorretor($corretorAtendimento);
            $atendimentoUm->setImovel($imovelAtendimento);
            $atendimentoDois->setCliente($compradorAtendimentoDois);
            $atendimentoDois->setCorretor($corretorAtendimentoDois);
            $atendimentoDois->setImovel($imovelAtendimentoDois);
            self::$imobiliaria->cadastrarAtendimento($atendimentoUm);
            self::$imobiliaria->cadastrarAtendimento($atendimentoDois);
        }
    }
}
