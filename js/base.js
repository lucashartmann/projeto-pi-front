
function alterarSrc(event) {
    const a = event.target;
    const href = a.getAttribute("href");

    let caminho = window.location.pathname;
    if (caminho.includes("/html/")) {
        caminho = caminho.replace("/html/", "/");
    }
    // caminho += "/";
    caminho = caminho.replace(
        caminho.substring(caminho.lastIndexOf("/") + 1),
        href
    );

    a.setAttribute("href", caminho);
}

function mostrarNavLeft() {
    try {
        const nav = document.getElementById("side-nav");
        if (nav.style.display != "") {
            nav.style.display = "";
        } else {
            nav.style.display = "flex";
        }
    } catch {
        return;
    }
}

async function listarImoveis() {
    try {
        let caminho = window.location.pathname;
        if (caminho.includes("/html/")) {
            caminho = caminho.replace("/html/", "/");
        }
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/api/imoveis.php?acao=listar_imoveis"
        );
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

async function listarImoveisDisponiveis() {
    try {
        let caminho = window.location.pathname;
        if (caminho.includes("/html/")) {
            caminho = caminho.replace("/html/", "/");
        }
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/api/imoveis.php?acao=listar_imoveis_disponiveis"
        );
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
                    alert("Resposta inesperada do servidor");
                    console.error("Resposta não é JSON:", texto);
                    return;
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
        let caminho = window.location.pathname;
        if (caminho.includes("/html/")) {
            caminho = caminho.replace("/html/", "/");
        }
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/api/imoveis.php?acao=get_dados_imovel&id=" + id
        );
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
        let caminho = window.location.pathname;
        if (caminho.includes("/html/")) {
            caminho = caminho.replace("/html/", "/");
        }
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/api/login.php?acao=deslogar"
        );
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
        let caminho = window.location.pathname;
        substring = "";

        if (caminho.includes("/html/")) {
            caminho = caminho.replace("/html/", "/");
        }

        caminho = caminho.replace(caminho.substring(caminho.lastIndexOf("/"))
            , "/php/api/login.php?acao=get_usuario"
        );
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
            alert("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return;
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
                { text: "Atendimento", href: "../../projeto-pi-front/html/atendimento.html" },
                { text: "Estoque", href: "../../projeto-pi-front/html/estoque.html" },
                { text: "Agendar Visita", href: "../../projeto-pi-front/html/agendar-visita.html" }
            ];
            cadastros = [
                { text: "Imóveis", href: "../../projeto-pi-front/html/cadastro-imovel.html" },
                { text: "Cliente", href: "../../projeto-pi-front/html/cadastro-cliente.html" },
                { text: "Contratos", href: "../../projeto-pi-front/html/contratos.html" }
            ];
            dados = [
                { text: "Imobiliária", href: "../../projeto-pi-front/html/dados-imobiliaria.html" },
                { text: "Dados Cliente", href: "../../projeto-pi-front/html/dados-cliente.html" }
            ];
            break;

        case "FINANCEIRO":
            tabs = [
                { text: "Contratos", href: "../../projeto-pi-front/html/contratos.html" }
            ];
            break;

        case "VISTORIADOR":
            tabs = [
                { text: "Agendar Vistoria", href: "../../projeto-pi-front/html/agendar-visita.html" },
                { text: "Relatório", href: "../../projeto-pi-front/html/relatorio.html" }
            ];
            break;

        case "CORRETOR":
            tabs = [
                { text: "Atendimento", href: "../../projeto-pi-front/html/atendimento.html" },
                { text: "Estoque", href: "../../projeto-pi-front/html/estoque.html" },
                { text: "Agendar Visita", href: "../../projeto-pi-front/html/agendar-visita.html" }
            ];
            cadastros = [
                { text: "Imóveis", href: "../../projeto-pi-front/html/cadastro-imovel.html" },
                { text: "Venda/Aluguel", href: "../../projeto-pi-front/html/cadastro-venda-aluguel.html" },
                { text: "Cliente", href: "../../projeto-pi-front/html/cadastro-cliente.html" },
            ];
            break;

        case "GERENTE":
            tabs = [
                { text: "Estoque", href: "../../projeto-pi-front/html/estoque.html" }
            ];
            cadastros = [
                { text: "Imobiliária", href: "../../projeto-pi-front/html/dados-imobiliaria.html" }
            ];
            dados = [
                { text: "Imobiliária", href: "../../projeto-pi-front/html/dados-imobiliaria.html" },
            ];
            break;

        case "CAPTADOR":
            tabs = [
                { text: "Estoque", href: "../../projeto-pi-front/html/estoque.html" }
            ];
            cadastros = [
                { text: "Imóveis", href: "../../projeto-pi-front/html/cadastro-imovel.html" },
                { text: "Cliente", href: "../../projeto-pi-front/html/cadastro-cliente.html" },
            ];
            break;

        case "CLIENTE":
            tabs = [
                { text: "<i class='fas fa-user'></i>", href: "../../projeto-pi-front/html/dados-cliente.html" },

            ];
            break;
    }


    let html = tabs.map(tab =>
        `<li><a href="${tab.href}">${tab.text}</a></li>`
    ).join("");

    if (dados.length > 0 && cadastros.length > 0) {
        html += `
        <li class="dropdown">
            <a href="#">Cadastro ▾</a>
            <div class="dropdown-content">
                ${cadastros.map(c =>
            `<a href="${c.href}">${c.text}</a>`
        ).join("")}
            </div>
        </li>
        <li class="dropdown">
            <a href="#">Dados ▾</a>
            <div class="dropdown-content">
                ${dados.map(d =>
            `<a href="${d.href}">${d.text}</a>`
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
            `<a href="${c.href}">${c.text}</a>`
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
            `<a href="${d.href}">${d.text}</a>`
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
async function setup() {
    const usuario = await carregarUser();
    if (usuario) carregarTabs(usuario);
    const topNav = document.querySelector("#top-nav .fa-bars");

    topNav.addEventListener("mouseover", mostrarNavLeft);
    const nav = document.getElementById("side-nav");
    nav.addEventListener("mouseleave", () => {
        nav.style.display = "none";
    });
}


setup();


window.addEventListener("scroll", () => {
    const nav = document.querySelector("#top-nav");
    if (window.scrollY > 50) {
        nav.classList.add("scrolled");
    } else {        nav.classList.remove("scrolled");
    }
});