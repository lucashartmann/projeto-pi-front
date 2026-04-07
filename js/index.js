function imovelPrincipal(dados) {
    let imovel, b64;
    while (true) {
        var randomIndex = Math.floor(Math.random() * dados.length);
        imovel = dados[randomIndex];
        b64 = imovel.anuncio?.imagens?.[0];
        if (b64) break;
    }
    var banner = document.getElementById("imovelDestaque");
    if (!banner) return;
    let preco = document.createElement("span");
    if (imovel.valor_aluguel && imovel.valor_venda) {
        preco.innerHTML = `R$ <p class="preco">${imovel.valor_venda}</p>/ R$ <p class="preco">${imovel.valor_aluguel}</p> / mês`;
    } else if (imovel.valor_venda) {
        preco.innerHTML = `R$ <p class="preco">${imovel.valor_venda}</p>`;
    } else{
        preco.innerHTML = `R$ <p class="preco">${imovel.valor_aluguel}</p>/ mês`;
    }
    
    banner.innerHTML = `
        <h2 class="sobrepor">${imovel.anuncio.titulo}${preco.outerHTML}</h2>
        
    `
    banner.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(data:image/jpeg;base64,${b64})`
    banner.addEventListener("click", () => abrirAnuncio(imovel.id));
}

function bannerImoveis(dados) {
    var div = document.getElementsByClassName("swiper-wrapper")[0];
    var banner = document.createElement("div")
    banner.className = "swiper-slide";
    var wrapper = document.querySelector(".swiper-wrapper");
    if (!wrapper) return;
    let html = "";
    for (var i = 0; i < 5; i++) {
        var imovel = dados[i];
        if (!imovel) continue;
        var b64 = imovel.anuncio?.imagens?.[0];
        if (!b64) continue;
        let preco = document.createElement("span");
        if (imovel.valor_aluguel && imovel.valor_venda) {
            preco.innerHTML = `R$ <p class="preco">${imovel.valor_venda}</p>/ R$ <p class="preco">${imovel.valor_aluguel}</p> / mês`;
        } else if (imovel.valor_venda) {
            preco.innerHTML = `R$ <p class="preco">${imovel.valor_venda}</p>`;
        } else{
            preco.innerHTML = `R$ <p class="preco">${imovel.valor_aluguel}</p>/ mês`;
        }
        let div = document.createElement("div");
        div.className = "swiper-slide";
        div.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(data:image/jpeg;base64,${b64})`
        div.innerHTML = `<h2 class="sobrepor">${imovel.anuncio.titulo}${preco.outerHTML}</h2>`
        html += div.outerHTML;
    }
    wrapper.innerHTML = html;

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
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            scrollbar: { el: '.swiper-scrollbar' },
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

    console.log(dados)

    if (!section || !dados) return;

    select_categoria = document.getElementById("select_categoria");
    select_status = document.getElementById("select_status");

    const categorias = [...new Set(dados.map(imovel => imovel.categoria))];
    categorias.unshift("");
    const status = [...new Set(dados.map(imovel => imovel.status))];
    status.unshift("");

    select_categoria.innerHTML = categorias.map(cat => `<option value="${cat}">${cat}</option>`).join("");
    select_status.innerHTML = status.map(st => `<option value="${st}">${st}</option>`).join("");

    let html = "";
    for (const imovel of dados) {
        const b64 = imovel.anuncio?.imagens?.[0] || "";
        let preco = document.createElement("span");
        if (imovel.valor_aluguel && imovel.valor_venda) {
            preco.innerHTML = `Venda: R$ <p class="preco">${imovel.valor_venda} </p>  - Aluguel: R$ <p class="preco">${imovel.valor_aluguel}</p> / mês`;
        } else if (imovel.valor_venda) {
            preco.innerHTML = `Venda: R$ <p class="preco">${imovel.valor_venda}</p>`;
        } else{
            preco.innerHTML = `Aluguel: R$ <p class="preco">${imovel.valor_aluguel}</p>/ mês`;
        }
        html += `
            <div class="anuncio_imovel" onclick="abrirAnuncio(${imovel.id})">
                <img src="data:image/jpeg;base64,${b64}" />
                <h2>${imovel.anuncio.titulo}</h2>
                <p>${imovel.endereco.rua}, ${imovel.endereco.numero}, ${imovel.endereco.bairro}</p>
                ${preco.outerHTML}
                <p>${imovel.anuncio.descricao}</p>
            </div>
        `;
    }
    section.innerHTML = html;


}

function pesquisarCEP(event) {
    const termo = event.target.value.replace(/\D/g, '');
    const anuncios = document.querySelectorAll(".anuncio_imovel");
    if (dados_imoveis == null) return;
    console.log(termo);
    document.querySelector(".swiper").style.display = "none";
    document.getElementById("imovelDestaque").style.display = "none";
    const imovel = dados_imoveis.find(imovel => imovel.endereco.cep == termo);
    for (const anuncio of anuncios) {
        anuncio.style.display = "none";
    }
    if (imovel) {
        
        anuncios.forEach(anuncio => {
            console.log(imovel.id, anuncio.dataset.id);
            console.log(anuncio.event)
            if (imovel.id in anuncio.event) {
                anuncio.style.display = "block";
                document.querySelector(".swiper").style.display = "block";
                document.getElementById("imovelDestaque").style.display = "block"
            }
            else {
                anuncio.style.display = "none";
                document.querySelector(".swiper").style.display = "none";
                document.getElementById("imovelDestaque").style.display = "none";
            }
        });
    } 
   
    if (termo.length === 0) {
        for (const anuncio of anuncios) {
                anuncio.style.display = "block";
        }
        document.querySelector(".swiper").style.display = "block";
            document.getElementById("imovelDestaque").style.display = "block";
    }
}


async function abrirAnuncio(imovel_id) {
    sessionStorage.setItem("imovel_id", imovel_id);
    window.location.href = "html/dados-imovel.html";
}

function pesquisar() {
    const termo = document.getElementById("input_pesquisa").value.toLowerCase();
    
    const anuncios = document.querySelectorAll(".anuncio_imovel");
    document.querySelector(".swiper").style.display = "none";
    document.getElementById("imovelDestaque").style.display = "none";
    anuncios.forEach(anuncio => {
        const titulo = anuncio.querySelector("h2").textContent.toLowerCase();
        const descricao = anuncio.querySelector("p:nth-child(5)").textContent.toLowerCase();
        if (titulo.includes(termo) || descricao.includes(termo)) {
            anuncio.style.display = "block";
        }
        else {
            anuncio.style.display = "none";
        }
    });

    
}

let dados_imoveis = null;

window.addEventListener("DOMContentLoaded", async () => {
    const dados = await listarImoveisDisponiveis();
    dados_imoveis = dados;
    if (dados) {
        carregarAnuncios(dados);
        imovelPrincipal(dados);
        bannerImoveis(dados);
    }else {
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

