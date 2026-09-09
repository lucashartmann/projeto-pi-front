import { getCaminhoRelativo } from "./utils.js";

export async function cadastrarImovel(formData) {
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

    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=cadastrar");
        await fetch(caminho, {
            method: "POST",
            body: formData
        })
            .then(async (response) => {
                if (response.erro) {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao cadastrar imóvel: " + response.erro;
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
                    console.error("Resposta não é JSON:", texto);
                    div.innerText = mensagem;
                    div.style.display = "flex";
                    return null;
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao cadastrar imóvel: " + data.mensagem;
                    div.innerText = mensagem;
                    div.style.display = "flex";
                    return;
                }
                else if (data.mensagem) {
                    div.classList.add("sucesso");
                    div.classList.remove("erro");
                    mensagem = "Imóvel cadastrado com sucesso: " + data.mensagem;
                }
            })
            .catch(error => {
                div.classList.add("erro");
                div.classList.remove("sucesso");
                mensagem = "Erro ao cadastrar imóvel: " + error;
            });

    } catch (error) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Erro ao enviar dados do imóvel: " + error;
    }

    div.innerText = mensagem;
    div.style.display = "flex";

}

export async function destacarImovel(imovelId) {
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


    if (!imovelId) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Nenhum imóvel selecionado para destaque!";
        return;
    }
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=destacar&id=" + imovelId);
        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (res.erro) {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao tornar imóvel destaque: " + res.erro;
                }
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Resposta inesperada do servidor";
                    console.error("Resposta não é JSON:", texto);
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao tornar imóvel destaque: " + data.mensagem;
                }
                div.classList.add("sucesso");
                div.classList.remove("erro");
                mensagem = "Imóvel destacado com sucesso: " + data.mensagem;
            })
            .catch(erro => {
                div.classList.add("erro");
                div.classList.remove("sucesso");
                mensagem = "Falha ao conectar com o backend:" + erro;
            });


    } catch (erro) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Falha ao conectar com o backend: " + erro;
    }

    div.innerText = mensagem;
    div.style.display = "flex";

}

export async function excluirImovel(imovelId) {
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

    if (!imovelId) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Nenhum imóvel selecionado para exclusão!";
    } else {
        try {
            let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=apagar&id=" + imovelId);
            const response = await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },

            })
                .then(async (response) => {
                    if (response.erro) {
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Erro ao remover imóvel: " + response.erro;
                    }
                    const contentType = response.headers.get("content-type");
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
                .then(async (data) => {
                    if (data.status == "erro") {
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Erro ao excluir imóvel: " + data.mensagem;
                    } else if (data.status == "sucesso") {
                        div.classList.add("sucesso");
                        div.classList.remove("erro");
                        mensagem = "Imóvel excluído com sucesso: " + data.mensagem;
                    } else {
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Erro ao excluir imóvel: " + data.mensagem;
                    }
                })
                .catch(error => {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao excluir imóvel:" + error;
                });
        } catch (error) {
            div.classList.add("erro");
            div.classList.remove("sucesso");
            mensagem = "Erro ao enviar dados para exclusão do imóvel:" + error;
        }
    }

    div.innerText = mensagem;
    div.style.display = "flex";

}

export async function listarImoveis() {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=listar");
        const resposta = await fetch(caminho)
            .then(async (res) => {
                const contentType = res.headers.get("content-type");
                if (res.erro) {
                    console.error("Erro ao listar imóveis: " + res.erro);
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

export async function listarImoveisDisponiveis() {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=listar_disponiveis");
        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (res.erro) {
                    console.error("Erro ao listar imóveis disponíveis: " + res.erro);
                    return [];
                }
                const contentType = res.headers.get("content-type");
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
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return [];
            });

        if (!resposta || !Array.isArray(resposta)) {
            console.error("Resposta inválida ao listar imóveis disponíveis:", resposta);
            return [];
        }

        resposta.forEach(imovel => {
            switch (imovel.status) {
                case "Venda":
                    imovel.valor_aluguel = null;
                    break;
                case "Aluguel":
                    imovel.valor_venda = null;
                    break;
                default:
                    break;
            }
        });

        let imoveis = [];
        imoveis = resposta.filter(imovel => imovel.anuncio && imovel.anuncio.imagens && imovel.anuncio.imagens.length > 0);
        imoveis = resposta.filter(imovel => imovel.valor_venda > 0 || imovel.valor_aluguel > 0);

        return imoveis;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

export async function listarImoveisDestacados() {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=listar_destacados");
        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (res.erro) {
                    console.error("Erro ao listar imoveis destacados: " + res.erro);
                    return [];
                }
                const contentType = res.headers.get("content-type");
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
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return [];
            });

        if (!resposta || !Array.isArray(resposta)) {
            console.error("Resposta inválida ao listar imóveis destacados:", resposta);
            return [];
        }

        resposta.forEach(imovel => {
            switch (imovel.status) {
                case "Venda":
                    imovel.valor_aluguel = null;
                    break;
                case "Aluguel":
                    imovel.valor_venda = null;
                    break;
                default:
                    break;
            }
        });

        let imoveis = [];
        imoveis = resposta.filter(imovel => imovel.anuncio && imovel.anuncio.imagens && imovel.anuncio.imagens.length > 0);

        return imoveis;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return [];
    }
}

export async function getDadosImovel(id) {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=get_imovel&id=" + id);
        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (res.erro) {
                    console.error("Erro ao buscar imóvel: " + res.erro);
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