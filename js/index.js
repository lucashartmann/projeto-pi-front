import { listarImoveisDisponiveis, listarImoveisDestacados } from "./modules/imoveis.js";
import { carregarUser, salvarImoveisCurtidos, curtirImovel, imoveisCurtidos } from "./modules/usuario.js";
import { getCaminhoRelativo, formatarValor } from "./modules/utils.js";
import { usuarioLogado } from "./modules/usuario.js";

window.curtirImovel = curtirImovel;
window.filtrar = filtrar;

let dadosImoveis = [];
let imoveisFiltrados = [];

function imovelPrincipal(dados) {
    if (!Array.isArray(dados) || dados.length === 0) return;
    const imoveisComImagem = dados.filter(imovel => imovel?.anuncio?.imagens?.[0]);
    if (imoveisComImagem.length === 0) return;
    const imoveisEmbaralhados = [...imoveisComImagem].sort(() => Math.random() - 0.5);
    const lista = imoveisEmbaralhados.slice(0, 9);

    document.querySelector("#gallery").innerHTML = `
        ${lista[0] ? `<a href="html/dados-imovel.html?id=${lista[0].id}"><img src="${lista[0].anuncio.imagens[0]}"  onclick="html/dados-imovel.html?id=${lista[0].id}"></a>` : ''}
        ${lista[1] ? `<a href="html/dados-imovel.html?id=${lista[1].id}"><img src="${lista[1].anuncio.imagens[0]}"  onclick="html/dados-imovel.html?id=${lista[1].id}"></a>` : ''}
    `

    document.querySelector("#gallery3").innerHTML = `
        ${lista[2] ? `<a href="html/dados-imovel.html?id=${lista[2].id}"><img src="${lista[2].anuncio.imagens[0]}"  onclick="html/dados-imovel.html?id=${lista[2].id}"></a>` : ''}
        ${lista[3] ? `<a href="html/dados-imovel.html?id=${lista[3].id}"><img src="${lista[3].anuncio.imagens[0]}"  onclick="html/dados-imovel.html?id=${lista[3].id}"></a>` : ''}
    `

    document.querySelector("#gallery2").innerHTML = `
        ${lista[4] ? `<a href="html/dados-imovel.html?id=${lista[4].id}"><img src="${lista[4].anuncio.imagens[0]}"  onclick="html/dados-imovel.html?id=${lista[4].id}"></a>` : ''}
        ${lista[5] ? `<a href="html/dados-imovel.html?id=${lista[5].id}"><img src="${lista[5].anuncio.imagens[0]}"  onclick="html/dados-imovel.html?id=${lista[5].id}"></a>` : ''}
        ${lista[6] ? `<a href="html/dados-imovel.html?id=${lista[6].id}"><img src="${lista[6].anuncio.imagens[0]}"  onclick="html/dados-imovel.html?id=${lista[6].id}"></a>` : ''}
        ${lista[7] ? `<a href="html/dados-imovel.html?id=${lista[7].id}"><img src="${lista[7].anuncio.imagens[0]}"  onclick="html/dados-imovel.html?id=${lista[7].id}"></a>` : ''}
    `

}

function bannerImoveis(dados) {
    var wrapper = document.querySelector(".swiper-destaque .swiper-wrapper");
    if (!wrapper) return;
    wrapper.innerHTML = "";
    const tamanho = dados.length < 5 ? dados.length : 5;
    if (tamanho === 0) return;
    if (!Array.isArray(dados)) return;
    if (dados.length === 0) return;
    if (dados.filter(imovel => imovel?.anuncio?.imagens?.[0]).length === 0) return;
    for (var i = 0; i < tamanho; i++) {
        var imovel = dados[i];
        if (!imovel) continue;
        var b64 = imovel.anuncio?.imagens?.[0];
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
        wrapper.innerHTML += `
        <a class="swiper-slide" href="html/dados-imovel.html?id=${imovel.id}"> 
        <img src="${b64}" alt="${imovel.anuncio.titulo}"><div><h2>${imovel.anuncio.titulo}</h2>${precoVenda}${precoAluguel}<p>${imovel.anuncio.descricao}</p></div></a>
        `
    }
}

function inicializarSwiper() {
    if (!document.querySelector('.swiper')) {
        console.warn("Elemento .swiper não encontrado");
        return;
    }
    var swiper = new Swiper('.swiper-destaque', {
        loop: true,
        direction: 'horizontal',
        initialSlide: 0,
        scrollbar: false,
        obeserver: false,
        slidesPerView: 1,
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    });
    var swiper = new Swiper('.swiper-anuncio', {
        direction: 'horizontal',
        initialSlide: 0,
        scrollbar: false,
        obeserver: false,
        slidesPerView: 1,
        navigation: { nextEl: '.swiper-anuncio .swiper-button-next', prevEl: '.swiper-anuncio .swiper-button-prev' },
    });
    var swiper = new Swiper('.swiper-mais-vistos', {
        direction: 'horizontal',
        initialSlide: 0,
        loop: true,
        scrollbar: false,
        obeserver: false,
        navigation: { nextEl: '.swiper-mais-vistos .swiper-button-next', prevEl: '.swiper-mais-vistos .swiper-button-prev' },
        spaceBetween: 30,
        centeredSlides: false,
        breakpoints: {
            0: { slidesPerView:1 },
            640: { slidesPerView:2 },
            768: { slidesPerView:3 },
            1024: { slidesPerView: 4 },
        },
    });
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


async function carregarAnuncios(dados) {
    const section = document.getElementById("anuncios");
    let usuario = usuarioLogado;
    if (!section || !dados) return;
    if (dados.length === 0) return;
    if (dados.filter(imovel => imovel?.anuncio?.imagens?.[0]).length === 0) return;
    section.innerHTML = "";
    let html = "";
    html += `<h3>Imóveis Disponíveis</h3>`;
    for (const imovel of dados) {
        const b64 = imovel.anuncio?.imagens?.[0] || null;
        if (!b64) continue;
        let precoVenda = "";
        let precoAluguel = "";
        if (imovel.valor_aluguel && imovel.valor_venda) {
            precoVenda = `<span>Venda: <span class="preco">${formatarValor(imovel.valor_venda)}</span></span>`;
            precoAluguel = `<span>Aluguel: <span class="preco">${formatarValor(imovel.valor_aluguel)}</span></span>`;
        } else if (imovel.valor_venda) {
            precoVenda = `<span>Venda: <span class="preco">${formatarValor(imovel.valor_venda)}</span></span>`;
        } else {
            precoAluguel = `<span>Aluguel: <span class="preco">${formatarValor(imovel.valor_aluguel)}</span></span>`;
        }

        const classe = usuario && usuario.favoritos && usuario.favoritos.includes(imovel.id) ? "curtido" : "";

        // TODO: Botar os ids dos imóveis favoritados na lista imoveisCurtidos

        html += `
            <a href="html/dados-imovel.html?id=${imovel.id}" class="anuncio-link anuncio-imovel" >
                <i class="fas fa-heart ${classe}" onclick="curtirImovel(event, ${imovel.id})"></i>
                <div class="swiper swiper-anuncio">
                    <div class="swiper-wrapper">
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
                ${precoVenda}
                ${precoAluguel}
                <p class="descricao">${imovel.anuncio?.descricao}</p>
                <div class="emojis">
                    <i class="fas fa-ruler-combined"><p>${imovel.area_total != null ? imovel.area_total : 'N/A'} m²</p></i> 
                    <i class="fas fa-bath"><p>${imovel.quantidade_banheiros != null ? imovel.quantidade_banheiros : 'N/A'}</p></i> 
                    <i class="fas fa-couch"><p>${imovel.quantidade_salas != null ? imovel.quantidade_salas : 'N/A'}</p></i> 
                    <i class="fas fa-bed"><p>${imovel.quantidade_quartos != null ? imovel.quantidade_quartos : 'N/A'}</p></i>
                    <i class="fas fa-car"><p>${imovel.quantidade_vagas != null ? imovel.quantidade_vagas : 'N/A'}</p></i>
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

function pesquisarCEP(event) {
    const termo = event.target.value.replace(/\D/g, '');
    const anuncios = document.querySelectorAll(".anuncio-imovel");
    if (dadosImoveis == null) return;
    const gallery = document.getElementById("gallery");
    gallery.style.display = "none";
    const gallery2 = document.getElementById("gallery2");
    gallery2.style.display = "none";
    const gallery3 = document.getElementById("gallery3");
    gallery3.style.display = "none";
    const swiper = document.querySelector(".swiper-anuncio");
    swiper.style.display = "none";
    const imovelDestaque = document.getElementById("imovel-destaque");
    imovelDestaque.style.display = "none";
    const imovel = dadosImoveis.find(imovel => imovel.endereco.cep == termo);
    for (const anuncio of anuncios) {
        anuncio.style.display = "none";
    }
    if (imovel) {
        anuncios.forEach(anuncio => {
            if (imovel.id in anuncio.event) {
                anuncio.style.display = "block";
                const swiper = document.querySelector(".swiper");
                swiper.style.display = "block";
                const imovelDestaque = document.getElementById("imovel-destaque");
                imovelDestaque.style.display = "block"
            }
            else {
                anuncio.style.display = "none";
                const swiper = document.querySelector(".swiper");
                swiper.style.display = "none";
                const imovelDestaque = document.getElementById("imovel-destaque");
                imovelDestaque.style.display = "none";
            }
        });
    }
    if (termo.length === 0) {
        for (const anuncio of anuncios) {
            anuncio.style.display = "block";
        }
        swiper.style.display = "block";
        imovelDestaque.style.display = "flex";
        gallery.style.display = "flex";
        gallery2.style.display = "grid";
        gallery3.style.display = "flex";
    }
}


function filtrar() {

    const anuncios = document.querySelectorAll(".anuncio-imovel");
    const gallery = document.getElementById("gallery");
    const gallery2 = document.getElementById("gallery2");
    const gallery3 = document.getElementById("gallery3");
    const swiper = document.querySelector(".swiper");
    swiper.style.display = "flex";
    gallery.style.display = "flex";
    gallery2.style.display = "grid";
    gallery3.style.display = "flex";

    imoveisFiltrados = dadosImoveis;

    document.querySelector("#pesquisa").querySelectorAll("input, select").forEach(input => {
        let termo = input.value;
        if (termo.length === 0) {
            return;
        }
        swiper.style.display = "none";
        gallery3.style.display = "none";
        gallery2.style.display = "none";
        gallery.style.display = "none";

        switch (input.id) {
            case "input-cep":
                termo = termo.toLowerCase();
                imoveisFiltrados = imoveisFiltrados.filter(imovel => imovel.endereco.cep.includes(termo));
                break;
            case "input-pesquisa":
                termo = termo.toLowerCase();
                imoveisFiltrados = imoveisFiltrados.filter(imovel => {
                    const titulo = imovel.anuncio.titulo.toLowerCase();
                    const descricao = imovel.anuncio.descricao.toLowerCase();
                    const bairro = imovel.endereco.bairro.toLowerCase();
                    return titulo.includes(termo) || descricao.includes(termo) || bairro.includes(termo);
                });
                break;
            case "select-categoria":
                imoveisFiltrados = imoveisFiltrados.filter(imovel => imovel.categoria === termo);
                break;
            case "select-status":
                imoveisFiltrados = imoveisFiltrados.filter(imovel => imovel.status === termo);
                break;
            default:
                break;

        }
    });

    carregarAnuncios(imoveisFiltrados);
}

function estilizarDiv() {
    document.querySelectorAll(".swiper-slide > div").forEach(card => {
        let dragging = false;
        let startX, startY;
        let x = 20;
        let y = 20;

        card.addEventListener("pointerdown", e => {
            e.stopPropagation();
        });

        card.addEventListener("click", e => {
            e.stopPropagation();
        });

        card.addEventListener("pointerdown", e => {
            dragging = true;
            card.setPointerCapture(e.pointerId);
            startX = e.clientX;
            startY = e.clientY;
            card.style.cursor = "grabbing";
        });

        card.addEventListener("pointermove", e => {
            if (!dragging) return;

            x -= startX - e.clientX;
            y -= startY - e.clientY;

            startX = e.clientX;
            startY = e.clientY;

            card.style.right = `${20 - x}px`;
            card.style.bottom = `${20 - y}px`;
        });

        card.addEventListener("pointerup", () => {
            dragging = false;
            card.style.cursor = "grab";
        });
    });
}

function maisVistos(dados) {
    const section = document.getElementById('mais-vistos');
    const swiper = document.querySelector(".swiper-mais-vistos");
    const wrapper = document.querySelector(".swiper-mais-vistos .swiper-wrapper");
    const tamanho = dados.length < 5 ? dados.length : 5;
    if (tamanho === 0) return;
    if (!Array.isArray(dados)) return;
    if (dados.length === 0) return;
    if (dados.filter(imovel => imovel?.anuncio?.imagens?.[0]).length === 0) return;
    if (dados.filter(imovel => imovel?.quant_clicks > 0).length < 5) return;
    if (tamanho < 5) return;
    dados = dados.filter(imovel => imovel?.quant_clicks > 0);
    section.style.display = "flex";
    section.style.flexDirection = "column";
    let usuario = usuarioLogado;
    for (var i = 0; i < tamanho; i++) {
        var imovel = dados[i];
        const b64 = imovel.anuncio?.imagens?.[0] || null;
        if (!b64) continue;
        let precoVenda = "";
        let precoAluguel = "";
        if (imovel.valor_aluguel && imovel.valor_venda) {
            precoVenda = `<span>Venda: <span class="preco">${formatarValor(imovel.valor_venda)}</span></span>`;
            precoAluguel = `<span>Aluguel: <span class="preco">${formatarValor(imovel.valor_aluguel)}</span></span>`;
        } else if (imovel.valor_venda) {
            precoVenda = `<span>Venda: <span class="preco">${formatarValor(imovel.valor_venda)}</span></span>`;
        } else {
            precoAluguel = `<span>Aluguel: <span class="preco">${formatarValor(imovel.valor_aluguel)}</span></span>`;
        }

        const classe = usuario && usuario.favoritos && usuario.favoritos.includes(imovel.id) ? "curtido" : "";

        let html = `
            <a href="html/dados-imovel.html?id=${imovel.id}" class="swiper-slide anuncio-link anuncio-imovel" >
                <i class="fas fa-heart ${classe}" onclick="curtirImovel(event, ${imovel.id})"></i>
                <div class="swiper swiper-anuncio">
                    <div class="swiper-wrapper">
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
                ${precoVenda}
                ${precoAluguel}
                <p class="descricao">${imovel.anuncio?.descricao}</p>
                <div class="emojis">
                    <i class="fas fa-ruler-combined"><p>${imovel.area_total != null ? imovel.area_total : 'N/A'} m²</p></i> 
                    <i class="fas fa-bath"><p>${imovel.quantidade_banheiros != null ? imovel.quantidade_banheiros : 'N/A'}</p></i> 
                    <i class="fas fa-couch"><p>${imovel.quantidade_salas != null ? imovel.quantidade_salas : 'N/A'}</p></i> 
                    <i class="fas fa-bed"><p>${imovel.quantidade_quartos != null ? imovel.quantidade_quartos : 'N/A'}</p></i>
                    <i class="fas fa-car"><p>${imovel.quantidade_vagas != null ? imovel.quantidade_vagas : 'N/A'}</p></i>
                </div>
            </a>
        `;
        wrapper.innerHTML += html;
    }
}

window.addEventListener("DOMContentLoaded", async () => {
    const dados = await listarImoveisDisponiveis() || null;
    let destacados = null;
    if (dados && dados.length > 0 && dados.filter(imovel => imovel?.anuncio?.imagens?.[0]).length > 0) {
        destacados = await listarImoveisDestacados() || null;
    }

    dadosImoveis = dados;
    if (dados && dados.length > 0 && dados.filter(imovel => imovel?.anuncio?.imagens?.[0]).length > 0) {
        carregarAnuncios(dados);
        imovelPrincipal(dados);
        maisVistos(dados);
    } else {
        console.error("Não foi possível carregar os imóveis");
    }


    if (dados || destacados) {
        let lista =
            destacados?.length > 0
                ? destacados
                : dados?.length > 0
                    ? dados
                    : null;

        if (lista && lista.length > 0 && lista.filter(imovel => imovel?.anuncio?.imagens?.[0]).length > 0) {
            bannerImoveis(lista);
            inicializarSwiper();
            estilizarDiv();
            setInterval(() => {
                const swiper = document.querySelector('.swiper-destaque').swiper;
                if (swiper) {
                    swiper.slideNext();
                }
            }, 7500);
            setInterval(() => {
                const swiper = document.querySelector('.swiper-mais-vistos').swiper;
                if (swiper) {
                    swiper.slideNext();
                }
            }, 8000);
        }
    }

});

window.addEventListener('beforeunload', async function (event) {
    // event.preventDefault();
    // event.returnValue = '';
    if (imoveisCurtidos.length > 0) {
        await salvarImoveisCurtidos();
    }

});


window.addEventListener("onclick", async () => {
    if (event.target.classList.contains("swiper-button-prev") || event.target.classList.contains("swiper-button-next") || event.target.classList.contains("fa-heart") || event.target.classList.contains("fa-whatsapp")) {
        event.stopPropagation();
    }
});