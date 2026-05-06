<?php

require_once __DIR__ . '/cliente.php';
require_once __DIR__ . '/imobiliaria.php';
require_once __DIR__ . '/corretor.php';
require_once __DIR__ . '/imovel.php';
require_once __DIR__ . '/captador.php';
require_once __DIR__ . '/atendimento.php';
require_once __DIR__ . '/endereco.php';
require_once __DIR__ . '/anuncio.php';
require_once __DIR__ . '/venda_aluguel.php';
require_once __DIR__ . '/condominio.php';
require_once __DIR__ . '/gerente.php';
require_once __DIR__ . '/usuario.php';
require_once __DIR__ . '/proprietario.php';


class Init
{

    

    public static $imobiliaria;
    public static $usuario_atual;
    private static $filtros_imovel = [
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

    private static $filtros_condominio = [
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
    
    public static function getInstance() {
        if (self::$imobiliaria === null) {
            self::initialize();
        }
        return self::$imobiliaria;
    }

    public static function initialize()
    {

        $consultar_condominio = NULL;
        $consulta_um = NULL;
        $consulta_dois = NULL;
        $cadastro_condominio = NULL;
        $consultar_condominio = NULL;
        $blob = NULL;
        $blob2 = NULL;
        $blob3 = NULL;
        $condominio_dois = NULL;
        $cadastro_anuncio = NULL;
        $cadastro_anuncio2 = NULL;

        self::$imobiliaria = new Imobiliaria("GameStart", "00000000000");

        if(empty(self::$imobiliaria->get_lista_filtros_apartamento())){
            self::$imobiliaria->cadastrar_lista_filtros(self::$filtros_imovel, "filtros_imovel");
        }
        if(empty(self::$imobiliaria->get_lista_filtros_condominio())){
            self::$imobiliaria->cadastrar_lista_filtros(self::$filtros_condominio, "filtros_condominio");
        }

        $vistoriador_um = new Usuario(
            username: "vistoriador",
            senha: "123",
            email: "vistoriador@example.com",
            nome: "Carlos",
            cpf_cnpj: "54624242424",
            tipo: Tipo::VISTORIADOR
        );

        $vistoriador_dois = new Usuario(
            username: "vistoriador2",
            senha: "123",
            email: "vistoriador2@example.com",
            nome: "Carlos Dois",
            cpf_cnpj: "12323232323",
            tipo: Tipo::VISTORIADOR
        );

        $financeiro_um = new Usuario(
            username: "financeiro",
            senha: "123",
            email: "financeiro@example.com",
            nome: "Fernanda",
            cpf_cnpj: "42424242424",
            tipo: Tipo::FINANCEIRO
        );

        $financeiro_dois = new Usuario(
            username: "financeiro2",
            senha: "123",
            email: "financeiro2@example.com",
            nome: "Fernanda Dois",
            cpf_cnpj: "34543345345",
            tipo: Tipo::FINANCEIRO
        );

        $administrador = new Usuario(
            username: "administrador",
            senha: "123",
            email: "admin@example.com",
            nome: "Lucas",
            cpf_cnpj: "00000000000",
            tipo: Tipo::ADMINISTRADOR
        );
        $administrador_dois = new Usuario(
            username: "admin2",
            senha: "123",
            email: "admin2@example.com",
            nome: "Felipe",
            cpf_cnpj: "11111111111",
            tipo: Tipo::ADMINISTRADOR
        );
        $gerente_um = new Gerente(
            username: "gerente",
            senha: "123",
            email: "gerente@example.com",
            nome: "Pedro",
            cpf_cnpj: "22222222222"
        );
        $gerente_dois = new Gerente(
            username: "gerente2",
            senha: "123",
            email: "gerente2@example.com",
            nome: "Rosangela",
            cpf_cnpj: "33333333333"
        );
        $comprador = new Cliente(
            username: "cliente",
            senha: "123",
            email: "cliente@example.com",
            nome: "Marcela",
            cpf_cnpj: "44444444444"
        );
        $comprador_dois = new Cliente(
            username: "cliente2",
            senha: "123",
            email: "cliente2@example.com",
            nome: "Rute Dois",
            cpf_cnpj: "77777777777"
        );
        $captador_um = new Captador(
            username: "captador",
            senha: "123",
            email: "captador@example.com",
            nome: "Ana",
            cpf_cnpj: "55555555555"
        );
        $captador_dois = new Captador(
            username: "captador2",
            senha: "123",
            email: "captador2@example.com",
            nome: "Ana Dois",
            cpf_cnpj: "88888888888"
        );
        $corretor_um = new Corretor(
            username: "corretor",
            senha: "123",
            email: "corretor@example.com",
            nome: "João",
            cpf_cnpj: "66666666666",
            creci: "123456"
        );
        $corretor_dois = new Corretor(
            username: "corretor2",
            senha: "123",
            email: "corretor2@example.com",
            nome: "Elisabeth",
            cpf_cnpj: "99999999999",
            creci: "654321"
        );

        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('99999999999')) {
            self::$imobiliaria->cadastrar_usuario($corretor_dois);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('66666666666')) {
            self::$imobiliaria->cadastrar_usuario($corretor_um);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('00000000000')) {
            self::$imobiliaria->cadastrar_usuario($administrador);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('22222222222')) {
            self::$imobiliaria->cadastrar_usuario($gerente_um);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('44444444444')) {
            self::$imobiliaria->cadastrar_usuario($comprador);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('55555555555')) {
            self::$imobiliaria->cadastrar_usuario($captador_um);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('66666666666')) {
            self::$imobiliaria->cadastrar_usuario($corretor_um);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('11111111111')) {
            self::$imobiliaria->cadastrar_usuario($administrador_dois);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('33333333333')) {
            self::$imobiliaria->cadastrar_usuario($gerente_dois);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('77777777777')) {
            self::$imobiliaria->cadastrar_usuario($comprador_dois);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('88888888888')) {
            self::$imobiliaria->cadastrar_usuario($captador_dois);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('99999999999')) {
            self::$imobiliaria->cadastrar_usuario($corretor_dois);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('54624242424')) {
            self::$imobiliaria->cadastrar_usuario($vistoriador_um);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('12323232323')) {
            self::$imobiliaria->cadastrar_usuario($vistoriador_dois);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('42424242424')) {
            self::$imobiliaria->cadastrar_usuario($financeiro_um);
        }
        if (!self::$imobiliaria->get_usuario_por_cpf_cnpj('34543345345')) {
            self::$imobiliaria->cadastrar_usuario($financeiro_dois);
        }

        $proprietario_um = new Proprietario(
            email: "proprietario@example.com",
            nome: "Maria",
            cpf_cnpj: "00000000000"
        );

        $proprietario_dois = new Proprietario(
            email: "proprietario2@example.com",
            nome: "Joaquim",
            cpf_cnpj: "11111111111"
        );

        if (!self::$imobiliaria->get_proprietario_por_cpf_cnpj('00000000000')) {
            self::$imobiliaria->cadastrar_proprietario($proprietario_um);
        }

        if (!self::$imobiliaria->get_proprietario_por_cpf_cnpj('11111111111')) {
            self::$imobiliaria->cadastrar_proprietario($proprietario_dois);
        }


        $endereco_um = new Endereco(
            "Rua A",
            "Centro",
            12345678,
            "Cidade X",
            "Estado Y"
        );
        $endereco_um->set_numero("123");

        $endereco_dois = new Endereco(
            "Rua B",
            "Bairro Z",
            87654321,
            "Cidade W",
            "Estado V"
        );
        $endereco_dois->set_numero("456");

        $consulta_um = self::$imobiliaria->verificar_endereco($endereco_um);
        $consulta_dois = self::$imobiliaria->verificar_endereco($endereco_dois);

        if (!$consulta_um) {
            self::$imobiliaria->cadastrar_endereco($endereco_um);
            $consulta_um = self::$imobiliaria->verificar_endereco($endereco_um);
        }
        if (!$consulta_dois) {
            self::$imobiliaria->cadastrar_endereco($endereco_dois);
            $consulta_dois = self::$imobiliaria->verificar_endereco($endereco_dois);
        }


        $condominio_um = new Condominio("Way", $consulta_um);
        if ($consulta_um && !self::$imobiliaria->get_condominio_por_id_endereco($consulta_um->get_id())) {
            $cadastro_condominio = self::$imobiliaria->cadastrar_condominio($condominio_um);

            if ($cadastro_condominio) {
                $consultar_condominio = self::$imobiliaria->get_condominio_por_id_endereco(
                    $consulta_um->get_id()
                );
            }
        } else if ($consulta_um && self::$imobiliaria->get_condominio_por_id_endereco($consulta_um->get_id())) {
            $consultar_condominio = self::$imobiliaria->get_condominio_por_id_endereco(
                $consulta_um->get_id()
            );
        }
        else {
            $consultar_condominio = NULL;
        }


        $anuncio_um = new Anuncio();

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
        
        
        if ($blob && $blob2 ) {
            $anuncio_um->set_imagens([$blob, $blob, $blob2, $blob2, $blob]);
        }
        
        $anuncio_um->set_titulo("Apartamento de 1 quarto, venda ou aluguel");
        $anuncio_um->set_descricao("Imóvel com uma posição privilegiada, próximo a parques, shoppings e com fácil acesso ao transporte público-> O apartamento possui uma sala aconchegante, cozinha funcional, banheiro moderno e um quarto confortável-> Ideal para quem busca praticidade e qualidade de vida->");

        $anuncio_dois = new Anuncio();
        
        try {
            $blob3 = file_get_contents("../../assets/patio.jpg");
        } catch (Exception $e) {
            echo "Erro ao ler os arquivos de imagem: " . $e->getMessage();
        }
        if ($blob3) {
            $anuncio_dois->set_imagens([$blob3, $blob3, $blob3, $blob3, $blob3]);
        }

        $anuncio_dois->set_titulo("Apartamento de 2 quartos, venda ou aluguel");
        $anuncio_dois->set_descricao("Imóvel localizado no centro da cidade, próximo a escolas, supermercados e com fácil acesso ao transporte público-> O apartamento possui uma sala ampla, cozinha americana, banheiro social e um quarto espaçoso-> Ideal para quem busca conforto e praticidade->");

        $imovel_um = new Imovel(
            endereco: $consulta_um,
            status: Status::VENDA_ALUGUEL,
            categoria: Categoria::APARTAMENTO
        );
        $imovel_um->set_valor_aluguel(1500);
        $imovel_um->set_valor_venda(300000);
        $imovel_dois = new Imovel(
            endereco: $consulta_um,
            status: Status::ALUGUEL,
            categoria: Categoria::APARTAMENTO
        );
        $imovel_dois->set_valor_aluguel(2000);
        $imovel_tres = new Imovel(
            endereco: $consulta_um,
            status: Status::VENDIDO,
            categoria: Categoria::LOFT
        );

        if(!self::$imobiliaria->get_anuncio_por_id(1)){
            $cadastro_anuncio = self::$imobiliaria->get_estoque()->cadastrar_anuncio($anuncio_um);
        }

        if(!self::$imobiliaria->get_anuncio_por_id(2)){
            $cadastro_anuncio2 = self::$imobiliaria->get_estoque()->cadastrar_anuncio($anuncio_dois);
        }

        if ($cadastro_anuncio && !self::$imobiliaria->get_imovel_por_id(1))
            {
                $anuncio_um->set_id($cadastro_anuncio);
                $imovel_um->set_anuncio($anuncio_um);
                if ($consultar_condominio) {
                    $imovel_um->set_condominio($consultar_condominio);
                }
                self::$imobiliaria->get_estoque()->cadastrar_imovel($imovel_um);
            }

        if ($cadastro_anuncio2 && !self::$imobiliaria->get_imovel_por_id(2)) {
                $anuncio_dois->set_id($cadastro_anuncio2);
                $imovel_dois->set_anuncio($anuncio_dois);
                if ($consultar_condominio) {
                    $imovel_dois->set_condominio($consultar_condominio);
                }
                self::$imobiliaria->get_estoque()->cadastrar_imovel($imovel_dois);
            }

        if ($cadastro_anuncio && !self::$imobiliaria->get_imovel_por_id(3)) {
                $anuncio_um->set_id($cadastro_anuncio);
                $imovel_tres->set_anuncio($anuncio_um);
                if ($consultar_condominio) {
                    $imovel_tres->set_condominio($consultar_condominio);
                }
                self::$imobiliaria->get_estoque()->cadastrar_imovel($imovel_tres);
            }
       
        $condominio_dois = new Condominio("Premium", $consulta_dois);
        
        if ($consulta_dois && !self::$imobiliaria->get_condominio_por_id_endereco($consulta_dois->get_id())) {
            $cadastro_condominio2 = self::$imobiliaria->cadastrar_condominio($condominio_dois);

            if ($cadastro_condominio2) {
                $condominio_dois = self::$imobiliaria->get_condominio_por_id_endereco(
                    $consulta_dois->get_id()
                );
            }
        } else if ($consulta_dois && self::$imobiliaria->get_condominio_por_id_endereco($consulta_dois->get_id())) {
            $condominio_dois = self::$imobiliaria->get_condominio_por_id_endereco(
                $consulta_dois->get_id()
            );
        }
        else {
            $condominio_dois = NULL;
        }

        $imovel_quatro = new Imovel(
            endereco: $consulta_dois,
            status: Status::PENDENTE,
            categoria: Categoria::TERRENO
        );
        $imovel_cinco = new Imovel(
            endereco: $consulta_dois,
            status: Status::VENDA_ALUGUEL,
            categoria: Categoria::CASA
        );

        if($cadastro_anuncio && !self::$imobiliaria->get_imovel_por_id(4)){
            $anuncio_um->set_id($cadastro_anuncio);
            $imovel_quatro->set_anuncio($anuncio_um);
            if ($condominio_dois) {
                $imovel_quatro->set_condominio($condominio_dois);
            }
            self::$imobiliaria->get_estoque()->cadastrar_imovel($imovel_quatro);
        }

        if($cadastro_anuncio && !self::$imobiliaria->get_imovel_por_id(5)){
            $anuncio_um->set_id($cadastro_anuncio);
            $imovel_cinco->set_anuncio($anuncio_um);
            if ($condominio_dois) {
                $imovel_cinco->set_condominio($condominio_dois);
            }
            self::$imobiliaria->get_estoque()->cadastrar_imovel($imovel_cinco);
        }

        $atendimento_um = new Atendimento();
        $atendimento_dois = new Atendimento();
        $atendimento_um->set_status(Status_Atendimento::EM_ANDAMENTO);
        $atendimento_dois->set_status(Status_Atendimento::PENDENTE);

        if (empty(self::$imobiliaria->get_lista_atendimentos())) {
            $comprador_atendimento = self::$imobiliaria->get_usuario_por_id(6);
            $corretor_atendimento = self::$imobiliaria->get_usuario_por_id(3);
            $imovel_atendimento = self::$imobiliaria->get_estoque()->get_lista_imoveis()[0];
            $comprador_atendimento_dois = self::$imobiliaria->get_usuario_por_id(11);
            $corretor_atendimento_dois = self::$imobiliaria->get_usuario_por_id(2);
            $imovel_atendimento_dois = self::$imobiliaria->get_estoque()->get_lista_imoveis()[1];
            $atendimento_um->set_cliente($comprador_atendimento);
            $atendimento_um->set_corretor($corretor_atendimento);
            $atendimento_um->set_imovel($imovel_atendimento);
            $atendimento_dois->set_cliente($comprador_atendimento_dois);
            $atendimento_dois->set_corretor($corretor_atendimento_dois);
            $atendimento_dois->set_imovel($imovel_atendimento_dois);
            self::$imobiliaria->cadastrar_atendimento($atendimento_um);
            self::$imobiliaria->cadastrar_atendimento($atendimento_dois);
        }
    }
}
