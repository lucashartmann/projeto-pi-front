
const usuario = null;



function adicionarTelefone() {
    event.preventDefault();
    const campoTelefone = document.getElementById("container-telefones");
    const novoTelefone = document.createElement("input");
    novoTelefone.type = "text";
    novoTelefone.name = "telefone";
    novoTelefone.placeholder = "Telefone";
    novoTelefone.classList.add("inpt-telefone");
    Inputmask("(99) 99999-9999").mask(novoTelefone);
    campoTelefone.appendChild(novoTelefone);
    const botao = document.createElement("button");
    botao.type = "button";
    botao.textContent = "Remover";
    botao.id = "bt-remover-numero";
    botao.addEventListener("click", removerTelefone);
    campoTelefone.appendChild(botao);
}

function removerTelefone() {
    event.preventDefault();
    const campoTelefone = document.getElementById("container-telefones");
    const telefones = campoTelefone.getElementsByTagName("input");
    if (telefones.length > 1) {
        campoTelefone.removeChild(telefones[telefones.length - 1]);
    }
    if (telefones.length === 1) {
        document.querySelector("#bt-remover-numero").style.display = "none";
    }
}

window.addEventListener('beforeunload', async function (event) {
    usuario = await carregarUser();
    Inputmask("(99) 99999-9999").mask("#inpt-telefone");
    Inputmask("999.999.999-99").mask("#inpt-cpf");
    Inputmask("99999-999").mask("#ta-cep");

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
});