import { getCaminhoRelativo } from "./utils.js";

export async function listarProprietarios() {
    try {
        let caminho = getCaminhoRelativo("/php/api/proprietarios.php?acao=listar");
        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (res.erro) {
                    alert("Erro ao listar proprietários: " + res.erro);
                    return null;
                }
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    alert("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return null;
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    alert("Erro ao listar proprietários: " + data.mensagem);
                    return null;
                }
                return data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });
        return resposta;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}
