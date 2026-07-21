import { getCaminhoRelativo } from "./utils.js";

let notificacoes = [];


function marcarComoLida(id) {
}

export async function carregarNotificacoes() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=get_notificacoes");
        const resposta = await fetch(caminho)
            // .then(res => console.log(res))
            .then(async (res) => {
                if (res.erro) {
                    alert("Erro ao listar notificações: " + res.erro);
                    return [];
                }
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    // alert("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return [];
                }
            })
            .then(async (data) => {
                // console.log(data);
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });
        if (resposta ) {
            return resposta.notificacoes || [];
        } else {
            console.error("Resposta inválida ao obter dados da notificação:", resposta);
            return [];
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return [];
    }
}