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
            alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
        }

        if (dados.status == "erro") {
            alert(dados.mensagem);
            return;
        }

        if (resposta.ok && dados.status == "ok") {
            window.location.href = "../html/cadastro-imovel.html";
            return;
        }

        alert("Login invalido!");
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }
}