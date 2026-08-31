import { getCaminhoRelativo } from "./utils.js";

export let usuarioLogado = null;
export let imoveisCurtidos = [];
export let logado = false;

export async function listarImoveisFavoritados() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=get_favoritos");

        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (!res.ok) {
                    throw new Error(`Erro na requisição: ${res.status}`);
                    return null;
                }

                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    throw new Error("Resposta não é JSON");
                    return null;
                }

            })
            .then(async (data) => {
                if (data.status == "erro") {
                    console.log("Erro ao listar imóveis: " + data.mensagem);
                    return null;
                }
                return await data;
            })
            .catch((error) => {
                console.log("Erro ao listar imóveis favoritados:", error);
                return null;
            });

        if (!resposta) {
            console.log("Falha ao obter imóveis favoritados");
            return null;
        }

        resposta?.forEach(imovel => {
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

        // imoveisCurtidos = resposta?.map(f => f.id) || [];
        return resposta;
    } catch (erro) {
        console.log("Falha ao conectar com o backend:", erro);
        return null;
    }

}

export async function curtirImovel(event, imovelId) {
    let div = document.querySelector(".mensagem");
    let mensagem = "";
    if (!div) {
        div = document.createElement("div");
        div.classList.add("mensagem");
        document.body.appendChild(div);
    }

    if (!logado) {
        if (!usuarioLogado) {
            div.classList.add("erro");
            div.classList.remove("sucesso");
            mensagem = "Você precisa estar logado para curtir um imóvel!";
        }
        else {
            logado = true;
        }
    }
    if (logado) {
        if (imoveisCurtidos.includes(imovelId)) {
            imoveisCurtidos.splice(imoveisCurtidos.indexOf(imovelId), 1);
            console.log("Imóvel removido dos curtidos:", imoveisCurtidos);
            event.target.classList.remove("curtido");
            return;
        }
        imoveisCurtidos.push(imovelId);
        console.log("Imóvel adicionado aos curtidos:", imoveisCurtidos);
        event.target.classList.toggle("curtido");
        event.stopPropagation();

        try {
            let caminho = getCaminhoRelativo("/php/api/login.php?acao=favoritar_imoveis");
            const resposta = await fetch(caminho, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id_imoveis: imovelId })
            })
                .then(async (response) => {
                    if (response.erro) {
                        mensagem = "Erro ao cadastrar favoritos: " + response.erro;
                    }
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return await response.json();
                    } else {
                        const texto = await response.text();
                        mensagem = "Resposta inesperada do servidor";
                        console.error("Resposta não é JSON:", texto);
                    }
                })
                .then(data => {
                    let div = document.querySelector(".mensagem");

                    if (!div) {
                        div = document.createElement("div");
                        div.classList.add("mensagem");
                        document.body.appendChild(div);
                    }

                    if (data.status === "sucesso") {
                        carregarUser();
                        mensagem = "Imóveis curtidos salvos com sucesso: " + data.mensagem;
                        div.classList.add("sucesso");
                        div.classList.remove("erro");
                    } else {
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Erro ao salvar imóveis curtidos: " + data.mensagem;
                    }

                })
                .catch(err => {
                    mensagem = "Erro na requisição para salvar imóveis curtidos: " + err;
                });
        } catch (err) {
            mensagem = "Erro ao salvar imóveis curtidos: " + err;
        }
    }
    div.innerText = mensagem;
    div.style.display = "flex";

    setTimeout(() => {
        div.style.display = "none";
    }, 3000);
}

export async function deslogar() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=deslogar");
        const resposta = await fetch(caminho, {
            method: "POST"
        });
        if (resposta.erro) {
            console.error("Erro ao listar atendimentos: " + resposta.erro);
            return null;
        }
        if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
        const contentType = resposta.headers.get("content-type");
        let dados = null;
        if (contentType && contentType.includes("application/json")) {
            dados = await resposta.json();
        } else {
            const texto = await resposta.text();
            console.error("Resposta não é JSON:", texto);
            return null;
        }
        if (dados.status == "sucesso") {
            const nav = document.querySelector("nav ul");
            if (nav) {
                for (const li of nav.children) {
                    const a = li.querySelector("a");
                    if (a && a.innerText === "Sair") {
                        a.innerText = "Logar";
                        a.removeEventListener("click", deslogar);
                        a.href = "login.html";
                    }
                }
            } else {
                console.warn("Elemento de navegação não encontrado para atualizar estado de login");
                return null;
            }
            console.log("Deslogado com sucesso!");
            usuarioLogado = null;
            imoveisCurtidos = [];
            if (window.location.pathname.endsWith("index.html") || window.location.pathname.endsWith("/")) {
                window.location.reload();
                return null;
            } else {
                window.location.href = getCaminhoRelativo("index.html");
            }
        }
        else {
            console.warn("Erro ao deslogar: " + dados.mensagem);
        }

    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

export async function carregarUser() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=get_usuario");
        const resposta = await fetch(caminho, {
            method: "GET"
        });
        if (resposta.erro) {
            console.error("Erro ao listar atendimentos: " + resposta.erro);
            return null;
        }
        if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
        const contentType = resposta.headers.get("content-type");
        let dados = null;
        if (contentType && contentType.includes("application/json")) {
            dados = await resposta.json();
        } else {
            const texto = await resposta.text();
            console.error("Resposta não é JSON:", texto);
            return null;
        }
        if (dados.status == "erro") {
            console.error("Erro ao carregar usuário: " + dados.mensagem);
            return null;
        }
        usuarioLogado = dados.usuario;
        // if (usuarioLogado && usuarioLogado.tipo && usuarioLogado.tipo === "CLIENTE") {
        //     imoveisCurtidos = dados?.usuario?.favoritos?.map(f => f.id) || [];
        // }
        return dados;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}