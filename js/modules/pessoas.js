import { getCaminhoRelativo } from "./utils.js";


export async function removerPessoa(id) {
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
        let caminho = getCaminhoRelativo("/php/api/usuarios.php?acao=apagar&id=" + id);
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
                    mensagem = "Erro ao remover usuário: " + response.erro;
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
                    mensagem = "Erro ao excluir usuário: " + data.mensagem;
                    div.innerText = mensagem;
                    div.style.display = "flex";
                } else {
                    div.classList.add("sucesso");
                    div.classList.remove("erro");
                    mensagem = "Usuário excluído com sucesso";
                    div.innerText = mensagem;
                    div.style.display = "flex";
                    window.location.href = "estoque.html";
                }
            })
            .catch(error => {
                div.classList.add("erro");
                div.classList.remove("sucesso");
                mensagem = "Erro ao excluir usuário:", error;
                div.innerText = mensagem;
                div.style.display = "flex";
            });
    } catch (error) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Erro ao enviar dados para exclusão do usuário:" + error;
        div.innerText = mensagem;
        div.style.display = "flex";
    }
    div.innerText = mensagem;
    div.style.display = "flex";
}

export async function cadastrarPessoa(data) {
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
        let caminho = getCaminhoRelativo("/php/api/usuarios.php?acao=cadastro");
        await fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
            .then(async response => {
                if (response.erro) {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao cadastrar usuário: " + response.erro;
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
                    mensagem = "Erro ao cadastrar usuário: " + data.mensagem;
                    div.innerText = mensagem;
                    div.style.display = "flex";
                    return;
                }
                else if (data.mensagem) {
                    div.classList.add("sucesso");
                    div.classList.remove("erro");
                    mensagem = "Usuário cadastrado com sucesso: " + data.mensagem;
                    div.innerText = mensagem;
                    div.style.display = "flex";
                }

            })
            .catch(error => {
                div.classList.add("erro");
                div.classList.remove("sucesso");
                mensagem = "Erro ao cadastrar usuário: " + error;
                div.innerText = mensagem;
                div.style.display = "flex";
            });

    } catch (error) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Erro ao enviar dados do usuário:" + error;
        div.innerText = mensagem;
        div.style.display = "flex";
    }
    div.innerText = mensagem;
    div.style.display = "flex";
}

export async function getPessoa(id) {
    try {
        let caminho = getCaminhoRelativo(`/php/api/usuarios.php?acao=buscar&id=${id}`);
        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (res.erro) {
                    console.error("Erro ao buscar usuário: " + res.erro);
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
                    console.error("Erro ao buscar usuário: " + data.mensagem);
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

export async function listarPessoas(tipo) {
    try {
        let caminho = getCaminhoRelativo("/php/api/usuarios.php?acao=listar&tipo=" + tipo);
        const resposta = await fetch(caminho)
            // .then(res => console.log(res))
            .then(async (res) => {
                if (res.erro) {
                    alert("Erro ao listar pessoas: " + res.erro);
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
                if (data.status == "erro") {
                    console.error("Erro ao listar pessoas: " + data.mensagem);
                    return [];
                }
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return [];
            });

        return resposta;

    } catch (error) {
        console.error("Falha ao conectar com o backend:", erro);
        return [];
    }
}