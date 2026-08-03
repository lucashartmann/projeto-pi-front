import { getCaminhoRelativo, formatarValor } from "./modules/utils.js";
import { usuarioLogado, salvarImoveisCurtidos, curtirImovel, imoveisCurtidos, carregarUser } from "./modules/usuario.js";
import { getDadosImovel } from "./modules/imoveis.js";

window.abrirImagem = abrirImagem;
window.curtirImovel = curtirImovel;
window.cadastrarAtendimento = cadastrarAtendimento;
window.compartilharImovel = compartilharImovel;

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

function setupDados(imovel) {
    // let imovel = JSON.parse(dados);
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

        document.querySelector("#entrar-contato #label-venda").style.marginTop = "40px";
        document.querySelector("#entrar-contato #label-aluguel").style.marginTop = "10px";
        document.querySelector("#entrar-contato #valor-aluguel").style.marginBottom = "50px";

    } else if (imovel.valor_venda) {
        document.querySelectorAll("#div-titulo h3")[1].innerText = formatarValor(imovel.valor_venda);
        document.querySelector("#entrar-contato #valor-venda").innerText = formatarValor(imovel.valor_venda);
        document.querySelector("#entrar-contato #valor-aluguel").style.display = "none";
        document.querySelector("#entrar-contato #label-aluguel").style.display = "none";
        document.querySelector("#entrar-contato #label-venda").style.marginTop = "20px";
        document.querySelector("#entrar-contato #valor-venda").style.marginBottom = "50px";
    } else if (imovel.valor_aluguel) {
        document.querySelectorAll("#div-titulo h3")[1].innerText = formatarValor(imovel.valor_aluguel);
        document.querySelector("#entrar-contato #valor-aluguel").innerText = formatarValor(imovel.valor_aluguel);
        document.querySelector("#entrar-contato #valor-venda").style.display = "none";
        document.querySelector("#entrar-contato #label-venda").style.display = "none";
        document.querySelector("#entrar-contato #label-aluguel").style.marginTop = "20px";
        document.querySelector("#entrar-contato #valor-aluguel").style.marginBottom = "50px";
    }

    document.querySelector("#entrar-contato #condominio").innerText = formatarValor(imovel.valor_condominio != null ? imovel.valor_condominio : "n/a");
    document.querySelector("#entrar-contato #iptu").innerText = formatarValor(imovel.valor_iptu != null ? imovel.valor_iptu : "n/a");
    document.querySelector("#entrar-contato #area-total").innerText = `${imovel.area_total != null ? imovel.area_total : 'n/a'} m²`;
    document.querySelector("#entrar-contato #area-privativa").innerText = `${imovel.area_privativa != null ? imovel.area_privativa : 'n/a'} m²`;
    document.querySelector("#entrar-contato #quartos").innerText = imovel.quantidade_quartos != null ? imovel.quantidade_quartos : 'n/a';
    // document.querySelector("#entrar-contato #suite").innerText = imovel.suite;
    document.querySelector("#entrar-contato #banheiros").innerText = imovel.quantidade_banheiros != null ? imovel.quantidade_banheiros : 'n/a';
    document.querySelector("#entrar-contato #vagas").innerText = imovel.quantidade_vagas != null ? imovel.quantidade_vagas : "n/a";

    if (imovel.filtros) {
        const divFiltros = document.createElement("div");
        divFiltros.className = "div-filtros";
        for (const filtro of imovel.filtros) {
            divFiltros.innerHTML += `<input type="checkbox" checked disabled><label>${filtro}</label>`;
        }
        const h3Filtros = document.createElement("h3");
        h3Filtros.innerText = "Características do imóvel";
        divPai.appendChild(h3Filtros);
        divPai.appendChild(divFiltros);
    }

    if (imovel.condominio?.filtros) {
        const divFiltros = document.createElement("div");
        divFiltros.className = "div-filtros";
        for (const filtro of imovel.condominio.filtros) {
            divFiltros.innerHTML += `<input type="checkbox" checked disabled><label>${filtro}</label>`;
        }
        const h3Filtros = document.createElement("h3");
        h3Filtros.innerText = "Características do condomínio";
        divPai.appendChild(h3Filtros);
        divPai.appendChild(divFiltros);
    }

}

function adicionarClick() {
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
    var modal = document.createElement("div");
    modal.id = "modal-imagem";

    var img = document.createElement("img");
    img.src = src;

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

    var swiper = new Swiper('.swiper-galeria', {
        loop: true,
        pagination: {
            el: '.swiper-galeria .swiper-pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-galeria .swiper-button-next',
            prevEl: '.swiper-galeria .swiper-button-prev'
        },
        spaceBetween: 30,
        centeredSlides: true,
        breakpoints: {
            0: { slidesPerView: 1 },
            640: { slidesPerView: 2 },
            768: { slidesPerView: 3 },
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

function calcularPrecoMedio() {
    return;
}

function prevSlide() {
    if (window.swiperInstance) {
        window.swiperInstance.slidePrev();
    }
}