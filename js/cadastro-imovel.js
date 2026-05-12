Inputmask("99999-999").mask("#ta-cep");

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

function salvar() {
    var form = document.getElementById("container-cadastro");
    var formAnuncio = document.getElementById("container-anuncio");
    var data = {};

    for (formulario of form) {
        var formData = new FormData(form);
        formData.forEach(function (value, key) {
            data[key] = value;
        });
    };

    for (formulario of formAnuncio) {
        var formData = new FormData(formulario);
        formData.forEach(function (value, key) {
            data[key] = value;
        });
    };

    if (JSON.stringify(data).length > 0) {
        try {
            let caminho = window.location.pathname;
            if (caminho.includes("/html/")) {
                caminho = caminho.replace("/html/", "/");
            }
            caminho = caminho.replace(
                caminho.substring(caminho.lastIndexOf("/")),
                "/php/api/imoveis.php?acao=cadastrar_imovel"
            );
            fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
                .then(response => {
                    if (response.erro) {
                        alert("Erro ao listar atendimentos: " + response.erro);
                        return null;
                    }
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return response.json();
                    } else {
                        const texto = response.text();
                        alert("Resposta inesperada do servidor");
                        console.error("Resposta não é JSON:", texto);
                        return null;
                    }
                })
                .then(data => {
                    if (data.status == "erro") {
                        alert("Erro ao cadastrar imóvel: " + data.mensagem);
                        return;
                    }
                    else if (data.mensagem) {
                        alert("Imóvel cadastrado com sucesso: " + data.mensagem);
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
            let caminho = window.location.pathname;
            if (caminho.includes("/html/")) {
                caminho = caminho.replace("/html/", "/");
            }
            caminho = caminho.replace(
                caminho.substring(caminho.lastIndexOf("/")),
                "/php/api/imoveis.php?acao=apagar_imovel"
            );
            const response = await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ ref: imovelId })
            })
                .then(response => {
                    if (response.erro) {
                        alert("Erro ao listar atendimentos: " + response.erro);
                        return null;
                    }
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return response.json();
                    } else {
                        const texto = response.text();
                        alert("Resposta inesperada do servidor");
                        console.error("Resposta não é JSON:", texto);
                        return null;
                    }
                })
                .then(data => {
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
        alert("Nenhum imóvel selecionado para exclusão!");
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
    console.log("Dados do imóvel para cadastro:", imovel);
    if (imovel) {
        document.getElementById("select-status").value = imovel.status?.toLowerCase()?.trim()?.replace(/\s+/g, "_") || "Selecionar";
        document.getElementById("select-situacao").value = imovel.situacao?.toLowerCase()?.trim()?.replace(/\s+/g, "_") || "Selecionar";
        document.getElementById("select-estado").value = imovel.estado?.toLowerCase()?.trim()?.replace(/\s+/g, "_") || "Selecionar";
        document.getElementById("select-ocupacao").value = imovel.ocupacao?.toLowerCase()?.trim()?.replace(/\s+/g, "_") || "Selecionar";
        document.getElementById("ta-nome-condominio").value = imovel.condominio?.nome || "";
        document.getElementById("ta-rua").value = imovel.endereco?.rua || "";
        document.getElementById("ta-bairro").value = imovel.endereco?.bairro || "";
        document.getElementById("ta-cidade").value = imovel.endereco?.cidade || "";
        document.getElementById("ta-estado").value = imovel.endereco?.uf || "";
        document.getElementById("select-categoria").value = imovel.categoria?.toLowerCase()?.trim()?.replace(/\s+/g, "_") || "Selecionar";
        // document.getElementById("ta-titulo").value = imovel.anuncio?.titulo || "";
        // document.getElementById("ta-descricao").value = imovel.anuncio?.descricao || "";
        document.getElementById("ta-numero").value = imovel.endereco.numero || "";
        document.getElementById("ta-complemento").value = imovel.complemento || "";
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

window.addEventListener("DOMContentLoaded", function () {
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
        abrirCadastro(imovelId);
    }
});


function preencherEndereco(event) {
    let cep = event.target.value;
    if (cep.length > 7) {
        let inputRua = document.getElementById("ta-rua");
        let inputBairro = document.getElementById("ta-bairro");
        let inputCidade = document.getElementById("ta-cidade");
        let inputEstado = document.getElementById("ta-estado");
        fetch(`https://viacep.com.br/ws/${cep}/json`)
            .then(response => response.json())
            .then(dados => {
                if (dados.erro) {
                    alert("CEP não encontrado!");
                    return;
                }
                inputRua.value = dados.logradouro || "";
                inputBairro.value = dados.bairro || "";
                inputCidade.value = dados.localidade || "";
                inputEstado.value = dados.uf || "";
            });
    }
}