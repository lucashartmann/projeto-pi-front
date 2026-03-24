function setupDados(dados) {
    var div = document.getElementById("dados_imovel");
    let imagensHtml = "";
    console.log(dados);
    if (dados.anuncio.imagens && dados.anuncio.imagens.length > 0) {
        imagensHtml += `<img src="data:image/jpeg;base64,${dados.anuncio.imagens[0]}" alt="Imagem do imóvel" />`;
        imagensHtml += `<ul id="ul_imagens">`;
        for (const imagem of dados.anuncio.imagens) {
            imagensHtml += `<li><img src="data:image/jpeg;base64,${imagem}" alt="Imagem do imóvel" onclick="abrirImagem(this.src)" /></li>`;
        }
        imagensHtml += `</ul>`;
    }

    let html = `
        <div id="div_pai">
            <div id="div_titulo">
                <h3>${dados.anuncio.titulo}</h3>
                <p>${dados.endereco.rua}, ${dados.endereco.numero}, ${dados.endereco.bairro}</p>
                ${imagensHtml}
            </div>
            <h3>Descrição</h3>
            <p>${dados.anuncio.descricao}</p>
        </div>
        <div id="entrar_contato">
            <div>
                <button>Agendar Visita</button>
                <button id="whatsapp">Whatsapp</button>
            </div>
            <div>
                <label id="condominio">Condominio: <p style='color:green;'>${dados.valor_condominio}</p></label>
                <div>
                    <label id="iptu">IPTU: <p style='color:green;'>${dados.valor_iptu}</p></label>
                    <button id="bt_contato">Entrar em contato</button>
                    <label>Um especialista irá entrar em contato por email ou whatsapp</label>
                </div>
            </div>
    `;
    div.innerHTML = html;

    //TODO: adicionar filtros, localizacao, valores, etc
}


window.addEventListener("DOMContentLoaded", async () => {
    id = sessionStorage.getItem("imovel_id") || null;
    if (!id) {
        alert("Imóvel não encontrado!");
        window.location.href = "../html/index.html";
        return;
    }
    dados = await getDadosImovel(id);
    sessionStorage.removeItem("imovel_id");
    if (dados) {
        setupDados(dados);
    }
});

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
