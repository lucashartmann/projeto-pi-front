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
require_once __DIR__ . '/../model/__init__.php';
require_once __DIR__ . '/../controller/controller.php';

ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';

switch ($acao) {

    case "cadastrar_imovel":
        cadastrar_imovel();
        break;

    case 'listar_imoveis':
        get_lista_imoveis();
        break;

    case 'listar_imoveis_disponiveis':
        get_lista_imoveis_disponiveis();
        break;

    case "get_dados_imovel":
        $id = $_GET['id'] ?? null;
        if ($id) {
            getImovelPorId($id);
        } else {
            echo json_encode(["erro" => "ID do imóvel não fornecido"]);
        }
        break;

    case "apagar_imovel":
        $id = $_GET['id'] ?? null;
        if ($id) {
            apagar_imovel($id);
        } else {
            echo json_encode(["erro" => "ID do imóvel não fornecido"]);
        }
        break;


    default:
        echo json_encode(["erro" => "Ação inválida"]);
        break;
}


function get_lista_imoveis()
{
    try {
        $imoveis = Init::getInstance()->get_estoque()->get_lista_imoveis();

        $lista = [];
        foreach ($imoveis as $imovel) {
            $endereco = null;
            if ($imovel->get_endereco()) {
                $enderecoObj = $imovel->get_endereco();
                $endereco = [
                    "rua" => $enderecoObj->rua ?? null,
                    "numero" => $enderecoObj->numero ?? null,
                    "bairro" => $enderecoObj->bairro ?? null,
                    "cidade" => $enderecoObj->cidade ?? null,
                    "uf" => $enderecoObj->uf ?? null,
                    "cep" => $enderecoObj->cep ?? null,
                    "complemento" => $enderecoObj->complemento ?? null,
                ];
            }

            $anuncio = null;
            if ($imovel->get_anuncio()) {
                $anuncioObj = $imovel->get_anuncio();
                $imagens = [];
                if ($anuncioObj->get_imagens()) {
                    foreach ($anuncioObj->get_imagens() as $idImagem) {
                        $imagens[] = "/projeto-pi-front/php/imagem.php?id=" . $idImagem;
                    }
                }
                $anuncio = [
                    "id" => $anuncioObj->get_id(),
                    "descricao" => $anuncioObj->get_descricao(),
                    "titulo" => $anuncioObj->get_titulo(),
                    "imagens" => $imagens
                ];
            }

            $categoria = $imovel->get_categoria();
            if (is_object($categoria) && isset($categoria->value)) {
                $categoria = $categoria->value;
            }

            $status = $imovel->get_status();
            if (is_object($status) && isset($status->value)) {
                $status = $status->value;
            }

            $lista[] = [
                "id" => $imovel->get_id(),
                "valor_venda" => $imovel->get_valor_venda(),
                "valor_aluguel" => $imovel->get_valor_aluguel(),
                "categoria" => $categoria,
                "status" => $status,
                "endereco" => $endereco,
                "anuncio" => $anuncio
            ];
        }

        echo json_encode($lista);
    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao listar imóveis: " . $e->getMessage()]);
    }
}


function get_lista_imoveis_disponiveis()
{
    try {
    $imoveis = Init::getInstance()->get_estoque()->get_lista_imoveis_disponiveis();
    // echo $imoveis;
    $lista = [];
    foreach ($imoveis as $imovel) {
        $endereco = null;
        if ($imovel->get_endereco()) {
            $enderecoObj = $imovel->get_endereco();
            $endereco = [
                "rua" => $enderecoObj->rua ?? null,
                "numero" => $enderecoObj->numero ?? null,
                "bairro" => $enderecoObj->bairro ?? null,
                "cidade" => $enderecoObj->cidade ?? null,
                "uf" => $enderecoObj->uf ?? null,
                "cep" => $enderecoObj->cep ?? null,
                "complemento" => $enderecoObj->complemento ?? null,
            ];
        }

        $anuncio = null;
        if ($imovel->get_anuncio()) {
            $anuncioObj = $imovel->get_anuncio();
            $imagens = [];
            if ($anuncioObj->get_imagens()) {
                foreach ($anuncioObj->get_imagens() as $idImagem) {
                    $imagens[] = "/projeto-pi-front/php/imagem.php?id=" . $idImagem;
                }
            }
            $anuncio = [
                "id" => $anuncioObj->get_id(),
                "descricao" => $anuncioObj->get_descricao(),
                "titulo" => $anuncioObj->get_titulo(),
                "imagens" => $imagens
            ];
        }

        $categoria = $imovel->get_categoria();
        if (is_object($categoria) && isset($categoria->value)) {
            $categoria = $categoria->value;
        }

        $status = $imovel->get_status();
        if (is_object($status) && isset($status->value)) {
            $status = $status->value;
        }

        $lista[] = [
            "id" => $imovel->get_id(),
            "valor_venda" => $imovel->get_valor_venda(),
            "valor_aluguel" => $imovel->get_valor_aluguel(),
            "categoria" => $categoria,
            "status" => $status,
            "endereco" => $endereco,
            "anuncio" => $anuncio
        ];
    }

    echo json_encode($lista);
    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao listar imóveis disponíveis: " . $e->getMessage()]);
    }
}


function getImovelPorId($id)
{
    try {
    // echo $id;
    // logging->info(f"Requisição para obter imóvel com ID => {id}")
    $imovel_obj = Init::getInstance()->get_imovel_por_id((int)$id);

    if ($imovel_obj) {
        $anuncio = null;
        if ($imovel_obj->get_anuncio()) {
            $anuncioObj = $imovel_obj->get_anuncio();
            $imagens = [];
            if ($anuncioObj->get_imagens()) {
                foreach ($anuncioObj->get_imagens() as $idImagem) {
                    $imagens[] = "/projeto-pi-front/php/imagem.php?id=" . $idImagem;
                }
            }
            $anuncio = [
                "id" => $anuncioObj->get_id(),
                "descricao" => $anuncioObj->get_descricao(),
                "titulo" => $anuncioObj->get_titulo(),
                "imagens" => $imagens
            ];
        }
        $resposta = [
            "id" => $imovel_obj->get_id(),
            "valor_venda" => $imovel_obj->get_valor_venda(),
            "valor_condominio" => $imovel_obj->get_valor_condominio(),
            "valor_iptu" => $imovel_obj->get_iptu(),
            "valor_aluguel" => $imovel_obj->get_valor_aluguel(),
            "categoria" => $imovel_obj->get_categoria()->value ?? null,
            "status" => $imovel_obj->get_status()->value ?? null,
            "endereco" => $imovel_obj->get_endereco() ? [
                "rua" => $imovel_obj->get_endereco()->rua ?? null,
                "numero" => $imovel_obj->get_endereco()->numero ?? null,
                "bairro" => $imovel_obj->get_endereco()->bairro ?? null,
                "cidade" => $imovel_obj->get_endereco()->cidade ?? null,
                "uf" => $imovel_obj->get_endereco()->uf ?? null,
                "cep" => $imovel_obj->get_endereco()->cep ?? null,
                "complemento" => $imovel_obj->get_endereco()->complemento ?? null
            ] : null,
            "anuncio" => $anuncio,
            "quantidade_quartos" => $imovel_obj->get_quant_quartos(),
            "quant_salas" => $imovel_obj->get_quant_salas(),
            "quant_vagas" => $imovel_obj->get_quant_vagas(),
            "quant_banheiros" => $imovel_obj->get_quant_banheiros(),
            "quant_varandas" => $imovel_obj->get_quant_varandas(),
            "andar" => $imovel_obj->get_andar(),
            "estado" => $imovel_obj->get_estado()->value ?? null,
            "bloco" => $imovel_obj->get_bloco(),
            "ano_construcao" => $imovel_obj->get_ano_construcao(),
            "area_total" => $imovel_obj->get_area_total(),
            "area_privativa" => $imovel_obj->get_area_privativa(),
            "situacao" => $imovel_obj->get_situacao()->value ?? null,
            "ocupacao" => $imovel_obj->get_ocupacao()->value ?? null,
            "proprietarios" => $imovel_obj->get_proprietarios() ? array_map(function ($proprietario) {
                return [
                    "id" => $proprietario->get_id(),
                    "email" => $proprietario->get_email(),
                    "nome" => $proprietario->get_nome(),
                    "cpf_cnpj" => $proprietario->get_cpf_cnpj(),
                    "rg" => $proprietario->get_rg(),
                    "telefones" => [$proprietario->get_telefones()],
                    "endereco" => $proprietario->get_endereco(),
                    "data_nascimento" => $proprietario->get_data_nascimento(),
                    "imoveis" => $proprietario->get_imoveis(),
                    "data_cadastro" => $proprietario->get_data_cadastro(),
                    "data_modificacao" => $proprietario->get_data_modificacao()
                ];
            }, $imovel_obj->get_proprietarios()) : [],
            "corretor" => $imovel_obj->get_corretor() ? ["username" => $imovel_obj->get_corretor()->get_username(), "senha" => $imovel_obj->get_corretor()->get_senha(), "email" => $imovel_obj->get_corretor()->get_email(), "nome" => $imovel_obj->get_corretor()->get_nome(), "cpf_cnpj" => $imovel_obj->get_corretor()->get_cpf_cnpj(), "tipo" => $imovel_obj->get_corretor()->get_tipo()] : null,
            "captador" => $imovel_obj->get_captador() ? ["username" => $imovel_obj->get_captador()->get_username(), "senha" => $imovel_obj->get_captador()->get_senha(), "email" => $imovel_obj->get_captador()->get_email(), "nome" => $imovel_obj->get_captador()->get_nome(), "cpf_cnpj" => $imovel_obj->get_captador()->get_cpf_cnpj(), "tipo" => $imovel_obj->get_captador()->get_tipo()] : null,
            "data_cadastro" => $imovel_obj->get_data_cadastro(),
            "data_modificacao" => $imovel_obj->get_data_modificacao(),
            "condominio" => $imovel_obj->get_condominio() ? ["id" => $imovel_obj->get_condominio()->get_id(), "nome" => $imovel_obj->get_condominio()->get_nome(), "filtros" => [$imovel_obj->get_condominio()->get_filtros()]] : null,
            "filtros" => [$imovel_obj->get_filtros()],
            "complemento" => $imovel_obj->get_anuncio() ? $imovel_obj->get_complemento() : null
        ];

        echo json_encode($resposta);
        return;
    } else {
        echo json_encode(["erro" => "Imovel nao encontrado"]);
        return;
    }
    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao obter imóvel: " . $e->getMessage()]);
    }
}

function cadastrar_imovel()
{

    try {
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["erro" => "JSON inválido"]);
            return;
        }

        $id =  array_key_exists("ref", $data) ? $data["ref"] : null;
        $nome_condominio = array_key_exists("nome_condominio", $data) ? $data["nome_condominio"] : null;
        $valor_venda = array_key_exists("valor_venda", $data) ? (float)($data["valor_venda"] ?? 0) : null;
        $valor_aluguel = array_key_exists("valor_aluguel", $data) ? (float)($data["valor_aluguel"] ?? 0) : null;
        $quant_quartos = array_key_exists("quant_quartos", $data) ? (int)($data["quant_quartos"] ?? 0) : null;
        $quant_salas = array_key_exists("quant_salas", $data) ? (int)($data["quant_salas"] ?? 0) : null;
        $quant_vagas = array_key_exists("quant_vagas", $data) ? (int)($data["quant_vagas"] ?? 0) : null;
        $quant_banheiros = array_key_exists("quant_banheiros", $data) ? (int)($data["quant_banheiros"] ?? 0) : null;
        $quant_varandas = array_key_exists("quant_varandas", $data) ? (int)($data["quant_varandas"] ?? 0) : null;
        $categoria = null;
        if (isset($data["categoria"])) {
            $valor = ucfirst(strtolower($data["categoria"]));
            $categoria = Categoria::tryFrom($valor);
        }
        $status = null;
        isset($data["status"]) ? $status = Status::tryFrom(ucfirst(strtolower($data["status"]))) : null;
        $iptu = array_key_exists("iptu", $data) ? (float)($data["iptu"] ?? 0) : null;
        $valor_condominio = array_key_exists("valor_condominio", $data) ? (float)($data["valor_condominio"] ?? 0) : null;
        $andar = array_key_exists("andar", $data) ? (int)($data["andar"] ?? 0) : null;
        $estado = null;
        isset($data["estado"]) ? $estado = Estado::tryFrom(ucfirst(strtolower($data["estado"]))) : null;
        $bloco = array_key_exists("bloco", $data) ? $data["bloco"] : null;
        $ano_construcao = array_key_exists("ano_construcao", $data) ? (int)($data["ano_construcao"] ?? 0) : null;
        $area_total = array_key_exists("area_total", $data) ? (float)($data["area_total"] ?? 0) : null;
        $area_privativa = array_key_exists("area_privativa", $data) ? (float)($data["area_privativa"] ?? 0) : null;
        $situacao = null;
        isset($data["situacao"]) ? $situacao = Situacao::tryFrom(ucfirst(strtolower($data["situacao"]))) : null;
        $ocupacao = null;
        isset($data["ocupacao"]) ? $ocupacao = Ocupacao::tryFrom(ucfirst(strtolower($data["ocupacao"]))) : null;
        # proprietarios = data["proprietarios"]
        # corretor = data["corretor"]
        # captador = data["captador"]
        $cep = array_key_exists("cep", $data) ? (int)($data["cep"] ?? null) : null;
        if ($cep) {
            $cep = str_replace("-", "", $cep);
        }
        $rua = array_key_exists("rua", $data) ? $data["rua"] : null;
        $bairro = array_key_exists("bairro", $data) ? $data["bairro"] : null;
        $cidade = array_key_exists("cidade", $data) ? $data["cidade"] : null;
        $titulo = array_key_exists("titulo", $data) ? $data["titulo"] : null;
        $descricao = array_key_exists("descricao", $data) ? $data["descricao"] : null;
        $complemento = array_key_exists("complemento", $data) ? $data["complemento"] : null;
        $uf = array_key_exists("uf", $data) ? $data["uf"] : null;
        $numero = array_key_exists("numero", $data) ? (int)($data["numero"] ?? null) : null;
        $anuncio_obj = new Anuncio();
        $anuncio_obj->set_titulo($titulo);
        $anuncio_obj->set_descricao($descricao);
        $endereco_obj = new Endereco($rua, $bairro, $cep, $cidade, $estado);
        $endereco_obj->set_numero($numero);
        $endereco_obj->set_complemento($complemento);
        $endereco_obj->set_uf($uf);
        $condominio_obj = new Condominio(
            $nome_condominio,
            $endereco_obj
        );
        # imagens = anuncio->get("imagens", [])
        # imagens_bytes = []
        # for imagem in imagens =>
        #     try =>
        #         imagem_bytes = base64->b64decode(imagem)
        #         imagens_bytes->append(imagem_bytes)
        #     catch (base64->binascii->Error, ValueError) =>
        #         continue
        # anuncio_obj->set_imagens(imagens_bytes)
        # condominio = data->get("condominio")
        # filtros = data->get("filtros", [])

        $imovel_obj = NULL;
        if ($id) {
            $imovel_obj = Init::getInstance()->get_imovel_por_id($id);
        } else {
            $imovel_obj = new Imovel($endereco_obj, $status, $categoria);
        }

        $imovel_obj->set_id($id);
        $imovel_obj->set_valor_venda($valor_venda);
        $imovel_obj->set_valor_aluguel($valor_aluguel);
        $imovel_obj->set_quant_quartos($quant_quartos);
        $imovel_obj->set_quant_salas($quant_salas);
        $imovel_obj->set_quant_vagas($quant_vagas);
        $imovel_obj->set_quant_banheiros($quant_banheiros);
        $imovel_obj->set_quant_varandas($quant_varandas);
        $imovel_obj->set_categoria($categoria);
        $imovel_obj->set_endereco($endereco_obj);
        $imovel_obj->set_status($status);
        $imovel_obj->set_iptu($iptu);
        $imovel_obj->set_valor_condominio($valor_condominio);
        $imovel_obj->set_andar($andar);
        $imovel_obj->set_estado($estado);
        $imovel_obj->set_bloco($bloco);
        $imovel_obj->set_ano_construcao($ano_construcao);
        $imovel_obj->set_area_total($area_total);
        $imovel_obj->set_area_privativa($area_privativa);
        $imovel_obj->set_situacao($situacao);
        $imovel_obj->set_ocupacao($ocupacao);
        # imovel_obj->set_corretor(corretor)
        # imovel_obj->set_captador(captador)
        $imovel_obj->set_anuncio($anuncio_obj);
        $imovel_obj->set_condominio($condominio_obj);

        $controller = new controller();
        $mensagem = "";
        if ($id) {
            $imovel_obj->set_data_modificacao(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
            $mensagem = $controller->editar_imovel(
                $imovel_obj
            );
        } else {
            $mensagem = $controller->cadastrar_imovel(
                $imovel_obj
            );
        }

        if (str_starts_with($mensagem, "ERRO") || str_starts_with($mensagem, "erro")) {
            echo json_encode(["erro" => $mensagem]);
            return;
        } else {

            echo json_encode(["mensagem" => $mensagem]);
            return;
        }
    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro interno"]);
    }
}

function apagar_imovel($id)
{
    try {
        $imovel = Init::getInstance()->get_imovel_por_id($id);
        if ($imovel) {
            $remocao = Init::getInstance()->remover("id_imovel", $id, "imovel");
            if ($remocao) {
                echo json_encode(["status" => "ok"]);
            } else {
                echo json_encode(["erro" => "Erro ao remover imóvel"]);
            }
        } else {
            echo json_encode(["erro" => "Imóvel não encontrado"]);
        }
    } catch (Exception $e) {
        echo (json_encode(["erro" => $e->getMessage()]));
    }
}
