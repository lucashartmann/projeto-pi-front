function setupDados(dados) {
    imovel = JSON.parse(dados);
    var div = document.getElementById("dados-imovel");
    let imagensHtml = "";
    if (imovel.anuncio.imagens && imovel.anuncio.imagens.length > 0) {
        swiperhtml = "";
        for (const imagem of imovel.anuncio.imagens) {
            swiperhtml += `<div class="swiper-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${imagem})" onclick="abrirImagem('${imagem}')"></div>`;
            imagensHtml += `<div class="swiper-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${imagem})" onclick="abrirImagem('${imagem}')"></div>`;
        }
    }

    const divPai = document.getElementById("div-pai");
    const swiperWrapper = document.querySelector(".swiper-destaque .swiper-wrapper");
    const divTitulo = document.getElementById("div-titulo");
    const divGaleria = document.querySelector(".swiper-galeria .swiper-wrapper");
    const pDescricao = document.getElementById("p-descricao");
    const divContato = document.getElementById("entrar-contato");

    divTitulo.querySelector("h3").innerText = imovel.anuncio.titulo;
    divTitulo.querySelector("p").innerText = `${imovel.endereco.rua}, ${imovel.endereco.numero}, ${imovel.endereco.bairro}`;
    swiperWrapper.innerHTML = swiperhtml;
    divGaleria.innerHTML = imagensHtml;
    pDescricao.innerText = imovel.anuncio.descricao;
    try {
        divContato.getElementById("condominio").querySelector("p").innerText = formatarValor(imovel.valor_condominio);
        divContato.getElementById("iptu").querySelector("p").innerText = formatarValor(imovel.valor_iptu);
    } catch (e) {
        console.warn("Valores de condomínio e IPTU não encontrados");
    }

    if (imovel.filtros) {
        const divFiltros = document.getElementById("div-filtros");
        for (const filtro of imovel.filtros) {
            divFiltros.innerHTML += `<input type="checkbox" checked disabled><label>${filtro}</label>`;
        }
        divPai.appendChild(divFiltros);
    }

    // Adiiconar mapa do google maps

    //TODO: adicionar filtros, localizacao, valores, etc
}


window.addEventListener("DOMContentLoaded", async () => {
    const dados = sessionStorage.getItem("dados_imovel") || null;

    if (JSON.parse(dados).length === 0) {
        alert("Imóvel não encontrado!");
        window.location.href = getCaminhoRelativo("index.html");
        return;
    }

    sessionStorage.removeItem("dados_imovel");
    await setupDados(dados);
    await inicializarSwiper();
   
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

    var swiper = new Swiper('.swiper-destaque', {
        loop: true,
        pagination: {
            el: '.swiper-destaque .swiper-pagination', 
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-destaque .swiper-button-next', 
            prevEl: '.swiper-destaque .swiper-button-prev'  
        },
        scrollbar: {
            el: '.swiper-destaque .swiper-scrollbar'
        },
    });

    try {
        window.swiperInstance = new Swiper('.swiper-galeria', {
            loop: true,
            pagination: {
                el: '.swiper-galeria .swiper-pagination', 
                clickable: true
            },
            navigation: {
                nextEl: '.swiper-galeria .swiper-button-next',
                prevEl: '.swiper-galeria .swiper-button-prev'
            },
            slidesPerView: 3,
            spaceBetween: 30,
            centeredSlides: true,
            breakpoints: {
                0: { slidesPerView: 3 },
                640: { slidesPerView: 4 },
                768: { slidesPerView: 5 },
                1024: { slidesPerView: 6 },
            },
        });
    } catch (error) {
        console.error("Erro ao inicializar o Swiper da galeria:", error);
    }

}

function nextSlide() {
    if (window.swiperInstance && typeof window.swiperInstance.slideNext === "function") {
        window.swiperInstance.slideNext();
    } else {
        console.warn("Swiper ainda não inicializado");
    }
}

function calcularPrecoMedio() {
    return;
}

function prevSlide() {
    if (window.swiperInstance) {
        window.swiperInstance.slidePrev();
    }
}