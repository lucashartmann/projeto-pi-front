import { getCaminhoRelativo } from "./modules/utils.js";

let atendimentos = [];

async function listarAtendimentos() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=get_atendimentos");

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

        return await res.json();

    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

function carregarAtendimentos() {
    const section = document.querySelector("section");
    let html = "";
    if (atendimentos && atendimentos.length > 0) {
        atendimentos.forEach(atendimento => {
            html += `
            <div class="card">
            ${imovel.anuncio.imagens && imovel.anuncio.imagens.length > 0 ? `<img src="${imovel.anuncio.imagens[0]}" alt="Imagem do imóvel">` : ``}
            <label>Imóvel: ${atendimento.imovel.anuncio.titulo}</label>
            <label>Status: ${atendimento.status}</label>
            </div>
            `;
        });
    } else {
        html = `
        <div id="vazio">
            <p>Nenhum atendimento encontrado.</p>
        </div>
        `;
    }
    section.innerHTML = html;
}


window.addEventListener("DOMContentLoaded", async () => {
    atendimentos = await listarAtendimentos();
    carregarAtendimentos();
});

