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
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/js_controller.php?acao=listar_imoveis"
        );
        const resposta = await fetch(caminho)
            .then(res => res.json())
            .then(data => {
                return data;
                console.log(data);
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
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/js_controller.php?acao=listar_imoveis_disponiveis"
        );
        const resposta = await fetch(caminho)
            // .then(res => console.log(res))
            .then(res => res.json() ||  console.log(res.text()) )
            .then(data => {
                // console.log(data);
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


async function getDadosImovel(id) {
    console.log("Buscando dados do imóvel com id:", id);
    try {
        let caminho = window.location.pathname;
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/js_controller.php?acao=get_dados_imovel&id=" + id
        );
        const resposta = await fetch(caminho, {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
            }
        );

        if (!resposta.ok) {
            throw new Error(`HTTP ${resposta.status}`);
        }

        return await resposta.json();
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }

}

async function deslogar() {
    try {
        let caminho = window.location.pathname;
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/js_controller.php?acao=deslogar"
        );
        const resposta = await fetch(caminho, {
            method: "POST"
        });
        if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
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
        }
        console.log("Deslogado com sucesso!");
        if (window.location.pathname.endsWith("index.html") || window.location.pathname.endsWith("/")) {
            window.location.reload();
            return;
        } else {
            window.location.href = "../index.html";
        }
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

async function carregarUser() {
    try {
        let caminho = window.location.pathname;
        caminho = caminho.replace(
            caminho.substring(caminho.lastIndexOf("/")),
            "/php/js_controller.php?acao=get_usuario"
        );
        const resposta = await fetch(caminho, {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
        });
        if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
        const dados = await resposta.json();
        return dados.tipo;
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

    switch (usuario) {
        case 'ADMIN':
            tabs = [
                { text: "Atendimento", href: "atendimento.html" },
                { text: "Estoque", href: "estoque.html" }
            ];
            cadastros = [
                { text: "Imóveis", href: "cadastro-imovel.html" },
                { text: "Venda/Aluguel", href: "cadastro-venda-aluguel.html" },
                { text: "Cliente", href: "cadastro-cliente.html" },

            ];
            dados = [
                { text: "Imobiliária", href: "dados-imobiliaria.html" },
                { text: "Dados Cliente", href: "dados-cliente.html" }
            ];
            break;

        case "CORRETOR":
            tabs = [
                { text: "Atendimento", href: "atendimento.html" },
                { text: "Estoque", href: "estoque.html" }
            ];
            cadastros = [
                { text: "Imóveis", href: "cadastro-imovel.html" },
                { text: "Venda/Aluguel", href: "cadastro-venda-aluguel.html" },
                { text: "Cliente", href: "cadastro-cliente.html" },
            ];
            break;

        case "GERENTE":
            tabs = [
                { text: "Estoque", href: "estoque.html" }
            ];
            cadastros = [
                { text: "Imobiliária", href: "dados-imobiliaria.html" }
            ];
            dados = [
                { text: "Imobiliária", href: "dados-imobiliaria.html" },
            ];
            break;

        case "CAPTADOR":
            tabs = [
                { text: "Estoque", href: "estoque.html" }
            ];
            cadastros = [
                { text: "Imóveis", href: "cadastro-imovel.html" },
                { text: "Cliente", href: "cadastro-cliente.html" },
            ];
            break;

        case "CLIENTE":
            tabs = [
                { text: "Dados Cliente", href: "dados-cliente.html" }
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
    }
    div = nav.querySelector(".right");
    if (div) {
        div.innerHTML = html + `<li><a href="#" id="logout">Sair</a></li>`;
    }

}
async function setup() {
    const usuario = await carregarUser();
    if (usuario) carregarTabs(usuario);
}


setup();
