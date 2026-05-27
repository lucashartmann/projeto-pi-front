const imoveis_cache = [];

async function carregarAnuncios() {
    let dados = [];
    if (imoveis_cache.length === 0) {
        dados = await listarImoveis();
        imoveis_cache.push(...dados);
    } else {
        dados = imoveis_cache;
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
                dados.sort((a, b) => new Date(a.data_cadastro?.date) - new Date(b.data_cadastro?.date));
            } else {
                dados.sort((a, b) => new Date(b.data_cadastro?.date) - new Date(a.data_cadastro?.date));
            }
            break;
        case "data_modificacao":
            if (seta.textContent === "⬇️") {
                dados.sort((a, b) => new Date(a.data_modificacao?.date) - new Date(b.data_modificacao?.date));
            } else {
                dados.sort((a, b) => new Date(b.data_modificacao?.date) - new Date(a.data_modificacao?.date));
            }
            break;
        default:
            break;
    }


    section.innerHTML = "";
    for (let imovel of dados) {
        console.log(imovel.data_cadastro);
        const b64 = imovel.anuncio?.imagens?.[0] || null;
        section.innerHTML += `
            <div class="resultado">
                <input type="checkbox" class="checkbox-selecionar" onclick="montarOpcoes()">
                <img src="${b64}" alt="">
                <div class="dados" onclick="abrirCadastro(${imovel.id})">
                    <label>Ref: ${imovel.id}</label>
                    <label for="">Rua: ${imovel.endereco?.rua}, ${imovel.endereco?.numero}, ${imovel.endereco?.bairro}, ${imovel.endereco?.cep}</label>
                    <label for="">Categoria: ${imovel.categoria}</label>
                    <label for="">Status: ${imovel.status}</label>
                    <label for="">Aluguel: R$ ${imovel.valor_aluguel}</label>
                    <label for="">Venda: R$ ${imovel.valor_venda}</label>
                    <label for="">Data de Cadastro: ${new Date(imovel.data_cadastro?.date).toLocaleDateString()}</label>
                    <label for="">Data de Modificação: ${new Date(imovel.data_modificacao?.date).toLocaleDateString() || 'N/A'}</label>
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

function montarOpcoes() {
    const checkboxes = document.querySelectorAll(".checkbox-selecionar:checked");
    const botaoApagar = filtro.querySelector("#apagar-multiplos");
    const botaoAbrir = filtro.querySelector("#abrir-multiplos");
    if (checkboxes.length > 0) {
        const filtro = document.getElementById("h-filtro");
        filtro.innerHTML = "";
        botaoApagar.style.display = "block";
        botaoAbrir.style.display = "block";     
    } else {
        const filtro = document.getElementById("h-filtro");
        botaoApagar.style.display = "none";
        botaoAbrir.style.display = "none";
    }
}

function selecionarTodos() {
    const checkboxes = document.querySelectorAll(".checkbox-selecionar");
    const todosSelecionados = Array.from(checkboxes).every(checkbox => checkbox.checked);
    checkboxes.forEach(checkbox => checkbox.checked = !todosSelecionados);
    montarOpcoes();
}

function filtrar() {
    carregarAnuncios();
}

function abrirCadastro(imovel_id) {
    sessionStorage.setItem("imovel_id_estoque", imovel_id);
    window.location.href = "cadastro-imovel.html";
}

function pesquisar(event) {
    const termo = event.target.value.toLowerCase();

    const imoveis = document.querySelectorAll(".resultado");
    imoveis.forEach(anuncio => {
        for (const label of document.querySelectorAll(".resultado .dados label")) {
            if (label.textContent.toLowerCase().includes(termo)) {
                anuncio.style.display = "flex";
                return;
            } else {
                anuncio.style.display = "none";
            }
        }
    });
}

window.addEventListener("DOMContentLoaded", () => {
    carregarAnuncios();
});
