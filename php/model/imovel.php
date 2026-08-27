<?php


require_once __DIR__ . '/endereco.php';
require_once __DIR__ . '/corretor.php';
require_once __DIR__ . '/funcionario.php';
require_once __DIR__ . '/anuncio.php';
require_once __DIR__ . '/condominio.php';

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
    case CONSTRUCAO = "Em Construção";
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
    private int $id;
    private float $valorVenda;
    private float $valorAluguel;
    private int $quantQuartos;
    private int $quantSalas;
    private int $quantVagas;
    private int $quantBanheiros;
    private int $quantVarandas;
    private int $quantSuites;
    private ?Categoria $categoria;
    private ?Endereco $endereco;
    private ?Status $status;
    private float $iptu;
    private float $valorCondominio;
    private int $andar;
    private ?Estado $estado;
    private string $bloco;
    private int $anoConstrucao;
    private float $areaTotal;
    private float $areaPrivativa;
    private ?Situacao $situacao;
    private ?Ocupacao $ocupacao;
    private array $proprietarios;
    private ?Corretor $corretor;
    private ?Funcionario $captador;
    private ?DateTime $dataCadastro;
    private ?DateTime $dataModificacao;
    private ?Anuncio $anuncio;
    private ?Condominio $condominio;
    private array $filtros;
    private bool $destacado;
    private int $quantClicks;

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
        $this->quantSuites = 0;
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
        $this->quantClicks = 0;
        $this->destacado = false;
    }

    public function isDestacado(): bool
    {
        return $this->destacado;
    }

    public function setDestacado(bool $destacado)
    {
        $this->destacado = $destacado;
    }

    public function getQuantClicks(): int
    {
        return $this->quantClicks;
    }

    public function setQuantClicks(int $quantClicks)
    {
        $this->quantClicks = $quantClicks;
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

    public function setQuantSuites(int $value)
    {
        $this->quantSuites = $value;
    }

    public function getQuantSuites()
    {
        return $this->quantSuites;
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

    public function setCaptador(?Funcionario $value)
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

    public function __toString()
    {
        return "Imovel: { id: " . $this->id . ", valorVenda: " . $this->valorVenda . ", valorAluguel: " . $this->valorAluguel . ", quantQuartos: " . $this->quantQuartos . ", quantSalas: " . $this->quantSalas . ", quantVagas: " . $this->quantVagas . ", quantBanheiros: " . $this->quantBanheiros . ", quantVarandas: " . $this->quantVarandas . ", quantSuites: " . $this->quantSuites . ", categoria: " . ($this->categoria ? $this->categoria->value : 'null') . ", endereco: " . ($this->endereco ? $this->endereco->__toString() : 'null') . ", status: " . ($this->status ? $this->status->value : 'null') . ", iptu: " . $this->iptu . ", valorCondominio: " . $this->valorCondominio . ", andar: " . $this->andar . ", estado: " . ($this->estado ? $this->estado->value : 'null') . ", bloco: " . $this->bloco . ", anoConstrucao: " . $this->anoConstrucao . ", areaTotal: " . $this->areaTotal . ", areaPrivativa: " . $this->areaPrivativa . ", situacao: " . ($this->situacao ? $this->situacao->value : 'null') . ", ocupacao: " . ($this->ocupacao ? $this->ocupacao->value : 'null')  .
            ", proprietarios: [" .
            implode(", ", array_map(function ($proprietario) {
                return ($proprietario instanceof Funcionario) ? $proprietario->__toString() : 'null';
            }, $this->proprietarios)) .
            "]" .
            ", corretor: " . ($this->corretor ? $this->corretor->__toString() : 'null') .
            ", captador: " . ($this->captador ? $this->captador->__toString() : 'null') .
            ", dataCadastro: " . ($this->dataCadastro ? $this->dataCadastro->format('Y-m-d H:i:s') : 'null') .
            ", dataModificacao: " . ($this->dataModificacao ? $this->dataModificacao->format('Y-m-d H:i:s') : 'null') .
            ", anuncio: " . ($this->anuncio ? $this->anuncio->__toString() : 'null') .
            ", condominio: " . ($this->condominio ? $this->condominio->__toString() : 'null') .
            ", filtros: [" .
            implode(", ", array_map(function ($filtro) {
                return $filtro;
            }, $this->filtros)) .
            "]";
    }
}
