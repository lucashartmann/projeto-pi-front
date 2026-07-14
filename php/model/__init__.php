<?php

require_once __DIR__ . '/cliente.php';
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
require_once __DIR__ . '/anexo.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/anexoDAO.php';
require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/condominioDAO.php';
require_once __DIR__ . '/../dao/anuncioDAO.php';
require_once __DIR__ . '/../dao/atendimentoDAO.php';
require_once __DIR__ . '/../dao/enderecoDAO.php';
require_once __DIR__ . '/../dao/proprietarioDAO.php';
require_once __DIR__ . '/../dao/visitaDAO.php';
require_once __DIR__ . '/../dao/vistoriaDAO.php';

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

    // public static function getInstance()
    // {
    //     if ( === null) {
    //         self::initialize();
    //     }
    //     return ;
    // }

    public static function initialize()
    {

        $nomesCondominio = [
            "Vila Nova",
            "Residencial das Flores",
            "Jardim Europa",
            "Bosque Imperial",
            "Parque das Águas",
            "Villa Toscana",
            "Morada do Sol",
            "Horizonte Azul",
            "Green Park",
            "Alto da Serra",
            "Solar dos Ipês",
            "Residencial Primavera",
            "Condomínio Bella Vista",
            "Portal do Lago",
            "Recanto Verde",
            "Residencial Monte Carlo",
            "Reserva das Palmeiras",
            "Parque dos Pássaros",
            "Villa Verona",
            "Residencial Viena",
            "Condomínio Firenze",
            "Jardins do Vale",
            "Essenza Residence",
            "Residencial Infinity",
            "Villa di Roma",
            "Mirante do Bosque",
            "Residencial Porto Seguro",
            "Reserva Imperial",
            "Condomínio Atlântico",
            "Jardim das Acácias",
            "Parque Central",
            "Residencial Alameda",
            "Condomínio Aurora",
            "Vivendas do Parque",
            "Residencial San Marino",
            "Villa Toscana Premium",
            "Condomínio Belvedere",
            "Residencial Costa Verde",
            "Jardim dos Lagos",
            "Portal das Nações",
            "Residencial Saint Germain",
            "Bosque dos Pinheiros",
            "Condomínio Mont Blanc",
            "Villa Firenze",
            "Parque das Oliveiras",
            "Residencial Barcelona",
            "Jardins de Provence",
            "Condomínio Riviera",
            "Reserva do Lago",
            "Residencial Harmonia",
        ];

        $emails = [
            "hotmail.com",
            "gmail.com",
            "outlook.com",
        ];

        $dds = [
            "11",
            "21",
            "31",
            "41",
            "51",
            "61",
            "71",
            "81",
            "91",
        ];

        $nomes = [
            "Carlos",
            "Maria",
            "João",
            "Ana",
            "Pedro",
            "Julia",
            "Lucas",
            "Mariana",
            "Gabriel",
            "Beatriz",
            "Rafael",
            "Larissa",
            "Felipe",
            "Camila",
            "Bruno",
            "Diego",
            "Fernanda",
            "Gustavo",
        ];

        $sobrenomes = [
            "Silva",
            "Santos",
            "Oliveira",
            "Souza",
            "Rodrigues",
            "Ferreira",
            "Almeida",
            "Costa",
            "Gomes",
            "Martins",
            "Lima",
            "Carvalho",
            "Pereira",
            "Barbosa",
            "Rocha",
            "Dias",
            "Mendes",
        ];

        $segundoNomes = [
            "Alves",
            "Ribeiro",
            "Moura",
            "Cardoso",
            "Araújo",
            "Correia",
            "Castro",
            "Freitas",
            "Teixeira",
            "Moreira",
            "Melo",
            "Cavalcante",
            "Barros",
            "Farias",
            "Pinto",
        ];


        $situacoes = [
            "Em Costrução",
            "Novo",
            "Usado"
        ];

        $ocupacoes = [
            "Desocupado",
            "Inquilino",
            "Proprietário"
        ];


        $condicoes = [
            "Bom",
            "Ótimo",
            "Regular"
        ];


        $enderecos = [
            [
                "Rua" => "Av. Bento Gonçalves",
                "Bairro" => "Partenon",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90650001"
            ],
            [
                "Rua" => "Rua dos Andradas",
                "Bairro" => "Centro",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90020007"
            ],
            [
                "Rua" => "Av. Ipiranga",
                "Bairro" => "Centro Histórico",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90010000"
            ],
            [
                "Rua" => "Rua Zélia Maria Dutra Abichequer",
                "Bairro" => "Florestal",
                "Cidade" => "Lajeado",
                "Estado" => "RS",
                "CEP" => "95900708"
            ],
            [
                "Rua" => "Rua Monsenhor Scalabrini",
                "Bairro" => "Centro",
                "Cidade" => "Encantado",
                "Estado" => "RS",
                "CEP" => "95960000"
            ],
            [
                "Rua" => "Rua Padre Chagas",
                "Bairro" => "Moinhos de Vento",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90570080"
            ],
            [
                "Rua" => "Av. Getúlio Vargas",
                "Bairro" => "Menino Deus",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90150000"
            ],
            [
                "Rua" => "Rua Vicente da Fontoura",
                "Bairro" => "Rio Branco",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90640000"
            ],
            [
                "Rua" => "Rua Domingos Crescêncio",
                "Bairro" => "Santana",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90650090"
            ],
            [
                "Rua" => "Av. Assis Brasil",
                "Bairro" => "Sarandi",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "91110000"
            ],
            [
                "Rua" => "Rua Coronel Bordini",
                "Bairro" => "Auxiliadora",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90440001"
            ],
            [
                "Rua" => "Av. Nilo Peçanha",
                "Bairro" => "Boa Vista",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "91330000"
            ],
            [
                "Rua" => "Rua Mariante",
                "Bairro" => "Independência",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90035110"
            ],
            [
                "Rua" => "Av. Cristóvão Colombo",
                "Bairro" => "Floresta",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90560000"
            ],
            [
                "Rua" => "Rua Félix da Cunha",
                "Bairro" => "Higienópolis",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90570000"
            ],
            [
                "Rua" => "Av. Wenceslau Escobar",
                "Bairro" => "Tristeza",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "91900000"
            ],
            [
                "Rua" => "Rua José de Alencar",
                "Bairro" => "Azenha",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "908800480"
            ],
            [
                "Rua" => "Av. Cavalhada",
                "Bairro" => "Cavalhada",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "91740000"
            ],
            [
                "Rua" => "Rua Anita Garibaldi",
                "Bairro" => "Mont'Serrat",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90450000"
            ],
            [
                "Rua" => "Av. Carlos Gomes",
                "Bairro" => "Três Figueiras",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90480000"
            ]
        ];

        $lista_status = ["Venda", "Aluguel", "Venda e Aluguel", "Alugado", "Vendido", "Pendente"];

        $categorias = [
            "Sala Comercial",
            "Apartamento",
            "Loja",
            "Casa",
            "Cobertura",
            "Loft",
            "Studio",
            "Depósito",
            "Galpão",
            "Pavilhão",
            "Prédio Comercial",
            "Ponto Comercial",
            "Empreendimento",
            "Casa em Condomínio",
            "Sobrado",
            "Sítio",
            "Terreno",
            "Kitnet",
            "Chácara",
            "Fazenda"
        ];

        $complementos = [
            "A",
            "B",
            "C",
            "D",
            "E",
            "F",
        ];

        $imovelDAO = new ImovelDAO();
        $anexoDAO = new AnexoDAO();
        $anuncioDAO = new AnuncioDAO();
        $atendimentoDAO = new AtendimentoDAO();
        $condominioDAO = new CondominioDAO();
        $enderecoDAO = new EnderecoDAO();
        $proprietarioDAO = new ProprietarioDAO();
        $usuarioDAO = new UsuarioDAO();
        $visitaDAO = new VisitaDAO();
        $vistoriaDAO = new VistoriaDAO();

        if (empty($imovelDAO->getConexao()->getListaFiltrosApartamento())) {
            $imovelDAO->getConexao()->cadastrarListaFiltros(self::$filtrosImovel, "filtros_imovel");
        }
        if (empty($condominioDAO->getListaFiltrosCondominio())) {
            $imovelDAO->getConexao()->cadastrarListaFiltros(self::$filtrosCondominio, "filtros_condominio");
        }

        if (count($imovelDAO->getListaImoveis()) == 0) {
            for ($i = 1; $i <= 50; $i++) {
                $sequencial = ($i - 1) * 8;
                $cpfVistoriador = str_pad((string) ($sequencial + 1), 11, '0', STR_PAD_LEFT);
                $cpfFinanceiro = str_pad((string) ($sequencial + 2), 11, '0', STR_PAD_LEFT);
                $cpfCorretor = str_pad((string) ($sequencial + 3), 11, '0', STR_PAD_LEFT);
                $cpfCaptador = str_pad((string) ($sequencial + 4), 11, '0', STR_PAD_LEFT);
                $cpfGerente = str_pad((string) ($sequencial + 5), 11, '0', STR_PAD_LEFT);
                $cpfAdministrador = str_pad((string) ($sequencial + 6), 11, '0', STR_PAD_LEFT);
                $cpfCliente = str_pad((string) ($sequencial + 7), 11, '0', STR_PAD_LEFT);
                $cpfProprietario = str_pad((string) ($sequencial + 8), 11, '0', STR_PAD_LEFT);

                $rgVistoriador = str_pad((string) ($sequencial + 1), 9, '0', STR_PAD_LEFT);
                $rgFinanceiro = str_pad((string) ($sequencial + 2), 9, '0', STR_PAD_LEFT);
                $rgCorretor = str_pad((string) ($sequencial + 3), 9, '0', STR_PAD_LEFT);
                $rgCaptador = str_pad((string) ($sequencial + 4), 9, '0', STR_PAD_LEFT);
                $rgGerente = str_pad((string) ($sequencial + 5), 9, '0', STR_PAD_LEFT);
                $rgAdministrador = str_pad((string) ($sequencial + 6), 9, '0', STR_PAD_LEFT);
                $rgCliente = str_pad((string) ($sequencial + 7), 9, '0', STR_PAD_LEFT);
                $rgProprietario = str_pad((string) ($sequencial + 8), 9, '0', STR_PAD_LEFT);

                $telefone = function (int $id): string {
                    return '55519' . str_pad((string) $id, 8, '0', STR_PAD_LEFT);
                };

                $telefoneVistoriador = [$telefone($sequencial + 1), $telefone($sequencial + 2)];
                $telefoneFinanceiro = [$telefone($sequencial + 3), $telefone($sequencial + 4)];
                $telefoneCorretor = [$telefone($sequencial + 5), $telefone($sequencial + 6)];
                $telefoneCaptador = [$telefone($sequencial + 7), $telefone($sequencial + 8)];
                $telefoneGerente = [$telefone($sequencial + 9), $telefone($sequencial + 10)];
                $telefoneAdministrador = [$telefone($sequencial + 11), $telefone($sequencial + 12)];
                $telefoneCliente = [$telefone($sequencial + 13), $telefone($sequencial + 14)];
                $telefoneProprietario = [$telefone($sequencial + 15), $telefone($sequencial + 16)];

                $vistoriador = new Usuario(
                    username: "vistoriador$i@{$emails[array_rand($emails)]}",
                    senha: "Vistoriador$i#",
                    email: "vistoriador$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: $cpfVistoriador,
                    tipo: Tipo::VISTORIADOR
                );

                $vistoriador->setRg($rgVistoriador);
                $vistoriador->setTelefones($telefoneVistoriador);
                $vistoriador->setDataNascimento(DateTime::createFromFormat('Y-m-d', '1990-01-01')->modify("+$i days"));

                $dataRandom = new DateTime();
                $dataRandom->setTimestamp(rand(strtotime('2020-01-01'), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $vistoriador->setDataCadastro($dataRandom);
                $dataRandomModificacao = new DateTime();
                $dataRandomModificacao->setTimestamp(rand(strtotime($dataRandom->format('Y-m-d H:i:s')), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $opcoes = [
                    null,
                    $dataRandomModificacao
                ];
                $vistoriador->setDataModificacao($opcoes[array_rand($opcoes)]);

                $financeiro = new Usuario(
                    username: "financeiro$i@{$emails[array_rand($emails)]}",
                    senha: "Financeiro$i#",
                    email: "financeiro$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: $cpfFinanceiro,
                    tipo: Tipo::FINANCEIRO
                );

                $financeiro->setRg($rgFinanceiro);
                $financeiro->setTelefones($telefoneFinanceiro);
                $financeiro->setDataNascimento(DateTime::createFromFormat('Y-m-d', '1990-01-01')->modify("+$i days"));

                $dataRandom = new DateTime();
                $dataRandom->setTimestamp(rand(strtotime('2020-01-01'), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $financeiro->setDataCadastro($dataRandom);
                $dataRandomModificacao = new DateTime();
                $dataRandomModificacao->setTimestamp(rand(strtotime($dataRandom->format('Y-m-d H:i:s')), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $opcoes = [
                    null,
                    $dataRandomModificacao
                ];
                $financeiro->setDataModificacao($opcoes[array_rand($opcoes)]);

                $corretor = new Corretor(
                    username: "corretor$i@{$emails[array_rand($emails)]}",
                    senha: "Corretor$i#",
                    email: "corretor$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: $cpfCorretor,
                    creci: str_repeat($i, 6)
                );

                $corretor->setRg($rgCorretor);
                $corretor->setTelefones($telefoneCorretor);
                $corretor->setDataNascimento(DateTime::createFromFormat('Y-m-d', '1990-01-01')->modify("+$i days"));

                $dataRandom = new DateTime();
                $dataRandom->setTimestamp(rand(strtotime('2020-01-01'), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $corretor->setDataCadastro($dataRandom);
                $dataRandomModificacao = new DateTime();
                $dataRandomModificacao->setTimestamp(rand(strtotime($dataRandom->format('Y-m-d H:i:s')), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $opcoes = [
                    null,
                    $dataRandomModificacao
                ];
                $corretor->setDataModificacao($opcoes[array_rand($opcoes)]);

                $captador = new Captador(
                    username: "captador$i@{$emails[array_rand($emails)]}",
                    senha: "Captador$i#",
                    email: "captador$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: $cpfCaptador
                );

                $captador->setRg($rgCaptador);
                $captador->setTelefones($telefoneCaptador);
                $captador->setDataNascimento(DateTime::createFromFormat('Y-m-d', '1990-01-01')->modify("+$i days"));

                $dataRandom = new DateTime();
                $dataRandom->setTimestamp(rand(strtotime('2020-01-01'), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $captador->setDataCadastro($dataRandom);
                $dataRandomModificacao = new DateTime();
                $dataRandomModificacao->setTimestamp(rand(strtotime($dataRandom->format('Y-m-d H:i:s')), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $opcoes = [
                    null,
                    $dataRandomModificacao
                ];
                $captador->setDataModificacao($opcoes[array_rand($opcoes)]);

                $gerente = new Gerente(
                    username: "gerente$i@{$emails[array_rand($emails)]}",
                    senha: "Gerente$i#",
                    email: "gerente$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: $cpfGerente
                );

                $gerente->setRg($rgGerente);
                $gerente->setTelefones($telefoneGerente);
                $gerente->setDataNascimento(DateTime::createFromFormat('Y-m-d', '1990-01-01')->modify("+$i days"));

                $dataRandom = new DateTime();
                $dataRandom->setTimestamp(rand(strtotime('2020-01-01'), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $gerente->setDataCadastro($dataRandom);
                $dataRandomModificacao = new DateTime();
                $dataRandomModificacao->setTimestamp(rand(strtotime($dataRandom->format('Y-m-d H:i:s')), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $opcoes = [
                    null,
                    $dataRandomModificacao
                ];
                $gerente->setDataModificacao($opcoes[array_rand($opcoes)]);

                $administrador = new Usuario(
                    username: "administrador$i@{$emails[array_rand($emails)]}",
                    senha: "Administrador$i#",
                    email: "administrador$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: $cpfAdministrador,
                    tipo: Tipo::ADMINISTRADOR
                );

                $administrador->setRg($rgAdministrador);
                $administrador->setTelefones($telefoneAdministrador);
                $administrador->setDataNascimento(DateTime::createFromFormat('Y-m-d', '1990-01-01')->modify("+$i days"));

                $dataRandom = new DateTime();
                $dataRandom->setTimestamp(rand(strtotime('2020-01-01'), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $administrador->setDataCadastro($dataRandom);
                $dataRandomModificacao = new DateTime();
                $dataRandomModificacao->setTimestamp(rand(strtotime($dataRandom->format('Y-m-d H:i:s')), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $opcoes = [
                    null,
                    $dataRandomModificacao
                ];
                $administrador->setDataModificacao($opcoes[array_rand($opcoes)]);

                $cliente = new Cliente(
                    username: "cliente$i@{$emails[array_rand($emails)]}",
                    senha: "Cliente$i#",
                    email: "cliente$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: $cpfCliente
                );

                $cliente->setRg($rgCliente);
                $cliente->setTelefones($telefoneCliente);
                $cliente->setDataNascimento(DateTime::createFromFormat('Y-m-d', '1990-01-01')->modify("+$i days"));

                $dataRandom = new DateTime();
                $dataRandom->setTimestamp(rand(strtotime('2020-01-01'), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $cliente->setDataCadastro($dataRandom);
                $dataRandomModificacao = new DateTime();
                $dataRandomModificacao->setTimestamp(rand(strtotime($dataRandom->format('Y-m-d H:i:s')), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $opcoes = [
                    null,
                    $dataRandomModificacao
                ];
                $cliente->setDataModificacao($opcoes[array_rand($opcoes)]);

                $proprietario = new Proprietario(
                    email: "proprietario$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: $cpfProprietario
                );

                $proprietario->setRg($rgProprietario);
                $proprietario->setTelefones($telefoneProprietario);
                $proprietario->setDataNascimento(DateTime::createFromFormat('Y-m-d', '1990-01-01')->modify("+$i days"));

                $dataRandom = new DateTime();
                $dataRandom->setTimestamp(rand(strtotime('2020-01-01'), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $proprietario->setDataCadastro($dataRandom);
                $dataRandomModificacao = new DateTime();
                $dataRandomModificacao->setTimestamp(rand(strtotime($dataRandom->format('Y-m-d H:i:s')), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $opcoes = [
                    null,
                    $dataRandomModificacao
                ];
                $proprietario->setDataModificacao($opcoes[array_rand($opcoes)]);

                $idCorretor = $usuarioDAO->cadastrarUsuario($corretor);
                if ($idCorretor) {
                    $corretor->setId($idCorretor);
                } else {
                    $corretor = null;
                }
                $idCaptador = $usuarioDAO->cadastrarUsuario($captador);
                if ($idCaptador) {
                    $captador->setId($idCaptador);
                } else {
                    $captador = null;
                }
                $idGerente = $usuarioDAO->cadastrarUsuario($gerente);
                if ($idGerente) {
                    $gerente->setId($idGerente);
                } else {
                    $gerente = null;
                }
                $idCliente = $usuarioDAO->cadastrarUsuario($cliente);
                if ($idCliente) {
                    $cliente->setId($idCliente);
                } else {
                    $cliente = null;
                }
                $idAdministrador = $usuarioDAO->cadastrarUsuario($administrador);
                if ($idAdministrador) {
                    $administrador->setId($idAdministrador);
                } else {
                    $administrador = null;
                }
                $idVistoriador = $usuarioDAO->cadastrarUsuario($vistoriador);
                if ($idVistoriador) {
                    $vistoriador->setId($idVistoriador);
                } else {
                    $vistoriador = null;
                }
                $idFinanceiro = $usuarioDAO->cadastrarUsuario($financeiro);
                if ($idFinanceiro) {
                    $financeiro->setId($idFinanceiro);
                } else {
                    $financeiro = null;
                }
                $proprietarioDAO->cadastrarProprietario($proprietario);

                $numeroAleatorioEndereco = rand(0, count($enderecos) - 1);

                $endereco = new Endereco(
                    rua: $enderecos[$numeroAleatorioEndereco]["Rua"],
                    bairro: $enderecos[$numeroAleatorioEndereco]["Bairro"],
                    cidade: $enderecos[$numeroAleatorioEndereco]["Cidade"],
                    cep: $enderecos[$numeroAleatorioEndereco]["CEP"],
                    uf: $enderecos[$numeroAleatorioEndereco]["Estado"],
                );

                $endereco->setNumero($i);

                $verificarEndereco = $enderecoDAO->verificarEndereco($endereco);

                if ($verificarEndereco) {
                    $endereco = $verificarEndereco;
                } else {
                    $idEndereco = $enderecoDAO->cadastrarEndereco($endereco) ?? null;
                    if ($idEndereco) {
                        $endereco->setId($idEndereco);
                    } else {
                        $endereco = null;
                    }
                }

                $venda = floatval(rand(0, 1000000) / 1000000) * 1000000;
                $aluguel = floatval(rand(0, 10000) / 10000) * 10000;

                $imovel = new Imovel($endereco, Status::tryFrom($lista_status[array_rand($lista_status)]), Categoria::tryFrom($categorias[array_rand($categorias)]));
                $numeroComplemento = rand(1, 100);
                $imovel->setComplemento($numeroComplemento ? $numeroComplemento . " " . $complementos[array_rand($complementos)] : null);
                $imovel->setValorVenda($venda);
                $imovel->setValorAluguel($aluguel);
                $imovel->setAndar((((string) $numeroComplemento)[0]) ?? 0);
                $imovel->setAnoConstrucao(rand(1950, 2024));
                $imovel->setAreaPrivativa(rand(0, 500));
                $imovel->setAreaTotal(rand(0, 1000));
                $imovel->setQuantBanheiros(rand(0, 5));
                $imovel->setQuantSalas(rand(0, 5));
                $imovel->setQuantVagas(rand(0, 5));
                $imovel->setQuantVarandas(rand(0, 5));
                $imovel->setQuantQuartos(rand(0, 5));
                $imovel->setIptu(rand(0, 10000));
                $imovel->setValorCondominio(rand(0, 1000));
                $limiteMaximo = isset($i) ? min($i - 1, count(self::$filtrosImovel)) : count(self::$filtrosImovel);
                $filtros = self::$filtrosImovel;
                shuffle($filtros);
                $opcao = [
                    [],
                    array_slice($filtros, 0, rand(0, $limiteMaximo))
                ];
                $imovel->setFiltros($opcao[array_rand($opcao)]);
                $listaProprietarios = $proprietarioDAO->getListaProprietarios();
                $listaProprietarios = array_values($listaProprietarios);
                $limiteMaximo = min(
                    count($listaProprietarios) - 1,
                    isset($i) ? $i - 1 : count($listaProprietarios) - 1
                );
                $opcao = [
                    [],
                    [$listaProprietarios[rand(0, $limiteMaximo)]]
                ];
                $imovel->setProprietarios($opcao[array_rand($opcao)]);
                $opcao = [
                    null,
                    $corretor,
                ];
                $imovel->setCorretor($opcao[array_rand($opcao)]);
                $opcao = [
                    null,
                    $captador,
                ];
                $imovel->setCaptador($opcao[array_rand($opcao)]);


                $dataRandom = new DateTime();
                $dataRandom->setTimestamp(rand(strtotime('2020-01-01'), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $imovel->setDataCadastro($dataRandom);

                $dataRandomModificacao = new DateTime();
                $dataRandomModificacao->setTimestamp(rand(strtotime($dataRandom->format('Y-m-d H:i:s')), strtotime(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s'))));
                $opcoes = [
                    null,
                    $dataRandomModificacao
                ];
                $imovel->setDataModificacao($opcoes[array_rand($opcoes)]);

                $imovel->setSituacao(Situacao::TryFrom($situacoes[array_rand($situacoes)] ?? null));
                $imovel->setOcupacao(Ocupacao::TryFrom($ocupacoes[array_rand($ocupacoes)] ?? null));
                $imovel->setEstado(Estado::TryFrom($condicoes[array_rand($condicoes)] ?? null));

                $titulo = $imovel->getCategoria()->value . " com " . ($imovel->getQuantidadeQuartos() ? $imovel->getQuantidadeQuartos() . " quartos" : $imovel->getAreaTotal() . " m²" ?? "") . " no bairro " . $imovel->getEndereco()->getBairro();

                $descricao = "Imóvel localizado no bairro " . $imovel->getEndereco()->getBairro() . ", com " . ($imovel->getQuantidadeQuartos() ? $imovel->getQuantidadeQuartos() . " quartos" : $imovel->getAreaTotal() . " m²" ?? "") . ". Valor de venda: R$ " . number_format($imovel->getValorVenda(), 2, ",", ".") . ". Valor de aluguel: R$ " . number_format($imovel->getValorAluguel(), 2, ",", ".") . ".";

                $anuncio = new Anuncio();


                $anuncio->setTitulo($titulo);
                $anuncio->setDescricao($descricao);
                $idAnuncio = $anuncioDAO->cadastrarAnuncio($anuncio);


                if ($idAnuncio > 0) {
                    $anuncio->setId($idAnuncio);
                    $imagem = new Anexo(
                        $idAnuncio,
                        "imoveis/imovel_" . $i . ".webp",
                        TipoAnexo::IMAGEM
                    );
                    $anuncio->setImagens([$imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem]);
                    $anuncioDAO->atualizarAnuncio($anuncio);
                }

                $imovel->setAnuncio($anuncio ?? null);

                $imovelDAO->cadastrarImovel($imovel);

                // $condominio = new Condominio($nomesCondominio[array_rand($nomesCondominio)], $endereco);
                // $limiteMaximo = isset($i) ? $i - 1 : count(self::$filtrosCondominio);
                // $limiteMaximo = max(0, $limiteMaximo);
                // $opcao = [
                //     [],
                //     array_slice(self::$filtrosCondominio, 0, rand(0, $limiteMaximo))
                // ];
                // $condominio->setFiltros($opcao[array_rand($opcao)]);
                // ->cadastrarCondominio($condominio);
            }
        }
    }
}
