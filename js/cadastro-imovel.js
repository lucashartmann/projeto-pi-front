function salvar() {
    var form = document.getElementsByName("form");
    var data = {};

    for (formulario of form) {
        var formData = new FormData(form);
        formData.forEach(function (value, key) {
            data[key] = value;
        });
    };

    if (data) {
        try {
            fetch("http://127.0.0.1:8000/estoque/cadastrar/", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Imóvel cadastrado com sucesso:", data);
                })
                .catch(error => {
                    console.error("Erro ao cadastrar imóvel:", error);
                });

        } catch (error) {
            console.error("Erro ao enviar dados do imóvel:", error);
        }

    }

    console.log("Dados do imóvel a serem enviados:", data);
}

async function excluir() {
    if (imovel_id) {
        try {
            const response = await fetch("http://127.0.0.1:8000/estoque/apagar/" + imovel_id + "/", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ ref: imovel_id })
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Imóvel excluído com sucesso:", data);
                    window.location.href = "estoque.html";
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

async function abrirCadastro(imovel_id) {
    imovel = await getDadosImovel(imovel_id);
    console.log("Dados do imóvel para cadastro:", imovel);
    if (imovel) {
        document.getElementById("select_status").value = imovel.status?.toLowerCase()?.trim()?.replace(/\s+/g, "_") || "Selecionar";
        document.getElementById("select_situacao").value = imovel.situacao?.toLowerCase()?.trim()?.replace(/\s+/g, "_") || "Selecionar";
        document.getElementById("select_estado").value = imovel.estado?.toLowerCase()?.trim()?.replace(/\s+/g, "_") || "Selecionar";
        document.getElementById("select_ocupacao").value = imovel.ocupacao?.toLowerCase()?.trim()?.replace(/\s+/g, "_") || "Selecionar";
        document.getElementById("ta_nome_condominio").value = imovel.condominio?.nome || "";
        document.getElementById("ta_rua").value = imovel.endereco?.rua || "";
        document.getElementById("ta_bairro").value = imovel.endereco?.bairro || "";
        document.getElementById("ta_cidade").value = imovel.endereco?.cidade || "";
        document.getElementById("ta_estado").value = imovel.endereco?.uf || "";
        document.getElementById("select_categoria").value = imovel.categoria?.toLowerCase()?.trim()?.replace(/\s+/g, "_") || "Selecionar";
        // document.getElementById("ta_titulo").value = imovel.anuncio?.titulo || "";
        // document.getElementById("ta_descricao").value = imovel.anuncio?.descricao || "";
        document.getElementById("ta_numero").value = imovel.endereco.numero || "";
        document.getElementById("ta_complemento").value = imovel.complemento || "";
        document.getElementById("ta_bloco").value = imovel.bloco || "";
        document.getElementById("ta_andar").value = imovel.andar || "";
        document.getElementById("ta_salas").value = imovel.quantidade_salas || "";
        document.getElementById("ta_banheiros").value = imovel.quantidade_banheiros || "";
        document.getElementById("ta_vagas").value = imovel.quantidade_vagas || "";
        document.getElementById("ta_varandas").value = imovel.quantidade_varandas || "";
        document.getElementById("ta_quartos").value = imovel.quantidade_quartos || "";
        document.getElementById("ta_area_total").value = imovel.area_total || "";
        document.getElementById("ta_area_privativa").value = imovel.area_privativa || "";
        document.getElementById("ta_venda").value = imovel.valor_venda || "";
        document.getElementById("ta_aluguel").value = imovel.valor_aluguel || "";
        document.getElementById("ta_condominio").value = imovel.valor_condominio || "";
        document.getElementById("ta_iptu").value = imovel.valor_iptu || "";
        document.getElementById("ta_ano_construcao").value = imovel.ano_construcao || "";
    } else {
        alert("Imóvel não encontrado!");
        window.location.href = "estoque.html";
    }
}

function adicionarAnexo(event) {
                //abre seletor de arquivo e adiciona iamagem no pai
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

let imovel_id = null;

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

    imovel_id = this.sessionStorage.getItem("imovel_id_estoque") || null;
    if (imovel_id) {
        sessionStorage.removeItem("imovel_id_estoque");
        abrirCadastro(imovel_id);
    }
});
