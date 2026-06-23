Inputmask("(99) 99999-9999").mask("#inpt-telefone");
Inputmask("999.999.999-99").mask("#inpt-cpf");
Inputmask("99999-999").mask("#ta-cep");

const select = document.querySelector("#select-tipo");

const usuario = await carregarUser();

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