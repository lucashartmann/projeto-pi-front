
<?php

require_once __DIR__ . '/../database/banco.php';

class Estoque{
    public $banco_dados;

    public function __construct(){
        $this->banco_dados = new Banco();
    }

    public function cadastrar_anuncio($anuncio){
        return $this->banco_dados->cadastrar_anuncio($anuncio);
    }

    public function cadastrar_imovel($imovel){
        return $this->banco_dados->cadastrar_imovel($imovel);
    }
    public function atualizar_imovel($imovel){
        return $this->banco_dados->atualizar_imovel($imovel);
    }
    public function get_lista_imoveis(){
        return $this->banco_dados->get_lista_imoveis();
    }
    public function get_lista_imoveis_disponiveis(){
        return $this->banco_dados->get_lista_imoveis_disponiveis();
    }
    // public function get_imoveis_por_categoria($categoria){
    //     return $this->banco_dados->get_imoveis_por_categoria($categoria);
    // }

        //     public function adicionar_anexo($anexo, $tipo, $codigo){
    //         return $this->banco_dados->adicionar_anexo($anexo, $tipo, $codigo);

    //     }        
    // }


    public function get_imovel_por_id($id){
        return $this->banco_dados->get_imovel_por_id($id);
    }
}