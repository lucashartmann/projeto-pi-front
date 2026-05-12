async function fazerLogin() {
    event.preventDefault();
    const usuario = document.getElementById("usuario").value;
    const senha = document.getElementById("senha").value;

    try {
        let caminho = window.location.pathname;
        if (caminho.includes("/html/")) {
            caminho = caminho.replace("/html/", "/");
        }
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/api/login.php?acao=login"
        );
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                usuario: usuario,
                senha: senha
            })
        });
        if (resposta.erro) {
            alert("Erro ao fazer login: " + resposta.erro);
            return null;
        }
        const contentType = resposta.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            dados = await resposta.json();
        } else {
            const texto = await resposta.text();
            // alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
        }

        if (dados.status == "erro") {
            alert(dados.mensagem);
            return;
        }

        if (resposta.ok && dados.status == "sucesso") {
            window.location.href = "../html/cadastro-imovel.html";
            return;
        }

        alert("Login invalido!");
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }
}

document.getElementById("h3-login").addEventListener("click", function () {
    document.getElementById("form-login").style.display = "flex";
    document.getElementById("form-cadastro").style.display = "none";
    document.getElementById("h3-login").style.color = "var(--background-nav)";
    document.getElementById("h3-cadastro").style.color = "white";
    document.getElementById("login-header").style.top = "26%";
});

document.getElementById("h3-cadastro").addEventListener("click", function () {
    document.getElementById("form-login").style.display = "none";
    document.getElementById("form-cadastro").style.display = "flex";
    document.getElementById("h3-cadastro").style.color = "var(--background-nav)";
    document.getElementById("h3-login").style.color = "white";
    document.getElementById("login-header").style.top = "18%";
});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("form-cadastro").style.display = "none";
    document.getElementById("h3-login").style.color = "var(--background-nav)";
    document.getElementById("h3-cadastro").style.color = "white";
});

async function fazerCadastro() {
    event.preventDefault();
    var formAnuncio = document.getElementById("form-cadastro");
    var data = {};
    var formData = new FormData(formAnuncio);
    formData.forEach(function (value, key) {
        data[key] = value;
    });

    try {
        let caminho = window.location.pathname;
        if (caminho.includes("/html/")) {
            caminho = caminho.replace("/html/", "/");
        }
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/api/login.php?acao=cadastro"
        );
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });
        if (resposta.erro) {
            alert("Erro ao fazer cadastro: " + resposta.erro);
            return null;
        }
        const contentType = resposta.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            const data = resposta.status !== 204 ? await resposta.json() : {};
            if (data.length === 0) {
                alert("Resposta inesperada do servidor: JSON vazio");
                console.error("Resposta JSON vazia:", data);
                return;
            } else {
                if (dados.status == "erro") {
                    alert(dados.mensagem);
                    return;
                }

                if (resposta.ok && dados.status == "sucesso") {
                    window.location.href = "../index.html";
                    return;
                }
            }
        } else {
            const texto = await resposta.text();
            // alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
        }

        alert("Login invalido!");
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }
}