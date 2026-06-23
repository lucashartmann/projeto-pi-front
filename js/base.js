function formatarValor(valor) {
    const formatoMoeda = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(valor);
    return formatoMoeda;
}

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

function getCaminhoRelativo(destino) {
    let caminho = window.location.pathname;

    substring = "";

    if (caminho.includes("/html/")) {
        caminho = caminho.replace(caminho.substring(caminho.lastIndexOf("/html/")), "/");
    }

    if (caminho.includes("/index.html")) {
        caminho = caminho.replace(caminho.substring(caminho.lastIndexOf("/index.html")), "/");
    }

    if (caminho.slice(-2) != "//") {
        caminho += "/";
    }

    const regex = new RegExp("/" + "$");

    // console.log(caminho)

    caminho = caminho.replace(regex, destino);

    return caminho;
}



function alterarSrc(event, caminho) {
    let a = event.target;
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

async function listarImoveis() {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=listar_imoveis");
        const resposta = await fetch(caminho)
            .then(async (res) => {
                const contentType = res.headers.get("content-type");
                if (res.erro) {
                    alert("Erro ao listar atendimentos: " + res.erro);
                    return null;
                }
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    alert("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return;
                }
            })
            .then(async (data) => {
                if (data.status == "erro") {
                    alert("Erro ao listar imóveis: " + data.mensagem);
                    return null;
                }
                return data;
            })
            .catch(erro => {
                console.error("Falha ao conectar com o backend:", erro);
                return null;
            });

        return resposta;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

function removerCardPessoa(container, event) {
    if (document.body.contains(container) && !container.contains(event.target)) {
            const checkboxes = container.querySelectorAll("input[type='checkbox']");
            const selecionados = Array.from(checkboxes).filter(checkbox => checkbox.checked);
            if (selecionados.length > 0) {
                for (let checkbox of selecionados) {
                    let containerPessoa = checkbox.closest(".resultado-pessoa");
                    switch (tipo) {
                        case "proprietario":
                            containerPessoa.classList.add("pessoa-selecionada");
                            document.getElementById("container-proprietario").appendChild(containerPessoa.cloneNode(true));
                            break;
                        case "corretor":
                            document.getElementById("container-corretor").appendChild(containerPessoa.cloneNode(true));
                            break;
                        case "captador":
                            document.getElementById("container-captador").appendChild(containerPessoa.cloneNode(true));
                            break;
                    }

                }
            }
            document.body.removeChild(container);
            document.removeEventListener("click", removerCardPessoa);
            console.log("Card removido");
        }

}

async function listarImoveisDisponiveis() {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=listar_imoveis_disponiveis");
        const resposta = await fetch(caminho)
            // .then(res => console.log(res))
            .then(async (res) => {
                if (res.erro) {
                    alert("Erro ao listar atendimentos: " + res.erro);
                    return null;
                }
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return await res.json();
                } else {
                    const texto = await res.text();
                    // alert("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return null;
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

        return resposta;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}


async function getDadosImovel(id) {
    try {
        let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=get_dados_imovel&id=" + id);
        const resposta = await fetch(caminho, {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
        }
        );

        if (resposta.erro) {
            alert("Erro ao listar atendimentos: " + resposta.erro);
            return null;
        }

        const contentType = resposta.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return await resposta.json();
        } else {
            const texto = await resposta.text();
            alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
        }

        if (!resposta.ok) {
            throw new Error(`HTTP ${resposta.status}`);
        }


    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }

}


async function deslogar() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=deslogar");
        const resposta = await fetch(caminho, {
            method: "POST"
        });
        if (resposta.erro) {
            alert("Erro ao listar atendimentos: " + resposta.erro);
            return null;
        }
        if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
        const contentType = resposta.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            dados = await resposta.json();
        } else {
            const texto = await resposta.text();
            alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
        }
        if (dados.status == "sucesso") {
            const nav = document.querySelector("nav ul");
            if (nav) {
                for (const li of nav.children) {
                    const a = li.querySelector("a");
                    if (a && a.innerText === "Sair") {
                        a.innerText = "Logar";
                        a.removeEventListener("click", deslogar);
                        a.href = "login.html";
                    }
                }
            } else {
                console.warn("Elemento de navegação não encontrado para atualizar estado de login");
                return;
            }

            console.log("Deslogado com sucesso!");
            if (window.location.pathname.endsWith("index.html") || window.location.pathname.endsWith("/")) {
                window.location.reload();
                return;
            } else {
                window.location.href = "../index.html";
            }
        }
        else {
            console.warn("Erro ao deslogar: " + dados.mensagem);
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

async function carregarUser() {
    try {
        let caminho = getCaminhoRelativo("/php/api/login.php?acao=get_usuario");
        const resposta = await fetch(caminho, {
            method: "GET"
        });
        if (resposta.erro) {
            alert("Erro ao listar atendimentos: " + resposta.erro);
            return null;
        }
        if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
        const contentType = resposta.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            dados = await resposta.json();
        } else {
            const texto = await resposta.text();
            // alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return null;
        }

        if (dados.status == "erro") {
            console.error("Erro ao carregar usuário: " + dados.mensagem);
            return null;
        }
        return dados;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

function carregarTabs(usuario) {
    const nav = document.getElementById("top-nav");

    if (!nav) return;

    let tabs = [];
    let cadastros = [];

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
    div = nav.querySelector(".right");
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
    const usuario = await carregarUser();
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



// window.addEventListener("scroll", () => {
//     const nav = document.querySelector("#top-nav");
//     if (window.scrollY > 50) {
//         nav.classList.add("scrolled");
//     } else {        nav.classList.remove("scrolled");
//     }
// });