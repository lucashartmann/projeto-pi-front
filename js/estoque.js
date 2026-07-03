const imoveisCache = [];
const proprietariosCache = [];
const usuariosCache = [];

function trocarCadastro() {
    const valor = event.target.value;
    console.log("Valor selecionado:", valor);
    switch (valor) {
        case "imovel":
            console.log("Carregando anúncios...");
            carregarAnuncios();
            break;
        case "cliente":
            carregarUsuarios("CLIENTE");
            break;
        case "corretor":
            carregarUsuarios("CORRETOR");
            break;
        case "proprietario":
            carregarProprietarios();
            break;
        case "todos usuarios":
            carregarUsuarios("TODOS");
            break;
        default:
            break;
    }
}


async function carregarUsuarios(tipo) {
    let dados = [];
    if (usuariosCache.length === 0) {
        dados = await listarUsuarios();
        usuariosCache.push(...dados);
    } else {
        dados = usuariosCache;
    }
    const section = document.getElementById("container-pai");
    const seta = document.getElementById("seta");
    const filtro = document.getElementById("select-filtro").value;
    if (!section || !dados) {
        console.log("Erro: Elementos não encontrados");
        return;
    }
    console.log(dados);
    dados = dados.filter(usuario => usuario.tipo === tipo);
    section.innerHTML = "";
    // document.getElementById("contador-imoveis").textContent = `${dados.length} ${dados.length === 1 ? 'usuário' : 'usuários'}`;
    // <input type="checkbox" class="checkbox-selecionar" onclick="montarOpcoes()">
    //             <div class="dados" onclick="abrirCadastro(null, ${usuario.id})"></div>
    section.innerHTML = `
    <table class="resultado">                
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Data de Cadastro</th>
                        <th>Data de Modificação</th>
                    </tr>
                </thead>
    `;
    for (let usuario of dados) {
        section.innerHTML += `
                <tbody>
                    <tr>
                        <td>${usuario.id}</td>
                        <td>${usuario.nome}</td>
                        <td>${usuario.email}</td>
                        <td>${usuario.telefone}</td>
                        <td>${new Date(usuario.data_cadastro?.date).toLocaleDateString()}</td>
                        <td>${usuario.data_modificacao ? new Date(usuario.data_modificacao?.date).toLocaleDateString() : 'N/A'}</td>
                    </tr>
                </tbody>
            </table>
        `;
    }
}

async function carregarAnuncios() {
    let dados = [];
    if (imoveisCache.length === 0) {
        dados = await listarImoveis();
        imoveisCache.push(...dados);
    } else {
        dados = imoveisCache;
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
    document.getElementById("contador-imoveis").textContent = `${dados.length} ${dados.length === 1 ? 'imóvel' : 'imóveis'}`;
    for (let imovel of dados) {

        const b64 = imovel.anuncio?.imagens?.[0] || null;
        // console.log(b64);
        section.innerHTML += `
            <div class="resultado">
                <input type="checkbox" class="checkbox-selecionar" onclick="montarOpcoes()">
                <img src="${b64}" alt="">
                <div class="dados" onclick="abrirCadastro(null, ${imovel.id})">
                    <label>Ref: ${imovel.id}</label>
                    <label for="">Rua: ${imovel.endereco?.rua}, ${imovel.endereco?.numero}, ${imovel.endereco?.bairro}, ${imovel.endereco?.cep}</label>
                    <label for="">Categoria: ${imovel.categoria}</label>
                    <label for="">Status: ${imovel.status}</label>
                    <label for="">Aluguel: ${formatarValor(imovel.valor_aluguel)}</label>
                    <label for="">Venda: ${formatarValor(imovel.valor_venda)}</label>
                    <label for="">Data de Cadastro: ${new Date(imovel.data_cadastro?.date).toLocaleDateString()}</label>
                    <label for="">Data de Modificação: ${imovel.data_modificacao ? new Date(imovel.data_modificacao?.date).toLocaleDateString() : 'N/A'}</label>
                </div>
                <li style="list-style: none;">
                    <i class="fas fa-bars" onclick="openMenu(this)"></i>
                </li>
            </div>
        `;
    }
}

let barra = null;

function openMenu(element) {
    if (document.querySelector(".menu-opcoes")) {
        document.querySelector(".menu-opcoes").remove();
    }

    if (barra === element) {
        barra = null;
        document.querySelector(".menu-opcoes").remove();
        return;
    }

    barra = element;
    const menu = document.createElement("div");
    menu.classList.add("menu-opcoes");
    menu.innerHTML = `
        <button onclick="abrirCadastro(null, ${element.closest('.resultado').querySelector('.dados label').textContent.split('Ref: ')[1]})">Editar</button>
        <button onclick="apagarImovel(${element.closest('.resultado').querySelector('.dados label').textContent.split('Ref: ')[1]})">Apagar</button>
        <button onclick="duplicarImovel(${element.closest('.resultado').querySelector('.dados label').textContent.split('Ref: ')[1]})">Duplicar</button>
    `;
    document.body.appendChild(menu);
    let posicao = element.getBoundingClientRect();
    menu.style.top = `${posicao.bottom}px`;
    menu.style.left = `${posicao.left - 320}px`;
    // menu.style.top = `${rect.bottom + window.scrollY}px`;
    // menu.style.left = `${rect.left + window.scrollX}px`;
    document.addEventListener("click", function handler(event) {
        if (!menu.contains(event.target) && event.target !== element) {
            menu.remove();
            document.removeEventListener("click", handler);
        }
    });
}

async function duplicarImovel(imovelId) {
    const imovel = imoveisCache.find(imovel => imovel.id == imovelId);
    imovel.id = null;
    imovel.data_cadastro = null;
    imovel.data_modificacao = null;
    imovel.endereco.complemento = null;
    // imovel.anuncio.imagens = [];
    imovel.anuncio.documentos = [];
    // imovel.anuncio.videos = [];
    abrirCadastro(imovel, null);
}

async function apagarImovel(imovelId) {
    confirmar = confirm("Tem certeza que deseja excluir este imóvel?");
    if (imovelId && confirmar) {
        try {
            let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=apagar_imovel&id=" + imovelId);
            const response = await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },

            })
                .then(async (response) => {
                    if (response.erro) {
                        alert("Erro ao remover imóvel: " + response.erro);
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
                        console.log("Imóvel excluído com sucesso:", data);
                        window.location.href = "estoque.html";
                    }
                })
                .catch(error => {
                    console.error("Erro ao excluir imóvel:", error);
                });
        } catch (error) {
            console.error("Erro ao enviar dados para exclusão do imóvel:", error);
        }
    }
    else {
        // alert("Nenhum imóvel selecionado para exclusão!");
        window.location.href = "estoque.html";
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


function abrirCadastro(imovel = null, id = null) {
    if (id && !imovel) {
        imovel = imoveisCache.find(imovel => imovel.id == id);
    } else if (!imovel && !id) {
        imovel = null;
        return
    }
    sessionStorage.setItem("imovel", JSON.stringify(imovel));
    window.location.href = "cadastro-imovel.html";
}

function pesquisar(event) {
    const termo = event.target.value.toLowerCase();
    let contador = 0;
    const imoveis = document.querySelectorAll(".resultado");
    imoveis.forEach(anuncio => {
        for (const label of anuncio.querySelectorAll("label")) {
            if (label.textContent.toLowerCase().includes(termo)) {
                anuncio.style.display = "flex";
                contador++;
                break;
            } else {
                anuncio.style.display = "none";
                // contador =- 1;
                continue;
            }
        }
    });
    document.getElementById("contador-imoveis").textContent = `${contador} ${contador === 1 ? 'imóvel' : 'imóveis'}`;
}

window.addEventListener("DOMContentLoaded", () => {
    carregarAnuncios();
});
