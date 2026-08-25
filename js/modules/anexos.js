import { getCaminhoRelativo } from "./utils.js";


export async function buscarAnexoPorCaminho(caminhoRecebido) {
    try {
        let caminho = getCaminhoRelativo("/php/api/anexos.php?acao=buscar_por_caminho&caminho=" + caminhoRecebido);
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

export async function cadastrarAnexo(formData) {
    if (!formData || !(formData instanceof FormData)) {
        console.error("Arquivo inválido:", formData);
        return null;
    }

    try {
        const caminho = getCaminhoRelativo("/php/api/anexos.php?acao=cadastrar");
        console.log("Enviando dados do anexo para:", caminho);
        await fetch(caminho, {
            method: "POST",
            body: formData
        })
            .then(async (response) => {
                if (response.erro) {
                    alert("Erro ao cadastrar anexo: " + response.erro);
                    return null;
                }
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await response.json();
                } else {
                    const texto = await response.text();
                    alert("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return null;
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    alert("Erro ao cadastrar anexo: " + data.mensagem);
                    return;
                }
                else if (data.mensagem) {
                    alert("Anexo cadastrado com sucesso: " + data.mensagem);
                }

            })
            .catch(error => {
                alert("Erro ao cadastrar anexo:", error);
            });

    } catch (error) {
        console.error("Erro ao enviar dados do anexo:", error);
    }
}