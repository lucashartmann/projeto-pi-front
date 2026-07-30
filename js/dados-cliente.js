import { usuarioLogado, carregarUser } from "./modules/usuario.js";
import { getCaminhoRelativo } from "./modules/utils.js";

var montou = false;
let usuario = null;

window.preencherEndereco = preencherEndereco;
window.adicionarNumeroTelefone = adicionarNumeroTelefone;
window.removerNumeroTelefone = removerNumeroTelefone;
window.salvarDados = salvarDados;
window.apagar = apagar;
window.editarDados = editarDados;
window.enviarNovaSenha = enviarNovaSenha;

async function enviarNovaSenha() {
    event.preventDefault();
    const email = document.getElementById("inpt-email").value;
    if (!email) {
        alert("Por favor, insira um email válido.");
        return;
    }
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=recuperar_senha");
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                email: email
            })
        });
        if (!resposta.ok) {
            alert("Erro ao recuperar senha: ");
            return null;
        }
        let dados = null;
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
            alert(dados.mensagem);
            return;
            // window.location.href = "../index.html";
        }

    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }
}


async function preencherEndereco(event) {
    const cep = event.target.value.replace(/\D/g, "");

    if (cep.length !== 8) {
        return;
    }

    const labelRua = document.getElementById("lbl-rua");
    const labelBairro = document.getElementById("lbl-bairro");
    const labelCidade = document.getElementById("lbl-cidade");
    const labelEstado = document.getElementById("lbl-uf");


    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);

    if (!response.ok) {
        console.log("CEP inválido ou não encontrado");
        return;
    }

    const data = await response.json();

    if (data.erro) {
        alert("CEP não encontrado!");
        return;
    }

    labelBairro.value = data.bairro || "";
    labelCidade.value = data.localidade || "";
    labelEstado.value = data.uf || "";
    labelRua.value = data.logradouro || "";

}


function editarDados() {
    const divs = document.querySelectorAll('.separador');
    for (let i = 0; i < divs.length; i++) {
        const label = divs[i].querySelectorAll('label')[1];
        const input = divs[i].querySelector('input');
        if (!label || !input) continue;
        label.style.display = 'none';
        input.style.display = 'block';
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
    let dados = usuario;
    if (!dados) {
        alert("Usuário não encontrado. Faça login novamente.");
        // window.location.href = "../html/login.html";
        return;
    }

    if (!dados.usuario) {
        console.warn("Dados do usuário não encontrados na resposta:", dados);
    }

    if (dados.usuario) {
        let form = document.getElementById("dados");
        form.innerHTML += `
        <div class="separador">
                    <label for="">Nome</label>
                    <label>${dados.usuario.nome ?? ''}</label>
                    <input type="text" style='display: none;' id="inpt-nome" name="nome" value="${dados.usuario.nome ?? ''}">
                </div>
                <div class="separador">
                    <label for="">Email</label>
                    <label>${dados.usuario.email ?? ''}</label>
                    <input type="email" style='display: none;' id="inpt-email" name="email" value="${dados.usuario.email ?? ''}">
                </div>
               
                <div class="separador">
                    <label for="">Telefone</label>
                    <label>${dados.usuario.telefone ?? ''}</label>
                    <input type="text" style='display: none;' id="inpt-telefone" name="telefone"
                        value="${dados.usuario.telefone ?? ''}">
                </div>
                <div class="separador">
                    <label for="">CPF/CNPJ</label>
                    <label>${dados.usuario.cpf_cnpj ?? ''}</label>
                    <input type="text" style='display: none;' id="inpt-cpf-cnpj" name="cpf_cnpj" value="${dados.usuario.cpf_cnpj ?? ''}">
                </div>
                <div class="separador">
                    <label for="">RG</label>
                    <label>${dados.usuario.rg ?? ''}</label>
                    <input type="text" style='display: none;' id="inpt-rg" name="rg" value="${dados.usuario.rg ?? ''}">
                </div>
                <div class="separador">
                    <label for="">Data de Nascimento</label>
                    <label type="date">${dados.usuario.data_nascimento ? formatarData(dados.usuario.data_nascimento) : ''}</label>
                    <input type="date" style='display: none;' id="inpt-data-nascimento" name="data_nascimento"
                        value="${dados.usuario.data_nascimento ?? ''}">
                </div>
                <div class="separador">
                    <label for="">Rua</label>
                    <label id="lbl-rua" name="rua">${dados.usuario.rua ?? ''}</label>
                </div>
                <div class="separador">
                    <label for="">Bairro</label>
                    <label id="lbl-bairro">${dados.usuario.bairro ?? ''}</label>
                    <input type="text" style='display: none;' id="inpt-bairro" name="bairro" value="${dados.usuario.bairro ?? ''}">
                </div>
                <div class="separador">
                    <label for="">Número</label>
                    <label>${dados.usuario.numero ?? ''}</label>
                    <input type="text" style='display: none;' id="inpt-numero" name="numero" value="${dados.usuario.numero ?? ''}">
                </div>
                <div class="separador">
                    <label for="">Complemento</label>
                    <label>${dados.usuario.complemento ?? ''}</label>
                    <input type="text" style='display: none;' id="inpt-complemento" name="complemento" value="${dados.usuario.complemento ?? ''}">
                </div>
                <div class="separador">
                    <label for="">CEP</label>
                    <label>${dados.usuario.cep ?? ''}</label>
                    <input oninput="preencherEndereco(event)" type="text" style='display: none;' id="inpt-cep" name="cep" value="${dados.usuario.cep ?? ''}">
                </div>
                <div class="separador">
                    <label for="" id="lbl-cidade">Cidade</label>
                    <label id="lbl-cidade" name="cidade">${dados.usuario.cidade ?? ''}</label>
                </div>
                <div class="separador">
                    <label for="" id="lbl-uf">UF</label>
                    <label id="lbl-uf" name="uf">${dados.usuario.uf ?? ''}</label>
                </div>
                <div class="separador">
                    <label for="" id="lbl-data-cadastro">Data de Cadastro</label>
                    <label id="lbl-data-cadastro" name="data-cadastro">${new Date(dados.usuario.data_cadastro?.date).toLocaleDateString()}</label>
                </div>
        `;
        let dadosBasicos = document.getElementById("dados-basicos");
        dadosBasicos.innerHTML += `
        <label for="" class="nome">${dados.usuario.nome ?? ''}</label>
                <label for="" class="cargo">${dados.usuario.tipo ?? ''}</label>
        `;
        let emailTelefone = document.getElementById("email-telefone");
        emailTelefone.innerHTML += ` <div id="div-email">
                    <label for="">Email:</label>
                    <label for="" class="email">${dados.usuario.email ?? ''}</label>
                </div>
                <div id="div-telefone">
                    <label for="">Numero de telefone:</label>
                    <label for="" class="telefone">${dados.usuario.telefone ?? ''}</label>
                </div>
        `;

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

async function apagar() {
    confirmar = confirm("Tem certeza que deseja excluir este usuário?");
    usuarioID = usuario ? usuario.id : null;
    if (usuarioID && confirmar) {
        try {
            let caminho = getCaminhoRelativo("/php/api/usuarios.php?acao=apagar&id=" + usuarioID);
            const response = await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },

            })
                .then(async (response) => {
                    if (response.erro) {
                        alert("Erro ao remover usuário: " + response.erro);
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
                .then(async (data) => {
                    if (data.status == "erro") {
                        alert("Erro ao excluir imóvel: " + data.mensagem);
                    } else {
                        console.log("Usuário excluído com sucesso:", data);
                        window.location.href = "index.html";
                    }
                })
                .catch(error => {
                    console.error("Erro ao excluir usuário:", error);
                });
        } catch (error) {
            console.error("Erro ao enviar dados para exclusão do usuário:", error);
        }
    }
    else {
        alert("Nenhum usuário para exclusão!");
        return;
    }


}


document.addEventListener("DOMContentLoaded", async function () {

    usuario = usuarioLogado || await carregarUser();
    await carregarDados();
    Inputmask("999.999.999-99").mask("#inpt-cpf-cnpj");
    Inputmask("99999-999").mask("#inpt-cep");
    const containers = document.getElementsByClassName("telefone");
    for (let i = 0; i < containers.length; i++) {
        Inputmask("(99) 99999-9999").mask(containers[i]);
    }
});