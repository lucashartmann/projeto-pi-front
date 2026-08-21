import { usuarioLogado, carregarUser, deslogar } from "./modules/usuario.js";
import { getCaminhoRelativo } from "./modules/utils.js";

window.alterarSrc = alterarSrc;
window.aumentarFonte = aumentarFonte;
window.diminuirFonte = diminuirFonte;
window.altoContraste = altoContraste;
window.modoNoturno = modoNoturno;
window.openNav = openNav;
window.closeNav = closeNav;
window.deslogar = deslogar;

let notificacoes = [];


function aumentarFonte() {
    const root = document.documentElement;
    const style = getComputedStyle(root);
    const fontSize = parseFloat(style.fontSize);
    root.style.fontSize = (fontSize + 2) + 'px';
    localStorage.setItem("fontSize", root.style.fontSize);
}

function diminuirFonte() {
    const root = document.documentElement;
    const style = window.getComputedStyle(root);
    const fontSize = parseFloat(style.fontSize);
    root.style.fontSize = (fontSize - 2) + 'px';
    localStorage.setItem("fontSize", root.style.fontSize);
}

function altoContraste() {
    document.documentElement.classList.toggle("alto-contraste");

    if (document.documentElement.classList.contains("alto-contraste")) {
        localStorage.setItem("contraste", "alto");
    } else {
        localStorage.setItem("contraste", "normal");
    }
}

function modoNoturno() {
    document.documentElement.classList.toggle("modo-noturno");

    if (document.documentElement.classList.contains("modo-noturno")) {
        localStorage.setItem("tema", "escuro");
    } else {
        localStorage.setItem("tema", "claro");
    }
}



function alterarSrc(event, caminho) {
    let a = event.target;

    if (a.tagName !== "A") {
        a = a.closest("a");
    }
    let caminhoRelativo = getCaminhoRelativo(caminho);
    a.setAttribute("href", caminhoRelativo);
}

function openNav() {
    const overlay = document.createElement("div");
    overlay.className = "overlay";
    overlay.style.cssText = `
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 999;
    `;

    document.body.appendChild(overlay);

    document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
    document.querySelector('.overlay')?.remove();
    document.getElementById("mySidenav").style.width = "0";
}

function carregarTabs(usuario) {
    const nav = document.getElementById("top-nav");
    const navMobile = document.getElementById("mySidenav");

    if (!nav && !navMobile) return;

    let tabs = [];
    let cadastros = [];
    let dados = [];

    if (!usuario || !usuario.tipo) {
        console.warn("Tipo de usuário não encontrado:", usuario);
        return;
    }

    switch (usuario.tipo) {

        case "ADMIN":
            tabs = [
                { text: "Atendimento", href: "html/atendimento.html" },
                { text: "Estoque", href: "html/estoque.html" },
                { text: "Agendar Visita", href: "html/agendar-visita.html" }
            ];

            cadastros = [
                { text: "Imóveis", href: "html/cadastro-imovel.html" },
                { text: "Cliente", href: "html/cadastro-cliente.html" },
                { text: "Contratos", href: "html/contratos.html" }
            ];

            dados = [
                { text: "Imobiliária", href: "html/dados-imobiliaria.html" },
                { text: "Dados Cliente", href: "html/dados-cliente.html" }
            ];
            break;

        case "FINANCEIRO":
            tabs = [
                { text: "Contratos", href: "html/contratos.html" }
            ];
            break;

        case "VISTORIADOR":
            tabs = [
                { text: "Agendar Vistoria", href: "html/agendar-visita.html" },
                { text: "Relatório", href: "html/relatorio.html" }
            ];
            break;

        case "CORRETOR":
            tabs = [
                { text: "Atendimento", href: "html/atendimento.html" },
                { text: "Estoque", href: "html/estoque.html" },
                { text: "Agendar Visita", href: "html/agendar-visita.html" }
            ];

            cadastros = [
                { text: "Imóveis", href: "html/cadastro-imovel.html" },
                { text: "Venda/Aluguel", href: "html/cadastro-venda-aluguel.html" },
                { text: "Cliente", href: "html/cadastro-cliente.html" }
            ];
            break;

        case "GERENTE":
            tabs = [
                { text: "Estoque", href: "html/estoque.html" }
            ];

            cadastros = [
                { text: "Imobiliária", href: "html/dados-imobiliaria.html" }
            ];

            dados = [
                { text: "Imobiliária", href: "html/dados-imobiliaria.html" }
            ];
            break;

        case "CAPTADOR":
            tabs = [
                { text: "Estoque", href: "html/estoque.html" }
            ];

            cadastros = [
                { text: "Imóveis", href: "html/cadastro-imovel.html" },
                { text: "Cliente", href: "html/cadastro-cliente.html" }
            ];
            break;

        case "CLIENTE":
            tabs = [
                {
                    text: "Atendimento",
                    href: "html/atendimento-cliente.html"
                },
                {
                    text: "Favoritos",
                    href: "html/anuncios.html?favoritos=true"
                },
                {
                    text: "<i class='fas fa-user'></i>",
                    href: "html/dados-cliente.html"
                }
            ];
            break;
    }

    if (nav) {

        let html = tabs.map(tab =>
            `<li>
                <a href="${getCaminhoRelativo(tab.href)}">${tab.text}</a>
            </li>`
        ).join("");

        if (dados.length > 0 && cadastros.length > 0) {

            html += `
                <li class="dropdown">
                    <a href="#">Cadastro ▾</a>
                    <div class="dropdown-content">
                        ${cadastros.map(c =>
                `<a href="${getCaminhoRelativo(c.href)}">
                                ${c.text}
                        </a>`
            ).join("")}

                </div>
                </li>

                <li class="dropdown">
                    <a href="#">Dados ▾</a>
                    <div class="dropdown-content">
                        ${dados.map(d =>
                `<a href="${getCaminhoRelativo(d.href)}">
                                ${d.text}
                        </a>`
            ).join("")}

                </div>
                </li>
            `;

        } else if (cadastros.length > 0) {

            html += `
                <li class="dropdown">
                    <a href="#">Cadastro ▾</a>
                    <div class="dropdown-content">
                        ${cadastros.map(c =>
                `<a href="${getCaminhoRelativo(c.href)}">
                                ${c.text}
                    </a>`
            ).join("")}
                    </div>
                </li>
            `;

        } else if (dados.length > 0) {
            html += `
                <li class="dropdown">
                    <a href="#">Dados ▾</a>

                    <div class="dropdown-content">
                        ${dados.map(d =>
                `<a href="${getCaminhoRelativo(d.href)}">
                                ${d.text}
                            </a>`
            ).join("")}
                    </div>
                </li>
            `;
        }

        const div = nav.querySelector(".right");

        if (div) {

            html += `
                <li class="dropdown notificacoes">

                    <a href="#">
                        <i class="fas fa-bell"></i>
                        ${notificacoes.some(n => !n.lida)
                    ?
                    `<i class="fa fa-exclamation" aria-hidden="true" style="color: red;position: absolute; top: 5px; right: 0px;"></i>`
                    : ""
                }
                    </a>
                    <div class="dropdown-content notificacoes-content">
                        <p>Nenhuma Notificação</p>
                    </div>
                </li>
                <li>
                    <a href="#" onclick="deslogar()" id="logout">Sair</a>
                </li>
            `;

            div.innerHTML = html;

            const dropdownContent = document.querySelector(".notificacoes-content");

            if (dropdownContent) {
                dropdownContent.addEventListener("scroll", function () {
                    if (notificacoes) {
                        const elementos = dropdownContent.querySelectorAll("p");
                        elementos.forEach((elemento, index) => {
                            const rect = elemento.getBoundingClientRect();
                            if (rect.top >= 0 && rect.bottom <= window.innerHeight) {
                                if (notificacoes[index]) {
                                    notificacoes[index].lida = true;
                                }
                            }
                        });
                    }
                });

                dropdownContent.addEventListener("mouseleave", function () {

                    if (notificacoes) {

                        const alerta =
                            document.querySelector(".fa-exclamation");

                        if (alerta) {
                            alerta.remove();
                        }

                        const elementos =
                            dropdownContent.querySelectorAll("p");

                        elementos.forEach((elemento, index) => {

                            const rect =
                                elemento.getBoundingClientRect();

                            if (rect.top >= 0 && rect.bottom <= window.innerHeight) {
                                if (notificacoes[index]) {
                                    notificacoes[index].lida = true;
                                }
                            }
                        });
                        atualizarNotificacoes();
                    }
                });
            }
        }
    }

    if (navMobile) {
        let mobileHtml = "";
        mobileHtml += `<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        `;
        mobileHtml += tabs.map(tab => `
            <a href="${getCaminhoRelativo(tab.href)}">
                ${tab.text}
            </a>
        `).join("");

        if (cadastros.length > 0) {
            mobileHtml += `
                <div class="mobile-menu-group">
                    <button type="button" class="mobile-menu-title">
                        <span>Cadastro</span>
                        <span class="mobile-arrow">▾</span>
                    </button>
                    <div class="mobile-submenu">
                        ${cadastros.map(c => `
                            <a href="${getCaminhoRelativo(c.href)}">
                                ${c.text}
                            </a>
                        `).join("")}
                    </div>
                </div>
            `;
        }

        if (dados.length > 0) {
            mobileHtml += `
                <div class="mobile-menu-group">
                    <button type="button" class="mobile-menu-title">
                    <span>Dados</span>
                    <span class="mobile-arrow">▾</span>
                    </button>
                    <div class="mobile-submenu">
                        ${dados.map(d => `
                            <a href="${getCaminhoRelativo(d.href)}">
                                ${d.text}
                            </a>
                        `).join("")}
                    </div>
                </div>
            `;
        }

        mobileHtml += `
            <a href="#" class="mobile-notificacoes"><i class="fas fa-bell" style="margin-right:10px;"></i>Notificações</a>`;

        mobileHtml += `<a href="${getCaminhoRelativo('html/sobre-nos.html')}">Sobre Nós</a>
        `;

        mobileHtml += `
            <a href="#" onclick="deslogar()" id="mobile-logout">Sair</a>`;


        navMobile.innerHTML = mobileHtml;



        navMobile
            .querySelectorAll(".mobile-menu-title")
            .forEach(botao => {

                botao.addEventListener("click", function () {

                    const submenu =
                        this.nextElementSibling;

                    const aberto =
                        submenu.classList.toggle("aberto");

                    const seta =
                        this.querySelector(".mobile-arrow");

                    if (seta) {
                        seta.textContent =
                            aberto ? "▴" : "▾";
                    }
                });
            });
    }


}

async function marcarComoLida() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=marcar_como_lido");
        const resposta = await fetch(caminho, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ notificacoes: notificacoes })
        })
            .then(async (res) => {
                if (res.erro) {
                    alert("Erro ao marcar notificações como lidas: " + res.erro);
                    return [];
                }
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    console.error("Resposta não é JSON:", texto);
                    return [];
                }
            })
            .then(async (data) => {
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });
        if (resposta) {
            console.log("Notificações marcadas como lidas com sucesso.");
        } else {
            console.error("Resposta inválida ao marcar notificações como lidas:", resposta);
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
    }

}

async function carregarNotificacoes() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=get_notificacoes");
        const resposta = await fetch(caminho)
            // .then(res => console.log(res))
            .then(async (res) => {
                if (res.erro) {
                    alert("Erro ao listar notificações: " + res.erro);
                    return [];
                }
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    // alert("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return [];
                }
            })
            .then(async (data) => {
                // console.log(data);
                return await data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });
        if (resposta) {

            return resposta.notificacoes || [];
        } else {
            console.error("Resposta inválida ao obter dados da notificação:", resposta);
            return [];
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return [];
    }
}

function atualizarNotificacoes() {
    const dropdownContent = document.querySelector(".notificacoes-content");
    if (notificacoes && notificacoes.length > 0) {
        notificacoes = notificacoes.filter(n => {
            const dataNotificacao = new Date(n.data);
            const dataAtual = new Date();
            const diffDias = Math.floor((dataAtual - dataNotificacao) / (1000 * 60 * 60 * 24));
            return diffDias <= 5;
        });
        dropdownContent.innerHTML = notificacoes.map(n => `<p ${n.lida ? 'style=opacity:0.5' : ''}>${n.texto}</p>`).join("");
    } else {
        dropdownContent.innerHTML = "<p>Nenhuma Notificação</p>";
    }
}


async function setup() {
    const usuario = usuarioLogado || await carregarUser();
    notificacoes = await carregarNotificacoes();

    const nav = document.getElementById("top-nav");
    if (nav) {
        document.querySelector(".left").querySelector("a").href = getCaminhoRelativo("index.html");
        document.querySelector(".center").querySelector("a").href = getCaminhoRelativo("index.html");
        document.querySelectorAll('#top-nav li a').forEach(link => {
            const temImagemOuIcone = link.querySelector('img, i, svg');
            if (!temImagemOuIcone) {
                link.classList.add('so-texto');
            }
        });
    }

    if (usuario) {
        carregarTabs(usuario)
        if (notificacoes && notificacoes.length > 0) {
            atualizarNotificacoes();
        }
        document.querySelectorAll('#top-nav li a').forEach(link => {
            const temImagemOuIcone = link.querySelector('img, i, svg');
            if (!temImagemOuIcone) {
                link.classList.add('so-texto');
            }
        });
    } else {
        if (!nav) return;
        document.querySelector(".right") ? document.querySelector(".right").querySelectorAll("a")[0].href = getCaminhoRelativo("html/sobre-nos.html") : null;
        document.querySelector(".right") ? document.querySelector(".right").querySelectorAll("a")[1].href = getCaminhoRelativo("html/login.html") : null;
        document.querySelector(".sidenav").querySelectorAll("a")[1].href = getCaminhoRelativo("html/sobre-nos.html");
        document.querySelector(".sidenav").querySelectorAll("a")[2].href = getCaminhoRelativo("html/login.html");
    }

    if (document.getElementById("logo")) {
        document.getElementById("logo").src = getCaminhoRelativo("assets/logo.webp");
    }



    // if (document.querySelector('.fa-whatsapp') && CONFIG.whatsapp) {
    //     document.querySelector('.fa-whatsapp').addEventListener('click', function (event) {
    //         event.preventDefault();
    //         const numero = CONFIG.whatsapp;
    //         const url = `https://wa.me/${numero}`;
    //         window.open(url, '_blank');
    //     });
    // }

    // if (document.querySelector(".fa-phone") && CONFIG.whatsapp) {
    //     const p = document.querySelector(".fa-phone").closest("p");
    //     p.addEventListener("click", function (event) {
    //         event.preventDefault();
    //         const numero = CONFIG.whatsapp;
    //         const url = `tel:${numero}`;
    //         window.open(url, '_blank');
    //     });
    //     const telefoneFormatado = CONFIG.whatsapp.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    //     p.innerHTML = `<i class="fas fa-phone"></i> ${telefoneFormatado}`;
    // }

}


window.addEventListener("DOMContentLoaded", setup);

window.addEventListener("beforeunload", marcarComoLida);