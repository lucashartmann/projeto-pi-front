<?php

require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/captador.php';
require_once __DIR__ . '/../model/atendimento.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/anuncio.php';
require_once __DIR__ . '/../model/venda_aluguel.php';
require_once __DIR__ . '/../model/condominio.php';
require_once __DIR__ . '/../model/gerente.php';
require_once __DIR__ . '/../model/usuario.php';
require_once __DIR__ . '/../model/proprietario.php';


class Init
{

    public $imobiliaria;
    public $usuario_atual = "";
    public $filtros_imovel = [
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

    public $filtros_condominio = [
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

    public function __construct()
    {
        $this->imobiliaria = new Imobiliaria("GameStart", "00000000000");

        $this->imobiliaria->cadastrar_lista_filtros($this->filtros_imovel, "filtros_imovel");
        $this->imobiliaria->cadastrar_lista_filtros($this->filtros_condominio, "filtros_condominio");

        $administrador = new Usuario(
            username: "administrador",
            senha: "123",
            email: "admin@example->com",
            nome: "Lucas",
            cpf_cnpj: "00000000000",
            tipo: Tipo::ADMINISTRADOR
        );
        $administrador_dois = new Usuario(
            username: "admin2",
            senha: "123",
            email: "admin2@example->com",
            nome: "Felipe",
            cpf_cnpj: "11111111111",
            tipo: Tipo::ADMINISTRADOR
        );
        $gerente_um = new Gerente(
            username: "gerente",
            senha: "123",
            email: "gerente@example->com",
            nome: "Pedro",
            cpf_cnpj: "22222222222"
        );
        $gerente_dois = new Gerente(
            username: "gerente2",
            senha: "123",
            email: "gerente2@example->com",
            nome: "Rosangela",
            cpf_cnpj: "33333333333"
        );
        $comprador = new Cliente(
            username: "cliente",
            senha: "123",
            email: "cliente@example->com",
            nome: "Marcela",
            cpf_cnpj: "44444444444"
        );
        $comprador_dois = new Cliente(
            username: "cliente2",
            senha: "123",
            email: "cliente2@example->com",
            nome: "Rute Dois",
            cpf_cnpj: "77777777777"
        );
        $captador_um = new Captador(
            username: "captador",
            senha: "123",
            email: "captador@example->com",
            nome: "Ana",
            cpf_cnpj: "55555555555"
        );
        $captador_dois = new Captador(
            username: "captador2",
            senha: "123",
            email: "captador2@example->com",
            nome: "Ana Dois",
            cpf_cnpj: "88888888888"
        );
        $corretor_um = new Corretor(
            username: "corretor",
            senha: "123",
            email: "corretor@example->com",
            nome: "João",
            cpf_cnpj: "66666666666",
            creci: "123456"
        );
        $corretor_dois = new Corretor(
            username: "corretor2",
            senha: "123",
            email: "corretor2@example->com",
            nome: "Elisabeth",
            cpf_cnpj: "99999999999",
            creci: "654321"
        );

        if (!$this->imobiliaria->get_lista_usuarios()) {

            $this->imobiliaria->cadastrar_usuario($administrador);
            $this->imobiliaria->cadastrar_usuario($gerente_um);
            $this->imobiliaria->cadastrar_usuario($comprador);
            $this->imobiliaria->cadastrar_usuario($captador_um);
            $this->imobiliaria->cadastrar_usuario($corretor_um);
            $this->imobiliaria->cadastrar_usuario($administrador_dois);
            $this->imobiliaria->cadastrar_usuario($gerente_dois);
            $this->imobiliaria->cadastrar_usuario($comprador_dois);
            $this->imobiliaria->cadastrar_usuario($captador_dois);
            $this->imobiliaria->cadastrar_usuario($corretor_dois);
        }

        $proprietario_um = new Proprietario(
            email: "proprietario@example->com",
            nome: "Maria",
            cpf_cnpj: "00000000000"
        );

        $proprietario_dois = new Proprietario(
            email: "proprietario2@example->com",
            nome: "Joaquim",
            cpf_cnpj: "11111111111"
        );


        if (!$this->imobiliaria->get_lista_proprietarios()) {

            $this->imobiliaria->cadastrar_proprietario($proprietario_dois);
            $this->imobiliaria->cadastrar_proprietario($proprietario_um);
        }

        $endereco_um = new Endereco(
            rua: "Rua A",
            bairro: "Centro",
            cep: 12345678,
            cidade: "Cidade X",
            uf: "Estado Y"
        );
        $endereco_um->set_numero("123");

        $endereco_dois = new Endereco(
            rua: "Rua B",
            bairro: "Bairro Z",
            cep: 87654321,
            cidade: "Cidade W",
            uf: "Estado V"
        );
        $endereco_dois->set_numero("456");

        if (!$this->imobiliaria->get_lista_enderecos()) {
            $cadastro = $this->imobiliaria->cadastrar_endereco($endereco_um);
            $cadastro_dois = $this->imobiliaria->cadastrar_endereco($endereco_dois);
        }
        $consulta_um = NULL;
        $consulta_dois = NULL;
        $consulta_um = $this->imobiliaria->verificar_endereco($endereco_um);
        $condominio_um = new Condominio("Way", $consulta_um);
        $this->imobiliaria->cadastrar_condominio($condominio_um);
        $consultar_condominio = NULL;
        if ($consulta_um) {
            $consultar_condominio = $this->imobiliaria->get_condominio_por_id_endereco(
                $consulta_um->get_id()
            );
        }
        $anuncio_um = new Anuncio();

        $blob = file_get_contents("assets/apartamento2/5661211031.jpg");
        $blob2 = file_get_contents("assets/apartamento2/5661211294.jpg");

        $anuncio_um->set_imagens([$blob, $blob, $blob2, $blob2, $blob]);
        $anuncio_um->set_titulo("Apartamento de 1 quarto, venda ou aluguel");
        $anuncio_um->set_descricao("Imóvel com uma posição privilegiada, próximo a parques, shoppings e com fácil acesso ao transporte público-> O apartamento possui uma sala aconchegante, cozinha funcional, banheiro moderno e um quarto confortável-> Ideal para quem busca praticidade e qualidade de vida->");

        $anuncio_dois = new Anuncio();

        $blob3 = file_get_contents("assets/apartamento1/5661162882.jpg");
        $anuncio_dois->set_imagens([$blob3, $blob3, $blob3, $blob3, $blob3]);
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

        if (!$this->imobiliaria->get_estoque()->get_lista_imoveis()) {
            $cadastro_anuncio = $this->imobiliaria->get_estoque()->cadastrar_anuncio($anuncio_um);
            $cadastro_anuncio2 = $this->imobiliaria->get_estoque()->cadastrar_anuncio($anuncio_dois);
            if ($cadastro_anuncio !== NULL) {
                $anuncio_um->set_id($cadastro_anuncio);
                $imovel_um->set_anuncio($anuncio_um);
                if ($consultar_condominio) {
                    $imovel_um->set_condominio($consultar_condominio);
                }
                $this->imobiliaria->get_estoque()->cadastrar_imovel($imovel_um);
            }

            if ($cadastro_anuncio2 !== NULL) {
                $anuncio_dois->set_id($cadastro_anuncio2);
                $imovel_dois->set_anuncio($anuncio_dois);
                $imovel_dois->set_condominio($consultar_condominio);
                $this->imobiliaria->get_estoque()->cadastrar_imovel($imovel_dois);
            }

            $cadastro_anuncio = $this->imobiliaria->get_estoque()->cadastrar_anuncio(
                $anuncio_um
            );

            if ($cadastro_anuncio !== NULL) {
                $anuncio_um->set_id($cadastro_anuncio);
                $imovel_tres->set_anuncio($anuncio_um);
                $imovel_tres->set_condominio($consultar_condominio);
                $this->imobiliaria->get_estoque()->cadastrar_imovel($imovel_tres);
            }
        }

        $consulta_dois = $this->imobiliaria->verificar_endereco($endereco_dois);
        $condominio_dois = new Condominio("Premium", $consulta_dois);
        $this->imobiliaria->cadastrar_condominio($condominio_dois);
        $condominio_dois = NULL;
        if ($consulta_dois) {
            $condominio_dois = $this->imobiliaria->get_condominio_por_id_endereco(
                $consulta_dois->get_id()
            );
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
        if (!$this->imobiliaria->get_estoque()->get_lista_imoveis()) {
            $cadastro_anuncio = $this->imobiliaria->get_estoque()->cadastrar_anuncio(
                $anuncio_um
            );

            if ($cadastro_anuncio !== NULL) {
                $anuncio_um->set_id($cadastro_anuncio);
                $imovel_quatro->set_anuncio($anuncio_um);
                $imovel_quatro->set_condominio($condominio_dois);
                $this->imobiliaria->get_estoque()->cadastrar_imovel($imovel_quatro);
            }
            $cadastro_anuncio = $this->imobiliaria->get_estoque()->cadastrar_anuncio(
                $anuncio_um
            );

            if ($cadastro_anuncio !== NULL) {
                $anuncio_um->set_id($cadastro_anuncio);
                $imovel_cinco->set_anuncio($anuncio_um);
                $imovel_cinco->set_condominio($condominio_dois);
                $this->imobiliaria->get_estoque()->cadastrar_imovel($imovel_cinco);
            }
        }
        $atendimento_um = new Atendimento();
        $atendimento_um->set_cliente($comprador);
        $atendimento_um->set_corretor($corretor_um);
        $atendimento_um->set_imovel($imovel_um);
        $atendimento_um->set_status(Status::EM_ANDAMENTO);
        $atendimento_dois = new Atendimento();
        $atendimento_dois->set_cliente($comprador_dois);
        $atendimento_dois->set_corretor($corretor_dois);
        $atendimento_dois->set_imovel($imovel_dois);
        $atendimento_dois->set_status(Status::PENDENTE);
        if (!$this->imobiliaria->get_lista_atendimentos()) {
            $this->imobiliaria->cadastrar_atendimento($atendimento_um);
            $this->imobiliaria->cadastrar_atendimento($atendimento_dois);
        }
    }
}
