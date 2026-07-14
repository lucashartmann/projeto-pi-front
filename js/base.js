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

function aumentarFonte() {
    const root = document.documentElement;
    const style = getComputedStyle(root);
    const fontSize = parseFloat(style.fontSize);
    root.style.fontSize = (fontSize + 2) + 'px';
}

function diminuirFonte() {
    const root = document.documentElement;
    const style = window.getComputedStyle(root);
    const fontSize = parseFloat(style.fontSize);
    root.style.fontSize = (fontSize - 2) + 'px';
}

function altoContraste() {
    document.body.classList.toggle("alto-contraste");
}

function modoNoturno() {
    document.body.classList.toggle("modo-noturno");
}



function alterarSrc(event, caminho) {
    let a = event.target;
    if (a.textContent.includes("Favoritos")) {
        sessionStorage.setItem("favoritos", true);
    }
    if (a.tagName !== "a") {
        a = a.closest("a");
    }
    let caminhoRelativo = getCaminhoRelativo(caminho);
    a.setAttribute("href", caminhoRelativo);
}

function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.querySelector("main").style.opacity = "0.7";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.querySelector("main").style.opacity = "1";
}

// function removerCardPessoa(container, event) {
//     if (document.body.contains(container) && !container.contains(event.target)) {
//             const checkboxes = container.querySelectorAll("input[type='checkbox']");
//             const selecionados = Array.from(checkboxes).filter(checkbox => checkbox.checked);
//             if (selecionados.length > 0) {
//                 for (let checkbox of selecionados) {
//                     let containerPessoa = checkbox.closest(".resultado-pessoa");
//                     switch (tipo) {
//                         case "proprietario":
//                             containerPessoa.classList.add("pessoa-selecionada");
//                             document.getElementById("container-proprietario").appendChild(containerPessoa.cloneNode(true));
//                             break;
//                         case "corretor":
//                             document.getElementById("container-corretor").appendChild(containerPessoa.cloneNode(true));
//                             break;
//                         case "captador":
//                             document.getElementById("container-captador").appendChild(containerPessoa.cloneNode(true));
//                             break;
//                     }
//                 }
//             }
//             document.body.removeChild(container);
//             document.removeEventListener("click", removerCardPessoa);
//             console.log("Card removido");
//         }
// }


function carregarTabs(usuario) {
    const nav = document.getElementById("top-nav");
    if (!nav) return;
    let tabs = [];
    let cadastros = [];
    let dados = [];
    if (!usuario || !usuario.tipo) {
        console.warn("Tipo de usuário não encontrado:", usuario);
        return;
    }
    switch (usuario.tipo) {
        case 'ADMIN':
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
                { text: "Cliente", href: "html/cadastro-cliente.html" },
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
                { text: "Imobiliária", href: "html/dados-imobiliaria.html" },
            ];
            break;
        case "CAPTADOR":
            tabs = [
                { text: "Estoque", href: "html/estoque.html" }
            ];
            cadastros = [
                { text: "Imóveis", href: "html/cadastro-imovel.html" },
                { text: "Cliente", href: "html/cadastro-cliente.html" },
            ];
            break;
        case "CLIENTE":
            tabs = [
                { text: "Atendimento", href: "html/atendimento-cliente.html" },
                { text: "Favoritos", href: "html/anuncios.html" },
                { text: "<i class='fas fa-user'></i>", href: "html/dados-cliente.html" },

            ];
            break;
    }
    let html = tabs.map(tab =>
        `<li><a href="#" onclick="alterarSrc(event, '${tab.href}')">${tab.text}</a></li>`
    ).join("");
    if (dados.length > 0 && cadastros.length > 0) {
        html += `
        <li class="dropdown">
            <a href="#">Cadastro ▾</a>
            <div class="dropdown-content">
                ${cadastros.map(c =>
            `<a href="#" onclick="alterarSrc(event, '${c.href}')">${c.text}</a>`
        ).join("")}
            </div>
        </li>
        <li class="dropdown">
            <a href="#">Dados ▾</a>
            <div class="dropdown-content">
                ${dados.map(d =>
            `<a href="#" onclick="alterarSrc(event, '${d.href}')">${d.text}</a>`
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
            `<a href="#" onclick="alterarSrc(event, '${c.href}')">${c.text}</a>`
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
            `<a href="#" onclick="alterarSrc(event, '${d.href}')">${d.text}</a>`
        ).join("")}
            </div>
        </li>
        `;
    }
    let div = nav.querySelector(".right");
    if (div) {
        div.innerHTML = html + `<li><a href="#" onclick="deslogar()" id="logout">Sair</a></li>`;
    }
}

// function colide(el1, el2) {
//   const r1 = el1.getBoundingClientRect();
//   const r2 = el2.getBoundingClientRect();

//   return !(
//     r1.right < r2.left ||
//     r1.left > r2.right ||
//     r1.bottom < r2.top ||
//     r1.top > r2.bottom
//   );
// }

// function reorganizarMenu() {
//   const logo = document.getElementById("logo");
//   const elementoA = document.querySelector(".item-a");
//   const menuSecundario = document.querySelector(".menu-secundario");

//   if (!logo || !elementoA) return;

//   if (colide(elementoA, logo)) {
//   } else {
//   }
// }

// window.addEventListener("resize", reorganizarMenu);
// window.addEventListener("load", reorganizarMenu);

async function setup() {
    const usuario = usuarioLogado || await carregarUser();
    if (usuario) carregarTabs(usuario);
    // const topNav = document.querySelector("#top-nav .fa-bars");
    // if (topNav){
    //     topNav.addEventListener("click", mostrarNavLeft);
    // }
    if (document.getElementById("logo")) {
        document.getElementById("logo").src = getCaminhoRelativo("assets/logo.webp");
    }
}


setup();