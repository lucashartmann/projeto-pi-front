async function listarImoveis() {
    try {
        const resposta = await fetch("http://127.0.0.1:8000/estoque/", {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
        });

        if (!resposta.ok) {
            throw new Error(`HTTP ${resposta.status}`);
        }

        return await resposta.json();
    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return null;
    }
}

async function listarImoveisDisponiveis() {
    try {
        const resposta = await fetch("http://127.0.0.1:8000/estoque/disponivel/", {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
        });

        if (!resposta.ok) {
            throw new Error(`HTTP ${resposta.status}`);
        }

        return await resposta.json();
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
    switch (usuario) {
        case 'ADMIN':
            tabs = [
                { text: "Atendimento", id: "tab_atendimento", href: "atendimento.html" },
                { text: "Cadastro de Imoveis", id: "tab_cadastro_imovel", href: "cadastro-imovel.html" },
                { text: "Estoque", id: "tab_estoque", href: "estoque.html" },
                { text: "Dados Cliente", id: "tab_dados_cliente", href: "dados-cliente.html" },
                { text: "Dados da imobiliaria", id: "tab_dados_imobiliaria", href: "dados-imobiliaria.html" },
                { text: "Cadastro de Venda/Aluguel", id: "tab_cadastro_venda_aluguel", href: "cadastro-venda-aluguel.html" }
            ];
            break;
        case "CORRETOR":
            tabs = [
                { text: "Atendimento", id: "tab_atendimento", href: "atendimento.html" },
                { text: "Cadastro de Imoveis", id: "tab_cadastro_imovel", href: "cadastro-imovel.html" },
                { text: "Cadastro de Venda/Aluguel", id: "tab_cadastro_venda_aluguel", href: "cadastro-venda-aluguel.html" },
                { text: "Estoque", id: "tab_estoque", href: "estoque.html" }
            ];
            break;
        case "GERENTE":
            tabs = [
                { text: "Dados da imobiliaria", id: "tab_dados_imobiliaria", href: "dados-imobiliaria.html" },
                { text: "Estoque", id: "tab_estoque", href: "estoque.html" }
            ];
            break;
        case "CAPTADOR":
            tabs = [
                { text: "Cadastro de Imoveis", id: "tab_cadastro_imovel", href: "cadastro-imovel.html" },
                { text: "Estoque", id: "tab_estoque", href: "estoque.html" }
            ];
            break;
        case "CLIENTE":
            tabs = [
                { text: "Dados Cliente", id: "tab_dados_cliente", href: "dados-cliente.html" }
            ];
            break;
    }
    // tabs.push({ text: "Login", id: "tab_login", href: "login.html" });
    // tabs.unshift({ text: "Página Inicial", id: "tab_inicio", href: "../index.html" });
    nav.innerHTML += tabs.map(tab => `<li><a id="${tab.id}" href="${tab.href}">${tab.text}</a></li>`).join("");
    for (const li of nav.children) {
        const a = li.querySelector("a");
        if (a && a.innerText === "Login") {
            a.innerText = "Sair";
            a.addEventListener("click", deslogar);
            a.href = "#";
        }
    }
}

async function setup() {
    const usuario = await carregarUser();
    if (usuario) carregarTabs(usuario);
}

window.addEventListener("DOMContentLoaded", () => {
    setup();
});
