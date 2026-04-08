async function listarImoveis() {
    try {
        const resposta = await fetch('../php/api/api.php')
        .then(res => res.json())
        .then(data => {
            return data;
        })
        .catch(erro => {
            console.error("Falha ao conectar com o backend:", erro);
            return null;
        });
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

async function listarImoveisDisponiveis() {
    try {
        const resposta = await fetch('../php/api/api.php')
        .then(res => res.json())
        .then(data => {
        
            return data;
        })
        .catch(erro => {
            console.error("Falha ao conectar com o backend:", erro);
            return null;
        });
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}


async function getDadosImovel(id) {
    console.log("Buscando dados do imóvel com id:", id);
    try {
        const resposta = await fetch(`http://127.0.0.1:8000/estoque/${id}/`,
            {
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
        const resposta = await fetch("http://127.0.0.1:8000/deslogar/", {
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
        const resposta = await fetch("http://127.0.0.1:8000/usuario/");
        if (!resposta.ok) throw new Error(`HTTP ${resposta.status}`);
        const dados = await resposta.json();
        return dados.tipo;
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

function carregarTabs(usuario) {
    const nav = document.querySelector("nav ul");
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
   
    }else if (cadastros.length > 0) {
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
    // const usuario = await carregarUser();
    // if (usuario) carregarTabs(usuario);
    carregarTabs("ADMIN"); // TODO: remover depois de implementar o login e deslogar
}

window.addEventListener("DOMContentLoaded", () => {
    setup();
});
