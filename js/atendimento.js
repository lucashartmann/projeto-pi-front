import { getCaminhoRelativo } from "./modules/utils.js";

let listaAtendimentos = [];

window.abrirAtendimento = abrirAtendimento;

async function listarAtendimentos() {
    try {
        let caminho = getCaminhoRelativo("/php/api/atendimentos.php?acao=listar");

        const res = await fetch(caminho);

        if (!res.ok) {
            console.error(`HTTP ${res.status}`);
            return [];
        }

        const contentType = res.headers.get("content-type");

        if (contentType && contentType.includes("application/json")) {
            const dados = await res.json();
            if (dados.status === "erro") {
                console.warn("Erro ao listar atendimentos: " + dados.mensagem);
                return [];
            }
            else {
                return dados;
            }
        } else {
            const texto = await res.text();
            console.warn("Resposta inesperada do servidor");
            console.error("Resposta não é JSON:", texto);
            return [];
        }

    } catch (erro) {
        console.error("Falha ao conectar com o backend:", erro);
        return [];
    }
}

function formatarTelefone(numero) {
    if (!numero) return '';

    let telefone = String(numero).replace(/\D/g, '');

    if (telefone.startsWith('55')) {
        const nacional = telefone.slice(2);

        if (nacional.length === 11) {
            return nacional.replace(
                /(\d{2})(\d{5})(\d{4})/,
                '+55 ($1) $2-$3'
            );
        }

        if (nacional.length === 10) {
            return nacional.replace(
                /(\d{2})(\d{4})(\d{4})/,
                '+55 ($1) $2-$3'
            );
        }
    }

    if (telefone.length === 11) {
        return telefone.replace(
            /(\d{2})(\d{5})(\d{4})/,
            '($1) $2-$3'
        );
    }

    if (telefone.length === 10) {
        return telefone.replace(
            /(\d{2})(\d{4})(\d{4})/,
            '($1) $2-$3'
        );
    }

    if (telefone.length > 11) {
        return '+' + telefone;
    }

    return telefone;
}

async function carregarAtendimentos() {
    const dados = listaAtendimentos;
    const section = document.getElementById("container-horizontal");
    const divRecemCadastrados = document.getElementById("container-cadastrados");
    const divEmAndamento = document.getElementById("container-andamento");
    const divPendente = document.getElementById("container-esperando");
    let divVazio = null;

    if (!dados || dados.length === 0) {
        divVazio = document.createElement("div");
        divVazio.className = "vazio";
        divVazio.textContent = "Nenhum atendimento encontrado.";
        divRecemCadastrados.appendChild(divVazio);
        divVazio = document.createElement("div");
        divVazio.className = "vazio";
        divVazio.textContent = "Nenhum atendimento encontrado.";
        divEmAndamento.appendChild(divVazio);
        divVazio = document.createElement("div");
        divVazio.className = "vazio";
        divVazio.textContent = "Nenhum atendimento encontrado.";
        divPendente.appendChild(divVazio);
    }

    if (!section || !dados) return;

    for (child of divRecemCadastrados.children) {
        child.remove();
    }

    for (child of divEmAndamento.children) {
        child.remove();
    }

    for (child of divPendente.children) {
        child.remove();
    }

    const tamanho = dados.length < 5 ? dados.length : 5;

    for (let i = 0; i < tamanho; i++) {
        const divCard = document.createElement("div");
        divCard.className = "card";
        divCard.onclick = () => abrirAtendimento(dados[i].id);
        divCard.innerHTML = `
            <h2>Nome: ${dados[i].cliente.nome}</h2>
            <p>Telefone: ${dados[i].cliente.telefones?.flat()
                            .map(telefone => formatarTelefone(telefone))
                            .join(', ') ?? '' ?? ''}</p>
            <p>Email: ${dados[i].cliente.email}</p>
        `;
        divRecemCadastrados.appendChild(divCard);
    }

    for (const atendimento of dados) {
        if (atendimento.status === "Em andamento") {
            const divEmAndamento = document.getElementById("container-em-andamento");
            if (!divEmAndamento) continue;
            const divCard = document.createElement("div");
            divCard.className = "card";
            divCard.onclick = () => abrirAtendimento(atendimento.id);
            divCard.innerHTML = `
                <h2>Cliente: ${atendimento.cliente.nome ?? ''}</h2>
                <p>
                    Telefone: ${
                        atendimento.cliente.telefones
                            ?.flat()
                            .map(telefone => formatarTelefone(telefone))
                            .join(', ') ?? ''
                    }
                </p>
                <p>Email: ${atendimento.cliente.email ?? ''}</p>
                <p>Data de cadastro: ${atendimento.data_cadastro ?? ''}</p>
                <h2>Imovel: ${atendimento.imovel ? `${atendimento.imovel.id} - ${atendimento.imovel.endereco?.rua}, ${atendimento.imovel.endereco?.numero}/${atendimento.imovel.endereco?.complemento}` : ''}</h2>
                <div class="botoes">
                    <button>Mandar Email</button>
                    <button>Enviar Whatsapp</button>
                    <button type="button" id="btn-agendar-visita" onclick=event.preventDefault(); event.stopPropagation(); agendarVisita(${atendimento.cliente?.id ?? ""}, ${atendimento.imovel?.id ?? ""})>Agendar Visita</button>
                    <button class="bt-delete">Encerrar</button>
                </div>
            `;
            divEmAndamento.appendChild(divCard);
        } else if (atendimento.status === "Pendente") {
            const divPendente = document.getElementById("container-esperando");
            if (!divPendente) continue;
            const divCard = document.createElement("div");
            divCard.className = "card";
            divCard.onclick = () => abrirAtendimento(atendimento.id);
            console.log(atendimento.cliente.telefones);
            divCard.innerHTML = `
                <h2>Cliente: ${atendimento.cliente.nome ?? ''}</h2>
                <p>
                    Telefone: ${
                        atendimento.cliente.telefones
                            ?.flat()
                            .map(telefone => formatarTelefone(telefone))
                            .join(', ') ?? ''
                    }
                </p>
                <p>Email: ${atendimento.cliente.email ?? ''}</p>
                <p>Data de cadastro: ${atendimento.data_cadastro ?? ''}</p>
                <h2>Imovel: ${atendimento.imovel ? `${atendimento.imovel.id} - ${atendimento.imovel.endereco?.rua}, ${atendimento.imovel.endereco?.numero}/${atendimento.imovel.endereco?.complemento}` : ''}</h2>
                <div class="botoes">
                    <button>Atender</button>
                    <button type="button" id="btn-agendar-visita" onclick="event.preventDefault(); event.stopPropagation(); agendarVisita(${atendimento.cliente?.id ?? ""}, ${atendimento.imovel?.id ?? ""})">Agendar Visita</button>
                    <button class="bt-delete">Encerrar</button>
                </div>
            `;
            divPendente.appendChild(divCard);
        }
    }

    if (dados.filter(a => a.status === "Em andamento").length === 0) {
        divVazio = document.createElement("div");
        divVazio.class = "vazio";
        divVazio.textContent = "Nenhum atendimento encontrado.";
        divEmAndamento.appendChild(divVazio);
    }

    if (dados.filter(a => a.status === "Pendente").length === 0) {
        divVazio = document.createElement("div");
        divVazio.class = "vazio";
        divVazio.textContent = "Nenhum atendimento encontrado.";
        divPendente.appendChild(divVazio);
    }

}

window.agendarVisita = agendarVisita;

function agendarVisita(idCliente, idImovel) {
    console.log(`Agendar visita para cliente ${idCliente} e imóvel ${idImovel}`);
    window.location.href = `agendar-visita.html?idCliente=${idCliente}&idImovel=${idImovel}`;
}

function abrirAtendimento(atendimentoId) {
    const atendimento = listaAtendimentos.find(
        a => a.id === atendimentoId
    );

    if (!atendimento) {
        console.warn("Atendimento não encontrado.");
        return;
    }

    const cardExistente = document.getElementById("card-dados");

    if (cardExistente) {
        cardExistente.remove();
        document.querySelector(".overlay")?.remove();
        return;
    }

    const overlay = document.createElement("div");
    overlay.className = "overlay";

    overlay.style.cssText = `
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 9998;
    `;

    let html = `
        <div id="cliente"> 
            <h3>Informações do Cliente:</h3>
            <div class="vertical-separator">
                <label>Nome:</label>
                <label>${atendimento.cliente?.nome ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Idade:</label>
                <label>${atendimento.cliente?.idade ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Telefone:</label>
                <label>${atendimento.cliente?.telefone ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>CPF/CNPJ:</label>
                <label>${atendimento.cliente?.cpf_cnpj ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>RG:</label>
                <label>${atendimento.cliente?.rg ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Email:</label>
                <label>${atendimento.cliente?.email ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Data de Nascimento:</label>
                <label>${atendimento.cliente?.data_nascimento ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Data de cadastro:</label>
                <label>${atendimento.data_cadastro ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Rua:</label>
                <label>${atendimento.cliente?.endereco?.rua ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Numero:</label>
                <label>${atendimento.cliente?.endereco?.numero ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Complemento:</label>
                <label>${atendimento.cliente?.endereco?.complemento ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Bairro:</label>
                <label>${atendimento.cliente?.endereco?.bairro ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>CEP:</label>
                <label>${atendimento.cliente?.endereco?.cep ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Cidade:</label>
                <label>${atendimento.cliente?.endereco?.cidade ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>UF:</label>
                <label>${atendimento.cliente?.endereco?.uf ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Profissão:</label>
                <label>${atendimento.cliente?.profissao ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Renda Mensal:</label>
                <label>${atendimento.cliente?.renda_mensal ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Tipo de imoveis Desejados:</label>
                <label>${atendimento.cliente?.tipo_imovel_desejado ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Endereço desejado:</label>
                <label>${atendimento.cliente?.endereco_desejado ?? ""}</label>
            </div>
            <div class="vertical-separator">
                <label>Orçamento:</label>
                <label>${atendimento.cliente?.orcamento ?? ""}</label>
            </div>
        </div>
    `;

    if (atendimento.imovel) {
        html += `
            <div id="imovel"> 
                <h3>Informações do Imóvel:</h3>
                <div class="vertical-separator">
                    <label>Referência:</label>
                    <label>${atendimento.imovel?.id ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Rua:</label>
                    <label>${atendimento.imovel?.endereco?.rua ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Número:</label>
                    <label>${atendimento.imovel?.endereco?.numero ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Complemento:</label>
                    <label>${atendimento.imovel?.endereco?.complemento ?? ""}${atendimento.imovel?.bloco ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Bairro:</label>
                    <label>${atendimento.imovel?.endereco?.bairro ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>CEP:</label>
                    <label>${atendimento.imovel?.endereco?.cep ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Cidade:</label>
                    <label>${atendimento.imovel?.endereco?.cidade ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>UF:</label>
                    <label>${atendimento.imovel?.endereco?.uf ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Valor de Venda:</label> 
                    <label>${atendimento.imovel?.valorVenda ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Valor de Aluguel:</label> 
                    <label>${atendimento.imovel?.valorAluguel ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Quantidade de Quartos:</label> 
                    <label>${atendimento.imovel?.quantQuartos ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Quantidade de Salas:</label> 
                    <label>${atendimento.imovel?.quantSalas ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Quantidade de Vagas:</label> 
                    <label>${atendimento.imovel?.quantVagas ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Quantidade de Banheiros:</label> 
                    <label>${atendimento.imovel?.quantBanheiros ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Quantidade de Varandas:</label> 
                    <label>${atendimento.imovel?.quantVarandas ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Quantidade de Suites:</label> 
                    <label>${atendimento.imovel?.quantSuites ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Categoria:</label> 
                    <label>${atendimento.imovel?.categoria ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Status:</label> 
                    <label>${atendimento.imovel?.status ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>IPTU:</label> 
                    <label>${atendimento.imovel?.iptu ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Valor do Condomínio:</label> 
                    <label>${atendimento.imovel?.valorCondominio ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Andar:</label> 
                    <label>${atendimento.imovel?.andar ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Estado:</label> 
                    <label>${atendimento.imovel?.estado ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Bloco:</label> 
                    <label>${atendimento.imovel?.bloco ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Ano de Construção:</label> 
                    <label>${atendimento.imovel?.anoConstrucao ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Área Total:</label> 
                    <label>${atendimento.imovel?.areaTotal ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Área Privativa:</label> 
                    <label>${atendimento.imovel?.areaPrivativa ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Situação:</label>
                    <label> ${atendimento.imovel?.situacao ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Ocupação:</label> 
                    <label>${atendimento.imovel?.ocupacao ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Proprietários:</label> 
                    ${atendimento.imovel?.proprietarios?.length > 0 ? atendimento.imovel.proprietarios.map(proprietario => `<label>${proprietario.id} - ${proprietario.nome}</label>`).join('') : '<label></label>'}
                </div>
                <div class="vertical-separator">
                    <label>Corretor:</label> 
                    <label>${atendimento.imovel?.corretor ? `${atendimento.imovel?.corretor.id} - ${atendimento.imovel?.corretor.nome}` : ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Captador:</label> 
                    <label>${atendimento.imovel?.captador ? `${atendimento.imovel?.captador.id} - ${atendimento.imovel?.captador.nome}` : ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Data de Cadastro:</label> 
                    <label>${atendimento.imovel?.dataCadastro ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Data de Modificação:</label> 
                    <label>${atendimento.imovel?.dataModificacao ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Anúncio:</label> 
                    ${atendimento.imovel?.anuncio ? `<label>${atendimento.imovel?.anuncio.titulo}</label><label>${atendimento.imovel?.anuncio.descricao}</label>` : "<label></label>"}
                </div>
                <div class="vertical-separator">
                    <label>Condomínio:</label> 
                    ${atendimento.imovel?.condominio ? `<label>${atendimento.imovel?.condominio.nome}</label><label>${atendimento.imovel?.condominio.endereco?.rua ?? ""}, ${atendimento.imovel?.condominio.endereco?.numero ?? ""}/${atendimento.imovel?.condominio.endereco?.complemento ?? ""}</label> ${atendimento.imovel?.condominio.filtros ? atendimento.imovel.condominio.filtros.map(filtro => `<label>${filtro}</label>`).join('') : "<label></label>"} ` : "<label></label>"}
                </div>
                <div class="vertical-separator">
                    <label>Filtros:</label> 
                    ${atendimento.imovel?.filtros ? atendimento.imovel.filtros.map(filtro => `<label>${filtro}</label>`).join('') : "<label></label>"}
                </div>
                <div class="vertical-separator">
                    <label>Destacado:</label> 
                    <label>${atendimento.imovel?.destacado ?? ""}</label>
                </div>
                <div class="vertical-separator">
                    <label>Quantidade de Cliques:</label> 
                    <label>${atendimento.imovel?.quantClicks ?? ""}</label>
                </div>
            </div>
        `;
    }

    html += `
        <div id="atendimento"> 
            <h3>Informações do Atendimento:</h3>
            <div class="vertical-separator">
            <label>Status:</label>
            <label>${atendimento.status ?? ""}</label>
            </div>
            <select id="status-select">
                <option value="" disabled>
                    Selecionar uma opção
                </option>
                <option value="Pendente">
                    Pendente
                </option>
                <option value="Em andamento">
                    Em andamento
                </option>
                <option value="Concluído">
                    Concluído
                </option>
            </select>
            <button>Mandar Email</button>
            <button>Enviar Whatsapp</button>
            <button type="button" id="btn-agendar-visita" onclick="agendarVisita(${atendimento.cliente?.id ?? ""}, ${atendimento.imovel?.id ?? ""})">Agendar Visita</button>
            <button class="bt-delete">Encerrar</button>
            <button class="bt-success">Salvar</button>
        </div> 
    `;

    const divDados = document.createElement("div");

    divDados.id = "card-dados";
    divDados.innerHTML = html;

    document.body.appendChild(overlay);
    document.body.appendChild(divDados);

    const statusSelect = document.getElementById("status-select");

    if (statusSelect) {
        statusSelect.value = atendimento.status ?? "";
    }

    overlay.addEventListener("click", () => {
        divDados.remove();
        overlay.remove();
    });

    console.log("Card criado:", divDados);
}

window.addEventListener("DOMContentLoaded", async () => {
    listaAtendimentos = await listarAtendimentos();
    carregarAtendimentos();
});

