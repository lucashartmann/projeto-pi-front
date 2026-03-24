async function fazerLogin() {
    const usuario = document.getElementById("usuario").value;
    const senha = document.getElementById("senha").value;

    try {
        const resposta = await fetch("http://127.0.0.1:8000/login/", {
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