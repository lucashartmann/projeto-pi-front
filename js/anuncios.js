import { salvarImoveisCurtidos, curtirImovel, imoveisCurtidos } from "./modules/usuario.js";
import { getCaminhoRelativo, formatarValor } from "./modules/utils.js";
import { listarImoveisDisponiveis } from "./modules/imoveis.js";

let imoveisCache = [];
let proprietariosCache = [];
let usuariosFiltrados = [];
let usuariosCache = [];
let imoveisFiltrados = [];
let filtroUsuario = "";
let seta = "";
var favoritos = false;

window.abrirAnuncio = abrirAnuncio;
window.curtirImovel = curtirImovel;
window.filtrar = filtrar;
window.filtroOrdenado = filtroOrdenado;

async function listarImoveisFavoritados() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=get_favoritos");

        const resposta = await fetch(caminho)
            .then(async (res) => {
                if (!res.ok) {
                    throw new Error(`Erro na requisição: ${res.status}`);
                    return null;
                }

                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    throw new Error("Resposta não é JSON");
                    return null;
                }

            })
            .then(async (data) => {
                if (data.status == "erro") {
                    console.log("Erro ao listar imóveis: " + data.mensagem);
                    return null;
                }
                return await data;
            })
            .catch((error) => {
                console.log("Erro ao listar imóveis favoritados:", error);
                return null;
            });

        if (!resposta) {
            console.log("Falha ao obter imóveis favoritados");
            return null;
        }

        resposta?.forEach(imovel => {
            switch (imovel.status) {
                case "Venda":
                    imovel.valor_aluguel = null;
                    break;
                case "Aluguel":
                    imovel.valor_venda = null;
                    break;
                default:
                    break;
            }
        });

        return resposta;
    } catch (erro) {
        console.log("Falha ao conectar com o backend:", erro);
        return null;
    }

}

async function abrirAnuncio(imovel = null, id = null) {
    if (id && !imovel) {
        imovel = imoveisCache.find(imovel => imovel.id == id);
    } else if (!imovel && !id) {
        imovel = null;
        return
    }

    if (event.target.classList.contains("swiper-button-prev") || event.target.classList.contains("swiper-button-next") || event.target.classList.contains("fa-heart") || event.target.classList.contains("fa-whatsapp")) {
        return;
    }

    if (event.target.classList.contains("swiper-slide") && !event.target.classList.contains("swiper-slide-active")) {
        return;
    }

    sessionStorage.setItem("dados_imovel", JSON.stringify(imovel));
    window.location.href = "dados-imovel.html";
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

    let seta = document.querySelector("#seta");
    let nome = document.getElementById("select-filtro") ? document.getElementById("select-filtro").value : null;

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
        let precoVenda = document.createElement("span");
        let precoAluguel = document.createElement("span");
        if (imovel.valor_aluguel && imovel.valor_venda) {
            precoVenda.innerHTML = `Venda: <p class="preco">${formatarValor(imovel.valor_venda)}</p>`;
            precoAluguel.innerHTML = `Aluguel: <p class="preco">${formatarValor(imovel.valor_aluguel)}</p>`;
        } else if (imovel.valor_venda) {
            precoVenda.innerHTML = `Venda: <p class="preco">${formatarValor(imovel.valor_venda)}</p>`;
        } else {
            precoAluguel.innerHTML = `Aluguel: <p class="preco">${formatarValor(imovel.valor_aluguel)}</p>`;
        }
        const classe = favoritos ? "curtido" : "";
        html += `
            <div class="anuncio-imovel" onclick="abrirAnuncio(null, ${imovel.id})">
                <div class="swiper">
                    <div class="swiper-wrapper">
                    <i class="fas fa-heart ${classe}" onclick="curtirImovel(event, ${imovel.id})"></i>
                    ${imovel.anuncio.imagens.map(img => `
                        <div class="swiper-slide" style="background-image: url(${img})">
                        </div>
                    `).join('')}
                    </div>
                    <div class="swiper-button-prev" onclick="prevSlide()"></div>
                    <div class="swiper-button-next" onclick="nextSlide()"></div>
                </div>
                <h2>${imovel.anuncio?.titulo}</h2>
                <p>${imovel.endereco?.rua}, ${imovel.endereco?.numero}, ${imovel.endereco?.bairro}</p>
                ${precoVenda.outerHTML}
                ${precoAluguel.outerHTML}
                <p class="descricao">${imovel.anuncio?.descricao}</p>
                <div class="emojis">
                    <i class="fas fa-ruler-combined"><p>${imovel.area_total || 0.00} m²</p></i> 
                    <i class="fas fa-bath"><p>${imovel.quantidade_banheiros || 0}</p></i> 
                    <i class="fas fa-couch"><p>${imovel.quantidade_salas || 0}</p></i> 
                    <i class="fas fa-bed"><p>${imovel.quantidade_quartos || 0}</p></i>
                    <i class="fas fa-car"><p>${imovel.quantidade_vagas || 0}</p></i>
                    <a href="https://wa.me/" style="text-decoration: none;" target="_blank" class="fab fa-whatsapp"></a>
                </div>
            </div>
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

window.addEventListener("DOMContentLoaded", async () => {
    favoritos = sessionStorage.getItem("favoritos") === "true";
    sessionStorage.removeItem("favoritos");
    let dados = [];

    if (favoritos) {
        dados = await listarImoveisFavoritados();
        if (dados.length === 0 || !dados) {
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
});

window.addEventListener('beforeunload', async function (event) {
    // event.preventDefault();
    // event.returnValue = '';
    if (favoritos) {
        await salvarImoveisCurtidos();
    }

});