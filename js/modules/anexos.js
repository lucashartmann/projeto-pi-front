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
                    console.error("Erro ao buscar anexo: " + data.mensagem);
                    return null;
                }
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend: " + erro);
                return null;
            });

        if (resposta && typeof resposta === "object") {
            return resposta;
        } else {
            console.error("Resposta inválida ao obter dados do anexo: " + resposta);
            return null;
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend: " + erro);
        return null;
    }
}

export async function cadastrarAnexo(formData) {
    let divPai = document.querySelector("#container-mensagens");
    let mensagem = "";
    let div = document.createElement("div");

    if (!divPai) {
        divPai = document.createElement("div");
        divPai.id = "container-mensagens";
        document.body.appendChild(divPai);
    }

    div.classList.add("mensagem");
    divPai.appendChild(div);


    if (!formData || !(formData instanceof FormData)) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Arquivo inválido: " + formData;
        div.innerText = mensagem;
        div.style.display = "flex";
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
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao cadastrar anexo: " + response.erro;
                    div.innerText = mensagem;
                    div.style.display = "flex";
                    return null;
                }
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await response.json();
                } else {
                    const texto = await response.text();
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Resposta inesperada do servidor";
                    div.innerText = mensagem;
                    div.style.display = "flex";
                    console.error("Resposta não é JSON:", texto);
                    return null;
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao cadastrar anexo: " + data.mensagem;
                    div.innerText = mensagem;
                    div.style.display = "flex";
                    return;
                }
                else if (data.mensagem) {
                    div.classList.add("sucesso");
                    div.classList.remove("erro");
                    mensagem = "Anexo cadastrado com sucesso: " + data.mensagem;
                    div.innerText = mensagem;
                    div.style.display = "flex";
                }

            })
            .catch(error => {
                div.classList.add("erro");
                div.classList.remove("sucesso");
                mensagem = "Erro ao cadastrar anexo: " + error;
                div.innerText = mensagem;
                div.style.display = "flex";
            });


    } catch (error) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Erro ao enviar dados do anexo: " + error;
        div.innerText = mensagem;
        div.style.display = "flex";
    }
    div.innerText = mensagem;
    div.style.display = "flex";
}