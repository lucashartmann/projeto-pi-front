function setupDados(dados) {
    var div = document.getElementById("dados_imovel");
    let imagensHtml = "";
    console.log(dados);
    if (dados.anuncio.imagens && dados.anuncio.imagens.length > 0) {
        swiperhtml = "";
        for (const imagem of dados.anuncio.imagens) {
            swiperhtml += `<div class="swiper-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${imagem})"></div>`;
            imagensHtml += `<li><img src="${imagem}" alt="Imagem do imóvel" onclick="abrirImagem(this.src)" /></li>`;
        }
        
    }

    const div_pai = document.getElementById("div_pai");
    const swiper_wrapper = document.querySelector(".swiper-wrapper");
    const swiper = document.querySelector(".swiper");
    const div_titulo = document.getElementById("div_titulo");
    const div_galeria = document.getElementById("ul_imagens");
    const p_descricao = document.getElementById("p_descricao");
    const div_contato = document.getElementById("entrar_contato");

    div_titulo.querySelector("h3").innerText = dados.anuncio.titulo;
    div_titulo.querySelector("p").innerText = `${dados.endereco.rua}, ${dados.endereco.numero}, ${dados.endereco.bairro}`;
    swiper_wrapper.innerHTML = swiperhtml;
    div_galeria.innerHTML = imagensHtml;
    p_descricao.innerText = dados.anuncio.descricao;
    try {
        div_contato.getElementById("condominio").querySelector("p").innerText = dados.valor_condominio;
        div_contato.getElementById("iptu").querySelector("p").innerText = dados.valor_iptu;
    } catch (e) {
        console.warn("Valores de condomínio e IPTU não encontrados");
    }

    //TODO: adicionar filtros, localizacao, valores, etc
}


window.addEventListener("DOMContentLoaded", async () => {
    id = sessionStorage.getItem("imovel_id") || null;
    if (!id) {
        alert("Imóvel não encontrado!");
        window.location.href = "../html/index.html";
        return;
    }
    dados = await getDadosImovel(id);
    sessionStorage.removeItem("imovel_id");
    if (dados) {
        await setupDados(dados);
        await inicializarSwiper();
        
        setInterval(() => {
            const swiper = document.querySelector('.swiper').swiper;
            if (swiper) {
                swiper.slideNext();
            }
        }, 3500);
    } else {
        alert("Erro ao carregar dados do imóvel!");
        window.location.href = "../html/index.html";
    }
});

function abrirImagem(src) {
    var modal = document.createElement("div");
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100%";
    modal.style.height = "100%";
    modal.style.backgroundColor = "rgba(0, 0, 0, 0.8)";
    modal.style.display = "flex";
    modal.style.justifyContent = "center";
    modal.style.alignItems = "center";
    modal.style.zIndex = "1000";
    var img = document.createElement("img");
    img.src = src;
    img.style.maxWidth = "90%";
    img.style.maxHeight = "90%";
    modal.appendChild(img);
    document.body.appendChild(modal);
    modal.addEventListener("click", function () {
        document.body.removeChild(modal);
    });
    img.addEventListener("click", function (event) {
        event.stopPropagation();
        document.body.removeChild(modal);
    });
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
        console.log("Swiper inicializado");
    } else {
        console.warn("Swiper não encontrado");
    }
}

function nextSlide() {
    if (window.swiperInstance && typeof window.swiperInstance.slideNext === "function") {
        window.swiperInstance.slideNext();
        console.log("Próximo slide");
    } else {
        console.warn("Swiper ainda não inicializado");
    }
}

function prevSlide() {
    if (window.swiperInstance) {
        window.swiperInstance.slidePrev();
    }
}