import { getCaminhoRelativo } from "./modules/utils.js";

let listaAtendimentos = [];

async function listarAtendimentos() {
    try {
        let caminho = getCaminhoRelativo("/php/api/atendimentos.php?acao=listar");

        const res = await fetch(caminho);

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }

        const contentType = res.headers.get("content-type");

        if (contentType && contentType.includes("application/json")) {
            const dados = await res.json();
            if (dados.status === "erro") {
                console.warn("Erro ao listar atendimentos: " + dados.mensagem);
                return null;
            }
            else {
                return dados;
            }
        } else {
            const texto = await res.text();
            console.warn("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return null;
        }


    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

async function carregarAtendimentos() {
    const dados = listaAtendimentos;
    const section = document.getElementById("container-horizontal");
    const divRecemCadastrados = document.getElementById("container-cadastrados");
    const divEmAndamento = document.getElementById("container-andamento");
    const divPendente = document.getElementById("container-esperando");
    let divVazio = null;

    if (!dados || dados.length === 0) {
        divVazio = document.createElement("div");
        divVazio.className = "vazio";
        divVazio.textContent = "Nenhum atendimento encontrado.";
        divRecemCadastrados.appendChild(divVazio);
        divVazio = document.createElement("div");
        divVazio.className = "vazio";
        divVazio.textContent = "Nenhum atendimento encontrado.";
        divEmAndamento.appendChild(divVazio);
        divVazio = document.createElement("div");
        divVazio.className = "vazio";
        divVazio.textContent = "Nenhum atendimento encontrado.";
        divPendente.appendChild(divVazio);
    }

    if (!section || !dados) return;


    for (child of divRecemCadastrados.children) {
        child.remove();
    }


    for (child of divEmAndamento.children) {
        child.remove();
    }


    for (child of divPendente.children) {
        child.remove();
    }

    const tamanho = dados.length < 5 ? dados.length : 5;

    for (let i = 0; i < tamanho; i++) {
        const divCard = document.createElement("div");
        divCard.id = "card-cadastrado";
        divCard.className = "card";
        divCard.onclick = () => abrirAtendimento(dados[i].id);
        divCard.innerHTML = `
            <p style="margin-top: 20px;">Nome: ${dados[i].cliente.nome}</p>
            <p>Telefone: ${dados[i].cliente.telefones}</p>
            <p>Email: ${dados[i].cliente.email}</p>
        `;
        divRecemCadastrados.appendChild(divCard);
    }




    for (const atendimento of dados) {
        if (atendimento.status === "Em andamento") {
            const divEmAndamento = document.getElementById("container-em-andamento");
            if (!divEmAndamento) continue;
            const divCard = document.createElement("div");
            divCard.id = "card-cadastrado";
            divCard.className = "card";
            divCard.onclick = () => abrirAtendimento(atendimento.id);
            divCard.innerHTML = `
                <h2>Nome: ${atendimento.cliente.nome ?? ''}</h2>
                <p>Idade: ${atendimento.cliente.idade ?? ''}</p>
                <p>Telefone: ${atendimento.cliente.telefone ?? ''}</p>
                <p>Email: ${atendimento.cliente.email ?? ''}</p>
                <p>Data de cadastro: ${atendimento.data_cadastro ?? ''}</p>
                <p>Imovel: ${atendimento.imovel ? '${atendimento.imovel.id} - ${atendimento.imovel.endereco?.rua}, ${atendimento.imovel.endereco?.numero}/${atendimento.imovel.endereco?.complemento}' : ''}</p>
            `;
            divEmAndamento.appendChild(divCard);
        } else if (atendimento.status === "Pendente") {
            const divPendente = document.getElementById("container-esperando");
            if (!divPendente) continue;
            const divCard = document.createElement("div");
            divCard.id = "card-cadastrado";
            divCard.className = "card";
            divCard.onclick = () => abrirAtendimento(atendimento.id);
            divCard.innerHTML = `
                <h2>Nome: ${atendimento.cliente.nome ?? 'Não informado'}</h2>
                <p>Idade: ${atendimento.cliente.idade ?? 'Não informada'}</p>
                <p>Telefone: ${atendimento.cliente.telefone ?? 'Não informado'}</p>
                <p>Email: ${atendimento.cliente.email ?? 'Não informado'}</p>
                <p>Status: ${atendimento.status ?? 'Não informado'}</p>
                <p>Data de cadastro: ${atendimento.data_cadastro ?? 'Não informada'}</p>
                <p>Imovel: ${atendimento.imovel ? atendimento.imovel.endereco : 'Não informado'}</p>
            `;
            divPendente.appendChild(divCard);
        }
    }

    if (dados.filter(a => a.status === "Em andamento").length === 0) {
        divVazio = document.createElement("div");
        divVazio.class = "vazio";
        divVazio.textContent = "Nenhum atendimento encontrado.";
        divEmAndamento.appendChild(divVazio);
    }

    if (dados.filter(a => a.status === "Pendente").length === 0) {
        divVazio = document.createElement("div");
        divVazio.class = "vazio";
        divVazio.textContent = "Nenhum atendimento encontrado.";
        divPendente.appendChild(divVazio);
    }

}

async function abrirAtendimento(atendimentoId) {
    let atendimento = listaAtendimentos.find(a => a.id === atendimentoId);
    if (!atendimento) {
        console.warn("Atendimento não encontrado.");
        return;
    }
    const overlay = document.createElement("div");
    overlay.className = "overlay";
    overlay.style.cssText = `
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 999;
    `;

    if (!document.contains("#card-dados")) {
        document.body.appendChild(overlay);
    } else {
        document.querySelector('.overlay')?.remove();
    }


    let html = `
    <div id="card-dados">
        <h2>Nome: ${atendimento.cliente.nome ?? 'Não informado'}</h2>
        <p>Idade: ${atendimento.cliente.idade ?? 'Não informada'}</p>
        <p>Telefone: ${atendimento.cliente.telefone ?? 'Não informado'}</p>
        <p>Email: ${atendimento.cliente.email ?? 'Não informado'}</p>
        <h2>Status: ${atendimento.status ?? 'Não informado'}</h2>
                
    `

    if (atendimento.imovel) {
        html += `
        <h3>Informações do Imóvel</h3>
        <p>Endereço: ${atendimento.imovel.endereco ?? 'Não informado'}</p>
        <p>Tipo: ${atendimento.imovel.tipo ?? 'Não informado'}</p>
        <p>Valor: ${atendimento.imovel.valor ?? 'Não informado'}</p>
        `;
    }

    html += `
    <select id="status-select">
        <option value="" disabled selected>Selecionar uma opção</option>
        <option value="Pendente">Pendente</option>
        <option value="Em andamento">Em andamento</option>
        <option value="Concluído">Concluído</option>
    </select>
    <button>Atender</button></div>`;

    const div = document.createElement("div");
    div.innerHTML = html;

    document.body.appendChild(div);
}

window.addEventListener("DOMContentLoaded", async () => {
    listaAtendimentos = await listarAtendimentos();
    carregarAtendimentos();

    document.addEventListener("click", function (e) {
        if (document.body.contains(document.getElementById("card-dados"))) {
            document.getElementById("card-dados").remove();
        }
    });
});

