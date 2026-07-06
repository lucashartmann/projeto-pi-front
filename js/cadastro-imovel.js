let imovel = null;


async function preencherEndereco(event) {
    const cep = event.target.value.replace(/\D/g, "");

    if (cep.length !== 8) {
        return;
    }

    try {
        const inputRua = document.getElementById("ta-rua");
        const inputBairro = document.getElementById("ta-bairro");
        const inputCidade = document.getElementById("ta-cidade");
        const inputEstado = document.getElementById("ta-estado");

        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);

        if (!response.ok) {
            console.log("CEP inválido ou não encontrado");
            return;
        }

        const data = await response.json();

        if (data.erro) {
            alert("CEP não encontrado!");
            return;
        }

        inputRua.value = data.logradouro || "";
        inputBairro.value = data.bairro || "";
        inputCidade.value = data.localidade || "";
        inputEstado.value = data.uf || "";
    } catch (error) {
        console.error("Erro ao buscar endereço:", error);
    }
}


async function listarPessoas(tipo) {
    if (tipo !== "proprietario") {
        try {
            let caminho = getCaminhoRelativo("/php/api/proprietarios.php?acao=listar");
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

            return null;

        } catch (error) {
            console.error("Falha ao conectar com o backend:", erro);
            return null;
        }
    } else {
        try {
            let caminho = getCaminhoRelativo("/php/api/usuarios.php?acao=listar");
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

        } catch (error) {
            console.error("Falha ao conectar com o backend:", erro);
            return null;
        }
    }
}

let lista = null;

async function editarPessoa(tipo) {
    if (tipo !== "proprietario" && tipo !== "corretor" && tipo !== "captador") {
        alert("Tipo de pessoa inválido!");
        return;
    }

    if (!lista) {
        console.log("Carregando lista de pessoas...");
        lista = await listarPessoas(tipo);
    }

    if (!lista || lista.length === 0 || !lista.dados || lista.dados.length === 0) {
        alert("Nenhuma pessoa encontrada para editar!");
        return;
    }


    const container = document.createElement("div");
    const click = function (event) {
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
        }
        document.removeEventListener("click", click);
        console.log("É para o evento ser removido")
    };
    if (!document.eventListenerList || !document.eventListenerList.some(listener => listener.type === "click" && listener.listener === click)) {
        setTimeout(() => {
            document.addEventListener("click", click, { once: true });
        }, 0);
    }
    const pesquisarInput = document.createElement("input");
    pesquisarInput.type = "text";
    pesquisarInput.placeholder = "Pesquisar...";
    pesquisarInput.oninput = function () {
        const query = pesquisarInput.value.toLowerCase();
        const resultados = container.querySelectorAll("div");
        resultados.forEach(resultado => {
            const nome = resultado.querySelector("label")?.textContent.toLowerCase();
            resultado.style.display = nome.includes(query) ? "flex" : "none";
            const creciLabel = resultado.querySelector("label:nth-child(2)");
            if (creciLabel) {
                const creci = creciLabel.textContent.toLowerCase();
                resultado.style.display = (nome.includes(query) || creci.includes(query)) ? "flex" : "none";
            }
            const emailLabel = resultado.querySelector("label:nth-child(3)");
            if (emailLabel) {
                const email = emailLabel.textContent.toLowerCase();
                resultado.style.display = (nome.includes(query) || email.includes(query)) ? "flex" : "none";
            }
            const telefoneLabel = resultado.querySelector("label:nth-child(4)");
            if (telefoneLabel) {
                const telefone = telefoneLabel.textContent.toLowerCase();
                resultado.style.display = (nome.includes(query) || email.includes(query) || telefone.includes(query)) ? "flex" : "none";
            }
        });
    };

    idsExistentes = [];

    switch (tipo) {
        case "proprietario":
            idsExistentes = document.getElementById("container-proprietario").querySelectorAll(".resultado-pessoa .id-pessoa");
            break;
        case "corretor":
            idsExistentes = document.getElementById("container-corretor").querySelectorAll(".resultado-pessoa .id-pessoa");
            break;
        case "captador":
            idsExistentes = document.getElementById("container-captador").querySelectorAll(".resultado-pessoa .id-pessoa");
            break;
    }

    container.appendChild(pesquisarInput);
    for (let pessoa of lista.dados) {
        let div_resultado = document.createElement("div");
        div_resultado.classList.add("resultado-pessoa");
        let checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.value = (pessoa.id in idsExistentes) ? true : false;
        checkbox.name = "pessoa-selecionada";

        div_left = document.createElement("div");
        div_left.classList.add("div-left");
        div_left.appendChild(checkbox);

        div_resultado.appendChild(div_left);
        let nome = document.createElement("label");
        nome.textContent = pessoa.nome;
        nome.classList.add("nome-pessoa");

        div_right = document.createElement("div");
        div_right.classList.add("div-right");
        div_right.appendChild(nome);

        let inputHidden = document.createElement("input");
        inputHidden.type = "hidden";
        inputHidden.value = pessoa.id;
        inputHidden.classList.add("id-pessoa");
        div_right.appendChild(inputHidden);

        if (pessoa?.creci) {
            let creci = document.createElement("label");
            creci.textContent = "CRECI: " + pessoa.creci;
            creci.classList.add("creci-pessoa");
            div_right.appendChild(creci);
        }
        let email = document.createElement("label");
        email.textContent = "Email: " + pessoa.email;
        div_right.appendChild(email);
        let telefone = document.createElement("label");
        telefone.textContent = "Telefone: " + pessoa.telefone;
        div_right.appendChild(telefone);
        div_resultado.appendChild(div_right);
        container.appendChild(div_resultado);
    }

    container.classList.add('form-container');
    document.body.appendChild(container);

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

async function getOutrosDados(formData) {
    const containerImagens = document.getElementById("container-imagens");
    const containerDocumentos = document.getElementById("container-anexos");
    const containerProprietario = document.getElementById("container-proprietario");
    const containerCorretor = document.getElementById("container-corretor");
    const containerCaptador = document.getElementById("container-captador");

    const divImagens = containerImagens.querySelectorAll("div.imagem-anuncio");

    const imagens = Array.from(divImagens).map(div => {
        const bg = div.style.backgroundImage;
        const match = bg.match(/url\((['"]?)(.*?)\1\)/);
        return match ? match[2] : null;
    }).filter(url => url !== null);

    formData.append("imagens", []);
    formData.append("documentos", []);
    console.log("Imagens a serem enviadas:", imagens);

    if (imagens.length > 0) {
        for (let img of imagens) {
            if (!imovel || !imovel.anuncio || !imovel.anuncio.imagens || !imovel.anuncio.imagens.includes(img)) {
                try {
                    const response = await fetch(img);
                    if (!response.ok) throw new Error("Falha ao buscar o blob");
                    const blob = await response.blob();
                    const extensao = blob.type.split("/")[1] || "webp";
                    formData.append("imagens[]", blob, `imagem.${extensao}`);
                } catch (error) {
                    console.error("Erro ao processar imagem:", img, error);
                }
            }
        }
    }

    if (containerDocumentos && containerDocumentos.querySelectorAll("a").length > 0) {
        for (let doc of containerDocumentos.querySelectorAll("a")) {
            try {
                const response = await fetch(doc.href);
                if (!response.ok) throw new Error("Falha ao buscar o documento");
                const blob = await response.blob();
                const nomeArquivo = doc.textContent.trim().split(" ").join("_");
                formData.append("documentos[]", blob, `${nomeArquivo}`);
            } catch (error) {
                console.error("Erro ao processar documento:", doc.href, error);
            }
        }
    }

    if (containerProprietario && containerProprietario.querySelectorAll(".resultado-pessoa").length > 0) {
        const idProprietario = containerProprietario.querySelector(".resultado-pessoa .id-pessoa")?.value;
        if (idProprietario) {
            formData.append("proprietarios[]", idProprietario);
        }
    } else {
        formData.append("proprietarios", []);
    }

    if (containerCorretor && containerCorretor.querySelectorAll(".resultado-pessoa").length > 0) {
        const idCorretor = containerCorretor.querySelector(".resultado-pessoa .id-pessoa")?.value;
        if (idCorretor) {
            formData.append("corretor", idCorretor);
        }
    } else {
        formData.append("captador", null);
    }

    if (containerCaptador && containerCaptador.querySelectorAll(".resultado-pessoa").length > 0) {
        const idCaptador = containerCaptador.querySelector(".resultado-pessoa .id-pessoa")?.value;
        if (idCaptador) {
            formData.append("captador", idCaptador);
        }
    } else {
        formData.append("corretor", null);
    }

    return formData;
}

async function salvar() {
    var forms = document.querySelectorAll("form");

    let formData = new FormData();

    for (let formulario of forms) {
        let dados = new FormData(formulario);

        dados.forEach((value, key) => {
            formData.append(key, value);
        });
    }

    formData = await getOutrosDados(formData);
    const data = {};

    formData.forEach((value, key) => {
        data[key] = value;
    });


    if (JSON.stringify(formData).length > 0) {
        try {
            let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=cadastrar_imovel");
            await fetch(caminho, {
                method: "POST",
                body: formData
            })
                .then(async (response) => {
                    if (response.erro) {
                        alert("Erro ao cadastrar imóvel: " + response.erro);
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
                        if (!imovel) {
                            forms.forEach(form => form.reset());
                        }
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
    confirmar = confirm("Tem certeza que deseja excluir este imóvel?");
    if (imovelId && confirmar) {
        try {
            let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=apagar_imovel&id=" + imovelId);
            const response = await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },

            })
                .then(async (response) => {
                    if (response.erro) {
                        alert("Erro ao remover imóvel: " + response.erro);
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



async function abrirCadastro(imovel) {
    imovel = JSON.parse(imovel);
    console.log("Abrindo cadastro do imóvel:", imovel);
    if (imovel) {
        let containerImagens = document.getElementById("container-imagens");
        prepararContainerArrastavel(containerImagens);

        document.getElementById("ta-ref").value = imovel.id || "";
        document.getElementById("select-status").value = imovel.status || "";
        document.getElementById("select-situacao").value = imovel.situacao || "";
        document.getElementById("select-estado").value = imovel.estado || "";
        document.getElementById("select-ocupacao").value = imovel.ocupacao || "";
        document.getElementById("ta-nome-condominio").value = imovel.condominio?.nome || "";
        document.getElementById("ta-rua").value = imovel.endereco?.rua || "";
        document.getElementById("ta-bairro").value = imovel.endereco?.bairro || "";
        document.getElementById("ta-cidade").value = imovel.endereco?.cidade || "";
        document.getElementById("ta-estado").value = imovel.endereco?.uf || "";
        // console.log(imovel.categoria);
        document.getElementById("select-categoria").value = imovel.categoria || "";
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
        document.getElementById("ta-area-total").value = imovel.area_total ? imovel.area_total.toLocaleString('pt-BR', {
            style: 'unit',
            unit: 'meter',
            unitDisplay: 'long'
        }) : "";
        document.getElementById("ta-area-privativa").value = imovel.area_privativa ? imovel.area_privativa.toLocaleString('pt-BR', {
            style: 'unit',
            unit: 'meter',
            unitDisplay: 'long'
        }) : "";
        document.getElementById("ta-venda").value = imovel.valor_venda.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) || "";
        document.getElementById("ta-aluguel").value = imovel.valor_aluguel.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) || "";
        document.getElementById("ta-condominio").value = imovel.valor_condominio.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) || "";
        document.getElementById("ta-iptu").value = imovel.valor_iptu.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) || "";
        document.getElementById("ta-ano-construcao").value = imovel.ano_construcao || "";
        document.getElementById("ta-cep").value = imovel.endereco?.cep;
        if (imovel.anuncio?.imagens && imovel.anuncio.imagens.length > 0) {
            let contadorImgens = 0;
            for (let imagem of imovel.anuncio.imagens) {
                // console.log(imagem);
                let divImagem = document.createElement("div");
                divImagem.classList.add("imagem-anuncio");
                divImagem.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${imagem})`
                // console.log(imagem);
                divImagem.setAttribute("onclick", `abrirImagem('${imagem}')`);
                let checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.classList.add("checkbox-imagem");
                checkbox.value = imagem;
                checkbox.name = "imagens-selecionadas";
                divImagem.appendChild(checkbox);
                // divImagem.appendChild()
                prepararImagemArrastavel(divImagem);
                containerImagens.appendChild(divImagem);
                // console.log(divImagem.style.backgroundImage.split("url(")[1].slice(0, -1))
                contadorImgens++;
            }
            document.getElementById("contador-imagens").textContent = contadorImgens + " imagem(s)";
            containerImagens.querySelector(".abrir-multiplos").style.display = "inline-block";
            containerImagens.querySelector(".apagar-multiplos").style.display = "inline-block";
        }
        // console.log(imovel.anuncio);
        if (imovel.anuncio?.documentos && imovel.anuncio.documentos.length > 0) {
            let contadorDocumentos = 0;
            let containerDocumentos = document.getElementById("container-anexos");
            for (let documento of imovel.anuncio.documentos) {
                let fileElement = document.createElement("div");
                fileElement.classList.add("anexo-documento");
                let docElement = document.createElement("a");
                console.log(documento);
                docElement.href = documento;
                docElement.textContent = documento.split("_").slice(1);
                docElement.target = "_blank";
                let checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.classList.add("checkbox-documento");
                checkbox.value = false;
                checkbox.name = "documentos-selecionados";
                fileElement.appendChild(checkbox);
                fileElement.appendChild(docElement);
                containerDocumentos.appendChild(fileElement);
                contadorDocumentos++;
            }
            containerDocumentos.querySelector(".abrir-multiplos").style.display = "inline-block";
            containerDocumentos.querySelector(".apagar-multiplos").style.display = "inline-block";
            document.getElementById("contador-documentos").textContent = contadorDocumentos + " documento(s)";
        }
        pessoas = {};
        if (imovel.proprietarios) {
            pessoas["proprietarios"] = imovel.proprietarios;
        }
        if (imovel.corretor) {
            pessoas["corretor"] = imovel.corretor;
        }
        if (imovel.captador) {
            pessoas["captador"] = imovel.captador;
        }

        if (Object.keys(pessoas).length > 0) {
            for (let chave in pessoas) {
                let container = null;
                switch (chave) {
                    case "proprietarios":
                        container = document.getElementById("container-proprietario");
                        break;
                    case "corretor":
                        container = document.getElementById("container-corretor");
                        break;
                    case "captador":
                        container = document.getElementById("container-captador");
                        break;
                }
                for (let pessoa of pessoas[chave]) {
                    let div_resultado = document.createElement("div");
                    div_resultado.classList.add("resultado-pessoa");
                    let checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.value = pessoa.id;
                    checkbox.name = "pessoa-selecionada";

                    div_left = document.createElement("div");
                    div_left.classList.add("div-left");
                    div_left.appendChild(checkbox);

                    div_resultado.appendChild(div_left);
                    let nome = document.createElement("label");
                    nome.textContent = pessoa.nome;
                    nome.classList.add("nome-pessoa");

                    div_right = document.createElement("div");
                    div_right.classList.add("div-right");
                    div_right.appendChild(nome);

                    let inputHidden = document.createElement("input");
                    inputHidden.type = "hidden";
                    inputHidden.value = pessoa.id;
                    inputHidden.classList.add("id-pessoa");
                    div_right.appendChild(inputHidden);

                    if (pessoa?.creci) {
                        let creci = document.createElement("label");
                        creci.textContent = "CRECI: " + pessoa.creci;
                        creci.classList.add("creci-pessoa");
                        div_right.appendChild(creci);
                    }
                    let email = document.createElement("label");
                    email.textContent = "Email: " + pessoa.email;
                    div_right.appendChild(email);
                    let telefone = document.createElement("label");
                    telefone.textContent = "Telefone: " + pessoa.telefone;
                    div_right.appendChild(telefone);
                    div_resultado.appendChild(div_right);
                    container.appendChild(div_resultado);
                }
            }
        }



    } else {
        alert("Imóvel não encontrado!");
        window.location.href = "estoque.html";
    }
}

function abrirImagem(src) {
    var modal = document.createElement("div");
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100%";
    modal.style.height = "100%";
    modal.style.backgroundColor = "rgba(0, 0, 0, 0.8)";
    modal.style.display = "flex";
    modal.style.justifyContent = "center";
    modal.style.alignItems = "center";
    modal.style.zIndex = "1000";
    var img = document.createElement("img");
    img.src = src;
    img.style.maxWidth = "90%";
    img.style.maxHeight = "90%";
    modal.appendChild(img);
    document.body.appendChild(modal);
    modal.addEventListener("click", function () {
        document.body.removeChild(modal);
    });
    img.addEventListener("click", function (event) {
        event.stopPropagation();
        document.body.removeChild(modal);
    });
}

let imagemArrastadaAtual = null;
let placeholderArrasteAtual = null;

function obterPlaceholderArraste() {
    if (!placeholderArrasteAtual) {
        placeholderArrasteAtual = document.createElement("div");
        placeholderArrasteAtual.classList.add("drag-placeholder");
    }

    return placeholderArrasteAtual;
}

function limparPlaceholderArraste() {
    if (placeholderArrasteAtual && placeholderArrasteAtual.parentNode) {
        placeholderArrasteAtual.parentNode.removeChild(placeholderArrasteAtual);
    }
}

function atualizarIndicadorPosicaoArraste(event) {
    const draggedId = event.dataTransfer.getData("text/plain");
    const draggedImg = document.querySelector(`[data-drag-id="${draggedId}"]`);
    if (!draggedImg) {
        return;
    }

    imagemArrastadaAtual = draggedImg;

    const target = event.target;
    const container = target.closest("#container-imagens");
    if (!container) {
        return;
    }

    const placeholder = obterPlaceholderArraste();

    if (target.tagName === "DIV" && target !== draggedImg) {
        if (placeholder.parentNode !== container || placeholder.nextSibling !== target) {
            container.insertBefore(placeholder, target);
        }
    } else {
        const botaoAdicionar = container.querySelector("button");
        if (botaoAdicionar) {
            if (placeholder.parentNode !== container || placeholder.nextSibling !== botaoAdicionar) {
                container.insertBefore(placeholder, botaoAdicionar);
            }
        } else {
            container.appendChild(placeholder);
        }
    }
}

function prepararContainerArrastavel(container) {
    if (!container || container.dataset.dropReady === "true") {
        return;
    }

    container.dataset.dropReady = "true";
    container.addEventListener("dragover", function (event) {
        event.preventDefault();
        atualizarIndicadorPosicaoArraste(event);
    });
    container.addEventListener("dragenter", function (event) {
        event.preventDefault();
        atualizarIndicadorPosicaoArraste(event);
    });
    container.addEventListener("drop", mudarPosicaoNoContainer);
}

function prepararImagemArrastavel(imgElement) {
    const dragId = `drag-${Date.now()}-${Math.random().toString(16).slice(2)}`;

    imgElement.dataset.dragId = dragId;
    imgElement.setAttribute("draggable", "true");
    imgElement.addEventListener("dragstart", function (event) {
        imagemArrastadaAtual = imgElement;
        imgElement.classList.add("imagem-arrastando");
        event.dataTransfer.setData("text/plain", dragId);
        event.dataTransfer.effectAllowed = "move";
        if (event.dataTransfer.setDragImage) {
            event.dataTransfer.setDragImage(imgElement, imgElement.width / 2, imgElement.height / 2);
        }
    });
    imgElement.addEventListener("dragend", function () {
        imgElement.classList.remove("imagem-arrastando");
        imagemArrastadaAtual = null;
        limparPlaceholderArraste();
    });
    imgElement.addEventListener("dragover", function (event) {
        event.preventDefault();
        atualizarIndicadorPosicaoArraste(event);
    });
    imgElement.addEventListener("dragenter", function (event) {
        event.preventDefault();
        atualizarIndicadorPosicaoArraste(event);
    });
    imgElement.addEventListener("drop", mudarPosicaoNoContainer);
}

function mudarPosicaoNoContainer(event) {
    event.preventDefault();
    const draggedId = event.dataTransfer.getData("text/plain");
    const draggedImg = document.querySelector(`[data-drag-id="${draggedId}"]`);
    const container = event.target.closest("#container-imagens");
    const placeholder = obterPlaceholderArraste();

    if (!draggedImg || !container) {
        limparPlaceholderArraste();
        return;
    }

    if (placeholder.parentNode === container) {
        container.insertBefore(draggedImg, placeholder);
    } else {

        container.appendChild(draggedImg);

    }

    limparPlaceholderArraste();
}

function abrirMultiplos(event) {

}

function apagarMultiplos(event) {
    const container = event.target.closest(".container");
    const checkboxes = container.querySelectorAll("input[type='checkbox']:checked");
    if (checkboxes.length === 0) {
        alert("Nenhum item selecionado para exclusão!");
        return;
    }
    // if (confirm(`Tem certeza que deseja excluir os ${checkboxes.length} itens selecionados?`)) {}
    checkboxes.forEach(checkbox => {
        const item = checkbox.closest(".imagem-anuncio, a");
        if (item) {
            item.parentNode.removeChild(item);
        }
    });

    document.getElementById("contador-imagens").textContent = container.querySelectorAll(".imagem-anuncio").length + " imagem(s)";
    document.getElementById("contador-documentos").textContent = container.querySelectorAll("a").length + " documento(s)";

}

function selecionarTodos(event) {
    const container = event.target.closest(".container");
    const checkboxes = container.querySelectorAll("input[type='checkbox']");
    const todosSelecionados = Array.from(checkboxes).every(checkbox => checkbox.checked);
    checkboxes.forEach(checkbox => checkbox.checked = !todosSelecionados);
}

function adicionarAnexo(event) {
    var input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*,application/pdf";
    input.multiple = true;
    let contadorImagens = 0;
    let contadorDocumentos = 0;
    input.onchange = function () {
        var files = input.files;
        var container = event.target.closest(".container");
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var fileURL = URL.createObjectURL(file);
            var fileElement;
            if (file.type.startsWith("image/")) {
                fileElement = document.createElement("div");
                fileElement.classList.add("imagem-anuncio");
                fileElement.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url("${fileURL}")`;
                fileElement.onclick = (function (url) {
                    return function () {
                        abrirImagem(url);
                    };
                })(fileURL);
                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.classList.add("checkbox-imagem");
                checkbox.value = fileURL;
                checkbox.name = "imagens-selecionadas";
                fileElement.appendChild(checkbox);
                prepararImagemArrastavel(fileElement);
                contadorImagens++;
                // console.log(fileURL);
            } else if (file.type === "application/pdf") {
                fileElement = document.createElement("div");
                fileElement.classList.add("anexo-documento");
                let a = document.createElement("a");
                a.href = fileURL;
                a.textContent = file.name;
                a.target = "_blank";
                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.classList.add("checkbox-documento");
                checkbox.value = fileURL;
                checkbox.name = "documentos-selecionados";
                fileElement.appendChild(checkbox);
                fileElement.appendChild(a);
                contadorDocumentos++;
            }
            if (fileElement) {
                container.appendChild(fileElement);
                container.querySelector(".abrir-multiplos").style.display = "inline-block";
                container.querySelector(".apagar-multiplos").style.display = "inline-block";
            }
        }
    }
    document.getElementById("contador-imagens").textContent = contadorImagens + " imagem(s)";
    document.getElementById("contador-documentos").textContent = contadorDocumentos + " documento(s)";
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

    imovel = this.sessionStorage.getItem("imovel") || null;
    if (imovel) {
        sessionStorage.removeItem("imovel");
        await abrirCadastro(imovel);
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


