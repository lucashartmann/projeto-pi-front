const imoveisCurtidos = [];
let dadosImoveis = null;

async function salvarImoveisCurtidos() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=favoritar_imoveis");
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id_imoveis: imoveisCurtidos })
        })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    console.log("Imóveis curtidos salvos com sucesso");
                } else {
                    console.error("Erro ao salvar imóveis curtidos:", data.mensagem);
                }
            })
            .catch(err => {
                console.error("Erro na requisição para salvar imóveis curtidos:", err);
            });
    } catch (err) {
        console.error("Erro ao salvar imóveis curtidos:", err);
    }

}

var logado = false;

async function curtirImovel(imovelId) {
    if (!logado) {
        $usuario = await carregarUser();
        if (!$usuario) {
            alert("Você precisa estar logado para curtir um imóvel!");
            return;
        }
        else {
            logado = true;
        }
    }
    if (imoveisCurtidos.includes(imovelId)) {
        imoveisCurtidos.splice(imoveisCurtidos.indexOf(imovelId), 1);
        event.target.classList.remove("curtido");
        return;
    }
    imoveisCurtidos.push(imovelId);
    event.target.classList.toggle("curtido");
    event.stopPropagation();
}

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
    banner.innerHTML = "";
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
    var wrapper = document.querySelector(".swiper-destaque .swiper-wrapper");
    if (!wrapper) return;
    wrapper.innerHTML = "";
    for (var i = 0; i < 5; i++) {
        var imovel = dados[i];
        console.log("Imóvel do banner:", imovel);
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
        wrapper.innerHTML += `
        <div class="swiper-slide"  onclick="abrirAnuncio(${imovel.id})"> 
        <img src="${b64}" alt="${imovel.anuncio.titulo}"><div><h2>${imovel.anuncio.titulo}</h2>${precoVenda.outerHTML}${precoAluguel.outerHTML}<p>${imovel.anuncio.descricao}</p></div></div>
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

window.addEventListener('beforeunload', function (event) {
    if (imoveisCurtidos.length > 0) {
        salvarImoveisCurtidos();
    }
    // event.preventDefault();
    // event.returnValue = '';
});


async function carregarAnuncios(dados) {
    const section = document.getElementById("anuncios");
    if (!section || !dados) return;
    section.innerHTML = "";
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
                <i class="fas fa-heart" onclick="curtirImovel(${imovel.id})"></i>
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
                ${precoVenda.outerHTML}
                ${precoAluguel.outerHTML}
                <p class="descricao">${imovel.anuncio?.descricao}</p>
                <div class="emojis">
                    <i class="fas fa-ruler-combined"><p>${imovel.area_total || 'N/A'} m²</p></i> 
                    <i class="fas fa-bath"><p>${imovel.quantidade_banheiros || 'N/A'}</p></i> 
                    <i class="fas fa-couch"><p>${imovel.quantidade_salas || 'N/A'}</p></i> 
                    <i class="fas fa-bed"><p>${imovel.quantidade_quartos || 'N/A'}</p></i>
                    <i class="fas fa-car"><p>${imovel.quantidade_vagas || 'N/A'}</p></i>
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

async function abrirAnuncio(imovel_id) {
    if (event.target.classList.contains("swiper-button-prev") || event.target.classList.contains("swiper-button-next") || event.target.classList.contains("fa-heart") || event.target.classList.contains("fa-whatsapp")) {
        return;
    }
    // console.log(event.target.closest(".swiper-slide-active"));
    if (event.target.classList.contains("swiper-slide") && !event.target.classList.contains("swiper-slide-active")) {
        return;
    }
    // console.log("Abrindo anúncio do imóvel com ID:", imovel_id);
    // sessionStorage.setItem("imovel_id", imovel_id);
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
        const swiper = document.querySelector('.swiper-destaque').swiper;
        if (swiper) {
            swiper.slideNext();
        }
    }, 7500);
});