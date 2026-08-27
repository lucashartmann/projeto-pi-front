import { getCaminhoRelativo, formatarValor } from "./modules/utils.js";
import { usuarioLogado, curtirImovel, imoveisCurtidos, carregarUser } from "./modules/usuario.js";
import { getDadosImovel } from "./modules/imoveis.js";
import { buscarCoordenadas, carregarMapa } from "./modules/mapa.js";
import { buscarAnexoPorCaminho } from "./modules/anexos.js";

window.abrirImagem = abrirImagem;
window.curtirImovel = curtirImovel;
window.cadastrarAtendimento = cadastrarAtendimento;
window.compartilharImovel = compartilharImovel;
window.ativarImagem = ativarImagem;
window.nextSlide = nextSlide;
window.prevSlide = prevSlide;

let imovel = null;
let usuario = null;

async function compartilharImovel() {
    if (!navigator.share) {
        alert("Compartilhamento não suportado por este navegador.");
        return;
    }
    try {
        await navigator.share({
            title: imovel.anuncio.titulo,
            text: imovel.anuncio.descricao,
            url: window.location.href
        });
    } catch (error) {
        console.error("Erro ao compartilhar o imóvel:", error);
    }
}

async function cadastrarAtendimento() {
    if (!usuario) {
        alert("Você precisa estar logado para solicitar atendimento.");
        return;
    }
    alert("Um especialista irá entrar em contato por email ou whatsapp");
    try {
        let caminho = getCaminhoRelativo("/php/api/atendimentos.php?acao=cadastrar&usuario=true&idImovel=" + imovel.id);
        const resposta = await fetch(caminho, {
            method: "POST",
            body: JSON.stringify(imovel)
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

function ativarImagem(index) {
    document.querySelector('.swiper-destaque').swiper.slideTo(index);
    document.querySelector('.swiper-galeria').swiper.slideTo(index);
}

function aplicarLogoNoElemento(container, logo, anexo) {
    const antigo = container.querySelector(":scope > .sobrepor");
    if (antigo) antigo.remove();

    const divSobrepor = document.createElement("div");
    divSobrepor.classList.add("sobrepor");
    divSobrepor.style.position = "absolute";
    divSobrepor.style.right = "auto";
    divSobrepor.style.bottom = "auto";

    if (anexo?.largura != null) {
        divSobrepor.style.width = `${anexo.largura}%`;
    }

    if (anexo?.altura != null) {
        divSobrepor.style.height = `${anexo.altura}%`;
    }

    const img = document.createElement("img");
    img.src = "../assets/" + logo;
    divSobrepor.appendChild(img);

    container.appendChild(divSobrepor);

    const maxX = Math.max(0, container.clientWidth - divSobrepor.offsetWidth);
    const maxY = Math.max(0, container.clientHeight - divSobrepor.offsetHeight);

    const posX = anexo?.posicao_x;
    const posY = anexo?.posicao_y;

    if (posX != null) {
        divSobrepor.style.left = `${maxX * posX / 100}px`;
    }

    if (posY != null) {
        divSobrepor.style.top = `${maxY * posY / 100}px`;
    }
}


async function setupDados(imovel) {
    // let imovel = JSON.parse(dados);
    var div = document.getElementById("dados-imovel");
    let imagensHtml = "";
    let swiperhtml = "";
    let logoRequisicao = await buscarAnexoPorCaminho("logo.webp");
    let logo = logoRequisicao?.anexo?.caminho || null;

    if (imovel.anuncio.imagens && imovel.anuncio.imagens.length > 0) {
        swiperhtml = "";
        for (let i = 0; i < imovel.anuncio.imagens.length; i++) {
            const imagem = imovel.anuncio.imagens[i];
            swiperhtml += `<div class="swiper-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${imagem})" onclick="abrirImagem('${imagem}')"></div>`;
            imagensHtml += `<div class="swiper-slide" style="background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${imagem})" onclick="ativarImagem('${i}')"></div>`;
        }
    }

    if (logo && logoRequisicao) {
        requestAnimationFrame(() => {
            const slides = document.querySelectorAll('.swiper-destaque .swiper-slide');
            for (const slide of slides) {
                aplicarLogoNoElemento(slide, logo, logoRequisicao.anexo);
            }
        });
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

        document.querySelector("#entrar-contato #label-venda").style.marginTop = "40px";
        document.querySelector("#entrar-contato #label-aluguel").style.marginTop = "10px";
        document.querySelector("#entrar-contato #valor-aluguel").style.marginBottom = "30px";

    } else if (imovel.valor_venda) {
        document.querySelectorAll("#div-titulo h3")[1].innerText = formatarValor(imovel.valor_venda);
        document.querySelector("#entrar-contato #valor-venda").innerText = formatarValor(imovel.valor_venda);
        document.querySelector("#entrar-contato #valor-aluguel").style.display = "none";
        document.querySelector("#entrar-contato #label-aluguel").style.display = "none";
        document.querySelector("#entrar-contato #label-venda").style.marginTop = "20px";
        document.querySelector("#entrar-contato #valor-venda").style.marginBottom = "30px";
    } else if (imovel.valor_aluguel) {
        document.querySelectorAll("#div-titulo h3")[1].innerText = formatarValor(imovel.valor_aluguel);
        document.querySelector("#entrar-contato #valor-aluguel").innerText = formatarValor(imovel.valor_aluguel);
        document.querySelector("#entrar-contato #valor-venda").style.display = "none";
        document.querySelector("#entrar-contato #label-venda").style.display = "none";
        document.querySelector("#entrar-contato #label-aluguel").style.marginTop = "20px";
        document.querySelector("#entrar-contato #valor-aluguel").style.marginBottom = "30px";
    }

    document.querySelector("#entrar-contato #condominio").innerText = formatarValor(imovel.valor_condominio != null ? imovel.valor_condominio : "n/a");
    document.querySelector("#entrar-contato #iptu").innerText = formatarValor(imovel.valor_iptu != null ? imovel.valor_iptu : "n/a");
    document.querySelector("#entrar-contato #area-total").innerText = `${imovel.area_total != null ? imovel.area_total : 'n/a'} m²`;
    document.querySelector("#entrar-contato #area-privativa").innerText = `${imovel.area_privativa != null ? imovel.area_privativa : 'n/a'} m²`;
    document.querySelector("#entrar-contato #quartos").innerText = imovel.quantidade_quartos != null ? imovel.quantidade_quartos : 'n/a';
    document.querySelector("#entrar-contato #suites").innerText = imovel.quantidade_suites != null ? imovel.quantidade_suites : 'n/a';
    document.querySelector("#entrar-contato #banheiros").innerText = imovel.quantidade_banheiros != null ? imovel.quantidade_banheiros : 'n/a';
    document.querySelector("#entrar-contato #vagas").innerText = imovel.quantidade_vagas != null ? imovel.quantidade_vagas : "n/a";

    document.querySelector('#categoria').innerText = imovel.categoria ?? "n/a";

    if (imovel.filtros && imovel.filtros.length > 0) {
        const divFiltros = document.createElement("div");
        divFiltros.className = "div-filtros";
        console.log("Filtros do imóvel:", imovel.filtros);
        for (const filtro of imovel.filtros) {
            if (!filtro || filtro.trim() === "") {
                console.warn("Filtro inválido encontrado:", filtro);
                continue;
            }
            divFiltros.innerHTML += `<input type="checkbox" checked disabled><label>${filtro}</label>`;
        }
        const h3Filtros = document.createElement("h3");
        h3Filtros.innerText = "Características do imóvel";
        // divPai.appendChild(h3Filtros, before #mapa);
        divPai.insertBefore(h3Filtros, document.getElementById("mapa"));
        divPai.insertBefore(divFiltros, document.getElementById("mapa"));
    }

    if (imovel.condominio?.filtros && imovel.condominio.filtros.length > 0) {
        const divFiltros = document.createElement("div");
        divFiltros.className = "div-filtros";
        for (const filtro of imovel.condominio.filtros) {
            if (!filtro || filtro.trim() === "") {
                console.warn("Filtro inválido encontrado:", filtro);
                continue;
            }
            divFiltros.innerHTML += `<input type="checkbox" checked disabled><label>${filtro}</label>`;
        }
        const h3Filtros = document.createElement("h3");
        h3Filtros.innerText = "Características do condomínio";
        divPai.appendChild(h3Filtros);
        divPai.appendChild(divFiltros);
    }

    if (imovel.endereco?.cep) {
        const endereco =
            `${imovel.endereco.rua},
                ${imovel.endereco.numero},
                ${imovel.endereco.cidade},
                ${imovel.endereco.uf},
                Brasil`;

        const coordenadas = await buscarCoordenadas(endereco);

        if (coordenadas) {

            carregarMapa(
                coordenadas.lat,
                coordenadas.lng
            );

        }
    }
}

async function adicionarClick() {
    if (!imovel || !imovel.id) {
        return;
    }
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=cadastrarClick&id=" + imovel.id);
        const resposta = await fetch(caminho, {
            method: "POST",
        })
            .then(async response => {
                if (response.erro) {
                    return false;
                }
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await response.json();
                } else {
                    const texto = await response.text();
                    return false;
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    return false;
                }
                else if (data.mensagem) {
                    return true;
                }

            })
            .catch(error => {
                return false;
            });

    } catch (error) {
        return false;
    }
}


window.addEventListener("DOMContentLoaded", async () => {
    const id = new URLSearchParams(window.location.search).get("id");
    if (!id) {
        alert("ID do imóvel não fornecido!");
        window.location.href = getCaminhoRelativo
            ("index.html");
        return;
    }
    imovel = await getDadosImovel(id);
    usuario = usuarioLogado ?? await carregarUser();

    if (!imovel) {
        alert("Imóvel não encontrado!");
        window.location.href = getCaminhoRelativo("index.html");
        return;
    }

    if (usuario && usuario.tipo == 'CLIENTE') {
        adicionarClick();
    }

    sessionStorage.removeItem("dados_imovel");

    await setupDados(imovel);
    await inicializarSwiper();

});

function abrirImagem(src) {
    // document.querySelector(".swiper-destaque").swiper.slideTo(imovel.anuncio.imagens.indexOf(src));
    const overlay = document.createElement("div");
    overlay.className = "overlay";
    overlay.style.cssText = `
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 999;
    `;



    if (!document.querySelector(".swiper-destaque").classList.contains("modal-imagem")) {
        document.body.appendChild(overlay);
    } else {
        document.querySelector('.overlay')?.remove();
    }

    document.querySelector(".swiper-destaque").classList.toggle("modal-imagem");

    // document.addEventListener("click", function () {
    //     document.querySelector(".swiper-destaque").classList.remove("modal-imagem");
    // });


}

function inicializarSwiper() {
    if (!document.querySelector('.swiper')) {
        console.warn("Elemento .swiper não encontrado");
        return;
    }

    var swiper = new Swiper('.swiper-destaque', {
        loop: imovel.anuncio.imagens.length > 1 ? true : false,
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

    var swiper = new Swiper('.swiper-galeria', {
        loop: imovel.anuncio.imagens.length > 1 ? true : false,
        pagination: {
            el: '.swiper-galeria .swiper-pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-galeria .swiper-button-next',
            prevEl: '.swiper-galeria .swiper-button-prev'
        },
        spaceBetween: 30,
        centeredSlides: false,
        breakpoints: {
            0: { slidesPerView: imovel.anuncio.imagens.length > 1 ? 1 : imovel.anuncio.imagens.length },
            640: { slidesPerView: imovel.anuncio.imagens.length > 2 ? 2 : imovel.anuncio.imagens.length },
            768: { slidesPerView: imovel.anuncio.imagens.length > 3 ? 3 : imovel.anuncio.imagens.length },
            1024: { slidesPerView: imovel.anuncio.imagens.length > 4 ? 4 : imovel.anuncio.imagens.length },
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