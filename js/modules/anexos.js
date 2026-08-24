import { getCaminhoRelativo } from "./utils.js";


export async function buscarAnexoPorCaminho(caminho) {
    try {
        let caminho = getCaminhoRelativo("/php/api/anexos.php?acao=buscar_por_caminho&caminho=" + caminho);
        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (res.erro) {
                    console.error("Erro ao buscar anexo: " + res.erro);
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

        if (resposta && typeof resposta === "object") {
            return resposta;
        } else {
            console.error("Resposta inválida ao obter dados do anexo:", resposta);
            return null;
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

export async function cadastrarAnexo(data) {
    if (!data || JSON.stringify(formData).length < 1) {
        console.error("Dados inválidos para cadastrar anexo:", data);
        return null;
    }
    try {
        let caminho = getCaminhoRelativo("/php/api/anexos.php?acao=cadastrar");
        const resposta = await fetch(caminho, {
            method: "POST",
            body: JSON.stringify(data)
        })
            .then(async (res) => {
                if (res.erro) {
                    console.error("Erro ao cadastrar anexo: " + res.erro);
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
    } catch (error) {
        console.error("Erro ao enviar dados do usuário:", error);
    }



}