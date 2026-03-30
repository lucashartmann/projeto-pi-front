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


header('Content-Type: application/json');
Init::initialize();
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
            foreach ($anuncioObj->get_imagens() as $imagem) {
                // Supondo que $imagem já seja um blob binário
                $imagens[] = base64_encode($imagem);
            }
        }
        $anuncio = [
            "id" => $anuncioObj->get_id(),
            "descricao" => $anuncioObj->get_descricao(),
            "titulo" => $anuncioObj->get_titulo(),
            "imagens" => $imagens
        ];
    }

    $lista[] = [
        "id" => $imovel->get_id(),
        "valor_venda" => $imovel->get_valor_venda(),
        "valor_aluguel" => $imovel->get_valor_aluguel(),
        "categoria" => $imovel->get_categoria() ? $imovel->get_categoria()->value : null,
        "status" => $imovel->get_status() ? $imovel->get_status()->value : null,
        "endereco" => $endereco,
        "anuncio" => $anuncio
    ];
}

echo json_encode($lista);