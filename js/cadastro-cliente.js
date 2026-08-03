import { getCaminhoRelativo } from "./modules/utils.js";
import { usuarioLogado, carregarUser } from "./modules/usuario.js";
import { getUsuario } from "./modules/usuarios.js";

Inputmask("(99) 99999-9999").mask("#inpt-telefone");
Inputmask("999.999.999-99").mask("#inpt-cpf");
Inputmask("99999-999").mask("#ta-cep");

let usuario = null;

window.abrirCadastro = abrirCadastro;
window.salvar = salvar;
window.apagar = apagar;
window.abrirImovel = abrirImovel;

async function salvar() {
    var form = document.querySelector("form");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    let formData = new FormData(form);
    const data = {};

    document.querySelectorAll("input[name='telefone']").forEach((input, index) => {
        const telefone = input.value.trim();
        if (telefone !== "") {
            if (!data.telefones) {
                data.telefones = [];
            }
            data.telefones.push(telefone);
        }
    });

    formData.forEach((value, key) => {
        data[key] = value;
    });
    if (JSON.stringify(formData).length > 0) {
        try {
            let caminho = getCaminhoRelativo("/php/api/usuarios.php?acao=cadastro");
            await fetch(caminho, {
                method: "POST",
                body: JSON.stringify(data)
            })
                .then(async response => {
                    if (response.erro) {
                        alert("Erro ao cadastrar usuário: " + response.erro);
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
                        alert("Erro ao cadastrar usuário: " + data.mensagem);
                        return;
                    }
                    else if (data.mensagem) {
                        alert("Usuário cadastrado com sucesso: " + data.mensagem);
                        if (!imovel) {
                            forms.forEach(form => form.reset());
                        }
                    }

                })
                .catch(error => {
                    alert("Erro ao cadastrar usuário:", error);
                });

        } catch (error) {
            console.error("Erro ao enviar dados do usuário:", error);
        }

    } else {
        alert("Nenhum dado para enviar!");
    }

    // console.log("Dados do imóvel a serem enviados:", data);
}

async function apagar() {
    confirmar = confirm("Tem certeza que deseja excluir este usuário?");
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
                        alert("Erro ao excluir usuário: " + data.mensagem);
                    } else {
                        console.log("Usuário excluído com sucesso:", data);
                        window.location.href = "estoque.html";
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
        // alert("Nenhum imóvel selecionado para exclusão!");
        window.location.href = "estoque.html";
    }


}

function formatarData(data) {
    const partes = data.split("-");
    if (partes.length === 3) {
        return `${partes[2]}-${partes[1]}-${partes[0]}`;
    }
    return data;
}

async function abrirImovel(imovel = null) {
    sessionStorage.setItem("imovel", JSON.stringify(imovel));
    window.location.href = "cadastro-imovel.html";
}

async function abrirCadastro(usuario) {
    if (usuario) {
        document.getElementById("inpt-nome").value = usuario.nome || "";
        // document.getElementById("inpt-username").value = usuario.username || "";
        document.getElementById("inpt-email").value = usuario.email || "";
        document.getElementById("inpt-cpf").value = usuario.cpf_cnpj || "";
        document.getElementById("ta-cep").value = usuario.endereco?.cep || "";
        document.getElementById("ta-rua").value = usuario.endereco?.rua || "";
        document.getElementById("ta-numero").value = usuario.endereco?.numero || "";
        document.getElementById("ta-bairro").value = usuario.endereco?.bairro || "";
        document.getElementById("ta-cidade").value = usuario.endereco?.cidade || "";
        document.getElementById("ta-estado").value = usuario.endereco?.uf || "";
        document.getElementById("ta-complemento").value = usuario.endereco?.complemento || "";
        document.getElementById("ta-bloco").value = usuario.endereco?.bloco || "";
        document.getElementById("inpt-rg").value = usuario.rg || "";
        document.getElementById("inpt-creci").value = usuario.creci || "";
        document.getElementById("inpt-salario").value = usuario.salario || "";
        document.getElementById("select-tipo").value = usuario.tipo || "";
        if (dados.usuario?.data_nascimento) {
            document.getElementById("inpt-data-nascimento").value = usuario.data_nascimento ? formatarData(usuario.data_nascimento) : "";
        }
        if (usuario.telefones && Array.isArray(usuario.telefones)) {
            const containerTelefones = document.getElementById("container-telefones");
            containerTelefones.innerHTML = "";
            usuario.telefones.forEach(telefone => {
                const novoTelefone = document.createElement("input");
                novoTelefone.type = "text";
                novoTelefone.name = "telefone";
                novoTelefone.value = telefone;
                novoTelefone.classList.add("inpt-telefone");
                Inputmask("+99 (99) 99999-9999").mask(novoTelefone);
                containerTelefones.appendChild(novoTelefone);
            });
        }
        if (usuario.imoveis) {
            html = "";

            usuario.imoveis.forEach(imovel => {
                html += `
                <div class="resultado" onclick="abrirImovel(imovel)">
                    <img src="${imovel.anuncio?.imagens?.[0] || null}" alt="">
                    <div class="dados">
                        <label for="">REF: ${imovel.id}</label>
                        <label>${imovel.anuncio?.titulo || ""}</label>
                        <label for="">${imovel.endereco || ""}</label>
                        <label for="">${imovel.categoria || ""}</label>
                        <label for="">${imovel.status || ""}</label>
                    </div>
                </div>
            `;
            });
            if (html) {
                const titulo = document.querySelector(".titulo");
                titulo.style.display = "flex";
                titulo.style.flexDirection = "row";
                const containerResultado = document.querySelector("#container-resultado");
                containerResultado.style.display = "flex";
                containerResultado.style.flexDirection = "column";
                containerResultado.innerHTML = "";
                containerResultado.innerHTML = html;
            }
        }
    } else {
        alert("Usuário não encontrado para edição.");
        window.location.href = "estoque.html";
    }

}

window.addEventListener('DOMContentLoaded', async function (event) {
    usuario = usuarioLogado || await carregarUser();

    const select = document.querySelector("#select-tipo");

    select.innerHTML = '<option value="" selected>Selecione uma opção...</option>'

    if (usuario && usuario.tipo) {
        switch (usuario.tipo) {
            case 'ADMIN':
                select.innerHTML += `<option value="Proprietário">Proprietário</option>
                <option value="Financeiro">Financeiro</option>
                <option value="Captador">Captador</option>
                <option value="Corretor">Corretor</option>
                <option value="Cliente">Cliente</option>
                <option value="Vistoriador">Vistoriador</option>
                <option value="Gerente">Gerente</option>
                <option value="Administrador">Administrador</option>`
                break;

            case "CORRETOR":
                select.innerHTML += `<option value="Proprietário">Proprietário</option>
                <option value="Cliente">Cliente</option>`
                break;

            case "GERENTE":
                select.innerHTML += `<option value="Proprietário">Proprietário</option>
                <option value="Financeiro">Financeiro</option>
                <option value="Captador">Captador</option>
                <option value="Corretor">Corretor</option>
                <option value="Cliente">Cliente</option>
                <option value="Vistoriador">Vistoriador</option>`
                break;

            case "CAPTADOR":
                select.innerHTML += `<option value="Proprietário">Proprietário</option>
                <option value="Cliente">Cliente</option>`
                break;

            case "CLIENTE":
                select.style.display = "none";
                break;
        }
    }

    const id = new URLSearchParams(window.location.search).get("id");
    let usuario2 = id ? await getDadosUsuario(id) : null;
    if (usuario2) {
        await abrirCadastro(usuario2);
    }
});
