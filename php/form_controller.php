<?php

require_once __DIR__ . '/controller/controller.php';
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

$controller = new controller();
Init::initialize();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';

    switch ($acao) {

        case 'cadastro_imovel':
            $endereco = new Endereco($_POST['cep'], $_POST['rua'], $_POST['numero'], $_POST['complemento'], $_POST['bloco'], $_POST['andar'], $_POST['bairro'], $_POST['cidade'], $_POST['uf']);
            $status = Status::tryFrom($_POST['status']);
            $categoria = Categoria::tryFrom($_POST['categoria']);
            $imovel = new Imovel($endereco, $status, $categoria);
            $imovel->set_situacao(Situacao::tryFrom($_POST['situacao']));
            $imovel->set_estado(Estado::tryFrom($_POST['estado_imovel']));
            $imovel->set_ocupacao(Ocupacao::tryFrom($_POST['ocupacao']));
            $condominio = new Condominio($_POST['nome_condominio'], $_POST['valor_condominio']);
            $imovel->set_condominio($condominio);
            $imovel->set_ano_construcao($_POST['ano_construcao']);
            $imovel->set_complemento($_POST['complemento']);
            $imovel->set_bloco($_POST['bloco']);
            $imovel->set_andar($_POST['andar']);
            $imovel->set_estado($_POST['uf']);
            $imovel->set_quant_salas($_POST['quantidade_salas']);
            $imovel->set_quant_banheiros($_POST['quantidade_banheiros']);
            $imovel->set_quant_vagas($_POST['quantidade_vagas']);
            $imovel->set_quant_varandas($_POST['quantidade_varandas']);
            $imovel->set_quant_quartos($_POST['quantidade_quartos']);
            $imovel->set_area_total($_POST['area_total']);
            $imovel->set_area_privativa($_POST['area_privativa']);
            $imovel->set_valor_venda($_POST['valor_venda']);
            $imovel->set_valor_aluguel($_POST['valor_aluguel']);
            $imovel->set_valor_condominio($_POST['valor_condominio']);
            $imovel->set_iptu($_POST['iptu']);
            $resultado = $controller->cadastrar_imovel($imovel);
            break;
        case 'editar_imovel':
            $endereco = new Endereco($_POST['cep'], $_POST['rua'], $_POST['numero'], $_POST['complemento'], $_POST['bloco'], $_POST['andar'], $_POST['bairro'], $_POST['cidade'], $_POST['uf']);
            $status = Status::tryFrom($_POST['status']);
            $categoria = Categoria::tryFrom($_POST['categoria']);
            $imovel = new Imovel($endereco, $status, $categoria);
            $imovel->set_situacao(Situacao::tryFrom($_POST['situacao']));
            $imovel->set_estado(Estado::tryFrom($_POST['estado_imovel']));
            $imovel->set_ocupacao(Ocupacao::tryFrom($_POST['ocupacao']));
            $condominio = new Condominio($_POST['nome_condominio'], $_POST['valor_condominio']);
            $imovel->set_condominio($condominio);
            $imovel->set_ano_construcao($_POST['ano_construcao']);
            $imovel->set_complemento($_POST['complemento']);
            $imovel->set_bloco($_POST['bloco']);
            $imovel->set_andar($_POST['andar']);
            $imovel->set_estado($_POST['uf']);
            $imovel->set_quant_salas($_POST['quantidade_salas']);
            $imovel->set_quant_banheiros($_POST['quantidade_banheiros']);
            $imovel->set_quant_vagas($_POST['quantidade_vagas']);
            $imovel->set_quant_varandas($_POST['quantidade_varandas']);
            $imovel->set_quant_quartos($_POST['quantidade_quartos']);
            $imovel->set_area_total($_POST['area_total']);
            $imovel->set_area_privativa($_POST['area_privativa']);
            $imovel->set_valor_venda($_POST['valor_venda']);
            $imovel->set_valor_aluguel($_POST['valor_aluguel']);
            $imovel->set_valor_condominio($_POST['valor_condominio']);
            $imovel->set_iptu($_POST['iptu']);
            $resultado = $controller->editar_imovel($imovel);
            break;
        case 'remover':
            $resultado = $controller->remover($_POST['campo'], $_POST['valor'], $_POST['tabela']);
            break;
        case 'cadastrar_atendimento':
            $resultado = $controller->cadastrar_atendimento($_POST);
            break;
        case 'editar_proprietario':
            $resultado = $controller->editar_proprietario($_POST);
            break;
        case 'cadastrar_proprietario':
            $resultado = $controller->cadastrar_proprietario($_POST);
            break;
        case 'editar_usuario':
            $resultado = $controller->editar_usuario($_POST);
            break;
        case 'cadastrar_usuario':
            $resultado = $controller->cadastrar_usuario($_POST);
            break;
        case 'verificar_login':
            $resultado = $controller->verificar_login($_POST['email'], $_POST['senha']);

            if ($resultado) {
                header("Location: ../html/cadastro-imovel.html");
                exit;
            } else {
                header("Location: ../html/login.html?erro=1");
                exit;
            }
            break;
        case 'salvar_login':
            $resultado = $controller->salvar_login($_POST['username'], $_POST['senha'], $_POST['email']);
            break;
    }

    // print_r($resultado);
}
