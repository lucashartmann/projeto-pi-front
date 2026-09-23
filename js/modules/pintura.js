import { getCaminhoRelativo } from "./utils.js";

export async function pintar(caminhoRecebido, cor) {
    try {
        let caminho = getCaminhoRelativo("/php/api/pinturas.php?acao=pintar");
        const resposta = await fetch(caminho, {
            method: "POST",
            body: JSON.stringify({ "caminho": caminhoRecebido, "cor": cor })
        })
            .then(async (res) => {
                if (res.erro) {
                    console.error("Erro ao pintar imagem: " + res.erro);
                    return null;
                }
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    console.error("Resposta não é JSON:", texto);
                    return null;
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    console.error(data.mensagem);
                    return null;
                }
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });
        if (resposta && Array.isArray(resposta) && resposta.length > 0) {
            return resposta[0];
        } else if (resposta && typeof resposta === "object") {
            return resposta;
        } else {
            console.error("Resposta inválida ao obter dados do imóvel:", resposta);
            return null;
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}