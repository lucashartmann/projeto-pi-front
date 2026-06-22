function setupDados(dados) {
    var div = document.getElementById("dados-imovel");
    let imagensHtml = "";
    if (dados.anuncio.imagens && dados.anuncio.imagens.length > 0) {
        swiperhtml = "";
        for (const imagem of dados.anuncio.imagens) {
            swiperhtml += `<div class="swiper-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${imagem})"></div>`;
            imagensHtml += `<div class="swiper-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${imagem})" onclick="abrirImagem(this.style.backgroundImage.slice(5, -2))"></div>`;
        }
    }

    const divPai = document.getElementById("div-pai");
    const swiperWrapper = document.querySelector(".swiper-wrapper");
    const swiper = document.querySelector(".swiper");
    const divTitulo = document.getElementById("div-titulo");
    const divGaleria = document.querySelector(".swiper-galeria");
    const pDescricao = document.getElementById("p-descricao");
    const divContato = document.getElementById("entrar-contato");

    divTitulo.querySelector("h3").innerText = dados.anuncio.titulo;
    divTitulo.querySelector("p").innerText = `${dados.endereco.rua}, ${dados.endereco.numero}, ${dados.endereco.bairro}`;
    swiperWrapper.innerHTML = swiperhtml;
    divGaleria.innerHTML = imagensHtml;
    pDescricao.innerText = dados.anuncio.descricao;
    try {
        divContato.getElementById("condominio").querySelector("p").innerText = formatarValor(dados.valor_condominio);
        divContato.getElementById("iptu").querySelector("p").innerText = formatarValor(dados.valor_iptu);
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

    const swiper1 = new Swiper('.swiper', {
        loop: true,
        pagination: {
            el: '.swiper .swiper-pagination', 
            clickable: true
        },
        navigation: {
            nextEl: '.swiper .swiper-button-next', 
            prevEl: '.swiper .swiper-button-prev'  
        },
        scrollbar: {
            el: '.swiper .swiper-scrollbar'
        },
    });

    console.log(document.querySelector('.swiper-galeria'));

    const swiper2 = new Swiper('.swiper-galeria', {
        pagination: {
            el: '.swiper-galeria .swiper-pagination', 
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-galeria .swiper-button-next', 
            prevEl: '.swiper-galeria .swiper-button-prev'  
        },
        slidesPerView: 3,
        spaceBetween: 50,
        centeredSlides: true,
        breakpoints: {
            0: { slidesPerView: 1 },
            640: { slidesPerView: 2 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
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

function calcularPrecoMedio() {
    return;
}

function prevSlide() {
    if (window.swiperInstance) {
        window.swiperInstance.slidePrev();
    }
}