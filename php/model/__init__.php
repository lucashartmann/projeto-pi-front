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
require_once __DIR__ . '/anexo.php';


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
                "CEP" => "90650-001"
            ],
            [
                "Rua" => "Rua dos Andradas",
                "Bairro" => "Centro",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90020-007"
            ],
            [
                "Rua" => "Av. Ipiranga",
                "Bairro" => "Centro Histórico",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90010-000"
            ],
            [
                "Rua" => "Rua Zélia Maria Dutra Abichequer",
                "Bairro" => "Florestal",
                "Cidade" => "Lajeado",
                "Estado" => "RS",
                "CEP" => "95900-708"
            ],
            [
                "Rua" => "Rua Monsenhor Scalabrini",
                "Bairro" => "Centro",
                "Cidade" => "Encantado",
                "Estado" => "RS",
                "CEP" => "95960-000"
            ],
            [
                "Rua" => "Rua Padre Chagas",
                "Bairro" => "Moinhos de Vento",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90570-080"
            ],
            [
                "Rua" => "Av. Getúlio Vargas",
                "Bairro" => "Menino Deus",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90150-000"
            ],
            [
                "Rua" => "Rua Vicente da Fontoura",
                "Bairro" => "Rio Branco",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90640-000"
            ],
            [
                "Rua" => "Rua Domingos Crescêncio",
                "Bairro" => "Santana",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90650-090"
            ],
            [
                "Rua" => "Av. Assis Brasil",
                "Bairro" => "Sarandi",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "91110-000"
            ],
            [
                "Rua" => "Rua Coronel Bordini",
                "Bairro" => "Auxiliadora",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90440-001"
            ],
            [
                "Rua" => "Av. Nilo Peçanha",
                "Bairro" => "Boa Vista",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "91330-000"
            ],
            [
                "Rua" => "Rua Mariante",
                "Bairro" => "Independência",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90035-110"
            ],
            [
                "Rua" => "Av. Cristóvão Colombo",
                "Bairro" => "Floresta",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90560-000"
            ],
            [
                "Rua" => "Rua Félix da Cunha",
                "Bairro" => "Higienópolis",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90570-000"
            ],
            [
                "Rua" => "Av. Wenceslau Escobar",
                "Bairro" => "Tristeza",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "91900-000"
            ],
            [
                "Rua" => "Rua José de Alencar",
                "Bairro" => "Azenha",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90880-480"
            ],
            [
                "Rua" => "Av. Cavalhada",
                "Bairro" => "Cavalhada",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "91740-000"
            ],
            [
                "Rua" => "Rua Anita Garibaldi",
                "Bairro" => "Mont'Serrat",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90450-000"
            ],
            [
                "Rua" => "Av. Carlos Gomes",
                "Bairro" => "Três Figueiras",
                "Cidade" => "Porto Alegre",
                "Estado" => "RS",
                "CEP" => "90480-000"
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

        self::$imobiliaria = new Imobiliaria("GameStart", "00000000000");

        if (empty(self::$imobiliaria->getListaFiltrosApartamento())) {
            self::$imobiliaria->cadastrarListaFiltros(self::$filtrosImovel, "filtros_imovel");
        }
        if (empty(self::$imobiliaria->getListaFiltrosCondominio())) {
            self::$imobiliaria->cadastrarListaFiltros(self::$filtrosCondominio, "filtros_condominio");
        }


        if (count(self::$imobiliaria->getEstoque()->getListaImoveis()) < 51) {
            for ($i = 1; $i <= 51; $i++) {

                $vistoriador = new Usuario(
                    username: "vistoriador$i@{$emails[array_rand($emails)]}",
                    senha: "Vistoriador$i#",
                    email: "vistoriador$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: str_repeat($i, 11),
                    tipo: Tipo::VISTORIADOR
                );

                $financeiro = new Usuario(
                    username: "financeiro$i@{$emails[array_rand($emails)]}",
                    senha: "Financeiro$i#",
                    email: "financeiro$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: str_repeat($i + 1, 11),
                    tipo: Tipo::FINANCEIRO
                );

                $corretor = new Corretor(
                    username: "corretor$i@{$emails[array_rand($emails)]}",
                    senha: "Corretor$i#",
                    email: "corretor$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: str_repeat($i + 2, 11),
                    creci: str_repeat($i, 6)
                );

                $captador = new Captador(
                    username: "captador$i@{$emails[array_rand($emails)]}",
                    senha: "Captador$i#",
                    email: "captador$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: str_repeat($i + 3, 11)
                );

                $gerente = new Gerente(
                    username: "gerente$i@{$emails[array_rand($emails)]}",
                    senha: "Gerente$i#",
                    email: "gerente$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: str_repeat($i + 4, 11)
                );

                $administrador = new Usuario(
                    username: "administrador$i@{$emails[array_rand($emails)]}",
                    senha: "Administrador$i#",
                    email: "administrador$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: str_repeat($i + 5, 11),
                    tipo: Tipo::ADMINISTRADOR
                );

                $cliente = new Cliente(
                    username: "cliente$i@{$emails[array_rand($emails)]}",
                    senha: "Cliente$i#",
                    email: "cliente$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: str_repeat($i + 6, 11)
                );

                $proprietario = new Proprietario(
                    email: "proprietario$i@{$emails[array_rand($emails)]}",
                    nome: "{$nomes[array_rand($nomes)]} {$segundoNomes[array_rand($segundoNomes)]} {$sobrenomes[array_rand($sobrenomes)]}",
                    cpfCnpj: str_repeat($i + 7, 11)
                );

                self::$imobiliaria->cadastrarUsuario($corretor);
                self::$imobiliaria->cadastrarUsuario($captador);
                self::$imobiliaria->cadastrarUsuario($gerente);
                self::$imobiliaria->cadastrarUsuario($cliente);
                self::$imobiliaria->cadastrarUsuario($administrador);
                self::$imobiliaria->cadastrarUsuario($vistoriador);
                self::$imobiliaria->cadastrarUsuario($financeiro);
                self::$imobiliaria->cadastrarProprietario($proprietario);

                $numeroAleatorioEndereco = rand(0, count($enderecos) - 1);

                $endereco = new Endereco(
                    rua: $enderecos[$numeroAleatorioEndereco]["Rua"],
                    bairro: $enderecos[$numeroAleatorioEndereco]["Bairro"],
                    cidade: $enderecos[$numeroAleatorioEndereco]["Cidade"],
                    cep: $enderecos[$numeroAleatorioEndereco]["CEP"],
                    uf: $enderecos[$numeroAleatorioEndereco]["Estado"],
                );

                $endereco->setNumero($i);

                $idEndereco = self::$imobiliaria->verificarEndereco($endereco) ?? self::$imobiliaria->cadastrarEndereco($endereco) ?? null;
                $endereco->setId($idEndereco);

                $venda = floatval(rand(0, 1000000) / 1000000) * 1000000;
                $aluguel = floatval(rand(0, 10000) / 10000) * 10000;

                $imovel = new Imovel($endereco, Status::tryFrom($lista_status[array_rand($lista_status)]), Categoria::tryFrom($categorias[array_rand($categorias)]));
                $numeroComplemento = rand(1, 100);
                $imovel->setComplemento($numeroComplemento ? $numeroComplemento . " " . $complementos[array_rand($complementos)] : null);
                $imovel->setValorVenda($venda);
                $imovel->setValorAluguel($aluguel);
                $imovel->setAndar((((string)$numeroComplemento)[0]) ?? 0);
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
                $imovel->setDataCadastro(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
                $imovel->setSituacao(Situacao::TryFrom($situacoes[array_rand($situacoes)] ?? null));
                $imovel->setOcupacao(Ocupacao::TryFrom($ocupacoes[array_rand($ocupacoes)] ?? null));
                $imovel->setEstado(Estado::TryFrom($condicoes[array_rand($condicoes)] ?? null));

                $titulo = $imovel->getCategoria()->value . " com " . ($imovel->getQuantidadeQuartos() ? $imovel->getQuantidadeQuartos() . " quartos" : $imovel->getAreaTotal() . " m²" ?? "") . " no bairro " . $imovel->getEndereco()->getBairro();

                $descricao = "Imóvel localizado no bairro " . $imovel->getEndereco()->getBairro() . ", com " . ($imovel->getQuantidadeQuartos() ? $imovel->getQuantidadeQuartos() . " quartos" : $imovel->getAreaTotal() . " m²" ?? "") . ". Valor de venda: R$ " . number_format($imovel->getValorVenda(), 2, ",", ".") . ". Valor de aluguel: R$ " . number_format($imovel->getValorAluguel(), 2, ",", ".") . ".";

                $anuncio = new Anuncio();


                $anuncio->setTitulo($titulo);
                $anuncio->setDescricao($descricao);
                $idAnuncio = self::$imobiliaria->getEstoque()->cadastrarAnuncio($anuncio);


                if ($idAnuncio > 0) {
                    $anuncio->setId($idAnuncio);
                    $imagem = new Anexo(
                        $idAnuncio,
                        "imoveis/imovel_" . $i . ".webp",
                        TipoAnexo::IMAGEM
                    );
                    self::$imobiliaria->cadastrarAnexo(
                        $imagem,
                    );
                    $anuncio->setImagens([$imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem, $imagem]);
                    self::$imobiliaria->getEstoque()->atualizarAnuncio($anuncio);
                }

                $imovel->setAnuncio($anuncio);

                if ($venda > 0 && $aluguel > 0) {
                    $imovel->setStatus(Status::VENDA_ALUGUEL);
                }

                self::$imobiliaria->getEstoque()->cadastrarImovel($imovel);
            }
        }
    }
}
