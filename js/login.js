async function fazerLogin() {
    const usuario = document.getElementById("usuario").value;
    const senha = document.getElementById("senha").value;

    try {
        let caminho = window.location.pathname;
        if (caminho.includes("/html/")) {
            caminho = caminho.replace("/html/", "/");
        }
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/js_controller.php?acao=login"
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

        const dados = await resposta.json();

        if (resposta.ok && dados.status === "ok") {
            console.log("Login correto!");
            window.location.href = "../html/cadastro-imovel.html";
            return
        }

        alert("Login invalido!");
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }
}