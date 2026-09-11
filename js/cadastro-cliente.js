import { getCaminhoRelativo, formatarValor } from "./modules/utils.js";
import { usuarioLogado, carregarUser } from "./modules/usuario.js";
import { cadastrarPessoa, getPessoa, removerPessoa } from "./modules/pessoas.js";
import { listarHistoricoPorIdCliente } from "./modules/historico.js";

Inputmask("(99) 99999-9999").mask("#inpt-telefone");
Inputmask("999.999.999-99").mask("#inpt-cpf");
Inputmask("99999-999").mask("#ta-cep");

let usuario = null;
let usuario2 = null;

window.abrirCadastro = abrirCadastro;
window.salvar = salvar;
window.apagar = apagar;
window.abrirImovel = abrirImovel;
window.preencherEndereco = preencherEndereco;
window.formatarValor = formatarValor;
window.limpar = limpar;
window.cadastrarPessoa = cadastrarPessoa;
window.getPessoa = getPessoa;
window.removerPessoa = removerPessoa;

function limpar() {
    let forms = document.querySelectorAll("form");
    forms.forEach(form => form.reset());
}

async function preencherEndereco(event) {
    const cep = event.target.value.replace(/\D/g, "");

    if (cep.length !== 8) {
        return;
    }

    try {
        const inputRua = document.getElementById("ta-rua");
        const inputBairro = document.getElementById("ta-bairro");
        const inputCidade = document.getElementById("ta-cidade");
        const inputEstado = document.getElementById("ta-estado");

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

        inputRua.value = data.logradouro || "";
        inputBairro.value = data.bairro || "";
        inputCidade.value = data.localidade || "";
        inputEstado.value = data.uf || "";
    } catch (error) {
        console.error("Erro ao buscar endereço:", error);
    }
}

async function salvar() {

    var form = document.querySelector("form");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
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

    data['id'] = usuario2 ? usuario2.id : null;

    if (JSON.stringify(formData).length > 0) {
        cadastrarPessoa(data);

    } else {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Nenhum dado para enviar!";
        div.innerText = mensagem;
        div.style.display = "flex";
    }

}

async function apagar() {
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
    if (!usuarioID) {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Nenhum usuário selecionado para exclusão!";
        div.innerText = mensagem;
        div.style.display = "flex";
        return;
    }
    let confirmar = confirm("Tem certeza que deseja excluir este usuário?");
    if (usuarioID && confirmar) {
        removerPessoa(usuarioID);
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
        if (usuario?.data_nascimento) {
            document.getElementById("inpt-data-nascimento").value = usuario.data_nascimento ? formatarData(usuario.data_nascimento) : "";
        }
        if (usuario.telefones && Array.isArray(usuario.telefones)) {
            const telefoneInputs = document.querySelectorAll("input[name='telefone']");
            usuario.telefones.forEach((telefone, index) => {
                if (index < telefoneInputs.length) {
                    telefoneInputs[index].value = telefone;
                }
            });
        }
        if (usuario.imoveis) {
            let html = "";

            usuario.imoveis.forEach(imovel => {
                imovel = imovel[0];
                html += `
                <a class="resultado" href="cadastro-imovel.html?id=${imovel.id}">
                    <img src="${imovel.anuncio?.imagens?.[0]}" alt="">
                    <div class="dados">
                        <label>Ref: ${imovel.id}</label>
                        <label for="">Rua: ${imovel.endereco?.rua}, ${imovel.endereco?.numero}, ${imovel.endereco?.bairro}, ${imovel.endereco?.cep}</label>
                        <label for="">Categoria: ${imovel.categoria}</label>
                        <label for="">Status: ${imovel.status}</label>
                        <label for="">Aluguel: ${formatarValor(imovel.valor_aluguel)}</label>
                        <label for="">Venda: ${formatarValor(imovel.valor_venda)}</label>
                        <label for="">Data de Cadastro: ${new Date(imovel.data_cadastro?.date).toLocaleString()}</label>
                        <label for="">Data de Modificação: ${imovel.data_modificacao ? new Date(imovel.data_modificacao?.date).toLocaleString() : ''}</label>
                    </div>
                              
                </a>
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

async function carregarHistorico(idCliente) {
    const lista = await listarHistoricoPorIdCliente(idCliente);
    console.log(lista);
    if (lista != null && lista.length > 0) {
        const tabela = document.getElementById("historico");
        tabela.style.display = "flex";
        tabela.style.flexDirection = "column";
        for (let item of lista) {
            let tr = document.createElement("tr");
            let tdData = document.createElement("td");
            tdData.textContent = item.data;
            tr.appendChild(tdData);
            let tdUsuario = document.createElement("td");
            tdUsuario.textContent = item.funcionario.nome;
            tr.appendChild(tdUsuario);
            let tdAlteracao = document.createElement("td");
            tdAlteracao.textContent = item.alteracao;
            tr.appendChild(tdAlteracao);
            document.getElementById("historico-body").appendChild(tr);
        }
    }
}

window.addEventListener('DOMContentLoaded', async function (event) {
    usuario = usuarioLogado || await carregarUser();
    const id = new URLSearchParams(window.location.search).get("id");
    usuario2 = id ? await getPessoa(id) : null;

    const select = document.querySelector("#select-tipo");

    select.innerHTML = '<option value="" selected>Selecione uma opção...</option>'

    if (usuario && usuario.tipo) {
        carregarHistorico(usuario2 ? usuario2.id : null);
        switch (usuario.tipo) {
            case 'ADMIN':
                select.innerHTML += `<option value="PROPRIETARIO">Proprietário</option>
                <option value="FINANCEIRO">Financeiro</option>
                <option value="CAPTADOR">Captador</option>
                <option value="CORRETOR">Corretor</option>
                <option value="CLIENTE">Cliente</option>
                <option value="VISTORIADOR">Vistoriador</option>
                <option value="GERENTE">Gerente</option>
                <option value="ADMINISTRADOR">Administrador</option>`
                break;

            case "CORRETOR":
                select.innerHTML += `<option value="PROPRIETARIO">Proprietário</option>
                <option value="CLIENTE">Cliente</option>`
                break;

            case "GERENTE":
                select.innerHTML += `<option value="PROPRIETARIO">Proprietário</option>
                <option value="FINANCEIRO">Financeiro</option>
                <option value="CAPTADOR">Captador</option>
                <option value="CORRETOR">Corretor</option>
                <option value="CLIENTE">Cliente</option>
                <option value="VISTORIADOR">Vistoriador</option>`
                break;

            case "CAPTADOR":
                select.innerHTML += `<option value="PROPRIETARIO">Proprietário</option>
                <option value="CLIENTE">Cliente</option>`
                break;

            case "CLIENTE":
                select.style.display = "none";
                break;
        }
    }


    if (usuario2) {
        await abrirCadastro(usuario2);
    }
});
