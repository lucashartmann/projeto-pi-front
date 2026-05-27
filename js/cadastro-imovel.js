

async function preencherEndereco(event) {
    let cep = event.target.value;

    if (cep.length > 7) {
        try {
            let inputRua = document.getElementById("ta-rua");
            let inputBairro = document.getElementById("ta-bairro");
            let inputCidade = document.getElementById("ta-cidade");
            let inputEstado = document.getElementById("ta-estado");
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json`);
            const data = await response.json();
            if (data.erro) {
                alert("CEP não encontrado!");
                return;
            }
            inputRua.value = data.logradouro || "";
            inputBairro.value = data.bairro || "";
            inputCidade.value = data.localidade || "";
            inputEstado.value = data.uf || "";
        } catch (Exception) {
            // alert("Erro ao buscar endereço pelo CEP!");
            console.error("Erro ao buscar endereço:", Exception);
        }
    };
}


async function listarPessoas(tipo) {
    if (tipo !== "proprietario") {
        try {
            let caminho = getCaminhoRelativo("/php/api/proprietarios.php?acao=listar");
            await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
                .then(async (response) => {
                    if (response.erro) {
                        alert("Erro ao listar atendimentos: " + response.erro);
                        return null;
                    }
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return await response.json();
                    } else {
                        const texto = await response.text();
                        alert("Resposta inesperada do servidor");
                        console.error("Resposta não é JSON:", texto);
                        return null;
                    }
                })
                .then(async (data) => {
                    if (data.status == "erro") {
                        alert("Erro ao listar pessoas: " + data.mensagem);
                        return null;
                    }
                    return data;

                })
                .catch(error => {
                    console.error("Falha ao conectar com o backend:", erro);
                    return null;
                });

        } catch (error) {
            console.error("Falha ao conectar com o backend:", erro);
            return null;
        }
    } else {
        try {
            let caminho = getCaminhoRelativo("/php/api/usuarios.php?acao=listar");
            await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
                .then(async (response) => {
                    if (response.erro) {
                        alert("Erro ao listar atendimentos: " + response.erro);
                        return null;
                    }
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return await response.json();
                    } else {
                        const texto = await response.text();
                        alert("Resposta inesperada do servidor");
                        console.error("Resposta não é JSON:", texto);
                        return null;
                    }
                })
                .then(async (data) => {
                    if (data.status == "erro") {
                        alert("Erro ao listar pessoas: " + data.mensagem);
                        return null;
                    }
                    return data;
                })
                .catch(error => {
                    console.error("Falha ao conectar com o backend:", erro);
                    return null;
                });

        } catch (error) {
            console.error("Falha ao conectar com o backend:", erro);
            return null;
        }
    }
}

async function editarPessoa(tipo) {
    if (tipo !== "proprietario" && tipo !== "corretor" && tipo !== "captador") {
        alert("Tipo de pessoa inválido!");
        return;
    }

    const lista = await listarPessoas(tipo);

    if (!lista || lista.length === 0) {
        alert("Nenhuma pessoa encontrada para editar!");
        return;
    }

    const container = document.createElement("div");
    const pesquisarInput = document.createElement("input");
    pesquisarInput.type = "text";
    pesquisarInput.placeholder = "Pesquisar...";
    pesquisarInput.oninput = function () {
        const query = pesquisarInput.value.toLowerCase();
        const resultados = container.querySelectorAll("div");
        resultados.forEach(resultado => {
            const nome = resultado.querySelector("label").textContent.toLowerCase();
            resultado.style.display = nome.includes(query) ? "block" : "none";
            const creciLabel = resultado.querySelector("label:nth-child(2)");
            if (creciLabel) {
                const creci = creciLabel.textContent.toLowerCase();
                resultado.style.display = (nome.includes(query) || creci.includes(query)) ? "block" : "none";
            }
            const emailLabel = resultado.querySelector("label:nth-child(3)");
            if (emailLabel) {
                const email = emailLabel.textContent.toLowerCase();
                resultado.style.display = (nome.includes(query) || email.includes(query)) ? "block" : "none";
            }
            const telefoneLabel = resultado.querySelector("label:nth-child(4)");
            if (telefoneLabel) {
                const telefone = telefoneLabel.textContent.toLowerCase();
                resultado.style.display = (nome.includes(query) || email.includes(query) || telefone.includes(query)) ? "block" : "none";
            }
        });
    };

    container.appendChild(pesquisarInput);
    for (const pessoa of lista) {
        const div_resultado = document.createElement("div");
        const nome = document.createElement("label");
        nome.textContent = pessoa.nome;
        div_resultado.appendChild(nome);
        container.appendChild(div_resultado);
        if (pessoa?.creci) {
            const creci = document.createElement("label");
            creci.textContent = "CRECI: " + pessoa.creci;
            div_resultado.appendChild(creci);
        }
        const email = document.createElement("label");
        email.textContent = "Email: " + pessoa.email;
        div_resultado.appendChild(email);
        const telefone = document.createElement("label");
        telefone.textContent = "Telefone: " + pessoa.telefone;
        div_resultado.appendChild(telefone);
    }

}

function salvarMultiplosForms() {
    const form1 = document.getElementById("container-cadastro");
    const form2 = document.getElementById("container-anuncio");

    if (!form1.checkValidity() || !form2.checkValidity()) {
        form1.reportValidity();
        form2.reportValidity();
        return;
    }

    salvar();
}

async function getOutrosDados(data) {
    const containerImagens = document.getElementById("container-imagens");
    const containerDocumentos = document.getElementById("container-documentos");
    const containerProprietario = document.getElementById("container-proprietario");
    const containerCorretor = document.getElementById("container-corretor");
    const containerCaptador = document.getElementById("container-captador");
    const imagens = containerImagens.querySelectorAll("img");
    for (let img of imagens) {
        const response = await fetch(img.src);
        const blob = await response.blob();
        data["imagens"].push(blob);
    }
    for (let doc of containerDocumentos.querySelectorAll("a")) {
        const response = await fetch(doc.href);
        const blob = await response.blob();
        data["documentos"].push(blob);
    }
    data["proprietario"] = {};
    data["corretor"] = {};
    data["captador"] = {};
}

async function salvar() {
    var forms = document.querySelectorAll("form");

    var data = {};

    for (let formulario of forms) {
        var formData = new FormData(formulario);
        formData.forEach(function (value, key) {
            data[key] = value;
        });
    }

    if (JSON.stringify(data).length > 0) {
        try {
            let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=cadastrar_imovel");
            await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
                .then(async (response) => {
                    if (response.erro) {
                        alert("Erro ao listar atendimentos: " + response.erro);
                        return null;
                    }
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return await response.json();
                    } else {
                        const texto = await response.text();
                        alert("Resposta inesperada do servidor");
                        console.error("Resposta não é JSON:", texto);
                        return null;
                    }
                })
                .then(async (data) => {
                    if (data.status == "erro") {
                        alert("Erro ao cadastrar imóvel: " + data.mensagem);
                        return;
                    }
                    else if (data.mensagem) {
                        alert("Imóvel cadastrado com sucesso: " + data.mensagem);
                        forms.forEach(form => form.reset());
                    }

                })
                .catch(error => {
                    alert("Erro ao cadastrar imóvel:", error);
                });

        } catch (error) {
            console.error("Erro ao enviar dados do imóvel:", error);
        }

    } else {
        alert("Nenhum dado para enviar!");
    }

    // console.log("Dados do imóvel a serem enviados:", data);
}

async function excluir() {
    if (imovelId) {
        try {
            let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=apagar_imovel");
            const response = await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ ref: imovelId })
            })
                .then(async (response) => {
                    if (response.erro) {
                        alert("Erro ao listar atendimentos: " + response.erro);
                        return null;
                    }
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return await response.json();
                    } else {
                        const texto = await response.text();
                        alert("Resposta inesperada do servidor");
                        console.error("Resposta não é JSON:", texto);
                        return null;
                    }
                })
                .then(async (data) => {
                    if (data.status == "erro") {
                        alert("Erro ao excluir imóvel: " + data.mensagem);
                    } else {
                        console.log("Imóvel excluído com sucesso:", data);
                        window.location.href = "estoque.html";
                    }
                })
                .catch(error => {
                    console.error("Erro ao excluir imóvel:", error);
                });
        } catch (error) {
            console.error("Erro ao enviar dados para exclusão do imóvel:", error);
        }
    }
    else {
        // alert("Nenhum imóvel selecionado para exclusão!");
        window.location.href = "estoque.html";
    }


}

var tabDisplays = {};

function hideAllTabContents() {
    var tabcontent = document.getElementsByClassName("tabcontent");
    for (var i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
}

function clearActiveTabLinks() {
    var tablinks = document.getElementsByClassName("tablinks");
    for (var i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
}

function findTabButtonByTarget(tabId) {
    var selector = ".tablinks[onclick*=\"'" + tabId + "'\"], .tablinks[onclick*=\"\\\"" + tabId + "\\\"\"]";
    return document.querySelector(selector);
}

function activateTab(tabId, tabButton) {
    var tabPanel = document.getElementById(tabId);
    if (!tabPanel) {
        return;
    }

    hideAllTabContents();
    clearActiveTabLinks();

    tabPanel.style.display = tabDisplays[tabId] || "block";

    if (tabButton) {
        tabButton.classList.add("active");
    } else {
        findTabButtonByTarget(tabId)?.classList.add("active");
    }

    sessionStorage.setItem("activeTab", tabId);
}

function openTab(evento, tabId) {
    activateTab(tabId, evento?.currentTarget || evento?.target || null);
}

async function abrirCadastro(imovelId) {
    imovel = await getDadosImovel(imovelId);
    if (imovel) {
        document.getElementById("ta-ref").value = imovel.id || "";
        document.getElementById("select-status").value = imovel.status || "Selecionar";
        document.getElementById("select-situacao").value = imovel.situacao || "Selecionar";
        document.getElementById("select-estado").value = imovel.estado || "Selecionar";
        document.getElementById("select-ocupacao").value = imovel.ocupacao || "Selecionar";
        document.getElementById("ta-nome-condominio").value = imovel.condominio?.nome || "";
        document.getElementById("ta-rua").value = imovel.endereco?.rua || "";
        document.getElementById("ta-bairro").value = imovel.endereco?.bairro || "";
        document.getElementById("ta-cidade").value = imovel.endereco?.cidade || "";
        document.getElementById("ta-estado").value = imovel.endereco?.uf || "";
        console.log(imovel.categoria);
        document.getElementById("select-categoria").value = imovel.categoria || "Selecionar";
        document.getElementById("ta-titulo-anuncio").value = imovel.anuncio?.titulo || "";
        document.getElementById("ta-descricao-anuncio").value = imovel.anuncio?.descricao || "";
        document.getElementById("ta-numero").value = imovel.endereco?.numero || "";
        document.getElementById("ta-complemento").value = imovel.endereco?.complemento || "";
        document.getElementById("ta-bloco").value = imovel.bloco || "";
        document.getElementById("ta-andar").value = imovel.andar || "";
        document.getElementById("ta-salas").value = imovel.quantidade_salas || "";
        document.getElementById("ta-banheiros").value = imovel.quantidade_banheiros || "";
        document.getElementById("ta-vagas").value = imovel.quantidade_vagas || "";
        document.getElementById("ta-varandas").value = imovel.quantidade_varandas || "";
        document.getElementById("ta-quartos").value = imovel.quantidade_quartos || "";
        document.getElementById("ta-area-total").value = imovel.area_total || "";
        document.getElementById("ta-area-privativa").value = imovel.area_privativa || "";
        document.getElementById("ta-venda").value = imovel.valor_venda || "";
        document.getElementById("ta-aluguel").value = imovel.valor_aluguel || "";
        document.getElementById("ta-condominio").value = imovel.valor_condominio || "";
        document.getElementById("ta-iptu").value = imovel.valor_iptu || "";
        document.getElementById("ta-ano-construcao").value = imovel.ano_construcao || "";
        document.getElementById("ta-cep").value = imovel.endereco?.cep;
    } else {
        alert("Imóvel não encontrado!");
        window.location.href = "estoque.html";
    }
}

function adicionarAnexo(event) {
    var input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*,application/pdf";
    input.multiple = true;
    input.onchange = function () {
        var files = input.files;
        var container = event.target.closest(".container");
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var fileURL = URL.createObjectURL(file);
            var fileElement;
            if (file.type.startsWith("image/")) {
                fileElement = document.createElement("img");
                fileElement.src = fileURL;
            } else if (file.type === "application/pdf") {
                fileElement = document.createElement("a");
                fileElement.href = fileURL;
                fileElement.textContent = file.name;
                fileElement.target = "_blank";
            }
            if (fileElement) {
                container.appendChild(fileElement);
            }
        }
    }
    input.click();
}

let imovelId = null;

window.addEventListener("DOMContentLoaded", async function () {
    var tabcontent = document.getElementsByClassName("tabcontent");

    for (var i = 0; i < tabcontent.length; i++) {
        var panel = tabcontent[i];
        var inlineDisplay = panel.style.display;
        if (inlineDisplay && inlineDisplay !== "none") {
            tabDisplays[panel.id] = inlineDisplay;
        } else {
            var computedDisplay = window.getComputedStyle(panel).display;
            tabDisplays[panel.id] = computedDisplay !== "none" ? computedDisplay : "block";
        }
    }

    var savedTabId = sessionStorage.getItem("activeTab");
    var defaultTabId = tabcontent.length > 0 ? tabcontent[0].id : null;
    var initialTabId = savedTabId && document.getElementById(savedTabId) ? savedTabId : defaultTabId;

    if (initialTabId) {
        activateTab(initialTabId, null);
    }

    imovelId = this.sessionStorage.getItem("imovel_id_estoque") || null;
    if (imovelId) {
        sessionStorage.removeItem("imovel_id_estoque");
        await abrirCadastro(imovelId);
    }

    Inputmask("99999-999").mask("#ta-cep");

    Inputmask('currency', {
        prefix: 'R$ ',
        groupSeparator: '.',
        radixPoint: ',',
        digits: 2,
        autoGroup: true,
        allowMinus: false,
        placeholder: '0'
    }).mask('#ta-aluguel, #ta-venda, #ta-condominio, #ta-iptu');

    Inputmask({
        alias: 'decimal',
        rightAlign: false,
        radixPoint: ',',
        groupSeparator: '.',
        autoGroup: true,
        suffix: ' m²',
        digits: 2,
        allowMinus: false,
        placeholder: '0'
    }).mask('#ta-area-privativa, #ta-area-total');
});


