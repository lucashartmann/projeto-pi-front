import { getCaminhoRelativo, formatarValor } from "./modules/utils.js";
import { usuarioLogado } from "./modules/usuario.js";

window.abrirImagem = abrirImagem;
let imovel = null;
let usuario = usuarioLogado;

async function cadastrarAtendimento() {
    if (!usuario) {
        alert("Você precisa estar logado para solicitar atendimento.");
        return;
    }
    alert("Um especialista irá entrar em contato por email ou whatsapp");
    try {
        let caminho = getCaminhoRelativo("/php/api/atendimentos.php?acao=cadastro&idImovel=" + imovel.id);
        const resposta = await fetch(caminho, {
            method: "POST",
            body: JSON.stringify(data)
        })
            .then(async response => {
                if (response.erro) {
                    console.error("Erro ao cadastrar atendimento: " + response.erro);
                    return null;
                }
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await response.json();
                } else {
                    const texto = await response.text();
                    console.error("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return null;
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    console.error("Erro ao cadastrar atendimento: " + data.mensagem);
                    return;
                }
                else if (data.mensagem) {
                    console.log("Atendimento cadastrado com sucesso: " + data.mensagem);
                }

            })
            .catch(error => {
                console.error("Erro ao cadastrar atendimento:", error);
            });

    } catch (error) {
        console.error("Erro ao enviar dados do usuário:", error);
    }



}

function setupDados(dados) {
    imovel = JSON.parse(dados);
    var div = document.getElementById("dados-imovel");
    let imagensHtml = "";
    let swiperhtml = "";
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
    divTitulo.querySelector("span p").innerText = `${imovel.endereco.rua}, ${imovel.endereco.numero}, ${imovel.endereco.bairro}`;

    divGaleria.innerHTML = imagensHtml;
    pDescricao.innerText = imovel.anuncio.descricao;
    swiperWrapper.innerHTML = swiperhtml;

    if (imovel.valor_aluguel && imovel.valor_venda) {
        document.querySelectorAll("#div-titulo h3")[1].innerText = formatarValor(imovel.valor_venda) + " | " + formatarValor(imovel.valor_aluguel);
        document.querySelector("#entrar-contato #valor-venda").innerText = formatarValor(imovel.valor_venda);
        document.querySelector("#entrar-contato #valor-aluguel").innerText = formatarValor(imovel.valor_aluguel);
    } else if (imovel.valor_venda) {
        document.querySelectorAll("#div-titulo h3")[1].innerText = formatarValor(imovel.valor_venda);
        document.querySelector("#entrar-contato #valor-venda").innerText = formatarValor(imovel.valor_venda);
        document.querySelector("#entrar-contato #valor-aluguel").style.display = "none";
        document.querySelector("#entrar-contato #label-aluguel").style.display = "none";
    } else if (imovel.valor_aluguel) {
        document.querySelectorAll("#div-titulo h3")[1].innerText = formatarValor(imovel.valor_aluguel);
        document.querySelector("#entrar-contato #valor-aluguel").innerText = formatarValor(imovel.valor_aluguel);
        document.querySelector("#entrar-contato #valor-venda").style.display = "none";
        document.querySelector("#entrar-contato #label-venda").style.display = "none";
    }


    document.querySelector("#entrar-contato #condominio").innerText = formatarValor(imovel.valor_condominio ?? 0);
    document.querySelector("#entrar-contato #iptu").innerText = formatarValor(imovel.valor_iptu ?? 0);
    document.querySelector("#entrar-contato #area-total").innerText = `${imovel.area_total ?? '0.00'} m²`;
    document.querySelector("#entrar-contato #area-privativa").innerText = `${imovel.area_privativa ?? '0.00'} m²`;
    document.querySelector("#entrar-contato #quartos").innerText = imovel.quantidade_quartos ?? 0;
    // document.querySelector("#entrar-contato #suite").innerText = imovel.suite;
    document.querySelector("#entrar-contato #banheiros").innerText = imovel.quantidade_banheiros ?? 0;
    document.querySelector("#entrar-contato #vagas").innerText = imovel.quantidade_vagas ?? 0;


    // console.log(imovel.filtros);

    if (imovel.filtros) {
        const divFiltros = document.createElement("div");
        divFiltros.className = "div-filtros";
        for (const filtro of imovel.filtros) {
            divFiltros.innerHTML += `<input type="checkbox" checked disabled><label>${filtro}</label>`;
        }
        divPai.appendChild(divFiltros);
    }

    if (imovel.condominio?.filtros) {
        const divFiltros = document.createElement("div");
        divFiltros.className = "div-filtros";
        for (const filtro of imovel.condominio.filtros) {
            divFiltros.innerHTML += `<input type="checkbox" checked disabled><label>${filtro}</label>`;
        }
        divPai.appendChild(divFiltros);
    }

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