<?php


require_once __DIR__ . '/endereco.php';
require_once __DIR__ . '/corretor.php';
require_once __DIR__ . '/captador.php';
require_once __DIR__ . '/anuncio.php';
require_once __DIR__ . '/condominio.php';

$bairros = [
    "Aberta dos Morros",
    "Agronomia",
    "Anchieta",
    "Arquipélago",
    "Auxiliadora",
    "Azenha",
    "Bela Vista",
    "Belém Novo",
    "Belém Velho",
    "Boa Vista",
    "Bom Jesus",
    "Bom Fim",
    "Camaquã",
    "Cascata",
    "Cavalhada",
    "Centro Histórico",
    "Chácara das Pedras",
    "Cidade Baixa",
    "Coronado",
    "Cristal",
    "Cristo Redentor",
    "Espírito Santo",
    "Farrapos",
    "Floresta",
    "Glória",
    "Guarujá",
    "Higienópolis",
    "Hípica",
    "Humaitá",
    "Independência",
    "Ipanema",
    "Jardim Botânico",
    "Jardim Carvalho",
    "Jardim do Salso",
    "Jardim Europa",
    "Jardim Floresta",
    "Jardim Isabel",
    "Lagoa da Conceição",
    "Lami",
    "Lomba do Pinheiro",
    "Menino Deus",
    "Moinhos de Vento",
    "Mont'Serrat",
    "Navegantes",
    "Nonoai",
    "Passo da Areia",
    "Passo D'Areia",
    "Partenon",
    "Petrópolis",
    "Ponta Grossa",
    "Praia de Belas",
    "Restinga",
    "Rio Branco",
    "Rubem Berta",
    "Santa Cecília",
    "Santa Maria Goretti",
    "Santa Teresa",
    "Santana",
    "Santo Antônio",
    "Sarandi",
    "São Geraldo",
    "São João",
    "São José",
    "São Sebastião",
    "Serraria",
    "Terra Nova",
    "Três Figueiras",
    "Tristeza",
    "Vila Assunção",
    "Vila Conceição",
    "Vila Ipiranga",
    "Vila Jardim",
    "Vila João Pessoa",
    "Vila Nova",
    "Vila São José"
];


enum Categoria: string
{
    case SALA_COMERCIAL = "Sala Comercial";
    case APARTAMENTO = "Apartamento";
    case LOJA = "Loja";
    case CASA = "Casa";
    case COBERTURA = "Cobertura";
    case LOFT = "Loft";
    case STUDIO = "Studio";
    case DEPOSITO = "Depósito";
    case GALPAO = "Galpão";
    case PAVILHAO = "Pavilhão";
    case PREDIO_COMERCIAL = "Prédio Comercial";
    case PONTO_COMERCIAL = "Ponto Comercial";
    case EMPREENDIMENTO = "Empreendimento";
    case CASA_EM_CONDOMINIO = "Casa em Condomínio";
    case SOBRADO = "Sobrado";
    case SITIO = "Sítio";
    case TERRENO = "Terreno";
    case KITNET = "Kitnet";
    case CHACARA = "Chácara";
    case FAZENDA = "Fazenda";
}


enum Situacao: string
{
    case COSTRUCAO = "Em Construção";
    case NOVO = "Novo";
    case USADO = "Usado";
}

enum Ocupacao: string
{
    case DESOCUPADO = "Desocupado";
    case INQUILINO = "Inquilino";
    case PROPRIETARIO = "Proprietário";
}

enum Estado: string
{
    case BOM = "Bom";
    case OTIMO = "Ótimo";
    case REGULAR = "Regular";
}

enum Status: string
{
    case VENDA = "Venda";
    case ALUGUEL = "Aluguel";
    case VENDA_ALUGUEL = "Venda e Aluguel";
    case ALUGADO = "Alugado";
    case VENDIDO = "Vendido";
    case PENDENTE = "Pendente";
}

class Imovel
{
    public int $id;
    public float $valorVenda;
    public float $valorAluguel;
    public int $quantQuartos;
    public int $quantSalas;
    public int $quantVagas;
    public int $quantBanheiros;
    public int $quantVarandas;
    public ?Categoria $categoria;
    public ?Endereco $endereco;
    public ?Status $status;
    public float $iptu;
    public float $valorCondominio;
    public int $andar;
    public ?Estado $estado;
    public string $bloco;
    public int $anoConstrucao;
    public float $areaTotal;
    public float $areaPrivativa;
    public ?Situacao $situacao;
    public ?Ocupacao $ocupacao;
    public array $proprietarios;
    public ?Corretor $corretor;
    public ?Captador $captador;
    public ?DateTime $dataCadastro;
    public ?DateTime $dataModificacao;
    public ?Anuncio $anuncio;
    public ?Condominio $condominio;
    public array $filtros;
    public string $complemento;

    public int $quantClicks;

    public function __construct(?Endereco $endereco, Status $status, Categoria $categoria)
    {
        $this->id = 0;
        $this->valorVenda = 0;
        $this->valorAluguel = 0;
        $this->quantQuartos = 0;
        $this->quantSalas = 0;
        $this->quantVagas = 0;
        $this->quantBanheiros = 0;
        $this->quantVarandas = 0;
        $this->categoria = $categoria;
        $this->endereco = $endereco;
        $this->status = $status;
        $this->iptu = 0;
        $this->valorCondominio = 0;
        $this->andar = 0;
        $this->estado = NULL;
        $this->bloco = "";
        $this->anoConstrucao = 0;
        $this->areaTotal = 0;
        $this->areaPrivativa = 0;
        $this->situacao = NULL;
        $this->ocupacao = NULL;
        $this->proprietarios = [];
        $this->corretor = NULL;
        $this->captador = NULL;
        $this->dataCadastro = NULL;
        $this->dataModificacao = NULL;
        $this->anuncio = NULL;
        $this->condominio = NULL;
        $this->filtros = [];
        $this->complemento = "";
        $this->quantClicks = 0;
    }

    public function getComplemento()
    {
        return $this->complemento;
    }

    public function setComplemento(string $complemento)
    {
        $this->complemento = $complemento;
    }

    public function setFiltros(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function getFiltros()
    {
        return $this->filtros;
    }

    public function getCondominio()
    {
        return $this->condominio;
    }

    public function setCondominio(?Condominio $nome)
    {
        $this->condominio = $nome;
    }

    public function setDataCadastro(?DateTime $data)
    {
        $this->dataCadastro = $data;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    public function setDataModificacao(?DateTime $data)
    {
        $this->dataModificacao = $data;
    }

    public function getDataModificacao()
    {
        return $this->dataModificacao;
    }

    public function valor_venda()
    {
        return $this->valorVenda;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $value)
    {
        $this->id = $value;
    }

    public function getValorVenda()
    {
        return $this->valorVenda;
    }

    public function setValorVenda(float $value)
    {
        $this->valorVenda = $value;
    }

    public function getValorAluguel()
    {
        return $this->valorAluguel;
    }

    public function setValorAluguel(float $value)
    {
        $this->valorAluguel = $value;
    }

    public function getQuantQuartos()
    {
        return $this->quantQuartos;
    }

    public function setQuantQuartos(int $value)
    {
        $this->quantQuartos = $value;
    }

    public function getQuantSalas()
    {
        return $this->quantSalas;
    }

    public function setQuantSalas(int $value)
    {
        $this->quantSalas = $value;
    }

    public function getQuantVagas()
    {
        return $this->quantVagas;
    }

    public function setQuantVagas(int $value)
    {
        $this->quantVagas = $value;
    }

    public function getQuantBanheiros()
    {
        return $this->quantBanheiros;
    }

    public function setQuantBanheiros(int $value)
    {
        $this->quantBanheiros = $value;
    }

    public function getQuantVarandas()
    {
        return $this->quantVarandas;
    }

    public function setQuantVarandas(int $value)
    {
        $this->quantVarandas = $value;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $value)
    {
        $this->categoria = $value;
    }

    public function getEndereco()
    {
        return $this->endereco;
    }

    public function setEndereco(?Endereco $value)
    {
        $this->endereco = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(?Status $value)
    {
        $this->status = $value;
    }

    public function getIptu()
    {
        return $this->iptu;
    }

    public function setIptu(float $value)
    {
        $this->iptu = $value;
    }

    public function getValorCondominio()
    {
        return $this->valorCondominio;
    }

    public function setValorCondominio(float $value)
    {
        $this->valorCondominio = $value;
    }

    public function getAndar()
    {
        return $this->andar;
    }

    public function setAndar(int $value)
    {
        $this->andar = $value;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado(?Estado $value)
    {
        $this->estado = $value;
    }

    public function getBloco()
    {
        return $this->bloco;
    }

    public function setBloco(string $value)
    {
        $this->bloco = $value;
    }

    public function getAnoConstrucao()
    {
        return $this->anoConstrucao;
    }

    public function setAnoConstrucao(int $value)
    {
        $this->anoConstrucao = $value;
    }

    public function getAreaTotal()
    {
        return $this->areaTotal;
    }

    public function setAreaTotal(float $value)
    {
        $this->areaTotal = $value;
    }

    public function getAreaPrivativa()
    {
        return $this->areaPrivativa;
    }

    public function setAreaPrivativa(float $value)
    {
        $this->areaPrivativa = $value;
    }

    public function getSituacao()
    {
        return $this->situacao;
    }

    public function setSituacao(?Situacao $value)
    {
        $this->situacao = $value;
    }

    public function getOcupacao()
    {
        return $this->ocupacao;
    }

    public function setOcupacao(?Ocupacao $value)
    {
        $this->ocupacao = $value;
    }

    public function getProprietarios()
    {
        return $this->proprietarios;
    }

    public function setProprietarios(array $value)
    {
        $this->proprietarios = $value;
    }

    public function getCorretor()
    {
        return $this->corretor;
    }

    public function setCorretor(?Corretor $value)
    {
        $this->corretor = $value;
    }

    public function getCaptador()
    {
        return $this->captador;
    }

    public function setCaptador(?Captador $value)
    {
        $this->captador = $value;
    }

    public function setAnuncio(?Anuncio $value)
    {
        $this->anuncio = $value;
    }

    public function getAnuncio()
    {
        return $this->anuncio;
    }

    public function getQuantidadeSalas()
    {
        return $this->quantSalas;
    }

    public function getQuantidadeVagas()
    {
        return $this->quantVagas;
    }

    public function getQuantidadeVarandas()
    {
        return $this->quantVarandas;
    }

    public function getQuantidadeBanheiros()
    {
        return $this->quantBanheiros;
    }

    public function getQuantidadeQuartos()
    {
        return $this->quantQuartos;
    }
}
