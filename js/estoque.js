const imoveis_cache = [];

async function carregarAnuncios() {
    const dados = [];
    if (!imoveis_cache) {
        imoveis_cache = await listarImoveis();
        dados = imoveis_cache;
    } else {
        dados = await listarImoveis();
    }
    const section = document.getElementById("container-resultado");
    const seta = document.getElementById("seta");
    const filtro = document.getElementById("select-filtro").value;
    if (!section || !dados) { 
        console.log("Erro: Elementos não encontrados"); 
        return; 
    }
    switch (filtro) {
        case "id":
            if (seta.textContent === "⬇️") {
                dados.sort((a, b) => a.id - b.id);
            } else {
                dados.sort((a, b) => b.id - a.id);
            }
            break;
        case "categoria":
            if (seta.textContent === "⬇️") {
                dados.sort((a, b) => a.categoria.localeCompare(b.categoria));
            } else {
                dados.sort((a, b) => b.categoria.localeCompare(a.categoria));
            }
            break;
        case "status":
            if (seta.textContent === "⬇️") {
                dados.sort((a, b) => a.status.localeCompare(b.status));
            } else {
                dados.sort((a, b) => b.status.localeCompare(a.status));
            }
            break;
        case "cep":
            if (seta.textContent === "⬇️") {
                dados.sort((a, b) => a.endereco?.cep - b.endereco?.cep);
            } else {
                dados.sort((a, b) => b.endereco?.cep - a.endereco?.cep);
            }
            break;
        case "numero":
            if (seta.textContent === "⬇️") {
                dados.sort((a, b) => a.endereco?.numero - b.endereco?.numero);
            } else {
                dados.sort((a, b) => b.endereco?.numero - a.endereco?.numero);
            }
            break;
        case "aluguel":
            if (seta.textContent === "⬇️") {
                dados.sort((a, b) => a.valor_aluguel - b.valor_aluguel);
            } else {
                dados.sort((a, b) => b.valor_aluguel - a.valor_aluguel);
            }
            break;
        case "venda":
            if (seta.textContent === "⬇️") {
                dados.sort((a, b) => a.valor_venda - b.valor_venda);
            } else {
                dados.sort((a, b) => b.valor_venda - a.valor_venda);
            }
            break;
        case "data_cadastro":
            if (seta.textContent === "⬇️") {
                dados.sort((a, b) => new Date(a.data_cadastro) - new Date(b.data_cadastro));
            } else {
                dados.sort((a, b) => new Date(b.data_cadastro) - new Date(a.data_cadastro));
            }
            break;
        case "data_modificacao":
            if (seta.textContent === "⬇️") {
                dados.sort((a, b) => new Date(a.data_modificacao) - new Date(b.data_modificacao));
            } else {
                dados.sort((a, b) => new Date(b.data_modificacao) - new Date(a.data_modificacao));
            }
            break;
        default:
            break;
    }


    section.innerHTML = "";
    for (let imovel of dados) {
        const b64 = imovel.anuncio?.imagens?.[0] || null;
        section.innerHTML += `
            <div class="resultado" onclick="abrirCadastro(${imovel.id})">
                <img src="${b64}" alt="">
                <div class="dados">
                    <label>${imovel.id}</label>
                    <label for="">${imovel.endereco?.rua}</label>
                    <label for="">${imovel.categoria}</label>
                    <label for="">${imovel.status}</label>
                </div>
            </div>
        `;
    }
}

function mudarOrdem() {
    const seta = document.getElementById("seta");
    seta.textContent = seta.textContent === "⬇️" ? "⬆️" : "⬇️";
    carregarAnuncios();
}

function filtrar() {
    carregarAnuncios();
}

function abrirCadastro(imovel_id) {
    sessionStorage.setItem("imovel-id-estoque", imovel_id);
    window.location.href = "cadastro-imovel.html";
}

window.addEventListener("DOMContentLoaded", () => {
    carregarAnuncios();
});
