import { listarUsuarios } from "./modules/usuarios.js";
import { listarImoveis, destacarImovel, excluirImovel } from "./modules/imoveis.js";
import { formatarValor } from "./modules/utils.js";

let imoveisCache = [];
let proprietariosCache = [];
let usuariosFiltrados = [];
let usuariosCache = [];
let imoveisFiltrados = [];
let filtroUsuario = "";
let seta = "";

window.filtroOrdenado = filtroOrdenado;
window.filtrar = filtrar;
window.trocarCadastro = trocarCadastro;
window.openMenu = openMenu;
window.selecionarTodos = selecionarTodos;
window.tornarDestaqueMultiplos = tornarDestaqueMultiplos;
window.apagarMultiplos = apagarMultiplos;
window.abrirMultiplos = abrirMultiplos;
window.apagarImovel = apagarImovel;
window.abrirCadastro = abrirCadastro;
window.duplicarImovel = duplicarImovel;
window.destacarImovel = destacarImovel;
window.montarOpcoes = montarOpcoes;
window.abrirAnuncio = abrirAnuncio;
window.apagarPessoa = apagarPessoa;

function abrirCadastro(id) {
    if (id) {
        window.open(`cadastro-imovel.html?id=${id}`, '_blank');
    }
}

function abrirMultiplos(event) {
    const checkboxes = document.querySelectorAll(".checkbox-selecionar:checked");
    const lista_ids = Array.from(checkboxes).map(checkbox => {
        const resultado = checkbox.closest(".resultado");
        return resultado ? resultado.getAttribute("href").split("=")[1] : null;
    }).filter(id => id !== null);
    for (const id of lista_ids) {
        window.open(`cadastro-imovel.html?id=${id}`, '_blank');
    }
}

function apagarMultiplos(event) {
    confirmar = confirm("Tem certeza que deseja excluir os imóveis selecionados?");
    if (!confirmar) {
        return;
    }
    const checkboxes = document.querySelectorAll(".checkbox-selecionar:checked");
    const lista_ids = Array.from(checkboxes).map(checkbox => {
        const resultado = checkbox.closest(".resultado");
        return resultado ? resultado.getAttribute("href").split("=")[1] : null;
    }).filter(id => id !== null);
    excluirImovel(lista_ids);
}

async function tornarDestaqueMultiplos() {

    const checkboxes = document.querySelectorAll(".checkbox-selecionar:checked");
    const lista_ids = Array.from(checkboxes).map(checkbox => {
        const resultado = checkbox.closest(".resultado");
        return resultado ? resultado.getAttribute("href").split("=")[1] : null;
    }).filter(id => id !== null);

    if (lista_ids.length === 0) {
        let div = document.querySelector(".mensagem");
        if (!div) {
            div = document.createElement("div");
            div.classList.add("mensagem");
            document.body.appendChild(div);
        }
        div.classList.add("erro");
        div.classList.remove("sucesso");
        div.innerText = "Nenhum imóvel selecionado.";
        div.style.display = "flex";
        setTimeout(() => {
            div.style.display = "none";
        }, 3000);
        return;
    }

    destacarImovel(lista_ids);
}

async function filtroOrdenado() {
    seta = event.target;

    if (event.target.tagName == "TH" || event.target.tagName == "th") {
        seta = event.target.querySelector("#seta");
    } else if (event.target.tagName == "I" || event.target.tagName == "i") {
        seta = event.target;
    }

    if (!seta) {
        console.warn("Elemento de seta não encontrado.");
        return;
    }

    if (seta.classList.contains("fa-angle-up")) {
        document.querySelectorAll(".fa-angle-down").length === 1 ? document.querySelectorAll(".fa-angle-down").forEach((elemento) => {
            elemento.classList.remove("fa-angle-down");
            elemento.classList.add("fa-angle-up");
        }) : null;
    }

    if (seta.classList.contains("fa-arrow-down")) {
        seta.classList.remove("fa-arrow-down");
        seta.classList.add("fa-arrow-up");
    } else if (seta.classList.contains("fa-arrow-up")) {
        seta.classList.remove("fa-arrow-up");
        seta.classList.add("fa-arrow-down");

    } else {
        return;
    }

    document.querySelectorAll(".active").forEach((elemento) => {
        elemento.classList.remove("active");
    });

    seta.classList.add("active");

    filtrar();
}

async function filtrar() {
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
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.cep.toString().includes(valor.replace(/-/g, '').replace(/\_/g, '').trim()));
                    break;
                case "numero":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.numero.toString().includes(valor.trim()));
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
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_quartos >= parseInt(valor.trim()));
                    break;
                case "quantidade_maxima_quartos":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_quartos <= parseInt(valor.trim()));
                    break;
                case "quantidade_minima_banheiros":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_banheiros >= parseInt(valor.trim()));
                    break;
                case "quantidade_maxima_banheiros":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_banheiros <= parseInt(valor.trim()));
                    break;
                case "quantidade_minima_vagas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_vagas >= parseInt(valor.trim()));
                    break;
                case "quantidade_maxima_vagas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_vagas <= parseInt(valor.trim()));
                    break;
                case "quantidade_minima_salas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_salas >= parseInt(valor.trim()));
                    break;
                case "quantidade_maxima_salas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_salas <= parseInt(valor.trim()));
                    break;
                case "quantidade_minima_varandas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_varandas >= parseInt(valor.trim()));
                    break;
                case "quantidade_maxima_varandas":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_varandas <= parseInt(valor.trim()));
                    break;
                case "valor_minimo_aluguel":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_aluguel >= parseFloat(valor.replace(/[^0-9,]/g, '').replace(',', '.').trim()));
                    break;
                case "valor_maximo_aluguel":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_aluguel <= parseFloat(valor.replace(/[^0-9,]/g, '').replace(',', '.').trim()));
                    break;
                case "valor_minimo_venda":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_venda >= parseFloat(valor.replace(/[^0-9,]/g, '').replace(',', '.').trim()));
                    break;
                case "valor_maximo_venda":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_venda <= parseFloat(valor.replace(/[^0-9,]/g, '').replace(',', '.').trim()));
                    break;
                case "area_minima_total":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.area_total >= parseFloat(valor.replace("m²", '').replace(',', '.').trim()));
                    break;
                case "area_maxima_total":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.area_total <= parseFloat(valor.replace("m²", '').replace(',', '.').trim()));
                    break;
                case "area_minima_privativa":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.area_privativa >= parseFloat(valor.replace("m²", '').replace(',', '.').trim()));
                    break;
                case "area_maxima_privativa":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.area_privativa <= parseFloat(valor.replace("m²", '').replace(',', '.').trim()));
                    break;
                case "valor_minimo_condominio":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_condominio >= parseFloat(valor.replace(/[^0-9.,]/g, '').replace(',', '.').replace("R$", '').trim()));
                    break;
                case "valor_maximo_condominio":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_condominio <= parseFloat(valor.replace(/[^0-9.,]/g, '').replace(',', '.').replace("R$", '').trim()));
                    break;
                case "valor_minimo_iptu":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_iptu >= parseFloat(valor.replace(/[^0-9.,]/g, '').replace(',', '.').replace("R$", '').trim()));
                    break;
                case "valor_maximo_iptu":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.valor_iptu <= parseFloat(valor.replace(/[^0-9.,]/g, '').replace(',', '.').replace("R$", '').trim()));
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
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.andar >= parseInt(valor.trim()));
                    break;
                case "maximo_andar":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.andar <= parseInt(valor.trim()));
                    break;
                case "numero_imovel":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.numero.toString().includes(valor.trim()));
                    break;
                case "bloco":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.endereco?.bloco.toLowerCase().includes(valor.toLowerCase()));
                    break;
                case "quantidade_minima_suites":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_suites >= parseInt(valor.trim()));
                    break;
                case "quantidade_maxima_suites":
                    imoveisFiltrados = imoveisFiltrados.filter((imovel) => imovel.quantidade_suites <= parseInt(valor.trim()));
                    break;
                default:
                    break;
            }
        });

        let containerFiltrosApartamento = document.getElementById("container-info-imovel");
        if (containerFiltrosApartamento) {
            let filtros = containerFiltrosApartamento.querySelectorAll("input[type='checkbox']");
            let filtrosSelecionados = Array.from(filtros).filter(checkbox => checkbox.checked);
            filtrosSelecionados.forEach(checkbox => {
                imoveisFiltrados = imoveisFiltrados.filter(imovel => imovel.filtros.includes(checkbox.value));
            });
        }

        let containerFiltrosCondominio = document.getElementById("container-info-condominio");
        if (containerFiltrosCondominio) {
            let filtros2 = containerFiltrosCondominio.querySelectorAll("input[type='checkbox']");
            let filtrosSelecionados2 = Array.from(filtros2).filter(checkbox => checkbox.checked);
            filtrosSelecionados2.forEach(checkbox => {
                imoveisFiltrados = imoveisFiltrados.filter(imovel => imovel.condominio.filtros.includes(checkbox.value));
            });
        }

        let seta = document.querySelector("#seta");
        let nome = document.getElementById("select-filtro") ? document.getElementById("select-filtro").value : null;

        sessionStorage.setItem("estoque-filtroSelecionado", nome);
        sessionStorage.setItem("estoque-seta", seta?.className);

        if (seta && nome) {
            switch (nome) {
                case "referencia":
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
                case "venda":
                    if (seta.classList.contains("fa-arrow-down")) {
                        imoveisFiltrados.sort((a, b) => a.valor_venda - b.valor_venda);
                    } else {
                        imoveisFiltrados.sort((a, b) => b.valor_venda - a.valor_venda);
                    }
                    break;
                case "aluguel":
                    if (seta.classList.contains("fa-arrow-down")) {
                        imoveisFiltrados.sort((a, b) => a.valor_aluguel - b.valor_aluguel);
                    } else {
                        imoveisFiltrados.sort((a, b) => b.valor_aluguel - a.valor_aluguel);
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

        let seta = document.querySelector("thead") ? document.querySelector("thead").querySelector(".active") : null;
        let nome = seta ? seta.closest("th").getAttribute("name") : null;
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
            case "vistoriador":
                carregarUsuarios("VISTORIADOR");
                break;
            case "financeiro":
                carregarUsuarios("FINANCEIRO");
                break;
            case "captador":
                carregarUsuarios("CAPTADOR");
                break;
            case "administrador":
                carregarUsuarios("ADMINISTRADOR");
                break;
            case "gerente":
                carregarUsuarios("GERENTE");
                break;
            case "corretor":
                carregarUsuarios("CORRETOR");
                break;
            case "proprietario":
                carregarUsuarios("PROPRIETARIO");
                break;
            case "todos":
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
    nav.classList.add("active");
    navbarImoveis.style.display = "none";
    navbarImoveis.classList.remove("active");
    sessionStorage.setItem("estoque-cadastroSelecionado", valor);
    switch (valor) {
        case "vistoriador":
            filtrar();
            document.querySelector("#select-cadastro").value = "vistoriador";
            break;
        case "financeiro":
            filtrar();
            document.querySelector("#select-cadastro").value = "financeiro";
            break;
        case "captador":
            filtrar();
            document.querySelector("#select-cadastro").value = "captador";
            break;
        case "administrador":
            filtrar();
            document.querySelector("#select-cadastro").value = "administrador";
            break;
        case "gerente":
            filtrar();
            document.querySelector("#select-cadastro").value = "gerente";
            break;
        case "imovel":
            filtrar();
            nav.style.display = "none";
            nav.classList.remove("active");
            navbarImoveis.style.display = "flex";
            navbarImoveis.classList.add("active");
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
        case "todos":
            filtrar();
            document.querySelector("#select-cadastro").value = "todos";
            break;
        default:
            break;
    }
}

async function apagarPessoa(usuarioID) {
    let div = document.querySelector(".mensagem");
    let mensagem = "";

    if (!div) {
        div = document.createElement("div");
        div.classList.add("mensagem");
        document.body.appendChild(div);
    }

    confirmar = confirm("Tem certeza que deseja excluir este usuário?");
    if (usuarioID && confirmar) {
        try {
            let caminho = getCaminhoRelativo("/php/api/usuarios.php?acao=apagar&id=" + usuarioID);
            const response = await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },

            })
                .then(async (response) => {
                    if (response.erro) {
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Erro ao remover usuário: " + response.erro;
                        return null;
                    }
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return await response.json();
                    } else {
                        const texto = await response.text();
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Resposta inesperada do servidor";
                        console.error("Resposta não é JSON:", texto);
                        return null;
                    }
                })
                .then(async (data) => {
                    if (data.status == "erro") {
                        div.classList.add("erro");
                        div.classList.remove("sucesso");
                        mensagem = "Erro ao excluir usuário: " + data.mensagem;
                    } else {
                        div.classList.add("sucesso");
                        div.classList.remove("erro");
                        mensagem = "Usuário excluído com sucesso: " + data.mensagem;
                        window.location.href = "estoque.html";
                    }
                })
                .catch(error => {
                    div.classList.add("erro");
                    div.classList.remove("sucesso");
                    mensagem = "Erro ao excluir usuário: " + error.message;
                });
        } catch (error) {
            div.classList.add("erro");
            div.classList.remove("sucesso");
            mensagem = "Erro ao enviar dados para exclusão do usuário: " + error.message;
        }
    }
    else {
        div.classList.add("erro");
        div.classList.remove("sucesso");
        mensagem = "Exclusão de usuário cancelada.";
        window.location.href = "estoque.html";
    }
    
    div.innerText = mensagem;
    div.style.display = "flex";

    setTimeout(() => {
        div.style.display = "none";
    }, 3000);

}


async function carregarUsuarios(tipo) {
    let dados = usuariosFiltrados;
    const section = document.getElementById("container-resultado");
    if (!section || !dados) {
        console.log("Erro: Elementos não encontrados");
        return;
    }

    if (tipo !== "") {
        if (tipo !== "TODOS") {
            dados = dados.filter(usuario => usuario.tipo === tipo);
        }

    }

    if (dados.length === 0 || !dados) {
        const divVazio = document.createElement("div");
        divVazio.id = "vazio";
        divVazio.textContent = "Nenhum usuário encontrado.";
        section.innerHTML = "";
        section.appendChild(divVazio);
        return;
    }

    let classID = document.querySelector(".set a")?.className || "fa fa-angle-up seta";
    let classNome = document.querySelectorAll(".seta")[1]?.className || "fa fa-angle-up seta";
    let classEmail = document.querySelectorAll(".seta")[2]?.className || "fa fa-angle-up seta";
    let classTelefone = document.querySelectorAll(".seta")[3]?.className || "fa fa-angle-up seta";
    let classDataCadastro = document.querySelectorAll(".seta")[4]?.className || "fa fa-angle-down seta active";
    let classDataModificacao = document.querySelectorAll(".seta")[5]?.className || "fa fa-angle-up seta";

    document.getElementById("filtro-seta").innerHTML = ``;

    document.getElementById("contador").innerHTML = `${dados.length} pessoas(s) encontrado(s)`;

    section.innerHTML = "";


    let html = `
    <table >                
                <thead>
                    <tr>
                    <th><input type="checkbox" id="checkbox-selecionar-todos" onclick="selecionarTodos(event)"></th>
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
                    
                        <tr onclick="window.location.href='cadastro-cliente.html?id=${usuario.id}'" style="cursor: pointer;">
                        <td><input type="checkbox" class="checkbox-selecionar" onclick="event.preventDefault(); event.stopPropagation(); montarOpcoes()"></td>
                            <td><a href="cadastro-cliente.html?id=${usuario.id}">${usuario.id}</a></td>
                            <td><a href="cadastro-cliente.html?id=${usuario.id}">${usuario.nome}</a></td>
                            <td><a href="cadastro-cliente.html?id=${usuario.id}">${usuario.email}</a></td>
                            <td class="telefones"><a href="cadastro-cliente.html?id=${usuario.id}">${telefonesFormatados.join('<br>')}</a></td>
                            <td><a href="cadastro-cliente.html?id=${usuario.id}">${usuario.data_cadastro ? new Date(usuario.data_cadastro?.date).toLocaleString() : ''}</a></td>
                            <td><a href="cadastro-cliente.html?id=${usuario.id}">${usuario.data_modificacao ? new Date(usuario.data_modificacao?.date).toLocaleString() : ''}</a></td>
                             <td onclick="window.location.href='cadastro-cliente.html?id=${usuario.id}'" style="cursor: pointer;"><button id="botao-editar">Editar</button></td>
                            <td onclick="apagarPessoa(${usuario.id})" style="cursor: pointer;"><button class='bt-delete'>Deletar</button></td>
                        </tr>
                    
        `;
    }
    html += `</tbody></table>`;
    section.innerHTML = html;

}

function carregarAnuncios() {
    let dados = imoveisFiltrados;
    const section = document.getElementById("container-resultado");
    const seta = document.getElementById("seta");
    if (!section || !dados) {
        console.log("Erro: Elementos não encontrados");
        return;
    }

    let classSeta = document.querySelector("#seta")?.className || "fas fa-arrow-up";

    section.innerHTML = "";

    document.getElementById("filtro-seta").innerHTML = `
         <select id="select-filtro" onchange="filtrar()">
                        <option value="referencia" ${sessionStorage.getItem("estoque-filtroSelecionado") == "referencia" ? "selected" : ""}>Referência</option>
                        <option value="categoria" ${sessionStorage.getItem("estoque-filtroSelecionado") == "categoria" ? "selected" : ""}>Categoria</option>
                        <option value="status" ${sessionStorage.getItem("estoque-filtroSelecionado") == "status" ? "selected" : ""}>Status</option>
                        <option value="cep" ${sessionStorage.getItem("estoque-filtroSelecionado") == "cep" ? "selected" : ""}>CEP</option>
                        <option value="numero" ${sessionStorage.getItem("estoque-filtroSelecionado") == "numero" ? "selected" : ""}>Número</option>
                        <option value="aluguel" ${sessionStorage.getItem("estoque-filtroSelecionado") == "aluguel" ? "selected" : ""}>Aluguel</option>
                        <option value="venda" ${sessionStorage.getItem("estoque-filtroSelecionado") == "venda" ? "selected" : ""}>Venda</option>
                        <option value="data_cadastro" ${sessionStorage.getItem("estoque-filtroSelecionado") == "data_cadastro" ? "selected" : ""}>Data de Cadastro</option>
                        <option value="data_modificacao" ${sessionStorage.getItem("estoque-filtroSelecionado") == "data_modificacao" ? "selected" : ""}>Data de Modificação</option>
                    </select>
                    <i class="${classSeta}" id="seta" flat=True onclick="filtroOrdenado()"></i>
    `;



    document.getElementById("contador").innerHTML = `${dados.length} imóveis`;

    if (dados.length === 0 || !dados) {
        const divVazio = document.createElement("div");
        divVazio.id = "vazio";
        divVazio.textContent = "Nenhum imóvel encontrado.";
        section.innerHTML = "";
        section.appendChild(divVazio);
        return;
    }

    let html = ``
    document.getElementById("contador-imoveis") && (document.getElementById("contador-imoveis").textContent = `${dados.length} ${dados.length === 1 ? 'imóvel' : 'imóveis'}`);
    for (let imovel of dados) {
        const b64 = imovel.anuncio?.imagens?.[0] || null;
        html += `
            <a class="resultado" href="cadastro-imovel.html?id=${imovel.id}">
                <input type="checkbox" class="checkbox-selecionar" onclick="event.preventDefault(); event.stopPropagation(); montarOpcoes()">
                <img src="${b64}" alt="">
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
                <li style="list-style: none;">
                    <i class="fas fa-bars" onclick="event.preventDefault(); event.stopPropagation(); openMenu(this)"></i>
                </li>
            </a>
        `;
    }
    html += `</section>`;
    section.innerHTML = html;
}

let barra = null;

function abrirAnuncio(imovelId) {
    let urlAtual = window.location.href;
    urlAtual = urlAtual.replace("estoque.html", `dados-imovel.html?id=${imovelId}`);
    window.open(urlAtual);
}

function openMenu(element) {
    if (window.event) {
        window.event.preventDefault();
        window.event.stopPropagation();
    }

    const menuExistente = document.querySelector(".menu-opcoes");
    if (menuExistente) {
        menuExistente.remove();
    }

    if (barra === element) {
        barra = null;
        return;
    }

    barra = element;
    const cardPai = element.closest('.resultado');
    const idImovel = cardPai.querySelector('.dados label').textContent.split('Ref: ')[1];

    const menu = document.createElement("div");
    menu.classList.add("menu-opcoes");
    menu.innerHTML = `
        <button onclick="abrirCadastro(null, ${idImovel})">Editar</button>
        <button onclick="apagarImovel(${idImovel})">Apagar</button>
        <button onclick="duplicarImovel(${idImovel})">Duplicar</button>
        <button onclick="destacarImovel(${idImovel})">Tornar Destaque</button>
        <button onclick="abrirAnuncio(${idImovel})">Abrir Anuncio</button>
    `;

    cardPai.appendChild(menu);

    menu.style.top = `${element.offsetTop + element.offsetHeight}px`;
    menu.style.right = `10px`;
    menu.style.left = `auto`;

    document.addEventListener("click", function handler(event) {
        if (!menu.contains(event.target) && event.target !== element) {
            menu.remove();
            barra = null;
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
    imovel.anuncio.documentos = [];
    abrirCadastro(imovel, null);
}

async function apagarImovel(imovelId) {
    confirmar = confirm("Tem certeza que deseja excluir este imóvel?");
    if (!confirmar) {
        return;
    }
    else {
        excluirImovel(imovelId);
    }
}

window.mudarLarguraSidebar = mudarLarguraSidebar;

function mudarLarguraSidebar() {
    const sidebar = document.querySelector(".active");
    if (sidebar) {
        if (getComputedStyle(sidebar).display == "flex") {
            sidebar.style.display = "none";
        } else {
            sidebar.style.display = "flex";
        }
    }

}

function montarOpcoes() {
    const checkboxes = document.querySelectorAll(".checkbox-selecionar:checked");
    const botaoApagar = document.querySelector("#apagar-multiplos");
    const botaoAbrir = document.querySelector("#abrir-multiplos");
    const botaoDestaque = document.querySelector("#destaque-multiplos");
    if (checkboxes.length > 0) {
        // const filtro = document.getElementById("h-filtro");
        // filtro.innerHTML = "";
        botaoApagar.style.display = "block";
        botaoAbrir.style.display = "block";
        botaoDestaque.style.display = "block";
    } else {
        const filtro = document.getElementById("h-filtro");
        botaoApagar.style.display = "none";
        botaoAbrir.style.display = "none";
        botaoDestaque.style.display = "none";
    }
}

function selecionarTodos() {
    const checkboxes = document.querySelectorAll(".checkbox-selecionar");
    const todosSelecionados = Array.from(checkboxes).every(checkbox => checkbox.checked);
    checkboxes.forEach(checkbox => checkbox.checked = !todosSelecionados);
}

window.addEventListener("DOMContentLoaded", async () => {
    document.addEventListener("change", montarOpcoes);

    const filtroSelecionado = sessionStorage.getItem("estoque-filtroSelecionado");
    const seta = sessionStorage.getItem("estoque-seta");

    const cadastroSelecionado = sessionStorage.getItem("estoque-cadastroSelecionado");
    if (cadastroSelecionado) {
        const selectCadastro = document.getElementById("select-cadastro");
        if (selectCadastro) {
            selectCadastro.value = cadastroSelecionado;
            trocarCadastro();
        }
    }

    if (filtroSelecionado) {
        const selectFiltro = document.getElementById("select-filtro");
        if (selectFiltro) {
            selectFiltro.value = filtroSelecionado;
        }
    }

    if (seta) {
        const setaElement = document.getElementById("seta");
        if (setaElement) {
            setaElement.className = seta;
        }
    }

    let dados = [];
    dados = await listarUsuarios();
    if (!dados || dados.length === 0) {
        const section = document.getElementById("container-pai");
        const divVazio = document.createElement("div");
        divVazio.id = "vazio";
        divVazio.textContent = "Nenhum usuário encontrado.";
        section.innerHTML = "";
        section.appendChild(divVazio);
        return;
    }
    dados.sort((a, b) => new Date(b.data_cadastro?.date) - new Date(a.data_cadastro?.date));
    usuariosCache.push(...dados);
    usuariosFiltrados = usuariosCache;

    dados = [];
    dados = await listarImoveis();
    if (dados.length === 0 || !dados) {
        const section = document.getElementById("container-pai");
        const divVazio = document.createElement("div");
        divVazio.id = "vazio";
        divVazio.textContent = "Nenhum imóvel encontrado.";
        section.innerHTML = "";
        section.appendChild(divVazio);
        return;
    }
    dados.sort((a, b) => new Date(b.data_cadastro?.date) - new Date(a.data_cadastro?.date));
    imoveisCache.push(...dados);
    imoveisFiltrados = imoveisCache;

    if (cadastroSelecionado === "imovel" || !cadastroSelecionado) {
        carregarAnuncios();
    } else if (cadastroSelecionado) {
        carregarUsuarios(cadastroSelecionado.toUpperCase());
    }

    document.querySelectorAll(".sidebar-anuncios").forEach((element) => {
        element.querySelectorAll("input, textarea").forEach((input) => {
            input.addEventListener("input", filtrar);
        });
        element.querySelectorAll("select").forEach((select) => {
            select.addEventListener("change", filtrar);
        });
        element.textarea
    });

    Inputmask("99999-999").mask("#input-cep");

    Inputmask('currency', {
        prefix: 'R$ ',
        groupSeparator: '.',
        radixPoint: ',',
        digits: 2,
        autoGroup: true,
        allowMinus: false,
        rightAlign: false,
        placeholder: '0',
        numericInput: true,
        positionCaretOnClick: "radixFocus"
    }).mask('#valor_minimo_aluguel, #valor_maximo_aluguel, #valor_minimo_venda, #valor_maximo_venda, #valor_minimo_iptu, #valor_maximo_iptu, #valor_minimo_condominio, #valor_maximo_condominio, #valor-iptu-maximo, #valor-iptu-minimo');

    Inputmask({
        alias: 'decimal',
        rightAlign: false,
        radixPoint: ',',
        groupSeparator: '.',
        autoGroup: true,
        suffix: ' m²',
        digits: 2,
        allowMinus: false,
        placeholder: '0'
    }).mask('#area_minima_privativa, #area_maxima_privativa, #area_minima_total, #area_maxima_total');
});
