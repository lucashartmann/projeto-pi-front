
async function carregarAnuncios(dados) {

    const section = document.getElementById("anuncios");

    if (!section || !dados) return;

    let html = "";
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
        html += `
            <div class="anuncio-imovel" onclick="abrirAnuncio(${imovel.id})">
                <div class="swiper">
                    <div class="swiper-wrapper">
                    <i class="fas fa-heart" onclick="curtirImovel(${imovel.id})"></i>
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
                    <i class="fas fa-ruler-combined"><p>${imovel.area_total || 'N/A'} m²</p></i> 
                    <i class="fas fa-bath"><p>${imovel.quant_banheiros || 'N/A'}</p></i> 
                    <i class="fas fa-couch"><p>${imovel.quant_salas || 'N/A'}</p></i> 
                    <i class="fas fa-bed"><p>${imovel.quant_quartos || 'N/A'}</p></i>
                    <i class="fas fa-car"><p>${imovel.quant_vagas || 'N/A'}</p></i>
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
    const dados = await listarImoveisDisponiveis() || NaN;
    dadosImoveis = dados;
    if (dados) {
        carregarAnuncios(dados);
    } else {
        console.error("Não foi possível carregar os imóveis");
    }

    inicializarSwiper();

    setInterval(() => {
        const swiper = document.querySelector('.swiper').swiper;
        if (swiper) {
            swiper.slideNext();
        }
    }, 7500);
});



