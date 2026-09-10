import { listarImoveisDisponiveis } from "./modules/imoveis.js";
import { usuarioLogado, carregarUser } from "./modules/usuario.js";
import { listarPessoas } from "./modules/usuarios.js";
import { getCaminhoRelativo } from "./modules/utils.js";

window.adicionarAnexo = adicionarAnexo;
window.cadastrar = cadastrar;
window.remover = remover;

async function getOutrosDados(formData) {
    const containerDocumentos = document.getElementById("container-anexos");

    formData.append("documentos", []);

    if (containerDocumentos && containerDocumentos.querySelectorAll("a").length > 0) {
        for (let doc of containerDocumentos.querySelectorAll("a")) {
            try {
                const response = await fetch(doc.href);
                if (!response.ok) console.error("Falha ao buscar o documento");
                const blob = await response.blob();
                const nomeArquivo = doc.textContent.trim().split(" ").join("_");
                formData.append("documentos[]", blob, `${nomeArquivo}`);
            } catch (error) {
                console.error("Erro ao processar documento:", doc.href, error);
            }
        }
    }

    return formData;
}

function adicionarAnexo(event) {
    var input = document.createElement("input");
    input.type = "file";
    input.accept = "application/pdf";
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
            if (file.type === "application/pdf") {
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
    document.getElementById("contador-documentos").textContent = contadorDocumentos + " documento(s)";
    input.click();
}

async function cadastrar() {
    var form = document.querySelector("form");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    let formData = new FormData(form);
    const data = {};

    formData = await getOutrosDados(formData);

    formData.forEach((value, key) => {
        data[key] = value;
    });


    if (JSON.stringify(formData).length > 0) {
        try {
            let caminho = getCaminhoRelativo("/php/api/contratos.php?acao=cadastrar");
            await fetch(caminho, {
                method: "POST",
                body: JSON.stringify(data)
            })
                .then(async response => {
                    if (response.erro) {
                        alert("Erro ao cadastrar contrato: " + response.erro);
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
                        alert("Erro ao cadastrar contrato: " + data.mensagem);
                        return;
                    }
                    else if (data.mensagem) {
                        alert("Contrato cadastrado com sucesso: " + data.mensagem);
                    }

                })
                .catch(error => {
                    alert("Erro ao cadastrar contrato:", error);
                });

        } catch (error) {
            console.error("Erro ao enviar dados do contrato:", error);
        }

    } else {
        alert("Nenhum dado para enviar!");
    }

}

async function listar() {
    try {
        let caminho = getCaminhoRelativo("/php/api/contratos.php?acao=listar");
        const resposta = await fetch(caminho)
            // .then(res => console.log(res))
            .then(async (res) => {
                if (res.erro) {
                    alert("Erro ao listar contratos: " + res.erro);
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
                if (data.status == "erro") {
                    console.error("Erro ao listar contratos: " + data.mensagem);
                    return null;
                }
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

async function remover() {
    let confirmar = confirm("Tem certeza que deseja excluir este contrato?");
    if (contratoID && confirmar) {
        try {
            let caminho = getCaminhoRelativo("/php/api/contratos.php?acao=apagar&id=" + contratoID);
            const response = await fetch(caminho, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },

            })
                .then(async (response) => {
                    if (response.erro) {
                        alert("Erro ao remover contrato: " + response.erro);
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
                        alert("Erro ao excluir contrato: " + data.mensagem);
                    } else {
                        console.log("Contrato excluído com sucesso:", data);
                        window.location.href = "estoque.html";
                    }
                })
                .catch(error => {
                    console.error("Erro ao excluir contrato:", error);
                });
        } catch (error) {
            console.error("Erro ao enviar dados para exclusão do contrato:", error);
        }
    }
    else {
        // alert("Nenhum imóvel selecionado para exclusão!");
        window.location.href = "contratos.html";
    }

}

document.getElementById("aluguel").addEventListener("change", function () {
    document.getElementById("valor-aluguel").disabled = !this.checked;
    document.getElementById("mesmo-aluguel").disabled = !this.checked;
    if (!document.getElementById("valor-venda").disabled) {
        document.getElementById("valor-venda").disabled = this.checked;
    }
    if (!document.getElementById("mesmo-venda").disabled) {
        document.getElementById("mesmo-venda").disabled = this.checked;
    }
    document.getElementById("venda").checked = false;
});

document.getElementById("venda").addEventListener("change", function () {
    if (!document.getElementById("valor-aluguel").disabled) {
        document.getElementById("valor-aluguel").disabled = this.checked;
    }
    if (!document.getElementById("mesmo-aluguel").disabled) {
        document.getElementById("mesmo-aluguel").disabled = this.checked;
    }
    document.getElementById("valor-venda").disabled = !this.checked;
    document.getElementById("mesmo-venda").disabled = !this.checked;
    document.getElementById("aluguel").checked = false;
});


window.montarConsulta = montarConsulta;

async function montarConsulta() {
    const contratos = await listar() || null;
    const container = document.getElementById("resultado");
    container.innerHTML = "";
    if (contratos && contratos.length > 0) {
        for (let contrato of contratos) {
            const card = document.createElement("div");
            card.classList.add("card-resultado");
            card.innerHTML = `
                <div class="horizontal">
                <label for="">Proprietário:</label>
                <label for="">${contrato.proprietario.nome}</label>
                </div>
                <div class="horizontal">
                <label for="">Imovel:</label>
                <label for="">${contrato.imovel.endereco.rua}</label>
                </div>
                <div class="horizontal">
                <label for="">Captador:</label>
                <label for="">${contrato.captador.nome}</label>
                </div>
                <div class="horizontal">
                <label for="">Corretor:</label>
                <label for="">${contrato.corretor.nome}</label>
                </div>
                <div class="horizontal">
                <label for="">Data de término:</label>
                <label for="">${contrato.data_termino}</label>
                </div>
                <div class="horizontal">
                <label for="">Data de cadastro:</label>
                <label for="">${contrato.data_cadastro}</label>
                </div>
                <div class="horizontal">
                <label for="">Data de modificação:</label>
                <label for="">${contrato.data_modificacao}</label>
                </div>
                `;
        }
    } else {
        const divVazio = document.createElement("div");
        divVazio.id = "vazio";
        divVazio.textContent = "Nenhum contrato encontrado.";
        container.innerHTML = "";
        container.appendChild(divVazio);
    }
}

document.getElementById("mesmo-aluguel").addEventListener("change", function () {
    document.getElementById("valor-aluguel").disabled = this.checked;
});

document.getElementById("mesmo-venda").addEventListener("change", function () {
    document.getElementById("valor-venda").disabled = this.checked;
});

window.addEventListener("DOMContentLoaded", async () => {
    const imoveis = await listarImoveisDisponiveis() || null;
    const usuario = usuarioLogado || await carregarUser();
    const proprietarios = await listarPessoas('PROPRIETARIO') || null;
    const locadores = await listarPessoas('CLIENTE') || null;
    if (imoveis && imoveis.length > 0) {
        document.getElementById("select-imovel").innerHTML = `
       <option value="">Selecione uma opção...</option>
            ${imoveis.map(imovel =>
            `<option value="${imovel.id}">${imovel.id} - ${imovel.endereco?.rua}, ${imovel.endereco?.numero}/${imovel.endereco?.complemento}</option>`
        ).join('')}
        `;
    } else {
        document.getElementById("select-imovel").innerHTML = `
       <option value="">Nenhum imóvel encontrado</option>
        `;
    }

    if (proprietarios && proprietarios.length > 0) {
        document.getElementById("select-proprietario").innerHTML = `
       <option value="">Selecione uma opção...</option>
            ${proprietarios.map(pessoa =>
            `<option value="${pessoa.id}">${pessoa.id} - ${pessoa.nome}</option>`
        ).join('')}
        `;
    } else {
        document.getElementById("select-proprietario").innerHTML = `
       <option value="">Nenhum proprietário encontrado</option>
        `;
    }

    if (locadores && locadores.length > 0) {
        document.getElementById("select-locador").innerHTML = `
       <option value="">Selecione uma opção...</option>
            ${locadores.map(pessoa =>
            `<option value="${pessoa.id}">${pessoa.id} - ${pessoa.nome}</option>`
        ).join('')}
        `;
    } else {
        document.getElementById("select-locador").innerHTML = `
       <option value="">Nenhum locador encontrado</option>
        `;
    }


});