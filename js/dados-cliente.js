import { usuarioLogado, carregarUser } from "./modules/usuario.js";
import { getCaminhoRelativo } from "./modules/utils.js";

var montou = false;

window.adicionarNumeroTelefone = adicionarNumeroTelefone;
window.removerNumeroTelefone = removerNumeroTelefone;
window.salvarDados = salvarDados;

function editarDados() {
    const dados = document.querySelector('#dados');
    for (const i = 0; i < dados.children.length; i+2) {
        dados.children[i].remove;
        // TODO: implementar
    }
}

function adicionarNumeroTelefone() {
    const container = document.getElementById("container-telefones");
    const novoTelefone = document.createElement("input");
    novoTelefone.type = "text";
    novoTelefone.id = "inpt-telefone";
    novoTelefone.classList.add("telefone");
    novoTelefone.placeholder = "(00) 00000-0000";
    container.appendChild(novoTelefone);
}

function removerNumeroTelefone() {
    const container = document.getElementById("container-telefones");
    if (container.children.length > 1) {
        container.removeChild(container.lastChild);
    } else {
        alert("Pelo menos um número de telefone deve ser mantido.");
    }
}

async function carregarDados() {
    let dados = usuarioLogado || await carregarUser();
    console.log("Dados do usuário logado:", dados);
    if (!dados) {
        alert("Usuário não encontrado. Faça login novamente.");
        // window.location.href = "../html/login.html";
        return;
    }

    if (!dados.usuario) {
        console.warn("Dados do usuário não encontrados na resposta:", dados);
    }

    if (dados.usuario?.tipo != "CLIENTE" && montou == false) {
        let form = document.getElementById("grid-container");
        form.innerHTML += `
         <label id="stt-cep">CEP</label>
            <input type="text" id="ta-cep" maxlength="9" minlength="9" name="cep" required placeholder="00000-000">
            <label id="stt-rua">Rua</label>
            <textarea disabled=True name="rua" id="ta-rua"></textarea>
            <label id="stt-numero">Número do endereço</label>
            <input id="ta-numero" type="number" name="numero" min="0">
            <label id="stt-complemento">Complemento do endereço</label>
            <textarea name="complemento" id="ta-complemento"></textarea>
            <label id="stt-bloco">Bloco do endereço</label>
            <textarea name="bloco" id="ta-bloco"></textarea>
            <label id="stt-bairro">Bairro</label>
            <textarea name="bairro" id="ta-bairro" disabled=True></textarea>
            <label id="stt-cidade">Cidade</label>
            <textarea name="cidade" id="ta-cidade" disabled=True></textarea>
            <label id="stt-estado">Estado</label>
            <input name="uf" disabled=True placeholder="XX" id="ta-estado" maxlength="2" minlength="2" type="text">
        `;
    }

    montou = true;

    document.getElementById("inpt-nome").value = dados.usuario?.nome || "";
    document.getElementById("inpt-cpf").value = dados.usuario?.cpf_cnpj || "";
    document.getElementById("inpt-rg").value = dados.usuario?.rg || "";
    document.getElementById("inpt-telefone").value = dados.usuario?.telefones ? dados.usuario.telefones[0] : "";

    if (dados.usuario?.tipo != "CLIENTE") {
        document.getElementById("ta-cep").value = dados.usuario?.endereco?.cep || "";
        document.getElementById("ta-rua").value = dados.usuario?.endereco?.rua || "";
        document.getElementById("ta-numero").value = dados.usuario?.endereco?.numero || "";
        document.getElementById("ta-complemento").value = dados.usuario?.endereco?.complemento || "";
        document.getElementById("ta-bloco").value = dados.usuario?.endereco?.bloco || "";
        document.getElementById("ta-bairro").value = dados.usuario?.endereco?.bairro || "";
        document.getElementById("ta-cidade").value = dados.usuario?.endereco?.cidade || "";
        document.getElementById("ta-estado").value = dados.usuario?.endereco?.uf || "";
    }
    document.getElementById("inpt-email").value = dados.usuario?.email || "";
    // document.getElementById("inpt-data-nascimento").value = "00/00/0000";
    if (dados.usuario?.data_nascimento) {
        document.getElementById("inpt-data-nascimento").value = formatarData(dados.usuario.data_nascimento);
    }
}

function formatarData(data) {
    const partes = data.split("-");
    if (partes.length === 3) {
        return `${partes[2]}-${partes[1]}-${partes[0]}`;
    }
    return data;
}

async function salvarDados() {
    const form = document.querySelector(".grid-container");
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    const telefones = [];
    for (let i = 0; i < formData.getAll("telefone").length; i++) {
        const telefone = formData.getAll("telefone")[i];
        if (telefone.trim() !== "") {
            telefones.push(telefone);
        }
    }
    data["telefones"] = telefones;
    let dados = usuarioLogado || await carregarUser();
    if (dados && dados.usuario) {
        if (dados.usuario.tipo == "CLIENTE") {
            data["tipo"] = dados.usuario.tipo;
            data["id"] = dados.usuario.id;
        }
    }

    try {
        let caminho = getCaminhoRelativo("/php/api/usuarios.php?acao=atualizar");
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
        let dados = null;
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
    Inputmask("999.999.999-99").mask("#inpt-cpf");
    Inputmask("99999-999").mask("#ta-cep");
    const containers = document.getElementsByClassName("telefone");
    for (let i = 0; i < containers.length; i++) {
        Inputmask("(99) 99999-9999").mask(containers[i]);
    }
    await carregarDados();
});