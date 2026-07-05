let imoveisCache = [];
let proprietariosCache = [];
let usuariosFiltrados = [];
let usuariosCache = [];
let imoveisFiltrados = [];
let filtroUsuario = "";
let seta = "";

async function filtroOrdenado() {
    seta = event.target;

    if (event.target.tagName == "TH" || event.target.tagName == "th") {
        seta = event.target.querySelector(".seta");
    } else if (event.target.tagName == "I" || event.target.tagName == "i") {
        seta = event.target;
    }

    if (seta.classList.contains("fa-angle-up")) {
        document.querySelectorAll(".fa-angle-down").length === 1 ? document.querySelectorAll(".fa-angle-down").forEach((elemento) => {
            elemento.classList.remove("fa-angle-down");
            elemento.classList.add("fa-angle-up");
        }) : null;
    }


    if (!seta.classList.contains("seta")) {
        if (seta.classList.contains("fa-arrow-down")) {
            seta.classList.remove("fa-arrow-down");
            seta.classList.add("fa-arrow-up");
        } else if (seta.classList.contains("fa-arrow-up")) {
            seta.classList.remove("fa-arrow-up");
            seta.classList.add("fa-arrow-down");
        }
    } else if (seta.classList.contains("seta")) {
        if (seta.classList.contains("fa-angle-down")) {
            seta.classList.remove("fa-angle-down");
            seta.classList.add("fa-angle-up");
        } else if (seta.classList.contains("fa-angle-up")) {
            seta.classList.remove("fa-angle-up");
            seta.classList.add("fa-angle-down");
        }
    } else {
        return;
    }

    document.querySelectorAll(".active").forEach((elemento) => {
        elemento.classList.remove("active");
    });

    seta.classList.add("active");
    
    console.log(seta);

    filtrar();

}

async function filtrar() {
    if (!event || !event.target) {
        return;
    }

    if (document.getElementById("sidebar-imoveis").style.display !== "none") {
        imoveisFiltrados = imoveisCache;
        document.getElementById("sidebar-imoveis").querySelectorAll("input, select, textarea").forEach((elemento) => {
            if (!elemento.name || !elemento.value || elemento.value === "") {
                return;
            }
            const nome = elemento.name;
            const valor = elemento.value;

            switch (nome) {
                case "ref":
                case "id":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.id.toString().includes(valor));
                    break;
                case "categoria":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.categoria.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "status":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.status.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "cep":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.cep.toString().includes(valor));
                    break;
                case "numero":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.numero.toString().includes(valor));
                    break;
                case "data_cadastro":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.data_cadastro?.date.toString().includes(valor));
                    break;
                case "data_modificacao":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.data_modificacao?.date.toString().includes(valor));
                    break;
                case "situacao":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.situacao.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "estado_imovel":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.estado.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "ocupacao":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.ocupacao.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "quantidade_minima_quartos":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_quartos >= parseInt(valor));
                    break;
                case "quantidade_maxima_quartos":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_quartos <= parseInt(valor));
                    break;
                case "quantidade_minima_banheiros":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_banheiros >= parseInt(valor));
                    break;
                case "quantidade_maxima_banheiros":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_banheiros <= parseInt(valor));
                    break;
                case "quantidade_minima_vagas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_vagas >= parseInt(valor));
                    break;
                case "quantidade_maxima_vagas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_vagas <= parseInt(valor));
                    break;
                case "quantidade_minima_salas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_salas >= parseInt(valor));
                    break;
                case "quantidade_maxima_salas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_salas <= parseInt(valor));
                    break;
                case "quantidade_minima_varandas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_varandas >= parseInt(valor));
                    break;
                case "quantidade_maxima_varandas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_varandas <= parseInt(valor));
                    break;
                case "valor_minimo_aluguel":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_aluguel >= parseFloat(valor));
                    break;
                case "valor_maximo_aluguel":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_aluguel <= parseFloat(valor));
                    break;
                case "valor_minimo_venda":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_venda >= parseFloat(valor));
                    break;
                case "valor_maximo_venda":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_venda <= parseFloat(valor));
                    break;
                case "area_minima_total":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.area_total >= parseFloat(valor));
                    break;
                case "area_maxima_total":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.area_total <= parseFloat(valor));
                    break;
                case "area_minima_privativa":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.area_privativa >= parseFloat(valor));
                    break;
                case "area_maxima_privativa":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.area_privativa <= parseFloat(valor));
                    break;
                case "valor_minimo_condominio":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_condominio >= parseFloat(valor));
                    break;
                case "valor_maximo_condominio":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_condominio <= parseFloat(valor));
                    break;
                case "valor_minimo_iptu":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_iptu >= parseFloat(valor));
                    break;
                case "valor_maximo_iptu":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_iptu <= parseFloat(valor));
                    break;
                case "rua":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.rua.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "bairro":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.bairro.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "cidade":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.cidade.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "uf":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.uf.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "minimo_andar":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.andar >= parseInt(valor));
                    break;
                case "maximo_andar":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.andar <= parseInt(valor));
                    break;
                case "numero_imovel":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.numero.toString().includes(valor));
                    break;
                case "bloco":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.bloco.toLowerCase().includes(valor.toLowerCase()));
                    break;
                default:
                    break;
            }
        });

        seta = document.querySelector("#seta");
        nome = document.getElementById("select-filtro") ? document.getElementById("select-filtro").value : null;

        if (seta && nome) {
            switch (nome) {
                case "ref":
                case "id":
                    if (seta.classList.contains("fa-arrow-down")) {
                        imoveisFiltrados.sort((a, b) => a.id - b.id);
                    } else {
                        imoveisFiltrados.sort((a, b) => b.id - a.id);
                    }
                    break;
                case "categoria":
                    if (seta.classList.contains("fa-arrow-down")) {
                        imoveisFiltrados.sort((a, b) => a.categoria.localeCompare(b.categoria));
                    } else {
                        imoveisFiltrados.sort((a, b) => b.categoria.localeCompare(a.categoria));
                    }
                    break;
                case "status":
                    if (seta.classList.contains("fa-arrow-down")) {
                        imoveisFiltrados.sort((a, b) => a.status.localeCompare(b.status));
                    } else {
                        imoveisFiltrados.sort((a, b) => b.status.localeCompare(a.status));
                    }
                    break;
                case "cep":
                    if (seta.classList.contains("fa-arrow-down")) {
                        imoveisFiltrados.sort((a, b) => a.endereco?.cep - b.endereco?.cep);
                    } else {
                        imoveisFiltrados.sort((a, b) => b.endereco?.cep - a.endereco?.cep);
                    }
                    break;
                case "numero":
                    if (seta.classList.contains("fa-arrow-down")) {
                        imoveisFiltrados.sort((a, b) => a.endereco?.numero - b.endereco?.numero);
                    } else {
                        imoveisFiltrados.sort((a, b) => b.endereco?.numero - a.endereco?.numero);
                    }
                    break;
                case "data_cadastro":
                    if (seta.classList.contains("fa-arrow-down")) {
                        imoveisFiltrados.sort((a, b) => new Date(a.data_cadastro?.date) - new Date(b.data_cadastro?.date));
                    } else {
                        imoveisFiltrados.sort((a, b) => new Date(b.data_cadastro?.date) - new Date(a.data_cadastro?.date));
                    }
                    break;
                case "data_modificacao":
                    if (seta.classList.contains("fa-arrow-down")) {
                        imoveisFiltrados.sort((a, b) => new Date(a.data_modificacao?.date) - new Date(b.data_modificacao?.date));
                    } else {
                        imoveisFiltrados.sort((a, b) => new Date(b.data_modificacao?.date) - new Date(a.data_modificacao?.date));
                    }
                    break;
                default:
                    break;
            }
        }
        carregarAnuncios();
    } else {
        usuariosFiltrados = usuariosCache;
        document.getElementById("sidebar-pessoas").querySelectorAll("input, select, textarea").forEach((elemento) => {
            if (!elemento.name || !elemento.value || elemento.value === "") {
                return;
            }
            const nome = elemento.name;
            const valor = elemento.value;

            switch (nome) {
                case "id":
                    usuariosFiltrados = usuariosFiltrados.filter((usuario) => usuario.id.toString().includes(valor));
                    break;
                case "nome":
                    usuariosFiltrados = usuariosFiltrados.filter((usuario) => usuario.nome.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "email":
                    usuariosFiltrados = usuariosFiltrados.filter((usuario) => usuario.email.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "telefone":
                    usuariosFiltrados = usuariosFiltrados.filter((usuario) => usuario.telefones[0].some(telefone => telefone.includes(valor)));
                    break;
                case "data_cadastro":
                    usuariosFiltrados = usuariosFiltrados.filter((usuario) => usuario.data_cadastro?.toString().includes(valor));
                    break;
                case "data_modificacao":
                    usuariosFiltrados = usuariosFiltrados.filter((usuario) => usuario.data_modificacao?.toString().includes(valor));
                    break;
                default:
                    break;
            }
        });

        seta = document.querySelector("thead") ? document.querySelector("thead").querySelector(".active") : null;
        nome = seta ? seta.closest("th").getAttribute("name") : null;
        if (seta && nome) {
            switch (nome) {
                case "id":
                    if (seta.classList.contains("fa-angle-down")) {
                        usuariosFiltrados.sort((a, b) => a.id - b.id);
                    } else {
                        usuariosFiltrados.sort((a, b) => b.id - a.id);
                    }
                    break;
                case "nome":
                    if (seta.classList.contains("fa-angle-down")) {
                        usuariosFiltrados.sort((a, b) => a.nome.localeCompare(b.nome));
                    } else {
                        usuariosFiltrados.sort((a, b) => b.nome.localeCompare(a.nome));
                    }
                    break;
                case "email":
                    if (seta.classList.contains("fa-angle-down")) {
                        usuariosFiltrados.sort((a, b) => a.email.localeCompare(b.email));
                    } else {
                        usuariosFiltrados.sort((a, b) => b.email.localeCompare(a.email));
                    }
                    break;
                case "telefone":
                    if (seta.classList.contains("fa-angle-down")) {
                        usuariosFiltrados.sort((a, b) => a.telefones?.toString().localeCompare(b.telefones?.toString()));
                    } else {
                        usuariosFiltrados.sort((a, b) => b.telefones?.toString().localeCompare(a.telefones?.toString()));
                    }
                    break;
                case "data_cadastro":
                    if (seta.classList.contains("fa-angle-down")) {
                        usuariosFiltrados.sort((a, b) => new Date(a.data_cadastro?.date) - new Date(b.data_cadastro?.date));
                    } else {
                        usuariosFiltrados.sort((a, b) => new Date(b.data_cadastro?.date) - new Date(a.data_cadastro?.date));
                    }
                    break;
                case "data_modificacao":
                    if (seta.classList.contains("fa-angle-down")) {
                        usuariosFiltrados.sort((a, b) => new Date(a.data_modificacao?.date) - new Date(b.data_modificacao?.date));
                    } else {
                        usuariosFiltrados.sort((a, b) => new Date(b.data_modificacao?.date) - new Date(a.data_modificacao?.date));
                    }
                    break;
                default:
                    break;
            }

        }
        const valor = document.querySelector("#select-cadastro").value;
        if (!valor) {
            return;
        }

        switch (valor) {
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

}

function trocarCadastro() {
    const valor = event.target.value || document.querySelector("#select-cadastro").value;
    if (!valor) {
        return;
    }
    const nav = document.getElementById("sidebar-pessoas");
    const navbarImoveis = document.getElementById("sidebar-imoveis");
    nav.style.display = "flex";
    navbarImoveis.style.display = "none";
    switch (valor) {
        case "imovel":
            filtrar();
            nav.style.display = "none";
            navbarImoveis.style.display = "flex";
            navbarImoveis.querySelector("#select-cadastro").value = "imovel";
            break;
        case "cliente":
            filtrar();
            document.querySelector("#select-cadastro").value = "cliente";
            break;
        case "corretor":
            filtrar();
            document.querySelector("#select-cadastro").value = "corretor";
            break;
        case "proprietario":
            filtrar();
            document.querySelector("#select-cadastro").value = "proprietario";
            break;
        case "todos usuarios":
            filtrar();
            document.querySelector("#select-cadastro").value = "";
            break;
        default:
            break;
    }
}

async function carregarUsuarios(tipo) {
    let dados = usuariosFiltrados.length ? usuariosFiltrados : usuariosCache;
    const section = document.getElementById("container-pai");
    if (!section || !dados) {
        console.log("Erro: Elementos não encontrados");
        return;
    }
    dados = dados.filter(usuario => usuario.tipo === tipo);

    if (dados.length === 0) {
        alert("Nenhum usuário encontrado para o tipo selecionado.");
        return;
    }

    let classID = document.querySelector(".seta")?.className || "fa fa-angle-up seta";
    let classNome = document.querySelectorAll(".seta")[1]?.className || "fa fa-angle-up seta";
    let classEmail = document.querySelectorAll(".seta")[2]?.className || "fa fa-angle-up seta";
    let classTelefone = document.querySelectorAll(".seta")[3]?.className || "fa fa-angle-up seta";
    let classDataCadastro = document.querySelectorAll(".seta")[4]?.className || "fa fa-angle-down seta active";
    let classDataModificacao = document.querySelectorAll(".seta")[5]?.className || "fa fa-angle-up seta";

    section.innerHTML = "";

    html = `
    <table class="resultado">                
                <thead>
                    <tr>
                        <th onclick="filtroOrdenado(event)" style="cursor: pointer;" name="id">ID <i class="${classID}"></i></th>
                        <th onclick="filtroOrdenado(event)" style="cursor: pointer;" name="nome">Nome <i class="${classNome}"></i></th>
                        <th onclick="filtroOrdenado(event)" style="cursor: pointer;" name="email">Email <i class="${classEmail}"></i></th>
                        <th onclick="filtroOrdenado(event)" style="cursor: pointer;" name="telefone">Telefone <i class="${classTelefone}"></i></th>
                        <th onclick="filtroOrdenado(event)" style="cursor: pointer;" name="data_cadastro">Data de Cadastro <i class="${classDataCadastro}"></i></th>
                        <th onclick="filtroOrdenado(event)" style="cursor: pointer;" name="data_modificacao">Data de Modificação <i class="${classDataModificacao}"></i></th>
                    </tr>
                </thead>
                <tbody>
    `;

    for (let usuario of dados) {

        let telefonesFormatados = usuario.telefones[0].map(telefone => {
            if (telefone.length === 13) {
                return `+${telefone.slice(0, 2)} (${telefone.slice(2, 4)}) ${telefone.slice(4, 9)}-${telefone.slice(9)}`;
            } else if (telefone.length === 11) {
                return `(${telefone.slice(0, 2)}) ${telefone.slice(2, 7)}-${telefone.slice(7)}`;
            }
            return telefone;
        });

        html += `
                    <tr>
                        <td>${usuario.id}</td>
                        <td>${usuario.nome}</td>
                        <td>${usuario.email}</td>
                        <td class="telefones">${telefonesFormatados.join('<br>')}</td>
                        <td>${usuario.data_cadastro ? new Date(usuario.data_cadastro?.date).toLocaleDateString() : ''}</td>
                        <td>${usuario.data_modificacao ? new Date(usuario.data_modificacao?.date).toLocaleDateString() : ''}</td>
                    </tr>
        `;
    }
    html += `</tbody></table>`;
    section.innerHTML = html;

}

function carregarAnuncios() {
    let dados = imoveisFiltrados.length ? imoveisFiltrados : imoveisCache;
    const section = document.getElementById("container-pai");
    const seta = document.getElementById("seta");
    if (!section || !dados) {
        console.log("Erro: Elementos não encontrados");
        return;
    }

    let classSeta = document.querySelector("#seta")?.className || "fas fa-arrow-up";

    section.innerHTML = "";

    html = `
    <div id="h-filtro">
                <select id="select-filtro" onchange="filtrar()">
                    <option value="referencia">Referência</option>
                    <option value="categoria">Categoria</option>
                    <option value="status">Status</option>
                    <option value="cep">CEP</option>
                    <option value="numero">Número</option>
                    <option value="aluguel">Aluguel</option>
                    <option value="venda">Venda</option>
                    <option value="data_cadastro" selected>Data de Cadastro</option>
                    <option value="data_modificacao">Data de Modificação</option>
                </select>
                <i class="${classSeta}" id="seta" flat=True onclick="filtroOrdenado()"></i>
                <button id="selecionar-todos" onclick="selecionarTodos()">Selecionar Todos</button>
                <button id="abrir-multiplos" style="display: none;" onclick="abrirMultiplos()">Abrir</button>
                <button id="apagar-multiplos" style="display: none;" onclick="apagarMultiplos()">Apagar</button>
                <p id="contador-imoveis">${dados.length} imóveis</p>
            </div>
            <section id="container-resultado">
    `
    document.getElementById("contador-imoveis") && (document.getElementById("contador-imoveis").textContent = `${dados.length} ${dados.length === 1 ? 'imóvel' : 'imóveis'}`);
    for (let imovel of dados) {
        const b64 = imovel.anuncio?.imagens?.[0] || null;
        html += `
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
                    <label for="">Data de Modificação: ${imovel.data_modificacao ? new Date(imovel.data_modificacao?.date).toLocaleDateString() : ''}</label>
                </div>
                <li style="list-style: none;">
                    <i class="fas fa-bars" onclick="openMenu(this)"></i>
                </li>
            </div>
        `;
    }
    html += `</section>`;
    section.innerHTML = html;
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


window.addEventListener("DOMContentLoaded", async () => {
    let dados = [];
    if (usuariosCache.length === 0) {
        dados = await listarUsuarios();
        dados.sort((a, b) => new Date(b.data_cadastro?.date) - new Date(a.data_cadastro?.date));
        usuariosCache.push(...dados);
    } else {
        dados = usuariosCache;
    }


    dados = [];
    if (imoveisCache.length === 0) {
        dados = await listarImoveis();
        dados.sort((a, b) => new Date(b.data_cadastro?.date) - new Date(a.data_cadastro?.date));
        imoveisCache.push(...dados);
    } else {
        dados = imoveisCache;
    }


    carregarAnuncios();

    document.querySelectorAll(".sidebar-anuncios").forEach((element) => {
        element.querySelectorAll("input").forEach((input) => {
            input.addEventListener("input", filtrar);
        });
        element.querySelectorAll("select").forEach((select) => {
            select.addEventListener("change", filtrar);
        });
    });
});
