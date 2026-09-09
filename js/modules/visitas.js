export async function listarVisitas() {
    try {
        let caminho = getCaminhoRelativo("/php/api/visitas.php?acao=listar_por_corretor");
        const resposta = await fetch(caminho)
            .then(async (res) => {
                const contentType = res.headers.get("content-type");
                if (res.erro) {
                    console.error("Erro ao listar visitas: " + res.erro);
                    return [];
                }
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    console.error("Resposta não é JSON:", texto);
                    return [];
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    console.error(data.mensagem);
                    return [];
                }
                return data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return [];
            });

        return resposta;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return [];
    }
}

export function cadastrarVisita(formData) {
    let div = document.querySelector(".mensagem");
    let mensagem = "";


    div = document.createElement("div");
    div.classList.add("mensagem");
    document.body.appendChild(div);
    try {
        let caminho = getCaminhoRelativo("/php/api/visitas.php?acao=cadastrar");
        fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(formData)
        })
            .then(async response => {
                const contentType = response.headers.get("content-type");
                if (response.erro) {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao cadastrar evento: " + response.erro;
                }
                if (contentType && contentType.includes("application/json")) {
                    return await response.json();
                } else {
                    const texto = await response.text();
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Resposta inesperada do servidor";
                    console.error("Resposta não é JSON:", texto);
                }
            })
            .then(async data => {
                if (data.status == "erro") {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao cadastrar evento: " + data.mensagem;
                }
                else if (data.mensagem) {
                    div.classList.add("sucesso");
                    div.classList.remove("erro");
                    mensagem = "Evento cadastrado com sucesso: " + data.mensagem;
                    adicionarEventoAoCalendario(dataRecebida);
                }

            })
            .catch(error => {
                div.classList.add("erro");
                div.classList.remove("sucesso");
                mensagem = "Erro ao cadastrar evento:", error;
            });

    } catch (error) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Erro ao enviar dados do imóvel:" + error;
    }
    div.innerText = mensagem;
    div.style.display = "flex";
}