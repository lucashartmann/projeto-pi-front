import { getDadosImovel, destacarImovel, excluirImovel, listarImoveis } from "./modules/imoveis.js";
import { listarHistoricoPorIdImovel } from "./modules/historico.js";
import { listarPessoas } from "./modules/usuarios.js";
import { getCaminhoRelativo } from "./modules/utils.js";
import { buscarCoordenadas, carregarMapa } from "./modules/mapa.js";
import { usuarioLogado, carregarUser } from "./modules/usuario.js";

let imovel = null;


window.abrirTab = abrirTab;
window.preencherEndereco = preencherEndereco;
window.salvarMultiplosForms = salvarMultiplosForms;
window.excluir = excluir;
window.editarPessoa = editarPessoa;
window.apagarMultiplos = apagarMultiplos;
window.adicionarAnexo = adicionarAnexo;
window.selecionarTodos = selecionarTodos;
window.compartilhar = compartilhar;
window.abrirAnuncio = abrirAnuncio;
window.abrirImagem = abrirImagem;
window.abrirMultiplos = abrirMultiplos;
window.listarHistoricoPorIdImovel = listarHistoricoPorIdImovel;

function calcularMediaAluguel(imovelAlvo) {
    const listaImoveis = listarImoveis() || [];
    if (!listaImoveis.length || !imovelAlvo?.endereco) {
        return 0;
    }

    const enderecoAlvo = imovelAlvo.endereco;

    const imoveisMesmaRegiao = listaImoveis.filter(i => {
        return i?.endereco?.cep === enderecoAlvo.cep;
    });

    if (imoveisMesmaRegiao.length === 0) {
        return 0;
    }

    const somaMetroQuadrado = imoveisMesmaRegiao.reduce((acc, i) => {
        const valor = i?.anuncio?.valor_aluguel || 0;
        const area = i?.anuncio?.areaPrivativa || 1;
        return acc + (valor / area);
    }, 0);

    const mediaMetroQuadrado = somaMetroQuadrado / imoveisMesmaRegiao.length;

    const areaAlvo = imovelAlvo?.anuncio?.areaPrivativa || 1;
    const mediaAoImovel = mediaMetroQuadrado * areaAlvo;

    return mediaAoImovel;
}

function calcularMediaVenda(imovelAlvo) {
    const listaImoveis = listarImoveis() || [];
    if (!listaImoveis.length || !imovelAlvo?.endereco) {
        return 0;
    }

    const enderecoAlvo = imovelAlvo.endereco;

    const imoveisMesmaRegiao = listaImoveis.filter(i => {
        return i?.endereco?.cep === enderecoAlvo.cep;
    });

    if (imoveisMesmaRegiao.length === 0) {
        return 0;
    }

    const somaMetroQuadrado = imoveisMesmaRegiao.reduce((acc, i) => {
        const valor = i?.anuncio?.valor_venda || 0;
        const area = i?.anuncio?.areaPrivativa || 1;
        return acc + (valor / area);
    }, 0);

    const mediaMetroQuadrado = somaMetroQuadrado / imoveisMesmaRegiao.length;

    const areaAlvo = imovelAlvo?.anuncio?.areaPrivativa || 1;
    const mediaAoImovel = mediaMetroQuadrado * areaAlvo;

    return mediaAoImovel;
}

function calcularMedia() {
    const container = document.getElementById("media-valores");
    if (!imovel || !imovel.status || !container) {
        return;
    }
    switch (imovel.status.toLowerCase()) {
        case "aluguel":
            if (calcularMediaAluguel(imovel) === 0) {
                return;
            }
            container.innerHTML = `Média de aluguel: R$ ${calcularMediaAluguel(imovel).toFixed(2)}`;
            if (calcularMediaAluguel(imovel) < imovel.valor_aluguel) {
                container.innerHTML += `<br><p style="color:red">Imóvel está acima da média</p>`;
            }
            break;
        case "venda":
            if (calcularMediaVenda(imovel) === 0) {
                return;
            }
            container.innerHTML = `Média de venda: R$ ${calcularMediaVenda(imovel).toFixed(2)}`;
            if (calcularMediaVenda(imovel) < imovel.valor_venda) {
                container.innerHTML += `<br><p style="color:red">Imóvel está acima da média</p>`;
            }
            break;
        case "venda e aluguel":
            if (calcularMediaVenda(imovel) === 0 && calcularMediaAluguel(imovel) === 0) {
                return;
            }
            container.innerHTML = `Média de aluguel: R$ ${calcularMediaAluguel(imovel).toFixed(2)}<br>Média de venda: R$ ${calcularMediaVenda(imovel).toFixed(2)}`;
            if (calcularMediaAluguel(imovel) < imovel.valor_aluguel) {
                container.innerHTML += `<br><p style="color:red">Imóvel está acima da média de aluguel</p>`;
            }
            if (calcularMediaVenda(imovel) < imovel.valor_venda) {
                container.innerHTML += `<br><p style="color:red">Imóvel está acima da média de venda</p>`;
            }
            break;
    }
}

function abrirAnuncio() {
    let urlAtual = window.location.href;
    urlAtual = urlAtual.replace("cadastro-imovel.html", "dados-imovel.html");
    window.open(urlAtual);
}

async function compartilhar(tipo) {
    if (!navigator.share) {
        alert("Compartilhamento não suportado por este navegador.");
        return;
    }
    try {
        switch (tipo) {
            case "cadastro":
                await navigator.share({
                    title: imovel.anuncio.titulo,
                    text: imovel.anuncio.descricao,
                    url: window.location.href
                });
                break;
            case "anuncio":
                let urlAtual = window.location.href;
                urlAtual = urlAtual.replace("cadastro-imovel.html", "dados-imovel.html");
                await navigator.share({
                    title: imovel.anuncio.titulo,
                    text: imovel.anuncio.descricao,
                    url: urlAtual
                });
                break;
        }
    } catch (error) {
        console.error("Erro ao compartilhar o imóvel:", error);
    }
}

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

async function editarPessoa(tipo) {

    if (tipo !== "PROPRIETARIO" && tipo !== "CORRETOR" && tipo !== "CAPTADOR") {
        alert("Tipo de pessoa inválido!");
        return;
    }

    let lista = await listarPessoas(tipo);

    if (lista == null || lista == [] || lista.length === 0) {
        alert("Nenhuma pessoa encontrada para editar!");
        return;
    }

    const container = document.createElement("div");
    container.classList = "div-dados";
    const click = function (event) {
        if (document.body.contains(container) && !container.contains(event.target)) {

            const checkboxes = container.querySelectorAll("input[type='checkbox']");
            const selecionados = Array.from(checkboxes).filter(checkbox => checkbox.checked);


            if (selecionados.length > 0) {
                for (let checkbox of selecionados) {
                    let containerPessoa = checkbox.closest(".resultado-pessoa");
                    const id = containerPessoa.querySelector(".div-right .id-pessoa").value;
                    let pessoasExistentes = null;
                    let encontrou = false;

                    switch (tipo) {
                        case "PROPRIETARIO":
                            pessoasExistentes = document
                                .getElementById("container-proprietario")
                                .querySelectorAll(".id-pessoa");

                            encontrou = false;

                            pessoasExistentes.forEach(input => {
                                if (input.value == id) {
                                    encontrou = true;
                                }
                            });

                            if (!encontrou) {
                                containerPessoa.classList.add("pessoa-selecionada");
                                document
                                    .getElementById("container-proprietario")
                                    .appendChild(containerPessoa.cloneNode(true));
                            }

                            break;

                        case "CORRETOR":
                            pessoasExistentes = document
                                .getElementById("container-corretor")
                                .querySelectorAll(".id-pessoa");

                            encontrou = false;

                            pessoasExistentes.forEach(input => {
                                if (input.value == id) {
                                    encontrou = true;
                                }
                            });

                            if (!encontrou) {
                                containerPessoa.classList.add("pessoa-selecionada");
                                document
                                    .getElementById("container-corretor")
                                    .appendChild(containerPessoa.cloneNode(true));
                            }

                            break;

                        case "CAPTADOR":
                            pessoasExistentes = document
                                .getElementById("container-captador")
                                .querySelectorAll(".id-pessoa");

                            encontrou = false;

                            pessoasExistentes.forEach(input => {
                                if (input.value == id) {
                                    encontrou = true;
                                }
                            });

                            if (!encontrou) {
                                containerPessoa.classList.add("pessoa-selecionada");
                                document
                                    .getElementById("container-captador")
                                    .appendChild(containerPessoa.cloneNode(true));
                            }

                            break;
                    }
                }
            }

            document.body.removeChild(container);
            document.removeEventListener("click", click);
        }
    };
    if (!document.eventListenerList || !document.eventListenerList.some(listener => listener.type === "click" && listener.listener === click)) {
        setTimeout(() => {
            document.addEventListener("click", click);
        }, 0);
    }
    const pesquisarInput = document.createElement("input");
    pesquisarInput.type = "text";
    pesquisarInput.placeholder = "Pesquisar...";
    pesquisarInput.oninput = function () {
        const query = pesquisarInput.value.toLowerCase();
        const resultados = container.querySelectorAll("div");
        resultados.forEach(resultado => {
            const labels = resultado.querySelectorAll("label");
            let encontrou = false;
            labels.forEach(label => {
                if (label.textContent.toLowerCase().includes(query.toLowerCase())) {
                    encontrou = true;
                }
            });
            resultado.closest(".resultado-pessoa").style.display = encontrou ? "flex" : "none";
        });
    };

    let idsExistentes = [];

    switch (tipo) {
        case "PROPRIETARIO":
            idsExistentes = Array.from(
                document.getElementById("container-proprietario")
                    .querySelectorAll(".resultado-pessoa .div-right .id-pessoa")
            ).map(input => input.value);
            break;
        case "CORRETOR":
            idsExistentes = Array.from(document.getElementById("container-corretor").querySelectorAll(".resultado-pessoa .div-right .id-pessoa")
            ).map(input => input.value);
            break;
        case "CAPTADOR":
            idsExistentes = Array.from(document.getElementById("container-captador").querySelectorAll(".resultado-pessoa .div-right .id-pessoa")
            ).map(input => input.value);
            break;
    }

    container.appendChild(pesquisarInput);

    for (let pessoa of lista) {
        let div_resultado = document.createElement("div");
        div_resultado.classList.add("resultado-pessoa");
        let checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.checked = (idsExistentes?.includes(pessoa.id?.toString())) ? true : false;
        checkbox.name = "pessoa-selecionada";

        let div_left = document.createElement("div");
        div_left.classList.add("div-left");
        div_left.appendChild(checkbox);

        div_resultado.appendChild(div_left);
        let nome = document.createElement("label");
        nome.textContent = "Nome: " + (pessoa.nome ? pessoa.nome : "");
        nome.classList.add("nome-pessoa");

        let div_right = document.createElement("div");
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
        email.textContent = "Email: " + (pessoa.email ? pessoa.email : "");
        div_right.appendChild(email);
        let telefone = document.createElement("label");
        telefone.textContent = "Telefone: " + (pessoa.telefone ? pessoa.telefone : "");
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
    formData.append("proprietarios", []);

    if (imagens.length > 0) {
        for (let img of imagens) {

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

    let containerFiltrosApartamento = document.getElementById("container-info-imovel");
    if (containerFiltrosApartamento) {
        const filtros = containerFiltrosApartamento.querySelectorAll("input[type='checkbox']");
        const filtrosSelecionados = Array.from(filtros).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
        formData.append("filtros_apartamento", JSON.stringify(filtrosSelecionados));
    }

    let containerFiltrosCondominio = document.getElementById("container-info-condominio");
    if (containerFiltrosCondominio) {
        const filtros = containerFiltrosCondominio.querySelectorAll("input[type='checkbox']");
        const filtrosSelecionados = Array.from(filtros).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
        formData.append("filtros_condominio", JSON.stringify(filtrosSelecionados));
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
            let caminho = getCaminhoRelativo("/php/api/imoveis.php?acao=cadastrar");
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
    let confirmar = confirm("Tem certeza que deseja excluir este imóvel?");
    let imovelId = imovel.id;
    if (imovelId && confirmar) {
        excluirImovel(imovelId);
    }
    else {
        alert("Nenhum imóvel selecionado para exclusão!");
        // window.location.href = "estoque.html";
    }


}

function abrirTab(posicao) {
    const divTabs = document.querySelector(".seaTabs_switch");

    for (let i = 0; i < divTabs.children.length; i++) {
        if (divTabs.children[i].classList.contains("seaTabs_switch_active")) {
            divTabs.children[i].classList.remove("seaTabs_switch_active");
        }
    }

    switch (posicao) {
        case 0:
            divTabs.children[0].classList.toggle("seaTabs_switch_active");
            document.getElementById("container-cadastro").style.display = "grid";
            document.getElementById("container-anuncio").style.display = "none";
            break;
        case 1:
            divTabs.children[1].classList.toggle("seaTabs_switch_active");
            document.getElementById("container-anuncio").style.display = "flex";
            document.getElementById("container-cadastro").style.display = "none";
            break;
        default:
            break;
    }

    sessionStorage.setItem("cadastro-imovel: abaAtiva", posicao);
}


async function abrirCadastro(imovel) {
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
                let divImagem = document.createElement("div");
                divImagem.classList.add("imagem-anuncio");
                divImagem.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url(${imagem})`
                divImagem.setAttribute("onclick", `abrirImagem('${imagem}')`);
                let checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.classList.add("checkbox-imagem");
                checkbox.value = imagem;
                checkbox.name = "imagens-selecionadas";
                divImagem.appendChild(checkbox);
                prepararImagemArrastavel(divImagem);
                containerImagens.appendChild(divImagem);
                contadorImgens++;
            }
            document.getElementById("contador-imagens").textContent = contadorImgens + " imagem(s)";
            containerImagens.querySelector(".abrir-multiplos").style.display = "inline-block";
            containerImagens.querySelector(".apagar-multiplos").style.display = "inline-block";
        }
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
        let pessoas = {};
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
                    div_resultado.classList.add("pessoa-selecionada");
                    let checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.name = "pessoa-selecionada";

                    let div_left = document.createElement("div");
                    div_left.classList.add("div-left");
                    div_left.appendChild(checkbox);

                    div_resultado.appendChild(div_left);
                    let nome = document.createElement("label");
                    nome.textContent = "Nome: " + (pessoa.nome ? pessoa.nome : "");
                    nome.classList.add("nome-pessoa");

                    let div_right = document.createElement("div");
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
                    email.textContent = "Email: " + (pessoa.email ? pessoa.email : "");
                    div_right.appendChild(email);
                    let telefone = document.createElement("label");
                    telefone.textContent = "Telefone: " + (pessoa.telefone ? pessoa.telefone : "");
                    div_right.appendChild(telefone);
                    div_resultado.appendChild(div_right);
                    container.appendChild(div_resultado);
                }
            }
        }

        if (imovel.filtros && imovel.filtros.length > 0) {
            let containerFiltrosApartamento = document.getElementById("container-info-imovel");
            if (containerFiltrosApartamento) {
                const filtros = containerFiltrosApartamento.querySelectorAll("input[type='checkbox']");
                filtros.forEach(checkbox => {
                    if (imovel.filtros.includes(checkbox.value)) {
                        checkbox.checked = true;
                    }
                });
            }
        }

        if (imovel.condominio && imovel.condominio.filtros && imovel.condominio.filtros.length > 0) {
            let containerFiltrosCondominio = document.getElementById("container-info-condominio");
            if (containerFiltrosCondominio) {
                const filtros = containerFiltrosCondominio.querySelectorAll("input[type='checkbox']");
                filtros.forEach(checkbox => {
                    if (imovel.condominio.filtros.includes(checkbox.value)) {
                        checkbox.checked = true;
                    }
                });
            }
        }

        if (imovel.destacado) {
            document.getElementById("checkbox").querySelector("input[type='checkbox']").checked = true;
        }

        if (imovel.quant_clicks) {
            document.getElementById("quant-clicks").value = 'Quantidade de cliques: ' + imovel.quant_clicks;
        }

        if (imovel.endereco?.cep) {
            const endereco =
                `${imovel.endereco.rua},
                ${imovel.endereco.numero},
                ${imovel.endereco.cidade},
                ${imovel.endereco.uf},
                Brasil`;

            const coordenadas = await buscarCoordenadas(endereco);

            if (coordenadas) {

                carregarMapa(
                    coordenadas.lat,
                    coordenadas.lng
                );

            }
        }


    } else {
        alert("Imóvel não encontrado!");
        window.location.href = "estoque.html";
    }
}


function abrirImagem(src) {
    if (event.target.tagName === "INPUT" && event.target.type === "checkbox") {
        return;
    }
    var modal = document.createElement("div");
    modal.id = "modal-imagem";

    var img = document.createElement("img");
    img.src = src;

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

async function carregarHistorico(idImovel) {
    const lista = await listarHistoricoPorIdImovel(idImovel);
    if (lista != null && lista.length > 0) {
        const tabela = document.getElementById("historico");
        tabela.style.display = "flex";
        tabela.style.flexDirection = "column";
        for (let item of lista) {
            let tr = document.createElement("tr");
            let tdData = document.createElement("td");
            tdData.textContent = item.data;
            tr.appendChild(tdData);
            let tdUsuario = document.createElement("td");
            tdUsuario.textContent = item.funcionario.nome;
            tr.appendChild(tdUsuario);
            let tdAlteracao = document.createElement("td");
            tdAlteracao.textContent = item.alteracao;
            tr.appendChild(tdAlteracao);
            document.getElementById("historico-body").appendChild(tr);
        }
    }
}

window.addEventListener("DOMContentLoaded", async function () {

    sessionStorage.getItem("cadastro-imovel: abaAtiva") ? abrirTab(parseInt(sessionStorage.getItem("cadastro-imovel: abaAtiva"))) : abrirTab(0);

    const id = new URLSearchParams(window.location.search).get("id");
    imovel = id ? await getDadosImovel(id) : null;
    if (imovel) {
        carregarHistorico(imovel.id);
        if (imovel.endereco.complemento && /[a-zA-ZÀ-ÿ]/i.test(imovel.endereco.complemento)) {
            if (imovel.bloco == null || imovel.bloco === "") {
                imovel.bloco
                    = imovel.endereco.complemento.match(/[a-zA-ZÀ-ÿ]/g).join('') || null;
            }
            imovel.endereco.complemento = imovel.endereco.complemento.replace(/[a-zA-ZÀ-ÿ]/g, "").trim();
        }
        await abrirCadastro(imovel);
        calcularMedia();
    }

    document.getElementById("destacar").addEventListener('change', function () {
        destacarImovel(imovel.id);
    });


    const usuario = usuarioLogado || await carregarUser();

    if (usuario && usuario.tipo && usuario.tipo === "ADMIN") {
        const botao = document.createElement("button");
        botao.textContent = "Alterar Logo";
        document.querySelector(".selecionar-todos").after(botao);
    }

    Inputmask("99999-999").mask("#ta-cep");

    Inputmask('currency', {
        prefix: 'R$ ',
        groupSeparator: '.',
        radixPoint: ',',
        digits: 2,
        autoGroup: true,
        allowMinus: false,
        rightAlign: false,
        placeholder: '0',
        numericInput: true,
        positionCaretOnClick: "radixFocus"
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


