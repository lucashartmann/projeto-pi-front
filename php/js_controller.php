<?php

require_once __DIR__ . '/model/cliente.php';
require_once __DIR__ . '/model/corretor.php';
require_once __DIR__ . '/model/imovel.php';
require_once __DIR__ . '/model/captador.php';
require_once __DIR__ . '/model/atendimento.php';
require_once __DIR__ . '/model/endereco.php';
require_once __DIR__ . '/model/anuncio.php';
require_once __DIR__ . '/model/venda_aluguel.php';
require_once __DIR__ . '/model/condominio.php';
require_once __DIR__ . '/model/gerente.php';
require_once __DIR__ . '/model/usuario.php';
require_once __DIR__ . '/model/proprietario.php';
require_once __DIR__ . '/model/__init__.php';
require_once __DIR__ . '/controller/controller.php';


header('Content-Type => application/json');
Init::initialize();

$acao = $_GET['acao'] ?? '';

switch ($acao) {

    case "cadastrar_imovel":
        cadastrar_imovel();
        break;

    case "listar_atendimentos":
        listar_atendimentos();
        break;

    case 'listar_imoveis':
        get_lista_imoveis();
        break;

    case 'listar_imoveis_disponiveis':
        get_lista_imoveis_disponiveis();
        break;

    case "login":
        $usuario = $_GET['usuario'] ?? null;
        $senha = $_GET['senha'] ?? null;
        if (!$usuario || !$senha) {
            echo json_encode(["erro" => "Usuário ou senha não fornecidos"]);
            return;
        }
        verificar_login($usuario, $senha);
        break;

    case "deslogar":
        deslogar();
        break;

    case "get_usuario":
        carregar_usuario();
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
    $imoveis = Init::$imobiliaria->get_estoque()->get_lista_imoveis();

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
}


function get_lista_imoveis_disponiveis()
{
    $imoveis = Init::$imobiliaria->get_estoque()->get_lista_imoveis_disponiveis();
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
}


function getImovelPorId($id)
{
    echo $id;
    // logging->info(f"Requisição para obter imóvel com ID => {id}")
    $imovel_obj = Init::$imobiliaria->get_imovel_por_id((int)$id);

    if ($imovel_obj) {
        $anuncioObj = $imovel_obj->get_anuncio();

        $imagens = [];
        if ($anuncioObj && is_array($anuncioObj->get_imagens())) {
            $imagens = array_map(function ($idImagem) {
                return "/projeto-pi-front/php/imagem.php?id=" . $idImagem;
            }, $anuncioObj->get_imagens());
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
            "anuncio" => $anuncioObj ? [
                "id" => $anuncioObj->get_id(),
                "descricao" => $anuncioObj->get_descricao(),
                "titulo" => $anuncioObj->get_titulo(),
                "imagens" => $imagens
            ] : null,
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

        $id =  in_array("ref", $data) ? $data["ref"] : null;
        $nome_condominio = in_array("nome_condominio", $data) ? $data["nome_condominio"] : null;
        $valor_venda = in_array("valor_venda", $data) ? (float)($data["valor_venda"] ?? 0) : null;
        $valor_aluguel = in_array("valor_aluguel", $data) ? (float)($data["valor_aluguel"] ?? 0) : null;
        $quant_quartos = in_array("quant_quartos", $data) ? (int)($data["quant_quartos"] ?? 0) : null;
        $quant_salas = in_array("quant_salas", $data) ? (int)($data["quant_salas"] ?? 0) : null;
        $quant_vagas = in_array("quant_vagas", $data) ? (int)($data["quant_vagas"] ?? 0) : null;
        $quant_banheiros = in_array("quant_banheiros", $data) ? (int)($data["quant_banheiros"] ?? 0) : null;
        $quant_varandas = in_array("quant_varandas", $data) ? (int)($data["quant_varandas"] ?? 0) : null;
        $categoria = in_array("categoria", $data) ? Categoria::tryFrom($data["categoria"]) : null;
        $status = in_array("status", $data) ? Status::tryFrom($data["status"]) : null;
        $iptu = in_array("iptu", $data) ? (float)($data["iptu"] ?? 0) : null;
        $valor_condominio = in_array("valor_condominio", $data) ? (float)($data["valor_condominio"] ?? 0) : null;
        $andar = in_array("andar", $data) ? (int)($data["andar"] ?? 0) : null;
        $estado = in_array("estado", $data) ? Estado::tryFrom($data["estado"]) : null;
        $bloco = in_array("bloco", $data) ? $data["bloco"] : null;
        $ano_construcao = in_array("ano_construcao", $data) ? (int)($data["ano_construcao"] ?? 0) : null;
        $area_total = in_array("area_total", $data) ? (float)($data["area_total"] ?? 0) : null;
        $area_privativa = in_array("area_privativa", $data) ? (float)($data["area_privativa"] ?? 0) : null;
        $situacao = in_array("situacao", $data) ? Situacao::tryFrom($data["situacao"]) : null;
        $ocupacao = in_array("ocupacao", $data) ? Ocupacao::tryFrom($data["ocupacao"]) : null;
        # proprietarios = data["proprietarios"]
        # corretor = data["corretor"]
        # captador = data["captador"]
        $cep = in_array("cep", $data) ? (int)($data["cep"] ?? null) : null;
        $rua = in_array("rua", $data) ? $data["rua"] : null;
        $bairro = in_array("bairro", $data) ? $data["bairro"] : null;
        $cidade = in_array("cidade", $data) ? $data["cidade"] : null;
        $titulo = in_array("titulo", $data) ? $data["titulo"] : null;
        $descricao = in_array("descricao", $data) ? $data["descricao"] : null;
        $complemento = in_array("complemento", $data) ? $data["complemento"] : null;
        $uf = in_array("uf", $data) ? $data["uf"] : null;
        $numero = in_array("numero", $data) ? (int)($data["numero"] ?? null) : null;
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
            $imovel_obj = Init::$imobiliaria->get_imovel_por_id($id);
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
        if ($id) {
            $imovel_obj->set_data_modificacao(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
            $controller->editar_imovel(
                $imovel_obj
            );
        } else {
            $controller->cadastrar_imovel(
                $imovel_obj
            );
        }

        echo json_encode(["status" => "ok"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro interno"]);
    }
}

function deslogar()
{
    Init::$usuario_atual = NULL;
    echo json_encode(["status" => "ok"]);
}




function carregar_usuario()
{


    if (Init::$usuario_atual) {
        echo (json_encode([
            "tipo" => Init::$usuario_atual->get_tipo()->value ? Init::$usuario_atual->get_tipo()->value : NULL,
        ]));
    }

    echo json_encode(["erro" => "Usuario nao encontrado"]);
}




function apagar_imovel($id)
{
    try {
        $imovel = Init::$imobiliaria->get_imovel_por_id($id);
        if ($imovel) {
            $remocao = Init::$imobiliaria->remover("id_imovel", $id, "imovel");
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


function verificar_login($usuario, $senha)
{


    try {
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["erro" => "JSON inválido"]);
            return;
        }


        $consulta = Init::$imobiliaria->verificar_usuario($usuario, $senha);

        if ($consulta) {
            Init::$usuario_atual = $consulta;
            echo (json_encode(["status" => "ok"]));
        } else {
            echo (json_encode(["status" => "erro"]));
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro interno"]);
    }
}



function listar_atendimentos()
{

    $atendimentos = Init::$imobiliaria->get_lista_atendimentos();
    $lista = [];
    if ($atendimentos) {
        foreach ($atendimentos as $atendimento) {
            $lista[] = [
                "id" => $atendimento->get_id(),
                "corretor" => $atendimento->get_corretor()->get_nome() ?: NULL,
                "cliente" =>  $atendimento->get_cliente() ? [
                    "id" => $atendimento->get_cliente()->get_id(),
                    "nome" => $atendimento->get_cliente()->get_nome(),
                    # "idade" => $atendimento->get_cliente()->get_idade(),
                    "telefones" => [$atendimento->get_cliente()->get_telefones()],
                    "email" => $atendimento->get_cliente()->get_email(),
                ] : NULL,
                "imovel" => $atendimento->get_imovel() ? [
                    "id" => $atendimento->get_imovel()->get_id(),
                    "titulo" => $atendimento->get_imovel()->get_anuncio()->get_titulo() ?: NULL,
                ] : NULL,
                "status" => $atendimento->get_status()->value ?: NULL,
            ];
        }
    }
    echo (json_encode($lista));
}
