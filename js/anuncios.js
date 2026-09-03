import { curtirImovel, listarImoveisFavoritados } from "./modules/usuario.js";
import { formatarValor } from "./modules/utils.js";
import { listarImoveisDisponiveis } from "./modules/imoveis.js";

let imoveisCache = [];
let imoveisFiltrados = [];
let seta = "";
var favoritos = false;

window.curtirImovel = curtirImovel;
window.filtrar = filtrar;
window.filtroOrdenado = filtroOrdenado;
window.listarImoveisFavoritados = listarImoveisFavoritados;
window.nextSlide = nextSlide;
window.prevSlide = prevSlide;

window.mudarLarguraSidebar = mudarLarguraSidebar;

function mudarLarguraSidebar() {
    const sidebar = document.getElementById("sidebar-anuncios");
    console.log("Sidebar:", sidebar);
    if (sidebar) {
        if (getComputedStyle(sidebar).display == "flex") {
            sidebar.style.display = "none";
        } else {
            sidebar.style.display = "flex";
        }
    }

}

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

    filtrar();
}

async function filtrar() {
    imoveisFiltrados = imoveisCache;
    document.getElementById("sidebar-anuncios").querySelectorAll("input, select, textarea").forEach((elemento) => {
        if (!elemento.name || !elemento.value || elemento.value === "") {
            return;
        }
        let nome = elemento.name;
        let valor = elemento.value;

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

    sessionStorage.setItem("anuncios-filtroSelecionado", nome);
    sessionStorage.setItem("anuncios-seta", seta?.className);

    if (seta && nome) {
        switch (nome) {
            case "referencia":
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
            default:
                break;
        }
    }
    carregarAnuncios();
}


async function carregarAnuncios() {
    let dados = imoveisFiltrados;
    const section = document.getElementById("anuncios");

    if (!section || !dados) return;

    let html = "";
    const contador = document.querySelector("#contador-imoveis");
    if (contador) {
        contador.textContent = `${dados.length} imóveis`;
    }

    if (dados.length === 0 || !dados) {
        const divVazio = document.createElement("div");
        divVazio.id = "vazio";
        divVazio.textContent = "Nenhum imóvel encontrado.";
        section.innerHTML = "";
        section.appendChild(divVazio);
        return;
    }

    for (const imovel of dados) {
        const b64 = imovel.anuncio?.imagens?.[0] || null;
        if (!b64) continue;
        let precoVenda = '';
        let precoAluguel = '';
        if (imovel.valor_aluguel && imovel.valor_venda) {
            precoVenda = `<span>Venda: <span class="preco">${formatarValor(imovel.valor_venda)}</span></span>`;
            precoAluguel = `<span>Aluguel: <span class="preco">${formatarValor(imovel.valor_aluguel)}</span></span>`;
        } else if (imovel.valor_venda) {
            precoVenda = `<span>Venda: <span class="preco">${formatarValor(imovel.valor_venda)}</span></span>`;
        } else {
            precoAluguel = `<span>Aluguel: <span class="preco">${formatarValor(imovel.valor_aluguel)}</span></span>`;
        }
        const classe = favoritos ? "curtido" : "";
        html += `
            <a href="dados-imovel.html?id=${imovel.id}" class="anuncio-link anuncio-imovel" >
                <i class="fas fa-heart ${classe}" onclick="event.preventDefault(); event.stopPropagation(); curtirImovel(event, ${imovel.id})"></i>
                <div class="swiper">
                    <div class="swiper-wrapper">
                        ${imovel.anuncio.imagens.map(img => `
                            <div class="swiper-slide" style="background-image: url(${img})">
                            </div>
                        `).join('')}
                        </div>
                    <div class="swiper-button-prev" onclick="event.preventDefault(); event.stopPropagation(); prevSlide()"></div>
                    <div class="swiper-button-next" onclick="event.preventDefault(); event.stopPropagation(); nextSlide()"></div>
                </div>
                <h2>${imovel.anuncio?.titulo}</h2>
                <p class='categoria'>${imovel.categoria}</p>
                <p>${imovel.endereco?.rua}, ${imovel.endereco?.numero}, ${imovel.endereco?.bairro}</p>
                ${precoVenda}
                ${precoAluguel}
                <p class="descricao">${imovel.anuncio?.descricao}</p>
                <div class="emojis">
                    <i class="fas fa-ruler-combined"></i><p>${imovel.area_total != null ? imovel.area_total : 0.00} m²</p> 
                    <i class="fas fa-bath"></i><p>${imovel.quantidade_banheiros != null ? imovel.quantidade_banheiros : 0}</p></i> 
                    <i class="fas fa-couch"></i><p>${imovel.quantidade_salas != null ? imovel.quantidade_salas : 0}</p></i> 
                    <i class="fas fa-bed"></i><p>${imovel.quantidade_quartos != null ? imovel.quantidade_quartos : 0}</p></i>
                    <i class="fas fa-car"></i><p>${imovel.quantidade_vagas != null ? imovel.quantidade_vagas : 0}</p></i>
                
                </div>
           
            </a>
        `;
    }
    if (html.length === 0) {
        console.warn("Nenhum imóvel com imagem encontrado para os anúncios");
        return;
    }
    section.innerHTML = html;

}


function inicializarSwiper() {
    if (!document.querySelector('.swiper')) {
        console.warn("Elemento .swiper não encontrado");
        return;
    }
    if (window.Swiper) {
        if (window.swiperInstance) window.swiperInstance.destroy(true, true);
        window.swiperInstance = new Swiper('.swiper', {
            loop: true,
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    }
}

function nextSlide() {
    if (window.swiperInstance && typeof window.swiperInstance.slideNext === "function") {
        window.swiperInstance.slideNext();
    } else {
        console.warn("Swiper ainda não inicializado");
    }
}

function prevSlide() {
    if (window.swiperInstance) {
        window.swiperInstance.slidePrev();
    }
}

window.addEventListener("beforeunload", () => {
    sessionStorage.setItem("reloading", "true");
});

window.addEventListener("DOMContentLoaded", async () => {
    favoritos = "?favoritos=true" === window.location.search;
    const filtroSelecionado = sessionStorage.getItem("anuncios-filtroSelecionado");
    const seta = sessionStorage.getItem("anuncios-seta");

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

    if (favoritos) {
        dados = await listarImoveisFavoritados();
        if (!dados || dados.length === 0) {
            const section = document.getElementById("anuncios");
            const divVazio = document.createElement("div");
            divVazio.id = "vazio";
            divVazio.textContent = "Nenhum imóvel encontrado.";
            section.innerHTML = "";
            section.appendChild(divVazio);
            return;
        }
        imoveisCache.push(...dados);
        imoveisFiltrados = imoveisCache;
    } else {
        if (imoveisCache.length === 0) {
            dados = await listarImoveisDisponiveis();
            if (dados.length === 0 || !dados) {
                const section = document.getElementById("anuncios");
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
        } else {
            dados = imoveisCache;
            imoveisFiltrados = imoveisCache;
        }
    }

    if (dados && dados.length > 0) {
        carregarAnuncios();
        inicializarSwiper();
        const element = document.querySelector("#sidebar-anuncios");
        if (element) {
            element.querySelectorAll("input").forEach((input) => {
                input.addEventListener("input", filtrar);
            });
            element.querySelectorAll("select").forEach((select) => {
                select.addEventListener("change", filtrar);
            });
        }
    }

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

window.addEventListener("onclick", async () => {
    if (event.target.classList.contains("swiper-button-prev") || event.target.classList.contains("swiper-button-next") || event.target.classList.contains("fa-heart") || event.target.classList.contains("fa-whatsapp")) {
        event.stopPropagation();
    }
});