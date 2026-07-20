import { getCaminhoRelativo } from "./modules/utils.js";

async function listarAtendimentos() {
    try {
        let caminho = getCaminhoRelativo("/php/api/atendimentos.php?acao=listar_atendimentos");

        const res = await fetch(caminho);

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }

        if (res.status == "erro") {
            alert("Erro ao listar atendimentos: " + res.mensagem);
            return null;
        }

        const contentType = res.headers.get("content-type");

        if (contentType && contentType.includes("application/json")) {
            return await res.json();
        } else {
            const texto = await res.text();
            alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return null;
        }

    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

async function carregarAtendimentos() {
    const dados = await listarAtendimentos();
    const section = document.getElementById("container-horizontal");

    if (!section || !dados) return;

    const divRecemCadastrados = document.getElementById("container-cadastrados");

    for (child of divRecemCadastrados.children) {
        child.remove();
    }

    const divEmAndamento = document.getElementById("container-andamento");

    for (child of divEmAndamento.children) {
        child.remove();
    }

    const divPendente = document.getElementById("container-esperando");

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
                <h2>Nome: ${atendimento.cliente.nome ?? 'Não informado'}</h2>
                <p>Idade: ${atendimento.cliente.idade ?? 'Não informada'}</p>
                <p>Telefone: ${atendimento.cliente.telefone ?? 'Não informado'}</p>
                <p>Email: ${atendimento.cliente.email ?? 'Não informado'}</p>
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
            `;
            divPendente.appendChild(divCard);
        }
    }

}

async function abrirAtendimento(atendimentoId) {
    sessionStorage.setItem("atendimentoId", atendimentoId);
    window.location.href = "html/dados-atendimento.html";
}

window.addEventListener("DOMContentLoaded", () => {
    carregarAtendimentos();
});

