<?php


require_once __DIR__ . '/endereco.php';

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
    case COSTRUCAO = "Em Costrução";
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
    case VENDA_ALUGUEL = "Venda_Aluguel";
    case ALUGADO = "Alugado";
    case VENDIDO = "Vendido";
    case PENDENTE = "Pendente";
}

class Imovel
{
    public $id;
    public $valor_venda;
    public $valor_aluguel;
    public $quant_quartos;
    public $quant_salas;
    public $quant_vagas;
    public $quant_banheiros;
    public $quant_varandas;
    public $categoria;
    public $endereco;
    public $status;
    public $iptu;
    public $valor_condominio;
    public $andar;
    public $estado;
    public $bloco;
    public $ano_construcao;
    public $area_total;
    public $area_privativa;
    public $situacao;
    public $ocupacao;
    public $proprietarios;
    public $corretor;
    public $captador;
    public $data_cadastro;
    public $data_modificacao;
    public $anuncio;
    public $condominio;
    public $filtros;
    public $complemento;

    public function __construct($endereco, $status, $categoria)
    {
        $this->id = NULL;
        $this->valor_venda = 0;
        $this->valor_aluguel = 0;
        $this->quant_quartos = 0;
        $this->quant_salas = 0;
        $this->quant_vagas = 0;
        $this->quant_banheiros = 0;
        $this->quant_varandas = 0;
        $this->categoria = $categoria;
        $this->endereco = $endereco;
        $this->status = $status;
        $this->iptu = 0;
        $this->valor_condominio = 0;
        $this->andar = 0;
        $this->estado = NULL;
        $this->bloco = NULL;
        $this->ano_construcao = NULL;
        $this->area_total = 0;
        $this->area_privativa = 0;
        $this->situacao = NULL;
        $this->ocupacao = NULL;
        $this->proprietarios = [];
        $this->corretor = NULL;
        $this->captador = NULL;
        $this->data_cadastro = NULL;
        $this->data_modificacao = NULL;
        $this->anuncio = NULL;
        $this->condominio = NULL;
        $this->filtros = [];
        $this->complemento = NULL;
    }

    public function get_complemento()
    {
        return $this->complemento;
    }

    public function set_complemento($complemento)
    {
        $this->complemento = $complemento;
    }

    public function set_filtros($filtros)
    {
        $this->filtros = $filtros;
    }

    public function get_filtros()
    {
        return $this->filtros;
    }

    public function get_condominio()
    {
        return $this->condominio;
    }

    public function set_condominio($nome)
    {
        $this->condominio = $nome;
    }

    public function set_data_cadastro($data)
    {
        $this->data_cadastro = $data;
    }

    public function get_data_cadastro()
    {
        return $this->data_cadastro;
    }

    public function set_data_modificacao($data)
    {
        $this->data_modificacao = $data;
    }

    public function get_data_modificacao()
    {
        return $this->data_modificacao;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_id($value)
    {
        $this->id = $value;
    }

    public function get_valor_venda()
    {
        return $this->valor_venda;
    }

    public function set_valor_venda($value)
    {
        $this->valor_venda = $value;
    }

    public function get_valor_aluguel()
    {
        return $this->valor_aluguel;
    }

    public function set_valor_aluguel($value)
    {
        $this->valor_aluguel = $value;
    }

    public function get_quant_quartos()
    {
        return $this->quant_quartos;
    }

    public function set_quant_quartos($value)
    {
        $this->quant_quartos = $value;
    }

    public function get_quant_salas()
    {
        return $this->quant_salas;
    }

    public function set_quant_salas($value)
    {
        $this->quant_salas = $value;
    }

    public function get_quant_vagas()
    {
        return $this->quant_vagas;
    }

    public function set_quant_vagas($value)
    {
        $this->quant_vagas = $value;
    }

    public function get_quant_banheiros()
    {
        return $this->quant_banheiros;
    }

    public function set_quant_banheiros($value)
    {
        $this->quant_banheiros = $value;
    }

    public function get_quant_varandas()
    {
        return $this->quant_varandas;
    }

    public function set_quant_varandas($value)
    {
        $this->quant_varandas = $value;
    }

    public function get_categoria()
    {
        return $this->categoria;
    }

    public function set_categoria($value)
    {
        $this->categoria = $value;
    }

    public function get_endereco()
    {
        return $this->endereco;
    }

    public function set_endereco($value)
    {
        $this->endereco = $value;
    }

    public function get_status()
    {
        return $this->status;
    }

    public function set_status($value)
    {
        $this->status = $value;
    }

    public function get_iptu()
    {
        return $this->iptu;
    }

    public function set_iptu($value)
    {
        $this->iptu = $value;
    }

    public function get_valor_condominio()
    {
        return $this->valor_condominio;
    }

    public function set_valor_condominio($value)
    {
        $this->valor_condominio = $value;
    }

    public function get_andar()
    {
        return $this->andar;
    }

    public function set_andar($value)
    {
        $this->andar = $value;
    }

    public function get_estado()
    {
        return $this->estado;
    }

    public function set_estado($value)
    {
        $this->estado = $value;
    }

    public function get_bloco()
    {
        return $this->bloco;
    }

    public function set_bloco($value)
    {
        $this->bloco = $value;
    }

    public function get_ano_construcao()
    {
        return $this->ano_construcao;
    }

    public function set_ano_construcao($value)
    {
        $this->ano_construcao = $value;
    }

    public function get_area_total()
    {
        return $this->area_total;
    }

    public function set_area_total($value)
    {
        $this->area_total = $value;
    }

    public function get_area_privativa()
    {
        return $this->area_privativa;
    }

    public function set_area_privativa($value)
    {
        $this->area_privativa = $value;
    }

    public function get_situacao()
    {
        return $this->situacao;
    }

    public function set_situacao($value)
    {
        $this->situacao = $value;
    }

    public function get_ocupacao()
    {
        return $this->ocupacao;
    }

    public function set_ocupacao($value)
    {
        $this->ocupacao = $value;
    }

    public function get_proprietarios()
    {
        return $this->proprietarios;
    }

    public function set_proprietarios($value)
    {
        $this->proprietarios = $value;
    }

    public function get_corretor()
    {
        return $this->corretor;
    }

    public function set_corretor($value)
    {
        $this->corretor = $value;
    }

    public function get_captador()
    {
        return $this->captador;
    }

    public function set_captador($value)
    {
        $this->captador = $value;
    }

    public function set_anuncio($value)
    {
        $this->anuncio = $value;
    }

    public function get_anuncio()
    {
        return $this->anuncio;
    }
}
