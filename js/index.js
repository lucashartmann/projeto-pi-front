function imovelPrincipal(dados) {
    if (!Array.isArray(dados) || dados.length === 0) return;


    const imoveisComImagem = dados.filter(imovel => imovel?.anuncio?.imagens?.[0]);
    const ramdomNumber = Math.floor(Math.random() * imoveisComImagem.length);
    const imovel = imoveisComImagem[ramdomNumber] || dados[0];
    const b64 = imovel?.anuncio?.imagens?.[0] || null;

    if (!b64) {
        console.warn("Imóvel principal não possui imagem");
        return;
    }

    var banner = document.getElementById("imovel-destaque");
    if (!banner) return;
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

    banner.innerHTML = `
        <h2 class="sobrepor">${imovel.anuncio.titulo}${precoVenda.outerHTML}${precoAluguel.outerHTML}</h2>
        
    `
    banner.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${b64})`
    banner.addEventListener("click", () => abrirAnuncio(imovel.id));
}

function bannerImoveis(dados) {
    var wrapper = document.querySelector(".swiper-wrapper");
    if (!wrapper) return;
    const fragment = document.createDocumentFragment();
    for (var i = 0; i < 5; i++) {
        var imovel = dados[i];
        if (!imovel) continue;
        var b64 = imovel.anuncio?.imagens?.[0];
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
        let div = document.createElement("div");
        div.className = "swiper-slide";
        div.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${b64})`
        div.innerHTML = `<div class="sobrepor"><h2>${imovel.anuncio.titulo}</h2>${precoVenda.outerHTML}${precoAluguel.outerHTML}</div>`
        var id = imovel.id;
        div.addEventListener("click", () => abrirAnuncio(id));
        fragment.appendChild(div);
    }
    if (fragment.childNodes.length === 0) {
        console.warn("Nenhum imóvel com imagem encontrado para o banner");
        return;
    }
    wrapper.replaceChildren(fragment);

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
                    <i class="fas fa-heart"></i>
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
                    <i class="fas fa-ruler-combined">${imovel.area_total || 'N/A'} m²</i> 
                    <i class="fas fa-bath">${imovel.quant_banheiros || 'N/A'}</i> 
                    <i class="fas fa-couch">${imovel.quant_salas || 'N/A'}</i> 
                    <i class="fas fa-bed">${imovel.quant_quartos || 'N/A'}</i>
                    <i class="fas fa-car">${imovel.quant_vagas || 'N/A'}</i>
                    <i class="fab fa-whatsapp"></i>
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
    const swiper = document.querySelector(".swiper");
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


async function abrirAnuncio(imovel_id) {
    if (event.target.classList.contains("swiper-button-prev") || event.target.classList.contains("swiper-button-next")) {
        return;
    }
    sessionStorage.setItem("imovel_id", imovel_id);
    window.location.href = "html/dados-imovel.html";
}

function pesquisar() {
    const termo = document.getElementById("input-pesquisa").value.toLowerCase();

    const anuncios = document.querySelectorAll(".anuncio-imovel");
    const gallery = document.getElementById("gallery");
    gallery.style.display = "none";
    const gallery2 = document.getElementById("gallery2");
    gallery2.style.display = "none";
    const gallery3 = document.getElementById("gallery3");
    gallery3.style.display = "none";
    const swiper = document.querySelector(".swiper");
    swiper.style.display = "none";
    const imovelDestaque = document.getElementById("imovel-destaque");
    imovelDestaque.style.display = "none";
    anuncios.forEach(anuncio => {
        const titulo = anuncio.querySelector("h2").textContent.toLowerCase();
        const descricao = anuncio.querySelector(".descricao").textContent.toLowerCase();
        if (titulo.includes(termo) || descricao.includes(termo)) {
            anuncio.style.display = "block";
        }
        else {
            anuncio.style.display = "none";
        }
    });
    if (termo.length === 0) {
        swiper.style.display = "block";
        imovelDestaque.style.display = "flex";
        gallery.style.display = "flex";
        gallery2.style.display = "grid";
        gallery3.style.display = "flex";
    }
}

let dadosImoveis = null;

window.addEventListener("DOMContentLoaded", async () => {
    const dados = await listarImoveisDisponiveis() || NaN;
    dadosImoveis = dados;
    if (dados) {
        carregarAnuncios(dados);
        imovelPrincipal(dados);
        bannerImoveis(dados);
    } else {
        console.error("Não foi possível carregar os imóveis");
    }

    inicializarSwiper();

    setInterval(() => {
        const swiper = document.querySelector('.swiper').swiper;
        if (swiper) {
            swiper.slideNext();
        }
    }, 3500);
});

