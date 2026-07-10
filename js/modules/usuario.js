import { getCaminhoRelativo } from "./utils.js";

export let usuarioLogado = null;
export let imoveisCurtidos = [];
export var logado = false;

export async function salvarImoveisCurtidos() {
    try {
        console.log(imoveisCurtidos);
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=favoritar_imoveis");
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id_imoveis: imoveisCurtidos })
        })
            .then(async (response) => {
                if (response.erro) {
                    alert("Erro ao cadastrar favoritos: " + response.erro);
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
            .then(data => {
                if (data.status === "sucesso") {
                    carregarUser(); 
                    console.log("Imóveis curtidos salvos com sucesso", data.mensagem);
                } else {
                    console.error("Erro ao salvar imóveis curtidos:", data.mensagem);
                }
            })
            .catch(err => {
                console.error("Erro na requisição para salvar imóveis curtidos:", err);
            });
    } catch (err) {
        console.error("Erro ao salvar imóveis curtidos:", err);
    }

}

export async function curtirImovel(event, imovelId) {
    if (!logado) {
        if (!usuarioLogado) {
            alert("Você precisa estar logado para curtir um imóvel!");
            return;
        }
        else {
            logado = true;
        }
    }
    if (imoveisCurtidos.includes(imovelId)) {
        imoveisCurtidos.splice(imoveisCurtidos.indexOf(imovelId), 1);
        console.log("Imóvel removido dos curtidos:", imoveisCurtidos);
        event.target.classList.remove("curtido");
        return;
    }
    imoveisCurtidos.push(imovelId);
    event.target.classList.toggle("curtido");
    event.stopPropagation();
}

export async function deslogar() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=deslogar");
        const resposta = await fetch(caminho, {
            method: "POST"
        });
        if (resposta.erro) {
            alert("Erro ao listar atendimentos: " + resposta.erro);
            return null;
        }
        if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
        const contentType = resposta.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            let dados = await resposta.json();
        } else {
            const texto = await resposta.text();
            alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
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
                return;
            }
            console.log("Deslogado com sucesso!");
            usuarioLogado = null;
            imoveisCurtidos = [];
            if (window.location.pathname.endsWith("index.html") || window.location.pathname.endsWith("/")) {
                window.location.reload();
                return;
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
            alert("Erro ao listar atendimentos: " + resposta.erro);
            return null;
        }
        if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
        const contentType = resposta.headers.get("content-type");
        let dados = null;
        if (contentType && contentType.includes("application/json")) {
            dados = await resposta.json();
        } else {
            const texto = await resposta.text();
            // alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return null;
        }
        if (dados.status == "erro") {
            console.error("Erro ao carregar usuário: " + dados.mensagem);
            return null;
        }
        usuarioLogado = dados.usuario;
        if (usuarioLogado.tipo === "CLIENTE") {
            imoveisCurtidos = dados.imoveis || [];
        }
        return dados;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}