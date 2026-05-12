Inputmask("(99) 99999-9999").mask("#inpt-telefone");
Inputmask("999.999.999-99").mask("#inpt-cpf");
Inputmask("99999-999").mask("#ta-cep");



async function carregarDados() {
    const dados = await carregarUser();
    if (dados.status == "erro") {
        alert("Usuário não encontrado. Faça login novamente.");
        window.location.href = "../html/login.html";
        return;
    }

    if (!dados.usuario) {
        console.warn("Dados do usuário não encontrados na resposta:", dados);
    }

    document.getElementById("inpt-nome").value = dados.usuario?.nome || "";
    document.getElementById("inpt-cpf").value = dados.usuario?.cpf_cnpj || "";
    document.getElementById("inpt-rg").value = dados.usuario?.rg || "";
    document.getElementById("inpt-telefone").value = dados.usuario?.telefones ? dados.usuario.telefones[0] : "";
    document.getElementById("inpt-endereco").value = dados.usuario?.endereco && dados.usuario.endereco.rua ? dados.usuario.endereco.rua : "";
    document.getElementById("inpt-email").value = dados.usuario?.email || "";
    document.getElementById("inpt-data-nascimento").value = dados.usuario?.data_nascimento || "";
}

function salvarDados() {
    const form = documento.querySelector(".grid-container");
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    const dados = await carregarUser();
    if (dados.status == "sucesso" && dados.usuario) {
        if (dados.usuario.tipo == "CLIENTE") {
            data["tipo"] = dados.usuario.tipo;
            data["id"] = dados.usuario.id;
        }
    }
    
    try {
        let caminho = window.location.pathname;
        if (caminho.includes("/html/")) {
            caminho = caminho.replace("/html/", "/");
        }
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/api/usuarios.php?acao=atualizar"
        );
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
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
            alert("Dados atualizados com sucesso!");
            return;
        }

        alert("Falha ao atualizar dados!");
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }
}

document.addEventListener("DOMContentLoaded", async function () {
    await carregarDados();
});